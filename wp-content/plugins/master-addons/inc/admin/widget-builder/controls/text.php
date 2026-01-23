<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * TEXT Control
 * Handles Elementor TEXT control type
 *
 * Supported properties from Control Settings Panel:
 *
 * EDIT TEXT SETTINGS:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - placeholder: Placeholder text
 * - default: Default value
 *
 * COMMON SETTINGS (handled by base class):
 * - show_label: Show/hide label (boolean)
 * - label_block: Display label on separate line (boolean)
 * - responsive: Enable responsive control (boolean)
 * - dynamic: Enable dynamic content support (boolean)
 * - separator: Control separator position (none/before/after)
 *
 * TEXT-SPECIFIC SETTINGS:
 * - input_type: HTML5 input type (text, url, email, tel, password, search, number)
 * - title: Tooltip text on hover
 * - classes: Custom CSS classes for control wrapper
 *
 * ADVANCED:
 * - condition: Conditional display logic (array)
 * - selectors: CSS selectors for styling (array)
 */
class Text extends Control_Base {

    public function get_type() {
        return 'TEXT';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Text';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header (label + type + responsive)
        // If responsive=true, this will use add_responsive_control instead of add_control
        $content = $this->build_control_header($control_key, $label, 'TEXT', $is_responsive);

        // Add TEXT-specific properties FIRST (before common properties)
        $content .= $this->build_text_properties($field);

        // Add common properties (default, placeholder, description, separator, condition, etc.)
        // This includes: default, placeholder, description, label_block, show_label,
        // dynamic, separator, condition/conditions, selectors
        // Note: responsive is handled via method name change in header
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build TEXT-specific properties
     * These are properties unique to TEXT control type
     *
     * @param array $field Field configuration from React UI
     * @return string
     */
    protected function build_text_properties($field) {
        $content = '';

        // Input type (HTML5 input types: text, url, email, tel, password, search, number)
        // From form: Not visible in image, but supported by Elementor
        if (!empty($field['input_type']) && $field['input_type'] !== 'text') {
            $content .= $this->format_plain_string_property('input_type', $field['input_type']);
        }

        // Title (tooltip on hover)
        // From form: Not visible in image, but supported by Elementor
        if (!empty($field['title'])) {
            $content .= $this->format_string_property('title', $field['title']);
        }

        // Classes (custom CSS classes for control wrapper in the panel)
        // From form: "Control Classes" field - "Add custom classes to control wrapper in the panel"
        if (!empty($field['classes'])) {
            $content .= $this->format_plain_string_property('classes', $field['classes']);
        }

        return $content;
    }
}
/**
 * FIELD MAPPING FROM REACT UI TO PHP:
 * ====================================
 *
 * From Control Settings Panel [Image #1]:
 *
 * 1. Dynamic Support (toggle) → field['dynamic'] → handled by base class
 *    Output: 'dynamic' => ['active' => true]
 *
 * 2. Separator (dropdown: None/Before/After) → field['separator'] → handled by base class
 *    Output: 'separator' => 'none|before|after'
 *
 * 3. Conditions (condition builder) → field['condition'] → handled by base class
 *    Example: ['other_control' => 'value']
 *    Output: 'condition' => ['other_control' => 'value']
 *
 * 4. Control Classes (text input) → field['classes'] → handled in build_text_properties()
 *    Output: 'classes' => 'custom-class-name'
 *
 * 5. Selector (text input) → field['selector'] or field['selectors'] → handled by base class
 *    Example: {{WRAPPER}} .control-class
 *    Output: 'selectors' => ['{{WRAPPER}} .control-class' => 'css-property']
 *
 * Additional common fields:
 * - Label → field['label'] → used in control header
 * - Name → used for $control_key
 * - Description → field['description'] → handled by base class
 * - Placeholder → field['placeholder'] → handled by base class
 * - Default Value → field['default'] → handled by base class
 * - Show Label → field['show_label'] → handled by base class
 * - Label Block → field['label_block'] → handled by base class
 * - Responsive Control → field['responsive'] → handled by base class
 */
