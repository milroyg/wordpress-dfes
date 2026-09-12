<?php
/**
 * Widget Class
 * Widget related functions and widget registration.
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Include widget classes
require_once( PSAC_DIR . '/includes/widgets/class-psacp-post-scrolling-widget.php' );

/**
 * Register Widgets.
 *
 * @since 1.0
 */
function psac_register_widgets() {
	register_widget( 'PSAC_Post_Scrolling_Widget' );
}
add_action( 'widgets_init', 'psac_register_widgets' );