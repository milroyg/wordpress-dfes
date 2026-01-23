<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * HIDDEN Control
 * Handles Elementor HIDDEN control type
 * Hidden input field control
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - default: Default hidden value
 *
 * Common settings (handled by base class):
 * - condition: Conditional display logic
 */
class Hidden extends Control_Base {

    public function get_type() {
        return 'HIDDEN';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Hidden';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'HIDDEN', $is_responsive);

        // HIDDEN doesn't have specific properties beyond common ones
        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }
}
