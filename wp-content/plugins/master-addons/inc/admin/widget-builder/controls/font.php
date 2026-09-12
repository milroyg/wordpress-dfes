<?php
namespace MasterAddons\Inc\Admin\WidgetBuilder\Controls;

use MasterAddons\Inc\Admin\WidgetBuilder\Controls\Control_Base;

defined('ABSPATH') || exit;

/**
 * FONT Control
 * Handles Elementor FONT control type
 * Font family selector control
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default font family
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - separator: Control separator position
 * - condition: Conditional display logic
 * - selectors: CSS selectors for styling
 */
class Font extends Control_Base {

    public function get_type() {
        return 'FONT';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Font Family';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'FONT', $is_responsive);

        // FONT doesn't have specific properties beyond common ones
        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    protected function default_label() {
        return 'Font Family';
    }
}
