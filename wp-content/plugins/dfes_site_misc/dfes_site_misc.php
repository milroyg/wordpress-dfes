<?php
/**
 * Plugin Name: DFES Site Misc
 * Description: Miscellaneous site-wide functionality for DFES WordPress including security headers, script SRI, and minor UI tweaks.
 * Version: 1.0.0
 * Author: DFES
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter( 'wpcf7_mail_components', 'strip_urls_from_cf7_email', 10, 3 );
function strip_urls_from_cf7_email( $components, $contact_form, $submit ) {
  // Regex pattern to match most URLs
  $url_pattern = '/\b((https?|ftp|file):\/\/|www\.)[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';

  // Strip URLs from the Subject field
  if ( ! empty( $components['subject'] ) ) {
    $components['subject'] = preg_replace( $url_pattern, '[URL REMOVED]', $components['subject'] );
  }

  // Strip URLs from the Message Body field
  if ( ! empty( $components['body'] ) ) {
    $components['body'] = preg_replace( $url_pattern, '[URL REMOVED]', $components['body'] );
  }

  return $components;
}
