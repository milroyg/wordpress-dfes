<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * MEDIA Control
 * Handles Elementor MEDIA control type
 * Media library selector for images, videos, etc.
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default media array ['url' => '', 'id' => '']
 * - media_type: Media type filter (image, video, audio)
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Media extends Control_Base {

    public function get_type() {
        return 'MEDIA';
    }

    public function build($control_key, $field) {
        // Ensure proper default structure for MEDIA control
        if (empty($field['default']) || !is_array($field['default'])) {
            $field['default'] = [
                'id' => '',
                'url' => '',
            ];
        }

        $label = !empty($field['label']) ? $field['label'] : 'Media';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'MEDIA', $is_responsive);

        // Add MEDIA-specific properties FIRST
        $content .= $this->build_media_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build MEDIA-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_media_properties($field) {
        $content = '';

        // Media type
        if (!empty($field['media_type'])) {
            $content .= $this->format_plain_string_property('media_type', $field['media_type']);
        }

        return $content;
    }
}
