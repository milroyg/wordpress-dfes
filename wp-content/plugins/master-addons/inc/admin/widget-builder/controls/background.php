<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * BACKGROUND Control
 * Handles Elementor GROUP CONTROL for background (color, image, gradient, video)
 * Note: This is a Group Control that includes multiple sub-controls
 *
 * Supported properties:
 * - label: Control label (optional for group controls)
 * - name: Field name prefix (used for control group key)
 * - selector: CSS selector where background styles will be applied
 * - types: Background types to enable (array: classic, gradient, video)
 * - exclude: Sub-controls to exclude (array)
 *
 * Common settings (handled by base class):
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Background extends Control_Base {

    public function get_type() {
        return 'GROUP_CONTROL_BACKGROUND';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : '';

        // Background is a Group Control, different structure
        $content = "\t\t\$this->add_group_control(\n";
        $content .= "\t\t\t\\Elementor\\Group_Control_Background::get_type(),\n";
        $content .= "\t\t\t[\n";
        $content .= "\t\t\t\t'name' => '{$control_key}',\n";

        if (!empty($label)) {
            $content .= "\t\t\t\t'label' => esc_html__('{$label}', 'master-addons'),\n";
        }

        // Add BACKGROUND-specific properties
        $content .= $this->build_background_properties($field);

        // Add common properties (selector, condition, separator)
        $content .= $this->build_common_properties($field);

        $content .= "\t\t\t]\n";
        $content .= "\t\t);\n\n";

        return $content;
    }

    /**
     * Build BACKGROUND-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_background_properties($field) {
        $content = '';

        // Types - background types to enable
        if (!empty($field['types']) && is_array($field['types'])) {
            $content .= "\t\t\t\t'types' => ['" . implode("', '", array_map('esc_js', $field['types'])) . "'],\n";
        }

        // Exclude - sub-controls to exclude
        if (!empty($field['exclude']) && is_array($field['exclude'])) {
            $content .= "\t\t\t\t'exclude' => ['" . implode("', '", array_map('esc_js', $field['exclude'])) . "'],\n";
        }

        return $content;
    }
}
