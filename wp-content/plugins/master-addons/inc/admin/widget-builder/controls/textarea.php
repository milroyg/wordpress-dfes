<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * TEXTAREA Control
 * Handles Elementor TEXTAREA control type
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - placeholder: Placeholder text
 * - default: Default value
 * - rows: Number of rows for textarea
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - responsive: Enable responsive control
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 * - selectors: CSS selectors for styling
 */
class Textarea extends Control_Base {

    public function get_type() {
        return 'TEXTAREA';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Textarea';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'TEXTAREA', $is_responsive);

        // Add TEXTAREA-specific properties FIRST
        $content .= $this->build_textarea_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build TEXTAREA-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_textarea_properties($field) {
        $content = '';

        // Rows - number of visible text lines
        if (isset($field['rows']) && $field['rows'] !== '' && $field['rows'] !== null) {
            $content .= $this->format_number_property('rows', intval($field['rows']));
        }

        return $content;
    }
}
