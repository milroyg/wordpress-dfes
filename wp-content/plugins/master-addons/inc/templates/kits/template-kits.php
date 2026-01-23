<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Template Kits Main Handler
 * Uses unified React architecture with Template Library
 */

// Register Menus
function jltma_addons_add_templates_kit_menu() {
    add_submenu_page('master-addons-settings', 'Template Kits', 'Template Kits', 'manage_options', 'jltma-templates-kit', 'jltma_addons_templates_kit_page');
}
add_action('admin_menu', 'jltma_addons_add_templates_kit_menu', 20);

/**
** Render Templates Kit Page
*/
function jltma_addons_templates_kit_page() {
    // Enqueue scripts and styles for unified React app
    wp_enqueue_script('jltma-template-kits-app', JLTMA_URL . '/assets/js/admin/template-kits-app.js', array('wp-element', 'wp-i18n'), JLTMA_VER, true);
    wp_enqueue_style('jltma-template-library', JLTMA_URL . '/assets/css/admin/template-library.css', array(), JLTMA_VER);
    wp_enqueue_style('jltma-page-importer', JLTMA_URL . '/assets/css/admin/page-importer.css', array(), JLTMA_VER);

    // Localize script for Template Kits
    $localize_data = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('jltma_template_kits_nonce_action'),
        'is_pro'=> \MasterAddons\Inc\Helper\Master_Addons_Helper::jltma_premium(),
        'pluginUrl' => JLTMA_URL,
        'strings' => array(
            'importSuccess' => __('Template Kit imported successfully!', 'master-addons'),
            'importError' => __('Failed to import template kit.', 'master-addons'),
            'previewTemplate' => __('Preview', 'master-addons'),
            'importTemplate' => __('Import Kit', 'master-addons'),
            'searchPlaceholder' => __('Search template kits...', 'master-addons'),
        )
    );

    wp_localize_script('jltma-template-kits-app', 'JLTMATemplatesKit', $localize_data);

    echo '<div class="wrap"><div id="jltma-template-kits-app"></div></div>';
}

add_filter('jltma_template_kits_data', 'ma_el_get_template_kits', 10, 3);
function ma_el_get_template_kits($cached_data = false, $force_refresh = false, $category = 'all'){
    // If already have valid cached data and not forcing refresh, return it
    if ($cached_data !== false && !$force_refresh && is_array($cached_data) && isset($cached_data['kits'])) {
        return $cached_data;
    }

    // Use the Template Kit Cache Manager to get cached kits
    if (class_exists('JLTMA_Site_Importer_Cache')) {
        $cache_manager = JLTMA_Site_Importer_Cache::get_instance();

        // Get kits from cache or fetch from API if needed
        $kits = $cache_manager->get_cached_kits($force_refresh);

        // Return the cached data in the expected format
        if ($kits !== false && is_array($kits)) {
            return array(
                'kits' => $kits,
                'cached' => true,
                'timestamp' => time()
            );
        }
    }

    // Return empty kits array as fallback
    return array(
        'kits' => array(),
        'cached' => false,
        'timestamp' => time()
    );
}
