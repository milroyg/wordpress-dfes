<?php
/**
* Vertical Post Slider Widget Class.
*
* @package Post Slider and Carousel
* @since 1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PSAC_Post_Scrolling_Widget extends WP_Widget {

	// Widget variables
	var $defaults;

	function __construct() {

		/*
		 * Widget settings
		 * Note : String will be escaped in WordPress core widget mechanism
		 */
		$widget_ops = array('classname' => 'psacp-post-scrolling-widget', 'description' => __('Display posts with vertical slider view.', 'post-slider-and-carousel') );

		/*
		 * Create the widget
		 * Note : String will be escaped in WordPress core widget mechanism
		 */
		parent::__construct( 'psacp-post-scrolling-widget', __('PSAC - Post Vertical Slider Widget', 'post-slider-and-carousel'), $widget_ops);

		// Widgets defaults
		$this->defaults = array(
			'title' 				=> __( 'Latest Posts', 'post-slider-and-carousel' ),
			'show_date'				=> 1,
			'show_category'			=> 1,
			'show_image'			=> 1,
			'show_content'			=> 0,
			'content_words_limit'	=> 20,
			'media_size' 			=> 'thumbnail',
			'category' 				=> '',
			'height'				=> 400,
			'pause'					=> 4000,
			'speed'					=> 600,
			'limit' 				=> 5,
			'order'					=> 'DESC',
			'orderby'				=> 'date',
			'query_offset'			=> '',
			'css_class'				=> '',
			'tab'					=> 'general',
		);
	}

	/**
	 * Updates the widget control options
	 *
	 * @since 1.0
	 */
	function update( $new_instance, $old_instance ) {

		$instance		= $old_instance;
		$new_instance 	= wp_parse_args( (array) $new_instance, $this->defaults );

		// Input fields
		$instance['title']					= psac_clean( $new_instance['title'] );
		$instance['category']				= psac_clean( $new_instance['category'] );
		$instance['show_image']				= ( !empty( $new_instance['show_image'] ) ) 			? 1 : 0;
		$instance['media_size']				= psac_clean( $new_instance['media_size'] );
		$instance['orderby']				= psac_clean( $new_instance['orderby'] );
		$instance['show_date']				= ( !empty( $new_instance['show_date'] ) ) 				? 1 : 0;
		$instance['show_category']			= ( !empty( $new_instance['show_category'] ) ) 			? 1 : 0;
		$instance['show_content']			= ( !empty( $new_instance['show_content'] ) )			? 1 : 0;
		$instance['limit']					= psac_clean_number( $new_instance['limit'], 5, 'number' );
		$instance['query_offset']			= psac_clean_number( $new_instance['query_offset'], '' );
		$instance['content_words_limit']	= psac_clean_number( $new_instance['content_words_limit'], 20 );
		$instance['pause']					= psac_clean_number( $new_instance['pause'], 4000 );
		$instance['speed']					= psac_clean_number( $new_instance['speed'], 600 );
		$instance['pause']					= ( $instance['pause'] > 500 ) ? $instance['pause'] : 500;
		$instance['speed']					= ( $instance['speed'] > 300 ) ? $instance['speed'] : 300;
		$instance['speed']					= ( $instance['speed'] >= $instance['pause'] ) ? $instance['pause'] : $instance['speed'];
		$instance['order']					= ( strtolower($new_instance['order']) == 'asc' ) ? 'ASC' : 'DESC';
		$instance['height']					= is_numeric( $new_instance['height'] ) ? psac_clean_number( $new_instance['height'], 400 ) : 'auto';
		$instance['css_class']				= psac_sanitize_html_classes( $new_instance['css_class'] );
		$instance['tab']					= psac_clean( $new_instance['tab'] );

		return $instance;
	}

	/**
	 * Displays the widget form in widget area
	 *
	 * @since 1.0
	 */
	function form( $instance ) {
		
		$instance = wp_parse_args( (array) $instance, $this->defaults );
?>
		<div class="psacp-widget-content">
			<div class="psacp-widget-title psacp-widget-acc" data-target="general"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e('General Fields', 'post-slider-and-carousel'); ?> <span class="dashicons dashicons-arrow-down-alt2" title="<?php esc_attr_e('Click to toggle', 'post-slider-and-carousel'); ?>"></span></div>
			<div class="psacp-widget-acc-cnt-wrap psacp-widget-general <?php if( $instance['tab'] != 'general' ) { echo 'psacp-hide'; } ?>">
				<!-- Title -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'post-slider-and-carousel'); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
				</p>

				<!-- Show Date -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('show_date') ); ?>"><?php esc_html_e( 'Show Date', 'post-slider-and-carousel' ); ?>:</label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>">
						<option value="1" <?php selected( $instance['show_date'], 1 ); ?>><?php esc_html_e('Yes', 'post-slider-and-carousel'); ?></option>
						<option value="0" <?php selected( $instance['show_date'], 0 ); ?>><?php esc_html_e('No', 'post-slider-and-carousel'); ?></option>
					</select>
				</p>

				<!-- Show Category -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('show_category') ); ?>"><?php esc_html_e( 'Show Category', 'post-slider-and-carousel' ); ?>:</label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_category' ) ); ?>">
						<option value="1" <?php selected( $instance['show_category'], 1 ); ?>><?php esc_html_e('Yes', 'post-slider-and-carousel'); ?></option>
						<option value="0" <?php selected( $instance['show_category'], 0 ); ?>><?php esc_html_e('No', 'post-slider-and-carousel'); ?></option>
					</select>
				</p>
				
				<!-- Show Content -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('show_content') ); ?>"><?php esc_html_e( 'Show Short Content', 'post-slider-and-carousel' ); ?>:</label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_content' ) ); ?>">
						<option value="1" <?php selected( $instance['show_content'], 1 ); ?>><?php esc_html_e('Yes', 'post-slider-and-carousel'); ?></option>
						<option value="0" <?php selected( $instance['show_content'], 0 ); ?>><?php esc_html_e('No', 'post-slider-and-carousel'); ?></option>
					</select>
				</p>

				<!-- Content Word Limit -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'content_words_limit' ) ); ?>"><?php esc_html_e( 'Content Word Limit', 'post-slider-and-carousel'); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'content_words_limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content_words_limit' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['content_words_limit'] ); ?>" />
					<em><?php esc_html_e( 'Enter Content word limit e.g 20. Content word limit will only work if Show Short Content is set to Yes.', 'post-slider-and-carousel' ); ?></em>
				</p>
				
				<!-- Show image -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('show_image') ); ?>"><?php esc_html_e( 'Show Media Image', 'post-slider-and-carousel' ); ?>:</label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_image' ) ); ?>">
						<option value="1" <?php selected( $instance['show_image'], 1 ); ?>><?php esc_html_e('Yes', 'post-slider-and-carousel'); ?></option>
						<option value="0" <?php selected( $instance['show_image'], 0 ); ?>><?php esc_html_e('No', 'post-slider-and-carousel'); ?></option>
					</select>
				</p>

				<!-- Image Size -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('media_size') ); ?>"><?php esc_html_e( 'Image Size', 'post-slider-and-carousel' ); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('media_size') ); ?>" name="<?php echo esc_attr( $this->get_field_name('media_size') ); ?>" type="text" value="<?php echo esc_attr( $instance['media_size'] ); ?>" />
					<em><?php esc_html_e( 'Choose WordPress registered media size. e.g thumbnail, medium, large, full.', 'post-slider-and-carousel' ); ?></em>
				</p>

				<!-- CSS Class -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('css_class') ); ?>"><?php esc_html_e( 'CSS Class', 'post-slider-and-carousel' ); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('css_class') ); ?>" name="<?php echo esc_attr( $this->get_field_name('css_class') ); ?>" type="text" value="<?php echo esc_attr( $instance['css_class'] ); ?>" />
					<em><?php esc_html_e( 'Add an extra CSS class for designing purpose.', 'post-slider-and-carousel' ); ?></em>
				</p>
			</div><!-- end .psacp-widget-acc-cnt-wrap -->

			<div class="psacp-widget-title psacp-widget-acc" data-target="slider"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e('Slider Fields', 'post-slider-and-carousel'); ?> <span class="dashicons dashicons-arrow-down-alt2" title="<?php esc_attr_e('Click to toggle', 'post-slider-and-carousel'); ?>"></span></div>
			<div class="psacp-widget-acc-cnt-wrap psacp-widget-slider <?php if( $instance['tab'] != 'slider' ) { echo 'psacp-hide'; } ?>">

				<!-- Height -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php esc_html_e( 'Height', 'post-slider-and-carousel'); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['height'] ); ?>" />
					<em><?php esc_html_e( 'Enter "auto" for auto height or numerical value e.g. 400 for fixed height.', 'post-slider-and-carousel' ); ?></em>
				</p>
				
				<!-- Pause -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'pause' ) ); ?>"><?php esc_html_e( 'Interval', 'post-slider-and-carousel'); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pause' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pause' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['pause'] ); ?>" />
					<em><?php esc_html_e( 'Default value is 4000. Value should not be less than 500.', 'post-slider-and-carousel' ); ?></em>
				</p>

				<!-- Speed -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>"><?php esc_html_e( 'Speed', 'post-slider-and-carousel'); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'speed' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['speed'] ); ?>" />
					<em><?php esc_html_e( 'Default value is 600. Value should not be less than 300 and equal to `Interval`.', 'post-slider-and-carousel' ); ?></em>
				</p>
			</div><!-- end .psacp-widget-acc-cnt-wrap -->

			<div class="psacp-widget-title psacp-widget-acc" data-target="query"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e('Query Fields', 'post-slider-and-carousel'); ?> <span class="dashicons dashicons-arrow-down-alt2" title="<?php esc_attr_e('Click to toggle', 'post-slider-and-carousel'); ?>"></span></div>
			<div class="psacp-widget-acc-cnt-wrap psacp-widget-query <?php if( $instance['tab'] != 'query' ) { echo 'psacp-hide'; } ?>">
				<!-- Limit -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number of Items', 'post-slider-and-carousel'); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['limit'] ); ?>" />
				</p>

				<!-- Order By -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order By', 'post-slider-and-carousel' ); ?>:</label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
						<option value="date" <?php selected( $instance['orderby'], 'date' ); ?>><?php esc_html_e( 'Post Date', 'post-slider-and-carousel' ); ?></option>
						<option value="modified" <?php selected( $instance['orderby'], 'modified' ); ?>><?php esc_html_e( 'Post Updated Date', 'post-slider-and-carousel' ); ?></option>
						<option value="ID" <?php selected( $instance['orderby'], 'ID' ); ?>><?php esc_html_e( 'Post Id', 'post-slider-and-carousel' ); ?></option>
						<option value="title" <?php selected( $instance['orderby'], 'title' ); ?>><?php esc_html_e( 'Post Title', 'post-slider-and-carousel' ); ?></option>
						<option value="name" <?php selected( $instance['orderby'], 'name' ); ?>><?php esc_html_e( 'Post Slug', 'post-slider-and-carousel' ); ?></option>
						<option value="rand" <?php selected( $instance['orderby'], 'rand' ); ?>><?php esc_html_e( 'Random', 'post-slider-and-carousel' ); ?></option>
						<option value="menu_order" <?php selected( $instance['orderby'], 'menu_order' ); ?>><?php esc_html_e( 'Menu Order (Sort Order)', 'post-slider-and-carousel' ); ?></option>
						<option value="comment_count" <?php selected( $instance['orderby'], 'comment_count' ); ?>><?php esc_html_e( 'Number of Comments', 'post-slider-and-carousel' ); ?></option>
						<option value="author" <?php selected( $instance['orderby'], 'author' ); ?>><?php esc_html_e( 'Post Author', 'post-slider-and-carousel' ); ?></option>
						<option value="relevance" <?php selected( $instance['orderby'], 'relevance' ); ?>><?php esc_html_e( 'Relevance', 'post-slider-and-carousel' ); ?></option>
					</select>
				</p>

				<!-- Order -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order', 'post-slider-and-carousel' ); ?>:</label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
						<option value="asc" <?php selected( $instance['order'], 'ASC' ); ?>><?php esc_html_e( 'Ascending', 'post-slider-and-carousel' ); ?></option>
						<option value="desc" <?php selected( $instance['order'], 'DESC' ); ?>><?php esc_html_e( 'Descending', 'post-slider-and-carousel' ); ?></option>
					</select>
				</p>

				<!-- Category -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('category') ); ?>"><?php esc_html_e( 'Display Specific Category', 'post-slider-and-carousel' ); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('category') ); ?>" name="<?php echo esc_attr( $this->get_field_name('category') ); ?>" type="text" value="<?php echo esc_attr( $instance['category'] ); ?>" />
					<em><?php esc_html_e( 'Enter category id or category slug to display categories wise posts.', 'post-slider-and-carousel' ); ?> <label title="<?php esc_attr_e("You can pass multiple ids or slug with comma seperated. You can find id or slug at relevant category listing page. \n\nPlease be sure that you have added valid category id or slug otherwise no result will be displayed.", 'post-slider-and-carousel'); ?>">[?]</label></em>
				</p>

				<!-- Query Offset -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('query_offset') ); ?>"><?php esc_html_e( 'Query Offset', 'post-slider-and-carousel' ); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('query_offset') ); ?>" name="<?php echo esc_attr( $this->get_field_name('query_offset') ); ?>" type="text" value="<?php echo esc_attr( $instance['query_offset'] ); ?>" />
					<em><?php esc_html_e('Query `offset` parameter to exclude number of post. Leave empty for default.', 'post-slider-and-carousel'); ?></em><br/>
					<em><?php esc_html_e('Note: This parameter will not work when Number of Items is set to -1.', 'post-slider-and-carousel'); ?></em>
				</p>
			</div><!-- end .psacp-widget-acc-cnt-wrap -->

			<input type="hidden" name="<?php echo esc_attr( $this->get_field_name('tab') ); ?>" value="<?php echo esc_attr( $instance['tab'] ); ?>" class="psacp-widget-sel-tab" />
			<div class="psacp-widget-loader"></div>
		</div><!-- end .psacp-widget-content -->
	<?php
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @since 1.0
	 */
	function widget( $widget_args, $instance ) {
		
		// Taking some globals
		global $post;

		$atts 					= wp_parse_args( (array) $instance, $this->defaults );
		$title					= apply_filters( 'widget_title', $atts['title'], $atts, $this->id_base );
		$atts['category'] 		= ! empty( $atts['category'] ) ? explode(',', $atts['category']) : array();
		$atts['unique']			= psac_get_unique();

		// enqueue script
		wp_enqueue_script( 'jquery-vticker' );
		wp_enqueue_script( 'psacp-public-script' );
		psac_enqueue_script();

		// Taking some variables
		$atts['slider_conf'] 	= array( 'speed' => $atts['speed'], 'height' => $atts['height'], 'pause' => $atts['pause'] );
		$atts['image_style']	= '';

		// WP Query Parameters
		$args = array( 
			'post_type'				=> PSAC_POST_TYPE,
			'post_status' 			=> array('publish'),
			'order'					=> $atts['order'],
			'orderby'				=> $atts['orderby'],
			'posts_per_page' 		=> $atts['limit'],
			'offset'				=> $atts['query_offset'],
			'no_found_rows'			=> true,
			'ignore_sticky_posts'	=> true,
		);

		// Category Parameter
		if( $atts['category'] ) {

			$args['tax_query'] = array(
									array(
										'taxonomy'	=> PSAC_CAT,
										'terms'		=> $atts['category'],
										'field'		=> ( isset( $atts['category'][0] ) && is_numeric( $atts['category'][0] ) ) ? 'term_id' : 'slug',
									));
		}

		$args = apply_filters( 'psacp_post_scrolling_widget_query_args', $args, $atts );

		// WP Query
		$query = new WP_Query( $args );

		// Start Widget Output
		echo $widget_args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo $widget_args['before_title'] . $title . $widget_args['after_title']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		if( $query->have_posts() ) {

			include( PSAC_DIR . "/templates/widget/post-scrolling/loop-start.php" );

			while ( $query->have_posts() ) : $query->the_post();
				
				$atts['format']		= psac_get_post_format();
				$atts['feat_img'] 	= psac_get_post_feat_image( $post->ID, $atts['media_size'], true );
				$atts['post_link'] 	= psac_get_post_link( $post->ID );
				$atts['cate_name'] 	= psac_get_post_terms( $post->ID, PSAC_CAT );
				
				$atts['wrp_cls']	= "psacp-post-{$atts['format']}";
				$atts['wrp_cls']	.= ( is_sticky( $post->ID ) ) ? ' psacp-sticky' 	: '';
				$atts['wrp_cls'] 	.= empty($atts['feat_img'])	  ? ' psacp-no-thumb'	: ' psacp-has-thumb';				

				// Creating image style
				if( $atts['feat_img'] ) {
					$atts['image_style'] = 'background-image:url('.esc_url( $atts['feat_img'] ).');';
				}

				// Include Dsign File
				include( PSAC_DIR . "/templates/widget/post-scrolling/design-1.php" );

			endwhile;

			include( PSAC_DIR . "/templates/widget/post-scrolling/loop-end.php" );

		} // end of have_post()

		wp_reset_postdata(); // Reset WP Query

		echo $widget_args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}
}