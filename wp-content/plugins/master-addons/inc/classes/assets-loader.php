<?php

/**
 * Master Addons - Dynamic Assets Loader
 *
 * Loads CSS/JS assets only for widgets actually used on each page.
 * This significantly reduces page load time by avoiding global asset loading.
 *
 * ## Asset File Variants
 * The build system generates multiple variants for each addon:
 * - file.css        - Expanded CSS (development/debugging)
 * - file.min.css    - Minified CSS (production, ~20% smaller)
 * - file.rtl.css    - RTL version for right-to-left languages
 * - file.rtl.min.css - RTL minified
 *
 * ## Gzipped Files (.css.gz / .js.gz)
 * Pre-compressed files are generated for optimal delivery:
 * - file.min.css.gz     - Gzipped minified CSS (~80% smaller than original)
 * - file.rtl.min.css.gz - Gzipped RTL CSS
 *
 * ### How Gzip Works:
 * 1. Build script pre-compresses .min.css files to .min.css.gz
 * 2. Web server (Apache/Nginx) serves .gz files automatically when:
 *    - Client sends "Accept-Encoding: gzip" header
 *    - Server has mod_deflate (Apache) or gzip module (Nginx) enabled
 * 3. If server gzip is disabled, regular .min.css files are served
 *
 * ### Server Configuration Required:
 * Apache (.htaccess):
 *   <IfModule mod_deflate.c>
 *     AddEncoding gzip .gz
 *     RewriteCond %{HTTP:Accept-Encoding} gzip
 *     RewriteCond %{REQUEST_FILENAME}.gz -f
 *     RewriteRule ^(.*)$ $1.gz [L]
 *   </IfModule>
 *
 * Nginx:
 *   gzip_static on;
 *
 * @package MasterAddons\Inc\Classes
 * @since 2.0.0
 * @see docs/plans/2026-01-12-vite-migration-asset-management-design.md
 */

namespace MasterAddons\Inc\Classes;

use MasterAddons\Inc\Admin\Config;
use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Classes\Assets_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Assets_Loader
{
    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Widget to asset mapping
     * Format: widget_name => ['css' => 'filename', 'js' => 'filename']
     */
    private $widget_assets = [];

    /**
     * Detected widgets on current page
     */
    private $page_widgets = [];

    /**
     * Post meta key for cached widget list
     */
    const META_KEY = '_jltma_used_widgets';

    /**
     * Option name for dynamic loading setting
     */
    const OPTION_KEY = 'jltma_dynamic_assets_enabled';

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
     * Track which script handles need type="module"
     */
    private $module_script_handles = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Only initialize asset loading if dynamic loading is enabled
        if (!$this->is_enabled()) {
            return;
        }

        // Build widget-asset map on init
        add_action('init', [$this, 'build_asset_map'], 5);

        // Register all addon assets (but don't enqueue yet) - both frontend and admin
        add_action('wp_enqueue_scripts', [$this, 'register_addon_assets'], 5);
        add_action('admin_enqueue_scripts', [$this, 'register_addon_assets'], 5);

        // Enqueue only needed assets on frontend
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets'], 100);

        // Localize scripts after they are enqueued
        add_action('wp_enqueue_scripts', [$this, 'localize_addon_scripts'], 101);

        // Load all enabled assets in editor/preview
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_assets']);
        add_action('elementor/preview/enqueue_styles', [$this, 'enqueue_editor_assets']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_editor_scripts']);

        // Localize scripts in editor/preview
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'localize_addon_scripts'], 20);
        add_action('elementor/preview/enqueue_scripts', [$this, 'localize_addon_scripts'], 20);

        // Update cache on post save in Elementor
        add_action('elementor/editor/after_save', [$this, 'update_widget_cache'], 10, 2);

        // Clear cache when post is trashed or deleted
        add_action('trashed_post', [$this, 'clear_post_cache']);
        add_action('deleted_post', [$this, 'clear_post_cache']);

        // Add type="module" to ES module scripts (for WordPress < 6.5 compatibility)
        add_filter('script_loader_tag', [$this, 'add_module_type_attribute'], 10, 3);
        
        // Master Addons button into Elementor editor promo panel
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'inject_promo_button_script']);

    }

    /**
     * Inject Master Addons button into Elementor editor promo panel
     * Adds our button below Elementor's existing promo button via JavaScript
     *
     * @return void
     */
    public function inject_promo_button_script()
    {
        // Show settings for premium users, pricing page for free users
        if (Helper::jltma_premium()) {
            $jltma_link = esc_url(admin_url('admin.php?page=master-addons-settings'));
        } else {
            $jltma_link = esc_url('https://master-addons.com/pricing/?utm_source=starter-user&utm_medium=elementor-editor&utm_campaign=editor-promo-panel');
        }
        $button_text = esc_js(__('Explore Master Addons', 'master-addons'));

        $script = "(function() {
            function injectMasterAddonsButton() {
                var promoPanel = document.getElementById('elementor-panel-get-pro-elements');
                if (!promoPanel) return;
                if (promoPanel.querySelector('.jltma-promo-button')) return;
                var existingButton = promoPanel.querySelector('.elementor-button.go-pro');
                if (!existingButton) return;
                var maButton = document.createElement('a');
                maButton.href = '{$jltma_link}';
                maButton.className = 'elementor-button jltma-promo-button';
                maButton.target = '_blank';
                maButton.textContent = '{$button_text}';
                maButton.style.cssText = 'display: inline-flex; margin-top: 10px; background: linear-gradient(135deg, #6f42c1, #9c27b0); color: #fff;';
                existingButton.insertAdjacentElement('afterend', maButton);
            }
            var observer = new MutationObserver(function() { injectMasterAddonsButton(); });
            observer.observe(document.body, { childList: true, subtree: true });
            window.addEventListener('load', function() {
                setTimeout(injectMasterAddonsButton, 500);
                setTimeout(injectMasterAddonsButton, 2000);
            });
        })();";

        wp_add_inline_script('elementor-editor', $script);
    }

    /**
     * Add type="module" attribute to ES module scripts
     * Required for scripts built with Vite/Rollup ES format
     *
     * @param string $tag    Script HTML tag
     * @param string $handle Script handle
     * @param string $src    Script source URL
     * @return string Modified script tag
     */
    public function add_module_type_attribute($tag, $handle, $src)
    {
        // Only modify addon scripts that we registered as modules
        if (!in_array($handle, $this->module_script_handles, true)) {
            return $tag;
        }

        // Don't add if already has type attribute
        if (strpos($tag, 'type=') !== false) {
            return $tag;
        }

        // Add type="module" attribute
        return str_replace(' src=', ' type="module" src=', $tag);
    }

    /**
     * Check if dynamic asset loading is enabled
     * Default: true (enabled) - per-addon CSS/JS loading active by default
     */
    public function is_enabled()
    {
        return (bool) get_option(self::OPTION_KEY, true);
    }

    /**
     * Enable dynamic asset loading
     */
    public static function enable()
    {
        update_option(self::OPTION_KEY, true);
    }

    /**
     * Disable dynamic asset loading
     */
    public static function disable()
    {
        update_option(self::OPTION_KEY, false);
    }

    /**
     * Build mapping of widget names to their asset files
     * Uses JLTMA_Config as the single source of truth
     * Maps by Elementor widget name (from widget_name field or config key)
     * Includes both addons and extensions/modules
     *
     * New simplified format supported:
     * - 'css' => true  → auto-maps to ma-{widget-key}.css
     * - 'js'  => true  → auto-maps to ma-{widget-key}.js
     * - 'vendors' => ['fancybox', 'tippy'] → resolves from Assets_Manager registry
     */
    public function build_asset_map()
    {
        $this->widget_assets = [];

        // Get all addons from unified config
        $addons = Config::get_addons();

        foreach ($addons as $key => $addon) {
            // Get assets defined in config
            $assets = isset($addon['assets']) ? $addon['assets'] : [];

            // Build CSS array - handle both true and explicit array
            $css_files = [];
            if (isset($assets['css'])) {
                if ($assets['css'] === true) {
                    // Auto-map to widget key filename
                    $css_files = [$key];
                } elseif (is_array($assets['css'])) {
                    $css_files = $assets['css'];
                } elseif (is_string($assets['css'])) {
                    $css_files = [$assets['css']];
                }
            }

            // Build JS array - handle both true and explicit array
            $js_files = [];
            if (isset($assets['js'])) {
                if ($assets['js'] === true) {
                    // Auto-map to widget key filename
                    $js_files = [$key];
                } elseif (is_array($assets['js'])) {
                    $js_files = $assets['js'];
                } elseif (is_string($assets['js'])) {
                    $js_files = [$assets['js']];
                }
            }

            // Build vendor dependencies - support both 'vendor' (legacy) and 'vendors' (new)
            $vendors = isset($assets['vendors']) ? $assets['vendors'] : (isset($assets['vendor']) ? $assets['vendor'] : []);

            // Track if this is a premium addon
            $is_pro = isset($addon['is_pro']) ? (bool) $addon['is_pro'] : false;

            // Use config key directly - widgets declare their own dependencies
            // via get_style_depends() and get_script_depends() methods
            $this->widget_assets[$key] = [
                'css'        => $css_files,
                'js'         => $js_files,
                'vendors'    => $vendors,
                'is_pro'     => $is_pro,
                'asset_type' => 'addon', // Mark as addon for path resolution
            ];
        }

        // Get all extensions/modules from unified config
        $extensions = Config::get_extensions();

        foreach ($extensions as $key => $extension) {
            // Get assets defined in config
            $assets = isset($extension['assets']) ? $extension['assets'] : [];

            // Build CSS array - handle both true and explicit array
            $css_files = [];
            if (isset($assets['css'])) {
                if ($assets['css'] === true) {
                    $css_files = [$key];
                } elseif (is_array($assets['css'])) {
                    $css_files = $assets['css'];
                } elseif (is_string($assets['css'])) {
                    $css_files = [$assets['css']];
                }
            }

            // Build JS array - handle both true and explicit array
            $js_files = [];
            if (isset($assets['js'])) {
                if ($assets['js'] === true) {
                    $js_files = [$key];
                } elseif (is_array($assets['js'])) {
                    $js_files = $assets['js'];
                } elseif (is_string($assets['js'])) {
                    $js_files = [$assets['js']];
                }
            }

            // Build vendor dependencies - support both 'vendor' (legacy) and 'vendors' (new)
            $vendors = isset($assets['vendors']) ? $assets['vendors'] : (isset($assets['vendor']) ? $assets['vendor'] : []);

            // Track if this is a premium extension
            $is_pro = isset($extension['is_pro']) ? (bool) $extension['is_pro'] : false;

            $this->widget_assets[$key] = [
                'css'        => $css_files,
                'js'         => $js_files,
                'vendors'    => $vendors,
                'is_pro'     => $is_pro,
                'asset_type' => 'module', // Mark as module for path resolution
            ];
        }

        // Note: Common swiper-carousel styles are now loaded via vendor dependency
        // Each swiper widget declares 'swiper-carousel' in its vendors config

        // Allow filtering to add more widget mappings or modify existing
        $this->widget_assets = apply_filters('jltma/assets/widget_map', $this->widget_assets);
    }

    /**
     * Register all addon and module assets without enqueuing
     * Supports SCRIPT_DEBUG for unminified files
     * Handles separate paths for:
     *   - Free addons: assets/css/addons/, assets/js/addons/
     *   - Premium addons: premium/assets/css/addons/, premium/assets/js/addons/
     *   - Free modules: assets/css/modules/, assets/js/modules/
     *   - Premium modules: premium/assets/css/modules/, premium/assets/js/modules/
     * Premium assets only load if user has valid license
     */
    public function register_addon_assets()
    {
        // Register all vendor assets from central registry
        Assets_Manager::get_instance()->register_all();

        // Base URL and paths — switch constants based on is_pro flag
        $free_url  = defined('JLTMA_URL') ? trailingslashit(JLTMA_URL) : (defined('JLTMA_PRO_URL') ? JLTMA_PRO_URL : '');
        $free_path = defined('JLTMA_PATH') ? JLTMA_PATH : (defined('JLTMA_PRO_PATH') ? JLTMA_PRO_PATH : '');
        $pro_url   = defined('JLTMA_PRO_URL') ? JLTMA_PRO_URL : $free_url;
        $pro_path  = defined('JLTMA_PRO_PATH') ? JLTMA_PRO_PATH : $free_path;
        $version   = defined('JLTMA_VER') ? JLTMA_VER : (defined('JLTMA_PRO_VER') ? JLTMA_PRO_VER : '1.0.0');
        $suffix    = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

        foreach ($this->widget_assets as $widget => $assets) {
            // Determine asset type (addon or module) - defaults to addon for backward compatibility
            $asset_type = isset($assets['asset_type']) ? $assets['asset_type'] : 'addon';
            $type_folder = ($asset_type === 'module') ? 'modules' : 'addons';

            // Check if this is a pro asset — use JLTMA_PRO_* constants for pro assets
            $is_pro_asset = isset($assets['is_pro']) && $assets['is_pro'];
            $plugin_url  = $is_pro_asset ? $pro_url : $free_url;
            $plugin_path = $is_pro_asset ? $pro_path : $free_path;

            // Build paths based on asset type
            $free_css_url  = $plugin_url . 'assets/css/' . $type_folder . '/';
            $free_js_url   = $plugin_url . 'assets/js/' . $type_folder . '/';
            $free_css_path  = $plugin_path . 'assets/css/' . $type_folder . '/';
            $free_js_path   = $plugin_path . 'assets/js/' . $type_folder . '/';

            // Premium asset locations live in the pro plugin's premium/ directory.
            // The pro plugin (MasterAddons\Pro\Classes\Assets_Pro) supplies them
            // through this filter; in a free-only install the defaults stay empty
            // so the free plugin never references the premium/ directory itself.
            $premium_base = wp_parse_args(
                (array) apply_filters('master_addons/assets/premium_base', array(), $type_folder),
                array('css_url' => '', 'js_url' => '', 'css_path' => '', 'js_path' => '')
            );
            $premium_css_url  = $premium_base['css_url'];
            $premium_js_url   = $premium_base['js_url'];
            $premium_css_path = $premium_base['css_path'];
            $premium_js_path  = $premium_base['js_path'];

            // Register CSS files (now an array)
            // First style is the main addon style, rest are dependencies
            if (!empty($assets['css'])) {
                $css_files = (array) $assets['css'];
                $css_dependencies = [];

                // Add vendor CSS as dependencies (vendors are registered by Assets_Manager).
                // Only add the dep if the vendor style was actually registered — a pro-only
                // vendor (e.g. prism) keeps its config but is not registered when the license
                // is inactive, which would otherwise produce an "unregistered dependency" notice.
                if (!empty($assets['vendors'])) {
                    foreach ((array) $assets['vendors'] as $vendor) {
                        $vendor_config = Assets_Manager::get($vendor);
                        if ($vendor_config && !empty($vendor_config['files']['css']) && wp_style_is('jltma-' . $vendor, 'registered')) {
                            $css_dependencies[] = 'jltma-' . $vendor;
                        }
                    }
                }

                // First CSS is the main addon style
                $main_css = array_shift($css_files);

                // Remaining CSS files are dependencies (already registered as vendor styles)
                foreach ($css_files as $dep_css) {
                    $css_dependencies[] = $dep_css;
                }

                // Generate handle for main addon style
                $handle_name = preg_replace('/^(ma-|jltma-)/', '', $main_css);
                $ltr_handle = 'master-addons-' . $handle_name;

                // Try minified first, then unminified
                $css_filename = $main_css . $suffix . '.css';
                $css_fallback = $main_css . '.css';

                // Determine which path to use: premium or free.
                // The premium base is only populated when the pro plugin is
                // active (see the premium_base filter above), so a premium
                // version is preferred whenever one is shipped — no separate
                // license check, which would hide premium styling from
                // existing content on trial/expired sites.
                $css_url = $free_css_url;
                $css_path = $free_css_path;

                if (!empty($premium_css_path)
                    && (file_exists($premium_css_path . $css_filename) || file_exists($premium_css_path . $css_fallback))) {
                    $css_url = $premium_css_url;
                    $css_path = $premium_css_path;
                }

                if (file_exists($css_path . $css_filename)) {
                    wp_register_style($ltr_handle, $css_url . $css_filename, $css_dependencies, $version);
                } elseif (file_exists($css_path . $css_fallback)) {
                    wp_register_style($ltr_handle, $css_url . $css_fallback, $css_dependencies, $version);
                }

                // Register RTL CSS (depends on LTR version)
                $rtl_handle = $ltr_handle . '-rtl';
                $rtl_filename = $main_css . '.rtl' . $suffix . '.css';
                $rtl_fallback = $main_css . '.rtl.css';

                if (file_exists($css_path . $rtl_filename)) {
                    wp_register_style($rtl_handle, $css_url . $rtl_filename, [$ltr_handle], $version);
                } elseif (file_exists($css_path . $rtl_fallback)) {
                    wp_register_style($rtl_handle, $css_url . $rtl_fallback, [$ltr_handle], $version);
                }
            }

            // Register JS files (now an array)
            // First script is the main addon script, rest are dependencies
            if (!empty($assets['js'])) {
                $js_files = (array) $assets['js'];
                $js_dependencies = ['jquery'];

                // Add vendor JS as dependencies (vendors are registered by Assets_Manager).
                // Skip vendors that aren't actually registered (e.g. pro-only prism on an
                // inactive license) to avoid "unregistered dependency" notices.
                if (!empty($assets['vendors'])) {
                    foreach ((array) $assets['vendors'] as $vendor) {
                        $vendor_config = Assets_Manager::get($vendor);
                        if ($vendor_config && !empty($vendor_config['files']['js']) && wp_script_is('jltma-' . $vendor, 'registered')) {
                            $js_dependencies[] = 'jltma-' . $vendor;
                        }
                    }
                }

                // First script is the main addon script
                $main_js = array_shift($js_files);

                // Remaining scripts are dependencies (already registered as vendor scripts)
                foreach ($js_files as $dep_js) {
                    $js_dependencies[] = $dep_js;
                }

                // Generate handle for main addon script
                $handle_name = preg_replace('/^(ma-|jltma-)/', '', $main_js);
                $js_handle = 'master-addons-' . $handle_name;

                // Try minified first, then unminified
                $js_filename = $main_js . $suffix . '.js';
                $js_fallback = $main_js . '.js';

                // Determine which path to use: premium or free.
                // Premium base is populated only when the pro plugin is active,
                // so a premium version is preferred whenever one is shipped.
                $js_url = $free_js_url;
                $js_path = $free_js_path;

                if (!empty($premium_js_path)
                    && (file_exists($premium_js_path . $js_filename) || file_exists($premium_js_path . $js_fallback))) {
                    $js_url = $premium_js_url;
                    $js_path = $premium_js_path;
                }

                if (file_exists($js_path . $js_filename)) {
                    wp_register_script($js_handle, $js_url . $js_filename, $js_dependencies, $version, true);
                    // Only add type="module" if the file actually uses ES module syntax (import/export)
                    // jQuery IIFEs and other non-module scripts break with type="module" in some browsers
                    if ($this->is_es_module_file($js_path . $js_filename)) {
                        $this->module_script_handles[] = $js_handle;
                    }
                } elseif (file_exists($js_path . $js_fallback)) {
                    wp_register_script($js_handle, $js_url . $js_fallback, $js_dependencies, $version, true);
                    if ($this->is_es_module_file($js_path . $js_fallback)) {
                        $this->module_script_handles[] = $js_handle;
                    }
                }
            }

            // Note: Vendor dependencies are registered by Assets_Manager::register_all()
            // The 'vendors' array just contains handles to be enqueued at runtime
        }
    }

    /**
     * Detect widgets used on current page
     */
    public function detect_page_widgets($post_id = null)
    {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        if (!$post_id) {
            return [];
        }

        // Try cached post meta first (fast path)
        $cached = get_post_meta($post_id, self::META_KEY, true);

        if (!empty($cached) && is_array($cached)) {
            return $cached;
        }

        // Fallback: Parse Elementor data at runtime
        return $this->parse_elementor_widgets($post_id);
    }

    /**
     * Parse Elementor data to find Master Addons widgets
     */
    private function parse_elementor_widgets($post_id)
    {
        $elementor_data = get_post_meta($post_id, '_elementor_data', true);

        if (empty($elementor_data)) {
            return [];
        }

        if (is_string($elementor_data)) {
            $elementor_data = json_decode($elementor_data, true);
        }

        if (!is_array($elementor_data)) {
            return [];
        }

        $widgets = [];
        $this->extract_widgets_recursive($elementor_data, $widgets);

        $unique_widgets = array_unique($widgets);

        // Cache for future requests
        update_post_meta($post_id, self::META_KEY, $unique_widgets);

        return $unique_widgets;
    }

    /**
     * Recursively extract widget names and enabled extensions from Elementor data
     */
    private function extract_widgets_recursive($elements, &$widgets)
    {
        if (!is_array($elements)) {
            return;
        }

        foreach ($elements as $element) {
            // Check if this is a Master Addons widget
            if (isset($element['widgetType'])) {
                $widget_type = $element['widgetType'];

                // Track Master Addons widgets (ma-* or jltma-* prefix)
                if (strpos($widget_type, 'ma-') === 0 || strpos($widget_type, 'jltma-') === 0) {
                    $widgets[] = $widget_type;
                }
            }

            // Check for enabled extensions in element settings
            // Extensions use settings like: ma_el_animated_gradient_enable, ma_el_particles_enable, etc.
            if (!empty($element['settings']) && is_array($element['settings'])) {
                foreach ($element['settings'] as $setting_key => $setting_value) {
                    // Match pattern: ma_el_{extension}_enable = 'yes'
                    if (preg_match('/^ma_el_(.+)_enable$/', $setting_key, $matches) && $setting_value === 'yes') {
                        // Convert underscores to hyphens: animated_gradient -> animated-gradient
                        $extension_key = str_replace('_', '-', $matches[1]);
                        $widgets[] = $extension_key;
                    }

                    // Match pattern: ma_el_enable_{extension} = 'yes' (e.g., ma_el_enable_particles, ma_el_enable_bg_slider)
                    if (preg_match('/^ma_el_enable_(.+)$/', $setting_key, $matches) && $setting_value === 'yes') {
                        $extension_key = str_replace('_', '-', $matches[1]);
                        $widgets[] = $extension_key;
                    }

                    // Match pattern: enabled_{extension} = 'yes' (e.g., enabled_rellax)
                    if (preg_match('/^enabled_(.+)$/', $setting_key, $matches) && $setting_value === 'yes') {
                        $extension_key = str_replace('_', '-', $matches[1]);
                        $widgets[] = $extension_key;
                    }
                }
            }

            // Recurse into nested elements
            if (!empty($element['elements'])) {
                $this->extract_widgets_recursive($element['elements'], $widgets);
            }
        }
    }

    /**
     * Enqueue only assets needed for current page (frontend)
     */
    public function enqueue_frontend_assets()
    {
        // Skip in editor - handled separately
        if ($this->is_elementor_editor()) {
            return;
        }

        $widgets = $this->detect_page_widgets();
        $is_rtl = is_rtl();

        // Track which files we've enqueued (to avoid duplicates)
        $enqueued_css = [];
        $enqueued_js = [];
        $enqueued_vendor = [];

        foreach ($widgets as $widget_name) {
            // Try direct lookup first, then try without ma-/jltma-/ma-el- prefix
            // This handles cases where config key is 'contact-form-7' but widget name is 'ma-contact-form-7'
            // or widget name is 'ma-el-ninja-forms' but config key is 'ninja-forms'
            $asset_key = $widget_name;
            if (!isset($this->widget_assets[$asset_key])) {
                // Try stripping ma-el-, ma- or jltma- prefix
                $asset_key = preg_replace('/^(ma-el-|ma-|jltma-)/', '', $widget_name);
            }

            if (!isset($this->widget_assets[$asset_key])) {
                continue;
            }

            $assets = $this->widget_assets[$asset_key];

            // Enqueue CSS files (now an array)
            if (!empty($assets['css'])) {
                foreach ((array) $assets['css'] as $css_file) {
                    // Skip if already enqueued
                    if (isset($enqueued_css[$css_file])) {
                        continue;
                    }

                    // Generate handle: master-addons-flipbox (strip ma- prefix)
                    $handle_name = preg_replace('/^(ma-|jltma-)/', '', $css_file);
                    $css_handle = 'master-addons-' . $handle_name;

                    // Enqueue CSS (LTR or RTL based on site setting)
                    if ($is_rtl) {
                        $rtl_handle = $css_handle . '-rtl';
                        if (wp_style_is($rtl_handle, 'registered')) {
                            wp_enqueue_style($rtl_handle);
                            $enqueued_css[$css_file] = true;
                            continue;
                        }
                    }

                    if (wp_style_is($css_handle, 'registered')) {
                        wp_enqueue_style($css_handle);
                        $enqueued_css[$css_file] = true;
                    }
                }
            }

            // Enqueue JS files (now an array)
            if (!empty($assets['js'])) {
                foreach ((array) $assets['js'] as $js_file) {
                    if (isset($enqueued_js[$js_file])) {
                        continue;
                    }

                    // Generate handle: master-addons-flipbox (strip ma- prefix)
                    $handle_name = preg_replace('/^(ma-|jltma-)/', '', $js_file);
                    $js_handle = 'master-addons-' . $handle_name;

                    if (wp_script_is($js_handle, 'registered')) {
                        wp_enqueue_script($js_handle);
                        $enqueued_js[$js_file] = true;
                    }
                }
            }

            // Enqueue vendor dependencies (simple array of handles from Assets_Manager)
            if (!empty($assets['vendors'])) {
                foreach ((array) $assets['vendors'] as $vendor) {
                    if (isset($enqueued_vendor[$vendor])) {
                        continue;
                    }

                    // Use Assets_Manager to enqueue vendor and all its dependencies
                    Assets_Manager::enqueue($vendor);
                    $enqueued_vendor[$vendor] = true;
                }
            }
        }

    }

    /**
     * Load ALL enabled addon CSS in editor/preview
     */
    public function enqueue_editor_assets()
    {
        $enabled_widgets = $this->get_enabled_widgets();
        $is_rtl = is_rtl();

        foreach ($this->widget_assets as $widget_name => $assets) {
            // Only load if widget is enabled
            if (!in_array($widget_name, $enabled_widgets)) {
                continue;
            }

            // Enqueue CSS files (now an array)
            if (!empty($assets['css'])) {
                foreach ((array) $assets['css'] as $css_file) {
                    // Generate handle: master-addons-flipbox (strip ma- prefix)
                    $handle_name = preg_replace('/^(ma-|jltma-)/', '', $css_file);
                    $css_handle = 'master-addons-' . $handle_name;

                    // Enqueue LTR
                    if (wp_style_is($css_handle, 'registered')) {
                        wp_enqueue_style($css_handle);
                    }

                    // Also enqueue RTL in editor for live preview switching
                    if ($is_rtl) {
                        $rtl_handle = $css_handle . '-rtl';
                        if (wp_style_is($rtl_handle, 'registered')) {
                            wp_enqueue_style($rtl_handle);
                        }
                    }
                }
            }

            // Enqueue vendor assets in editor
            if (!empty($assets['vendors'])) {
                foreach ((array) $assets['vendors'] as $vendor) {
                    Assets_Manager::enqueue($vendor);
                }
            }
        }

    }

    /**
     * Load ALL enabled addon JS in editor/preview
     */
    public function enqueue_editor_scripts()
    {
        $enabled_widgets = $this->get_enabled_widgets();

        foreach ($this->widget_assets as $widget_name => $assets) {
            if (!in_array($widget_name, $enabled_widgets)) {
                continue;
            }

            // Enqueue JS files (now an array)
            if (!empty($assets['js'])) {
                foreach ((array) $assets['js'] as $js_file) {
                    // Generate handle: master-addons-flipbox (strip ma- prefix)
                    $handle_name = preg_replace('/^(ma-|jltma-)/', '', $js_file);
                    $js_handle = 'master-addons-' . $handle_name;
                    if (wp_script_is($js_handle, 'registered')) {
                        wp_enqueue_script($js_handle);
                    }
                }
            }

            // Note: Vendor assets already enqueued in enqueue_editor_assets()
            // Assets_Manager handles both CSS and JS when enqueue() is called
        }
    }

    /**
     * Localize addon scripts with required data
     *
     * Script-specific localization:
     * - ma-data-table: DataTable translation strings (JLTMA_DATA_TABLE)
     * - ma-bg-slider: Plugin URL for overlay images (jltma_scripts)
     * - ma-restrict-content: AJAX URL for password verification (jltma_scripts)
     */
    public function localize_addon_scripts()
    {
        // Data Table localization
        if (wp_script_is('master-addons-data-table', 'enqueued') || wp_script_is('master-addons-data-table', 'registered')) {
            $jltma_data_table_vars = array(
                'lengthMenu'        => esc_html__('Display _MENU_ records per page', 'master-addons'),
                'zeroRecords'       => esc_html__('Nothing found - sorry', 'master-addons'),
                'info'              => esc_html__('Showing page _PAGE_ of _PAGES_', 'master-addons'),
                'infoEmpty'         => esc_html__('No records available', 'master-addons'),
                'infoFiltered'      => esc_html__('(filtered from _MAX_ total records)', 'master-addons'),
                'searchPlaceholder' => esc_html__('Search...', 'master-addons'),
                'processing'        => esc_html__('Processing...', 'master-addons'),
                'csvHtml5'          => esc_html__('CSV', 'master-addons'),
                'excelHtml5'        => esc_html__('Excel', 'master-addons'),
                'pdfHtml5'          => esc_html__('PDF', 'master-addons'),
                'print'             => esc_html__('Print', 'master-addons'),
            );
            wp_localize_script('master-addons-data-table', 'JLTMA_DATA_TABLE', $jltma_data_table_vars);
        }

        // JLTMA_SCRIPTS is a single shared JS global used by several scripts
        // (bg-slider needs plugin_url; restrict-content needs ajaxurl + nonce).
        // In the Elementor editor every widget script is loaded, so localizing
        // only a subset per handle lets one script's data overwrite another's on
        // the shared global (e.g. bg-slider wiping the restrict-content nonce ->
        // "Security check failed"). Localize the FULL key set on every handle so
        // whichever wins still has everything.
        $jltma_shared_scripts_data = array(
            'plugin_url' => defined('JLTMA_URL') ? JLTMA_URL : (defined('JLTMA_PRO_URL') ? untrailingslashit(JLTMA_PRO_URL) : ''),
            'assets_url' => defined('JLTMA_ASSETS') ? untrailingslashit(JLTMA_ASSETS) : (defined('JLTMA_PRO_ASSETS') ? untrailingslashit(JLTMA_PRO_ASSETS) : ''),
            'ajaxurl'    => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('master-addons-elementor'),
        );

        // Background Slider localization (needs plugin_url for Vegas overlay images)
        if (wp_script_is('master-addons-bg-slider', 'enqueued') || wp_script_is('master-addons-bg-slider', 'registered')) {
            wp_localize_script('master-addons-bg-slider', 'JLTMA_SCRIPTS', $jltma_shared_scripts_data);
        }

        // Restrict Content localization (needs ajaxurl + nonce for AJAX calls)
        if (wp_script_is('master-addons-restrict-content', 'enqueued') || wp_script_is('master-addons-restrict-content', 'registered')) {
            wp_localize_script('master-addons-restrict-content', 'JLTMA_SCRIPTS', $jltma_shared_scripts_data);
        }

        // Allow extensions to add more localizations
        do_action('jltma/assets/localize_scripts');
    }

    /**
     * Get list of enabled widgets from settings
     */
    private function get_enabled_widgets()
    {
        $settings = \MasterAddons\Inc\Admin\Settings\Settings::get_addons() ?: [];

        $enabled = [];
        foreach ($settings as $widget_key => $is_enabled) {
            if ($is_enabled) {
                $enabled[] = $widget_key;
            }
        }

        return $enabled;
    }

    /**
     * Update widget cache when post is saved in Elementor
     */
    public function update_widget_cache($post_id, $editor_data)
    {
        $widgets = [];
        $this->extract_widgets_recursive($editor_data, $widgets);

        $unique_widgets = array_unique($widgets);
        update_post_meta($post_id, self::META_KEY, $unique_widgets);

        // Trigger cache invalidation hook for Cache Manager
        do_action('jltma/cache/invalidate_post', $post_id);
    }

    /**
     * Clear cached widget list for a post
     */
    public function clear_post_cache($post_id)
    {
        delete_post_meta($post_id, self::META_KEY);
    }

    /**
     * Check if a JS file is an ES module by looking for import/export statements
     * Only files with actual ES module syntax should get type="module"
     * jQuery IIFEs and other traditional scripts should load as regular scripts
     */
    private function is_es_module_file($file_path)
    {
        // Read just the first 512 bytes — module indicators are always near the top
        $content = @file_get_contents($file_path, false, null, 0, 512);
        if ($content === false) {
            return false;
        }

        // Check for ES module import/export syntax
        if (preg_match('/\b(import\s*[{"\']|import\s+\w|export\s+(default|{|\w))/', $content)) {
            return true;
        }

        // Check if file starts with top-level const/let/class declarations
        // These indicate Vite ES module output where imports were inlined/resolved
        // Non-module scripts start with IIFE patterns: (function, ;(function, jQuery(, !function, var
        $trimmed = ltrim($content);
        if (preg_match('/^(const |let |class )\w/', $trimmed)) {
            return true;
        }

        return false;
    }

    /**
     * Check if we're in Elementor editor or preview
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
     * Get widget assets map (for Cache Manager)
     */
    public function get_widget_assets()
    {
        return $this->widget_assets;
    }

    /**
     * Manually trigger asset detection for a post
     */
    public function refresh_post_cache($post_id)
    {
        delete_post_meta($post_id, self::META_KEY);
        return $this->parse_elementor_widgets($post_id);
    }

    /**
     * Get stats about widget usage
     */
    public function get_usage_stats()
    {
        global $wpdb;

        $meta_key = self::META_KEY;

        // Get all posts with cached widget data
        $results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct query required for bulk postmeta scan across all posts; no core API supports this efficiently
            $wpdb->prepare(
                "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s",
                $meta_key
            )
        );

        $stats = [
            'total_posts' => count($results),
            'widget_usage' => [],
        ];

        foreach ($results as $row) {
            $widgets = maybe_unserialize($row->meta_value);
            if (is_array($widgets)) {
                foreach ($widgets as $widget) {
                    if (!isset($stats['widget_usage'][$widget])) {
                        $stats['widget_usage'][$widget] = 0;
                    }
                    $stats['widget_usage'][$widget]++;
                }
            }
        }

        // Sort by usage count
        arsort($stats['widget_usage']);

        return $stats;
    }
}
