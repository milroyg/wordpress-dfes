<?php
/**
 * Initialize REST API Controller
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */

namespace MasterAddons\Admin\WidgetBuilder;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register REST API routes
 */
\add_action('rest_api_init', function() {
    $controller = new JLTMA_REST_Controller();
    $controller->register_routes();
});

/**
 * Flush rewrite rules on plugin activation or when routes change
 * This ensures REST API endpoints are properly registered
 */
\add_action('admin_init', function() {
    // Check if we need to flush rewrite rules
    $version_option = 'jltma_rest_api_version';
    $current_version = '1.0.1'; // Increment this when REST routes change

    if (get_option($version_option) !== $current_version) {
        flush_rewrite_rules();
        update_option($version_option, $current_version);
    }
});
