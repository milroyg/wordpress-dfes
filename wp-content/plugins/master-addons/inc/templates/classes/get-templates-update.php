<?php
// TEMPORARY FILE - Instructions for updating manager.php get_templates() method
// Replace the get_templates() method in manager.php (lines 130-233) with this updated version:

function get_templates()
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

	// Update thumbnail URLs
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