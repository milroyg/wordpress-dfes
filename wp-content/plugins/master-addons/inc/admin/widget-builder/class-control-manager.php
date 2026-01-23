<?php
namespace MasterAddons\Admin\WidgetBuilder;

defined('ABSPATH') || exit;

/**
 * Control Manager
 * Loads and manages all control type builders
 */
class Control_Manager {

    private static $instance = null;
    private $controls = [];

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->load_controls();
    }

    /**
     * Load all control classes
     */
    private function load_controls() {
        // Load base class first
        require_once __DIR__ . '/controls/class-control-base.php';

        // Load all control type classes
        $control_files = [
            'background',
            'border',
            'box-shadow',
            'choose',
            'code',
            'color',
            'date-time',
            'dimensions',
            'divider',
            'font',
            'gallery',
            'heading',
            'hidden',
            'icons',
            'media',
            'number',
            'popover-toggle',
            'repeater',
            'select',
            'select2',
            'slider',
            'switcher',
            'tabs',
            'text',
            'textarea',
            'text-shadow',
            'typography',
            'url',
            'visual-choice',
            'wysiwyg',
        ];

        foreach ($control_files as $file) {
            $file_path = __DIR__ . '/controls/' . $file . '.php';
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }

    /**
     * Get control builder instance by type
     *
     * @param string $type Control type (TEXT, TEXTAREA, etc.) - can be uppercase or lowercase
     * @return Controls\Control_Base|null
     */
    public function get_control($type) {
        // Convert to uppercase for consistency (React UI sends lowercase, but we need uppercase for mapping)
        $type = strtoupper($type);

        // Return cached instance if exists
        if (isset($this->controls[$type])) {
            return $this->controls[$type];
        }

        // Map control types to classes
        $class_map = [
            'BACKGROUND' => 'Background',
            'BORDER' => 'Border',
            'BOX_SHADOW' => 'Box_Shadow',
            'CHOOSE' => 'Choose',
            'CODE' => 'Code',
            'COLOR' => 'Color',
            'DATE_TIME' => 'Date_Time',
            'DIMENSIONS' => 'Dimensions',
            'DIVIDER' => 'Divider',
            'FONT' => 'Font',
            'GALLERY' => 'Gallery',
            'GROUP_CONTROL_TYPOGRAPHY' => 'Typography',
            'HEADING' => 'Heading',
            'HIDDEN' => 'Hidden',
            'ICONS' => 'Icons',
            'MEDIA' => 'Media',
            'NUMBER' => 'Number',
            'POPOVER_TOGGLE' => 'Popover_Toggle',
            'REPEATER' => 'Repeater',
            'SELECT' => 'Select',
            'SELECT2' => 'Select2',
            'SLIDER' => 'Slider',
            'SWITCHER' => 'Switcher',
            'TABS' => 'Tabs',
            'TEXT' => 'Text',
            'TEXTAREA' => 'Textarea',
            'TEXT_SHADOW' => 'Text_Shadow',
            'TYPOGRAPHY' => 'Typography',
            'URL' => 'Url',
            'VISUAL_CHOICE' => 'Visual_Choice',
            'WYSIWYG' => 'Wysiwyg',
        ];
        if (!isset($class_map[$type])) {
            return null;
        }

        $class_name = '\\MasterAddons\\Admin\\WidgetBuilder\\Controls\\' . $class_map[$type];

        if (!class_exists($class_name)) {
            return null;
        }

        // Create and cache instance
        $this->controls[$type] = new $class_name();

        return $this->controls[$type];
    }

    /**
     * Build control output
     *
     * @param string $control_key Control key
     * @param array $field Field configuration
     * @param string $type Control type
     * @return string
     */
    public function build_control($control_key, $field, $type) {
        $control = $this->get_control($type);
        
        if (!$control) {
            // Fallback for unknown control types
            return $this->build_fallback_control($control_key, $field, $type);
        }

        return $control->build($control_key, $field);
    }

    /**
     * Build fallback control for unknown types
     *
     * @param string $control_key Control key
     * @param array $field Field configuration
     * @param string $type Control type
     * @return string
     */
    private function build_fallback_control($control_key, $field, $type) {
        $label = !empty($field['label']) ? esc_js($field['label']) : 'Control';

        $content = "\t\t\$this->add_control(\n";
        $content .= "\t\t\t'{$control_key}',\n";
        $content .= "\t\t\t[\n";
        $content .= "\t\t\t\t'label' => esc_html__('{$label}', 'master-addons'),\n";
        $content .= "\t\t\t\t'type' => Controls_Manager::{$type},\n";

        if (isset($field['default'])) {
            $default = is_string($field['default']) ? "'" . esc_js($field['default']) . "'" : $field['default'];
            $content .= "\t\t\t\t'default' => {$default},\n";
        }

        $content .= "\t\t\t]\n";
        $content .= "\t\t);\n\n";

        return $content;
    }
}
