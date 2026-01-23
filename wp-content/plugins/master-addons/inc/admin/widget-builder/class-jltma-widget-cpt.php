<?php
/**
 * Master Addons Widget Builder Custom Post Type
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */

namespace MasterAddons\Admin\WidgetBuilder;

if (!defined('ABSPATH')) {
    exit;
}

class JLTMA_Widget_CPT {

    private static $instance = null;
    private $post_type = 'jltma_widget';

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('init', [$this, 'register_post_type']);
        add_filter('manage_jltma_widget_posts_columns', [$this, 'set_custom_columns']);
        add_action('manage_jltma_widget_posts_custom_column', [$this, 'custom_column_content'], 10, 2);
        add_filter('manage_edit-jltma_widget_sortable_columns', [$this, 'set_sortable_columns']);
        add_action('save_post', [$this, 'save_meta_data']);
        add_filter('post_row_actions', [$this, 'modify_row_actions'], 10, 2);
        add_action('admin_head', [$this, 'admin_head_css']);
        add_action('admin_footer', [$this, 'admin_footer_js']);
        add_action('wp_ajax_jltma_widget_get_shortcode', [$this, 'get_shortcode_ajax']);
        add_action('wp_ajax_jltma_add_widget_category', [$this, 'add_widget_category_ajax']);
        add_action('template_redirect', [$this, 'handle_widget_preview']);
    }

    public function register_post_type() {
        $labels = [
            'name'                  => _x('Master Addons Widgets', 'Post Type General Name', 'master-addons'),
            'singular_name'         => _x('Widget', 'Post Type Singular Name', 'master-addons'),
            'menu_name'             => __('Widgets', 'master-addons'),
            'name_admin_bar'        => __('Widget', 'master-addons'),
            'archives'              => __('Widget Archives', 'master-addons'),
            'attributes'            => __('Widget Attributes', 'master-addons'),
            'parent_item_colon'     => __('Parent Widget:', 'master-addons'),
            'all_items'             => __('All Widgets', 'master-addons'),
            'add_new_item'          => __('Add New Widget', 'master-addons'),
            'add_new'               => __('Add New', 'master-addons'),
            'new_item'              => __('New Widget', 'master-addons'),
            'edit_item'             => __('Edit Widget', 'master-addons'),
            'update_item'           => __('Update Widget', 'master-addons'),
            'view_item'             => __('View Widget', 'master-addons'),
            'view_items'            => __('View Widgets', 'master-addons'),
            'search_items'          => __('Search Widget', 'master-addons'),
            'not_found'             => __('Not found', 'master-addons'),
            'not_found_in_trash'    => __('Not found in Trash', 'master-addons'),
            'featured_image'        => __('Featured Image', 'master-addons'),
            'set_featured_image'    => __('Set featured image', 'master-addons'),
            'remove_featured_image' => __('Remove featured image', 'master-addons'),
            'use_featured_image'    => __('Use as featured image', 'master-addons'),
            'insert_into_item'      => __('Insert into widget', 'master-addons'),
            'uploaded_to_this_item' => __('Uploaded to this widget', 'master-addons'),
            'items_list'            => __('Widgets list', 'master-addons'),
            'items_list_navigation' => __('Widgets list navigation', 'master-addons'),
            'filter_items_list'     => __('Filter widgets list', 'master-addons'),
        ];

        $args = [
            'label'                 => __('Widget', 'master-addons'),
            'description'           => __('Master Addons Custom Widgets', 'master-addons'),
            'labels'                => $labels,
            'supports'              => ['title', 'editor'],
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false, // We'll add it to our custom menu
            'menu_position'         => 20,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => false,
        ];

        register_post_type($this->post_type, $args);
    }

    public function set_custom_columns($columns) {
        $new_columns = [];

        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Widget Name', 'master-addons');
        $new_columns['jltma_widget_category'] = __('Widget Category', 'master-addons');
        $new_columns['author'] = __('Author', 'master-addons');
        $new_columns['jltma_widget_shortcode'] = __('Shortcode', 'master-addons');

        return $new_columns;
    }

    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'jltma_widget_category':
                $this->render_category_column($post_id);
                break;

            case 'jltma_widget_shortcode':
                $this->render_shortcode_column($post_id);
                break;
        }
    }

    private function render_category_column($post_id) {
        $category = get_post_meta($post_id, '_jltma_widget_category', true);

        if (empty($category)) {
            $category = 'general';
        }

        $category_name = ucfirst(str_replace('_', ' ', $category));

        echo '<span class="jltma-widget-category">' . esc_html($category_name) . '</span>';
        echo '<br><a href="#" class="jltma-widget-edit-category" data-post-id="' . $post_id . '" data-category="' . esc_attr($category) . '">';
        echo '<span class="dashicons dashicons-edit"></span> ' . __('Edit Category', 'master-addons');
        echo '</a>';
    }

    private function render_shortcode_column($post_id) {
        // Use the new shortcode format with widget ID
        $shortcode = '[jltma_widget_' . $post_id . ']';

        echo '<div style="position: relative; display: inline-block; max-width: 280px;">';
        echo '<input type="text" readonly value="' . esc_attr($shortcode) . '" onclick="this.select()" style="width: 100%; font-family: monospace; font-size: 12px; padding: 4px 32px 4px 8px; border: 1px solid #ddd; border-radius: 3px; background: #f9f9f9; box-sizing: border-box;" title="' . esc_attr__('Click to select', 'master-addons') . '">';
        echo '<button type="button" class="jltma-copy-shortcode-widget" data-shortcode="' . esc_attr($shortcode) . '" style="position: absolute; right: 2px; top: 2px; padding: 2px 4px; border: 1px solid #2271b1; border-radius: 3px; background: #fff; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; color: #2271b1;" title="' . esc_attr__('Copy to clipboard', 'master-addons') . '">';
        echo '<span class="dashicons dashicons-clipboard" style="font-size: 14px; width: 14px; height: 14px;"></span>';
        echo '</button>';
        echo '</div>';
    }

    public function set_sortable_columns($columns) {
        $columns['jltma_widget_category'] = 'jltma_widget_category';
        return $columns;
    }

    public function modify_row_actions($actions, $post) {
        if ($post->post_type === $this->post_type) {
            // Remove default actions
            unset($actions['inline hide-if-no-js']);
            unset($actions['view']);
        }
        return $actions;
    }

    public function save_meta_data($post_id) {
        if (!isset($_POST['jltma_widget_meta_nonce_field']) ||
            !wp_verify_nonce($_POST['jltma_widget_meta_nonce_field'], 'jltma_widget_meta_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save widget name
        if (isset($_POST['jltma_widget_name'])) {
            $widget_name = sanitize_title($_POST['jltma_widget_name']);
            update_post_meta($post_id, '_jltma_widget_name', $widget_name);
        }

        // Save category
        if (isset($_POST['jltma_widget_category'])) {
            update_post_meta($post_id, '_jltma_widget_category', sanitize_text_field($_POST['jltma_widget_category']));
        }
    }

    public function admin_head_css() {
        global $current_screen;

        if (!$current_screen || ($current_screen->post_type !== $this->post_type && $current_screen->id !== 'master-addons_page_jltma-widget-builder')) {
            return;
        }

        ?>
        <style>
        .jltma-widget-category {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            background: #dcdcde;
            color: #50575e;
        }
        .jltma-widget-edit-category {
            color: #2271b1;
            text-decoration: none;
            font-size: 12px;
        }
        .jltma-widget-edit-category:hover {
            color: #135e96;
        }
        /* Shortcode Column Styling */
        .jltma-shortcode-box {
            display: inline-flex;
            align-items: center;
            border: 1px solid #dcdcde;
            border-radius: 4px;
            background: #fff;
            overflow: hidden;
            max-width: 280px;
            transition: border-color 0.2s ease;
        }
        .jltma-shortcode-box:hover {
            border-color: #8c8f94;
        }
        .jltma-shortcode-input {
            flex: 1;
            padding: 6px 10px;
            border: none;
            background: transparent;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            color: #2c3338;
            outline: none;
            min-width: 0;
        }
        .jltma-copy-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            background: transparent;
            border: none;
            border-left: 1px solid #dcdcde;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #50575e;
        }
        .jltma-copy-icon-btn:hover {
            background: #f0f0f1;
            color: #2271b1;
        }
        .jltma-copy-icon-btn:active {
            background: #dcdcde;
        }
        .jltma-copy-icon-btn svg {
            width: 18px;
            height: 18px;
            display: block;
        }
        .jltma-copy-feedback {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 6px;
            color: #00a32a;
            font-size: 12px;
            font-weight: 500;
            animation: fadeInOut 2.5s ease-in-out;
        }
        .jltma-copy-feedback .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
        }
        @keyframes fadeInOut {
            0%, 100% { opacity: 0; }
            10%, 90% { opacity: 1; }
        }

        /* Widget Preview Styles */
        .jltma-preview-notice {
            padding: 20px;
            background: #f0f6fc;
            border: 1px solid #c3dafe;
            border-radius: 4px;
            color: #0c5460;
            text-align: center;
        }
        .jltma-widget-preview-wrapper {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        .jltma-preview-toolbar {
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .jltma-preview-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .jltma-refresh-preview {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #2271b1;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .jltma-refresh-preview:hover {
            background: #135e96;
        }
        .jltma-refresh-preview .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
        }
        .jltma-preview-size-selector {
            display: flex;
            gap: 4px;
            border: 1px solid #ddd;
            border-radius: 3px;
            overflow: hidden;
        }
        .jltma-preview-size {
            padding: 6px 10px;
            background: #fff;
            border: none;
            border-right: 1px solid #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .jltma-preview-size:last-child {
            border-right: none;
        }
        .jltma-preview-size:hover {
            background: #f0f0f1;
        }
        .jltma-preview-size.active {
            background: #2271b1;
            color: #fff;
        }
        .jltma-preview-size .dashicons {
            font-size: 18px;
            width: 18px;
            height: 18px;
        }
        .jltma-preview-container {
            position: relative;
            background: #fff;
            min-height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .jltma-preview-loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 10;
        }
        .jltma-preview-loader.hidden {
            display: none;
        }
        .jltma-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #2271b1;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 12px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .jltma-preview-loader p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        .jltma-preview-frame {
            width: 100%;
            min-height: 400px;
            border: none;
            background: #fff;
            transition: all 0.3s ease;
        }
        .jltma-preview-frame.loading {
            opacity: 0.3;
        }
        .jltma-preview-frame.desktop {
            width: 100%;
        }
        .jltma-preview-frame.tablet {
            width: 768px;
            max-width: 100%;
            margin: 20px auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .jltma-preview-frame.mobile {
            width: 375px;
            max-width: 100%;
            margin: 20px auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        /* Shortcode Copy Button */
        .jltma-copy-shortcode-widget {
            transition: all 0.2s ease;
        }
        .jltma-copy-shortcode-widget:hover {
            background: #f0f6fc !important;
            border-color: #0073aa !important;
        }
        .jltma-copy-shortcode-widget:hover .dashicons {
            color: #0073aa !important;
        }

        /* Toast Notification */
        .jltma-toast-notification {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
            color: #fff;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 176, 155, 0.4);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
            min-width: 300px;
            max-width: 500px;
            text-align: center;
            z-index: 999999;
            animation: jltmaSlideUp 0.3s ease-out;
            overflow: hidden;
        }
        .jltma-toast-notification::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
            pointer-events: none;
        }
        .jltma-toast-notification .dashicons {
            font-size: 20px;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }
        .jltma-toast-notification span:not(.dashicons) {
            position: relative;
            z-index: 1;
        }
        @keyframes jltmaSlideUp {
            from {
                transform: translateX(-50%) translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }

        /* Add New Category Modal */
        .jltma-category-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999999;
            animation: fadeIn 0.2s ease-out;
        }
        .jltma-category-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #2c2f36;
            border-radius: 8px;
            padding: 30px;
            min-width: 500px;
            max-width: 90%;
            z-index: 1000000;
            animation: slideDown 0.3s ease-out;
        }
        .jltma-category-modal h2 {
            color: #ffffff;
            font-size: 20px;
            margin: 0 0 24px 0;
            font-weight: 500;
        }
        .jltma-category-modal-input-wrapper {
            margin-bottom: 20px;
        }
        .jltma-category-modal input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            background: #1e2126;
            border: 1px solid #3e434a;
            border-radius: 6px;
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease;
        }
        .jltma-category-modal input[type="text"]:focus {
            border-color: #5b6aff;
        }
        .jltma-category-modal input[type="text"]::placeholder {
            color: #8a909a;
        }
        .jltma-category-modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }
        .jltma-category-modal-btn {
            padding: 10px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .jltma-category-modal-btn.primary {
            background: #5b6aff;
            color: #ffffff;
        }
        .jltma-category-modal-btn.primary:hover {
            background: #4a59ee;
        }
        .jltma-category-modal-btn.primary:disabled {
            background: #3e4a8a;
            cursor: not-allowed;
            opacity: 0.6;
        }
        .jltma-category-modal-btn.secondary {
            background: transparent;
            color: #ff4757;
            border: 1px solid #ff4757;
        }
        .jltma-category-modal-btn.secondary:hover {
            background: rgba(255, 71, 87, 0.1);
        }
        .jltma-category-modal-description {
            color: #8a909a;
            font-size: 13px;
            font-style: italic;
            margin-top: 12px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideDown {
            from {
                transform: translate(-50%, -60%);
                opacity: 0;
            }
            to {
                transform: translate(-50%, -50%);
                opacity: 1;
            }
        }
        </style>
        <?php
    }

    public function admin_footer_js() {
        $screen = get_current_screen();

        // Check if we're on widget listing or edit page
        if (!$screen || !in_array($screen->id, ['edit-jltma_widget', 'jltma_widget', 'master-addons_page_jltma-widget-builder'])) {
            return;
        }

        ?>
        <!-- Add New Category Modal -->
        <div class="jltma-category-modal-overlay">
            <div class="jltma-category-modal">
                <h2><?php _e('Add New Widget Category', 'master-addons'); ?></h2>
                <div class="jltma-category-modal-input-wrapper">
                    <input type="text" id="jltma-new-category-name" placeholder="<?php esc_attr_e('Enter category name', 'master-addons'); ?>" />
                    <p class="jltma-category-modal-description"><?php _e('Category slug will be auto-generated from the name', 'master-addons'); ?></p>
                </div>
                <div class="jltma-category-modal-buttons">
                    <button type="button" class="jltma-category-modal-btn secondary" id="jltma-cancel-category">
                        <?php _e('Cancel', 'master-addons'); ?>
                    </button>
                    <button type="button" class="jltma-category-modal-btn primary" id="jltma-add-category" disabled>
                        <?php _e('Add Category', 'master-addons'); ?>
                    </button>
                </div>
            </div>
        </div>

        <script>

        jQuery(document).ready(function($) {

            // ===========================
            // Add New Category Modal
            // ===========================
            var $categorySelect = $('#jltma_widget_category');
            var $modalOverlay = $('.jltma-category-modal-overlay');
            var $categoryInput = $('#jltma-new-category-name');
            var $addButton = $('#jltma-add-category');
            var $cancelButton = $('#jltma-cancel-category');
            var previousCategoryValue = '';

            // Handle "+ Add New Category" selection
            $categorySelect.on('change', function() {
                if ($(this).val() === 'add_new_category') {
                    previousCategoryValue = $(this).data('previous-value') || 'general';
                    openCategoryModal();
                } else {
                    $(this).data('previous-value', $(this).val());
                }
            });

            // Open modal
            function openCategoryModal() {
                $modalOverlay.fadeIn(200);
                $categoryInput.val('').focus();
                $addButton.prop('disabled', true);
            }

            // Close modal
            function closeCategoryModal() {
                $modalOverlay.fadeOut(200);
                $categoryInput.val('');
                $categorySelect.val(previousCategoryValue);
                $addButton.prop('disabled', true);
            }

            // Close modal on overlay click
            $modalOverlay.on('click', function(e) {
                if (e.target === this) {
                    closeCategoryModal();
                }
            });

            // Close modal on Cancel button
            $cancelButton.on('click', function() {
                closeCategoryModal();
            });

            // Enable/disable Add button based on input
            $categoryInput.on('input', function() {
                var value = $(this).val().trim();
                $addButton.prop('disabled', value.length === 0);
            });

            // Handle Enter key in input
            $categoryInput.on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    if (!$addButton.prop('disabled')) {
                        $addButton.click();
                    }
                }
            });

            // Handle Escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $modalOverlay.is(':visible')) {
                    closeCategoryModal();
                }
            });

            // Handle Add Category button
            $addButton.on('click', function() {
                var categoryName = $categoryInput.val().trim();

                if (categoryName.length === 0) {
                    return;
                }

                // Disable button and show loading state
                $addButton.prop('disabled', true).text('<?php echo esc_js(__('Adding...', 'master-addons')); ?>');

                // Send AJAX request
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jltma_add_widget_category',
                        category_name: categoryName,
                        _nonce: '<?php echo wp_create_nonce('jltma_add_category_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Add new option to select (before "+ Add New Category")
                            var newOption = '<option value="' + response.data.slug + '">' +
                                          response.data.name + '</option>';
                            $categorySelect.find('option[value="add_new_category"]').before(newOption);

                            // Select the new category
                            $categorySelect.val(response.data.slug).data('previous-value', response.data.slug);

                            // Close modal
                            closeCategoryModal();

                            // Show success message
                            showToast(response.data.message || '<?php echo esc_js(__('Category added successfully!', 'master-addons')); ?>');
                        } else {
                            alert(response.data.message || '<?php echo esc_js(__('Failed to add category. Please try again.', 'master-addons')); ?>');
                            $addButton.prop('disabled', false).text('<?php echo esc_js(__('Add Category', 'master-addons')); ?>');
                        }
                    },
                    error: function() {
                        alert('<?php echo esc_js(__('An error occurred. Please try again.', 'master-addons')); ?>');
                        $addButton.prop('disabled', false).text('<?php echo esc_js(__('Add Category', 'master-addons')); ?>');
                    }
                });
            });

            // Copy shortcode to clipboard (listing page - new style)
            $(document).on('click', '.jltma-copy-shortcode-widget', function(e) {
                e.preventDefault();
                var button = $(this);
                var shortcode = button.data('shortcode');
                var originalIcon = button.find('.dashicons').attr('class');

                if (!shortcode) {
                    return;
                }

                // Use modern clipboard API if available
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shortcode).then(function() {
                        showCopySuccess(button, originalIcon);
                    }).catch(function(err) {
                        fallbackCopyButton(shortcode, button, originalIcon);
                    });
                } else {
                    fallbackCopyButton(shortcode, button, originalIcon);
                }
            });

            function fallbackCopyButton(text, button, originalIcon) {
                var temp = $('<textarea>');
                $('body').append(temp);
                temp.val(text).select();
                document.execCommand('copy');
                temp.remove();
                showCopySuccess(button, originalIcon);
            }

            function showCopySuccess(button, originalIcon) {
                // Change icon to checkmark
                button.find('.dashicons').removeClass('dashicons-clipboard').addClass('dashicons-yes-alt');

                // Use JLTMA Toaster if available, otherwise create custom toast
                if (typeof JLTMA_Toaster !== 'undefined' && JLTMA_Toaster.success) {
                    JLTMA_Toaster.success('<?php echo esc_js(__('Shortcode copied to clipboard!', 'master-addons')); ?>', 2000);
                } else {
                    showToast('<?php echo esc_js(__('Shortcode copied to clipboard!', 'master-addons')); ?>');
                }

                // Reset icon after delay
                setTimeout(function() {
                    button.find('.dashicons').attr('class', originalIcon);
                }, 2000);
            }

            function showToast(message) {
                // Remove any existing toasts
                $('.jltma-toast-notification').remove();

                // Create toast
                var toast = $('<div class="jltma-toast-notification">' +
                    '<span class="dashicons dashicons-yes"></span>' +
                    '<span>' + message + '</span>' +
                    '</div>');

                $('body').append(toast);

                // Auto-remove after 3 seconds
                setTimeout(function() {
                    toast.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }

            // Copy shortcode to clipboard (old style - if any remain)
            $(document).on('click', '.jltma-copy-icon-btn', function(e) {
                e.preventDefault();
                var button = $(this);
                var shortcode = button.data('shortcode');
                var box = button.closest('.jltma-shortcode-box');
                var feedback = box.next('.jltma-copy-feedback');

                // Use modern clipboard API if available
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shortcode).then(function() {
                        showCopyFeedback(feedback);
                    }).catch(function() {
                        fallbackCopy(shortcode, feedback);
                    });
                } else {
                    fallbackCopy(shortcode, feedback);
                }
            });

            // Fallback copy method for older browsers
            function fallbackCopy(text, feedback) {
                var temp = $('<textarea>');
                $('body').append(temp);
                temp.val(text).select();
                document.execCommand('copy');
                temp.remove();
                showCopyFeedback(feedback);
            }

            // Show copy feedback
            function showCopyFeedback(feedback) {
                feedback.show();
                setTimeout(function() {
                    feedback.fadeOut(400);
                }, 2000);
            }

            // Copy shortcode button (meta box)
            $('.jltma-copy-shortcode').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                var shortcode = button.data('shortcode');

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shortcode).then(function() {
                        showButtonFeedback(button);
                    }).catch(function() {
                        fallbackCopyButton(shortcode, button);
                    });
                } else {
                    fallbackCopyButton(shortcode, button);
                }
            });

            function fallbackCopyButton(text, button) {
                var temp = $('<textarea>');
                $('body').append(temp);
                temp.val(text).select();
                document.execCommand('copy');
                temp.remove();
                showButtonFeedback(button);
            }

            function showButtonFeedback(button) {
                var originalText = button.text();
                button.text('<?php echo esc_js(__('Copied!', 'master-addons')); ?>').css({
                    'background': '#00a32a',
                    'border-color': '#00a32a'
                });
                setTimeout(function() {
                    button.text(originalText).css({
                        'background': '',
                        'border-color': ''
                    });
                }, 2000);
            }

            // Widget Preview Functionality
            var $previewFrame = $('#jltma-widget-preview-frame');
            var $previewLoader = $('.jltma-preview-loader');

            if ($previewFrame.length) {
                // Load preview on page load
                function loadPreview() {
                    var widgetId = $previewFrame.data('widget-id');
                    var shortcode = $previewFrame.data('shortcode');

                    if (!widgetId) return;

                    // Show loader
                    $previewLoader.removeClass('hidden');
                    $previewFrame.addClass('loading');

                    // Create preview URL
                    var previewUrl = '<?php echo esc_js(add_query_arg(['jltma_widget_preview' => '1'], home_url('/'))); ?>';
                    previewUrl += '&widget_id=' + widgetId;
                    previewUrl += '&shortcode=' + encodeURIComponent(shortcode);
                    previewUrl += '&t=' + new Date().getTime(); // Cache buster

                    // Load iframe
                    $previewFrame.attr('src', previewUrl);
                }

                // Hide loader when iframe loads
                $previewFrame.on('load', function() {
                    setTimeout(function() {
                        $previewLoader.addClass('hidden');
                        $previewFrame.removeClass('loading');
                    }, 300);
                });

                // Refresh button
                $('.jltma-refresh-preview').on('click', function(e) {
                    e.preventDefault();
                    loadPreview();
                });

                // Preview size selector
                $('.jltma-preview-size').on('click', function(e) {
                    e.preventDefault();
                    var size = $(this).data('size');

                    // Update active state
                    $('.jltma-preview-size').removeClass('active');
                    $(this).addClass('active');

                    // Update frame class
                    $previewFrame.removeClass('desktop tablet mobile').addClass(size);
                });

                // Initial load
                loadPreview();
            }
        });
        </script>
        <?php
    }

    public function get_shortcode_ajax() {
        check_ajax_referer('jltma_widget_nonce', '_nonce');

        $post_id = intval($_POST['post_id']);

        // Use the new shortcode format with widget ID
        $shortcode = '[jltma_widget_' . $post_id . ']';

        wp_send_json_success(['shortcode' => $shortcode]);
    }

    public function add_widget_category_ajax() {
        // Verify nonce
        check_ajax_referer('jltma_add_category_nonce', '_nonce');

        // Check user capabilities
        if (!current_user_can('edit_posts')) {
            wp_send_json_error([
                'message' => __('You do not have permission to add categories.', 'master-addons')
            ]);
        }

        // Get category name
        $category_name = isset($_POST['category_name']) ? sanitize_text_field($_POST['category_name']) : '';

        if (empty($category_name)) {
            wp_send_json_error([
                'message' => __('Category name is required.', 'master-addons')
            ]);
        }

        // Generate slug from name
        $category_slug = sanitize_title($category_name);

        // Get existing categories
        $categories = get_option('jltma_widget_categories', []);

        // Check if category already exists
        if (isset($categories[$category_slug])) {
            wp_send_json_error([
                'message' => __('A category with this name already exists.', 'master-addons')
            ]);
        }

        // Add new category
        $categories[$category_slug] = $category_name;

        // Save categories
        update_option('jltma_widget_categories', $categories);

        // Return success
        wp_send_json_success([
            'message' => sprintf(__('Category "%s" added successfully!', 'master-addons'), $category_name),
            'slug' => $category_slug,
            'name' => $category_name
        ]);
    }

    public function handle_widget_preview() {
        // Check if this is a preview request
        if (!isset($_GET['jltma_widget_preview']) || $_GET['jltma_widget_preview'] != '1') {
            return;
        }

        // Verify user permissions
        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have permission to preview widgets.', 'master-addons'));
        }

        $widget_id = isset($_GET['widget_id']) ? intval($_GET['widget_id']) : 0;
        $shortcode = isset($_GET['shortcode']) ? sanitize_text_field($_GET['shortcode']) : '';

        if (!$widget_id || !$shortcode) {
            wp_die(__('Invalid widget preview request.', 'master-addons'));
        }

        // Check if widget exists
        $widget_post = get_post($widget_id);
        if (!$widget_post || $widget_post->post_type !== $this->post_type) {
            wp_die(__('Widget not found.', 'master-addons'));
        }

        // Render preview template
        $this->render_preview_template($widget_id, $shortcode);
        exit;
    }

    private function render_preview_template($widget_id, $shortcode) {
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php _e('Widget Preview', 'master-addons'); ?></title>
            <?php wp_head(); ?>
            <style>
                body {
                    margin: 0;
                    padding: 20px;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                    background: #fff;
                }
                .jltma-preview-content {
                    max-width: 100%;
                    margin: 0 auto;
                }
                .jltma-preview-notice {
                    background: #f0f6fc;
                    border-left: 4px solid #2271b1;
                    padding: 12px 16px;
                    margin-bottom: 20px;
                    font-size: 13px;
                    color: #1d2327;
                }
            </style>
        </head>
        <body>
            <div class="jltma-preview-content">
                <div class="jltma-preview-notice">
                    <?php _e('This is a live preview of your widget. Changes made to the widget will be reflected here after saving and refreshing.', 'master-addons'); ?>
                </div>
                <div class="jltma-widget-preview-output">
                    <?php echo do_shortcode($shortcode); ?>
                </div>
            </div>
            <?php wp_footer(); ?>
        </body>
        </html>
        <?php
    }

    public function get_post_type() {
        return $this->post_type;
    }
}
