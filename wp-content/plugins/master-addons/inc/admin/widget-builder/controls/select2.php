<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * SELECT2 Control
 * Handles Elementor SELECT2 control type
 * Enhanced select dropdown with search and multiple selection capabilities
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default selected value(s)
 * - options: Array of options (key => label pairs)
 * - multiple: Allow multiple selections (boolean)
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Select2 extends Control_Base {

    public function get_type() {
        return 'SELECT2';
    }

    public function build($control_key, $field) {
        // Ensure proper default structure for SELECT2 control
        // If multiple is true, default must be an array
        if (isset($field['multiple']) && $field['multiple']) {
            // If default is a string, convert it to an array
            if (isset($field['default']) && is_string($field['default']) && $field['default'] !== '') {
                $field['default'] = [$field['default']];
            }
            // If default is not set or empty, set first option as default array
            else if (!isset($field['default']) || $field['default'] === '' || (is_array($field['default']) && empty($field['default']))) {
                // Set first option as default if options exist
                if (!empty($field['options']) && is_array($field['options'])) {
                    // Handle array of objects from UI
                    if (isset($field['options'][0]) && is_array($field['options'][0]) && isset($field['options'][0]['value'])) {
                        $field['default'] = [$field['options'][0]['value']];
                    }
                    // Handle associative array
                    else if (!isset($field['options'][0])) {
                        $firstKey = array_key_first($field['options']);
                        if ($firstKey !== null) {
                            $field['default'] = [$firstKey];
                        } else {
                            $field['default'] = [];
                        }
                    } else {
                        $field['default'] = [];
                    }
                } else {
                    $field['default'] = [];
                }
            }
        } else {
            // For single select, set first option as default if no default is set
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
        }

        $label = !empty($field['label']) ? $field['label'] : 'Select2';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'SELECT2', $is_responsive);

        // Add SELECT2-specific properties FIRST
        $content .= $this->build_select2_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build SELECT2-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_select2_properties($field) {
        $content = '';

        // Options - dropdown options
        if (!empty($field['options']) && is_array($field['options'])) {
            $content .= $this->build_options($field['options']);
        }

        // Multiple - allow multiple selections
        if (isset($field['multiple']) && $field['multiple']) {
            $content .= "\t\t\t\t'multiple' => true,\n";
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
