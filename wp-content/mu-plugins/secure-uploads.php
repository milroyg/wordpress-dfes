<?php
// mu-plugins/secure-uploads.php
defined( 'ABSPATH' ) || exit;

add_filter('wp_handle_upload_prefilter', 'sg_secure_uploaded_files');
// Keep/override upload_mimes if desired
add_filter('upload_mimes', 'sg_restrict_media_uploads');
function sg_secure_uploaded_files($file) {
    // Config
    $max_size = 5 * 1024 * 1024; // 5 MB
    $allowed_exts = array('jpg','jpeg','png','pdf');
    $allowed_mimes = array(
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'pdf'  => 'application/pdf',
    );
    $dangerous_segments = '/^(php|phtml|pl|cgi|asp|aspx|exe|sh|bash|js|svg|scr|dll)$/i';

    // Basic file props
    $name = isset($file['name']) ? $file['name'] : '';
    $tmp  = isset($file['tmp_name']) ? $file['tmp_name'] : '';
    $size = isset($file['size']) ? (int) $file['size'] : 0;
    $type = isset($file['type']) ? $file['type'] : '';

    // 0) ensure tmp present
    if (empty($tmp) || !is_uploaded_file($tmp)) {
        $file['error'] = 'Upload error: temporary file missing or invalid.';
        return $file;
    }

    // 1) max file size check (enforced early)
    if ($size > $max_size) {
        $file['error'] = 'File is too large. Maximum allowed size is 5 MB.';
        return $file;
    }

    // 2) normalize extension
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (empty($ext)) {
        $file['error'] = 'File has no extension; upload blocked.';
        return $file;
    }

    // 3) block explicitly disallowed ext (SVG especially)
    if ($ext === 'svg') {
        $file['error'] = 'SVG files are not allowed due to security risks.';
        return $file;
    }

    // 4) allowed extension check
    if (!in_array($ext, $allowed_exts, true)) {
        $file['error'] = 'This file extension is not allowed.';
        return $file;
    }

    // 5) double-extension / suspicious filename segments
    $segments = explode('.', $name);
    if (count($segments) > 1) {
        // check every segment except the last (which is the true extension)
        $pre_segments = array_slice($segments, 0, -1);
        foreach ($pre_segments as $seg) {
            if (preg_match($dangerous_segments, $seg)) {
                $file['error'] = 'Filename contains suspicious extensions and is blocked.';
                return $file;
            }
        }
    }

    // 6) MIME type detection using finfo (more reliable than client-provided type)
    $detected_mime = false;
    if (function_exists('finfo_open')) {
        $f = finfo_open(FILEINFO_MIME_TYPE);
        if ($f) {
            $detected_mime = @finfo_file($f, $tmp);
            finfo_close($f);
        }
    }

    // fallback for images: getimagesize gives MIME
    if (!$detected_mime && in_array($ext, array('jpg','jpeg','png'), true)) {
        $info = @getimagesize($tmp);
        if ($info && !empty($info['mime'])) {
            $detected_mime = $info['mime'];
        }
    }

    // final fallback to client-provided type
    if (!$detected_mime) {
        $detected_mime = $type;
    }

    // 7) ensure detected mime matches allowed for the extension
    if (!isset($allowed_mimes[$ext]) || $detected_mime !== $allowed_mimes[$ext]) {
        $file['error'] = sprintf(
            'MIME type mismatch or disallowed MIME detected (expected %s, got %s).',
            $allowed_mimes[$ext],
            $detected_mime ?: 'unknown'
        );
        return $file;
    }

    // 8) read beginning of file for signatures / lightweight content checks
    $contents = @file_get_contents($tmp);
    if ($contents === false) {
        $file['error'] = 'Unable to read uploaded file for validation.';
        return $file;
    }

    // 9) Block embedded PHP code anywhere
    if (preg_match('/<\?php/i', $contents)) {
        $file['error'] = 'Potentially malicious code detected in file.';
        return $file;
    }

    // 10) images: verify signatures and look for script tags
    if (in_array($ext, array('jpg','jpeg','png'), true)) {
        // Check PNG signature
        if ($ext === 'png' && strpos($contents, "\x89PNG") !== 0) {
            $file['error'] = 'Invalid PNG signature.';
            return $file;
        }
        // Check JPEG signature
        if (in_array($ext, array('jpg','jpeg'), true) && strpos($contents, "\xFF\xD8") !== 0) {
            $file['error'] = 'Invalid JPEG signature.';
            return $file;
        }

        // Block script tags inside images (SVG already blocked by ext, but check anyway)
        if (preg_match('/<script\b/i', $contents) || preg_match('/<svg\b/i', $contents)) {
            $file['error'] = 'Image contains potentially dangerous inline content.';
            return $file;
        }

        // Extra: getimagesize validation (already used for MIME fallback earlier)
        $check = @getimagesize($tmp);
        if ($check === false) {
            $file['error'] = 'Invalid image file.';
            return $file;
        }
    }

    // 11) PDF checks
    if ($ext === 'pdf') {
        // check header
        if (strpos($contents, '%PDF-') !== 0) {
            $file['error'] = 'Invalid PDF file header.';
            return $file;
        }
        // check footer exists (%%EOF)
        if (strrpos($contents, '%%EOF') === false) {
            $file['error'] = 'Corrupted or truncated PDF file.';
            return $file;
        }
        // block embedded JS / actions
         if (preg_match('/\/(JavaScript|JS|AA|OpenAction)\s*(<<|\/|[^\w])/i', $contents)) {
        // Optional deeper check: verify it's followed by dictionary or parentheses content
        if (preg_match('/\/(JavaScript|JS|AA|OpenAction)\s*(\(|<<)/is', $contents)) {
            $file['error'] = 'PDF contains embedded scripts and is blocked for security.';
            return $file;
        }
    }
        // optional: block object streams
        // if (preg_match('/\/ObjStm\b/i', $contents)) {
        //     $file['error'] = 'PDF contains object streams (potentially unsafe).';
        //     return $file;
        // }
    }

    // 12) final safety: normalize filename (remove control chars)
    $clean_name = preg_replace('/[^\w\.\-\_ ]+/u', '', $name);
    if ($clean_name !== $name) {
        // rename to safe name to avoid surprises (WordPress will also sanitize)
        $file['name'] = $clean_name;
    }

    // If all checks pass, return file unchanged (or with sanitized name)
    return $file;
}

function sg_restrict_media_uploads($mime_types) {
    return array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'png'          => 'image/png',
        'pdf'          => 'application/pdf',
    );
}