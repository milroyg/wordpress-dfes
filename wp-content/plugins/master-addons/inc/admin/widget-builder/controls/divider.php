<?php
namespace MasterAddons\Inc\Admin\WidgetBuilder\Controls;

use MasterAddons\Inc\Admin\WidgetBuilder\Controls\Control_Base;

defined('ABSPATH') || exit;

/**
 * DIVIDER Control
 * Handles Elementor DIVIDER control type
 * Visual divider/separator between controls
 *
 * Supported properties:
 * - label: Optional label for the divider
 * - name: Field name (used for control key)
 * - style: Divider style (default, thick, double)
 *
 * Common settings (handled by base class):
 * - separator: Control separator position
 * - condition: Conditional display logic
 *
 * Note: DIVIDER is a visual separator, it doesn't store any value
 */
class Divider extends Control_Base {

    public function get_type() {
        return 'DIVIDER';
    }

    public function build($control_key, $field) {
        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header without label for divider
        $content = "\t\t\$this->add_control(\n";
        $content .= "\t\t\t'{$control_key}',\n";
        $content .= "\t\t\t[\n";
        $content .= "\t\t\t\t'type' => Controls_Manager::DIVIDER,\n";

        // Style property
        if (!empty($field['style'])) {
            $content .= "\t\t\t\t'style' => '" . esc_js($field['style']) . "',\n";
        }

        // Add common properties (separator, condition)
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= "\t\t\t]\n";
        $content .= "\t\t);\n\n";

        return $content;
    }

    /**
     * DIVIDER has no label and is never responsive (mirrors build()).
     */
    public function get_config($control_key, $field) {
        $args = ['type' => $this->type_constant()];
        if (!empty($field['style'])) {
            $args['style'] = $field['style'];
        }
        $args = array_merge($args, $this->get_common_config($field));

        return [
            'key'        => $control_key,
            'responsive' => false,
            'args'       => $args,
        ];
    }
}
