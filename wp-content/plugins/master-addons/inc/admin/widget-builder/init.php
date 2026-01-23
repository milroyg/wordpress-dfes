<?php
/**
 * Widget Builder Initialization
 * Loads and initializes Widget Builder CPT and Admin classes
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */

namespace MasterAddons\Admin\WidgetBuilder;

if (!defined('ABSPATH')) {
    exit;
}

// Load Widget Builder classes
require_once __DIR__ . '/class-jltma-widget-cpt.php';
require_once __DIR__ . '/class-jltma-widget-admin.php';
require_once __DIR__ . '/class-jltma-rest-controller.php';
require_once __DIR__ . '/class-jltma-shortcode-manager.php';
require_once __DIR__ . '/icon-library-helper.php';

// Initialize Widget Builder components
function jltma_init_widget_builder() {
    // Initialize CPT
    JLTMA_Widget_CPT::get_instance();

    // Initialize Admin
    JLTMA_Widget_Admin::get_instance();

    // Initialize REST API
    add_action('rest_api_init', function() {
        $controller = new \MasterAddons\Admin\WidgetBuilder\JLTMA_REST_Controller();
        $controller->register_routes();
    });

    // Initialize Shortcode Manager
    JLTMA_Shortcode_Manager::get_instance();

    // Register custom widgets with Elementor
    add_action('elementor/widgets/register', __NAMESPACE__ . '\\jltma_register_custom_widgets');
}

/**
 * Register custom widgets from CPT with Elementor
 */
function jltma_register_custom_widgets($widgets_manager) {
    // Register custom categories first
    jltma_register_custom_categories();

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

    $upload = wp_upload_dir();
    $widgets_base_dir = $upload['basedir'] . '/master_addons/widgets';

    foreach ($widgets as $widget_post) {
        $widget_id = $widget_post->ID;
        $widget_file = $widgets_base_dir . '/' . $widget_id . '/widget.php';

        if (file_exists($widget_file)) {
            require_once $widget_file;

            $class_name = 'MasterAddons\\Addons\\JLTMA_WB_' . $widget_id;

            if (class_exists($class_name)) {
                $widgets_manager->register(new $class_name());
            }
        }
    }
}

/**
 * Register custom Elementor categories
 */
function jltma_register_custom_categories() {
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

add_action('plugins_loaded', __NAMESPACE__ . '\\jltma_init_widget_builder', 20);
