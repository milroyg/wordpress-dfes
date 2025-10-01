<?php
/**
 * xevso functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package xevso 
 */
define( "xevso_VERSION", time() );
define( "xevso_ASSETS_DIR", get_template_directory_uri() . "/assets/" );
define( "xevso_FILE_DIR", get_template_directory() . "/" );
require_once xevso_FILE_DIR . 'inc/function/theme-setup.php';
require_once xevso_FILE_DIR . 'inc/function/theme-widget.php';
require_once xevso_FILE_DIR . 'inc/function/theme-filter.php';

/**
 * TGM Plugin 
 */
require_once xevso_FILE_DIR . 'inc/plugins-activation.php';
/**
 * Demo Content 
 */
require_once xevso_FILE_DIR . 'inc/demo.php';
/**
 * Blog Comment List
 */
require_once xevso_FILE_DIR . 'inc/comments-list.php';
/**
 * Enqueue scripts and styles.
 */
require_once xevso_FILE_DIR . 'inc/theme-style.php';
/**
 * Implement the Custom Header feature.
 */
require_once xevso_FILE_DIR . 'inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require_once xevso_FILE_DIR . 'inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require_once xevso_FILE_DIR . 'inc/template-functions.php';
require_once xevso_FILE_DIR . 'inc/xevso-default-options.php';

/**
 * Customizer additions.
 */
require_once xevso_FILE_DIR . 'inc/customizer.php';
require_once xevso_FILE_DIR . 'inc/theme-and-options/ini.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require_once xevso_FILE_DIR . 'inc/jetpack.php';
}
/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require_once xevso_FILE_DIR . 'inc/woocommerce.php';
}
if( class_exists( 'CSF' ) ) {
	require_once xevso_FILE_DIR . 'inc/theme-and-options/metabox-and-options.php';
	require_once xevso_FILE_DIR . 'inc/css.php';
	require_once xevso_FILE_DIR . 'inc/js.php';
}




