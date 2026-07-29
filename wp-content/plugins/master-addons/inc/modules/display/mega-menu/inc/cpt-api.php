<?php

namespace MasterAddons\Modules\Display\MegaMenu;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use MasterAddons\Inc\Classes\Helper;

class Megamenu_Cpt_Api
{

    use Rest_API;

    private static $_instance = null;

    public function __construct()
    {
        $this->config("/mastermega-content", "/(?P<type>\w+)/(?P<key>[\w-]+)?(?:/(?P<id>\d+))?/?", array('get_jltma_content_editor'));
        // $this->config("/mastermega-content", "/(?P<type>\w+)/(?P<key>[\w-]+)(?:/(?P<id>\d+))?/?"); 2 
        // $this->config("/mastermega-content", "/(?P<type>\w+)/(?P<key>\w+(|[-]\w+))/"); 1 
        $this->init();
    }
    


    public function get_jltma_content_editor()
    {
        // This action creates and edits mega menu content posts, so it needs a
        // stricter capability than the shared route's edit_posts gate. Mega menu
        // content is part of nav-menu management, which maps to edit_theme_options.
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'master-addons' ), 403 );
        }

        $content_key  = isset( $this->request['key'] ) ? sanitize_key( $this->request['key'] ) : '';
        $content_type = isset( $this->request['type'] ) ? sanitize_key( $this->request['type'] ) : '';
        $menuitemId   = isset( $this->request['id'] ) ? absint( $this->request['id'] ) : 0;

        $builder_post_title = 'mastermega-content-' . $content_type . '-' . $content_key . $menuitemId;
        
        $builder_post_id    = Helper::get_page_by_title( $builder_post_title, 'mastermega_content' );

        if (is_null($builder_post_id)) {
            $defaults = array(
                'post_content'  => '',
                'post_title'    => $builder_post_title,
                'post_status'   => 'publish',
                'post_type'     => 'mastermega_content',
            );
            $builder_post_id = wp_insert_post($defaults);

            update_post_meta($builder_post_id, '_wp_page_template', 'elementor_canvas');
            // _elementor_edit_mode builder
            // _elementor_template_type wp-post
        } else {
            $builder_post_id = $builder_post_id->ID;
        }
        
        $url = get_admin_url() . 'post.php?post=' . $builder_post_id . '&action=elementor';
        wp_safe_redirect($url);
        exit;
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

// Returns Instanse of the Master Mega Menu Custom Post Type
if (!function_exists('jltma_megamenu_cpt_api')) {
    function jltma_megamenu_cpt_api()
    {
        return Megamenu_Cpt_Api::get_instance();
    }
}
jltma_megamenu_cpt_api();
