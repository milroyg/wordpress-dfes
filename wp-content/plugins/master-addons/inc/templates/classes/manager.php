<?php

namespace MasterAddons\Inc\Templates\Classes;

use MasterAddons\Inc\Templates;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/8/19
 */


if (!defined('ABSPATH')) exit;

if (!class_exists('Master_Addons_Templates_Manager')) {


	class Master_Addons_Templates_Manager
	{

		private static $instance = null;

		private $sources = array();


		public function __construct()
		{

			//Register AJAX hooks
			add_action('wp_ajax_jltma_get_templates', array($this, 'get_templates'));
			add_action('wp_ajax_nopriv_jltma_get_templates', array($this, 'get_templates'));

			add_action('wp_ajax_ma_el_get_templates', array($this, 'get_paginated_templates'));
			add_action('wp_ajax_nopriv_ma_el_get_templates', array($this, 'get_paginated_templates'));

			add_action('wp_ajax_jltma_inner_template', array($this, 'jltma_insert_inner_template'));
			add_action('wp_ajax_nopriv_jltma_inner_template', array($this, 'jltma_insert_inner_template'));


			if (defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '2.2.8', '>')) {
				add_action('elementor/ajax/register_actions', array($this, 'jltma_register_ajax_actions'), 20);
			} else {
				add_action('wp_ajax_elementor_get_template_data', array($this, 'get_template_data'), -1);
			}

			$this->register_sources();

			add_filter('master-addons-core/assets/editor/localize', array($this, 'localize_tabs'));
		}


		public function localize_tabs($data)
		{

			$tabs    = $this->get_template_tabs();
			$ids     = array_keys($tabs);
			$default = $ids[0];

			$data['tabs']       = $this->get_template_tabs();
			$data['defaultTab'] = $default;

			// Pass popup builder context
			$current_post_type = get_post_type();
			$is_popup_builder = false;

			if ($current_post_type === 'jltma_popup' || $current_post_type === 'popupbuilder' ||
				(isset($_GET['post']) && get_post_type($_GET['post']) === 'jltma_popup') ||
				(isset($_GET['post']) && get_post_type($_GET['post']) === 'popupbuilder') ||
				(isset($_GET['page']) && strpos($_GET['page'], 'popup') !== false)) {
				$is_popup_builder = true;
			}

			if (isset($_GET['action']) && $_GET['action'] === 'elementor' &&
				isset($_GET['post']) && (get_post_type($_GET['post']) === 'ma_popup' || get_post_type($_GET['post']) === 'jltma_popup' || get_post_type($_GET['post']) === 'popupbuilder')) {
				$is_popup_builder = true;
			}

			$data['isPopupBuilder'] = $is_popup_builder;

			return $data;
		}


		public function register_sources()
		{

			if (!class_exists('MasterAddons\\Inc\\Templates\\Sources\\Master_Addons_Templates_Source_Base')) {
				require JLTMA_PATH . 'inc/templates/sources/base.php';
			}

			$namespace = str_replace('Classes', 'Sources', __NAMESPACE__);

			$sources = array(
				'master-api'   =>  $namespace . '\Master_Addons_Templates_Source_Api',
			);

			foreach ($sources as $key => $class) {

				if (!class_exists($class)) {
					require JLTMA_PATH . 'inc/templates/sources/' . $key . '.php';
				}

				$this->add_source($key, $class);
			}
		}


		public function get_template_tabs()
		{

			$tabs = Templates\master_addons_templates()->types->get_types_for_popup();
			return $tabs;
		}


		public function add_source($key, $class)
		{
			$this->sources[$key] = new $class();
		}


		public function get_source($slug = null)
		{
			return isset($this->sources[$slug]) ? $this->sources[$slug] : false;
		}



		public function get_templates()
		{
			// Try multiple nonce actions for compatibility
			$nonce_valid = check_ajax_referer('jltma_get_templates_nonce_action', '_wpnonce', false) ||
						   check_ajax_referer('jltma_template_library_nonce', '_wpnonce', false) || check_ajax_referer('jltma_get_templates_nonce_action', 'security', false);

			// Enhanced security checks
			if (!$nonce_valid) {
				wp_send_json_error(array('message' => 'Permission denied - nonce failed'));
			}

			// Check user permissions
			if (!current_user_can('edit_posts')) {
				wp_send_json_error(array('message' => 'Permission denied - insufficient capabilities'));
			}

			// Rate limiting - prevent abuse
			// $user_id = get_current_user_id();
			// $rate_limit_key = "jltma_template_requests_{$user_id}";
			// $request_count = get_transient($rate_limit_key);

			// if ($request_count && $request_count > 500) {
			// 	wp_send_json_error(array('message' => 'Rate limit exceeded. Please wait before making more requests.'));
			// }

			// set_transient($rate_limit_key, ($request_count ? $request_count + 1 : 1), HOUR_IN_SECONDS);

			// Enhanced input validation - check both POST and GET for compatibility
			$tab = isset($_POST['tab']) ? sanitize_key($_POST['tab']) : (isset($_GET['tab']) ? sanitize_key($_GET['tab']) : null);
		$search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : (isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '');
		$category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : (isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all');
		$page = isset($_POST['page']) ? absint($_POST['page']) : (isset($_GET['page']) ? absint($_GET['page']) : 1);
		$per_page = 15; // Templates per page


			if (!$tab) {
				wp_send_json_error(array('message' => 'Tab parameter is required'));
			}

			// Validate tab value against allowed values
			$allowed_tabs = $this->get_allowed_tabs();
			if (!in_array($tab, $allowed_tabs, true)) {
				wp_send_json_error(array('message' => 'Invalid tab parameter'));
			}

			// Additional validation for tab data structure
			if (!$this->validate_tab_structure($tab) && $tab !== 'master_popups') {
				wp_send_json_error(array('message' => 'Invalid tab configuration'));
			}
			$tabs    = $this->get_template_tabs();
			$sources = $tabs[$tab]['sources'];

			$result = array(
				//					'ready_pages'  => array(),
				//					'ready_widgets'  => array(),
				'ready_headers'  => array(),
				'ready_footers'  => array(),
				'templates'  => array(),
				'categories' => array(),
				'keywords'   => array(),
			);

			foreach ($sources as $source_slug) {

				$source = isset($this->sources[$source_slug]) ? $this->sources[$source_slug] : false;

				if ($source) {
					// $result['ready_pages']  = array_merge( $result['ready_pages'], $source->get_items( $tab ) );
					$result['ready_headers']  = array_merge($result['ready_headers'], $source->get_items($tab));
					$result['ready_footers']  = array_merge($result['ready_footers'], $source->get_items($tab));
					$result['templates']  = array_merge($result['templates'], $source->get_items($tab));
					$result['categories'] = array_merge($result['categories'], $source->get_categories($tab));
					$result['keywords']   = array_merge($result['keywords'], $source->get_keywords($tab));
				}
			}


			$all_cats = array(
				array(
					'slug' => '',
					'title' => __('All Sections', 'master-addons' ),
				),
			);

			if (!empty($result['categories'])) {
				$result['categories'] = array_merge($all_cats, $result['categories']);
			}

		// Filter templates by category
		if ($category && $category !== 'all' && !empty($result['templates'])) {
			$result['templates'] = array_filter($result['templates'], function($template) use ($category) {
				if (isset($template['categories'])) {
					$template_categories = is_array($template['categories']) ? $template['categories'] : array($template['categories']);
					return in_array($category, $template_categories);
				}
				return false;
			});
			$result['templates'] = array_values($result['templates']); // Re-index array
		}

		// Filter templates by search term
		if (!empty($search) && !empty($result['templates'])) {
			$search_lower = strtolower($search);
			$result['templates'] = array_filter($result['templates'], function($template) use ($search_lower) {
				// Search in title
				if (isset($template['title']) && stripos($template['title'], $search_lower) !== false) {
					return true;
				}
				// Search in keywords
				if (isset($template['keywords']) && is_array($template['keywords'])) {
					foreach ($template['keywords'] as $keyword) {
						if (stripos(strtolower($keyword), $search_lower) !== false) {
							return true;
						}
					}
				}
				// Search in categories
				if (isset($template['categories']) && is_array($template['categories'])) {
					foreach ($template['categories'] as $cat) {
						if (stripos(strtolower($cat), $search_lower) !== false) {
							return true;
						}
					}
				}
				return false;
			});
			$result['templates'] = array_values($result['templates']); // Re-index array
		}

		// Calculate pagination
		$total_templates = count($result['templates']);
		$total_pages = ceil($total_templates / $per_page);
		$offset = ($page - 1) * $per_page;

		// Apply pagination
		$result['templates'] = array_slice($result['templates'], $offset, $per_page);



			if( $result ){
				$base_url = wp_upload_dir()['baseurl'];
				$extensions = ['.jpg', '.png', '.svg', '.webp'];
				foreach($result as $type => $content){
					if( 'ready_headers' !== $type && 'ready_footers' !== $type && 'templates' !== $type) continue;
					if( $type === 'templates'){
						$template_type = explode('_', $tab)[1];
					}else{
						$template_type = explode('_', $type)[1];
					}
					foreach($content as $index => $item){

						$template_id = $item['template_id'];
						$file_path = $base_url .'/master_addons/templates-library/master_' . $template_type . '/images/template-'. $template_id .'-preview';
						foreach ( $extensions as $extension ){
							if(file_exists($file_path . $extension )){
								$item['preview'] = $base_url .'/master_addons/templates-library/master_' . $template_type . '/images/template-'. $template_id .'-preview' . $extension;
								$item['thumbnail'] = $base_url .'/master_addons/templates-library/master_' . $template_type . '/images/template-'. $template_id .'-thumb' . $extension;
								break;
							}
						}
						$content[$index] = $item;
					}
					$result[$type] = $content;
				}
			}

		// Add pagination metadata
		$result['pagination'] = array(
			'current_page' => $page,
			'per_page' => $per_page,
			'total_items' => $total_templates,
			'total_pages' => $total_pages,
			'has_more' => $page < $total_pages
		);

			wp_send_json_success($result);
		}

		public function get_paginated_templates()
		{
			// Enhanced security checks
			if (!check_ajax_referer('jltma_get_templates_nonce_action', '_wpnonce', false)) {
				wp_send_json_error(array('message' => 'Security verification failed'));
			}

			// Check user permissions
			if (!current_user_can('edit_posts')) {
				wp_send_json_error(array('message' => 'Insufficient permissions'));
			}

			// Get pagination parameters
			$page = absint($_POST['page'] ?? 1);
			$per_page = absint($_POST['per_page'] ?? 10);
			$tab = sanitize_key($_POST['tab'] ?? 'master_section');

			// Validate tab value against allowed values
			$allowed_tabs = $this->get_allowed_tabs();
			if (!in_array($tab, $allowed_tabs, true)) {
				wp_send_json_error(array('message' => 'Invalid tab parameter'));
			}

			// Get all templates for the tab
			$tabs = $this->get_template_tabs();
			$sources = $tabs[$tab]['sources'];

			$all_templates = array();

			foreach ($sources as $source_slug) {
				$source = isset($this->sources[$source_slug]) ? $this->sources[$source_slug] : false;
				if ($source) {
					$templates = $source->get_items($tab);
					$all_templates = array_merge($all_templates, $templates);
				}
			}

			// Apply pagination
			$total_templates = count($all_templates);
			$offset = ($page - 1) * $per_page;
			$paginated_templates = array_slice($all_templates, $offset, $per_page);

			// Update thumbnail URLs to use cache folder first, then remote fallback
			foreach ($paginated_templates as &$template) {
				if (class_exists('JLTMA_Template_Kit_Cache')) {
					$cache_manager = JLTMA_Template_Kit_Cache::get_instance();
					if (!empty($template['thumbnail'])) {
						$cached_thumbnail = $cache_manager->get_kit_thumbnail_url('', $template['title'], $template['thumbnail']);
						if ($cached_thumbnail) {
							$template['thumbnail'] = $cached_thumbnail;
						}
					}
				}
			}

			wp_send_json_success(array(
				'templates' => $paginated_templates,
				'pagination' => array(
					'current_page' => $page,
					'per_page' => $per_page,
					'total_templates' => $total_templates,
					'total_pages' => ceil($total_templates / $per_page),
					'has_more' => ($offset + $per_page) < $total_templates
				)
			));
		}

		public function get_template_source($source_name)
		{
			return isset($this->sources[$source_name]) ? $this->sources[$source_name] : false;
		}

		public function get_template_defaults()
		{
			return [
				'template_id' => false,
				'source' => false,
			];
		}

		public function sanitize_template(array $template)
		{

			$template = array_merge($this->get_template_defaults(), $template);

			// Enhanced sanitization and validation
			$template['template_id'] = isset($template['template_id']) ? sanitize_text_field($template['template_id']) : false;
			$template['source'] = isset($template['source']) ? sanitize_text_field($template['source']) : false;
			$template['title'] = isset($template['title']) ? sanitize_text_field($template['title']) : 'Untitled Template';

			// Validate template_id format (alphanumeric and dashes only)
			if ($template['template_id'] && !preg_match('/^[a-zA-Z0-9\-_]+$/', $template['template_id'])) {
				$template['template_id'] = false;
			}

			// Validate source against allowed sources
			$allowed_sources = ['master-api'];
			if ($template['source'] && !in_array($template['source'], $allowed_sources, true)) {
				$template['source'] = false;
			}

			return $template;
		}

		/**
		 * Get allowed tabs for validation
		 */
		private function get_allowed_tabs()
		{
			return apply_filters('jltma_allowed_template_tabs', [
				'master_section',
				'master_pages',
				'master_popups',
				'master_headers',
				'master_footers'
			]);
		}

		/**
		 * Validate tab structure and configuration
		 */
		private function validate_tab_structure($tab)
		{
			$tabs = $this->get_template_tabs();

			// Check if tab exists in configuration
			if (!isset($tabs[$tab])) {
				return false;
			}

			// Validate tab has required structure
			$required_keys = ['sources'];
			foreach ($required_keys as $key) {
				if (!isset($tabs[$tab][$key])) {
					return false;
				}
			}

			// Validate sources are not empty
			if (empty($tabs[$tab]['sources']) || !is_array($tabs[$tab]['sources'])) {
				return false;
			}

			return true;
		}

		/**
		 * Validate template data structure
		 */
		private function validate_template_data($template_data)
		{
			if (!is_array($template_data)) {
				return false;
			}

			// Required fields
			$required_fields = ['content'];
			foreach ($required_fields as $field) {
				if (!isset($template_data[$field])) {
					return false;
				}
			}

			// Validate content is not empty
			if (empty($template_data['content'])) {
				return false;
			}

			// Validate content size (prevent extremely large templates)
			// if (strlen($template_data['content']) > 1048576) { // 1MB limit
			// 	return false;
			// }

			return true;
		}

		/**
		 * Validate source name against registered sources
		 */
		private function validate_source($source_name)
		{
			if (empty($source_name)) {
				return false;
			}

			// Check if source is registered
			if (!isset($this->sources[$source_name])) {
				return false;
			}

			// Validate source object
			$source = $this->sources[$source_name];
			if (!is_object($source)) {
				return false;
			}

			// Check if source has required methods
			$required_methods = ['get_item', 'get_items'];
			foreach ($required_methods as $method) {
				if (!method_exists($source, $method)) {
					return false;
				}
			}

			return true;
		}

		/*
		* Insert Template
		*/
		public function jltma_insert_inner_template()
		{
			// Enhanced security verification
			if (!check_ajax_referer('jltma_insert_templates_nonce_action', 'security', false)) {
				wp_send_json_error(array('message' => 'Security verification failed'));
			}

			// Check user permissions
			if (!current_user_can('edit_posts')) {
				wp_send_json_error(array('message' => 'Insufficient permissions'));
			}

			// Rate limiting for template insertions
			// $user_id = get_current_user_id();
			// $rate_limit_key = "jltma_template_inserts_{$user_id}";
			// $insert_count = get_transient($rate_limit_key);

			// if ($insert_count && $insert_count > 500) {
			// 	wp_send_json_error(array('message' => 'Template insertion rate limit exceeded'));
			// }

			// set_transient($rate_limit_key, ($insert_count ? $insert_count + 1 : 1), HOUR_IN_SECONDS);

			// Enhanced template validation
			if (!isset($_REQUEST['template'])) {
				wp_send_json_error(array('message' => 'Template data is required'));
			}

			$template = $this->sanitize_template((array) $_REQUEST['template']);

			if (!$template || empty($template['template_id']) || empty($template['source'])) {
				wp_send_json_error(array('message' => 'Invalid template data'));
			}

			// Validate source
			if (!$this->validate_source($template['source'])) {
				wp_send_json_error(array('message' => 'Invalid template source'));
			}

			$source = $this->get_template_source($template['source']);

			if (!$source || !$template['template_id']) {
				wp_send_json_error(array('message' => 'Template source or ID not found'));
			}

			$template_data = $source->get_item($template['template_id']);

			// Validate template data structure
			if (!$this->validate_template_data($template_data)) {
				wp_send_json_error(array('message' => 'Invalid template data structure'));
			}

			if (!empty($template_data['content'])) {
				// Additional validation before post creation
				$post_title = !empty($template['title']) ? sanitize_text_field($template['title']) : 'Master Addons Template ' . time();

				// Validate post title length
				if (strlen($post_title) > 255) {
					$post_title = substr($post_title, 0, 255);
				}

				// Validate Elementor data format
				$elementor_data = $template_data['content'];
				if (is_string($elementor_data)) {
					$decoded_data = json_decode($elementor_data, true);
					if (json_last_error() !== JSON_ERROR_NONE) {
						wp_send_json_error(array('message' => 'Invalid Elementor data format'));
					}
				}

				$post_id = wp_insert_post(array(
					'post_type'   => 'elementor_library',
					'post_title'  => $post_title,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'meta_input'  => array(
						'_elementor_data'          => $elementor_data,
						'_elementor_edit_mode'     => 'builder',
						'_elementor_template_type' => 'section',
						'_jltma_template_source'   => sanitize_text_field($template['source']),
						'_jltma_template_id'       => sanitize_text_field($template['template_id']),
					),
				));

				// Validate post creation success
				if (!$post_id || is_wp_error($post_id)) {
					wp_send_json_error(array('message' => 'Failed to create template post'));
				}
			} else {
				wp_send_json_error(array('message' => 'Template content is empty'));
			}

			wp_send_json_success();
		}

		public function jltma_register_ajax_actions($ajax_manager)
		{

			if (empty($_REQUEST['actions'])) {
				return;
			}

			$actions     = (array) json_decode(stripslashes(sanitize_text_field($_REQUEST['actions'])), true);
			$data        = false;

			foreach ($actions as $action_data) {
				if( in_array('get_template_data',  $action_data) || in_array('save_template',  $action_data) ){
					$data = $action_data;
				}
			}

			if (!$data) {
				return;
			}

			if (!isset($data['data'])) {
				return;
			}

			if (!isset($data['data']['source'])) {
				return;
			}

			// Handle both single source string and array of sources
			$sources = $data['data']['source'];
			if (!is_array($sources)) {
				$sources = [$sources];
			}

			foreach ( $sources as $source ) {
				if ( isset( $this->sources[ $source ] ) ) {
					// Register AJAX actions only once
					$ajax_manager->register_ajax_action( 'get_template_data', function( $data ) {
						return $this->get_template_data_array( $data );
					});

					$ajax_manager->register_ajax_action( 'save_template', function( $data ) {
						return $this->save_template_data_array( $data );
					});

					break; // Exit loop after registering once
				}
			}

		}


		public function save_template_data_array($data) {
				$post_id = sanitize_text_field($data['template_id'] ?? '');
				$template_data = wp_unslash($data['template_data'] ?? '');

				if ($post_id && $template_data) {
						return \Elementor\Plugin::$instance->templates_manager->save_template(
								$post_id,
								$template_data
						);
				}

				return new \WP_Error('invalid_data', 'Missing template ID or data');
		}


		public function get_template_data_array($data)
		{

			if (!current_user_can('edit_posts')) {
				return false;
			}

			if (empty($data['template_id'])) {
				return false;
			}

			$source_name = isset($data['source']) ? $data['source'] : '';

			// Handle both single source string and array of sources
			if (is_array($source_name)) {
				$source_name = !empty($source_name) ? esc_attr($source_name[0]) : '';
			} else {
				$source_name = esc_attr($source_name);
			}

			if (!$source_name) {
				return false;
			}

			$source = isset($this->sources[$source_name]) ? $this->sources[$source_name] : false;

			if (!$source) {
				return false;
			}

			if (empty($data['tab'])) {
				return false;
			}

			$template = $source->get_item($data['template_id'], $data['tab']);

			return $template;
		}


		public function get_template_data()
		{

			$template = $this->get_template_data_array($_REQUEST);

			if (!$template) {
				wp_send_json_error();
			}

			wp_send_json_success($template);
		}


		public static function get_instance()
		{

			// If the single instance hasn't been set, set it now.
			if (null == self::$instance) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
}