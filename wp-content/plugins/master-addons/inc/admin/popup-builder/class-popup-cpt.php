<?php
/**
 * Master Addons Popup Custom Post Type
 *
 * @package MasterAddons
 * @subpackage PopupBuilder
 */

namespace MasterAddons\Inc\Admin\PopupBuilder;

if (!defined('ABSPATH')) {
    exit;
}

class Popup_CPT {

    private static $instance = null;
    private $post_type = 'jltma_popup';

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
        add_filter('manage_jltma_popup_posts_columns', [$this, 'set_custom_columns']);
        add_action('manage_jltma_popup_posts_custom_column', [$this, 'custom_column_content'], 10, 2);
        add_filter('manage_edit-jltma_popup_sortable_columns', [$this, 'set_sortable_columns']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_data']);
        add_filter('post_row_actions', [$this, 'modify_row_actions'], 10, 2);
        add_action('admin_head', [$this, 'admin_head_css']);
        add_action('wp_ajax_jltma_popup_get_shortcode', [$this, 'get_shortcode_ajax']);
        // Don't override single_template - let Elementor handle it
        // add_filter('single_template', [$this, 'load_canvas_template']);

        // Add Elementor support
        add_filter('elementor/utils/get_public_post_types', [$this, 'add_elementor_cpt_support']);
        add_action('elementor/init', [$this, 'register_with_elementor']);

        // Prevent redirect when editing with Elementor
        add_action('admin_init', [$this, 'prevent_elementor_redirect'], 1);
        add_filter('user_has_cap', [$this, 'grant_elementor_edit_cap'], 10, 4);
    }

    public function register_post_type() {
        $labels = [
            'name'                  => _x('Master Addons Popups', 'Post Type General Name', 'master-addons'),
            'singular_name'         => _x('Popup', 'Post Type Singular Name', 'master-addons'),
            'menu_name'             => __('Popups', 'master-addons'),
            'name_admin_bar'        => __('Popup', 'master-addons'),
            'archives'              => __('Popup Archives', 'master-addons'),
            'attributes'            => __('Popup Attributes', 'master-addons'),
            'parent_item_colon'     => __('Parent Popup:', 'master-addons'),
            'all_items'             => __('All Popups', 'master-addons'),
            'add_new_item'          => __('Add New Popup', 'master-addons'),
            'add_new'               => __('Add New', 'master-addons'),
            'new_item'              => __('New Popup', 'master-addons'),
            'edit_item'             => __('Edit Popup', 'master-addons'),
            'update_item'           => __('Update Popup', 'master-addons'),
            'view_item'             => __('View Popup', 'master-addons'),
            'view_items'            => __('View Popups', 'master-addons'),
            'search_items'          => __('Search Popup', 'master-addons'),
            'not_found'             => __('Not found', 'master-addons'),
            'not_found_in_trash'    => __('Not found in Trash', 'master-addons'),
            'featured_image'        => __('Featured Image', 'master-addons'),
            'set_featured_image'    => __('Set featured image', 'master-addons'),
            'remove_featured_image' => __('Remove featured image', 'master-addons'),
            'use_featured_image'    => __('Use as featured image', 'master-addons'),
            'insert_into_item'      => __('Insert into popup', 'master-addons'),
            'uploaded_to_this_item' => __('Uploaded to this popup', 'master-addons'),
            'items_list'            => __('Popups list', 'master-addons'),
            'items_list_navigation' => __('Popups list navigation', 'master-addons'),
            'filter_items_list'     => __('Filter popups list', 'master-addons'),
        ];

        $args = [
            'label'                 => __('Popup', 'master-addons'),
            'description'           => __('Master Addons Popup Templates', 'master-addons'),
            'labels'                => $labels,
            'supports'              => ['title', 'editor', 'elementor'],
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => false, // We'll add it to our custom menu
            'menu_position'         => 20,
            'show_in_admin_bar'     => true,  // Enable this so Elementor can detect it
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => false, // Disable rewrite since we use canvas template
        ];

        register_post_type($this->post_type, $args);

        // Add Elementor support explicitly for this post type
        if (defined('ELEMENTOR_VERSION')) {
            add_post_type_support($this->post_type, 'elementor');

            // Update Elementor's CPT support option
            $cpt_support = get_option('elementor_cpt_support', ['page', 'post']);
            if (!in_array($this->post_type, $cpt_support)) {
                $cpt_support[] = $this->post_type;
                update_option('elementor_cpt_support', $cpt_support);
            }
        }
    }

    /**
     * Load Elementor canvas template for popup frontend display
     * Don't override when editing in Elementor
     */
    public function load_canvas_template($single_template) {
        global $post;

        // Don't override template when editing with Elementor
        if (is_admin() || (isset($_GET['action']) && $_GET['action'] === 'elementor')) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only template check, no form submission
            return $single_template;
        }

        if ($post && $post->post_type === $this->post_type) {
            // Check if Elementor is installed
            if (defined('ELEMENTOR_PATH')) {
                $elementor_2_0_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

                if (file_exists($elementor_2_0_canvas)) {
                    return $elementor_2_0_canvas;
                } else {
                    return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
                }
            }
        }

        return $single_template;
    }

    public function set_custom_columns($columns) {
        $new_columns = [];

        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Popup Name', 'master-addons');
        $new_columns['jltma_popup_conditions'] = __('Conditions', 'master-addons');
        $new_columns['jltma_popup_status'] = __('Status', 'master-addons');
        $new_columns['jltma_popup_shortcode'] = __('Shortcode', 'master-addons');
        // $new_columns['date'] = $columns['date'];

        return $new_columns;
    }

    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'jltma_popup_conditions':
                $this->render_conditions_column($post_id);
                break;

            case 'jltma_popup_shortcode':
                $this->render_shortcode_column($post_id);
                break;

            case 'jltma_popup_status':
                $this->render_status_column($post_id);
                break;
        }
    }

    private function render_conditions_column($post_id) {
        $conditions_data = get_post_meta($post_id, '_jltma_popup_conditions_data', true);
        $cond_parts = [];

        if (!empty($conditions_data) && is_array($conditions_data)) {
            foreach ($conditions_data as $condition) {
                $type_label = isset($condition['type']) ? ucfirst($condition['type']) : 'Include';
                $rule_label = isset($condition['rule']) ? ucwords(str_replace('_', ' ', $condition['rule'])) : '';

                $cond_text = $type_label . ' &gt; ' . $rule_label;

                // Handle singular conditions with post types
                if (isset($condition['rule']) && $condition['rule'] === 'singular') {
                    if (!empty($condition['specific'])) {
                        $post_type_obj = get_post_type_object($condition['specific']);
                        $post_type_label = $post_type_obj ? $post_type_obj->label : $condition['specific'];

                        // Check if specific posts are selected
                        if (!empty($condition['posts']) && is_array($condition['posts'])) {
                            $post_titles = [];
                            foreach ($condition['posts'] as $condition_post_id) {
                                if ($condition_post_id && is_numeric($condition_post_id)) {
                                    $post_title = get_the_title($condition_post_id);
                                    if ($post_title) {
                                        $post_titles[] = $post_title;
                                    }
                                }
                            }

                            if (!empty($post_titles)) {
                                $cond_text .= ' &gt; ' . $post_type_label . ' &gt; ' . implode(', ', $post_titles);
                            } else {
                                $cond_text .= ' &gt; All ' . $post_type_label;
                            }
                        } else {
                            $cond_text .= ' &gt; All ' . $post_type_label;
                        }
                    } else {
                        $cond_text .= ' &gt; All';
                    }
                }
                // Handle archive conditions
                elseif (isset($condition['rule']) && $condition['rule'] === 'archive') {
                    if (!empty($condition['specific'])) {
                        // Format archive type labels
                        $archive_label = $condition['specific'];
                        if ($condition['specific'] === 'category') {
                            $archive_label = 'Category';
                        } elseif ($condition['specific'] === 'post_tag') {
                            $archive_label = 'Tag';
                        } elseif ($condition['specific'] === 'author') {
                            $archive_label = 'Author';
                        } elseif ($condition['specific'] === 'date') {
                            $archive_label = 'Date';
                        } else {
                            // For custom taxonomies
                            $taxonomy = get_taxonomy($condition['specific']);
                            if ($taxonomy) {
                                $archive_label = $taxonomy->labels->singular_name;
                            }
                        }
                        $cond_text .= ' &gt; ' . $archive_label;
                    } else {
                        $cond_text .= ' &gt; All';
                    }
                }
                // Other condition types remain as is (404, search, front_page, entire_site)

                $cond_parts[] = $cond_text;
            }
        }

        if (empty($cond_parts)) {
            $cond_parts[] = 'Include &gt; Entire Site';
        }

        $condition_text = implode('<br>', $cond_parts);
        echo wp_kses_post( $condition_text );
        $edit_conditions_html = '<br><a href="#" class="jltma-popup-edit-conditions" data-popup-id="' . $post_id . '">'
            . '<span class="dashicons dashicons-edit"></span> ' . __('Edit Conditions', 'master-addons')
            . '</a>';
        echo wp_kses_post( apply_filters('master_addons/popup_builder/edit_conditions_link', $edit_conditions_html, $post_id) );
    }

    private function render_shortcode_column($post_id) {
        $shortcode = '[jltma_popup id="' . $post_id . '"]';
        echo '<div style="display: flex; gap: 4px; position: relative;">';
        echo '<input type="text" readonly value="' . \esc_attr($shortcode) . '" onclick="this.select()" style="flex: 1; font-family: monospace; font-size: 12px; padding: 4px 8px; border: 1px solid #ddd; border-radius: 3px; background: #f9f9f9;" title="' . \esc_attr__('Click to select', 'master-addons') . '">';
        echo '<button type="button" class="jltma-copy-shortcode" data-shortcode="' . \esc_attr($shortcode) . '" style="padding: 4px 6px; border: 1px solid #ddd; border-radius: 3px; background: #f9f9f9; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; position: absolute; right: 4px; top: 50%; transform: translateY(-50%);" title="' . \esc_attr__('Copy to clipboard', 'master-addons') . '">';
        echo '<span class="dashicons dashicons-clipboard" style="font-size: 14px; width: 14px; height: 14px;"></span>';
        echo '</button>';
        echo '</div>';
    }

    private function render_status_column($post_id) {
        $status = get_post_meta($post_id, '_jltma_popup_activation', true);
        $is_active = ($status === 'yes');

        // Check if popup has automatic expiration and has expired
        if ($is_active) {
            $elementor_settings = get_post_meta($post_id, '_elementor_page_settings', true);

            if (!empty($elementor_settings['popup_disable_automatic']) && $elementor_settings['popup_disable_automatic'] === 'yes') {
                if (!empty($elementor_settings['popup_disable_after'])) {
                    $expiration_date = strtotime($elementor_settings['popup_disable_after']);
                    $current_time = current_time('timestamp');

                    // If expired, update the status
                    if ($current_time > $expiration_date) {
                        $is_active = false;
                        // Update the activation status in database
                        update_post_meta($post_id, '_jltma_popup_activation', 'no');

                        // Reset the automatic disable settings
                        $elementor_settings['popup_disable_automatic'] = 'no';
                        update_post_meta($post_id, '_elementor_page_settings', $elementor_settings);
                    }
                }
            }
        }

        $status_class = $is_active ? 'jltma-popup-status-active' : 'jltma-popup-status-inactive';
        $status_text = $is_active ? 'Active' : 'Inactive';

        echo '<span class="jltma-popup-status ' . esc_attr( $status_class ) . '">' . esc_html($status_text) . '</span>';
    }

    public function set_sortable_columns($columns) {
        $columns['jltma_popup_status'] = 'jltma_popup_status';
        return $columns;
    }

    public function modify_row_actions($actions, $post) {
        if ($post->post_type === $this->post_type) {
            // Remove default actions
            unset($actions['inline hide-if-no-js']);
            unset($actions['view']);

            // Replace default edit with modal opener
            $actions['edit'] = '<a href="#" class="jltma-edit-popup" data-popup-id="' . $post->ID . '">' . __('Edit', 'master-addons') . '</a>';
        }

        return $actions;
    }

    public function add_meta_boxes() {
        add_meta_box(
            'jltma_popup_settings',
            __('Popup Settings', 'master-addons'),
            [$this, 'render_popup_settings_meta_box'],
            $this->post_type,
            'side',
            'high'
        );

        add_meta_box(
            'jltma_popup_conditions',
            __('Display Conditions', 'master-addons'),
            [$this, 'render_popup_conditions_meta_box'],
            $this->post_type,
            'normal',
            'high'
        );
    }

    public function render_popup_settings_meta_box($post) {
        wp_nonce_field('jltma_popup_meta_nonce', 'jltma_popup_meta_nonce_field');

        $activation = get_post_meta($post->ID, '_jltma_popup_activation', true);

        ?>
        <table class="form-table">
            <tr>
                <td>
                    <label>
                        <input type="checkbox" name="jltma_popup_activation" value="yes" <?php checked($activation, 'yes'); ?>>
                        <?php esc_html_e('Active', 'master-addons'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_popup_conditions_meta_box($post) {
        $conditions_data = get_post_meta($post->ID, '_jltma_popup_conditions_data', true);

        ?>
        <div class="jltma-popup-conditions-meta">
            <p><?php esc_html_e('Configure where this popup should be displayed. Use the "Edit Conditions" button in the popup list for advanced condition management.', 'master-addons'); ?></p>
            <button type="button" class="button jltma-popup-edit-conditions" data-popup-id="<?php echo absint( $post->ID ); ?>">
                <?php esc_html_e('Edit Conditions', 'master-addons'); ?>
            </button>
        </div>
        <?php
    }

    public function save_meta_data($post_id) {
        if (!isset($_POST['jltma_popup_meta_nonce_field']) ||
            !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jltma_popup_meta_nonce_field'] ) ), 'jltma_popup_meta_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save activation status
        $activation = isset($_POST['jltma_popup_activation']) ? 'yes' : 'no';
        update_post_meta($post_id, '_jltma_popup_activation', $activation);
    }

    public function admin_head_css() {
        global $current_screen;

        if (!$current_screen || $current_screen->post_type !== $this->post_type) {
            return;
        }

        ?>
        <style>
        .jltma-popup-status {
            display: inline-block;
            margin-left: 5px;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 11px;
            line-height: 1.4;
            font-weight: 600;
            text-transform: capitalize;
            letter-spacing: 0.02em;
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .jltma-popup-status-active {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .jltma-popup-edit-conditions {
            color: #2271b1;
            text-decoration: none;
            font-size: 12px;
        }
        .jltma-copy-shortcode {
            color: #2271b1;
            text-decoration: none;
            font-size: 12px;
        }
        .jltma-copy-shortcode:hover {
            color: #135e96;
        }
        .jltma-shortcode-display {
            background: #f0f0f1;
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 11px;
        }
        .jltma-popup-conditions {
            font-size: 12px;
            color: #50575e;
        }
        </style>
        <?php
    }

    public function get_shortcode_ajax() {
        check_ajax_referer('jltma_popup_nonce', '_nonce');

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( [ 'message' => __( 'Insufficient permissions', 'master-addons' ) ] );
        }

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        $shortcode = '[jltma_popup id="' . $post_id . '"]';

        wp_send_json_success(['shortcode' => $shortcode]);
    }

    public function get_post_type() {
        return $this->post_type;
    }

    /**
     * Add popup post type to Elementor supported post types
     */
    public function add_elementor_cpt_support($post_types) {
        $post_types[$this->post_type] = $this->post_type;
        return $post_types;
    }

    /**
     * Register popup post type with Elementor on init
     */
    public function register_with_elementor() {
        if (!defined('ELEMENTOR_VERSION')) {
            return;
        }

        // Ensure the post type is in Elementor's CPT support list
        $cpt_support = get_option('elementor_cpt_support', ['page', 'post']);
        if (!in_array($this->post_type, $cpt_support)) {
            $cpt_support[] = $this->post_type;
            update_option('elementor_cpt_support', $cpt_support);
        }
    }

    /**
     * Prevent redirect when editing popup with Elementor
     * Also ensures proper document type is set
     */
    public function prevent_elementor_redirect() {
        // Check if we're trying to edit a popup with Elementor
        if (!isset($_GET['action']) || $_GET['action'] !== 'elementor') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only redirect check, no form submission
            return;
        }

        if (!isset($_GET['post'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only redirect check, no form submission
            return;
        }

        $post_id = intval($_GET['post']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only redirect check, no form submission

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $post = get_post($post_id);

        if (!$post || $post->post_type !== $this->post_type) {
            return;
        }

        // Force add to Elementor's CPT support before any checks
        if (defined('ELEMENTOR_VERSION')) {
            $cpt_support = get_option('elementor_cpt_support', ['page', 'post']);
            if (!in_array($this->post_type, $cpt_support)) {
                $cpt_support[] = $this->post_type;
                update_option('elementor_cpt_support', $cpt_support);
            }

            // Force add post type support
            add_post_type_support($this->post_type, 'elementor');
        }

        // CRITICAL: Ensure Elementor mode is set
        update_post_meta($post_id, '_elementor_edit_mode', 'builder');

        // ALWAYS force our custom document type
        update_post_meta($post_id, '_elementor_template_type', 'jltma_popup');

        // Ensure page template is set to canvas for proper rendering
        update_post_meta($post_id, '_wp_page_template', 'elementor_canvas');
    }

    /**
     * Grant edit_posts capability for popup post type
     * This prevents WordPress from redirecting when Elementor checks capabilities
     */
    public function grant_elementor_edit_cap($allcaps, $caps, $args, $user) {
        // Only apply when editing with Elementor
        if (!isset($_GET['action']) || $_GET['action'] !== 'elementor' || !isset($_GET['post'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only capability check, no form submission
            return $allcaps;
        }

        $post_id = intval($_GET['post']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only capability check, no form submission
        $post = get_post($post_id);

        if (!$post || $post->post_type !== $this->post_type) {
            return $allcaps;
        }

        // Security: only grant the cap if the user already has a base editing
        // capability through their role. Without this guard, any logged-in user
        // (incl. subscribers) visiting the Elementor edit URL for a popup would be
        // granted edit_post for that request — a privilege escalation. With
        // capability_type='post' on the CPT, authors+ already have edit_posts
        // natively, so the workaround stays a no-op for them and is fully off for
        // lower-privileged users.
        if (empty($user->allcaps['edit_posts']) && empty($user->allcaps['edit_pages'])) {
            return $allcaps;
        }

        // Grant edit capability for this specific post type
        if (isset($caps[0]) && in_array($caps[0], ['edit_post', 'edit_posts'])) {
            $allcaps[$caps[0]] = true;
        }

        return $allcaps;
    }
}
