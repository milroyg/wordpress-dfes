<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Template Library Main Class
 *
 * Handles the Template Library functionality for Master Addons
 */
class JLTMA_Template_Library {

    private static $instance = null;

    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'), 30);
        // add_action('wp_ajax_jltma_get_templates', array($this, 'get_templates')); // Disabled to prevent conflict with manager.php
        add_action('wp_ajax_jltma_get_categories', array($this, 'get_categories'));
        add_action('wp_ajax_jltma_get_kit_categories', array($this, 'get_kit_categories'));
        add_action('wp_ajax_jltma_import_template', array($this, 'import_template'));
        add_action('wp_ajax_jltma_preview_template', array($this, 'preview_template'));
        add_action('wp_ajax_jltma_open_template', array($this, 'jltma_template_kit_open_kit'));
        add_action('wp_ajax_jltma_get_plugins_status', array($this, 'jltma_get_plugins_status'));
        add_action('wp_ajax_jltma_refresh_templates_cache', array($this, 'refresh_templates_cache'));
        add_action('wp_ajax_jltma_delete_purchased_kit', array($this, 'delete_purchased_kit'));
    }

    /**
     * Add Template Library submenu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'master-addons-settings',
            __('Template Library', 'master-addons'),
            __('Template Library', 'master-addons'),
            'manage_options',
            'jltma-template-library',
            array($this, 'admin_page'),
            3 // Position after Template Kits
        );
    }

    /**
     * Render the Template Library admin page
     */
    public function admin_page() {
        ?>
        <div id="jltma-template-library-root" class="jltma-template-library">
            <!-- React app will be mounted here -->
            <div class="loading-placeholder">
                <div class="loading-spinner"></div>
                <p><?php esc_html_e('Loading Template Library...', 'master-addons'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX: Get templates
     */
    public function get_templates() {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce') || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = 15;
        $tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : 'master_section';

        // Use existing template manager instead of mock data
        $templates = $this->fetch_real_templates($tab, $category, $search, $page, $per_page);

        // Add debug logging

        // If no templates found, provide some fallback data for debugging
        if (empty($templates['templates'])) {
            $templates = [
                'templates' => [
                    [
                        'id' => 'test-template-1',
                        'title' => 'Test Business Template',
                        'category' => 'business',
                        'thumbnail' => JLTMA_URL . '/assets/images/icon.png',
                        'preview_url' => '#',
                        'is_pro' => false,
                        'tags' => ['business', 'test']
                    ],
                    [
                        'id' => 'test-template-2',
                        'title' => 'Test Portfolio Template',
                        'category' => 'portfolio',
                        'thumbnail' => JLTMA_URL . '/assets/images/icon.png',
                        'preview_url' => '#',
                        'is_pro' => false,
                        'tags' => ['portfolio', 'test']
                    ]
                ],
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => 1,
                    'total_items' => 2,
                    'has_more' => false
                ]
            ];
        }

        wp_send_json_success($templates);
    }

    /**
     * AJAX: Get categories
     */
    public function get_categories() {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce') || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : 'master_section';
        $categories = $this->fetch_real_categories($tab);

        // Add debug logging

        // If no categories found, provide fallback data
        if (empty($categories) || count($categories) <= 1) {
            $categories = [
                ['id' => 'all', 'name' => 'All Types', 'count' => 2],
                ['id' => 'business', 'name' => 'Business', 'count' => 1],
                ['id' => 'portfolio', 'name' => 'Portfolio', 'count' => 1],
            ];
        }

        wp_send_json_success($categories);
    }

    /**
     * AJAX: Get kit categories
     */
    public function get_kit_categories() {
        $valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce') ||
                       wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action');

        if (!$valid_nonce || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        // Check if force refresh is requested
        $force_refresh = isset($_POST['force_refresh']) && $_POST['force_refresh'] === 'true';

        // Get cached or fresh categories from API
        $categories = $this->get_cached_kit_categories($force_refresh);

        if (empty($categories)) {
            $categories = [];
        }

        // Ensure we have an array structure
        if (!is_array($categories)) {
            $categories = [];
        }

        // Always add "All Types" as first category if not present
        $has_all = false;
        foreach ($categories as $cat) {
            if (isset($cat['id']) && $cat['id'] === 'all') {
                $has_all = true;
                break;
            }
        }

        if (!$has_all) {
            array_unshift($categories, ['id' => 'all', 'name' => 'All Types', 'count' => 0]);
        }

        // Count purchased kits and add/update purchased category
        $purchased_count = 0;
        if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();
            if (method_exists($cache_manager, 'get_purchased_kits')) {
                $purchased_kits = $cache_manager->get_purchased_kits();
                $purchased_count = is_array($purchased_kits) ? count($purchased_kits) : 0;
            }
        }

        // Add or update purchased category if there are purchased kits
        if ($purchased_count > 0) {
            $has_purchased = false;
            foreach ($categories as &$cat) {
                if (isset($cat['id']) && $cat['id'] === 'purchased') {
                    $cat['count'] = $purchased_count;
                    $has_purchased = true;
                    break;
                }
            }

            // If purchased category doesn't exist, add it
            if (!$has_purchased) {
                $categories[] = [
                    'id' => 'purchased',
                    'name' => 'Purchased',
                    'count' => $purchased_count
                ];
            }
        }

        // Calculate total count for "All Types"
        $total_count = 0;
        foreach ($categories as &$cat) {
            if (isset($cat['id']) && $cat['id'] !== 'all' && isset($cat['count'])) {
                $total_count += (int)$cat['count'];
            }
        }

        // Update "All Types" count
        foreach ($categories as &$cat) {
            if (isset($cat['id']) && $cat['id'] === 'all') {
                $cat['count'] = $total_count;
                break;
            }
        }

        // If still empty, provide default categories
        if (empty($categories)) {
            $categories = [
                ['id' => 'all', 'name' => 'All Types', 'count' => 0],
            ];

            if ($purchased_count > 0) {
                $categories[] = ['id' => 'purchased', 'name' => 'Purchased', 'count' => $purchased_count];
            }
        }

        wp_send_json_success($categories);
    }

    /**
     * Get cached kit categories
     */
    private function get_cached_kit_categories($force_refresh = false) {
        $upload_dir = wp_upload_dir();
        $categories_file = $upload_dir['basedir'] . '/master_addons/templates_kits/kit-categories.json';

        if (!$force_refresh && file_exists($categories_file)) {
            $categories_content = file_get_contents($categories_file);
            $categories = json_decode($categories_content, true);

            if ($categories !== null && is_array($categories)) {
                return $categories;
            }
        }

        // Try to get cache manager instance
        if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();

            // Check if we can use file cache
            if (method_exists($cache_manager, 'get_cached_kit_categories')) {
                $cached = $cache_manager->get_cached_kit_categories($force_refresh);
                if ($cached !== false) {
                    if (!file_exists($categories_file) && !empty($cached)) {
                        @file_put_contents($categories_file, json_encode($cached));
                    }
                    return $cached;
                }
            }
        }

        // Fallback to transient cache if cache manager not available
        $transient_key = 'jltma_kit_categories_cache';
        $cache_expiry = 12 * HOUR_IN_SECONDS; // 12 hours

        // Check transient cache first
        if (!$force_refresh) {
            $cached_categories = get_transient($transient_key);
            if ($cached_categories !== false) {
                return $cached_categories;
            }
        }

        // Only fetch from API if local file doesn't exist or force refresh
        if ($force_refresh || !file_exists($categories_file)) {
            // Fetch fresh categories from API
            $fresh_categories = $this->fetch_kit_categories();

            if ($fresh_categories !== false && !empty($fresh_categories)) {
                // Cache the categories
                set_transient($transient_key, $fresh_categories, $cache_expiry);

                // Save to local file
                @file_put_contents($categories_file, json_encode($fresh_categories));

                return $fresh_categories;
            }
        }

        // Return cached data even if expired as fallback
        $cached_categories = get_transient($transient_key);
        return $cached_categories !== false ? $cached_categories : [];
    }

    private function fetch_kit_categories()
    {
        // Get config from the templates system
        $config = null;
        if (function_exists('MasterAddons\Inc\Templates\master_addons_templates')) {
            $templates_instance = \MasterAddons\Inc\Templates\master_addons_templates();
            if ($templates_instance && isset($templates_instance->config)) {
                $config = $templates_instance->config->get('api');
            }
        }
        $api_url = $config['base'] . $config['path'] . $config['endpoints']['categories'] . 'template_kits';

        // Add pro_enabled parameter if pro is enabled
        if (\MasterAddons\Inc\Helper\Master_Addons_Helper::jltma_premium()) {
            $api_url = add_query_arg('pro_enabled', 'true', $api_url);
        }

        $response = wp_remote_get($api_url, [
            'timeout' => 15, // Reduced from 60 to 15 seconds
            'sslverify' => false,
            'headers' => [
                'User-Agent' => 'Master Addons Template Kit Cache/' . JLTMA_VER
            ]
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['success']) || !$data['success']) {
            return false;
        }

        return isset($data['categories']) ? $data['categories'] : [];
    }

    /**
     * AJAX: Import template
     */
    public function import_template() {
        // Accept both template library and template kits nonces
        $valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce') ||
                       wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action');

        if (!$valid_nonce || !current_user_can('import')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
        $tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : 'master_section';
        $page_name = isset($_POST['page_name']) ? sanitize_text_field($_POST['page_name']) : null;

        if (empty($template_id)) {
            wp_send_json_error(['message' => 'Template ID is required']);
            return;
        }

        // Check if this is a demo import
        if ($tab === 'demo') {
            $result = $this->import_demo_template($template_id);
        } else {
            // Use existing template manager for import - now with page_name
            $result = $this->import_real_template($template_id, $tab, $page_name);
        }

        if ($result && !is_wp_error($result)) {
            wp_send_json_success([
                'message' => 'Template imported successfully',
                'page_id' => $result['page_id'] ?? 0,
                'edit_url' => $result['edit_url'] ?? ''
            ]);
        } else {
            $error_message = is_wp_error($result) ? $result->get_error_message() : 'Failed to import template';
            wp_send_json_error(['message' => $error_message]);
        }
    }

    /**
     * AJAX: Preview template
     */
    public function preview_template() {
        // Accept both template library and template kits nonces
        $valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce') ||
                       wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action');

        if (!$valid_nonce || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
        $mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : 'library';
        if (empty($template_id)) {
            wp_send_json_error(['message' => 'Template ID is required']);
            return;
        }

        // Get preview URL
        $preview_url = $this->get_template_preview_url($template_id, $mode);

        wp_send_json_success(['preview_url' => $preview_url]);
    }

    /**
     * Fetch templates from real API using existing manager
     */
    private function fetch_real_templates($tab = 'master_section', $category = '', $search = '', $page = 1, $per_page = 12) {
        // Get template manager instance
        if (!class_exists('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Manager')) {
            return [
                'templates' => [],
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => 0,
                    'total_items' => 0,
                    'has_more' => false
                ]
            ];
        }

        $manager = \MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Manager::get_instance();

        // Get the source for the tab
        $source = $manager->get_template_source('master-api');
        if (!$source) {
            return [
                'templates' => [],
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => 0,
                    'total_items' => 0,
                    'has_more' => false
                ]
            ];
        }


        // Get templates directly from source
        $templates = $source->get_items($tab);
        if (!is_array($templates)) {
            $templates = [];
        } else {
        }

        // Filter by category if specified
        if (!empty($category) && $category !== 'all') {
            $templates = array_filter($templates, function($template) use ($category) {
                return isset($template['categories']) && in_array($category, $template['categories']);
            });
        }

        // Filter by search term if specified
        if (!empty($search)) {
            $templates = array_filter($templates, function($template) use ($search) {
                return stripos($template['title'] ?? '', $search) !== false ||
                       stripos($template['content'] ?? '', $search) !== false;
            });
        }

        // Apply pagination
        $total_templates = count($templates);
        $offset = ($page - 1) * $per_page;
        $paginated_templates = array_slice($templates, $offset, $per_page);

        return [
            'templates' => array_values($paginated_templates),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_templates / $per_page),
                'total_items' => $total_templates,
                'has_more' => ($offset + $per_page) < $total_templates
            ]
        ];
    }

    /**
     * Fetch categories from real API using existing manager
     */
    private function fetch_real_categories($tab = 'master_section') {
        // Get template manager instance
        if (!class_exists('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Manager')) {
            return [['id' => 'all', 'name' => 'All Types', 'count' => 0]];
        }

        $manager = \MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Manager::get_instance();

        // Get the source for the tab
        $source = $manager->get_template_source('master-api');
        if (!$source) {
            return [['id' => 'all', 'name' => 'All Types', 'count' => 0]];
        }

        // Get templates to calculate counts
        $templates = $source->get_items($tab);
        if (!is_array($templates)) {
            $templates = [];
        }

        // Get categories directly from source
        $source_categories = $source->get_categories($tab);
        if (!is_array($source_categories)) {
            $source_categories = [];
        }

        // Calculate category counts
        $category_counts = [];
        foreach ($templates as $template) {
            if (isset($template['categories']) && is_array($template['categories'])) {
                foreach ($template['categories'] as $cat) {
                    if (!isset($category_counts[$cat])) {
                        $category_counts[$cat] = 0;
                    }
                    $category_counts[$cat]++;
                }
            }
        }

        $categories = [];

        // Add "All Types" as first category with total count
        $categories[] = ['id' => 'all', 'name' => 'All Types', 'count' => count($templates)];

        // Transform categories to expected format with counts
        foreach ($source_categories as $category) {
            $slug = $category['slug'] ?? '';
            $categories[] = [
                'id' => $slug,
                'name' => $category['title'] ?? 'Unknown',
                'count' => $category_counts[$slug] ?? 0
            ];
        }

        return $categories;
    }

    /**
     * AJAX: Refresh templates cache
     */
    public function refresh_templates_cache() {
        $nonce_valid = wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce') ||
                        wp_verify_nonce($_POST['_wpnonce'], 'master_addons_nonce');

        // Enhanced security checks
        if (!$nonce_valid) {
            wp_send_json_error(array('message' => 'Permission denied - nonce failed'));
        }

        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied - insufficient capabilities'));
        }


        // Get cache manager and refresh
        if (class_exists('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager')) {
            $cache_manager = \MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager::get_instance();
            $refreshed_data = $cache_manager->refresh_cache();

            wp_send_json_success([
                'message' => 'Templates cache refreshed successfully',
                'data' => $refreshed_data
            ]);
        } else {
            wp_send_json_error(['message' => 'Cache manager not available']);
        }
    }

    /**
     * Import demo template from local files
     */
    private function import_demo_template($template_id) {
        // Get template file from local demo directory
        $templates_dir = get_stylesheet_directory() . '/jltma_demo/templates';
        $template_files = glob($templates_dir . '/*.json');

        $template_data = null;
        foreach ($template_files as $template_file) {
            $json_content = file_get_contents($template_file);
            $decoded = json_decode($json_content, true);

            if ($decoded && isset($decoded['template_id']) && $decoded['template_id'] == $template_id) {
                $template_data = $decoded;
                break;
            }
        }

        if (!$template_data) {
            return new WP_Error('template_not_found', 'Demo template not found');
        }

        // Ensure Elementor is loaded
        if (!did_action('elementor/loaded')) {
            return new WP_Error('elementor_not_loaded', 'Elementor is not loaded');
        }

        // Check if this is a re-import
        $is_reimport = isset($_POST['is_reimport']) && $_POST['is_reimport'] === '1';

        // Create a new page with the template content
        $page_title = isset($template_data['title']) ? $template_data['title'] : 'Demo Page ' . $template_id;
        $template_type = isset($template_data['type']) ? $template_data['type'] : 'page';

        // If re-importing, add a suffix to the title and slug
        if ($is_reimport) {
            // Find existing pages with this title
            $existing_pages = get_posts([
                'post_type' => 'page',
                'post_status' => 'any',
                's' => $page_title,
                'numberposts' => -1
            ]);

            // Calculate suffix number
            $suffix = 2;
            $base_title = $page_title;

            // Check for existing numbered versions
            foreach ($existing_pages as $page) {
                if (preg_match('/(.+)\s+(\d+)$/', $page->post_title, $matches)) {
                    if ($matches[1] === $base_title) {
                        $num = intval($matches[2]);
                        if ($num >= $suffix) {
                            $suffix = $num + 1;
                        }
                    }
                }
            }

            $page_title = $base_title . ' ' . $suffix;
        }

        // Prepare the page data
        $page_data = [
            'post_title' => $page_title,
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_content' => '', // Elementor will handle the content
            'meta_input' => [
                '_elementor_edit_mode' => 'builder',
                '_elementor_template_type' => $template_type,
                '_wp_page_template' => 'elementor_header_footer',
                '_jltma_demo_import_item' => true,
                '_jltma_import_date' => current_time('mysql'),
                '_jltma_original_template_id' => $template_id
            ]
        ];

        // Create the page
        $page_id = wp_insert_post($page_data);

        if (is_wp_error($page_id)) {
            return $page_id;
        }

        // Import Elementor data if available
        if (isset($template_data['content']) && !empty($template_data['content'])) {
            // Process the content for image imports if needed
            $processed_content = $this->process_demo_content($template_data['content']);

            // Save Elementor data
            update_post_meta($page_id, '_elementor_data', wp_json_encode($processed_content));
            update_post_meta($page_id, '_elementor_version', ELEMENTOR_VERSION);

            // Clear Elementor cache
            if (class_exists('\Elementor\Plugin')) {
                \Elementor\Plugin::$instance->files_manager->clear_cache();
            }
        }

        // Return success with page details
        return [
            'page_id' => $page_id,
            'edit_url' => admin_url('post.php?post=' . $page_id . '&action=elementor'),
            'view_url' => get_permalink($page_id),
            'message' => 'Demo template imported successfully'
        ];
    }

    /**
     * Process demo content for imports (handle images, etc.)
     */
    private function process_demo_content($content) {
        // If content is already an array, use it directly
        if (is_array($content)) {
            return $content;
        }

        // Try to decode if it's JSON
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // Return as-is if we can't process it
        return $content;
    }

    /**
     * Import real template using existing manager
     */
    private function import_real_template($template_id, $tab = 'master_section', $page_name = null) {
        // Get template manager instance
        if (!class_exists('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Manager')) {
            return new WP_Error('manager_not_found', 'Template manager not available');
        }

        $manager = \MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Manager::get_instance();

        // Instead of calling jltma_insert_inner_template which calls wp_die(),
        // we'll replicate its logic without the wp_send_json_* functions

        // Get the template source
        $source = $manager->get_template_source('master-api');
        if (!$source) {
            return new WP_Error('source_not_found', 'Template source not available');
        }

        // Get the template data
        $template_data = $source->get_item($template_id);
        if (!$template_data || empty($template_data['content'])) {
            return new WP_Error('template_not_found', 'Template content not found');
        }

        // Prepare the post title
        $post_title = $page_name ? sanitize_text_field($page_name) : 'Imported Template: ' . $template_id;
        if (strlen($post_title) > 255) {
            $post_title = substr($post_title, 0, 255);
        }

        // Validate Elementor data format
        $elementor_data = $template_data['content'];
        if (is_string($elementor_data)) {
            $decoded_data = json_decode($elementor_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new WP_Error('invalid_data', 'Invalid Elementor data format');
            }
        }

        // Create the template in Elementor library
        $library_post_id = wp_insert_post([
            'post_type'   => 'elementor_library',
            'post_title'  => $post_title,
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'meta_input'  => [
                '_elementor_data'          => $elementor_data,
                '_elementor_edit_mode'     => 'builder',
                '_elementor_template_type' => 'section',
                '_jltma_template_source'   => 'master-api',
                '_jltma_template_id'       => $template_id,
            ],
        ]);

        if (!$library_post_id || is_wp_error($library_post_id)) {
            return new WP_Error('creation_failed', 'Failed to create template in library');
        }

        // Now create a page that uses this template (if page_name is provided)
        if ($page_name) {
            $page_id = wp_insert_post([
                'post_title' => $page_name,
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'meta_input' => [
                    '_elementor_edit_mode' => 'builder',
                    '_elementor_data' => $elementor_data,
                    '_jltma_imported_template' => $template_id,
                    '_jltma_library_post_id' => $library_post_id,
                    '_jltma_import_date' => current_time('mysql')
                ]
            ]);

            if ($page_id && !is_wp_error($page_id)) {
                return [
                    'success' => true,
                    'page_id' => $page_id,
                    'library_post_id' => $library_post_id,
                    'edit_url' => admin_url('post.php?post=' . $page_id . '&action=elementor'),
                    'message' => 'Page created successfully with template'
                ];
            }
        }

        // Return library post info if no page was created
        return [
            'success' => true,
            'library_post_id' => $library_post_id,
            'edit_url' => admin_url('post.php?post=' . $library_post_id . '&action=elementor'),
            'message' => 'Template imported to library successfully'
        ];
    }

    /**
     * Get template preview URL
     */
    private function get_template_preview_url($template_id, $mode) {
        // if($mode === 'kits'){
        //     return 'http://testing.local/template-kits/?preview-kit=' . $template_id;
        // }
        // return 'http://testing.local/template-library/?preview-template=' . $template_id;
        if($mode === 'kits'){
            return 'https://staging.master-addons.com/template-kits/?kit=' . $template_id;
        }
        return 'https://staging.master-addons.com/template-library/?template=' . $template_id;
        // if($mode === 'kits'){
        //     return 'https://master-addons.com/template-kits/' . $template_id;
        // }
        // return 'https://master-addons.com/template-library/' . $template_id;
    }

    public function jltma_template_kit_open_kit(){
        // Verify nonce and permissions
        $valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce') ||
                       wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action');

        if (!$valid_nonce || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $kit_id = sanitize_text_field($_POST['template_id']);
        $kit_category = sanitize_text_field($_POST['template_category']);
        $force_refresh = isset($_POST['force_refresh']) && $_POST['force_refresh'] === 'true';


        $templates = false;
        $required_plugins = [];

        // First check if this is a purchased kit
        if ($kit_category === 'purchased' || $this->is_purchased_kit($kit_id)) {
            // Handle purchased kit
            if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
                $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();

                // Get purchased kit manifest
                if (method_exists($cache_manager, 'get_kit_manifest')) {
                    $manifest = $cache_manager->get_kit_manifest($kit_id);

                    if ($manifest) {
                        // Extract templates from manifest
                        $templates = $manifest['templates'] ?? $manifest['pages'] ?? [];

                        // Format templates for consistent response
                        if (!empty($templates)) {
                            // Build the base URL for the kit folder
                            $upload_dir = wp_upload_dir();
                            $kit_base_url = $upload_dir['baseurl'] . '/master_addons/purchased_kits/kits/kit_' . $kit_id . '/';

                            $formatted_templates = [];
                            foreach ($templates as $key => $template) {
                                // Get screenshot path from manifest
                                $screenshot_path = $template['screenshot'] ?? $template['thumbnail'] ?? '';

                                // Build full URL if we have a screenshot path
                                $thumbnail = '';
                                if (!empty($screenshot_path)) {
                                    // Check if it's already a full URL
                                    if (filter_var($screenshot_path, FILTER_VALIDATE_URL)) {
                                        $thumbnail = $screenshot_path;
                                    } else {
                                        // Remove leading slash if present
                                        $screenshot_path = ltrim($screenshot_path, '/');
                                        // Build the full URL
                                        $thumbnail = $kit_base_url . $screenshot_path;
                                    }
                                }

                                // Ensure each template has required fields
                                $formatted_template = [
                                    'template_id' => $template['template_id'] ?? $template['id'] ?? $key,
                                    'title' => $template['title'] ?? $template['name'] ?? '',
                                    'type' => $template['type'] ?? 'page',
                                    'thumbnail' => $thumbnail,
                                    'is_pro' => false,
                                    'content' => $template['content'] ?? null,
                                    'menu_order' => $template['menu_order'] ?? 0
                                ];
                                $formatted_templates[] = $formatted_template;
                            }
                            $templates = $formatted_templates;
                        }

                        // Get required plugins from manifest
                        $required_plugins = $manifest['required_plugins'] ?? [];

                        // Process required plugins to check their installation status
                        $processed_plugins = [];
                        foreach ($required_plugins as $key => $plugin) {
                            // Handle both array and string formats
                            if (is_string($plugin)) {
                                $plugin = ['name' => $plugin, 'slug' => sanitize_title($plugin)];
                            }

                            // Map 'file' to 'plugin_file' if needed
                            if (isset($plugin['file']) && !isset($plugin['plugin_file'])) {
                                $plugin['plugin_file'] = $plugin['file'];
                            }

                            // Determine plugin slug
                            $plugin_slug = $plugin['slug'] ?? sanitize_title($plugin['name'] ?? '');

                            // Check plugin installation and activation status
                            $is_installed = false;
                            $is_active = false;

                            // Check if it's a theme based on type from manifest
                            if (isset($plugin['type']) && $plugin['type'] === 'theme') {
                                $theme = wp_get_theme($plugin_slug);
                                $is_installed = $theme->exists();
                                $is_active = (get_option('stylesheet') === $plugin_slug);
                                $plugin_file = 'theme';
                            } else {
                                // It's a plugin - determine the plugin file path
                                $plugin_file = '';

                                if (isset($plugin['plugin_file'])) {
                                    $plugin_file = $plugin['plugin_file'];
                                } elseif (isset($plugin['file'])) {
                                    $plugin_file = $plugin['file'];
                                } else {
                                    // Default pattern: slug/slug.php
                                    $plugin_file = $plugin_slug . '/' . $plugin_slug . '.php';
                                }

                                // Check if plugin is installed
                                if (!function_exists('get_plugins')) {
                                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                                }

                                $all_plugins = get_plugins();
                                $is_installed = isset($all_plugins[$plugin_file]);

                                // If not found with default path, search through all plugins
                                if (!$is_installed) {
                                    foreach ($all_plugins as $path => $plugin_data) {
                                        if (strpos($path, $plugin_slug . '/') === 0 ||
                                            strpos($path, $plugin_slug . '.php') !== false) {
                                            $is_installed = true;
                                            $plugin_file = $path;
                                            break;
                                        }
                                    }
                                }

                                // Check if plugin is active
                                if ($is_installed) {
                                    $is_active = is_plugin_active($plugin_file);
                                }
                            }

                            // Build the processed plugin data
                            $processed_plugin = [
                                'slug' => $plugin_slug,
                                'name' => $plugin['name'] ?? ucfirst(str_replace('-', ' ', $plugin_slug)),
                                'required' => $plugin['required'] ?? true,
                                'version' => $plugin['version'] ?? '',
                                'is_installed' => $is_installed,
                                'is_active' => $is_active,
                                'plugin_file' => $plugin_file,
                                'type' => (isset($plugin['type']) && $plugin['type'] === 'theme') ? 'theme' : 'plugin'
                            ];

                            $processed_plugins[] = $processed_plugin;
                        }

                        $required_plugins = $processed_plugins;
                    }
                }

                // If no manifest, try to get from purchased kit data
                if (!$templates && method_exists($cache_manager, 'get_purchased_kit')) {
                    $purchased_kit = $cache_manager->get_purchased_kit($kit_id);
                    if ($purchased_kit) {
                        $templates = $purchased_kit['templates'] ?? [];

                        // Process templates to build proper thumbnail URLs
                        if (!empty($templates)) {
                            // Build the base URL for the kit folder
                            $upload_dir = wp_upload_dir();
                            $kit_base_url = $upload_dir['baseurl'] . '/master_addons/purchased_kits/kits/kit_' . $kit_id . '/';

                            $formatted_templates = [];
                            foreach ($templates as $key => $template) {
                                // Get screenshot path from data
                                $screenshot_path = $template['screenshot'] ?? $template['thumbnail'] ?? '';

                                // Build full URL if we have a screenshot path
                                $thumbnail = '';
                                if (!empty($screenshot_path)) {
                                    // Remove leading slash if present
                                    $screenshot_path = ltrim($screenshot_path, '/');
                                    // Build the full URL
                                    $thumbnail = $kit_base_url . $screenshot_path;
                                }

                                // Ensure template has all required fields
                                $formatted_template = [
                                    'template_id' => $template['template_id'] ?? $template['id'] ?? $key,
                                    'title' => $template['title'] ?? $template['name'] ?? '',
                                    'type' => $template['type'] ?? 'page',
                                    'thumbnail' => $thumbnail,
                                    'is_pro' => $template['is_pro'] ?? false,
                                    'content' => $template['content'] ?? null,
                                    'menu_order' => $template['menu_order'] ?? 0
                                ];

                                // Preserve other fields if they exist
                                foreach ($template as $field => $value) {
                                    if (!isset($formatted_template[$field])) {
                                        $formatted_template[$field] = $value;
                                    }
                                }

                                $formatted_templates[] = $formatted_template;
                            }
                            $templates = $formatted_templates;
                        }

                        $raw_plugins = $purchased_kit['required_plugins'] ?? [];

                        // Process the required plugins
                        $processed_plugins = [];
                        foreach ($raw_plugins as $key => $plugin) {
                            // Handle both array and string formats
                            if (is_string($plugin)) {
                                $plugin = ['name' => $plugin, 'slug' => sanitize_title($plugin)];
                            }

                            // Map 'file' to 'plugin_file' if needed
                            if (isset($plugin['file']) && !isset($plugin['plugin_file'])) {
                                $plugin['plugin_file'] = $plugin['file'];
                            }

                            // Determine plugin slug
                            $plugin_slug = $plugin['slug'] ?? sanitize_title($plugin['name'] ?? '');

                            // Check plugin installation and activation status
                            $is_installed = false;
                            $is_active = false;

                            // Check if it's a theme based on type from manifest
                            if (isset($plugin['type']) && $plugin['type'] === 'theme') {
                                $theme = wp_get_theme($plugin_slug);
                                $is_installed = $theme->exists();
                                $is_active = (get_option('stylesheet') === $plugin_slug);
                                $plugin_file = 'theme';
                            } else {
                                // Determine plugin file path
                                $plugin_file = '';

                                if (isset($plugin['plugin_file'])) {
                                    $plugin_file = $plugin['plugin_file'];
                                } elseif (isset($plugin['file'])) {
                                    $plugin_file = $plugin['file'];
                                } else {
                                    // Default pattern: slug/slug.php
                                    $plugin_file = $plugin_slug . '/' . $plugin_slug . '.php';
                                }

                                // Check if plugin is installed
                                if (!function_exists('get_plugins')) {
                                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                                }

                                $all_plugins = get_plugins();
                                $is_installed = isset($all_plugins[$plugin_file]);

                                // If not found, search through all plugins
                                if (!$is_installed) {
                                    foreach ($all_plugins as $path => $plugin_data) {
                                        if (strpos($path, $plugin_slug . '/') === 0 ||
                                            strpos($path, $plugin_slug . '.php') !== false) {
                                            $is_installed = true;
                                            $plugin_file = $path;
                                            break;
                                        }
                                    }
                                }

                                // Check if plugin is active
                                if ($is_installed) {
                                    $is_active = is_plugin_active($plugin_file);
                                }
                            }

                            $processed_plugin = [
                                'slug' => $plugin_slug,
                                'name' => $plugin['name'] ?? ucfirst(str_replace('-', ' ', $plugin_slug)),
                                'required' => $plugin['required'] ?? true,
                                'version' => $plugin['version'] ?? '',
                                'is_installed' => $is_installed,
                                'is_active' => $is_active,
                                'plugin_file' => $plugin_file,
                                'type' => (isset($plugin['type']) && $plugin['type'] === 'theme') ? 'theme' : 'plugin'
                            ];

                            $processed_plugins[] = $processed_plugin;
                        }

                        $required_plugins = $processed_plugins;
                    }
                }
            }
        }

        // If not a purchased kit or failed to get purchased templates, try API/cache
        if ($templates === false || empty($templates)) {
            $templates = $this->get_cached_kit_templates($kit_id, $kit_category, $force_refresh);

            // Get required plugins from manifest for API kits
            if (empty($required_plugins)) {
                $required_plugins = $this->get_kit_required_plugins($kit_id);
            }
        }

        if ($templates === false || empty($templates)) {
            wp_send_json_error(['message' => 'Failed to load template kit']);
            return;
        }

        // Prepare response with templates and required plugins
        $response_data = [
            'templates' => $templates,
            'required_plugins' => $required_plugins
        ];

        wp_send_json_success(['data' => $response_data]);
    }

    /**
     * Check if a kit is purchased
     */
    private function is_purchased_kit($kit_id) {
        if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();
            if (method_exists($cache_manager, 'is_kit_purchased')) {
                return $cache_manager->is_kit_purchased($kit_id);
            }
        }
        return false;
    }

    /**
     * Get required plugins from kit manifest
     */
    private function get_kit_required_plugins($kit_id) {
        if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();

            // Get manifest from cache
            $manifest = $cache_manager->get_kit_manifest($kit_id);

            if ($manifest && isset($manifest['required_plugins'])) {
                // Format the required plugins data
                $required_plugins = [];
                foreach ($manifest['required_plugins'] as $plugin_slug => $plugin_data) {
                    // Check if plugin is installed and active
                    $is_installed = false;
                    $is_active = false;
                    $plugin_file = '';

                    // Check if it's a theme based on type in manifest
                    if (is_array($plugin_data) && isset($plugin_data['type']) && $plugin_data['type'] === 'theme') {
                        $theme = wp_get_theme($plugin_slug);
                        $is_installed = $theme->exists();
                        $is_active = (get_option('stylesheet') === $plugin_slug);
                        $plugin_file = 'theme';
                    } else {
                        // Check for plugin
                        if (is_array($plugin_data) && isset($plugin_data['plugin_file'])) {
                            $plugin_file = $plugin_data['plugin_file'];
                        } elseif (is_array($plugin_data) && isset($plugin_data['file'])) {
                            $plugin_file = $plugin_data['file'];
                        } else {
                            // Default pattern: slug/slug.php
                            $plugin_file = $plugin_slug . '/' . $plugin_slug . '.php';
                        }

                        // Check if plugin is installed
                        if (!function_exists('get_plugins')) {
                            require_once ABSPATH . 'wp-admin/includes/plugin.php';
                        }

                        $installed_plugins = get_plugins();
                        $is_installed = isset($installed_plugins[$plugin_file]);

                        // If not found, search through all plugins
                        if (!$is_installed) {
                            foreach ($installed_plugins as $path => $plugin_info) {
                                if (strpos($path, $plugin_slug . '/') === 0 ||
                                    strpos($path, $plugin_slug . '.php') !== false) {
                                    $is_installed = true;
                                    $plugin_file = $path;
                                    break;
                                }
                            }
                        }

                        // Check if plugin is active
                        if ($is_installed) {
                            $is_active = is_plugin_active($plugin_file);
                        }
                    }

                    // Build plugin info array
                    $plugin_info = [
                        'slug' => $plugin_slug,
                        'name' => is_array($plugin_data) ? ($plugin_data['name'] ?? ucfirst(str_replace('-', ' ', $plugin_slug))) : $plugin_data,
                        'required' => is_array($plugin_data) ? ($plugin_data['required'] ?? true) : true,
                        'version' => is_array($plugin_data) ? ($plugin_data['version'] ?? '') : '',
                        'is_installed' => $is_installed,
                        'is_active' => $is_active,
                        'plugin_file' => $plugin_file,
                        'type' => (is_array($plugin_data) && isset($plugin_data['type']) && $plugin_data['type'] === 'theme') ? 'theme' : 'plugin'
                    ];

                    // Add install/activate URLs if needed
                    if (!$is_installed && $plugin_info['type'] === 'plugin') {
                        $plugin_info['install_url'] = wp_nonce_url(
                            admin_url('update.php?action=install-plugin&plugin=' . $plugin_slug),
                            'install-plugin_' . $plugin_slug
                        );
                    } elseif ($is_installed && !$is_active && $plugin_info['type'] === 'plugin') {
                        $plugin_info['activate_url'] = wp_nonce_url(
                            admin_url('plugins.php?action=activate&plugin=' . $plugin_file),
                            'activate-plugin_' . $plugin_file
                        );
                    }

                    $required_plugins[] = $plugin_info;
                }

                return $required_plugins;
            }
        }

        // Return empty array if no manifest found
        return [];
    }

    public function jltma_get_plugins_status(){
        $valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce') ||
                       wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action');

        if (!$valid_nonce) {
            wp_send_json_error(['message' => 'Invalid nonce']);
            return;
        }

        $plugins_with_status = array();

        if( isset($_POST['required_plugins']) && !empty( $_POST['required_plugins'])){
            $plugins = json_decode(stripslashes($_POST['required_plugins']), true);

            // Include plugin.php if needed
            if (!function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $installed_plugins = get_plugins();

            foreach($plugins as $plugin){
                $plugin_file = isset($plugin['file']) ? $plugin['file'] : '';
                $plugin_path = explode('/', $plugin_file)[1];
                $plugin_slug = explode('.', $plugin_path)[0]; 

                // If no plugin_file provided, try common patterns
                if (empty($plugin_file) && !empty($plugin_slug)) {
                    $plugin_file = $plugin_slug . '/' . $plugin_slug . '.php';
                }
                
                $is_installed = false;
                $is_active = false;

                // Check if plugin is installed
                if (!empty($plugin_file) && isset($installed_plugins[$plugin_file])) {
                    $is_installed = true;
                } 

                // Check if plugin is active
                if ($is_installed) {
                    $is_active = is_plugin_active($plugin_file);
                }

                // Build the plugin data with status
                $plugin_with_status = array(
                    'slug' => $plugin_slug,
                    'name' => isset($plugin['name']) ? $plugin['name'] : ucfirst(str_replace('-', ' ', $plugin_slug)),
                    'plugin_file' => $plugin_file,
                    'is_installed' => $is_installed,
                    'is_active' => $is_active
                );

                // Add any other fields from original plugin data
                if (isset($plugin['version'])) {
                    $plugin_with_status['version'] = $plugin['version'];
                }
                if (isset($plugin['required'])) {
                    $plugin_with_status['required'] = $plugin['required'];
                }

                $plugins_with_status[] = $plugin_with_status;
            }

            wp_send_json_success(['plugins' => $plugins_with_status]);
        } else {
            wp_send_json_error(['message' => 'No plugins provided']);
        }
    }

    /**
     * Get cached kit templates with fallback to API
     */
    private function get_cached_kit_templates($kit_id, $kit_category, $force_refresh = false) {
        $upload_dir = wp_upload_dir();
        $kit_manifest_path = $upload_dir['basedir'] . '/master_addons/templates_kits/kits/' . $kit_id . '/manifest.json';

        // If local manifest exists and we're not forcing refresh, use it directly
        if (!$force_refresh && file_exists($kit_manifest_path)) {
            $manifest_content = file_get_contents($kit_manifest_path);
            $manifest = json_decode($manifest_content, true);

            if ($manifest && isset($manifest['templates'])) {
                // Process templates from local manifest
                $templates = [];
                $kit_base_url = $upload_dir['baseurl'] . '/master_addons/templates_kits/kits/' . $kit_id . '/';

                foreach ($manifest['templates'] as $template) {
                    // Build proper URLs for screenshots
                    $screenshot = $template['screenshot'] ?? '';
                    if (!empty($screenshot) && strpos($screenshot, 'http') !== 0) {
                        // It's a relative path, build full URL
                        $screenshot = $kit_base_url . ltrim($screenshot, '/');
                    }

                    $templates[] = [
                        'template_id' => $template['template_id'] ?? $template['id'] ?? '',
                        'title' => $template['name'] ?? $template['title'] ?? '',
                        'type' => $template['type'] ?? 'page',
                        'thumbnail' => $screenshot,
                        'is_pro' => $template['is_pro'] ?? false,
                        'preview_url' => $template['preview_url'] ?? '',
                        'source' => $template['source'] ?? '',
                        'menu_order' => $template['menu_order'] ?? 0
                    ];
                }

                // Return templates from local cache
                return $templates;
            }
        }

        // Try to get from cache manager if available
        if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();

            // Check if cache manager has a method for kit templates
            if (method_exists($cache_manager, 'get_cached_kit_content')) {
                $cached_data = $cache_manager->get_cached_kit_content($kit_id, $kit_category, $force_refresh);
                if ($cached_data !== false) {
                    return $cached_data;
                }
            }
        }

        // Fallback to transient cache
        $cache_key = 'jltma_kit_templates_' . $kit_category . '_' . $kit_id;
        $cache_expiry = 12 * HOUR_IN_SECONDS;

        // Check transient cache if not forcing refresh
        if (!$force_refresh) {
            $cached_templates = get_transient($cache_key);
            if ($cached_templates !== false) {
                return $cached_templates;
            }
        }

        // Only fetch from API if local files don't exist
        if (!file_exists($kit_manifest_path)) {
            // Fetch fresh data from API
            $fresh_templates = $this->fetch_kit_templates_from_api($kit_id, $kit_category);

            if ($fresh_templates !== false && !empty($fresh_templates)) {
                set_transient($cache_key, $fresh_templates, $cache_expiry);

                // Also save to file cache if manager is available
                if (isset($cache_manager) && method_exists($cache_manager, 'save_kit_content')) {
                    $cache_manager->save_kit_content($kit_id, $kit_category, $fresh_templates);
                }

                return $fresh_templates;
            }
        }

        // Return cached data even if expired as fallback
        $cached_templates = get_transient($cache_key);
        return $cached_templates !== false ? $cached_templates : false;
    }

    /**
     * Fetch kit templates from API
     */
    private function fetch_kit_templates_from_api($kit_id, $kit_category) {
        // Get API configuration
        $config = null;
        if (function_exists('MasterAddons\Inc\Templates\master_addons_templates')) {
            $templates_instance = \MasterAddons\Inc\Templates\master_addons_templates();
            if ($templates_instance && isset($templates_instance->config)) {
                $config = $templates_instance->config->get('api');
            }
        }

        if (!$config) {
            return false;
        }

        // Build API URL for template kits
        $api_url = $config['base'] . $config['path'] . '/template-kits/' . $kit_category . '/' . $kit_id;

        // Add pro_enabled parameter if pro is enabled
        if (\MasterAddons\Inc\Helper\Master_Addons_Helper::jltma_premium()) {
            $api_url = add_query_arg('pro_enabled', 'true', $api_url);
        }

        $response = wp_remote_get($api_url, [
            'timeout' => 15, // Reduced from 60 to 15 seconds
            'sslverify' => false,
            'headers' => [
                'User-Agent' => 'Master Addons Template Kit Cache/' . JLTMA_VER
            ]
        ]);

        // if (is_wp_error($response)) {
        //     // error_log('MA Template Kit API Error: ' . $response->get_error_message());
        //     return false;
        // }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // error_log('MA Template Kit JSON Error: ' . json_last_error_msg());
            return false;
        }

        if (!isset($data['success']) || !$data['success'] || !isset($data['templates'])) {
            return false;
        }

        return $data['templates'];
    }

    /**
     * AJAX: Delete purchased kit
     */
    public function delete_purchased_kit() {
        // Verify nonce and permissions
        $valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce') ||
                       wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action');

        if (!$valid_nonce || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $kit_id = isset($_POST['kit_id']) ? sanitize_text_field($_POST['kit_id']) : '';

        if (empty($kit_id)) {
            wp_send_json_error(['message' => 'Kit ID is required']);
            return;
        }

        // Get cache manager instance
        if (!class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
            wp_send_json_error(['message' => 'Cache manager not available']);
            return;
        }

        $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();

        // Check if this is actually a purchased kit
        if (!method_exists($cache_manager, 'is_kit_purchased') || !$cache_manager->is_kit_purchased($kit_id)) {
            wp_send_json_error(['message' => 'This kit is not a purchased kit or does not exist']);
            return;
        }

        // Delete the kit and its files
        $deleted = $this->delete_kit_files($kit_id);

        if ($deleted) {
            // Also remove from metadata
            if (method_exists($cache_manager, 'delete_purchased_kit')) {
                $cache_manager->delete_purchased_kit($kit_id);
            }

            wp_send_json_success([
                'message' => 'Kit deleted successfully',
                'kit_id' => $kit_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to delete kit files']);
        }
    }

    /**
     * Delete kit files from the filesystem
     */
    private function delete_kit_files($kit_id) {
        $upload_dir = wp_upload_dir();
        $kit_dir = $upload_dir['basedir'] . '/master_addons/purchased_kits/kits/kit_' . $kit_id;

        // Check if directory exists
        if (!file_exists($kit_dir)) {
            return false;
        }

        // Use WordPress filesystem
        global $wp_filesystem;
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        WP_Filesystem();

        // Delete directory recursively
        return $this->delete_directory_recursive($kit_dir);
    }

    /**
     * Recursively delete a directory and its contents
     */
    private function delete_directory_recursive($dir) {
        if (!file_exists($dir)) {
            return false;
        }

        // Get all files and directories
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                // Recursively delete subdirectories
                $this->delete_directory_recursive($path);
            } else {
                // Delete file
                @unlink($path);
            }
        }

        // Delete the empty directory
        return @rmdir($dir);
    }

}

// Initialize the Template Library
JLTMA_Template_Library::get_instance();