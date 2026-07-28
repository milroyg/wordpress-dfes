<?php

//Sitemap
function sitemap_menu_shortcode($atts) {
  $atts = shortcode_atts([
    'menu' => '',
  ], $atts);

  $menu_name = $atts['menu'];
  $menu = wp_get_nav_menu_items($menu_name);

  if (!$menu) {
    return '<p>Menu not found.</p>';
  }

  // Group menu items by parent, exclude only "मराठी" or similar language switchers
  $parents = [];
  foreach ($menu as $item) {
    // Skip Polylang language switcher items
    $classes = is_array($item->classes) ? $item->classes : [];

    if (
      in_array('lang-item', $classes) ||
      in_array('menu-item-language', $classes) ||
      strpos(strtolower($item->title), 'english') !== FALSE ||
      strpos(strtolower($item->title), 'मराठी') !== FALSE ||
      strpos(strtolower($item->title), 'konkani') !== FALSE
    ) {
      continue;
    }

    $parents[$item->menu_item_parent][] = $item;
  }

  // Recursive function for submenus
  function build_sub_menu($parent_id, $parents) {
    if (!isset($parents[$parent_id])) {
      return '';
    }

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

add_filter('rest_authentication_errors', function($result) {
  if (TRUE === $result || is_wp_error($result)) {
    return $result;
  }

  if (isset($_SERVER['REQUEST_URI'])) {
    $uri = $_SERVER['REQUEST_URI'];

    // Allow CF7 requests
    if (strpos($uri, '/wp-json/contact-form-7/v1/contact-forms') !== FALSE) {
      return NULL;
    }

    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/dfes/data/live/update') !== FALSE) {
      return NULL; // allow public
    }
  }

  if (!is_user_logged_in()) {
    return new WP_Error(
      'access_denied',
      __('Access Denied'),
      ['status' => 401]
    );
  }

  return $result;
});

// Remove the default inline style from AccessibleWP Toolbar : style present in body moves to head
remove_action('wp_footer', 'acwp_iconsize_style');

// Optional: Add your cleaned custom styles correctly in <head> or <footer>
add_action('wp_head', function() {
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
add_action('template_redirect', function() {
  ob_start(function($buffer) {
    return preg_replace('#<script type="speculationrules">.*?</script>#s', '', $buffer);
  });
});

//ACF Delete pdf
add_filter('acf/update_value/name=upload_pdf', function($value, $post_id, $field) {
  // Get the old attachment ID
  $old_value = get_field('upload_pdf', $post_id, FALSE); // false = raw ID

  // If a new file was uploaded, and it's different from old, delete old
  if ($old_value && $value && $value != $old_value) {
    wp_delete_attachment($old_value, TRUE);
  }

  // If the file is removed (i.e., $value is empty) but old file exists, delete it
  if (empty($value) && $old_value) {
    wp_delete_attachment($old_value, TRUE);
  }

  return $value;
}, 10, 3);

/*
Sanitize title
*/
//  Sanitize post titles before saving (works for posts, pages, CPTs)
add_filter('wp_insert_post_data', function($data) {
  if (isset($data['post_title'])) {
    // Force plain text in titles
    $data['post_title'] = sanitize_text_field($data['post_title']);
  }
  return $data;
}, 10, 1);

// Sanitize post content before saving
add_filter('content_save_pre', function($content) {
  // Allow only safe HTML tags/attributes
  return wp_kses_post($content);
});

// Force new session ID after login
add_action('wp_login', function($user_login, $user) {
  if (session_status() === PHP_SESSION_ACTIVE) {
    session_regenerate_id(TRUE); // Replace session ID securely
  }
}, 10, 2);

// Force new session ID and destroy old one after logout
add_action('wp_logout', function() {
  if (session_status() === PHP_SESSION_ACTIVE) {
    // Regenerate to invalidate old ID
    session_regenerate_id(TRUE);

    // Clear session variables
    $_SESSION = [];

    // Expire PHPSESSID cookie explicitly
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }

    // Destroy session on server
    session_destroy();
  }
});

// User login Headers
add_action('init', function() {
  // Security headers for login page
  header('X-Frame-Options: SAMEORIGIN');
  header('X-Content-Type-Options: nosniff');
  header('X-XSS-Protection: 1; mode=block');
  header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');

  // Harden PHP session cookies (if plugins call session_start)
  @ini_set('session.cookie_secure', 1);
  @ini_set('session.cookie_httponly', 1);
  @ini_set('session.cookie_samesite', 'Strict');

  // --- Enforce SameSite+Secure on WP cookies ---
  if (isset($_COOKIE)) {
    foreach ($_COOKIE as $name => $value) {
      if (strpos($name, 'wordpress_logged_in_') === 0) {
        // frontend login cookie → Lax
        setcookie($name, $value, [
          'expires' => 0,
          'path' => COOKIEPATH,
          'domain' => COOKIE_DOMAIN,
          'secure' => TRUE,
          'httponly' => TRUE,
          'samesite' => 'Lax',
        ]);
      }
      if (strpos($name, 'wordpress_sec_') === 0) {
        // admin/auth cookie → Strict
        setcookie($name, $value, [
          'expires' => 0,
          'path' => COOKIEPATH,
          'domain' => COOKIE_DOMAIN,
          'secure' => TRUE,
          'httponly' => TRUE,
          'samesite' => 'Strict',
        ]);
      }
    }
  }
});
