<?php
//Live Calls Chart
require_once get_template_directory() . '/inc/live-calls-charts.php';
require_once get_template_directory() . '/inc/dmrp-map.php';

//Live Vehicle
require_once get_template_directory() . '/inc/live-vehicle.php';

add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('xevso-parent-style', get_template_directory_uri() . '/style.css');
  wp_dequeue_style('bootstrap');
  wp_dequeue_script('bootstrap');
  wp_enqueue_style('bootstrap5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
  wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js');
  wp_enqueue_script('bootstrap5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array(), null, true);

  if (is_page([9616, 9625])) {
    // Load Leaflet core
    wp_enqueue_style('leaflet-css', get_template_directory_uri() . '/assets/css/leaflet.css', [], '1.9.4');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);
    // Load MarkerCluster plugin
    wp_enqueue_style('leaflet-markercluster-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css');
    wp_enqueue_style('leaflet-markercluster-default-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css');
    wp_enqueue_script('leaflet-markercluster-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js', ['leaflet-js'], null, true);
  }

}, 20);

add_filter('script_loader_tag', function ($tag, $handle, $src) {
  if ($handle === 'leaflet-js') {
    $integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
    return '<script src="' . esc_url($src) . '" integrity="' . esc_attr($integrity) . '" crossorigin=""></script>';
  }
  return $tag;
}, 10, 3);

add_filter('style_loader_tag', function ($html, $handle, $href, $media) {
  if ($handle === 'leaflet-css') {
    $integrity = 'sha256-V3EH7RVdB4sO7/yu12GB6tryHbOuqdqnqeV+ewWGAN8=';
    return '<link rel="stylesheet" href="' . esc_url($href) . '" integrity="' . esc_attr($integrity) . '" crossorigin="anonymous" media="' . esc_attr($media) . '" />';
  }
  return $html;
}, 10, 4);

//Hide Konkani 
add_filter('wp_nav_menu_objects', function ($items, $args) {
  foreach ($items as $key => $item) {
    // Check if the menu item is Konkani using ID or class
    if (
      (isset($item->ID) && $item->ID == 10058) || // By menu item ID
      (isset($item->classes) && in_array('lang-item-kok', $item->classes)) // By class
    ) {
      unset($items[$key]);
    }
  }
  return $items;
}, 10, 2);

//Copy Paste Buffer
 function my_custom_login_script() {
        wp_enqueue_script(
            'my-custom-login-script', // Handle for your script
            get_template_directory_uri() . '/assets/copypastebuffer.js', // Path to your script
            array('jquery'), // Dependencies (e.g., jQuery if needed)
            null, // Version number (optional)
            true // Load in the footer (recommended)
        );
    }
    add_action('login_enqueue_scripts', 'my_custom_login_script');