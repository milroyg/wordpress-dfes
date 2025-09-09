<?php
// mu-plugins/secure-uploads.php

function secure_uploaded_files($file) {
    $file_path = $file['tmp_name'];
    $type      = $file['type'];

    // Allowed MIME types
    $allowed_types = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
    ];

    if (!in_array($type, $allowed_types, true)) {
        $file['error'] = 'This file type is not allowed for security reasons.';
        return $file;
    }

    $contents = @file_get_contents($file_path);

    // Block PHP code
    if (preg_match('/<\?php/i', $contents)) {
        $file['error'] = 'Potentially malicious code detected in file.';
        return $file;
    }

    // Block suspicious JS in images
    if (str_starts_with($type, 'image/')) {
        if (preg_match('/<script\b/i', $contents)) {
            $file['error'] = 'Malicious script detected inside the file.';
            return $file;
        }
    }

    // === PDF validation ===
    if ($type === 'application/pdf') {
        // Check PDF header
        if (strpos($contents, '%PDF-') !== 0) {
            $file['error'] = 'Invalid PDF file.';
            return $file;
        }

        // Check PDF footer
        if (strrpos($contents, '%%EOF') === false) {
            $file['error'] = 'Corrupted or invalid PDF file.';
            return $file;
        }

        // Detect embedded JavaScript objects in PDF
        // Common PDF JS patterns: /JavaScript, /JS, /AA, /OpenAction
        if (preg_match('/\/(JavaScript|JS|AA|OpenAction)\b/i', $contents)) {
            $file['error'] = 'PDF contains embedded scripts and is blocked for security.';
            return $file;
        }

        // Optional: reject PDFs with suspicious object streams (advanced)
        if (preg_match('/\/ObjStm\b/i', $contents)) {
            $file['error'] = 'PDF contains object streams (potentially unsafe).';
            return $file;
        }
    }

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'secure_uploaded_files');

/*
Medial upload limit
*/
// Restrict MIME types
function restrict_media_uploads($mime_types){
    return array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'png'          => 'image/png',
        'gif'          => 'image/gif',
        'pdf'          => 'application/pdf'
    );
}
add_filter('upload_mimes', 'restrict_media_uploads');

// Restrict upload size (max 5 MB)
function restrict_upload_size( $size ) {
    return 5 * 1024 * 1024; // 5 MB
}
add_filter( 'upload_size_limit', 'restrict_upload_size' );