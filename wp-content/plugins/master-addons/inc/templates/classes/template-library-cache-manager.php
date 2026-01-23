<?php

namespace MasterAddons\Inc\Templates\Classes;

use MasterAddons\Inc\Templates;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced Template Import Cache Manager
 * Provides file-based caching with scheduled updates for template import functionality
 */
class Master_Addons_Templates_Cache_Manager
{

    private static $instance = null;
    private $cache_dir;
    private $cache_expiry;
    private $config;

    public function __construct()
    {
        $this->cache_dir = wp_upload_dir()['basedir'] . '/master_addons/templates-library/';
        $this->cache_expiry = apply_filters('jltma_cache_expiry', 6 * HOUR_IN_SECONDS); // 6 hours default, filterable

        // Initialize config safely
        add_action('init', [$this, 'init_config'], 20);
        add_action('init', [$this, 'init'], 25);
    }

    public function init_config()
    {
        // Initialize config after templates system is ready
        if (function_exists('MasterAddons\Inc\Templates\master_addons_templates')) {
            $templates_instance = Templates\master_addons_templates();
            if ($templates_instance && isset($templates_instance->config)) {
                $this->config = $templates_instance->config->get('api');
            }
        }

    }

    public function init()
    {
        // Clean up old incorrect template-kits folder if it exists
        $this->cleanup_incorrect_folders();
        
        // Ensure cache directory exists
        $this->ensure_cache_directory();

        // Schedule cache updates
        add_action('wp', [$this, 'schedule_cache_updates']);
        add_action('jltma_templates_cache_update', [$this, 'update_templates_cache']);

        // Admin hooks
        add_action('admin_init', [$this, 'maybe_clear_cache']);

        // Performance optimizations
        add_action('wp_ajax_jltma_preload_cache', [$this, 'preload_cache_ajax']);
        add_action('jltma_templates_preload_cache', [$this, 'preload_popular_templates']);

        // Extend existing cache methods
        add_filter('jltma_templates_cache_enabled', '__return_true');
    }

    /**
     * Ensure cache directory exists with proper structure
     */
    private function ensure_cache_directory()
    {
        // Check if uploads directory is writable
        if (!$this->is_uploads_writable()) {
            return false;
        }

        if (!file_exists($this->cache_dir)) {
            if (!wp_mkdir_p($this->cache_dir)) {
                return false;
            }

            // Create subdirectories for different template types
            // Removed 'template-kits' as it should be in its own separate folder
            $subdirs = ['master_section', 'master_pages', 'master_popups', 'master_headers', 'master_footers'];
            foreach ($subdirs as $subdir) {
                wp_mkdir_p($this->cache_dir . $subdir . '/');
                wp_mkdir_p($this->cache_dir . $subdir . '/categories/');
                wp_mkdir_p($this->cache_dir . $subdir . '/keywords/');
                wp_mkdir_p($this->cache_dir . $subdir . '/templates/');
                wp_mkdir_p($this->cache_dir . $subdir . '/images/');
            }

            // Create .htaccess for security
            $htaccess_content = "Options -Indexes\n<Files \"*.json\">\nOrder allow,deny\nAllow from all\n</Files>";
            file_put_contents($this->cache_dir . '.htaccess', $htaccess_content);

            // Create index.php files
            $index_content = "<?php\n// Silence is golden.\n";
            file_put_contents($this->cache_dir . 'index.php', $index_content);

            foreach ($subdirs as $subdir) {
                file_put_contents($this->cache_dir . $subdir . '/index.php', $index_content);
            }
        }

        return true;
    }

    /**
     * Clean up incorrect folders created in wrong location
     */
    private function cleanup_incorrect_folders()
    {
        // Remove template-kits folder from templates-library if it exists
        $incorrect_folder = $this->cache_dir . 'template-kits/';
        if (file_exists($incorrect_folder)) {
            $this->delete_directory_recursively($incorrect_folder);
        }
        
        // Also check for any variations that might have been created
        $variations = ['template_kits', 'templatekits', 'template-kit'];
        foreach ($variations as $variant) {
            $incorrect_variant = $this->cache_dir . $variant . '/';
            if (file_exists($incorrect_variant)) {
                $this->delete_directory_recursively($incorrect_variant);
            }
        }
    }
    
    /**
     * Delete a directory and all its contents recursively
     */
    private function delete_directory_recursively($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        if (!is_dir($dir)) {
            return;
        }

        // Normalize the directory path to avoid double slashes
        $dir = rtrim($dir, '/\\');

        // Try to scan the directory, but handle failures gracefully
        $scan_result = @scandir($dir);
        if ($scan_result === false) {
            // If we can't scan it, try to remove it directly
            @rmdir($dir);
            return;
        }

        $files = array_diff($scan_result, array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->delete_directory_recursively($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    /**
     * Check if uploads directory is writable
     */
    private function is_uploads_writable()
    {
        $upload_dir = wp_upload_dir();

        // Check if uploads dir exists and is writable
        if (!file_exists($upload_dir['basedir'])) {
            return false;
        }

        return is_writable($upload_dir['basedir']);
    }

    /**
     * Schedule cache update events
     */
    public function schedule_cache_updates()
    {
        if (!wp_next_scheduled('jltma_templates_cache_update')) {
            // Schedule twice daily (every 12 hours)
            wp_schedule_event(time(), 'twicedaily', 'jltma_templates_cache_update');
        }
    }

    /**
     * Get cached templates for specific tab
     */
    public function get_cached_templates($tab, $force_refresh = false)
    {
        // Try transient cache first if file cache is not available
        if (!$this->is_file_cache_available()) {
            return $this->get_transient_cached_templates($tab, $force_refresh);
        }

        $cache_file = $this->cache_dir . "{$tab}/templates/templates.json";
        $cache_meta_file = $this->cache_dir . "{$tab}/templates/meta.json";

        // Check if cache exists and is valid
        if (!$force_refresh && $this->is_cache_valid($cache_meta_file)) {
            $cached_data = $this->read_cache_file($cache_file);
            if ($cached_data !== false) {
                // Update thumbnail URLs to use cache folder first
                foreach ($cached_data as &$template) {
                    $cached_thumbnail = $this->get_kit_thumbnail_url('', $template['title'], $template['thumbnail']);
                    if ($cached_thumbnail) {
                        $template['thumbnail'] = $cached_thumbnail;
                    }
                }
                return $cached_data;
            }
        }

        // Fetch fresh data from remote API
        $fresh_data = $this->fetch_remote_templates($tab);

        if ($fresh_data !== false) {
            // Update thumbnail URLs to use cache folder first
            foreach ($fresh_data as &$template) {
                $cached_thumbnail = $this->get_kit_thumbnail_url('', $template['title'], $template['thumbnail']);
                if ($cached_thumbnail) {
                    $template['thumbnail'] = $cached_thumbnail;
                }
            }

            // Cache the data
            $this->write_cache_file($cache_file, $fresh_data);
            $this->write_cache_meta($cache_meta_file);

            // Cache individual template thumbnails
            $this->cache_template_images($fresh_data, $tab);

            return $fresh_data;
        }

        // Fallback to expired cache if available
        $fallback_data = $this->read_cache_file($cache_file);
        if ($fallback_data !== false) {
            // Update thumbnail URLs to use cache folder first for fallback data
            foreach ($fallback_data as &$template) {
                $cached_thumbnail = $this->get_kit_thumbnail_url('', $template['title'], $template['thumbnail']);
                if ($cached_thumbnail) {
                    $template['thumbnail'] = $cached_thumbnail;
                }
            }
        }
        return $fallback_data;
    }

    /**
     * Get cached categories for specific tab
     */
    public function get_cached_categories($tab, $force_refresh = false)
    {
        // Try transient cache first if file cache is not available
        if (!$this->is_file_cache_available()) {
            return $this->get_transient_cached_categories($tab, $force_refresh);
        }

        $cache_file = $this->cache_dir . "{$tab}/categories/categories.json";
        $cache_meta_file = $this->cache_dir . "{$tab}/categories/meta.json";

        if (!$force_refresh && $this->is_cache_valid($cache_meta_file)) {
            $cached_data = $this->read_cache_file($cache_file);
            if ($cached_data !== false) {
                return $cached_data;
            }
        }

        $fresh_data = $this->fetch_remote_categories($tab);

        if ($fresh_data !== false) {
            $this->write_cache_file($cache_file, $fresh_data);
            $this->write_cache_meta($cache_meta_file);
            return $fresh_data;
        }

        return $this->read_cache_file($cache_file);
    }

    /**
     * Get cached keywords for specific tab
     */
    public function get_cached_keywords($tab, $force_refresh = false)
    {
        // Try transient cache first if file cache is not available
        if (!$this->is_file_cache_available()) {
            return $this->get_transient_cached_keywords($tab, $force_refresh);
        }

        $cache_file = $this->cache_dir . "{$tab}/keywords/keywords.json";
        $cache_meta_file = $this->cache_dir . "{$tab}/keywords/meta.json";

        if (!$force_refresh && $this->is_cache_valid($cache_meta_file)) {
            $cached_data = $this->read_cache_file($cache_file);
            if ($cached_data !== false) {
                return $cached_data;
            }
        }

        $fresh_data = $this->fetch_remote_keywords($tab);

        if ($fresh_data !== false) {
            $this->write_cache_file($cache_file, $fresh_data);
            $this->write_cache_meta($cache_meta_file);
            return $fresh_data;
        }

        return $this->read_cache_file($cache_file);
    }

    /**
     * Get cached individual template
     */
    public function get_cached_template($template_id, $tab, $force_refresh = false)
    {
        $cache_file = $this->cache_dir . "{$tab}/templates/template-{$template_id}.json";
        $cache_meta_file = $this->cache_dir . "{$tab}/templates/template-{$template_id}-meta.json";

        if (!$force_refresh && $this->is_cache_valid($cache_meta_file)) {
            $cached_data = $this->read_cache_file($cache_file);
            if ($cached_data !== false) {
                return $cached_data;
            }
        }

        // For individual templates, we don't cache them unless they're part of a larger fetch
        // This prevents excessive API calls for single template requests
        return false;
    }

    /**
     * Cache individual template data (called after successful API fetch)
     */
    public function cache_template_data($template_id, $tab, $data)
    {
        $cache_file = $this->cache_dir . "{$tab}/templates/template-{$template_id}.json";
        $cache_meta_file = $this->cache_dir . "{$tab}/templates/template-{$template_id}-meta.json";

        $this->write_cache_file($cache_file, $data);
        $this->write_cache_meta($cache_meta_file);
    }

    /**
     * Fetch templates from remote API
     */
    private function fetch_remote_templates($tab)
    {
        $api_url = $this->config['base'] . $this->config['path'] . $this->config['endpoints']['templates'] . $tab;

        $response = wp_remote_get($api_url, [
            'timeout' => 60,
            'sslverify' => false,
            'headers' => [
                'User-Agent' => 'Master Addons Templates Cache/' . JLTMA_VER
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

        return isset($data['templates']) ? $data['templates'] : [];
    }

    /**
     * Fetch categories from remote API
     */
    private function fetch_remote_categories($tab)
    {
        $api_url = $this->config['base'] . $this->config['path'] . $this->config['endpoints']['categories'] . $tab;

        $response = wp_remote_get($api_url, [
            'timeout' => 60,
            'sslverify' => false
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['success']) || !$data['success']) {
            return false;
        }

        return isset($data['terms']) ? $data['terms'] : [];
    }

    /**
     * Fetch keywords from remote API
     */
    private function fetch_remote_keywords($tab)
    {
        $api_url = $this->config['base'] . $this->config['path'] . $this->config['endpoints']['keywords'] . $tab;

        $response = wp_remote_get($api_url, [
            'timeout' => 60,
            'sslverify' => false
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['success']) || !$data['success']) {
            return false;
        }

        return isset($data['terms']) ? $data['terms'] : [];
    }

    /**
     * Cache template images locally
     */
    private function cache_template_images($templates, $tab)
    {
        if (!is_array($templates)) {
            return;
        }

        foreach ($templates as $template) {
            if (isset($template['thumbnail']) && !empty($template['thumbnail'])) {
                $template_id = $template['template_id'] ?? uniqid();
                $this->cache_image($template['thumbnail'], $tab, "template-{$template_id}-thumb");
            }

            if (isset($template['preview']) && !empty($template['preview'])) {
                $template_id = $template['template_id'] ?? uniqid();
                $this->cache_image($template['preview'], $tab, "template-{$template_id}-preview");
            }
        }
    }

    /**
     * Cache individual image
     */
    private function cache_image($image_url, $tab, $filename)
    {
        if (empty($image_url)) {
            return false;
        }

        $extension = pathinfo($image_url, PATHINFO_EXTENSION);
        if (empty($extension)) {
            $extension = 'jpg';
        }

        $local_file = $this->cache_dir . "{$tab}/images/{$filename}.{$extension}";

        // Skip if already cached and recent
        if (file_exists($local_file) && (time() - filemtime($local_file)) < DAY_IN_SECONDS) {
            return $local_file;
        }

        // Ensure the directory exists before trying to write
        $image_dir = dirname($local_file);
        if (!file_exists($image_dir)) {
            wp_mkdir_p($image_dir);
        }

        $response = wp_remote_get($image_url, [
            'timeout' => 30
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $image_data = wp_remote_retrieve_body($response);

        if (file_put_contents($local_file, $image_data)) {
            return $local_file;
        }

        return false;
    }

    /**
     * Update templates cache (scheduled event)
     */
    public function update_templates_cache()
    {
        $template_types = ['master_section', 'master_pages', 'master_popups', 'master_headers', 'master_footers'];

        foreach ($template_types as $tab) {
            // Update templates
            $this->get_cached_templates($tab, true);

            // Update categories
            $this->get_cached_categories($tab, true);

            // Update keywords
            $this->get_cached_keywords($tab, true);
        }

        // Clean up old cache files
        $this->cleanup_old_cache();

        // Update last cache time
        set_transient('jltma_templates_last_cache_update', time(), DAY_IN_SECONDS);

    }

    /**
     * Check if cache is valid
     */
    private function is_cache_valid($meta_file)
    {
        if (!file_exists($meta_file)) {
            return false;
        }

        $meta = json_decode(file_get_contents($meta_file), true);
        if (!$meta || !isset($meta['timestamp'])) {
            return false;
        }

        return (time() - $meta['timestamp']) < $this->cache_expiry;
    }

    /**
     * Read cache file with priority tracking
     */
    private function read_cache_file($file_path)
    {
        if (!file_exists($file_path)) {
            return false;
        }

        // Track access for priority system
        $this->track_cache_access($file_path);

        $content = file_get_contents($file_path);
        if ($content === false) {
            return false;
        }

        $data = json_decode($content, true);
        return json_last_error() === JSON_ERROR_NONE ? $data : false;
    }

    /**
     * Write cache file
     */
    private function write_cache_file($file_path, $data)
    {
        $dir = dirname($file_path);
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }

        $json = wp_json_encode($data, JSON_PRETTY_PRINT);
        return file_put_contents($file_path, $json) !== false;
    }

    /**
     * Write cache metadata
     */
    private function write_cache_meta($meta_file)
    {
        $meta = [
            'timestamp' => time(),
            'version' => JLTMA_VER,
            'expiry' => $this->cache_expiry
        ];

        return file_put_contents($meta_file, wp_json_encode($meta)) !== false;
    }

    /**
     * Clear all template cache
     */
    public function clear_cache()
    {
        $cleared = false;

        // Clear file cache if available
        if (file_exists($this->cache_dir)) {
            $this->delete_directory_contents($this->cache_dir);
            $this->ensure_cache_directory();
            $cleared = true;
        }

        // Always clear transient cache
        $this->clear_transient_cache();

        // Clear related transients (legacy support)
        $template_types = ['master_section', 'master_pages', 'master_popups', 'master_headers', 'master_footers'];
        foreach ($template_types as $tab) {
            delete_transient("master_addons_templates_master-api_{$tab}");
            delete_transient("master_addons_categories_master-api_{$tab}");
            delete_transient("master_addons_keywords_master-api_{$tab}");
        }

        delete_transient('jltma_templates_last_cache_update');

        return true;
    }

    /**
     * Refresh cache by clearing and fetching fresh data from API
     */
    public function refresh_cache()
    {
        // Clear all existing cache
        $this->clear_cache();

        // Force fetch fresh data from API for all template types
        $template_types = ['master_section', 'master_pages', 'master_popups', 'master_headers', 'master_footers'];
        $refreshed_data = [];

        foreach ($template_types as $tab) {
            // Force refresh templates
            $templates = $this->get_cached_templates($tab, true);

            // Force refresh categories
            $categories = $this->get_cached_categories($tab, true);

            // Force refresh keywords
            $keywords = $this->get_cached_keywords($tab, true);

            $refreshed_data[$tab] = [
                'templates' => is_array($templates) ? count($templates) : 0,
                'categories' => is_array($categories) ? count($categories) : 0,
                'keywords' => is_array($keywords) ? count($keywords) : 0
            ];
        }

        // Update last cache refresh time
        set_transient('jltma_templates_last_cache_update', time(), DAY_IN_SECONDS);

        // Log successful refresh

        return $refreshed_data;
    }

    /**
     * Clean up old cache files with priority system
     */
    private function cleanup_old_cache()
    {
        $template_types = ['master_section', 'master_pages', 'master_popups', 'master_headers', 'master_footers'];

        // Cache priority ages (high priority files are kept longer)
        $priority_ages = [
            'high' => 14 * DAY_IN_SECONDS,   // 14 days for high priority
            'medium' => 7 * DAY_IN_SECONDS,  // 7 days for medium priority
            'low' => 3 * DAY_IN_SECONDS      // 3 days for low priority
        ];

        foreach ($template_types as $tab) {
            $tab_dir = $this->cache_dir . $tab . '/';
            if (!file_exists($tab_dir)) {
                continue;
            }

            $subdirs = ['categories', 'keywords', 'templates', 'images'];
            foreach ($subdirs as $subdir) {
                $full_dir = $tab_dir . $subdir . '/';
                if (!file_exists($full_dir)) {
                    continue;
                }

                $files = glob($full_dir . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $file_age = time() - filemtime($file);
                        $priority = $this->get_file_cache_priority($file, $tab);
                        $max_age = $priority_ages[$priority] ?? $priority_ages['low'];

                        if ($file_age > $max_age) {
                            unlink($file);
                        }
                    }
                }
            }
        }
    }

    /**
     * Determine cache file priority based on usage patterns
     */
    private function get_file_cache_priority($file_path, $tab)
    {
        $filename = basename($file_path);
        $access_count = $this->get_file_access_count($file_path);
        $recent_access = $this->get_recent_access_time($file_path);

        // High priority: Frequently accessed files (>10 times) or recently accessed (within 2 days)
        if ($access_count > 10 || (time() - $recent_access) < (2 * DAY_IN_SECONDS)) {
            return 'high';
        }

        // Medium priority: Moderately accessed files (3-10 times) or accessed within a week
        if ($access_count >= 3 || (time() - $recent_access) < (7 * DAY_IN_SECONDS)) {
            return 'medium';
        }

        // Low priority: Everything else
        return 'low';
    }

    /**
     * Get file access count from usage tracking
     */
    private function get_file_access_count($file_path)
    {
        $access_data = get_transient('jltma_cache_access_' . md5($file_path));
        return $access_data ? (int) $access_data['count'] : 0;
    }

    /**
     * Get recent access time for file
     */
    private function get_recent_access_time($file_path)
    {
        $access_data = get_transient('jltma_cache_access_' . md5($file_path));
        return $access_data ? (int) $access_data['last_access'] : filemtime($file_path);
    }

    /**
     * Track cache file access for priority system
     */
    private function track_cache_access($file_path)
    {
        $access_key = 'jltma_cache_access_' . md5($file_path);
        $access_data = get_transient($access_key) ?: ['count' => 0, 'last_access' => 0];

        $access_data['count']++;
        $access_data['last_access'] = time();

        set_transient($access_key, $access_data, 30 * DAY_IN_SECONDS);
    }

    /**
     * Delete directory contents recursively
     */
    private function delete_directory_contents($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->delete_directory_contents($file);
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }

    /**
     * Handle cache clearing from admin
     */
    public function maybe_clear_cache()
    {
        if (isset($_GET['jltma_clear_templates_cache']) &&
            isset($_GET['_wpnonce']) &&
            wp_verify_nonce($_GET['_wpnonce'], 'jltma_clear_templates_cache') &&
            current_user_can('manage_options')) {

            $this->clear_cache();

            wp_redirect(add_query_arg([
                'jltma_templates_cache_cleared' => '1'
            ], remove_query_arg(['jltma_clear_templates_cache', '_wpnonce'])));
            exit;
        }

        // Handle cache refresh from admin
        if (isset($_GET['jltma_refresh_templates_cache']) &&
            isset($_GET['_wpnonce']) &&
            wp_verify_nonce($_GET['_wpnonce'], 'jltma_refresh_templates_cache') &&
            current_user_can('manage_options')) {

            $this->refresh_cache();

            wp_redirect(add_query_arg([
                'jltma_templates_cache_refreshed' => '1'
            ], remove_query_arg(['jltma_refresh_templates_cache', '_wpnonce'])));
            exit;
        }
    }

    /**
     * Get cached image URL
     */
    public function get_cached_image_url($original_url, $tab, $filename)
    {
        $extension = pathinfo($original_url, PATHINFO_EXTENSION) ?: 'jpg';
        $local_file = $this->cache_dir . "{$tab}/images/{$filename}.{$extension}";

        if (file_exists($local_file)) {
            $upload_dir = wp_upload_dir();
            $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
            return $upload_dir['baseurl'] . $relative_path;
        }

        return $original_url;
    }
    
    /**
     * Get template kit thumbnail from cache or generate fallback URL
     */
    public function get_kit_thumbnail_url($kit_name, $template_name = 'home', $original_url = null)
    {
        // Normalize kit name for filename
        $kit_slug = sanitize_title($kit_name);
        $template_slug = sanitize_title($template_name);

        // Check cache directory first (templates-library images)
        $cache_image_dir = $this->cache_dir . 'master_section/images/';
        $cached_file_patterns = [
            "{$kit_slug}-{$template_slug}.jpg",
            "{$kit_slug}-{$template_slug}.png",
            "{$kit_slug}.jpg",
            "{$kit_slug}.png"
        ];

        foreach ($cached_file_patterns as $pattern) {
            $cached_file = $cache_image_dir . $pattern;
            if (file_exists($cached_file)) {
                $upload_dir = wp_upload_dir();
                $relative_path = str_replace($upload_dir['basedir'], '', $cached_file);
                return $upload_dir['baseurl'] . $relative_path;
            }
        }

        // If original URL provided, return it
        if ($original_url) {
            return $original_url;
        }

        // Generate expected thumbnail URL from master-addons.com
        $kit_version = $this->get_kit_version($kit_name);
        return "https://master-addons.com/templates-kit/{$kit_slug}{$kit_version}/{$template_slug}.jpg";
    }

    /**
     * Get kit version suffix for URL generation
     */
    private function get_kit_version($kit_name)
    {
        // Common version patterns for kits
        $version_patterns = [
            'business-agency' => '-v1',
            'restaurant' => '-v2',
            'portfolio' => '-v1',
            'ecommerce' => '-v3'
        ];

        $kit_slug = sanitize_title($kit_name);
        return $version_patterns[$kit_slug] ?? '-v1';
    }

    /**
     * Get cache statistics
     */
    public function get_cache_stats()
    {
        $stats = [
            'cache_dir_exists' => file_exists($this->cache_dir),
            'cache_size' => $this->get_directory_size($this->cache_dir),
            'last_update' => get_transient('jltma_templates_last_cache_update'),
            'template_types' => [],
            'next_scheduled_update' => wp_next_scheduled('jltma_templates_cache_update'),
            'total_kits' => 0,
            'total_templates' => 0
        ];

        $template_types = ['master_section', 'master_pages', 'master_popups', 'master_headers', 'master_footers'];
        $total_templates = 0;

        foreach ($template_types as $type) {
            $template_count = 0;
            $image_count = 0;

            // Count file cache if available
            if ($this->is_file_cache_available()) {
                $template_count = count(glob($this->cache_dir . "{$type}/templates/template-*.json"));
                $image_count = count(glob($this->cache_dir . "{$type}/images/*"));
            } else {
                // Count transient cache
                $cached_templates = get_transient("jltma_templates_{$type}");
                if ($cached_templates && is_array($cached_templates)) {
                    $template_count = count($cached_templates);
                }
            }

            $stats['template_types'][$type] = [
                'templates' => $template_count,
                'images' => $image_count
            ];

            $total_templates += $template_count;
        }

        // For template kits, we'll count unique kits from cached data
        $stats['total_kits'] = $this->count_cached_kits();
        $stats['total_templates'] = $total_templates;

        return $stats;
    }

    /**
     * Count cached template kits
     */
    private function count_cached_kits()
    {
        $kit_count = 0;

        // If using file cache, look for kit manifest files
        if ($this->is_file_cache_available()) {
            $template_types = ['master_section', 'master_pages', 'master_popups', 'master_headers', 'master_footers'];
            foreach ($template_types as $type) {
                $templates_file = $this->cache_dir . "{$type}/templates/templates.json";
                if (file_exists($templates_file)) {
                    $templates_data = $this->read_cache_file($templates_file);
                    if ($templates_data && is_array($templates_data)) {
                        $kit_count += count($templates_data);
                    }
                }
            }
        } else {
            // Count from transients
            $template_types = ['master_section', 'master_pages', 'master_popups', 'master_headers', 'master_footers'];
            foreach ($template_types as $type) {
                $cached_templates = get_transient("jltma_templates_{$type}");
                if ($cached_templates && is_array($cached_templates)) {
                    $kit_count += count($cached_templates);
                }
            }
        }

        return $kit_count;
    }

    /**
     * Get directory size in bytes
     */
    private function get_directory_size($dir)
    {
        $size = 0;
        if (file_exists($dir)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        return $size;
    }

    /**
     * Check if file cache is available
     */
    private function is_file_cache_available()
    {
        return file_exists($this->cache_dir) && is_writable($this->cache_dir);
    }

    /**
     * Get cached templates using transients (fallback method)
     */
    private function get_transient_cached_templates($tab, $force_refresh = false)
    {
        $transient_key = "jltma_templates_{$tab}";
        $meta_transient_key = "jltma_templates_{$tab}_meta";

        // Check if cache exists and is valid
        if (!$force_refresh) {
            $cached_meta = get_transient($meta_transient_key);
            if ($cached_meta && (time() - $cached_meta['timestamp']) < $this->cache_expiry) {
                $cached_data = get_transient($transient_key);
                if ($cached_data !== false) {
                    // Update thumbnail URLs to use cache folder first for transient cached templates
                    foreach ($cached_data as &$template) {
                        $cached_thumbnail = $this->get_kit_thumbnail_url('', $template['title'], $template['thumbnail']);
                        if ($cached_thumbnail) {
                            $template['thumbnail'] = $cached_thumbnail;
                        }
                    }
                    return $cached_data;
                }
            }
        }

        // Fetch fresh data from remote API
        $fresh_data = $this->fetch_remote_templates($tab);

        if ($fresh_data !== false) {
            // Update thumbnail URLs to use cache folder first for fresh transient templates
            foreach ($fresh_data as &$template) {
                $cached_thumbnail = $this->get_kit_thumbnail_url('', $template['title'], $template['thumbnail']);
                if ($cached_thumbnail) {
                    $template['thumbnail'] = $cached_thumbnail;
                }
            }

            // Cache the data using transients
            set_transient($transient_key, $fresh_data, $this->cache_expiry);
            set_transient($meta_transient_key, ['timestamp' => time()], $this->cache_expiry);

            return $fresh_data;
        }

        // Return cached data even if expired
        $fallback_transient_data = get_transient($transient_key);
        if ($fallback_transient_data !== false) {
            // Update thumbnail URLs to use cache folder first for expired transient templates
            foreach ($fallback_transient_data as &$template) {
                $cached_thumbnail = $this->get_kit_thumbnail_url('', $template['title'], $template['thumbnail']);
                if ($cached_thumbnail) {
                    $template['thumbnail'] = $cached_thumbnail;
                }
            }
        }
        return $fallback_transient_data;
    }

    /**
     * Get cached categories using transients (fallback method)
     */
    private function get_transient_cached_categories($tab, $force_refresh = false)
    {
        $transient_key = "jltma_categories_{$tab}";
        $meta_transient_key = "jltma_categories_{$tab}_meta";

        if (!$force_refresh) {
            $cached_meta = get_transient($meta_transient_key);
            if ($cached_meta && (time() - $cached_meta['timestamp']) < $this->cache_expiry) {
                $cached_data = get_transient($transient_key);
                if ($cached_data !== false) {
                    return $cached_data;
                }
            }
        }

        $fresh_data = $this->fetch_remote_categories($tab);

        if ($fresh_data !== false) {
            set_transient($transient_key, $fresh_data, $this->cache_expiry);
            set_transient($meta_transient_key, ['timestamp' => time()], $this->cache_expiry);
            return $fresh_data;
        }

        return get_transient($transient_key);
    }

    /**
     * Get cached keywords using transients (fallback method)
     */
    private function get_transient_cached_keywords($tab, $force_refresh = false)
    {
        $transient_key = "jltma_keywords_{$tab}";
        $meta_transient_key = "jltma_keywords_{$tab}_meta";

        if (!$force_refresh) {
            $cached_meta = get_transient($meta_transient_key);
            if ($cached_meta && (time() - $cached_meta['timestamp']) < $this->cache_expiry) {
                $cached_data = get_transient($transient_key);
                if ($cached_data !== false) {
                    return $cached_data;
                }
            }
        }

        $fresh_data = $this->fetch_remote_keywords($tab);

        if ($fresh_data !== false) {
            set_transient($transient_key, $fresh_data, $this->cache_expiry);
            set_transient($meta_transient_key, ['timestamp' => time()], $this->cache_expiry);
            return $fresh_data;
        }

        return get_transient($transient_key);
    }

    /**
     * Clear transient cache (fallback method)
     */
    private function clear_transient_cache()
    {
        $template_types = ['master_section', 'master_pages', 'master_popups', 'master_headers', 'master_footers'];

        foreach ($template_types as $tab) {
            delete_transient("jltma_templates_{$tab}");
            delete_transient("jltma_templates_{$tab}_meta");
            delete_transient("jltma_categories_{$tab}");
            delete_transient("jltma_categories_{$tab}_meta");
            delete_transient("jltma_keywords_{$tab}");
            delete_transient("jltma_keywords_{$tab}_meta");
        }
    }

    /**
     * Preload popular templates in background
     */
    public function preload_popular_templates()
    {
        $popular_tabs = ['master_section', 'master_headers'];

        foreach ($popular_tabs as $tab) {
            if (!get_transient("jltma_preload_{$tab}")) {
                wp_schedule_single_event(time() + 60, 'jltma_background_preload', [$tab]);
                set_transient("jltma_preload_{$tab}", true, HOUR_IN_SECONDS);
            }
        }
    }

    /**
     * AJAX handler for cache preloading
     */
    public function preload_cache_ajax()
    {
        if (!current_user_can('manage_options')) {
            wp_die(-1);
        }

        if (!check_ajax_referer('jltma_preload_cache_nonce', 'security', false)) {
            wp_die(-1);
        }

        $tab = sanitize_text_field($_POST['tab'] ?? '');

        if (empty($tab)) {
            wp_send_json_error('Invalid tab');
        }

        // Preload in background
        $this->get_cached_templates($tab, true);

        wp_send_json_success('Cache preloaded for ' . $tab);
    }

    /**
     * Get singleton instance
     */
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

// Initialize templates cache manager
Master_Addons_Templates_Cache_Manager::get_instance();

// Create alias for backward compatibility
if (!class_exists('JLTMA_Template_Kit_Cache')) {
    class_alias('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager', 'JLTMA_Template_Kit_Cache');
}
