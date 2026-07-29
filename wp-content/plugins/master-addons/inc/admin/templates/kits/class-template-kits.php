<?php

namespace MasterAddons\Inc\Admin\Templates\Kits;

use MasterAddons\Inc\Classes\Helper;

defined('ABSPATH') || exit;

/**
 * Template Kits Main Handler
 * Uses unified React architecture with Template Library
 */
class Template_Kits
{
    private static $_instance = null;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_templates_kit_menu'], 20);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_filter('jltma_template_kits_data', [$this, 'get_template_kits'], 10, 3);
    }

    /**
     * Register Template Kits Menu
     */
    public function add_templates_kit_menu()
    {
        add_submenu_page(
            'master-addons-settings',
            'Template Kits',
            'Template Kits',
            'manage_options',
            'jltma-template-kits',
            [$this, 'render_templates_kit_page']
        );
    }

    /**
     * Enqueue Template Kits JS & CSS on its admin page
     */
    public function enqueue_assets($hook)
    {
        if (strpos($hook, 'jltma-template-kits') === false) {
            return;
        }

        // Enqueue registered assets from Assets_Manager
        wp_enqueue_style('jltma-page-importer');
        wp_enqueue_style('jltma-template-kits-app');
        wp_enqueue_script('jltma-template-kits-app');

        wp_localize_script('jltma-template-kits-app', 'JLTMATemplatesKit', array(
            'ajaxurl'   => admin_url('admin-ajax.php'),
            'nonce'     => wp_create_nonce('jltma_template_kits_nonce_action'),
            'is_pro'    => Helper::jltma_premium(),
            'pluginUrl'  => JLTMA_URL,
            'strings'   => array(
                'importSuccess'     => __('Template Kit imported successfully!', 'master-addons'),
                'importError'       => __('Failed to import template kit.', 'master-addons'),
                'previewTemplate'   => __('Preview', 'master-addons'),
                'importTemplate'    => __('Import Kit', 'master-addons'),
                'searchPlaceholder' => __('Search template kits...', 'master-addons'),
            ),
        ));
    }

    /**
     * Render Templates Kit Page
     */
    public function render_templates_kit_page()
    {
        echo '<div class="wrap"><div id="jltma-template-kits-app"></div></div>';
    }

    /**
     * Get Template Kits Data
     */
    public function get_template_kits($cached_data = false, $force_refresh = false, $category = 'all')
    {
        // If already have valid cached data and not forcing refresh, return it
        if ($cached_data !== false && !$force_refresh && is_array($cached_data) && isset($cached_data['kits'])) {
            return $cached_data;
        }

        // Use the Template Kit Cache Manager to get cached kits
        if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();

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

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
