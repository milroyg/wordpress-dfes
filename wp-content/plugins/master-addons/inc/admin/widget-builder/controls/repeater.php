<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * REPEATER Control
 * Handles Elementor REPEATER control type
 * Creates repeatable rows of fields for dynamic content
 *
 * Usage pattern in Elementor:
 * 1. Create a new Repeater instance
 * 2. Add controls to the repeater using $repeater->add_control()
 * 3. Add the repeater to the widget with type Controls_Manager::REPEATER
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - fields: Array of control configurations for repeater fields
 * - default: Default values array
 * - title_field: Template for item title (e.g., '{{{ item.text }}}')
 * - prevent_empty: Prevent empty repeater (default true)
 * - min_items: Minimum number of items
 * - max_items: Maximum number of items
 * - button_text: Text for add button
 * - item_actions: Array of allowed actions (add, duplicate, remove, sort)
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Repeater extends Control_Base {

    public function get_type() {
        return 'REPEATER';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Items';

        // Build the repeater variable name
        $repeater_var = '$repeater_' . $control_key;

        $content = "\n\t\t// Repeater: {$label}\n";
        $content .= "\t\t{$repeater_var} = new \\Elementor\\Repeater();\n\n";

        // Add controls to the repeater
        if (!empty($field['fields']) && is_array($field['fields'])) {
            $content .= $this->build_repeater_controls($repeater_var, $field['fields']);
        }

        // Add the repeater control to the widget
        $content .= "\t\t\$this->add_control(\n";
        $content .= "\t\t\t'{$control_key}',\n";
        $content .= "\t\t\t[\n";
        $content .= "\t\t\t\t'label' => esc_html__('" . esc_js($label) . "', 'master-addons'),\n";
        $content .= "\t\t\t\t'type' => \\Elementor\\Controls_Manager::REPEATER,\n";
        $content .= "\t\t\t\t'fields' => {$repeater_var}->get_controls(),\n";

        if($field['title_field'] === '{{{ field_1 }}}'){
            $field['title_field'] = '{{{ ' . $field['fields'][0]['name'] .' }}}';
            
            foreach($field['default'] as $key => $value){
                if(array_key_exists('field_1', $value)){
                    $field_value = $value['field_1'];
                    unset($value['field_1']);
                    $value[$field['fields'][0]['name']] = $field_value;
                    
                    $field['default'][$key] = $value;
                }
            }
            
        }
        // Add repeater-specific properties
        $content .= $this->build_repeater_properties($field);

        // Add common properties (excluding those already handled)
        $content .= $this->build_filtered_common_properties($field);

        // Close control
        $content .= "\t\t\t]\n";
        $content .= "\t\t);\n\n";

        return $content;
    }

    /**
     * Build repeater controls
     *
     * @param string $repeater_var Repeater variable name
     * @param array $fields Array of field configurations
     * @return string Generated PHP code
     */
    protected function build_repeater_controls($repeater_var, $fields) {
        $content = '';
        $processed_fields = [];

        foreach ($fields as $field) {
            if (empty($field['type']) || empty($field['name'])) {
                continue;
            }

            // Skip duplicate fields
            if (isset($processed_fields[$field['name']])) {
                continue;
            }

            $processed_fields[$field['name']] = true;
            $content .= $this->build_repeater_control($repeater_var, $field);
        }

        return $content;
    }

    /**
     * Build individual repeater control
     *
     * @param string $repeater_var Repeater variable name
     * @param array $field Field configuration
     * @return string Generated PHP code
     */
    protected function build_repeater_control($repeater_var, $field) {
        $control_type = strtoupper($field['type']);
        $control_name = $field['name'];
        $control_label = !empty($field['label']) ? $field['label'] : ucfirst(str_replace('_', ' ', $control_name));

        $content = "\t\t{$repeater_var}->add_control(\n";
        $content .= "\t\t\t'{$control_name}',\n";
        $content .= "\t\t\t[\n";
        $content .= "\t\t\t\t'label' => esc_html__('" . esc_js($control_label) . "', 'master-addons'),\n";
        $content .= "\t\t\t\t'type' => \\Elementor\\Controls_Manager::{$control_type},\n";

        // Add control-specific properties based on type
        $content .= $this->build_control_properties($field);

        $content .= "\t\t\t]\n";
        $content .= "\t\t);\n\n";

        return $content;
    }

    /**
     * Build control properties based on control type
     *
     * @param array $field Field configuration
     * @return string Generated PHP code
     */
    protected function build_control_properties($field) {
        $content = '';
        $control_type = strtolower($field['type']);

        // Default value
        if (isset($field['default'])) {
            $default = $this->format_property('default', $field['default']);
            if ($default !== null) {
                $content .= "\t\t\t\t'default' => {$default},\n";
            }
        }

        // Common properties
        if (isset($field['placeholder'])) {
            $content .= "\t\t\t\t'placeholder' => esc_html__('" . esc_js($field['placeholder']) . "', 'master-addons'),\n";
        }

        if (isset($field['description'])) {
            $content .= "\t\t\t\t'description' => esc_html__('" . esc_js($field['description']) . "', 'master-addons'),\n";
        }

        if (isset($field['label_block']) && $field['label_block']) {
            $content .= "\t\t\t\t'label_block' => true,\n";
        }

        if (isset($field['show_label']) && !$field['show_label']) {
            $content .= "\t\t\t\t'show_label' => false,\n";
        }

        // Type-specific properties
        switch ($control_type) {
            case 'select':
            case 'select2':
                if (!empty($field['options']) && is_array($field['options'])) {
                    $content .= $this->build_options($field['options']);
                }
                if ($control_type === 'select2' && isset($field['multiple']) && $field['multiple']) {
                    $content .= "\t\t\t\t'multiple' => true,\n";
                }
                break;

            case 'choose':
                if (!empty($field['options']) && is_array($field['options'])) {
                    $content .= $this->build_choose_options($field['options']);
                }
                if (isset($field['toggle'])) {
                    $content .= "\t\t\t\t'toggle' => " . ($field['toggle'] ? 'true' : 'false') . ",\n";
                }
                break;

            case 'icons':
            case 'icon':
                // Icon defaults are handled in format_property
                break;

            case 'media':
                if (!empty($field['media_types']) && is_array($field['media_types'])) {
                    $content .= $this->build_media_types($field['media_types']);
                }
                break;

            case 'url':
                if (isset($field['show_external']) && !$field['show_external']) {
                    $content .= "\t\t\t\t'show_external' => false,\n";
                }
                break;

            case 'slider':
                if (!empty($field['size_units'])) {
                    $content .= $this->build_size_units($field['size_units']);
                }
                if (!empty($field['range'])) {
                    $content .= $this->build_range($field['range']);
                }
                break;

            case 'dimensions':
                if (!empty($field['size_units'])) {
                    $content .= $this->build_size_units($field['size_units']);
                }
                break;
        }

        // Selectors
        if (!empty($field['selectors']) && is_array($field['selectors'])) {
            $content .= $this->build_selectors($field['selectors']);
        }

        // Dynamic content
        if (isset($field['dynamic']) && $field['dynamic']) {
            $content .= "\t\t\t\t'dynamic' => [\n";
            $content .= "\t\t\t\t\t'active' => true,\n";
            $content .= "\t\t\t\t],\n";
        }

        // Condition
        if (!empty($field['condition'])) {
            $content .= $this->build_condition($field['condition']);
        }

        return $content;
    }

    /**
     * Build repeater-specific properties
     *
     * @param array $field Field configuration
     * @return string Generated PHP code
     */
    protected function build_repeater_properties($field) {
        $content = '';

        // Get the first field name from fields array
        $first_field_name = null;
        if (!empty($field['fields']) && is_array($field['fields']) && isset($field['fields'][0]['name'])) {
            $first_field_name = $field['fields'][0]['name'];
        }

        // Title field template - use first field name if not provided
        if (!empty($field['title_field'])) {
            $content .= "\t\t\t\t'title_field' => '" . esc_js($field['title_field']) . "',\n";
        } elseif ($first_field_name) {
            // Generate title_field using first field name
            $content .= "\t\t\t\t'title_field' => '{{{ " . $first_field_name . " }}}',\n";
        }

        // Default items - update to use first field name instead of 'field_1'
        if (!empty($field['default']) && is_array($field['default'])) {
            $content .= $this->build_repeater_defaults($field['default'], $first_field_name);
        }

        // Prevent empty
        if (isset($field['prevent_empty'])) {
            $content .= "\t\t\t\t'prevent_empty' => " . ($field['prevent_empty'] ? 'true' : 'false') . ",\n";
        }

        // Min/Max items
        if (!empty($field['min_items'])) {
            $content .= "\t\t\t\t'min_items' => " . intval($field['min_items']) . ",\n";
        }

        if (!empty($field['max_items'])) {
            $content .= "\t\t\t\t'max_items' => " . intval($field['max_items']) . ",\n";
        }

        // Button text
        if (!empty($field['button_text'])) {
            $content .= "\t\t\t\t'button_text' => esc_html__('" . esc_js($field['button_text']) . "', 'master-addons'),\n";
        }

        // Item actions
        if (!empty($field['item_actions']) && is_array($field['item_actions'])) {
            $content .= "\t\t\t\t'item_actions' => [\n";
            foreach ($field['item_actions'] as $action => $enabled) {
                $content .= "\t\t\t\t\t'{$action}' => " . ($enabled ? 'true' : 'false') . ",\n";
            }
            $content .= "\t\t\t\t],\n";
        }

        return $content;
    }

    /**
     * Build filtered common properties (excluding those already handled by repeater)
     *
     * @param array $field Field configuration
     * @return string Generated PHP code
     */
    protected function build_filtered_common_properties($field) {
        $content = '';

        // Label block
        if (isset($field['label_block']) && $field['label_block']) {
            $content .= "\t\t\t\t'label_block' => true,\n";
        }

        // Show label
        if (isset($field['show_label']) && !$field['show_label']) {
            $content .= "\t\t\t\t'show_label' => false,\n";
        }

        // Separator
        if (!empty($field['separator'])) {
            $content .= "\t\t\t\t'separator' => '" . esc_js($field['separator']) . "',\n";
        }

        // Condition (at widget level, not repeater item level)
        if (!empty($field['condition']) && !isset($field['_in_repeater'])) {
            $content .= $this->build_condition($field['condition']);
        }

        return $content;
    }

    /**
     * Build repeater default values
     *
     * @param array $defaults Array of default items
     * @return string Generated PHP code
     */
    protected function build_repeater_defaults($defaults) {
        $content = "\t\t\t\t'default' => [\n";

        foreach ($defaults as $item) {
            if (!is_array($item)) {
                continue;
            }

            $content .= "\t\t\t\t\t[\n";
            foreach ($item as $key => $value) {
                // Format the value directly without the key prefix
                if (is_string($value)) {
                    $formatted_value = "esc_html__('" . esc_js($value) . "', 'master-addons')";
                } elseif (is_numeric($value)) {
                    $formatted_value = $value;
                } elseif (is_bool($value)) {
                    $formatted_value = $value ? 'true' : 'false';
                } elseif (is_array($value)) {
                    // Handle array values (like icons, media, etc.)
                    $formatted_value = $this->format_array_value($value);
                } else {
                    $formatted_value = "''";
                }

                $content .= "\t\t\t\t\t\t'{$key}' => {$formatted_value},\n";
            }
            $content .= "\t\t\t\t\t],\n";
        }

        $content .= "\t\t\t\t],\n";
        return $content;
    }

    /**
     * Format array values for repeater defaults
     *
     * @param array $value
     * @return string
     */
    private function format_array_value($value) {
        $content = "[\n";
        foreach ($value as $k => $v) {
            if (is_string($v)) {
                $content .= "\t\t\t\t\t\t\t'{$k}' => '" . esc_js($v) . "',\n";
            } elseif (is_numeric($v)) {
                $content .= "\t\t\t\t\t\t\t'{$k}' => {$v},\n";
            } elseif (is_bool($v)) {
                $content .= "\t\t\t\t\t\t\t'{$k}' => " . ($v ? 'true' : 'false') . ",\n";
            }
        }
        $content .= "\t\t\t\t\t\t]";
        return $content;
    }

    /**
     * Build options for select/choose controls
     */
    private function build_options($options) {
        $content = "\t\t\t\t'options' => [\n";

        // Check if options is an array of objects or key-value pairs
        if (isset($options[0]) && is_array($options[0])) {
            // Handle array of objects [{value: 'val1', label: 'Label 1'}, ...]
            foreach ($options as $option) {
                if (isset($option['value']) && isset($option['label'])) {
                    $content .= "\t\t\t\t\t'" . esc_js($option['value']) . "' => esc_html__('" . esc_js($option['label']) . "', 'master-addons'),\n";
                }
            }
        } else {
            // Handle associative array (key => label pairs)
            foreach ($options as $key => $label) {
                if (is_array($label) && isset($label['label'])) {
                    $label = $label['label'];
                }
                $content .= "\t\t\t\t\t'" . esc_js($key) . "' => esc_html__('" . esc_js($label) . "', 'master-addons'),\n";
            }
        }

        $content .= "\t\t\t\t],\n";
        return $content;
    }

    /**
     * Build choose options with icons
     */
    private function build_choose_options($options) {
        $content = "\t\t\t\t'options' => [\n";

        foreach ($options as $key => $option) {
            $content .= "\t\t\t\t\t'" . esc_js($key) . "' => [\n";

            if (isset($option['title'])) {
                $content .= "\t\t\t\t\t\t'title' => esc_html__('" . esc_js($option['title']) . "', 'master-addons'),\n";
            }

            if (isset($option['icon'])) {
                $content .= "\t\t\t\t\t\t'icon' => '" . esc_js($option['icon']) . "',\n";
            }

            $content .= "\t\t\t\t\t],\n";
        }

        $content .= "\t\t\t\t],\n";
        return $content;
    }

    /**
     * Build media types
     */
    private function build_media_types($media_types) {
        $content = "\t\t\t\t'media_types' => [";
        foreach ($media_types as $type) {
            $content .= "'" . esc_js($type) . "', ";
        }
        $content = rtrim($content, ', ');
        $content .= "],\n";
        return $content;
    }

    /**
     * Build size units
     */
    private function build_size_units($size_units) {
        if (is_array($size_units)) {
            $content = "\t\t\t\t'size_units' => [";
            foreach ($size_units as $unit) {
                $content .= "'" . esc_js($unit) . "', ";
            }
            $content = rtrim($content, ', ');
            $content .= "],\n";
        } else {
            $content = "\t\t\t\t'size_units' => ['" . esc_js($size_units) . "'],\n";
        }
        return $content;
    }

    /**
     * Build range
     */
    private function build_range($range) {
        $content = "\t\t\t\t'range' => [\n";
        foreach ($range as $unit => $values) {
            $content .= "\t\t\t\t\t'" . esc_js($unit) . "' => [\n";
            if (isset($values['min'])) {
                $content .= "\t\t\t\t\t\t'min' => " . floatval($values['min']) . ",\n";
            }
            if (isset($values['max'])) {
                $content .= "\t\t\t\t\t\t'max' => " . floatval($values['max']) . ",\n";
            }
            if (isset($values['step'])) {
                $content .= "\t\t\t\t\t\t'step' => " . floatval($values['step']) . ",\n";
            }
            $content .= "\t\t\t\t\t],\n";
        }
        $content .= "\t\t\t\t],\n";
        return $content;
    }
}