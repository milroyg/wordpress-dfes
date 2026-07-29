<?php
namespace MasterAddons\Inc\Admin\PopupBuilder;

if (!defined('ABSPATH')) {
    exit;
}

class Popup_Conditions {
    
    private $conditions = [];
    
    public function __construct() {
        $this->init_conditions();
    }
    
    private function init_conditions() {
        $this->conditions = [
            'pages' => [
                'label' => __('Pages', 'master-addons'),
                'options' => [
                    'all' => __('All Pages', 'master-addons'),
                    'home' => __('Home Page', 'master-addons'),
                    'blog' => __('Blog Page', 'master-addons'),
                    'archive' => __('Archive Pages', 'master-addons'),
                    'search' => __('Search Results', 'master-addons'),
                    '404' => __('404 Page', 'master-addons'),
                    'specific' => __('Specific Pages', 'master-addons'),
                ]
            ],
            'posts' => [
                'label' => __('Posts', 'master-addons'),
                'options' => [
                    'all_posts' => __('All Posts', 'master-addons'),
                    'specific_posts' => __('Specific Posts', 'master-addons'),
                    'post_categories' => __('Post Categories', 'master-addons'),
                    'post_tags' => __('Post Tags', 'master-addons'),
                ]
            ],
            'users' => [
                'label' => __('User Status', 'master-addons'),
                'options' => [
                    'all_users' => __('All Users', 'master-addons'),
                    'logged_in' => __('Logged In', 'master-addons'),
                    'logged_out' => __('Logged Out', 'master-addons'),
                    'first_time' => __('First Time Visitors', 'master-addons'),
                    'returning' => __('Returning Visitors', 'master-addons'),
                ]
            ],
            'devices' => [
                'label' => __('Devices', 'master-addons'),
                'options' => [
                    'all_devices' => __('All Devices', 'master-addons'),
                    'desktop' => __('Desktop', 'master-addons'),
                    'tablet' => __('Tablet', 'master-addons'),
                    'mobile' => __('Mobile', 'master-addons'),
                ]
            ],
            'referrer' => [
                'label' => __('Referrer', 'master-addons'),
                'options' => [
                    'all_referrers' => __('All Referrers', 'master-addons'),
                    'search_engines' => __('Search Engines', 'master-addons'),
                    'social_media' => __('Social Media', 'master-addons'),
                    'direct' => __('Direct Traffic', 'master-addons'),
                    'specific_url' => __('Specific URL', 'master-addons'),
                ]
            ],
            'time' => [
                'label' => __('Time & Date', 'master-addons'),
                'options' => [
                    'always' => __('Always', 'master-addons'),
                    'date_range' => __('Date Range', 'master-addons'),
                    'time_range' => __('Time Range', 'master-addons'),
                    'days_of_week' => __('Days of Week', 'master-addons'),
                ]
            ],
            'woocommerce' => [
                'label' => __('WooCommerce', 'master-addons'),
                'options' => [
                    'shop' => __('Shop Page', 'master-addons'),
                    'product' => __('Product Pages', 'master-addons'),
                    'cart' => __('Cart Page', 'master-addons'),
                    'checkout' => __('Checkout Page', 'master-addons'),
                    'cart_empty' => __('Cart is Empty', 'master-addons'),
                    'cart_has_items' => __('Cart Has Items', 'master-addons'),
                ]
            ]
        ];
    }
    
    public function check_conditions($popup_id) {
        $popup = $this->get_popup_settings($popup_id);
        
        if (!$popup || $popup['status'] !== 'active') {
            return false;
        }
        
        $conditions = isset($popup['conditions']) ? $popup['conditions'] : [];
        
        // Check page conditions
        if (!$this->check_page_conditions($conditions)) {
            return false;
        }
        
        // Check user conditions
        if (!$this->check_user_conditions($conditions)) {
            return false;
        }
        
        // Check device conditions
        if (!$this->check_device_conditions($conditions)) {
            return false;
        }
        
        // Check time conditions
        if (!$this->check_time_conditions($conditions)) {
            return false;
        }
        
        // Check referrer conditions
        if (!$this->check_referrer_conditions($conditions)) {
            return false;
        }
        
        // Check WooCommerce conditions if WooCommerce is active
        if (class_exists('WooCommerce')) {
            if (!$this->check_woocommerce_conditions($conditions)) {
                return false;
            }
        }
        
        // Check frequency conditions
        if (!$this->check_frequency_conditions($popup_id, $conditions)) {
            return false;
        }
        
        return true;
    }
    
    private function check_page_conditions($conditions) {
        if (!isset($conditions['pages']) || $conditions['pages'] === 'all') {
            return true;
        }
        
        $page_condition = $conditions['pages'];
        
        switch ($page_condition) {
            case 'home':
                return is_front_page() || is_home();
            case 'blog':
                return is_home() || is_archive() || is_single();
            case 'archive':
                return is_archive();
            case 'search':
                return is_search();
            case '404':
                return is_404();
            case 'specific':
                if (isset($conditions['specific_pages']) && is_array($conditions['specific_pages'])) {
                    return is_page($conditions['specific_pages']) || is_single($conditions['specific_pages']);
                }
                break;
        }
        
        return false;
    }
    
    private function check_user_conditions($conditions) {
        if (!isset($conditions['users']) || $conditions['users'] === 'all_users') {
            return true;
        }
        
        $user_condition = $conditions['users'];
        
        switch ($user_condition) {
            case 'logged_in':
                return is_user_logged_in();
            case 'logged_out':
                return !is_user_logged_in();
            case 'first_time':
                return !isset($_COOKIE['jltma_popup_visitor']);
            case 'returning':
                return isset($_COOKIE['jltma_popup_visitor']);
        }
        
        return false;
    }
    
    private function check_device_conditions($conditions) {
        if (!isset($conditions['devices']) || $conditions['devices'] === 'all_devices') {
            return true;
        }
        
        $device = $this->detect_device();
        return $conditions['devices'] === $device;
    }
    
    private function check_time_conditions($conditions) {
        if (!isset($conditions['time']) || $conditions['time'] === 'always') {
            return true;
        }
        
        $current_time = current_time('timestamp');
        
        switch ($conditions['time']) {
            case 'date_range':
                if (isset($conditions['start_date']) && isset($conditions['end_date'])) {
                    $start = strtotime($conditions['start_date']);
                    $end = strtotime($conditions['end_date']);
                    return $current_time >= $start && $current_time <= $end;
                }
                break;
                
            case 'time_range':
                if (isset($conditions['start_time']) && isset($conditions['end_time'])) {
                    $current_hour = wp_date('H:i', $current_time);
                    return $current_hour >= $conditions['start_time'] && $current_hour <= $conditions['end_time'];
                }
                break;
                
            case 'days_of_week':
                if (isset($conditions['days']) && is_array($conditions['days'])) {
                    $current_day = strtolower(wp_date('l', $current_time));
                    return in_array($current_day, $conditions['days']);
                }
                break;
        }
        
        return false;
    }
    
    private function check_referrer_conditions($conditions) {
        if (!isset($conditions['referrer']) || $conditions['referrer'] === 'all_referrers') {
            return true;
        }
        
        $referrer = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
        
        switch ($conditions['referrer']) {
            case 'search_engines':
                $search_engines = ['google.', 'bing.', 'yahoo.', 'duckduckgo.', 'baidu.'];
                foreach ($search_engines as $engine) {
                    if (strpos($referrer, $engine) !== false) {
                        return true;
                    }
                }
                return false;
                
            case 'social_media':
                $social_sites = ['facebook.', 'twitter.', 'instagram.', 'linkedin.', 'pinterest.'];
                foreach ($social_sites as $site) {
                    if (strpos($referrer, $site) !== false) {
                        return true;
                    }
                }
                return false;
                
            case 'direct':
                return empty($referrer);
                
            case 'specific_url':
                if (isset($conditions['specific_referrer'])) {
                    return strpos($referrer, $conditions['specific_referrer']) !== false;
                }
                break;
        }
        
        return false;
    }
    
    private function check_woocommerce_conditions($conditions) {
        if (!isset($conditions['woocommerce']) || empty($conditions['woocommerce'])) {
            return true;
        }
        
        $woo_condition = $conditions['woocommerce'];
        
        switch ($woo_condition) {
            case 'shop':
                return is_shop();
            case 'product':
                return is_product();
            case 'cart':
                return is_cart();
            case 'checkout':
                return is_checkout();
            case 'cart_empty':
                return WC()->cart && WC()->cart->is_empty();
            case 'cart_has_items':
                return WC()->cart && !WC()->cart->is_empty();
        }
        
        return false;
    }
    
    private function check_frequency_conditions($popup_id, $conditions) {
        if (!isset($conditions['show_frequency'])) {
            return true;
        }
        
        $frequency = $conditions['show_frequency'];
        $cookie_name = 'jltma_popup_shown_' . $popup_id;
        
        switch ($frequency) {
            case 'always':
                return true;
                
            case 'once_session':
                return !isset($_COOKIE[$cookie_name]);
                
            case 'once_day':
                if (isset($_COOKIE[$cookie_name])) {
                    $last_shown = absint( sanitize_text_field( wp_unslash( $_COOKIE[$cookie_name] ) ) );
                    return (time() - $last_shown) > DAY_IN_SECONDS;
                }
                return true;

            case 'once_week':
                if (isset($_COOKIE[$cookie_name])) {
                    $last_shown = absint( sanitize_text_field( wp_unslash( $_COOKIE[$cookie_name] ) ) );
                    return (time() - $last_shown) > WEEK_IN_SECONDS;
                }
                return true;
                
            case 'once':
                return !isset($_COOKIE[$cookie_name]);
        }
        
        return true;
    }
    
    private function detect_device() {
        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
        
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $user_agent)) {
            return 'tablet';
        }
        
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $user_agent)) {
            return 'mobile';
        }
        
        return 'desktop';
    }
    
    private function get_popup_settings($popup_id) {
        // Get popup post
        $popup_post = get_post($popup_id);

        if (!$popup_post || $popup_post->post_type !== 'jltma_popup') {
            return false;
        }

        // Get settings from post meta
        $settings = get_post_meta($popup_id, '_jltma_popup_settings', true);
        $conditions = get_post_meta($popup_id, '_jltma_popup_conditions', true);
        $status = get_post_meta($popup_id, '_jltma_popup_status', true) ?: ($popup_post->post_status === 'publish' ? 'active' : 'inactive');

        // Decode JSON if needed
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }
        if (is_string($conditions)) {
            $conditions = json_decode($conditions, true);
        }

        $popup = [
            'id' => $popup_id,
            'name' => $popup_post->post_title,
            'status' => $status,
            'settings' => $settings ?: [],
            'conditions' => $conditions ?: []
        ];

        return $popup;
    }
    
    public function get_all_conditions() {
        return $this->conditions;
    }
}