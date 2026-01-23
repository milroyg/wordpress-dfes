<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * CODE Control
 * Handles Elementor CODE control type
 * Code editor control with syntax highlighting
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default code value
 * - language: Programming language for syntax highlighting (html, css, javascript, php, etc.)
 * - rows: Number of rows for code editor
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Code extends Control_Base {

    public function get_type() {
        return 'CODE';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Code';

        // Ensure language is set (required for ACE editor)
        if (!isset($field['language']) || $field['language'] === '') {
            $field['language'] = 'html';
        }

        // Ensure default is set (ACE editor expects a string, not undefined)
        if (!isset($field['default'])) {
            $field['default'] = '';
        }

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'CODE', $is_responsive);

        // Add CODE-specific properties FIRST
        $content .= $this->build_code_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build CODE-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_code_properties($field) {
        $content = '';

        // Language - syntax highlighting language (always output, defaults to 'html')
        $language = !empty($field['language']) ? $field['language'] : 'html';
        $content .= $this->format_plain_string_property('language', $language);

        // Rows - number of visible text lines
        if (isset($field['rows']) && $field['rows'] !== '' && $field['rows'] !== null) {
            $content .= $this->format_number_property('rows', intval($field['rows']));
        }

        return $content;
    }
}
