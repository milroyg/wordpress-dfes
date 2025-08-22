<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// Register Widget Styles
add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );