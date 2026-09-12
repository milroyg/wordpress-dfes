<?php
/**
 * Post Slider and Carousel Elementor Widget
 *
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Register Custom Controls.
 * 
 * @since 3.5
 */
function psac_register_elementor_custom_controls() {

	$controls_manager = \Elementor\Plugin::$instance->controls_manager;
	$controls_manager->register( new PSAC_Elementor_Select2_Ajax_Control() );
}
add_action( 'elementor/controls/controls_registered', 'psac_register_elementor_custom_controls' );

/**
 * Register elementor widget.
 * 
 * @since 3.5
 */
function psac_register_elementor_widgets( $widgets_manager ) {

	require_once( PSAC_DIR . '/includes/integrations/elementor/psacp-layout-elementor-wdgt.php' );
	$widgets_manager->register( new \PSAC_Layout_Elementor_Widget() );
}
add_action( 'elementor/widgets/register', 'psac_register_elementor_widgets' );