<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * TABS Control
 * Handles Elementor TABS control type
 * Creates a tabbed interface for organizing controls
 *
 * Note: Tabs in Elementor are not regular controls but structural elements
 * They require special handling with start_controls_tabs() and end_controls_tabs()
 *
 * Structure:
 * - start_controls_tabs (container)
 *   - start_controls_tab (individual tab)
 *     - controls inside tab
 *   - end_controls_tab
 *   - start_controls_tab (another tab)
 *     - controls inside tab
 *   - end_controls_tab
 * - end_controls_tabs
 *
 * Supported properties:
 * - tabs: Array of tab configurations, each with:
 *   - name: Tab identifier
 *   - label: Tab label text
 *   - controls: Array of controls within this tab
 */
class Tabs extends Control_Base {

    public function get_type() {
        return 'TABS';
    }

    public function build($control_key, $field) {

        if (empty($field['tabs'])) {
            // Try to build tabs array from React UI format (fields + tab_fields)
            if (!empty($field['fields']) && is_array($field['fields'])) {
                $tabs = [];
                $tab_fields = !empty($field['tab_fields']) && is_array($field['tab_fields']) ? $field['tab_fields'] : [];

                foreach ($field['fields'] as $tab_definition) {
                    if (empty($tab_definition['name'])) {
                        continue;
                    }

                    $tab_name = $tab_definition['name'];
                    $tab = [
                        'name' => $tab_name,
                        'label' => !empty($tab_definition['label']) ? $tab_definition['label'] : ucfirst($tab_name),
                        'controls' => []
                    ];

                    // Add controls for this tab if they exist
                    if (!empty($tab_fields[$tab_name]) && is_array($tab_fields[$tab_name])) {
                        $tab['controls'] = $tab_fields[$tab_name];
                    }

                    $tabs[] = $tab;
                }

                // Update field with converted tabs array
                $field['tabs'] = $tabs;
            }
        }

        // Now check if we have tabs
        if (empty($field['tabs']) || !is_array($field['tabs'])) {
            return "// Tabs control requires 'tabs' array with tab configurations\n";
        }

        $content = "\n\t\t// Start tabs container\n";
        $content .= "\t\t\$this->start_controls_tabs(\n";
        $content .= "\t\t\t'{$control_key}'\n";
        $content .= "\t\t);\n\n";

        // Process each tab
        foreach ($field['tabs'] as $tab_index => $tab) {
            if (empty($tab['name']) || empty($tab['label'])) {
                continue;
            }

            $tab_key = $control_key . '_tab_' . $tab['name'];
            $tab_label = !empty($tab['label']) ? $tab['label'] : 'Tab ' . ($tab_index + 1);

            // Start individual tab
            $content .= "\t\t// Tab: {$tab_label}\n";
            $content .= "\t\t\$this->start_controls_tab(\n";
            $content .= "\t\t\t'{$tab_key}',\n";
            $content .= "\t\t\t[\n";
            $content .= "\t\t\t\t'label' => esc_html__('" . esc_js($tab_label) . "', 'master-addons'),\n";

            // Add condition if present
            if (!empty($tab['condition'])) {
                $content .= $this->build_condition($tab['condition']);
            }

            $content .= "\t\t\t]\n";
            $content .= "\t\t);\n\n";

            // Add controls within this tab
            if (!empty($tab['controls']) && is_array($tab['controls'])) {
                $content .= $this->build_tab_controls($tab['controls'], $field);
            }

            // End individual tab
            $content .= "\t\t\$this->end_controls_tab();\n\n";
        }

        // End tabs container
        $content .= "\t\t// End tabs container\n";
        $content .= "\t\t\$this->end_controls_tabs();\n\n";

        return $content;
    }

    /**
     * Build controls within a tab
     * This delegates to the appropriate control builders
     *
     * @param array $controls Array of control configurations
     * @param array $parent_field Parent field context for passing tab/widget_id
     * @return string Generated PHP code
     */
    protected function build_tab_controls($controls, $parent_field = []) {
        $content = '';

        // Get the Control_Manager instance
        $control_manager = \MasterAddons\Admin\WidgetBuilder\Control_Manager::get_instance();

        foreach ($controls as $control) {
            if (empty($control['type']) || empty($control['name'])) {
                continue;
            }

            // Get tab context from parent field
            $tab = !empty($parent_field['_tab']) ? $parent_field['_tab'] : 'content';
            $widget_id = !empty($parent_field['_widget_id']) ? $parent_field['_widget_id'] : '';

            // Generate control key with proper prefix based on tab
            $tab_prefix = '';
            if ($tab === 'style') {
                $tab_prefix = 'jltma_style_';
            } elseif ($tab === 'advanced') {
                $tab_prefix = 'jltma_advanced_';
            } else {
                $tab_prefix = 'jltma_content_';
            }

            // Create sanitized control slug from label and add widget ID
            $control_label = !empty($control['label']) ? $control['label'] : $control['name'];
            $control_slug = $this->sanitize_key($control_label);
            $control_key = $tab_prefix . $control_slug . '_' . $widget_id;

            // Pass context to control
            $control['_tab'] = $tab;
            $control['_widget_id'] = $widget_id;
            $control['_tab_prefix'] = $tab_prefix;

            // Use Control_Manager to build the control
            $control_type = strtoupper($control['type']);
            $content .= $control_manager->build_control($control_key, $control, $control_type);
        }

        return $content;
    }
}