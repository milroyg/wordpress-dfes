<?php
/**
 * Master Addons Widget Builder - Shortcode Manager
 * Registers all custom widgets as shortcodes
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */

namespace MasterAddons\Admin\WidgetBuilder;

if (!defined('ABSPATH')) {
    exit;
}

class JLTMA_Shortcode_Manager {

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

        // Load widget file
        $upload = wp_upload_dir();
        $widget_file = $upload['basedir'] . '/master_addons/widgets/' . $widget_id . '/widget.php';

        if (!file_exists($widget_file)) {
            return '';
        }

        // Start output buffering
        ob_start();

        try {
            // Enqueue widget assets
            $this->enqueue_widget_assets($widget_id);

            // Require widget file if not already loaded
            require_once $widget_file;

            $class_name = 'MasterAddons\\Addons\\JLTMA_WB_' . $widget_id;

            if (class_exists($class_name)) {
                // Create widget instance
                $widget = new $class_name();

                // Prepare settings from shortcode attributes
                $settings = $this->prepare_settings($widget_id, $atts);

                // Render widget with settings
                echo '<div class="jltma-widget-shortcode jltma-wb-' . esc_attr($widget_id) . '">';

                // Call the widget's render method with settings
                $widget->render_shortcode($settings);

                echo '</div>';
            }
        } catch (\Exception $e) {
            if (current_user_can('manage_options')) {
                echo '<div class="jltma-shortcode-error">' . esc_html($e->getMessage()) . '</div>';
            }
        }

        return ob_get_clean();
    }

    /**
     * Enqueue widget assets
     *
     * @param int $widget_id Widget ID
     */
    private function enqueue_widget_assets($widget_id) {
        $upload = wp_upload_dir();
        $widget_url = $upload['baseurl'] . '/master_addons/widgets/' . $widget_id;
        $widget_dir = $upload['basedir'] . '/master_addons/widgets/' . $widget_id;

        // Enqueue CSS
        if (file_exists($widget_dir . '/style.css')) {
            wp_enqueue_style(
                'jltma-wb-' . $widget_id . '-style',
                $widget_url . '/style.css',
                [],
                filemtime($widget_dir . '/style.css')
            );
        }

        // Enqueue JS
        if (file_exists($widget_dir . '/script.js')) {
            wp_enqueue_script(
                'jltma-wb-' . $widget_id . '-script',
                $widget_url . '/script.js',
                ['jquery'],
                filemtime($widget_dir . '/script.js'),
                true
            );
        }

        // Enqueue includes from meta
        $includes = get_post_meta($widget_id, '_jltma_widget_includes', true);

        if (!empty($includes) && is_array($includes)) {
            // CSS libraries
            if (!empty($includes['css_libraries']) && is_array($includes['css_libraries'])) {
                foreach ($includes['css_libraries'] as $css) {
                    if (!empty($css['handle']) && !empty($css['src'])) {
                        $deps = !empty($css['dependencies']) && is_array($css['dependencies']) ? $css['dependencies'] : [];
                        wp_enqueue_style($css['handle'], $css['src'], $deps);
                    }
                }
            }

            // JS libraries
            if (!empty($includes['js_libraries']) && is_array($includes['js_libraries'])) {
                foreach ($includes['js_libraries'] as $js) {
                    if (!empty($js['handle']) && !empty($js['src'])) {
                        $deps = !empty($js['dependencies']) && is_array($js['dependencies']) ? $js['dependencies'] : [];
                        wp_enqueue_script($js['handle'], $js['src'], $deps, null, true);
                    }
                }
            }
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
        $sections = get_post_meta($widget_id, '_jltma_widget_sections', true);
        $settings = [];

        if (!empty($sections) && is_array($sections)) {
            foreach ($sections as $section) {
                if (!empty($section['controls']) && is_array($section['controls'])) {
                    foreach ($section['controls'] as $control) {
                        $control_name = isset($control['name']) ? $control['name'] : '';
                        if ($control_name && isset($atts[$control_name])) {
                            $settings[$control_name] = $atts[$control_name];
                        } elseif ($control_name && isset($control['default'])) {
                            $settings[$control_name] = $control['default'];
                        }
                    }
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
