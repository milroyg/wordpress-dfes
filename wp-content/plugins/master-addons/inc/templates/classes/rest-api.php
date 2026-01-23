<?php
/**
 * Template Library REST API
 *
 * Registers REST API endpoints for template library functionality
 * Fixes missing permission_callback warnings for WordPress 5.5+
 *
 * @package MasterAddons
 * @since 1.0.0
 */

namespace MasterAddons\Inc\Templates\Classes;

if (!defined('ABSPATH')) {
    exit;
}

class Master_Addons_Templates_REST_API {

    private static $instance = null;
    private $namespace = 'masteraddons/v2';

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Register all REST API routes
     */
    public function register_routes() {
        // Info endpoint
        register_rest_route($this->namespace, '/info', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_info'),
            'permission_callback' => '__return_true', // Public endpoint
        ));

        // Single template endpoint - requires edit_posts capability for security
        register_rest_route($this->namespace, '/template/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_template'),
            'permission_callback' => array($this, 'check_edit_permission'),
            'args' => array(
                'id' => array(
                    'required' => true,
                    'validate_callback' => function($param) {
                        return is_numeric($param);
                    }
                ),
            ),
        ));

        // Register routes for each template type
        $template_types = array('master_section', 'master_pages', 'master_popups');

        foreach ($template_types as $type) {
            // Templates endpoint
            register_rest_route($this->namespace, "/templates/{$type}", array(
                'methods' => 'GET',
                'callback' => array($this, 'get_templates'),
                'permission_callback' => '__return_true', // Public endpoint
                'args' => array(
                    'type' => array(
                        'default' => $type,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ));

            // Categories endpoint
            register_rest_route($this->namespace, "/categories/{$type}", array(
                'methods' => 'GET',
                'callback' => array($this, 'get_categories'),
                'permission_callback' => '__return_true', // Public endpoint
                'args' => array(
                    'type' => array(
                        'default' => $type,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ));

            // Keywords endpoint
            register_rest_route($this->namespace, "/keywords/{$type}", array(
                'methods' => 'GET',
                'callback' => array($this, 'get_keywords'),
                'permission_callback' => '__return_true', // Public endpoint
                'args' => array(
                    'type' => array(
                        'default' => $type,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ));
        }
    }

    /**
     * Check if user has permission to access templates
     * Prevents IDOR vulnerability by requiring authentication
     *
     * @return bool|\WP_Error
     */
    public function check_edit_permission() {
        if (!is_user_logged_in()) {
            return new \WP_Error(
                'rest_not_logged_in',
                __('You must be logged in to access templates.', 'master-addons'),
                array('status' => 401)
            );
        }

        if (!current_user_can('edit_posts')) {
            return new \WP_Error(
                'rest_forbidden',
                __('You do not have permission to access templates.', 'master-addons'),
                array('status' => 403)
            );
        }

        return true;
    }

    /**
     * Get plugin/API info
     */
    public function get_info($request) {
        return rest_ensure_response(array(
            'success' => true,
            'version' => JLTMA_VER,
            'api_version' => '2.0',
            'plugin_name' => 'Master Addons',
        ));
    }

    /**
     * Get single template by ID
     */
    public function get_template($request) {
        $id = $request->get_param('id');

        // Get template data (you can customize this based on your implementation)
        $template = get_post($id);

        if (!$template || $template->post_type !== 'elementor_library') {
            return new \WP_Error(
                'template_not_found',
                'Template not found',
                array('status' => 404)
            );
        }

        // Security check: Only allow access to published templates
        // Users must be logged in to access draft/private templates
        if ($template->post_status !== 'publish') {
            if (!is_user_logged_in() || !current_user_can('edit_post', $id)) {
                return new \WP_Error(
                    'rest_forbidden',
                    'You do not have permission to access this template.',
                    array('status' => 403)
                );
            }
        }

        return rest_ensure_response(array(
            'success' => true,
            'data' => array(
                'id' => $template->ID,
                'title' => $template->post_title,
                'content' => $template->post_content,
                'type' => get_post_meta($template->ID, '_elementor_template_type', true),
            ),
        ));
    }

    /**
     * Get templates for a specific type
     */
    public function get_templates($request) {
        $type = $request->get_param('type');

        // Use existing template manager if available
        if (function_exists('MasterAddons\Inc\Templates\master_addons_templates')) {
            $manager = \MasterAddons\Inc\Templates\master_addons_templates()->temp_manager;
            if ($manager && method_exists($manager, 'get_source')) {
                $source = $manager->get_source('master-api');
                if ($source) {
                    $templates = $source->get_items($type);
                    return rest_ensure_response(array(
                        'success' => true,
                        'data' => $templates ?: array(),
                    ));
                }
            }
        }

        return rest_ensure_response(array(
            'success' => true,
            'data' => array(),
        ));
    }

    /**
     * Get categories for a specific type
     */
    public function get_categories($request) {
        $type = $request->get_param('type');

        // Use existing template manager if available
        if (function_exists('MasterAddons\Inc\Templates\master_addons_templates')) {
            $manager = \MasterAddons\Inc\Templates\master_addons_templates()->temp_manager;
            if ($manager && method_exists($manager, 'get_source')) {
                $source = $manager->get_source('master-api');
                if ($source && method_exists($source, 'get_categories')) {
                    $categories = $source->get_categories($type);
                    return rest_ensure_response(array(
                        'success' => true,
                        'data' => $categories ?: array(),
                    ));
                }
            }
        }

        return rest_ensure_response(array(
            'success' => true,
            'data' => array(),
        ));
    }

    /**
     * Get keywords for a specific type
     */
    public function get_keywords($request) {
        $type = $request->get_param('type');

        // Use existing template manager if available
        if (function_exists('MasterAddons\Inc\Templates\master_addons_templates')) {
            $manager = \MasterAddons\Inc\Templates\master_addons_templates()->temp_manager;
            if ($manager && method_exists($manager, 'get_source')) {
                $source = $manager->get_source('master-api');
                if ($source && method_exists($source, 'get_keywords')) {
                    $keywords = $source->get_keywords($type);
                    return rest_ensure_response(array(
                        'success' => true,
                        'data' => $keywords ?: array(),
                    ));
                }
            }
        }

        return rest_ensure_response(array(
            'success' => true,
            'data' => array(),
        ));
    }
}

// Initialize the REST API
Master_Addons_Templates_REST_API::get_instance();