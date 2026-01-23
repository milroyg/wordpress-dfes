<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * CHOOSE Control
 * Handles Elementor CHOOSE control type
 * Visual chooser with icon options (like alignment, position selector)
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default selected value
 * - options: Array of options with icon and title (key => ['title' => '', 'icon' => ''])
 * - toggle: Allow deselecting by clicking again (boolean)
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - responsive: Enable responsive control
 * - separator: Control separator position
 * - condition: Conditional display logic
 * - selectors: CSS selectors for styling
 */
class Choose extends Control_Base {

    public function get_type() {
        return 'CHOOSE';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Choose';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'CHOOSE', $is_responsive);

        // Add CHOOSE-specific properties FIRST
        $content .= $this->build_choose_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build CHOOSE-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_choose_properties($field) {
        $content = '';

        // Options - chooser options with icons
        if (!empty($field['options']) && is_array($field['options'])) {
            $content .= $this->build_options($field['options']);
        }

        // Toggle - allow deselecting
        if (isset($field['toggle']) && $field['toggle']) {
            $content .= "\t\t\t\t'toggle' => true,\n";
        }

        return $content;
    }

    /**
     * Build options array with icons
     *
     * @param array $options
     * @return string
     */
    private function build_options($options) {
        // Convert array format to keyed object format
        // If options is [{key: 'left', title: '...', icon: '...'}, ...]
        // Convert to {'left': {title: '...', icon: '...'}, ...}
        $normalized_options = [];

        foreach ($options as $index => $option) {
            // If option has a 'key' property, use it; otherwise use the array index
            if (isset($option['key']) && !empty($option['key'])) {
                $key = $option['key'];
                $normalized_options[$key] = [
                    'title' => $option['title'] ?? '',
                    'icon' => $option['icon'] ?? ''
                ];
            } else {
                // Fallback to using array index if no key is specified
                $normalized_options[$index] = $option;
            }
        }

        $content = "\t\t\t\t'options' => [\n";
        foreach ($normalized_options as $key => $option) {
            $content .= "\t\t\t\t\t'" . esc_js($key) . "' => [\n";
            if (isset($option['title'])) {
                $content .= "\t\t\t\t\t\t'title' => esc_html__('" . esc_js($option['title']) . "', 'master-addons'),\n";
            }
            if (isset($option['icon'])) {
                $content .= "\t\t\t\t\t\t'icon' => '" . esc_js($option['icon']) . "',\n";
            }
            $content .= "\t\t\t\t\t],\n";
        }
        $content .= "\t\t\t\t],\n";
        return $content;
    }
}
