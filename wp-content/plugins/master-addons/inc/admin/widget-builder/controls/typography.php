<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * TYPOGRAPHY Control
 * Handles Elementor Group Control Typography
 * Comprehensive typography control with font family, size, weight, style, line height, letter spacing
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key and selector prefix)
 * - description: Description below field
 * - selector: CSS selector for applying typography styles
 * - exclude: Array of typography sub-controls to exclude (e.g., ['font_family', 'font_size'])
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - separator: Control separator position
 * - condition: Conditional display logic
 *
 * Note: This is a GROUP CONTROL, uses add_group_control instead of add_control
 */
class Typography extends Control_Base {

    public function get_type() {
        return 'GROUP_CONTROL_TYPOGRAPHY';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Typography';
        $name = !empty($field['name']) ? $field['name'] : 'typography';

        // Build group control
        $content = "\t\t\$this->add_group_control(\n";
        $content .= "\t\t\t\\Elementor\\Group_Control_Typography::get_type(),\n";
        $content .= "\t\t\t[\n";

        // Name property
        $content .= "\t\t\t\t'name' => '{$name}',\n";

        // Label property
        if (!empty($label)) {
            $content .= $this->format_string_property('label', $label);
        }

        // Selector property (required for typography)
        if (!empty($field['selector'])) {
            $content .= $this->format_plain_string_property('selector', $field['selector']);
        } else {
            // Default selector using the control name
            $content .= "\t\t\t\t'selector' => '{{WRAPPER}} .elementor-widget-container',\n";
        }

        // Global typography default value
        // Convert dropdown value to Elementor Global_Typography constant
        if (!empty($field['default'])) {
            $global_value = $this->convert_to_global_constant($field['default']);
            if ($global_value) {
                $content .= "\t\t\t\t'global' => [\n";
                $content .= "\t\t\t\t\t'default' => Global_Typography::{$global_value},\n";
                $content .= "\t\t\t\t],\n";
            }
        }

        // Exclude property - allows excluding specific typography controls
        if (!empty($field['exclude']) && is_array($field['exclude'])) {
            $content .= "\t\t\t\t'exclude' => [";
            $content .= "'" . implode("', '", array_map('esc_js', $field['exclude'])) . "'";
            $content .= "],\n";
        }

        // Add common properties (separator, condition, etc.)
        $content .= $this->build_common_properties($field);

        // Close group control array
        $content .= "\t\t\t]\n";
        $content .= "\t\t);\n\n";

        return $content;
    }

    /**
     * Convert global typography dropdown value to Elementor constant name
     *
     * @param string $value Dropdown value like 'primary', 'secondary', 'text', 'accent'
     * @return string|null Constant name like 'TYPOGRAPHY_PRIMARY' or null if not found
     */
    private function convert_to_global_constant($value) {
        $mapping = [
            'primary' => 'TYPOGRAPHY_PRIMARY',
            'secondary' => 'TYPOGRAPHY_SECONDARY',
            'text' => 'TYPOGRAPHY_TEXT',
            'accent' => 'TYPOGRAPHY_ACCENT',
        ];

        return isset($mapping[$value]) ? $mapping[$value] : null;
    }
}
