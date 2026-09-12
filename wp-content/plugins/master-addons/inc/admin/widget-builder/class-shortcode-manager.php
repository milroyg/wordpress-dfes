<?php
/**
 * Master Addons Widget Builder - Shortcode Manager
 * Registers all custom widgets as shortcodes
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */

namespace MasterAddons\Inc\Admin\WidgetBuilder;

if (!defined('ABSPATH')) {
    exit;
}

class Shortcode_Manager {

    private static $instance = null;
    private $widgets = [];

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('init', [$this, 'register_shortcodes'], 20);
    }

    /**
     * Register all widget shortcodes
     */
    public function register_shortcodes() {
        // Get all published widgets
        $args = [
            'post_type' => 'jltma_widget',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ];

        $widgets = get_posts($args);

        if (empty($widgets)) {
            return;
        }

        foreach ($widgets as $widget_post) {
            $widget_id = $widget_post->ID;
            $shortcode_tag = 'jltma_widget_' . $widget_id;

            // Register shortcode with closure that captures widget ID
            add_shortcode($shortcode_tag, function($atts, $content = null) use ($widget_id) {
                return $this->render_widget_shortcode($atts, $content, $widget_id);
            });

            // Store widget info
            $this->widgets[$shortcode_tag] = [
                'id' => $widget_id,
                'title' => $widget_post->post_title,
                'tag' => $shortcode_tag
            ];
        }
    }

    /**
     * Render widget shortcode
     *
     * @param array $atts Shortcode attributes
     * @param string $content Shortcode content
     * @param int $widget_id Widget post ID
     * @return string Widget output
     */
    public function render_widget_shortcode($atts, $content = null, $widget_id = 0) {
        if (!$widget_id) {
            return '';
        }

        // Rendered from post meta through the shared template engine, the same
        // way Dynamic_Widget renders in Elementor. The old path required a
        // generated uploads/master_addons/widgets/{id}/widget.php, which
        // Widget_Generator stopped writing when rendering moved to runtime — so
        // every shortcode silently returned an empty string.
        $data = $this->read_widget_meta($widget_id, '_jltma_widget_data');
        $html = isset($data['html_code']) ? (string) $data['html_code'] : '';

        if ('' === trim($html)) {
            return '';
        }

        try {
            $this->enqueue_widget_assets($widget_id);

            $settings = $this->prepare_settings($widget_id, $atts);
            $engine   = new Widget_Template_Engine($this->collect_control_types($widget_id));

            $output = '<div class="jltma-widget-shortcode jltma-wb-' . esc_attr($widget_id) . '">';

            // Custom CSS/JS output, matching Dynamic_Widget::render(): the stored
            // bodies are sanitized on save and placeholder substitution is escaped
            // per output, so only the <style>/<script> wrappers are added here.
            $css = isset($data['css_code']) ? (string) $data['css_code'] : '';
            if ('' !== trim($css)) {
                $css_rendered = $engine->render($css, $settings);
                if ('' !== trim($css_rendered)) {
                    $output .= '<style>' . $css_rendered . '</style>';
                }
            }

            $output .= $engine->render($html, $settings);

            // Custom JS goes to the footer, not into the returned markup: the
            // shortcode output passes through the_content, where convert_chars()
            // would turn every `&` inside a <script> into `&#038;`. In the editor
            // it stays inline so it re-runs when the canvas re-renders — same
            // split as Dynamic_Widget::render().
            $js = isset($data['js_code']) ? (string) $data['js_code'] : '';
            if ('' !== trim($js)) {
                $js_rendered = $engine->render($js, $settings);
                if ('' !== trim($js_rendered)) {
                    if (Widget_Builder_Init::is_editor_context()) {
                        $output .= '<script>' . $js_rendered . '</script>';
                    } else {
                        Widget_Builder_Init::enqueue_inline_js($js_rendered, $widget_id);
                    }
                }
            }

            return $output . '</div>';
        } catch (\Exception $e) {
            if (current_user_can('manage_options')) {
                return '<div class="jltma-shortcode-error">' . esc_html($e->getMessage()) . '</div>';
            }
            return '';
        }
    }

    /**
     * Read widget meta as an array.
     *
     * The REST/admin save paths store arrays; the demo importer stores the same
     * keys JSON-encoded. Accept either.
     *
     * @param int    $widget_id
     * @param string $key
     * @return array
     */
    private function read_widget_meta($widget_id, $key) {
        $value = get_post_meta($widget_id, $key, true);

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && '' !== $value) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Map control name => type, so the engine escapes each value correctly.
     *
     * @param int $widget_id
     * @return array
     */
    private function collect_control_types($widget_id) {
        $sections = $this->read_widget_meta($widget_id, '_jltma_widget_sections');
        $types    = [];

        foreach ($sections as $section) {
            if (!is_array($section)) {
                continue;
            }

            $controls = !empty($section['controls'])
                ? $section['controls']
                : (!empty($section['fields']) ? $section['fields'] : []);

            foreach ((array) $controls as $control) {
                if (empty($control['name'])) {
                    continue;
                }

                $types[$control['name']] = $control['type'] ?? 'text';

                if (!empty($control['popover_fields']) && is_array($control['popover_fields'])) {
                    foreach ($control['popover_fields'] as $pf) {
                        if (!empty($pf['name'])) {
                            $types[$pf['name']] = $pf['type'] ?? 'text';
                        }
                    }
                }
            }
        }

        return $types;
    }

    /**
     * Enqueue the external libraries a widget declares.
     *
     * The widget's own CSS/JS is rendered inline by render_widget_shortcode();
     * nothing is read from disk, since no per-widget assets are generated.
     *
     * @param int $widget_id Widget ID
     */
    private function enqueue_widget_assets($widget_id) {
        // Premium-gated, normalised and URL-validated; empty on free builds.
        $includes = Widget_Builder_Init::get_widget_includes($widget_id);

        foreach ($includes['css_libraries'] as $css) {
            wp_enqueue_style($css['handle'], $css['src'], $css['dependencies'], JLTMA_VER);
        }

        foreach ($includes['js_libraries'] as $js) {
            wp_enqueue_script($js['handle'], $js['src'], $js['dependencies'], JLTMA_VER, true);
        }
    }

    /**
     * Prepare settings from shortcode attributes
     *
     * @param int $widget_id Widget ID
     * @param array $atts Shortcode attributes
     * @return array Settings array
     */
    private function prepare_settings($widget_id, $atts) {
        // Get widget sections/controls from meta
        $sections = $this->read_widget_meta($widget_id, '_jltma_widget_sections');
        $settings = [];
        $atts     = is_array($atts) ? $atts : [];

        foreach ($sections as $section) {
            if (!is_array($section)) {
                continue;
            }

            $controls = !empty($section['controls'])
                ? $section['controls']
                : (!empty($section['fields']) ? $section['fields'] : []);

            foreach ((array) $controls as $control) {
                if (empty($control['name'])) {
                    continue;
                }

                $control_name = $control['name'];

                if (isset($atts[$control_name])) {
                    $settings[$control_name] = $atts[$control_name];
                } elseif (isset($control['default'])) {
                    $settings[$control_name] = $control['default'];
                }
            }
        }

        return $settings;
    }

    /**
     * Get all registered shortcodes
     *
     * @return array
     */
    public function get_registered_shortcodes() {
        return $this->widgets;
    }

    /**
     * Get shortcode tag for widget
     *
     * @param int $widget_id Widget ID
     * @return string Shortcode tag
     */
    public static function get_shortcode_tag($widget_id) {
        return 'jltma_widget_' . $widget_id;
    }
}
