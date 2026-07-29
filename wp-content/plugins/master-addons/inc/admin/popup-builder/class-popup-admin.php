<?php
/**
 * Master Addons Popup Admin Page
 * Similar to Theme Builder but for Popups
 *
 * @package MasterAddons
 * @subpackage PopupBuilder
 */

namespace MasterAddons\Inc\Admin\PopupBuilder;

use MasterAddons\Inc\Classes\Assets_Manager;
use MasterAddons\Inc\Classes\Template_Library_Cache;
use MasterAddons\Inc\Admin\Templates;

if (!defined('ABSPATH')) {
    exit;
}

class Popup_Admin {

    private static $instance = null;
    private $popup_cpt;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->popup_cpt = Popup_CPT::get_instance();
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu'], 55);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_footer', [$this, 'render_popup_modals_in_footer']);
        add_action('wp_ajax_jltma_popup_get_data', [$this, 'get_popup_data']);
        add_action('wp_ajax_jltma_popup_save_data', [$this, 'save_popup_data']);
        add_action('wp_ajax_jltma_popup_delete', [$this, 'delete_popup']);
        add_action('wp_ajax_jltma_popup_get_templates', [$this, 'get_popup_templates']);
        add_action('wp_ajax_jltma_popup_import_template', [$this, 'import_popup_template']);
        add_filter('submenu_file', [$this, 'highlight_popup_menu'], 10, 2);
    }

    public function add_admin_menu() {
        add_submenu_page(
            'master-addons-settings',
            __('Popup Builder', 'master-addons'),
            __('Popup Builder', 'master-addons'),
            'manage_options',
            'edit.php?post_type=jltma_popup'
        );
    }

    public function highlight_popup_menu($submenu_file, $parent_file) {
        global $current_screen;

        if ($current_screen && $current_screen->post_type === $this->popup_cpt->get_post_type()) {
            $submenu_file = 'edit.php?post_type=jltma_popup';
        }

        return $submenu_file;
    }

    /**
     * Render popup modals in footer (similar to Theme Builder)
     */
    public function render_popup_modals_in_footer() {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'edit-jltma_popup') {
            $this->render_popup_modal();
            $this->render_templates_modal();
        }
    }

    private function render_popup_modal() {
        ?>
        <!-- Popup Settings Modal - Similar to Theme Builder -->
        <div id="jltma_popup_builder_modal" class="jltma_popup_builder_modal jltma-modal">
            <div class="jltma-modal-backdrop"></div>
            <div class="jltma-modal-dialog">
                <div class="jltma-modal-content">
                    <?php include JLTMA_PATH . 'inc/admin/popup-builder/view/popup-modal-header.php'; ?>
                    <?php include JLTMA_PATH . 'inc/admin/popup-builder/view/popup-modal-body.php'; ?>
                </div>
            </div>
        </div>
        <?php
    }

    public function enqueue_admin_assets($hook) {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'edit-jltma_popup') {
            return;
        }

        // Enqueue popup builder assets via Assets Manager
        Assets_Manager::enqueue('popup-builder-admin');

        // Condition type options (Pro adds "Exclude")
        $condition_type_options = apply_filters('master_addons/popup_builder/condition_type_options', [
            ['value' => 'include', 'label' => __('Include', 'master-addons')],
            ['value' => 'exclude', 'label' => __('Exclude (Pro)', 'master-addons'), 'pro' => true],
        ]);

        // Condition rule options (Pro unlocks all beyond "Entire Site")
        $condition_rule_options = apply_filters('master_addons/popup_builder/condition_rule_options', [
            ['value' => 'entire_site', 'label' => __('Entire Site', 'master-addons')],
            ['value' => 'front_page', 'label' => __('Front Page (Pro)', 'master-addons'), 'pro' => true],
            ['value' => 'singular', 'label' => __('Singular (Pro)', 'master-addons'), 'pro' => true],
            ['value' => 'archive', 'label' => __('Archive (Pro)', 'master-addons'), 'pro' => true],
            ['value' => 'search', 'label' => __('Search (Pro)', 'master-addons'), 'pro' => true],
            ['value' => '404', 'label' => __('404 Page (Pro)', 'master-addons'), 'pro' => true],
        ]);

        // Localize script with REST API endpoints
        wp_localize_script('jltma-popup-builder-admin', 'jltmaPopupAdmin', [
            'resturl' => get_rest_url() . 'master-addons/v1/',
            'nonce' => wp_create_nonce('wp_rest'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'admin_url' => admin_url(),
            'plugin_url' => JLTMA_URL,
            'popup_nonce' => wp_create_nonce('jltma_popup_nonce'),
            'woocommerce_active' => class_exists('WooCommerce'),
            'is_premium' => apply_filters('master_addons/is_premium', false),
            'condition_type_options' => $condition_type_options,
            'condition_rule_options' => $condition_rule_options,
            'strings' => [
                'confirm_delete' => __('Are you sure you want to delete this popup?', 'master-addons'),
                'saving' => __('Saving...', 'master-addons'),
                'saved' => __('Popup saved successfully!', 'master-addons'),
                'error' => __('An error occurred. Please try again.', 'master-addons'),
                'popup_name_required' => __('Popup name is required.', 'master-addons'),
                'loading_templates' => __('Loading templates...', 'master-addons'),
                'importing_template' => __('Importing template...', 'master-addons'),
                'template_imported' => __('Template imported successfully!', 'master-addons'),
                'import_failed' => __('Failed to import template. Please try again.', 'master-addons'),
                'all_categories' => __('All', 'master-addons'),
                'use_template' => __('Import', 'master-addons'),
                'preview' => __('Preview', 'master-addons'),
                'no_templates' => __('No templates found.', 'master-addons'),
                'search_placeholder' => __('Search templates...', 'master-addons'),
                'retry' => __('Retry', 'master-addons'),
                'load_error' => __('Failed to load templates. Please check your connection and try again.', 'master-addons'),
            ]
        ]);

        // Global masteraddons variable for consistency
        wp_localize_script('jltma-popup-builder-admin', 'masteraddons', [
            'resturl' => get_rest_url() . 'masteraddons/v2/',
            'rest_nonce' => wp_create_nonce('wp_rest'),
            'ajax_nonce' => wp_create_nonce('jltma_frontend_ajax_nonce'),
            'woocommerce_active' => class_exists('WooCommerce'),
        ]);
    }

    public function get_popup_data() {
        check_ajax_referer('jltma_popup_nonce', '_nonce');

        $popup_id = isset( $_GET['popup_id'] ) ? absint( $_GET['popup_id'] ) : 0;

        if (!$popup_id) {
            wp_send_json_error(['message' => 'Invalid popup ID']);
        }

        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== $this->popup_cpt->get_post_type()) {
            wp_send_json_error(['message' => 'Popup not found']);
        }

        $data = [
            'id' => $popup_id,
            'title' => $popup->post_title,
            'activation' => get_post_meta($popup_id, '_jltma_popup_activation', true) ?: 'no',
            'conditions_data' => get_post_meta($popup_id, '_jltma_popup_conditions_data', true) ?: [],
        ];

        wp_send_json_success($data);
    }

    public function save_popup_data() {
        check_ajax_referer('jltma_popup_nonce', '_nonce');

        $popup_id   = isset($_POST['popup_id']) ? intval($_POST['popup_id']) : 0;
        $title      = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
        $activation = isset($_POST['activation']) ? sanitize_text_field(wp_unslash($_POST['activation'])) : '';

        // Handle conditions data
        $conditions_data = $this->parse_conditions_from_post();

        if ($popup_id) {
            // Update existing popup
            wp_update_post([
                'ID' => $popup_id,
                'post_title' => $title,
            ]);
        } else {
            // Create new popup
            $popup_id = wp_insert_post([
                'post_title' => $title,
                'post_type' => $this->popup_cpt->get_post_type(),
                'post_status' => 'publish',
            ]);

            if (is_wp_error($popup_id)) {
                wp_send_json_error(['message' => 'Failed to create popup']);
                return;
            }
        }

        // Free: only allow include + entire_site. Pro hooks this filter to return data as-is.
        $conditions_data = apply_filters('master_addons/popup_builder/sanitize_conditions',
            $this->sanitize_free_conditions($conditions_data)
        );

        // Save meta data
        update_post_meta($popup_id, '_jltma_popup_activation', $activation);
        update_post_meta($popup_id, '_jltma_popup_conditions_data', $conditions_data);

        // Enable Elementor for this post with our custom document type
        if (defined('ELEMENTOR_VERSION')) {
            update_post_meta($popup_id, '_elementor_edit_mode', 'builder');
            update_post_meta($popup_id, '_elementor_template_type', 'jltma_popup');
            update_post_meta($popup_id, '_wp_page_template', 'elementor_canvas');
        }

        // Get proper Elementor edit URL
        $edit_url = get_edit_post_link($popup_id);
        if (defined('ELEMENTOR_VERSION')) {
            try {
                $document = \Elementor\Plugin::$instance->documents->get($popup_id);
                if ($document) {
                    $edit_url = $document->get_edit_url();
                } else {
                    // Fallback to standard Elementor URL format
                    $edit_url = admin_url('post.php?post=' . $popup_id . '&action=elementor');
                }
            } catch (\Exception $e) {
                // Fallback to standard Elementor URL format
                $edit_url = admin_url('post.php?post=' . $popup_id . '&action=elementor');
            }
        }

        $response_data = [
            'id' => $popup_id,
            'title' => $title,
            'activation' => $activation,
            'edit_url' => $edit_url,
        ];

        wp_send_json_success($response_data);
    }

    public function delete_popup() {
        check_ajax_referer('jltma_popup_nonce', '_nonce');

        $popup_id = isset( $_POST['popup_id'] ) ? absint( $_POST['popup_id'] ) : 0;

        if (!$popup_id) {
            wp_send_json_error(['message' => 'Invalid popup ID']);
        }

        $result = wp_delete_post($popup_id, true);

        if ($result) {
            wp_send_json_success(['message' => 'Popup deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete popup']);
        }
    }

    private function render_templates_modal() {
        ?>
        <!-- Popup Templates Modal -->
        <div id="jltma_popup_templates_modal" class="jltma_popup_builder_modal jltma-modal">
            <div class="jltma-modal-backdrop"></div>
            <div class="jltma-modal-dialog">
                <div class="jltma-modal-content">
                    <div class="jltma-pop-contents-head">
                        <div class="jltma-header-top">
                            <div class="jltma-popup-head-content">
                                <span>
                                    <img src="<?php echo esc_url( JLTMA_IMAGE_DIR . 'logo.svg' ); ?>">
                                </span>
                                <h3><?php echo esc_html__('Popup Templates', 'master-addons'); ?></h3>
                            </div>
                            <div class="jltma-templates-search">
                                <input type="text" id="jltma-popup-template-search" placeholder="<?php echo esc_attr__('Search templates...', 'master-addons'); ?>">
                                <span class="dashicons dashicons-search"></span>
                            </div>
                            <div class="jltma-pop-close">
                                <button class="close-btn" data-dismiss="modal"><span class="dashicons dashicons-no-alt"></span></button>
                            </div>
                        </div>
                        <div class="jltma-templates-categories" id="jltma-popup-template-categories"></div>
                    </div>

                    <div class="jltma-pop-contents-body">
                        <div class="jltma-templates-container">
                            <!-- Loading state -->
                            <div class="jltma-templates-loading" id="jltma-popup-templates-loading">
                                <div class="jltma-loading-logo">
                                    <img src="<?php echo esc_url(JLTMA_URL . '/assets/images/logo.svg'); ?>" alt="Master Addons" class="ma-el-loading-logo">
                                </div>
                                <p><?php echo esc_html__('Loading Templates...', 'master-addons'); ?></p>
                            </div>

                            <!-- Error state -->
                            <div class="jltma-templates-error" id="jltma-popup-templates-error" style="display:none;">
                                <span class="dashicons dashicons-warning"></span>
                                <p><?php echo esc_html__('Failed to load templates. Please check your connection and try again.', 'master-addons'); ?></p>
                                <button type="button" class="button" id="jltma-popup-templates-retry"><?php echo esc_html__('Retry', 'master-addons'); ?></button>
                            </div>

                            <!-- Empty state -->
                            <div class="jltma-templates-empty" id="jltma-popup-templates-empty" style="display:none;">
                                <span class="dashicons dashicons-layout"></span>
                                <p><?php echo esc_html__('No templates found.', 'master-addons'); ?></p>
                            </div>

                            <!-- Template grid (populated by JS) -->
                            <div class="jltma-templates-grid" id="jltma-popup-templates-grid" style="display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX: Get popup templates from cache/API
     */
    public function get_popup_templates() {
        check_ajax_referer('jltma_popup_nonce', '_nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
            return;
        }

        $cache = Template_Library_Cache::get_instance();
        $templates = $cache->get_cached_templates('master_popups');
        $categories = $cache->get_cached_categories('master_popups');

        if ($templates === false) {
            $templates = [];
        }
        if ($categories === false) {
            $categories = [];
        }

        // Normalize categories from {"slug": "name"} object to [{slug, name}] array
        $normalized_categories = [];
        if (is_array($categories) || is_object($categories)) {
            foreach ($categories as $slug => $name) {
                $normalized_categories[] = [
                    'slug' => $slug,
                    'name' => is_string($name) ? $name : (string) $name,
                ];
            }
        }

        wp_send_json_success([
            'templates' => $templates,
            'categories' => $normalized_categories,
        ]);
    }

    /**
     * AJAX: Import a popup template
     */
    public function import_popup_template() {
        check_ajax_referer('jltma_popup_nonce', '_nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
            return;
        }

        $template_id = intval($_POST['template_id'] ?? 0);
        $title = sanitize_text_field( wp_unslash( $_POST['title'] ?? '' ) );

        if (!$template_id) {
            wp_send_json_error(['message' => 'Invalid template ID']);
            return;
        }

        if (empty($title)) {
            $title = 'Popup #' . $template_id;
        }

        // Fetch template content via the existing API source
        $elementor_content = null;

        if (function_exists('MasterAddons\\Inc\\Admin\\Templates\\master_addons_templates')) {
            $templates_instance = Templates\master_addons_templates();
            if ($templates_instance && isset($templates_instance->temp_manager)) {
                $source = $templates_instance->temp_manager->get_source('master-api');
                if ($source) {
                    $template_data = $source->get_item($template_id, 'master_popups');
                    if ($template_data && !empty($template_data['content'])) {
                        $elementor_content = $template_data['content'];
                    }
                }
            }
        }

        // Fallback: read directly from the cached template file (bulk-cached format uses 'dependencies')
        if (empty($elementor_content) && class_exists('MasterAddons\Inc\Classes\Template_Library_Cache')) {
            $cache_dir = wp_upload_dir()['basedir'] . '/master_addons/templates-library/master_popups/templates/';
            $cache_file = $cache_dir . 'template-' . $template_id . '.json';

            if (file_exists($cache_file)) {
                $cached_raw = json_decode(file_get_contents($cache_file), true);
                if ($cached_raw && !empty($cached_raw['dependencies'])) {
                    $elementor_content = $cached_raw['dependencies'];
                } elseif ($cached_raw && !empty($cached_raw['content'])) {
                    $elementor_content = $cached_raw['content'];
                }
            }
        }

        // Create the popup post
        $popup_id = wp_insert_post([
            'post_title' => $title,
            'post_type' => $this->popup_cpt->get_post_type(),
            'post_status' => 'publish',
        ]);

        if (is_wp_error($popup_id)) {
            wp_send_json_error(['message' => 'Failed to create popup']);
            return;
        }

        // Set Elementor data if template content was fetched
        if (!empty($elementor_content)) {
            if (is_array($elementor_content)) {
                $elementor_content = wp_json_encode($elementor_content);
            }
            update_post_meta($popup_id, '_elementor_data', wp_slash($elementor_content));

            if (defined('ELEMENTOR_VERSION')) {
                update_post_meta($popup_id, '_elementor_version', ELEMENTOR_VERSION);

                // Clear Elementor cache so it picks up the new content
                if (class_exists('\Elementor\Plugin')) {
                    \Elementor\Plugin::$instance->files_manager->clear_cache();
                }
            }
        }

        // Set activation and default conditions
        $activation = sanitize_text_field( wp_unslash( $_POST['activation'] ?? 'no' ) );
        update_post_meta($popup_id, '_jltma_popup_activation', $activation);

        // Parse conditions if provided
        $conditions_data = $this->parse_conditions_from_post();
        if (empty($conditions_data)) {
            $conditions_data = [['type' => 'include', 'rule' => 'entire_site', 'specific' => '', 'posts' => []]];
        }
        update_post_meta($popup_id, '_jltma_popup_conditions_data', $conditions_data);

        // Enable Elementor for this post
        if (defined('ELEMENTOR_VERSION')) {
            update_post_meta($popup_id, '_elementor_edit_mode', 'builder');
            update_post_meta($popup_id, '_elementor_template_type', 'jltma_popup');
            update_post_meta($popup_id, '_wp_page_template', 'elementor_canvas');
        }

        // Get Elementor edit URL
        $edit_url = admin_url('post.php?post=' . $popup_id . '&action=elementor');
        if (defined('ELEMENTOR_VERSION')) {
            try {
                $document = \Elementor\Plugin::$instance->documents->get($popup_id);
                if ($document) {
                    $edit_url = $document->get_edit_url();
                }
            } catch (\Exception $e) {
                // Fallback already set above
            }
        }

        wp_send_json_success([
            'id' => $popup_id,
            'title' => $title,
            'edit_url' => $edit_url,
        ]);
    }

    /**
     * Parse conditions from POST data (reusable helper)
     */
    /**
     * Sanitize conditions for free version — only allow include + entire_site.
     */
    private function sanitize_free_conditions($conditions_data) {
        if (empty($conditions_data) || !is_array($conditions_data)) {
            return [['type' => 'include', 'rule' => 'entire_site']];
        }
        $filtered = array_filter($conditions_data, function ($c) {
            return (isset($c['type']) && $c['type'] === 'include' && isset($c['rule']) && $c['rule'] === 'entire_site');
        });
        return !empty($filtered) ? array_values($filtered) : [['type' => 'include', 'rule' => 'entire_site']];
    }

    private function parse_conditions_from_post() {
        $conditions_data = [];
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in calling method (import_popup_template)
        $condition_types = isset( $_POST['jltma_condition_type'] ) ? map_deep( wp_unslash( $_POST['jltma_condition_type'] ), 'sanitize_text_field' ) : []; // phpcs:ignore WordPress.Security.NonceVerification.Missing
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in calling method (import_popup_template)
        $condition_rules = isset( $_POST['jltma_condition_rule'] ) ? map_deep( wp_unslash( $_POST['jltma_condition_rule'] ), 'sanitize_text_field' ) : []; // phpcs:ignore WordPress.Security.NonceVerification.Missing
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in calling method (import_popup_template)
        $condition_specifics = isset( $_POST['jltma_condition_specific'] ) ? map_deep( wp_unslash( $_POST['jltma_condition_specific'] ), 'sanitize_text_field' ) : []; // phpcs:ignore WordPress.Security.NonceVerification.Missing
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in calling method (import_popup_template)
        $condition_posts = isset( $_POST['jltma_condition_posts'] ) ? map_deep( wp_unslash( $_POST['jltma_condition_posts'] ), 'sanitize_text_field' ) : []; // phpcs:ignore WordPress.Security.NonceVerification.Missing

        foreach ($condition_types as $index => $type) {
            $rule = $condition_rules[$index] ?? '';
            $specific = $condition_specifics[$index] ?? '';
            $posts = $condition_posts[$index] ?? [];

            if (!empty($type) && !empty($rule)) {
                $conditions_data[] = [
                    'type' => sanitize_text_field($type),
                    'rule' => sanitize_text_field($rule),
                    'specific' => sanitize_text_field($specific),
                    'posts' => array_map('intval', (array) $posts),
                ];
            }
        }

        return $conditions_data;
    }
}
