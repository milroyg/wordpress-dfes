<?php
/**
 * Plugin Name: Post Slider and Carousel
 * Plugin URI: https://infornweb.com/post-slider-and-carousel-pro/
 * Version: 3.5.4
 * Description: Posts Slider or Post Carousel add WordPress posts in slider & carousel layouts on your WordPress website. Also added Latest/Recent vertical post scrolling widget.
 * Text Domain: post-slider-and-carousel
 * Domain Path: /languages/
 * Author: InfornWeb
 * Requires at least: 4.7
 * Requires PHP: 5.4
 * Author URI: https://infornweb.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( function_exists( 'psac_fs' ) ) {
	psac_fs()->set_basename( true, __FILE__ );
}

if ( ! class_exists( 'Post_Slider_and_Carousel_Lite' ) )  :

	/**
	 * Main Class
	 * @package Post Slider and Carousel
	 * @version	1.0
	 */
	final class Post_Slider_and_Carousel_Lite {

		// Instance
		private static $instance;

		/**
		 * Script Object.
		 *
	 	 * @version	1.0
		 */
		public $scripts;

		/**
		 * Main Post Slider and Carousel Lite Instance.
		 *
		 * Ensures only one instance of Post_Slider_and_Carousel_Lite is loaded or can be loaded.
		 *
	 	 * @version	1.0
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Post_Slider_and_Carousel_Lite ) ) {
				self::$instance = new Post_Slider_and_Carousel_Lite();
				self::$instance->setup_constants();
				self::$instance->includes(); // Including required files
				self::$instance->init_hooks();
				self::$instance->scripts = new PSAC_Scripts(); // Script Class
			}
			return self::$instance;
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param string      $name  Constant name.
		 * @param string|bool $value Constant value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Setup plugin constants
		 * Basic plugin definitions
		 * 
		 * @since 1.0
		 */
		private function setup_constants() {

			$this->define( 'PSAC_VERSION', '3.5.4' ); // Version of plugin
			$this->define( 'PSAC_FILE', __FILE__ );
			$this->define( 'PSAC_DIR', dirname( __FILE__ ) );
			$this->define( 'PSAC_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'PSAC_BASENAME', basename( PSAC_DIR ) );
			$this->define( 'PSAC_META_PREFIX', '_bdp_' );
			$this->define( 'PSAC_POST_TYPE', 'post' );
			$this->define( 'PSAC_CAT', 'category' );
			$this->define( 'PSAC_LAYOUT_POST_TYPE', 'psacp_layout' );
			$this->define( 'PSAC_SETTING_PAGE_URL', add_query_arg( array('page' => 'psacp-settings', 'tab' => 'general'), 'admin.php' ) );
			$this->define( 'PSAC_PRO_TAB_URL', add_query_arg( array('page' => 'psacp-settings', 'tab' => 'pro'), 'admin.php' ) );
			$this->define( 'PSAC_UPGRADE_URL', add_query_arg( array('page' => 'psacp-layouts-pricing'), 'admin.php' ) );
		}

		/**
		 * Load Localisation files
		 *
		 * @since 1.0
		 */
		public function psac_load_textdomain() {

			global $wp_version;

			// Set filter for plugin's languages directory.
			$psacp_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$psacp_lang_dir = apply_filters( 'psacp_languages_directory', $psacp_lang_dir );

			// Traditional WordPress plugin locale filter.
		    $get_locale = get_locale();

		    if ( $wp_version >= 4.7 ) {
		        $get_locale = get_user_locale();
		    }

			// Traditional WordPress plugin locale filter.
			$locale	= apply_filters( 'plugin_locale',  $get_locale, 'post-slider-and-carousel' );
			$mofile	= sprintf( '%1$s-%2$s.mo', 'post-slider-and-carousel', $locale );

			// Setup paths to current locale file
			$mofile_global = WP_LANG_DIR . '/plugins/' . PSAC_BASENAME . '/' . $mofile;
			
			if ( file_exists( $mofile_global ) ) { // Look in global /wp-content/languages/post-slider-and-carousel-pro folder

				load_textdomain( 'post-slider-and-carousel', $mofile_global );

			} else { // Load the default language files
				load_plugin_textdomain( 'post-slider-and-carousel', false, $psacp_lang_dir );
			}
		}

		/**
		 * Do stuff once all the plugin has been loaded
		 *
		 * @since 1.0
		 */
		public function psac_plugins_loaded() {

			// Visual Composer Page Builder Support
			if( class_exists('Vc_Manager') ) {
				include_once( PSAC_DIR . '/includes/integrations/wpbakery/wpbakery.php' );
			}

			// If Elementor Page Builder is Installed
			if( defined('ELEMENTOR_PLUGIN_BASE') ) {
				require_once( PSAC_DIR . '/includes/integrations/elementor/select2-ajax-control.php' );
				require_once( PSAC_DIR . '/includes/integrations/elementor/elementor.php' );
			}
		}

		/**
		 * Include required files
		 *
		 * @since 1.0
		 */
		private function includes() {

			global $psacp_options;

			// Including freemius file
			include_once( PSAC_DIR . '/freemius.php' );

			// Register Post Type
			require_once( PSAC_DIR . '/includes/psacp-post-types.php' );

			// Including common functions file
			include_once( PSAC_DIR . '/includes/psacp-functions.php' );

			// Plugin Settings
			require_once( PSAC_DIR . '/includes/admin/settings/psacp-register-settings.php' );
			$psacp_options = psac_get_settings(); // Get plugin settings

			// Class Script
			require_once( PSAC_DIR . '/includes/class-psacp-scripts.php' );

			// Plugin shortcodes
			require_once( PSAC_DIR . '/includes/shortcodes/psacp-post-slider.php' );
			require_once( PSAC_DIR . '/includes/shortcodes/psacp-post-carousel.php' );
			require_once( PSAC_DIR . '/includes/shortcodes/psacp-shrt-tmpl.php' );

			// Shortcode Supports
			include_once( PSAC_DIR . '/includes/admin/shortcode-support/shortcode-fields.php' );

			// Widget Class
			require_once( PSAC_DIR . '/includes/widgets/class-psacp-widgets.php' );

			// For Admin Side Only
			if ( is_admin() ) {

				// Class Metabox
				require_once( PSAC_DIR . '/includes/admin/class-psacp-metabox.php' );

				// Class Admin
				require_once( PSAC_DIR . '/includes/admin/class-psacp-admin.php' );

				// Class Shortcode Builder
				require_once( PSAC_DIR . '/includes/admin/shortcode-builder/class-psacp-shortcode-generator.php' );

				include_once( PSAC_DIR . '/includes/admin/settings/psacp-welcome-settings.php' );
				include_once( PSAC_DIR . '/includes/admin/settings/psacp-general-settings.php' );
				include_once( PSAC_DIR . '/includes/admin/settings/psacp-trending-settings.php' );
				include_once( PSAC_DIR . '/includes/admin/settings/psacp-sharing-settings.php' );
				include_once( PSAC_DIR . '/includes/admin/settings/psacp-css-settings.php' );
				include_once( PSAC_DIR . '/includes/admin/settings/psacp-misc-settings.php' );
				include_once( PSAC_DIR . '/includes/admin/settings/psacp-pro-settings.php' );
			}

			// Plugin installation file
			require_once PSAC_DIR . '/includes/class-psacp-install.php';
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 1.0
		 */
		private function init_hooks() {

			register_activation_hook( PSAC_FILE, array( 'PSAC_Install', 'install' ) );

			add_action( 'after_setup_theme', array( $this, 'psac_setup_environment' ) );
			add_action( 'plugins_loaded', array( $this, 'psac_plugins_loaded' ) );
			add_action( 'init', array( $this, 'psac_init_processes' ) );
		}

		/**
		 * Ensure theme and server variable compatibility and setup image sizes.
		 *
		 * @since 1.0
		 */
		public function psac_setup_environment() {

			// Support Post Thumbnails
			if ( ! current_theme_supports( 'post-thumbnails' ) ) {
				add_theme_support( 'post-thumbnails' );
			}
			add_post_type_support( 'post', 'thumbnail' );
		}

		/**
		 * Prior Init Processes
		 *
		 * @since 1.0
		 */
		public function psac_init_processes() {

			// Load Plugin Text Domain
			$this->psac_load_textdomain();

			/*
			 * Plugin Menu Name just to check the screen ID to load condition based assets
			 * This var is not going to be echo anywhere. `sanitize_title` will take care of string.
			 */
			$this->define( 'PSAC_SCREEN_ID', sanitize_title(__('Post Slider and Carousel', 'post-slider-and-carousel')) );

			// Register Image Size
			add_image_size( 'psacp-medium', 500, 500, true );
		}
	}

endif; // End if class_exists check.

/**
 * The main function for that returns Post_Slider_and_Carousel_Lite
 *
 * Example: <?php $psac_lite = PSAC_Lite(); ?>
 *
 * @since 1.0
 * @return object|Post_Slider_and_Carousel_Lite The one true Post_Slider_and_Carousel_Lite Instance.
 */
function PSAC_Lite() {
	return Post_Slider_and_Carousel_Lite::instance();
}

// Get Plugin Running
PSAC_Lite();