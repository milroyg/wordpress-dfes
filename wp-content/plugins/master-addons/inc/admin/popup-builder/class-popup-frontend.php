<?php
namespace MasterAddons\Inc\Admin\PopupBuilder;

use MasterAddons\Inc\Classes\Assets_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Popup_Frontend {

    private static $instance = null;
    private $conditions_checker;
    private $displayed_popups = [];

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // Initialize conditions checker
        add_action('init', function(){
            $this->conditions_checker = new Popup_Conditions();
        });
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('wp_footer', [$this, 'render_popups']);
        add_action('wp_ajax_jltma_popup_track_view', [$this, 'track_popup_view']);
        add_action('wp_ajax_nopriv_jltma_popup_track_view', [$this, 'track_popup_view']);
        add_action('wp_ajax_jltma_popup_track_conversion', [$this, 'track_popup_conversion']);
        add_action('wp_ajax_nopriv_jltma_popup_track_conversion', [$this, 'track_popup_conversion']);
        add_action('wp_ajax_jltma_popup_disable_expired', [$this, 'disable_expired_popup']);
        add_action('wp_ajax_nopriv_jltma_popup_disable_expired', [$this, 'disable_expired_popup']);
        add_action('wp_head', [$this, 'set_visitor_cookie']);
    }

    public function enqueue_frontend_assets() {
        $popups = $this->get_active_popups_cpt();

        // Enqueue popup builder frontend assets via Assets Manager
        Assets_Manager::enqueue('popup-builder-frontend');

        wp_localize_script('jltma-popup-builder-frontend', 'jltma_popup_frontend', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jltma_popup_frontend'),
            'popups' => $this->prepare_popups_data($popups)
        ]);

        // Pro: enqueue additional popup frontend scripts (triggers, etc.)
        do_action('master_addons/popup_builder/enqueue_frontend_scripts');
    }

    public function render_popups() {
        // Get all published popup posts
        $args = [
            'post_type' => 'jltma_popup',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'ASC',
        ];

        $popups = get_posts($args);

        if (empty($popups) || !$popups) {
            return;
        }

        foreach ($popups as $popup) {
            // Check conditions before rendering
            if ($this->should_display_popup($popup->ID)) {
                $this->render_single_popup($popup->ID);
                $this->displayed_popups[] = $popup->ID;
            }
        }
    }

    public function get_displayed_popups() {
        return $this->displayed_popups;
    }

    private function should_display_popup($popup_id) {
        // First check activation status
        $activation_status = get_post_meta($popup_id, '_jltma_popup_activation', true);
        if ($activation_status !== 'yes') {
            return false;
        }

        // Check if popup has expired based on automatic disable settings
        $elementor_settings = get_post_meta($popup_id, '_elementor_page_settings', true);

        // Check if automatic disable is enabled and if the expiration date has passed
        if (!empty($elementor_settings['popup_disable_automatic']) && $elementor_settings['popup_disable_automatic'] === 'yes') {
            if (!empty($elementor_settings['popup_disable_after'])) {
                $expiration_date = strtotime($elementor_settings['popup_disable_after']);
                $current_time = current_time('timestamp');

                // If current time is past the expiration date, don't display the popup
                if ($current_time > $expiration_date) {
                    // Deactivate the popup and reset automatic disable settings
                    update_post_meta($popup_id, '_jltma_popup_activation', 'no');

                    // Reset the automatic disable settings in Elementor page settings
                    if (!empty($elementor_settings)) {
                        $elementor_settings['popup_disable_automatic'] = 'no';
                        update_post_meta($popup_id, '_elementor_page_settings', $elementor_settings);
                    }

                    return false;
                }
            }
        }

        // Get popup conditions from meta
        $conditions_data = get_post_meta($popup_id, '_jltma_popup_conditions_data', true);

        // If no conditions set, don't display
        if (empty($conditions_data)) {
            return false;
        }

        // Unserialize if needed
        if (is_string($conditions_data)) {
            $conditions_data = maybe_unserialize($conditions_data);
        }

        // Exclude conditions always win, regardless of their order relative to
        // includes, so includes only resolve after the whole set is scanned.
        $has_include     = false;
        $include_matched = false;

        // Check each condition
        foreach ($conditions_data as $condition) {
            $type = $condition['type'] ?? 'include';
            $rule = $condition['rule'] ?? '';
            $specific = $condition['specific'] ?? '';
            // Pro "Singular/Archive" schema stores target IDs in a separate `posts` array,
            // with `specific` holding the sub-type (page/post/CPT slug).
            $posts = isset($condition['posts']) ? (array) $condition['posts'] : [];

            $condition_met = false;

            // Check different rule types
            switch ($rule) {
                case 'entire_site':
                    $condition_met = true;
                    break;

                // Pro condition schema: rule=singular, specific=<page|post|CPT>, posts=<IDs|empty=all>
                case 'singular':
                    $target_ids = array_filter(array_map('intval', $posts));
                    if ($specific === 'page') {
                        $condition_met = empty($target_ids) ? is_page() : is_page($target_ids);
                    } elseif ($specific === 'post') {
                        $condition_met = empty($target_ids) ? is_single() : is_single($target_ids);
                    } elseif (!empty($specific)) {
                        $condition_met = empty($target_ids)
                            ? is_singular($specific)
                            : (is_singular($specific) && in_array((int) get_the_ID(), $target_ids, true));
                    } else {
                        $condition_met = is_singular();
                    }
                    break;

                case 'archive':
                    $condition_met = is_archive();
                    break;

                case 'search':
                    $condition_met = is_search();
                    break;

                case '404':
                    $condition_met = is_404();
                    break;

                case 'front_page':
                    $condition_met = is_front_page();
                    break;

                case 'home_page':
                    $condition_met = is_home();
                    break;

                case 'all_pages':
                    $condition_met = is_page();
                    break;

                case 'all_posts':
                    $condition_met = is_single();
                    break;

                case 'all_archives':
                    $condition_met = is_archive();
                    break;

                case 'search_results':
                    $condition_met = is_search();
                    break;

                case '404_page':
                    $condition_met = is_404();
                    break;

                case 'specific_pages':
                    if (!empty($specific)) {
                        $page_ids = is_array($specific) ? $specific : explode(',', $specific);
                        $condition_met = is_page($page_ids);
                    }
                    break;

                case 'specific_posts':
                    if (!empty($specific)) {
                        $post_ids = is_array($specific) ? $specific : explode(',', $specific);
                        $condition_met = is_single($post_ids);
                    }
                    break;

                case 'post_categories':
                    if (!empty($specific)) {
                        $category_ids = is_array($specific) ? $specific : explode(',', $specific);
                        $condition_met = is_category($category_ids) || (is_single() && has_category($category_ids));
                    }
                    break;

                case 'post_tags':
                    if (!empty($specific)) {
                        $tag_ids = is_array($specific) ? $specific : explode(',', $specific);
                        $condition_met = is_tag($tag_ids) || (is_single() && has_tag($tag_ids));
                    }
                    break;

                case 'woo_shop':
                    if (class_exists('WooCommerce')) {
                        $condition_met = is_shop();
                    }
                    break;

                case 'woo_product':
                    if (class_exists('WooCommerce')) {
                        $condition_met = is_product();
                    }
                    break;

                case 'woo_cart':
                    if (class_exists('WooCommerce')) {
                        $condition_met = is_cart();
                    }
                    break;

                case 'woo_checkout':
                    if (class_exists('WooCommerce')) {
                        $condition_met = is_checkout();
                    }
                    break;
            }

            // Handle include/exclude logic
            if ($type === 'exclude') {
                // An exclude match wins immediately and hides the popup.
                if ($condition_met) {
                    return false;
                }
            } else {
                // Defer the include decision until every exclude has been checked.
                $has_include = true;
                if ($condition_met) {
                    $include_matched = true;
                }
            }
        }

        // Only display if at least one include condition was defined and met.
        return $has_include && $include_matched;
    }

    private function render_single_popup($popup_id) {
        // Get Elementor content
        $elementor_content = \Elementor\Plugin::instance()->frontend->get_builder_content($popup_id, false);

        if (empty($elementor_content)) {
            return;
        }

        // Get popup settings from Elementor page settings
        $elementor_settings = get_post_meta($popup_id, '_elementor_page_settings', true);
        if (empty($elementor_settings)) {
            $elementor_settings = [];
        }

        // Prepare popup settings for JavaScript
        $popup_settings = [
            // Trigger settings
            'popup_trigger' => $elementor_settings['popup_trigger'] ?? 'page-load',
            'popup_load_delay' => $elementor_settings['popup_load_delay'] ?? 1,
            'popup_scroll_progress' => isset($elementor_settings['popup_scroll_progress']['size']) ? $elementor_settings['popup_scroll_progress']['size'] : ($elementor_settings['popup_scroll_progress'] ?? 50),
            'popup_element_scroll' => $elementor_settings['popup_element_scroll'] ?? '',
            'popup_specific_date' => $elementor_settings['popup_specific_date'] ?? '',
            'popup_inactivity_time' => $elementor_settings['popup_inactivity_time'] ?? 15,
            'popup_custom_trigger' => $elementor_settings['popup_custom_trigger'] ?? '',
            'popup_click_trigger' => $elementor_settings['popup_click_trigger'] ?? '',

            // Display settings
            'popup_display_as' => $elementor_settings['popup_display_as'] ?? 'modal',
            'popup_position' => $elementor_settings['popup_position'] ?? 'center-center',
            'popup_animation' => $elementor_settings['popup_animation'] ?? 'jltma-anim-fade-in',
            'popup_animation_duration' => $elementor_settings['popup_animation_duration'] ?? 400,
            'popup_custom_positioning' => $elementor_settings['popup_custom_positioning'] ?? '',

            // Close settings
            'popup_show_close_button' => $elementor_settings['popup_show_close_button'] ?? 'yes',
            'popup_close_button_position' => $elementor_settings['popup_close_button_position'] ?? 'top-right',
            'popup_close_button_display_delay' => $elementor_settings['popup_close_button_display_delay'] ?? 0,
            'popup_automatic_close_switch' => $elementor_settings['popup_automatic_close_switch'] ?? 'no',
            'popup_automatic_close_delay' => $elementor_settings['popup_automatic_close_delay'] ?? 10,
            'popup_close_on_overlay' => $elementor_settings['popup_close_on_overlay'] ?? 'yes',
            'popup_close_esc_key' => $elementor_settings['popup_close_esc_key'] ?? 'yes',
            'popup_show_overlay' => $elementor_settings['popup_show_overlay'] ?? 'yes',

            // Behavior settings
            'popup_disable_page_scroll' => $elementor_settings['popup_disable_page_scroll'] ?? 'yes',
            'popup_show_again_delay' => $elementor_settings['popup_show_again_delay'] ?? 'no-delay',
            'popup_show_on_device' => $elementor_settings['popup_show_on_device'] ?? 'yes',
            'popup_show_on_device_tablet' => $elementor_settings['popup_show_on_device_tablet'] ?? 'yes',
            'popup_show_on_device_mobile' => $elementor_settings['popup_show_on_device_mobile'] ?? 'yes',

            // Advanced settings
            'popup_stop_after_date' => $elementor_settings['popup_stop_after_date'] ?? 'no',
            'popup_stop_after_date_select' => $elementor_settings['popup_stop_after_date_select'] ?? '',
            'popup_show_via_referral' => $elementor_settings['popup_show_via_referral'] ?? 'no',
            'popup_referral_keyword' => $elementor_settings['popup_referral_keyword'] ?? '',

            // Automatic disable settings
            'popup_disable_automatic' => $elementor_settings['popup_disable_automatic'] ?? 'no',
            'popup_disable_after' => $elementor_settings['popup_disable_after'] ?? '',
        ];

        // Free: restrict Pro features. Pro hooks to return original settings.
        $free_settings = $popup_settings;
        $free_settings['popup_trigger'] = 'page-load';
        $free_settings['popup_close_esc_key'] = 'yes';
        $free_settings['popup_disable_automatic'] = 'no';

        // Restrict show again delay to free options only
        $free_delay_options = ['no-delay', '1-minute', '3-minutes', '5-minutes'];
        if (!in_array($free_settings['popup_show_again_delay'], $free_delay_options, true)) {
            $free_settings['popup_show_again_delay'] = 'no-delay';
        }

        $popup_settings = apply_filters('master_addons/popup_builder/frontend_settings', $free_settings, $popup_settings);

        // Add position class
        $position_class = 'jltma-popup-position-' . str_replace('_', '-', $popup_settings['popup_position']);

        // Add custom positioning class if enabled
        if (!empty($elementor_settings['popup_custom_positioning']) && $elementor_settings['popup_custom_positioning'] === 'yes') {
            $position_class .= ' jltma-popup-custom-position';
        }

        // Encode settings for data attribute
        $encoded_settings = wp_json_encode($popup_settings);

        // Don't render in preview mode (editor handles it differently)
        if (\Elementor\Plugin::instance()->preview->is_preview_mode()) {
            return;
        }

        // Render popup HTML structure (matching Royal Addons pattern with jltma- prefix)
        ?>
        <div id="jltma-popup-id-<?php echo esc_attr($popup_id); ?>" class="jltma-template-popup <?php echo esc_attr($position_class); ?>" data-settings='<?php echo esc_attr($encoded_settings); ?>'>
            <div class="jltma-template-popup-inner">

                <!-- Popup Overlay -->
                <div class="jltma-popup-overlay"></div>

                <!-- Template Container -->
                <div class="jltma-popup-container">

                    <?php
                    // Show close button by default or when explicitly set to 'yes'
                    $show_close_button = !isset($elementor_settings['popup_show_close_button']) || $elementor_settings['popup_show_close_button'] === 'yes';
                    if ($show_close_button) :
                    ?>
                        <!-- Close Button -->
                        <div class="jltma-popup-close-btn jltma-popup-close-<?php echo esc_attr($elementor_settings['popup_close_button_position'] ?? 'top-right'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </div>
                    <?php endif; ?>

                    <!-- Elementor Template Content -->
                    <div class="jltma-popup-container-inner">
                        <?php echo $elementor_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>

                </div>

            </div>
        </div>
        <?php
    }

    private function get_active_popups_cpt() {
        $args = [
            'post_type' => 'jltma_popup',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => '_jltma_popup_status',
                    'value' => 'active',
                    'compare' => '='
                ]
            ]
        ];

        $posts = get_posts($args);
        $popups = [];

        foreach ($posts as $post) {
            $popups[] = [
                'id' => $post->ID,
                'name' => $post->post_title,
                'type' => get_post_meta($post->ID, '_jltma_popup_type', true) ?: 'modal',
                'settings' => get_post_meta($post->ID, '_jltma_popup_settings', true) ?: [],
                'conditions' => get_post_meta($post->ID, '_jltma_popup_conditions', true) ?: []
            ];
        }

        return $popups;
    }

    private function prepare_popups_data($popups) {
        $data = [];

        foreach ($popups as $popup) {
            if (!$this->conditions_checker->check_conditions($popup['id'])) {
                continue;
            }

            // Settings might be JSON string or array
            $settings = is_string($popup['settings']) ? json_decode($popup['settings'], true) : $popup['settings'];
            if (!$settings) {
                $settings = [];
            }

            $data[] = [
                'id' => $popup['id'],
                'type' => $popup['type'],
                'trigger' => 'load',
                'trigger_delay' => 0,
                'trigger_scroll_percent' => $settings['trigger_scroll_percent'] ?? 50,
                'trigger_element' => $settings['trigger_element'] ?? '',
                'show_frequency' => $settings['show_frequency'] ?? 'always',
                'close_on_overlay' => $settings['close_on_overlay'] ?? true,
                'close_on_esc' => $settings['close_on_esc'] ?? true,
                'prevent_scroll' => $settings['prevent_scroll'] ?? true,
                'auto_close' => $settings['popup_automatic_close_switch'] ?? false,
                'auto_close_delay' => ($settings['popup_automatic_close_delay'] ?? 10) * 1000,
            ];
        }

        return $data;
    }

    public function track_popup_view() {
        check_ajax_referer('jltma_popup_frontend', 'nonce');

        $popup_id = isset( $_POST['popup_id'] ) ? intval( $_POST['popup_id'] ) : 0;

        if (!$popup_id) {
            wp_send_json_error();
        }

        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== 'jltma_popup' || 'publish' !== $popup->post_status) {
            wp_send_json_error(['message' => esc_html__('Invalid popup', 'master-addons')]);
        }

        // Update views count in post meta
        $current_views = get_post_meta($popup_id, '_jltma_popup_views', true);
        $current_views = $current_views ? intval($current_views) : 0;
        update_post_meta($popup_id, '_jltma_popup_views', $current_views + 1);

        // Set cookie to track frequency
        $this->set_popup_cookie($popup_id);

        // Track in analytics if enabled
        do_action('jltma_popup_view_tracked', $popup_id);

        wp_send_json_success();
    }

    public function track_popup_conversion() {
        check_ajax_referer('jltma_popup_frontend', 'nonce');

        $popup_id = isset( $_POST['popup_id'] ) ? intval( $_POST['popup_id'] ) : 0;

        if (!$popup_id) {
            wp_send_json_error();
        }

        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== 'jltma_popup' || 'publish' !== $popup->post_status) {
            wp_send_json_error(['message' => esc_html__('Invalid popup', 'master-addons')]);
        }

        // Update conversions count in post meta
        $current_conversions = get_post_meta($popup_id, '_jltma_popup_conversions', true);
        $current_conversions = $current_conversions ? intval($current_conversions) : 0;
        update_post_meta($popup_id, '_jltma_popup_conversions', $current_conversions + 1);

        // Track in analytics if enabled
        do_action('jltma_popup_conversion_tracked', $popup_id);

        wp_send_json_success();
    }

    private function set_popup_cookie($popup_id) {
        $popup = $this->get_popup_settings($popup_id);

        if (!$popup) {
            return;
        }

        // Settings might be JSON string or array
        $settings = is_string($popup['settings']) ? json_decode($popup['settings'], true) : $popup['settings'];
        $frequency = $settings['show_frequency'] ?? 'always';

        $cookie_name = 'jltma_popup_shown_' . $popup_id;
        $expiration = 0;

        switch ($frequency) {
            case 'once_day':
                $expiration = time() + DAY_IN_SECONDS;
                break;
            case 'once_week':
                $expiration = time() + WEEK_IN_SECONDS;
                break;
            case 'once':
                $expiration = time() + (30 * DAY_IN_SECONDS);
                break;
        }

        if ($expiration > 0) {
             $max_age = $expiration - time();
        
            $cookie_js = sprintf(
                'document.cookie = "%1$s=%2$d; max-age=%3$d; path=/;";',
                esc_js($cookie_name),
                absint(time()),
                (int) $max_age
            );
            $this->add_popup_inline_script($cookie_js);
        }

        if ($frequency === 'once_session') {
            // Session cookie (no max-age/expires) — cleared automatically when the browser closes.
            // Avoids PHP sessions, which break full-page caching on the frontend.
            $session_js = sprintf(
                'document.cookie = "%1$s=%2$d; path=/;";',
                esc_js($cookie_name),
                absint(time())
            );
            $this->add_popup_inline_script($session_js);
        }
    }

    // public function set_visitor_cookie() {
    //     if (!isset($_COOKIE['jltma_popup_visitor'])) {
    //         setcookie('jltma_popup_visitor', '1', time() + (365 * DAY_IN_SECONDS), '/');
    //     }
    // }
    
    public function set_visitor_cookie() {
        // Skip on admin, Elementor preview/editor, and if cookie already set
        if ( is_feed() || is_admin() || isset($_COOKIE['jltma_popup_visitor']) || isset($_GET['elementor-preview']) || ( defined('ELEMENTOR_VERSION') && \Elementor\Plugin::$instance->preview->is_preview_mode() ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only existence check, no data processed
            return;
        }
        $this->add_popup_inline_script('if (document.cookie.indexOf("jltma_popup_visitor=") === -1) { document.cookie = "jltma_popup_visitor=1; max-age=31536000; path=/;"; }');
    }

    /**
     * Queue a small inline script through the enqueue API (printed in the footer)
     * instead of echoing a hardcoded <script> tag. Multiple calls accumulate on the
     * same inline-only handle.
     *
     * @param string $js
     */
    private function add_popup_inline_script($js) {
        if (!wp_script_is('jltma-popup-inline', 'registered')) {
            wp_register_script('jltma-popup-inline', false, array(), JLTMA_VER, true);
        }
        if (!wp_script_is('jltma-popup-inline', 'enqueued')) {
            wp_enqueue_script('jltma-popup-inline');
        }
        wp_add_inline_script('jltma-popup-inline', $js);
    }

    private function get_popup_settings($popup_id) {
        // Get popup post
        $popup_post = get_post($popup_id);

        if (!$popup_post || $popup_post->post_type !== 'jltma_popup') {
            return false;
        }

        // Get settings from post meta
        $popup = [
            'id' => $popup_id,
            'name' => $popup_post->post_title,
            'type' => get_post_meta($popup_id, '_jltma_popup_type', true) ?: 'modal',
            'status' => get_post_meta($popup_id, '_jltma_popup_status', true) ?: 'active',
            'settings' => get_post_meta($popup_id, '_jltma_popup_settings', true) ?: [],
            'conditions' => get_post_meta($popup_id, '_jltma_popup_conditions', true) ?: []
        ];

        return $popup;
    }

    // private function create_database_table() {
    //     global $wpdb;

    //     $table_name = $wpdb->prefix . 'jltma_popups';
    //     $charset_collate = $wpdb->get_charset_collate();

    //     $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    //         id mediumint(9) NOT NULL AUTO_INCREMENT,
    //         name varchar(255) NOT NULL,
    //         type varchar(50) NOT NULL,
    //         content longtext NOT NULL,
    //         settings longtext,
    //         status varchar(20) DEFAULT 'active',
    //         views int DEFAULT 0,
    //         conversions int DEFAULT 0,
    //         created_at datetime DEFAULT CURRENT_TIMESTAMP,
    //         updated_at datetime DEFAULT CURRENT_TIMESTAMP,
    //         PRIMARY KEY (id)
    //     ) $charset_collate;";

    //     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    //     dbDelta($sql);
    // }

    public function disable_expired_popup() {
        // Verify nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'jltma_popup_frontend')) {
            wp_die('Security check failed');
        }

        if (!isset($_POST['popup_id'])) {
            wp_send_json_error('Popup ID not provided');
        }

        $popup_id = intval($_POST['popup_id']);

        // Validate target is a real popup post. The publish status is intentionally
        // not enforced here because this handler disables expired popups, which may
        // already be in a non-publish state.
        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== 'jltma_popup') {
            wp_send_json_error(['message' => esc_html__('Invalid popup', 'master-addons')]);
        }

        // Deactivate the popup
        update_post_meta($popup_id, '_jltma_popup_activation', 'no');

        // Reset the automatic disable settings in Elementor page settings
        $elementor_settings = get_post_meta($popup_id, '_elementor_page_settings', true);
        if (!empty($elementor_settings)) {
            $elementor_settings['popup_disable_automatic'] = 'no';
            update_post_meta($popup_id, '_elementor_page_settings', $elementor_settings);
        }

        wp_send_json_success([
            'message' => 'Popup has been disabled due to expiration',
            'popup_id' => $popup_id
        ]);
    }
}
