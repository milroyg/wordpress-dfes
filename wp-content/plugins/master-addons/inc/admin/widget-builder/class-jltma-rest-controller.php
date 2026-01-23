<?php
/**
 * Widget Builder REST API Controller
 *
 * Provides REST endpoints for:
 * - GET /assets - Fetches registered WP/Elementor scripts and styles
 * - POST /widgets/move - Moves widget between categories
 * - GET/POST/PUT /widgets - CRUD operations for widgets
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */

namespace MasterAddons\Admin\WidgetBuilder;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

if (!defined('ABSPATH')) {
    exit;
}

class JLTMA_REST_Controller extends WP_REST_Controller {

    /**
     * Namespace
     */
    protected $namespace = 'jltma/v1';

    /**
     * Constructor
     */
    public function __construct() {
        // Load widget generator class
        require_once \JLTMA_PATH . 'inc/admin/widget-builder/class-jltma-widget-generator.php';
    }

    /**
     * Register routes
     */
    public function register_routes() {
        // GET /assets - Get registered WP/Elementor dependencies
        register_rest_route($this->namespace, '/assets', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_assets'],
                'permission_callback' => [$this, 'check_permission'],
            ],
        ]);

        // POST /widgets/move - Move widget between categories
        register_rest_route($this->namespace, '/widgets/move', [
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'move_widget'],
                'permission_callback' => [$this, 'check_permission'],
                'args' => [
                    'id' => [
                        'required' => true,
                        'type' => 'integer',
                        'sanitize_callback' => 'absint',
                    ],
                    'category' => [
                        'required' => true,
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
            ],
        ]);

        // POST /widgets - Create new widget
        register_rest_route($this->namespace, '/widgets', [
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'create_widget'],
                'permission_callback' => [$this, 'check_permission'],
            ],
        ]);

        // GET/PUT/DELETE /widgets/{id} - CRUD operations for single widget
        register_rest_route($this->namespace, '/widgets/(?P<id>\d+)', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_widget'],
                'permission_callback' => [$this, 'check_permission'],
            ],
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'update_widget'],
                'permission_callback' => [$this, 'check_permission'],
            ],
            [
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => [$this, 'delete_widget'],
                'permission_callback' => [$this, 'check_permission'],
            ],
        ]);

        // POST /widgets/{id}/controls - Add control to widget section
        register_rest_route($this->namespace, '/widgets/(?P<id>\d+)/controls', [
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'add_control'],
                'permission_callback' => [$this, 'check_permission'],
            ],
        ]);

        // DELETE /widgets/{id}/controls/{control_id} - Delete control
        register_rest_route($this->namespace, '/widgets/(?P<id>\d+)/controls/(?P<control_id>[a-zA-Z0-9_]+)', [
            [
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => [$this, 'delete_control'],
                'permission_callback' => [$this, 'check_permission'],
            ],
        ]);

        // GET /categories - Get Elementor widget categories
        register_rest_route($this->namespace, '/categories', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_categories'],
                'permission_callback' => [$this, 'check_permission'],
            ],
        ]);

        // POST /categories - Register new Elementor category
        register_rest_route($this->namespace, '/categories', [
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'register_category'],
                'permission_callback' => [$this, 'check_permission'],
                'args' => [
                    'name' => [
                        'required' => true,
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'slug' => [
                        'required' => false,
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_title',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Permission callback
     */
    public function check_permission() {
        return current_user_can('edit_posts');
    }

    /**
     * GET /assets
     * Returns registered WordPress and Elementor scripts/styles
     */
    public function get_assets(WP_REST_Request $request) {
        global $wp_scripts, $wp_styles;

        $response = [
            'wp_scripts' => [],
            'wp_styles' => [],
            'elementor' => defined('ELEMENTOR_VERSION'),
            'elementor_scripts' => [],
            'elementor_styles' => [],
        ];

        // WordPress scripts
        if (!empty($wp_scripts->registered)) {
            foreach ($wp_scripts->registered as $handle => $script) {
                $response['wp_scripts'][] = [
                    'handle' => $handle,
                    'src' => $script->src,
                    'deps' => $script->deps,
                    'version' => $script->ver,
                ];
            }
        }

        // WordPress styles
        if (!empty($wp_styles->registered)) {
            foreach ($wp_styles->registered as $handle => $style) {
                $response['wp_styles'][] = [
                    'handle' => $handle,
                    'src' => $style->src,
                    'deps' => $style->deps,
                    'version' => $style->ver,
                ];
            }
        }

        // Elementor dependencies (if available)
        if (defined('ELEMENTOR_VERSION')) {
            $elementor_scripts = apply_filters('jltma_elementor_scripts', [
                'elementor-frontend',
                'elementor-waypoints',
                'swiper',
                'elementor-dialog',
            ]);

            foreach ($elementor_scripts as $handle) {
                if (isset($wp_scripts->registered[$handle])) {
                    $script = $wp_scripts->registered[$handle];
                    $response['elementor_scripts'][] = [
                        'handle' => $handle,
                        'src' => $script->src,
                        'deps' => $script->deps,
                        'version' => $script->ver,
                    ];
                }
            }
        }

        return new WP_REST_Response($response, 200);
    }

    /**
     * POST /widgets/move
     * Move widget to different category
     */
    public function move_widget(WP_REST_Request $request) {
        $widget_id = $request->get_param('id');
        $category = $request->get_param('category');

        // Verify widget exists
        $widget = get_post($widget_id);
        if (!$widget || $widget->post_type !== 'jltma_widget') {
            return new WP_Error('widget_not_found', 'Widget not found', ['status' => 404]);
        }

        // Update category meta
        $updated = update_post_meta($widget_id, '_jltma_widget_category', $category);

        if ($updated !== false) {
            return new WP_REST_Response([
                'success' => true,
                'message' => 'Widget category updated successfully',
                'data' => [
                    'id' => $widget_id,
                    'category' => $category,
                ],
            ], 200);
        }

        return new WP_Error('update_failed', 'Failed to update widget category', ['status' => 500]);
    }

    /**
     * GET /widgets/{id}
     * Get single widget data
     */
    public function get_widget(WP_REST_Request $request) {
        $widget_id = $request->get_param('id');

        $widget = get_post($widget_id);
        if (!$widget || $widget->post_type !== 'jltma_widget') {
            return new WP_Error('widget_not_found', 'Widget not found', ['status' => 404]);
        }

        // Get the unified widget data that includes HTML/CSS/JS code
        $unified_data = get_post_meta($widget_id, '_jltma_widget_data', true);

        // Get includes data and ensure proper structure
        $includes = get_post_meta($widget_id, '_jltma_widget_includes', true);
        if (empty($includes) || !is_array($includes)) {
            $includes = array(
                'css_libraries' => array(),
                'js_libraries' => array()
            );
        }

        // Get dependencies data and ensure proper structure
        $dependencies = get_post_meta($widget_id, '_jltma_widget_dependencies', true);
        if (empty($dependencies) || !is_array($dependencies)) {
            $dependencies = array(
                'wp' => array(),
                'elementor' => array()
            );
        }

        $widget_data = [
            'id' => $widget_id,
            'title' => $widget->post_title,
            'category' => get_post_meta($widget_id, '_jltma_widget_category', true) ?: 'general',
            'sections' => get_post_meta($widget_id, '_jltma_widget_sections', true) ?: [],
            'includes' => $includes,
            'dependencies' => $dependencies,
            // Include HTML/CSS/JS code from unified widget data
            'html_code' => isset($unified_data['html_code']) ? $unified_data['html_code'] : '',
            'css_code' => isset($unified_data['css_code']) ? $unified_data['css_code'] : '',
            'js_code' => isset($unified_data['js_code']) ? $unified_data['js_code'] : '',
            'icon' => isset($unified_data['icon']) ? $unified_data['icon'] : 'eicon-code',
        ];

        return new WP_REST_Response($widget_data, 200);
    }

    /**
     * POST /widgets
     * Create new widget
     */
    public function create_widget(WP_REST_Request $request) {
        $data = $request->get_json_params();

        $widget_id = wp_insert_post([
            'post_title' => sanitize_text_field($data['title'] ?? 'New Widget'),
            'post_type' => 'jltma_widget',
            'post_status' => 'publish',
            'post_content' => '',
        ]);

        if (is_wp_error($widget_id)) {
            return new WP_Error('create_failed', 'Failed to create widget', ['status' => 500]);
        }

        // Save meta data
        $this->save_widget_meta($widget_id, $data);

        // Generate widget files
        $this->generate_widget_files($widget_id);

        return new WP_REST_Response([
            'id' => $widget_id,
            'title' => get_the_title($widget_id),
            'message' => 'Widget created successfully',
            'edit_url' => admin_url('admin.php?page=jltma-widget-builder&widget_id=' . $widget_id),
        ], 201);
    }

    /**
     * PUT /widgets/{id}
     * Update widget
     */
    public function update_widget(WP_REST_Request $request) {
        $widget_id = $request->get_param('id');
        $data = $request->get_json_params();

        $widget = get_post($widget_id);

        if (!$widget || $widget->post_type !== 'jltma_widget') {
            $error_details = [
                'widget_id' => $widget_id,
                'widget_exists' => !empty($widget),
                'widget_post_type' => $widget ? $widget->post_type : null,
            ];

            return new WP_Error('widget_not_found', 'Widget not found', ['status' => 404, 'details' => $error_details]);
        }

        // Update post
        wp_update_post([
            'ID' => $widget_id,
            'post_title' => sanitize_text_field($data['title'] ?? $widget->post_title),
        ]);

        // Save meta data
        $this->save_widget_meta($widget_id, $data);

        // Generate widget files
        $this->generate_widget_files($widget_id);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Widget updated successfully',
            'data' => [
                'id' => $widget_id,
            ],
        ], 200);
    }

    /**
     * DELETE /widgets/{id}
     * Delete widget
     */
    public function delete_widget(WP_REST_Request $request) {
        $widget_id = $request->get_param('id');

        $widget = get_post($widget_id);
        if (!$widget || $widget->post_type !== 'jltma_widget') {
            return new WP_Error('widget_not_found', 'Widget not found', ['status' => 404]);
        }

        $deleted = wp_delete_post($widget_id, true);

        if (!$deleted) {
            return new WP_Error('delete_failed', 'Failed to delete widget', ['status' => 500]);
        }

        // Delete generated widget files
        JLTMA_Widget_Generator::delete_widget_files($widget_id);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Widget deleted successfully',
        ], 200);
    }

    /**
     * POST /widgets/{id}/controls
     * Add control to widget section
     */
    public function add_control(WP_REST_Request $request) {
        $widget_id = $request->get_param('id');
        $data = $request->get_json_params();

        $sections = get_post_meta($widget_id, '_jltma_widget_sections', true) ?: [];

        // Find section and add control
        $section_id = $data['section_id'] ?? null;
        foreach ($sections as &$section) {
            if ($section['id'] === $section_id) {
                $section['controls'][] = $data['control'];
                break;
            }
        }

        update_post_meta($widget_id, '_jltma_widget_sections', $sections);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Control added successfully',
        ], 200);
    }

    /**
     * DELETE /widgets/{id}/controls/{control_id}
     * Delete control from widget
     */
    public function delete_control(WP_REST_Request $request) {
        $widget_id = $request->get_param('id');
        $control_id = $request->get_param('control_id');

        $sections = get_post_meta($widget_id, '_jltma_widget_sections', true) ?: [];

        // Find and remove control
        foreach ($sections as &$section) {
            $section['controls'] = array_filter($section['controls'], function($control) use ($control_id) {
                return $control['id'] !== $control_id;
            });
            $section['controls'] = array_values($section['controls']); // Re-index
        }

        update_post_meta($widget_id, '_jltma_widget_sections', $sections);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Control deleted successfully',
        ], 200);
    }

    /**
     * GET /categories
     * Get all registered Elementor widget categories
     */
    public function get_categories(WP_REST_Request $request) {
        $categories = [];

        // Check if Elementor is active
        if (did_action('elementor/loaded')) {
            $elements_manager = \Elementor\Plugin::$instance->elements_manager;
            $elementor_categories = $elements_manager->get_categories();

            foreach ($elementor_categories as $slug => $category_data) {
                $categories[] = [
                    'slug' => $slug,
                    'title' => isset($category_data['title']) ? $category_data['title'] : $slug,
                    'icon' => isset($category_data['icon']) ? $category_data['icon'] : '',
                ];
            }
        }

        // Add custom categories from options
        $custom_categories = get_option('jltma_custom_widget_categories', []);
        if (!empty($custom_categories) && is_array($custom_categories)) {
            foreach ($custom_categories as $slug => $title) {
                // Check if not already in list
                $exists = false;
                foreach ($categories as $cat) {
                    if ($cat['slug'] === $slug) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $categories[] = [
                        'slug' => $slug,
                        'title' => $title,
                        'icon' => '',
                    ];
                }
            }
        }

        return new WP_REST_Response($categories, 200);
    }

    /**
     * POST /categories
     * Register a new Elementor widget category
     */
    public function register_category(WP_REST_Request $request) {
        $name = $request->get_param('name');
        $slug = $request->get_param('slug');

        // Generate slug from name if not provided
        if (empty($slug)) {
            $slug = sanitize_title($name);
        }

        // Store in custom categories option
        $custom_categories = get_option('jltma_custom_widget_categories', []);
        $custom_categories[$slug] = $name;
        update_option('jltma_custom_widget_categories', $custom_categories);

        // Register with Elementor if active
        if (did_action('elementor/loaded')) {
            $elements_manager = \Elementor\Plugin::$instance->elements_manager;
            $elements_manager->add_category(
                $slug,
                [
                    'title' => $name,
                    'icon' => 'eicon-posts-ticker',
                ]
            );
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Category registered successfully',
            'data' => [
                'slug' => $slug,
                'title' => $name,
            ],
        ], 201);
    }

    /**
     * Helper: Save widget meta data
     */
    private function save_widget_meta($widget_id, $data) {
        

        if (isset($data['category'])) {
            update_post_meta($widget_id, '_jltma_widget_category', sanitize_text_field($data['category']));
        }

        if (isset($data['sections'])) {
            // Debug log sections and controls data in cleaner format

            // Group sections by tab
            $widget_details = [
                'widget_settings' => [
                    'title' => get_the_title($widget_id),
                    'icon' => isset($data['icon']) ? $data['icon'] : 'eicon-code',
                    'category' => isset($data['category']) ? $data['category'] : 'general',
                ],
                'content_tab' => [],
                'style_tab' => [],
                'advanced_tab' => []
            ];

            foreach ($data['sections'] as $section) {
                $tab = isset($section['tab']) ? $section['tab'] : 'content';
                $section_data = [
                    'section_id' => isset($section['id']) ? $section['id'] : '',
                    'section_label' => isset($section['label']) ? $section['label'] : '',
                    'controls' => []
                ];

                if (isset($section['controls']) && is_array($section['controls'])) {
                    foreach ($section['controls'] as $control) {
                        // Only include non-empty values
                        $clean_control = [];
                        foreach ($control as $key => $value) {
                            // Skip empty values and default values
                            if ($value !== '' && $value !== null && $value !== [] &&
                                !($key === 'show_label' && $value == 1) &&
                                !($key === 'label_on' && $value === 'Yes') &&
                                !($key === 'label_off' && $value === 'No') &&
                                !($key === 'return_value' && $value === 'yes') &&
                                !($key === 'separator' && $value === 'default') &&
                                !($key === 'language' && $value === 'html') &&
                                !($key === 'minute_increment' && $value == 1) &&
                                !($key === 'skin' && $value === 'inline') &&
                                !($key === 'prevent_empty' && $value == 1) &&
                                !($key === 'media_types' && is_array($value) && count($value) === 1 && $value[0] === 'image') &&
                                !($key === 'allowed_dimensions' && is_array($value) && count($value) === 4)
                            ) {
                                $clean_control[$key] = $value;
                            }
                        }
                        $section_data['controls'][] = $clean_control;
                    }
                }

                // Add to appropriate tab
                $tab_key = $tab . '_tab';
                $widget_details[$tab_key][] = $section_data;
            }

            update_post_meta($widget_id, '_jltma_widget_sections', $data['sections']);
        }

        if (isset($data['includes'])) {
            update_post_meta($widget_id, '_jltma_widget_includes', $data['includes']);
        }

        if (isset($data['dependencies'])) {
            update_post_meta($widget_id, '_jltma_widget_dependencies', $data['dependencies']);
        }

        // Generate widget name from title
        $widget_name = get_post_meta($widget_id, '_jltma_widget_name', true);
        if (empty($widget_name) && isset($data['title'])) {
            update_post_meta($widget_id, '_jltma_widget_name', sanitize_title($data['title']));
        }

        // Sanitize code data before saving
        $html_code = '';
        $css_code = '';
        $js_code = '';

        // Sanitize HTML code - allow all HTML/PHP for widget development
        if (isset($data['html_code'])) {
            // For admin users with widget building capability, we allow unfiltered HTML
            // This is necessary for Elementor widget development
            if (current_user_can('unfiltered_html')) {
                $html_code = $data['html_code'];
            } else {
                // For other users, use wp_kses_post which allows safe HTML
                $html_code = wp_kses_post($data['html_code']);
            }
        }

        // Sanitize CSS code - allow CSS but strip PHP/JS tags
        if (isset($data['css_code'])) {
            // Remove any potential PHP/script tags from CSS
            $css_code = $this->sanitize_css($data['css_code']);
        }

        // Sanitize JavaScript code - allow JS but validate syntax
        if (isset($data['js_code'])) {
            // For admin users, allow JavaScript for widget development
            if (current_user_can('unfiltered_html')) {
                $js_code = $data['js_code'];
            } else {
                // For other users, strip tags
                $js_code = wp_strip_all_tags($data['js_code']);
            }
        }

        // Also save data in unified format for widget generator
        $widget_data = [
            'title' => get_the_title($widget_id),
            'icon' => isset($data['icon']) ? sanitize_text_field($data['icon']) : 'eicon-code',
            'category' => isset($data['category']) ? sanitize_text_field($data['category']) : 'master-addons',
            'sections' => isset($data['sections']) ? $data['sections'] : [],
            'html_code' => $html_code,
            'css_code' => $css_code,
            'js_code' => $js_code
        ];

        update_post_meta($widget_id, '_jltma_widget_data', $widget_data);
    }

    /**
     * Sanitize CSS code
     *
     * @param string $css
     * @return string
     */
    private function sanitize_css($css) {
        // Remove any PHP tags
        $css = preg_replace('/<\?php.*?\?>/s', '', $css);
        $css = preg_replace('/<\?.*?\?>/s', '', $css);

        // Remove any script tags
        $css = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $css);

        // Remove any JavaScript event handlers
        $css = preg_replace('/on\w+\s*=\s*["\'].*?["\']/i', '', $css);

        return $css;
    }

    /**
     * Generate widget files
     */
    private function generate_widget_files($widget_id) {
        $generator = new JLTMA_Widget_Generator($widget_id);
        $result = $generator->generate();
    }
}
