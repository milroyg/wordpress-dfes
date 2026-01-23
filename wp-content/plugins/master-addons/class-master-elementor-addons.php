<?php

namespace MasterAddons;

use MasterAddons\Admin\Dashboard\Master_Addons_Admin_Settings;
use MasterAddons\Admin\Dashboard\Addons\Extensions\JLTMA_Addon_Extensions;
use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Elements;
use MasterAddons\Inc\Helper\Master_Addons_Helper;
use MasterAddons\Inc\Classes\Feedback;
use MasterAddons\Inc\Classes\Pro_Upgrade;
use MasterAddons\Inc\Classes\Recommended_Plugins;
use MasterAddons\Inc\Admin\Promote_Pro_Addons;

if (!defined('ABSPATH')) {
	exit;
} // No, Direct access Sir !!!

if (!class_exists('Master_Elementor_Addons')) {
	/**
	 * Base class for Master Addons
	 * Can be extended by Pro version for additional features
	 * Removed 'final' to allow Pro extension
	 */
	class Master_Elementor_Addons
	{

		static public $class_namespace = '\\MasterAddons\\Inc\\Classes\\';
		public $controls_manager;

		const VERSION = JLTMA_VER;

		const MINIMUM_PHP_VERSION = '7.0';
		const MINIMUM_ELEMENTOR_VERSION = '3.5.0';

		// Changed from private to protected to allow Pro class extension
		protected $_localize_settings = [];
		protected $reflection;
		protected static $plugin_path;
		protected static $plugin_url;
		protected static $plugin_slug;
		public static $plugin_dir_url;
		protected static $instance = null;
		protected $jltma_classes = array();


		public static function get_instance()
		{
			if (!self::$instance) {
				self::$instance = new self;
				self::$instance->jltma_init();
			}
			return self::$instance;
		}


		public function __construct()
		{
			$this->reflection = new \ReflectionClass($this);
			$this->jltma_register_autoloader();
			$this->jltma_include_files();

			self::$plugin_slug = 'master-addons';
			self::$plugin_path = untrailingslashit(plugin_dir_path('/', __FILE__));
			self::$plugin_url  = untrailingslashit(plugins_url('/', __FILE__));

			// Load textdomain for translations
			add_action('init', [ $this, 'load_textdomain' ]);
			// Initialize Plugin
			add_action('plugins_loaded', [$this, 'jltma_plugins_loaded']);

			//Hook: elementor/elements/categories_register
			// add_action('elementor/init', [$this, 'jltma_add_category_to_editor']);

			add_action('elementor/init', [$this, 'jltma_add_actions_to_elementor'], 0);

			// Add Elementor Widgets
			add_action('elementor/widgets/register', [$this, 'jltma_init_widgets']);
			add_action('elementor/elements/categories_registered', [$this, 'jltma_add_category_to_editor']);


			// Register Controls - Must run for both Free and Pro versions
			add_action('elementor/controls/register', [$this, 'jltma_register_controls']);

			//Body Class
			add_action('body_class', [$this, 'jltma_body_class']);

			// AJAX handler for plugin install/activate
			add_action('wp_ajax_jltma_plugin_action', [$this, 'jltma_ajax_plugin_action']);

			add_filter('plugin_action_links_' . JLTMA_BASE, array($this, 'plugin_action_links'));
			add_filter('network_admin_plugin_action_links_' . JLTMA_BASE, array($this, 'plugin_action_links'));
		}

		public function jltma_init()
		{
			$this->jltma_image_size();

			//Redirect Hook
			add_action('admin_init', [$this, 'jltma_add_redirect_hook']);
		}

		public static function jltma_elementor()
		{
			return \Elementor\Plugin::$instance;
		}

		// Deactivation Hook
		public static function jltma_plugin_deactivation_hook()
		{
			delete_option('jltma_activation_time');
		}

		// Activation Hook
		public static function jltma_plugin_activation_hook()
		{

			self::activated_widgets();
			self::activated_extensions();
			self::activated_third_party_plugins();
			self::activated_icons_library();

			// Current Master Addons Version
			$current_version = get_option('_master_addons_version', null);
			if (is_null($current_version)) {
				update_option('_master_addons_version', JLTMA_VER);
			}

			$jltma_white_label_setting 	= jltma_get_options('jltma_white_label_settings') ?? [];
			if( !empty($jltma_white_label_setting) && isset($jltma_white_label_setting['jltma_wl_plugin_tab_white_label']) ) {
				$jltma_white_label_setting['jltma_wl_plugin_tab_white_label'] = 0;
				update_option( 'jltma_white_label_settings', $jltma_white_label_setting );
			}
		}

		public function set_plugin_activation_time()
		{

			if (is_multisite()) {

				if (get_site_option('jltma_activation_time') === false) {

					if (!function_exists('is_plugin_active_for_network')) {
						require_once(ABSPATH . '/wp-admin/includes/plugin.php');
					}

					if (is_plugin_active_for_network('master-addons-pro/master-addons.php') || is_plugin_active_for_network('master-addons/master-addons.php')) {
						update_site_option('jltma_activation_time', strtotime("now"));
					}
				}
			} else {
				if (get_option('jltma_activation_time') === false) {
					update_option('jltma_activation_time', strtotime("now"));
				}
			}
		}


		// Initialize
		public function jltma_plugins_loaded()
		{
			$this->set_plugin_activation_time();

			// Check if Elementor installed and activated
			if (!did_action('elementor/loaded')) {
				add_action('admin_notices', array($this, 'jltma_admin_notice_missing_main_plugin'));
				return;
			}

			// Check for required Elementor version
			if (defined('ELEMENTOR_VERSION') &&  !version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
				add_action('admin_notices', array($this, 'jltma_admin_notice_minimum_elementor_version'));
				return;
			}

			// Check for required PHP version
			if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
				add_action('admin_notices', array($this, 'jltma_admin_notice_minimum_php_version'));
				return;
			}

			// self::jltma_plugin_activation_hook();
		}

		public function jltma_register_autoloader()
		{
			spl_autoload_register([__CLASS__, 'jltma_autoload']);
		}

		function jltma_autoload($class)
		{

			if (0 !== strpos($class, __NAMESPACE__)) {
				return;
			}


			if (!class_exists($class)) {

				$filename = strtolower(
					preg_replace(
						['/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/'],
						['', '$1-$2', '-', DIRECTORY_SEPARATOR],
						$class
					)
				);

				$filename = JLTMA_PATH . $filename . '.php';

				if (is_readable($filename)) {
					include($filename);
				}
			}
		}

		function jltma_add_category_to_editor($widgets_manager)
		{

			$widgets_manager->add_category(
				'master-addons',
				[
					'title' => esc_html__('Master Addons', 'master-addons'),
					'icon'  => 'font',
				],
				1
			);
		}

		public function jltma_image_size()
		{
			add_image_size('master_addons_team_thumb', 250, 330, true);
		}

		// Widget Elements
		public static function activated_widgets()
		{
			$jltma_default_element_settings 	= array_fill_keys(Master_Addons_Admin_Settings::jltma_addons_array(), true);
			$jltma_get_element_settings     	= get_option('maad_el_save_settings', $jltma_default_element_settings);
			$jltma_new_element_settings     	= array_diff_key($jltma_default_element_settings, $jltma_get_element_settings);
			$jltma_updated_element_settings 	= array_merge($jltma_get_element_settings, $jltma_new_element_settings);

			if ($jltma_get_element_settings === false) {
				$jltma_updated_element_settings = $jltma_default_element_settings;
			}
			update_option('maad_el_save_settings', $jltma_updated_element_settings);

			return $jltma_updated_element_settings;
		}

		// Extensions
		public static function activated_extensions()
		{
			$jltma_default_extensions_settings 	= array_fill_keys(Master_Addons_Admin_Settings::jltma_addons_extensions_array(), true);
			$jltma_default_extensions_settings['mega-menu'] = 0;
			$jltma_get_extension_settings     	= get_option('ma_el_extensions_save_settings', $jltma_default_extensions_settings);
			$jltma_new_extension_settings     	= array_diff_key($jltma_default_extensions_settings, $jltma_get_extension_settings);
			$jltma_updated_extension_settings 	= array_merge($jltma_get_extension_settings, $jltma_new_extension_settings);

			if ($jltma_get_extension_settings === false) {
				$jltma_updated_extension_settings = $jltma_default_extensions_settings;
			}

			update_option('ma_el_extensions_save_settings', $jltma_updated_extension_settings);

			return $jltma_updated_extension_settings;
		}


		// Third Party Plugins
		public static function activated_third_party_plugins()
		{
			$jltma_third_party_plugins_settings 		= array_fill_keys(Master_Addons_Admin_Settings::jltma_addons_third_party_plugins_array(), true);
			$jltma_get_third_party_plugins_settings     = get_option('ma_el_third_party_plugins_save_settings', $jltma_third_party_plugins_settings);
			$jltma_new_third_party_plugins_settings     = array_diff_key($jltma_third_party_plugins_settings, $jltma_get_third_party_plugins_settings);
			$jltma_updated_third_party_plugins_settings = array_merge($jltma_get_third_party_plugins_settings, $jltma_new_third_party_plugins_settings);

			if ($jltma_get_third_party_plugins_settings === false) {
				$jltma_updated_third_party_plugins_settings = $jltma_third_party_plugins_settings;
			}
			update_option('ma_el_third_party_plugins_save_settings', $jltma_updated_third_party_plugins_settings);

			return $jltma_updated_third_party_plugins_settings;
		}

		// Icons Library
		public static function activated_icons_library()
		{
			$jltma_icons_library_settings           = array_fill_keys(Master_Addons_Admin_Settings::jltma_addons_icons_library_array(), true);
			$jltma_get_icons_library_settings       = get_option('jltma_icons_library_save_settings', $jltma_icons_library_settings);
			$jltma_new_icons_library_settings       = array_diff_key($jltma_icons_library_settings, $jltma_get_icons_library_settings);
			$jltma_updated_icons_library_settings   = array_merge($jltma_get_icons_library_settings, $jltma_new_icons_library_settings);

			if ($jltma_get_icons_library_settings === false) {
				$jltma_updated_icons_library_settings = $jltma_icons_library_settings;
			}
			update_option('jltma_icons_library_save_settings', $jltma_updated_icons_library_settings);

			return $jltma_updated_icons_library_settings;
		}

		/**
		 * Load all extension classes and instance them.
		 *
		 * This method will:
		 * 1. Get all PHP files inside the inc/classes directory.
		 * 2. Include all of them.
		 * 3. Create an instance of each class.
		 * 4. Store the instance in the $jltma_classes property.
		 *
		 * @since 1.1.0
		 * @return void
		 */
		public function jltma_add_actions_to_elementor() {
			$classes = glob(JLTMA_PATH . 'inc/classes/JLTMA_*.php');

			// include all classes FIRST (extensions depend on JLTMA_Extension_Prototype)
			foreach ($classes as $key => $value) {
					require_once $value;
			}

			// Load extensions AFTER prototype classes are loaded
			$this->jltma_load_extensions();

			// instance all classes
			foreach ($classes as $key => $value) {
					$name = pathinfo($value, PATHINFO_FILENAME);
					$class = self::$class_namespace . $name;

					// Now this will no longer trigger a deprecated warning
					$this->jltma_classes[strtolower($name)] = new $class();
			}
	}

		public function jltma_register_controls($controls_manager)
		{

			$controls_manager = \Elementor\Plugin::$instance->controls_manager;

			$controls = array(
				'jltma-visual-select' => array(
					'file'  => JLTMA_PATH . 'inc/controls/visual-select.php',
					'class' => 'MasterAddons\Inc\Controls\MA_Control_Visual_Select',
					'type'  => 'single'
				),
				'jltma-transitions' => array(
					'file'  => JLTMA_PATH . 'inc/controls/group/transitions.php',
					'class' => 'MasterAddons\Inc\Controls\MA_Group_Control_Transition',
					'type'  => 'group'
				),
				'jltma-filters-hsb' => array(
					'file'  => JLTMA_PATH . 'inc/controls/group/filters-hsb.php',
					'class' => 'MasterAddons\Inc\Controls\MA_Group_Control_Filters_HSB',
					'type'  => 'group'
				),
				'jltma-button-background' => array(
					'file'  => JLTMA_PATH . 'inc/controls/group/button-background.php',
					'class' => 'MasterAddons\Inc\Controls\MA_Group_Control_Button_Background',
					'type'  => 'group'
				),
				'jltma-choose-text' => array(
					'file'  => JLTMA_PATH . 'inc/controls/choose-text.php',
					'class' => 'MasterAddons\Inc\Controls\JLTMA_Control_Choose_Text',
					'type'  => 'single'
				),
				'jltma-file-select' => array(
					'file'  => JLTMA_PATH . 'inc/controls/file-select.php',
					'class' => 'MasterAddons\Inc\Controls\JLTMA_Control_File_Select',
					'type'  => 'single'
				),
				'jltma_query' => array(
					'file'  => JLTMA_PATH . 'inc/controls/jltma-query.php',
					'class' => 'MasterAddons\Inc\Controls\JLTMA_Control_Query',
					'type'  => 'single'
				),

			);

			foreach ($controls as $control_type => $control_info) {
				if (!empty($control_info['file']) && !empty($control_info['class'])) {

					include_once($control_info['file']);

					if (class_exists($control_info['class'])) {
						$class_name = $control_info['class'];
					} elseif (class_exists(__NAMESPACE__ . '\\' . $control_info['class'])) {
						$class_name = __NAMESPACE__ . '\\' . $control_info['class'];
					}

					if ($control_info['type'] === 'group') {
						$controls_manager->add_group_control($control_type, new $class_name());
					} else {
						$controls_manager->register(new $class_name());
					}
				}
			}
		}

		public function get_widgets()
		{
			return [];
		}

		public function jltma_init_widgets()
		{
			$activated_widgets = self::activated_widgets();
			$is_premium = ma_el_fs()->can_use_premium_code__premium_only();

			// Network Check
			if (defined('JLTMA_NETWORK_ACTIVATED') && JLTMA_NETWORK_ACTIVATED) {
				global $wpdb;
				$blogs = $wpdb->get_results("
				    SELECT blog_id
				    FROM {$wpdb->blogs}
				    WHERE site_id = '{$wpdb->siteid}'
				    AND spam = '0'
				    AND deleted = '0'
				    AND archived = '0'
				");
				$original_blog_id = get_current_blog_id();

				foreach ($blogs as $blog_id) {
					switch_to_blog($blog_id->blog_id);
					$this->register_widgets($activated_widgets, $is_premium);
				}
				switch_to_blog($original_blog_id);
			} else {
				$this->register_widgets($activated_widgets, $is_premium);
			}
		}

		private function register_widgets($activated_widgets, $is_premium)
		{
			$widget_manager = Master_Addons_Helper::jltma_elementor()->widgets_manager;
			$jltma_all_addons = Master_Addons_Admin_Settings::jltma_merged_addons_array();
			ksort($jltma_all_addons);

			foreach ($jltma_all_addons as $key => $widget) {
				if (!isset($activated_widgets[$widget['key']]) || !$activated_widgets[$widget['key']]) {
					continue;
				}

				$is_pro_widget = isset($widget['is_pro']) && $widget['is_pro'];

				// Skip pro widgets if user doesn't have premium
				if ($is_pro_widget && !$is_premium) {
					continue;
				}

				// Determine widget file path
				$widget_path = ($is_premium && $is_pro_widget) ? JLTMA_PRO_ADDONS : JLTMA_ADDONS;
				$widget_file = $widget_path . $widget['key'] . '/' . $widget['key'] . '.php';

				if (file_exists($widget_file)) {
					require_once $widget_file;
					$class_name = $widget['class'];
					$widget_manager->register(new $class_name);
				}
			}
		}




		public function jltma_load_extensions()
		{
			$activated_extensions = self::activated_extensions();
			$is_premium = ma_el_fs()->can_use_premium_code__premium_only();

			// Network Check
			if (defined('JLTMA_NETWORK_ACTIVATED') && JLTMA_NETWORK_ACTIVATED) {
				global $wpdb;
				$blogs = $wpdb->get_results("
				    SELECT blog_id
				    FROM {$wpdb->blogs}
				    WHERE site_id = '{$wpdb->siteid}'
				    AND spam = '0'
				    AND deleted = '0'
				    AND archived = '0'
				");
				$original_blog_id = get_current_blog_id();

				foreach ($blogs as $blog_id) {
					switch_to_blog($blog_id->blog_id);
					$this->register_extensions($activated_extensions, $is_premium);
				}
				switch_to_blog($original_blog_id);
			} else {
				$this->register_extensions($activated_extensions, $is_premium);
			}
		}

		private function register_extensions($activated_extensions, $is_premium)
		{
			ksort(JLTMA_Addon_Extensions::$jltma_extensions['jltma-extensions']['extension']);

			foreach (JLTMA_Addon_Extensions::$jltma_extensions['jltma-extensions']['extension'] as $extension) {
				if (!isset($activated_extensions[$extension['key']]) || !$activated_extensions[$extension['key']]) {
					continue;
				}

				$is_pro_extension = isset($extension['is_pro']) && $extension['is_pro'];

				// Skip pro extensions if user doesn't have premium
				if ($is_pro_extension && !$is_premium) {
					continue;
				}

				// Determine extension file path
				$extension_path = ($is_premium && $is_pro_extension) ? JLTMA_PRO_EXTENSIONS : JLTMA_PATH . 'inc/modules/';
				$extension_file = $extension_path . $extension['key'] . '/' . $extension['key'] . '.php';

				if (file_exists($extension_file)) {
					require_once $extension_file;
				}
			}
		}

		public function jltma_editor_scripts_enqueue_js()
		{

			wp_enqueue_script('ma-el-rellaxjs-lib', JLTMA_URL . '/assets/vendor/rellax/rellax.min.js', array('jquery'), self::VERSION, true);
		}

		public function jltma_editor_scripts_css()
		{
			wp_enqueue_style('master-addons-editor', JLTMA_URL . '/assets/css/master-addons-editor.css');
		}




		public function is_elementor_activated($plugin_path = 'elementor/elementor.php')
		{
			$installed_plugins_list = get_plugins();

			return isset($installed_plugins_list[$plugin_path]);
		}


		/*
		 * Activation Plugin redirect hook
		 */
		public function jltma_add_redirect_hook()
		{
			if (is_plugin_active('elementor/elementor.php')) {
				if (get_option('ma_el_update_redirect', false)) {
					delete_option('ma_el_update_redirect');
					delete_transient('ma_el_update_redirect');
					if (!isset($_GET['activate-multi']) && $this->is_elementor_activated()) {
						wp_redirect('admin.php?page=master-addons-settings');
						exit;
					}
				}
			}
		}


		public function plugin_action_links($links) {

			$links['settings'] = apply_filters(
				'jltma_settings_link',
				sprintf('<a class="master-addons-settings" href="%1$s">%2$s</a>', admin_url('admin.php?page=master-addons-settings'), __('Settings', 'master-addons'))
			);

			return apply_filters('master_addons/plugin_links', $links);
		}


		// Include Files
		public function jltma_include_files()
		{

				// Helper Class (must be loaded before Freemius_Hooks which depends on it)
				include_once JLTMA_PATH . 'inc/classes/helper-class.php';

				// Freemius Hooks
				include_once JLTMA_PATH . 'inc/classes/Freemius_Hooks.php';

				// Base Class
				// include_once JLTMA_PATH . 'inc/classes/Base/Base.php';

				// Assets Manager
				include_once JLTMA_PATH . 'inc/classes/assets-manager.php';

				// Templates Control Class
				include_once JLTMA_PATH . 'inc/classes/template-controls.php';

				//Reset Theme Styles
				include_once JLTMA_PATH . 'inc/classes/class-reset-themes.php';

				// Dashboard Settings
				include_once JLTMA_PATH . 'inc/admin/dashboard-settings.php';

				// Promote Pro Addons
				include_once JLTMA_PATH . 'inc/admin/promote-pro-addons.php';
				Promote_Pro_Addons::get_instance();

				// Page Importer
				include_once JLTMA_PATH . 'inc/admin/class-jltma-page-importer.php';

				// Master Addons Demo Importer (Standalone System)
				include_once JLTMA_PATH . 'inc/classes/importer/class-jltma-templates-importer.php';
				include_once JLTMA_PATH . 'inc/classes/importer/class-jltma-demo-importer.php';


				// Theme Builder
				include_once JLTMA_PATH . 'inc/admin/theme-builder/theme-builder.php';

				//Utils
				include_once JLTMA_PATH . 'inc/classes/utils.php';

				//Rollback
				include_once JLTMA_PATH . 'inc/classes/rollback.php';

				// Template Conditions Upgrader
				include_once JLTMA_PATH . 'inc/classes/Upgrades/Template_Conditions_Upgrader.php';

				// Templates
				require_once JLTMA_PATH . 'inc/templates/templates.php';

				// Extensions
				require_once JLTMA_PATH . 'inc/classes/JLTMA_Extension_Prototype.php';

				// Widget Builder
				// require_once JLTMA_PATH . 'inc/admin/widget-builder/widget-builder.php';
				// require_once JLTMA_PATH . 'inc/admin/widget-builder/init.php';

				// Extensions
				require_once JLTMA_PATH . 'inc/classes/Animation.php';

				// Traits: Global Controls
				require_once JLTMA_PATH . 'inc/traits/swiper-controls.php';
				include_once JLTMA_PATH . 'inc/traits/widget-notice.php';

				// Recommeded Plugins
				// require_once JLTMA_PATH . 'lib/Recommended.php';
				// require_once JLTMA_PATH . 'inc/classes/Recommended_Plugins.php';

				// Notifications
				require_once JLTMA_PATH . 'inc/classes/Notifications/Base/Date.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/Base/Data.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/Base/User_Data.php';

				require_once JLTMA_PATH . 'inc/classes/Notifications/Model/Notification.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/Model/Notice.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/Model/Popup.php';


				require_once JLTMA_PATH . 'inc/classes/Notifications/Latest_Updates.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/Ask_For_Rating.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/Subscribe.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/What_We_Collect.php';
				require_once JLTMA_PATH . 'inc/classes/Pro_Upgrade.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/Upgrade_Notice.php';
				// require_once JLTMA_PATH . 'inc/classes/Notifications/New_Features_Notice.php';


			// Load notification traits - Pro version takes priority if it loaded first
			// If Pro is active, it will have already loaded these from its /inc/ directory
			if(!trait_exists('MasterAddons\Inc\Classes\Notifications\Base\User_Data')){
				require_once JLTMA_PATH . 'inc/classes/Notifications/Base/Date.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/Base/Data.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/Base/User_Data.php';
			}

			// Load Feedback and Pro_Upgrade classes - Pro version takes priority
			// If Pro is active, these will already be loaded from Pro's /inc/ directory
			if(!class_exists('MasterAddons\Inc\Classes\Feedback')){
				require_once JLTMA_PATH . 'inc/classes/Feedback.php';
			}
			if(!class_exists('MasterAddons\Inc\Classes\Pro_Upgrade')){
				require_once JLTMA_PATH . 'inc/classes/Pro_Upgrade.php';
			}

			if(ma_el_fs()->is_free_plan() ){
				require_once JLTMA_PATH . 'inc/classes/Notifications/Pro_Sale_Notice.php';
			}

			if(!Master_Addons_Helper::jltma_premium()){
				require_once JLTMA_PATH . 'inc/classes/Notifications/Manager.php';
				require_once JLTMA_PATH . 'inc/classes/Notifications/Notifications.php';
				require_once JLTMA_PATH . 'lib/Featured.php';
			}

			// Instantiate shared classes only if not already instantiated by Pro
			// Pro plugin instantiates these in load_shared_files_from_pro()
			// Free only instantiates if Pro hasn't done so
			if (!Master_Addons_Helper::jltma_premium()) {
				// Free version only - Pro not active, safe to instantiate
				if (class_exists('MasterAddons\Inc\Classes\Feedback')) {
					static $feedback_instance;
					if (!$feedback_instance) {
						$feedback_instance = new Feedback();
					}
				}

				if (class_exists('MasterAddons\Inc\Classes\Pro_Upgrade')) {
					static $pro_upgrade_instance;
					if (!$pro_upgrade_instance) {
						$pro_upgrade_instance = new Pro_Upgrade();
					}
				}
			}
		}


		public function jltma_body_class($classes)
		{
			global $pagenow;

			if (in_array($pagenow, ['post.php', 'post-new.php'], true) && \Elementor\Utils::is_post_support()) {
				$post = get_post();

				$mode_class = \Elementor\Plugin::$instance->db->is_built_with_elementor($post->ID) ? 'elementor-editor-active' : 'elementor-editor-inactive master-addons';

				$classes .= ' ' . $mode_class;
			}

			return $classes;
		}


		public function get_localize_settings()
		{
			return $this->_localize_settings;
		}

		public function add_localize_settings($setting_key, $setting_value = null)
		{
			if (is_array($setting_key)) {
				$this->_localize_settings = array_replace_recursive($this->_localize_settings, $setting_key);

				return;
			}

			if (!is_array($setting_value) || !isset($this->_localize_settings[$setting_key]) || !is_array($this->_localize_settings[$setting_key])) {
				$this->_localize_settings[$setting_key] = $setting_value;

				return;
			}

			$this->_localize_settings[$setting_key] = array_replace_recursive($this->_localize_settings[$setting_key], $setting_value);
		}



	/**
	 * Check if running as premium-only version (master-addons-pro)
	 *
	 * @return bool
	 */
	public function is_premium_only_version()
	{
		// Check if the plugin basename contains 'master-addons-pro'
		return (strpos(JLTMA_BASE, 'master-addons-pro/') === 0);
	}



	/**
	 * AJAX handler for plugin install/activate
	 */
	public function jltma_ajax_plugin_action()
	{
		// Verify nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'jltma_plugin_action')) {
			wp_send_json_error(__('Security check failed.', 'master-addons'));
		}

		$plugin_action = isset($_POST['plugin_action']) ? sanitize_text_field($_POST['plugin_action']) : '';
		$plugin_slug = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : '';

		if (empty($plugin_action) || empty($plugin_slug)) {
			wp_send_json_error(__('Invalid request.', 'master-addons'));
		}

		// Plugin file mapping
		$plugin_files = [
			'elementor' => 'elementor/elementor.php',
			'master-addons' => 'master-addons/master-addons.php',
		];

		$plugin_file = isset($plugin_files[$plugin_slug]) ? $plugin_files[$plugin_slug] : $plugin_slug . '/' . $plugin_slug . '.php';

		if ($plugin_action === 'install') {
			// Check permission
			if (!current_user_can('install_plugins')) {
				wp_send_json_error(__('You do not have permission to install plugins.', 'master-addons'));
			}

			// Include required files
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
			require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

			// Get plugin info from WordPress.org
			$api = plugins_api('plugin_information', [
				'slug' => $plugin_slug,
				'fields' => [
					'short_description' => false,
					'sections' => false,
					'requires' => false,
					'rating' => false,
					'ratings' => false,
					'downloaded' => false,
					'last_updated' => false,
					'added' => false,
					'tags' => false,
					'compatibility' => false,
					'homepage' => false,
					'donate_link' => false,
				],
			]);

			if (is_wp_error($api)) {
				wp_send_json_error($api->get_error_message());
			}

			// Install plugin
			$skin = new \WP_Ajax_Upgrader_Skin();
			$upgrader = new \Plugin_Upgrader($skin);
			$result = $upgrader->install($api->download_link);

			if (is_wp_error($result)) {
				wp_send_json_error($result->get_error_message());
			}

			if ($result === false) {
				wp_send_json_error(__('Plugin installation failed.', 'master-addons'));
			}

			wp_send_json_success([
				'message' => __('Plugin installed successfully!', 'master-addons'),
				'next_action' => 'activate',
			]);

		} elseif ($plugin_action === 'activate') {
			// Check permission
			if (!current_user_can('activate_plugins')) {
				wp_send_json_error(__('You do not have permission to activate plugins.', 'master-addons'));
			}

			// Include plugin functions
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			// Check if plugin exists
			if (!file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
				wp_send_json_error(__('Plugin file not found.', 'master-addons'));
			}

			// Activate plugin
			$result = activate_plugin($plugin_file);

			if (is_wp_error($result)) {
				wp_send_json_error($result->get_error_message());
			}

			wp_send_json_success([
				'message' => __('Plugin activated successfully!', 'master-addons'),
				'next_action' => 'done',
			]);
		}

		wp_send_json_error(__('Invalid action.', 'master-addons'));
	}

	public function jltma_admin_notice_missing_main_plugin()
	{
		$plugin = 'elementor/elementor.php';

		if ($this->is_elementor_activated()) {
			if (!current_user_can('activate_plugins')) {
				return;
			}
			$title = __('Elementor is Not Activated', 'master-addons');
			$message = __('Master Addons requires Elementor plugin to be active. Please activate Elementor to continue.', 'master-addons');
			$button_text = __('Activate Elementor', 'master-addons');
			$notice_type = 'success';
			$ajax_action = 'activate';
		} else {
			if (!current_user_can('install_plugins')) {
				return;
			}
			$title = __('Elementor is Not Installed', 'master-addons');
			$message = __('Master Addons requires Elementor plugin to be installed and activated. Please install Elementor to continue.', 'master-addons');
			$button_text = __('Install Elementor', 'master-addons');
			$notice_type = 'warning';
			$ajax_action = 'install';
		}

		$this->jltma_render_ajax_plugin_notice($title, $message, $button_text, $notice_type, 'elementor', $ajax_action);
	}

	public function jltma_admin_notice_minimum_elementor_version()
	{
		if (isset($_GET['activate'])) {
			unset($_GET['activate']);
		}

		$title = __('Elementor Version Update Required', 'master-addons');
		$message = sprintf(
			/* translators: 1: Required Elementor version */
			__('Master Addons requires Elementor version %s or greater. Please update Elementor to continue.', 'master-addons'),
			self::MINIMUM_ELEMENTOR_VERSION
		);

		$this->jltma_render_elementor_style_notice($title, $message, '', '', 'warning');
	}

	public function jltma_admin_notice_minimum_php_version()
	{
		if (isset($_GET['activate'])) {
			unset($_GET['activate']);
		}

		$title = __('PHP Version Update Required', 'master-addons');
		$message = sprintf(
			/* translators: 1: Required PHP version */
			__('Master Addons requires PHP version %s or greater. Please contact your hosting provider to upgrade PHP.', 'master-addons'),
			self::MINIMUM_PHP_VERSION
		);

		$this->jltma_render_elementor_style_notice($title, $message, '', '', 'error');
	}

	/**
	 * Render Elementor-style admin notice
	 *
	 * @param string $title Notice title
	 * @param string $message Notice message
	 * @param string $button_url Optional button URL
	 * @param string $button_text Optional button text
	 * @param string $type Notice type: 'info', 'warning', 'error', 'success'
	 */
	private function jltma_render_elementor_style_notice($title, $message, $button_url = '', $button_text = '', $type = 'info')
	{
		?>
		<style>
		.jltma-notice{--jltma-notice-color:#5046e5;--jltma-notice-color-dark:#3f35c5;--jltma-notice-tint:#eeedfc;position:relative;display:flex;font-family:Roboto,Arial,Helvetica,sans-serif;background:#fff;border:1px solid #ccd0d4;border-left-width:4px;box-shadow:0 1px 4px rgba(0,0,0,.15);margin:5px 0 15px 0;padding:0;padding-right:30px;clear:both}
		.jltma-notice--jltma-warning{--jltma-notice-color:#f0a93b;--jltma-notice-color-dark:#d89a2f;--jltma-notice-tint:#fef8ee}
		.jltma-notice--jltma-error{--jltma-notice-color:#d63638;--jltma-notice-color-dark:#b32d2e;--jltma-notice-tint:#fcf0f1}
		.jltma-notice--jltma-success{--jltma-notice-color:#00a32a;--jltma-notice-color-dark:#008a20;--jltma-notice-tint:#edfaef}
		.jltma-notice::before{display:block;content:"";position:absolute;left:-4px;top:-1px;bottom:-1px;width:4px;background-color:var(--jltma-notice-color)}
		.jltma-notice__aside{overflow:hidden;background-color:var(--jltma-notice-tint);width:50px;text-align:center;padding-top:15px;flex-grow:0;flex-shrink:0}
		.jltma-notice__icon{display:inline-block;width:24px;height:24px;line-height:24px;border-radius:50%;overflow:hidden}
		.jltma-notice__icon img{width:24px;height:24px;border-radius:50%}
		.jltma-notice__content{padding:20px;flex:1}
		.jltma-notice__content h3{font-size:1.0625rem;font-weight:600;line-height:1.2;margin:0;color:#1e1e1e}
		.jltma-notice__content p{font-size:13px;font-weight:400;line-height:1.4;margin:8px 0 0 0;padding:0;color:#50575e}
		.jltma-notice__actions{display:flex;margin-top:1rem}
		.jltma-notice__actions>*+*{margin-left:8px}
		.jltma-notice__btn{display:inline-block;padding:8px 16px;font-size:13px;font-weight:500;line-height:1;text-decoration:none;color:#fff;background-color:var(--jltma-notice-color);border:none;border-radius:3px;cursor:pointer;transition:background-color .2s ease}
		.jltma-notice__btn:hover,.jltma-notice__btn:focus{background-color:var(--jltma-notice-color-dark);color:#fff;outline:none;box-shadow:none}
		.jltma-notice__dismiss{position:absolute;top:10px;right:10px;width:20px;height:20px;padding:0;background:none;border:none;cursor:pointer;line-height:1}
		.jltma-notice__dismiss::before{font-family:dashicons;content:"\f335";font-size:20px;color:#787c82;font-weight:400}
		.jltma-notice__dismiss:hover::before{color:#d63638}
		.jltma-notice__dismiss:focus{outline:none}
		</style>
		<div class="jltma-notice<?php echo $type !== 'info' ? ' jltma-notice--jltma-' . esc_attr($type) : ''; ?>">
			<div class="jltma-notice__aside">
				<div class="jltma-notice__icon">
					<img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'logo.svg'); ?>" alt="Master Addons">
				</div>
			</div>
			<div class="jltma-notice__content">
				<h3><?php echo esc_html($title); ?></h3>
				<p><?php echo esc_html($message); ?></p>
				<?php if (!empty($button_url) && !empty($button_text)) : ?>
					<div class="jltma-notice__actions">
						<a href="<?php echo esc_url($button_url); ?>" class="jltma-notice__btn"><?php echo esc_html($button_text); ?></a>
					</div>
				<?php endif; ?>
			</div>
			<button type="button" class="jltma-notice__dismiss" onclick="this.parentElement.remove();"></button>
		</div>
		<?php
	}

	/**
	 * Render AJAX-enabled plugin install/activate notice
	 *
	 * @param string $title Notice title
	 * @param string $message Notice message
	 * @param string $button_text Button text
	 * @param string $type Notice type
	 * @param string $plugin_slug Plugin slug (e.g., 'elementor')
	 * @param string $action 'install' or 'activate'
	 */
	private function jltma_render_ajax_plugin_notice($title, $message, $button_text, $type, $plugin_slug, $action)
	{
		$nonce = wp_create_nonce('jltma_plugin_action');
		?>
		<style>
		.jltma-notice{--jltma-notice-color:#5046e5;--jltma-notice-color-dark:#3f35c5;--jltma-notice-tint:#eeedfc;position:relative;display:flex;font-family:Roboto,Arial,Helvetica,sans-serif;background:#fff;border:1px solid #ccd0d4;border-left-width:4px;box-shadow:0 1px 4px rgba(0,0,0,.15);margin:5px 0 15px 0;padding:0;padding-right:30px;clear:both}
		.jltma-notice--jltma-warning{--jltma-notice-color:#f0a93b;--jltma-notice-color-dark:#d89a2f;--jltma-notice-tint:#fef8ee}
		.jltma-notice--jltma-error{--jltma-notice-color:#d63638;--jltma-notice-color-dark:#b32d2e;--jltma-notice-tint:#fcf0f1}
		.jltma-notice--jltma-success{--jltma-notice-color:#00a32a;--jltma-notice-color-dark:#008a20;--jltma-notice-tint:#edfaef}
		.jltma-notice::before{display:block;content:"";position:absolute;left:-4px;top:-1px;bottom:-1px;width:4px;background-color:var(--jltma-notice-color)}
		.jltma-notice__aside{overflow:hidden;background-color:var(--jltma-notice-tint);width:50px;text-align:center;padding-top:15px;flex-grow:0;flex-shrink:0}
		.jltma-notice__icon{display:inline-block;width:24px;height:24px;line-height:24px;border-radius:50%;overflow:hidden}
		.jltma-notice__icon img{width:24px;height:24px;border-radius:50%}
		.jltma-notice__content{padding:20px;flex:1}
		.jltma-notice__content h3{font-size:1.0625rem;font-weight:600;line-height:1.2;margin:0;color:#1e1e1e}
		.jltma-notice__content p{font-size:13px;font-weight:400;line-height:1.4;margin:8px 0 0 0;padding:0;color:#50575e}
		.jltma-notice__actions{display:flex;align-items:center;margin-top:1rem;gap:10px}
		.jltma-notice__btn{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;font-size:13px;font-weight:500;line-height:1;text-decoration:none;color:#fff;background-color:var(--jltma-notice-color);border:none;border-radius:3px;cursor:pointer;transition:all .2s ease}
		.jltma-notice__btn:hover,.jltma-notice__btn:focus{background-color:var(--jltma-notice-color-dark);color:#fff;outline:none;box-shadow:none}
		.jltma-notice__btn:disabled{opacity:0.7;cursor:not-allowed}
		.jltma-notice__btn .jltma-spinner{display:none;width:14px;height:14px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:jltma-spin 0.8s linear infinite}
		.jltma-notice__btn.is-loading .jltma-spinner{display:inline-block}
		.jltma-notice__btn.is-loading .jltma-btn-text{opacity:0.8}
		.jltma-notice__status{font-size:13px;color:#50575e}
		.jltma-notice__status.success{color:#00a32a}
		.jltma-notice__status.error{color:#d63638}
		@keyframes jltma-spin{to{transform:rotate(360deg)}}
		.jltma-notice__dismiss{position:absolute;top:10px;right:10px;width:20px;height:20px;padding:0;background:none;border:none;cursor:pointer;line-height:1}
		.jltma-notice__dismiss::before{font-family:dashicons;content:"\f335";font-size:20px;color:#787c82;font-weight:400}
		.jltma-notice__dismiss:hover::before{color:#d63638}
		.jltma-notice__dismiss:focus{outline:none}
		</style>
		<div class="jltma-notice<?php echo $type !== 'info' ? ' jltma-notice--jltma-' . esc_attr($type) : ''; ?>" id="jltma-plugin-notice">
			<div class="jltma-notice__aside">
				<div class="jltma-notice__icon">
					<img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'logo.svg'); ?>" alt="Master Addons">
				</div>
			</div>
			<div class="jltma-notice__content">
				<h3><?php echo esc_html($title); ?></h3>
				<p><?php echo esc_html($message); ?></p>
				<div class="jltma-notice__actions">
					<button type="button"
						class="jltma-notice__btn jltma-ajax-plugin-btn"
						data-plugin="<?php echo esc_attr($plugin_slug); ?>"
						data-action="<?php echo esc_attr($action); ?>"
						data-nonce="<?php echo esc_attr($nonce); ?>">
						<span class="jltma-spinner"></span>
						<span class="jltma-btn-text"><?php echo esc_html($button_text); ?></span>
					</button>
					<span class="jltma-notice__status"></span>
				</div>
			</div>
			<button type="button" class="jltma-notice__dismiss" onclick="this.parentElement.remove();"></button>
		</div>
		<script>
		(function() {
			var btn = document.querySelector('.jltma-ajax-plugin-btn');
			if (!btn) return;

			btn.addEventListener('click', function(e) {
				e.preventDefault();

				var button = this;
				var notice = document.getElementById('jltma-plugin-notice');
				var statusEl = notice.querySelector('.jltma-notice__status');
				var plugin = button.dataset.plugin;
				var action = button.dataset.action;
				var nonce = button.dataset.nonce;
				var btnText = button.querySelector('.jltma-btn-text');
				var originalText = btnText.textContent;

				// Disable button and show loading
				button.disabled = true;
				button.classList.add('is-loading');
				statusEl.textContent = '';
				statusEl.className = 'jltma-notice__status';

				if (action === 'install') {
					btnText.textContent = '<?php echo esc_js(__('Installing...', 'master-addons')); ?>';
				} else {
					btnText.textContent = '<?php echo esc_js(__('Activating...', 'master-addons')); ?>';
				}

				// Make AJAX request
				var formData = new FormData();
				formData.append('action', 'jltma_plugin_action');
				formData.append('plugin_action', action);
				formData.append('plugin', plugin);
				formData.append('nonce', nonce);

				fetch(ajaxurl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin'
				})
				.then(function(response) {
					return response.json();
				})
				.then(function(data) {
					button.classList.remove('is-loading');

					if (data.success) {
						statusEl.textContent = data.data.message;
						statusEl.classList.add('success');

						// If installed, now activate
						if (action === 'install' && data.data.next_action === 'activate') {
							button.dataset.action = 'activate';
							btnText.textContent = '<?php echo esc_js(__('Activate Elementor', 'master-addons')); ?>';
							button.disabled = false;
							statusEl.textContent = '<?php echo esc_js(__('Installed! Click to activate.', 'master-addons')); ?>';
						} else {
							// All done, reload page
							btnText.textContent = '<?php echo esc_js(__('Reloading...', 'master-addons')); ?>';
							setTimeout(function() {
								window.location.reload();
							}, 1000);
						}
					} else {
						statusEl.textContent = data.data || '<?php echo esc_js(__('An error occurred.', 'master-addons')); ?>';
						statusEl.classList.add('error');
						btnText.textContent = originalText;
						button.disabled = false;
					}
				})
				.catch(function(error) {
					button.classList.remove('is-loading');
					statusEl.textContent = '<?php echo esc_js(__('Connection error. Please try again.', 'master-addons')); ?>';
					statusEl.classList.add('error');
					btnText.textContent = originalText;
					button.disabled = false;
				});
			});
		})();
		</script>
		<?php
	}

		// Add this method to load the textdomain properly
		public function load_textdomain() {
			load_plugin_textdomain('master-addons', false, dirname(plugin_basename(__FILE__)) . '/languages/');
		}

		// Check if Master Addons Pro is activated
	public function is_master_addons_pro_activated($plugin_path = 'master-addons-pro/master-addons.php')
		{
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			return is_plugin_active($plugin_path);
		}
	}
}
