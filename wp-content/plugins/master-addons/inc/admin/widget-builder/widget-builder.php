<?php

namespace MasterAddons\Inc\Admin\WidgetBuilder;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use MasterAddons\Inc\Classes\JLTMA_Extension_Prototype;

/**
 * Master Addons Widget Builder
 * No-code custom widget creation for Elementor
 */

class JLTMA_Widget_Builder extends JLTMA_Extension_Prototype {

    public static $instance = null;

    public function __construct() {
        // NOTE: admin_menu is now handled by JLTMA_Widget_Admin class
        // add_action('admin_menu', array($this, 'add_widget_builder_menu'));

        // NOTE: admin_enqueue_scripts is now handled by JLTMA_Widget_Admin class
        // add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        add_action('wp_ajax_jltma_save_custom_widget', array($this, 'save_custom_widget'));
        add_action('wp_ajax_jltma_delete_custom_widget', array($this, 'delete_custom_widget'));
        add_action('wp_ajax_jltma_get_custom_widgets', array($this, 'get_custom_widgets'));
        add_action('wp_ajax_jltma_get_widget_fields', array($this, 'get_widget_fields'));
        add_action('elementor/widgets/widgets_registered', array($this, 'register_custom_widgets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_widget_styles'));
    }

    /**
     * Add Widget Builder submenu under Master Addons
     * NOTE: Menu is now handled by JLTMA_Widget_Admin class
     */
    public function add_widget_builder_menu() {
        // Menu registration moved to JLTMA_Widget_Admin class
        // This method kept for backward compatibility but does nothing
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        // Only load on Widget Builder page
        if ($hook !== 'master-addons_page_jltma-widget-builder') {
            return;
        }

        // Enqueue Widget Builder React app
        wp_enqueue_script(
            'jltma-widget-builder-app',
            JLTMA_URL . '/assets/js/admin/widget-builder-app.js',
            array('wp-element', 'wp-i18n'),
            JLTMA_VER,
            true
        );

        // Enqueue Widget Builder styles
        wp_enqueue_style(
            'jltma-widget-builder',
            JLTMA_URL . '/assets/css/admin/widget-builder.css',
            array(),
            JLTMA_VER
        );

        // Localize script data
        $localize_data = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jltma_widget_builder_nonce'),
            'pluginUrl' => JLTMA_URL,
            'strings' => array(
                'widgetBuilder' => __('Widget Builder', 'master-addons'),
                'createWidget' => __('Create New Widget', 'master-addons'),
                'editWidget' => __('Edit Widget', 'master-addons'),
                'deleteWidget' => __('Delete Widget', 'master-addons'),
                'saveWidget' => __('Save Widget', 'master-addons'),
                'widgetName' => __('Widget Name', 'master-addons'),
                'widgetTitle' => __('Widget Title', 'master-addons'),
                'widgetIcon' => __('Widget Icon', 'master-addons'),
                'widgetCategory' => __('Widget Category', 'master-addons'),
                'addField' => __('Add Field', 'master-addons'),
                'fieldType' => __('Field Type', 'master-addons'),
                'fieldLabel' => __('Field Label', 'master-addons'),
                'fieldName' => __('Field Name', 'master-addons'),
                'preview' => __('Preview', 'master-addons'),
                'livePreview' => __('Live Preview', 'master-addons'),
                'widgetCode' => __('Widget Code', 'master-addons'),
                'exportWidget' => __('Export Widget', 'master-addons'),
                'importWidget' => __('Import Widget', 'master-addons'),
                'noCodeBuilder' => __('No-Code Widget Builder', 'master-addons'),
                'dragDropInterface' => __('Drag & Drop Interface', 'master-addons'),
                'unlimitedWidgets' => __('Create Unlimited Custom Widgets', 'master-addons'),
                'searchWidgets' => __('Search widgets...', 'master-addons'),
                'filterByCategory' => __('Filter by Category', 'master-addons'),
                'allCategories' => __('All Categories', 'master-addons'),
                'basic' => __('Basic', 'master-addons'),
                'advanced' => __('Advanced', 'master-addons'),
                'ecommerce' => __('eCommerce', 'master-addons'),
                'forms' => __('Forms', 'master-addons'),
                'media' => __('Media', 'master-addons'),
                'fieldTypes' => array(
                    'text' => __('Text Input', 'master-addons'),
                    'textarea' => __('Textarea', 'master-addons'),
                    'wysiwyg' => __('WYSIWYG Editor', 'master-addons'),
                    'select' => __('Select Dropdown', 'master-addons'),
                    'choose' => __('Choose Control', 'master-addons'),
                    'color' => __('Color Picker', 'master-addons'),
                    'media' => __('Media Upload', 'master-addons'),
                    'gallery' => __('Gallery', 'master-addons'),
                    'icon' => __('Icon Picker', 'master-addons'),
                    'url' => __('URL Input', 'master-addons'),
                    'number' => __('Number Input', 'master-addons'),
                    'slider' => __('Range Slider', 'master-addons'),
                    'date_time' => __('Date Time Picker', 'master-addons'),
                    'switcher' => __('Switcher Toggle', 'master-addons'),
                    'border' => __('Border Control', 'master-addons'),
                    'typography' => __('Typography', 'master-addons'),
                    'background' => __('Background', 'master-addons'),
                    'box_shadow' => __('Box Shadow', 'master-addons'),
                    'text_shadow' => __('Text Shadow', 'master-addons'),
                    'repeater' => __('Repeater Fields', 'master-addons')
                )
            ),
        );

        wp_localize_script('jltma-widget-builder-app', 'JLTMAWidgetBuilder', $localize_data);
    }

    /**
     * Render Widget Builder page
     * NOTE: This is now handled by JLTMA_Widget_Admin class
     * When widget_id is present, it loads React editor
     * When no widget_id, it shows list table (handled by JLTMA_Widget_Admin)
     */
    public function widget_builder_page() {
        // Check if we're editing a specific widget
        $widget_id = isset($_GET['widget_id']) ? intval($_GET['widget_id']) : 0;

        if ($widget_id) {
            // Render the Widget Builder React app editor
            ?>
            <div class="wrap jltma-widget-builder-wrap">
                <div id="jltma-widget-builder-app"></div>
            </div>
            <?php
        } else {
            // List table is handled by JLTMA_Widget_Admin class
            // This ensures backward compatibility
            ?>
            <div class="wrap jltma-widget-builder-wrap">
                <div id="jltma-widget-builder-app"></div>
            </div>
            <?php
        }
    }


    /**
     * AJAX: Save custom widget
     */
    public function save_custom_widget() {
        check_ajax_referer('jltma_widget_builder_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions', 'master-addons'));
            return;
        }

        $widget_data = isset($_POST['widget_data']) ? json_decode(stripslashes($_POST['widget_data']), true) : array();

        if (empty($widget_data['name']) || empty($widget_data['title'])) {
            wp_send_json_error(__('Widget name and title are required', 'master-addons'));
            return;
        }

        // Sanitize widget data
        $widget_data = $this->sanitize_widget_data($widget_data);

        // Validate widget name format
        if (!preg_match('/^[a-z0-9_-]+$/', $widget_data['name'])) {
            wp_send_json_error(__('Widget name must contain only lowercase letters, numbers, hyphens and underscores', 'master-addons'));
            return;
        }

        // Save to database
        $widgets = get_option('jltma_custom_widgets', array());
        $widget_id = isset($widget_data['id']) ? $widget_data['id'] : uniqid('jltma_widget_');
        $widget_data['id'] = $widget_id;
        $widgets[$widget_id] = $widget_data;

        if (update_option('jltma_custom_widgets', $widgets)) {
            // Generate widget class file
            if ($this->generate_widget_class($widget_data)) {
                wp_send_json_success(array(
                    'message' => __('Widget saved successfully', 'master-addons'),
                    'widget_id' => $widget_id
                ));
            } else {
                wp_send_json_error(__('Widget saved but failed to generate class file', 'master-addons'));
            }
        } else {
            wp_send_json_error(__('Failed to save widget', 'master-addons'));
        }
    }

    /**
     * AJAX: Delete custom widget
     */
    public function delete_custom_widget() {
        check_ajax_referer('jltma_widget_builder_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions', 'master-addons'));
        }

        $widget_id = sanitize_text_field($_POST['widget_id']);
        $widgets = get_option('jltma_custom_widgets', array());

        if (isset($widgets[$widget_id])) {
            unset($widgets[$widget_id]);
            update_option('jltma_custom_widgets', $widgets);

            // Delete widget class file
            $this->delete_widget_class($widget_id);

            wp_send_json_success(__('Widget deleted successfully', 'master-addons'));
        } else {
            wp_send_json_error(__('Widget not found', 'master-addons'));
        }
    }

    /**
     * AJAX: Get custom widgets
     */
    public function get_custom_widgets() {
        check_ajax_referer('jltma_widget_builder_nonce', 'nonce');

        $widgets = get_option('jltma_custom_widgets', array());
        wp_send_json_success($widgets);
    }

    /**
     * AJAX: Get widget field types
     */
    public function get_widget_fields() {
        check_ajax_referer('jltma_widget_builder_nonce', 'nonce');

        wp_send_json_success($this->get_available_field_types());
    }

    /**
     * Register custom widgets with Elementor
     */
    public function register_custom_widgets() {
        if (!class_exists('\\Elementor\\Plugin')) {
            return;
        }

        // Register custom categories first
        $this->register_custom_categories();

        $widgets = get_option('jltma_custom_widgets', array());


        if (empty($widgets) || !is_array($widgets)) {
            return;
        }

        foreach ($widgets as $widget_id => $widget_data) {
            if (is_array($widget_data) && !empty($widget_data['name'])) {
                $this->register_single_widget($widget_id, $widget_data);
            }
        }
    }

    /**
     * Register custom Elementor categories
     */
    private function register_custom_categories() {
        if (!class_exists('\\Elementor\\Plugin')) {
            return;
        }

        // Get custom categories from options
        $custom_categories = get_option('jltma_custom_widget_categories', array());

        if (empty($custom_categories) || !is_array($custom_categories)) {
            return;
        }

        $elements_manager = \Elementor\Plugin::$instance->elements_manager;

        // Register each custom category
        foreach ($custom_categories as $slug => $title) {
            // Check if category doesn't already exist
            $existing_categories = $elements_manager->get_categories();

            if (!isset($existing_categories[$slug])) {
                $elements_manager->add_category(
                    $slug,
                    array(
                        'title' => $title,
                        'icon' => 'eicon-posts-ticker',
                    )
                );
            }
        }
    }

    /**
     * Register a single custom widget
     */
    private function register_single_widget($widget_id, $widget_data) {
        $widget_file = JLTMA_PATH . 'inc/admin/widgetbuilder/generated/' . $widget_id . '.php';

        if (file_exists($widget_file)) {
            require_once $widget_file;

            $class_name = 'JLTMA_Custom_Widget_' . ucfirst($widget_id);
            if (class_exists($class_name)) {
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new $class_name());
            }
        }
    }

    /**
     * Sanitize widget data
     */
    private function sanitize_widget_data($data) {
        if (!is_array($data)) {
            return array();
        }

        $sanitized = array();

        $sanitized['id'] = isset($data['id']) ? sanitize_key($data['id']) : '';
        $sanitized['name'] = isset($data['name']) ? sanitize_key($data['name']) : '';
        $sanitized['title'] = isset($data['title']) ? sanitize_text_field($data['title']) : '';
        $sanitized['icon'] = isset($data['icon']) ? sanitize_text_field($data['icon']) : 'eicon-star';
        $sanitized['category'] = isset($data['category']) ? sanitize_key($data['category']) : 'basic';
        $sanitized['description'] = isset($data['description']) ? sanitize_textarea_field($data['description']) : '';
        $sanitized['fields'] = isset($data['fields']) && is_array($data['fields']) ? $this->sanitize_fields($data['fields']) : array();
        $sanitized['template'] = isset($data['template']) ? wp_kses($data['template'], wp_kses_allowed_html('post')) : '';
        $sanitized['css'] = isset($data['css']) ? $this->sanitize_css($data['css']) : '';
        $sanitized['js'] = isset($data['js']) ? $this->sanitize_js($data['js']) : '';
        $sanitized['created_at'] = isset($data['created_at']) ? sanitize_text_field($data['created_at']) : current_time('mysql');
        $sanitized['updated_at'] = current_time('mysql');

        return $sanitized;
    }

    /**
     * Sanitize widget fields
     */
    private function sanitize_fields($fields) {
        $sanitized = array();

        foreach ($fields as $field) {
            $sanitized_field = array();
            $sanitized_field['id'] = sanitize_key($field['id']);
            $sanitized_field['type'] = sanitize_text_field($field['type']);
            $sanitized_field['label'] = sanitize_text_field($field['label']);
            $sanitized_field['name'] = sanitize_key($field['name']);
            $sanitized_field['default'] = isset($field['default']) ? sanitize_text_field($field['default']) : '';
            $sanitized_field['options'] = isset($field['options']) ? array_map('sanitize_text_field', $field['options']) : array();
            $sanitized_field['condition'] = isset($field['condition']) ? $field['condition'] : array();

            $sanitized[] = $sanitized_field;
        }

        return $sanitized;
    }

    /**
     * Sanitize CSS content
     */
    private function sanitize_css($css) {
        // Remove potentially dangerous CSS
        $css = sanitize_textarea_field($css);

        // Remove @import, javascript:, and other potentially dangerous CSS
        $css = preg_replace('/@import\s+/i', '', $css);
        $css = preg_replace('/javascript\s*:/i', '', $css);
        $css = preg_replace('/expression\s*\(/i', '', $css);
        $css = preg_replace('/behavior\s*:/i', '', $css);

        return $css;
    }

    /**
     * Sanitize JavaScript content
     */
    private function sanitize_js($js) {
        // Basic sanitization for JavaScript
        $js = sanitize_textarea_field($js);

        // Remove potentially dangerous functions
        $dangerous_functions = array(
            'eval',
            'setTimeout',
            'setInterval',
            'Function',
            'XMLHttpRequest',
            'fetch'
        );

        foreach ($dangerous_functions as $func) {
            $js = preg_replace('/\b' . preg_quote($func, '/') . '\s*\(/i', '/* ' . $func . ' removed */ (', $js);
        }

        return $js;
    }

    /**
     * Generate widget class file
     */
    private function generate_widget_class($widget_data) {
        $generated_dir = JLTMA_PATH . 'inc/admin/widgetbuilder/generated/';

        if (!file_exists($generated_dir)) {
            if (!wp_mkdir_p($generated_dir)) {
                return false;
            }
        }

        if (!is_writable($generated_dir)) {
            return false;
        }

        $widget_id = $widget_data['id'];
        $class_name = 'JLTMA_Custom_Widget_' . ucfirst($widget_id);
        $widget_name = sanitize_key($widget_data['name']);

        $class_content = $this->get_widget_class_template($widget_data, $class_name, $widget_name);

        $result = file_put_contents($generated_dir . $widget_id . '.php', $class_content);
        return $result !== false;
    }

    /**
     * Get widget class template
     */
    private function get_widget_class_template($widget_data, $class_name, $widget_name) {
        // Generate field controls
        $field_controls = '';
        foreach ($widget_data['fields'] as $field) {
            $field_controls .= $this->generate_field_control($field) . "\n";
        }

        // Generate template rendering code
        $template_code = '';
        if (!empty($widget_data['template'])) {
            $template_escaped = addslashes($widget_data['template']);
            $template_code = "
        // Custom template
        \$template = '{$template_escaped}';

        // Replace placeholders with actual values
        foreach (\$settings as \$key => \$value) {
            if (is_string(\$value)) {
                \$template = str_replace('{{' . \$key . '}}', \$value, \$template);
            } elseif (is_array(\$value) && isset(\$value['url'])) {
                \$template = str_replace('{{' . \$key . '}}', \$value['url'], \$template);
            }
        }

        echo wp_kses_post(\$template);";
        } else {
            // Generate default template
            foreach ($widget_data['fields'] as $field) {
                if ($field['type'] === 'text' || $field['type'] === 'textarea') {
                    $template_code .= "
        if (!empty(\$settings['{$field['name']}'])) {
            echo '<div class=\"field-{$field['name']}\">' . esc_html(\$settings['{$field['name']}']) . '</div>';
        }";
                } elseif ($field['type'] === 'wysiwyg') {
                    $template_code .= "
        if (!empty(\$settings['{$field['name']}'])) {
            echo '<div class=\"field-{$field['name']}\">' . wp_kses_post(\$settings['{$field['name']}']) . '</div>';
        }";
                } elseif ($field['type'] === 'media') {
                    $template_code .= "
        if (!empty(\$settings['{$field['name']}']['url'])) {
            echo '<img src=\"' . esc_url(\$settings['{$field['name']}']['url']) . '\" alt=\"{$field['label']}\" class=\"field-{$field['name']}\">';
        }";
                }
            }
        }

        // Optional dependencies
        $style_depends = !empty($widget_data['css']) ? "
    public function get_style_depends() {
        return ['jltma-custom-widget-{$widget_name}'];
    }" : '';

        $script_depends = !empty($widget_data['js']) ? "
    public function get_script_depends() {
        return ['jltma-custom-widget-{$widget_name}'];
    }" : '';

        $template = "<?php

namespace MasterAddons\\Inc\\Admin\\WidgetBuilder\\Generated;

use Elementor\\Widget_Base;
use Elementor\\Controls_Manager;
use Elementor\\Group_Control_Typography;
use Elementor\\Group_Control_Background;
use Elementor\\Group_Control_Border;
use Elementor\\Group_Control_Box_Shadow;
use Elementor\\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Widget: {$widget_data['title']}
 * Generated by Master Addons Widget Builder
 */
class {$class_name} extends Widget_Base {

    public function get_name() {
        return '{$widget_name}';
    }

    public function get_title() {
        return __('{$widget_data['title']}', 'master-addons');
    }

    public function get_icon() {
        return '{$widget_data['icon']}';
    }

    public function get_categories() {
        return ['{$widget_data['category']}'];
    }

    public function get_keywords() {
        return ['master-addons', 'custom', 'widget', '{$widget_name}'];
    }

    protected function _register_controls() {
        \$this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'master-addons'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

{$field_controls}
        \$this->end_controls_section();

        // Style section
        \$this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'master-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        \$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'widget_typography',
                'label' => __('Typography', 'master-addons'),
                'selector' => '{{WRAPPER}} .jltma-custom-widget',
            ]
        );

        \$this->end_controls_section();
    }

    protected function render() {
        \$settings = \$this->get_settings_for_display();

        echo '<div class=\"jltma-custom-widget jltma-{$widget_name}\">';
        \$this->render_widget_content(\$settings);
        echo '</div>';
    }

    private function render_widget_content(\$settings) {
{$template_code}
    }
{$style_depends}
{$script_depends}
}";

        return $template;
    }

    /**
     * Generate field control code
     */
    private function generate_field_control($field) {
        $control_type = $this->get_elementor_control_type($field['type']);

        $control_code = "        \$this->add_control(\n";
        $control_code .= "            '{$field['name']}',\n";
        $control_code .= "            [\n";
        $control_code .= "                'label' => __('{$field['label']}', 'master-addons'),\n";
        $control_code .= "                'type' => {$control_type},\n";

        if (!empty($field['default'])) {
            $control_code .= "                'default' => '{$field['default']}',\n";
        }

        if (!empty($field['options'])) {
            $options_export = var_export($field['options'], true);
            $control_code .= "                'options' => {$options_export},\n";
        }

        if (!empty($field['condition'])) {
            $condition_export = var_export($field['condition'], true);
            $control_code .= "                'condition' => {$condition_export},\n";
        }

        $control_code .= "            ]\n";
        $control_code .= "        );\n";

        return $control_code;
    }

    /**
     * Get Elementor control type
     */
    private function get_elementor_control_type($field_type) {
        $type_mapping = array(
            'text' => 'Controls_Manager::TEXT',
            'textarea' => 'Controls_Manager::TEXTAREA',
            'wysiwyg' => 'Controls_Manager::WYSIWYG',
            'select' => 'Controls_Manager::SELECT',
            'choose' => 'Controls_Manager::CHOOSE',
            'color' => 'Controls_Manager::COLOR',
            'media' => 'Controls_Manager::MEDIA',
            'gallery' => 'Controls_Manager::GALLERY',
            'icon' => 'Controls_Manager::ICONS',
            'url' => 'Controls_Manager::URL',
            'number' => 'Controls_Manager::NUMBER',
            'slider' => 'Controls_Manager::SLIDER',
            'date_time' => 'Controls_Manager::DATE_TIME',
            'switcher' => 'Controls_Manager::SWITCHER',
            'border' => 'Controls_Manager::BORDER',
            'repeater' => 'Controls_Manager::REPEATER'
        );

        return isset($type_mapping[$field_type]) ? $type_mapping[$field_type] : 'Controls_Manager::TEXT';
    }

    /**
     * Delete widget class file
     */
    private function delete_widget_class($widget_id) {
        $widget_file = JLTMA_PATH . 'inc/admin/widgetbuilder/generated/' . $widget_id . '.php';

        if (file_exists($widget_file)) {
            unlink($widget_file);
        }
    }

    /**
     * Enqueue widget styles and scripts
     */
    public function enqueue_widget_styles() {
        $widgets = get_option('jltma_custom_widgets', array());

        foreach ($widgets as $widget_id => $widget_data) {
            if (!empty($widget_data['css'])) {
                wp_add_inline_style('elementor-frontend', $widget_data['css']);
            }

            if (!empty($widget_data['js'])) {
                wp_add_inline_script('elementor-frontend', $widget_data['js']);
            }
        }
    }

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

// Initialize Widget Builder
JLTMA_Widget_Builder::get_instance();


// Widget Builder - Production Ready
