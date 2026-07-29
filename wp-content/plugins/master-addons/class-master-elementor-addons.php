<?php

namespace MasterAddons;

use MasterAddons\Inc\Admin\Config;
use MasterAddons\Inc\Admin\REST_API;
use MasterAddons\Inc\Admin\Settings\Settings;
use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Classes\Feedback;
use MasterAddons\Inc\Classes\Freemius_Hooks;
use MasterAddons\Inc\Classes\Pro_Upgrade;
use MasterAddons\Inc\Classes\Recommended_Plugins;
use MasterAddons\Inc\Classes\Utils;
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
		const MINIMUM_PRO_VERSION = '3.0.0';

		// Changed from private to protected to allow Pro class extension
		protected $_localize_settings = [];
		protected $reflection;
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

			// Register deprecated Elementor class stubs to prevent fatal errors
			// from third-party themes/plugins using removed Scheme_Color/Scheme_Typography
			$this->jltma_register_elementor_compat_stubs();

			$this->jltma_include_files();

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

			// Keep paged requests alive for widgets that paginate on singular pages
			add_filter('pre_handle_404', [$this, 'jltma_allow_widget_pagination'], 10, 2);

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

			// Run pending upgrade migrations (e.g. legacy option key migration)
			add_action('admin_init', [$this, 'jltma_maybe_run_upgrades'], 5);
		}

		/**
		 * Stops WordPress from turning a paged singular request into a 404.
		 *
		 * WP::handle_404() only accepts the "page" query var when the post content is split
		 * with <!--nextpage-->, so pagination rendered by a widget is answered with a 404 and
		 * then canonically redirected back to page 1. Allow the request when the document
		 * really does contain a widget that paginates that far.
		 *
		 * @see \WP::handle_404()
		 */
		public function jltma_allow_widget_pagination($handled, $wp_query)
		{
			if (false !== $handled || empty($wp_query->query_vars['page']) || !is_singular() || empty($wp_query->post)) {
				return $handled;
			}

			if (!did_action('elementor/loaded') || !class_exists('\Elementor\Plugin')) {
				return $handled;
			}

			$page = (int) trim($wp_query->query_vars['page'], '/');

			if ($page < 2) {
				return $handled;
			}

			$document = \Elementor\Plugin::$instance->documents->get($wp_query->post->ID);

			if (!$document) {
				return $handled;
			}

			$max_pages = $this->jltma_get_widget_max_pages($document->get_elements_data());

			return ($page <= $max_pages) ? true : $handled;
		}

		/**
		 * Highest pagination page any paginating widget in the document can render.
		 */
		private function jltma_get_widget_max_pages($elements)
		{
			$max_pages = 0;

			foreach ((array) $elements as $element) {

				if (!empty($element['elements'])) {
					$max_pages = max($max_pages, $this->jltma_get_widget_max_pages($element['elements']));
				}

				if (empty($element['widgetType']) || 'ma-blog-post' !== $element['widgetType']) {
					continue;
				}

				$settings = $element['settings'] ?? [];

				if ('yes' !== ($settings['ma_el_blog_pagination'] ?? '') || 'yes' === ($settings['ma_el_blog_carousel'] ?? '')) {
					continue;
				}

				$max_pages = max($max_pages, Helper::jltma_el_blog_get_total_pages($settings));
			}

			return $max_pages;
		}

		/**
		 * Run pending upgrade scripts if the stored version is older than the current version.
		 */
		public function jltma_maybe_run_upgrades()
		{
			$upgrades = new \MasterAddons\Inc\Classes\Upgrades();
			if ($upgrades->if_updates_available()) {
				$upgrades->run_updates();
			}
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

			$jltma_white_label_setting 	= Utils::get_options('jltma_white_label_settings') ?? [];
			if( !empty($jltma_white_label_setting) && isset($jltma_white_label_setting['jltma_wl_plugin_tab_white_label']) ) {
				$jltma_white_label_setting['jltma_wl_plugin_tab_white_label'] = 0;
				update_option( 'jltma_white_label_settings', $jltma_white_label_setting );
			}

			// Only auto-redirect to setup wizard for fresh installs or users already on 3.0.0+
			// Old users upgrading from < 3.0.0 should NOT see the auto-redirect
			$should_redirect = false;
			if ( is_null( $current_version ) ) {
				// Fresh install — show wizard
				$should_redirect = true;
			} elseif ( version_compare( $current_version, '3.0.0', '>=' ) ) {
				// Re-activation of 3.0.0+ — show wizard if not completed
				$should_redirect = true;
			}

			if ( $should_redirect ) {
				set_transient( '_master_addons_activation_redirect', true, 30 );
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
		/**
		 * Register stub classes for deprecated Elementor Scheme_Color / Scheme_Typography.
		 * Prevents fatal errors from third-party themes/plugins that still use them
		 * (e.g. Brooklyn Lite, themes built for older Elementor).
		 */
		private function jltma_register_elementor_compat_stubs() {
			\MasterAddons\Inc\Classes\Elementor_Compat::init();
		}

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

		/**
		 * Register autoloader
		 *
		 * @since 2.1.0
		 */
		public function jltma_register_autoloader()
		{
			spl_autoload_register([$this, 'jltma_autoload']);
		}

		/**
		 * Autoload classes, traits, and interfaces
		 *
		 * Converts namespace to file path (WordPress style: kebab-case):
		 * - MasterAddons\Inc\Classes\Cache_Manager → inc/classes/cache-manager.php
		 * - MasterAddons\Inc\Traits\Swiper_Controls → inc/traits/swiper-controls.php
		 * - MasterAddons\Addons\MA_Accordion → addons/ma-accordion.php
		 *
		 * @param string $class Fully qualified class name
		 */
		public function jltma_autoload($class)
		{
			// Only handle MasterAddons namespace
			if (0 !== strpos($class, __NAMESPACE__)) {
				return;
			}

			// Skip if already loaded
			if (class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false)) {
				return;
			}

			// Get relative class name (without MasterAddons\ prefix)
			$relative_class = str_replace(__NAMESPACE__ . '\\', '', $class);

			// Handle Pro\Modules\{Category}\{Class} pattern
			if (preg_match('/^Pro\\\\Modules\\\\([^\\\\]+)\\\\([^\\\\]+)$/', $relative_class, $matches)) {
				$category = $this->to_kebab_case($matches[1]);
				$class_name = $this->to_kebab_case($matches[2]);
				$file = JLTMA_PATH . "premium/modules/{$category}/{$class_name}/{$class_name}.php";

				if (file_exists($file)) {
					require_once $file;
					return;
				}
			}

			// Handle Modules\{Category}\{Class} pattern (free)
			if (preg_match('/^Modules\\\\([^\\\\]+)\\\\([^\\\\]+)$/', $relative_class, $matches)) {
				$category = $this->to_kebab_case($matches[1]);
				$class_name = $this->to_kebab_case($matches[2]);
				$file = JLTMA_PATH . "inc/modules/{$category}/{$class_name}/{$class_name}.php";

				if (file_exists($file)) {
					require_once $file;
					return;
				}
			}

			// Convert namespace to file path (kebab-case)
			$file_name = strtolower(
				preg_replace(
					['/([a-z])([A-Z])/', '/_/', '/\\\\/'],
					['$1-$2', '-', '/'],
					$relative_class
				)
			);

			// Inc\Classes folder
			if (0 === strpos($class, __NAMESPACE__ . '\\Inc\\Classes\\')) {
				$file = JLTMA_PATH . $file_name . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
			}

			// Inc\Controls folder
			if (0 === strpos($class, __NAMESPACE__ . '\\Inc\\Controls\\')) {
				$file = JLTMA_PATH . $file_name . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
			}

			// Inc\Modules folder
			if (0 === strpos($class, __NAMESPACE__ . '\\Inc\\Modules\\')) {
				$file = JLTMA_PATH . $file_name . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
			}

			// Inc\Admin\Templates folder (directory: inc/admin/templates/)
			// NOTE: Must come BEFORE general Inc\Admin check
			if (0 === strpos($class, __NAMESPACE__ . '\\Inc\\Admin\\Templates\\')) {
				$templates_class = str_replace(__NAMESPACE__ . '\\Inc\\Admin\\Templates\\', '', $class);

				// Handle Kits subnamespace
				if (0 === strpos($templates_class, 'Kits\\')) {
					$kits_class = str_replace('Kits\\', '', $templates_class);
					$kits_file_name = strtolower(preg_replace(['/([a-z])([A-Z])/', '/_/'], ['$1-$2', '-'], $kits_class));
					$file = JLTMA_PATH . 'inc/admin/templates/kits/class-' . $kits_file_name . '.php';
					if (is_readable($file)) {
						include_once $file;
						return;
					}
				}

				// Handle Includes subnamespace (types, classes, sources, documents)
				if (0 === strpos($templates_class, 'Includes\\')) {
					$includes_class = str_replace('Includes\\', '', $templates_class);
					$includes_file_name = strtolower(
						preg_replace(
							['/([a-z])([A-Z])/', '/_/', '/\\\\/'],
							['$1-$2', '-', '/'],
							$includes_class
						)
					);
					$file = JLTMA_PATH . 'inc/admin/templates/includes/' . $includes_file_name . '.php';
					if (is_readable($file)) {
						include_once $file;
						return;
					}
				}

				$templates_file_name = strtolower(
					preg_replace(
						['/([a-z])([A-Z])/', '/_/', '/\\\\/'],
						['$1-$2', '-', '/'],
						$templates_class
					)
				);
				$file = JLTMA_PATH . 'inc/admin/templates/' . $templates_file_name . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
			}

			// Inc\Admin\Theme_Builder folder (directory: inc/admin/theme-builder/)
			// NOTE: Must come BEFORE general Inc\Admin check
			if (0 === strpos($class, __NAMESPACE__ . '\\Inc\\Admin\\Theme_Builder\\')) {
				$tb_class = str_replace(__NAMESPACE__ . '\\Inc\\Admin\\Theme_Builder\\', '', $class);

				// Handle subnamespaces: Api, Comments, Hooks
				if (0 === strpos($tb_class, 'Api\\')) {
					$api_class = str_replace('Api\\', '', $tb_class);
					$api_file_name = strtolower(preg_replace(['/([a-z])([A-Z])/', '/_/'], ['$1-$2', '-'], $api_class));
					$file = JLTMA_PATH . 'inc/admin/theme-builder/inc/api/' . $api_file_name . '.php';
					if (is_readable($file)) {
						include_once $file;
						return;
					}
				} elseif (0 === strpos($tb_class, 'Comments\\')) {
					$comments_class = str_replace('Comments\\', '', $tb_class);
					// Handle Addon subnamespace
					if (0 === strpos($comments_class, 'Addon\\')) {
						$addon_class = str_replace('Addon\\', '', $comments_class);
						$file = JLTMA_PATH . 'inc/admin/theme-builder/inc/comments/jltma-comments-addon.php';
					} else {
						$comments_file_name = strtolower(preg_replace(['/([a-z])([A-Z])/', '/_/'], ['$1-$2', '-'], $comments_class));
						$file = JLTMA_PATH . 'inc/admin/theme-builder/inc/comments/class-' . $comments_file_name . '.php';
					}
					if (is_readable($file)) {
						include_once $file;
						return;
					}
				} elseif (0 === strpos($tb_class, 'Theme_Hooks\\')) {
					$hooks_class = str_replace('Theme_Hooks\\', '', $tb_class);
					$hooks_file_name = strtolower(preg_replace(['/([a-z])([A-Z])/', '/_/'], ['$1-$2', '-'], $hooks_class));
					$file = JLTMA_PATH . 'inc/admin/theme-builder/inc/theme-hooks/' . $hooks_file_name . '.php';
					if (is_readable($file)) {
						include_once $file;
						return;
					}
				} else {
					// Main Theme_Builder classes
					$tb_file_name = strtolower(preg_replace(['/([a-z])([A-Z])/', '/_/'], ['$1-$2', '-'], $tb_class));
					// Try class-{name}.php format first
					$file = JLTMA_PATH . 'inc/admin/theme-builder/class-' . $tb_file_name . '.php';
					if (is_readable($file)) {
						include_once $file;
						return;
					}
					// Try inc/{name}.php format
					$file = JLTMA_PATH . 'inc/admin/theme-builder/inc/' . $tb_file_name . '.php';
					if (is_readable($file)) {
						include_once $file;
						return;
					}
					// Try specific file mappings
					$tb_file_map = [
						'Loader' => 'theme-builder.php',
						'Theme_Builder' => 'class-theme-builder.php',
						'CPT' => 'inc/cpt.php',
						'CPT_Hooks' => 'inc/cpt-hooks.php',
						'Activator' => 'inc/jltma-activator.php',
						'Assets' => 'inc/theme-builder-assets.php',
					];
					if (isset($tb_file_map[$tb_class])) {
						$file = JLTMA_PATH . 'inc/admin/theme-builder/' . $tb_file_map[$tb_class];
						if (is_readable($file)) {
							include_once $file;
							return;
						}
					}
				}
			}

			// Inc\Admin\PopupBuilder folder (directory: inc/admin/popup-builder/)
			// NOTE: Must come BEFORE general Inc\Admin check
			if (0 === strpos($class, __NAMESPACE__ . '\\Inc\\Admin\\PopupBuilder\\')) {
				$pb_class = str_replace(__NAMESPACE__ . '\\Inc\\Admin\\PopupBuilder\\', '', $class);
				$pb_file_name = strtolower(preg_replace(['/([a-z])([A-Z])/', '/_/'], ['$1-$2', '-'], $pb_class));

				$file = JLTMA_PATH . 'inc/admin/popup-builder/class-' . $pb_file_name . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
			}

			// Inc\Admin\WidgetBuilder folder (directory: inc/admin/widget-builder/)
			// NOTE: Must come BEFORE general Inc\Admin check
			if (0 === strpos($class, __NAMESPACE__ . '\\Inc\\Admin\\WidgetBuilder\\')) {
				$wb_class = str_replace(__NAMESPACE__ . '\\Inc\\Admin\\WidgetBuilder\\', '', $class);

				// Handle Controls subnamespace
				if (0 === strpos($wb_class, 'Controls\\')) {
					$control_class = str_replace('Controls\\', '', $wb_class);
					$control_file_name = strtolower(preg_replace(['/([a-z])([A-Z])/', '/_/'], ['$1-$2', '-'], $control_class));
					$file = JLTMA_PATH . 'inc/admin/widget-builder/controls/' . $control_file_name . '.php';
					if (is_readable($file)) {
						include_once $file;
						return;
					}
				}

				// File mapping for widget-builder classes
				$wb_file_map = [
					'Widget_Builder_Init' => 'class-widget-builder-init.php',
					'Widget_CPT' => 'class-widget-cpt.php',
					'Widget_Admin' => 'class-widget-admin.php',
					'Widget_Generator' => 'class-widget-generator.php',
					'REST_Controller' => 'class-rest-controller.php',
					'Shortcode_Manager' => 'class-shortcode-manager.php',
					'Control_Manager' => 'class-control-manager.php',
					'Icon_Library_Helper' => 'icon-library-helper.php',
					'Control_Base' => 'controls/class-control-base.php',
				];
				if (isset($wb_file_map[$wb_class])) {
					$file = JLTMA_PATH . 'inc/admin/widget-builder/' . $wb_file_map[$wb_class];
					if (is_readable($file)) {
						include_once $file;
						return;
					}
				}

				// Fallback: Try kebab-case filename
				$wb_file_name = strtolower(preg_replace(['/([a-z])([A-Z])/', '/_/'], ['$1-$2', '-'], $wb_class));
				$file = JLTMA_PATH . 'inc/admin/widget-builder/' . $wb_file_name . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
			}

			// Inc\Admin folder (general - must come AFTER specific Inc\Admin\* checks)
			if (0 === strpos($class, __NAMESPACE__ . '\\Inc\\Admin\\')) {
				$file = JLTMA_PATH . $file_name . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
			}

			// Inc\Traits folder
			if (0 === strpos($class, __NAMESPACE__ . '\\Inc\\Traits\\')) {
				$file = JLTMA_PATH . $file_name . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
			}

			// Lib folder (lib/*.php)
			if (0 === strpos($class, __NAMESPACE__ . '\\Lib\\')) {
				$lib_class = str_replace(__NAMESPACE__ . '\\Lib\\', '', $class);
				$lib_file_name = strtolower(
					preg_replace(
						['/([a-z])([A-Z])/', '/_/'],
						['$1-$2', '-'],
						$lib_class
					)
				);
				$file = JLTMA_PATH . 'lib/' . $lib_file_name . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
				// Try PascalCase filename (Featured.php, etc.)
				$file = JLTMA_PATH . 'lib/' . $lib_class . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
			}

			// Pro\Classes folder (premium classes: premium/classes/)
			if (0 === strpos($class, __NAMESPACE__ . '\\Pro\\Classes\\')) {
				$pro_class = str_replace(__NAMESPACE__ . '\\Pro\\Classes\\', '', $class);
				$pro_file_name = strtolower(
					preg_replace(
						['/([a-z])([A-Z])/', '/_/', '/\\\\/'],
						['$1-$2', '-', '/'],
						$pro_class
					)
				);
				$file = JLTMA_PATH . 'premium/classes/' . $pro_file_name . '.php';
				if (is_readable($file)) {
					include_once $file;
					return;
				}
			}


			// Addons folder - organized by groups/subcategories (group/subcategory/widget/)
			if (0 === strpos($class, __NAMESPACE__ . '\\Addons\\')) {
				// Get class name and convert to file name
				$class_name = str_replace(__NAMESPACE__ . '\\Addons\\', '', $class);
				$addon_file_name = strtolower(
					preg_replace(
						['/([a-z])([A-Z])/', '/_/'],
						['$1-$2', '-'],
						$class_name
					)
				);
				// Scan addons directory for group/subcategory subdirectories
				$addons_dir = JLTMA_PATH . 'addons/';
				if (is_dir($addons_dir)) {
					$groups = array_filter(glob($addons_dir . '*'), 'is_dir');
					foreach ($groups as $group_path) {
						// Check in subcategories (group/subcategory/widget/)
						$subcategories = array_filter(glob($group_path . '/*'), 'is_dir');
						foreach ($subcategories as $subcategory_path) {
							$file = $subcategory_path . '/' . $addon_file_name . '/' . $addon_file_name . '.php';
							if (is_readable($file)) {
								include_once $file;
								return;
							}
						}
					}
				}
			}

			// Modules folder - organized by groups/subcategories (group/subcategory/module/)
			if (0 === strpos($class, __NAMESPACE__ . '\\Modules\\')) {
				$class_name = str_replace(__NAMESPACE__ . '\\Modules\\', '', $class);
				$module_file_name = strtolower(
					preg_replace(
						['/([a-z])([A-Z])/', '/_/'],
						['$1-$2', '-'],
						$class_name
					)
				);
				// Remove 'extension-' prefix if present for folder lookup
				$folder_name = str_replace('extension-', '', $module_file_name);
				// Scan modules directory for group/subcategory subdirectories
				$modules_dir = JLTMA_PATH . 'inc/modules/';
				if (is_dir($modules_dir)) {
					$groups = array_filter(glob($modules_dir . '*'), 'is_dir');
					foreach ($groups as $group_path) {
						// Check in subcategories (group/subcategory/module/)
						$subcategories = array_filter(glob($group_path . '/*'), 'is_dir');
						foreach ($subcategories as $subcategory_path) {
							$file = $subcategory_path . '/' . $folder_name . '/' . $folder_name . '.php';
							if (is_readable($file)) {
								include_once $file;
								return;
							}
						}
					}
				}
			}

			// Fallback: try direct path
			$file = JLTMA_PATH . $file_name . '.php';
			if (is_readable($file)) {
				include_once $file;
			}
		}

		/**
		 * Convert PascalCase to kebab-case
		 *
		 * @param string $string PascalCase string
		 * @return string kebab-case string
		 */
		private function to_kebab_case($string)
		{
			return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $string));
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
			// get_addons() migrates from legacy key automatically.
			// Only seed defaults on a genuine first install (no new AND no legacy key).
			$data = Settings::get_addons();
			if (empty($data)) {
				$data = Settings::get_default_addon_settings();
				Settings::save_addons($data);
			}
			return $data;
		}

		// Extensions
		public static function activated_extensions()
		{
			$data = Settings::get_extensions();
			if (empty($data)) {
				$data = Settings::get_default_extension_settings();
				Settings::save_extensions($data);
			}
			return $data;
		}


		// Third Party Plugins
		public static function activated_third_party_plugins()
		{
			$data = Settings::get_plugins();
			if (empty($data)) {
				$data = Settings::get_default_plugin_settings();
				Settings::save_plugins($data);
			}
			return $data;
		}

		// Icons Library
		public static function activated_icons_library()
		{
			$data = Settings::get_icons();
			if (empty($data)) {
				$data = Settings::get_default_icon_settings();
				Settings::save_icons($data);
			}
			return $data;
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
			// Ensure all settings exist in DB (seeds defaults on first install).
			// These are no-ops when the option already exists, so the overhead is negligible.
			self::activated_third_party_plugins();
			self::activated_icons_library();

			// Load extensions (extension-prototype.php is loaded in jltma_include_files)
			$this->jltma_load_extensions();

			// Icons Extended lives outside the extensions_category groups,
			// so register_extensions() doesn't pick it up. Load it directly.
			$activated_extensions = Settings::get_extensions() ?: [];
			if (!isset($activated_extensions['icons_extended']) || $activated_extensions['icons_extended']) {
				$icons_file = JLTMA_PATH . 'inc/modules/utilities/icons-extended/icons-extended.php';
				if (file_exists($icons_file)) {
					require_once $icons_file;
				}
			}
		}

		public function jltma_register_controls($controls_manager)
		{

			$controls_manager = \Elementor\Plugin::$instance->controls_manager;

			$controls = array(
				'jltma-visual-select' => array(
					'file'  => JLTMA_PATH . 'inc/controls/visual-select.php',
					'class' => 'MasterAddons\Inc\Controls\JLTMA_Visual_Select',
					'type'  => 'single'
				),
				'jltma-transitions' => array(
					'file'  => JLTMA_PATH . 'inc/controls/group/transitions.php',
					'class' => 'MasterAddons\Inc\Controls\Group\JLTMA_Transition',
					'type'  => 'group'
				),
				'jltma-filters-hsb' => array(
					'file'  => JLTMA_PATH . 'inc/controls/group/filters-hsb.php',
					'class' => 'MasterAddons\Inc\Controls\Group\JLTMA_Filters_HSB',
					'type'  => 'group'
				),
				'jltma-button-background' => array(
					'file'  => JLTMA_PATH . 'inc/controls/group/button-background.php',
					'class' => 'MasterAddons\Inc\Controls\Group\JLTMA_Button_Background',
					'type'  => 'group'
				),
				'jltma-choose-text' => array(
					'file'  => JLTMA_PATH . 'inc/controls/choose-text.php',
					'class' => 'MasterAddons\Inc\Controls\JLTMA_Choose_Text',
					'type'  => 'single'
				),
				'jltma-file-select' => array(
					'file'  => JLTMA_PATH . 'inc/controls/file-select.php',
					'class' => 'MasterAddons\Inc\Controls\JLTMA_File_Select',
					'type'  => 'single'
				),
				'jltma_query' => array(
					'file'  => JLTMA_PATH . 'inc/controls/query.php',
					'class' => 'MasterAddons\Inc\Controls\JLTMA_Query',
					'type'  => 'single'
				),
				'jltma-template-controls' => array(
					'file'  => JLTMA_PATH . 'inc/controls/templates/template-controls.php',
					'class' => 'MasterAddons\Inc\Controls\Templates\JLTMA_Template_Controls',
					'type'  => 'template'
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
					} elseif ($control_info['type'] === 'template') {
						// Template classes are just included, not registered with Elementor
						continue;
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
			$is_premium = Helper::jltma_premium();

			// Network Check
			if (defined('NETWORK_ACTIVATED') && JLTMA_NETWORK_ACTIVATED) {
				global $wpdb;
				$blogs = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for multisite blog enumeration; result is not user-supplied
					$wpdb->prepare(
						"SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = %d AND spam = '0' AND deleted = '0' AND archived = '0'",
						$wpdb->siteid
					)
				);
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
			$widget_manager = Helper::jltma_elementor()->widgets_manager;
			$jltma_all_addons = Config::get_all_elements_for_settings();
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

				// Skip pro widgets if the pro base class isn't available
				// (e.g., pro autoloader not registered due to version mismatch)
				if ($is_pro_widget && !class_exists('\\MasterAddons\\Pro\\Classes\\Base\\Master_Widgets_Pro')) {
					continue;
				}

				// Determine widget file path (group/subcategory/widget/)
				$group = isset($widget['group']) ? $widget['group'] : '';
				$subcategory = isset($widget['subcategory']) ? $widget['subcategory'] : '';
				$widget_key = $widget['key'];
				$widget_file = null;

				// Search using group from config
				if ($group) {
					$search_paths = [];

					// Try direct path first (group/widget/) - handles flat folder structures
					$search_paths[] = JLTMA_ADDONS . $group . '/' . $widget_key . '/' . $widget_key . '.php';
					if (defined('JLTMA_PRO_ADDONS')) {
						$search_paths[] = JLTMA_PRO_ADDONS . $group . '/' . $widget_key . '/' . $widget_key . '.php';
					}

					// Then try full subcategory path if subcategory is set (group/subcategory/widget/)
					if ($subcategory && $group !== $subcategory) {
						$search_paths[] = JLTMA_ADDONS . $group . '/' . $subcategory . '/' . $widget_key . '/' . $widget_key . '.php';
						if (defined('JLTMA_PRO_ADDONS')) {
							$search_paths[] = JLTMA_PRO_ADDONS . $group . '/' . $subcategory . '/' . $widget_key . '/' . $widget_key . '.php';
						}
					}

					foreach ($search_paths as $path) {
						if (file_exists($path)) {
							$widget_file = $path;
							break;
						}
					}
				}

				// Dynamic scan if not found (search group/widget/ and group/subcat/widget/ patterns)
				if (!$widget_file) {
					$base_dirs = [JLTMA_ADDONS];
					if (defined('JLTMA_PRO_ADDONS')) {
						$base_dirs[] = JLTMA_PRO_ADDONS;
					}
					foreach ($base_dirs as $base_dir) {
						if (!is_dir($base_dir)) continue;
						$groups = array_filter(glob($base_dir . '*'), 'is_dir');
						foreach ($groups as $group_path) {
							// First try direct: group/widget/widget.php
							$direct_path = $group_path . '/' . $widget_key . '/' . $widget_key . '.php';
							if (file_exists($direct_path)) {
								$widget_file = $direct_path;
								break 2;
							}
							// Then try nested: group/subcat/widget/widget.php
							$subdirs = array_filter(glob($group_path . '/*'), 'is_dir');
							foreach ($subdirs as $subdir_path) {
								$path = $subdir_path . '/' . $widget_key . '/' . $widget_key . '.php';
								if (file_exists($path)) {
									$widget_file = $path;
									break 3;
								}
							}
						}
					}
				}

				if ($widget_file) {
					require_once $widget_file;
					$class_name = $widget['class'];
					if (class_exists($class_name)) {
						$widget_manager->register(new $class_name);
					}
				}
			}
		}




		public function jltma_load_extensions()
		{
			$activated_extensions = self::activated_extensions();
			$is_premium = Helper::jltma_premium();

			// Network Check
			if (defined('NETWORK_ACTIVATED') && JLTMA_NETWORK_ACTIVATED) {
				global $wpdb;
				$blogs = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for multisite blog enumeration; result is not user-supplied
					$wpdb->prepare(
						"SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = %d AND spam = '0' AND deleted = '0' AND archived = '0'",
						$wpdb->siteid
					)
				);
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
			$extensions = Config::get_extensions();
			ksort($extensions);

			foreach ($extensions as $key => $extension) {
				// Treat missing keys as enabled (default is all-on; missing = newly added)
				if (isset($activated_extensions[$key]) && !$activated_extensions[$key]) {
					continue;
				}

				$is_pro_extension = isset($extension['is_pro']) && $extension['is_pro'];

				// Skip pro extensions if user doesn't have premium
				if ($is_pro_extension && !$is_premium) {
					continue;
				}

				// Determine extension file path (group/extension/)
				$group = isset($extension['group']) ? $extension['group'] : '';
				$subcategory = isset($extension['subcategory']) ? $extension['subcategory'] : '';

				$base_paths = [];
				if ($is_premium && $is_pro_extension) {
					// In Freemius code split, JLTMA_PRO_DIR points to the pro plugin
					// while JLTMA_PRO_EXTENSIONS may point to the free plugin's non-existent premium dir
					if (defined('JLTMA_PRO_DIR')) {
						$base_paths[] = JLTMA_PRO_DIR . 'premium/modules/';
					}
					if (defined('JLTMA_PRO_EXTENSIONS') && !in_array(JLTMA_PRO_EXTENSIONS, $base_paths, true)) {
						$base_paths[] = JLTMA_PRO_EXTENSIONS;
					}
				}
				if (empty($base_paths)) {
					$base_paths = [JLTMA_PATH . 'inc/modules/'];
				}

				$extension_file = null;

				// Search using group from config
				if ($group) {
					foreach ($base_paths as $base_path) {
						// Try direct path first: group/extension/extension.php
						$path = $base_path . $group . '/' . $key . '/' . $key . '.php';
						if (file_exists($path)) {
							$extension_file = $path;
							break;
						}
						// Try with subcategory if set: group/subcategory/extension/extension.php
						if ($subcategory && $group !== $subcategory) {
							$path = $base_path . $group . '/' . $subcategory . '/' . $key . '/' . $key . '.php';
							if (file_exists($path)) {
								$extension_file = $path;
								break;
							}
						}
					}
				}

				// Dynamic scan if not found (search group/extension/ and group/subcat/extension/ patterns)
				if (!$extension_file) {
					foreach ($base_paths as $base_path) {
						if (!is_dir($base_path)) continue;
						$groups = array_filter(glob($base_path . '*'), 'is_dir');
						foreach ($groups as $group_path) {
							// First try direct: group/extension/extension.php
							$direct_path = $group_path . '/' . $key . '/' . $key . '.php';
							if (file_exists($direct_path)) {
								$extension_file = $direct_path;
								break 2;
							}
							// Then try nested: group/subcat/extension/extension.php
							$subdirs = array_filter(glob($group_path . '/*'), 'is_dir');
							foreach ($subdirs as $subdir_path) {
								$path = $subdir_path . '/' . $key . '/' . $key . '.php';
								if (file_exists($path)) {
									$extension_file = $path;
									break 3;
								}
							}
						}
					}
				}

				if ($extension_file) {
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
			wp_enqueue_style('master-addons-editor', JLTMA_URL . '/assets/css/master-addons-editor.css', array(), JLTMA_VER);
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
			// Skip redirects on AJAX/CLI
			if (wp_doing_ajax() || (defined('WP_CLI') && WP_CLI)) {
				return;
			}

			// Setup Wizard redirect — only on first activation via transient.
			// After activation, users can freely navigate; wizard menu stays visible until completed.
			if (apply_filters('jltma/setup_wizard_run', true) && get_transient('_master_addons_activation_redirect') && !REST_API::is_setup_complete()) {

				// Don't redirect on multi-plugin activation
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of a WordPress-set admin query var for activation-redirect flow; not form processing.
				if (isset($_GET['activate-multi'])) {
					delete_transient('_master_addons_activation_redirect');
					return;
				}

				// Already on the wizard page — don't redirect loop
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of the current admin page query var; not form processing.
				if (isset($_GET['page']) && $_GET['page'] === 'master-addons-setup-wizard') {
					delete_transient('_master_addons_activation_redirect');
					return;
				}

				// Consume activation transient and redirect to wizard
				delete_transient('_master_addons_activation_redirect');

				wp_safe_redirect(admin_url('admin.php?page=master-addons-setup-wizard'));
				exit;
			}

			// Legacy redirect
			if (is_plugin_active('elementor/elementor.php')) {
				if (get_option('ma_el_update_redirect', false)) {
					delete_option('ma_el_update_redirect');
					delete_transient('ma_el_update_redirect');
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of a WordPress-set admin query var for activation-redirect flow; not form processing.
					if (!isset($_GET['activate-multi']) && $this->is_elementor_activated()) {
						wp_safe_redirect('admin.php?page=master-addons-settings');
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


		/**
		 * Initialize core classes and load non-autoloadable files
		 * Classes/traits are autoloaded - this only handles initialization and bootstrapping files
		 */
		public function jltma_include_files()
		{
			// Bootstrap file that initializes template system (calls master_addons_templates())
			require_once JLTMA_PATH . 'inc/admin/templates/templates.php';

			// Initialize singletons (autoloader loads the classes)
			\MasterAddons\Inc\Classes\Background_Task_Manager::get_instance();
			add_filter('cron_schedules', [\MasterAddons\Inc\Classes\Background_Task_Manager::get_instance(), 'add_cron_intervals']);
			\MasterAddons\Inc\Classes\Assets_Manager::get_instance();  // Register vendor assets (always needed)
			\MasterAddons\Inc\Classes\Assets_Loader::get_instance();
			\MasterAddons\Inc\Classes\Cache_Manager::get_instance();
			\MasterAddons\Inc\Classes\Template_Library_Cache::get_instance();
			\MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
			\MasterAddons\Inc\Classes\Ajax_Queries::get_instance();
			Promote_Pro_Addons::get_instance();
			
			Recommended_Plugins::get_instance();

			\MasterAddons\Inc\Admin\Theme_Builder\Loader::get_instance();
			\MasterAddons\Inc\Admin\PopupBuilder\Popup_Builder_Init::get_instance();
			\MasterAddons\Inc\Admin\WidgetBuilder\Widget_Builder_Init::get_instance();
			REST_API::get_instance();

			\MasterAddons\Inc\Admin\Page_Importer::get_instance();

			// Admin Settings
			new \MasterAddons\Inc\Admin\Settings\Settings();

			// Freemius hooks (filters, submenu reorder, etc.) — must load for all users
			Freemius_Hooks::get_instance();

			// Feedback dialog (deactivation survey) — loads for all users (CSS + dialog)
			new Feedback();

			// Admin notifications (latest updates, rating, subscribe, etc.)
			// Deferred to init so textdomain is loaded before any __() calls.
			add_action('init', function () {
				new \MasterAddons\Inc\Classes\Notifications\Notifications();
			});

			// Dashboard widget for all users
			$pro_upgrade = new Pro_Upgrade();

			// Conditional loading based on plan (classes autoloaded via Lib namespace)
			if (!Helper::jltma_premium()) {
				\MasterAddons\Lib\Featured::get_instance();
				// Image Optimizer for free users — commented out, will release later
				// \MasterAddons\Inc\Admin\Image_Optimizer::get_instance();
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
		if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'jltma_plugin_action')) {
			wp_send_json_error(__('Security check failed.', 'master-addons'));
		}

		$plugin_action = isset($_POST['plugin_action']) ? sanitize_text_field( wp_unslash( $_POST['plugin_action'] ) ) : '';
		$plugin_slug = isset($_POST['plugin']) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';

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
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of the WordPress-set "activate" query var to suppress the default activation notice; not form processing.
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
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of the WordPress-set "activate" query var to suppress the default activation notice; not form processing.
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

		// WordPress.org-hosted plugins have their translations loaded
		// automatically by WordPress (just-in-time, since 4.6), so a manual
		// load_plugin_textdomain() call is no longer required.
		public function load_textdomain() {}

		// Check if Master Addons Pro is activated
	public function is_master_addons_pro_activated($plugin_path = 'master-addons-pro/master-addons.php')
		{
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			return is_plugin_active($plugin_path);
		}
	}
}
