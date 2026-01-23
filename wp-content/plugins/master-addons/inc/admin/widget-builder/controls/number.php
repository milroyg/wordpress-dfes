<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * NUMBER Control
 * Handles Elementor NUMBER control type
 *
 * Supported properties:
 * - label: Control label
 * - type: NUMBER
 * - default: Default value
 * - placeholder: Placeholder text
 * - min: Minimum value
 * - max: Maximum value
 * - step: Step increment
 * - description: Description below field
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - separator: Control separator position
 */
class Number extends Control_Base {

    public function get_type() {
        return 'NUMBER';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Number';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        $content = $this->build_control_header($control_key, $label, 'NUMBER', $is_responsive);

        // Add NUMBER-specific properties first
        $content .= $this->build_number_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build NUMBER-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_number_properties($field) {
        $content = '';

        // Min value - only add if not empty and not null
        if (isset($field['min']) && $field['min'] !== '' && $field['min'] !== null) {
            $content .= $this->format_number_property('min', $field['min']);
        }

        // Max value - only add if not empty and not null
        if (isset($field['max']) && $field['max'] !== '' && $field['max'] !== null) {
            $content .= $this->format_number_property('max', $field['max']);
        }

        // Step value - only add if not empty and not null
        if (isset($field['step']) && $field['step'] !== '' && $field['step'] !== null) {
            $content .= $this->format_number_property('step', $field['step']);
        }

        return $content;
    }
}
