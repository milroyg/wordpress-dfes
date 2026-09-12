<?php
/**
 * Installation Class
 * Handles to manage plugin installation process
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PSAC_Install {

	/**
	 * Plugin Setup On Activation
	 * 
	 * @since 1.0
	 */
	public static function install() {

		// Registered Post Types
		psac_register_post_type();

		// Get plugin settings
		$psacp_opts = get_option('psacp_opts');

		// Update plugin settings if they are not set
		if( empty( $psacp_opts ) ) {
			psac_set_default_settings();

			update_option( 'psac_version', '1.0' );
		}

		// Deactivate Pro Plugin
		if( is_plugin_active('post-slider-and-carousel-pro/post-slider-and-carousel-pro.php') ) {
			add_action( 'update_option_active_plugins', array( 'PSAC_Install', 'psac_deactivate_lite_version' ) );
		}

		// Clear the permalinks
		flush_rewrite_rules();
	}

	/**
	 * Deactivate Pro Plugin
	 * 
	 * @since 1.0
	 */
	public static function psac_deactivate_lite_version() {
		deactivate_plugins('post-slider-and-carousel-pro/post-slider-and-carousel-pro.php', true);
	}
}