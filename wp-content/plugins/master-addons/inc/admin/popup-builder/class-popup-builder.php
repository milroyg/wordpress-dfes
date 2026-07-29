<?php
namespace MasterAddons\Inc\Admin\PopupBuilder;

use MasterAddons\Master_Elementor_Addons;

if (!defined('ABSPATH')) {
    exit;
}

class Popup_Builder {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        $this->init_hooks();
        $this->includes();
    }
    
    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu'], 55);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_jltma_popup_save', [$this, 'save_popup']);
        add_action('wp_ajax_jltma_popup_delete', [$this, 'delete_popup']);
        add_action('wp_ajax_jltma_popup_duplicate', [$this, 'duplicate_popup']);
        add_action('wp_ajax_jltma_popup_toggle_status', [$this, 'toggle_popup_status']);
    }
    
    private function includes() {
        // Classes are now autoloaded via the plugin autoloader
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'master-addons-settings',
            __('Popup Builder', 'master-addons'),
            __('Popup Builder', 'master-addons'),
            'manage_options',
            'jltma-popups',
            [$this, 'render_popup_page']
        );
    }
    
    public function render_popup_page() {
        $action = isset($_GET['action']) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : 'list'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display routing, no state change
        
        switch ($action) {
            case 'new':
                $this->render_new_popup_page();
                break;
            case 'edit':
                $this->render_edit_popup_page();
                break;
            case 'templates':
                $this->render_templates_page();
                break;
            default:
                $this->render_list_page();
                break;
        }
    }
    
    private function render_list_page() {
        $list_table = new Popup_List_Table();
        $list_table->prepare_items();
        ?>
        <div class="wrap ma-popup-builder">
            <h1 class="wp-heading-inline"><?php esc_html_e('Popups', 'master-addons'); ?></h1>
            <a href="<?php echo esc_url( admin_url('admin.php?page=jltma-popups&action=new') ); ?>" class="page-title-action">
                <?php esc_html_e('Add New', 'master-addons'); ?>
            </a>
            <a href="<?php echo esc_url( admin_url('admin.php?page=jltma-popups&action=templates') ); ?>" class="page-title-action">
                <?php esc_html_e('Templates', 'master-addons'); ?>
            </a>
            <hr class="wp-header-end">
            
            <form method="post">
                <?php $list_table->display(); ?>
            </form>
        </div>
        <?php
    }
    
    private function render_new_popup_page() {
        ?>
        <div class="wrap ma-popup-builder">
            <h1><?php esc_html_e('Add New Popup', 'master-addons'); ?></h1>
            
            <form id="ma-popup-form" method="post">
                <div class="ma-popup-builder-container">
                    <div class="ma-popup-main-content">
                        <div class="postbox">
                            <h2 class="hndle"><?php esc_html_e('Popup Details', 'master-addons'); ?></h2>
                            <div class="inside">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">
                                            <label for="popup-name"><?php esc_html_e('Popup Name', 'master-addons'); ?></label>
                                        </th>
                                        <td>
                                            <input type="text" id="popup-name" name="popup_name" class="regular-text" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="popup-type"><?php esc_html_e('Popup Type', 'master-addons'); ?></label>
                                        </th>
                                        <td>
                                            <select id="popup-type" name="popup_type">
                                                <option value="notification"><?php esc_html_e('Notification Bar', 'master-addons'); ?></option>
                                                <option value="modal"><?php esc_html_e('Modal', 'master-addons'); ?></option>
                                                <option value="slide-in"><?php esc_html_e('Slide-in', 'master-addons'); ?></option>
                                                <option value="full-screen"><?php esc_html_e('Full Screen', 'master-addons'); ?></option>
                                                <option value="corner"><?php esc_html_e('Corner Popup', 'master-addons'); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="popup-content"><?php esc_html_e('Content', 'master-addons'); ?></label>
                                        </th>
                                        <td>
                                            <?php
                                            $content = '';
                                            $editor_id = 'popup-content';
                                            $settings = array(
                                                'textarea_name' => 'popup_content',
                                                'media_buttons' => true,
                                                'textarea_rows' => 10,
                                            );
                                            wp_editor($content, $editor_id, $settings);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <?php $this->render_trigger_settings(); ?>
                        <?php $this->render_display_conditions(); ?>
                        <?php $this->render_design_settings(); ?>
                    </div>
                    
                    <div class="ma-popup-sidebar">
                        <?php $this->render_sidebar_settings(); ?>
                    </div>
                </div>
                
                <?php wp_nonce_field('jltma_popup_save', 'jltma_popup_nonce'); ?>
                <input type="hidden" name="action" value="jltma_popup_save">
            </form>
        </div>
        <?php
    }
    
    private function render_edit_popup_page() {
        $popup_id = isset($_GET['popup_id']) ? absint( $_GET['popup_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display routing, no state change
        $popup = $this->get_popup($popup_id);
        
        if (!$popup) {
            wp_die( esc_html__('Popup not found', 'master-addons') );
        }
        
        ?>
        <div class="wrap ma-popup-builder">
            <h1><?php esc_html_e('Edit Popup', 'master-addons'); ?></h1>
            
            <form id="ma-popup-form" method="post">
                <div class="ma-popup-builder-container">
                    <div class="ma-popup-main-content">
                        <div class="postbox">
                            <h2 class="hndle"><?php esc_html_e('Popup Details', 'master-addons'); ?></h2>
                            <div class="inside">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">
                                            <label for="popup-name"><?php esc_html_e('Popup Name', 'master-addons'); ?></label>
                                        </th>
                                        <td>
                                            <input type="text" id="popup-name" name="popup_name" class="regular-text"
                                                   value="<?php echo esc_attr($popup->name); ?>" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="popup-type"><?php esc_html_e('Popup Type', 'master-addons'); ?></label>
                                        </th>
                                        <td>
                                            <select id="popup-type" name="popup_type">
                                                <option value="notification" <?php selected($popup->type, 'notification'); ?>><?php esc_html_e('Notification Bar', 'master-addons'); ?></option>
                                                <option value="modal" <?php selected($popup->type, 'modal'); ?>><?php esc_html_e('Modal', 'master-addons'); ?></option>
                                                <option value="slide-in" <?php selected($popup->type, 'slide-in'); ?>><?php esc_html_e('Slide-in', 'master-addons'); ?></option>
                                                <option value="full-screen" <?php selected($popup->type, 'full-screen'); ?>><?php esc_html_e('Full Screen', 'master-addons'); ?></option>
                                                <option value="corner" <?php selected($popup->type, 'corner'); ?>><?php esc_html_e('Corner Popup', 'master-addons'); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="popup-content"><?php esc_html_e('Content', 'master-addons'); ?></label>
                                        </th>
                                        <td>
                                            <?php
                                            $content = $popup->content;
                                            $editor_id = 'popup-content';
                                            $settings = array(
                                                'textarea_name' => 'popup_content',
                                                'media_buttons' => true,
                                                'textarea_rows' => 10,
                                            );
                                            wp_editor($content, $editor_id, $settings);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <?php $this->render_trigger_settings($popup); ?>
                        <?php $this->render_display_conditions($popup); ?>
                        <?php $this->render_design_settings($popup); ?>
                    </div>
                    
                    <div class="ma-popup-sidebar">
                        <?php $this->render_sidebar_settings($popup); ?>
                    </div>
                </div>
                
                <?php wp_nonce_field('jltma_popup_save', 'jltma_popup_nonce'); ?>
                <input type="hidden" name="action" value="jltma_popup_save">
                <input type="hidden" name="popup_id" value="<?php echo absint( $popup_id ); ?>">
            </form>
        </div>
        <?php
    }
    
    private function render_trigger_settings($popup = null) {
        ?>
        <div class="postbox">
            <h2 class="hndle"><?php esc_html_e('Trigger Settings', 'master-addons'); ?></h2>
            <div class="inside">
                <input type="hidden" name="trigger_type" value="load">
                <input type="hidden" name="trigger_delay" value="0">
                <table class="form-table">
                    <tr class="trigger-scroll-row" style="display:none;">
                        <th scope="row">
                            <label for="trigger-scroll-percent"><?php esc_html_e('Scroll Percentage', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="trigger-scroll-percent" name="trigger_scroll_percent" min="0" max="100" value="50">
                        </td>
                    </tr>
                    <tr class="trigger-element-row" style="display:none;">
                        <th scope="row">
                            <label for="trigger-element"><?php esc_html_e('Element Selector', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="trigger-element" name="trigger_element" class="regular-text" placeholder="#element-id or .element-class">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }
    
    private function render_display_conditions($popup = null) {
        ?>
        <div class="postbox">
            <h2 class="hndle"><?php esc_html_e('Display Conditions', 'master-addons'); ?></h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e('Show On', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="radio" name="display_on" value="all" checked>
                                <?php esc_html_e('All Pages', 'master-addons'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="display_on" value="specific">
                                <?php esc_html_e('Specific Pages', 'master-addons'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="display_on" value="exclude">
                                <?php esc_html_e('All Pages Except', 'master-addons'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr class="specific-pages-row" style="display:none;">
                        <th scope="row">
                            <label for="specific-pages"><?php esc_html_e('Select Pages', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <select id="specific-pages" name="specific_pages[]" multiple class="regular-text">
                                <?php
                                $pages = get_pages();
                                foreach ($pages as $page) {
                                    echo '<option value="' . absint( $page->ID ) . '">' . esc_html( $page->post_title ) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e('User Conditions', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="show_logged_in" value="1">
                                <?php esc_html_e('Show to Logged-in Users', 'master-addons'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="show_logged_out" value="1" checked>
                                <?php esc_html_e('Show to Logged-out Users', 'master-addons'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="show_first_time" value="1">
                                <?php esc_html_e('Show to First-time Visitors Only', 'master-addons'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="show-frequency"><?php esc_html_e('Show Frequency', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <select id="show-frequency" name="show_frequency">
                                <option value="always"><?php esc_html_e('Always', 'master-addons'); ?></option>
                                <option value="once_session"><?php esc_html_e('Once Per Session', 'master-addons'); ?></option>
                                <option value="once_day"><?php esc_html_e('Once Per Day', 'master-addons'); ?></option>
                                <option value="once_week"><?php esc_html_e('Once Per Week', 'master-addons'); ?></option>
                                <option value="once"><?php esc_html_e('Once Ever', 'master-addons'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }
    
    private function render_design_settings($popup = null) {
        ?>
        <div class="postbox">
            <h2 class="hndle"><?php esc_html_e('Design Settings', 'master-addons'); ?></h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="popup-position"><?php esc_html_e('Position', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <select id="popup-position" name="popup_position">
                                <option value="center"><?php esc_html_e('Center', 'master-addons'); ?></option>
                                <option value="top-left"><?php esc_html_e('Top Left', 'master-addons'); ?></option>
                                <option value="top-center"><?php esc_html_e('Top Center', 'master-addons'); ?></option>
                                <option value="top-right"><?php esc_html_e('Top Right', 'master-addons'); ?></option>
                                <option value="bottom-left"><?php esc_html_e('Bottom Left', 'master-addons'); ?></option>
                                <option value="bottom-center"><?php esc_html_e('Bottom Center', 'master-addons'); ?></option>
                                <option value="bottom-right"><?php esc_html_e('Bottom Right', 'master-addons'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="popup-width"><?php esc_html_e('Width', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="popup-width" name="popup_width" value="600px" class="small-text">
                            <p class="description"><?php esc_html_e('Enter value with unit (px, %, vw)', 'master-addons'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="popup-animation"><?php esc_html_e('Animation', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <select id="popup-animation" name="popup_animation">
                                <option value="fade"><?php esc_html_e('Fade', 'master-addons'); ?></option>
                                <option value="slide-down"><?php esc_html_e('Slide Down', 'master-addons'); ?></option>
                                <option value="slide-up"><?php esc_html_e('Slide Up', 'master-addons'); ?></option>
                                <option value="slide-left"><?php esc_html_e('Slide Left', 'master-addons'); ?></option>
                                <option value="slide-right"><?php esc_html_e('Slide Right', 'master-addons'); ?></option>
                                <option value="zoom"><?php esc_html_e('Zoom', 'master-addons'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e('Overlay', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="show_overlay" value="1" checked>
                                <?php esc_html_e('Show Overlay', 'master-addons'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="close_on_overlay" value="1" checked>
                                <?php esc_html_e('Close on Overlay Click', 'master-addons'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e('Close Button', 'master-addons'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="show_close_button" value="1" checked>
                                <?php esc_html_e('Show Close Button', 'master-addons'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="close_on_esc" value="1" checked>
                                <?php esc_html_e('Close on ESC Key', 'master-addons'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }
    
    private function render_sidebar_settings($popup = null) {
        ?>
        <div class="postbox">
            <h2 class="hndle"><?php esc_html_e('Publish', 'master-addons'); ?></h2>
            <div class="inside">
                <div class="submitbox">
                    <div id="minor-publishing">
                        <div class="misc-pub-section">
                            <label>
                                <input type="checkbox" name="popup_status" value="active" <?php echo (!$popup || $popup->status == 'active') ? 'checked' : ''; ?>>
                                <?php esc_html_e('Active', 'master-addons'); ?>
                            </label>
                        </div>
                    </div>
                    <div id="major-publishing-actions">
                        <div id="publishing-action">
                            <button type="submit" class="button button-primary button-large">
                                <?php echo $popup ? esc_html__('Update', 'master-addons') : esc_html__('Publish', 'master-addons'); ?>
                            </button>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="postbox">
            <h2 class="hndle"><?php esc_html_e('Advanced Settings', 'master-addons'); ?></h2>
            <div class="inside">
                <p>
                    <label for="popup-priority"><?php esc_html_e('Priority', 'master-addons'); ?></label><br>
                    <input type="number" id="popup-priority" name="popup_priority" value="10" class="small-text">
                    <span class="description"><?php esc_html_e('Higher priority popups show first', 'master-addons'); ?></span>
                </p>
                <p>
                    <label for="popup-class"><?php esc_html_e('Custom CSS Class', 'master-addons'); ?></label><br>
                    <input type="text" id="popup-class" name="popup_class" class="regular-text">
                </p>
                <p>
                    <label for="popup-custom-css"><?php esc_html_e('Custom CSS', 'master-addons'); ?></label><br>
                    <textarea id="popup-custom-css" name="popup_custom_css" rows="5" class="large-text code"></textarea>
                </p>
            </div>
        </div>
        <?php
    }
    
    private function render_templates_page() {
        $templates = new Popup_Templates();
        $templates->render();
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'jltma-popups') === false) {
            return;
        }

        \MasterAddons\Inc\Classes\Assets_Manager::enqueue('popup-builder-admin');

        wp_localize_script('jltma-popup-builder-admin', 'jltma_popup_builder', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jltma_popup_builder'),
            'strings' => [
                'confirm_delete' => __('Are you sure you want to delete this popup?', 'master-addons'),
                'saving' => __('Saving...', 'master-addons'),
                'saved' => __('Saved!', 'master-addons'),
                'error' => __('An error occurred. Please try again.', 'master-addons'),
            ]
        ]);
    }
    
    public function save_popup() {
        if (!check_ajax_referer('jltma_popup_save', 'jltma_popup_nonce', false)) {
            wp_die( esc_html__('Security check failed', 'master-addons') );
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => esc_html__('Invalid access', 'master-addons')]);
        }

        $popup_id = isset( $_POST['popup_id'] ) ? absint( $_POST['popup_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
        $popup_data = [
            'name' => isset( $_POST['popup_name'] ) ? sanitize_text_field( wp_unslash( $_POST['popup_name'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            'type' => isset( $_POST['popup_type'] ) ? sanitize_text_field( wp_unslash( $_POST['popup_type'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            'content' => isset( $_POST['popup_content'] ) ? wp_kses_post( wp_unslash( $_POST['popup_content'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            'trigger_type' => isset( $_POST['trigger_type'] ) ? sanitize_text_field( wp_unslash( $_POST['trigger_type'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            'trigger_delay' => isset( $_POST['trigger_delay'] ) ? absint( $_POST['trigger_delay'] ) : 0, // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            'display_on' => isset( $_POST['display_on'] ) ? sanitize_text_field( wp_unslash( $_POST['display_on'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            'show_frequency' => isset( $_POST['show_frequency'] ) ? sanitize_text_field( wp_unslash( $_POST['show_frequency'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            'popup_position' => isset( $_POST['popup_position'] ) ? sanitize_text_field( wp_unslash( $_POST['popup_position'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            'popup_width' => isset( $_POST['popup_width'] ) ? sanitize_text_field( wp_unslash( $_POST['popup_width'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            'popup_animation' => isset( $_POST['popup_animation'] ) ? sanitize_text_field( wp_unslash( $_POST['popup_animation'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            'status' => isset($_POST['popup_status']) ? 'active' : 'inactive',
            'settings' => [
                'show_overlay' => isset($_POST['show_overlay']),
                'close_on_overlay' => isset($_POST['close_on_overlay']),
                'show_close_button' => isset($_POST['show_close_button']),
                'close_on_esc' => isset($_POST['close_on_esc']),
                'show_logged_in' => isset($_POST['show_logged_in']),
                'show_logged_out' => isset($_POST['show_logged_out']),
                'show_first_time' => isset($_POST['show_first_time']),
                'popup_priority' => isset( $_POST['popup_priority'] ) ? absint( $_POST['popup_priority'] ) : 0, // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
                'popup_class' => isset( $_POST['popup_class'] ) ? sanitize_text_field( wp_unslash( $_POST['popup_class'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
                'popup_custom_css' => isset( $_POST['popup_custom_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['popup_custom_css'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            ]
        ];
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'jltma_popups';
        
        if ($popup_id) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct update required for custom popup table, no WP core API equivalent
            $result = $wpdb->update(
                $table_name,
                [
                    'name' => $popup_data['name'],
                    'type' => $popup_data['type'],
                    'content' => $popup_data['content'],
                    'settings' => json_encode($popup_data['settings']),
                    'status' => $popup_data['status'],
                    'updated_at' => current_time('mysql')
                ],
                ['id' => $popup_id]
            );
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- direct insert required for custom popup table, no WP core API equivalent
            $result = $wpdb->insert(
                $table_name,
                [
                    'name' => $popup_data['name'],
                    'type' => $popup_data['type'],
                    'content' => $popup_data['content'],
                    'settings' => json_encode($popup_data['settings']),
                    'status' => $popup_data['status'],
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ]
            );
            $popup_id = $wpdb->insert_id;
        }
        
        if ($result !== false) {
            wp_send_json_success([
                'message' => __('Popup saved successfully', 'master-addons'),
                'popup_id' => $popup_id
            ]);
        } else {
            wp_send_json_error(['message' => __('Failed to save popup', 'master-addons')]);
        }
    }
    
    public function delete_popup() {
        if (!check_ajax_referer('jltma_popup_builder', 'nonce', false)) {
            wp_die( esc_html__('Security check failed', 'master-addons') );
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => esc_html__('Invalid access', 'master-addons')]);
        }

        $popup_id = isset( $_POST['popup_id'] ) ? absint( $_POST['popup_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above

        global $wpdb;
        $table_name = $wpdb->prefix . 'jltma_popups';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct delete required for custom popup table, no WP core API equivalent
        $result = $wpdb->delete($table_name, ['id' => $popup_id]);
        
        if ($result) {
            wp_send_json_success(['message' => __('Popup deleted successfully', 'master-addons')]);
        } else {
            wp_send_json_error(['message' => __('Failed to delete popup', 'master-addons')]);
        }
    }
    
    public function duplicate_popup() {
        if (!check_ajax_referer('jltma_popup_builder', 'nonce', false)) {
            wp_die( esc_html__('Security check failed', 'master-addons') );
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => esc_html__('Invalid access', 'master-addons')]);
        }

        $popup_id = isset( $_POST['popup_id'] ) ? absint( $_POST['popup_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above

        global $wpdb;
        $table_name = $wpdb->prefix . 'jltma_popups';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- $table_name is a safe prefixed table name, value is prepared with %d
        $popup = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $popup_id));

        if ($popup) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- direct insert required for custom popup table, no WP core API equivalent
            $result = $wpdb->insert(
                $table_name,
                [
                    'name' => $popup->name . ' - Copy',
                    'type' => $popup->type,
                    'content' => $popup->content,
                    'settings' => $popup->settings,
                    'status' => 'inactive',
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ]
            );
            
            if ($result) {
                wp_send_json_success(['message' => __('Popup duplicated successfully', 'master-addons')]);
            }
        }
        
        wp_send_json_error(['message' => __('Failed to duplicate popup', 'master-addons')]);
    }
    
    public function toggle_popup_status() {
        if (!check_ajax_referer('jltma_popup_builder', 'nonce', false)) {
            wp_die( esc_html__('Security check failed', 'master-addons') );
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => esc_html__('Invalid access', 'master-addons')]);
        }

        $popup_id = isset( $_POST['popup_id'] ) ? absint( $_POST['popup_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above

        global $wpdb;
        $table_name = $wpdb->prefix . 'jltma_popups';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- $table_name is a safe prefixed table name, value is prepared with %d
        $popup = $wpdb->get_row($wpdb->prepare("SELECT status FROM $table_name WHERE id = %d", $popup_id));

        if ($popup) {
            $new_status = $popup->status === 'active' ? 'inactive' : 'active';

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- direct update required for custom popup table, no WP core API equivalent
            $result = $wpdb->update(
                $table_name,
                ['status' => $new_status],
                ['id' => $popup_id]
            );
            
            if ($result !== false) {
                wp_send_json_success([
                    'message' => __('Status updated successfully', 'master-addons'),
                    'new_status' => $new_status
                ]);
            }
        }
        
        wp_send_json_error(['message' => __('Failed to update status', 'master-addons')]);
    }
    
    private function get_popup($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'jltma_popups';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- $table_name is a safe prefixed table name, value is prepared with %d
        $popup = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        
        if ($popup && !empty($popup->settings)) {
            $popup->settings = json_decode($popup->settings, true);
        }
        
        return $popup;
    }
    
    public static function create_database_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'jltma_popups';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            type varchar(50) NOT NULL,
            content longtext NOT NULL,
            settings longtext,
            status varchar(20) DEFAULT 'active',
            views int DEFAULT 0,
            conversions int DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}