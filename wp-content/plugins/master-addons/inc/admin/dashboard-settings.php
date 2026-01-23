<?php

namespace MasterAddons\Admin\Dashboard;

use MasterAddons\Master_Elementor_Addons;

use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Elements;
use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Forms;
use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Marketing;
use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Icons_Library;
use MasterAddons\Admin\Dashboard\Addons\Extensions\JLTMA_Addon_Extensions;
use MasterAddons\Admin\Dashboard\Addons\Extensions\JLTMA_Third_Party_Extensions;
use MasterAddons\Inc\Helper\Master_Addons_Helper;
/*
	* Master Admin Dashboard Page
	* Jewel Theme < Liton Arefin >
	*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit();
}

class Master_Addons_Admin_Settings
{

	public $menu_title;

	// Master Addons Elements Property
	private $jltma_default_element_settings;
	private $maad_el_settings;
	private $jltma_get_element_settings;

	// Master Addons Elements Property
	private $jltma_default_extension_settings;
	private $maad_el_extension_settings;
	private $jltma_get_extension_settings;
	private $jltma_get_icons_library_settings;

	// Master Addons Third Party Plugins Property
	private $jltma_default_third_party_plugins_settings;
	private $jltma_third_party_plugins_settings;
	private $jltma_get_third_party_plugins_settings;

	// Master Addons Icons Library Property
	private $jltma_default_icons_library_settings = array();
	private $jltma_icons_library_settings = array();


	public function __construct()
	{
		add_action('admin_menu', [$this, 'master_addons_admin_menu'],  '', 10);
		add_action('network_admin_menu', [$this, 'master_addons_admin_menu'],  '', 10);
		add_action('plugins_loaded', array($this, 'jltma_ajax_data_save'));
		add_action('admin_enqueue_scripts', [$this, 'master_addons_el_admin_scripts'], 99);
		add_action('admin_head', [$this, 'jltma_admin_head_script']);
		add_action('admin_body_class', [$this, 'jltma_admin_body_class']);

		// Enqueue admin SDK assets in Elementor editor
		add_action('elementor/editor/before_enqueue_scripts', [$this, 'elementor_editor_enqueue_scripts']);

		// Add jltma-admin class to body in Elementor editor
		add_action('elementor/editor/footer', [$this, 'elementor_editor_add_body_class']);


		// Master Addons Elements
		add_action('wp_ajax_jltma_save_elements_settings', [$this, 'jltma_save_elements_settings']);

		// Master Addons Extensions
		add_action('wp_ajax_master_addons_save_extensions_settings', [$this, 'master_addons_save_extensions_settings']);

		// Master Addons Icons Library
		add_action('wp_ajax_jltma_save_icons_library_settings', [$this, 'jltma_save_icons_library_settings']);

		// Master Addons API Settings
		add_action('wp_ajax_jltma_save_api_settings', [$this, 'jltma_save_api_settings']);


		$this->ma_el_include_files();
	}


	/**
	 * Admin Body Class
	 */
	public function jltma_admin_body_class($class)
	{
		$bodyclass = '';
		$bodyclass .= ' jltma-admin ';
		return $class . $bodyclass;
	}

	public function ma_el_include_files()
	{
		if(Master_Addons_Helper::jltma_premium() && defined('JLTMA_PRO_PATH')){
			include_once JLTMA_PRO_PATH . 'inc/admin/jltma-elements/ma-forms.php';
			include_once JLTMA_PRO_PATH . 'inc/admin/jltma-elements/ma-elements.php';
			include_once JLTMA_PRO_PATH . 'inc/admin/jltma-elements/ma-extensions.php';
			include_once JLTMA_PRO_PATH . 'inc/admin/jltma-elements/ma-icons-library.php';
			include_once JLTMA_PRO_PATH . 'inc/admin/jltma-elements/ma-marketing.php';
			include_once JLTMA_PRO_PATH . 'inc/admin/jltma-elements/ma-third-party-plugins.php';
		}else{
			include_once JLTMA_PATH . 'inc/admin/jltma-elements/ma-forms.php';
			include_once JLTMA_PATH . 'inc/admin/jltma-elements/ma-elements.php';
			include_once JLTMA_PATH . 'inc/admin/jltma-elements/ma-extensions.php';
			include_once JLTMA_PATH . 'inc/admin/jltma-elements/ma-icons-library.php';
			include_once JLTMA_PATH . 'inc/admin/jltma-elements/ma-marketing.php';
			include_once JLTMA_PATH . 'inc/admin/jltma-elements/ma-third-party-plugins.php';

		}


		// Template Kits functionality now loaded via inc/templates/templates.php
	}

	public function get_menu_title()
	{
		return ($this->menu_title) ? $this->menu_title : $this->get_page_title();
	}

	protected function get_page_title()
	{
		return __('Master Addons', 'master-addons');
	}

	// Main Menu
	public function master_addons_admin_menu()
	{
		// Default values for free version
		$jltma_logo_image = JLTMA_IMAGE_DIR . 'icon.png';
		$page_title = __('Master Addons for Elementor', 'master-addons');
		$menut_label = __('Master Addons', 'master-addons');

		// Allow premium to override via filters
		$jltma_logo_image = apply_filters('master_addons/white_label/menu_logo', $jltma_logo_image);
		$page_title = apply_filters('master_addons/white_label/page_title', $page_title);
		$menut_label = apply_filters('master_addons/white_label/menu_label', $menut_label);

		$jltma_white_label_setting = jltma_get_options('jltma_white_label_settings');
		if (!is_array($jltma_white_label_setting)) {
			$jltma_white_label_setting = array();
		}
		add_menu_page(
			$page_title, // Page Title
			$menut_label,    // Menu Title
			'manage_options',
			'master-addons-settings',
			[$this, 'jltma_admin_settings_page_content'],
			$jltma_logo_image,
			57
		);

		// Rename the first submenu item to "Settings"
		add_submenu_page(
			'master-addons-settings',
			$page_title,
			__('Settings', 'master-addons'),
			'manage_options',
			'master-addons-settings',
			[$this, 'jltma_admin_settings_page_content']
		);

		if(!empty($jltma_white_label_setting['jltma_wl_plugin_tab_white_label'])) {
			remove_submenu_page('master-addons-settings', 'master-addons-account');
			remove_submenu_page('master-addons-settings', 'https://wordpress.org/support/plugin/master-addons/#new-topic-0');
			add_action('admin_head', function () {
					if (is_admin() ) {
							?>
							<style>
								#toplevel_page_master-addons-settings .wp-submenu li a[href="admin.php?page=master-addons-account"] {
										display: none !important;
								}
								#toplevel_page_master-addons-settings .wp-submenu li a[href="https://wordpress.org/support/plugin/master-addons/#new-topic-0"] {
										display: none !important;
								}
								#toplevel_page_master-addons-settings .wp-submenu-head {
										display: none !important;
								}
							</style>
							<?php
					}
			});
		}
	}

	public function jltma_admin_head_script()
	{
		// Default logo for free version
		$jltma_logo_image = JLTMA_IMAGE_DIR . 'icon.png';
		$has_custom_logo = false;

		// Allow premium to override via filter
		$jltma_logo_image = apply_filters('master_addons/white_label/menu_logo', $jltma_logo_image);
		$has_custom_logo = apply_filters('master_addons/white_label/has_custom_logo', $has_custom_logo);

		if ($has_custom_logo) { ?>
			<style>
				.svg .wp-badge.welcome__logo {
					background: url('<?php echo esc_url($jltma_logo_image); ?>') left center no-repeat;
				}

				#adminmenu li.wp-has-current-submenu .wp-menu-image img {
					width: 16px;
					height: 25px;
				}

				.master_addons .header .ma_el_logo .wp-badge {
					width: none;
				}

				#adminmenu .wp-menu-image img {
					width: 20px;
				}
			</style>
<?php }
	}


	public function master_addons_el_admin_scripts($hook)
	{
		$screen = get_current_screen();

		// Check if we're on any Master Addons admin page
		$is_master_addons_page = (
			$screen->id == 'toplevel_page_master-addons-settings' ||
			$screen->id == 'toplevel_page_master-addons-settings-network' ||
			strpos($screen->id, 'master-addons_page_') === 0 ||
			(isset($screen->parent_base) && $screen->parent_base === 'master-addons-settings')
		);

		// Load Scripts only Master Addons Admin Page
		if ($is_master_addons_page) {

			//CSS
			wp_enqueue_style('master-addons-admin-settings', JLTMA_ADMIN_ASSETS . 'css/master-addons-admin.css');
			wp_enqueue_style('master-addons-el-switch', JLTMA_ADMIN_ASSETS . 'css/switch.css');

			//JS
			if (!did_action('wp_enqueue_media')) {
				wp_enqueue_media();
			}
			wp_enqueue_script('master-addons-el-welcome-tabs', JLTMA_ADMIN_ASSETS . 'js/welcome-tabs.js', ['jquery'], JLTMA_VER, true);
			wp_enqueue_script('master-addons-admin-settings', JLTMA_ADMIN_ASSETS . 'js/master-addons-admin-settings.js', ['jquery'], JLTMA_VER, true);
			
			wp_enqueue_style('sweetalert', JLTMA_ADMIN_ASSETS . 'css/sweetalert2.min.css');
			wp_enqueue_script('sweetalert', JLTMA_ADMIN_ASSETS . 'js/sweetalert2.min.js', ['jquery', 'master-addons-admin-settings'], JLTMA_VER, true);

			$jltma_localize_admin_script = array(
				'ajaxurl'                  => admin_url('admin-ajax.php'),
				'ajax_nonce'               => wp_create_nonce('jltma_options_settings_nonce_action'),
				'ajax_extensions_nonce'    => wp_create_nonce('jltma_extensions_settings_nonce_action'),
				'ajax_api_nonce'           => wp_create_nonce('jltma_api_settings_nonce_action'),
				'ajax_icons_library_nonce' => wp_create_nonce('jltma_icons_library_settings_nonce_action'),

				'home_url' => home_url(),
				'rollback' => [
					'rollback_confirm'             => __('Are you sure you want to reinstall version ?', 'master-addons'),
					'rollback_to_previous_version' => __('Rollback to Previous Version', 'master-addons'),
					'yes'                          => __('Yes', 'master-addons'),
					'cancel'                       => __('Cancel', 'master-addons'),
				]
			);

			wp_localize_script('master-addons-admin-settings', 'JLTMA_OPTIONS', $jltma_localize_admin_script);
		}

		// Load Scripts for Templates Kit Page
		if ($screen->id == 'master-addons_page_jltma-templates-kit') {
			// Hide admin notices for clean template interface
			$this->hide_admin_notices_on_templates_pages();

			//CSS
			wp_enqueue_style('jltma-templates-kit', JLTMA_ASSETS . 'css/admin/jltma-templates-kit.css', array(), JLTMA_VER);

			//JS
			wp_enqueue_script('jltma-templates-kit', JLTMA_ASSETS . 'js/admin/jltma-templates-kit.js', array('jquery', 'updates'), JLTMA_VER, true);

			wp_localize_script('jltma-templates-kit', 'JLTMATemplatesKitLoc', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('jltma-templates-kit-js'),
				'cache_nonce' => wp_create_nonce('master_addons_nonce')
			));
		}

		// Load Scripts for Template Library Page
		$library_screen_prefix = 'master-addons';
		$jltma_white_label_setting = jltma_get_options('jltma_white_label_settings');
		if( isset( $jltma_white_label_setting['jltma_wl_plugin_menu_label']) && !empty( $jltma_white_label_setting['jltma_wl_plugin_menu_label']) ){
			$library_screen_prefix  =  preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($jltma_white_label_setting['jltma_wl_plugin_menu_label']));
		}
		$library_screen = $library_screen_prefix . '_page_jltma-template-library';
		$kit_screen = $library_screen_prefix . '_page_jltma-templates-kit';

		if ($screen->id == $library_screen || $screen->id == $kit_screen) {
			// Hide admin notices for clean template interface
			$this->hide_admin_notices_on_templates_pages();

			// Enqueue WordPress React dependencies
			$asset_file_path = JLTMA_PATH . 'inc/templates/library/template-library.asset.php';
			$asset_file = file_exists($asset_file_path) ? include($asset_file_path) : array('dependencies' => array('wp-element', 'wp-i18n', 'wp-components', 'wp-api-fetch'), 'version' => JLTMA_VER);

			//CSS
			wp_enqueue_style('jltma-template-library', JLTMA_ASSETS . 'css/admin/template-library.css', array('wp-components'), $asset_file['version']);
			// Enqueue page importer CSS for modal styling (vertical progress steps)
			wp_enqueue_style('jltma-page-importer', JLTMA_ASSETS . 'css/admin/page-importer.css', array(), JLTMA_VER);

			//JS - React App
			wp_enqueue_script(
				'jltma-template-library',
				JLTMA_ASSETS . 'js/admin/template-library.js',
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);

			wp_localize_script('jltma-template-library', 'JLTMATemplateLibrary', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('jltma_template_library_nonce'),
				'restUrl' => get_rest_url(),
				'restNonce' => wp_create_nonce('wp_rest'),
				'pluginUrl' => JLTMA_URL,
				'assetsUrl' => JLTMA_ASSETS,
				'isProActive' => Master_Addons_Helper::jltma_premium(),
				'strings' => array(
					'searchPlaceholder' => __('Search templates...', 'master-addons'),
					'importTemplate' => __('Import', 'master-addons'),
					'previewTemplate' => __('Preview', 'master-addons'),
					'loadingTemplates' => __('Loading templates...', 'master-addons'),
					'noTemplatesFound' => __('No templates found', 'master-addons'),
					'importSuccess' => __('Template imported successfully!', 'master-addons'),
					'importError' => __('Failed to import template', 'master-addons')
				)
			));
		}

		// CSS Files .
		wp_enqueue_style('master-addons-admin-sdk', JLTMA_ADMIN_ASSETS . 'css/master-addons-admin-sdk.css', array('dashicons'), JLTMA_VER, 'all');

		// JS Files .
		wp_enqueue_script('master-addons-admin-sdk', JLTMA_ADMIN_ASSETS . 'js/master-addons-admin-sdk.js', array('jquery'), JLTMA_VER, true);
		wp_localize_script(
			'master-addons-admin-sdk',
			'JLTMACORE',
			array(
				'admin_ajax'        => admin_url('admin-ajax.php'),
				'recommended_nonce' => wp_create_nonce('jltma_recommended_nonce'),
				'is_premium'        => Master_Addons_Helper::jltma_premium(),
			)
		);

		// Localize Script
		if (is_customize_preview()) {
			return;
		}
	}

	/**
	 * Enqueue admin SDK assets in Elementor editor
	 * Loads popup and switcher disable functionality
	 */
	public function elementor_editor_enqueue_scripts()
	{
		// CSS Files
		wp_enqueue_style('master-addons-admin-sdk', JLTMA_ADMIN_ASSETS . 'css/master-addons-admin-sdk.css', array('dashicons'), JLTMA_VER, 'all');

		// JS Files
		wp_enqueue_script('master-addons-admin-sdk', JLTMA_ADMIN_ASSETS . 'js/master-addons-admin-sdk.js', array('jquery'), JLTMA_VER, true);
		wp_localize_script(
			'master-addons-admin-sdk',
			'JLTMACORE',
			array(
				'admin_ajax'        => admin_url('admin-ajax.php'),
				'recommended_nonce' => wp_create_nonce('jltma_recommended_nonce'),
				'is_premium'        => Master_Addons_Helper::jltma_premium()
			)
		);
	}

	/**
	 * Add jltma-admin class to body in Elementor editor
	 * Required for CSS scoping of admin SDK styles
	 */
	public function elementor_editor_add_body_class()
	{
		?>
		<script>
			(function($) {
				// Add class immediately
				$('body').addClass('jltma-admin');

				// Also add when DOM is ready (double check)
				$(document).ready(function() {
					$('body').addClass('jltma-admin');
				});
			})(jQuery);
		</script>
		<?php
	}


	public function jltma_admin_settings_page_content()
	{
		// Master Addons Elements Settings
		$this->jltma_default_element_settings = array_fill_keys(self::jltma_addons_array(), true);
		$this->jltma_get_element_settings     = get_option('maad_el_save_settings', $this->jltma_default_element_settings);

		// Master Addons Extensions Settings
		$this->jltma_default_extension_settings = array_fill_keys(self::jltma_addons_extensions_array(), true);
		$this->jltma_get_extension_settings     = get_option('ma_el_extensions_save_settings', $this->jltma_default_extension_settings);

		// Master Addons Third Party Plugins Settings
		$this->jltma_default_third_party_plugins_settings = array_fill_keys(self::jltma_addons_third_party_plugins_array(), true);
		$this->jltma_get_third_party_plugins_settings     = get_option('ma_el_third_party_plugins_save_settings', $this->jltma_default_third_party_plugins_settings);

		// Master Addons Icons Library Settings
		$this->jltma_default_icons_library_settings = array_fill_keys(self::jltma_addons_icons_library_array(), true);
		$this->jltma_get_icons_library_settings     = get_option('jltma_icons_library_save_settings', $this->jltma_default_icons_library_settings);

		// Welcome Page
		include JLTMA_PATH . 'inc/admin/welcome.php';
	}



	public static function jltma_addons_array()
	{
		// Separated Addons on new Format
		$jltma_new_widgets = [];

		foreach (JLTMA_Addon_Elements::$jltma_elements['jltma-addons']['elements'] as $key => $widget) {
			$jltma_new_widgets[] = $widget['key'];
		}
		foreach (JLTMA_Addon_Forms::$jltma_forms['jltma-forms']['elements'] as $key => $widget) {
			$jltma_new_widgets[] = $widget['key'];
		}
		foreach (JLTMA_Addon_Marketing::$jltma_marketing['jltma-marketing']['elements'] as $key => $widget) {
			$jltma_new_widgets[] = $widget['key'];
		}

		return $jltma_new_widgets;
	}


	// Merged All Addon Elements
	public static function jltma_merged_addons_array()
	{
		// Separated All Addons on new Format
		// $jltma_new_merged_widgets = [];
		$jltma_new_merged_widgets1 = JLTMA_Addon_Elements::$jltma_elements['jltma-addons']['elements'];
		$jltma_new_merged_widgets2 = JLTMA_Addon_Forms::$jltma_forms['jltma-forms']['elements'];
		$jltma_new_merged_widgets3 = JLTMA_Addon_Marketing::$jltma_marketing['jltma-marketing']['elements'];

		$jltma_merged_addons = array_merge($jltma_new_merged_widgets1, $jltma_new_merged_widgets2, $jltma_new_merged_widgets3);

		return $jltma_merged_addons;
	}

	public function jltma_ajax_data_save(){
		$ajax_data_save = new \MasterAddons\Inc\Classes\Notifications\What_We_Collect();
		$ajax_data_save->jltma_collect_ajax_data();
	}


	// Extensions Array
	public static function jltma_addons_extensions_array()
	{
		// Separated Addons on new Format
		$jltma_new_extensions = [];

		foreach (JLTMA_Addon_Extensions::$jltma_extensions['jltma-extensions']['extension'] as $key => $extension) {
			$jltma_new_extensions[] = $extension['key'];
		}

		return $jltma_new_extensions;
	}


	// Third Party Plugins Array
	public static function jltma_addons_third_party_plugins_array()
	{
		// Separated Addons on new Format
		$jltma_new_third_party_plugins = [];

		foreach (JLTMA_Third_Party_Extensions::$jltma_third_party_plugins['jltma-plugins']['plugin'] as $key => $plugin) {
			$jltma_new_third_party_plugins[] = $plugin['key'];
		}
		return $jltma_new_third_party_plugins;
	}


	// Icons Library Array
	public static function jltma_addons_icons_library_array()
	{
		// Separated Addons on new Format
		$jltma_new_icons_library = [];

		foreach (JLTMA_Icons_Library::$jltma_icons_library['jltma-icons-library']['libraries'] as $key => $icons_library) {
			$jltma_new_icons_library[] = $icons_library['key'];
		}
		return $jltma_new_icons_library;
	}


	public function jltma_save_elements_settings()
	{
		check_ajax_referer('jltma_options_settings_nonce_action', 'security');

		if (isset($_POST['fields'])) {
			parse_str($_POST['fields'], $settings);
		} else {
			return;
		}

		$this->maad_el_settings = [];

		foreach (self::jltma_addons_array() as $value) {

			if (isset($settings[$value])) {
				$this->maad_el_settings[$value] = 1;
			} else {
				$this->maad_el_settings[$value] = 0;
			}
		}

		update_option('maad_el_save_settings', $this->maad_el_settings);

		return true;
		die();
	}


	public function master_addons_save_extensions_settings()
	{

		check_ajax_referer('jltma_extensions_settings_nonce_action', 'security');

		if (isset($_POST['fields'])) {
			parse_str($_POST['fields'], $settings);
		} else {
			return;
		}

		$this->maad_el_extension_settings = [];

		foreach (self::jltma_addons_extensions_array() as $value) {

			// Force disable dynamic-tags and custom-css if Elementor Pro is active
			if ( ($value === 'dynamic-tags-s' || $value === 'custom-css-s') && defined('ELEMENTOR_PRO_VERSION')) {
				$this->maad_el_extension_settings[$value] = 0;
			} elseif (isset($settings[$value])) {
				$this->maad_el_extension_settings[$value] = 1;
			} else {
				$this->maad_el_extension_settings[$value] = 0;
			}
		}
		update_option('ma_el_extensions_save_settings', $this->maad_el_extension_settings);


		// Third Party Plugin Settings
		$this->jltma_third_party_plugins_settings = [];

		// New Format for Third Party Extensions
		$jltma_new_third_party_extensions = [];
		foreach (JLTMA_Third_Party_Extensions::$jltma_third_party_plugins['jltma-plugins']['plugin'] as $key => $plugin) {
			$jltma_new_third_party_extensions[] = $plugin['key'];
		}
		$jltma_new_third_party_extensions;

		foreach ($jltma_new_third_party_extensions as $value) {

			if (isset($settings[$value])) {
				$this->jltma_third_party_plugins_settings[$value] = 1;
			} else {
				$this->jltma_third_party_plugins_settings[$value] = 0;
			}
		}
		update_option('ma_el_third_party_plugins_save_settings', $this->jltma_third_party_plugins_settings);


		return true;
		die();
	}


	// API Settings Ajax Call
	public function jltma_save_api_settings()
	{

		check_ajax_referer('jltma_api_settings_nonce_action', 'security');

		$jltma_api_settings = [];
		if (isset($_POST['fields'])) {
			foreach ($_POST['fields'] as $value) {
				$jltma_api_settings[sanitize_key($value['name'])] = sanitize_text_field($value['value']);
			}
		}

		update_option('jltma_api_save_settings', $jltma_api_settings);

		return true;
		die();
	}


	// Icons Library Settings Ajax Call
	public function jltma_save_icons_library_settings()
	{
		check_ajax_referer('jltma_icons_library_settings_nonce_action', 'security');

		if (isset($_POST['fields'])) {
			parse_str($_POST['fields'], $settings);
		} else {
			return;
		}

		$this->jltma_icons_library_settings = [];

		foreach (self::jltma_addons_icons_library_array() as $value) {

			if (isset($settings[$value])) {
				$this->jltma_icons_library_settings[sanitize_key($value)] = 1;
			} else {
				$this->jltma_icons_library_settings[sanitize_key($value)] = 0;
			}
		}

		update_option('jltma_icons_library_save_settings', $this->jltma_icons_library_settings);

		return true;
		die();
	}

	/**
	 * Hide admin notices on Template Library and Template Kits pages for clean interface
	 */
	public function hide_admin_notices_on_templates_pages() {
		// Remove all admin notices
		remove_all_actions('admin_notices');
		remove_all_actions('all_admin_notices');
		remove_all_actions('network_admin_notices');
		remove_all_actions('user_admin_notices');

		// Remove WordPress update notices
		remove_action('admin_notices', 'update_nag', 3);
		remove_action('admin_notices', 'maintenance_nag', 10);

		// Also hide WordPress core notices with CSS
		add_action('admin_head', function() {
			$screen = get_current_screen();
			$is_template_library = ($screen && $screen->id === 'master-addons_page_jltma-template-library');

			// For Template Library page: exclude jltma- prefixed elements from hiding
			// For Template Kits page: hide all notices normally
			if ($is_template_library) {
				echo '<style type="text/css">
					/* Hide ALL WordPress admin notices but preserve ONLY modal elements */
					.notice:not(.jltma-page-import-modal):not(.jltma-page-import-modal *),
					.error:not(.jltma-page-import-modal):not(.jltma-page-import-modal *),
					.updated:not(.jltma-page-import-modal):not(.jltma-page-import-modal *),
					.update-nag,
					.admin-notice,
					.jltma-plugin-update-notice,
					.jltma-notice-latest_updates,
					.notice-jltma,
					.fs-notice,
					.fs-slug-master-addons,
					div[id*="message"]:not(.jltma-page-import-modal *),
					div[class*="notice"]:not(.jltma-page-import-modal):not(.jltma-page-import-modal *),
					div[class*="error"]:not(.jltma-page-import-modal):not(.jltma-page-import-modal *),
					div[class*="updated"]:not(.jltma-page-import-modal):not(.jltma-page-import-modal *),
					div[class*="fs-notice"],
					.wrap > .notice,
					.wrap > .error,
					.wrap > .updated,
					#wpbody-content > .notice,
					#wpbody-content > .error,
					#wpbody-content > .updated,
					#wpbody-content .wrap > .notice,
					#wpbody-content .wrap > .error,
					#wpbody-content .wrap > .updated {
						display: none !important;
						visibility: hidden !important;
						opacity: 0 !important;
						height: 0 !important;
						overflow: hidden !important;
						margin: 0 !important;
						padding: 0 !important;
					}

					/* Ensure modal container is visible - do not force display on children */
					.jltma-page-import-modal {
						display: flex !important;
					}
				</style>';
			} else {
				// Template Kits page and others: hide all notices
				echo '<style type="text/css">
					.notice,
					.error,
					.updated,
					.update-nag,
					.admin-notice,
					.jltma-plugin-update-notice,
					.fs-notice,
					.fs-slug-master-addons,
					.wrap > .notice,
					.wrap > .error,
					.wrap > .updated,
					#wpbody-content > .notice,
					#wpbody-content > .error,
					#wpbody-content > .updated {
						display: none !important;
						visibility: hidden !important;
						height: 0 !important;
						overflow: hidden !important;
						margin: 0 !important;
						padding: 0 !important;
					}
				</style>';
			}

			// Also inject JavaScript to remove any dynamically added notices
			if ($is_template_library) {
				// Template Library: hide all notices except modal elements
				echo '<script type="text/javascript">
					jQuery(document).ready(function($) {
						// Remove all notices that are NOT inside modal
						var hideNotices = function() {
							// Find all notices
							$(".notice, .error, .updated, .update-nag, .admin-notice, .fs-notice, .notice-jltma, .jltma-notice-latest_updates").each(function() {
								var $this = $(this);
								// Only preserve elements inside modal
								var isInsideModal = $this.closest(".jltma-page-import-modal").length > 0;
								var isModal = $this.hasClass("jltma-page-import-modal");

								// Hide everything except modal elements
								if (!isInsideModal && !isModal) {
									$this.css({
										"display": "none",
										"visibility": "hidden",
										"opacity": "0",
										"height": "0",
										"overflow": "hidden",
										"margin": "0",
										"padding": "0"
									});
								}
							});
						};

						hideNotices();

						// Run periodically to catch dynamically added notices
						setInterval(hideNotices, 500);

						// Also observe DOM changes
						if (window.MutationObserver) {
							var observer = new MutationObserver(hideNotices);
							observer.observe(document.body, { childList: true, subtree: true });
						}
					});
				</script>';
			} else {
				// Template Kits page and others: hide all notices
				echo '<script type="text/javascript">
					jQuery(document).ready(function($) {
						var hideNotices = function() {
							$(".notice, .error, .updated, .update-nag, .admin-notice, .fs-notice").hide();
						};

						hideNotices();

						// Run periodically to catch dynamically added notices
						setInterval(hideNotices, 1000);

						// Also observe DOM changes
						if (window.MutationObserver) {
							var observer = new MutationObserver(hideNotices);
							observer.observe(document.body, { childList: true, subtree: true });
						}
					});
				</script>';
			}
		});

		// Remove Freemius notices specifically
		if (function_exists('ma_el_fs')) {
			$freemius = ma_el_fs();
			if (method_exists($freemius, 'remove_all_admin_notices')) {
				$freemius->remove_all_admin_notices();
			}
		}

		// Remove WordPress core notices
		add_action('admin_head', array($this, 'jltma_remove_core_admin_notices'));

		// Additional cleanup for persistent notices
		add_filter('wp_kses_allowed_html', array($this, 'jltma_remove_admin_notices_from_kses'), 10, 2);
	}

	/**
	 * Remove WordPress core admin notices
	 */
	public function jltma_remove_core_admin_notices() {
		// Remove update notifications
		remove_action('admin_notices', 'update_nag', 3);
		remove_action('network_admin_notices', 'update_nag', 3);
		remove_action('admin_notices', 'maintenance_nag');

		// Remove plugin update notices
		if (isset($GLOBALS['pagenow']) && in_array($GLOBALS['pagenow'], array('plugins.php', 'update-core.php'))) {
			return; // Don't remove notices on plugin/update pages
		}

		// Clear any remaining notices
		if (function_exists('get_transient') && function_exists('delete_transient')) {
			delete_transient('settings_errors');
		}
	}

	/**
	 * Remove admin notice elements from allowed HTML during wp_kses filtering
	 */
	public function jltma_remove_admin_notices_from_kses($allowed_html, $context) {
		if ($context === 'post' && is_admin()) {
			$notice_elements = array('notice', 'error', 'updated', 'update-nag');
			foreach ($notice_elements as $element) {
				unset($allowed_html[$element]);
			}
		}
		return $allowed_html;
	}

}

new Master_Addons_Admin_Settings();
