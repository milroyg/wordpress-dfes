<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// Check if Elementor installed and activated
if ( ! did_action( 'elementor/loaded' ) ) {
	add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
	return;
}

// Check for required Elementor version
if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
	add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
	return;
}

// Check for required PHP version
if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
	add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
	return;
}

// Add Plugin actions
add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );