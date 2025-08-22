<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

$message = sprintf(
      /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
      esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'xevsocore' ),
      '<strong>' . esc_html__( 'Elementor Test Extension', 'xevsocore' ) . '</strong>',
      '<strong>' . esc_html__( 'Elementor', 'xevsocore' ) . '</strong>',
       self::MINIMUM_ELEMENTOR_VERSION
);

printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );