<?php
// Load parent theme stylesheet
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('xevso-parent-style', get_template_directory_uri() . '/style.css');
});


//ACF Delete pdf
add_filter('acf/update_value/name=upload_pdf', function($value, $post_id, $field) {
    // Get the old attachment ID
    $old_value = get_field('upload_pdf', $post_id, false); // false = raw ID

    // If a new file was uploaded, and it's different from old, delete old
    if ($old_value && $value && $value != $old_value) {
        wp_delete_attachment($old_value, true);
    }

    // If the file is removed (i.e., $value is empty) but old file exists, delete it
    if (empty($value) && $old_value) {
        wp_delete_attachment($old_value, true);
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
        session_regenerate_id(true); // Replace session ID securely
    }
}, 10, 2);

// Force new session ID and destroy old one after logout
add_action('wp_logout', function() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Regenerate to invalidate old ID
        session_regenerate_id(true);

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

    if (is_ssl()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }

    // Content Security Policy (CSP) for login page only
    $csp = "default-src 'self'; ";
    $csp .= "script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com https://challenges.cloudflare.com blob:; ";
    $csp .= "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ";
    $csp .= "img-src 'self' data: https://dfes.goa.gov.in https://s.w.org https://secure.gravatar.com https://*.tile.openstreetmap.org https:; ";
    $csp .= "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:; ";
    $csp .= "frame-src 'self' https://www.google.com/ https://www.google.com/recaptcha/ https://challenges.cloudflare.com; ";

    header("Content-Security-Policy: $csp");
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
                    'expires'  => 0,
                    'path'     => COOKIEPATH,
                    'domain'   => COOKIE_DOMAIN,
                    'secure'   => true,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
            if (strpos($name, 'wordpress_sec_') === 0) {
                // admin/auth cookie → Strict
                setcookie($name, $value, [
                    'expires'  => 0,
                    'path'     => COOKIEPATH,
                    'domain'   => COOKIE_DOMAIN,
                    'secure'   => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            }
        }
    }
});


