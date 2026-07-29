<?php

namespace MasterAddons\Inc\Classes;

if (!defined('ABSPATH')) {
    exit;
}

class Utils
{
    public static function get_options($key, $network_override = true)
    {
        if (is_network_admin()) {
            $value = get_site_option($key);
        } elseif (!$network_override && is_multisite()) {
            $value = get_site_option($key);
        } elseif ($network_override && is_multisite()) {
            $value = get_option($key);
            $value = (false === $value || (is_array($value) && in_array('disabled', $value))) ? get_site_option($key) : $value;
        } else {
            $value = get_option($key);
        }

        return $value;
    }

    public static function check_options($option_name)
    {
        return isset($option_name) ? esc_attr($option_name) : false;
    }

    /**
     * Allowed option-name prefixes for plugin writes. Prevents this generic
     * wrapper from being used to write arbitrary WordPress options.
     *
     * @var string[]
     */
    private static $jltma_allowed_option_prefixes = array('jltma_', 'ma_el_', 'master_addons', 'master-addons');

    public static function update_options($option_name, $option_value)
    {
        if (!is_string($option_name) || '' === $option_name) {
            return false;
        }

        $is_allowed = false;
        foreach (self::$jltma_allowed_option_prefixes as $prefix) {
            if (0 === strpos($option_name, $prefix)) {
                $is_allowed = true;
                break;
            }
        }

        if (!$is_allowed) {
            return false;
        }

        if (JLTMA_NETWORK_ACTIVATED == true) {
            return update_site_option($option_name, $option_value);
        } else {
            return update_option($option_name, $option_value);
        }
    }

    public static function is_plugin_active($plugin_basename)
    {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        return \is_plugin_active($plugin_basename);
    }

    public static function is_woocommerce_active()
    {
        return self::is_plugin_active('woocommerce/woocommerce.php');
    }

    public static function is_site_wide($plugin)
    {
        if (!is_multisite()) {
            return false;
        }

        $plugins = get_site_option('active_sitewide_plugins');
        if (isset($plugins[$plugin])) {
            return true;
        }

        return false;
    }

    public static function pretty_number($x = 0)
    {
        $x = (int) $x;

        if ($x > 1000000) {
            return floor($x / 1000000) . 'M';
        }

        if ($x > 10000) {
            return floor($x / 1000) . 'k';
        }
        return $x;
    }

    public static function get_site_domain()
    {
        return str_ireplace('www.', '', (string) wp_parse_url(home_url() ?? '', PHP_URL_HOST));
    }

    public static function human_readable_num($size)
    {
        $l    = substr($size, -1);
        $ret  = substr($size, 0, -1);

        switch (strtoupper($l)) {
            case 'P':
                $ret *= 1024;
            case 'T':
                $ret *= 1024;
            case 'G':
                $ret *= 1024;
            case 'M':
                $ret *= 1024;
            case 'K':
                $ret *= 1024;
        }
        return $ret;
    }

    public static function array_flatten($array)
    {
        if (!is_array($array)) {
            return false;
        }
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $value[0];
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function hex2rgb_array($hex)
    {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);
        return $rgb;
    }

    public static function hex2Rgb($hex, $alpha = false)
    {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ($alpha) {
            $rgb['a'] = $alpha;
        }
        return $rgb;
    }

    public static function image_filter_gallery_categories($gallery_items)
    {
        if (!is_array($gallery_items)) {
            return false;
        }

        $gallery_category_names = array();
        $gallery_category_names_final = array();

        foreach ($gallery_items as $gallery_item) {
            $gallery_category_names[] = $gallery_item['gallery_category_name'];
        }

        if (is_array($gallery_category_names) && !empty($gallery_category_names)) {
            foreach ($gallery_category_names as $gallery_category_name) {
                $gallery_category_names_final[] = explode(',', $gallery_category_name);
            }
        }

        if (is_array($gallery_category_names_final) && !empty($gallery_category_names_final)) {
            $gallery_category_names_final = self::image_filter_gallery_array_flatten($gallery_category_names_final);
            return array_unique(array_filter($gallery_category_names_final));
        }
    }

    public static function image_filter_gallery_category_classes($gallery_classes, $id)
    {
        if (!($gallery_classes)) {
            return false;
        }

        $gallery_cat_classes    = array();
        $gallery_classes        = explode(',', $gallery_classes);

        if (is_array($gallery_classes) && !empty($gallery_classes)) {
            foreach ($gallery_classes as $gallery_class) {
                $gallery_cat_classes[] = sanitize_title($gallery_class) . '-' . $id;
            }
        }

        return implode(' ', $gallery_cat_classes);
    }

    public static function image_filter_gallery_categories_parts($gallery_classes)
    {
        if (!($gallery_classes)) {
            return false;
        }

        $gallery_cat_classes    = array();
        $gallery_classes        = explode(',', $gallery_classes);

        if (is_array($gallery_classes) && !empty($gallery_classes)) {
            foreach ($gallery_classes as $gallery_class) {
                $gallery_cat_classes[] = '<div class="ma-el-label ma-el-added ma-el-image-filter-cat">' . sanitize_title($gallery_class) . '</div>';
            }
        }

        return implode(' ', $gallery_cat_classes);
    }

    public static function image_filter_gallery_array_flatten($array)
    {
        if (!is_array($array)) {
            return false;
        }

        $result = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, self::image_filter_gallery_array_flatten($value));
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public static function multi_dimension_flatten($array, $prefix = '')
    {
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = $result + self::multi_dimension_flatten($value, $prefix . $key . '.');
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function get_environment_info()
    {
        $curl_version = '';
        if (function_exists('curl_version')) {
            $curl_version = curl_version();
            $curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
        }

        $wp_memory_limit = self::human_readable_num(WP_MEMORY_LIMIT);
        if (function_exists('memory_get_usage')) {
            $wp_memory_limit = max($wp_memory_limit, self::human_readable_num(@ini_get('memory_limit')));
        }

        return array(
            'home_url'                  => get_option('home'),
            'site_url'                  => get_option('siteurl'),
            'version'                   => defined('JLTMA_VER') ? JLTMA_VER : '',
            'wp_version'                => get_bloginfo('version'),
            'wp_multisite'              => is_multisite(),
            'wp_memory_limit'           => $wp_memory_limit,
            'wp_debug_mode'             => (defined('WP_DEBUG') && WP_DEBUG),
            'wp_cron'                   => !(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON),
            'language'                  => get_locale(),
            'external_object_cache'     => wp_using_ext_object_cache(),
            'server_info'               => isset($_SERVER['SERVER_SOFTWARE']) ? sanitize_text_field( wp_unslash($_SERVER['SERVER_SOFTWARE']) ) : '',
            'php_version'               => phpversion(),
            'php_post_max_size'         => self::human_readable_num(ini_get('post_max_size')),
            'php_max_execution_time'    => ini_get('max_execution_time'),
            'php_max_input_vars'        => ini_get('max_input_vars'),
            'curl_version'              => $curl_version,
            'suhosin_installed'         => extension_loaded('suhosin'),
            'max_upload_size'           => wp_max_upload_size(),
            'default_timezone'          => date_default_timezone_get(),
            'fsockopen_or_curl_enabled' => (function_exists('fsockopen') || function_exists('curl_init')),
            'soapclient_enabled'        => class_exists('SoapClient'),
            'domdocument_enabled'       => class_exists('DOMDocument'),
            'gzip_enabled'              => is_callable('gzopen'),
            'mbstring_enabled'          => extension_loaded('mbstring'),
        );
    }
}
