<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * GALLERY Control
 * Handles Elementor GALLERY control type
 * Multiple media selector for image galleries
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default array of images
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Gallery extends Control_Base {

    public function get_type() {
        return 'GALLERY';
    }

    public function build($control_key, $field) {
        if( empty($field['default'] )) $field['default'] = [
            'id' => '1760339178748',
            'url' => JLTMA_IMAGE_DIR . 'placeholder.png'
        ];

        $label = !empty($field['label']) ? $field['label'] : 'Gallery';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'GALLERY', $is_responsive);

        // GALLERY doesn't have specific properties beyond common ones
        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }
}
