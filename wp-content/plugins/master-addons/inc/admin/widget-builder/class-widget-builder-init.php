<?php
/**
 * Master Addons Widget Builder Initialization
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */

namespace MasterAddons\Inc\Admin\WidgetBuilder;

if (!defined('ABSPATH')) {
    exit;
}

class Widget_Builder_Init {

    private static $instance = null;

    public static function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'initialize'], 1);
        add_action('admin_init', [$this, 'admin_redirects']);
        add_action('admin_init', [$this, 'maybe_migrate']);
    }

    public function initialize() {
        Widget_CPT::get_instance();
        Widget_Admin::get_instance();

        // Initialize REST API
        add_action('rest_api_init', function() {
            $controller = new REST_Controller();
            $controller->register_routes();
        });

        // Initialize Shortcode Manager
        Shortcode_Manager::get_instance();

        // Register custom widgets with Elementor
        add_action('elementor/widgets/register', [$this, 'register_custom_widgets']);
    }

    /** Handle prefix for the per-instance scripts carrying widget custom JS. */
    const INLINE_JS_HANDLE = 'jltma-widget-builder-inline';

    /**
     * Normalise an includes payload to the canonical shape, dropping entries
     * that could not be enqueued anyway.
     *
     * Every consumer used to re-implement its own subset of these checks, so a
     * library with a relative src was skipped by the Elementor widget but still
     * enqueued by the shortcode. One shape, validated once.
     *
     * @param mixed $includes Raw includes payload.
     * @return array {
     *     @type array $css_libraries List of ['handle' => string, 'src' => string, 'dependencies' => string[]].
     *     @type array $js_libraries  Same shape.
     * }
     */
    public static function normalize_includes($includes) {
        $normalized = ['css_libraries' => [], 'js_libraries' => []];

        if (!is_array($includes)) {
            return $normalized;
        }

        foreach (array_keys($normalized) as $group) {
            if (empty($includes[$group]) || !is_array($includes[$group])) {
                continue;
            }

            foreach ($includes[$group] as $lib) {
                if (!is_array($lib) || empty($lib['handle']) || empty($lib['src'])) {
                    continue;
                }

                // Only absolute URLs: these become wp_enqueue_* sources.
                if (!filter_var($lib['src'], FILTER_VALIDATE_URL)) {
                    continue;
                }

                $deps = (!empty($lib['dependencies']) && is_array($lib['dependencies']))
                    ? array_values(array_map('sanitize_text_field', $lib['dependencies']))
                    : [];

                $normalized[$group][] = [
                    'handle'       => sanitize_text_field($lib['handle']),
                    'src'          => esc_url_raw($lib['src']),
                    'dependencies' => $deps,
                ];
            }
        }

        return $normalized;
    }

    /**
     * The external CSS/JS libraries a widget may load, after the premium gate.
     *
     * Declaring external libraries is a premium capability. The free build
     * resolves to an empty set; the Pro build returns the stored libraries by
     * filtering `master_addons/widget_builder/render_includes` — see
     * MasterAddons\Pro\Classes\Pro_Modules. The stored value is passed as the
     * second argument, already normalised.
     *
     * @param int $widget_id Widget post ID.
     * @return array Normalised includes.
     */
    public static function get_widget_includes($widget_id) {
        $stored = self::normalize_includes(get_post_meta($widget_id, '_jltma_widget_includes', true));

        $includes = apply_filters(
            'master_addons/widget_builder/render_includes',
            self::normalize_includes(null),
            $stored,
            $widget_id
        );

        return self::normalize_includes($includes);
    }

    /**
     * Are we rendering for the Elementor editor (canvas, preview iframe, or an
     * editor ajax round-trip) rather than for a visitor?
     *
     * @return bool
     */
    public static function is_editor_context() {
        if (!class_exists('\Elementor\Plugin')) {
            return is_admin();
        }

        $elementor = \Elementor\Plugin::$instance;

        if (is_admin() || (isset($elementor->editor) && $elementor->editor->is_edit_mode())) {
            return true;
        }

        return isset($elementor->preview) && $elementor->preview->is_preview_mode();
    }

    /**
     * Queue a widget's rendered custom JS for the footer.
     *
     * Widget markup is emitted from inside the `the_content` filter chain, and
     * core's convert_chars() rewrites every bare `&` in that output to `&#038;`
     * without skipping <script> blocks — so an inline <script> holding `a && b`
     * reaches the browser as `a &#038;&#038; b` and dies with a SyntaxError.
     * (Elementor drops wpautop around builder content, but not convert_chars.)
     *
     * Routing the JS through the script queue prints it after wp_footer, well
     * clear of the content filters, and keeps execution after the markup exists.
     *
     * Every rendered instance gets its own handle, so it gets its own <script>
     * tag. That isolation is the point: sharing one handle concatenates every
     * widget on the page into a single block, where one widget throwing — a
     * premium-only library that is not loaded, say — aborts the block and takes
     * every later widget's JS down with it.
     *
     * @param string $js        Rendered JS body (no <script> wrapper).
     * @param int    $widget_id Widget post ID, used to label the handle.
     */
    public static function enqueue_inline_js($js, $widget_id = 0) {
        if (!is_string($js) || '' === trim($js)) {
            return;
        }

        static $instance = 0;
        $instance++;

        $handle = self::INLINE_JS_HANDLE . '-' . absint($widget_id) . '-' . $instance;
        $ver    = defined('JLTMA_VER') ? JLTMA_VER : false;

        wp_register_script($handle, false, [], $ver, true);
        wp_enqueue_script($handle);
        wp_add_inline_script($handle, $js);
    }

    /**
     * Register custom widgets from CPT with Elementor
     */
    public function register_custom_widgets($widgets_manager) {
        // Register custom categories first
        $this->register_custom_categories();

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

        // Widgets are rendered at runtime by Dynamic_Widget — no generated or
        // executed PHP. (No user input is ever written to or required as PHP.)
        require_once __DIR__ . '/class-dynamic-widget.php';

        foreach ($widgets as $widget_post) {
            $widgets_manager->register(new Dynamic_Widget([], ['jltma_post_id' => $widget_post->ID]));
        }
    }

    /**
     * Register custom Elementor categories
     */
    private function register_custom_categories() {
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Get custom categories from options
        $custom_categories = get_option('jltma_custom_widget_categories', []);

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
                    [
                        'title' => $title,
                        'icon' => 'eicon-posts-ticker',
                    ]
                );
            }
        }
    }

    public function admin_redirects() {
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only checks of WordPress-set admin query vars for redirect flow; no form data is processed.
        global $pagenow;
        $target_post_type = 'jltma_widget';
        $redirect_url = admin_url('edit.php?post_type=jltma_widget');

        if ('post.php' === $pagenow && isset($_GET['post'])) {
            if (isset($_GET['action']) && in_array($_GET['action'], ['elementor', 'trash', 'delete', 'restore', 'untrash'], true)) {
                return;
            }
            $post_id = absint($_GET['post']);
            if ($post_id && $target_post_type === get_post_type($post_id)) {
                wp_safe_redirect($redirect_url);
                exit;
            }
        }

        if ('post-new.php' === $pagenow && isset($_GET['post_type'])) {
            $current_post_type = sanitize_key($_GET['post_type']);
            if ($target_post_type === $current_post_type) {
                wp_safe_redirect($redirect_url);
                exit;
            }
        }
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
    }

    /**
     * One-time migration. Re-sanitizes stored widget code (strips any PHP/script that
     * older versions may have persisted), purges stale generated files from current and
     * legacy locations, and regenerates every widget from the now-clean data. Existing
     * widget posts are never deleted — only their data is scrubbed and files rebuilt.
     * Runs once per version (gated by an option) inside an authorised admin request.
     */
    public function maybe_migrate() {
        $option_key = 'jltma_widget_builder_migrated';
        $version    = '3.1.2-runtime';

        if (get_option($option_key) === $version) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        // 1) Remove stale generated trees (current + legacy locations).
        $upload = wp_upload_dir();
        $base   = trailingslashit($upload['basedir']);
        $this->delete_directory($base . 'master_addons/widgets');
        $this->delete_directory($base . 'master-addons/widget-builder/generated');
        $this->delete_directory($base . 'master-addons/widget-builder/tmp');

        // 2) Scrub persisted widget data, then regenerate files from clean data.
        $widget_ids = get_posts([
            'post_type'      => 'jltma_widget',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);

        foreach ($widget_ids as $widget_id) {
            $data = get_post_meta($widget_id, '_jltma_widget_data', true);
            if (is_array($data)) {
                if (isset($data['html_code'])) {
                    $data['html_code'] = $this->scrub_html($data['html_code']);
                }
                if (isset($data['css_code'])) {
                    $data['css_code'] = $this->scrub_css($data['css_code']);
                }
                if (isset($data['js_code'])) {
                    $data['js_code'] = $this->scrub_js($data['js_code']);
                }
                update_post_meta($widget_id, '_jltma_widget_data', $data);
            }

            $generator = new Widget_Generator($widget_id);
            $generator->generate();
        }

        // 3) Drop the orphaned option left by the old option-based builder.
        delete_option('jltma_custom_widgets');

        update_option($option_key, $version);
    }

    /**
     * Strip PHP tags and inline <script> from widget HTML.
     *
     * @param string $code
     * @return string
     */
    private function scrub_html($code) {
        if (!is_string($code) || '' === $code) {
            return '';
        }
        $code = str_replace(chr(0), '', $code);
        $code = preg_replace('/<\?php/i', '', $code);
        $code = str_replace(['<?=', '<?', '?>'], '', $code);
        $code = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $code);
        $code = preg_replace('#</?script\b[^>]*>#i', '', $code);
        return $code;
    }

    /**
     * Strip PHP tags and <style>/<script> tags from widget CSS.
     *
     * @param string $code
     * @return string
     */
    private function scrub_css($code) {
        if (!is_string($code) || '' === $code) {
            return '';
        }
        $code = str_replace(chr(0), '', $code);
        $code = preg_replace('/<\?php/i', '', $code);
        $code = str_replace(['<?=', '<?', '?>'], '', $code);
        $code = preg_replace('#</?style\b[^>]*>#i', '', $code);
        $code = preg_replace('#</?script\b[^>]*>#i', '', $code);
        return $code;
    }

    /**
     * Strip PHP tags and <script> tags from widget JS.
     *
     * @param string $code
     * @return string
     */
    private function scrub_js($code) {
        if (!is_string($code) || '' === $code) {
            return '';
        }
        $code = str_replace(chr(0), '', $code);
        $code = preg_replace('/<\?php/i', '', $code);
        $code = str_replace(['<?=', '<?', '?>'], '', $code);
        $code = preg_replace('#</?script\b[^>]*>#i', '', $code);
        return $code;
    }

    /**
     * Recursively delete a directory via WP_Filesystem.
     *
     * @param string $dir
     */
    private function delete_directory($dir) {
        if (empty($dir) || !is_dir($dir)) {
            return;
        }
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        if (!empty($wp_filesystem)) {
            $wp_filesystem->delete($dir, true);
        }
    }
}
