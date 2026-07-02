<?php
//Live Calls Chart
require_once get_template_directory() . '/inc/live-calls-charts.php';
require_once get_template_directory() . '/inc/dmrp-map.php';

//Live Vehicle
require_once get_template_directory() . '/inc/live-vehicle.php';

add_action('admin_enqueue_scripts', function () {
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

add_action('wp_enqueue_scripts', function() {
  wp_dequeue_script('bootstrap');
  wp_dequeue_script('eac-image-gallery');
  wp_deregister_script('eac-image-gallery');

  wp_register_script_module('eac-image-gallery', site_url('/wp-content/plugins/elementor-addon-components/assets/js/elementor/eac-image-gallery.min.js'), ['jquery'], '1.0.0');
  wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js');
  wp_enqueue_script('bootstrap5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', [], NULL, TRUE);
  wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], NULL, TRUE);
  wp_enqueue_script('markercluster-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js', ['leaflet-js'], NULL, TRUE);

  wp_enqueue_style('xevso-parent-style', get_template_directory_uri() . '/style.css');
  wp_enqueue_style('leaflet-css', get_template_directory_uri() . '/assets/css/leaflet.css', [], '1.9.4');
  wp_enqueue_style('bootstrap5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
  wp_enqueue_style('leaflet-markercluster-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css');
  wp_enqueue_style('leaflet-markercluster-default-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css');
}, 20);

add_filter('style_loader_tag', function ($html, $handle, $href, $media) {
    if ($handle === 'leaflet-css') {
        $integrity = 'sha256-V3EH7RVdB4sO7/yu12GB6tryHbOuqdqnqeV+ewWGAN8=';
        return '<link rel="stylesheet" href="' . esc_url($href) . '" integrity="' . esc_attr($integrity) . '" crossorigin="anonymous" media="' . esc_attr($media) . '" />';
    }
    return $html;
}, 10, 4);

function my_custom_login_script()
{
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
add_filter('authenticate', function ($user, $username, $password) {
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
function my_aes_decrypt($ciphertext, $passphrase)
{
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
function my_aes_evp_bytes($pass, $salt)
{
    $data = '';
    $d = '';
    while (strlen($data) < 48) {
        $d = md5($d . $pass . $salt, true);
        $data .= $d;
    }
    return substr($data, 0, 48);
}
//img:is([sizes="auto" i], [sizes^="auto," i])	Property contain-intrinsic-size doesn't exist : 3000px 1500px
add_filter('wp_img_tag_add_auto_sizes', '__return_false');

//HTML Validation

// 1. Remove the default inline style from AccessibleWP Toolbar : style present in body moves to head
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

//2. Swapped role="button" to role="presentation"
add_filter('wp_nav_menu', function ($nav) {
    // Only target submenu containers
    $nav = str_replace(
        '<div class="hfe-has-submenu-container" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">',
        '<div class="hfe-has-submenu-container" tabindex="0" role="presentation">',
        $nav
    );
    return $nav;
}, 20);

//4. Moved Timeline plugin (ctl_common_style-inline-css) <style> body to head
add_action('template_redirect', function () {
    ob_start(function ($buffer) {
        // Move ctl_common_style-inline-css from body to head
        if (preg_match("/<style id='ctl_common_style-inline-css'[^>]*>.*?<\/style>/s", $buffer, $matches)) {
            // Remove from body
            $buffer = str_replace($matches[0], '', $buffer);

            // Inject into head: replace <head> with <head> + style
            $buffer = preg_replace("/<head([^>]*)>/i", "<head$1>\n" . $matches[0], $buffer, 1);
        }
        return $buffer;
    });
});


// Remove <p>,<br> HTML validation in CF7
add_filter('wpcf7_autop_or_not', '__return_false');

// Allow Editors to manage Cool Timeline
add_action('admin_menu', function () {
    global $menu, $submenu;

    // Change main menu capability
    if (isset($menu)) {
        foreach ($menu as $key => $item) {
            if (isset($item[2]) && $item[2] === 'cool-plugins-timeline-addon') {
                $menu[$key][1] = 'edit_pages';
            }
        }
    }

    // Change submenu capabilities
    if (isset($submenu['cool-plugins-timeline-addon'])) {
        foreach ($submenu['cool-plugins-timeline-addon'] as $key => $item) {
            // Check if the item is an array and has the capability set
            if (is_array($item) && isset($item[1])) {
                $submenu['cool-plugins-timeline-addon'][$key][1] = 'edit_pages';
            }
        }
    }
}, 999);

// Fix Cool Timeline conflict with older Codestar Framework (v2.2.4 vs v2.2.8)
add_action('init', function () {
    // Manually load the datetime field class if it's missing (v2.2.4 doesn't have it)
    if (class_exists('CSF') && !class_exists('CSF_Field_datetime')) {
        $datetime_file = WP_PLUGIN_DIR . '/cool-timeline/admin/codestar-framework/fields/datetime/datetime.php';
        if (file_exists($datetime_file)) {
            require_once $datetime_file;
        }
    }
}, 1);

add_action('admin_enqueue_scripts', function () {
    // specific to Cool Timeline pages/post type
    $screen = get_current_screen();
    if ($screen && (strpos($screen->id, 'cool_timeline') !== false || $screen->post_type === 'cool_timeline')) {

        // Deregister old CSF scripts (from standalone plugin)
        wp_deregister_script('csf');
        wp_deregister_script('csf-plugins');
        wp_deregister_style('csf');

        // Register Cool Timeline's CSF scripts (v2.2.8) which support datetime field
        $ctl_csf_url = WP_PLUGIN_URL . '/cool-timeline/admin/codestar-framework/assets';
        $ctl_csf_ver = '2.2.8';

        wp_enqueue_style('csf', $ctl_csf_url . '/css/style.min.css', array(), $ctl_csf_ver, 'all');
        wp_enqueue_script('csf-plugins', $ctl_csf_url . '/js/plugins.min.js', array(), $ctl_csf_ver, true);
        wp_enqueue_script('csf', $ctl_csf_url . '/js/main.min.js', array('csf-plugins'), $ctl_csf_ver, true);

        // localized script for csf (from v2.2.8)
        wp_localize_script('csf', 'csf_vars', array(
            'color_palette' => apply_filters('csf_color_palette', array()),
            'i18n' => array(
                'confirm' => esc_html__('Are you sure?', 'csf'),
                'typing_text' => esc_html__('Please enter %s or more characters', 'csf'),
                'searching_text' => esc_html__('Searching...', 'csf'),
                'no_results_text' => esc_html__('No results found.', 'csf'),
            ),
        ));
    }
}, 100);
// Fix timing issue: Re-run CSF setup to pick up Cool Timeline's late-registered options
add_action('init', array('CSF', 'setup'), 999);

function add_multiple_sri_attributes($tag, $handle, $src) {
  $scripts_to_protect = [
    'chart-js' => 'sha384-jb8JQMbMoBUzgWatfe6COACi2ljcDdZQ2OxczGA3bGNeWe+6DChMTBJemed7ZnvJ',
    'crypto-js' => 'sha384-S3wQ/l0OsbJoFeJC81UIr3JOlx/OzNJpRt1bV+yhpWQxPAahfpQtpxBSfn+Isslc',
    'bootstrap5' => 'sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz',
    'leaflet-js' => 'sha384-cxOPjt7s7Iz04uaHJceBmS+qpjv2JkIHNVcuOrM+YHwZOmJGBXI00mdUXEq65HTH',
    'markercluster-js'   => 'sha384-eXVCORTRlv4FUUgS/xmOyr66XBVraen8ATNLMESp92FKXLAMiKkerixTiBvXriZr',
  ];

  if (array_key_exists($handle, $scripts_to_protect)) {
    $hash = $scripts_to_protect[$handle];
    $tag = str_replace(' src', " integrity='{$hash}' crossorigin='anonymous' src", $tag);
  }

  return $tag;
}
add_filter('script_loader_tag', 'add_multiple_sri_attributes', 10, 3);

function add_style_sri_attributes($tag, $handle, $href, $media) {
  $styles_to_protect = [
    'bootstrap5' => 'sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM',
    'leaflet-markercluster-css' => 'sha384-pmjIAcz2bAn0xukfxADbZIb3t8oRT9Sv0rvO+BR5Csr6Dhqq+nZs59P0pPKQJkEV',
    'leaflet-markercluster-default-css' => 'sha384-wgw+aLYNQ7dlhK47ZPK7FRACiq7ROZwgFNg0m04avm4CaXS+Z9Y7nMu8yNjBKYC+',
  ];
  if (array_key_exists($handle, $styles_to_protect)) {
    $hash = $styles_to_protect[$handle];
    $tag = str_replace(' href', " integrity='{$hash}' crossorigin='anonymous' href", $tag);
  }
  return $tag;
}
add_filter('style_loader_tag', 'add_style_sri_attributes', 10, 4);
