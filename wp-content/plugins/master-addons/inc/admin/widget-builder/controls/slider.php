<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * SLIDER Control
 * Handles Elementor SLIDER control type
 * Range slider control with min, max, and step values
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default slider value (array with 'size' and optional 'unit')
 * - min: Minimum value
 * - max: Maximum value
 * - step: Step increment
 * - size_units: Available size units (array: px, %, em, rem, etc.)
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
class Slider extends Control_Base {

    public function get_type() {
        return 'SLIDER';
    }

    public function build($control_key, $field) {
        // Ensure proper default structure for SLIDER control
        if (!isset($field['default']) || (is_string($field['default']) && $field['default'] === '')) {
            $field['default'] = [
                'unit' => 'px',
                'size' => '',
            ];
        }

        $label = !empty($field['label']) ? $field['label'] : 'Slider';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'SLIDER', $is_responsive);

        // Add SLIDER-specific properties FIRST
        $content .= $this->build_slider_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build SLIDER-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_slider_properties($field) {
        $content = '';

        // Size units - use defaults if not provided
        if (!empty($field['size_units']) && is_array($field['size_units'])) {
            $size_units = $field['size_units'];
        } else {
            // Default size units
            $size_units = ['px', '%', 'em', 'rem'];
        }
        $content .= "\t\t\t\t'size_units' => ['" . implode("', '", array_map('esc_js', $size_units)) . "'],\n";

        // Range - min, max, step for each size unit
        // Check if we have top-level min/max/step properties from widget builder
        $has_custom_values = (isset($field['min']) && $field['min'] !== '') ||
                            (isset($field['max']) && $field['max'] !== '') ||
                            (isset($field['step']) && $field['step'] !== '');

        if ($has_custom_values) {
            // Build range from widget builder settings
            $min_value = isset($field['min']) && is_numeric($field['min']) ? floatval($field['min']) : 0;
            $max_value = isset($field['max']) && is_numeric($field['max']) ? floatval($field['max']) : 100;
            $step_value = isset($field['step']) && is_numeric($field['step']) ? floatval($field['step']) : 1;

            // Apply these values to all size units
            $range = [];
            foreach ($size_units as $unit) {
                $range[$unit] = [
                    'min' => $min_value,
                    'max' => $max_value,
                    'step' => $step_value,
                ];
            }
        } elseif (!empty($field['range']) && is_array($field['range'])) {
            // Use pre-defined range array
            $range = $field['range'];
        } else {
            // Default ranges for common units
            $range = [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 0,
                    'max' => 10,
                    'step' => 0.1,
                ],
                'rem' => [
                    'min' => 0,
                    'max' => 10,
                    'step' => 0.1,
                ],
            ];
        }

        $content .= "\t\t\t\t'range' => [\n";
        foreach ($range as $unit => $values) {
            $content .= "\t\t\t\t\t'" . esc_js($unit) . "' => [\n";
            if (isset($values['min'])) {
                $content .= "\t\t\t\t\t\t'min' => " . (is_numeric($values['min']) ? $values['min'] : 0) . ",\n";
            }
            if (isset($values['max'])) {
                $content .= "\t\t\t\t\t\t'max' => " . (is_numeric($values['max']) ? $values['max'] : 100) . ",\n";
            }
            if (isset($values['step'])) {
                $content .= "\t\t\t\t\t\t'step' => " . (is_numeric($values['step']) ? $values['step'] : 1) . ",\n";
            }
            $content .= "\t\t\t\t\t],\n";
        }
        $content .= "\t\t\t\t],\n";

        return $content;
    }
}
