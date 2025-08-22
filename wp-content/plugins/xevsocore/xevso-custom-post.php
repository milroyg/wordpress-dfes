<?php 
add_action( 'init', 'xevsocore_custom_post_type' );
function xevsocore_custom_post_type() {
    register_post_type( 'project',
        array(
            'labels' => array(
                'name' => esc_html__('Projects','xevsocore'),
                'singular_name' => esc_html__('Project','xevsocore'),
            ),
            'show_in_rest'  => true,
            'supports'      => array('title','thumbnail', 'page-attributes','editor','excerpt'),
            'menu_icon'     => esc_attr__('dashicons-image-filter','xevsocore'),
            'public'        => true
        )
    );
    register_post_type( 'team',
        array(
            'labels' => array(
                'name' => esc_html__('Team','xevsocore'),
                'singular_name' => esc_html__('Team','xevsocore'),
            ),
            'show_in_rest'  => true,
            'supports'      => array('title','thumbnail','editor','excerpt'),
            'menu_icon'     => esc_attr__('dashicons-groups','xevsocore'),
            'public'        => true,
            'has_archive' => true,
            'hierarchical' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        )
    );
}
/*** Custom taxonomy ***/
add_action( 'init', 'xevsocore_custom_post_taxonomy');
function xevsocore_custom_post_taxonomy() {
    register_taxonomy(
        'project_cat',
        'project',                  
            array(
                'label'                 => esc_html__('project Category', 'xevsocore'),
                'query_var'             => true,
                'hierarchical'          => true,
                'public'                => true,
                'show_ui'               => true,
                'show_admin_column'     => false,
                'show_in_nav_menus'     => true,
                'show_in_rest'          => true,
                'show_tagcloud'         => true,
                'rewrite'               => array(
                    'slug'              => 'project-category', 
                    'with_front'        => true 
                )
            )
    );
}