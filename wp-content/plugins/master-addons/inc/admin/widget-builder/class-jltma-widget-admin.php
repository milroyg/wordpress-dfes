<?php
/**
 * Master Addons Widget Builder Admin Page
 * Similar to Popup Builder Admin
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */

namespace MasterAddons\Admin\WidgetBuilder;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) {
    exit;
}

class JLTMA_Widget_Admin {

    private static $instance = null;
    private $widget_cpt;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->widget_cpt = JLTMA_Widget_CPT::get_instance();
        $this->init_hooks();

        // Load widget generator class
        require_once JLTMA_PATH . '/inc/admin/widget-builder/class-jltma-widget-generator.php';
    }

    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu'], 60);
        add_action('admin_init', [$this, 'handle_bulk_actions']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_footer', [$this, 'render_widget_modals_in_footer']);
        add_action('wp_ajax_jltma_widget_get_data', [$this, 'get_widget_data']);
        add_action('wp_ajax_jltma_widget_save_data', [$this, 'save_widget_data']);
        add_action('wp_ajax_jltma_widget_delete', [$this, 'delete_widget']);
        add_action('wp_ajax_jltma_widget_update_category', [$this, 'update_widget_category']);
        add_action('wp_ajax_jltma_widget_get_conditions', [$this, 'get_widget_conditions']);
        add_action('wp_ajax_jltma_widget_save_conditions', [$this, 'save_widget_conditions']);
        add_action('wp_ajax_jltma_widget_render_preview', [$this, 'render_preview']);
        add_filter('submenu_file', [$this, 'highlight_widget_menu'], 10, 2);
        add_action('admin_print_styles', [$this, 'hide_admin_notices']);
    }

    public function add_admin_menu() {
        $hook = add_submenu_page(
            'master-addons-settings',
            __('Widget Builder', 'master-addons'),
            __('Widget Builder', 'master-addons'),
            'manage_options',
            'jltma-widget-builder',
            [$this, 'render_widget_page']
        );
    }

    public function highlight_widget_menu($submenu_file, $parent_file) {
        global $current_screen;

        if ($current_screen && $current_screen->post_type === $this->widget_cpt->get_post_type()) {
            $submenu_file = 'jltma-widget-builder';
        }

        return $submenu_file;
    }

    public function render_widget_page() {
        $widget_id = isset($_GET['widget_id']) ? intval($_GET['widget_id']) : 0;

        if ($widget_id) {
            // React app will handle the editor view
            ?>
            <div class="wrap jltma-widget-builder-wrap">
                <div id="jltma-widget-builder-app"></div>
            </div>
            <?php
        } else {
            // Traditional list view
            ?>
            <div class="wrap jltma-admin jltma-widget-admin">
                <h1 class="wp-heading-inline"><?php _e('Widget Builder', 'master-addons'); ?></h1>
                <a href="#" class="page-title-action jltma-add-new-widget">
                    <?php _e('Add New Widget', 'master-addons'); ?>
                </a>
                <a href="#" class="page-title-action jltma-import-widget" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; border: none;">
                    <span class="dashicons dashicons-upload" style="vertical-align: middle;"></span>
                    <?php _e('Import Widget', 'master-addons'); ?>
                </a>
                <a href="https://master-addons.com/widget-builder/" target="_blank" class="page-title-action" style="background: #2271b1; color: #ffffff; border: none;">
                    <span class="dashicons dashicons-book-alt" style="vertical-align: middle;"></span>
                    <?php _e('View Tutorial', 'master-addons'); ?>
                </a>
                <hr class="wp-header-end">

                <?php
                // Show success messages
                if (isset($_GET['trashed']) && intval($_GET['trashed']) > 0) {
                    $count = intval($_GET['trashed']);
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php printf(_n('%s widget moved to trash.', '%s widgets moved to trash.', $count, 'master-addons'), number_format_i18n($count)); ?></p>
                    </div>
                    <?php
                }

                if (isset($_GET['restored']) && intval($_GET['restored']) > 0) {
                    $count = intval($_GET['restored']);
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php printf(_n('%s widget restored from trash.', '%s widgets restored from trash.', $count, 'master-addons'), number_format_i18n($count)); ?></p>
                    </div>
                    <?php
                }

                if (isset($_GET['deleted']) && intval($_GET['deleted']) > 0) {
                    $count = intval($_GET['deleted']);
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php printf(_n('%s widget permanently deleted.', '%s widgets permanently deleted.', $count, 'master-addons'), number_format_i18n($count)); ?></p>
                    </div>
                    <?php
                }
                ?>

                <?php $this->render_widget_list(); ?>
            </div>
            <?php
        }
    }

    public function handle_bulk_actions() {
        // Only run on widget builder page
        if (!isset($_GET['page']) || $_GET['page'] !== 'jltma-widget-builder') {
            return;
        }

        // Check if action is set
        $action = isset($_GET['action']) && $_GET['action'] != '-1' ? $_GET['action'] : '';
        if (empty($action)) {
            $action = isset($_GET['action2']) && $_GET['action2'] != '-1' ? $_GET['action2'] : '';
        }

        // Check if we have selected posts
        if (empty($action) || !isset($_GET['post']) || !is_array($_GET['post'])) {
            return;
        }

        // Verify nonce
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'bulk-posts')) {
            wp_die(__('Security check failed. Please try again.', 'master-addons'));
        }

        $widget_ids = array_map('intval', $_GET['post']);
        $count = 0;
        $redirect_args = ['page' => 'jltma-widget-builder'];

        if ($action === 'trash') {
            foreach ($widget_ids as $widget_id) {
                if (wp_trash_post($widget_id)) {
                    $count++;
                }
            }
            $redirect_args['trashed'] = $count;
        } elseif ($action === 'restore') {
            foreach ($widget_ids as $widget_id) {
                if (wp_untrash_post($widget_id)) {
                    $count++;
                }
            }
            $redirect_args['restored'] = $count;
        } elseif ($action === 'delete') {
            foreach ($widget_ids as $widget_id) {
                if (wp_delete_post($widget_id, true)) {
                    $count++;
                }
            }
            $redirect_args['deleted'] = $count;
        }

        if ($count > 0) {
            // Redirect with success message
            $redirect_url = add_query_arg($redirect_args, admin_url('admin.php'));
            wp_redirect($redirect_url);
            exit;
        }
    }

    private function render_widget_list() {
        $widgets = $this->get_all_widgets();
        $total_items = count($widgets);
        $counts = $this->get_widget_counts();
        $current_status = isset($_GET['post_status']) ? sanitize_text_field($_GET['post_status']) : '';
        ?>

        <!-- Status Tabs -->
        <ul class="subsubsub">
            <li class="all">
                <a href="<?php echo admin_url('admin.php?page=jltma-widget-builder'); ?>" <?php echo empty($current_status) ? 'class="current"' : ''; ?>>
                    <?php _e('All', 'master-addons'); ?> <span class="count">(<?php echo $counts['all']; ?>)</span>
                </a> |
            </li>
            <li class="publish">
                <a href="<?php echo admin_url('admin.php?page=jltma-widget-builder&post_status=publish'); ?>" <?php echo $current_status === 'publish' ? 'class="current"' : ''; ?>>
                    <?php _e('Published', 'master-addons'); ?> <span class="count">(<?php echo $counts['publish']; ?>)</span>
                </a>
                <?php if ($counts['trash'] > 0) : ?>
                    |
                </li>
                <li class="trash">
                    <a href="<?php echo admin_url('admin.php?page=jltma-widget-builder&post_status=trash'); ?>" <?php echo $current_status === 'trash' ? 'class="current"' : ''; ?>>
                        <?php _e('Trash', 'master-addons'); ?> <span class="count">(<?php echo $counts['trash']; ?>)</span>
                    </a>
                <?php endif; ?>
            </li>
        </ul>

        <form id="posts-filter" method="get">
            <input type="hidden" name="page" value="jltma-widget-builder" />
            <?php if (!empty($current_status)) : ?>
                <input type="hidden" name="post_status" value="<?php echo esc_attr($current_status); ?>" />
            <?php endif; ?>
            <?php wp_nonce_field('bulk-posts'); ?>

            <!-- Top Table Navigation -->
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e('Select bulk action', 'master-addons'); ?></label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1"><?php _e('Bulk actions', 'master-addons'); ?></option>
                        <?php if ($current_status === 'trash') : ?>
                            <option value="restore"><?php _e('Restore', 'master-addons'); ?></option>
                            <option value="delete"><?php _e('Delete Permanently', 'master-addons'); ?></option>
                        <?php else : ?>
                            <option value="trash"><?php _e('Move to Trash', 'master-addons'); ?></option>
                        <?php endif; ?>
                    </select>
                    <input type="submit" id="doaction" class="button action" value="<?php esc_attr_e('Apply', 'master-addons'); ?>">
                </div>
                <div class="tablenav-pages one-page">
                    <span class="displaying-num"><?php printf(_n('%s item', '%s items', $total_items, 'master-addons'), number_format_i18n($total_items)); ?></span>
                </div>
                <br class="clear">
            </div>

            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="title" class="manage-column column-title column-primary sortable desc" style="width: 40%;">
                            <a href="#"><span><?php _e('Title', 'master-addons'); ?></span></a>
                        </th>
                        <th scope="col" id="jltma_widget_category" class="manage-column column-jltma_widget_category" style="width: 20%; text-align:center;"><?php _e('Widget Category', 'master-addons'); ?></th>
                        <th scope="col" id="author" class="manage-column column-author" style="width: 20%;"><?php _e('Author', 'master-addons'); ?></th>
                        <th scope="col" id="jltma_widget_shortcode" class="manage-column column-jltma_widget_shortcode" style="width: 20%;"><?php _e('Shortcode', 'master-addons'); ?></th>
                    </tr>
                </thead>
                <tbody id="the-list">
                    <?php if (empty($widgets)) : ?>
                        <tr class="no-items">
                            <td class="colspanchange" colspan="5">
                                <?php _e('No widgets found.', 'master-addons'); ?>
                                <a href="#" class="jltma-add-new-widget"><?php _e('Create your first widget', 'master-addons'); ?></a>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($widgets as $widget) : ?>
                            <tr id="post-<?php echo $widget->ID; ?>">
                                <th scope="row" class="check-column">
                                    <input id="cb-select-<?php echo $widget->ID; ?>" type="checkbox" name="post[]" value="<?php echo $widget->ID; ?>">
                                </th>
                                <td class="title column-title has-row-actions column-primary page-title">
                                    <strong>
                                        <a href="<?php echo admin_url('admin.php?page=jltma-widget-builder&widget_id=' . $widget->ID); ?>" class="row-title">
                                            <?php echo esc_html($widget->post_title); ?>
                                        </a>
                                    </strong>
                                    <div class="hidden" id="_inline_<?php echo $widget->ID; ?>">
                                        <div class="post_title"><?php echo esc_attr($widget->post_title); ?></div>
                                    </div>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a href="<?php echo admin_url('admin.php?page=jltma-widget-builder&widget_id=' . $widget->ID); ?>">
                                                <?php _e('Edit', 'master-addons'); ?>
                                            </a> |
                                        </span>
                                        <span class="trash">
                                            <a href="#" class="jltma-delete-widget" data-widget-id="<?php echo $widget->ID; ?>">
                                                <?php _e('Delete', 'master-addons'); ?>
                                            </a>
                                        </span>
                                    </div>
                                    <button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e('Show more details', 'master-addons'); ?></span></button>
                                </td>
                                <td class="jltma_widget_category column-jltma_widget_category" data-colname="<?php _e('Widget Category', 'master-addons'); ?>" style="text-align:center;">
                                    <?php $this->render_category_display($widget->ID); ?>
                                </td>
                                <td class="author column-author" data-colname="<?php _e('Author', 'master-addons'); ?>">
                                    <?php echo esc_html(get_the_author_meta('display_name', $widget->post_author)); ?>
                                </td>
                                <td class="jltma_widget_shortcode column-jltma_widget_shortcode" data-colname="<?php _e('Shortcode', 'master-addons'); ?>">
                                    <?php $this->render_shortcode_display($widget->ID); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-title column-primary sortable desc" style="width: 40%;">
                            <a href="#"><span><?php _e('Title', 'master-addons'); ?></span></a>
                        </th>
                        <th scope="col" class="manage-column column-jltma_widget_category" style="width: 20%; text-align:center;"><?php _e('Widget Category', 'master-addons'); ?></th>
                        <th scope="col" class="manage-column column-author" style="width: 20%;"><?php _e('Author', 'master-addons'); ?></th>
                        <th scope="col" class="manage-column column-jltma_widget_shortcode" style="width: 20%;"><?php _e('Shortcode', 'master-addons'); ?></th>
                    </tr>
                </tfoot>
            </table>

            <!-- Bottom Table Navigation -->
            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text"><?php _e('Select bulk action', 'master-addons'); ?></label>
                    <select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1"><?php _e('Bulk actions', 'master-addons'); ?></option>
                        <?php if ($current_status === 'trash') : ?>
                            <option value="restore"><?php _e('Restore', 'master-addons'); ?></option>
                            <option value="delete"><?php _e('Delete Permanently', 'master-addons'); ?></option>
                        <?php else : ?>
                            <option value="trash"><?php _e('Move to Trash', 'master-addons'); ?></option>
                        <?php endif; ?>
                    </select>
                    <input type="submit" id="doaction2" class="button action" value="<?php esc_attr_e('Apply', 'master-addons'); ?>">
                </div>
                <div class="tablenav-pages one-page">
                    <span class="displaying-num"><?php printf(_n('%s item', '%s items', $total_items, 'master-addons'), number_format_i18n($total_items)); ?></span>
                </div>
                <br class="clear">
            </div>
        </form>
        <?php
    }

    /**
     * Render widget modals in footer
     */
    public function render_widget_modals_in_footer() {
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'jltma-widget-builder') !== false) {
            $this->render_widget_modal();
        }
    }

    private function render_widget_modal() {
        // Get Elementor categories
        $elementor_categories = $this->get_elementor_categories();

        ?>
        <!-- Widget Settings Modal -->
        <div id="jltma_widget_builder_modal" class="jltma_widget_builder_modal jltma-modal">
            <div class="jltma-modal-backdrop"></div>
            <div class="jltma-modal-dialog">
                <div class="jltma-modal-content">
                    <div class="jltma-pop-contents-head">
                        <div class="jltma-header-top">
                            <div class="jltma-popup-head-content" style="display: flex; align-items:center; gap:8px">
                                <span>
                                    <img src="<?php echo JLTMA_IMAGE_DIR . 'logo.svg'; ?>" style="width: 30px;">
                                </span>
                                <h3 class="jltma-modal-title"><?php echo esc_html__('Edit Widget', 'master-addons'); ?></h3>
                            </div>
                            <div class="jltma-pop-close">
                                <button class="close-btn" data-dismiss="modal"><span class="dashicons dashicons-no-alt"></span></button>
                            </div>
                        </div>
                    </div>

                    <div class="jltma-pop-contents-body" id="jltma_widget_modal_body">
                        <div class="jltma-spinner"></div>

                        <div class="jltma-pop-contents-padding">
                            <form id="jltma-widget-form">
                                <input type="hidden" name="widget_id" id="jltma_widget_id" value="">

                                <div class="jltma-modal-content-area">
                                    <div class="jltma-tab-content jltma-tab-general active">
                                        <div class="jltma-label-container">
                                            <div class="jltma-row">

                                                <div class="jltma-form-group mb-2 jltma-col-6">
                                                    <label for="jltma_widget_title" style="font-size: 16px;">
                                                        <strong><?php _e('Widget Title', 'master-addons'); ?> <span style="color: red;">*</span></strong>
                                                    </label>
                                                </div>
                                                <div class="jltma-form-group mb-2 jltma-col-6">
                                                    <input required type="text" name="widget_title" id="jltma_widget_title" class="jltma-form-control" placeholder="<?php echo esc_attr__('Enter a descriptive name for your widget', 'master-addons'); ?>">
                                                </div>

                                                <div class="jltma-form-group mb-2 jltma-col-6">
                                                    <label for="jltma_widget_category" style="font-size: 16px;">
                                                        <strong><?php _e('Widget Category', 'master-addons'); ?></strong>
                                                    </label>
                                                </div>
                                                <div class="jltma-form-group mb-2 jltma-col-6">
                                                    <select name="widget_category" id="jltma_widget_category" class="jltma-form-control">
                                                        <?php if (!empty($elementor_categories)) : ?>
                                                            <?php foreach ($elementor_categories as $category_slug => $category_data) : ?>
                                                                <option value="<?php echo esc_attr($category_slug); ?>">
                                                                    <?php echo esc_html($category_data['title']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else : ?>
                                                            <option value="general"><?php _e('General', 'master-addons'); ?></option>
                                                            <option value="basic"><?php _e('Basic', 'master-addons'); ?></option>
                                                            <option value="pro"><?php _e('Pro', 'master-addons'); ?></option>
                                                        <?php endif; ?>
                                                        <option value="__add_new__"><?php _e('+ Add New Category', 'master-addons'); ?></option>
                                                    </select>
                                                    <p class="description" style="margin-top: 8px; font-size: 12px; color: #6c757d;">
                                                        <?php _e('Select an Elementor category for this widget.', 'master-addons'); ?>
                                                    </p>

                                                    <!-- Inline Add New Category -->
                                                    <div id="jltma-inline-category-add" style="display: none; margin-top: 12px;">
                                                        <input type="text" id="jltma_new_category_title" placeholder="<?php _e('Enter Category name', 'master-addons'); ?>" style="width: 100%; padding: 6px 12px; border: 1px solid #5b6aff; border-radius: 6px; font-size: 14px; outline: none; margin-bottom: 12px; transition: border-color 0.2s ease;">
                                                        <div style="display: flex; gap: 12px; align-items: center; justify-content: flex-start;">
                                                            <button type="button" class="button button-primary jltma-save-inline-category" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 20px; background: var(--jltma-primary-glow); color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s ease;">
                                                                <span class="dashicons dashicons-plus-alt"></span>
                                                                <?php _e('Add Category', 'master-addons'); ?>
                                                            </button>
                                                            <button type="button" class="button jltma-cancel-inline-category" style="background: transparent; color: #ff4757; border: 2px solid #ff4757; padding: 2px 20px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; box-shadow: none; text-shadow: none; white-space: nowrap;"><?php _e('Cancel', 'master-addons'); ?></button>
                                                        </div>
                                                        <p style="margin-top: 12px; font-size: 13px; color: #8a909a; font-style: italic; margin-bottom: 0;">
                                                            <?php _e('Enter a name for the new category and click Add Category.', 'master-addons'); ?>
                                                        </p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="jltma-modal-footer">
                                    <button type="submit" class="jltma-save-widget jltma-save-btn jltma-color-two">
                                        <?php _e('Save Changes', 'master-addons'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Widget Dropzone Modal -->
        <div id="jltma_import_widget_modal" class="jltma-import-modal" style="display: none;">
            <div class="jltma-import-backdrop"></div>
            <div class="jltma-import-dialog">
                <div class="jltma-import-header">
                    <h3><?php _e('Import Widget', 'master-addons'); ?></h3>
                    <button class="jltma-import-close">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                </div>
                <div class="jltma-import-body">
                    <div class="jltma-dropzone" id="jltma-dropzone">
                        <div class="jltma-dropzone-content">
                            <span class="dashicons dashicons-upload" style="font-size: 60px; color: #4c4c4c;"></span>
                            <h4><?php _e('Drag & Drop your JSON file here', 'master-addons'); ?></h4>
                            <p><?php _e('or', 'master-addons'); ?></p>
                            <label class="jltma-browse-btn">
                                <?php _e('Browse Files', 'master-addons'); ?>
                                <input type="file" id="jltma-import-file" accept=".json,application/json" style="display: none;" />
                            </label>
                            <p class="jltma-supported-format"><?php _e('Supported format: .json', 'master-addons'); ?></p>
                        </div>
                        <div class="jltma-importing" style="display: none;">
                            <div class="jltma-spinner-import"></div>
                            <p><?php _e('Importing widget...', 'master-addons'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Import Modal Styles */
            .jltma-import-modal {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 999999;
            }
            .jltma-import-backdrop {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.8);
            }
            .jltma-import-dialog {
                position: relative;
                max-width: 600px;
                margin: 200px auto;
                background: #1e1e1e;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
                z-index: 1;
            }
            .jltma-import-header {
                padding: 24px 32px;
                border-bottom: 1px solid #2c2c2c;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .jltma-import-header h3 {
                margin: 0;
                font-size: 18px;
                font-weight: 600;
                color: #ffffff;
            }
            .jltma-import-close {
                background: transparent;
                border: none;
                color: #a4afb7;
                font-size: 24px;
                cursor: pointer;
                padding: 0;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 4px;
                transition: all 0.2s;
            }
            .jltma-import-close:hover {
                background: #2c2c2c;
                color: #ffffff;
            }
            .jltma-import-body {
                padding: 32px;
            }
            .jltma-dropzone {
                border: 2px dashed #3c3c3c;
                border-radius: 8px;
                padding: 60px 40px;
                text-align: center;
                background: #252525;
                transition: all 0.3s ease;
                cursor: pointer;
            }
            .jltma-dropzone-content span {
                width: max-content;
                height: max-content;
                color: #fff !important;
            }
            .jltma-dropzone.dragging {
                border-color: #667eea;
                background: rgba(102, 126, 234, 0.1);
            }
            .jltma-dropzone.dragging .dashicons-upload {
                color: #667eea !important;
            }
            .jltma-dropzone h4 {
                color: #ffffff;
                font-size: 18px;
                font-weight: 600;
                margin: 20px 0 12px 0;
            }
            .jltma-dropzone p {
                color: #a4afb7;
                font-size: 14px;
                margin: 0 0 24px 0;
            }
            .jltma-browse-btn {
                display: inline-block;
                padding: 12px 32px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #ffffff;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .jltma-browse-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
            }
            .jltma-supported-format {
                color: #6c6c6c !important;
                font-size: 12px !important;
                margin: 24px 0 0 0 !important;
            }
            .jltma-importing {
                text-align: center;
            }
            .jltma-spinner-import {
                width: 60px;
                height: 60px;
                border: 4px solid #2c2c2c;
                border-top: 4px solid #667eea;
                border-radius: 50%;
                margin: 0 auto 20px;
                animation: jltma-spin 1s linear infinite;
            }
            .jltma-importing p {
                color: #a4afb7;
                font-size: 16px;
                margin: 0;
            }
            @keyframes jltma-spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
        <?php
    }

    private function get_all_widgets() {
        $current_status = isset($_GET['post_status']) ? sanitize_text_field($_GET['post_status']) : '';

        $post_status = ['publish', 'draft'];
        if ($current_status === 'trash') {
            $post_status = ['trash'];
        } elseif ($current_status === 'publish') {
            $post_status = ['publish'];
        } elseif ($current_status === 'draft') {
            $post_status = ['draft'];
        }

        $args = [
            'post_type' => $this->widget_cpt->get_post_type(),
            'post_status' => $post_status,
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ];
        return get_posts($args);
    }

    private function get_widget_counts() {
        global $wpdb;
        $post_type = $this->widget_cpt->get_post_type();

        $counts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT post_status, COUNT(*) as count
                FROM {$wpdb->posts}
                WHERE post_type = %s
                GROUP BY post_status",
                $post_type
            ),
            OBJECT_K
        );

        return [
            'all' => array_sum(wp_list_pluck($counts, 'count')),
            'publish' => isset($counts['publish']) ? $counts['publish']->count : 0,
            'draft' => isset($counts['draft']) ? $counts['draft']->count : 0,
            'trash' => isset($counts['trash']) ? $counts['trash']->count : 0,
        ];
    }

    private function render_category_display($widget_id) {
        $category = get_post_meta($widget_id, '_jltma_widget_category', true);

        if (empty($category)) {
            $category = 'general';
        }

        // Get proper category name from Elementor categories
        $elementor_categories = $this->get_elementor_categories();
        $category_name = isset($elementor_categories[$category])
            ? $elementor_categories[$category]['title']
            : ucfirst(str_replace(['_', '-'], ' ', $category));

        echo '<span class="jltma-widget-category">' . esc_html($category_name) . '</span>';
        echo '<br><a href="#" class="jltma-widget-edit-cond" id="' . esc_attr($widget_id) . '" style="display: flex; justify-content: center; align-items: center; gap: 4px; margin-top:4px; font-size: 13px; color: #2271b1; text-decoration: none;">' . esc_html__('Edit Conditions', 'master-addons') . ' <span class="dashicons dashicons-edit" style="font-size: 20px; vertical-align: -webkit-baseline-middle;"></span></a>';
    }

    private function render_shortcode_display($widget_id) {
        // Use the new shortcode format with widget ID
        $shortcode = '[jltma_widget_' . $widget_id . ']';

        echo '<div style="position: relative; display: inline-block; max-width: 280px;">';
        echo '<input type="text" readonly value="' . esc_attr($shortcode) . '" onclick="this.select()" style="width: 100%; font-family: monospace; font-size: 12px; padding: 4px 32px 4px 8px; border: 1px solid #ddd; border-radius: 3px; background: #f9f9f9; box-sizing: border-box;" title="' . esc_attr__('Click to select', 'master-addons') . '">';
        echo '<button type="button" class="jltma-copy-shortcode-widget" data-shortcode="' . esc_attr($shortcode) . '" style="position: absolute; right: -1px; top: 0; padding: 2px 4px; border: 1px solid #dddddd; border-radius: 3px; background: #fff; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="' . esc_attr__('Copy to clipboard', 'master-addons') . '">';
        echo '<span class="dashicons dashicons-clipboard" style="font-size: 14px; width: 14px; height: 14px;"></span>';
        echo '</button>';
        echo '</div>';
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'jltma-widget-builder') === false) {
            return;
        }

        // Enqueue WordPress Media Library
        if (!did_action('wp_enqueue_media')) {
            wp_enqueue_media();
        }

        // Enqueue Icon Library CSS Files
        $icon_library_helper = Icon_Library_Helper::get_instance();
        $icon_library_helper->enqueue_icon_libraries();

        // Enqueue Widget Builder CSS
        wp_enqueue_style(
            'jltma-widget-builder',
            JLTMA_URL . '/assets/css/admin/widget-builder.css',
            ['elementor-icons'],
            JLTMA_VER
        );

        // Enqueue Widget Builder List/Admin JS
        wp_enqueue_script(
            'jltma-widget-admin',
            JLTMA_URL . '/inc/admin/widget-builder/assets/js/widget-admin.js',
            ['jquery'],
            JLTMA_VER . '.' . time(),
            true
        );

        // Enqueue Widget Builder React App
        wp_enqueue_script(
            'jltma-widget-builder-app',
            JLTMA_URL . '/assets/js/admin/widget-builder-app.js?cb=' . time(),
            ['react', 'react-dom', 'wp-element', 'wp-i18n'],
            JLTMA_VER,
            true
        );

        // Enqueue Widget Builder Tooltip Enhancement
        wp_enqueue_script(
            'jltma-widget-builder-tooltip',
            JLTMA_URL . '/assets/js/admin/widget-builder-tooltip.js',
            ['jltma-widget-builder-app'],
            JLTMA_VER,
            true
        );

        // Localize script for list view
        wp_localize_script('jltma-widget-admin', 'jltmaWidgetAdmin', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'admin_url' => admin_url(),
            'rest_url' => rest_url('jltma/v1'),
            'widget_nonce' => wp_create_nonce('jltma_widget_nonce'),
            'rest_nonce' => wp_create_nonce('wp_rest'),
            'strings' => [
                'confirm_delete' => __('Are you sure you want to delete this widget?', 'master-addons'),
                'saving' => __('Saving...', 'master-addons'),
                'saved' => __('Widget saved successfully!', 'master-addons'),
                'error' => __('An error occurred. Please try again.', 'master-addons'),
                'widget_title_required' => __('Widget title is required.', 'master-addons'),
                'category_added' => __('Category added successfully!', 'master-addons'),
                'copied' => __('Copied to clipboard!', 'master-addons'),
            ]
        ]);

        // Localize script for React app
        $widget_id = isset($_GET['widget_id']) ? intval($_GET['widget_id']) : 0;
        wp_localize_script('jltma-widget-builder-app', 'JLTMAWidgetBuilder', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_rest'),
            'widget_id' => $widget_id,
            'rest_url' => rest_url('jltma/v1'),
            'apiBase' => rest_url('jltma/v1'),
            'pluginUrl' => JLTMA_URL,
        ]);

        // Localize Icon Library Configuration
        $icon_library_helper->localize_icon_library();
    }

    /**
     * Hide third-party admin notices on Widget Builder pages
     * Only show Master Addons notices
     */
    public function hide_admin_notices() {
        $screen = get_current_screen();

        // Check if we're on widget builder pages
        if (!$screen || strpos($screen->id, 'jltma-widget-builder') === false) {
            return;
        }

        // Hide all admin notices except Master Addons notices
        ?>
        <style>
            .notice:not(.jltma-notice):not(.master-addons-notice),
            .update-nag:not(.jltma-notice):not(.master-addons-notice),
            .updated:not(.jltma-notice):not(.master-addons-notice),
            .error:not(.jltma-notice):not(.master-addons-notice) {
                display: none !important;
            }
        </style>
        <?php
    }

    public function get_widget_data() {
        check_ajax_referer('jltma_widget_nonce', '_nonce');

        $widget_id = intval($_GET['widget_id']);

        if (!$widget_id) {
            wp_send_json_error(['message' => 'Invalid widget ID']);
        }

        $widget = get_post($widget_id);
        if (!$widget || $widget->post_type !== $this->widget_cpt->get_post_type()) {
            wp_send_json_error(['message' => 'Widget not found']);
        }

        $category = get_post_meta($widget_id, '_jltma_widget_category', true) ?: 'general';

        $data = [
            'id' => $widget_id,
            'title' => $widget->post_title,
            'widget_name' => get_post_meta($widget_id, '_jltma_widget_name', true),
            'category' => $category,
            'widget_data' => get_post_meta($widget_id, '_jltma_widget_data', true) ?: [],
        ];

        wp_send_json_success($data);
    }

    public function save_widget_data() {
        check_ajax_referer('jltma_widget_nonce', '_nonce');

        $widget_id = intval($_POST['widget_id']);
        $title = sanitize_text_field($_POST['widget_title']);
        $category = sanitize_text_field($_POST['widget_category']);

        // Generate widget name from title if creating new
        $widget_name = !$widget_id ? sanitize_title($title) : get_post_meta($widget_id, '_jltma_widget_name', true);
        if (empty($widget_name)) {
            $widget_name = sanitize_title($title);
        }

        if ($widget_id) {
            // Update existing widget
            wp_update_post([
                'ID' => $widget_id,
                'post_title' => $title,
            ]);
        } else {
            // Create new widget
            $widget_id = wp_insert_post([
                'post_title' => $title,
                'post_type' => $this->widget_cpt->get_post_type(),
                'post_status' => 'publish',
                'post_content' => '',
            ]);

            if (is_wp_error($widget_id)) {
                return;
            }

            // Verify widget was created
            $verify_widget = get_post($widget_id);
        }

        // Save meta data
        update_post_meta($widget_id, '_jltma_widget_name', $widget_name);
        update_post_meta($widget_id, '_jltma_widget_category', $category);

        // Clear post cache to ensure REST API can fetch it immediately
        clean_post_cache($widget_id);

        // Generate widget files
        $this->generate_widget_files($widget_id);

        $edit_url = admin_url('admin.php?page=jltma-widget-builder&widget_id=' . $widget_id);

        $response_data = [
            'id' => $widget_id,
            'title' => $title,
            'widget_name' => $widget_name,
            'category' => $category,
            'edit_url' => $edit_url,
        ];

        wp_send_json_success($response_data);
    }

    /**
     * Generate widget files using the generator
     *
     * @param int $widget_id
     */
    private function generate_widget_files($widget_id) {
        $generator = new JLTMA_Widget_Generator($widget_id);
        $result = $generator->generate();
    }

    public function delete_widget() {
        check_ajax_referer('jltma_widget_nonce', '_nonce');

        $widget_id = intval($_POST['widget_id']);

        if (!$widget_id) {
            wp_send_json_error(['message' => 'Invalid widget ID']);
        }

        // Delete widget files first
        JLTMA_Widget_Generator::delete_widget_files($widget_id);

        $result = wp_delete_post($widget_id, true);

        if ($result) {
            wp_send_json_success(['message' => 'Widget deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete widget']);
        }
    }

    public function update_widget_category() {
        check_ajax_referer('jltma_widget_nonce', '_nonce');

        $widget_id = intval($_POST['widget_id']);
        $category = sanitize_text_field($_POST['category']);

        if (!$widget_id) {
            wp_send_json_error(['message' => 'Invalid widget ID']);
        }

        update_post_meta($widget_id, '_jltma_widget_category', $category);

        wp_send_json_success(['message' => 'Category updated successfully']);
    }

    /**
     * Get all registered Elementor categories
     */
    private function get_elementor_categories() {
        if (!defined('ELEMENTOR_VERSION')) {
            return [];
        }

        $categories = [];

        try {
            $elementor_categories = \Elementor\Plugin::$instance->elements_manager->get_categories();

            foreach ($elementor_categories as $category_slug => $category_data) {
                $categories[$category_slug] = [
                    'title' => $category_data['title'],
                    'icon' => $category_data['icon'] ?? '',
                ];
            }
        } catch (\Exception $e) {
            // Fallback categories if Elementor is not available
            $categories = [
                'general' => ['title' => 'General', 'icon' => ''],
                'basic' => ['title' => 'Basic', 'icon' => ''],
                'pro' => ['title' => 'Pro', 'icon' => ''],
            ];
        }

        // Add custom categories from options (same as REST controller)
        $custom_categories = get_option('jltma_custom_widget_categories', []);
        if (!empty($custom_categories) && is_array($custom_categories)) {
            foreach ($custom_categories as $slug => $title) {
                // Check if not already in list (Elementor categories take precedence)
                if (!isset($categories[$slug])) {
                    $categories[$slug] = [
                        'title' => $title,
                        'icon' => '',
                    ];
                }
            }
        }

        return $categories;
    }

    /**
     * Get widget conditions via AJAX
     */
    public function get_widget_conditions() {
        check_ajax_referer('jltma_widget_nonce', '_nonce');

        $widget_id = intval($_GET['widget_id']);

        if (!$widget_id) {
            wp_send_json_error(['message' => 'Invalid widget ID']);
        }

        $conditions = get_post_meta($widget_id, '_jltma_widget_conditions', true);

        if (empty($conditions)) {
            $conditions = [
                'enabled' => false,
                'user_roles' => [],
                'device' => '',
                'page_type' => [],
                'date_start' => '',
                'date_end' => ''
            ];
        }

        wp_send_json_success(['conditions' => $conditions]);
    }

    /**
     * Save widget conditions via AJAX
     */
    public function save_widget_conditions() {
        check_ajax_referer('jltma_widget_nonce', '_nonce');

        $widget_id = intval($_POST['widget_id']);

        if (!$widget_id) {
            wp_send_json_error(['message' => 'Invalid widget ID']);
        }

        $conditions = [
            'enabled' => !empty($_POST['enabled']),
            'user_roles' => isset($_POST['user_roles']) ? array_map('sanitize_text_field', (array) $_POST['user_roles']) : [],
            'device' => sanitize_text_field($_POST['device'] ?? ''),
            'page_type' => isset($_POST['page_type']) ? array_map('sanitize_text_field', (array) $_POST['page_type']) : [],
            'date_start' => sanitize_text_field($_POST['date_start'] ?? ''),
            'date_end' => sanitize_text_field($_POST['date_end'] ?? '')
        ];

        update_post_meta($widget_id, '_jltma_widget_conditions', $conditions);

        wp_send_json_success(['message' => 'Conditions saved successfully']);
    }

    /**
     * Render widget preview with PHP execution
     * Handles AJAX request to render widget HTML with PHP code executed
     */
    public function render_preview() {
        $nonce = isset($_POST['_nonce']) ? $_POST['_nonce'] : (isset($_GET['_nonce']) ? $_GET['_nonce'] : '');
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            wp_send_json_error(['message' => 'Nonce verification failed']);
            return;
        }

        $html_code = isset($_POST['html_code']) ? wp_unslash($_POST['html_code']) : '';
        $css_code = isset($_POST['css_code']) ? wp_unslash($_POST['css_code']) : '';
        $controls = isset($_POST['controls']) ? json_decode(wp_unslash($_POST['controls']), true) : [];

        // Build mock settings array from controls
        $settings = [];
        if (!empty($controls)) {
            foreach ($controls as $control) {
                if (isset($control['name']) && isset($control['default'])) {
                    $settings[$control['name']] = $control['default'];
                }
            }
        }

        // Replace placeholders with PHP variables using regex to handle dot notation
        // Handles: {{field}}, {{field.property}}, {{field.property.subproperty}}
        if (!empty($controls)) {
            foreach ($controls as $control) {
                if (isset($control['name'])) {
                    $control_name = $control['name'];

                    // Regex to match {{control_name}} or {{control_name.property}} or {{control_name.property.subproperty}}
                    // Pattern: {{control_name}} followed by optional .property.property...
                    $pattern = '/\{\{' . preg_quote($control_name, '/') . '((?:\.[a-zA-Z0-9_]+)*)\}\}/';

                    $html_code = preg_replace_callback($pattern, function($matches) use ($control_name) {
                        $properties = $matches[1]; // e.g., "" or ".tabs" or ".property.subproperty"

                        if (empty($properties)) {
                            // No properties, just {{control_name}}
                            // Return: $settings['control_name']
                            return "\$settings['" . $control_name . "']";
                        } else {
                            // Has properties like .tabs or .property.subproperty
                            // Convert to: $settings['control_name']['tabs'] or $settings['control_name']['property']['subproperty']
                            $props = explode('.', ltrim($properties, '.'));
                            $result = "\$settings['" . $control_name . "']";
                            foreach ($props as $prop) {
                                if (!empty($prop)) {
                                    $result .= "['" . $prop . "']";
                                }
                            }
                            return $result;
                        }
                    }, $html_code);
                }
            }
        }

        // Execute PHP code in the HTML
        ob_start();

        // Make settings available in the PHP context
        extract($settings, EXTR_SKIP);

        // Evaluate the PHP code
        // Wrap in PHP tags if not present
        if (strpos($html_code, '<?php') === false && strpos($html_code, '<?=') === false) {
            // No PHP code, just output as is
            echo $html_code;
        } else {
            // Has PHP code, evaluate it
            try {
                eval('?>' . $html_code . '<?php ;');
            } catch (ParseError $e) {
                echo '<div style="color:red;padding:20px;background:#fff3cd;border:1px solid #ffc107;">';
                echo '<h3>PHP Parse Error in Preview:</h3>';
                echo '<pre>' . esc_html($e->getMessage()) . '</pre>';
                echo '<h4>Line ' . $e->getLine() . '</h4>';
                echo '</div>';
            } catch (Exception $e) {
                echo '<div style="color:red;padding:20px;background:#fff3cd;border:1px solid #ffc107;">';
                echo '<h3>PHP Error in Preview:</h3>';
                echo '<pre>' . esc_html($e->getMessage()) . '</pre>';
                echo '</div>';
            }
        }

        $rendered_html = ob_get_clean();

        // Build full HTML document with CSS
        $output = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>' . $css_code . '</style>
</head>
<body>' . $rendered_html . '</body>
</html>';

        wp_send_json_success(['html' => $output]);
    }
}
