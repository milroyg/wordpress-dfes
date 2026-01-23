<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * ICONS Control
 * Handles Elementor ICONS control type
 * Icon library selector with SVG upload support
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default icon array ['value' => '', 'library' => '']
 * - recommended: Recommended icons to show (array)
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Icons extends Control_Base {

    public function get_type() {
        return 'ICONS';
    }

    public function build($control_key, $field) {
        if( empty($field['default'] )) $field['default'] = [ 'value' => 'eicon eicon-elementor-circle', 'library' => 'eicons' ];
        $label = !empty($field['label']) ? $field['label'] : 'Icon';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'ICONS', $is_responsive);

        // Add ICONS-specific properties FIRST
        $content .= $this->build_icons_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build ICONS-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_icons_properties($field) {
        $content = '';

        // Recommended icons
        if (!empty($field['recommended']) && is_array($field['recommended'])) {
            $content .= "\t\t\t\t'recommended' => " . var_export($field['recommended'], true) . ",\n";
        }

        return $content;
    }
}
