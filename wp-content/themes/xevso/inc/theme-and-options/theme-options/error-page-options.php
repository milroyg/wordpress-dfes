<?php
CSF::createSection( $xevsoThemeOption, array(
    'parent' => 'xevso_page_options',
    'title'  => esc_html__( 'Error 404', 'xevso' ),
    'icon'   => 'fa fa-exclamation-triangle',
    'fields' => array(

        array(
            'id'       => 'xevso_error_page_banner',
            'type'     => 'switcher',
            'title'    => esc_html__( 'Enable Error Banner', 'xevso' ),
            'default'  => true,
            'text_on'  => esc_html__( 'Yes', 'xevso' ),
            'text_off' => esc_html__( 'No', 'xevso' ),
            'desc'     => esc_html__( 'Enable or disable search page banner.', 'xevso' ),
        ),
        array(
            'id'    => 'xevso_error_page_title',
            'type'  => 'text',
            'title' => esc_html__( 'Banner Title', 'xevso' ),
            'desc'  => esc_html__( 'Type Banner Title Here.', 'xevso' ),
        ),
        array(
            'id'            => 'xevso_not_found_text',
            'type'          => 'wp_editor',
            'title'         => esc_html__( 'Not Found Text', 'xevso' ),
            'tinymce'       => true,
            'quicktags'     => true,
            'media_buttons' => false,
            'height'        => '150px',
            'desc'          => esc_html__( 'Type not found text here.', 'xevso' ),
        ),

        array(
            'id'       => 'xevso_go_back_home',
            'type'     => 'switcher',
            'title'    => esc_html__( 'Enable Go Back Home Button', 'xevso' ),
            'text_on'  => esc_html__( 'Yes', 'xevso' ),
            'text_off' => esc_html__( 'No', 'xevso' ),
            'desc'     => esc_html__( 'Enable or disable go back home button.', 'xevso' ),
            'default'  => true,
        ),
        array(
            'id'         => 'xevso_error_page_button_text',
            'type'       => 'text',
            'dependency' => array( 'xevso_go_back_home', '==', 'true' ),
            'title'      => esc_html__( 'Bottom Text', 'xevso' ),
            'desc'       => esc_html__( 'Type Banner Title Here.', 'xevso' ),
            'default'    => esc_html__( 'Back Home', 'xevso' ),
        ),
        array(
            'id'           => 'xevso_error_bottom_image',
            'type'         => 'media',
            'title'        => esc_html__( 'Error Bottom Image', 'xevso' ),
            'library'      => 'image',
            'button_title' => esc_html__( 'Upload Image', 'xevso' ),
            'desc'         => esc_html__( 'Upload error page image', 'xevso' ),
        ),
    ),
) );