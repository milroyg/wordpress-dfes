<?php

namespace MasterAddons\Inc\Admin\Templates\Kits;

use MasterAddons\Inc\Classes\Helper;

defined('ABSPATH') || exit;

/**
 * Template Kits AJAX Handlers
 */
class Ajax_Handlers
{
    private static $_instance = null;

    public function __construct()
    {
        // Register AJAX actions
        add_action('wp_ajax_jltma_get_kit_templates_unified', [$this, 'get_kit_templates_unified']);
        add_action('wp_ajax_jltma_import_single_template', [$this, 'import_single_template_kit']);
        add_action('wp_ajax_jltma_refresh_template_kit_cache', [$this, 'refresh_template_kit_cache']);
        add_action('wp_ajax_jltma_cache_kit_images', [$this, 'cache_kit_images']);
        add_action('wp_ajax_jltma_get_kit_manifest', [$this, 'get_kit_manifest']);
        add_action('wp_ajax_jltma_update_purchased_kits_urls', [$this, 'update_purchased_kits_urls']);
    }

    /**
     * AJAX Handler for getting template kits data for the unified interface
     */
    public function get_kit_templates_unified()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing -- collecting nonce for immediate verification
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        // Get parameters
        $category = isset($_POST['category']) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : 'all';
        $search = isset($_POST['search']) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
        $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
        $per_page = 12;

        $force_refresh = isset($_POST['force_refresh']) && $_POST['force_refresh'] === 'true';

        // Use Template Kit Cache
        if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();

            // Check if the get_purchased_kits method exists
            $purchased_kits = [];
            if (method_exists($cache_manager, 'get_purchased_kits')) {
                static $purchased_kits_cache = null;
                static $manifests_cache = [];
                static $manifests_fully_loaded = false;

                if ($purchased_kits_cache === null) {
                    $purchased_kits_cache = $cache_manager->get_purchased_kits();

                    if (!empty($purchased_kits_cache) && !$manifests_fully_loaded) {
                        foreach ($purchased_kits_cache as $pk) {
                            $pk_id = $pk['kit_id'] ?? '';
                            if ($pk_id && !isset($manifests_cache[$pk_id])) {
                                if (isset($pk['manifest']) && is_array($pk['manifest'])) {
                                    $manifests_cache[$pk_id] = $pk['manifest'];
                                } else {
                                    $manifest = $cache_manager->get_kit_manifest($pk_id);
                                    if ($manifest) {
                                        $manifests_cache[$pk_id] = $manifest;
                                    }
                                }
                            }
                        }
                        $manifests_fully_loaded = true;
                    }
                }
                $purchased_kits = $purchased_kits_cache;
            }

            if (method_exists($cache_manager, 'get_category_kits')) {
                $cached_kits = $cache_manager->get_category_kits($category, $force_refresh);
            } else {
                $cached_kits = $cache_manager->get_cached_kits($force_refresh, $category);
            }

            $templates = [];
            $purchased_kit_ids = [];

            if ($cached_kits !== null) {
                // Process purchased kits first
                if (!empty($purchased_kits)) {
                    foreach ($purchased_kits as $purchased_kit) {
                        $kit_id = $purchased_kit['kit_id'] ?? '';
                        if ($kit_id) {
                            $purchased_kit_ids[] = $kit_id;
                        }

                        if ($category !== 'all') {
                            $kit_categories = $purchased_kit['categories'] ?? ['purchased'];
                            if (!in_array($category, $kit_categories) && $category !== 'purchased') {
                                continue;
                            }
                        }

                        $manifest = $manifests_cache[$kit_id] ?? null;
                        $required_plugins = [];

                        if ($manifest && isset($manifest['required_plugins'])) {
                            $required_plugins = $manifest['required_plugins'];
                        } elseif (isset($purchased_kit['required_plugins'])) {
                            $required_plugins = $purchased_kit['required_plugins'];
                        } elseif (isset($purchased_kit['manifest']['required_plugins'])) {
                            $required_plugins = $purchased_kit['manifest']['required_plugins'];
                        }

                        $template = [
                            'kit_id' => $kit_id,
                            'kit_name' => $purchased_kit['kit_name'] ?? $purchased_kit['title'] ?? '',
                            'thumbnail' => $purchased_kit['thumbnail'] ?? '',
                            'preview_url' => $purchased_kit['preview_url'] ?? '',
                            'is_pro' => false,
                            'downloadable' => true,
                            'purchasable' => false,
                            'is_purchased' => true,
                            'purchase_url' => $purchased_kit['purchase_url'] ?? '',
                            'downloads' => $purchased_kit['downloads'] ?? 0,
                            'descriptions' => $purchased_kit['descriptions'] ?? $purchased_kit['description'] ?? '',
                            'categories' => $purchased_kit['categories'] ?? ['purchased'],
                            'keywords' => $purchased_kit['keywords'] ?? [],
                            'purchased_date' => $purchased_kit['purchased_date'] ?? '',
                            'required_plugins' => $required_plugins,
                            'manifest' => $manifest
                        ];

                        if (!empty($search)) {
                            $searchable = strtolower($template['kit_name'] . ' ' . $template['descriptions'] . ' ' . implode(' ', $template['keywords']));
                            if (stripos($searchable, strtolower($search)) === false) {
                                continue;
                            }
                        }

                        $templates[] = $template;
                    }
                }

                // Process cached kits
                if ($cached_kits) {
                    $all_categories = [];
                    $kits_to_process = [];

                    if ($category === 'all' && is_array($cached_kits)) {
                        foreach ($cached_kits as $cat_key => $cat_kits) {
                            if (is_array($cat_kits)) {
                                $kits_to_process = array_merge($kits_to_process, $cat_kits);
                            }
                        }
                    } else {
                        $kits_to_process = is_array($cached_kits) ? $cached_kits : [];
                    }

                    foreach ($kits_to_process as $kit) {
                        $current_kit_id = $kit['kit_id'] ?? $kit['id'] ?? '';
                        if (in_array($current_kit_id, $purchased_kit_ids)) {
                            continue;
                        }

                        if (isset($kit['categories']) && is_string($kit['categories'])) {
                            $kit['categories'] = [$kit['categories']];
                        }

                        $template = [
                            'kit_id' => $kit['kit_id'] ?? $kit['id'] ?? '',
                            'kit_name' => $kit['kit_name'] ?? $kit['name'] ?? '',
                            'thumbnail' => $kit['thumbnail'] ?? '',
                            'preview_url' => $kit['preview_url'] ?? $kit['preview'] ?? '',
                            'is_pro' => $kit['is_pro'] ?? false,
                            'downloadable' => $kit['downloadable'] ?? true,
                            'purchasable' => $kit['purchasable'] ?? false,
                            'is_purchased' => false,
                            'purchase_url' => $kit['purchase_url'] ?? '',
                            'downloads' => $kit['downloads'] ?? 0,
                            'descriptions' => $kit['descriptions'] ?? $kit['description'] ?? '',
                            'categories' => $kit['categories'] ?? ['all'],
                            'keywords' => $kit['keywords'] ?? [],
                        ];

                        if (!empty($search)) {
                            $searchable = strtolower($template['kit_name'] . ' ' . $template['descriptions'] . ' ' . implode(' ', $template['keywords']));
                            if (stripos($searchable, strtolower($search)) === false) {
                                continue;
                            }
                        }

                        $templates[] = $template;

                        foreach ($template['categories'] as $cat) {
                            if (!isset($all_categories[$cat])) {
                                $all_categories[$cat] = 0;
                            }
                            $all_categories[$cat]++;
                        }
                    }
                }
            }

            // Apply pagination
            $total_templates = count($templates);
            $offset = ($page - 1) * $per_page;
            $paginated_templates = array_slice($templates, $offset, $per_page);

            $response = [
                'templates' => $paginated_templates,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total_templates / $per_page),
                    'total_items' => $total_templates,
                    'has_more' => ($offset + $per_page) < $total_templates
                ]
            ];

            wp_send_json_success($response);
        } else {
            wp_send_json_error(['message' => 'Template Kit cache not available']);
        }
    }

    /**
     * Import Single Template from Kit
     */
    public function import_single_template_kit()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'master_addons_nonce') || !current_user_can('import')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing -- collecting nonce for immediate verification
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $kit_id = isset($_POST['kit_id']) ? sanitize_text_field( wp_unslash( $_POST['kit_id'] ) ) : '';
        $template_slug = isset($_POST['template_slug']) ? sanitize_text_field( wp_unslash( $_POST['template_slug'] ) ) : '';
        $template_title = isset($_POST['template_title']) ? sanitize_text_field( wp_unslash( $_POST['template_title'] ) ) : '';

        if (empty($kit_id) || empty($template_slug)) {
            wp_send_json_error(['message' => 'Kit ID and template slug are required']);
            return;
        }

        $cache_manager = null;
        if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
            $cache_manager->cache_kit_all_images($kit_id);
        }

        $page_id = Importer::get_instance()->import_single_template_from_api($kit_id, $template_slug);

        if ($page_id) {
            if (!empty($template_title)) {
                wp_update_post([
                    'ID' => $page_id,
                    'post_title' => $template_title
                ]);
            }

            wp_send_json_success([
                'message' => 'Template imported successfully',
                'page_id' => $page_id,
                'edit_url' => admin_url('post.php?post=' . $page_id . '&action=elementor')
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to import template']);
        }
    }

    /**
     * AJAX handler for Template Kit cache refresh
     */
    public function refresh_template_kit_cache()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing -- collecting nonce for immediate verification
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        if (class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
            $refreshed_data = $cache_manager->refresh_cache();
            $stats = $cache_manager->get_cache_stats();

            wp_send_json_success([
                'message' => 'Template Kit cache refreshed successfully from API',
                'total_kits' => $stats['total_kits'],
                'total_templates' => $stats['total_templates'],
                'refreshed_data' => $refreshed_data,
                'cache_size' => $stats['cache_size'],
                'last_update' => wp_date('Y-m-d H:i:s', $stats['last_update'] ?? time())
            ]);
        } else {
            wp_send_json_error(['message' => 'Cache manager not available']);
        }
    }

    /**
     * AJAX handler for caching kit images
     */
    public function cache_kit_images()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing -- collecting nonce for immediate verification
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $kit_id = isset($_POST['kit_id']) ? sanitize_text_field( wp_unslash( $_POST['kit_id'] ) ) : '';
        $kit_category = isset($_POST['kit_category']) ? sanitize_text_field( wp_unslash( $_POST['kit_category'] ) ) : 'all';

        if (empty($kit_id)) {
            wp_send_json_error(['message' => 'Kit ID is required']);
            return;
        }

        if (!class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            wp_send_json_error(['message' => 'Cache manager not available']);
            return;
        }

        $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();

        $config = null;
        if (function_exists('MasterAddons\Inc\Admin\Templates\master_addons_templates')) {
            $templates_instance = \MasterAddons\Inc\Admin\Templates\master_addons_templates();
            if ($templates_instance && isset($templates_instance->config)) {
                $config = $templates_instance->config->get('api');
            }
        }

        if (!$config) {
            wp_send_json_error(['message' => 'API configuration not available']);
            return;
        }

        $api_url = $config['base'] . $config['path'] . '/template-kits/' . $kit_id . '/templates';

        if (Helper::jltma_premium()) {
            $api_url = add_query_arg('pro_enabled', 'true', $api_url);
        }

        $response = wp_remote_get($api_url, [
            'timeout' => 60,
            'sslverify' => false,
            'headers' => [
                'User-Agent' => 'Master Addons Template Kit Image Cache/' . \JLTMA_VER
            ]
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Failed to fetch kit templates: ' . $response->get_error_message()]);
            return;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || !isset($data['templates'])) {
            wp_send_json_error(['message' => 'Invalid response from API']);
            return;
        }

        $cache_manager->save_kit_content($kit_id, $kit_category, $data['templates']);
        $image_count = $cache_manager->cache_kit_all_images($kit_id, $data['templates']);

        wp_send_json_success([
            'message' => sprintf('Successfully cached %d images for kit %s', $image_count, $kit_id),
            'image_count' => $image_count,
            'kit_id' => $kit_id
        ]);
    }

    /**
     * AJAX handler for getting kit manifest with required plugins
     */
    public function get_kit_manifest()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing -- collecting nonce for immediate verification
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $kit_id = isset($_POST['kit_id']) ? sanitize_text_field( wp_unslash( $_POST['kit_id'] ) ) : '';

        if (empty($kit_id)) {
            wp_send_json_error(['message' => 'Kit ID is required']);
            return;
        }

        if (!class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            wp_send_json_error(['message' => 'Cache manager not available']);
            return;
        }

        $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();
        $manifest = $cache_manager->get_kit_manifest($kit_id);

        if ($manifest) {
            if (!isset($manifest['required_plugins']) && !isset($manifest['requirements'])) {
                $manifest['required_plugins'] = [];
            }

            wp_send_json_success([
                'manifest' => $manifest,
                'kit_id' => $kit_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Manifest not found for kit']);
        }
    }

    /**
     * AJAX handler for updating existing purchased kits to remove remote URLs
     */
    public function update_purchased_kits_urls()
    {
        if (!wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing -- collecting nonce for immediate verification
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        if (!class_exists('MasterAddons\Inc\Classes\Template_Kit_Cache')) {
            wp_send_json_error(['message' => 'Cache manager not available']);
            return;
        }

        $cache_manager = \MasterAddons\Inc\Classes\Template_Kit_Cache::get_instance();

        if (method_exists($cache_manager, 'update_existing_purchased_kits_urls')) {
            $updated_count = $cache_manager->update_existing_purchased_kits_urls();

            wp_send_json_success([
                'message' => sprintf('Successfully updated %d kit(s) to use local URLs', $updated_count),
                'updated_count' => $updated_count
            ]);
        } else {
            wp_send_json_error(['message' => 'This feature is not available with the current cache manager']);
        }
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
