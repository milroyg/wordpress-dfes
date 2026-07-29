<?php
namespace MasterAddons\Inc\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * HEADING Control
 * Handles Elementor HEADING control type
 * Section heading/title control for organizing widget settings
 *
 * Supported properties:
 * - label: The heading text to display
 * - name: Field name (used for control key)
 *
 * Common settings (handled by base class):
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Heading extends Control_Base {

    public function get_type() {
        return 'HEADING';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Heading';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'HEADING', $is_responsive);

        // HEADING doesn't have specific properties beyond common ones
        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    protected function default_label() {
        return 'Heading';
    }
}
