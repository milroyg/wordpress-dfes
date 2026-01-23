<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * URL Control
 * Handles Elementor URL control type
 * URL input control with link options (target, nofollow, custom attributes)
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - placeholder: Placeholder text (default: "Paste URL or type")
 * - default: Default URL array ['url' => '', 'is_external' => '', 'nofollow' => '']
 * - options: Array of enabled options ['url', 'is_external', 'nofollow', 'custom_attributes']
 * - autocomplete: Enable search functionality (boolean, default: true)
 * - show_external: Deprecated - use 'options' instead
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 *
 * Common settings (handled by base class):
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Url extends Control_Base {

    public function get_type() {
        return 'URL';
    }

    public function build($control_key, $field) {
        // Ensure proper default structure for URL control
        if (!isset($field['default']) || (is_string($field['default']) && $field['default'] === '')) {
            $field['default'] = [
                'url' => '',
                'is_external' => '',
                'nofollow' => '',
            ];
        }

        $label = !empty($field['label']) ? $field['label'] : 'Link';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'URL', $is_responsive);

        // Add URL-specific properties FIRST
        $content .= $this->build_url_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build URL-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_url_properties($field) {
        $content = '';

        // Options - Define which fields to show (url, is_external, nofollow, custom_attributes)
        if (!empty($field['options']) && is_array($field['options'])) {
            $content .= "\t\t\t\t'options' => [";
            $content .= "'" . implode("', '", array_map('esc_js', $field['options'])) . "'";
            $content .= "],\n";
        } else {
            // Default options if not specified
            $content .= "\t\t\t\t'options' => ['url', 'is_external', 'nofollow'],\n";
        }

        // Autocomplete - Enable search functionality
        if (isset($field['autocomplete'])) {
            $content .= "\t\t\t\t'autocomplete' => " . ($field['autocomplete'] ? 'true' : 'false') . ",\n";
        }

        // Show external checkbox (deprecated, kept for backwards compatibility)
        if (isset($field['show_external']) && !$field['show_external']) {
            $content .= "\t\t\t\t'show_external' => false,\n";
        }

        return $content;
    }
}
