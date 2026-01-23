<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

use MasterAddons\Admin\WidgetBuilder\Control_Manager;

defined('ABSPATH') || exit;

/**
 * POPOVER_TOGGLE Control
 * Handles Elementor POPOVER_TOGGLE control type
 * Toggle control that opens/closes a popover with additional controls
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - label_off: Text for OFF state
 * - label_on: Text for ON state
 * - return_value: Value when toggled ON
 * - popover_fields: Array of fields to display inside the popover
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - condition: Conditional display logic
 */
class Popover_Toggle extends Control_Base {

    public function get_type() {
        return 'POPOVER_TOGGLE';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Popover Toggle';

        // Popover toggle does not support responsive control
        // Always set to false regardless of field settings
        $is_responsive = false;

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'POPOVER_TOGGLE', $is_responsive);

        // Add POPOVER_TOGGLE-specific properties FIRST
        $content .= $this->build_popover_toggle_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        // Add popover fields if they exist
        if (!empty($field['popover_fields']) && is_array($field['popover_fields'])) {
            $content .= $this->build_popover_fields($control_key, $field['popover_fields'], $field);
        }

        return $content;
    }

    /**
     * Build POPOVER_TOGGLE-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_popover_toggle_properties($field) {
        $content = '';

        // Label ON
        if (!empty($field['label_on'])) {
            $content .= $this->format_string_property('label_on', $field['label_on']);
        }

        // Label OFF
        if (!empty($field['label_off'])) {
            $content .= $this->format_string_property('label_off', $field['label_off']);
        }

        // Popover toggle does not have return_value
        // It returns the fields inside as an array
        // Users access values like: {{popover_toggle.field_name}}

        return $content;
    }

    /**
     * Build popover fields
     * Generates start_popover(), individual field controls, and end_popover()
     *
     * @param string $control_key Base control key
     * @param array $popover_fields Array of field configurations
     * @param array $parent_field Parent popover toggle field data
     * @return string
     */
    protected function build_popover_fields($control_key, $popover_fields, $parent_field) {
        $content = '';

        // Start popover
        $content .= "\n\t\t\$this->start_popover();\n\n";

        // Get control manager instance
        $control_manager = Control_Manager::get_instance();

        // Build each popover field
        foreach ($popover_fields as $index => $popover_field) {
            if (empty($popover_field['name']) || empty($popover_field['type'])) {
                continue;
            }

            // Create unique control key for this popover field
            $field_name = $this->sanitize_key($popover_field['name']);
            $popover_control_key = $control_key . '_' . $field_name;

            // Inherit tab context from parent field
            $popover_field['_tab'] = $parent_field['_tab'] ?? 'content';
            $popover_field['_widget_id'] = $parent_field['_widget_id'] ?? '';
            $popover_field['_tab_prefix'] = $parent_field['_tab_prefix'] ?? 'jltma_content_';

            // Set label from popover field config
            if (empty($popover_field['label'])) {
                $popover_field['label'] = ucfirst($popover_field['name']);
            }

            // Build the control using control manager
            $field_type = strtoupper($popover_field['type']);
            $content .= $control_manager->build_control($popover_control_key, $popover_field, $field_type);
        }

        // End popover
        $content .= "\t\t\$this->end_popover();\n";

        return $content;
    }
}
