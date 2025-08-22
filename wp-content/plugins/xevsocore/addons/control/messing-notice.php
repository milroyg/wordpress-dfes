<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

      $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'xevsocore' ),
            '<strong>' . esc_html__( 'Elementor Test Extension', 'xevsocore' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'xevsocore' ) . '</strong>'
      );

	printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );