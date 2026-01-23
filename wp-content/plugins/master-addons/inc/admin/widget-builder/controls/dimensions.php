<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * DIMENSIONS Control
 * Handles Elementor DIMENSIONS control type
 * Multiple input control for dimensions (top, right, bottom, left) like padding, margin, border-radius
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default dimensions array ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => '', 'isLinked' => true]
 * - size_units: Available size units (array: px, %, em, rem, etc.)
 * - allowed_dimensions: Which dimensions to show ('all', 'horizontal', 'vertical', or array of specific dimensions)
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - responsive: Enable responsive control
 * - separator: Control separator position
 * - condition: Conditional display logic
 * - selectors: CSS selectors for styling
 */
class Dimensions extends Control_Base {

    public function get_type() {
        return 'DIMENSIONS';
    }

    public function build($control_key, $field) {
        // Ensure proper default structure for DIMENSIONS control
        if (!isset($field['default']) || (is_string($field['default']) && $field['default'] === '')) {
            $field['default'] = [
                'top' => '',
                'right' => '',
                'bottom' => '',
                'left' => '',
                'unit' => 'px',
                'isLinked' => true,
            ];
        }

        // Ensure size_units are always set for dimensions control
        if (empty($field['size_units']) || !is_array($field['size_units'])) {
            $field['size_units'] = ['px', '%', 'em', 'rem', 'vh', 'vw'];
        }

        // Handle property_type to auto-generate selectors
        if (!empty($field['property_type']) && $field['property_type'] !== 'custom') {
            // If no selectors or empty, create default with {{WRAPPER}}
            if (empty($field['selectors']) || !is_array($field['selectors'])) {
                $field['selectors'] = [
                    [
                        'selector' => '{{WRAPPER}}',
                        'properties' => ''
                    ]
                ];
            }

            // Generate appropriate CSS property based on property type
            $css_property = '';
            switch ($field['property_type']) {
                case 'margin':
                    $css_property = 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                    break;
                case 'padding':
                    $css_property = 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                    break;
                case 'border-width':
                    $css_property = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;';
                    break;
                case 'border-radius':
                    $css_property = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                    break;
            }

            // Update all selectors with the appropriate CSS property
            if ($css_property) {
                foreach ($field['selectors'] as &$selector) {
                    $selector['properties'] = $css_property;
                }
            }
        }

        $label = !empty($field['label']) ? $field['label'] : 'Dimensions';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'DIMENSIONS', $is_responsive);

        // Add DIMENSIONS-specific properties FIRST
        $content .= $this->build_dimensions_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build DIMENSIONS-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_dimensions_properties($field) {
        $content = '';

        // Size units - MUST be set before default for Elementor to recognize them
        if (!empty($field['size_units']) && is_array($field['size_units'])) {
            $content .= "\t\t\t\t'size_units' => ['" . implode("', '", array_map('esc_js', $field['size_units'])) . "'],\n";
        }

        // Range for each unit type (required for proper unit functionality)
        if (!empty($field['size_units']) && is_array($field['size_units'])) {
            $content .= "\t\t\t\t'range' => [\n";
            foreach ($field['size_units'] as $unit) {
                switch ($unit) {
                    case 'px':
                        $content .= "\t\t\t\t\t'px' => [\n";
                        $content .= "\t\t\t\t\t\t'min' => 0,\n";
                        $content .= "\t\t\t\t\t\t'max' => 100,\n";
                        $content .= "\t\t\t\t\t],\n";
                        break;
                    case '%':
                        $content .= "\t\t\t\t\t'%' => [\n";
                        $content .= "\t\t\t\t\t\t'min' => 0,\n";
                        $content .= "\t\t\t\t\t\t'max' => 100,\n";
                        $content .= "\t\t\t\t\t],\n";
                        break;
                    case 'em':
                        $content .= "\t\t\t\t\t'em' => [\n";
                        $content .= "\t\t\t\t\t\t'min' => 0,\n";
                        $content .= "\t\t\t\t\t\t'max' => 10,\n";
                        $content .= "\t\t\t\t\t],\n";
                        break;
                    case 'rem':
                        $content .= "\t\t\t\t\t'rem' => [\n";
                        $content .= "\t\t\t\t\t\t'min' => 0,\n";
                        $content .= "\t\t\t\t\t\t'max' => 10,\n";
                        $content .= "\t\t\t\t\t],\n";
                        break;
                    case 'vh':
                        $content .= "\t\t\t\t\t'vh' => [\n";
                        $content .= "\t\t\t\t\t\t'min' => 0,\n";
                        $content .= "\t\t\t\t\t\t'max' => 100,\n";
                        $content .= "\t\t\t\t\t],\n";
                        break;
                    case 'vw':
                        $content .= "\t\t\t\t\t'vw' => [\n";
                        $content .= "\t\t\t\t\t\t'min' => 0,\n";
                        $content .= "\t\t\t\t\t\t'max' => 100,\n";
                        $content .= "\t\t\t\t\t],\n";
                        break;
                    case 'custom':
                        $content .= "\t\t\t\t\t'custom' => [\n";
                        $content .= "\t\t\t\t\t\t'min' => 0,\n";
                        $content .= "\t\t\t\t\t\t'max' => 100,\n";
                        $content .= "\t\t\t\t\t],\n";
                        break;
                }
            }
            $content .= "\t\t\t\t],\n";
        }

        // Allowed dimensions
        if (!empty($field['allowed_dimensions'])) {
            if (is_array($field['allowed_dimensions'])) {
                $content .= "\t\t\t\t'allowed_dimensions' => ['" . implode("', '", array_map('esc_js', $field['allowed_dimensions'])) . "'],\n";
            } else {
                $content .= "\t\t\t\t'allowed_dimensions' => '" . esc_js($field['allowed_dimensions']) . "',\n";
            }
        }

        return $content;
    }
}
