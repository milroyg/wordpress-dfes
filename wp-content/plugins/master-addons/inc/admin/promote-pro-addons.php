<?php

namespace MasterAddons\Inc\Admin;

use MasterAddons\Inc\Classes\Helper;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Promote Pro Addons
 * Handles promotion of pro widgets in Elementor editor
 * Dynamically gets pro elements from Config
 */
class Promote_Pro_Addons
{
    /**
     * Instance
     *
     * @var Promote_Pro_Addons
     */
    private static $instance = null;

    /**
     * Get Instance
     *
     * @return Promote_Pro_Addons
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        // Don't show pro promotions if Pro version is active
        if (Helper::jltma_premium()) {
            return;
        }

        add_filter('elementor/editor/localize_settings', [$this, 'promote_pro_addons']);
    }

    /**
     * Extract icon from addon file by parsing get_icon() method
     * Searches in group/subcategory structure
     *
     * @param string $key The addon key (e.g., 'ma-news-ticker')
     * @param array $addon The addon config data
     * @return string|null The icon class or null if not found
     */
    private function get_icon_from_file($key, $addon = [])
    {
        $group = $addon['group'] ?? '';
        $subcategory = $addon['subcategory'] ?? '';

        // Build possible paths in order of priority
        $paths = [];

        // New structure: group/subcategory/widget/widget.php
        if ($group && $subcategory) {
            if (defined('JLTMA_PRO_ADDONS')) {
                $paths[] = JLTMA_PRO_ADDONS . $group . '/' . $subcategory . '/' . $key . '/' . $key . '.php';
            }
            if (defined('JLTMA_ADDONS')) {
                $paths[] = JLTMA_ADDONS . $group . '/' . $subcategory . '/' . $key . '/' . $key . '.php';
            }
        }

        // Dynamic scan as fallback
        $base_dirs = defined('JLTMA_ADDONS') ? [JLTMA_ADDONS] : [];
        if (defined('JLTMA_PRO_ADDONS')) {
            array_unshift($base_dirs, JLTMA_PRO_ADDONS);
        }
        foreach ($base_dirs as $base_dir) {
            if (!is_dir($base_dir)) continue;
            $groups = array_filter(glob($base_dir . '*'), 'is_dir');
            foreach ($groups as $group_path) {
                $subcategories = array_filter(glob($group_path . '/*'), 'is_dir');
                foreach ($subcategories as $subcategory_path) {
                    $paths[] = $subcategory_path . '/' . $key . '/' . $key . '.php';
                }
            }
        }

        foreach ($paths as $file_path) {
            if (!file_exists($file_path)) {
                continue;
            }

            $content = file_get_contents($file_path);
            if ($content === false) {
                continue;
            }

            // Match get_icon() method and extract the return value
            if (preg_match('/function\s+get_icon\s*\(\s*\)\s*\{[^}]*return\s+[\'"]([^\'"]+)[\'"]\s*;/s', $content, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Get Pro Elements from centralized config
     *
     * @return array Pro elements with icon and categories
     */
    private function get_pro_elements()
    {
        $pro_widgets = [];

        // Get all pro addons from unified config
        $all_addons = Config::get_addons();

        foreach ($all_addons as $key => $addon) {
            // Only get pro elements
            if (empty($addon['is_pro']) || $addon['is_pro'] !== true) {
                continue;
            }

            $widget_data = [
                'title'      => $addon['title'] ?? '',
                'name'       => $key,
                'icon'       => 'jltma-icon eicon-lock', // Default fallback icon
                'categories' => '["master-addons"]',     // Default category
            ];

            $icon_found = false;

            // Method 1: Try to get icon from the widget class if it exists
            if (!empty($addon['class']) && class_exists($addon['class'])) {
                try {
                    $widget_instance = new $addon['class']();

                    if (method_exists($widget_instance, 'get_icon')) {
                        $widget_data['icon'] = $widget_instance->get_icon();
                        $icon_found = true;
                    }

                    if (method_exists($widget_instance, 'get_categories')) {
                        $categories = $widget_instance->get_categories();
                        $widget_data['categories'] = json_encode($categories);
                    }
                } catch (\Exception $e) {
                    // Silently fail
                }
            }

            // Method 2: If class doesn't exist, extract icon from addon file
            if (!$icon_found) {
                $file_icon = $this->get_icon_from_file($key, $addon);
                if ($file_icon) {
                    $widget_data['icon'] = $file_icon;
                }
            }

            // Add pro widget class for styling
            $widget_data['icon'] .= ' jltma-pro-widget';

            $pro_widgets[] = $widget_data;
        }

        return $pro_widgets;
    }

    /**
     * Promote Pro Addons in Elementor Editor
     *
     * @param array $config Elementor localize settings config
     * @return array Modified config
     */
    public function promote_pro_addons($config)
    {
        $promotion_widgets = [];

        if (isset($config['promotionWidgets'])) {
            $promotion_widgets = $config['promotionWidgets'];
        }

        // Get pro elements dynamically
        $pro_elements = $this->get_pro_elements();

        if (!empty($pro_elements)) {
            $promotion_widgets = array_merge($promotion_widgets, $pro_elements);
        }

        $config['promotionWidgets'] = $promotion_widgets;

        return $config;
    }
}
