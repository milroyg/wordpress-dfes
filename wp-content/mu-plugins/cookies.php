<?php
add_action('send_headers', function() {
    // collect all Set-Cookie headers that were added so far
    $headers = headers_list();
    $cookies = [];
    foreach ($headers as $h) {
        if (stripos($h, 'Set-Cookie:') === 0) {
            // remove leading "Set-Cookie:" and trim
            $cookies[] = trim(substr($h, strlen('Set-Cookie:')));
        }
    }

    if (empty($cookies)) {
        return;
    }

    // Remove existing Set-Cookie headers so we can re-emit modified ones
    if (function_exists('header_remove')) {
        header_remove('Set-Cookie');
    } else {
        // if header_remove unavailable (very old PHP), bail out early
        return;
    }

    foreach ($cookies as $cookie) {
        $modified = $cookie;

        // ensure Secure flag (only if connection is HTTPS)
        if (is_ssl() && !preg_match('/;\s*secure/i', $modified)) {
            $modified .= '; Secure';
        }

        // ensure HttpOnly flag
        if (!preg_match('/;\s*httponly/i', $modified)) {
            $modified .= '; HttpOnly';
        }

        // detect cookie name (first token before '=')
        $firstPair = explode(';', $modified)[0];
        $cookieName = trim(strtok($firstPair, '='));

        // normalize/replace existing SameSite if present, otherwise append
        if (stripos($cookieName, 'wordpress_sec_') === 0 || $cookieName === session_name()) {
            if (preg_match('/;\s*samesite=/i', $modified)) {
                $modified = preg_replace('/;\s*samesite=[^;]*/i', '; SameSite=Strict', $modified);
            } else {
                $modified .= '; SameSite=Strict';
            }
        } elseif (stripos($cookieName, 'wordpress_logged_in_') === 0) {
            if (preg_match('/;\s*samesite=/i', $modified)) {
                $modified = preg_replace('/;\s*samesite=[^;]*/i', '; SameSite=Lax', $modified);
            } else {
                $modified .= '; SameSite=Lax';
            }
        } else {
            // For other cookies you can append Strict or leave alone.
            // We'll not force SameSite for unknown cookies to avoid breaking plugins.
        }

        // re-emit the cookie header (append, don't replace)
        header('Set-Cookie: ' . $modified, false);
    }
}, 100);
