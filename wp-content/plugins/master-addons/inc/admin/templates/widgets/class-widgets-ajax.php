<?php

namespace MasterAddons\Inc\Admin\Templates\Widgets;

use MasterAddons\Inc\Classes\Helper;

defined('ABSPATH') || exit;

/**
 * AJAX endpoints backing the Widgets Library screen.
 */
class Widgets_Ajax
{
    const NONCE_ACTION = 'jltma_widgets_library_nonce_action';
    const CACHE_KEY    = 'jltma_widgets_library_cache';
    const CACHE_TTL    = 12 * HOUR_IN_SECONDS;
    const PER_PAGE     = 12;

    private static $_instance = null;

    public function __construct()
    {
        add_action('wp_ajax_jltma_get_widgets_library', [$this, 'get_widgets']);
        add_action('wp_ajax_jltma_get_widget_categories', [$this, 'get_categories']);
        add_action('wp_ajax_jltma_download_widget', [$this, 'download_widget']);
        add_action('wp_ajax_jltma_refresh_widgets_cache', [$this, 'refresh_cache']);
    }

    /**
     * Shared guard. Dies with a JSON error when the request is not trusted.
     */
    private function guard()
    {
        $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- collecting nonce for immediate verification

        if (!wp_verify_nonce($nonce, self::NONCE_ACTION) || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'master-addons')]);
        }
    }

    /**
     * Base URL of the remote catalog, reusing the Template Library config.
     */
    private function api_base()
    {
        $config = null;

        if (function_exists('MasterAddons\\Inc\\Admin\\Templates\\master_addons_templates')) {
            $templates = \MasterAddons\Inc\Admin\Templates\master_addons_templates();
            if ($templates && isset($templates->config)) {
                $config = $templates->config->get('api');
            }
        }

        if (empty($config['base']) || empty($config['path'])) {
            return '';
        }

        /**
         * Filter the Widgets Library API base URL.
         *
         * Lets a staging or development site point at a non-production
         * catalog without editing the shared template config.
         *
         * @param string $base Fully qualified base, no trailing slash.
         */
        return apply_filters('jltma_widgets_library_api_base', $config['base'] . $config['path']);
    }

    /**
     * Fetch the catalog, cached in a transient.
     *
     * @param bool $force_refresh
     * @return array|\WP_Error
     */
    private function fetch_catalog($force_refresh = false)
    {
        // File cache first, so the catalog and its thumbnails live under
        // uploads/master_addons/templates-library alongside the templates and
        // survive a transient flush. Falls through to the remote fetch below
        // when the class is unavailable or the hub could not be reached.
        $file_cached = $this->file_cache_catalog($force_refresh);
        if (is_array($file_cached)) {
            return $file_cached;
        }

        if (!$force_refresh) {
            $cached = get_transient(self::CACHE_KEY);
            if (is_array($cached)) {
                return $cached;
            }
        }

        $base = $this->api_base();
        if (!$base) {
            return new \WP_Error('no_config', __('Template API is not configured.', 'master-addons'));
        }

        // The catalog endpoint is answered from a static file on the server that
        // is rebuilt on a schedule, so a plain re-request after clearing the
        // local cache returns the same stale listing. Any query parameter makes
        // the server answer live, so an explicit refresh sends one and routine
        // reads keep hitting the static file.
        $url = $base . '/widgets';
        if ($force_refresh) {
            $url = add_query_arg('force_refresh', '1', $url);
        }

        $response = wp_remote_get($url, [
            'timeout'   => 15,
            'sslverify' => false,
            'headers'   => ['User-Agent' => 'Master Addons Widgets Library/' . JLTMA_VER],
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($body['success']) || !isset($body['widgets'])) {
            return new \WP_Error('bad_response', __('Could not read the widgets library.', 'master-addons'));
        }

        set_transient(self::CACHE_KEY, $body['widgets'], self::CACHE_TTL);

        return $body['widgets'];
    }

    /**
     * Catalog from the shared templates-library file cache.
     *
     * @param bool $force_refresh
     * @return array|false
     */
    private function file_cache_catalog($force_refresh)
    {
        if (!class_exists('\\MasterAddons\\Inc\\Classes\\Template_Library_Cache')) {
            return false;
        }

        $cache = \MasterAddons\Inc\Classes\Template_Library_Cache::get_instance();

        if (!method_exists($cache, 'get_cached_widgets')) {
            return false;
        }

        return $cache->get_cached_widgets($force_refresh);
    }

    public function get_widgets()
    {
        $this->guard();

        $category = isset($_POST['category']) ? sanitize_text_field(wp_unslash($_POST['category'])) : 'all';
        $search   = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';
        $page     = isset($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
        $force    = isset($_POST['force_refresh']) && 'true' === $_POST['force_refresh'];

        $widgets = $this->fetch_catalog($force);

        if (is_wp_error($widgets)) {
            wp_send_json_error(['message' => $widgets->get_error_message()]);
        }

        if ('all' !== $category) {
            $widgets = array_values(array_filter($widgets, function ($widget) use ($category) {
                return !empty($widget['categories']) && in_array($category, $widget['categories'], true);
            }));
        }

        if ('' !== $search) {
            $needle  = strtolower($search);
            $widgets = array_values(array_filter($widgets, function ($widget) use ($needle) {
                $haystack = strtolower(
                    $widget['title'] . ' ' .
                    implode(' ', (array) ($widget['keywords'] ?? [])) . ' ' .
                    implode(' ', (array) ($widget['categories'] ?? []))
                );
                return false !== strpos($haystack, $needle);
            }));
        }

        $total  = count($widgets);
        $offset = ($page - 1) * self::PER_PAGE;

        // The grid reuses the Template Library shape, so each item is exposed
        // under the keys that component already reads.
        $templates = array_map(function ($widget) {
            return [
                'template_id' => $widget['widget_id'],
                'title'       => $widget['title'],
                'thumbnail'   => $widget['thumbnail'],
                'preview'     => $widget['preview'],
                // Catalog entries are Elementor documents, so this is the live
                // preview permalink when one has been laid out. Older payloads
                // without the key fall back to the thumbnail.
                'preview_url' => !empty($widget['preview_url']) ? $widget['preview_url'] : $widget['preview'],
                'url'         => $widget['url'],
                'icon'        => $widget['icon'],
                'is_pro'      => !empty($widget['pro']),
                'purchasable' => false,
                'categories'  => $widget['categories'],
                'keywords'    => $widget['keywords'],
                'notice'      => $widget['notice'],
            ];
        }, array_slice($widgets, $offset, self::PER_PAGE));

        wp_send_json_success([
            'templates'  => $templates,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => self::PER_PAGE,
                'total_items'  => $total,
                'total_pages'  => (int) ceil($total / self::PER_PAGE),
                'has_more'     => ($offset + self::PER_PAGE) < $total,
            ],
        ]);
    }

    public function get_categories()
    {
        $this->guard();

        $widgets = $this->fetch_catalog(false);

        if (is_wp_error($widgets)) {
            wp_send_json_success([]);
        }

        // Counts are derived from the widgets actually on offer, not from the
        // remote taxonomy — stale terms with no live widget never show up.
        $counts = [];
        foreach ($widgets as $widget) {
            foreach ((array) ($widget['categories'] ?? []) as $slug) {
                $counts[$slug] = isset($counts[$slug]) ? $counts[$slug] + 1 : 1;
            }
        }

        // id/name are what the shared CategorySidebar reads; slug/title are kept
        // so the shape stays readable on its own terms.
        $categories = [[
            'id'    => 'all',
            'slug'  => 'all',
            'name'  => __('ALL', 'master-addons'),
            'title' => __('ALL', 'master-addons'),
            'count' => count($widgets),
        ]];

        foreach ($counts as $slug => $count) {
            $label = ucwords(str_replace('-', ' ', $slug));

            $categories[] = [
                'id'    => $slug,
                'slug'  => $slug,
                'name'  => $label,
                'title' => $label,
                'count' => $count,
            ];
        }

        wp_send_json_success($categories);
    }

    public function download_widget()
    {
        $this->guard();

        $widget_id = isset($_POST['template_id']) ? absint($_POST['template_id']) : 0;

        if (!$widget_id) {
            wp_send_json_error(['message' => __('No widget specified.', 'master-addons')]);
        }

        $base = $this->api_base();
        if (!$base) {
            wp_send_json_error(['message' => __('Template API is not configured.', 'master-addons')]);
        }

        $response = wp_remote_get($base . '/widget/' . $widget_id, [
            'timeout'   => 30,
            'sslverify' => false,
            'headers'   => ['User-Agent' => 'Master Addons Widgets Library/' . JLTMA_VER],
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => $response->get_error_message()]);
        }

        if (404 === wp_remote_retrieve_response_code($response)) {
            wp_send_json_error(['message' => __('This widget is no longer available.', 'master-addons')]);
        }

        $payload = json_decode(wp_remote_retrieve_body($response), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => __('Could not read the widget from the server.', 'master-addons')]);
        }

        // Defense in depth: the grid already swaps in an Upgrade button.
        if (!empty($payload['pro']) && !Helper::jltma_premium()) {
            wp_send_json_error([
                'message'     => __('This widget requires Master Addons PRO.', 'master-addons'),
                'upgrade_url' => 'https://master-addons.com/pricing',
            ], 403);
        }

        $post_id = Widgets_Downloader::install($payload);

        if (is_wp_error($post_id)) {
            wp_send_json_error(['message' => $post_id->get_error_message()]);
        }

        wp_send_json_success([
            'message'   => __('Widget imported successfully!', 'master-addons'),
            'widget_id' => $post_id,
            // A jltma_widget is edited in the Widget Builder, not the classic
            // post editor — post.php would open a screen with none of the
            // widget's markup, styles or controls on it. This is the same URL
            // the CPT list table substitutes for its own Edit row action.
            'edit_url'  => admin_url('admin.php?page=jltma-widget-editor&widget_id=' . $post_id),
        ]);
    }

    public function refresh_cache()
    {
        $this->guard();

        delete_transient(self::CACHE_KEY);
        $widgets = $this->fetch_catalog(true);

        if (is_wp_error($widgets)) {
            wp_send_json_error(['message' => $widgets->get_error_message()]);
        }

        wp_send_json_success(['count' => count($widgets)]);
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
