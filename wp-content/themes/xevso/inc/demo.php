<?php
function xevso_ocdi_import_files() {
    return array(
        array(
            'import_file_name'             => esc_html__( 'Demo page', 'xevso' ),
            'local_import_file'            => trailingslashit( get_template_directory() ) . 'inc/demo/demo.xml',
            'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'inc/demo/widgets.wie',
            'local_import_customizer_file' => trailingslashit( get_template_directory() ) . 'inc/demo/customizer.dat',
            'local_import_json'           => array(
                array(
                  'file_path'   => trailingslashit( get_template_directory() ) . 'inc/demo/theme-options.json',
                  'option_name' => 'xevso_Theme_Option',
                ),
            ),
            'preview_url'                  => 'http://wptf.themepul.com/xevso',
        ),
    );
}
add_filter( 'pt-ocdi/import_files', 'xevso_ocdi_import_files' );
function xevso_after_import_setup() {


    $main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
        set_theme_mod( 'nav_menu_locations', array(
            'main-menu' => $main_menu->term_id
        )
    );

    $ft_menu = get_term_by( 'name', 'Footer Menu', 'nav_menu' );
        set_theme_mod( 'nav_menu_locations', array(
            'footer-menu' => $ft_menu->term_id
        )
    );

    // Assign front page and posts page (blog page).
    $xevso_front_page_id = get_page_by_title( 'Home' );
    $xevso_blog_page_id = get_page_by_title( 'Blog' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $xevso_front_page_id->ID );
    update_option( 'page_for_posts', $xevso_blog_page_id->ID );
}
add_action( 'pt-ocdi/after_import', 'xevso_after_import_setup' );

function ocdi_plugin_page_setup( $default_settings ) {
    $default_settings['parent_slug'] = 'admin.php';
    $default_settings['page_title'] = esc_html__( 'One Click Demo Import', 'xevso' );
    $default_settings['menu_title'] = esc_html__( 'Import Demo Data', 'xevso' );
    $default_settings['capability'] = 'import';
    $default_settings['menu_slug'] = 'demos-settings';

    return $default_settings;
}
add_filter( 'pt-ocdi/plugin_page_setup', 'ocdi_plugin_page_setup' );