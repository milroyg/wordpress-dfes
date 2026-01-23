<?php

namespace MasterAddons\Admin;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * JLTMA Page Importer
 * Adds an "Import Pages" button on the WordPress Pages list
 * that opens the existing Template Library in a modal popup
 */

defined('ABSPATH') || exit;

final class JLTMA_Page_Importer {

    const HANDLE = 'jltma-page-importer';

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('views_edit-page', array($this, 'add_import_pages_tab'));
        add_filter('admin_body_class', array($this, 'add_body_class'));
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

        // Enqueue Template Library React app (required for modal content)
        wp_enqueue_script(
            'jltma-template-library',
            JLTMA_URL . '/assets/js/admin/template-library.js',
            array('jquery', 'wp-element'),
            JLTMA_VER,
            true
        );

        // Localize Template Library (required by React app)
        wp_localize_script('jltma-template-library', 'JLTMATemplateLibrary', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jltma_template_library_nonce'),
            'restUrl' => get_rest_url(),
            'restNonce' => wp_create_nonce('wp_rest'),
            'pluginUrl' => JLTMA_URL,
            'assetsUrl' => JLTMA_URL . '/assets/',
            'isProActive' => Master_Addons_Helper::jltma_premium(),
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
            'is_pro' => Master_Addons_Helper::jltma_premium(),
            'pluginUrl' => JLTMA_URL,
            'strings' => array(
                'importSuccess' => __('Template Kit imported successfully!', 'master-addons'),
                'importError' => __('Failed to import template kit.', 'master-addons'),
                'previewTemplate' => __('Preview', 'master-addons'),
                'importTemplate' => __('Import Kit', 'master-addons'),
                'searchPlaceholder' => __('Search template kits...', 'master-addons'),
            )
        ));

        // Enqueue Template Library CSS
        wp_enqueue_style(
            'jltma-template-library',
            JLTMA_URL . '/assets/css/admin/template-library.css',
            array(),
            JLTMA_VER
        );

        // Enqueue Widget Builder modal styles (for beautiful modal design)
        wp_enqueue_style(
            'jltma-widget-builder-modal',
            JLTMA_URL . '/assets/css/admin/widget-builder.css',
            array(),
            JLTMA_VER
        );

        // Enqueue Macy.js for masonry layout
        wp_enqueue_script(
            'macy-js',
            JLTMA_URL . '/assets/vendor/macy/macy.js',
            array(),
            JLTMA_VER,
            true
        );

        // Enqueue Page Importer JavaScript
        // wp_enqueue_script(
        //     self::HANDLE,
        //     JLTMA_URL . '/assets/js/admin/page-importer.js',
        //     array('jquery', 'jltma-template-library', 'macy-js'),
        //     JLTMA_VER,
        //     true
        // );

        // Enqueue Page Importer CSS
        wp_enqueue_style(
            self::HANDLE,
            JLTMA_URL . '/assets/css/admin/page-importer.css',
            array('jltma-widget-builder-modal'),
            JLTMA_VER
        );

        // Localize script data
        wp_localize_script(
            self::HANDLE,
            'JLTMA_PAGE_IMPORTER',
            array(
                'logo_url'          => JLTMA_URL . '/assets/images/logo.svg',
                'red_logo_url'      => JLTMA_URL . '/assets/images/red-logo.svg',
                'library_url'       => admin_url('admin.php?page=jltma-template-library'),
                'template_kits_url' => JLTMA_URL . '/assets/js/admin/template-kits-app.js',
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
        $count = $wpdb->get_var("
            SELECT COUNT(*) FROM $wpdb->posts
            WHERE post_type = 'page'
            AND post_status = 'publish'
            AND ID IN (
                SELECT post_id FROM $wpdb->postmeta
                WHERE meta_key = '_jltma_imported_template' AND meta_value != ''
            )
        ");

        if ($count > 0) {
            $class = (isset($_GET['jltma_imported']) && $_GET['jltma_imported'] == '1') ? 'current' : '';
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
}

// Initialize
if (is_admin()) {
    new JLTMA_Page_Importer();
}
