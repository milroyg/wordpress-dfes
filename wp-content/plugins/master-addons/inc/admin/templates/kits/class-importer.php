<?php

namespace MasterAddons\Inc\Admin\Templates\Kits;

use MasterAddons\Inc\Classes\Importer\JLTMA_Templates_Importer;
use MasterAddons\Inc\Classes\Helper;
use Elementor\Plugin;

defined('ABSPATH') || exit;

/**
 * Template Kits Importer Class
 *
 * Handles all template kit import functionality including:
 * - AJAX handlers for imports
 * - Image imports to media library
 * - Plugin installation/activation
 * - Template kit uploads
 */
class Importer
{
    private static $_instance = null;

    /**
     * Track imported images to avoid duplicates
     */
    private $imported_images = [];

    public function __construct()
    {
        $this->register_ajax_hooks();
        $this->register_filters();
    }

    /**
     * Register AJAX action hooks
     */
    private function register_ajax_hooks()
    {
        add_action('wp_ajax_jltma_activate_required_theme', [$this, 'activate_required_theme']);
        add_action('wp_ajax_jltma_reset_previous_import', [$this, 'reset_previous_import']);
        add_action('wp_ajax_jltma_import_templates_kit', [$this, 'import_templates_kit']);
        add_action('wp_ajax_jltma_final_settings_setup', [$this, 'final_settings_setup']);
        add_action('wp_ajax_jltma_search_query_results', [$this, 'search_query_results']);
        add_action('wp_ajax_jltma_predownload_kit', [$this, 'predownload_kit']);
        add_action('wp_ajax_jltma_download_all_kits', [$this, 'download_all_kits']);
        add_action('wp_ajax_jltma_install_required_plugin', [$this, 'install_required_plugin']);
        add_action('wp_ajax_jltma_activate_required_plugin', [$this, 'activate_required_plugin']);
        add_action('wp_ajax_jltma_upload_template_kit', [$this, 'upload_template_kit']);
        add_action('wp_ajax_jltma_cleanup_template_images', [$this, 'cleanup_template_images']);
        add_action('wp_ajax_jltma_preimport_kit_images', [$this, 'preimport_kit_images']);
        add_action('wp_ajax_jltma_precache_kit_images', [$this, 'precache_kit_images']);
        add_action('init', [$this, 'disable_default_woo_pages_creation'], 2);
    }

    /**
     * Register filters
     */
    private function register_filters()
    {
        // Set Image Timeout
        if (version_compare(get_bloginfo('version'), '5.1.0', '>=')) {
            add_filter('http_request_timeout', [$this, 'set_image_request_timeout'], 10, 2);
            add_filter('wp_check_filetype_and_ext', [$this, 'real_mime_types_5_1_0'], 10, 5);
        } else {
            add_filter('wp_check_filetype_and_ext', [$this, 'real_mime_types'], 10, 4);
        }
    }

    /**
     * Get Theme Status
     */
    public function get_theme_status()
    {
        $theme = wp_get_theme();

        // Theme installed and activate.
        if ('Hello Elementor' === $theme->name || 'Hello Elementor' === $theme->parent_theme) {
            return 'req-theme-active';
        }

        // Theme installed but not activate.
        foreach ((array) wp_get_themes() as $theme_dir => $theme) {
            if ('Hello Elementor' === $theme->name || 'Hello Elementor' === $theme->parent_theme) {
                return 'req-theme-inactive';
            }
        }

        return 'req-theme-not-installed';
    }

    /**
     * Install/Activate Required Theme
     */
    public function activate_required_theme()
    {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if (!wp_verify_nonce($nonce, 'jltma-template-kits-js') || !current_user_can('manage_options')) {
            exit;
        }

        // Get Current Theme
        $theme = get_option('stylesheet');

        // No default theme activation - themes should be specified in manifest
    }

    /**
     * Custom upload mimes for template kit imports
     */
    public function custom_upload_mimes($mimes)
    {
        // Allow SVG files.
        $mimes['svg']  = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';

        // Allow XML files.
        $mimes['xml'] = 'text/xml';

        // Allow JSON files.
        $mimes['json'] = 'application/json';

        return $mimes;
    }

    /**
     * Sanitize SVG files on upload
     */
    public function sanitize_svg_on_upload($file)
    {
        if ($file['type'] === 'image/svg+xml') {
            // Direct file ops on the PHP upload temp path, before WordPress moves it.
            // WP_Filesystem (ftp/ssh methods) cannot reach the local upload temp file.
            $file_content = file_get_contents($file['tmp_name']); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading the PHP upload temp file for sanitization
            $sanitized_content = $this->sanitize_svg($file_content);
            file_put_contents($file['tmp_name'], $sanitized_content); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- writing back the sanitized SVG to the PHP upload temp file before WordPress moves it
        }
        return $file;
    }

    /**
     * Sanitize SVG content
     */
    public function sanitize_svg($svg_content)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($svg_content, LIBXML_NOENT | LIBXML_DTDLOAD);

        // Remove scripts
        $scripts = $dom->getElementsByTagName('script');
        while ($scripts->length > 0) {
            $scripts->item(0)->parentNode->removeChild($scripts->item(0));
        }

        return $dom->saveXML();
    }

    /**
     * Import Template Kit
     */
    public function import_templates_kit()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        // Include the importer class if not already loaded
        if (!class_exists('MasterAddons\Inc\Classes\Importer\JLTMA_Templates_Importer')) {
            require_once \JLTMA_PATH . 'inc/classes/importer/class-jltma-templates-importer.php';
        }

        // Get cache manager instance
        $cache_manager = null;
        if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
        }

        // Add upload mime types filter
        add_filter('upload_mimes', [$this, 'custom_upload_mimes'], 99);
        add_filter('wp_handle_upload_prefilter', [$this, 'sanitize_svg_on_upload']);

        // Temp Define Importers
        if (!defined('WP_LOAD_IMPORTERS')) {
            define('WP_LOAD_IMPORTERS', true);
        }

        // Include if Class Does NOT Exist
        if (!class_exists('WP_Import')) {
            $class_wp_importer = \JLTMA_PATH . 'inc/classes/importer/class-wordpress-importer.php';
            if (file_exists($class_wp_importer)) {
                require $class_wp_importer;
            }
        }

        // Get template kit ID
        $kit = isset($_POST['jltma_templates_kit']) ? sanitize_file_name(wp_unslash($_POST['jltma_templates_kit'])) : '';
        if (!$kit && isset($_POST['parentTemplate'])) {
            $kit = sanitize_text_field( wp_unslash( $_POST['parentTemplate'] ) );
        }

        // Handle template import
        if (isset($_POST['template'])) {
            $this->handle_single_template_import($kit, $cache_manager);
            return;
        }

        if (empty($kit)) {
            wp_send_json_error(array(
                'message' => 'Template kit ID is required.'
            ));
            return;
        }

        // Store kit ID temporarily
        update_option('jltma-import-kit-id', $kit);

        try {
            // Try REST API with caching first, fallback to local JSON
            $import_result = $this->import_kit_via_api($kit);

            if (!$import_result) {
                // Fallback to local JSON files
                $importer = new JLTMA_Templates_Importer($kit);
                $import_result = $importer->import_kit();
            }

            if ($import_result && $import_result['success']) {
                // Return serialized result similar to Royal Addons
                echo esc_html(serialize(array(
                    'success' => true,
                    'imported_pages' => $import_result['imported_pages'] ?? array(),
                    'message' => $import_result['message'] ?? 'Import completed successfully!'
                )));
            } else {
                wp_send_json_error(array(
                    'message' => $import_result['message'] ?? 'Import failed.'
                ));
            }
        } catch (\Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage()
            ));
        }
    }

    /**
     * Handle single template import
     */
    private function handle_single_template_import($kit, $cache_manager)
    {
        $template_data = isset( $_POST['template'] ) ? sanitize_textarea_field( wp_unslash( $_POST['template'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in calling method
        $decoded = json_decode($template_data, true);
        $kit_id = $decoded['template_id'];
        $kit_name = $decoded['title'];

        // Duplicate detection: check if this template was already imported for this kit
        $parent_kit = isset($_POST['parentTemplate']) ? sanitize_text_field( wp_unslash( $_POST['parentTemplate'] ) ) : $kit; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in calling method
        $existing_page = $this->find_existing_imported_page($kit_id, $parent_kit, $kit_name);
        if ($existing_page) {
            $page_edit_url = admin_url('post.php?post=' . $existing_page->ID . '&action=elementor');
            wp_send_json_success(array(
                'message'       => 'Template already imported, skipping duplicate.',
                'page_created'  => true,
                'page_id'       => $existing_page->ID,
                'page_title'    => $existing_page->post_title,
                'page_edit_url' => $page_edit_url,
                'edit_url'      => $page_edit_url,
                'view_url'      => get_permalink($existing_page->ID),
                'skipped'       => true,
            ));
            exit;
        }

        // Check if we have both parentTemplate and template_id for cached templates
        if (isset($_POST['parentTemplate']) && isset($decoded['template_id'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in calling method
            $decoded = $this->load_template_from_cache($decoded, $kit_name, $cache_manager);
        }

        // Continue with the import process
        $import_start_time = microtime(true);
        $decoded['content'] = $this->import_images_to_media($decoded['content'], $kit_id, $kit_name);
        $import_time = microtime(true) - $import_start_time;

        $template_type = $decoded['type'] ?? 'page';

        // Check if this is a global kit style template
        $is_global_kit = false;
        if (isset($decoded['metadata']['template_type']) && $decoded['metadata']['template_type'] === 'global-styles') {
            $is_global_kit = true;
            $template_type = 'kit';
        }

        // Prepare meta input
        $meta_input = array(
            '_elementor_edit_mode'        => 'builder',
            '_elementor_template_type'    => $template_type,
            '_elementor_version'          => ELEMENTOR_VERSION,
            '_elementor_data'             => wp_slash(json_encode($decoded['content'])),
            '_jltma_demo_import_item'     => true,
            '_jltma_import_kit_id'        => $kit_id,
            '_jltma_import_date'          => current_time('mysql'),
            '_jltma_original_template_id' => $kit_id,
            '_thumbnail_id'               => 0
        );

        $template_id = wp_insert_post(array(
            'post_title'    => $kit_name,
            'post_type'     => 'elementor_library',
            'post_status'   => 'publish',
            'post_content'  => '',
            'meta_input'    => $meta_input
        ));

        if (!is_wp_error($template_id) && $is_global_kit && isset($decoded['page_settings'])) {
            update_post_meta($template_id, '_elementor_page_settings', $decoded['page_settings']);
        }

        // Handle global kit styles activation
        if (!is_wp_error($template_id) && $is_global_kit) {
            update_option('elementor_active_kit', $template_id);
            if (class_exists('\Elementor\Plugin')) {
                \Elementor\Plugin::$instance->files_manager->clear_cache();
            }
        } elseif (!is_wp_error($template_id) && $template_type === 'kit') {
            $auto_activate = apply_filters('jltma_auto_activate_kit_template', true, $template_id, $kit_name, $decoded);
            if ($auto_activate) {
                update_option('elementor_active_kit', $template_id);
                if (isset($decoded['settings']) && is_array($decoded['settings'])) {
                    $kit_instance = \Elementor\Plugin::$instance->documents->get($template_id);
                    if ($kit_instance) {
                        $kit_instance->save(['settings' => $decoded['settings']]);
                    }
                }
                if (class_exists('\Elementor\Plugin')) {
                    \Elementor\Plugin::$instance->files_manager->clear_cache();
                }
            }
        }

        if (!is_wp_error($template_id)) {
            $response = $this->create_page_from_template($template_id, $template_type, $kit_name, $kit_id, $decoded, $is_global_kit);
            wp_send_json_success($response);
            exit;
        } else {
            wp_send_json_error(array(
                'message' => 'Failed to save template: ' . $template_id->get_error_message()
            ));
            exit;
        }
    }

    /**
     * Load template from cache
     */
    private function load_template_from_cache($decoded, $kit_name, $cache_manager)
    {
        $parent_template = isset( $_POST['parentTemplate'] ) ? sanitize_text_field( wp_unslash( $_POST['parentTemplate'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in calling method
        $template_id = sanitize_text_field($decoded['template_id']);

        $upload_dir = wp_upload_dir();

        // First check cached templates directory
        $cached_base = $upload_dir['basedir'] . '/master_addons/templates_kits/kits/';
        $manifest_path = $cached_base . $parent_template . '/manifest.json';

        // Check if manifest exists in cached location
        if (file_exists($manifest_path)) {
            $manifest_content = file_get_contents($manifest_path);
            $manifest = json_decode($manifest_content, true);

            // Find the template in manifest
            $template_found = false;
            $template_source = null;
            if (isset($manifest['templates']) && is_array($manifest['templates'])) {
                foreach ($manifest['templates'] as $tpl) {
                    if ((isset($tpl['template_id']) && $tpl['template_id'] == $template_id) ||
                        (isset($tpl['name']) && $tpl['name'] == $kit_name)) {
                        $template_found = true;
                        $template_source = isset($tpl['source']) ? $tpl['source'] : null;
                        break;
                    }
                }
            }

            if ($template_found && $template_source) {
                $json_file_path = $cached_base . $parent_template . '/' . $template_source;
                if (file_exists($json_file_path)) {
                    $json_content = file_get_contents($json_file_path);
                    $local_template = json_decode($json_content, true);
                    if ($local_template) {
                        $decoded = $local_template;
                        $decoded['template_id'] = $template_id;
                        $decoded['title'] = $kit_name;
                    }
                }
            } elseif ($kit_name === 'Global Kit Styles' || strpos(strtolower($kit_name), 'global') !== false) {
                $global_path = $cached_base . $parent_template . '/templates/global.json';
                if (file_exists($global_path)) {
                    $json_content = file_get_contents($global_path);
                    $local_template = json_decode($json_content, true);
                    if ($local_template) {
                        $decoded = $local_template;
                        if (!isset($decoded['template_id'])) {
                            $decoded['template_id'] = $template_id;
                        }
                        $decoded['title'] = $kit_name;
                    }
                }
            }
        } elseif (is_null($decoded['content'])) {
            $decoded = $this->load_template_from_purchased_kits($decoded, $parent_template, $kit_name, $template_id);
        }

        return $decoded;
    }

    /**
     * Load template from purchased kits
     */
    private function load_template_from_purchased_kits($decoded, $parent_template, $kit_name, $template_id)
    {
        $upload_dir = wp_upload_dir();
        $cache_base = $upload_dir['basedir'] . '/master_addons/purchased_kits/kits/';
        $manifest_data = json_decode(file_get_contents($cache_base . 'kit_' . $parent_template . '/manifest.json'));

        $thisTemplate = null;
        foreach ($manifest_data->templates as $template_key => $template) {
            if ($template->name === $kit_name) {
                $thisTemplate = $template;
            }
        }

        $template_slug = ($thisTemplate) ? $thisTemplate->source : str_replace(' ', '-', $kit_name) . '.json';
        if ($thisTemplate) {
            $json_file_path = $cache_base . 'kit_' . $parent_template . '/' . $template_slug;
        } else {
            $json_file_path = $cache_base . 'kit_' . $parent_template . '/templates/' . $template_slug;
        }

        // If direct path doesn't work, try to find the file
        if (!file_exists($json_file_path)) {
            $json_file_path = $this->find_template_file($cache_base, $parent_template, $template_slug, $kit_name);
        }

        // Read the JSON file
        if (file_exists($json_file_path)) {
            $json_content = file_get_contents($json_file_path);
            $template_json = json_decode($json_content, true);

            if ($template_json && isset($template_json['content'])) {
                $decoded['content'] = $template_json['content'];
                if (isset($template_json['type'])) {
                    if ($template_json['type'] == 'wp-post') {
                        $decoded['type'] = $template_json['metadata']['template_type'];
                    } else {
                        $decoded['type'] = $template_json['type'];
                    }
                }
                if (isset($template_json['version'])) {
                    $decoded['version'] = $template_json['version'];
                }
                if (isset($template_json['metadata'])) {
                    $decoded['metadata'] = $template_json['metadata'];
                }
            } else {
                $decoded = array_merge($decoded, $template_json);
            }
        } else {
            wp_send_json_error(array(
                'message' => 'Template file not found: ' . $template_slug . ' in kit_' . $parent_template
            ));
            exit;
        }

        return $decoded;
    }

    /**
     * Find template file with various naming conventions
     */
    private function find_template_file($cache_base, $parent_template, $template_slug, $kit_name)
    {
        $possible_names = [
            $template_slug,
            strtolower($template_slug),
            str_replace('-', '_', $template_slug),
            str_replace(' ', '_', $kit_name) . '.json',
            str_replace([' ', '_'], '-', $kit_name) . '.json',
            str_replace(['–', '—'], '-', $template_slug),
            str_replace(['–', '—'], '-', str_replace(' ', '-', $kit_name)) . '.json',
            str_replace(['\'', '\'', '\"', '\"'], '', $template_slug),
            urlencode(str_replace(' ', '-', $kit_name)) . '.json',
            preg_replace('/[^a-zA-Z0-9\-]/', '-', str_replace(' ', '-', $kit_name)) . '.json'
        ];

        foreach ($possible_names as $possible_name) {
            $test_path = $cache_base . 'kit_' . $parent_template . '/templates/' . $possible_name;
            if (file_exists($test_path)) {
                return $test_path;
            }
        }

        // Scan directory for matching file
        $templates_dir = $cache_base . 'kit_' . $parent_template . '/templates/';
        if (is_dir($templates_dir)) {
            $files = scandir($templates_dir);
            $normalized_kit_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $kit_name));

            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                    $normalized_filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', pathinfo($file, PATHINFO_FILENAME)));
                    if ($normalized_filename === $normalized_kit_name || strpos($normalized_filename, $normalized_kit_name) !== false) {
                        return $templates_dir . $file;
                    }
                }
            }
        }

        return $cache_base . 'kit_' . $parent_template . '/templates/' . $template_slug;
    }

    /**
     * Create page from template
     */
    private function create_page_from_template($template_id, $template_type, $kit_name, $kit_id, $decoded, $is_global_kit)
    {
        $created_page_id = null;
        $page_edit_url = null;

        $page_template_types = ['page', 'wp-page', 'single-page', 'landing-page', 'single-404', 'archive-blog'];
        $library_template_types = ['section-header', 'section-footer', 'single-post'];

        if (in_array($template_type, $page_template_types)) {
            // Duplicate detection: skip if page already exists for this template
            $existing = $this->find_existing_imported_page($kit_id, '', $kit_name);
            if ($existing) {
                $page_edit_url = admin_url('post.php?post=' . $existing->ID . '&action=elementor');
                return array(
                    'message'           => 'Template already imported, skipping duplicate.',
                    'template_id'       => $template_id,
                    'template_edit_url' => admin_url('post.php?post=' . $template_id . '&action=elementor'),
                    'page_created'      => true,
                    'page_id'           => $existing->ID,
                    'page_title'        => $existing->post_title,
                    'page_slug'         => $existing->post_name,
                    'page_edit_url'     => $page_edit_url,
                    'edit_url'          => $page_edit_url,
                    'view_url'          => get_permalink($existing->ID),
                    'skipped'           => true,
                );
            }

            $page_prefix = $this->get_page_prefix($kit_name, $decoded);
            $separator = apply_filters('jltma_new_page_separator', ' - ', $page_prefix, $kit_name);

            if (in_array($template_type, ['single-404', 'archive-blog'])) {
                $page_title = $kit_name;
                $page_slug = sanitize_title($kit_name);
            } else {
                $page_title = !empty($page_prefix) ? $page_prefix . $separator . $kit_name : $kit_name;
                $page_slug = sanitize_title(!empty($page_prefix) ? $page_prefix . '-' . $kit_name : $kit_name);
            }

            $slug_check = 0;
            $original_slug = $page_slug;
            while (get_page_by_path($page_slug)) {
                $slug_check++;
                $page_slug = $original_slug . '-' . $slug_check;
            }

            $post_status = apply_filters('jltma_new_page_status', 'publish', $kit_name, $decoded);

            $new_page_args = array(
                'post_title'    => $page_title,
                'post_name'     => $page_slug,
                'post_type'     => 'page',
                'post_status'   => $post_status,
                'post_content'  => '',
                'meta_input'    => array(
                    '_elementor_edit_mode'        => 'builder',
                    '_elementor_template_type'    => 'wp-page',
                    '_elementor_version'          => ELEMENTOR_VERSION,
                    '_elementor_data'             => wp_slash(json_encode($decoded['content'])),
                    '_jltma_demo_import_item'     => true,
                    '_jltma_import_kit_id'        => $kit_id,
                    '_jltma_import_date'          => current_time('mysql'),
                    '_jltma_original_template_id' => $kit_id,
                    '_jltma_from_template_id'     => $template_id
                )
            );

            $created_page_id = wp_insert_post($new_page_args);

            if (!is_wp_error($created_page_id)) {
                update_post_meta($created_page_id, '_wp_page_template', 'elementor_canvas');
                $this->handle_special_template_type($template_type, $created_page_id);
                $page_edit_url = admin_url('post.php?post=' . $created_page_id . '&action=elementor');

                // Generate Elementor CSS for the new page
                if (class_exists('\Elementor\Core\Files\CSS\Post')) {
                    $css_file = new \Elementor\Core\Files\CSS\Post($created_page_id);
                    $css_file->update();
                }
            }
        }

        // Clear Elementor global cache
        if (class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }

        // Prepare response
        $response = array(
            'message' => 'Template imported successfully!',
            'template_id' => $template_id,
            'template_edit_url' => admin_url('post.php?post=' . $template_id . '&action=elementor')
        );

        if ($is_global_kit) {
            $response['is_global_kit'] = true;
            $response['message'] = 'Global kit styles imported and activated successfully!';
            $response['edit_url'] = admin_url('admin.php?page=elementor#/kit-library');
            $response['active_kit_id'] = $template_id;
        } elseif ($created_page_id && !is_wp_error($created_page_id)) {
            $response['page_created'] = true;
            $response['page_id'] = $created_page_id;
            $response['page_title'] = $page_title ?? $kit_name;
            $response['page_slug'] = $page_slug ?? '';
            $response['page_edit_url'] = $page_edit_url;
            $response['edit_url'] = $page_edit_url;
            $response['view_url'] = get_permalink($created_page_id);
            $response['message'] = 'Template imported and page created successfully!';
        } else {
            $response['page_created'] = false;
            $response['edit_url'] = $response['template_edit_url'];
            $response['view_url'] = get_permalink($template_id);
        }

        return $response;
    }

    /**
     * Check if a page with this template was already imported
     */
    private function find_existing_imported_page($template_id, $kit, $title)
    {
        global $wpdb;

        // Check for existing page with same original_template_id
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for meta join lookup without core API equivalent
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT p.ID FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_type = 'page'
             AND p.post_status = 'publish'
             AND pm.meta_key = '_jltma_original_template_id'
             AND pm.meta_value = %s
             LIMIT 1",
            $template_id
        ));

        if ($existing) {
            return get_post($existing);
        }

        return null;
    }

    /**
     * Get page prefix from manifest
     */
    private function get_page_prefix($kit_name, $decoded)
    {
        $page_prefix = 'New';

        // Nonce is verified upstream in import_templates_kit() before this private helper runs.
        if (isset($_POST['parentTemplate'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in calling method
            $parent_template = sanitize_text_field( wp_unslash( $_POST['parentTemplate'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in calling method
            $upload_dir = wp_upload_dir();

            $manifest_path = $upload_dir['basedir'] . '/master_addons/purchased_kits/kits/kit_' . $parent_template . '/manifest.json';

            if (!file_exists($manifest_path)) {
                $manifest_path = $upload_dir['basedir'] . '/master_addons/templates_kits/kits/kit_' . $parent_template . '.json';
                if (!file_exists($manifest_path)) {
                    $manifest_path = $upload_dir['basedir'] . '/master_addons/templates_kits/kits/' . $parent_template . '.json';
                }
            }

            if (file_exists($manifest_path)) {
                $manifest_content = file_get_contents($manifest_path);
                $manifest = json_decode($manifest_content, true);

                if ($manifest && isset($manifest['title'])) {
                    $kit_title = $manifest['title'];
                    if (strpos($kit_title, '-') !== false) {
                        $parts = explode('-', $kit_title);
                        $page_prefix = trim($parts[0]);
                    } else {
                        $page_prefix = $kit_title;
                    }
                } elseif ($manifest && isset($manifest['kit_name'])) {
                    $kit_title = $manifest['kit_name'];
                    if (strpos($kit_title, '-') !== false) {
                        $parts = explode('-', $kit_title);
                        $page_prefix = trim($parts[0]);
                    } else {
                        $page_prefix = $kit_title;
                    }
                }
            } else {
                if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
                    $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
                    if (method_exists($cache_manager, 'get_kit_manifest')) {
                        $manifest = $cache_manager->get_kit_manifest($parent_template);
                        if ($manifest) {
                            $kit_title = $manifest['title'] ?? $manifest['kit_name'] ?? '';
                            if (!empty($kit_title)) {
                                if (strpos($kit_title, '-') !== false) {
                                    $parts = explode('-', $kit_title);
                                    $page_prefix = trim($parts[0]);
                                } else {
                                    $page_prefix = $kit_title;
                                }
                            }
                        }
                    }
                }
            }
        }

        return apply_filters('jltma_new_page_prefix', $page_prefix, $kit_name, $decoded);
    }

    /**
     * Handle special template types (landing page, 404, blog archive)
     */
    private function handle_special_template_type($template_type, $page_id)
    {
        if ($template_type === 'landing-page') {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $page_id);
        } elseif ($template_type === 'single-404') {
            update_option('jltma_404_page_id', $page_id);
            if (defined('ELEMENTOR_PRO_VERSION')) {
                $kit_id = get_option('elementor_active_kit');
                if ($kit_id) {
                    $kit_settings = get_post_meta($kit_id, '_elementor_page_settings', true);
                    if (!is_array($kit_settings)) {
                        $kit_settings = [];
                    }
                    $kit_settings['404_page_id'] = $page_id;
                    update_post_meta($kit_id, '_elementor_page_settings', $kit_settings);
                }
            }
        } elseif ($template_type === 'archive-blog') {
            update_option('show_on_front', 'page');
            update_option('page_for_posts', $page_id);
        }
    }

    /**
     * Import Template Kit via REST API with caching
     */
    public function import_kit_via_api($kit)
    {
        $cache_manager = null;
        if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
        }

        if (!$cache_manager) {
            return false;
        }

        $kit_data = $cache_manager->download_and_cache_kit($kit);

        if (!$kit_data || !$kit_data['success']) {
            return $this->import_kit_via_api_direct($kit);
        }

        $manifest_data = $kit_data['manifest'];
        if (!$manifest_data || !isset($manifest_data['pages'])) {
            $manifest_data = $cache_manager->get_kit_manifest($kit);
            if (!$manifest_data || !isset($manifest_data['pages'])) {
                return false;
            }
        }

        $imported_pages = array();
        foreach ($manifest_data['pages'] as $page_slug) {
            $template_data = $cache_manager->get_kit_template($kit, $page_slug);

            if ($template_data) {
                $page_id = $this->import_template_from_data($template_data, $kit);
            } else {
                $page_id = $this->import_single_template_from_api($kit, $page_slug);
            }

            if ($page_id) {
                $imported_pages[$page_slug] = $page_id;
            }
        }

        if (isset($manifest_data['global_settings'])) {
            $this->import_global_settings_from_api($manifest_data['global_settings']);
        }

        if (isset($imported_pages['home'])) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $imported_pages['home']);
        }

        return array(
            'success' => true,
            'imported_pages' => $imported_pages,
            'message' => 'Template kit imported successfully!'
        );
    }

    /**
     * Fallback: Import Template Kit directly via API (without caching)
     */
    public function import_kit_via_api_direct($kit)
    {
        $config = null;
        if (function_exists('MasterAddons\Inc\Admin\Templates\master_addons_templates')) {
            $templates_instance = \MasterAddons\Inc\Admin\Templates\master_addons_templates();
            if ($templates_instance && isset($templates_instance->config)) {
                $config = $templates_instance->config->get('api');
            }
        }

        $api_url = $config['base'] . $config['path'] . '/templates-kit/' . $kit . '/manifest.json';

        if (Helper::jltma_premium()) {
            $api_url = add_query_arg('pro_enabled', 'true', $api_url);
        }

        $response = wp_remote_get($api_url, [
            'timeout' => 60,
            'sslverify' => false,
            'headers' => [
                'User-Agent' => 'Master Addons Template Kit Importer/' . \JLTMA_VER
            ]
        ]);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        $manifest_data = json_decode(wp_remote_retrieve_body($response), true);
        if (!$manifest_data || !isset($manifest_data['pages'])) {
            return false;
        }

        $imported_pages = array();
        foreach ($manifest_data['pages'] as $page_slug) {
            $page_id = $this->import_single_template_from_api($kit, $page_slug);
            if ($page_id) {
                $imported_pages[$page_slug] = $page_id;
            }
        }

        if (isset($manifest_data['global_settings'])) {
            $this->import_global_settings_from_api($manifest_data['global_settings']);
        }

        if (isset($imported_pages['home'])) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $imported_pages['home']);
        }

        return array(
            'success' => true,
            'imported_pages' => $imported_pages,
            'message' => 'Template kit imported successfully via API!'
        );
    }

    /**
     * Import Single Template from API
     */
    public function import_single_template_from_api($kit, $template_slug)
    {
        $cache_manager = null;
        if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
        }

        if ($cache_manager) {
            $template_data = $cache_manager->get_kit_template($kit, $template_slug);
            if ($template_data) {
                return $this->import_template_from_data($template_data, $kit);
            }
        }

        $config = null;
        if (function_exists('MasterAddons\Inc\Admin\Templates\master_addons_templates')) {
            $templates_instance = \MasterAddons\Inc\Admin\Templates\master_addons_templates();
            if ($templates_instance && isset($templates_instance->config)) {
                $config = $templates_instance->config->get('api');
            }
        }

        $template_url = $config['base'] . $config['path'] . '/templates-kit/' . $kit . '/' . $template_slug . '.json';

        if (Helper::jltma_premium()) {
            $template_url = add_query_arg('pro_enabled', 'true', $template_url);
        }

        $response = wp_remote_get($template_url, array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json',
                'User-Agent' => 'Master-Addons/' . \JLTMA_VER
            )
        ));

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        $template_data = json_decode(wp_remote_retrieve_body($response), true);
        if (!$template_data) {
            return false;
        }

        return $this->import_template_from_data($template_data, $kit);
    }

    /**
     * Import template from data array
     */
    public function import_template_from_data($template_data, $kit)
    {
        if (!$template_data || !isset($template_data['page_settings'])) {
            return false;
        }

        // Duplicate detection: check if page with same title and kit already exists
        $post_title = $template_data['page_settings']['post_title'];
        $existing = get_posts(array(
            'post_type'   => 'page',
            'post_status' => 'publish',
            'title'       => $post_title,
            'meta_query'  => array(
                array(
                    'key'   => '_jltma_import_kit_id',
                    'value' => $kit,
                ),
            ),
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ));
        if (!empty($existing)) {
            return $existing[0];
        }

        $post_type = $template_data['page_settings']['post_type'] ?? 'page';

        $page_id = wp_insert_post(array(
            'post_title' => $template_data['page_settings']['post_title'],
            'post_type' => $post_type,
            'post_status' => 'publish',
            'post_content' => '',
            'meta_input' => array(
                '_elementor_edit_mode' => 'builder',
                '_elementor_template_type' => 'wp-page',
                '_elementor_version' => $template_data['version'],
                '_elementor_data' => wp_slash(json_encode($template_data['content'])),
                '_jltma_demo_import_item' => true,
                '_jltma_import_kit_id' => $kit,
                '_jltma_import_date' => current_time('mysql'),
                '_wp_page_template' => 'elementor_canvas',
            )
        ));

        if (is_wp_error($page_id)) {
            return false;
        }

        // Generate Elementor CSS for the imported page
        if (class_exists('\Elementor\Core\Files\CSS\Post')) {
            $css_file = new \Elementor\Core\Files\CSS\Post($page_id);
            $css_file->update();
        }

        return $page_id;
    }

    /**
     * Import Global Settings from API
     */
    public function import_global_settings_from_api($global_settings)
    {
        if (isset($global_settings['colors'])) {
            $elementor_scheme = get_option('elementor_scheme_color', array());

            $color_mapping = array(
                'primary' => '1',
                'secondary' => '2',
                'accent' => '3'
            );

            foreach ($global_settings['colors'] as $color_name => $color_value) {
                if (isset($color_mapping[$color_name])) {
                    $elementor_scheme[$color_mapping[$color_name]] = $color_value;
                }
            }

            update_option('elementor_scheme_color', $elementor_scheme);
        }

        if (isset($global_settings['typography'])) {
            $elementor_scheme = get_option('elementor_scheme_typography', array());

            if (isset($global_settings['typography']['primary_font'])) {
                $elementor_scheme['1'] = array(
                    'font_family' => $global_settings['typography']['primary_font'],
                    'font_weight' => '400'
                );
            }

            if (isset($global_settings['typography']['secondary_font'])) {
                $elementor_scheme['2'] = array(
                    'font_family' => $global_settings['typography']['secondary_font'],
                    'font_weight' => '400'
                );
            }

            update_option('elementor_scheme_typography', $elementor_scheme);
        }
    }

    /**
     * Reset Previous Import
     */
    public function reset_previous_import()
    {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if (!wp_verify_nonce($nonce, 'jltma-template-kits-js') || !current_user_can('manage_options')) {
            exit;
        }

        $args = [
            'post_type' => [
                'page',
                'post',
                'product',
                'elementor_library',
                'attachment',
            ],
            'post_status' => 'any',
            'posts_per_page' => '-1',
            'meta_key' => '_jltma_demo_import_item'
        ];

        $imported_items = new \WP_Query($args);

        if ($imported_items->have_posts()) {
            while ($imported_items->have_posts()) {
                $imported_items->the_post();

                if ('Default Kit' == get_the_title()) {
                    continue;
                }

                wp_delete_post(get_the_ID(), true);
            }

            wp_reset_query();

            $imported_terms = get_terms([
                'meta_key' => '_jltma_demo_import_item',
                'posts_per_page' => -1,
                'hide_empty' => false,
            ]);

            if (!empty($imported_terms)) {
                foreach ($imported_terms as $imported_term) {
                    wp_delete_term($imported_term->term_id, $imported_term->taxonomy);
                }
            }

            wp_send_json_success(esc_html__('Previous Import Files have been successfully Reset.', 'master-addons'));
        } else {
            wp_send_json_success(esc_html__('There is no Data for Reset.', 'master-addons'));
        }
    }

    /**
     * Final Settings Setup
     */
    public function final_settings_setup()
    {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if (!wp_verify_nonce($nonce, 'jltma-template-kits-js') || !current_user_can('manage_options')) {
            exit;
        }

        $kit = !empty(get_option('jltma-import-kit-id')) ? esc_html(get_option('jltma-import-kit-id')) : '';

        $this->import_elementor_site_settings($kit);
        $this->setup_templates($kit);
        $this->fix_elementor_images();

        delete_option('jltma-import-kit-id');

        $post = get_page_by_path('hello-world', OBJECT, 'post');
        if ($post) {
            wp_delete_post($post->ID, true);
        }

        wp_send_json_success();
    }

    /**
     * Activate Template Kit as Global Kit
     */
    public function activate_template_as_global_kit($template_id)
    {
        if (!$template_id || is_wp_error($template_id)) {
            return false;
        }

        $template_type = get_post_meta($template_id, '_elementor_template_type', true);
        if ($template_type !== 'kit') {
            return false;
        }

        update_option('elementor_active_kit', $template_id);

        if (class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }

        return true;
    }

    /**
     * Import Elementor Site Settings
     */
    public function import_elementor_site_settings($kit)
    {
        update_option('elementor_experiment-e_local_google_fonts', 'inactive');

        $response = wp_remote_get('https://master-addons.com/templates-kit/' . rawurlencode($kit) . '/site-settings.json', array('timeout' => 60));

        if (!is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response)) {
            $site_settings = json_decode(wp_remote_retrieve_body($response), true);

            if (!empty($site_settings['settings'])) {
                $default_kit = \Elementor\Plugin::$instance->documents->get_doc_for_frontend(get_option('elementor_active_kit'));

                $kit_settings = $default_kit->get_settings();
                $new_settings = $site_settings['settings'];
                $settings = array_merge($kit_settings, $new_settings);

                $default_kit->save(['settings' => $settings]);
            }
        }
    }

    /**
     * Setup Templates
     */
    public function setup_templates($kit)
    {
        $home_page = get_page_by_path('home-' . $kit);
        $blog_page = get_page_by_path('blog-' . $kit);

        if ($home_page) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $home_page->ID);

            if ($blog_page) {
                update_option('page_for_posts', $blog_page->ID);
            }
        }
    }

    /**
     * Fix Elementor Images
     */
    public function fix_elementor_images()
    {
        $args = array(
            'post_type' => ['page', 'elementor_library'],
            'posts_per_page' => '-1',
            'meta_key' => '_elementor_version'
        );
        $elementor_pages = new \WP_Query($args);

        if ($elementor_pages->have_posts()) {
            while ($elementor_pages->have_posts()) {
                $elementor_pages->the_post();

                $site_url = get_site_url();
                $site_url = str_replace('/', '\/', $site_url);
                $demo_site_url = 'https://el.master-addons.com/' . get_option('jltma-import-kit-id');
                $demo_site_url = str_replace('/', '\/', $demo_site_url);

                $data = get_post_meta(get_the_ID(), '_elementor_data', true);

                if (!empty($data)) {
                    if (is_string($data)) {
                        $data = preg_replace('/\\\{1}\/sites\\\{1}\/\d+/', '', $data);
                        $data = str_replace($demo_site_url, $site_url, $data);
                        $data = json_decode($data, true);
                    } elseif (is_array($data)) {
                        $data_string = wp_json_encode($data);
                        $data_string = preg_replace('/\\\{1}\/sites\\\{1}\/\d+/', '', $data_string);
                        $data_string = str_replace($demo_site_url, $site_url, $data_string);
                        $data = json_decode($data_string, true);
                    }
                }

                if (is_array($data)) {
                    $data = $this->sanitize_elementor_data($data);
                }

                update_metadata('post', get_the_ID(), '_elementor_data', $data);

                $page_settings = get_post_meta(get_the_ID(), '_elementor_page_settings', true);
                $page_settings = json_encode($page_settings);

                if (!empty($page_settings)) {
                    $page_settings = preg_replace('/\\\{1}\/sites\\\{1}\/\d+/', '', $page_settings);
                    $page_settings = str_replace($demo_site_url, $site_url, $page_settings);
                    $page_settings = json_decode($page_settings, true);
                }

                update_metadata('post', get_the_ID(), '_elementor_page_settings', $page_settings);
            }
        }

        Plugin::$instance->files_manager->clear_cache();
    }

    /**
     * Set Timeout for Image Request
     */
    public function set_image_request_timeout($timeout_value, $url)
    {
        if (strpos($url, 'https://master-addons.com/') === false) {
            return $timeout_value;
        }

        $valid_ext = preg_match('/^((https?:\/\/)|(www\.))([a-z0-9-].?)+(:[0-9]+)?\/[\w\-\@]+\.(jpg|png|gif|jpeg|svg)\/?$/i', $url);

        if ($valid_ext) {
            $timeout_value = 300;
        }

        return $timeout_value;
    }

    /**
     * Prevent WooCommerce creating default pages
     */
    public function disable_default_woo_pages_creation()
    {
        add_filter('woocommerce_create_pages', '__return_empty_array');
    }

    /**
     * Sanitize Elementor Data to prevent warnings
     */
    public function sanitize_elementor_data($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as &$element) {
            if (isset($element['elType']) && $element['elType'] === 'column') {
                if (!isset($element['settings']['_column_size'])) {
                    $element['settings']['_column_size'] = 100;
                }
            }

            if (isset($element['settings']) && is_array($element['settings'])) {
                $element['settings'] = $this->sanitize_element_settings($element['settings']);
            }

            if (isset($element['elements']) && is_array($element['elements'])) {
                $element['elements'] = $this->sanitize_elementor_data($element['elements']);
            }
        }

        return $data;
    }

    /**
     * Sanitize Element Settings (handles images and other settings)
     */
    public function sanitize_element_settings($settings)
    {
        if (!is_array($settings)) {
            return $settings;
        }

        $image_settings = [
            'image', 'background_image', 'hover_image', 'bg_image', 'icon_image',
            'gallery', 'images', 'slide_image', 'background_overlay_image',
            'testimonial_image', 'team_image', 'portfolio_image'
        ];

        foreach ($settings as $key => &$value) {
            if (in_array($key, $image_settings) && is_array($value)) {
                if (!isset($value['id']) || empty($value['id'])) {
                    $value['id'] = 0;
                }
                if (!isset($value['url'])) {
                    $value['url'] = '';
                }
            }

            if ($key === 'gallery' && is_array($value)) {
                foreach ($value as &$gallery_item) {
                    if (is_array($gallery_item) && !isset($gallery_item['id'])) {
                        $gallery_item['id'] = 0;
                    }
                    if (is_array($gallery_item) && !isset($gallery_item['url'])) {
                        $gallery_item['url'] = '';
                    }
                }
            }

            if (is_array($value) && !in_array($key, $image_settings)) {
                $value = $this->sanitize_element_settings($value);
            }
        }

        return $settings;
    }

    /**
     * Search Query Results
     */
    public function search_query_results()
    {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if (!wp_verify_nonce($nonce, 'jltma-template-kits-js') || !current_user_can('manage_options')) {
            exit;
        }

        $search_query = isset($_POST['search_query']) ? sanitize_text_field(wp_unslash($_POST['search_query'])) : '';
        // Log search queries for analytics (optional)
    }

    /**
     * Real mime types for WP 5.1.0+
     */
    public function real_mime_types_5_1_0($defaults, $file, $filename, $mimes, $real_mime)
    {
        return $this->real_mimes($defaults, $filename);
    }

    /**
     * Real mime types for older WP
     */
    public function real_mime_types($defaults, $file, $filename, $mimes)
    {
        return $this->real_mimes($defaults, $filename);
    }

    /**
     * Real mimes helper
     */
    public function real_mimes($defaults, $filename)
    {
        if (strpos($filename, 'main') !== false) {
            $defaults['ext']  = 'xml';
            $defaults['type'] = 'text/xml';
        }

        return $defaults;
    }

    /**
     * Import images to media library and replace URLs with attachment IDs
     */
    public function import_images_to_media($content, $kit_id, $kit_name = '')
    {
        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        if (!is_array($content)) {
            return $content;
        }

        foreach ($content as &$element) {
            if (isset($element['settings'])) {
                $element['settings'] = $this->import_settings_images($element['settings'], $kit_id, $kit_name);
            }

            if (isset($element['elements']) && is_array($element['elements'])) {
                $element['elements'] = $this->import_images_to_media($element['elements'], $kit_id, $kit_name);
            }
        }

        return $content;
    }

    /**
     * Replace image URLs with cached versions (deprecated - kept for compatibility)
     */
    public function replace_with_cached_images($content, $kit_id, $cache_manager)
    {
        return $this->import_images_to_media($content, $kit_id);
    }

    /**
     * Import images in element settings to media library
     */
    public function import_settings_images($settings, $kit_id, $kit_name = '')
    {
        if (!is_array($settings)) {
            return $settings;
        }

        $image_settings = [
            'image', 'background_image', 'hover_image', 'bg_image', 'icon_image',
            'gallery', 'images', 'slide_image', 'background_overlay_image',
            'testimonial_image', 'team_image', 'portfolio_image', 'logo_image',
            'before_image', 'after_image', 'author_image', 'product_image',
            'featured_image', 'fallback_image', 'mobile_image', 'tablet_image'
        ];

        foreach ($settings as $key => &$value) {
            if (in_array($key, $image_settings) && is_array($value) && isset($value['url'])) {
                $attachment_data = $this->import_single_image($value['url'], $kit_id, $kit_name);
                if ($attachment_data) {
                    $value['id'] = $attachment_data['id'];
                    $value['url'] = $attachment_data['url'];
                }
            }

            if ($key === 'gallery' && is_array($value)) {
                foreach ($value as &$gallery_item) {
                    if (is_array($gallery_item) && isset($gallery_item['url'])) {
                        $attachment_data = $this->import_single_image($gallery_item['url'], $kit_id, $kit_name);
                        if ($attachment_data) {
                            $gallery_item['id'] = $attachment_data['id'];
                            $gallery_item['url'] = $attachment_data['url'];
                        }
                    }
                }
            }

            if (is_array($value) && !in_array($key, $image_settings)) {
                foreach ($value as &$repeater_item) {
                    if (is_array($repeater_item)) {
                        $repeater_item = $this->import_settings_images($repeater_item, $kit_id, $kit_name);
                    }
                }
            }

            if (is_string($value) && (strpos($key, 'css') !== false || strpos($key, 'style') !== false)) {
                $value = $this->import_css_images($value, $kit_id, $kit_name);
            }
        }

        return $settings;
    }

    /**
     * Import images from CSS strings
     */
    public function import_css_images($css_string, $kit_id, $kit_name = '')
    {
        if (empty($css_string)) {
            return $css_string;
        }

        preg_match_all('/url\([\'"]?([^\'")]+)[\'"]?\)/i', $css_string, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $url) {
                if (strpos($url, 'data:') === 0 || strpos($url, 'http') !== 0) {
                    continue;
                }

                if (preg_match('/\.(jpg|jpeg|png|gif|svg|webp)$/i', $url)) {
                    $attachment_data = $this->import_single_image($url, $kit_id, $kit_name);
                    if ($attachment_data) {
                        $css_string = str_replace($url, $attachment_data['url'], $css_string);
                    }
                }
            }
        }

        return $css_string;
    }

    /**
     * Import a single image to media library
     */
    public function import_single_image($image_url, $kit_id, $kit_name = '')
    {
        if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
            return false;
        }

        if (isset($this->imported_images[$image_url])) {
            return $this->imported_images[$image_url];
        }

        $existing_attachment = $this->get_attachment_by_url($image_url);
        if ($existing_attachment) {
            $attachment_data = [
                'id' => $existing_attachment,
                'url' => wp_get_attachment_url($existing_attachment)
            ];
            $this->imported_images[$image_url] = $attachment_data;
            return $attachment_data;
        }

        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $tmp = download_url($image_url, 300);

        if (is_wp_error($tmp)) {
            $response = wp_remote_get($image_url, [
                'timeout' => 300,
                'sslverify' => false
            ]);

            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                return false;
            }

            $image_data = wp_remote_retrieve_body($response);
            $tmp = wp_tempnam();
            file_put_contents($tmp, $image_data); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- writing to a wp_tempnam() temp file that media_handle_sideload() then processes

            if (!file_exists($tmp) || filesize($tmp) === 0) {
                wp_delete_file($tmp);
                return false;
            }
        }

        $file_array = [];
        $file_array['name'] = basename($image_url);

        if (!pathinfo($file_array['name'], PATHINFO_EXTENSION)) {
            $file_info = wp_check_filetype($tmp);
            if ($file_info['ext']) {
                $file_array['name'] = 'image-' . uniqid() . '.' . $file_info['ext'];
            }
        }

        if (!empty($kit_name)) {
            $file_array['name'] = sanitize_title($kit_name) . '-' . $file_array['name'];
        }

        $file_array['tmp_name'] = $tmp;

        $attachment_id = media_handle_sideload($file_array, 0, null, [
            'post_title' => !empty($kit_name) ? $kit_name . ' - ' . pathinfo($file_array['name'], PATHINFO_FILENAME) : pathinfo($file_array['name'], PATHINFO_FILENAME),
            'post_content' => '',
            'post_status' => 'inherit'
        ]);

        wp_delete_file($tmp);

        if (is_wp_error($attachment_id)) {
            return false;
        }

        update_post_meta($attachment_id, '_jltma_imported_from_kit', $kit_id);
        update_post_meta($attachment_id, '_jltma_original_url', $image_url);
        update_post_meta($attachment_id, '_jltma_import_date', current_time('mysql'));
        if (!empty($kit_name)) {
            update_post_meta($attachment_id, '_jltma_kit_name', $kit_name);
        }

        $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if (empty($alt_text)) {
            update_post_meta($attachment_id, '_wp_attachment_image_alt', $kit_name ?: 'Template Image');
        }

        $attachment_data = [
            'id' => $attachment_id,
            'url' => wp_get_attachment_url($attachment_id)
        ];

        $this->imported_images[$image_url] = $attachment_data;

        return $attachment_data;
    }

    /**
     * Get attachment ID by URL
     */
    public function get_attachment_by_url($url)
    {
        global $wpdb;

        $clean_url = str_replace(['https://', 'http://'], '', $url);

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for attachment lookup by guid without core API equivalent
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s AND post_type='attachment';", $url));
        if (!empty($attachment)) {
            return $attachment[0];
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for attachment lookup by normalised guid without core API equivalent
        $attachment = $wpdb->get_col($wpdb->prepare(
            "SELECT ID FROM $wpdb->posts WHERE (REPLACE(REPLACE(guid, 'https://', ''), 'http://', '') = %s) AND post_type='attachment' LIMIT 1;",
            $clean_url
        ));
        if (!empty($attachment)) {
            return $attachment[0];
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for attachment lookup by custom meta without core API equivalent
        $attachment = $wpdb->get_col($wpdb->prepare(
            "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_jltma_original_url' AND meta_value=%s LIMIT 1;",
            $url
        ));
        if (!empty($attachment)) {
            return $attachment[0];
        }

        return false;
    }

    /**
     * Clean up orphaned template images
     */
    public function cleanup_template_images()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for bulk meta key lookup without core API equivalent
        $template_attachments = $wpdb->get_col(
            "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_jltma_imported_from_kit'"
        );

        $deleted_count = 0;
        $kept_count = 0;

        foreach ($template_attachments as $attachment_id) {
            $is_used = false;

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for bulk content LIKE search without core API equivalent
            $used_in_content = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->posts WHERE post_content LIKE %s OR post_content LIKE %s",
                '%' . $wpdb->esc_like( 'wp-image-' . $attachment_id ) . '%',
                '%' . $wpdb->esc_like( 'attachment_' . $attachment_id ) . '%'
            ));

            if ($used_in_content > 0) {
                $is_used = true;
            }

            if (!$is_used) {
                $attachment_url = wp_get_attachment_url($attachment_id);
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for Elementor meta LIKE search without core API equivalent
                $used_in_elementor = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key='_elementor_data' AND meta_value LIKE %s",
                    '%"id":' . $attachment_id . '%'
                ));

                if ($used_in_elementor > 0) {
                    $is_used = true;
                }
            }

            if (!$is_used) {
                wp_delete_attachment($attachment_id, true);
                $deleted_count++;
            } else {
                $kept_count++;
            }
        }

        wp_send_json_success([
            'message' => sprintf('Cleanup complete: %d orphaned images deleted, %d in use kept', $deleted_count, $kept_count),
            'deleted' => $deleted_count,
            'kept' => $kept_count
        ]);
    }

    /**
     * Pre-import all images for a template kit to media library
     */
    public function preimport_kit_images()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $kit_id = isset($_POST['kit_id']) ? sanitize_text_field( wp_unslash( $_POST['kit_id'] ) ) : '';
        $template_data = isset($_POST['template_data']) ? sanitize_textarea_field( wp_unslash( $_POST['template_data'] ) ) : '';

        if (empty($kit_id)) {
            wp_send_json_error(['message' => 'Kit ID is required']);
            return;
        }

        if (!empty($template_data)) {
            $decoded = json_decode($template_data, true);
            if ($decoded && isset($decoded['content'])) {
                $image_urls = $this->extract_all_image_urls($decoded['content']);
                $imported_count = 0;

                foreach ($image_urls as $url) {
                    $result = $this->import_single_image($url, $kit_id, $decoded['title'] ?? '');
                    if ($result) {
                        $imported_count++;
                    }
                }

                wp_send_json_success([
                    'message' => sprintf('Successfully imported %d images to media library', $imported_count),
                    'image_count' => $imported_count,
                    'imported_images' => $this->imported_images
                ]);
            }
        }

        wp_send_json_error(['message' => 'Invalid template data']);
    }

    /**
     * Extract all image URLs from Elementor content
     */
    public function extract_all_image_urls($content, &$urls = [])
    {
        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        if (!is_array($content)) {
            return $urls;
        }

        foreach ($content as $element) {
            if (isset($element['settings']) && is_array($element['settings'])) {
                $this->extract_urls_from_settings($element['settings'], $urls);
            }

            if (isset($element['elements']) && is_array($element['elements'])) {
                $this->extract_all_image_urls($element['elements'], $urls);
            }
        }

        return array_unique($urls);
    }

    /**
     * Extract image URLs from element settings
     */
    public function extract_urls_from_settings($settings, &$urls)
    {
        if (!is_array($settings)) {
            return;
        }

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                if (isset($value['url']) && filter_var($value['url'], FILTER_VALIDATE_URL)) {
                    $urls[] = $value['url'];
                }
                $this->extract_urls_from_settings($value, $urls);
            } elseif (is_string($value)) {
                if (preg_match_all('/(https?:\/\/[^\s"]+\.(?:jpg|jpeg|png|gif|svg|webp))/i', $value, $matches)) {
                    $urls = array_merge($urls, $matches[1]);
                }
            }
        }
    }

    /**
     * Pre-cache all images for a template kit (deprecated - kept for compatibility)
     */
    public function precache_kit_images()
    {
        $this->preimport_kit_images();
    }

    /**
     * Pre-download template kit for faster import
     */
    public function predownload_kit()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $kit_id = isset($_POST['kit_id']) ? sanitize_text_field( wp_unslash( $_POST['kit_id'] ) ) : '';

        if (empty($kit_id)) {
            wp_send_json_error(['message' => 'Kit ID is required']);
            return;
        }

        $cache_manager = null;
        if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
        }

        if (!$cache_manager) {
            wp_send_json_error(['message' => 'Cache manager not available']);
            return;
        }

        $result = $cache_manager->download_and_cache_kit($kit_id);

        if ($result && $result['success']) {
            $template_count = 0;
            if (isset($result['manifest']['pages']) && is_array($result['manifest']['pages'])) {
                $template_count = count($result['manifest']['pages']);
            }

            wp_send_json_success([
                'message' => sprintf('Kit pre-downloaded successfully with %d templates', $template_count),
                'kit_id' => $kit_id,
                'template_count' => $template_count,
                'cached' => true
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to download kit']);
        }
    }

    /**
     * Download all template kits for offline use
     */
    public function download_all_kits()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $cache_manager = null;
        if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
        }

        if (!$cache_manager) {
            wp_send_json_error(['message' => 'Cache manager not available']);
            return;
        }

        $stats = $cache_manager->download_all_kits();

        wp_send_json_success([
            'message' => sprintf(
                'Download complete: %d downloaded, %d skipped, %d failed out of %d total kits',
                $stats['downloaded'],
                $stats['skipped'],
                $stats['failed'],
                $stats['total']
            ),
            'stats' => $stats
        ]);
    }

    /**
     * Install required plugin for template kit
     */
    public function install_required_plugin()
    {
        $wpnonce_val = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
        $valid_nonce = wp_verify_nonce($wpnonce_val, 'jltma_template_kits_nonce_action') ||
                       wp_verify_nonce($wpnonce_val, 'jltma_template_library_nonce');

        if (!$valid_nonce || !current_user_can('install_plugins')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $plugin_name = isset($_POST['plugin_name']) ? sanitize_text_field( wp_unslash( $_POST['plugin_name'] ) ) : '';
        $plugin_file = isset($_POST['plugin_file']) ? sanitize_text_field( wp_unslash( $_POST['plugin_file'] ) ) : '';
        $plugin_slug = $this->get_plugin_slug($plugin_file);

        if (empty($plugin_slug)) {
            wp_send_json_error(['message' => 'Plugin slug is required']);
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

        if (empty($plugin_file)) {
            $plugin_file = $plugin_slug . '/' . $plugin_slug . '.php';
        }

        $installed_plugins = get_plugins();
        if (isset($installed_plugins[$plugin_file])) {
            $activate_result = activate_plugin($plugin_file);

            if (is_wp_error($activate_result)) {
                wp_send_json_error([
                    'message' => 'Plugin installed but activation failed: ' . $activate_result->get_error_message()
                ]);
            } else {
                wp_send_json_success([
                    'message' => sprintf('%s has been activated successfully!', $plugin_name ?: $plugin_slug),
                    'is_active' => true
                ]);
            }
            return;
        }

        $api = plugins_api('plugin_information', [
            'slug' => $plugin_slug,
            'fields' => [
                'sections' => false,
                'tags' => false
            ]
        ]);

        if (is_wp_error($api)) {
            if ($api->get_error_message() === "Plugin not found.") {
                $plugin_file_parts = explode('/', $plugin_file);
                if ($plugin_file_parts[0] !== $plugin_file_parts[1]) {
                    $plugin_slug = $plugin_file_parts[0];
                }
            }
            $new_api = plugins_api('plugin_information', [
                'slug' => $plugin_slug,
                'fields' => [
                    'sections' => false,
                    'tags' => false
                ]
            ]);
            if (is_wp_error($new_api)) {
                wp_send_json_error([
                    'message' => 'Failed to get plugin information: ',
                    'details' => $api->get_error_message()
                ]);
                return;
            } else {
                $api = $new_api;
            }
        }

        $skin = new \WP_Ajax_Upgrader_Skin();
        $upgrader = new \Plugin_Upgrader($skin);

        $result = $upgrader->install($api->download_link);

        if (is_wp_error($result)) {
            wp_send_json_error([
                'message' => 'Installation failed: ' . $result->get_error_message()
            ]);
            return;
        }

        if (!$result) {
            wp_send_json_error([
                'message' => 'Installation failed for unknown reason'
            ]);
            return;
        }

        $installed_plugins = get_plugins();
        $plugin_installed = false;

        foreach ($installed_plugins as $file => $plugin) {
            if (strpos($file, $plugin_slug . '/') === 0 || $file === $plugin_file) {
                $plugin_file = $file;
                $plugin_installed = true;
                break;
            }
        }

        if (!$plugin_installed) {
            wp_send_json_error([
                'message' => 'Plugin installed but could not be found'
            ]);
            return;
        }

        $activate_result = activate_plugin($plugin_file);

        if (is_wp_error($activate_result)) {
            wp_send_json_success([
                'message' => sprintf('%s has been installed successfully but needs manual activation.', $plugin_name ?: $plugin_slug),
                'is_active' => false
            ]);
        } else {
            wp_send_json_success([
                'message' => sprintf('%s has been installed and activated successfully!', $plugin_name ?: $plugin_slug),
                'is_active' => true
            ]);
        }
    }

    /**
     * Activate required plugin for template kit
     */
    public function activate_required_plugin()
    {
        $wpnonce_activate = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
        $valid_nonce = wp_verify_nonce($wpnonce_activate, 'jltma_template_kits_nonce_action') ||
                       wp_verify_nonce($wpnonce_activate, 'jltma_template_library_nonce');

        if (!$valid_nonce || !current_user_can('activate_plugins')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $plugin_slug = isset($_POST['plugin_slug']) ? sanitize_text_field( wp_unslash( $_POST['plugin_slug'] ) ) : '';
        $plugin_file = isset($_POST['plugin_file']) ? sanitize_text_field( wp_unslash( $_POST['plugin_file'] ) ) : '';

        if (empty($plugin_slug) && empty($plugin_file)) {
            wp_send_json_error(['message' => 'Plugin slug is required']);
            return;
        }

        if (!is_string($plugin_slug)) {
            $file_name = explode('/', $plugin_file)[1];
            $plugin_slug = explode('.', $file_name)[0];
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        if (empty($plugin_file) && $plugin_file !== 'theme') {
            $plugin_file = $plugin_slug . '/' . $plugin_slug . '.php';
        }

        if ($plugin_file === 'theme') {
            $theme = wp_get_theme($plugin_slug);

            if (!$theme->exists()) {
                wp_send_json_error(['message' => 'Theme not found. Please install it first.']);
                return;
            }

            switch_theme($plugin_slug);

            wp_send_json_success([
                'message' => ucfirst(str_replace('-', ' ', $plugin_slug)) . ' theme has been activated successfully!',
                'is_active' => true
            ]);
            return;
        }

        $installed_plugins = get_plugins();
        if (!isset($installed_plugins[$plugin_file])) {
            $plugin_found = false;
            foreach ($installed_plugins as $file => $plugin) {
                if (strpos($file, $plugin_slug . '/') === 0) {
                    $plugin_file = $file;
                    $plugin_found = true;
                    break;
                }
            }

            if (!$plugin_found) {
                wp_send_json_error(['message' => 'Plugin not found. Please install it first.']);
                return;
            }
        }

        if (is_plugin_active($plugin_file)) {
            wp_send_json_success([
                'message' => 'Plugin is already active!',
                'is_active' => true
            ]);
            return;
        }

        $result = activate_plugin($plugin_file);

        if (is_wp_error($result)) {
            wp_send_json_error([
                'message' => 'Activation failed: ' . $result->get_error_message()
            ]);
        } else {
            wp_send_json_success([
                'message' => 'Plugin activated successfully!',
                'is_active' => true
            ]);
        }
    }

    /**
     * Get plugin slug from plugin file
     */
    public function get_plugin_slug($plugin_file)
    {
        $without_extension = pathinfo($plugin_file, PATHINFO_FILENAME);
        $parts = explode('/', $without_extension);
        $filename = end($parts);

        return $filename;
    }

    /**
     * Handle Template Kit Upload
     */
    public function upload_template_kit()
    {
        $wpnonce_upload = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
        $valid_nonce = wp_verify_nonce($wpnonce_upload, 'jltma_template_kits_nonce_action') ||
                       wp_verify_nonce($wpnonce_upload, 'jltma_template_library_nonce');
        if (!$valid_nonce || !current_user_can('upload_files')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        if (!isset($_FILES['kit_file']) || $_FILES['kit_file']['error'] !== UPLOAD_ERR_OK) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- 'kit_file' existence verified by isset above
            wp_send_json_error(['message' => 'No file uploaded or upload failed']);
            return;
        }

        // ── Resource headroom ────────────────────────────────────────────
        // Extracting a multi-MB zip and reading dozens of JSON template
        // files in one request can easily hit the default 30s / 128M
        // limits on shared hosting. Request generous limits up-front so
        // the "Reading kit contents" step isn't dragged out by throttled
        // resources. Only bumps the ceiling if the host allows it — a
        // disabled ini_set or a lower hard cap simply becomes the limit.
        if (function_exists('set_time_limit')) {
            @set_time_limit(600);
        }
        if (function_exists('wp_raise_memory_limit')) {
            wp_raise_memory_limit('admin');
        }
        @ini_set('memory_limit', '512M');
        @ignore_user_abort(true);

        $uploaded_file = $_FILES['kit_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- file upload array; individual keys sanitized before use

        $file_type = wp_check_filetype($uploaded_file['name']);
        if ($file_type['ext'] !== 'zip') {
            wp_send_json_error(['message' => 'Please upload a valid ZIP file']);
            return;
        }

        $cache_manager = null;
        if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
        }

        if (!$cache_manager) {
            wp_send_json_error(['message' => 'Cache manager not available']);
            return;
        }

        $file_name_without_ext = pathinfo($uploaded_file['name'], PATHINFO_FILENAME);

        if (preg_match('/^kit[_-]?(\d+)$/i', $file_name_without_ext, $matches)) {
            $kit_id = $matches[1];
        } else {
            $kit_id = (string)(9000 + time() % 1000);
        }

        global $wp_filesystem;
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        WP_Filesystem();

        $upload_dir = wp_upload_dir();
        $kits_dir = $upload_dir['basedir'] . '/master_addons/purchased_kits/kits/kit_' . $kit_id;

        if (!$wp_filesystem->exists($kits_dir)) {
            $wp_filesystem->mkdir($kits_dir, 0755, true);
        }

        // Native PHP's ZipArchive is significantly faster than WP's
        // unzip_file() because it avoids the pclzip pure-PHP fallback,
        // streams file entries directly to disk, and doesn't double-buffer
        // each file in memory. Falls back to unzip_file() when the ext
        // isn't available (extremely rare on modern PHP).
        $unzip_ok = false;
        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($uploaded_file['tmp_name']) === true) {
                $unzip_ok = $zip->extractTo($kits_dir);
                $zip->close();
            }
        }
        if (!$unzip_ok) {
            $unzip_result = unzip_file($uploaded_file['tmp_name'], $kits_dir);
            if (is_wp_error($unzip_result)) {
                $wp_filesystem->delete($kits_dir, true);
                wp_send_json_error(['message' => 'Failed to extract ZIP file: ' . $unzip_result->get_error_message()]);
                return;
            }
        }

        $manifest_path = $kits_dir . '/manifest.json';
        $manifest = null;

        if ($wp_filesystem->exists($manifest_path)) {
            $manifest_content = $wp_filesystem->get_contents($manifest_path);
            $manifest = json_decode($manifest_content, true);
        } else {
            $files = $wp_filesystem->dirlist($kits_dir);
            foreach ($files as $file) {
                if ($file['type'] === 'd') {
                    $sub_manifest = $kits_dir . '/' . $file['name'] . '/manifest.json';
                    if ($wp_filesystem->exists($sub_manifest)) {
                        $sub_dir = $kits_dir . '/' . $file['name'];
                        $sub_files = $wp_filesystem->dirlist($sub_dir);
                        foreach ($sub_files as $sub_file) {
                            $wp_filesystem->move($sub_dir . '/' . $sub_file['name'], $kits_dir . '/' . $sub_file['name']);
                        }
                        $wp_filesystem->delete($sub_dir, true);

                        $manifest_content = $wp_filesystem->get_contents($manifest_path);
                        $manifest = json_decode($manifest_content, true);
                        break;
                    }
                }
            }
        }

        if (!$manifest) {
            $wp_filesystem->delete($kits_dir, true);
            wp_send_json_error(['message' => 'Invalid template kit: manifest.json not found']);
            return;
        }

        $required_manifest_fields = ['manifest_version', 'title', 'kit_version', 'templates'];
        $missing_fields = [];

        foreach ($required_manifest_fields as $field) {
            if (!isset($manifest[$field])) {
                $missing_fields[] = $field;
            }
        }

        if (!empty($missing_fields)) {
            $wp_filesystem->delete($kits_dir, true);
            wp_send_json_error(['message' => 'Invalid manifest.json. Missing required fields: ' . implode(', ', $missing_fields)]);
            return;
        }

        if (!is_array($manifest['templates']) || empty($manifest['templates'])) {
            $wp_filesystem->delete($kits_dir, true);
            wp_send_json_error(['message' => 'Invalid template kit: templates array is empty or invalid']);
            return;
        }

        $screenshots_dir = $kits_dir . '/screenshots';
        $templates_dir = $kits_dir . '/templates';

        $missing_folders = [];

        if (!$wp_filesystem->exists($screenshots_dir)) {
            $missing_folders[] = 'screenshots';
        }

        if (!$wp_filesystem->exists($templates_dir)) {
            $missing_folders[] = 'templates';
        }

        if (!empty($missing_folders)) {
            $wp_filesystem->delete($kits_dir, true);
            wp_send_json_error(['message' => 'Invalid template kit structure. Missing required folders: ' . implode(', ', $missing_folders)]);
            return;
        }

        $template_count = 0;
        $templates = [];

        $manifest_templates = $manifest['templates'];
        $template_count = count($manifest_templates);

        foreach ($manifest_templates as $page) {
            $slug = $page['slug'] ?? $page['fileName'] ?? $page['name'] ?? '';

            $template_file = $templates_dir . '/' . $slug . '.json';
            if (!$wp_filesystem->exists($template_file)) {
                $template_file = $templates_dir . '/' . $slug;
            }

            if ($wp_filesystem->exists($template_file)) {
                $template_content = $wp_filesystem->get_contents($template_file);
                $template_data = json_decode($template_content, true);

                $thumbnail = '';
                if (isset($page['thumbnail']) && !empty($page['thumbnail'])) {
                    if (strpos($page['thumbnail'], 'http') !== 0) {
                        $thumbnail_file = $screenshots_dir . '/' . basename($page['thumbnail']);
                        if ($wp_filesystem->exists($thumbnail_file)) {
                            $thumbnail = 'screenshots/' . basename($page['thumbnail']);
                        } else {
                            $thumbnail = $page['thumbnail'];
                        }
                    } else {
                        $thumbnail = $page['thumbnail'];
                    }
                }

                $templates[] = [
                    'id' => $page['id'] ?? $page['slug'] ?? $slug,
                    'slug' => $slug,
                    'title' => $page['title'] ?? $page['name'] ?? $slug,
                    'thumbnail' => $thumbnail,
                    'content' => $template_data['content'] ?? $template_data,
                    'page_settings' => $template_data['page_settings'] ?? [],
                    'type' => $page['type'] ?? 'page',
                    'template_id' => $page['template_id'] ?? $page['id'] ?? ''
                ];
            }
        }

        $kit_thumbnail = '';
        if (isset($manifest['thumbnail_url']) && !empty($manifest['thumbnail_url'])) {
            $kit_thumbnail = $manifest['thumbnail_url'];
        } elseif (isset($manifest['thumbnail']) && !empty($manifest['thumbnail'])) {
            if (strpos($manifest['thumbnail'], 'http') !== 0) {
                $thumbnail_path = str_replace('screenshots/', '', $manifest['thumbnail']);
                if ($wp_filesystem->exists($kits_dir . '/' . $thumbnail_path)) {
                    $kit_thumbnail = $manifest['thumbnail'];
                }
            } else {
                $kit_thumbnail = $manifest['thumbnail'];
            }
        } elseif (!empty($templates) && isset($templates[0]['thumbnail'])) {
            $kit_thumbnail = $templates[0]['thumbnail'];
        }

        $kit_name = isset($manifest['title']) && !empty($manifest['title']) ? $manifest['title'] : (
            isset($manifest['name']) && !empty($manifest['name']) ? $manifest['name'] : 'Kit ' . $kit_id
        );

        $categories = ['purchased'];
        if (isset($manifest['categories'])) {
            if (is_array($manifest['categories'])) {
                $categories = array_merge($categories, $manifest['categories']);
            } elseif (is_string($manifest['categories']) && !empty($manifest['categories'])) {
                if (strpos($manifest['categories'], ',') !== false) {
                    $cats = array_map('trim', explode(',', $manifest['categories']));
                    $categories = array_merge($categories, $cats);
                } else {
                    $categories[] = trim($manifest['categories']);
                }
            }
        }
        $categories = array_unique($categories);

        $keywords = [];
        if (isset($manifest['keywords'])) {
            if (is_array($manifest['keywords'])) {
                $keywords = $manifest['keywords'];
            } elseif (is_string($manifest['keywords']) && !empty($manifest['keywords'])) {
                $keywords = array_map('trim', explode(',', $manifest['keywords']));
                $keywords = array_filter($keywords);
            }
        }

        $required_plugins = [];
        if (isset($manifest['required_plugins']) && is_array($manifest['required_plugins'])) {
            $required_plugins = $manifest['required_plugins'];
        } elseif (isset($manifest['requirements']) && is_array($manifest['requirements'])) {
            $required_plugins = $manifest['requirements'];
        }

        $kit_data = [
            'kit_id' => $kit_id,
            'template_id' => $kit_id,
            'kit_name' => $kit_name,
            'title' => $kit_name,
            'descriptions' => isset($manifest['description']) ? $manifest['description'] : 'Uploaded template kit',
            'description' => isset($manifest['description']) ? $manifest['description'] : 'Uploaded template kit',
            'author' => isset($manifest['author']) ? $manifest['author'] : 'Unknown',
            'categories' => $categories,
            'keywords' => $keywords,
            'thumbnail' => $kit_thumbnail,
            'preview_url' => isset($manifest['preview_url']) ? $manifest['preview_url'] : '',
            'is_purchased' => true,
            'purchasable' => false,
            'downloadable' => true,
            'is_pro' => false,
            'downloads' => isset($manifest['downloads']) ? $manifest['downloads'] : 0,
            'purchase_url' => '',
            'uploaded_date' => current_time('mysql'),
            'template_count' => isset($manifest['template_count']) ? $manifest['template_count'] : $template_count,
            'templates' => $templates,
            'required_plugins' => $required_plugins,
            'manifest' => $manifest,
            'kit_path' => $kits_dir
        ];

        $stored = $cache_manager->store_purchased_kit($kit_data);
        if (!$stored) {
            $wp_filesystem->delete($kits_dir, true);
            wp_send_json_error(['message' => 'Failed to store template kit']);
            return;
        }

        $meta_data = [
            'kit_id' => $kit_id,
            'uploaded_date' => $kit_data['uploaded_date'],
            'original_filename' => $uploaded_file['name'],
            'is_purchased' => true
        ];

        $wp_filesystem->put_contents(
            $kits_dir . '/meta.json',
            wp_json_encode($meta_data, JSON_PRETTY_PRINT)
        );

        wp_send_json_success([
            'kit_id'         => $kit_id,
            'name'           => $kit_data['kit_name'],
            'template_count' => $template_count,
            'is_purchased'   => true,
            'message'        => sprintf('Template kit "%s" uploaded successfully with %d templates', $kit_data['kit_name'], $template_count),
            'templates'      => $templates,
            'parentTemplate' => [
                'kit_id'           => $kit_id,
                'kit_name'         => $kit_data['kit_name'],
                'title'            => $kit_data['kit_name'],
                'categories'       => $kit_data['categories'],
                'required_plugins' => $required_plugins,
                'is_purchased'     => true,
            ],
        ]);
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
