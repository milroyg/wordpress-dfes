<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * WYSIWYG Control
 * Handles Elementor WYSIWYG (What You See Is What You Get) control type
 * Rich text editor with formatting options
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default value
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Wysiwyg extends Control_Base {

    public function get_type() {
        return 'WYSIWYG';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Content';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'WYSIWYG', $is_responsive);

        // WYSIWYG doesn't have specific properties beyond common ones
        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }
}
