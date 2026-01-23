<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * SELECT Control
 * Handles Elementor SELECT control type
 * Dropdown select control with options
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default selected value
 * - options: Array of options (key => label pairs)
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - responsive: Enable responsive control
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Select extends Control_Base {

    public function get_type() {
        return 'SELECT';
    }

    public function build($control_key, $field) {
        // Set first option as default if no default is set
        if (!isset($field['default']) || $field['default'] === '') {
            if (!empty($field['options']) && is_array($field['options'])) {
                // Handle array of objects from UI
                if (isset($field['options'][0]) && is_array($field['options'][0]) && isset($field['options'][0]['value'])) {
                    $field['default'] = $field['options'][0]['value'];
                }
                // Handle associative array
                else if (!isset($field['options'][0])) {
                    $firstKey = array_key_first($field['options']);
                    if ($firstKey !== null) {
                        $field['default'] = $firstKey;
                    }
                }
            }
        }

        $label = !empty($field['label']) ? $field['label'] : 'Select';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'SELECT', $is_responsive);

        // Add SELECT-specific properties FIRST
        $content .= $this->build_select_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build SELECT-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_select_properties($field) {
        $content = '';

        // Options - dropdown options
        if (!empty($field['options']) && is_array($field['options'])) {
            $content .= $this->build_options($field['options']);
        }

        return $content;
    }

    /**
     * Build options array
     *
     * @param array $options
     * @return string
     */
    private function build_options($options) {
        $content = "\t\t\t\t'options' => [\n";

        // Check if options is an array of objects (from the UI) or key-value pairs
        if (!empty($options) && is_array($options)) {
            // Check if this is a numeric array (array of objects from UI)
            if (isset($options[0]) && is_array($options[0])) {
                // Handle array of objects [{value: 'val1', label: 'Label 1'}, ...]
                foreach ($options as $option) {
                    if (isset($option['value']) && isset($option['label'])) {
                        $value = esc_js($option['value']);
                        $label = esc_js($option['label']);
                        $content .= "\t\t\t\t\t'" . $value . "' => esc_html__('" . $label . "', 'master-addons'),\n";
                    }
                }
            } else {
                // Handle associative array (key => label pairs)
                foreach ($options as $key => $label) {
                    // If label is an array (shouldn't happen but let's be safe)
                    if (is_array($label)) {
                        if (isset($label['label'])) {
                            $label = $label['label'];
                        } else {
                            $label = 'Option ' . $key;
                        }
                    }
                    $content .= "\t\t\t\t\t'" . esc_js($key) . "' => esc_html__('" . esc_js($label) . "', 'master-addons'),\n";
                }
            }
        }

        $content .= "\t\t\t\t],\n";
        return $content;
    }
}
