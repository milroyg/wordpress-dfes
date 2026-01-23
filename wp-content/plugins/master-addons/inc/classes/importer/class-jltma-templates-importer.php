<?php
namespace MasterAddons\Inc\Classes\Importer;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Master Addons Templates Kit Importer
 * Handles importing template kits from local JSON files
 */
class JLTMA_Templates_Importer {

    private $kit_id;
    private $kit_path;
    private $manifest_data;

    public function __construct($kit_id) {
        $this->kit_id = $kit_id;
        $upload_dir = wp_upload_dir();
        $this->kit_path = $upload_dir['basedir'] . '/master_addons/templates_kit/' . $kit_id . '/';
        $this->load_manifest();
    }

    /**
     * Load kit manifest data
     */
    private function load_manifest() {
        $manifest_file = $this->kit_path . 'manifest.json';

        if (!file_exists($manifest_file)) {
            throw new \Exception('Template kit manifest not found: ' . $this->kit_id);
        }

        $manifest_content = file_get_contents($manifest_file);
        $this->manifest_data = json_decode($manifest_content, true);

        if (!$this->manifest_data) {
            throw new \Exception('Invalid manifest data for kit: ' . $this->kit_id);
        }
    }

    /**
     * Import entire template kit
     */
    public function import_kit() {
        try {
            // Set up WordPress environment
            $this->setup_import_environment();

            // Import global settings
            $this->import_global_settings();

            // Import all pages/templates
            $imported_pages = array();
            foreach ($this->manifest_data['pages'] as $page_slug) {
                $page_id = $this->import_template($page_slug);
                if ($page_id) {
                    $imported_pages[$page_slug] = $page_id;
                }
            }

            // Set home page if exists
            if (isset($imported_pages['home'])) {
                $this->set_home_page($imported_pages['home']);
            }

            // Mark imported content
            $this->mark_imported_content($imported_pages);

            return array(
                'success' => true,
                'imported_pages' => $imported_pages,
                'message' => 'Template kit imported successfully!'
            );

        } catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Import single template
     */
    public function import_template($template_slug) {
        $template_file = $this->kit_path . $template_slug . '.json';

        if (!file_exists($template_file)) {
            throw new \Exception('Template file not found: ' . $template_slug);
        }

        $template_content = file_get_contents($template_file);
        $template_data = json_decode($template_content, true);

        if (!$template_data) {
            throw new \Exception('Invalid template data: ' . $template_slug);
        }

        // Create WordPress page
        $page_id = wp_insert_post(array(
            'post_title' => $template_data['page_settings']['post_title'],
            'post_type' => $template_data['page_settings']['post_type'],
            'post_status' => 'publish',
            'post_content' => '', // Elementor handles content
            'meta_input' => array(
                '_elementor_edit_mode' => 'builder',
                '_elementor_template_type' => 'wp-page',
                '_elementor_version' => $template_data['version'],
                '_elementor_data' => wp_slash(json_encode($template_data['content'])),
                '_jltma_demo_import_item' => true,
                '_jltma_import_kit_id' => $this->kit_id
            )
        ));

        if (is_wp_error($page_id)) {
            throw new \Exception('Failed to create page: ' . $page_id->get_error_message());
        }

        return $page_id;
    }

    /**
     * Setup import environment
     */
    private function setup_import_environment() {
        // Disable image generation during import
        add_filter('intermediate_image_sizes_advanced', '__return_empty_array');

        // Increase memory and time limits
        if (function_exists('ini_set')) {
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', 300);
        }
    }

    /**
     * Import global settings from manifest
     */
    private function import_global_settings() {
        if (!isset($this->manifest_data['global_settings'])) {
            return;
        }

        $global_settings = $this->manifest_data['global_settings'];

        // Import Elementor global colors
        if (isset($global_settings['colors'])) {
            $this->import_elementor_colors($global_settings['colors']);
        }

        // Import Elementor global fonts
        if (isset($global_settings['typography'])) {
            $this->import_elementor_fonts($global_settings['typography']);
        }
    }

    /**
     * Import Elementor global colors
     */
    private function import_elementor_colors($colors) {
        $elementor_scheme = get_option('elementor_scheme_color', array());

        $color_mapping = array(
            'primary' => '1',
            'secondary' => '2',
            'accent' => '3'
        );

        foreach ($colors as $color_name => $color_value) {
            if (isset($color_mapping[$color_name])) {
                $elementor_scheme[$color_mapping[$color_name]] = $color_value;
            }
        }

        update_option('elementor_scheme_color', $elementor_scheme);
    }

    /**
     * Import Elementor global fonts
     */
    private function import_elementor_fonts($typography) {
        $elementor_scheme = get_option('elementor_scheme_typography', array());

        if (isset($typography['primary_font'])) {
            $elementor_scheme['1'] = array(
                'font_family' => $typography['primary_font'],
                'font_weight' => '400'
            );
        }

        if (isset($typography['secondary_font'])) {
            $elementor_scheme['2'] = array(
                'font_family' => $typography['secondary_font'],
                'font_weight' => '400'
            );
        }

        update_option('elementor_scheme_typography', $elementor_scheme);
    }

    /**
     * Set imported page as home page
     */
    private function set_home_page($page_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $page_id);
    }

    /**
     * Mark content as imported for cleanup purposes
     */
    private function mark_imported_content($imported_pages) {
        foreach ($imported_pages as $slug => $page_id) {
            update_post_meta($page_id, '_jltma_demo_import_item', true);
            update_post_meta($page_id, '_jltma_import_kit_id', $this->kit_id);
            update_post_meta($page_id, '_jltma_import_date', current_time('mysql'));
        }
    }

    /**
     * Get kit manifest data
     */
    public function get_manifest() {
        return $this->manifest_data;
    }

    /**
     * Check if kit exists
     */
    public static function kit_exists($kit_id) {
        $upload_dir = wp_upload_dir();
        $kit_path = $upload_dir['basedir'] . '/master_addons/templates_kit/' . $kit_id . '/';
        return file_exists($kit_path . 'manifest.json');
    }

    /**
     * Get all available kits
     */
    public static function get_available_kits() {
        $upload_dir = wp_upload_dir();
        $kits_path = $upload_dir['basedir'] . '/master_addons/templates_kit/';
        $kits = array();

        if (is_dir($kits_path)) {
            $directories = scandir($kits_path);
            foreach ($directories as $dir) {
                if ($dir !== '.' && $dir !== '..' && is_dir($kits_path . $dir)) {
                    $manifest_file = $kits_path . $dir . '/manifest.json';
                    if (file_exists($manifest_file)) {
                        $kits[] = $dir;
                    }
                }
            }
        }

        return $kits;
    }
}
