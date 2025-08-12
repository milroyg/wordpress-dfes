<?php
// Create typography section
CSF::createSection( $xevsoThemeOption, array(
    'title'  => esc_html__( 'Typography', 'xevso' ),
    'id'     => 'xevso_typography_options',
    'icon'   => 'fa fa-text-width',
    'fields' => array(

        array(
            'id'           => 'xevso_body_typo',
            'type'         => 'typography',
            'title'        => esc_html__( 'Body', 'xevso' ),
            'output'       => 'body',
            'default'      => array(
                'font-family'  => 'Heebo',
                'font-size'   => '15.5',
                'unit'        => 'px',
                'color'       => '#798490',
                'font-weight'  => '400',
                'extra-styles' => array( '300', '400', '500', '600', '700', '800', '900', '300i', '400i', '500i', '600i', '700i', '800i', '900i' ),
            ),
            'extra_styles' => true,
            'subtitle'     => esc_html__( 'Set body typography.', 'xevso' ),
        ),

        array(
            'id'           => 'xevso_h1_typo',
            'type'         => 'typography',
            'title'        => esc_html__( 'Heading One', 'xevso' ),
            'output'       => 'h1',
            'extra_styles' => true,
            'default'      => array(
                'font-family' => 'Josefin Sans',
                'unit'        => 'px',
                'font-weight' => '500',
                'color'       => '#2c3943'
            ),
            'subtitle'     => esc_html__( 'Set heading one typography.', 'xevso' ),
        ),

        array(
            'id'           => 'xevso_h2_typo',
            'type'         => 'typography',
            'title'        => esc_html__( 'Heading Two', 'xevso' ),
            'output'       => 'h2',
            'extra_styles' => true,
            'default'      => array(
                'font-family' => 'Josefin Sans',
                'unit'        => 'px',
                'font-weight'  => '500',
                'color'       => '#2c3943'
            ),
            'subtitle'     => esc_html__( 'Set heading two typography.', 'xevso' ),
        ),

        array(
            'id'           => 'xevso_h3_typo',
            'type'         => 'typography',
            'title'        => esc_html__( 'Heading Three', 'xevso' ),
            'output'       => 'h3',
            'default'      => array(
                'font-family' => 'Josefin Sans',
                'unit'        => 'px',
                'font-weight'  => '500',
                'color'       => '#2c3943'
            ),
            'extra_styles' => true,
            'subtitle'     => esc_html__( 'Set heading three typography.', 'xevso' ),
        ),

        array(
            'id'           => 'xevso_h4_typo',
            'type'         => 'typography',
            'title'        => esc_html__( 'Heading Four', 'xevso' ),
            'output'       => 'h4',
            'default'      => array(
                'font-family' => 'Josefin Sans',
                'unit'        => 'px',
                'font-weight'  => '500',
                'color'       => '#2c3943'
            ),
            'extra_styles' => true,
            'subtitle'     => esc_html__( 'Set heading four typography.', 'xevso' ),
        ),

        array(
            'id'           => 'xevso_h5_typo',
            'type'         => 'typography',
            'title'        => esc_html__( 'Heading Five', 'xevso' ),
            'output'       => 'h5',
            'default'      => array(
                'font-family' => 'Josefin Sans',
                'unit'        => 'px',
                'font-weight'  => '500',
                'color'       => '#2c3943'
            ),
            'extra_styles' => true,
            'subtitle'     => esc_html__( 'Set heading five typography.', 'xevso' ),
        ),

        array(
            'id'           => 'xevso_h6_typo',
            'type'         => 'typography',
            'title'        => esc_html__( 'Heading Six', 'xevso' ),
            'output'       => 'h6',
            'default'      => array(
                'font-family' => 'Josefin Sans',
                'unit'        => 'px',
                'font-weight'  => '500',
                'color'       => '#2c3943'
            ),
            'extra_styles' => true,
            'subtitle'     => esc_html__( 'Set heading six typography.', 'xevso' ),
        ),
    ),
) );
// End typography section