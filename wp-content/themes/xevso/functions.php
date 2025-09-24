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
//Live Calls Chart
require_once get_template_directory() . '/inc/live-calls-charts.php';
require_once get_template_directory() . '/inc/dmrp-map.php';

//Live Vehicle
require_once get_template_directory() . '/inc/live-vehicle.php';




function load_bootstrap_and_custom_css() {
    // Load Bootstrap CSS
	 wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
	  wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js');

    // Load Bootstrap JS
	    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'load_bootstrap_and_custom_css');


function enqueue_leaflet_cluster_scripts() {
	    if (is_page([9616, 9625])) {
     // Load Leaflet core
	wp_enqueue_style('leaflet-css', get_template_directory_uri() . '/assets/css/leaflet.css', [], '1.9.4');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);
    // Load MarkerCluster plugin
    wp_enqueue_style('leaflet-markercluster-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css');
    wp_enqueue_style('leaflet-markercluster-default-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css');
    wp_enqueue_script('leaflet-markercluster-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js', ['leaflet-js'], null, true);
}
}
add_action('wp_enqueue_scripts', 'enqueue_leaflet_cluster_scripts');

add_filter('script_loader_tag', 'add_leaflet_script_attributes', 10, 3);
function add_leaflet_script_attributes($tag, $handle, $src) {
    if ($handle === 'leaflet-js') {
        $integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
        return '<script src="' . esc_url($src) . '" integrity="' . esc_attr($integrity) . '" crossorigin=""></script>';
    }
    return $tag;
}
add_filter('style_loader_tag', function ($html, $handle, $href, $media) {
    if ($handle === 'leaflet-css') {
        $integrity = 'sha256-V3EH7RVdB4sO7/yu12GB6tryHbOuqdqnqeV+ewWGAN8=';
        return '<link rel="stylesheet" href="' . esc_url($href) . '" integrity="' . esc_attr($integrity) . '" crossorigin="anonymous" media="' . esc_attr($media) . '" />';
    }
    return $html;
}, 10, 4);




//Hide Konkani 
add_filter( 'wp_nav_menu_objects', 'remove_konkani_menu_item', 10, 2 );

function remove_konkani_menu_item( $items, $args ) {
    foreach ( $items as $key => $item ) {
        // Check if the menu item is Konkani using ID or class
        if (
            ( isset( $item->ID ) && $item->ID == 10058 ) || // By menu item ID
            ( isset( $item->classes ) && in_array( 'lang-item-kok', $item->classes ) ) // By class
        ) {
            unset( $items[$key] );
        }
    }
    return $items;
}