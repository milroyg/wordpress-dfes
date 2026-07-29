<?php
/**
 * Master Addons Popup Builder Initialization
 *
 * @package MasterAddons
 * @subpackage PopupBuilder
 */

namespace MasterAddons\Inc\Admin\PopupBuilder;

if (!defined('ABSPATH')) {
    exit;
}

class Popup_Builder_Init {

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
    }

    public function initialize() {
        Popup_CPT::get_instance();
        Popup_Admin::get_instance();
        Popup_Shortcode::get_instance();
        Elementor_Integration::get_instance();
        Popup_Frontend::get_instance();
    }

    public function admin_redirects() {
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only checks of WordPress-set admin query vars for redirect flow; no form data is processed.
        global $pagenow;
        $target_post_type = 'jltma_popup';
        $redirect_url = admin_url('edit.php?post_type=jltma_popup');

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
}
