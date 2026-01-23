<?php

namespace MasterAddons\Inc\Templates\Classes;

use MasterAddons\Inc\Templates;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template Kit Cache Manager
 * Provides file-based caching for Template Kit functionality (Site Importer)
 * Separate from Template Library cache to maintain independence
 * Renamed to avoid conflicts with external jltma-site-core plugin
 */
class JLTMA_Plugin_Template_Kit_Cache
{

    private static $instance = null;
    private $cache_dir;
    private $cache_expiry;
    private $is_pro_enabled;
    private $purchased_dir;

    public function __construct()
    {
        $this->is_pro_enabled = Master_Addons_Helper::jltma_premium();
        $this->cache_dir = wp_upload_dir()['basedir'] . '/master_addons/templates_kits/';
        $this->purchased_dir = wp_upload_dir()['basedir'] . '/master_addons/purchased_kits/';
        $this->cache_expiry = apply_filters('jltma_template_kit_cache_expiry', 12 * HOUR_IN_SECONDS); // 12 hours default

        // Initialize cache system
        add_action('init', [$this, 'init'], 25);
    }

    public function init()
    {
        // Ensure cache directory exists
        $this->ensure_cache_directory();

        // Schedule cache updates
        add_action('wp', [$this, 'schedule_cache_updates']);
        add_action('jltma_template_kits_cache_update', [$this, 'update_template_kits_cache']);

        // Admin hooks for cache management
        add_action('admin_init', [$this, 'maybe_clear_cache']);
    }

    /**
     * Ensure cache directory exists with proper structure for template kits
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

            // Create subdirectories for template kits with images folder
            $subdirs = ['kits', 'manifests', 'thumbnails', 'previews', 'images', 'categories'];
            foreach ($subdirs as $subdir) {
                $subdir_path = $this->cache_dir . $subdir . '/';
                if (!file_exists($subdir_path)) {
                    wp_mkdir_p($subdir_path);
                }
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
     * Check if uploads directory is writable
     */
    private function is_uploads_writable()
    {
        $upload_dir = wp_upload_dir();
        return file_exists($upload_dir['basedir']) && is_writable($upload_dir['basedir']);
    }

    /**
     * Schedule cache update events for template kits
     */
    public function schedule_cache_updates()
    {
        if (!wp_next_scheduled('jltma_template_kits_cache_update')) {
            // Schedule twice daily (every 12 hours)
            wp_schedule_event(time(), 'twicedaily', 'jltma_template_kits_cache_update');
        }
    }

    /**
     * Get cached kits for a specific category (public method)
     */
    public function get_category_kits($category, $force_refresh = false)
    {
        if ($category === 'all') {
            return $this->get_cached_kits($force_refresh, 'all');
        }
        
        // Try to get category-specific cache
        $category_data = $this->get_cached_category_kits($category, $force_refresh);
        
        if ($category_data !== false) {
            return $category_data;
        }
        
        // If category cache doesn't exist, get all and filter
        $all_kits = $this->get_cached_kits($force_refresh, 'all');
        
        if ($all_kits && is_array($all_kits)) {
            // Filter kits by category
            $filtered_kits = [];
            
            foreach ($all_kits as $cat_key => $kits) {
                if ($cat_key === $category && is_array($kits)) {
                    return $kits;
                }
                
                // Also check within kits
                if (is_array($kits)) {
                    foreach ($kits as $kit) {
                        $kit_categories = $kit['categories'] ?? [];
                        if (is_string($kit_categories)) {
                            $kit_categories = [$kit_categories];
                        }
                        if (in_array($category, $kit_categories)) {
                            $filtered_kits[] = $kit;
                        }
                    }
                }
            }
            
            // Save the filtered data as category cache for next time
            if (!empty($filtered_kits)) {
                $this->save_category_cache($category, $filtered_kits);
                return $filtered_kits;
            }
        }
        
        return [];
    }
    
    /**
     * Get cached kit categories
     */
    public function get_cached_kit_categories($force_refresh = false)
    {
        // Try transient cache first if file cache is not available
        if (!$this->is_file_cache_available()) {
            return $this->get_transient_cached_kit_categories($force_refresh);
        }

        $cache_file = $this->cache_dir . 'kit-categories.json';
        $cache_meta_file = $this->cache_dir . 'categories-meta.json';

        // Check if cache exists and is valid
        if (!$force_refresh && $this->is_cache_valid($cache_meta_file)) {
            $cached_data = $this->read_cache_file($cache_file);
            if ($cached_data !== false) {
                return $cached_data;
            }
        }

        // Fetch fresh data from remote API
        $fresh_data = $this->fetch_remote_kit_categories();

        if ($fresh_data !== false) {
            // Cache the data
            $this->write_cache_file($cache_file, $fresh_data);
            $this->write_cache_meta($cache_meta_file);
            return $fresh_data;
        }

        // Fallback to expired cache if available
        return $this->read_cache_file($cache_file);
    }

    /**
     * Fetch kit categories from remote API
     */
    private function fetch_remote_kit_categories()
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
        if ($this->is_pro_enabled) {
            $api_url = add_query_arg('pro_enabled', 'true', $api_url);
        }

        $response = wp_remote_get($api_url, [
            'timeout' => 10,
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
     * Get cached kit categories using transients (fallback method)
     */
    private function get_transient_cached_kit_categories($force_refresh = false)
    {
        $transient_key = 'jltma_kit_categories';
        $meta_transient_key = 'jltma_kit_categories_meta';

        // Check if cache exists and is valid
        if (!$force_refresh) {
            $cached_meta = get_transient($meta_transient_key);
            if ($cached_meta && (time() - $cached_meta['timestamp']) < $this->cache_expiry) {
                $cached_data = get_transient($transient_key);
                if ($cached_data !== false) {
                    return $cached_data;
                }
            }
        }

        // Fetch fresh data from remote API
        $fresh_data = $this->fetch_remote_kit_categories();

        if ($fresh_data !== false) {
            // Cache the data using transients
            set_transient($transient_key, $fresh_data, $this->cache_expiry);
            set_transient($meta_transient_key, ['timestamp' => time()], $this->cache_expiry);
            return $fresh_data;
        }

        // Return cached data even if expired
        return get_transient($transient_key);
    }

    /**
     * Get cached template kits
     */
    public function get_cached_kits($force_refresh = false, $category = 'all')
    {
        // Try transient cache first if file cache is not available
        if (!$this->is_file_cache_available()) {
            return $this->get_transient_cached_kits($force_refresh);
        }

        // For specific categories, try to load category-specific cache first
        if ($category !== 'all') {
            $category_data = $this->get_cached_category_kits($category, $force_refresh);
            if ($category_data !== false) {
                return $category_data;
            }
        }

        $cache_file = $this->cache_dir . 'template-kits.json';
        $cache_meta_file = $this->cache_dir . 'meta.json';

        // Check if cache exists and is valid
        if (!$force_refresh && $this->is_cache_valid($cache_meta_file)) {
            $cached_data = $this->read_cache_file($cache_file);
            if ($cached_data !== false) {
                if( $category !== 'all') {
                    // Filter by category if needed
                    $filtered_data = [];
                    foreach ($cached_data as $kit_category => $kits) {
                        if ($kit_category === $category) {
                            $filtered_data = $kits;
                            break;
                        }
                    }
                    $cached_data = $filtered_data;
                }
                // Update thumbnail URLs to use local cache
                $this->process_kit_thumbnails($cached_data);
                return $cached_data;
            }
        }

        // Fetch fresh data from remote API
        $fresh_data = $this->fetch_remote_kits();

        if ($fresh_data !== false) {
            // Process and cache the data
            $this->process_and_cache_kits($fresh_data);
            
            // After processing, load the cached data
            if ($category !== 'all') {
                return $this->get_cached_category_kits($category, false);
            } else {
                return $this->read_cache_file($this->cache_dir . 'template-kits.json');
            }
        }

        // Fallback to expired cache if available
        return $this->read_cache_file($cache_file);
    }

    /**
     * Get cached kits for a specific category
     */
    private function get_cached_category_kits($category, $force_refresh = false)
    {
        $category_file = $this->cache_dir . 'categories/' . sanitize_file_name($category) . '.json';
        $category_meta_file = $this->cache_dir . 'categories/' . sanitize_file_name($category) . '_meta.json';
        
        // Ensure categories directory exists
        if (!file_exists($this->cache_dir . 'categories/')) {
            wp_mkdir_p($this->cache_dir . 'categories/');
        }
        
        // Check if category cache exists and is valid
        if (!$force_refresh && $this->is_cache_valid($category_meta_file)) {
            $cached_data = $this->read_cache_file($category_file);
            if ($cached_data !== false) {
                // Process thumbnails for cached data
                $this->process_kit_thumbnails($cached_data);
                return $cached_data;
            }
        }
        
        return false;
    }
    
    /**
     * Save category-specific cache
     */
    private function save_category_cache($category, $kits)
    {
        $category_file = $this->cache_dir . 'categories/' . sanitize_file_name($category) . '.json';
        $category_meta_file = $this->cache_dir . 'categories/' . sanitize_file_name($category) . '_meta.json';
        
        // Ensure categories directory exists
        if (!file_exists($this->cache_dir . 'categories/')) {
            wp_mkdir_p($this->cache_dir . 'categories/');
        }
        
        // Write category data
        $this->write_cache_file($category_file, $kits);
        $this->write_cache_meta($category_meta_file);
    }
    
    /**
     * Process and cache kits data
     */
    private function process_and_cache_kits($fresh_data)
    {
        if (!is_array($fresh_data)) {
            return;
        }

        // Ensure kits directory exists
        $kits_dir = $this->cache_dir . 'kits/';
        if (!file_exists($kits_dir)) {
            wp_mkdir_p($kits_dir);
        }

        // The API returns kits already organized by category like:
        // { "business": [...], "design": [...], "agency": [...] }
        // So we just need to process the thumbnails and save the data

        $categories_data = [];
        $all_kits = [];

        // Process each category
        foreach ($fresh_data as $category => $kits) {
            if (!is_array($kits)) {
                continue;
            }

            $categories_data[$category] = [];

            // Process each kit in the category
            foreach ($kits as &$kit) {
                if (!is_array($kit) || !isset($kit['kit_id'])) {
                    continue;
                }

                $kit_name = $kit['kit_name'] ?? $kit['name'] ?? '';

                // Download and update thumbnail URL
                if (isset($kit['thumbnail']) && !empty($kit['thumbnail'])) {
                    $local_url = $this->cache_image($kit['thumbnail'], "kit-{$kit_name}-thumb", 'thumbnails');
                    if ($local_url) {
                        $kit['thumbnail'] = $local_url;
                    }
                }

                // Download and update preview URL
                if (isset($kit['preview']) && !empty($kit['preview'])) {
                    $local_url = $this->cache_image($kit['preview'], "kit-{$kit_name}-preview", 'previews');
                    if ($local_url) {
                        $kit['preview'] = $local_url;
                    }
                }

                // Process individual template thumbnails if available
                if (isset($kit['templates']) && is_array($kit['templates'])) {
                    foreach ($kit['templates'] as &$template) {
                        if (isset($template['thumbnail']) && !empty($template['thumbnail'])) {
                            $template_name = $template['name'] ?? 'template';
                            $local_url = $this->cache_image(
                                $template['thumbnail'],
                                "kit-{$kit_name}-{$template_name}",
                                'thumbnails'
                            );
                            if ($local_url) {
                                $template['thumbnail'] = $local_url;
                            }
                        }
                    }
                }

                // Ensure categories field is properly set
                if (!isset($kit['categories'])) {
                    $kit['categories'] = $category;
                } elseif (is_string($kit['categories'])) {
                    $kit['categories'] = [$kit['categories']];
                } elseif (!is_array($kit['categories'])) {
                    $kit['categories'] = [$category];
                }

                // Add to category data
                $categories_data[$category][] = $kit;

                // Also keep track of all kits
                $all_kits[] = $kit;

                // Automatically download and cache the kit if not already cached
                if (isset($kit['kit_id'])) {
                    $this->maybe_download_kit($kit['kit_id']);
                }
            }
        }

        // Save main cache file organized by categories
        $this->write_cache_file($this->cache_dir . 'template-kits.json', $categories_data);
        $this->write_cache_meta($this->cache_dir . 'meta.json');

        // Save individual category cache files
        foreach ($categories_data as $category => $kits) {
            if (!empty($kits)) {
                $this->save_category_cache($category, $kits);
            }
        }
    }
    
    /**
     * Process kit thumbnails (helper method)
     */
    private function process_kit_thumbnails(&$kits)
    {
        if (!is_array($kits)) {
            return;
        }
        
        foreach ($kits as &$kit) {
            if (isset($kit['thumbnail'])) {
                $kit_name = $kit['name'] ?? $kit['kit_name'] ?? '';
                $cached_thumbnail = $this->get_kit_thumbnail_url($kit_name, 'home', $kit['thumbnail']);
                if ($cached_thumbnail) {
                    $kit['thumbnail'] = $cached_thumbnail;
                }
            }
        }
    }

    /**
     * Fetch template kits from remote API
     */
    private function fetch_remote_kits()
    {
        // Get config from the templates system
        $config = null;
        if (function_exists('MasterAddons\Inc\Templates\master_addons_templates')) {
            $templates_instance = \MasterAddons\Inc\Templates\master_addons_templates();
            if ($templates_instance && isset($templates_instance->config)) {
                $config = $templates_instance->config->get('api');
            }
        }
        
        
        // Build API URL for template kits (use templates endpoint with template-kits path)
        $api_url = $config['base'] . $config['path'] . '/template-kits/';

        // Add pro_enabled parameter if pro is enabled
        if ($this->is_pro_enabled) {
            $api_url = add_query_arg('pro_enabled', 'true', $api_url);
        }

        $response = wp_remote_get($api_url, [
            'timeout' => 10, 
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

        return isset($data['kits']) ? $data['kits'] : [];
    }

    /**
     * Cache template kit images locally
     */
    private function cache_kit_images($kits)
    {
        if (!is_array($kits)) {
            return;
        }

        foreach ($kits as $kit) {
            $kit_name = $kit['name'] ?? '';
            
            if (isset($kit['thumbnail']) && !empty($kit['thumbnail'])) {
                $this->cache_image($kit['thumbnail'], "kit-{$kit_name}-thumb", 'thumbnails');
            }

            if (isset($kit['preview']) && !empty($kit['preview'])) {
                $this->cache_image($kit['preview'], "kit-{$kit_name}-preview", 'previews');
            }

            // Cache individual template thumbnails if available
            if (isset($kit['templates']) && is_array($kit['templates'])) {
                foreach ($kit['templates'] as $template) {
                    if (isset($template['thumbnail']) && !empty($template['thumbnail'])) {
                        $template_name = $template['name'] ?? 'template';
                        $this->cache_image($template['thumbnail'], "kit-{$kit_name}-{$template_name}", 'thumbnails');
                    }
                }
            }
        }
    }

    /**
     * Cache individual image and return URL
     * @param string $image_url The URL of the image to cache
     * @param string $filename The filename to save as (without extension)
     * @param string $folder The subfolder to save in (thumbnails, previews, images)
     * @return string|false Local URL or false on failure
     */
    private function cache_image($image_url, $filename, $folder = 'images')
    {
        if (empty($image_url)) {
            return false;
        }

        // Parse the URL to get the extension
        $parsed_url = parse_url($image_url);
        $path = $parsed_url['path'] ?? '';
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        // Handle cases where extension might be in query string
        if (empty($extension) || strlen($extension) > 4) {
            // Try to determine from content type
            $extension = 'jpg'; // Default fallback
        }

        // Sanitize filename
        $filename = sanitize_file_name($filename);

        $local_file = $this->cache_dir . "{$folder}/{$filename}.{$extension}";

        // Skip downloading if already cached and recent, but still return URL
        if (file_exists($local_file) && (time() - filemtime($local_file)) < DAY_IN_SECONDS) {
            // Convert to URL and return
            $upload_dir = wp_upload_dir();
            $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
            return $upload_dir['baseurl'] . $relative_path;
        }

        // Ensure the folder exists
        $folder_path = $this->cache_dir . $folder;
        if (!file_exists($folder_path)) {
            wp_mkdir_p($folder_path);
        }

        $response = wp_remote_get($image_url, [
            'timeout' => 30,
            'sslverify' => false,
            'headers' => [
                'User-Agent' => 'Master Addons Image Cache/' . JLTMA_VER
            ]
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $image_data = wp_remote_retrieve_body($response);

        if (file_put_contents($local_file, $image_data)) {
            // Convert to URL and return
            $upload_dir = wp_upload_dir();
            $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
            return $upload_dir['baseurl'] . $relative_path;
        }

        return false;
    }

    /**
     * Update template kits cache (scheduled event)
     */
    public function update_template_kits_cache()
    {
        // Ensure kits directory exists
        $kits_dir = $this->cache_dir . 'kits/';
        if (!file_exists($kits_dir)) {
            wp_mkdir_p($kits_dir);
        }

        // Update template kits
        $this->get_cached_kits(true);

        // Clean up old cache files
        $this->cleanup_old_cache();

        // Update last cache time
        set_transient('jltma_template_kits_last_cache_update', time(), DAY_IN_SECONDS);
    }

    /**
     * Get template kit thumbnail from cache or return original URL
     */
    public function get_kit_thumbnail_url($kit_name, $template_name = 'home', $original_url = null)
    {
        // Normalize names for filename
        $kit_slug = sanitize_title($kit_name);
        $template_slug = sanitize_title($template_name);

        // Check cache directory first
        $cache_image_dir = $this->cache_dir . 'thumbnails/';
        $cached_file_patterns = [
            "kit-{$kit_slug}-{$template_slug}.jpg",
            "kit-{$kit_slug}-{$template_slug}.png",
            "kit-{$kit_slug}-thumb.jpg",
            "kit-{$kit_slug}-thumb.png"
        ];

        foreach ($cached_file_patterns as $pattern) {
            $cached_file = $cache_image_dir . $pattern;
            if (file_exists($cached_file)) {
                $upload_dir = wp_upload_dir();
                $relative_path = str_replace($upload_dir['basedir'], '', $cached_file);
                return $upload_dir['baseurl'] . $relative_path;
            }
        }

        // Return original URL if provided
        return $original_url;
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

        $extended_cache_expiry = 24 * HOUR_IN_SECONDS;
        return (time() - $meta['timestamp']) < $extended_cache_expiry;
    }

    /**
     * Read cache file
     */
    private function read_cache_file($file_path)
    {
        if (!file_exists($file_path)) {
            return false;
        }

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
     * Clear all template kit cache (preserves purchased templates)
     */
    public function clear_cache()
    {
        $cleared = false;

        // Clear file cache if available, but preserve purchased templates
        if (file_exists($this->cache_dir)) {
            // Note: Purchased templates are stored in a separate directory ($this->purchased_dir)
            // So they won't be affected by clearing the regular cache
            $this->delete_directory_contents($this->cache_dir);
            $this->ensure_cache_directory();
            $cleared = true;
        }

        // Clear transient cache
        $this->clear_transient_cache();

        delete_transient('jltma_template_kits_last_cache_update');

        return true;
    }

    /**
     * Refresh cache by clearing and fetching fresh data from API
     */
    public function refresh_cache()
    {
        // Clear all existing cache
        $this->clear_cache();

        // Force fetch fresh data from API
        $kits = $this->get_cached_kits(true, 'all');

        // Process all existing kit manifests to fix URLs
        $this->reprocess_all_kit_manifests();

        // Update last cache refresh time
        set_transient('jltma_template_kits_last_cache_update', time(), DAY_IN_SECONDS);

        // Count total kits
        $total_kits = 0;
        if (is_array($kits)) {
            foreach ($kits as $category => $category_kits) {
                if (is_array($category_kits)) {
                    $total_kits += count($category_kits);
                }
            }
        }

        return [
            'kits' => $total_kits,
            'categories' => is_array($kits) ? array_keys($kits) : []
        ];
    }

    /**
     * Reprocess all existing kit manifests to fix URLs
     */
    private function reprocess_all_kit_manifests() {
        // Process regular cached kits
        $kits_dir = $this->cache_dir . 'kits/';
        if (file_exists($kits_dir)) {
            // Get all kit directories
            $kit_dirs = glob($kits_dir . '*', GLOB_ONLYDIR);

            foreach ($kit_dirs as $kit_dir) {
                // Process manifest
                $this->process_extracted_kit_manifest($kit_dir);

                // Process template JSON files
                $this->process_template_json_files($kit_dir);

                // Process nav_menu.json
                $this->process_nav_menu_json($kit_dir);
            }
        }

        // Process purchased kits
        $this->reprocess_all_purchased_kits();
    }

    /**
     * Reprocess all purchased kits to fix URLs
     */
    public function reprocess_all_purchased_kits() {
        $purchased_kits_dir = $this->purchased_dir . 'kits/';

        if (!file_exists($purchased_kits_dir)) {
            return;
        }

        // Process main kit JSON files (e.g., 9960.json, 9966.json)
        $main_json_files = glob($purchased_kits_dir . '*.json');
        foreach ($main_json_files as $json_file) {
            $this->process_purchased_kit_main_json($json_file);
        }

        // Get all purchased kit directories
        $kit_dirs = glob($purchased_kits_dir . 'kit_*', GLOB_ONLYDIR);

        foreach ($kit_dirs as $kit_dir) {
            // Process manifest
            $this->process_extracted_kit_manifest($kit_dir);

            // Process template JSON files
            $this->process_template_json_files($kit_dir);

            // Process nav_menu.json
            $this->process_nav_menu_json($kit_dir);
        }
    }

    /**
     * Process main purchased kit JSON file to replace remote URLs
     * @param string $json_file Path to the JSON file
     */
    private function process_purchased_kit_main_json($json_file) {
        if (!file_exists($json_file)) {
            return;
        }

        $content = file_get_contents($json_file);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return;
        }

        $kit_id = $data['kit_id'] ?? basename($json_file, '.json');
        $updated = false;

        // Process main thumbnail
        if (isset($data['thumbnail']) && !empty($data['thumbnail'])) {
            $local_url = $this->process_purchased_kit_image($data['thumbnail'], $kit_id, 'main-thumb');
            if ($local_url) {
                $data['thumbnail'] = $local_url;
                $updated = true;
            }
        }

        // Process preview_url if it's an image
        if (isset($data['preview_url']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $data['preview_url'])) {
            $local_url = $this->process_purchased_kit_image($data['preview_url'], $kit_id, 'preview');
            if ($local_url) {
                $data['preview_url'] = $local_url;
                $updated = true;
            }
        }

        // Process templates array
        if (isset($data['templates']) && is_array($data['templates'])) {
            foreach ($data['templates'] as &$template) {
                // Process screenshot
                if (isset($template['screenshot']) && !empty($template['screenshot'])) {
                    // If it's already a relative path, leave it
                    if (strpos($template['screenshot'], 'screenshots/') === 0) {
                        continue;
                    }

                    $template_id = $template['template_id'] ?? uniqid();
                    $local_url = $this->process_purchased_kit_image(
                        $template['screenshot'],
                        $kit_id,
                        "template-{$template_id}-screenshot"
                    );
                    if ($local_url) {
                        $template['screenshot'] = $local_url;
                        $updated = true;
                    }
                }

                // Process thumbnail if exists
                if (isset($template['thumbnail']) && !empty($template['thumbnail'])) {
                    $template_id = $template['template_id'] ?? uniqid();
                    $local_url = $this->process_purchased_kit_image(
                        $template['thumbnail'],
                        $kit_id,
                        "template-{$template_id}-thumb"
                    );
                    if ($local_url) {
                        $template['thumbnail'] = $local_url;
                        $updated = true;
                    }
                }
            }
        }

        // Process manifest data if exists
        if (isset($data['manifest']) && is_array($data['manifest'])) {
            // Process manifest thumbnail
            if (isset($data['manifest']['thumbnail'])) {
                $local_url = $this->process_purchased_kit_image(
                    $data['manifest']['thumbnail'],
                    $kit_id,
                    'manifest-thumb'
                );
                if ($local_url) {
                    $data['manifest']['thumbnail'] = $local_url;
                    $updated = true;
                }
            }

            // Process manifest templates
            if (isset($data['manifest']['templates']) && is_array($data['manifest']['templates'])) {
                foreach ($data['manifest']['templates'] as &$template) {
                    if (isset($template['screenshot']) && !empty($template['screenshot'])) {
                        // Skip if already relative
                        if (strpos($template['screenshot'], 'screenshots/') === 0) {
                            continue;
                        }

                        $template_id = $template['template_id'] ?? uniqid();
                        $local_url = $this->process_purchased_kit_image(
                            $template['screenshot'],
                            $kit_id,
                            "manifest-template-{$template_id}"
                        );
                        if ($local_url) {
                            $template['screenshot'] = $local_url;
                            $updated = true;
                        }
                    }
                }
            }
        }

        // Save the updated JSON if changes were made
        if ($updated) {
            $json = wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents($json_file, $json);
        }
    }

    /**
     * Process and cache a purchased kit image
     * @param string $image_url The image URL
     * @param string $kit_id The kit ID
     * @param string $filename_prefix Filename prefix
     * @return string|false Local URL or false
     */
    private function process_purchased_kit_image($image_url, $kit_id, $filename_prefix) {
        if (empty($image_url)) {
            return false;
        }

        // Check if it's already a local URL
        $upload_dir = wp_upload_dir();
        if (strpos($image_url, $upload_dir['baseurl']) === 0) {
            // Clean up any double slashes
            return preg_replace('#(?<!:)//+#', '/', $image_url);
        }

        // Skip if not a valid URL
        if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Generate filename
        $url_hash = md5($image_url);
        $filename = "kit-{$kit_id}-{$filename_prefix}-{$url_hash}";

        // Set cache directory for purchased kits
        $cache_base = $this->purchased_dir;
        $folder = 'images';

        // Try to detect extension from URL
        $parsed_url = parse_url($image_url);
        $path = $parsed_url['path'] ?? '';
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if (empty($extension) || strlen($extension) > 4) {
            $extension = 'jpg';
        }

        $local_file = $cache_base . "{$folder}/{$filename}.{$extension}";

        // Check if already cached
        if (file_exists($local_file)) {
            $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
            return $upload_dir['baseurl'] . $relative_path;
        }

        // Ensure folder exists
        $folder_path = $cache_base . $folder;
        if (!file_exists($folder_path)) {
            wp_mkdir_p($folder_path);
        }

        // Download the image
        $response = wp_remote_get($image_url, [
            'timeout' => 30,
            'sslverify' => false,
            'headers' => [
                'User-Agent' => 'Master Addons Image Cache/' . JLTMA_VER
            ]
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $image_data = wp_remote_retrieve_body($response);

        if (file_put_contents($local_file, $image_data)) {
            $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
            return $upload_dir['baseurl'] . $relative_path;
        }

        return false;
    }

    /**
     * Process kit data to convert remote URLs to local URLs
     * @param array $kit_data The kit data to process
     * @param string $kit_id The kit ID
     * @return array The processed kit data with local URLs
     */
    private function process_kit_data_urls($kit_data, $kit_id) {
        $processed_data = $kit_data;

        // Process main thumbnail
        if (isset($processed_data['thumbnail']) && !empty($processed_data['thumbnail'])) {
            $local_url = $this->process_purchased_kit_image($processed_data['thumbnail'], $kit_id, 'main-thumb');
            if ($local_url) {
                $processed_data['thumbnail'] = $local_url;
            }
        }

        // Process preview_url if it's an image
        if (isset($processed_data['preview_url']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $processed_data['preview_url'])) {
            $local_url = $this->process_purchased_kit_image($processed_data['preview_url'], $kit_id, 'preview');
            if ($local_url) {
                $processed_data['preview_url'] = $local_url;
            }
        }

        // Process templates array
        if (isset($processed_data['templates']) && is_array($processed_data['templates'])) {
            foreach ($processed_data['templates'] as &$template) {
                // Process screenshot
                if (isset($template['screenshot']) && !empty($template['screenshot'])) {
                    // Skip if it's already a relative path
                    if (strpos($template['screenshot'], 'screenshots/') !== 0) {
                        $template_id = $template['template_id'] ?? $template['id'] ?? uniqid();
                        $local_url = $this->process_purchased_kit_image(
                            $template['screenshot'],
                            $kit_id,
                            "template-{$template_id}-screenshot"
                        );
                        if ($local_url) {
                            $template['screenshot'] = $local_url;
                        }
                    }
                }

                // Process thumbnail if exists
                if (isset($template['thumbnail']) && !empty($template['thumbnail'])) {
                    $template_id = $template['template_id'] ?? $template['id'] ?? uniqid();
                    $local_url = $this->process_purchased_kit_image(
                        $template['thumbnail'],
                        $kit_id,
                        "template-{$template_id}-thumb"
                    );
                    if ($local_url) {
                        $template['thumbnail'] = $local_url;
                    }
                }

                // Process preview_url if it's an image
                if (isset($template['preview_url']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $template['preview_url'])) {
                    $template_id = $template['template_id'] ?? $template['id'] ?? uniqid();
                    $local_url = $this->process_purchased_kit_image(
                        $template['preview_url'],
                        $kit_id,
                        "template-{$template_id}-preview"
                    );
                    if ($local_url) {
                        $template['preview_url'] = $local_url;
                    }
                }
            }
        }

        // Process manifest data if exists
        if (isset($processed_data['manifest']) && is_array($processed_data['manifest'])) {
            // Process manifest thumbnail
            if (isset($processed_data['manifest']['thumbnail']) && !empty($processed_data['manifest']['thumbnail'])) {
                $local_url = $this->process_purchased_kit_image(
                    $processed_data['manifest']['thumbnail'],
                    $kit_id,
                    'manifest-thumb'
                );
                if ($local_url) {
                    $processed_data['manifest']['thumbnail'] = $local_url;
                }
            }

            // Process manifest thumbnail_url
            if (isset($processed_data['manifest']['thumbnail_url']) && !empty($processed_data['manifest']['thumbnail_url'])) {
                $local_url = $this->process_purchased_kit_image(
                    $processed_data['manifest']['thumbnail_url'],
                    $kit_id,
                    'manifest-thumb-url'
                );
                if ($local_url) {
                    $processed_data['manifest']['thumbnail_url'] = $local_url;
                }
            }

            // Process manifest preview_url if it's an image
            if (isset($processed_data['manifest']['preview_url']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $processed_data['manifest']['preview_url'])) {
                $local_url = $this->process_purchased_kit_image(
                    $processed_data['manifest']['preview_url'],
                    $kit_id,
                    'manifest-preview'
                );
                if ($local_url) {
                    $processed_data['manifest']['preview_url'] = $local_url;
                }
            }

            // Process manifest templates
            if (isset($processed_data['manifest']['templates']) && is_array($processed_data['manifest']['templates'])) {
                foreach ($processed_data['manifest']['templates'] as &$template) {
                    if (isset($template['screenshot']) && !empty($template['screenshot'])) {
                        // Skip if already relative
                        if (strpos($template['screenshot'], 'screenshots/') !== 0) {
                            $template_id = $template['template_id'] ?? $template['id'] ?? uniqid();
                            $local_url = $this->process_purchased_kit_image(
                                $template['screenshot'],
                                $kit_id,
                                "manifest-template-{$template_id}"
                            );
                            if ($local_url) {
                                $template['screenshot'] = $local_url;
                            }
                        }
                    }

                    if (isset($template['thumbnail']) && !empty($template['thumbnail'])) {
                        $template_id = $template['template_id'] ?? $template['id'] ?? uniqid();
                        $local_url = $this->process_purchased_kit_image(
                            $template['thumbnail'],
                            $kit_id,
                            "manifest-template-{$template_id}-thumb"
                        );
                        if ($local_url) {
                            $template['thumbnail'] = $local_url;
                        }
                    }
                }
            }

            // Process manifest pages (alternative structure)
            if (isset($processed_data['manifest']['pages']) && is_array($processed_data['manifest']['pages'])) {
                foreach ($processed_data['manifest']['pages'] as &$page) {
                    if (isset($page['screenshot']) && !empty($page['screenshot'])) {
                        $page_id = $page['page_id'] ?? $page['id'] ?? uniqid();
                        $local_url = $this->process_purchased_kit_image(
                            $page['screenshot'],
                            $kit_id,
                            "manifest-page-{$page_id}"
                        );
                        if ($local_url) {
                            $page['screenshot'] = $local_url;
                        }
                    }

                    if (isset($page['thumbnail']) && !empty($page['thumbnail'])) {
                        $page_id = $page['page_id'] ?? $page['id'] ?? uniqid();
                        $local_url = $this->process_purchased_kit_image(
                            $page['thumbnail'],
                            $kit_id,
                            "manifest-page-{$page_id}-thumb"
                        );
                        if ($local_url) {
                            $page['thumbnail'] = $local_url;
                        }
                    }
                }
            }

            // Process images array in manifest
            if (isset($processed_data['manifest']['images']) && is_array($processed_data['manifest']['images'])) {
                foreach ($processed_data['manifest']['images'] as &$image) {
                    // Process thumbnail_url
                    if (isset($image['thumbnail_url']) && !empty($image['thumbnail_url'])) {
                        $filename = $image['filename'] ?? uniqid();
                        $local_url = $this->process_purchased_kit_image(
                            $image['thumbnail_url'],
                            $kit_id,
                            "manifest-image-" . pathinfo($filename, PATHINFO_FILENAME)
                        );
                        if ($local_url) {
                            $image['thumbnail_url'] = $local_url;
                        }
                    }

                    // Process image_urls if it contains URLs
                    if (isset($image['image_urls']) && !empty($image['image_urls']) && filter_var($image['image_urls'], FILTER_VALIDATE_URL)) {
                        $filename = $image['filename'] ?? uniqid();
                        $local_url = $this->process_purchased_kit_image(
                            $image['image_urls'],
                            $kit_id,
                            "manifest-image-url-" . pathinfo($filename, PATHINFO_FILENAME)
                        );
                        if ($local_url) {
                            $image['image_urls'] = $local_url;
                        }
                    }
                }
            }
        }

        // Process content if it contains Elementor data with images
        if (isset($processed_data['content']) && is_array($processed_data['content'])) {
            $processed_data['content'] = $this->process_elementor_data_for_images(
                $processed_data['content'],
                $kit_id,
                'content',
                null
            );
        }

        return $processed_data;
    }

    /**
     * Clean up old cache files
     */
    private function cleanup_old_cache()
    {
        $subdirs = ['kits', 'manifests', 'thumbnails', 'previews', 'images', 'categories'];
        $max_age = 30 * DAY_IN_SECONDS; // 30 days

        foreach ($subdirs as $subdir) {
            $full_dir = $this->cache_dir . $subdir . '/';
            if (!file_exists($full_dir)) {
                // Create the directory if it doesn't exist
                wp_mkdir_p($full_dir);
                continue;
            }

            // For kits directory, handle subdirectories
            if ($subdir === 'kits') {
                $kit_dirs = glob($full_dir . '*', GLOB_ONLYDIR);
                foreach ($kit_dirs as $kit_dir) {
                    $meta_file = $kit_dir . '/meta.json';
                    if (file_exists($meta_file) && (time() - filemtime($meta_file)) > $max_age) {
                        $this->delete_directory_contents($kit_dir);
                        @rmdir($kit_dir);
                    }
                }
            } else {
                $files = glob($full_dir . '*');
                foreach ($files as $file) {
                    if (is_file($file) && (time() - filemtime($file)) > $max_age) {
                        unlink($file);
                    }
                }
            }
        }
    }

    /**
     * Delete directory contents recursively
     * @param string $dir Directory path
     * @param array $exclude_dirs Directories to exclude from deletion
     */
    private function delete_directory_contents($dir, $exclude_dirs = [])
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                // Check if this directory should be excluded
                $should_exclude = false;
                foreach ($exclude_dirs as $exclude_dir) {
                    if (strpos($file, $exclude_dir) !== false) {
                        $should_exclude = true;
                        break;
                    }
                }

                if (!$should_exclude) {
                    $this->delete_directory_contents($file, $exclude_dirs);
                    @rmdir($file);
                }
            } else {
                // Don't delete files in excluded directories
                $in_excluded_dir = false;
                foreach ($exclude_dirs as $exclude_dir) {
                    if (strpos($file, $exclude_dir) !== false) {
                        $in_excluded_dir = true;
                        break;
                    }
                }

                if (!$in_excluded_dir) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Handle cache clearing from admin
     */
    public function maybe_clear_cache()
    {
        if (isset($_GET['jltma_clear_template_kit_cache']) &&
            isset($_GET['_wpnonce']) &&
            wp_verify_nonce($_GET['_wpnonce'], 'jltma_clear_template_kit_cache') &&
            current_user_can('manage_options')) {

            $this->clear_cache();

            wp_redirect(add_query_arg([
                'jltma_template_kit_cache_cleared' => '1'
            ], remove_query_arg(['jltma_clear_template_kit_cache', '_wpnonce'])));
            exit;
        }

        // Handle cache refresh from admin
        if (isset($_GET['jltma_refresh_template_kit_cache']) &&
            isset($_GET['_wpnonce']) &&
            wp_verify_nonce($_GET['_wpnonce'], 'jltma_refresh_template_kit_cache') &&
            current_user_can('manage_options')) {

            $this->refresh_cache();

            wp_redirect(add_query_arg([
                'jltma_template_kit_cache_refreshed' => '1'
            ], remove_query_arg(['jltma_refresh_template_kit_cache', '_wpnonce'])));
            exit;
        }

        // Handle download all kits from admin
        if (isset($_GET['jltma_download_all_kits']) &&
            isset($_GET['_wpnonce']) &&
            wp_verify_nonce($_GET['_wpnonce'], 'jltma_download_all_kits') &&
            current_user_can('manage_options')) {

            $result = $this->download_all_kits();

            wp_redirect(add_query_arg([
                'jltma_kits_downloaded' => $result['downloaded'],
                'jltma_kits_failed' => $result['failed']
            ], remove_query_arg(['jltma_download_all_kits', '_wpnonce'])));
            exit;
        }
    }

    /**
     * Get cache statistics
     */
    public function get_cache_stats()
    {
        $stats = [
            'cache_dir_exists' => file_exists($this->cache_dir),
            'cache_size' => $this->get_directory_size($this->cache_dir),
            'last_update' => get_transient('jltma_template_kits_last_cache_update'),
            'next_scheduled_update' => wp_next_scheduled('jltma_template_kits_cache_update'),
            'total_kits' => 0,
            'total_templates' => 0
        ];

        // Count cached kits
        if ($this->is_file_cache_available()) {
            $kits_file = $this->cache_dir . 'template-kits.json';
            if (file_exists($kits_file)) {
                $kits_data = $this->read_cache_file($kits_file);
                if ($kits_data && is_array($kits_data)) {
                    $stats['total_kits'] = count($kits_data);
                    foreach ($kits_data as $kit) {
                        if (isset($kit['templates']) && is_array($kit['templates'])) {
                            $stats['total_templates'] += count($kit['templates']);
                        }
                    }
                }
            }
        } else {
            // Count from transients
            $cached_kits = get_transient('jltma_template_kits');
            if ($cached_kits && is_array($cached_kits)) {
                $stats['total_kits'] = count($cached_kits);
                foreach ($cached_kits as $kit) {
                    if (isset($kit['templates']) && is_array($kit['templates'])) {
                        $stats['total_templates'] += count($kit['templates']);
                    }
                }
            }
        }

        return $stats;
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
     * Get cached template kits using transients (fallback method)
     */
    private function get_transient_cached_kits($force_refresh = false)
    {
        $transient_key = 'jltma_template_kits';
        $meta_transient_key = 'jltma_template_kits_meta';

        // Check if cache exists and is valid
        if (!$force_refresh) {
            $cached_meta = get_transient($meta_transient_key);
            if ($cached_meta && (time() - $cached_meta['timestamp']) < $this->cache_expiry) {
                $cached_data = get_transient($transient_key);
                if ($cached_data !== false) {
                    // Update thumbnail URLs for transient cached kits
                    foreach ($cached_data as &$kit) {
                        if (isset($kit['thumbnail'])) {
                            $cached_thumbnail = $this->get_kit_thumbnail_url($kit['name'], 'home', $kit['thumbnail']);
                            if ($cached_thumbnail) {
                                $kit['thumbnail'] = $cached_thumbnail;
                            }
                        }
                    }
                    return $cached_data;
                }
            }
        }

        // Fetch fresh data from remote API
        $fresh_data = $this->fetch_remote_kits();
        $categories = ['all' => 'ALL Categories'];

        if ($fresh_data !== false) {
            // Update thumbnail URLs for fresh transient kits
            foreach ($fresh_data as &$kit) {
                if (isset($kit['thumbnail'])) {
                    $cached_thumbnail = $this->get_kit_thumbnail_url($kit['name'], 'home', $kit['thumbnail']);
                    if ($cached_thumbnail) {
                        $kit['thumbnail'] = $cached_thumbnail;
                    }
                }
            }

            // Cache the data using transients
            set_transient($transient_key, $fresh_data, $this->cache_expiry);
            set_transient($meta_transient_key, ['timestamp' => time()], $this->cache_expiry);

            return $fresh_data;
        }

        // Return cached data even if expired
        return get_transient($transient_key);
    }

    /**
     * Clear transient cache (fallback method)
     */
    private function clear_transient_cache()
    {
        delete_transient('jltma_template_kits');
        delete_transient('jltma_template_kits_meta');
        delete_transient('jltma_kit_categories');
        delete_transient('jltma_kit_categories_meta');
        
        // Clear kit template transients
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_jltma_kit_templates_%' 
             OR option_name LIKE '_transient_timeout_jltma_kit_templates_%'"
        );
    }
    
    /**
     * Get cached kit content (templates inside a kit)
     */
    public function get_cached_kit_content($kit_id, $kit_category, $force_refresh = false) {
        // Check if file cache is available
        if (!$this->is_file_cache_available()) {
            return false;
        }
        
        $cache_file = $this->cache_dir . $kit_category . '_' . $kit_id . '.json';
        $meta_file = $this->cache_dir . $kit_category . '_' . $kit_id . '_meta.json';
        
        // Check if we need to refresh
        if (!$force_refresh && file_exists($cache_file) && file_exists($meta_file)) {
            if ($this->is_cache_valid($meta_file)) {
                $cached_data = $this->read_cache_file($cache_file);
                if ($cached_data !== false) {
                    return $cached_data;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Save kit content to cache
     */
    public function save_kit_content($kit_id, $kit_category, $templates) {
        // Check if file cache is available
        if (!$this->is_file_cache_available()) {
            return false;
        }
        
        // Process templates to cache all images
        $templates = $this->process_and_cache_template_images($templates, $kit_id);
        
        $cache_file = $this->cache_dir . $kit_category . '_' . $kit_id . '.json';
        $meta_file = $this->cache_dir . $kit_category . '_' . $kit_id . '_meta.json';
        
        // Write cache file
        if ($this->write_cache_file($cache_file, $templates)) {
            // Write meta file
            $this->write_cache_meta($meta_file);
            return true;
        }
        
        return false;
    }

    /**
     * Process and cache all images in template content
     */
    private function process_and_cache_template_images($templates, $kit_id) {
        if (!is_array($templates)) {
            return $templates;
        }
        
        foreach ($templates as &$template) {
            if (isset($template['content'])) {
                $template['content'] = $this->process_elementor_content_images($template['content'], $kit_id);
            }
        }
        
        return $templates;
    }
    
    /**
     * Process Elementor content to find and cache images
     */
    private function process_elementor_content_images($content, $kit_id) {
        if (is_string($content)) {
            $content = json_decode($content, true);
        }
        
        if (!is_array($content)) {
            return $content;
        }
        
        // Recursively process elements
        foreach ($content as &$element) {
            if (isset($element['settings'])) {
                $element['settings'] = $this->process_element_settings_images($element['settings'], $kit_id);
            }
            
            if (isset($element['elements']) && is_array($element['elements'])) {
                $element['elements'] = $this->process_elementor_content_images($element['elements'], $kit_id);
            }
        }
        
        return $content;
    }
    
    /**
     * Process element settings to cache images
     */
    private function process_element_settings_images($settings, $kit_id) {
        if (!is_array($settings)) {
            return $settings;
        }
        
        // Image-related settings to check
        $image_settings = [
            'image', 'background_image', 'hover_image', 'bg_image', 'icon_image',
            'gallery', 'images', 'slide_image', 'background_overlay_image',
            'testimonial_image', 'team_image', 'portfolio_image', 'logo_image',
            'before_image', 'after_image', 'author_image', 'product_image'
        ];
        
        foreach ($settings as $key => &$value) {
            // Handle single image settings
            if (in_array($key, $image_settings) && is_array($value) && isset($value['url'])) {
                $cached_url = $this->cache_and_replace_image_url($value['url'], $kit_id, $key);
                if ($cached_url) {
                    $value['url'] = $cached_url;
                }
            }
            
            // Handle gallery settings (array of images)
            if ($key === 'gallery' && is_array($value)) {
                foreach ($value as &$gallery_item) {
                    if (is_array($gallery_item) && isset($gallery_item['url'])) {
                        $cached_url = $this->cache_and_replace_image_url($gallery_item['url'], $kit_id, 'gallery');
                        if ($cached_url) {
                            $gallery_item['url'] = $cached_url;
                        }
                    }
                }
            }
            
            // Handle repeater fields that might contain images
            if (is_array($value) && !in_array($key, $image_settings)) {
                foreach ($value as &$repeater_item) {
                    if (is_array($repeater_item)) {
                        $repeater_item = $this->process_element_settings_images($repeater_item, $kit_id);
                    }
                }
            }
        }
        
        return $settings;
    }
    
    /**
     * Cache an image and return the local URL
     */
    private function cache_and_replace_image_url($image_url, $kit_id, $context = 'content') {
        if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Generate a unique filename based on the URL and context
        $url_hash = md5($image_url);
        $filename = "kit-{$kit_id}-{$context}-{$url_hash}";
        
        // Cache the image
        $local_path = $this->cache_image($image_url, $filename, 'images');
        
        if ($local_path) {
            // Convert local path to URL
            $upload_dir = wp_upload_dir();
            $relative_path = str_replace($upload_dir['basedir'], '', $local_path);
            return $upload_dir['baseurl'] . $relative_path;
        }
        
        return false;
    }
    
    /**
     * Get cached image URL
     */
    public function get_cached_image_url($original_url, $kit_id = '', $context = 'content') {
        if (empty($original_url)) {
            return $original_url;
        }

        // Generate the expected filename
        $url_hash = md5($original_url);
        $filename = "kit-{$kit_id}-{$context}-{$url_hash}";

        // Check for cached file with common extensions
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
        foreach ($extensions as $ext) {
            $local_file = $this->cache_dir . "images/{$filename}.{$ext}";
            if (file_exists($local_file)) {
                $upload_dir = wp_upload_dir();
                $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
                return $upload_dir['baseurl'] . $relative_path;
            }
        }

        return $original_url;
    }

    /**
     * Download and cache kit manifest and ZIP file
     * @param string $kit_id The kit ID
     * @return array|false Array with kit data or false on failure
     */
    public function download_and_cache_kit($kit_id) {
        // Ensure kits directory exists
        $kits_dir = $this->cache_dir . 'kits/';
        if (!file_exists($kits_dir)) {
            wp_mkdir_p($kits_dir);
        }

        // Create kit-specific directory
        $kit_dir = $kits_dir . sanitize_file_name($kit_id) . '/';
        if (!file_exists($kit_dir)) {
            wp_mkdir_p($kit_dir);
        }

        // Check if kit is already cached and recent
        $manifest_file = $kit_dir . 'manifest.json';
        $meta_file = $kit_dir . 'meta.json';

        // Check if cache is valid
        if (!$this->is_cache_valid($meta_file)) {
            // Get API config
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

            // Build manifest URL
            $api_url = $config['base'] . $config['path'] . '/templates-kit/' . $kit_id . '/manifest.json';

            // Add pro_enabled parameter if pro is enabled
            if ($this->is_pro_enabled) {
                $api_url = add_query_arg('pro_enabled', 'true', $api_url);
            }

            // Fetch manifest from API
            $response = wp_remote_get($api_url, [
                'timeout' => 60,
                'sslverify' => false,
                'headers' => [
                    'User-Agent' => 'Master Addons Kit Downloader/' . JLTMA_VER
                ]
            ]);

            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                return false;
            }

            $body = wp_remote_retrieve_body($response);
            $manifest_data = json_decode($body, true);

            if (!$manifest_data || !isset($manifest_data['success']) || !$manifest_data['success']) {
                return false;
            }

            // Check if we have a ZIP URL
            if (isset($manifest_data['data']) && is_string($manifest_data['data'])) {
                $zip_url = $manifest_data['data'];

                // Download the ZIP file
                $zip_file = $kit_dir . 'kit.zip';
                $downloaded = $this->download_file($zip_url, $zip_file);

                if ($downloaded) {
                    // Extract the ZIP file
                    $extracted = $this->extract_kit_zip($zip_file, $kit_dir);

                    if ($extracted) {
                        // Delete the ZIP file after extraction
                        @unlink($zip_file);

                        // Process manifest and template files
                        $this->process_extracted_kit_manifest($kit_dir);
                        $this->process_template_json_files($kit_dir);
                        $this->process_nav_menu_json($kit_dir);

                        // Save meta information
                        $this->write_cache_meta($meta_file);

                        // Return kit directory path
                        return [
                            'success' => true,
                            'kit_dir' => $kit_dir,
                            'manifest' => $this->get_kit_manifest($kit_id)
                        ];
                    }
                }
            }
        } else {
            // Cache is valid, return existing data
            return [
                'success' => true,
                'kit_dir' => $kit_dir,
                'manifest' => $this->get_kit_manifest($kit_id)
            ];
        }

        return false;
    }

    /**
     * Download a file from URL
     * @param string $url The URL to download from
     * @param string $destination The local file path to save to
     * @return bool Success or failure
     */
    private function download_file($url, $destination) {
        // Use WordPress download function
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        $tmp_file = download_url($url, 300); // 5 minutes timeout

        if (is_wp_error($tmp_file)) {
            // Try alternative method
            $response = wp_remote_get($url, [
                'timeout' => 300,
                'sslverify' => false,
                'headers' => [
                    'User-Agent' => 'Master Addons Kit Downloader/' . JLTMA_VER
                ]
            ]);

            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                return false;
            }

            $file_data = wp_remote_retrieve_body($response);
            return file_put_contents($destination, $file_data) !== false;
        }

        // Move temp file to destination
        $moved = rename($tmp_file, $destination);

        // Clean up temp file if move failed
        if (!$moved && file_exists($tmp_file)) {
            @unlink($tmp_file);
        }

        return $moved;
    }

    /**
     * Extract kit ZIP file
     * @param string $zip_file Path to ZIP file
     * @param string $destination Extraction destination
     * @return bool Success or failure
     */
    private function extract_kit_zip($zip_file, $destination) {
        if (!file_exists($zip_file)) {
            return false;
        }

        // Use WordPress unzip function
        WP_Filesystem();
        $result = unzip_file($zip_file, $destination);

        if (is_wp_error($result)) {
            // Try PHP ZipArchive as fallback
            if (class_exists('ZipArchive')) {
                $zip = new \ZipArchive();
                if ($zip->open($zip_file) === true) {
                    $zip->extractTo($destination);
                    $zip->close();

                    // Process manifest after extraction
                    $this->process_extracted_kit_manifest($destination);
                    return true;
                }
            }
            return false;
        }

        // Process manifest after successful extraction
        $this->process_extracted_kit_manifest($destination);

        // Process template JSON files
        $this->process_template_json_files($destination);

        // Process nav_menu.json
        $this->process_nav_menu_json($destination);

        return true;
    }

    /**
     * Process extracted kit manifest to update all image URLs to local paths
     * @param string $kit_dir Path to the extracted kit directory
     * @return bool Success status
     */
    private function process_extracted_kit_manifest($kit_dir) {
        $manifest_file = $kit_dir . '/manifest.json';

        if (!file_exists($manifest_file)) {
            return false;
        }

        // Read the manifest
        $content = file_get_contents($manifest_file);
        $manifest = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($manifest)) {
            return false;
        }

        // Extract kit_id from directory name or manifest
        $kit_id = basename($kit_dir);
        if (isset($manifest['kit_id'])) {
            $kit_id = $manifest['kit_id'];
        }

        $updated = false;

        // Process main kit thumbnail if exists
        if (isset($manifest['thumbnail'])) {
            $local_url = $this->process_and_cache_manifest_image($manifest['thumbnail'], $kit_dir, $kit_id, 'kit-main');
            if ($local_url) {
                $manifest['thumbnail'] = $local_url;
                $updated = true;
            }
        }

        // Process thumbnail_url if exists
        if (isset($manifest['thumbnail_url'])) {
            $local_url = $this->process_and_cache_manifest_image($manifest['thumbnail_url'], $kit_dir, $kit_id, 'kit-thumb');
            if ($local_url) {
                $manifest['thumbnail_url'] = $local_url;
                $updated = true;
            }
        }

        // Process preview_url if exists
        if (isset($manifest['preview_url'])) {
            // Preview URLs are usually live sites, so we might not want to cache them
            // But if it's an image URL, we can cache it
            if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $manifest['preview_url'])) {
                $local_url = $this->process_and_cache_manifest_image($manifest['preview_url'], $kit_dir, $kit_id, 'kit-preview');
                if ($local_url) {
                    $manifest['preview_url'] = $local_url;
                    $updated = true;
                }
            }
        }

        // Process templates in manifest
        if (isset($manifest['templates']) && is_array($manifest['templates'])) {
            foreach ($manifest['templates'] as &$template) {
                $template_id = $template['template_id'] ?? $template['id'] ?? uniqid();

                // Process screenshot
                if (isset($template['screenshot'])) {
                    $local_url = $this->process_and_cache_manifest_image(
                        $template['screenshot'],
                        $kit_dir,
                        $kit_id,
                        "template-{$template_id}-screenshot"
                    );
                    if ($local_url) {
                        $template['screenshot'] = $local_url;
                        $updated = true;
                    }
                }

                // Process thumbnail
                if (isset($template['thumbnail'])) {
                    $local_url = $this->process_and_cache_manifest_image(
                        $template['thumbnail'],
                        $kit_dir,
                        $kit_id,
                        "template-{$template_id}-thumb"
                    );
                    if ($local_url) {
                        $template['thumbnail'] = $local_url;
                        $updated = true;
                    }
                }

                // Process preview_url
                if (isset($template['preview_url']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $template['preview_url'])) {
                    $local_url = $this->process_and_cache_manifest_image(
                        $template['preview_url'],
                        $kit_dir,
                        $kit_id,
                        "template-{$template_id}-preview"
                    );
                    if ($local_url) {
                        $template['preview_url'] = $local_url;
                        $updated = true;
                    }
                }
            }
        }

        // Process pages in manifest (alternative structure)
        if (isset($manifest['pages']) && is_array($manifest['pages'])) {
            foreach ($manifest['pages'] as &$page) {
                $page_id = $page['page_id'] ?? $page['id'] ?? uniqid();

                // Process screenshot
                if (isset($page['screenshot'])) {
                    $local_url = $this->process_and_cache_manifest_image(
                        $page['screenshot'],
                        $kit_dir,
                        $kit_id,
                        "page-{$page_id}-screenshot"
                    );
                    if ($local_url) {
                        $page['screenshot'] = $local_url;
                        $updated = true;
                    }
                }

                // Process thumbnail
                if (isset($page['thumbnail'])) {
                    $local_url = $this->process_and_cache_manifest_image(
                        $page['thumbnail'],
                        $kit_dir,
                        $kit_id,
                        "page-{$page_id}-thumb"
                    );
                    if ($local_url) {
                        $page['thumbnail'] = $local_url;
                        $updated = true;
                    }
                }

                // Process preview_url
                if (isset($page['preview_url']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $page['preview_url'])) {
                    $local_url = $this->process_and_cache_manifest_image(
                        $page['preview_url'],
                        $kit_dir,
                        $kit_id,
                        "page-{$page_id}-preview"
                    );
                    if ($local_url) {
                        $page['preview_url'] = $local_url;
                        $updated = true;
                    }
                }
            }
        }

        // Process images array in manifest
        if (isset($manifest['images']) && is_array($manifest['images'])) {
            foreach ($manifest['images'] as &$image) {
                // Process thumbnail_url
                if (isset($image['thumbnail_url'])) {
                    $filename = $image['filename'] ?? uniqid();
                    $local_url = $this->process_and_cache_manifest_image(
                        $image['thumbnail_url'],
                        $kit_dir,
                        $kit_id,
                        "image-" . pathinfo($filename, PATHINFO_FILENAME)
                    );
                    if ($local_url) {
                        $image['thumbnail_url'] = $local_url;
                        $updated = true;
                    }
                }

                // Process image_urls if it contains URLs
                if (isset($image['image_urls']) && !empty($image['image_urls'])) {
                    if (filter_var($image['image_urls'], FILTER_VALIDATE_URL)) {
                        $filename = $image['filename'] ?? uniqid();
                        $local_url = $this->process_and_cache_manifest_image(
                            $image['image_urls'],
                            $kit_dir,
                            $kit_id,
                            "image-url-" . pathinfo($filename, PATHINFO_FILENAME)
                        );
                        if ($local_url) {
                            $image['image_urls'] = $local_url;
                            $updated = true;
                        }
                    }
                }
            }
        }

        // If we made updates, save the manifest back
        if ($updated) {
            $json = wp_json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents($manifest_file, $json);
        }

        return true;
    }

    /**
     * Process and cache an image from manifest
     * @param string $image_url The image URL (can be relative or absolute)
     * @param string $kit_dir The kit directory path
     * @param string $kit_id The kit ID
     * @param string $filename_prefix Prefix for the cached file
     * @return string|false Local URL or false on failure
     */
    private function process_and_cache_manifest_image($image_url, $kit_dir, $kit_id, $filename_prefix) {
        if (empty($image_url)) {
            return false;
        }

        // Check if it's a relative path to screenshots folder
        if (strpos($image_url, 'screenshots/') === 0) {
            // It's already a local path in the kit directory
            $local_file = rtrim($kit_dir, '/') . '/' . $image_url; // Ensure no double slashes
            if (file_exists($local_file)) {
                // Convert to URL
                $upload_dir = wp_upload_dir();
                $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
                // Clean up any double slashes in the path (but preserve protocol ://)
                $url = $upload_dir['baseurl'] . $relative_path;
                $url = preg_replace('#(?<!:)//+#', '/', $url);
                return $url;
            }
        }

        // If it's a full URL, download and cache it
        if (filter_var($image_url, FILTER_VALIDATE_URL)) {
            // Check if it's already a local URL pointing to our cache
            $upload_dir = wp_upload_dir();
            if (strpos($image_url, $upload_dir['baseurl']) === 0) {
                // Clean up any double slashes (but preserve protocol ://)
                return preg_replace('#(?<!:)//+#', '/', $image_url);
            }

            // Determine if this is a purchased kit
            $is_purchased = strpos($kit_dir, '/purchased_kits/') !== false;

            // Download and cache remote image - using proper directory
            if ($is_purchased) {
                // For purchased kits, save to purchased_kits/images folder
                $cache_base = $this->purchased_dir;
                $folder = 'images';
                $local_file = $cache_base . "{$folder}/{$filename_prefix}.jpg";

                // Skip if already exists
                if (file_exists($local_file)) {
                    $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
                    return $upload_dir['baseurl'] . $relative_path;
                }

                // Ensure folder exists
                if (!file_exists($cache_base . $folder)) {
                    wp_mkdir_p($cache_base . $folder);
                }

                // Download image
                $response = wp_remote_get($image_url, [
                    'timeout' => 30,
                    'sslverify' => false
                ]);

                if (!is_wp_error($response)) {
                    $image_data = wp_remote_retrieve_body($response);
                    if (file_put_contents($local_file, $image_data)) {
                        $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
                        return $upload_dir['baseurl'] . $relative_path;
                    }
                }
            } else {
                // For regular kits, use existing cache_image method
                $cached_url = $this->cache_image($image_url, $filename_prefix, 'thumbnails');
                if ($cached_url) {
                    return $cached_url;
                }
            }
        }

        // Check if image exists in screenshots folder and return its URL
        $screenshots_dir = rtrim($kit_dir, '/') . '/screenshots/';
        if (file_exists($screenshots_dir)) {
            // Extract filename from URL
            $filename = basename(parse_url($image_url, PHP_URL_PATH));
            $local_file = $screenshots_dir . $filename;

            if (file_exists($local_file)) {
                // Convert to URL
                $upload_dir = wp_upload_dir();
                $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
                $url = $upload_dir['baseurl'] . $relative_path;
                return preg_replace('#(?<!:)//+#', '/', $url);
            }
        }

        return false;
    }

    /**
     * Process all template JSON files in the kit to replace remote URLs
     * @param string $kit_dir The kit directory path
     */
    private function process_template_json_files($kit_dir) {
        $templates_dir = rtrim($kit_dir, '/') . '/templates/';

        if (!file_exists($templates_dir)) {
            return;
        }

        // Get all JSON files in templates directory
        $json_files = glob($templates_dir . '*.json');

        foreach ($json_files as $json_file) {
            $this->process_single_template_json($json_file, $kit_dir);
        }
    }

    /**
     * Process a single template JSON file to replace remote image URLs
     * @param string $json_file Path to the JSON file
     * @param string $kit_dir The kit directory path
     */
    private function process_single_template_json($json_file, $kit_dir) {
        if (!file_exists($json_file)) {
            return;
        }

        $content = file_get_contents($json_file);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return;
        }

        $kit_id = basename($kit_dir);
        $template_name = basename($json_file, '.json');
        $updated = false;

        // Process the content recursively
        $processed_data = $this->process_elementor_data_for_images($data, $kit_id, $template_name, $kit_dir);

        if ($processed_data !== $data) {
            // Save the updated JSON
            $json = wp_json_encode($processed_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents($json_file, $json);
        }
    }

    /**
     * Process Elementor data recursively to replace image URLs
     * @param mixed $data The data to process
     * @param string $kit_id The kit ID
     * @param string $context Context for filename generation
     * @param string $kit_dir The kit directory path
     * @return mixed Processed data
     */
    private function process_elementor_data_for_images($data, $kit_id, $context, $kit_dir = '') {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => &$value) {
            // Check for image-related fields
            if ($key === 'url' && is_string($value) && $this->is_image_url($value)) {
                // Process image URL
                $local_url = $this->download_and_replace_image($value, $kit_id, $context, $kit_dir);
                if ($local_url) {
                    $value = $local_url;
                }
            } elseif (is_array($value)) {
                // Handle specific image structures
                if (isset($value['url']) && $this->is_image_url($value['url'])) {
                    $local_url = $this->download_and_replace_image($value['url'], $kit_id, $context, $kit_dir);
                    if ($local_url) {
                        $value['url'] = $local_url;
                    }
                }

                // Recursively process nested arrays
                $value = $this->process_elementor_data_for_images($value, $kit_id, $context, $kit_dir);
            }
        }

        return $data;
    }

    /**
     * Check if a URL is an image URL
     * @param string $url The URL to check
     * @return bool
     */
    private function is_image_url($url) {
        if (!is_string($url)) {
            return false;
        }

        // Check for image extensions
        if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)(\?.*)?$/i', $url)) {
            return true;
        }

        // Check for WordPress uploads
        if (strpos($url, '/wp-content/uploads/') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Download and replace an image URL with local cached version
     * @param string $image_url The image URL
     * @param string $kit_id The kit ID
     * @param string $context Context for filename
     * @param string $kit_dir Optional kit directory to determine if purchased
     * @return string|false Local URL or false on failure
     */
    private function download_and_replace_image($image_url, $kit_id, $context, $kit_dir = '') {
        if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check if it's already a local URL
        $upload_dir = wp_upload_dir();
        if (strpos($image_url, $upload_dir['baseurl']) === 0) {
            // Clean up any double slashes
            return preg_replace('#(?<!:)//+#', '/', $image_url);
        }

        // Determine if this is a purchased kit
        $is_purchased = false;
        if (!empty($kit_dir) && strpos($kit_dir, '/purchased_kits/') !== false) {
            $is_purchased = true;
        }

        // Generate filename
        $url_hash = md5($image_url);
        $filename = "kit-{$kit_id}-{$context}-{$url_hash}";

        // Download and cache the image (it will use the correct directory based on is_purchased)
        $cache_base = $is_purchased ? $this->purchased_dir : $this->cache_dir;
        $folder = 'images';
        $local_file = $cache_base . "{$folder}/{$filename}.jpg";

        // Skip downloading if already cached
        if (file_exists($local_file)) {
            $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
            return $upload_dir['baseurl'] . $relative_path;
        }

        // Ensure the folder exists
        $folder_path = $cache_base . $folder;
        if (!file_exists($folder_path)) {
            wp_mkdir_p($folder_path);
        }

        // Download the image
        $response = wp_remote_get($image_url, [
            'timeout' => 30,
            'sslverify' => false
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $image_data = wp_remote_retrieve_body($response);

        // Detect actual extension from content
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_buffer($finfo, $image_data);
        finfo_close($finfo);

        $extension = 'jpg';
        if ($mime_type === 'image/png') $extension = 'png';
        elseif ($mime_type === 'image/gif') $extension = 'gif';
        elseif ($mime_type === 'image/webp') $extension = 'webp';

        $local_file = $cache_base . "{$folder}/{$filename}.{$extension}";

        if (file_put_contents($local_file, $image_data)) {
            $relative_path = str_replace($upload_dir['basedir'], '', $local_file);
            return $upload_dir['baseurl'] . $relative_path;
        }

        return false;
    }

    /**
     * Process nav_menu.json to remove remote site URLs
     * @param string $kit_dir The kit directory path
     */
    private function process_nav_menu_json($kit_dir) {
        $nav_menu_file = rtrim($kit_dir, '/') . '/nav_menu.json';

        if (!file_exists($nav_menu_file)) {
            return;
        }

        $content = file_get_contents($nav_menu_file);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return;
        }

        $updated = false;

        // Replace site_url with placeholder
        if (isset($data['site_url'])) {
            $data['site_url'] = '{{SITE_URL}}';
            $updated = true;
        }

        // Process menu items to replace URLs
        if (isset($data['menus']) && is_array($data['menus'])) {
            foreach ($data['menus'] as &$menu) {
                if (isset($menu['items']) && is_array($menu['items'])) {
                    foreach ($menu['items'] as &$item) {
                        if (isset($item['url']) && filter_var($item['url'], FILTER_VALIDATE_URL)) {
                            // Replace absolute URLs with relative or placeholder
                            $parsed = parse_url($item['url']);
                            if (isset($parsed['path'])) {
                                // Use relative URL
                                $item['url'] = $parsed['path'];
                                if (isset($parsed['query'])) {
                                    $item['url'] .= '?' . $parsed['query'];
                                }
                                if (isset($parsed['fragment'])) {
                                    $item['url'] .= '#' . $parsed['fragment'];
                                }
                                $updated = true;
                            }
                        }
                    }
                }
            }
        }

        if ($updated) {
            // Save the updated nav_menu.json
            $json = wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents($nav_menu_file, $json);
        }
    }

    /**
     * Get local screenshot URL for a template
     * @param string $kit_dir Kit directory path
     * @param array $template Template data
     * @param string $type Type of image (thumbnail or preview)
     * @return string|false Local URL or false if not found
     */
    private function get_local_screenshot_url($kit_dir, $template, $type = 'thumbnail') {
        // Check for screenshots folder
        $screenshots_dir = $kit_dir . '/screenshots/';
        if (!file_exists($screenshots_dir)) {
            return false;
        }

        // Try to find the screenshot file
        $template_slug = $template['slug'] ?? $template['id'] ?? '';
        if (empty($template_slug)) {
            return false;
        }

        // Common image extensions
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Possible filename patterns
        $patterns = [
            $template_slug,
            $template_slug . '-' . $type,
            $type . '-' . $template_slug,
            str_replace('_', '-', $template_slug),
            str_replace('-', '_', $template_slug)
        ];

        foreach ($patterns as $pattern) {
            foreach ($extensions as $ext) {
                $screenshot_file = $screenshots_dir . $pattern . '.' . $ext;
                if (file_exists($screenshot_file)) {
                    // Convert to URL
                    $upload_dir = wp_upload_dir();
                    $relative_path = str_replace($upload_dir['basedir'], '', $screenshot_file);
                    return $upload_dir['baseurl'] . $relative_path;
                }
            }
        }

        // If type is thumbnail, also check without suffix
        if ($type === 'thumbnail') {
            foreach ($extensions as $ext) {
                $screenshot_file = $screenshots_dir . $template_slug . '.' . $ext;
                if (file_exists($screenshot_file)) {
                    // Convert to URL
                    $upload_dir = wp_upload_dir();
                    $relative_path = str_replace($upload_dir['basedir'], '', $screenshot_file);
                    return $upload_dir['baseurl'] . $relative_path;
                }
            }
        }

        return false;
    }

    /**
     * Get kit manifest from cache or purchased kits
     * @param string $kit_id The kit ID
     * @return array|false Manifest data or false
     */
    public function get_kit_manifest($kit_id) {
        // OPTIMIZATION: Use static cache for manifests
        static $manifest_cache = [];

        if (isset($manifest_cache[$kit_id])) {
            return $manifest_cache[$kit_id];
        }

        // First check if this is a purchased kit
        if ($this->is_kit_purchased($kit_id)) {
            // Check if manifest is already in purchased kits data
            $purchased_kits = $this->get_purchased_kits();
            foreach ($purchased_kits as $pk) {
                if (($pk['kit_id'] ?? '') === $kit_id && isset($pk['manifest'])) {
                    $manifest_cache[$kit_id] = $pk['manifest'];
                    $manifest_cache[$kit_id]['is_purchased'] = true;
                    return $manifest_cache[$kit_id];
                }
            }

            // First try to read the actual manifest.json file from purchased kit directory
            $purchased_kit_dir = $this->purchased_dir . 'kits/kit_' . sanitize_file_name($kit_id) . '/';
            $manifest_file = $purchased_kit_dir . 'manifest.json';

            if (file_exists($manifest_file)) {
                $content = file_get_contents($manifest_file);
                $data = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Add is_purchased flag
                    $data['is_purchased'] = true;
                    $manifest_cache[$kit_id] = $data;
                    return $data;
                }
            }

            // If no manifest file, try to get from stored data
            $purchased_kit = $this->get_purchased_kit($kit_id);
            if ($purchased_kit) {
                // Return manifest if available
                if (isset($purchased_kit['manifest'])) {
                    $purchased_kit['manifest']['is_purchased'] = true;
                    return $purchased_kit['manifest'];
                }

                // Check if there's a kit_path with manifest
                if (isset($purchased_kit['kit_path'])) {
                    $manifest_file = $purchased_kit['kit_path'] . '/manifest.json';
                    if (file_exists($manifest_file)) {
                        $content = file_get_contents($manifest_file);
                        $data = json_decode($content, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $data['is_purchased'] = true;
                            return $data;
                        }
                    }
                }

                // Build manifest from purchased kit data
                return [
                    'name' => $purchased_kit['kit_name'] ?? $purchased_kit['title'] ?? '',
                    'title' => $purchased_kit['kit_name'] ?? $purchased_kit['title'] ?? '',
                    'description' => $purchased_kit['description'] ?? '',
                    'author' => $purchased_kit['author'] ?? '',
                    'pages' => $purchased_kit['templates'] ?? [],
                    'templates' => $purchased_kit['templates'] ?? [],
                    'kit_id' => $kit_id,
                    'is_purchased' => true,
                    'required_plugins' => $purchased_kit['required_plugins'] ?? [],
                    'requirements' => $purchased_kit['required_plugins'] ?? [] // Also include as requirements for compatibility
                ];
            }
        }

        // Check regular cache
        $kit_dir = $this->cache_dir . 'kits/' . sanitize_file_name($kit_id) . '/';
        $manifest_file = $kit_dir . 'manifest.json';

        if (file_exists($manifest_file)) {
            $content = file_get_contents($manifest_file);
            $data = json_decode($content, true);
            return json_last_error() === JSON_ERROR_NONE ? $data : false;
        }

        return false;
    }

    /**
     * Get kit template JSON from cache or purchased kits
     * @param string $kit_id The kit ID
     * @param string $template_name The template name/slug
     * @return array|false Template data or false
     */
    public function get_kit_template($kit_id, $template_name) {
        // First check if this is a purchased/uploaded kit
        if ($this->is_kit_purchased($kit_id)) {
            $purchased_kit = $this->get_purchased_kit($kit_id);
            if ($purchased_kit && isset($purchased_kit['templates'])) {
                foreach ($purchased_kit['templates'] as $template) {
                    if ($template['slug'] === $template_name || $template['id'] === $template_name) {
                        return $template;
                    }
                }
            }

            // If purchased kit has a path, check the file system
            if (isset($purchased_kit['kit_path'])) {
                $template_file = $purchased_kit['kit_path'] . '/' . sanitize_file_name($template_name) . '.json';
                if (file_exists($template_file)) {
                    $content = file_get_contents($template_file);
                    $data = json_decode($content, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $data;
                    }
                }
            }

            // Check purchased kits directory
            $purchased_kit_dir = $this->purchased_dir . 'kits/' . sanitize_file_name($kit_id) . '/';
            $template_file = $purchased_kit_dir . sanitize_file_name($template_name) . '.json';
            if (file_exists($template_file)) {
                $content = file_get_contents($template_file);
                $data = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $data;
                }
            }
        }

        // Check regular cache
        $kit_dir = $this->cache_dir . 'kits/' . sanitize_file_name($kit_id) . '/';
        $template_file = $kit_dir . sanitize_file_name($template_name) . '.json';

        if (file_exists($template_file)) {
            $content = file_get_contents($template_file);
            $data = json_decode($content, true);
            return json_last_error() === JSON_ERROR_NONE ? $data : false;
        }

        return false;
    }

    /**
     * Check if kit is cached or purchased
     * @param string $kit_id The kit ID
     * @return bool
     */
    public function is_kit_cached($kit_id) {
        // First check if it's a purchased kit
        if ($this->is_kit_purchased($kit_id)) {
            return true;
        }

        // Check regular cache
        $kit_dir = $this->cache_dir . 'kits/' . sanitize_file_name($kit_id) . '/';
        $meta_file = $kit_dir . 'meta.json';

        return $this->is_cache_valid($meta_file);
    }

    /**
     * Clear kit cache
     * @param string $kit_id The kit ID (optional, clears all if not provided)
     * @return bool
     */
    public function clear_kit_cache($kit_id = null) {
        if ($kit_id) {
            // Clear specific kit
            $kit_dir = $this->cache_dir . 'kits/' . sanitize_file_name($kit_id) . '/';
            if (file_exists($kit_dir)) {
                $this->delete_directory_contents($kit_dir);
                return rmdir($kit_dir);
            }
        } else {
            // Clear all kits
            $kits_dir = $this->cache_dir . 'kits/';
            if (file_exists($kits_dir)) {
                $this->delete_directory_contents($kits_dir);
                return true;
            }
        }

        return false;
    }

    /**
     * Maybe download kit if not already cached
     * @param string $kit_id The kit ID
     * @return bool
     */
    private function maybe_download_kit($kit_id) {
        // Check if kit is already cached
        if ($this->is_kit_cached($kit_id)) {
            return true;
        }

        // Download the kit in background
        $result = $this->download_and_cache_kit($kit_id);
        return $result && $result['success'];
    }

    /**
     * Download all available kits
     * @return array Download statistics
     */
    public function download_all_kits() {
        $stats = [
            'downloaded' => 0,
            'failed' => 0,
            'skipped' => 0,
            'total' => 0
        ];

        // Get all kits from cache or API
        $all_kits = $this->get_cached_kits(false, 'all');

        if (!is_array($all_kits)) {
            return $stats;
        }

        // Process each category
        foreach ($all_kits as $category => $kits) {
            if (!is_array($kits)) {
                continue;
            }

            foreach ($kits as $kit) {
                if (!isset($kit['kit_id'])) {
                    continue;
                }

                $stats['total']++;

                // Check if already cached
                if ($this->is_kit_cached($kit['kit_id'])) {
                    $stats['skipped']++;
                    continue;
                }

                // Try to download
                $result = $this->download_and_cache_kit($kit['kit_id']);

                if ($result && $result['success']) {
                    $stats['downloaded']++;
                } else {
                    $stats['failed']++;
                }

                // Add a small delay to avoid overwhelming the server
                if ($stats['downloaded'] % 5 === 0) {
                    sleep(1);
                }
            }
        }

        return $stats;
    }
    
    /**
     * Cache all images from a template kit
     */
    public function cache_kit_all_images($kit_id, $templates = null) {
        if (!$templates) {
            // Try to get templates from cache
            $cached_data = $this->get_cached_kit_content($kit_id, 'all', false);
            if (!$cached_data) {
                return false;
            }
            $templates = $cached_data;
        }
        
        $image_count = 0;
        
        // Process each template
        foreach ($templates as $template) {
            if (isset($template['thumbnail'])) {
                $this->cache_image($template['thumbnail'], "kit-{$kit_id}-thumb-{$template['id']}", 'thumbnails');
                $image_count++;
            }
            
            if (isset($template['content'])) {
                // Count images in content
                $image_count += $this->count_and_cache_content_images($template['content'], $kit_id);
            }
        }
        
        return $image_count;
    }
    
    /**
     * Count and cache images in content
     */
    private function count_and_cache_content_images($content, $kit_id) {
        if (is_string($content)) {
            $content = json_decode($content, true);
        }
        
        if (!is_array($content)) {
            return 0;
        }
        
        $count = 0;
        
        // Process the content to find all image URLs
        $image_urls = $this->extract_image_urls_from_content($content);
        
        foreach ($image_urls as $url) {
            $url_hash = md5($url);
            $filename = "kit-{$kit_id}-content-{$url_hash}";
            if ($this->cache_image($url, $filename, 'images')) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Extract all image URLs from Elementor content
     */
    private function extract_image_urls_from_content($content, &$urls = []) {
        if (!is_array($content)) {
            return $urls;
        }
        
        foreach ($content as $element) {
            if (isset($element['settings']) && is_array($element['settings'])) {
                $this->extract_image_urls_from_settings($element['settings'], $urls);
            }
            
            if (isset($element['elements']) && is_array($element['elements'])) {
                $this->extract_image_urls_from_content($element['elements'], $urls);
            }
        }
        
        return array_unique($urls);
    }
    
    /**
     * Extract image URLs from element settings
     */
    private function extract_image_urls_from_settings($settings, &$urls) {
        if (!is_array($settings)) {
            return;
        }
        
        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                // Check if it's an image array with URL
                if (isset($value['url']) && filter_var($value['url'], FILTER_VALIDATE_URL)) {
                    $urls[] = $value['url'];
                }
                // Recursively check nested arrays
                $this->extract_image_urls_from_settings($value, $urls);
            } elseif (is_string($value)) {
                // Check if the string contains image URLs
                if (preg_match_all('/(https?:\/\/[^\s"]+\.(?:jpg|jpeg|png|gif|svg|webp))/i', $value, $matches)) {
                    $urls = array_merge($urls, $matches[1]);
                }
            }
        }
    }

    /**
     * Store a purchased template kit permanently
     * @param array $kit_data The kit data to store
     * @return bool Success status
     */
    public function store_purchased_kit($kit_data) {
        if (!file_exists($this->purchased_dir)) {
            wp_mkdir_p($this->purchased_dir);

            // Create subdirectories
            $subdirs = ['kits', 'manifests', 'thumbnails', 'metadata'];
            foreach ($subdirs as $subdir) {
                $subdir_path = $this->purchased_dir . $subdir . '/';
                if (!file_exists($subdir_path)) {
                    wp_mkdir_p($subdir_path);
                }
            }
        }

        // Extract kit ID and normalize it
        $kit_id = $kit_data['kit_id'] ?? $kit_data['template_id'] ?? '';
        if (empty($kit_id)) {
            return false;
        }

        // If kit_path is not provided but we have a kit_id, check if it exists in purchased_kits
        if (!isset($kit_data['kit_path']) && !empty($kit_id)) {
            $potential_path = $this->purchased_dir . 'kits/kit_' . sanitize_file_name($kit_id) . '/';
            if (file_exists($potential_path)) {
                $kit_data['kit_path'] = $potential_path;
            }
        }

        // Process manifest and templates to update URLs if kit_path is provided
        if (isset($kit_data['kit_path']) && file_exists($kit_data['kit_path'])) {
            $this->process_extracted_kit_manifest($kit_data['kit_path']);
            $this->process_template_json_files($kit_data['kit_path']);
            $this->process_nav_menu_json($kit_data['kit_path']);
            
            // Re-read the manifest after processing
            $manifest_file = $kit_data['kit_path'] . '/manifest.json';
            if (file_exists($manifest_file)) {
                $content = file_get_contents($manifest_file);
                $manifest = json_decode($content, true);
                if ($manifest) {
                    $kit_data['manifest'] = $manifest;

                    // Update templates from processed manifest
                    if (isset($manifest['templates'])) {
                        $kit_data['templates'] = $manifest['templates'];
                    } elseif (isset($manifest['pages'])) {
                        $kit_data['templates'] = $manifest['pages'];
                    }
                }
            }
        }

        // IMPORTANT: Process URLs FIRST before creating metadata to ensure NO remote URLs are saved
        $processed_kit_data = $this->process_kit_data_urls($kit_data, $kit_id);

        // Store kit metadata with same structure as cached kits - using PROCESSED data
        $metadata = [
            'kit_id' => $kit_id,
            'kit_name' => $processed_kit_data['kit_name'] ?? $processed_kit_data['title'] ?? '',
            'purchased_date' => current_time('mysql'),
            'is_purchased' => true,
            'purchasable' => false,
            'downloadable' => true,
            'is_pro' => false, // No restrictions for purchased templates
            'categories' => $processed_kit_data['categories'] ?? ['purchased'],
            'keywords' => $processed_kit_data['keywords'] ?? [],
            'thumbnail' => $processed_kit_data['thumbnail'] ?? '', // FIXED: Use processed thumbnail (local URL)
            'preview_url' => $processed_kit_data['preview_url'] ?? '', // FIXED: Use processed preview URL
            'descriptions' => $processed_kit_data['descriptions'] ?? $processed_kit_data['description'] ?? '',
            'downloads' => $processed_kit_data['downloads'] ?? 0,
            'purchase_url' => '', // Empty since already purchased
            'template_count' => isset($processed_kit_data['templates']) ? count($processed_kit_data['templates']) : 1,
            'required_plugins' => $processed_kit_data['required_plugins'] ?? [], // Store required plugins
            'kit_path' => $processed_kit_data['kit_path'] ?? null // Store the path if available
        ];

        // Update metadata with processed manifest data if available
        if (isset($processed_kit_data['manifest'])) {
            // Preserve the full processed manifest (with local URLs)
            $metadata['manifest'] = $processed_kit_data['manifest'];

            // Also extract required_plugins at the top level for easy access
            if (isset($processed_kit_data['manifest']['required_plugins'])) {
                $metadata['required_plugins'] = $processed_kit_data['manifest']['required_plugins'];
            }
        }

        // Save metadata with processed URLs
        $metadata_file = $this->purchased_dir . 'metadata/' . sanitize_file_name($kit_id) . '.json';
        $this->write_cache_file($metadata_file, $metadata);

        // Save full kit data if provided (already processed)
        if (isset($processed_kit_data['content']) || isset($processed_kit_data['templates']) || isset($processed_kit_data['manifest'])) {
            $kit_file = $this->purchased_dir . 'kits/' . sanitize_file_name($kit_id) . '.json';
            $this->write_cache_file($kit_file, $processed_kit_data);
        }

        return true;
    }

    /**
     * Update existing purchased kits to remove remote URLs and use local URLs
     * @return int Number of kits updated
     */
    public function update_existing_purchased_kits_urls() {
        if (!file_exists($this->purchased_dir . 'metadata/')) {
            return 0;
        }

        $updated_count = 0;
        $metadata_files = glob($this->purchased_dir . 'metadata/*.json');

        foreach ($metadata_files as $metadata_file) {
            $metadata = $this->read_cache_file($metadata_file);
            if (!$metadata) {
                continue;
            }

            $kit_id = $metadata['kit_id'] ?? '';
            if (empty($kit_id)) {
                continue;
            }

            $has_remote_urls = false;

            // Check if metadata has remote URLs
            if (isset($metadata['thumbnail']) && strpos($metadata['thumbnail'], 'http') === 0) {
                $has_remote_urls = true;
            }
            if (isset($metadata['preview_url']) && strpos($metadata['preview_url'], 'http') === 0) {
                $has_remote_urls = true;
            }
            if (isset($metadata['manifest']['thumbnail']) && strpos($metadata['manifest']['thumbnail'], 'http') === 0) {
                $has_remote_urls = true;
            }
            if (isset($metadata['manifest']['thumbnail_url']) && strpos($metadata['manifest']['thumbnail_url'], 'http') === 0) {
                $has_remote_urls = true;
            }

            if ($has_remote_urls) {
                // Process the metadata to convert remote URLs to local
                $processed_metadata = $this->process_kit_data_urls($metadata, $kit_id);

                // Save the updated metadata
                $this->write_cache_file($metadata_file, $processed_metadata);

                // Also update the kit data file if it exists
                $kit_file = $this->purchased_dir . 'kits/' . sanitize_file_name($kit_id) . '.json';
                if (file_exists($kit_file)) {
                    $kit_data = $this->read_cache_file($kit_file);
                    if ($kit_data) {
                        $processed_kit_data = $this->process_kit_data_urls($kit_data, $kit_id);
                        $this->write_cache_file($kit_file, $processed_kit_data);
                    }
                }

                $updated_count++;
            }
        }

        return $updated_count;
    }

    /**
     * Get all purchased kits
     * @return array Array of purchased kits
     */
    public function get_purchased_kits() {
        static $cached_purchased_kits = null;

        if ($cached_purchased_kits !== null) {
            return $cached_purchased_kits;
        }

        if (!file_exists($this->purchased_dir . 'metadata/')) {
            $cached_purchased_kits = [];
            return [];
        }

        $purchased_kits = [];
        $metadata_files = glob($this->purchased_dir . 'metadata/*.json');

        // OPTIMIZATION: Batch read all metadata files
        foreach ($metadata_files as $file) {
            $metadata = $this->read_cache_file($file);
            if ($metadata) {
                // Pre-load manifest data if available in the same directory
                $kit_id = $metadata['kit_id'] ?? '';
                if ($kit_id) {
                    // Check if manifest.json exists in the kit directory
                    $manifest_file = $this->purchased_dir . 'kits/kit_' . $kit_id . '/manifest.json';
                    if (file_exists($manifest_file) && !isset($metadata['manifest'])) {
                        $manifest_content = @file_get_contents($manifest_file);
                        if ($manifest_content) {
                            $manifest_data = json_decode($manifest_content, true);
                            if ($manifest_data) {
                                $metadata['manifest'] = $manifest_data;
                            }
                        }
                    }
                }
                $purchased_kits[] = $metadata;
            }
        }

        $cached_purchased_kits = $purchased_kits;
        return $purchased_kits;
    }

    /**
     * Check if a kit is purchased
     * @param string $kit_id The kit ID to check
     * @return bool
     */
    public function is_kit_purchased($kit_id) {
        // Check with exact kit_id
        $metadata_file = $this->purchased_dir . 'metadata/' . sanitize_file_name($kit_id) . '.json';
        if (file_exists($metadata_file)) {
            return true;
        }

        // Also check with kit_ prefix format for compatibility
        $metadata_file_alt = $this->purchased_dir . 'metadata/kit_' . sanitize_file_name($kit_id) . '.json';
        if (file_exists($metadata_file_alt)) {
            return true;
        }

        // Check if the kit folder exists directly
        $kit_dir = $this->purchased_dir . 'kits/kit_' . sanitize_file_name($kit_id) . '/';
        return file_exists($kit_dir);
    }

    /**
     * Get purchased kit data
     * @param string $kit_id The kit ID
     * @return array|false Kit data or false if not found
     */
    public function get_purchased_kit($kit_id) {
        // Try direct metadata file first
        $metadata_file = $this->purchased_dir . 'metadata/' . sanitize_file_name($kit_id) . '.json';
        if (file_exists($metadata_file)) {
            return $this->read_cache_file($metadata_file);
        }

        // Try with kit_ prefix
        $metadata_file_alt = $this->purchased_dir . 'metadata/kit_' . sanitize_file_name($kit_id) . '.json';
        if (file_exists($metadata_file_alt)) {
            return $this->read_cache_file($metadata_file_alt);
        }

        // Try kits directory with stored data
        $kit_file = $this->purchased_dir . 'kits/' . sanitize_file_name($kit_id) . '.json';
        if (file_exists($kit_file)) {
            return $this->read_cache_file($kit_file);
        }

        // Try kits directory with kit_ prefix
        $kit_file_alt = $this->purchased_dir . 'kits/kit_' . sanitize_file_name($kit_id) . '.json';
        if (file_exists($kit_file_alt)) {
            return $this->read_cache_file($kit_file_alt);
        }

        return false;
    }

    /**
     * Delete a purchased kit (if needed for management)
     * @param string $kit_id The kit ID to delete
     * @return bool
     */
    public function delete_purchased_kit($kit_id) {
        $deleted = false;

        // Delete metadata files (try both with and without kit_ prefix)
        $metadata_files = [
            $this->purchased_dir . 'metadata/' . sanitize_file_name($kit_id) . '.json',
            $this->purchased_dir . 'metadata/kit_' . sanitize_file_name($kit_id) . '.json'
        ];

        foreach ($metadata_files as $metadata_file) {
            if (file_exists($metadata_file)) {
                @unlink($metadata_file);
                $deleted = true;
            }
        }

        // Delete kit data files
        $kit_files = [
            $this->purchased_dir . 'kits/' . sanitize_file_name($kit_id) . '.json',
            $this->purchased_dir . 'kits/kit_' . sanitize_file_name($kit_id) . '.json'
        ];

        foreach ($kit_files as $kit_file) {
            if (file_exists($kit_file)) {
                @unlink($kit_file);
                $deleted = true;
            }
        }

        // Delete kit directory and all its contents
        $kit_directories = [
            $this->purchased_dir . 'kits/kit_' . sanitize_file_name($kit_id),
            $this->purchased_dir . 'kits/' . sanitize_file_name($kit_id)
        ];

        foreach ($kit_directories as $kit_dir) {
            if (file_exists($kit_dir) && is_dir($kit_dir)) {
                // Delete all contents recursively
                $this->delete_directory_contents($kit_dir);
                @rmdir($kit_dir);
                $deleted = true;
            }
        }

        // Clear any transient cache related to this kit
        delete_transient('jltma_kit_' . $kit_id);
        delete_transient('jltma_kit_templates_' . $kit_id);
        delete_transient('jltma_kit_manifest_' . $kit_id);

        return $deleted;
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

// Initialize template kit cache manager
JLTMA_Plugin_Template_Kit_Cache::get_instance();

// Create alias for Template Kit functionality (Site Importer)
if (!class_exists('JLTMA_Site_Importer_Cache')) {
    class_alias('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache', 'JLTMA_Site_Importer_Cache');
}