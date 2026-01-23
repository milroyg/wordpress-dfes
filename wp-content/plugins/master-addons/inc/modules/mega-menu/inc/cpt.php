<?php

namespace MasterAddons\Modules\MegaMenu;

defined('ABSPATH') || exit;

class JLTMA_Megamenu_Cpt
{

    private static $_instance = null;

    public function __construct()
    {
        if (!did_action('init')) {
            add_action('init', [$this, 'post_types']);
        } else {
            $this->post_types();
        }

        // Auto-enable post type in Elementor settings
        add_filter('elementor/utils/get_public_post_types', [$this, 'add_to_elementor_post_types']);

        // If Elementor is already loaded, register immediately
        if (did_action('elementor/init')) {
            $this->register_with_elementor();
        } else {
            add_action('elementor/init', [$this, 'register_with_elementor']);
        }
    }

    /**
     * Add mastermega_content to Elementor's public post types filter
     */
    public function add_to_elementor_post_types($post_types)
    {
        $post_types['mastermega_content'] = 'mastermega_content';
        return $post_types;
    }

    /**
     * Register post type with Elementor and enable in Post Types settings
     */
    public function register_with_elementor()
    {
        $post_type = 'mastermega_content';

        // Add Elementor support to the post type
        add_post_type_support($post_type, 'elementor');

        // Get existing Elementor CPT support option (grab all saved settings first)
        $cpt_support = get_option('elementor_cpt_support');

        // If option doesn't exist yet, initialize with defaults
        if ($cpt_support === false) {
            $cpt_support = ['post', 'page'];
        }

        // Ensure it's an array
        if (!is_array($cpt_support)) {
            $cpt_support = ['post', 'page'];
        }

        // Add our post type if not already in the list
        if (!in_array($post_type, $cpt_support)) {
            $cpt_support[] = $post_type;
            update_option('elementor_cpt_support', $cpt_support);
        }
    }

    public function post_types()
    {
        $labels = array(
            'name'                  => _x('Master Addons Items', 'Post Type General Name', 'master-addons' ),
            'singular_name'         => _x('Master Addons Item', 'Post Type Singular Name', 'master-addons' ),
            'menu_name'             => esc_html__('Master Addons item', 'master-addons' ),
            'name_admin_bar'        => esc_html__('Master Addons item', 'master-addons' ),
            'archives'              => esc_html__('Item Archives', 'master-addons' ),
            'attributes'            => esc_html__('Item Attributes', 'master-addons' ),
            'parent_item_colon'     => esc_html__('Parent Item:', 'master-addons' ),
            'all_items'             => esc_html__('All Items', 'master-addons' ),
            'add_new_item'          => esc_html__('Add New Item', 'master-addons' ),
            'add_new'               => esc_html__('Add New', 'master-addons' ),
            'new_item'              => esc_html__('New Item', 'master-addons' ),
            'edit_item'             => esc_html__('Edit Item', 'master-addons' ),
            'update_item'           => esc_html__('Update Item', 'master-addons' ),
            'view_item'             => esc_html__('View Item', 'master-addons' ),
            'view_items'            => esc_html__('View Items', 'master-addons' ),
            'search_items'          => esc_html__('Search Item', 'master-addons' ),
            'not_found'             => esc_html__('Not found', 'master-addons' ),
            'not_found_in_trash'    => esc_html__('Not found in Trash', 'master-addons' ),
            'featured_image'        => esc_html__('Featured Image', 'master-addons' ),
            'set_featured_image'    => esc_html__('Set featured image', 'master-addons' ),
            'remove_featured_image' => esc_html__('Remove featured image', 'master-addons' ),
            'use_featured_image'    => esc_html__('Use as featured image', 'master-addons' ),
            'insert_into_item'      => esc_html__('Insert into item', 'master-addons' ),
            'uploaded_to_this_item' => esc_html__('Uploaded to this item', 'master-addons' ),
            'items_list'            => esc_html__('Items list', 'master-addons' ),
            'items_list_navigation' => esc_html__('Items list navigation', 'master-addons' ),
            'filter_items_list'     => esc_html__('Filter items list', 'master-addons' ),
        );
        $rewrite = array(
            'slug'                  => 'mastermega-content',
            'with_front'            => true,
            'pages'                 => false,
            'feeds'                 => false,
        );
        $args = array(
            'label'                 => esc_html__('Master Addons Item', 'master-addons' ),
            'description'           => esc_html__('mastermega_content', 'master-addons' ),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'elementor', 'permalink'),
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => false,
            'show_in_menu'          => false,
            'menu_position'         => 5,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'publicly_queryable' => true,
            'rewrite'               => $rewrite,
            'query_var'             => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'mastermega-content',
        );
        register_post_type('mastermega_content', $args);
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}


/*
* Returns Instance of the Master Mega Menu
*/
if (!function_exists('jltma_megamenu_cpt')) {
    function jltma_megamenu_cpt()
    {
        return JLTMA_Megamenu_Cpt::get_instance();
    }
}

jltma_megamenu_cpt();
