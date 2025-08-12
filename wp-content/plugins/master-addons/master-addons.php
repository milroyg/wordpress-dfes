<?php

/**
 * Plugin Name: Master Addons for Elementor
 * Description: Master Addons is easy and must have Elementor Addons for WordPress Page Builder. Clean, Modern, Hand crafted designed Addons blocks.
 * Plugin URI: https://master-addons.com/all-widgets/
 * Author: Jewel Theme
 * Version: 2.0.8.1
 * Author URI: https://master-addons.com
 * Text Domain: master-addons
 * Domain Path: /languages
 * Elementor tested up to: 3.29.2
 * Elementor Pro tested up to: 3.29.0
 *   */
// No, Direct access Sir !!!
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$jltma_plugin_data = get_file_data( __FILE__, array(
    'Version'     => 'Version',
    'Plugin Name' => 'Plugin Name',
    'Author'      => 'Author',
    'Description' => 'Description',
    'Plugin URI'  => 'Plugin URI',
), false );
if ( !defined( 'JLTMA' ) ) {
    define( 'JLTMA', $jltma_plugin_data['Plugin Name'] );
}
if ( !defined( 'JLTMA_PLUGIN_DESC' ) ) {
    define( 'JLTMA_PLUGIN_DESC', $jltma_plugin_data['Description'] );
}
if ( !defined( 'JLTMA_PLUGIN_AUTHOR' ) ) {
    define( 'JLTMA_PLUGIN_AUTHOR', $jltma_plugin_data['Author'] );
}
if ( !defined( 'JLTMA_PLUGIN_URI' ) ) {
    define( 'JLTMA_PLUGIN_URI', $jltma_plugin_data['Plugin URI'] );
}
if ( !defined( 'JLTMA_VER' ) ) {
    define( 'JLTMA_VER', $jltma_plugin_data['Version'] );
}
if ( !defined( 'JLTMA_BASE' ) ) {
    define( 'JLTMA_BASE', plugin_basename( __FILE__ ) );
}
if ( !defined( 'JLTMA_SLUG' ) ) {
    define( 'JLTMA_SLUG', dirname( plugin_basename( __FILE__ ) ) );
}
if ( !defined( 'JLTMA_FILE' ) ) {
    define( 'JLTMA_FILE', __FILE__ );
}
// require_once __DIR__ . '/vendor/autoload.php';
if ( function_exists( 'ma_el_fs' ) ) {
    ma_el_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'ma_el_fs' ) ) {
        // Create a helper function for easy SDK access.
        function ma_el_fs() {
            global $ma_el_fs;
            if ( !isset( $ma_el_fs ) ) {
                // Activate multisite network integration.
                if ( !defined( 'WP_FS__PRODUCT_4015_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_4015_MULTISITE', true );
                }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/lib/freemius/start.php';
                $ma_el_fs = fs_dynamic_init( array(
                    'id'               => '4015',
                    'slug'             => 'master-addons',
                    'premium_slug'     => 'master-addons-pro',
                    'type'             => 'plugin',
                    'public_key'       => 'pk_3c9b5b4e47a06288e3500c7bf812e',
                    'premium_suffix'   => 'Pro',
                    'has_affiliation'  => 'selected',
                    'has_addons'       => false,
                    'has_paid_plans'   => true,
                    'is_org_compliant' => true,
                    'menu'             => array(
                        'slug'        => 'master-addons-settings',
                        'first-path'  => 'admin.php?page=master-addons-settings',
                        'contact'     => false,
                        'affiliation' => false,
                        'support'     => true,
                        'pricing'     => true,
                    ),
                    'trial'            => false,
                    'is_live'          => true,
                    'is_premium'       => false,
                ) );
            }
            return $ma_el_fs;
        }

        ma_el_fs();
        do_action( 'ma_el_fs_loaded' );
    }
}
if ( !class_exists( '\\MasterAddons\\Master_Elementor_Addons' ) ) {
    require_once dirname( __FILE__ ) . '/class-master-elementor-addons.php';
}
// Activation and Deactivation hooks
if ( class_exists( '\\MasterAddons\\Master_Elementor_Addons' ) ) {
    register_activation_hook( __FILE__, array('\\MasterAddons\\Master_Elementor_Addons', 'jltma_plugin_activation_hook') );
    register_deactivation_hook( __FILE__, array('\\MasterAddons\\Master_Elementor_Addons', 'jltma_plugin_deactivation_hook') );
}
// Instantiate Master Addons Class