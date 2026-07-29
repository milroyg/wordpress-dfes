<?php
/**
 * Master Addons Popup Builder Elementor Integration
 *
 * @package MasterAddons
 * @subpackage PopupBuilder
 */

namespace MasterAddons\Inc\Admin\PopupBuilder;

use MasterAddons\Inc\Classes\Assets_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Elementor_Integration {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        if (!defined('ELEMENTOR_VERSION')) {
            return;
        }
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Document type registration - MUST be priority 10 or lower
        add_action('elementor/documents/register', [$this, 'register_popup_document_type'], 10, 1);

        // Elementor editor hooks
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'editor_scripts']);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'editor_styles']);

        // Editor customization for popups
        add_action('elementor/editor/init', [$this, 'editor_init']);

        // Override Elementor canvas template for popups
        add_filter('template_include', [$this, 'popup_canvas_template'], 999999);

        // Enqueue preview styles in iframe
        add_action('elementor/preview/enqueue_styles', [$this, 'preview_styles']);

        // Re-activate popup when disable_after date is extended to future
        add_action('elementor/document/after_save', [$this, 'maybe_reactivate_popup'], 10, 2);
    }
    
    public function editor_scripts() {
        if ($this->is_popup_editor()) {
            Assets_Manager::enqueue('popup-builder-elementor');

            wp_localize_script('jltma-popup-builder-elementor', 'jltmaPopupElementor', [
                'popup_id' => get_the_ID(),
                'popup_settings' => $this->get_popup_settings(get_the_ID()),
                'strings' => [
                    'popup_settings' => __('Popup Settings', 'master-addons'),
                    'position' => __('Position', 'master-addons'),
                    'animation' => __('Animation', 'master-addons'),
                ]
            ]);
        }
    }

    public function editor_styles() {
        // CSS is already enqueued by editor_scripts via Assets_Manager
    }

    public function preview_styles() {
        $post_id = get_the_ID();
        if (get_post_type($post_id) === 'jltma_popup') {
            Assets_Manager::enqueue('popup-builder-elementor');
        }
    }
    
    public function register_popup_document_type($documents_manager) {
        $documents_manager->register_document_type('jltma_popup', '\MasterAddons\Inc\Admin\PopupBuilder\Popup_Document');
    }
    
    public function editor_init() {
        if ($this->is_popup_editor()) {
            // Add custom body class for popup editor
            add_filter('admin_body_class', function($classes) {
                return $classes . ' jltma-popup-editor elementor-editor-jltma_popup';
            });
        }
    }
    
    public function popup_canvas_template($template) {
        // Check if viewing a popup post type
        if (!is_singular('jltma_popup')) {
            return $template;
        }

        // Make sure Elementor is active
        if (!did_action('elementor/loaded')) {
            return $template;
        }

        // Load custom template for popup preview
        $editor_template = JLTMA_PATH . 'inc/admin/popup-builder/templates/editor.php';

        if (file_exists($editor_template)) {
            return $editor_template;
        }

        return $template;
    }
    
    /**
     * Re-activate popup when user extends the disable_after date to the future.
     */
    public function maybe_reactivate_popup($document, $data) {
        if ($document->get_name() !== 'jltma_popup') {
            return;
        }

        $popup_id = $document->get_main_id();
        $settings = $data['settings'] ?? [];

        // Check if auto-disable is enabled with a future date
        $auto_disable = $settings['popup_disable_automatic'] ?? '';
        $disable_after = $settings['popup_disable_after'] ?? '';

        if ($auto_disable === 'yes' && !empty($disable_after)) {
            $expiration_date = strtotime($disable_after);
            $current_time = current_time('timestamp');

            // If the date is in the future, re-activate the popup
            if ($expiration_date && $expiration_date > $current_time) {
                update_post_meta($popup_id, '_jltma_popup_activation', 'yes');
            }
        }
    }

    private function is_popup_editor() {
        if (!is_admin() || !isset($_GET['action']) || $_GET['action'] !== 'elementor') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only admin context detection, no data processed
            return false;
        }

        $post_id = isset($_GET['post']) ? intval($_GET['post']) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only admin context detection, no data processed
        if (!$post_id) {
            return false;
        }

        return get_post_type($post_id) === 'jltma_popup';
    }
    
    private function get_popup_settings($popup_id) {
        return [
            'position' => get_post_meta($popup_id, '_jltma_popup_position', true) ?: 'center',
            'animation' => get_post_meta($popup_id, '_jltma_popup_animation', true) ?: 'fade',
            'overlay' => get_post_meta($popup_id, '_jltma_popup_overlay', true) ?: 'yes',
            'close_button' => get_post_meta($popup_id, '_jltma_popup_close_button', true) ?: 'yes',
        ];
    }
}