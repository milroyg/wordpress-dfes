<?php
/*
plugin Name: xevso Core
Description: This is xevso Core plugin for xevso Theme.
Version:1.0.0
Author: Themebuzz
Author URI:#
Text Domain: xevsocore
Domain Path: /languages
 */
// Exit if accessed derictly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
define( 'xevso_CORE', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
// Define
define( 'xevso_ACC_PATH', plugin_dir_path( __FILE__ ) . '/' );
define( 'xevso_ACC_URL', plugin_dir_url( __FILE__ ) );
// xevso Widget

require_once xevso_ACC_PATH . 'inc/core-function.php';
require_once xevso_ACC_PATH . 'addons/ini.php';
require_once xevso_ACC_PATH . 'addons/icons.php';
// End xevso Widget
// Custom Post
require_once xevso_ACC_PATH . 'xevso-custom-post.php';
if( class_exists( 'CSF' ) ) {
require_once xevso_ACC_PATH . 'inc/custom-widgets.php';
require_once xevso_ACC_PATH . 'inc/admin/icons.php';
}

// Admin custom script
function xevsocore_admin_enqueue_scripts( $hook ) {
    wp_enqueue_style( 'xevsocore-admin', plugin_dir_url( __FILE__ ) . 'assets/xevso-admin.css', array(), '1.0.0', 'all' );
    if ( isset( $_REQUEST['post'] ) || isset( $_REQUEST['post_ID'] ) ) {
        $post_id = empty( $_REQUEST['post_ID'] ) ? $_REQUEST['post'] : $_REQUEST['post_ID'];
    }
    if ( "post.php" == $hook && !class_exists( 'Classic_Editor' ) ) {
        $post_format = get_post_format( $post_id );
        wp_enqueue_script( 'xevsocore-custm-admin', plugin_dir_url( __FILE__ ) . 'assets/js/custom-admin.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( "xevsocore-custm-admin", "post_format", array( "format" => $post_format ) );
    } elseif ( "post-new.php" == $hook && !class_exists( 'Classic_Editor' ) ) {
        wp_enqueue_script( 'xevsocore-custm-admin', plugin_dir_url( __FILE__ ) . 'assets/js/custom-admin.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( "xevsocore-custm-admin", "post_format", array( "format" => 'none' ) );
    }
    if ( class_exists( 'Classic_Editor' ) ) {
        wp_enqueue_script( 'xevsocore-classic-admin', plugin_dir_url( __FILE__ ) . 'assets/js/cleditor-admin.js', array( 'jquery' ), '1.0.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'xevsocore_admin_enqueue_scripts' );

// Registering toolkit files
function xevsocore_toolkit_files() {
    wp_enqueue_style( 'video', plugin_dir_url( __FILE__ ) . 'assets/video/video.css' );
    wp_enqueue_style( 'xevsocore', plugin_dir_url( __FILE__ ) . 'assets/xevsocore.css' );
    wp_enqueue_style( 'xevso-icons', plugin_dir_url( __FILE__ ) . 'assets/css/fontello-embedded.css' );
    wp_enqueue_style( 'xevso-iconfont', plugin_dir_url( __FILE__ ) . 'assets/css/iconfont.css' );
    wp_enqueue_script( 'wow-min', plugin_dir_url( __FILE__ ) . 'assets/js/wow-min.js', array( 'jquery' ), '1.1.3', true );
    wp_enqueue_script( 'video', plugin_dir_url( __FILE__ ) . 'assets/video/video.js', array( 'jquery' ), '1.0', true );

}
add_action( 'wp_enqueue_scripts', 'xevsocore_toolkit_files' );
/**
 * Enqueue Backend Styles And Scripts.
 **/
function xevso_backend_css_js( $screen ) {
    wp_enqueue_style( 'xevso-iconfont', plugin_dir_url( __FILE__ ) . 'assets/css/iconfont.css', array(), '1.0.0', 'all' );
    wp_enqueue_style( 'xevso-flaticon', get_theme_file_uri( 'assets/css/flaticon.css' ), array(), '1.0.0', 'all' );
}
add_action( 'admin_enqueue_scripts', 'xevso_backend_css_js' );
