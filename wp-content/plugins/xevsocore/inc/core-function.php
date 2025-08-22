<?php 
// Page List
function xevso_slide_list() {
    $args = wp_parse_args( array(
        'post_type'   => 'slider',
        'numberposts' => -1,
    ) );
    $posts = get_posts( $args );
    $post_options = array( esc_html__( '-- Select Slider --', 'xevsocore' ) => '' );
    if ( $posts ) {
        foreach ( $posts as $post ) {
            $post_options[$post->ID] = $post->post_title;
        }
    }
    return $post_options;
}

// Slider List
function xevso_page_list() {
    $args = wp_parse_args( array(
        'post_type'   => 'page',
        'numberposts' => -1,
    ) );
    $posts = get_posts( $args );
    $post_options = array( esc_html__( '-- Select Page --', 'xevsocore' ) => '' );
    if ( $posts ) {
        foreach ( $posts as $post ) {
            $post_options[$post->ID] = $post->post_title;
        }
    }
    return $post_options;
}

// Custom paginations Start
if ( !function_exists( 'xevso_paginate_nav' ) ):
    function xevso_paginate_nav( $xevsoQuery = null ) {
        if ( empty( $xevsoQuery ) ):
            $xevsoQuery = $GLOBALS['wp_query'];
        endif;
        // Don't print empty markup if there's only one page.
        if ( $xevsoQuery->max_num_pages < 2 ) {
            return;
        }
        $paged = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
        $pagenum_link = html_entity_decode( get_pagenum_link() );
        $query_args = array();
        $url_parts = explode( '?', $pagenum_link );
        if ( isset( $url_parts[1] ) ) {
            wp_parse_str( $url_parts[1], $query_args );
        }
        $pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
        $pagenum_link = trailingslashit( $pagenum_link ) . '%_%';
        $format = $GLOBALS['wp_rewrite']->using_index_permalinks() && !strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
        $format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';
        // Set up paginated links.
        $links = paginate_links( array(
            'base'      => $pagenum_link,
            'format'    => $format,
            'total'     => $xevsoQuery->max_num_pages,
            'current'   => $paged,
            'add_args'  => array_map( 'urlencode', $query_args ),
            'prev_text' => '<i class="fa fa-angle-left" aria-hidden="true"></i>',
            'next_text' => '<i class="fa fa-angle-right" aria-hidden="true"></i>',
            'type'      => 'array',
        ) );
        if ( $links ):
        ?>
		<div class="pagination-area">
			<ul class="">
                <?php foreach ( $links as $key => $page_link ): ?>
                    <li class="<?php if ( strpos( $page_link, 'current' ) !== false ) {echo ' active';}?>"><?php echo str_replace( 'span', 'a', $page_link ) ?></li>
                <?php endforeach?>
			</ul>
		</div><!-- .navigation -->
		<?php
endif;
}
endif;
// Custom paginations End

// Author Social Info
function xevso_user_contact_methot( $cm ) {
    $cm['facebook'] = esc_html__( 'Facebook', 'xevsocore' );
    $cm['twitter'] = esc_html__( 'Twitter', 'xevsocore' );
    $cm['linkedin'] = esc_html__( 'Linkedin', 'xevsocore' );
    $cm['pinterest'] = esc_html__( 'Pinterest', 'xevsocore' );
    $cm['instagram'] = esc_html__( 'Instagram', 'xevsocore' );
    $cm['dribbble'] = esc_html__( 'Dribbble', 'xevsocore' );
    return $cm;
}
add_filter( 'user_contactmethods', 'xevso_user_contact_methot' );

if ( !function_exists( 'xevso_post_share_social' ) ) {
    function xevso_post_share_social() {
        global $post;
        $crunchifyURL = esc_url( get_permalink() );
        $crunchifyTitle = str_replace( ' ', '%20', esc_url( get_the_title() ) );

        $twitterURL = 'https://twitter.com/intent/tweet?text=' . $crunchifyTitle . '&amp;url=' . $crunchifyURL . '&amp;via=Crunchify';
        $facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $crunchifyURL;
        $googleURL = 'https://plus.google.com/share?url=' . $crunchifyURL;
        $linkedinURL = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $crunchifyURL;
        ?>
           <a data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e( 'Facebook', 'xevsocore' )?>" data-original-title="<?php esc_attr_e( 'Facebook', 'xevsocore' )?>" class="xevso-sfacebook" href="<?php echo esc_url( $facebookURL ); ?>" target="_blank"><i class="fa fa-facebook"></i></a>

           <a data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e( 'Twitter', 'xevsocore' )?>" data-original-title="<?php esc_attr_e( 'Twitter', 'xevsocore' )?>" class="xevso-stiwtter" href="<?php echo esc_url( $twitterURL ); ?>" target="_blank"><i class="fa fa-twitter"></i></a>

           <a data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e( 'Google plus', 'xevsocore' )?>" data-original-title="<?php esc_attr_e( 'Google plus', 'xevsocore' )?>" class="xevso-spin" href="<?php echo esc_url( $googleURL ); ?>" target="_blank"><i class="fa fa-google-plus"></i></a>

           <a data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e( 'Linkedin', 'xevsocore' )?>" data-original-title="<?php esc_attr_e( 'Linkedin', 'xevsocore' )?>" class="xevso-slinked" href="<?php echo esc_url( $linkedinURL ); ?>" target="_blank"><i class="fa fa-linkedin"></i></a>
        <?php
    }
}
// Category Elementor Name 
function add_elementor_widget_categories( $elements_manager ) {
    $elements_manager->add_category(
        'xevsocore',
        [
            'title' => __( 'xevso Addons', 'xevsocore' ),
            'icon'  => 'fa fa-plug',
        ]
    );
}
add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories' );


if( ! function_exists( 'xevso_font_family' ) ) {

    function xevso_font_family( $fonts ) {
        //
        // Adding new icons
        $fonts['Gilroy'] = array('normal');
        $fonts['Gilroy-bold'] = array( '500', 'normal', '600', '700','800' );
        return $fonts;
    }
    add_filter( 'csf_field_typography_customwebfonts', 'xevso_font_family' );
}

// Codestar Options 
if ( ! function_exists( 'xevso_after_content_import_execution' ) ) {
	function xevso_after_content_import_execution( $selected_import_files, $import_files, $selected_index ) {
	  $downloader = new OCDI\Downloader();
	  if( ! empty( $import_files[$selected_index]['import_json'] ) ) {
		foreach( $import_files[$selected_index]['import_json'] as $index => $import ) {
		  $file_path = $downloader->download_file( $import['file_url'], 'demo-json-import-file-'. $index . '-'. date( 'Y-m-d__H-i-s' ) .'.json' );
		  $file_raw  = OCDI\Helpers::data_from_file( $file_path );
		  update_option( $import['option_name'], json_decode( $file_raw, true ) );
		}
	  } else if( ! empty( $import_files[$selected_index]['local_import_json'] ) ) {
  
		foreach( $import_files[$selected_index]['local_import_json'] as $index => $import ) {
		  $file_path = $import['file_path'];
		  $file_raw  = OCDI\Helpers::data_from_file( $file_path );
		  update_option( $import['option_name'], json_decode( $file_raw, true ) );
		}
	  }
	  $ocdi       = OCDI\OneClickDemoImport::get_instance();
	  $log_path   = $ocdi->get_log_file_path();
  
	  OCDI\Helpers::append_to_file( 'Custom Framework file loaded.', $log_path );
	}
	add_action('pt-ocdi/after_content_import_execution', 'xevso_after_content_import_execution', 3, 99 );
  }