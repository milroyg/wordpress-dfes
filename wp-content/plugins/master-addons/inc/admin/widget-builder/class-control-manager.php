<?php
namespace MasterAddons\Inc\Admin\WidgetBuilder;

use MasterAddons\Inc\Classes\Helper;

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

        // Load FREE control type classes only
        $control_files = [
            'color',
            'heading',
            'hidden',
            'text',
            'textarea',
            'url',
            'number',
            'choose',
            'divider',
            'font',
            'media',
            'select',
            'switcher',
            'wysiwyg',
        ];

        foreach ($control_files as $file) {
            $file_path = __DIR__ . '/controls/' . $file . '.php';
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }

        // Allow Pro to load additional controls
        do_action('jltma_widget_builder_load_controls');
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

        // Map control types to classes (FREE controls only)
        $class_map = [
            'COLOR'    => 'Color',
            'HEADING'  => 'Heading',
            'HIDDEN'   => 'Hidden',
            'TEXT'     => 'Text',
            'TEXTAREA' => 'Textarea',
            'URL'      => 'Url',
            'NUMBER'   => 'Number',
            'CHOOSE'   => 'Choose',
            'DIVIDER'  => 'Divider',
            'FONT'     => 'Font',
            'MEDIA'    => 'Media',
            'SELECT'   => 'Select',
            'SWITCHER' => 'Switcher',
            'WYSIWYG'  => 'Wysiwyg',
        ];

        // Allow Pro to add additional control class mappings
        $class_map = apply_filters('jltma_widget_builder_control_class_map', $class_map);

        if (!isset($class_map[$type])) {
            return null;
        }

        // Check if class_map value is a full class name (from Pro) or just class name (free)
        if (strpos($class_map[$type], '\\') !== false) {
            $class_name = $class_map[$type];
        } else {
            $class_name = '\\MasterAddons\\Inc\\Admin\\WidgetBuilder\\Controls\\' . $class_map[$type];
        }

        if (!class_exists($class_name)) {
            return null;
        }

        // Create and cache instance
        $this->controls[$type] = new $class_name();

        return $this->controls[$type];
    }

    /**
     * Control types that are premium and not unlocked on this site.
     *
     * The catalog is declared once by Widget_Admin and Pro flips each entry's
     * isPro flag off through `jltma_widget_builder_pro_controls` when a licence
     * is active — the same list that locks the builder UI, reused here so the
     * editor cannot be a way around it.
     *
     * @return array Uppercase control types, as a lookup map.
     */
    public static function get_locked_types() {
        static $locked = null;

        if (null !== $locked) {
            return $locked;
        }

        $locked  = [];
        $catalog = apply_filters(
            'jltma_widget_builder_pro_controls',
            class_exists('\\MasterAddons\\Inc\\Admin\\WidgetBuilder\\Widget_Admin')
                ? Widget_Admin::get_pro_controls_catalog()
                : []
        );

        if (!is_array($catalog)) {
            return $locked;
        }

        foreach ($catalog as $control) {
            if (is_array($control) && !empty($control['isPro']) && !empty($control['type'])) {
                $locked[strtoupper($control['type'])] = true;
            }
        }

        return $locked;
    }

    /**
     * Is this control type premium and still locked?
     *
     * @param string $type Control type.
     * @return bool
     */
    public function is_locked_type($type) {
        $type   = strtoupper((string) $type);
        $locked = self::get_locked_types();

        if (isset($locked[$type])) {
            return true;
        }

        // Group controls arrive as GROUP_CONTROL_TYPOGRAPHY etc.
        if (0 === strpos($type, 'GROUP_CONTROL_')) {
            return isset($locked[substr($type, strlen('GROUP_CONTROL_'))]);
        }

        return false;
    }

    /**
     * Config for a premium control on a site without a licence.
     *
     * Registers no editable control. The value is carried by a HIDDEN control
     * under the key the control would normally own, so anything already saved
     * — by a Pro site, or by an imported widget — still reaches the frontend
     * and is preserved on save. A RAW_HTML notice explains the lock.
     *
     * @param string $control_key  Control key.
     * @param array  $field        Field configuration.
     * @param string $type         Control type.
     * @param bool   $with_notice  Whether to include the panel notice.
     * @return array
     */
    public function build_locked_control_config($control_key, $field, $type, $with_notice = true) {
        $label = !empty($field['label']) ? $field['label'] : 'Control';

        $args = [
            // translators: dynamic user-defined control label.
            'label' => esc_html($label),
            'type'  => class_exists('\\Elementor\\Controls_Manager') ? \Elementor\Controls_Manager::HIDDEN : 'hidden',
        ];

        if (isset($field['default'])) {
            $args['default'] = $field['default'];
        }

        $descriptor = [
            'key'        => $control_key,
            'responsive' => false,
            'method'     => 'locked',
            'args'       => $args,
            'notice'     => null,
        ];

        if ($with_notice) {
            $descriptor['notice'] = $this->build_locked_badge_config($control_key, $field, $label);
        }

        return $descriptor;
    }

    /**
     * The locked "(Pro)" badge shown in place of a premium control.
     *
     * Same shape the rest of the plugin uses for locked controls (see the Popup
     * Builder's Pro options): a CHOOSE control whose only option is a lock icon,
     * so the row reads as a disabled button, with the upgrade link underneath.
     *
     * @param string $control_key Control key the real control would own.
     * @param array  $field       Field configuration.
     * @param string $label       Control label.
     * @return array
     */
    private function build_locked_badge_config($control_key, $field, $label) {
        return [
            'key'        => $control_key . '_jltma_pro_notice',
            'responsive' => !empty($field['responsive']),
            'args'       => [
                'label' => sprintf(
                    /* translators: %s: dynamic user-defined control label. */
                    esc_html__('%s (Pro)', 'master-addons'),
                    $label
                ),
                'type'    => class_exists('\\Elementor\\Controls_Manager') ? \Elementor\Controls_Manager::CHOOSE : 'choose',
                'options' => [
                    '1' => [
                        'title' => '',
                        'icon'  => 'eicon-lock',
                    ],
                ],
                'default'     => '1',
                'description' => Helper::unlock_pro_feature(),
            ],
        ];
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
     * Build control config ARRAY (runtime equivalent of build_control()).
     * Consumed by Dynamic_Widget::register_controls() so no PHP file is generated.
     *
     * @param string $control_key Control key
     * @param array  $field       Field configuration
     * @param string $type        Control type
     * @return array ['key' => string, 'responsive' => bool, 'args' => array]
     */
    public function build_control_config($control_key, $field, $type) {
        // Premium types never reach the fallback: without this the fallback
        // rebuilds them as native Elementor controls, handing free sites a fully
        // editable Typography/Background/... panel for any imported widget.
        if ($this->is_locked_type($type)) {
            return $this->build_locked_control_config($control_key, $field, $type);
        }

        $control = $this->get_control($type);

        if (!$control || !method_exists($control, 'get_config')) {
            return $this->build_fallback_control_config($control_key, $field, $type);
        }

        return $control->get_config($control_key, $field);
    }

    /**
     * Fallback control config for unknown types (array equivalent of build_fallback_control()).
     *
     * @param string $control_key Control key
     * @param array  $field       Field configuration
     * @param string $type        Control type
     * @return array
     */
    private function build_fallback_control_config($control_key, $field, $type) {
        $type_const = strtoupper(preg_replace('/[^A-Za-z0-9_]/', '', (string) $type));
        if ('' === $type_const) {
            $type_const = 'TEXT';
        }

        $const = '\\Elementor\\Controls_Manager::' . $type_const;
        $args  = [
            // translators: dynamic user-defined control label.
            'label' => esc_html(!empty($field['label']) ? $field['label'] : 'Control'),
            'type'  => defined($const) ? constant($const) : 'text',
        ];

        // REPEATER must always carry a 'fields' array.
        if ('REPEATER' === $type_const) {
            $args['fields'] = [];
        }

        if (isset($field['default'])) {
            $default = $this->normalize_fallback_default($field['default'], $args['type']);
            if (null !== $default) {
                $args['default'] = $default;
            }
        }

        return [
            'key'        => $control_key,
            'responsive' => !empty($field['responsive']),
            'args'       => $args,
        ];
    }

    /**
     * Reconcile a stored default with the shape Elementor expects for the control type.
     *
     * Data controls such as MEDIA, ICONS, GALLERY or DIMENSIONS declare an ARRAY
     * default. Elementor merges our value into it (array_merge), so handing it the
     * scalar '' the builder stores for an untouched field raises a TypeError and
     * fatals the editor's controls-config ajax request. Wrap a usable scalar into
     * the array shape when we can infer it, otherwise drop the key and let
     * Elementor fall back to its own default.
     *
     * @param mixed  $default      Stored default value.
     * @param string $control_type Resolved Elementor control type.
     * @return mixed|null Normalized default, or null when it must be omitted.
     */
    private function normalize_fallback_default($default, $control_type) {
        if (is_array($default) || !class_exists('\\Elementor\\Plugin')) {
            return $default;
        }

        $elementor = \Elementor\Plugin::instance();
        if (empty($elementor->controls_manager)) {
            return $default;
        }

        $instance = $elementor->controls_manager->get_control($control_type);
        if (!$instance || !($instance instanceof \Elementor\Base_Data_Control)) {
            return $default;
        }

        $control_default = $instance->get_default_value();
        if (!is_array($control_default)) {
            return $default;
        }

        $value = is_scalar($default) ? (string) $default : '';
        if ('' === $value) {
            return null;
        }

        // Single-value shapes we can safely infer (MEDIA/IMAGE -> url, ICONS -> value).
        foreach (['url', 'value'] as $key) {
            if (array_key_exists($key, $control_default)) {
                return array_merge($control_default, [$key => $value]);
            }
        }

        return null;
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

        // Elementor control constants are upper-case (e.g. REPEATER, SELECT). Normalise
        // the type to a safe constant name so the generated PHP is always valid.
        $type_const = strtoupper(preg_replace('/[^A-Za-z0-9_]/', '', (string) $type));
        if ('' === $type_const) {
            $type_const = 'TEXT';
        }

        $content = "\t\t\$this->add_control(\n";
        $content .= "\t\t\t'{$control_key}',\n";
        $content .= "\t\t\t[\n";
        $content .= "\t\t\t\t'label' => esc_html__('{$label}', 'master-addons'),\n";
        $content .= "\t\t\t\t'type' => Controls_Manager::{$type_const},\n";

        // REPEATER must always carry a 'fields' array, otherwise Elementor's
        // sanitize_settings() throws a TypeError when iterating null fields.
        if ('REPEATER' === $type_const) {
            $content .= "\t\t\t\t'fields' => array(),\n";
        }

        if (isset($field['default'])) {
            $const_name  = '\\Elementor\\Controls_Manager::' . $type_const;
            $native_type = defined($const_name) ? constant($const_name) : 'text';
            $normalized  = $this->normalize_fallback_default($field['default'], $native_type);
            if (null !== $normalized) {
                $default  = $this->export_default_value($normalized);
                $content .= "\t\t\t\t'default' => {$default},\n";
            }
        }

        $content .= "\t\t\t]\n";
        $content .= "\t\t);\n\n";

        return $content;
    }

    /**
     * Convert a control default value into a valid PHP literal for the generated file.
     * Arrays (e.g. repeater/group-control defaults) must never be interpolated directly,
     * which would emit the literal token "Array" and produce a fatal parse error.
     *
     * @param mixed $value
     * @return string
     */
    private function export_default_value($value) {
        if (is_array($value)) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export -- generating PHP source, not debug output
            return var_export($value, true);
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        if (null === $value) {
            return "''";
        }
        return "'" . esc_js((string) $value) . "'";
    }
}
