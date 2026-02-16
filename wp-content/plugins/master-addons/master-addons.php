<?php

/**
 * Plugin Name: Master Addons for Elementor
 * Description: Master Addons is easy and must have Elementor Addons for WordPress Page Builder. Clean, Modern, Hand crafted designed Addons blocks.
 * Plugin URI: https://master-addons.com/all-widgets/
 * Author: Jewel Theme
 * Author URI: https://master-addons.com
 * Text Domain: master-addons
 * Domain Path: /languages
 * Version: 2.1.2
 * Elementor tested up to: 3.35.4
 * Elementor Pro tested up to: 3.35.4
 * Wordfence Vendor Key: qgxtflvqaabgarz4gu9nozmceloswzrg
 *
 */
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
if ( !function_exists( 'jltma_menu_params__premium_only' ) ) {
}
if ( function_exists( 'ma_el_fs' ) ) {
    ma_el_fs()->set_basename( false, __FILE__ );
} elseif ( !function_exists( 'ma_el_fs' ) ) {
    // Create a helper function for easy SDK access.
    function ma_el_fs() {
        global $ma_el_fs;
        if ( !isset( $ma_el_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_4015_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_4015_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once __DIR__ . '/lib/freemius/start.php';
            $jltma_el_menu = array(
                'slug'        => 'master-addons-settings',
                'first-path'  => 'admin.php?page=master-addons-settings',
                'account'     => false,
                'network'     => false,
                'support'     => false,
                'contact'     => false,
                'affiliation' => false,
                'pricing'     => true,
                'addons'      => false,
            );
            $ma_el_fs = fs_dynamic_init( array(
                'id'                  => '4015',
                'slug'                => 'master-addons',
                'premium_slug'        => 'master-addons-pro',
                'type'                => 'plugin',
                'public_key'          => 'pk_3c9b5b4e47a06288e3500c7bf812e',
                'premium_suffix'      => 'Premium',
                'has_affiliation'     => 'selected',
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'is_org_compliant'    => true,
                'menu'                => ( function_exists( 'jltma_menu_params__premium_only' ) ? jltma_menu_params__premium_only() : $jltma_el_menu ),
                'trial'               => false,
                'is_live'             => true,
                'parallel_activation' => array(
                    'enabled'                  => true,
                    'premium_version_basename' => 'master-addons-pro/master-addons.php',
                ),
                'is_premium'          => false,
            ) );
        }
        return $ma_el_fs;
    }

    // Init Freemius.
    ma_el_fs();
    // Disable automatic plugin deactivation on activation
    ma_el_fs()->add_filter( 'deactivate_on_activation', '__return_false' );
    // Signal that SDK was initiated.
    do_action( 'ma_el_fs_loaded' );
}
if ( !defined( 'JLTMA' ) ) {
    define( 'JLTMA', $jltma_plugin_data['Plugin Name'] );
}
if ( !defined( 'JLTMA_PLUGIN_AUTHOR' ) ) {
    define( 'JLTMA_PLUGIN_AUTHOR', $jltma_plugin_data['Author'] );
}
if ( !defined( 'JLTMA_PLUGIN_DESC' ) ) {
    define( 'JLTMA_PLUGIN_DESC', $jltma_plugin_data['Description'] );
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
if ( !defined( 'JLTMA_PATH' ) ) {
    define( 'JLTMA_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}
if ( !defined( 'JLTMA_PLUGIN_PATH' ) ) {
    define( 'JLTMA_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'inc/admin/theme-builder' ) );
}
//Defined Constants
if ( !defined( 'JLTMA_BADGE' ) ) {
    define( 'JLTMA_BADGE', '<span class="jltma-badge"></span>' );
}
if ( !defined( 'JLTMA_EXTENSION_BADGE' ) ) {
    define( 'JLTMA_EXTENSION_BADGE', '<span class="jltma-icon-ma"></span>' );
}
if ( !defined( 'JLTMA_PRO_RESTRICTED_CLASS' ) ) {
    define( 'JLTMA_PRO_RESTRICTED_CLASS', '<span class="jltma-pro-disabled"></span>' );
}
if ( !defined( 'JLTMA_NEW_FEATURE' ) ) {
    define( 'JLTMA_NEW_FEATURE', '<span class="jltma-new-feature"></span>' );
}
if ( !defined( 'JLTMA_SCRIPT_SUFFIX' ) ) {
    define( 'JLTMA_SCRIPT_SUFFIX', ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) );
}
if ( !defined( 'JLTMA_URL' ) ) {
    define( 'JLTMA_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
}
if ( !defined( 'JLTMA_IMAGE_DIR' ) ) {
    define( 'JLTMA_IMAGE_DIR', trailingslashit( JLTMA_URL ) . 'assets/images/' );
}
if ( !defined( 'JLTMA_ASSETS' ) ) {
    define( 'JLTMA_ASSETS', trailingslashit( JLTMA_URL ) . 'assets/' );
}
if ( !defined( 'JLTMA_ADMIN_ASSETS' ) ) {
    define( 'JLTMA_ADMIN_ASSETS', JLTMA_URL . '/inc/admin/assets/' );
}
if ( !defined( 'JLTMA_ADDONS' ) ) {
    define( 'JLTMA_ADDONS', plugin_dir_path( __FILE__ ) . 'addons/' );
}
if ( !defined( 'JLTMA_PRO_EXTENSIONS' ) ) {
    define( 'JLTMA_PRO_EXTENSIONS', plugin_dir_path( __FILE__ ) . 'premium/modules/' );
}
if ( !defined( 'JLTMA_TEMPLATES' ) ) {
    define( 'JLTMA_TEMPLATES', plugin_dir_path( __FILE__ ) . 'inc/template-parts/' );
}
if ( !defined( 'JLTMA_ACTIVATION_REDIRECT_TRANSIENT_KEY' ) ) {
    define( 'JLTMA_ACTIVATION_REDIRECT_TRANSIENT_KEY', '_master_addons_activation_redirect' );
}
// Load the free class (only exists in free version)
if ( !class_exists( '\\MasterAddons\\Master_Elementor_Addons' ) ) {
    require_once __DIR__ . '/class-master-elementor-addons.php';
}
// Activation and Deactivation hooks
if ( class_exists( '\\MasterAddons\\Master_Elementor_Addons' ) ) {
    register_activation_hook( __FILE__, array('\\MasterAddons\\Master_Elementor_Addons', 'jltma_plugin_activation_hook') );
    register_deactivation_hook( __FILE__, array('\\MasterAddons\\Master_Elementor_Addons', 'jltma_plugin_deactivation_hook') );
    // Initialize the plugin
    \MasterAddons\Master_Elementor_Addons::get_instance();
}