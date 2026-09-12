<?php
/**
 * Metabox Class
 * Handles the admin side functionality of plugin
 * 
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PSAC_MetaBox {

	function __construct() {

		// Action to register admin menu
		add_action( 'add_meta_boxes', array( $this, 'psac_add_meta_box' ) );
	}

	/**
	 * Register all the meta boxes for the post type
	 * 
	 * @version	3.5
	 */
	function psac_add_meta_box() {

		// Allowed Post Types
		$allowed_post_types = psac_get_option( 'post_types', array() );

		// Post settings metabox
		add_meta_box( 'psacp_post_sett', __( 'Post Slider and Carousel - Settings', 'post-slider-and-carousel' ),  array( $this, 'psac_render_post_sett_meta_box' ), $allowed_post_types, 'normal', 'high' );
	}

	/**
	 * Post Setting MetaBox
	 * 
	 * @version	3.5
	 */
	function psac_render_post_sett_meta_box() {
		include_once( PSAC_DIR . '/includes/admin/metabox/post-settings.php' );
	}
}

$psac_metabox = new PSAC_MetaBox();