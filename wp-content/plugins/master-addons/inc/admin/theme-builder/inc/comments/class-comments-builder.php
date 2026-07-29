<?php

namespace MasterAddons\Inc\Admin\Theme_Builder\Comments;

use MasterAddons\Inc\Admin\Theme_Builder\Comments\Addon\Comments_Addon;

defined('ABSPATH') || exit;


if (!class_exists(__NAMESPACE__ . '\Comments_Builder')) {

    class Comments_Builder
    {

        private static $_instance = null;

        public $jltma_set_var;
        public $jltma_api_settings;

        private $settings;

        public function __construct(array $settings = [])
        {
            $this->jltma_api_settings = get_option('jltma_api_save_settings');

            add_action('init', [$this, 'jltma_enable_comments_custom_post_type'], 11);
            add_filter('wp_insert_post_data', [$this, 'jltma_comments_on_by_default']);

            // Remove Clickable Comment Links
            remove_filter('comment_text', 'make_clickable', 9);
            add_filter('pre_comment_content', [$this, 'jltma_strip_comment_links']);

            add_action('comment_post', array($this, 'jltma_save_comment_meta_data'));

            add_action('wp_ajax_jltma_like_dislike', array($this, 'jltma_like_dislike_action'));
            add_action('wp_ajax_nopriv_jltma_like_dislike', array($this, 'jltma_like_dislike_action'));

            // Comment Pagination Ajax
            add_action('wp_ajax_jltma_comment_pagination', array($this, 'jltma_comment_pagination'));
            add_action('wp_ajax_nopriv_jltma_comment_pagination', array($this, 'jltma_comment_pagination'));

            add_action('elementor/frontend/before_register_styles', [$this, 'jltma_comments_frontend_styles']);
            add_action('elementor/frontend/before_register_scripts', [$this, 'jltma_comments_frontend_scripts']);

            $this->jltma_set_var = $settings;

            // Extra Comment Fields
            add_action('comment_form_after_fields', [$this, 'jltma_build_input_settings'], 10, 2);
            add_action('add_meta_boxes_comment', [$this, 'jltma_comment_add_meta_box']);
            add_action('edit_comment', [$this, 'jltma_comment_edit_comment']);
            add_action('comment_post', [$this, 'jltma_comment_insert_comment'], 10, 1);
            add_filter('comment_text', array($this, 'render_comment_meta_front'), 10, 2);


            // Remove Autop on Comment Text
            add_filter('comment_text', 'wptexturize');
            add_filter('comment_text', 'convert_chars');
            add_filter('comment_text', 'make_clickable',      9);
            add_filter('comment_text', 'force_balance_tags', 25);
            add_filter('comment_text', 'convert_smilies',    20);
            add_filter('comment_text', 'wpautop',            30);

            //Check SPAM Protection reCaptcha
            add_action('pre_comment_on_post', [$this, 'jltma_verify_google_recaptcha']);

            // Remove all comments field filter for others
            remove_all_filters('comment_form_default_fields');
            // Unset Default Fields
            // add_action('comment_form_default_fields', [$this,'jltma_default_comment_fields']);
        }

        public function jltma_default_comment_fields($fields)
        {
            $jltma_comment_fields = $this->jltma_set_var;
            if (isset($jltma_comment_fields['jltma_comment_fields_url_display']) && $jltma_comment_fields['jltma_comment_fields_url_display'] == "show") {

                if (isset($fields['url']))
                    unset($fields['url']);
            }
            return $fields;
        }


        public function render_comment_meta_front($jltma_comment_text, $comment)
        {

            $comment_meta = get_option('jltma_comments');
            $comment_content = $comment->comment_content;

            $comment_extra = "";

            if (!empty($comment_meta)) {

                if (is_admin()) {
                    $jltma_extra_field_heading = esc_html__('Extra Fields:', 'master-addons');
                    $comment_extra .= '<p><strong>' . $jltma_extra_field_heading . '</strong></p>';
                }

                $comment_extra .= '<ul>';

                foreach ($comment_meta as $key => $value) {

                    $label_name         = $value['label_name'];
                    $field_type         = $value['field_type'];
                    $required           = $value['required'];

                    $unique_field_id    = strtolower(str_replace(" ", "_", $label_name));
                    $jltma_field_value = 'jltma_' . esc_attr($unique_field_id);

                    if (is_admin()) {
                        $jltma_comment_extra_value = get_comment_meta(get_comment_ID(), $jltma_field_value, true);
                    } else {
                        $jltma_comment_extra_value = get_comment_meta($comment->comment_ID, $jltma_field_value, true);
                    }

                    if ($jltma_comment_extra_value != '') {
                        $comment_extra .= '<li><strong>' . esc_attr($label_name) . ': </strong>';
                        $comment_extra .= esc_attr($jltma_comment_extra_value) . '</li>';
                    }
                }

                $comment_extra .= '</ul>';
            }

            $jltma_comment_text = $comment_content . $comment_extra;

            return $jltma_comment_text;
        }


        public function jltma_comment_insert_comment($comment_id)
        {

            $comment_meta = get_option('jltma_comments');

            if (!empty($comment_meta)) {
                foreach ($comment_meta as $key => $value) {

                    $label_name         = $value['label_name'];
                    $field_type         = $value['field_type'];
                    $required           = $value['required'];

                    $unique_field_id    = strtolower(str_replace(" ", "_", $label_name));

                    $jltma_field_value = 'jltma_' . esc_attr($unique_field_id);

                    if (isset($_POST[$jltma_field_value])) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for comment_insert hook
                        update_comment_meta($comment_id, $jltma_field_value, sanitize_text_field( wp_unslash( $_POST[$jltma_field_value] ) )); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for comment_insert hook
                }
            }
        }


        public function jltma_comment_edit_comment($comment_id)
        {
            if (empty($_POST['jltma_comment_update']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jltma_comment_update'] ) ), 'jltma_comment_update')) return; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified on this line

            $comment_meta = get_option('jltma_comments');

            if (!empty($comment_meta)) {
                foreach ($comment_meta as $key => $value) {

                    $label_name         = $value['label_name'];
                    $field_type         = $value['field_type'];
                    $required           = $value['required'];

                    $unique_field_id    = strtolower(str_replace(" ", "_", $label_name));
                    $jltma_field_value = 'jltma_' . esc_attr($unique_field_id);

                    if (isset($_POST[$jltma_field_value]))
                        update_comment_meta($comment_id, $jltma_field_value, sanitize_text_field( wp_unslash( $_POST[$jltma_field_value] ) )); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
                }
            }
        }


        public function jltma_comment_add_meta_box($comment)
        {

            add_meta_box('jltma-comment-extra-fields', esc_html__('Extra Comment Fields', 'master-addons'), [$this, 'jltma_comment_meta_box_cb'], 'comment', 'normal', 'high');
        }

        public function fixObject(&$object)
        {

            if (!is_object($object) && gettype($object) == 'object')
                return ($object = unserialize(serialize($object)));
            return $object;
        }

        public function jltma_comment_meta_box_cb($comment)
        {

            wp_nonce_field('jltma_comment_update', 'jltma_comment_update', false);

            $comment_meta = get_option('jltma_comments');

            if (!empty($comment_meta)) {
                foreach ($comment_meta as $key => $value) {

                    $label_name         = $value['label_name'];
                    $field_type         = $value['field_type'];
                    $required           = $value['required'];

                    $unique_field_id    = strtolower(str_replace(" ", "_", $label_name));
                    $jltma_field_value = get_comment_meta($comment->comment_ID, 'jltma_' . absint($unique_field_id), true);

                    if ($field_type == 'text') {
                        echo '<p>
                                <label for="jltma_' . esc_attr($unique_field_id) . '">' . esc_html($label_name) . '</label>
                                <input type="text" name="jltma_' . esc_attr($unique_field_id) . '" value="' . esc_attr($jltma_field_value) . '" class="widefat" />
                            </p>';
                    }
                }
            }
        }



        public function jltma_build_input_settings()
        {

            $jltma_comment_fields = $this->jltma_set_var;

            if (isset($jltma_comment_fields['jltma_comment_extra_fields_items'])) {
                foreach ($jltma_comment_fields['jltma_comment_extra_fields_items'] as $key => $value) {

                    $label_name         = $value['label_name'];
                    $field_type         = $value['field_type'];
                    $display_label      = $value['display_label'];
                    $placeholder        = $value['placeholder'];
                    $error_msg          = $value['error_msg'];
                    $required           = $value['required'];
                    // $checkbox_options   = $value['checkbox_options'];

                    $unique_field_id    = strtolower(str_replace(" ", "_", $label_name));


                    if ($required == 'yes') {
                        $required = 'true';
                        $required_label = ' <span class="required">' . esc_html__('*', 'master-addons') . '</span>';
                    } else {
                        $required = 'false';
                        $required_label = '';
                    }


                    // Render Field Types

                    if ($field_type == 'text') {

                        $jltma_cmnt_extra_label_container = "";
                        if ($display_label == "show") {
                            $label_name = ($label_name != '') ? esc_html($label_name) : '';
                            $required_labels = isset($required_label) ? esc_attr($required_label) : "";

                            $jltma_cmnt_extra_label_container = '<div class="jltma-name-div">
                                    <label>' . $label_name . $required_labels . '</label>
                                </div>';
                        }

                        $jltma_cmnt_extra_placeholder = ($placeholder != '') ? esc_attr($placeholder) : '';

                        $jltma_cmnt_extra_ft = (isset($value['jltma_field_type']) && $value['jltma_field_type']) ? $value['jltma_field_type'] : "text";

                        echo '<div class="jltma-name-value-div jltma-' . esc_attr($unique_field_id) . '">
                                ' . wp_kses_post($jltma_cmnt_extra_label_container) . '
                            <div class="jltma-value-div">
                                <input class="form-control" type="text" name="jltma_' . esc_attr($unique_field_id) . '" id="' . esc_attr($unique_field_id) . '" value="" placeholder="' . esc_attr($jltma_cmnt_extra_placeholder) . '"  aria-required="' . esc_attr($required) . '"/>
                            </div>
                        </div>';
                    }


                    if ($field_type == 'textarea') {

                        $jltma_cmnt_extra_label_container = "";
                        if ($display_label == "show") {
                            $label_name = ($label_name != '') ? esc_html($label_name) : '';
                            $required_labels = isset($required_label) ? esc_attr($required_label) : "";

                            $jltma_cmnt_extra_label_container = '<div class="jltma-name-div">
                                    <label>' . $label_name . $required_labels . '</label>
                                </div>';
                        }

                        $jltma_cmnt_extra_placeholder = ($placeholder != '') ? esc_attr($placeholder) : '';


                        echo '<div class="jltma-name-value-div jltma-' . esc_attr($unique_field_id) . '">
                                ' . wp_kses_post($jltma_cmnt_extra_label_container) . '
                            <div class="jltma-value-div">
                                <textarea class="form-control" name="jltma_' . esc_attr($unique_field_id) . '" id="' . esc_attr($unique_field_id) . '" rows="4" cols="50" placeholder="' . esc_attr($jltma_cmnt_extra_placeholder) .  '" aria-required="' . esc_attr($required) . '"></textarea>
                            </div>
                        </div>';
                    }


                    if ($field_type == 'checkbox') {

                        $jltma_cmnt_extra_label_container = "";
                        if ($display_label == "show") {
                            $label_name = ($label_name != '') ? esc_html($label_name) : '';
                            $required_labels = isset($required_label) ? esc_attr($required_label) : "";

                            $jltma_cmnt_extra_label_container = '<div class="jltma-name-div">
                                    <label>' . $label_name . $required_labels . '</label>
                                </div>';
                        }

                        $jltma_cmnt_extra_placeholder = ($placeholder != '') ? esc_attr($placeholder) : '';

                        echo '<div class="jltma-name-value-div jltma-' . esc_attr($unique_field_id) . '">
                                ' . esc_html($jltma_cmnt_extra_label_container) . '
                            <div class="jltma-value-div mb-4 mt-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="jltma_' . esc_attr($unique_field_id) . '" id="' . esc_attr($unique_field_id) . '">
                                    <label class="form-check-label" for="jltma_' . esc_attr(strtolower($label_name)) . '"> ' . esc_attr($label_name) . '  &nbsp;</label>
                                </div>
                            </div>
                        </div>';
                    }

                    update_option('jltma_comments', $jltma_comment_fields['jltma_comment_extra_fields_items']);
                }
            }
        }



        public static function jltma_get_post_settings($settings)
        {

            $extra_fields_items = $settings['jltma_comment_extra_fields_items'];

            foreach ($extra_fields_items as $key => $value) {
                $post_args['title']                 = $value['title'];
                $post_args['label_name']            = $value['label_name'];
                $post_args['field_type']            = $value['field_type'];
                $post_args['placeholder']           = $value['placeholder'];
                $post_args['error_msg']             = $value['error_msg'];
                $post_args['required']              = $value['required'];
            }

            return $post_args;
        }

        public function jltma_loadmore_comments()
        {
            global $post, $wpdb;
            $post = get_post( isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
            setup_postdata($post);

            // actually we must copy the params from wp_list_comments() used in our theme
            $comments_list = wp_list_comments(array(
                'avatar_size'       => 100,
                'page'              => isset( $_POST['jc_page'] ) ? absint( wp_unslash( $_POST['jc_page'] ) ) : 1, // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                'per_page'          => get_option('comments_per_page'),
                'short_ping'        => true
            ));

            $this->jltma_list_comments($comments_list, $class = "", $css = "", $template = "style_one", $settings = "");

            die; // don't forget this thing if you don't want "0" to be displayed
        }


        public function jltma_comments_preview_scripts()
        {
            // Comments assets registered by Assets_Manager (jltma-comments)
            \MasterAddons\Inc\Classes\Assets_Manager::enqueue('comments');
        }

        // CSS - Uses Assets_Manager registry (jltma-comments)
        public function jltma_comments_frontend_styles()
        {
            // Comments styles registered by Assets_Manager
        }


        // JS - Uses Assets_Manager registry (jltma-comments, jltma-google-recaptcha)
        public function jltma_comments_frontend_scripts()
        {
            // Comments script registered by Assets_Manager
            // Localize when enqueued
            $jc_page = get_query_var('cpage') ? get_query_var('cpage') : 1;

            $localize_comments_data = array(
                'ajax_url'             => admin_url('admin-ajax.php'),
                'ajax_nonce'           => wp_create_nonce('jltma_frontend_ajax_nonce'),
                'empty_comment'        => esc_html__('Comment cannot be empty', 'master-addons'),
                'page_number_loader'   => \JLTMA_ASSETS . 'images/ajax-loader.gif',
                'parent_post_id'       => get_the_ID(),
                'jc_page'              => (int) $jc_page
            );
            wp_localize_script('jltma-comments', 'jltma_localize_comments_data', $localize_comments_data);
        }


        function jltma_like_dislike_action($args)
        {
            if (!empty($_POST['_wpnonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'jltma_frontend_ajax_nonce')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized before passing to wp_verify_nonce

                $comment_id = isset( $_POST['comment_id'] ) ? intval( wp_unslash( $_POST['comment_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
                if (!empty($comment_id)) {
                    $comment = get_comment($comment_id);
                    $post_id = $comment ? (int) $comment->comment_post_ID : 0;
                    $post    = $post_id ? get_post($post_id) : null;
                    if (!$post || 'publish' !== $post->post_status) {
                        echo json_encode(array('success' => false, 'message' => esc_html__('Invalid post', 'master-addons')));
                        die();
                    }
                    $type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above

                    $jltma_like_cookie = isset( $_POST['jltma_like_cookie'] ) ? sanitize_text_field( wp_unslash( $_POST['jltma_like_cookie'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
                    $jltma_dislike_cookie = isset( $_POST['jltma_dislike_cookie'] ) ? sanitize_text_field( wp_unslash( $_POST['jltma_dislike_cookie'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above

                    $total_like_count = get_comment_meta($comment_id, 'jltma_like_count', true);
                    $total_dislike_count = get_comment_meta($comment_id, 'jltma_dislike_count', true);

                    $total_like_count = (empty($total_like_count) ? 0 : $total_like_count);
                    $total_dislike_count = (empty($total_dislike_count) ? 0 : $total_dislike_count);

                    if ($type == 'like') {
                        $total_like_count = $total_like_count + 1;
                        if (!empty($jltma_dislike_cookie)) {
                            $total_dislike_count = ($total_dislike_count - 1);
                            if ($total_dislike_count < 0) {
                                $total_dislike_count = 0;
                            }
                        }
                        $check = update_comment_meta($comment_id, 'jltma_like_count', $total_like_count);
                        if ($check) {
                            update_comment_meta($comment_id, 'jltma_dislike_count', $total_dislike_count);
                            $total_like_count = self::jltma_number_format($total_like_count);
                            $total_dislike_count = self::jltma_number_format($total_dislike_count);
                            $response_array = array('success' => true, 'latest_like_count' => $total_like_count, 'latest_dislike_count' => $total_dislike_count);
                        } else {
                            $response_array = array('success' => false, 'latest_like_count' => '');
                        }
                    }
                    if ($type == 'dislike') {
                        $total_dislike_count = $total_dislike_count + 1;
                        if (!empty($jltma_like_cookie)) {
                            $total_like_count = ($total_like_count - 1);
                            if ($total_like_count < 0) {
                                $total_like_count = 0;
                            }
                        }
                        $check = update_comment_meta($comment_id, 'jltma_dislike_count', $total_dislike_count);
                        if ($check) {
                            update_comment_meta($comment_id, 'jltma_like_count', $total_like_count);
                            $total_like_count = self::jltma_number_format($total_like_count);
                            $total_dislike_count = self::jltma_number_format($total_dislike_count);
                            $response_array = array('success' => true, 'latest_like_count' => $total_like_count, 'latest_dislike_count' => $total_dislike_count);
                        } else {
                            $response_array = array('success' => false, 'latest_dislike_count' => '');
                        }
                    }
                }
                echo json_encode($response_array);
                die();
            }
        }



        // Save Comments Meta data
        function jltma_save_comment_meta_data($comment_id)
        {

            add_comment_meta($comment_id, 'jltma_like_count', 0);
            add_comment_meta($comment_id, 'jltma_dislike_count', 0);
        }


        public function jltma_strip_comment_links($content)
        {

            global $allowedtags;

            $tags = $allowedtags;
            unset($tags['a']);
            $content = addslashes(wp_kses(stripslashes($content), $tags));

            return $content;
        }


        // Allow Comments for Master Template by default
        public function jltma_comments_on_by_default($data)
        {
            if ($data['post_type'] == 'master_template') {
                $data['comment_status'] = 'open';
            }
            return $data;
        }

        //Enable Comments for Master Template
        public function jltma_enable_comments_custom_post_type()
        {
            add_post_type_support('master_template', 'comments');
        }

        public function jltma_comment_pagination($settings)
        {
            global $post;
            if (!empty($_POST['_wpnonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'jltma_frontend_ajax_nonce')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized before passing to wp_verify_nonce

                $page_number   = isset( $_POST['page_number'] ) ? intval( wp_unslash( $_POST['page_number'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
                $post_id       = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
                $ajax_template = isset( $_POST['template'] ) ? sanitize_text_field( wp_unslash( $_POST['template'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
                $sort_type     = 'default';

                $this->jltma_comment_pagination_inner($comment_listing, $page_number, $post_id, $settings);
                die();
            }
        }


        public function jltma_comment_pagination_inner($comment_listing, $page_number, $post_id, $settings)
        {

            global $wpdb;

            $page_number      = empty($page_number) ? 1 : $page_number;
            $template          = ($settings['jltma_comment_style_preset']) ? esc_attr($settings['jltma_comment_style_preset']) : 'style_one';
            $pagination      = ($settings['jltma_comment_pagination'] == "yes") ? esc_attr($settings['jltma_comment_pagination']) : 'yes';
            $items_per_page  = ($settings['jltma_comment_pagination_items']['size']) ? esc_attr($settings['jltma_comment_pagination_items']['size']) : '2';
            $pagination_type = 'page_number';
            $sort_type          = "default";

?>

            <div class="jltma-comment-list-inner">
                <?php
                $db_table_name = $wpdb->prefix . "comments";
                $comment_listing = self::jltma_recursive_array_builder(
                    $db_table_name = $wpdb->prefix . "comments",
                    $parent = 0,
                    $parent_child = true,
                    $post_id,
                    $sort_type,
                    $pagination,
                    $items_per_page,
                    $pagination_type,
                    $page_number
                );
                ?>

                <div class="jltma-comment-listing-wrapper">
                    <?php
                    $class = 'jltma-comment-list';
                    $css = "";
                    $child = 0;
                    $this->jltma_list_comments($comment_listing, $class, $css, $template, $settings);
                    ?>
                </div>
            </div>
        <?php
        }


        public static function jltma_comment_elementor_preview_mode()
        {
            return (\Elementor\Plugin::$instance->preview->is_preview_mode() || \Elementor\Plugin::$instance->editor->is_edit_mode());
        }


        public static function jltma_recursive_array_builder($db_table_name, $parent, $parent_child, $post_id, $sort_type, $pagination, $items_per_page, $pagination_type, $page_number)
        {

            global $wpdb, $post;

            $db_table_name         = $wpdb->prefix . "comments";
            $jltma_commentmeta     = $wpdb->prefix . "commentmeta";

            if ($pagination == 'yes') {
                $all_comments_approved = self::parent_comment_counter($post_id);

                /* Comments offset */
                $offset = (($page_number - 1) * $items_per_page);
                $max_num_pages = ceil($all_comments_approved / $items_per_page);
                $page_query = 'LIMIT' . ' ' . $offset . ', ' . $items_per_page;
            } else {
                $page_query = '';
            }

            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- comment-tree query: values bound via prepare(); $page_query is a LIMIT clause of sanitized integers; table name cannot be parameterized
            $jltma_comments = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $db_table_name WHERE comment_parent = %d AND comment_post_ID = %d AND comment_approved = 1 ",
                    (int) $parent,
                    (int) $post_id
                ) . $page_query
            );
            // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
            $list = array();

            if (!empty($jltma_comments)) {
                foreach ($jltma_comments as $comment) {
                    $list[] = array(
                        'author_name'     => $comment->comment_author,
                        'time'             => $comment->comment_date,
                        'comment_text'     => get_comment_text($comment->comment_ID),
                        'author_email'     => $comment->comment_author_email,
                        'gravatar'         => get_avatar_url($comment->comment_author_email),
                        'comment_id'     => $comment->comment_ID,
                        'post_id'         => $comment->comment_post_ID,
                        "child"         => ($parent_child) ? self::jltma_recursive_array_builder($db_table_name, $comment->comment_ID, true, $comment->comment_post_ID, $sort_type, $pagination, $items_per_page, $pagination_type, $page_number) : ''
                    );
                }
                return $list;
            }
        }


        public static function jltma_number_format($input)
        {
            $prev = $input;
            $input = '10M';
            $input = number_format((float) $input);
            $input_count = substr_count($input, ',');
            $arr = array(1 => 'K', 'M', 'B', 'T');
            if (isset($arr[(int) $input_count])) {
                return substr($input, 0, (-1 * $input_count) * 4) . $arr[(int) $input_count];
            } else {
                return $prev;
            }
        }


        public function jltma_list_comments($comment_listing, $class, $css, $template, $settings)
        {

            if (!empty($comment_listing)) {
                foreach ($comment_listing as $listing) {
                    $gravatar             = $listing['gravatar'];
                    $author_name         = $listing['author_name'];
                    $time                 = $listing['time'];
                    $comment_content     = $listing['comment_text'];
                    $comment_id         = $listing['comment_id'];
                    $post_id             = $listing['post_id'];

                    $this->jltma_comment_listing_html($listing, $class, $css, $template, $settings);
                }
            }


            // Demo Contents for Elementor Template Preivew
            if (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) {

                $dummy_comment_array = array(1, 2, 3, 4, 5);
                foreach ($dummy_comment_array as $key => $value) {
                    $this->jltma_comment_listing_html($listing = "", $class, $css, $template, $settings, $value);
                }
            }
        }


        public function jltma_comment_rating($comment_id = "", $settings = "")
        {

            $total_like_count         = get_comment_meta($comment_id, 'jltma_like_count', true);
            $total_like_count         = apply_filters('jltma_like_count', $total_like_count, $comment_id);
            $total_like_count         = self::jltma_number_format($total_like_count);

            $total_dislike_count     = get_comment_meta($comment_id, 'jltma_dislike_count', true);
            $total_dislike_count     = apply_filters('jltma_dislike_count', $total_dislike_count, $comment_id);
            $total_dislike_count     = self::jltma_number_format($total_dislike_count);

            $template = (isset($settings['jltma_comment_style_preset']) && $settings['jltma_comment_style_preset'] != '') ? esc_attr($settings['jltma_comment_style_preset']) : 'style_one';

            if (isset($_COOKIE['jltma_like_' . $comment_id])) {
                $liked = 'jltma-already-liked';
                $disliked = '';
            } else if (isset($_COOKIE['jltma_dislike_' . esc_attr($comment_id)])) {
                $disliked = 'jltma-already-disliked';
                $liked = '';
            } else {
                $liked = '';
                $disliked = '';
            }
        ?>

            <div class="jltma-message" id="jltma-message-<?php echo esc_attr($comment_id); ?>"></div>

            <div class="jltma-like-dislike-wrapper clearfix">
                <div class="jltma-like-wrap  jltma-common-wrap jltma-mt20">
                    <a href="javascript:void(0);" class="jltma-like-trigger jltma-like-dislike-trigger <?php echo esc_attr($liked); ?>" data-comment-id="<?php echo esc_attr($comment_id); ?>" data-trigger-type="like" title="like">
                        <?php $likeicon = 'fa fa-thumbs-o-up'; ?>
                        <span class="<?php echo esc_attr($likeicon); ?> jltma-liked-wrap"> </span>
                    </a>
                    <div class="jltma-count-wrap  jltma-common-wrap ">
                        <span class="jltma-like-count-wrap jltma-count-wrapper" id="jltma-like-count-<?php echo esc_attr($comment_id); ?>">
                            <?php echo (empty($total_like_count)) ? 0 : esc_attr($total_like_count); ?>
                        </span>

                    </div>
                </div>
                <div class="jltma-dislike-wrap  jltma-common-wrap jltma-mt20 jltma-mr20">
                    <a href="javascript:void(0);" class="jltma-dislike-trigger jltma-like-dislike-trigger <?php echo esc_attr($disliked); ?> " data-comment-id="<?php echo esc_attr($comment_id); ?>" data-trigger-type="dislike" title="dislike">
                        <?php $dislikeicon = 'fa fa-thumbs-o-down'; ?>
                        <span class="<?php echo esc_attr($dislikeicon); ?> jltma-disliked-wrap"></span>
                    </a>
                    <div class="jltma-count-wrap  jltma-common-wrap ">
                        <span class="jltma-dislike-count-wrap jltma-count-wrapper" id="jltma-dislike-count-<?php echo esc_attr($comment_id); ?>">
                            <?php echo (empty($total_dislike_count)) ? 0 : esc_attr($total_dislike_count); ?>
                        </span>
                    </div>
                </div>
            </div>


        <?php }


        public function jltma_comment_templates($listing, $class, $css, $template, $settings, $demo_comment_id = "")
        {

            $class              = "img-rounded";
            $comment_id         = isset($listing['comment_id']) ? $listing['comment_id'] : '';

            $hide_replies         = ($settings['jltma_comment_replies']) ? $settings['jltma_comment_replies'] : '';

            $show_reply_label     = ($settings['jltma_comment_show_reply_label']) ? esc_attr($settings['jltma_comment_show_reply_label']) : esc_html__('Show Replies', 'master-addons');

            $hide_reply_label     = ($settings['jltma_comment_hide_reply_label']) ? esc_attr($settings['jltma_comment_hide_reply_label']) : esc_html__('Hide Replies', 'master-addons');

            $reply_button_label = ($settings['jltma_comment_reply_label']) ? esc_attr($settings['jltma_comment_reply_label']) : esc_html__('Reply', 'master-addons');

            $show_gravatar = ($settings['jltma_comment_gravatar']) ? $settings['jltma_comment_gravatar'] : "";

        ?>



            <?php if ($show_gravatar == "show") { ?>

                <div class="jltma-comment-gravatar">

                    <?php if (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) { ?>
                        <img class="<?php echo esc_attr($class); ?>" src="<?php echo esc_url(class_exists('\Elementor\Utils') ? \Elementor\Utils::get_placeholder_image_src() : ''); ?>" alt="<?php esc_attr_e('A WordPress Commenter', 'master-addons'); ?>">
                    <?php } else { ?>
                        <img class="<?php echo esc_attr($class); ?>" scr="<?php echo esc_url($listing['gravatar']); ?>" srcset="<?php echo esc_attr($listing['gravatar']); ?>">
                    <?php } ?>
                </div>

            <?php } ?>

            <div class="jltma-body media-body pl-3">

                <div class="jltma-title-date clearfix">
                    <div class="jltma-author-name">
                        <?php
                        if (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) {
                            echo esc_html__('A WordPress Commenter', 'master-addons');
                        } else {
                            echo esc_html($listing['author_name']);
                        } ?>
                    </div>
                    <div class="jltma-date-time" data-time="<?php echo esc_attr( get_the_modified_date('c') ); ?>">
                        <?php
                        if (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) {
                            echo esc_html( get_the_time('j M Y g:ia') );
                        } else {

                            $date                       = date_create($listing['time']);
                            $jltma_comment_date_time    = date_format($date, 'j M Y g:ia');
                            $comments_time_type         = ($settings['jltma_comments_time_type'] === 'custom') ? date_format($date, $settings['jltma_comments_time_format']) : $jltma_comment_date_time;
                        ?>
                            <div class="jltma-date">
                                <?php echo esc_html($comments_time_type); ?>
                            </div>
                        <?php }  ?>
                    </div>
                </div>

                <div class="jltma-comment jltma-comment-content-<?php echo esc_attr($comment_id); ?>" id="jltma-comment-<?php echo esc_attr($comment_id); ?>">

                    <?php
                    if (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) {
                        echo wp_kses_post( wp_specialchars_decode('Hi, this is a comment. <br>
								To get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.<br>
								Commenter avatars come from Gravatar.') );
                    } else {
                        $comment = get_comment($comment_id);
                        comment_text($comment_id);
                    }
                    ?>


                    <?php
                    if ($settings['jltma_comment_ratings'] == "show") {
                        if (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) {
                            $this->jltma_comment_rating();
                        } else {
                            $this->jltma_comment_rating($comment_id, $settings);
                        }
                    }
                    ?>
                </div>

                <div class="jltma-comment-footer clearfix">
                    <?php
                    $args = array('reply_text' => $reply_button_label, 'depth' => 1, 'max_depth' => 10, 'add_below' => "jltma-unique-comment");

                    if (comments_open()) {
                        if (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) {
                            echo '<div class="jltma-reply-button">';
                            echo esc_html($settings['jltma_comment_reply_label']);
                            echo '</div>';
                        } else {
                            echo '<div class="jltma-reply-button">';
                            comment_reply_link($args, $comment_id, get_the_ID());
                            echo '</div>';
                        }
                    }

                    if (!empty($listing['child'])) {
                        $children = $listing['child'];
                    } else {
                        $children = null;
                    }

                    if (!empty($children)) {

                        if ($hide_replies == 'show') { ?>
                            <a href="javascript:void(0);" class="jltma-show-replies-trigger jltma-show-reply-trigger-<?php echo esc_attr($comment_id); ?>" data-comment-id="<?php echo esc_attr($comment_id); ?>">
                                <?php echo esc_html($show_reply_label); ?>
                            </a>

                            <a href="javascript:void(0);" class="jltma-hide-replies-trigger jltma-hide-reply-trigger-<?php echo esc_attr($comment_id); ?>" data-comment-id="<?php echo esc_attr($comment_id); ?>" style="display:none;"> <?php echo esc_html($hide_reply_label); ?> </a> <?php
                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                    } elseif (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) {

                                                                                                                                                                                                                                                                                        if ($demo_comment_id % 2 != 0 && $demo_comment_id != 5) { ?>

                            <a href="javascript:void(0);" class="jltma-show-replies-trigger jltma-show-reply-trigger-<?php echo esc_attr($comment_id); ?>" data-comment-id="<?php echo esc_attr($comment_id); ?>">
                                <?php echo esc_html($show_reply_label); ?>
                            </a>

                            <a href="javascript:void(0);" class="jltma-hide-replies-trigger jltma-hide-reply-trigger-<?php echo esc_attr($comment_id); ?>" data-comment-id="<?php echo esc_attr($comment_id); ?>" style="display:none;"> <?php echo esc_html($hide_reply_label); ?> </a>
                    <?php }
                                                                                                                                                                                                                                                                                    } ?>
                </div>
            </div>

            <?php
        }


        public static function parent_comment_counter($post_id)
        {
            global $wpdb;
            $db_table_name = $wpdb->prefix . "comments";
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- comment-count query: post ID bound via prepare(); table name cannot be parameterized
            $parents = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT COUNT(comment_post_id) AS count FROM $db_table_name WHERE comment_approved = 1 AND comment_post_ID = %d AND comment_parent = 0",
                    (int) $post_id
                )
            );
            // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
            return $parents->count;
        }


        public function jltma_comment_listing_html($comment_listing, $class, $css, $template, $settings, $demo_comment_id = 0)
        {

            $comment_id         = isset($comment_listing['comment_id']) ? $comment_listing['comment_id'] : '';
            $hide_replies         = ($settings['jltma_comment_replies'] == 'show') ? 'show' : '';
            $css = "style='display:block;'";

            // Demo Contents for Elementor Template Preivew
            if (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) {

                if ($demo_comment_id % 2 != 0) { ?>
                    <ul class="<?php echo esc_attr($class); ?> " data-comment-id="<?php echo esc_attr($demo_comment_id); ?>" <?php echo esc_attr($css); ?>>
                    <?php } else {

                    if ($hide_replies == 'show') {
                        $css = "style='display:none;'";
                    } else {
                        $css = "style='display:block;'";
                    }
                    ?>
                        <ul class="jltma-children <?php echo esc_attr($class); ?> " data-comment-id="<?php echo esc_attr($demo_comment_id); ?>" <?php echo esc_attr($css); ?>>
                        <?php } ?>

                    <?php } else { ?>
                        <ul class="<?php echo esc_attr($class); ?> " data-comment-id="<?php echo esc_attr($comment_id); ?>" <?php echo esc_attr($css); ?>>
                        <?php } ?>


                        <?php if ($template == 'style_one' || $template == 'style_two' || $template == 'style_four') {
                            $clearfix = 'clearfix';
                        } else {
                            $clearfix = '';
                        } ?>

                        <?php
                        // Demo Contents for Elementor Template Preivew
                        if (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) { ?>
                            <div class="jltma-comment-template media jltma-comment-<?php echo esc_attr($template); ?> <?php echo esc_attr($clearfix); ?>" id="jltma-unique-comment-<?php echo esc_attr($demo_comment_id); ?>">
                            <?php } else { ?>
                                <div class="jltma-comment-template media jltma-comment-<?php echo esc_attr($template); ?> <?php echo esc_attr($clearfix); ?>" id="jltma-unique-comment-<?php echo esc_attr($comment_id);; ?>">
                                <?php } ?>

                                <?php
                                $this->jltma_comment_templates($comment_listing, $c = "", $css, $template, $settings, $demo_comment_id);
                                ?>
                                </div>

                    <?php
                    if (!empty($comment_listing['child'])) {
                        $children = $comment_listing['child'];
                    } else {
                        $children = null;
                    }

                    if (!empty($children)) {

                        $c = 'jltma-children' . ' ' . 'jltma-comment-list';

                        if ($hide_replies == 'show') {
                            $css = "style='display:none;'";
                        } else {
                            $css = "style='display:block;'";
                        }
                        $this->jltma_list_comments($children, $c, $css, $template, $settings);
                    } else {
                        $css = "style='display:block;'";
                    }

                    if (is_user_logged_in() && Comments_Builder::jltma_comment_elementor_preview_mode()) {

                        if ($demo_comment_id % 2 != 0) {
                            return;
                        } else {
                            echo "</ul></ul>";
                        }
                    } else {
                        echo "</ul>";
                    }
                }

                /**
                 * Google recaptcha check, validate and catch the spammer
                 */
                public function jltma_is_valid_captcha($captcha)
                {
                    $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
                        'timeout' => 15,
                        'body'    => array(
                            'secret'   => $this->jltma_api_settings['recaptcha_secret_key'],
                            'response' => $captcha,
                            'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '',
                        ),
                    ));

                    if (is_wp_error($response)) {
                        return false;
                    }

                    $captcha_response = json_decode(wp_remote_retrieve_body($response), true);

                    return ! empty($captcha_response['success']);
                }


                public function jltma_verify_google_recaptcha()
                {
                    $jltma_comment_recaptha = $this->jltma_set_var;
                    if (isset($jltma_comment_recaptha['jltma_comment_spam_protection']) && $jltma_comment_recaptha['jltma_comment_spam_protection'] == "yes") {
                        $recaptcha = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for comment spam protection hook
                        if (empty($recaptcha))
                            wp_die( wp_kses_post( __("<b>ERROR:</b> please select <b>I'm not a robot!</b><p><a href='javascript:history.back()'>« Back</a></p>", "master-addons") ) );
                        else if (!$this->jltma_is_valid_captcha($recaptcha))
                            wp_die( '<b>' . esc_html__('Go away SPAMMERsss!', 'master-addons') . '</b>' );
                    }
                }


                // Enable/Disable Comments for Post Types
                public function jltma_comments_open($open, $post_id = 0)
                {

                    // post types without comments
                    $closed_comments_post_types = array('page', 'attachment');

                    // is the current post type among the ones without comments?
                    if (in_array(get_post_type(), $closed_comments_post_types)) return false;
                    return $open;
                }

                public static function get_instance()
                {
                    if (is_null(self::$_instance)) {
                        self::$_instance = new self();
                    }
                    return self::$_instance;
                }
            }
            Comments_Builder::get_instance();
        }
