<?php
//Live Calls Chart
require_once get_template_directory() . '/inc/live-calls-charts.php';
require_once get_template_directory() . '/inc/dmrp-map.php';

//Live Vehicle
require_once get_template_directory() . '/inc/live-vehicle.php';

add_action('admin_enqueue_scripts', function() {
    // Remove WP's bundled TinyMCE
    wp_deregister_script('tinymce');
    wp_deregister_script('wp-tinymce');

    // Register & enqueue the latest version
    wp_register_script(
        'tinymce',
        'https://cdn.jsdelivr.net/npm/tinymce@8.1.2/tinymce.min.js',
        [],
        null,
        true
    );
    wp_enqueue_script('tinymce');
});

add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('xevso-parent-style', get_template_directory_uri() . '/style.css');
  // wp_dequeue_style('bootstrap');
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

},20);

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
    // 1️⃣ Load CryptoJS (encryption library)
    wp_enqueue_script(
        'crypto-js',
        'https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js',
        [],
        null,
        true
    );

    // 2️⃣ Load copy-paste protection script
    wp_enqueue_script(
        'copy-paste-buffer',
        get_template_directory_uri() . '/assets/copypastebuffer.js',
        ['jquery'],
        null,
        true
    );

    // 3️⃣ Load encryption script (depends on crypto-js + jQuery)
    wp_enqueue_script(
        'encrypt-login',
        get_template_directory_uri() . '/assets/encrypt_login.js',
        ['jquery', 'crypto-js'],
        null,
        true
    );
}
add_action('login_enqueue_scripts', 'my_custom_login_script');

// 📌 Decrypt AES-encrypted login values before authentication
add_filter('authenticate', function($user, $username, $password) {
    // Only decrypt if data looks encrypted (starts with "U2Fsd")
    if (strpos($username, 'U2Fsd') === 0 || strpos($password, 'U2Fsd') === 0) {
        require_once ABSPATH . 'wp-includes/class-phpass.php';
        require_once ABSPATH . 'wp-includes/pluggable.php';

        // AES Key must match the one in encrypt_login.js
        $key = "my32charsecretkeymy32charsecretkey";

        // Decrypt both fields
        $username = my_aes_decrypt($username, $key);
        $password = my_aes_decrypt($password, $key);
    }

    // Now try to authenticate again with decrypted credentials
    return wp_authenticate_username_password(null, $username, $password);
}, 10, 3);

// 🔐 PHP AES decryption (CryptoJS-compatible)
function my_aes_decrypt($ciphertext, $passphrase) {
    if (strpos($ciphertext, 'U2FsdGVkX1') !== 0) {
        return $ciphertext; // not encrypted
    }

    $data = base64_decode($ciphertext);
    $salt = substr($data, 8, 8);
    $key_iv = my_aes_evp_bytes($passphrase, $salt);
    $key = substr($key_iv, 0, 32);
    $iv = substr($key_iv, 32, 16);

    return openssl_decrypt(substr($data, 16), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

// 🔑 Helper for deriving key/IV like CryptoJS
function my_aes_evp_bytes($pass, $salt) {
    $data = '';
    $d = '';
    while (strlen($data) < 48) {
        $d = md5($d . $pass . $salt, true);
        $data .= $d;
    }
    return substr($data, 0, 48);
}
//img:is([sizes="auto" i], [sizes^="auto," i])	Property contain-intrinsic-size doesn't exist : 3000px 1500px
add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );