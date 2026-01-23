<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * COLOR Control
 * Handles Elementor COLOR control type
 * Color picker control for selecting colors
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default color value (hex, rgb, rgba)
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 * - selectors: CSS selectors for styling
 */
class Color extends Control_Base {

    public function get_type() {
        return 'COLOR';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Color';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'COLOR', $is_responsive);

        // COLOR doesn't have specific properties beyond common ones
        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }
}
