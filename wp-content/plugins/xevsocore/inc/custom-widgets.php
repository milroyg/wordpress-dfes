<?php if ( !defined( 'ABSPATH' ) ) {die;} // Cannot access directly.
// Contact info

CSF::createWidget( 'xevso_contact_info_widget', array(
    'title'       => esc_html__( 'xevso Contact Info', 'xevsocore' ),
    'classname'   => 'footer-widget__contact',
    'description' => esc_html__( 'Add Your Contact Info', 'xevsocore' ),
    'fields'      => array(
        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__( 'Title', 'xevsocore' ),
        ),
        array(
            'id'      => 'cinfo_img_enable',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Enable Logo', 'xevsocore' ),
            'default' => true,
        ),

        array(
            'id'          => 'cinfo_img',
            'type'        => 'media',
            'title'       => esc_html__( 'Upload Logo', 'xevsocore' ),
            'library'     => 'image',
            'preview'     => true,
            'placeholder' => 'http://',
            'dependency'  => array( 'cinfo_img_enable', '==', 'true' ), // check for true/false by field id
        ),
        array(
            'id'      => 'xevso_widget_contact_info',
            'type'    => 'group',
            'title'   => esc_html__( 'Add Information', 'xevsocore' ),
            'fields'  => array(
                array(
                    'id'    => 'xevso_widget_contact_dec',
                    'type'  => 'text',
                    'title' => esc_html__( 'Content', 'xevsocore' ),
                ),
                array(
                    'id'    => 'xevso_widget_contact_icon',
                    'type'  => 'icon',
                    'title' => esc_html__( 'Icon', 'xevsocore' ),
                ),
                array(
                    'id'    => 'xevso_widget_contact_link',
                    'type'  => 'text',
                    'title' => esc_html__( 'Link', 'xevsocore' ),
                ),
            ),
            'default' => array(
                array(
                    'xevso_widget_contact_dec'  => esc_html__( '184 Main Rd E, St Albans VIC 3021, Australia', 'xevsocore' ),
                    'xevso_widget_contact_icon' => 'icon icon-map-marker',
                ),
                array(
                    'xevso_widget_contact_dec'  => esc_html__( 'example@example.com', 'xevsocore' ),
                    'xevso_widget_contact_icon' => 'icon icon-email',
                    'xevso_widget_contact_link' => esc_html__( '#', 'xevsocore' ),
                ),
                array(
                    'xevso_widget_contact_dec'  => esc_html__( '(+02) 1800 5656 3010', 'xevsocore' ),
                    'xevso_widget_contact_icon' => 'icon icon-phone3',
                    'xevso_widget_contact_link' => esc_html__( '#', 'xevsocore' ),
                ),
            ),
        ),
    ),
) );

// OutPut

if ( !function_exists( 'xevso_contact_info_widget' ) ) {
    function xevso_contact_info_widget( $args, $instance ) {
        echo wp_kses_post( $args['before_widget'] );
        ?>
            <?php
if ( !empty( $instance['title'] ) ) {
            echo wp_kses_post( $args['before_title'] ) . apply_filters( 'widget_title widtet-title', $instance['title'] ) . wp_kses_post( $args['after_title'] );
        }
        ?>
            <?php if ( $instance['cinfo_img_enable'] == true ):
            $logo = $instance['cinfo_img'];
            ?>
	            <div class="info-img">
	                <img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php esc_html_e( 'Intech by Themepul', 'xevsocore' );?>">
	            </div>
	            <?php endif;?>
            <ul class="list-unstyled footer-widget__contact-list">
                <?php
                foreach ( $instance['xevso_widget_contact_info'] as $xevso_widget_contact ) {
                ?>
                <li>
                    <?php if ( !empty( $xevso_widget_contact['xevso_widget_contact_link'] ) ): ?>
                    <a href="<?php echo esc_url( $xevso_widget_contact['xevso_widget_contact_link'] ) ?>">
                    <?php endif;?>
                        <i class="<?php echo esc_attr( $xevso_widget_contact['xevso_widget_contact_icon'] ); ?>"></i>
                        <?php echo esc_html( $xevso_widget_contact['xevso_widget_contact_dec'] ); ?>
                    <?php if ( !empty( $xevso_widget_contact['xevso_widget_contact_link'] ) ): ?>
                    </a>
                    <?php endif;?>
                </li>
                <?php
                }
                ?>
            </ul>
        <?php
echo wp_kses_post( $args['after_widget'] );
    }
}

// Blog Post

CSF::createWidget( 'xevso_blog_post_widget', array(
    'title'       => esc_html__( 'xevso Post With Thumbnail', 'xevsocore' ),
    'classname'   => 'footer-widget__post',
    'description' => esc_html__( 'Add your Contact Info', 'xevsocore' ),
    'fields'      => array(
        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__( 'Title', 'xevsocore' ),
        ),
        array(
            'id'      => 'xevso_widget_blog_number',
            'type'    => 'number',
            'title'   => esc_html__( 'Show Post', 'xevsocore' ),
            'default' => 2,
        ),
        array(
            'id'      => 'xevso_widget_blog_show_meta',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Enable Meta', 'xevsocore' ),
            'default' => true,
        ),
        array(
            'id'      => 'xevso_widget_blog_show_image',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Enable Image', 'xevsocore' ),
            'default' => true,
        ),
    ),
) );

// OutPut

if ( !function_exists( 'xevso_blog_post_widget' ) ) {
    function xevso_blog_post_widget( $args, $instance ) {
        echo wp_kses_post( $args['before_widget'] );
        if ( !empty( $instance['title'] ) ) {
            echo wp_kses_post( $args['before_title'] ) . apply_filters( 'widget_title widtet-title', $instance['title'] ) . wp_kses_post( $args['after_title'] );
        }
        ?>
        <ul class="list-unstyled footer-widget__post-list">
            <?php
            $post_q = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => $instance['xevso_widget_blog_number'],
            'order'          => 'DESC',
             ) );
            if ( $post_q->have_posts() ):
            while ( $post_q->have_posts() ):
                $post_q->the_post();
                ?>
		        <li>
                    <?php if ( !empty( $instance['xevso_widget_blog_show_image'] == true ) ) {
                        the_post_thumbnail( 'thumbnail' );
                    }?>
                    <div class="footer-widget__post-list-content">
                        <span><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo the_title(); ?></a></span>
                        <?php if ( !empty( $instance['xevso_widget_blog_show_meta'] == true ) ) {
                        echo '<strong>' . get_the_date() . '</strong>';
                    }?>
                    </div><!-- /.footer-widget__post-content -->
		        </li>
				<?php endwhile;endif;?>
        </ul>
        <?php
echo wp_kses_post( $args['after_widget'] );
    }
}

// Subscribe Options

CSF::createWidget( 'xevso_newsletter_widget', array(
    'title'       => esc_html__( 'xevso Newletter Widget', 'xevsocore' ),
    'classname'   => 'footer-widget__newsletter',
    'description' => esc_html__( 'Add Newsletter Info', 'xevsocore' ),
    'fields'      => array(
        array(
            'id'      => 'title',
            'type'    => 'text',
            'default' => esc_html( 'Newsletter Signup', 'xevsocore' ),
            'title'   => esc_html__( 'Title', 'xevsocore' ),
        ),
        array(
            'id'      => 'newsletter_dec',
            'type'    => 'textarea',
            'title'   => esc_html__( 'Sort Description', 'xevsocore' ),
            'desc'    => esc_html__( 'Add Sort Description', 'xevsocore' ),
            'default' => esc_html__( 'Subscribe To Our Newsletter And Get Daily 10% Off Your First Purchase.', 'xevsocore' ),
        ),
        array(
            'id'          => 'select_newsletter',
            'type'        => 'select',
            'title'       => esc_html__( 'Select Type', 'xevsocore' ),
            'placeholder' => esc_html__( 'Select an option', 'xevsocore' ),
            'options'     => array(
                '1' => esc_html__( 'Shortcode form Plugin', 'xevsocore' ),
                '2' => esc_html__( 'Add Link', 'xevsocore' ),
            ),
            'default'     => '2',
        ),
        array(
            'id'         => 'newsletter_shortcode',
            'type'       => 'textarea',
            'title'      => esc_html__( 'Add Shortcode', 'xevsocore' ),
            'desc'       => esc_html__( 'Add Shortcode from Mailchip wordpress Plugin', 'xevsocore' ),
            'dependency' => array( 'select_newsletter', '==', '1' ),
        ),
        array(
            'id'         => 'newsletter_link',
            'type'       => 'textarea',
            'title'      => esc_html__( 'Add Link', 'xevsocore' ),
            'desc'       => esc_html__( 'Add Newsletter Link from your Account', 'xevsocore' ),
            'dependency' => array( 'select_newsletter', '==', '2' ),
        ),
    ),
) );

// OutPut
if ( !function_exists( 'xevso_newsletter_widget' ) ) {
    function xevso_newsletter_widget( $args, $instance ) {
        echo wp_kses_post( $args['before_widget'] );
        if ( !empty( $instance['title'] ) ) {
            echo wp_kses_post( $args['before_title'] ) . apply_filters( 'widget_title widtet-title', $instance['title'] ) . wp_kses_post( $args['after_title'] );
        }
        ?>
        <div class="footer-subscribe">
        <?php if ( !empty( $instance['newsletter_dec'] ) ): ?>
            <div class="ft-subscribe-dec">
                <p>
                    <?php echo esc_html( $instance['newsletter_dec'] ); ?>
                </p>
            </div>
            <?php endif;?>
            <?php
if ( $instance['select_newsletter'] == '1' ) {
            ?>
                <div class="ft-newletter">
                    <?php echo do_shortcode( $instance['newsletter_shortcode'] ); ?>
                </div>
                <?php
} else {
            ?>
            <div class="ft-newletter">
                <form action="<?php echo esc_url( $instance['newsletter_link'] ) ?>" method="post">
                    <div class="mc4wp-form-fields">
                        <input type="email" name="EMAIL" placeholder="Your email address" required="" />
                        <button value="submit"><i class="fa fa-location-arrow"></i></button>
                    </div>
                </form>
            </div>
            <?php
}
        ?>
        </div>
        <?php
echo wp_kses_post( $args['after_widget'] );
    }
}

// Social Links
CSF::createWidget( 'xevso_social_widget', array(
    'title'       => esc_html__( 'xevso Social Widget', 'xevsocore' ),
    'classname'   => 'xevso-social-widgets',
    'description' => esc_html__( 'Add Your Social Info', 'xevsocore' ),
    'fields'      => array(
        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__( 'Title', 'xevsocore' ),
        ),
        array(
            'id'    => 'xevso_slabel',
            'type'  => 'text',
            'title' => esc_html__( 'Label', 'xevsocore' ),
        ),
        array(
            'id'      => 'xevso_socials_widget',
            'type'    => 'group',
            'title'   => esc_html__( 'Add Social Links', 'xevsocore' ),
            'fields'  => array(
                array(
                    'id'    => 'xevso_social_label',
                    'type'  => 'text',
                    'title' => esc_html__( 'Name', 'xevsocore' ),
                ),
                array(
                    'id'    => 'xevso_social_link',
                    'type'  => 'text',
                    'title' => esc_html__( 'Site Link', 'xevsocore' ),
                ),
                array(
                    'id'    => 'xevso_social_icon',
                    'type'  => 'icon',
                    'title' => esc_html__( 'Site Icon', 'xevsocore' ),
                ),
            ),
            'default' => array(
                array(
                    'xevso_social_label' => esc_html__( 'Facebook', 'xevsocore' ),
                    'xevso_social_link'  => '#',
                    'xevso_social_icon'  => 'fas fa-facebook',
                ),
                array(
                    'xevso_social_label' => esc_html__( 'Twitter', 'xevsocore' ),
                    'xevso_social_link'  => '#',
                    'xevso_social_icon'  => 'fas fa-twitter',
                ),
                array(
                    'xevso_social_label' => esc_html__( 'Linkedin', 'xevsocore' ),
                    'xevso_social_link'  => '#',
                    'xevso_social_icon'  => 'fas fa-linkedin',
                ),
                array(
                    'xevso_social_label' => esc_html__( 'Instagram', 'xevsocore' ),
                    'xevso_social_link'  => '#',
                    'xevso_social_icon'  => 'fas fa-instagram',
                ),
            ),
        ),
    ),
) );

// OutPut
if ( !function_exists( 'xevso_social_widget' ) ) {
    function xevso_social_widget( $args, $instance ) {
        echo wp_kses_post( $args['before_widget'] );
        if ( !empty( $instance['title'] ) ) {
            echo wp_kses_post( $args['before_title'] ) . apply_filters( 'widget_title widtet-title', $instance['title'] ) . wp_kses_post( $args['after_title'] );
        }
        ?>
            <?php if ( !empty( $instance['xevso_slabel'] ) ) {
            echo ' <label>' . esc_html( $instance['xevso_slabel'] ) . '</label>';
        }?>
            <ul>
            <?php foreach ( $instance['xevso_socials_widget'] as $social ) {
            echo ' <li><a href="' . esc_url( $social['xevso_social_link'] ) . '" data-toggle="tooltip" data-placement="top" title="' . esc_attr( $social['xevso_social_label'] ) . '"><i class="' . esc_attr( $social['xevso_social_icon'] ) . '"></i></a></li>';
        }
        ?>
            </ul>
        <?php
echo wp_kses_post( $args['after_widget'] );
    }
}

// Social Links
CSF::createWidget( 'xevso_nav_widget', array(
    'title'       => esc_html__( 'xevso Nav Widget', 'xevsocore' ),
    'classname'   => 'xevso-nav-widgets',
    'description' => esc_html__( 'Add Your Nav Nemu', 'xevsocore' ),
    'fields'      => array(
        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__( 'Title', 'xevsocore' ),
        ),
        array(
            'id'      => 'xevso_column',
            'type'    => 'slider',
            'title'   => esc_html__( 'Column', 'xevsocore' ),
            'min'     => 10,
            'max'     => 100,
            'step'    => 1,
            'default' => 100,
            'unit'    => '%',
            'output_important'  => true
        ),
        array(
            'id'          => 'xevso_navs',
            'type'        => 'select',
            'title'       => esc_html__( 'Select Menu', 'xevsocore' ),
            'placeholder' =>esc_html__( 'Select A Menu', 'xevsocore' ),
            'options'     => 'menus',
        ),
    ),
) );

// OutPut
if ( !function_exists( 'xevso_nav_widget' ) ) {
    function xevso_nav_widget( $args, $instance ) {
        ?>
        <style>
            .xevso-nav-widgets>nav>ul>li{
                width:<?php echo esc_attr($instance['xevso_column']); ?>%
            }
        </style>
        <?php 
        echo wp_kses_post( $args['before_widget'] );
        if ( !empty( $instance['title'] ) ) {
            echo wp_kses_post( $args['before_title'] ) . apply_filters( 'widget_title widtet-title', $instance['title'] ) . wp_kses_post( $args['after_title'] );
        }
        wp_nav_menu(array(
            'fallback_cb'          => '',
            'menu'                 => $instance['xevso_navs'],
            'container'            => 'nav',
            'items_wrap'           => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        ));
    echo wp_kses_post( $args['after_widget'] );
    }
}