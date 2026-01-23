<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * VISUAL_CHOICE Control
 * Handles Elementor VISUAL_CHOICE control type
 * Visual selector with image options (like layout selector)
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default selected value
 * - options: Array of options with title and image (key => ['title' => '', 'image' => ''])
 * - columns: Number of columns for display (default 3)
 * - prefix_class: CSS class prefix for selected option
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Visual_Choice extends Control_Base {

    public function get_type() {
        return 'VISUAL_CHOICE';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Visual Choice';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'VISUAL_CHOICE', $is_responsive);

        // Add VISUAL_CHOICE-specific properties FIRST
        $content .= $this->build_visual_choice_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build visual choice-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_visual_choice_properties($field) {
        $content = '';

        // Options - visual options with images
        if (!empty($field['options']) && is_array($field['options'])) {
            $content .= $this->build_visual_options($field['options']);
        }

        // Columns
        if (!empty($field['columns'])) {
            $content .= "\t\t\t\t'columns' => " . intval($field['columns']) . ",\n";
        }

        // Prefix class
        if (!empty($field['prefix_class'])) {
            $content .= "\t\t\t\t'prefix_class' => '" . esc_js($field['prefix_class']) . "',\n";
        }

        return $content;
    }

    /**
     * Build visual options array with images
     *
     * @param array $options
     * @return string
     */
    private function build_visual_options($options) {
        // Convert array format to keyed object format
        // If options is [{key: 'fist', title: '...', image: '...'}, ...]
        // Convert to {'fist': {title: '...', image: '...'}, ...}
        $normalized_options = [];

        foreach ($options as $index => $option) {
            // If option has a 'key' property, use it; otherwise use the array index
            if (isset($option['key']) && !empty($option['key'])) {
                $key = $option['key'];
                $normalized_options[$key] = [
                    'title' => $option['title'] ?? '',
                    'image' => $option['image'] ?? ''
                ];
            } else {
                // Fallback to using array index if no key is specified
                $normalized_options[$index] = $option;
            }
        }

        $content = "\t\t\t\t'options' => [\n";
        foreach ($normalized_options as $key => $option) {
            $content .= "\t\t\t\t\t'" . esc_js($key) . "' => [\n";

            // Title is required
            if (isset($option['title'])) {
                $content .= "\t\t\t\t\t\t'title' => esc_attr__('" . esc_js($option['title']) . "', 'master-addons'),\n";
            }

            // Image URL
            if (isset($option['image'])) {
                $content .= "\t\t\t\t\t\t'image' => '" . esc_js($option['image']) . "',\n";
            }

            $content .= "\t\t\t\t\t],\n";
        }
        $content .= "\t\t\t\t],\n";
        return $content;
    }
}
