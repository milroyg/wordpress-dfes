<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * Base Control Builder
 * Base class for all control type builders
 */
abstract class Control_Base {

    /**
     * Build control output
     *
     * @param string $control_key Unique control key
     * @param array $field Field configuration
     * @return string Generated control code
     */
    abstract public function build($control_key, $field);

    /**
     * Get control type name
     *
     * @return string
     */
    abstract public function get_type();

    /**
     * Format property value
     *
     * @param string $name Property name
     * @param mixed $value Property value
     * @param int $depth Indentation depth
     * @return string
     */
    protected function format_property($name, $value, $depth = 4) {
        $tabs = str_repeat("\t", $depth);

        // Special handling for 'default' property with arrays
        if ($name === 'default' && is_array($value)) {
            return $this->format_default_array($value, $depth);
        }

        // Format based on value type
        if (is_bool($value)) {
            return "{$tabs}'{$name}' => " . ($value ? 'true' : 'false') . ",\n";
        } elseif (is_numeric($value)) {
            return "{$tabs}'{$name}' => {$value},\n";
        } elseif (is_string($value)) {
            // Special handling for default values - don't translate them
            if ($name === 'default') {
                if ($value === '') {
                    return "{$tabs}'default' => '',\n";
                }
                // Default values should not be translated
                return "{$tabs}'default' => '" . esc_js($value) . "',\n";
            }
            // Use translation function for other string values (labels, placeholders, etc.)
            return "{$tabs}'{$name}' => esc_html__('" . esc_js($value) . "', 'master-addons'),\n";
        } elseif (is_array($value)) {
            return $this->format_array_property($name, $value, $depth);
        }
        return '';
    }

    /**
     * Format default array property
     * Special handling for default values that must be arrays
     *
     * @param array $value Default array value
     * @param int $depth Indentation depth
     * @return string
     */
    protected function format_default_array($value, $depth = 4) {
        $tabs = str_repeat("\t", $depth);

        // If empty array, return empty array
        if (empty($value)) {
            return "{$tabs}'default' => [],\n";
        }

        // Check if this is a simple indexed array (like for Select2 multiple)
        // Example: ['value-1', 'value-2']
        if (array_keys($value) === range(0, count($value) - 1)) {
            $content = "{$tabs}'default' => [";
            $items = [];
            foreach ($value as $item) {
                if (is_string($item)) {
                    $items[] = "'" . esc_js($item) . "'";
                } elseif (is_numeric($item)) {
                    $items[] = $item;
                } elseif (is_bool($item)) {
                    $items[] = $item ? 'true' : 'false';
                } else {
                    $items[] = "'" . esc_js($item) . "'";
                }
            }
            $content .= implode(', ', $items);
            $content .= "],\n";
            return $content;
        }

        // Handle associative arrays (with string keys)
        $content = "{$tabs}'default' => [\n";

        foreach ($value as $key => $val) {
            if (is_string($key)) {
                if (is_bool($val)) {
                    $content .= $tabs . "\t'" . esc_js($key) . "' => " . ($val ? 'true' : 'false') . ",\n";
                } elseif (is_numeric($val)) {
                    $content .= $tabs . "\t'" . esc_js($key) . "' => " . $val . ",\n";
                } elseif (is_array($val)) {
                    // Handle nested arrays
                    $content .= $this->format_nested_array($key, $val, $depth + 1);
                } else {
                    // String values - don't use esc_html__ for defaults
                    $content .= $tabs . "\t'" . esc_js($key) . "' => '" . esc_js($val) . "',\n";
                }
            }
        }

        $content .= "{$tabs}],\n";
        return $content;
    }

    /**
     * Format array property
     *
     * @param string $name Property name
     * @param array $array Array values
     * @param int $depth Indentation depth
     * @return string
     */
    protected function format_array_property($name, $array, $depth = 4) {
        if (!is_array($array) || empty($array)) {
            return '';
        }

        $tabs = str_repeat("\t", $depth);

        // Check if simple array (indexed with string values)
        if (array_keys($array) === range(0, count($array) - 1)) {
            $content = "{$tabs}'{$name}' => [";
            $items = [];
            foreach ($array as $item) {
                if (is_string($item)) {
                    $items[] = "'" . esc_js($item) . "'";
                } else {
                    $items[] = $item;
                }
            }
            $content .= implode(', ', $items);
            $content .= "],\n";
            return $content;
        }

        // Nested associative array
        return $this->format_nested_array($name, $array, $depth);
    }

    /**
     * Format nested array
     *
     * @param string $name Property name
     * @param array $array Nested array
     * @param int $depth Indentation depth
     * @return string
     */
    protected function format_nested_array($name, $array, $depth = 4) {
        if (!is_array($array) || empty($array)) {
            return '';
        }

        $tabs = str_repeat("\t", $depth);
        $content = "{$tabs}'{$name}' => [\n";

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $content .= $this->format_nested_array($key, $value, $depth + 1);
            } elseif (is_bool($value)) {
                $content .= $tabs . "\t'" . esc_js($key) . "' => " . ($value ? 'true' : 'false') . ",\n";
            } elseif (is_numeric($value)) {
                $content .= $tabs . "\t'" . esc_js($key) . "' => {$value},\n";
            } else {
                $content .= $tabs . "\t'" . esc_js($key) . "' => '" . esc_js($value) . "',\n";
            }
        }

        $content .= "{$tabs}],\n";
        return $content;
    }

    /**
     * Format localized string property
     *
     * @param string $name Property name
     * @param string $value String value
     * @param int $depth Indentation depth
     * @return string
     */
    protected function format_string_property($name, $value, $depth = 4) {
        $tabs = str_repeat("\t", $depth);
        return "{$tabs}'{$name}' => esc_html__('" . esc_js($value) . "', 'master-addons'),\n";
    }

    /**
     * Format plain string property (not localized)
     *
     * @param string $name Property name
     * @param string $value String value
     * @param int $depth Indentation depth
     * @return string
     */
    protected function format_plain_string_property($name, $value, $depth = 4) {
        $tabs = str_repeat("\t", $depth);
        return "{$tabs}'{$name}' => '" . esc_js($value) . "',\n";
    }

    /**
     * Format boolean property
     *
     * @param string $name Property name
     * @param bool $value Boolean value
     * @param int $depth Indentation depth
     * @return string
     */
    protected function format_bool_property($name, $value, $depth = 4) {
        $tabs = str_repeat("\t", $depth);
        return "{$tabs}'{$name}' => " . ($value ? 'true' : 'false') . ",\n";
    }

    /**
     * Format number property
     *
     * @param string $name Property name
     * @param number $value Numeric value
     * @param int $depth Indentation depth
     * @return string
     */
    protected function format_number_property($name, $value, $depth = 4) {
        $tabs = str_repeat("\t", $depth);
        return "{$tabs}'{$name}' => {$value},\n";
    }

    /**
     * Build common control properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_common_properties($field) {
        $content = '';
        // Default value
        if (isset($field['default']) ) {
            $content .= $this->format_property('default', $field['default']);
        }

        // Placeholder
        if (!empty($field['placeholder'])) {
            $content .= $this->format_string_property('placeholder', $field['placeholder']);
        }

        // Description
        if (!empty($field['description'])) {
            $content .= $this->format_string_property('description', $field['description']);
        }

        // Label block
        if (isset($field['label_block'])) {
            $content .= $this->format_bool_property('label_block', $field['label_block']);
        }

        // Show label
        if (isset($field['show_label'])) {
            $content .= $this->format_bool_property('show_label', $field['show_label']);
        }

        // Responsive is handled in build_control_header via method name change
        // We don't output 'responsive' => true as a property

        // Dynamic (dynamic content support)
        if (isset($field['dynamic']) && $field['dynamic']) {
            // Dynamic property should be an array with 'active' => true
            $content .= "\t\t\t\t'dynamic' => [\n";
            $content .= "\t\t\t\t\t'active' => true,\n";
            $content .= "\t\t\t\t],\n";
        }

        // Frontend Available (expose control value to frontend JavaScript)
        if (isset($field['frontend_available']) && $field['frontend_available']) {
            $content .= $this->format_bool_property('frontend_available', true);
        }

        // Separator
        if (!empty($field['separator']) && $field['separator'] !== 'default') {
            $content .= $this->format_plain_string_property('separator', $field['separator']);
        }

        // Conditions - Convert from React UI format to Elementor format
        // React sends: conditions: [{control_name: 'x', equality: 'equal', control_value: 'y'}]
        // Elementor expects: 'condition' => ['x' => 'y'] OR 'conditions' => [...]
        if (!empty($field['conditions']) && is_array($field['conditions'])) {
            $content .= $this->build_conditions($field['conditions'], $field);
        }

        if (!empty($field['selectors']) && is_array($field['selectors'])) {
            $content .= $this->build_selectors($field['selectors']);
        }
        elseif (!empty($field['selector']) && is_string($field['selector'])) {
            $selector_value = $field['selector'];

            if (strpos($selector_value, ',') !== false) {
                $selector_parts = explode(',', $selector_value);
                $processed_parts = [];

                foreach ($selector_parts as $part) {
                    $part = trim($part);
                    if (!empty($part) && strpos($part, '{{WRAPPER}}') === false) {
                        $part = '{{WRAPPER}} ' . $part;
                    }
                    $processed_parts[] = $part;
                }

                $selector_value = implode(', ', $processed_parts);
            } else {
                // Single selector - ensure it has {{WRAPPER}}
                if (strpos($selector_value, '{{WRAPPER}}') === false) {
                    $selector_value = '{{WRAPPER}} ' . $selector_value;
                }
            }

            // Determine default CSS property based on control type
            $default_property = 'color: {{VALUE}};'; // Default for most controls

            if (isset($field['type'])) {
                $type = strtolower($field['type']);
                if (in_array($type, ['slider', 'number'])) {
                    $default_property = '{{VALUE}}';
                }
                elseif ($type === 'dimensions') {
                    return $content;
                }
            }

            $content .= "\t\t\t\t'selectors' => [\n";
            $content .= "\t\t\t\t\t'" . esc_js($selector_value) . "' => '" . esc_js($default_property) . "',\n";
            $content .= "\t\t\t\t],\n";
        }

        return $content;
    }

    /**
     * Build conditions from React UI format to Elementor format
     *
     * React format: [{control_name: 'field', equality: 'equal', control_value: 'value'}]
     * Elementor formats:
     *   - Simple: 'condition' => ['field' => 'value'] (single condition with 'equal' operator)
     *   - Advanced: 'conditions' => ['terms' => [...]] (multiple conditions or non-equal operators)
     *
     * @param array $conditions_array Conditions array from React UI
     * @param array $field Field context with tab and widget_id
     * @return string
     */
    protected function build_conditions($conditions_array, $field = []) {
        if (empty($conditions_array) || !is_array($conditions_array)) {
            return '';
        }

        // Check if we need advanced format (multiple conditions OR non-equal operators)
        $use_advanced_format = false;
        $has_multiple_conditions = count($conditions_array) > 1;
        $has_non_equal_operators = false;

        foreach ($conditions_array as $condition) {
            if (!empty($condition['equality']) && $condition['equality'] !== 'equal') {
                $has_non_equal_operators = true;
                break;
            }
        }

        $use_advanced_format = $has_multiple_conditions || $has_non_equal_operators;

        if ($use_advanced_format) {
            // Use advanced 'conditions' format with 'terms'
            return $this->build_advanced_conditions($conditions_array, $field);
        } else {
            // Use simple 'condition' format (single condition with equal operator)
            $condition = $conditions_array[0];
            if (!empty($condition['control_name']) && isset($condition['control_value'])) {
                // Convert control_name to full key (only if it's a control reference)
                $control_name = $this->convert_to_control_key($condition['control_name'], $field);

                // Keep control_value as literal string (don't convert to control key)
                // Users enter literal values like 'yes', 'no', 'value-1', etc.
                $control_value = $condition['control_value'];

                $simple_condition = [
                    $control_name => $control_value
                ];
                return $this->format_nested_array('condition', $simple_condition);
            }
        }

        return '';
    }

    /**
     * Build advanced conditions format with operators
     *
     * @param array $conditions_array Conditions array from React UI
     * @param array $field Field context with tab and widget_id
     * @return string
     */
    protected function build_advanced_conditions($conditions_array, $field = []) {
        $terms = [];

        // Map React equality to Elementor operators
        $operator_map = [
            'equal' => '===',
            'not_equal' => '!==',
            'in' => 'in',
            'not_in' => '!in',
            'contains' => 'contains',
            'not_contains' => '!contains',
        ];

        foreach ($conditions_array as $condition) {
            if (!empty($condition['control_name']) && isset($condition['control_value'])) {
                $equality = $condition['equality'] ?? 'equal';
                $operator = $operator_map[$equality] ?? '===';

                // Convert control_name to full key
                $control_name = $this->convert_to_control_key($condition['control_name'], $field);

                // Keep control_value as literal string (don't convert)
                // Users enter literal values like 'yes', 'no', 'value-1', etc.
                $control_value = $condition['control_value'];

                $terms[] = [
                    'name' => $control_name,
                    'operator' => $operator,
                    'value' => $control_value
                ];
            }
        }

        if (empty($terms)) {
            return '';
        }

        // Build the conditions array with terms
        $content = "\t\t\t\t'conditions' => [\n";
        $content .= "\t\t\t\t\t'terms' => [\n";

        foreach ($terms as $term) {
            $content .= "\t\t\t\t\t\t[\n";
            $content .= "\t\t\t\t\t\t\t'name' => '" . esc_js($term['name']) . "',\n";
            $content .= "\t\t\t\t\t\t\t'operator' => '" . esc_js($term['operator']) . "',\n";
            $content .= "\t\t\t\t\t\t\t'value' => '" . esc_js($term['value']) . "',\n";
            $content .= "\t\t\t\t\t\t],\n";
        }

        $content .= "\t\t\t\t\t],\n";
        $content .= "\t\t\t\t],\n";

        return $content;
    }

    /**
     * Sanitize label to create control/section key
     * Converts spaces to underscores instead of hyphens
     *
     * @param string $label Label to sanitize
     * @return string Sanitized key with underscores
     */
    protected function sanitize_key($label) {
        // Convert to lowercase
        $key = strtolower($label);

        // Replace spaces with underscores
        $key = str_replace(' ', '_', $key);

        // Remove special characters, keeping only alphanumeric and underscores
        $key = preg_replace('/[^a-z0-9_]/', '', $key);

        // Remove multiple consecutive underscores
        $key = preg_replace('/_+/', '_', $key);

        // Trim underscores from beginning and end
        $key = trim($key, '_');

        return $key;
    }

    /**
     * Convert short control name to full control key
     *
     * @param string $name Short name like 'text' or already full key like 'jltma_content_text_7641'
     * @param array $field Field context with _tab, _widget_id, _tab_prefix
     * @return string Full control key
     */
    protected function convert_to_control_key($name, $field = []) {
        // If already a full key (starts with jltma_), return as-is
        if (strpos($name, 'jltma_') === 0) {
            return $name;
        }

        // Extract context
        $tab_prefix = $field['_tab_prefix'] ?? 'jltma_content_';
        $widget_id = $field['_widget_id'] ?? '';

        // Build full key: tab_prefix + sanitized_name + '_' + widget_id
        $sanitized_name = $this->sanitize_key($name);
        return $tab_prefix . $sanitized_name . '_' . $widget_id;
    }

    /**
     * Build selectors from React UI format to Elementor format
     *
     * React format: [{selector: '{{WRAPPER}} .class', properties: 'color: {{VALUE}};'}]
     * Elementor format: 'selectors' => ['{{WRAPPER}} .class' => 'color: {{VALUE}};']
     *
     * @param array $selectors_array Selectors array from React UI
     * @return string
     */
    protected function build_selectors($selectors_array) {
        if (empty($selectors_array) || !is_array($selectors_array)) {
            return '';
        }

        // Convert React selectors format to Elementor format
        $elementor_selectors = [];

        foreach ($selectors_array as $item) {
            if (!empty($item['selector']) && !empty($item['properties'])) {
                $selector = $item['selector'];

                // Process comma-separated selectors
                // Each individual selector needs {{WRAPPER}}
                if (strpos($selector, ',') !== false) {
                    $selector_parts = explode(',', $selector);
                    $processed_parts = [];

                    foreach ($selector_parts as $part) {
                        $part = trim($part);
                        // Add {{WRAPPER}} if not already present
                        if (!empty($part) && strpos($part, '{{WRAPPER}}') === false) {
                            $part = '{{WRAPPER}} ' . $part;
                        }
                        $processed_parts[] = $part;
                    }

                    $selector = implode(', ', $processed_parts);
                } else {
                    // Single selector - ensure it has {{WRAPPER}}
                    if (strpos($selector, '{{WRAPPER}}') === false) {
                        $selector = '{{WRAPPER}} ' . $selector;
                    }
                }

                $elementor_selectors[$selector] = $item['properties'];
            }
        }

        if (empty($elementor_selectors)) {
            return '';
        }

        $content = "\t\t\t\t'selectors' => [\n";
        foreach ($elementor_selectors as $selector => $css) {
            $content .= "\t\t\t\t\t'" . esc_js($selector) . "' => '" . esc_js($css) . "',\n";
        }
        $content .= "\t\t\t\t],\n";

        return $content;
    }

    /**
     * Build control header
     *
     * @param string $control_key Unique control key
     * @param string $label Control label
     * @param string $type Control type
     * @param bool $is_responsive Whether this is a responsive control
     * @return string
     */
    protected function build_control_header($control_key, $label, $type, $is_responsive = false) {
        // Use add_responsive_control if responsive is enabled
        $method = $is_responsive ? 'add_responsive_control' : 'add_control';

        $content = "\t\t\$this->{$method}(\n";
        $content .= "\t\t\t'{$control_key}',\n";
        $content .= "\t\t\t[\n";
        $content .= "\t\t\t\t'label' => esc_html__('" . esc_js($label) . "', 'master-addons'),\n";
        $content .= "\t\t\t\t'type' => Controls_Manager::{$type},\n";
        return $content;
    }

    /**
     * Build control footer
     *
     * @return string
     */
    protected function build_control_footer() {
        return "\t\t\t]\n\t\t);\n\n";
    }
}
