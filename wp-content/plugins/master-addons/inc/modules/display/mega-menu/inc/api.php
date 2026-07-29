<?php

namespace MasterAddons\Modules\Display\MegaMenu;

use MasterAddons\Modules\Display\MegaMenu\Megamenu_Options;

defined('ABSPATH') || exit;

class Megamenu_Api
{

    use Rest_API;

    private static $_instance = null;

    public function __construct()
    {
        $this->config('/megamenu', '', array('get_jltma_save_menuitem_settings', 'get_get_menuitem_settings'));
        $this->init();
    }

    public function get_jltma_save_menuitem_settings()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $nonce = isset( $_SERVER['HTTP_X_WP_NONCE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) ) : '';
        if ( ! wp_verify_nonce($nonce, 'wp_rest') ) {
            wp_send_json_error('Nonce verification failed.', 403);
        }

        $menu_item_id = $this->request['settings']['menu_id'];
        $menu_item_settings = json_encode($this->request['settings']);

        update_post_meta($menu_item_id, Megamenu_Options::$jltma_menuitem_settings_key, $menu_item_settings);

        return [
            'saved' => 1,
            'message' => esc_html__('Saved', 'master-addons' ),
        ];
    }

    public function get_get_menuitem_settings()
    {
        // Verify user permissions
        if (!current_user_can('edit_posts')) {
            return new \WP_Error(
                'rest_forbidden',
                esc_html__('Sorry, you are not allowed to do that.', 'master-addons'),
                array('status' => 401)
            );
        }

        $menu_item_id = $this->request['menu_id'];
        $data = get_post_meta($menu_item_id, Megamenu_Options::$jltma_menuitem_settings_key, true);

        return (array) json_decode($data);
    }


    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

if (!function_exists('jltma_megamenu_api')) {
    function jltma_megamenu_api()
    {
        return Megamenu_Api::get_instance();
    }
}

jltma_megamenu_api();
