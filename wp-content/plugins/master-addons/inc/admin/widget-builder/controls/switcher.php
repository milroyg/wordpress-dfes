<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * SWITCHER Control
 * Handles Elementor SWITCHER control type
 * Toggle switch control with On/Off states
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default value (yes/no or custom return_value)
 * - label_on: Text for ON state (default: 'Yes')
 * - label_off: Text for OFF state (default: 'No')
 * - return_value: Value when ON (default: 'yes')
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Switcher extends Control_Base {

    public function get_type() {
        return 'SWITCHER';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Switcher';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'SWITCHER', $is_responsive);

        // Add SWITCHER-specific properties FIRST
        $content .= $this->build_switcher_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build SWITCHER-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_switcher_properties($field) {
        $content = '';

        // Label ON - text displayed for ON state
        if (!empty($field['label_on'])) {
            $content .= $this->format_string_property('label_on', $field['label_on']);
        }

        // Label OFF - text displayed for OFF state
        if (!empty($field['label_off'])) {
            $content .= $this->format_string_property('label_off', $field['label_off']);
        }

        // Return value - value returned when switcher is ON
        if (isset($field['return_value'])) {
            $content .= $this->format_plain_string_property('return_value', $field['return_value']);
        }

        return $content;
    }
}
