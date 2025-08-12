<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Header Setings
CSF::createSection( $xevsoThemeOption, array(
    'id'    => 'xevso_header_settings',
    'title' => esc_html__( 'Header Settings', 'xevso' ),
    'icon'  => 'fa fa-header',
) );



// Header Options
CSF::createSection( $xevsoThemeOption, array(
    'parent' => 'xevso_header_settings',
    'title'  => esc_html__( 'Header Options', 'xevso' ),
    'icon'   => 'far fa-picture-o',
    'fields' => array(
       
        array(
            'type'       => 'subheading',
            'content'    => esc_html__( 'Header Option', 'xevso' ),
           
        ),
        array(
            'id'         => 'xevso_header2_logo',
            'type'       => 'media',
            'title'      => esc_html__( 'Upload Site Logo', 'xevso' ),
            'library'    => 'image',
           
        ),
        array(
            'type'       => 'subheading',
            'content'    => esc_html__( 'Quote Option', 'xevso' ),
           
        ),
        array(
            'id'         => 'xevso_enable_quete',
            'type'       => 'switcher',
            'default'    => true,
            'title'      => esc_html__( 'Enable', 'xevso' ),
            
        ),
        array(
            'id'         => 'intech_header2_quete_text',
            'type'       => 'text',
            'title'      => esc_html__( 'Quote Text', 'xevso' ),
            'default'    => esc_html__( 'Get A quote', 'xevso' ),
           
        ),
        array(
            'id'          => 'intech_header2_quete_link_select',
            'type'        => 'select',
            'title'       => esc_html__( 'Select Link Options', 'xevso' ),
            'placeholder' => esc_html__( 'Select an option', 'xevso' ),
            
            'options'     => array(
                '1' => esc_html__( 'Extranal', 'xevso' ),
                '2' => esc_html__( 'Page', 'xevso' ),
            ),
            'default'     => '1',
        ),
        array(
            'id'         => 'intech_header2_quete_link',
            'type'       => 'text',
            'title'      => esc_html__( 'Quote Link', 'xevso' ),
            'default'    => esc_html__( '#', 'xevso' ),
           

        ),
        array(
            'id'          => 'intech_header2_quete_page',
            'type'        => 'select',
            'title'       => esc_html__( 'Select Page', 'xevso' ),
            'placeholder' => esc_html__( 'Select a page', 'xevso' ),
           
            'options'     => 'pages',
            'query_args'  => array(
                'posts_per_page' => -1,
            ),
        ),
        // Quete Css
        array(
            'id'         => 'xevso_header2_quete_colors',
            'type'       => 'color_group',
            'title'      => esc_html__( 'Quote Colors', 'xevso' ),
          
            'subtitle'   => esc_html__( 'Choose Your Top Header Colors', 'xevso' ),
            'options'    => array(
                'header2_qc'   => esc_html__( 'Text Color', 'xevso' ),
                'header2_qbg'  => esc_html__( 'Background Color', 'xevso' ),
                'header2_qhtc' => esc_html__( 'Hover Text Color', 'xevso' ),
                'header2_qhbc' => esc_html__( 'Background Hover Color', 'xevso' ),
            ),
            'default'    => array(
                'header2_qc'   => '#ffffff',
                'header2_qbg'  => '#fff',
                'header2_qhtc' => '#ffffff',
                'header2_qhbc' => '#fff',
            ),
        ),
        array(
            'type'       => 'subheading',
            'content'    => esc_html__( 'Menu Color Options', 'xevso' ),
           
        ),
        array(
            'id'         => 'xevso_header2_menu_colors',
            'type'       => 'color_group',
            'title'      => esc_html__( 'Menu Colors', 'xevso' ),
            'subtitle'   => esc_html__( 'Choose your color for Menu', 'xevso' ),
            
            'options'    => array(
                'header2_menu_normal_c' => esc_html__( 'Normal Color', 'xevso' ),
                'header2_menu_hcolor'   => esc_html__( 'Hover Color', 'xevso' ),
            ),
            'default'    => array(
                'header2_menu_normal_c' => '#ffffff',
                'header2_menu_hcolor'   => '#061738',
            ),
        ),
        array(
            'id'         => 'xevso_header2_submenu_colors',
            'type'       => 'color_group',
            'title'      => esc_html__( 'SubMenu Colors', 'xevso' ),
            'subtitle'   => esc_html__( 'Choose your color for SubMenu', 'xevso' ),
           
            'options'    => array(
                'header2_menu_box_c'        => esc_html__( 'Menu Box BG Color', 'xevso' ),
                'header2_menu_item_text_c'  => esc_html__( 'Text Color', 'xevso' ),
                'header2_menu_item_hbg'     => esc_html__( 'Hover BG', 'xevso' ),
                'header2_menu_item_htext_c' => esc_html__( 'Hover Text', 'xevso' ),
                'header2_menu_active_bg'    => esc_html__( 'Active Background', 'xevso' ),
                'header2_menu_active_c'     => esc_html__( 'Active Color', 'xevso' ),
            ),
            'default'    => array(
                'header2_menu_box_c'        => '#fff',
                'header2_menu_item_text_c'  => '#061738',
                'header2_menu_item_hbg'     => '#ffffff',
                'header2_menu_item_htext_c' => '#FB6D62',
                'header2_menu_active_bg'    => '#ffffff',
                'header2_menu_active_c'     => '#FB6D62',
            ),
        ),

      
        ),
      
        ),
    );
// End Header Options