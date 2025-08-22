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


function allow_json_uploads($mimes) {
    $mimes['json'] = 'application/json';
    return $mimes;
}
add_filter('upload_mimes', 'allow_json_uploads');


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



//Sitemap
function sitemap_menu_shortcode($atts) {
    $atts = shortcode_atts([
        'menu' => '',
    ], $atts);

    $menu_name = $atts['menu'];
    $menu = wp_get_nav_menu_items($menu_name);

    if (!$menu) return '<p>Menu not found.</p>';

    // Group menu items by parent, exclude only "मराठी" or similar language switchers
    $parents = [];
   foreach ($menu as $item) {
    // Skip Polylang language switcher items
    $classes = is_array($item->classes) ? $item->classes : [];

    if (
        in_array('lang-item', $classes) ||
        in_array('menu-item-language', $classes) ||
        strpos(strtolower($item->title), 'english') !== false ||
        strpos(strtolower($item->title), 'मराठी') !== false ||
        strpos(strtolower($item->title), 'konkani') !== false
    ) {
        continue;
    }

    $parents[$item->menu_item_parent][] = $item;
}


    // Recursive function for submenus
    function build_sub_menu($parent_id, $parents) {
        if (!isset($parents[$parent_id])) return '';

        $html = '<ul style="padding-left:1rem; list-style:disc; font-family:\'Segoe UI\', Roboto, sans-serif; margin-top:0.5rem;">';
        foreach ($parents[$parent_id] as $item) {
            $html .= '<li style="margin-bottom:0.4rem; line-height:1.6;">';
            $html .= '<a href="' . esc_url($item->url) . '" style="text-decoration:none; color:#1a1a1a;">' . esc_html($item->title) . '</a>';
            $html .= build_sub_menu($item->ID, $parents);
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    // Top-level blocks
   $output = '<div class="sitemap-wrapper" style="
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.5rem;
    font-family: \'Segoe UI\', Roboto, sans-serif;
    max-width: 900px;
    margin: 2rem auto;
    justify-content: center;
">';


    if (isset($parents[0])) {
        foreach ($parents[0] as $parent_item) {
            $output .= '<div class="sitemap-block" style="border-radius:10px; background:#fff; box-shadow:0 4px 15px rgba(0,0,0,0.07); padding:1.2rem;">';
            $output .= '<h3 style="margin:0 0 0.8rem 0; font-size:1.1rem; background:#16203b; color:#fff; padding:0.6rem 1rem; border-radius:6px 6px 0 0;">';
            $output .= '<a href="' . esc_url($parent_item->url) . '" style="color:#fff; text-decoration:none;">' . esc_html($parent_item->title) . '</a></h3>';
            $output .= build_sub_menu($parent_item->ID, $parents);
            $output .= '</div>';
        }
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('sitemap_menu', 'sitemap_menu_shortcode');

//Classic editor
add_filter('the_content', function($content) {
    // Check if this is in the admin area and not during REST or block editor (Gutenberg)
    if (is_admin()) {
        // Check if Classic Editor is being used by inspecting the global $pagenow
        global $pagenow;

        if ($pagenow === 'post.php' || $pagenow === 'post-new.php') {
            // Check if Gutenberg is not used
            if (!function_exists('use_block_editor_for_post') || !use_block_editor_for_post(get_the_ID())) {
                // Classic Editor is in use
                return wp_kses_post($content);
            }
        }
    }

    // Also check if viewing content on the front end
    if (!is_admin()) {
        // If the post was created using Classic Editor, sanitize it
        $post_id = get_the_ID();
        if (!function_exists('use_block_editor_for_post') || !use_block_editor_for_post($post_id)) {
            return wp_kses_post($content);
        }
    }

    // Otherwise, return content untouched
    return $content;
}, 5); // Run early to sanitize before other filters

add_filter( 'rest_authentication_errors', function( $result ) {
    if ( true === $result || is_wp_error( $result ) ) {
        return $result;
    }

    if ( isset( $_SERVER['REQUEST_URI'] ) ) {
        $uri = $_SERVER['REQUEST_URI'];

        // Allow CF7 requests
        if ( strpos( $uri, '/wp-json/contact-form-7/v1/contact-forms' ) !== false ) {
            return null;
        }

      if ( isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/dfes/data/live/update') !== false ) {
    return null; // allow public
}
    }

    if ( ! is_user_logged_in() ) {
        return new WP_Error(
            'access_denied',
            __( 'Access Denied' ),
            array( 'status' => 401 )
        );
    }

    return $result;
});


// Only apply on login page
add_action('login_head', function () {
    ob_start(function ($buffer) {
        // Replace autocomplete attributes
        $buffer = str_replace('autocomplete="username"', 'autocomplete="off"', $buffer);
        $buffer = str_replace('autocomplete="current-password"', 'autocomplete="off"', $buffer);
        $buffer = str_replace('<form', '<form autocomplete="off"', $buffer);
        return $buffer;
    });
});

// Remove the default inline style from AccessibleWP Toolbar : style present in body moves to head
remove_action('wp_footer', 'acwp_iconsize_style');

// Optional: Add your cleaned custom styles correctly in <head> or <footer>
add_action('wp_head', function () {
    ?>
    <style>
        body #acwp-toolbar-btn-wrap {
            top: 120px;
            right: 20px;
        }
        .acwp-toolbar {
            top: -100vh;
            right: 20px;
        }
        .acwp-toolbar.acwp-toolbar-show {
            top: 55px;
        }
    </style>
    <?php
});
//To remove speculation rules
add_action('template_redirect', function () {
    ob_start(function ($buffer) {
        return preg_replace('#<script type="speculationrules">.*?</script>#s', '', $buffer);
    });
});

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