<?php

/**
 * Widget Assets Trait
 *
 * Provides automatic asset dependency resolution and help URL for Elementor widgets.
 * Uses Config as the single source of truth for widget configuration.
 *
 * Looks up config by class name (not widget name) to handle mismatches
 * between widget get_name() and config keys.
 *
 * Asset handle detection:
 * - Assets starting with 'ma-' or 'jltma-' → widget assets → 'master-addons-*' handle
 * - Vendor assets (from 'vendors' array) → vendor handles → 'jltma-*' handle
 * - Other assets (gridder, font-awesome-5-all, etc.) → use handle directly
 *
 * Fallback behavior:
 * - If 'assets' not defined in config, derives CSS from config key (e.g., 'ma-tooltip' → 'master-addons-tooltip')
 *
 * Example config:
 *   'ma-image-filter-gallery' => [
 *       'class'    => 'MasterAddons\Addons\Filterable_Image_Gallery',
 *       'demo_url' => 'https://master-addons.com/demos/image-gallery/',
 *       'assets'   => [
 *           'css'     => true,
 *           'js'      => true,
 *           'vendors' => ['fancybox', 'isotope', 'tilt', 'tippy'],
 *       ],
 *   ]
 *
 * Results in:
 *   get_style_depends()  → ['jltma-fancybox', 'jltma-tippy', 'master-addons-image-filter-gallery']
 *   get_script_depends() → ['jltma-fancybox', 'jltma-isotope', 'jltma-tilt', 'jltma-tippy', 'master-addons-image-filter-gallery']
 *   get_help_url()       → 'https://master-addons.com/demos/image-gallery/'
 *
 * @package MasterAddons\Inc\Traits
 * @since 2.1.0
 */

namespace MasterAddons\Inc\Traits;

use MasterAddons\Inc\Admin\Config;
use MasterAddons\Inc\Classes\Assets_Manager;

trait Widget_Assets_Trait
{
    /**
     * Cached config lookup result
     *
     * @var array|null
     */
    private $_widget_config_cache = null;

    /**
     * Get the widget's config from Config (by class name)
     *
     * @return array|null Array with 'key' and 'config', or null
     */
    protected function get_widget_config()
    {
        if ($this->_widget_config_cache === null) {
            $this->_widget_config_cache = Config::get_addon_by_class(get_class($this)) ?: false;
        }

        return $this->_widget_config_cache ?: null;
    }

    /**
     * Get the config key for this widget
     *
     * @return string|null Config key (e.g., 'ma-counter-up')
     */
    protected function get_config_key()
    {
        $config = $this->get_widget_config();
        return $config ? $config['key'] : null;
    }

    /**
     * Convert asset name to WordPress registered handle
     *
     * Auto-detects asset type:
     * - 'ma-*' or 'jltma-*' → widget asset → 'master-addons-*' handle
     * - Others (gridder, font-awesome, etc.) → vendor/external → use as-is
     *
     * @param string $asset_name Asset name from config
     * @return string WordPress registered handle
     */
    protected function asset_to_handle($asset_name)
    {
        // Check if it's a widget asset (starts with ma- or jltma-)
        if (preg_match('/^(ma-|jltma-)/', $asset_name)) {
            // Strip prefix, then add master-addons- prefix
            $handle_name = preg_replace('/^(ma-|jltma-)/', '', $asset_name);
            return 'master-addons-' . $handle_name;
        }

        // Vendor/external asset - use handle directly as registered
        return $asset_name;
    }

    /**
     * Get style dependencies from unified config
     *
     * Automatically resolves CSS dependencies based on the widget's class
     * and the asset definitions in Config. Also includes vendor CSS.
     *
     * Fallback: If no assets defined, derives from config key.
     *
     * @return array Array of registered style handles to enqueue
     */
    public function get_style_depends()
    {
        $config = $this->get_widget_config();
        $styles = [];

        if ($config) {
            // Add vendor CSS dependencies first (they should load before widget CSS)
            if (!empty($config['config']['assets']['vendors'])) {
                foreach ((array) $config['config']['assets']['vendors'] as $vendor) {
                    $vendor_config = Assets_Manager::get($vendor);
                    if ($vendor_config && !empty($vendor_config['files']['css'])) {
                        $styles[] = 'jltma-' . $vendor;
                    }
                }
            }

            if (!empty($config['config']['assets']['css'])) {
                // Handle 'css' => true format (auto-map to widget key)
                if ($config['config']['assets']['css'] === true) {
                    $styles[] = $this->asset_to_handle($config['key']);
                } else {
                    // Use explicitly defined CSS assets
                    foreach ((array) $config['config']['assets']['css'] as $css) {
                        $styles[] = $this->asset_to_handle($css);
                    }
                }
            } elseif (empty($config['config']['assets'])) {
                // Fallback: derive from config key if no assets defined at all
                $styles[] = $this->asset_to_handle($config['key']);
            }
        }

        return $styles;
    }

    /**
     * Get script dependencies from unified config
     *
     * Automatically resolves JS dependencies based on the widget's class
     * and the asset definitions in Config. Also includes vendor JS.
     *
     * @return array Array of registered script handles to enqueue
     */
    public function get_script_depends()
    {
        $config = $this->get_widget_config();
        $scripts = [];

        if ($config) {
            // Add vendor JS dependencies first (they should load before widget JS)
            if (!empty($config['config']['assets']['vendors'])) {
                foreach ((array) $config['config']['assets']['vendors'] as $vendor) {
                    $vendor_config = Assets_Manager::get($vendor);
                    if ($vendor_config && !empty($vendor_config['files']['js'])) {
                        $scripts[] = 'jltma-' . $vendor;
                    }
                }
            }

            if (!empty($config['config']['assets']['js'])) {
                // Handle 'js' => true format (auto-map to widget key)
                if ($config['config']['assets']['js'] === true) {
                    $scripts[] = $this->asset_to_handle($config['key']);
                } else {
                    foreach ((array) $config['config']['assets']['js'] as $js) {
                        $scripts[] = $this->asset_to_handle($js);
                    }
                }
            }
        }

        return $scripts;
    }

    /**
     * Get help URL from config's demo_url
     *
     * Provides the documentation/demo URL for the widget.
     *
     * @return string Help URL or empty string if not defined
     */
    public function get_help_url()
    {
        $config = $this->get_widget_config();
        return $config ? ($config['config']['demo_url'] ?? '') : '';
    }

    /**
     * Check if this widget has any registered styles
     *
     * @return bool
     */
    public function has_style_depends()
    {
        return !empty($this->get_style_depends());
    }

    /**
     * Check if this widget has any registered scripts
     *
     * @return bool
     */
    public function has_script_depends()
    {
        return !empty($this->get_script_depends());
    }

    /**
     * Get the widget's assets configuration from Config
     *
     * Useful for debugging or advanced customization.
     *
     * @return array Assets configuration array
     */
    public function get_widget_assets_config()
    {
        $config = $this->get_widget_config();
        return $config ? ($config['config']['assets'] ?? []) : [];
    }
}
