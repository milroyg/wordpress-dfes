<?php

namespace MasterAddons\Inc\Admin;

use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Classes\Assets_Manager;

/**
 * JLTMA Page Importer
 * Adds an "Import Pages" button on the WordPress Pages list
 * that opens the existing Template Library in a modal popup
 */

defined('ABSPATH') || exit;

final class Page_Importer {

    private static $instance = null;
    const HANDLE = 'jltma-page-importer';

    public static function get_instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('views_edit-page', array($this, 'add_import_pages_tab'));
        add_filter('admin_body_class', array($this, 'add_body_class'));
        add_action('pre_get_posts', array($this, 'filter_imported_pages'));
    }

    /**
     * Enqueue scripts only on Pages list screen
     */
    public function enqueue_scripts($hook_suffix) {
        // Only load on Pages list screen
        $screen = get_current_screen();

        if (!$screen || $screen->id !== 'edit-page' || $screen->post_type !== 'page') {
            return;
        }

        // Enqueue WordPress React (required for template library)
        wp_enqueue_script('wp-element');
        wp_enqueue_script('wp-components');
        wp_enqueue_script('wp-api-fetch');

        // Enqueue all page importer assets via Assets_Manager
        // This includes: macy, template-library, widget-builder, page-importer
        Assets_Manager::enqueue('page-importer');

        // Resolve plugin URL — use JLTMA_PRO_URL fallback for pro-only builds
        $plugin_url = defined('JLTMA_URL') ? JLTMA_URL : (defined('JLTMA_PRO_URL') ? untrailingslashit(JLTMA_PRO_URL) : '');

        // Localize Template Library (required by React app)
        wp_localize_script('jltma-template-library', 'JLTMATemplateLibrary', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jltma_template_library_nonce'),
            'restUrl' => get_rest_url(),
            'restNonce' => wp_create_nonce('wp_rest'),
            'pluginUrl' => $plugin_url,
            'assetsUrl' => $plugin_url . '/assets/',
            'isProActive' => Helper::jltma_premium(),
            'strings' => array(
                'searchPlaceholder' => __('Search templates...', 'master-addons'),
                'importTemplate' => __('Import', 'master-addons'),
                'previewTemplate' => __('Preview', 'master-addons'),
                'loadingTemplates' => __('Loading templates...', 'master-addons'),
                'noTemplatesFound' => __('No templates found', 'master-addons'),
                'importSuccess' => __('Template imported successfully!', 'master-addons'),
                'importError' => __('Failed to import template', 'master-addons')
            )
        ));

        // Localize Template Kits config (for Template Kits dropdown option)
        wp_localize_script('jltma-template-library', 'JLTMATemplatesKit', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jltma_template_kits_nonce_action'),
            'is_pro' => Helper::jltma_premium(),
            'pluginUrl' => $plugin_url,
            'strings' => array(
                'importSuccess' => __('Template Kit imported successfully!', 'master-addons'),
                'importError' => __('Failed to import template kit.', 'master-addons'),
                'previewTemplate' => __('Preview', 'master-addons'),
                'importTemplate' => __('Import Kit', 'master-addons'),
                'searchPlaceholder' => __('Search template kits...', 'master-addons'),
            )
        ));

        // Localize script data for page importer
        wp_localize_script(
            'jltma-page-importer',
            'JLTMA_PAGE_IMPORTER',
            array(
                'logo_url'          => $plugin_url . '/assets/images/logo.svg',
                'red_logo_url'      => $plugin_url . '/assets/images/red-logo.svg',
                'library_url'       => admin_url('admin.php?page=jltma-template-library'),
                'template_kits_url' => $plugin_url . '/assets/js/admin/template-kits-app.js',
                'brand_color'       => '#6814cd',
                'nonce'             => wp_create_nonce('jltma_page_importer_nonce'),
                'i18n'              => array(
                    'import_pages' => __('Import Pages', 'master-addons'),
                    'close'        => __('Close', 'master-addons'),
                    'loading'      => __('Loading Template Library...', 'master-addons'),
                ),
            )
        );
    }

    /**
     * Add body class to Pages list screen
     * This helps with styling and JavaScript targeting
     */
    public function add_body_class($classes) {
        $screen = get_current_screen();

        // Only add class on Pages list screen
        if ($screen && $screen->id === 'edit-page' && $screen->post_type === 'page') {
            $classes .= ' jltma-page-importer-btn-active';
        }

        return $classes;
    }

    /**
     * Add custom tab to Pages list view (optional enhancement)
     * This adds a tab showing imported pages
     */
    public function add_import_pages_tab($views) {
        global $wpdb;

        // Count pages imported via Master Addons
        // Check both meta keys: _jltma_demo_import_item (kit imports) and _jltma_imported_template (single imports)
        $count = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for counting imported pages by meta key; query is fully static with no user-supplied values
            "SELECT COUNT(*) FROM {$wpdb->posts}
            WHERE post_type = 'page'
            AND post_status = 'publish'
            AND ID IN (
                SELECT post_id FROM {$wpdb->postmeta}
                WHERE meta_key IN ('_jltma_demo_import_item', '_jltma_imported_template')
            )"
        );

        if ($count > 0) {
            $class = (isset($_GET['jltma_imported']) && $_GET['jltma_imported'] == '1') ? 'current' : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only view filter, no data processed
            $url = add_query_arg('jltma_imported', '1', admin_url('edit.php?post_type=page'));
            $views['jltma_imported'] = sprintf(
                '<a href="%s" class="%s" style="color: %s; font-weight: 500">%s <span class="count">(%s)</span></a>',
                $url,
                $class,
                '#6814cd',
                __('MA Imported', 'master-addons'),
                $count
            );
        }

        return $views;
    }

    /**
     * Filter pages list to show only MA imported pages
     */
    public function filter_imported_pages($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'edit-page') {
            return;
        }

        if (isset($_GET['jltma_imported']) && $_GET['jltma_imported'] === '1') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only query filter, no data processed
            $query->set('meta_query', [
                'relation' => 'OR',
                [
                    'key'     => '_jltma_demo_import_item',
                    'compare' => 'EXISTS',
                ],
                [
                    'key'     => '_jltma_imported_template',
                    'compare' => 'EXISTS',
                ],
            ]);
        }
    }
}

