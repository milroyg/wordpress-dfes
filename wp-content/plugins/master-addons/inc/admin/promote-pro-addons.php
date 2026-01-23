<?php

namespace MasterAddons\Inc\Admin;

use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Elements;
use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Forms;
use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Marketing;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Promote Pro Addons
 * Handles promotion of pro widgets in Elementor editor
 * Dynamically gets pro elements from JLTMA_Addon_Elements
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
        if (\MasterAddons\Inc\Helper\Master_Addons_Helper::jltma_premium()) {
            return;
        }

        add_filter('elementor/editor/localize_settings', [$this, 'promote_pro_addons']);
    }

    /**
     * Extract icon from addon file by parsing get_icon() method
     *
     * @param string $key The addon key (e.g., 'ma-news-ticker')
     * @return string|null The icon class or null if not found
     */
    private function get_icon_from_file($key)
    {
        // Possible addon file locations
        $paths = [
            JLTMA_PATH . 'premium/addons/' . $key . '/' . $key . '.php',
            JLTMA_PATH . 'addons/' . $key . '/' . $key . '.php',
        ];

        foreach ($paths as $file_path) {
            if (!file_exists($file_path)) {
                continue;
            }

            $content = file_get_contents($file_path);
            if ($content === false) {
                continue;
            }

            // Match get_icon() method and extract the return value
            // Pattern matches: function get_icon() { return 'icon-class'; }
            if (preg_match('/function\s+get_icon\s*\(\s*\)\s*\{[^}]*return\s+[\'"]([^\'"]+)[\'"]\s*;/s', $content, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Get Pro Elements from centralized elements list
     *
     * @return array Pro elements with icon and categories
     */
    private function get_pro_elements()
    {
        $pro_widgets = [];

        // Collect all element sources
        $element_sources = [];

        // Get elements from JLTMA_Addon_Elements
        if (class_exists('MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Elements')) {
            JLTMA_Addon_Elements::get_instance();
            if (!empty(JLTMA_Addon_Elements::$jltma_elements)) {
                $element_sources[] = JLTMA_Addon_Elements::$jltma_elements;
            }
        }

        // Get elements from JLTMA_Addon_Forms
        if (class_exists('MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Forms')) {
            JLTMA_Addon_Forms::get_instance();
            if (!empty(JLTMA_Addon_Forms::$jltma_forms)) {
                $element_sources[] = JLTMA_Addon_Forms::$jltma_forms;
            }
        }

        // Get elements from JLTMA_Addon_Marketing
        if (class_exists('MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Marketing')) {
            JLTMA_Addon_Marketing::get_instance();
            if (!empty(JLTMA_Addon_Marketing::$jltma_marketing)) {
                $element_sources[] = JLTMA_Addon_Marketing::$jltma_marketing;
            }
        }

        // Loop through all element sources
        foreach ($element_sources as $all_elements) {
            foreach ($all_elements as $group_key => $group) {
                if (empty($group['elements'])) {
                    continue;
                }

                foreach ($group['elements'] as $element) {
                    // Only get pro elements
                    if (empty($element['is_pro']) || $element['is_pro'] !== true) {
                        continue;
                    }

                    $widget_data = [
                        'title'      => $element['title'] ?? '',
                        'name'       => $element['key'] ?? '',
                        'icon'       => 'jltma-icon eicon-lock', // Default fallback icon
                        'categories' => '["master-addons"]',     // Default category
                    ];

                    $icon_found = false;

                    // Method 1: Try to get icon from the widget class if it exists
                    if (!empty($element['class']) && class_exists($element['class'])) {
                        try {
                            $widget_instance = new $element['class']();

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
                    if (!$icon_found && !empty($element['key'])) {
                        $file_icon = $this->get_icon_from_file($element['key']);
                        if ($file_icon) {
                            $widget_data['icon'] = $file_icon;
                        }
                    }

                    // Add pro widget class for styling
                    $widget_data['icon'] .= ' jltma-pro-widget';

                    $pro_widgets[] = $widget_data;
                }
            }
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
