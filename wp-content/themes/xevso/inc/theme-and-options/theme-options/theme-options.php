<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
//
// Set a unique slug-like ID
//
$xevsoThemeOption = 'xevso_Theme_Option';

//
// Create options
//
CSF::createOptions( $xevsoThemeOption, array(
  'framework_title' => 'xevso Theme Options <small>by Themebuzz</small>',
  'menu_title' => 'Theme Options',
  'menu_slug'  => 'theme-options',
  'menu_type'   => 'submenu',
  'menu_parent' => 'admin.php',
  'class'      => 'xevso-theme-option-wrapper',
  'defaults'    => xevso_default_theme_options(),
));

require_once 'general-options.php';
require_once 'typography-options.php';
require_once 'header-options.php';
// Create layout and options section
CSF::createSection($xevsoThemeOption, array(
  'title' => esc_html__('Layout & Options', 'xevso'),
  'id'    => 'xevso_page_options',
  'icon'  => 'fa fa-calculator',
));
require_once 'banner-options.php';
require_once 'blog-page-options.php';
require_once 'single-post-options.php';
require_once 'archive-page-options.php';
require_once 'search-page-options.php';
require_once 'error-page-options.php';
require_once 'footer-options.php';
CSF::createSection($xevsoThemeOption, array(
  'title' => esc_html__('Code Editor', 'xevso'),
  'id'    => 'xevso_code_editor_options',
  'icon'  => 'fa fa-code',
));
CSF::createSection($xevsoThemeOption, array(
  'parent' => 'xevso_code_editor_options',
  'title'  => esc_html__('CSS Editor', 'xevso'),
  'icon'   => 'fa fa-pencil-square-o',
  'fields' => array(
      array(
        'id'       => 'xevso_css_editor',
        'type'     => 'code_editor',
        'title'    => esc_html__('CSS Editor', 'xevso'),
        'settings' => array(
          'theme'  => 'mbo',
          'mode'   => 'css',
        ),
      ),
    )
));
CSF::createSection($xevsoThemeOption, array(
  'parent' => 'xevso_code_editor_options',
  'title'  => esc_html__('Javascript Editor', 'xevso'),
  'icon'   => 'fa fa-pencil-square-o',
  'fields' => array(
      array(
        'id'       => 'xevso_js_editor',
        'type'     => 'code_editor',
        'title'    => esc_html__('Javascript Editor', 'xevso'),
        'settings' => array(
          'theme'  => 'dracula',
          'mode'   => 'javascript',
        ),
        'default'  => ';(function( $, window, document, undefined ) {
  $(document).ready( function() {

    // do stuff

  });
})( jQuery, window, document );',
      ),
    )
));
//
// Field: backup
//
CSF::createSection( $xevsoThemeOption, array(
  'title'       => 'Backup',
  'icon'        => 'fas fa-shield-alt',
  'description' => esc_html__('Backup Theme Options all Data', 'xevso'),
  'fields'      => array(
    array(
      'type' => 'backup',
    ),
  )
) );
