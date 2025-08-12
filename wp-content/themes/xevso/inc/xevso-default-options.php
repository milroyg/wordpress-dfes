<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
function xevso_default_theme_options() {
  return array(
    'xevso_copyright_text' => wp_kses(
      __('&copy; xevso 2022 | Developed by: <a href="#">Themebuzz</a>', 'xevso'),
      array(
        'a' => array(
          'href' => array(),
        ),
        'strong' => array(),
        'small' => array(),
        'span' => array(),
      )
    ),

        'xevso_not_found_text' => wp_kses(
            __('<h2>Oops!</h2><h2> That page can&rsquo;t be found.</h2><p>We Are Really Sorry But The Page You Requested is Missing.</p>', 'xevso'),
            array(
                'a' => array(
                    'href' => array(),
                ),
                'strong' => array(),
                'small' => array(),
                'span' => array(),
                'p' => array(),
                'h1' => array(),
                'h2' => array(),
                'h3' => array(),
                'h4' => array(),
                'h5' => array(),
                'h6' => array(),
            )
        ),

    'xevso_blog_title' => esc_html__('Blog Posts', 'xevso'),
    'xevso_error_page_title' => esc_html__('Page Not Found', 'xevso'),
  );
}

if ( ! function_exists( 'xevso_options' ) ) {
  function xevso_options( $option = '', $default = null ) {
    $defaults = xevso_default_theme_options();
    $options = get_option( 'xevso_Theme_Option' );
    $default  = ( ! isset( $default ) && isset( $defaults[$option] ) ) ? $defaults[$option] : $default;
    return ( isset( $options[$option] ) ) ? $options[$option] : $default;
  }
}
$xevso_enable_svg = xevso_options('xevso_enable_svg');
if(!empty($xevso_enable_svg == 'true')){
function cc_mime_types($mimes) {
$mimes['svg'] = 'image/svg+xml';
return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');
}
add_filter( 'csf_welcome_page', '__return_false' );