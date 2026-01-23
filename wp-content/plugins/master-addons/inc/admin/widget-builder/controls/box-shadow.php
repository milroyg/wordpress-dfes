<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * BOX SHADOW Group Control
 * Handles Elementor Group_Control_Box_Shadow
 * Combines shadow position, blur, spread, color, and offset controls
 *
 * This is a GROUP control that includes:
 * - Shadow Type (none, shadow, inset)
 * - Horizontal Offset
 * - Vertical Offset
 * - Blur
 * - Spread
 * - Shadow Color
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - selector: CSS selector for the box shadow styles
 * - fields_options: Override options for individual controls
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Box_Shadow extends Control_Base {

    public function get_type() {
        return 'BOX_SHADOW';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Box Shadow';

        // This is a group control, use add_group_control
        $content = "\t\t\$this->add_group_control(\n";
        $content .= "\t\t\t\\Elementor\\Group_Control_Box_Shadow::get_type(),\n";
        $content .= "\t\t\t[\n";
        $content .= "\t\t\t\t'name' => '{$control_key}',\n";
        $content .= "\t\t\t\t'label' => esc_html__('" . esc_js($label) . "', 'master-addons'),\n";

        // Add selector if provided
        if (!empty($field['selector']) || !empty($field['selectors'])) {
            // Build selectors
            if (!empty($field['selectors']) && is_array($field['selectors'])) {
                $content .= $this->build_box_shadow_selectors($field['selectors']);
            } elseif (!empty($field['selector']) && is_string($field['selector'])) {
                // Single selector
                $selector = $field['selector'];

                // Process comma-separated selectors
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

                $content .= "\t\t\t\t'selector' => '" . esc_js($selector) . "',\n";
            }
        }

        // Add fields_options if provided (for customizing individual controls)
        if (!empty($field['fields_options']) && is_array($field['fields_options'])) {
            $content .= "\t\t\t\t'fields_options' => [\n";

            // Box shadow type options
            if (!empty($field['fields_options']['box_shadow_type'])) {
                $content .= "\t\t\t\t\t'box_shadow_type' => [\n";
                if (isset($field['fields_options']['box_shadow_type']['default'])) {
                    $content .= "\t\t\t\t\t\t'default' => '" . esc_js($field['fields_options']['box_shadow_type']['default']) . "',\n";
                }
                $content .= "\t\t\t\t\t],\n";
            }

            // Box shadow options (combined x, y, blur, spread)
            if (!empty($field['fields_options']['box_shadow'])) {
                $content .= "\t\t\t\t\t'box_shadow' => [\n";
                if (isset($field['fields_options']['box_shadow']['default'])) {
                    $default = $field['fields_options']['box_shadow']['default'];
                    if (is_array($default)) {
                        $content .= "\t\t\t\t\t\t'default' => [\n";
                        foreach ($default as $key => $value) {
                            $content .= "\t\t\t\t\t\t\t'" . esc_js($key) . "' => '" . esc_js($value) . "',\n";
                        }
                        $content .= "\t\t\t\t\t\t],\n";
                    }
                }
                $content .= "\t\t\t\t\t],\n";
            }

            // Color options
            if (!empty($field['fields_options']['box_shadow_color'])) {
                $content .= "\t\t\t\t\t'box_shadow_color' => [\n";
                if (isset($field['fields_options']['box_shadow_color']['default'])) {
                    $content .= "\t\t\t\t\t\t'default' => '" . esc_js($field['fields_options']['box_shadow_color']['default']) . "',\n";
                }
                $content .= "\t\t\t\t\t],\n";
            }

            $content .= "\t\t\t\t],\n";
        }

        // Add common properties
        if (isset($field['show_label'])) {
            $content .= $this->format_bool_property('show_label', $field['show_label']);
        }

        if (!empty($field['separator']) && $field['separator'] !== 'default') {
            $content .= $this->format_plain_string_property('separator', $field['separator']);
        }

        // Add conditions if present
        if (!empty($field['conditions']) && is_array($field['conditions'])) {
            $content .= $this->build_conditions($field['conditions'], $field);
        }

        $content .= "\t\t\t]\n";
        $content .= "\t\t);\n\n";

        return $content;
    }

    /**
     * Build box shadow selectors
     *
     * @param array $selectors_array
     * @return string
     */
    protected function build_box_shadow_selectors($selectors_array) {
        if (empty($selectors_array) || !is_array($selectors_array)) {
            return '';
        }

        // For box shadow group control, we just need the selector
        // Extract the first selector's selector value
        foreach ($selectors_array as $item) {
            if (!empty($item['selector'])) {
                $selector = $item['selector'];

                // Process comma-separated selectors
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

                return "\t\t\t\t'selector' => '" . esc_js($selector) . "',\n";
            }
        }

        return '';
    }
}
