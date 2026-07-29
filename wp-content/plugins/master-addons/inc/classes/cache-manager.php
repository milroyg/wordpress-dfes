<?php

/**
 * Master Addons - Cache Manager
 *
 * Bundles per-page assets into single optimized CSS/JS files.
 * Stores cached files in /wp-content/uploads/master_addons/assets_cache/
 * with database fallback for metadata tracking.
 *
 * @package MasterAddons\Inc\Classes
 * @since 2.0.0
 * @see docs/plans/2026-01-12-vite-migration-asset-management-design.md
 */

namespace MasterAddons\Inc\Classes;

if (!defined('ABSPATH')) {
    exit;
}

class Cache_Manager
{
    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Cache directory name (inside uploads)
     */
    const CACHE_DIR = 'master_addons/assets_cache';

    /**
     * Database option for global cache metadata
     */
    const CACHE_META_OPTION = 'jltma_cache_meta';

    /**
     * Transient prefix for per-post cache info
     */
    const POST_CACHE_PREFIX = 'jltma_post_cache_';

    /**
     * Option name for cache enabled setting
     */
    const OPTION_KEY = 'jltma_cache_enabled';

    /**
     * Cache path (filesystem)
     */
    private $cache_path;

    /**
     * Cache URL
     */
    private $cache_url;

    /**
     * Get singleton instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setup_cache_directory();

        // Listen for cache invalidation
        add_action('jltma/cache/invalidate_post', [$this, 'invalidate_post_cache']);
        add_action('jltma/cache/clear_all', [$this, 'clear_all_cache']);

        // Hook into asset loading when caching enabled
        if ($this->is_enabled()) {
            add_action('wp_enqueue_scripts', [$this, 'maybe_serve_cached_bundle'], 99);
        }

        // Clear cache on theme/plugin updates
        add_action('switch_theme', [$this, 'clear_all_cache']);
        add_action('upgrader_process_complete', [$this, 'on_upgrade_complete'], 10, 2);

        // AJAX handlers for admin
        add_action('wp_ajax_jltma_clear_cache', [$this, 'ajax_clear_cache']);
        add_action('wp_ajax_jltma_regenerate_cache', [$this, 'ajax_regenerate_cache']);
        add_action('wp_ajax_jltma_get_cache_stats', [$this, 'ajax_get_cache_stats']);
        add_action('wp_ajax_jltma_clear_single_cache', [$this, 'ajax_clear_single_cache']);
        add_action('wp_ajax_jltma_regenerate_single_cache', [$this, 'ajax_regenerate_single_cache']);
        add_action('wp_ajax_jltma_save_performance_settings', [$this, 'ajax_save_performance_settings']);
    }

    /**
     * Setup cache directory in uploads
     */
    private function setup_cache_directory()
    {
        $upload_dir = wp_upload_dir();

        if (!empty($upload_dir['error']) || empty($upload_dir['basedir'])) {
            return;
        }

        $this->cache_path = $upload_dir['basedir'] . '/' . self::CACHE_DIR;
        $this->cache_url  = $upload_dir['baseurl'] . '/' . self::CACHE_DIR;

        // Create directory if it doesn't exist
        if (!file_exists($this->cache_path)) {
            wp_mkdir_p($this->cache_path);

            // Add index.php for security
            file_put_contents(
                $this->cache_path . '/index.php',
                '<?php // Silence is golden'
            );

            // Add .htaccess for gzip and caching
            $htaccess = <<<'HTACCESS'
# Master Addons Cache with Gzip Support

# Enable gzip compression for CSS and JS
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE text/javascript
</IfModule>

# Serve pre-compressed .gz files if they exist
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Check if browser accepts gzip
    RewriteCond %{HTTP:Accept-Encoding} gzip

    # Serve .css.gz for .css requests
    RewriteCond %{REQUEST_FILENAME}.gz -f
    RewriteRule ^(.+)\.(css|js)$ $1.$2.gz [L]
</IfModule>

# Set correct content types for .gz files
<IfModule mod_mime.c>
    AddType text/css .css.gz
    AddType application/javascript .js.gz
    AddEncoding gzip .gz
</IfModule>

# Cache control headers
<IfModule mod_headers.c>
    Header set Cache-Control "max-age=31536000, public"

    # Vary header for gzip
    <FilesMatch "\.(css|js)(\.gz)?$">
        Header append Vary Accept-Encoding
    </FilesMatch>

    # Content-Encoding for .gz files
    <FilesMatch "\.gz$">
        Header set Content-Encoding gzip
    </FilesMatch>
</IfModule>
HTACCESS;
            file_put_contents($this->cache_path . '/.htaccess', $htaccess);
        }
    }

    /**
     * Check if caching is enabled
     */
    public function is_enabled()
    {
        return (bool) get_option(self::OPTION_KEY, false);
    }

    /**
     * Enable caching
     */
    public static function enable()
    {
        update_option(self::OPTION_KEY, true);
    }

    /**
     * Disable caching
     */
    public static function disable()
    {
        update_option(self::OPTION_KEY, false);
    }

    /**
     * Maybe serve cached bundle for current post
     */
    public function maybe_serve_cached_bundle()
    {
        // Skip in admin or editor
        if (is_admin() || $this->is_elementor_editor()) {
            return;
        }

        $post_id = get_the_ID();
        if (!$post_id) {
            return;
        }

        // Check for existing valid cache
        $cache_info = $this->get_post_cache_info($post_id);

        if ($cache_info && $this->is_cache_valid($cache_info)) {
            $this->serve_cached_bundle($cache_info);
            return;
        }

        // Generate new cache on shutdown (non-blocking)
        add_action('shutdown', function () use ($post_id) {
            $this->generate_post_cache($post_id);
        });
    }

    /**
     * Generate bundled CSS/JS for a post
     */
    public function generate_post_cache($post_id)
    {
        $assets_loader = Assets_Loader::get_instance();
        $widgets = $assets_loader->detect_page_widgets($post_id);

        if (empty($widgets)) {
            return false;
        }

        // Generate unique hash based on widgets used
        $hash = $this->generate_cache_hash($widgets);

        // Bundle CSS
        $css_content = $this->bundle_css_files($widgets);
        $css_filename = "post-{$post_id}-{$hash}.css";

        // Bundle JS
        $js_content = $this->bundle_js_files($widgets);
        $js_filename = "post-{$post_id}-{$hash}.js";

        // Write files with gzip compression
        $css_result = ['written' => false, 'gzip_written' => false, 'gzip_size' => 0];
        $js_result = ['written' => false, 'gzip_written' => false, 'gzip_size' => 0];

        if (!empty($css_content)) {
            $css_result = $this->write_with_gzip(
                $this->cache_path . '/' . $css_filename,
                $css_content
            );
        }

        if (!empty($js_content)) {
            $js_result = $this->write_with_gzip(
                $this->cache_path . '/' . $js_filename,
                $js_content
            );
        }

        if (!$css_result['written'] && !$js_result['written']) {
            // File write failed - store in database fallback
            $this->store_in_database($post_id, $css_content, $js_content);
            return false;
        }

        // Store cache metadata
        $cache_info = [
            'hash'           => $hash,
            'css_file'       => $css_result['written'] ? $css_filename : null,
            'js_file'        => $js_result['written'] ? $js_filename : null,
            'widgets'        => $widgets,
            'created'        => time(),
            'size_css'       => strlen($css_content),
            'size_js'        => strlen($js_content),
            'gzip_size_css'  => $css_result['gzip_size'],
            'gzip_size_js'   => $js_result['gzip_size'],
            'gzip_enabled'   => $css_result['gzip_written'] || $js_result['gzip_written'],
            'is_rtl'         => is_rtl(),
        ];

        set_transient(
            self::POST_CACHE_PREFIX . $post_id,
            $cache_info,
            WEEK_IN_SECONDS
        );

        $this->update_global_cache_meta($post_id, $cache_info);

        return true;
    }

    /**
     * Bundle multiple CSS files into one
     * Handles array format from JLTMA_Config
     */
    private function bundle_css_files($widgets)
    {
        $assets_loader = Assets_Loader::get_instance();
        $widget_assets = $assets_loader->get_widget_assets();
        $is_rtl = is_rtl();

        $bundled_css = "/* Master Addons Bundled CSS - " . gmdate('Y-m-d H:i:s') . " */\n";
        $processed = [];

        foreach ($widgets as $widget_name) {
            if (!isset($widget_assets[$widget_name])) {
                continue;
            }

            // CSS is now an array
            $css_files = $widget_assets[$widget_name]['css'] ?? [];

            if (empty($css_files)) {
                continue;
            }

            foreach ((array) $css_files as $css_slug) {
                if (isset($processed[$css_slug])) {
                    continue;
                }

                $css_file = JLTMA_PATH . "assets/css/addons/{$css_slug}.css";

                // Use RTL file if site is RTL
                if ($is_rtl) {
                    $rtl_file = JLTMA_PATH . "assets/css/addons/{$css_slug}.rtl.css";
                    if (file_exists($rtl_file)) {
                        $css_file = $rtl_file;
                    }
                }

                if (file_exists($css_file)) {
                    $bundled_css .= "/* Widget: {$widget_name} ({$css_slug}) */\n";
                    $bundled_css .= file_get_contents($css_file) . "\n";
                    $processed[$css_slug] = true;
                }
            }

            // Also bundle vendor CSS
            $vendor_css = $widget_assets[$widget_name]['vendor']['css'] ?? [];
            foreach ((array) $vendor_css as $vendor_slug) {
                if (isset($processed['vendor-' . $vendor_slug])) {
                    continue;
                }

                $vendor_file = JLTMA_PATH . "assets/vendor/{$vendor_slug}/{$vendor_slug}.css";
                if (file_exists($vendor_file)) {
                    $bundled_css .= "/* Vendor: {$vendor_slug} */\n";
                    $bundled_css .= file_get_contents($vendor_file) . "\n";
                    $processed['vendor-' . $vendor_slug] = true;
                }
            }
        }

        // Add common swiper styles if needed
        $swiper_widgets = ['ma-logo-slider', 'ma-team-members-slider', 'ma-image-carousel', 'ma-twitter-slider', 'ma-blog', 'ma-timeline'];
        if (array_intersect($widgets, $swiper_widgets)) {
            $swiper_file = JLTMA_PATH . 'assets/css/common/swiper-carousel.css';
            if (file_exists($swiper_file) && !isset($processed['common-swiper-carousel'])) {
                $bundled_css .= "/* Common: Swiper */\n";
                $bundled_css .= file_get_contents($swiper_file) . "\n";
            }
        }

        return $this->minify_css($bundled_css);
    }

    /**
     * Bundle multiple JS files into one
     * Handles array format from JLTMA_Config
     */
    private function bundle_js_files($widgets)
    {
        $assets_loader = Assets_Loader::get_instance();
        $widget_assets = $assets_loader->get_widget_assets();

        $bundled_js = "/* Master Addons Bundled JS - " . gmdate('Y-m-d H:i:s') . " */\n";
        $bundled_js .= "(function($){\n'use strict';\n";

        $has_content = false;
        $processed = [];

        foreach ($widgets as $widget_name) {
            if (!isset($widget_assets[$widget_name])) {
                continue;
            }

            // JS is now an array
            $js_files = $widget_assets[$widget_name]['js'] ?? [];

            foreach ((array) $js_files as $js_slug) {
                if (empty($js_slug) || isset($processed[$js_slug])) {
                    continue;
                }

                $js_file = JLTMA_PATH . "assets/js/addons/{$js_slug}.js";

                if (file_exists($js_file)) {
                    $bundled_js .= "/* Widget: {$widget_name} ({$js_slug}) */\n";
                    $bundled_js .= file_get_contents($js_file) . "\n";
                    $processed[$js_slug] = true;
                    $has_content = true;
                }
            }

            // Also bundle vendor JS
            $vendor_js = $widget_assets[$widget_name]['vendor']['js'] ?? [];
            foreach ((array) $vendor_js as $vendor_slug) {
                if (isset($processed['vendor-' . $vendor_slug])) {
                    continue;
                }

                $vendor_file = JLTMA_PATH . "assets/vendor/{$vendor_slug}/{$vendor_slug}.js";
                if (file_exists($vendor_file)) {
                    $bundled_js .= "/* Vendor: {$vendor_slug} */\n";
                    $bundled_js .= file_get_contents($vendor_file) . "\n";
                    $processed['vendor-' . $vendor_slug] = true;
                    $has_content = true;
                }
            }
        }

        $bundled_js .= "})(jQuery);";

        return $has_content ? $bundled_js : '';
    }

    /**
     * Serve cached bundle instead of individual files
     * Handles array format from JLTMA_Config
     */
    private function serve_cached_bundle($cache_info)
    {
        $assets_loader = Assets_Loader::get_instance();
        $widget_assets = $assets_loader->get_widget_assets();

        // Dequeue individual addon assets
        foreach ($cache_info['widgets'] as $widget_name) {
            if (!isset($widget_assets[$widget_name])) {
                continue;
            }

            // CSS is now an array
            $css_files = $widget_assets[$widget_name]['css'] ?? [];
            foreach ((array) $css_files as $css_slug) {
                wp_dequeue_style('jltma-' . $css_slug);
                wp_dequeue_style('jltma-' . $css_slug . '-rtl');
            }

            // JS is now an array
            $js_files = $widget_assets[$widget_name]['js'] ?? [];
            foreach ((array) $js_files as $js_slug) {
                wp_dequeue_script('jltma-' . $js_slug);
            }

            // Also dequeue vendor assets
            $vendor_css = $widget_assets[$widget_name]['vendor']['css'] ?? [];
            foreach ((array) $vendor_css as $vendor_slug) {
                wp_dequeue_style('jltma-vendor-' . $vendor_slug);
            }

            $vendor_js = $widget_assets[$widget_name]['vendor']['js'] ?? [];
            foreach ((array) $vendor_js as $vendor_slug) {
                wp_dequeue_script('jltma-vendor-' . $vendor_slug);
            }
        }

        // Also dequeue common swiper if cached
        wp_dequeue_style('jltma-swiper-carousel');

        // Enqueue bundled CSS
        if (!empty($cache_info['css_file'])) {
            wp_enqueue_style(
                'jltma-bundled-' . $cache_info['hash'],
                $this->cache_url . '/' . $cache_info['css_file'],
                [],
                JLTMA_VER
            );
        }

        // Enqueue bundled JS
        if (!empty($cache_info['js_file'])) {
            wp_enqueue_script(
                'jltma-bundled-' . $cache_info['hash'],
                $this->cache_url . '/' . $cache_info['js_file'],
                ['jquery'],
                JLTMA_VER,
                true
            );
        }
    }

    /**
     * Generate hash from widget list
     */
    private function generate_cache_hash($widgets)
    {
        sort($widgets);  // Consistent ordering
        $rtl_suffix = is_rtl() ? '-rtl' : '';
        return substr(md5(implode('|', $widgets) . JLTMA_VER . $rtl_suffix), 0, 8);
    }

    /**
     * Check if cache is still valid
     */
    private function is_cache_valid($cache_info)
    {
        // Check if RTL setting changed
        if (isset($cache_info['is_rtl']) && $cache_info['is_rtl'] !== is_rtl()) {
            return false;
        }

        // Check if files exist
        if (!empty($cache_info['css_file'])) {
            if (!file_exists($this->cache_path . '/' . $cache_info['css_file'])) {
                return false;
            }
        }

        if (!empty($cache_info['js_file'])) {
            if (!file_exists($this->cache_path . '/' . $cache_info['js_file'])) {
                return false;
            }
        }

        // Check if plugin version changed (hash includes version)
        $current_hash = $this->generate_cache_hash($cache_info['widgets']);
        if ($current_hash !== $cache_info['hash']) {
            return false;
        }

        return true;
    }

    /**
     * Invalidate cache for a specific post
     */
    public function invalidate_post_cache($post_id)
    {
        $cache_info = $this->get_post_cache_info($post_id);

        if ($cache_info) {
            // Delete cached files (including gzipped versions)
            if (!empty($cache_info['css_file'])) {
                wp_delete_file($this->cache_path . '/' . $cache_info['css_file']);
                wp_delete_file($this->cache_path . '/' . $cache_info['css_file'] . '.gz');
            }
            if (!empty($cache_info['js_file'])) {
                wp_delete_file($this->cache_path . '/' . $cache_info['js_file']);
                wp_delete_file($this->cache_path . '/' . $cache_info['js_file'] . '.gz');
            }

            // Clear transient
            delete_transient(self::POST_CACHE_PREFIX . $post_id);

            // Clear database fallback
            delete_post_meta($post_id, '_jltma_cached_css');
            delete_post_meta($post_id, '_jltma_cached_js');
            delete_post_meta($post_id, '_jltma_cache_in_db');

            // Update global meta
            $this->remove_from_global_cache_meta($post_id);
        }
    }

    /**
     * Clear all cache files and metadata
     */
    public function clear_all_cache()
    {
        // Delete all cache files (including gzipped versions)
        $files = glob($this->cache_path . '/*.{css,js,css.gz,js.gz}', GLOB_BRACE);

        if ($files) {
            foreach ($files as $file) {
                wp_delete_file($file);
            }
        }

        // Clear all transients (using global meta to find them)
        $global_meta = get_option(self::CACHE_META_OPTION, []);

        if (!empty($global_meta['posts'])) {
            foreach (array_keys($global_meta['posts']) as $post_id) {
                delete_transient(self::POST_CACHE_PREFIX . $post_id);
                delete_post_meta($post_id, '_jltma_cached_css');
                delete_post_meta($post_id, '_jltma_cached_js');
                delete_post_meta($post_id, '_jltma_cache_in_db');
            }
        }

        // Reset global meta
        update_option(self::CACHE_META_OPTION, [
            'posts'        => [],
            'total_size'   => 0,
            'file_count'   => 0,
            'last_cleared' => time(),
        ]);

        return true;
    }

    /**
     * Update global cache metadata for dashboard
     */
    private function update_global_cache_meta($post_id, $cache_info)
    {
        $global_meta = get_option(self::CACHE_META_OPTION, [
            'posts'        => [],
            'total_size'   => 0,
            'file_count'   => 0,
            'last_cleared' => null,
        ]);

        // Remove old entry size if updating
        if (isset($global_meta['posts'][$post_id])) {
            $old = $global_meta['posts'][$post_id];
            $global_meta['total_size'] -= ($old['size_css'] ?? 0) + ($old['size_js'] ?? 0);
            $global_meta['file_count'] -= 2;
        }

        // Add new entry
        $global_meta['posts'][$post_id] = [
            'hash'      => $cache_info['hash'],
            'size_css'  => $cache_info['size_css'],
            'size_js'   => $cache_info['size_js'],
            'widgets'   => $cache_info['widgets'],
            'created'   => $cache_info['created'],
            'title'     => get_the_title($post_id),
        ];

        $global_meta['total_size'] += $cache_info['size_css'] + $cache_info['size_js'];
        $global_meta['file_count'] += 2;

        update_option(self::CACHE_META_OPTION, $global_meta);
    }

    /**
     * Remove post from global cache meta
     */
    private function remove_from_global_cache_meta($post_id)
    {
        $global_meta = get_option(self::CACHE_META_OPTION, []);

        if (isset($global_meta['posts'][$post_id])) {
            $entry = $global_meta['posts'][$post_id];
            $global_meta['total_size'] -= ($entry['size_css'] ?? 0) + ($entry['size_js'] ?? 0);
            $global_meta['file_count'] -= 2;
            unset($global_meta['posts'][$post_id]);

            update_option(self::CACHE_META_OPTION, $global_meta);
        }
    }

    /**
     * Get post cache info from transient
     */
    public function get_post_cache_info($post_id)
    {
        return get_transient(self::POST_CACHE_PREFIX . $post_id);
    }

    /**
     * Get cache statistics for dashboard
     */
    public function get_cache_stats()
    {
        $global_meta = get_option(self::CACHE_META_OPTION, []);

        // Verify actual files match metadata
        $actual_files = glob($this->cache_path . '/*.{css,js}', GLOB_BRACE);
        $actual_count = $actual_files ? count($actual_files) : 0;

        // Calculate actual size
        $actual_size = 0;
        if ($actual_files) {
            foreach ($actual_files as $file) {
                $actual_size += filesize($file);
            }
        }

        // Get cached files list with details
        $cached_files = [];
        if (!empty($global_meta['posts'])) {
            foreach ($global_meta['posts'] as $post_id => $info) {
                $css_file = $this->cache_path . "/post-{$post_id}-{$info['hash']}.css";
                $file_size = 0;
                $file_modified = 0;

                if (file_exists($css_file)) {
                    $file_size = filesize($css_file);
                    $file_modified = filemtime($css_file);
                }

                $cached_files[] = [
                    'post_id'           => $post_id,
                    'post_title'        => $info['title'] ?? get_the_title($post_id),
                    'filename'          => "post-{$post_id}-{$info['hash']}.css",
                    'size'              => $file_size,
                    'size_formatted'    => size_format($file_size),
                    'modified'          => $file_modified,
                    'modified_formatted' => $file_modified ? human_time_diff($file_modified) . ' ' . __('ago', 'master-addons') : __('N/A', 'master-addons'),
                    'widgets'           => $info['widgets'] ?? [],
                ];
            }
        }

        // Format last cleared time
        $last_cleared = $global_meta['last_cleared'] ?? null;
        $last_cleared_formatted = $last_cleared
            ? human_time_diff($last_cleared) . ' ' . __('ago', 'master-addons')
            : __('Never', 'master-addons');

        return [
            'enabled'              => $this->is_enabled(),
            'total_size'           => $actual_size,
            'total_size_formatted' => size_format($actual_size),
            'total_size_hr'        => size_format($actual_size),
            'file_count'           => $actual_count,
            'cached_pages'         => count($global_meta['posts'] ?? []),
            'post_count'           => count($global_meta['posts'] ?? []),
            'last_cleared'         => $last_cleared,
            'last_cleared_formatted' => $last_cleared_formatted,
            'cache_path'           => $this->cache_path,
            'cache_url'            => $this->cache_url,
            'cache_directory'      => str_replace(ABSPATH, '', $this->cache_path),
            'posts'                => $global_meta['posts'] ?? [],
            'cached_files'         => $cached_files,
        ];
    }

    /**
     * Regenerate cache for all posts with Elementor content
     */
    public function regenerate_all_cache()
    {
        // Clear existing first
        $this->clear_all_cache();

        // Find all posts with Elementor data
        $posts = get_posts([
            'post_type'      => ['page', 'post', 'elementor_library'],
            'posts_per_page' => -1,
            'meta_key'       => '_elementor_data',
            'fields'         => 'ids',
            'post_status'    => 'publish',
        ]);

        $count = 0;
        foreach ($posts as $post_id) {
            if ($this->generate_post_cache($post_id)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Database fallback when file writes fail
     */
    private function store_in_database($post_id, $css_content, $js_content)
    {
        if (!empty($css_content)) {
            update_post_meta($post_id, '_jltma_cached_css', $css_content);
        }
        if (!empty($js_content)) {
            update_post_meta($post_id, '_jltma_cached_js', $js_content);
        }
        update_post_meta($post_id, '_jltma_cache_in_db', true);
    }

    /**
     * Simple CSS minification
     */
    private function minify_css($css)
    {
        // Remove comments
        $css = preg_replace('/\/\*[^*]*\*+([^\/][^*]*\*+)*\//', '', $css);
        // Remove whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        // Remove space around selectors
        $css = preg_replace('/\s*([\{\}\;\:\,])\s*/', '$1', $css);
        return trim($css);
    }

    /**
     * Compress content with gzip
     *
     * @param string $content Content to compress
     * @param int $level Compression level (1-9, default 9)
     * @return string|false Compressed content or false on failure
     */
    private function gzip_content($content, $level = 9)
    {
        if (!function_exists('gzencode')) {
            return false;
        }

        return gzencode($content, $level);
    }

    /**
     * Write file with optional gzip version
     *
     * @param string $filepath Full path to file
     * @param string $content File content
     * @return array ['written' => bool, 'gzip_written' => bool, 'gzip_size' => int]
     */
    private function write_with_gzip($filepath, $content)
    {
        $result = [
            'written' => false,
            'gzip_written' => false,
            'gzip_size' => 0,
        ];

        // Write original file
        $result['written'] = (bool) file_put_contents($filepath, $content);

        if (!$result['written']) {
            return $result;
        }

        // Write gzipped version
        $gzipped = $this->gzip_content($content);
        if ($gzipped !== false) {
            $gzip_path = $filepath . '.gz';
            $result['gzip_written'] = (bool) file_put_contents($gzip_path, $gzipped);
            if ($result['gzip_written']) {
                $result['gzip_size'] = strlen($gzipped);
            }
        }

        return $result;
    }

    /**
     * Check if we're in Elementor editor
     */
    private function is_elementor_editor()
    {
        if (!class_exists('\Elementor\Plugin')) {
            return false;
        }

        $elementor = \Elementor\Plugin::$instance;

        if (!$elementor || !isset($elementor->editor) || !isset($elementor->preview)) {
            return false;
        }

        return $elementor->editor->is_edit_mode() || $elementor->preview->is_preview_mode();
    }

    /**
     * Handle plugin/theme upgrades
     */
    public function on_upgrade_complete($upgrader, $options)
    {
        // Clear cache when Master Addons is updated
        if (
            $options['action'] === 'update' &&
            $options['type'] === 'plugin' &&
            isset($options['plugins']) &&
            in_array('master-addons/master-addons.php', $options['plugins'])
        ) {
            $this->clear_all_cache();
        }
    }

    /**
     * AJAX: Clear all cache
     */
    public function ajax_clear_cache()
    {
        check_ajax_referer('jltma_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'master-addons')]);
        }

        $this->clear_all_cache();

        wp_send_json_success([
            'message' => __('Cache cleared successfully', 'master-addons'),
            'stats'   => $this->get_cache_stats(),
        ]);
    }

    /**
     * AJAX: Regenerate all cache
     */
    public function ajax_regenerate_cache()
    {
        check_ajax_referer('jltma_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'master-addons')]);
        }

        $count = $this->regenerate_all_cache();

        wp_send_json_success([
            /* translators: %d: number of pages */
            'message' => sprintf(__('Regenerated cache for %d pages', 'master-addons'), $count),
            'stats'   => $this->get_cache_stats(),
        ]);
    }

    /**
     * AJAX: Get cache stats
     */
    public function ajax_get_cache_stats()
    {
        check_ajax_referer('jltma_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'master-addons')]);
        }

        wp_send_json_success($this->get_cache_stats());
    }

    /**
     * Get cache directory path
     */
    public function get_cache_path()
    {
        return $this->cache_path;
    }

    /**
     * Get cache directory URL
     */
    public function get_cache_url()
    {
        return $this->cache_url;
    }

    /**
     * AJAX: Clear single post cache
     */
    public function ajax_clear_single_cache()
    {
        check_ajax_referer('jltma_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'master-addons')]);
        }

        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;

        if (!$post_id) {
            wp_send_json_error(['message' => __('Invalid post ID', 'master-addons')]);
        }

        $this->invalidate_post_cache($post_id);

        wp_send_json_success([
            /* translators: %d: post ID number */
            'message' => sprintf(__('Cache cleared for post #%d', 'master-addons'), $post_id),
            'stats'   => $this->get_cache_stats(),
        ]);
    }

    /**
     * AJAX: Regenerate single post cache
     */
    public function ajax_regenerate_single_cache()
    {
        check_ajax_referer('jltma_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'master-addons')]);
        }

        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;

        if (!$post_id) {
            wp_send_json_error(['message' => __('Invalid post ID', 'master-addons')]);
        }

        // Clear existing cache first
        $this->invalidate_post_cache($post_id);

        // Regenerate
        $result = $this->generate_post_cache($post_id);

        if ($result) {
            wp_send_json_success([
                /* translators: %d: post ID number */
                'message' => sprintf(__('Cache regenerated for post #%d', 'master-addons'), $post_id),
                'stats'   => $this->get_cache_stats(),
            ]);
        } else {
            wp_send_json_error([
                /* translators: %d: post ID number */
                'message' => sprintf(__('Failed to regenerate cache for post #%d', 'master-addons'), $post_id),
            ]);
        }
    }

    /**
     * AJAX: Save performance settings
     */
    public function ajax_save_performance_settings()
    {
        check_ajax_referer('jltma_performance_settings_nonce_action', '_wpnonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'master-addons')]);
        }

        // Save settings
        $dynamic_assets = isset($_POST['jltma_dynamic_assets_enabled']) ? true : false;
        $cache_enabled = isset($_POST['jltma_cache_enabled']) ? true : false;
        $cache_minify = isset($_POST['jltma_cache_minify']) ? true : false;
        $cache_debug = isset($_POST['jltma_cache_debug']) ? true : false;

        update_option('jltma_dynamic_assets_enabled', $dynamic_assets);
        update_option('jltma_cache_enabled', $cache_enabled);
        update_option('jltma_cache_minify', $cache_minify);
        update_option('jltma_cache_debug', $cache_debug);

        // Clear cache if caching was disabled
        if (!$cache_enabled) {
            $this->clear_all_cache();
        }

        wp_send_json_success([
            'message' => __('Performance settings saved', 'master-addons'),
        ]);
    }
}
