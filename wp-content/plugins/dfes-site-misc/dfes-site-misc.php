<?php
/**
 Plugin Name: DFES Site Misc
 Description: Miscellaneous site-wide functionality for DFES WordPress including security headers, script SRI, and minor UI tweaks.
 Version: 1.1
 Author: Milroy Gomes
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

add_action('wp_enqueue_scripts', 'dfes_site_misc_enqueue_scripts');
add_action('login_enqueue_scripts', 'dfes_site_misc_enqueue_scripts');
function dfes_site_misc_enqueue_scripts() {
  wp_enqueue_script(
    'dfes-site-misc',
    plugin_dir_url(__FILE__) . 'assets/js/dfes-site-misc.js',
    ['jquery'],
    '1.1',
    true
  );
}

add_action('wp_footer', function() {
  ?>
  <button id="feedback-float-btn" title="Give Feedback Button">Feedback</button>
  <style>
    #feedback-float-btn {
      position: fixed;
      bottom: 200px;
      right: 20px;
      background-color: #D44500;
      color: #FFFF;
      padding: 12px 18px;
      border: none;
      border-radius: 50px;
      font-size: 18px;
      font-weight: 500;
      cursor: pointer;
      box-shadow: 0 4px 6px rgba(0,0,0,0.3);
      z-index: 9999;
      transition: background-color 0.3s ease;
    }

    #feedback-float-btn:hover {
      background-color: #191919;
      float:right;
    }
  </style>
<?php
});