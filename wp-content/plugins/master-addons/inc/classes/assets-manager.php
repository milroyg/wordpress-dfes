<?php

/**
 * Master Addons - Assets Manager
 *
 * Central registry for all vendor libraries and asset registration.
 * Handles dependency chains and provides organized asset management.
 *
 * @package MasterAddons\Inc\Classes
 * @since 2.1.0
 */

namespace MasterAddons\Inc\Classes;

if (!defined('ABSPATH')) {
    exit;
}

class Assets_Manager
{
    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Vendor registry cache
     */
    private static $registry = null;

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
        // Register vendor assets early - both frontend and admin
        add_action('wp_enqueue_scripts', [$this, 'register_all'], 5);
        add_action('admin_enqueue_scripts', [$this, 'register_all'], 5);

        // Also register in Elementor editor
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'register_all'], 5);
        add_action('elementor/preview/enqueue_scripts', [$this, 'register_all'], 5);
    }

    /**
     * Get vendor registry
     *
     * All vendor libraries with their file paths and dependencies.
     * No 'name' key needed - the array key IS the identifier.
     *
     * @return array
     */
    public static function get_registry()
    {
        if (self::$registry !== null) {
            return self::$registry;
        }

        self::$registry = [
            // =========================================================
            // TOOLTIP & POPOVER
            // =========================================================
            'popper' => [
                'files' => [
                    'js' => 'popper.min.js',
                ],
                'deps' => [],
            ],

            'tippy' => [
                'files' => [
                    'css' => 'tippyjs/css/tippy.css',
                    'js'  => 'tippyjs/js/tippy.min.js',
                ],
                'deps' => ['popper'],
            ],

            // =========================================================
            // LIGHTBOX & GALLERY
            // =========================================================
            'fancybox' => [
                'files' => [
                    'css' => 'fancybox/jquery.fancybox.min.css',
                    'js'  => 'fancybox/jquery.fancybox.min.js',
                ],
                'deps' => [],
            ],

            'gridder' => [
                'files' => [
                    'css' => 'gridder/css/jquery.gridder.min.css',
                    'js'  => 'gridder/js/jquery.gridder.min.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // GRID & FILTERING
            // =========================================================
            'isotope' => [
                'files' => [
                    'js' => 'isotope/isotope.js',
                ],
                'deps' => [],
            ],

            'macy' => [
                'files' => [
                    'js' => 'macy/macy.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // CAROUSEL & SLIDER
            // =========================================================
            'swiper' => [
                'files' => [
                    'css' => 'swiper/css/swiper.min.css',
                    'js'  => 'swiper/js/swiper-bundle.min.js',
                ],
                'deps' => [],
            ],

            'swiper-carousel' => [
                'files' => [
                    'css' => 'common/swiper-carousel.min.css',
                ],
                'path' => 'css',  // Override: assets/css/
                'deps' => ['swiper'],
            ],

            'owl-carousel' => [
                'files' => [
                    'css' => 'owlcarousel/owl.carousel.min.css',
                    'js'  => 'owlcarousel/owl.carousel.min.js',
                ],
                'deps'       => [],
                'deprecated' => true,
            ],

            // =========================================================
            // IMAGE EFFECTS
            // =========================================================
            'tilt' => [
                'files' => [
                    'js' => 'tilt/tilt.jquery.min.js',
                ],
                'deps' => [],
            ],

            'twentytwenty' => [
                'files' => [
                    'css' => 'twentytwenty/css/twentytwenty.css',
                    'js'  => 'twentytwenty/js/jquery.twentytwenty.js',
                ],
                'deps' => ['jquery-event-move'],
            ],

            'jquery-event-move' => [
                'files' => [
                    'js' => 'twentytwenty/js/jquery.event.move.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // ANIMATION
            // =========================================================
            // Note: anime.js is now bundled via ES import in modules that need it
            // reveal is now integrated into the reveal module with anime.js v4

            'rellax' => [
                'files' => [
                    'js' => 'rellax/rellax.min.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // BACKGROUND & EFFECTS
            // =========================================================
            'vegas' => [
                'files' => [
                    'css' => 'vegas/vegas.min.css',
                    'js'  => 'vegas/vegas.min.js',
                ],
                'deps' => ['common'],
            ],

            'particles' => [
                'files' => [
                    'js' => 'particles/particles.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // COUNTDOWN & TIMER
            // =========================================================
            'countdown' => [
                'files' => [
                    'js' => 'countdown/jquery.countdown.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // PROGRESS & LOADING
            // =========================================================
            'loading-bar' => [
                'files' => [
                    'js' => 'loading-bar/loading-bar.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // SCROLLING
            // =========================================================
            'perfect-scrollbar' => [
                'files' => [
                    'css' => 'perfect-scrollbar/perfect-scrollbar.min.css',
                    'js'  => 'perfect-scrollbar/perfect-scrollbar.min.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // LAZY LOADING
            // =========================================================
            'lazysizes' => [
                'files' => [
                    'js' => 'lazysizes/lazysizes.min.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // NEWS TICKER
            // =========================================================
            'jquery-rss' => [
                'files' => [
                    'js' => 'newsticker/js/jquery.rss.min.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // TABLE OF CONTENTS
            // =========================================================
            'table-of-content' => [
                'files' => [
                    'js' => 'jltma-table-of-content/jltma-table-of-content.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // TOGGLE CONTENT
            // =========================================================
            'toggle-content' => [
                'files' => [
                    'js' => 'toggle-content/toggle-content.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // ICON FONTS
            // =========================================================
            'elementor-icons' => [
                'files' => [
                    'css' => 'elementor-icons.css',
                ],
                'path' => 'fonts/elementor-icon',
                'deps' => [],
            ],

            // =========================================================
            // COMMON/SHARED STYLES
            // =========================================================
            'common' => [
                'files' => [
                    'css' => 'common/common.min.css',
                ],
                'path' => 'css',
                'deps' => [],
            ],

            // =========================================================
            // Animation Effects
            // =========================================================
            'animation' => [
                'files' => [
                    'css' => 'common/animations.css',
                ],
                'path' => 'css',
                'deps' => [],
            ],

            // =========================================================
            // FORM & UI LIBRARIES
            // =========================================================
            'select2' => [
                'files' => [
                    'css' => 'select2/select2.min.css',
                    'js'  => 'select2/select2.min.js',
                ],
                'deps' => [],
            ],

            // =========================================================
            // RECOMMENDED PLUGINS
            // =========================================================
            'recommended-plugins' => [
                'files' => [
                    'css' => 'css/admin/recommended-plugins.css',
                    'js'  => 'js/admin/recommended-plugins.js',
                ],
                'path' => '',  // Root assets folder
                'deps' => ['jquery'],
            ],

            // =========================================================
            // ADMIN SDK
            // =========================================================
            'admin-sdk' => [
                'files' => [
                    'css' => 'css/admin/master-addons-admin-sdk.css',
                    'js'  => 'js/admin/master-addons-admin-sdk.js',
                ],
                'path' => '',  // Root assets folder
                'deps' => ['jquery'],
            ],

            // =========================================================
            // ADMIN SETTINGS ASSETS
            // =========================================================
            'admin-settings' => [
                'files' => [
                    'css' => 'css/admin/admin-settings.css',
                    'js'  => 'js/admin/admin-settings.js',
                ],
                'path' => '',  // Root assets folder
                'deps' => ['react', 'react-dom', 'wp-element', 'wp-i18n', 'wp-api-fetch'],
            ],

            // =========================================================
            // SETUP WIZARD ASSETS
            // =========================================================
            'setup-wizard' => [
                'files' => [
                    'css' => 'css/admin/setup-wizard.css',
                    'js'  => 'js/admin/setup-wizard.js',
                ],
                'path' => '',  // Root assets folder
                'deps' => ['react', 'react-dom', 'wp-element', 'wp-i18n', 'wp-api-fetch'],
            ],

            // =========================================================
            // THEME BUILDER ASSETS
            // =========================================================
            'theme-builder' => [
                'files' => [
                    'css' => 'css/admin/theme-builder.css',
                    'js'  => 'js/admin/theme-builder.js',
                ],
                'path' => '',  // Root assets folder
                'deps' => ['select2'],
            ],

            // =========================================================
            // COMMENTS BUILDER
            // =========================================================
            'comments' => [
                'files' => [
                    'css' => 'css/theme-builder-comments.css',
                    'js'  => 'js/theme-builder-comments.js',
                ],
                'path' => '',  // Root assets folder
                'deps' => [],
            ],

            // =========================================================
            // POPUP BUILDER ASSETS
            // =========================================================
            'popup-builder-admin' => [
                'files' => [
                    'css' => 'css/admin/popup-builder/popup-builder.css',
                    'js'  => 'js/admin/popup-builder/popup-admin.js',
                ],
                'path' => '',
                'deps' => ['select2'],
            ],
            'popup-builder-frontend' => [
                'files' => [
                    'css' => 'css/admin/popup-builder/popup-frontend.css',
                    'js'  => 'js/admin/popup-builder/modal-popup.js',
                ],
                'path' => '',
                'deps' => ['animation'],
            ],
            'popup-builder-elementor' => [
                'files' => [
                    'css' => 'css/admin/popup-builder/elementor-editor.css',
                    'js'  => 'js/admin/popup-builder/elementor-editor.js',
                ],
                'path' => '',
                'deps' => [],
            ],

            // =========================================================
            // TEMPLATE LIBRARY & PAGE IMPORTER
            // =========================================================
            'template-library' => [
                'files' => [
                    'css' => 'css/admin/template-library.css',
                    'js'  => 'js/admin/template-library.js',
                ],
                'path' => '',  // Root assets folder
                'wp_deps' => ['wp-element', 'wp-i18n', 'wp-components', 'wp-api-fetch', 'wp-hooks', 'wp-util'],
                'wp_css_deps' => ['wp-components'],
                'deps' => [],
            ],

            'template-kits-app' => [
                'files' => [
                    'js'  => 'js/admin/template-kits-app.js',
                    'css'  => 'css/admin/template-kits-app.css',
                ],
                'path' => '',  // Root assets folder
                'wp_deps' => ['wp-element', 'wp-i18n'],
                'deps' => [],
            ],

            // =========================================================
            // Wizard Builder
            // =========================================================
            'widget-builder' => [
                'files' => [
                    'css' => 'css/admin/widget-builder.css',
                ],
                'path' => '',
                'deps' => [],
            ],

            'widget-admin' => [
                'files' => [
                    'js' => 'js/admin/widget-admin.js',
                ],
                'path' => '',
                'deps' => [],
            ],

            'widget-builder-app' => [
                'files' => [
                    'css' => 'css/admin/widget-builder-app.css',
                    'js' => 'js/admin/widget-builder-app.js',
                ],
                'path' => '',
                'deps' => [],
            ],

            'widget-builder-tooltip' => [
                'files' => [
                    'js' => 'js/admin/widget-builder-tooltip.js',
                ],
                'path' => '',
                'deps' => ['widget-builder-app'],
            ],

            'page-importer' => [
                'files' => [
                    'css' => 'css/admin/page-importer.css',
                    'js'  => 'js/admin/page-importer.js',
                ],
                'path' => '',  // Root assets folder
                'deps' => ['template-library', 'widget-builder'],
            ],

            // =========================================================
            // IMAGE OPTIMIZER (Free Version - Upsell Display)
            // =========================================================
            'image-optimizer-base' => [
                'files' => [
                    'js' => 'js/admin/image-optimizer/image-optimizer-base.js',
                ],
                'path' => '',  // Root assets folder
                'deps' => [],
            ],

            // =========================================================
            // ELEMENTOR EDITOR CONTROLS
            // =========================================================
            'module-editor' => [
                'files' => [
                    'js' => 'js/admin/module-editor.js',
                ],
                'path' => '',
                'deps' => [],
            ],

            'visual-select' => [
                'files' => [
                    'css' => 'css/admin/master-addons-editor.css',
                    'js'  => 'js/admin/visual-select.js',
                ],
                'path' => '',
                'deps' => ['module-editor'],
            ],

            'file-select-control' => [
                'files' => [
                    'js' => 'js/admin/file-select-control.js',
                ],
                'path' => '',
                'deps' => [],
            ],

            // =========================================================
            // EXTERNAL CDN LIBRARIES
            // =========================================================
            'google-recaptcha' => [
                'files' => [
                    'js' => 'https://www.google.com/recaptcha/api.js',
                ],
                'deps' => [],
                'cdn'  => true,
            ],
        ];

        // Allow extensions to add more vendors
        self::$registry = apply_filters('jltma/assets/vendor_registry', self::$registry);

        return self::$registry;
    }

    /**
     * Get single vendor config
     *
     * @param string $handle Vendor handle
     * @return array|null
     */
    public static function get($handle)
    {
        $registry = self::get_registry();
        return $registry[$handle] ?? null;
    }

    /**
     * Check if vendor exists
     *
     * @param string $handle Vendor handle
     * @return bool
     */
    public static function exists($handle)
    {
        $registry = self::get_registry();
        return isset($registry[$handle]);
    }

    /**
     * Get all dependencies for a vendor (recursive)
     *
     * @param string $handle Vendor handle
     * @return array Flat array of all dependency handles
     */
    public static function get_all_deps($handle)
    {
        $registry = self::get_registry();
        $all_deps = [];

        if (!isset($registry[$handle])) {
            return $all_deps;
        }

        foreach ($registry[$handle]['deps'] ?? [] as $dep) {
            // Get nested deps first (depth-first)
            $nested = self::get_all_deps($dep);
            $all_deps = array_merge($all_deps, $nested);
            // Then add the dep itself
            $all_deps[] = $dep;
        }

        return array_unique($all_deps);
    }

    /**
     * Topological sort for correct registration order
     * Ensures dependencies are registered before dependents
     *
     * @return array Sorted vendor registry
     */
    public static function get_sorted_registry()
    {
        $registry = self::get_registry();
        $sorted = [];
        $visited = [];

        foreach (array_keys($registry) as $handle) {
            self::visit($handle, $registry, $sorted, $visited);
        }

        return $sorted;
    }

    /**
     * Visit node for topological sort
     */
    private static function visit($handle, $registry, &$sorted, &$visited)
    {
        if (isset($visited[$handle])) {
            return;
        }

        $visited[$handle] = true;

        if (isset($registry[$handle]['deps'])) {
            foreach ($registry[$handle]['deps'] as $dep) {
                if (isset($registry[$dep])) {
                    self::visit($dep, $registry, $sorted, $visited);
                }
            }
        }

        $sorted[$handle] = $registry[$handle];
    }

    /**
     * Register all vendor assets
     * Called early in wp_enqueue_scripts
     */
    public function register_all()
    {
        $sorted = self::get_sorted_registry();
        $assets_url = defined('JLTMA_ASSETS') ? JLTMA_ASSETS : (defined('JLTMA_PRO_ASSETS') ? JLTMA_PRO_ASSETS : '');
        $vendor_url = $assets_url . 'vendor/';
        $pro_vendor_url = defined('JLTMA_PRO_ASSETS') ? JLTMA_PRO_ASSETS . 'vendor/' : '';
        $version = defined('JLTMA_VER') ? JLTMA_VER : (defined('JLTMA_PRO_VER') ? JLTMA_PRO_VER : '1.0.0');
        $suffix = defined('JLTMA_SCRIPT_SUFFIX') ? JLTMA_SCRIPT_SUFFIX : '.min';

        foreach ($sorted as $handle => $vendor) {
            // Skip pro vendors if not licensed
            if (!empty($vendor['pro']) && !Helper::jltma_premium()) {
                continue;
            }

            $wp_handle = 'jltma-' . $handle;

            // Determine base URL based on path override and pro status
            $is_pro = !empty($vendor['pro']);
            $pro_assets = defined('JLTMA_PRO_ASSETS') ? JLTMA_PRO_ASSETS : $assets_url;
            $base_assets = $is_pro ? $pro_assets : $assets_url;

            if (isset($vendor['path'])) {
                // Custom path: use pro or free assets base + custom path
                $base_url = $base_assets . ($vendor['path'] ? $vendor['path'] . '/' : '');
            } elseif ($is_pro && $pro_vendor_url) {
                // Pro vendor: use pro vendor folder
                $base_url = $pro_vendor_url;
            } else {
                // Free vendor: use free vendor folder
                $base_url = $vendor_url;
            }

            // Build dependency arrays for WordPress
            $css_deps = $vendor['wp_css_deps'] ?? [];
            $js_deps = array_merge(['jquery'], $vendor['wp_deps'] ?? []);

            foreach ($vendor['deps'] ?? [] as $dep) {
                $dep_handle = 'jltma-' . $dep;
                if (isset($sorted[$dep]['files']['css'])) {
                    $css_deps[] = $dep_handle;
                }
                if (isset($sorted[$dep]['files']['js'])) {
                    $js_deps[] = $dep_handle;
                }
            }

            // Register CSS
            if (!empty($vendor['files']['css'])) {
                // Resolve to the built variant that exists on disk (.min in production,
                // source in debug) so a missing variant never 404s.
                $css_file = $this->resolve_built_file($vendor['files']['css'], $suffix, $vendor, $is_pro, '.css');

                // Check if it's a CDN/full URL or relative path
                $css_url = $this->is_external_url($vendor['files']['css'])
                    ? $vendor['files']['css']
                    : $base_url . $css_file;

                wp_register_style(
                    $wp_handle,
                    $css_url,
                    $css_deps,
                    !empty($vendor['cdn']) ? null : $this->resolve_asset_version($css_file, $vendor, $is_pro, $version)
                );
            }

            // Register JS
            if (!empty($vendor['files']['js'])) {
                $js_file = $this->resolve_built_file($vendor['files']['js'], $suffix, $vendor, $is_pro, '.js');

                // Check if it's a CDN/full URL or relative path
                $js_url = $this->is_external_url($vendor['files']['js'])
                    ? $vendor['files']['js']
                    : $base_url . $js_file;

                // Load in footer by default (true), unless in_header is explicitly true
                $in_footer = isset($vendor['in_header']) && $vendor['in_header'] === true ? false : true;

                wp_register_script(
                    $wp_handle,
                    $js_url,
                    $js_deps,
                    !empty($vendor['cdn']) ? null : $this->resolve_asset_version($js_file, $vendor, $is_pro, $version),
                    $in_footer
                );
            }
        }

        // Register Font Awesome with global handle (if not already registered by Elementor/other plugins)
        $this->register_global_assets();

        // Allow extensions to register additional assets
        do_action('jltma/assets/after_register_vendors');
    }

    /**
     * Resolve the version string for an asset.
     *
     * Local, built assets (path override of '', non-CDN, non-external) are versioned
     * by their file modification time so a rebuild or plugin update busts the browser
     * cache automatically. Everything else falls back to the plugin version.
     *
     * @param string $relative_file Asset path relative to its assets base (already suffixed).
     * @param array  $vendor        Registry entry for the asset.
     * @param bool   $is_pro        Whether the asset lives in the premium assets folder.
     * @param string $fallback      Version to use when mtime is unavailable.
     * @return string
     */
    /**
     * Resolve a locally-built asset to the variant that actually exists on disk.
     *
     * Production zips ship only the minified (.min) build; debug/dev trees may have
     * only the unminified source. Picking by file existence (preferring .min in
     * production, source in debug) guarantees the registered URL never 404s — a
     * missing variant otherwise returns the HTML error page, which the browser then
     * parses as JS ("Unexpected token '<'") and the React panel never mounts.
     *
     * Non-local assets (vendors, CDN, external, custom path) are returned unchanged.
     *
     * @param string $relative Asset path relative to its assets base.
     * @param string $suffix   '.min' in production, '' in debug.
     * @param array  $vendor   Registry entry.
     * @param bool   $is_pro   Whether the asset lives in the premium assets folder.
     * @param string $ext      File extension including dot (e.g. '.js', '.css').
     * @return string
     */
    private function resolve_built_file($relative, $suffix, $vendor, $is_pro, $ext)
    {
        $is_local = isset($vendor['path'])
            && $vendor['path'] === ''
            && empty($vendor['cdn'])
            && !$this->is_external_url($relative);

        if (!$is_local) {
            return $relative;
        }

        $base_dir = $is_pro
            ? (defined('JLTMA_PRO_PATH') ? trailingslashit(JLTMA_PRO_PATH) . 'premium/assets/' : '')
            : (defined('JLTMA_PATH') ? trailingslashit(JLTMA_PATH) . 'assets/' : '');

        $min = preg_replace('/' . preg_quote($ext, '/') . '$/', '.min' . $ext, $relative);

        // Prefer minified in production; prefer source in debug.
        $candidates = ($suffix === '.min') ? array($min, $relative) : array($relative, $min);

        if ($base_dir) {
            foreach ($candidates as $candidate) {
                if (is_file($base_dir . $candidate)) {
                    return $candidate;
                }
            }
        }

        return $candidates[0];
    }

    private function resolve_asset_version($relative_file, $vendor, $is_pro, $fallback)
    {
        $is_local = isset($vendor['path'])
            && $vendor['path'] === ''
            && empty($vendor['cdn'])
            && !$this->is_external_url($relative_file);

        if (!$is_local) {
            return $fallback;
        }

        $base_dir = $is_pro
            ? (defined('JLTMA_PRO_PATH') ? trailingslashit(JLTMA_PRO_PATH) . 'premium/assets/' : '')
            : (defined('JLTMA_PATH') ? trailingslashit(JLTMA_PATH) . 'assets/' : '');

        if ($base_dir) {
            $file_path = $base_dir . $relative_file;
            if (is_file($file_path)) {
                return (string) filemtime($file_path);
            }
        }

        return $fallback;
    }

    /**
     * Get global assets registry
     *
     * These use standard global handles shared across plugins/themes
     * to avoid duplicate loading (e.g., Font Awesome, Google Fonts).
     *
     * @return array
     */
    private static function get_global_assets()
    {
        $assets = [];

        // Use Font Awesome bundled with Elementor (a required dependency) instead of a CDN.
        if (defined('ELEMENTOR_ASSETS_URL')) {
            $assets['font-awesome-5-all-css'] = [
                'type'    => 'css',
                'url'     => ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.min.css',
                'deps'    => [],
                'version' => '6.5.1',
            ];
        }

        return $assets;
    }

    /**
     * Register global assets with standard handles
     *
     * Uses standard handles (e.g., 'font-awesome-5-all-css') so assets
     * are not duplicated if already loaded by Elementor or other plugins.
     */
    private function register_global_assets()
    {
        $global_assets = self::get_global_assets();

        foreach ($global_assets as $handle => $asset) {
            // Skip if already registered by another plugin/theme
            if ($asset['type'] === 'css' && wp_style_is($handle, 'registered')) {
                continue;
            }
            if ($asset['type'] === 'js' && wp_script_is($handle, 'registered')) {
                continue;
            }

            // Register the asset
            if ($asset['type'] === 'css') {
                wp_register_style($handle, $asset['url'], $asset['deps'], $asset['version']);
            } else {
                wp_register_script($handle, $asset['url'], $asset['deps'], $asset['version'], true);
            }
        }
    }

    /**
     * Enqueue vendor assets by handle
     *
     * @param string|array $handles Single handle or array of handles
     */
    public static function enqueue($handles)
    {
        $handles = (array) $handles;

        foreach ($handles as $handle) {
            // Recursively enqueue dependencies first (for CSS - JS deps are handled by WP)
            self::enqueue_with_deps($handle);
        }
    }

    /**
     * Enqueue a vendor with all its dependencies (including CSS)
     * WordPress handles JS dependencies automatically, but not CSS
     *
     * @param string $handle Vendor handle
     * @param array $enqueued Track already enqueued to avoid duplicates
     */
    private static function enqueue_with_deps($handle, &$enqueued = [])
    {
        if (isset($enqueued[$handle])) {
            return;
        }

        $vendor = self::get($handle);
        if (!$vendor) {
            return;
        }

        // Enqueue dependencies first
        foreach ($vendor['deps'] ?? [] as $dep) {
            self::enqueue_with_deps($dep, $enqueued);
        }

        $wp_handle = 'jltma-' . $handle;

        // Enqueue CSS if exists
        if (!empty($vendor['files']['css'])) {
            wp_enqueue_style($wp_handle);
        }

        // Enqueue JS if exists
        if (!empty($vendor['files']['js'])) {
            wp_enqueue_script($wp_handle);
        }

        $enqueued[$handle] = true;
    }

    /**
     * Get WordPress handle for a vendor
     *
     * @param string $handle Vendor handle
     * @return string WordPress handle with jltma- prefix
     */
    public static function get_wp_handle($handle)
    {
        return 'jltma-' . $handle;
    }

    /**
     * Check if a vendor is deprecated
     *
     * @param string $handle Vendor handle
     * @return bool
     */
    public static function is_deprecated($handle)
    {
        $vendor = self::get($handle);
        return !empty($vendor['deprecated']);
    }

    /**
     * Check if a vendor is pro-only
     *
     * @param string $handle Vendor handle
     * @return bool
     */
    public static function is_pro($handle)
    {
        $vendor = self::get($handle);
        return !empty($vendor['pro']);
    }

    /**
     * Get all vendor handles
     *
     * @return array
     */
    public static function get_all_handles()
    {
        return array_keys(self::get_registry());
    }

    /**
     * Check if a URL is external (CDN/full URL)
     *
     * @param string $url URL to check
     * @return bool
     */
    private function is_external_url($url)
    {
        return strpos($url ?? '', '//') === 0 || strpos($url ?? '', 'http') === 0;
    }

    /**
     * Debug: Get registration info for a vendor
     *
     * @param string $handle Vendor handle
     * @return array
     */
    public static function debug_vendor($handle)
    {
        $vendor = self::get($handle);
        if (!$vendor) {
            return ['error' => 'Vendor not found: ' . $handle];
        }

        $wp_handle = 'jltma-' . $handle;
        $all_deps = self::get_all_deps($handle);

        return [
            'handle'     => $handle,
            'wp_handle'  => $wp_handle,
            'files'      => $vendor['files'] ?? [],
            'deps'       => $vendor['deps'] ?? [],
            'all_deps'   => $all_deps,
            'path'       => $vendor['path'] ?? 'vendor',
            'deprecated' => !empty($vendor['deprecated']),
            'pro'        => !empty($vendor['pro']),
            'css_registered' => wp_style_is($wp_handle, 'registered'),
            'js_registered'  => wp_script_is($wp_handle, 'registered'),
        ];
    }
}

// Initialize Assets Manager
Assets_Manager::get_instance();
