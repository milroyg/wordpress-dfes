<?php
/**
 * Shortcode Fields for Shortcode Preview
 *
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Generate 'psac_post_slider' shortcode fields
 * 
 * @since 1.0
 */
function psac_post_slider_lite_shortcode_fields( $shortcode = '' ) {
	$fields = array(
			// General Settings
			'general' => array(
					'title'     => __('General & Designs', 'post-slider-and-carousel'),
					'params'   	=>  array(
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Design', 'post-slider-and-carousel' ),
											'name' 		=> 'design',
											'value' 	=> psac_post_slider_designs(),
											'desc' 		=> __( 'Choose layout design.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Image Size', 'post-slider-and-carousel' ),
											'name' 			=> 'media_size',
											'value' 		=> 'large',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Choose WordPress registered image size. e.g.', 'post-slider-and-carousel' ).' thumbnail, medium, large, full.',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'CSS Class', 'post-slider-and-carousel' ),
											'name' 			=> 'css_class',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Enter an extra CSS class for design customization.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('Extra class will be added at top most parent so using extra class you customize your design.', 'post-slider-and-carousel').'"> [?]</label>',
										)
									)
			),

			// Slider Fields
			'slider' => array(
					'title'		=> __('Slider', 'post-slider-and-carousel'),
					'params'    => array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Loop', 'post-slider-and-carousel' ),
											'name' 			=> 'loop',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable slider loop.', 'post-slider-and-carousel' ),
										),
										array(
											'type'		=> 'dropdown',
											'heading' 	=> __( 'Show Arrows', 'post-slider-and-carousel' ),
											'name' 		=> 'arrows',
											'value' 	=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc'		=> __( 'Show prev - next arrows.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Show Dots', 'post-slider-and-carousel' ),
											'name' 		=> 'dots',
											'value' 	=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 		=> __( 'Show pagination dots.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Autoplay', 'post-slider-and-carousel' ),
											'name' 			=> 'autoplay',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable slider autoplay.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Autoplay Interval', 'post-slider-and-carousel' ),
											'name' 			=> 'autoplay_interval',
											'value' 		=> 5000,
											'desc' 			=> __( 'Enter autoplay interval.', 'post-slider-and-carousel' ),
											'dependency' 	=> array(
																	'element' 	=> 'autoplay',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Speed', 'post-slider-and-carousel' ),
											'name' 			=> 'speed',
											'value' 		=> 500,
											'desc' 			=> __( 'Enter slider speed.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slider Height', 'post-slider-and-carousel' ),
											'name' 			=> 'sliderheight',
											'value' 		=> '',
											'desc' 			=> __( 'Enter slider height i.e. 500.', 'post-slider-and-carousel' ),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'post-slider-and-carousel' ),
											'desc'			=> sprintf( __( '%s Unlock more Slider options like Show Thumbnail etc.', 'post-slider-and-carousel' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Autoplay Pause on Hover', 'post-slider-and-carousel' ),
											'name' 			=> 'autoplay_hover_pause',
											'value' 		=> array(
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
															),
											'desc' 			=> __( 'Autoplay pause on hover.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Previous Button Text', 'post-slider-and-carousel' ),
											'name' 			=> 'prev_text',
											'value' 		=> '',
											'desc' 			=> __( 'Slider previous button text.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Next Button Text', 'post-slider-and-carousel' ),
											'name' 			=> 'next_text',
											'value' 		=> '',
											'desc' 			=> __( 'Slider next button text.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Slider Auto Height', 'post-slider-and-carousel' ),
											'name' 			=> 'auto_height',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable slider auto height.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slider Start Position', 'post-slider-and-carousel' ),
											'name' 			=> 'start_position',
											'value' 		=> '',
											'desc' 			=> __( 'Enter slide number to start from that.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slide Margin', 'post-slider-and-carousel' ),
											'name' 			=> 'slide_margin',
											'value' 		=> 5,
											'desc' 			=> __( 'Slide margin.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slider Stage Padding', 'post-slider-and-carousel' ),
											'name' 			=> 'stage_padding',
											'value' 		=> 0,
											'desc' 			=> __( 'Enter slider stage padding. A partial slide will be visible at both the end.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Thumbnail', 'post-slider-and-carousel' ),
											'name' 			=> 'show_thumbnail',
											'value' 		=> array(
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display slider thumbnail.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Number of Thumbnails', 'post-slider-and-carousel' ),
											'name' 			=> 'thumbnail',
											'value' 		=> 7,
											'min'			=> 1,
											'desc' 			=> __( 'Enter number of thumbnails. The ideal value should be 7.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('Note: Number of thumbnails will adjust according to responsive layout mode.', 'post-slider-and-carousel').'"> [?]</label>',
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'URL Hash Listner', 'post-slider-and-carousel' ),
											'name' 			=> 'url_hash_listener',
											'value' 		=> array(
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable url hash listner of slider.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Lazyload', 'post-slider-and-carousel' ),
											'name' 			=> 'lazyload',
											'value' 		=> array(
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'default'		=> 'false',
											'desc' 			=> __( 'Enable slider lazyload behaviour.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
								)
			),

			// Meta Fields
			'meta' => array(
					'title'     => __('Meta & Content', 'post-slider-and-carousel'),
					'params'   	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Post Date', 'post-slider-and-carousel' ),
											'name' 			=> 'show_date',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post date.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Author', 'post-slider-and-carousel' ),
											'name' 			=> 'show_author',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post author.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Tags', 'post-slider-and-carousel' ),
											'name' 			=> 'show_tags',
											'value' 		=> array( 
																	'false'		=> __( 'False', 'post-slider-and-carousel' ),
																	'true'		=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post tags.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Comments', 'post-slider-and-carousel' ),
											'name' 			=> 'show_comments',
											'value' 		=> array(
																	'true'		=> __( 'True', 'post-slider-and-carousel' ),
																	'false'		=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post comment count.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Category', 'post-slider-and-carousel' ),
											'name' 			=> 'show_category',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'post-slider-and-carousel' ),
																	'false'		=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post category.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Content', 'post-slider-and-carousel' ),
											'name' 			=> 'show_content',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post content.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Content Word Limit', 'post-slider-and-carousel' ),
											'name' 			=> 'content_words_limit',
											'value' 		=> 20,
											'desc' 			=> __( 'Enter content word limit.', 'post-slider-and-carousel' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Read More', 'post-slider-and-carousel' ),
											'name' 			=> 'show_read_more',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Show read more.', 'post-slider-and-carousel' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Read More Text', 'post-slider-and-carousel' ),
											'name' 			=> 'read_more_text',
											'value' 		=> __( 'Read More', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Enter read more text.', 'post-slider-and-carousel' ),
											'refresh_time'	=> 1000,
											'dependency' 	=> array(
																	'element' 	=> 'show_read_more',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'post-slider-and-carousel' ),
											'desc'			=> sprintf( __( '%s Unlock more Meta & Content options like Read More Text etc.', 'post-slider-and-carousel' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Post Sub Title', 'post-slider-and-carousel' ),
											'name' 			=> 'show_sub_title',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post sub title.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Post Link Target', 'post-slider-and-carousel' ),
											'name'		=> 'link_behaviour',
											'value' 	=> array(
																'self'	=> __( 'Same Tab', 'post-slider-and-carousel' ),
																'new'	=> __( 'New Tab', 'post-slider-and-carousel' ),
															),
											'desc'		=> __( 'Choose post link behaviour.', 'post-slider-and-carousel' ),
											'premium'	=> true,
										),
								)
			),			

			// Data Fields
			'query' => array(
					'title'		=> __('Query', 'post-slider-and-carousel'),
					'params'    => array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post type', 'post-slider-and-carousel' ),
											'name' 			=> 'post_type',
											'value' 		=> psac_get_supported_post_types(),
											'default'		=> PSAC_POST_TYPE,
											'class'			=> 'psacp-post-type-sel',
											'ajax'			=> true,
											'desc' 			=> sprintf( __( 'Choose registered post type. You can enable it from plugin %ssetting%s page.', 'post-slider-and-carousel' ), '<a href="'.esc_url( PSAC_SETTING_PAGE_URL ).'" target="_blank">', '</a>' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Post', 'post-slider-and-carousel' ),
											'name' 			=> 'limit',
											'value' 		=> 20,
											'min'			=> -1,
											'validation'	=> 'number',
											'desc' 			=> __( 'Enter total number of post to be displayed. Enter -1 to display all.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Category', 'post-slider-and-carousel' ),
											'name' 			=> 'category',
											'class'			=> 'psacp-ajax-select2 psacp-category-sel',
											'multi'			=> true,
											'ajax'			=> true,
											'ajax_action'	=> 'psac_category_sugg',
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Choose categories to display category wise posts.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order By', 'post-slider-and-carousel' ),
											'name' 			=> 'orderby',
											'value' 		=>  array(
																	'date' 			=> __( 'Post Date', 'post-slider-and-carousel' ),
																	'ID' 			=> __( 'Post ID', 'post-slider-and-carousel' ),
																	'author' 		=> __( 'Post Author', 'post-slider-and-carousel' ),
																	'title' 		=> __( 'Post Title', 'post-slider-and-carousel' ),
																	'name' 			=> __( 'Post Slug', 'post-slider-and-carousel' ),
																	'modified' 		=> __( 'Post Modified Date', 'post-slider-and-carousel' ),
																	'menu_order'	=> __( 'Menu Order', 'post-slider-and-carousel' ),
																	'parent'		=> __( 'Parent ID', 'post-slider-and-carousel' ),
																	'rand' 			=> __( 'Random', 'post-slider-and-carousel' ),
																	'comment_count'	=> __( 'Number of Comments', 'post-slider-and-carousel' ),
																	'post__in'		=> __( 'Preserve Post ID Order', 'post-slider-and-carousel' ),
																	'relevance'		=> __( 'Relevance', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Select order type.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order', 'post-slider-and-carousel' ),
											'name' 			=> 'order',
											'value' 		=> array(
																	'desc'	=> __( 'Descending', 'post-slider-and-carousel' ),
																	'asc'	=>  __( 'Ascending', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Select sorting order.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 1', 'post-slider-and-carousel' ),
											'name' 			=> 'custom_param_1',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query, HTML etc via Hooks and Filters with the help of this parameter.', 'post-slider-and-carousel').'"> [?]</label>',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 2', 'post-slider-and-carousel' ),
											'name' 			=> 'custom_param_2',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query, HTML etc via Hooks and Filters with the help of this parameter.', 'post-slider-and-carousel').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'post-slider-and-carousel' ),
											'desc'			=> sprintf( __( '%s Unlock more Query options like Cat Taxonomy, Exclude By Category etc.', 'post-slider-and-carousel' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Taxonomy', 'post-slider-and-carousel' ),
											'name' 			=> 'taxonomy',
											'value' 		=> psac_get_post_type_taxonomy( PSAC_POST_TYPE ),
											'class'			=> 'psacp-taxonomy-sel',
											'desc' 			=> __( 'Choose registered taxonomy if you want to display category wise post.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Cat Taxonomy', 'post-slider-and-carousel' ),
											'name' 			=> 'cat_taxonomy',
											'class'			=> 'psacp-cat-taxonomy-sel',
											'value' 		=> array( '' => __('Select Taxonomy', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Choose a category taxonomy just to display categories as meta information.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Tag Taxonomy', 'post-slider-and-carousel' ),
											'name' 			=> 'tag_taxonomy',
											'class'			=> 'psacp-tag-taxonomy-sel',
											'value' 		=> array( '' => __('Select Taxonomy', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Choose a tag taxonomy just to display tags as meta information.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Category Operator', 'post-slider-and-carousel'),
											'name'			=> 'category_operator',
											'value'			=> array( 
																	'IN'	=> __( 'IN', 'post-slider-and-carousel' ),
																	'AND'	=> __( 'AND', 'post-slider-and-carousel' ),
																),
											'desc'			=> __( 'Select category operator. Default value is IN', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Display Child Category', 'post-slider-and-carousel'),
											'name'			=> 'include_cat_child',
											'value'			=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc'			=> __( 'Whether or not to include children category posts if parent category is there.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Category', 'post-slider-and-carousel' ),
											'name' 			=> 'exclude_cat',
											'value' 		=> array('' => __('Select Data', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Choose categories to exclude posts of it.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include Post', 'post-slider-and-carousel' ),
											'name' 			=> 'posts',
											'value' 		=> array('' => __('Select Data', 'post-slider-and-carousel') ),
											'desc' 			=> __('Choose posts which you want to display.', 'post-slider-and-carousel'),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude Post', 'post-slider-and-carousel' ),
											'name' 			=> 'hide_post',
											'value' 		=> array('' => __('Select Data', 'post-slider-and-carousel') ),
											'desc' 			=> __('Choose posts which you do not want to display.', 'post-slider-and-carousel'),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Author', 'post-slider-and-carousel' ),
											'name' 			=> 'author',
											'value' 		=> array('' => __('Select Data', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Choose authors to show posts associated with that.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Author', 'post-slider-and-carousel' ),
											'name' 			=> 'exclude_author',
											'value' 		=> array('' => __('Select Data', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Choose authors to hide posts associated with that. Works only if `Include Author` field is empty.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sticky Posts', 'post-slider-and-carousel' ),
											'name' 			=> 'sticky_posts',
											'value' 		=> array(
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display sticky posts. This only effects the frontend.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__("Note: Sticky post only be displayed at front side. In preview mode sticky post will not be displayed.", 'post-slider-and-carousel').'"> [?]</label>',
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Type', 'post-slider-and-carousel' ),
											'name' 			=> 'type',
											'value' 		=> array(
																	'' 			=> __( 'Select Type', 'post-slider-and-carousel' ),
																	'featured'	=> __( 'Featured', 'post-slider-and-carousel' ),
																	'trending'	=> __( 'Trending', 'post-slider-and-carousel'),
																),
											'desc' 			=> __( 'Select display type of post. Is it Featured or Trending?', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('Note: For trending post type make sure you have enabled the post type from Plugin Settings > Trending Post.', 'post-slider-and-carousel').'"> [?]</label>',
											'premium'		=> true,
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Query Offset', 'post-slider-and-carousel' ),
											'name' 			=> 'query_offset',
											'value' 		=> '',
											'desc' 			=> __( 'Skip number of posts from starting.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('e.g. 5 to skip over 5 posts. Note: Do not use limit=-1 and pagination=true with this.', 'post-slider-and-carousel').'"> [?]</label>',
											'premium'		=> true,
										),
									)
			),
			// Social Sharing
			'social_sharing' => array(
					'title'		=> __('Social Sharing', 'post-slider-and-carousel'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 			=> 'dropdown',
											'name' 			=> 'sharing',
											'value' 		=> array('' => __('No Social Sharing', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Enable social sharing. You can enable it from plugin setting page.', 'post-slider-and-carousel' ) . '<label> [?]</label>',
										),
									)
			),

			// Filter Settings
			'filter' => array(
					'title'		=> __('Filter', 'post-slider-and-carousel'),
					'premium'	=> true,
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Enable Filter', 'post-slider-and-carousel' ),
											'name' 			=> 'filter',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable category filter.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Filter Design', 'post-slider-and-carousel' ),
											'name' 		=> 'filter_design',
											'value' 	=> array( 
																	'design-1'	=> __( 'Design 1', 'post-slider-and-carousel' ),
															),
											'desc' 		=> __( 'Choose filter design.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter All Text', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_all_text',
											'value' 		=> __( 'All', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Enter `ALL` field text. Leave it empty to remove it.', 'post-slider-and-carousel' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter More Text', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_more_text',
											'value' 		=> __( 'More', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Enter filter `More` field text. This will be displayed when the category filter is wider than screen.', 'post-slider-and-carousel' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Position', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_position',
											'value' 		=> array( 
																	'top'		=> __( 'Top', 'post-slider-and-carousel' ),
																	'left'		=> __( 'Left', 'post-slider-and-carousel' ),
																	'right'		=> __( 'Right', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Choose filter position.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Alignment', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_align',
											'value' 		=> array( 
																	'right'		=> __( 'Right', 'post-slider-and-carousel' ),
																	'left'		=> __( 'Left', 'post-slider-and-carousel' ),
																	'center'	=> __( 'Center', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Choose filter alignment.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Filter Responsive Screen', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_res_screen',
											'value' 		=> 768,
											'desc' 			=> __( 'Enter filter responsive screen. Filter will be on top position below this screen resolution.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Categories', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_cat_limit',
											'value' 		=> 10,
											'desc' 			=> __( 'Enter number of categories to display at a time. Enter 0 to display all.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order By', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_cat_orderby',
											'value' 		=>  array(
																	'name' 			=> __( 'Category Name', 'post-slider-and-carousel' ),
																	'slug' 			=> __( 'Category Slug', 'post-slider-and-carousel' ),
																	'term_group' 	=> __( 'Category Group', 'post-slider-and-carousel' ),
																	'term_id' 		=> __( 'Category ID', 'post-slider-and-carousel' ),
																	'id' 			=> __( 'ID', 'post-slider-and-carousel' ),
																	'description' 	=> __( 'Category Description', 'post-slider-and-carousel' ),
																	'parent'		=> __( 'Category Parent', 'post-slider-and-carousel' ),
																	'term_order'	=> __( 'Category Order', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Select filter category order type.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_cat_order',
											'value' 		=> array(
																	'asc'	=> __( 'Ascending', 'post-slider-and-carousel' ),
																	'desc'	=> __( 'Descending', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Select filter category sorting order.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Child of Category', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_cat_child_of',
											'class'			=> 'psacp-select2',
											'value' 		=> array(
																	'' => __('Select Category', 'post-slider-and-carousel')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Select term id to retrieve child terms of.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Parent Categories', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_cat_parent',
											'class'			=> 'psacp-select2',
											'value' 		=> array(
																	'' => __('Select Category', 'post-slider-and-carousel')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Select parent term id to retrieve direct child terms of. Add 0 to display only parent categories.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Active Filter Category', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_active',
											'value' 		=> '',
											'desc' 			=> __( 'Choose active category. Enter number starting form 1 OR category ID like cat-ID. Default first will be active.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Allow Multiple Filter Categories', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_allow_multiple',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Allow multiple filter category selection at a time.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Reload Filter', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_reload',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Reload page on filter category selection.', 'post-slider-and-carousel' ),
										),
									)
			),

			// Style Manager
			'style_manager' => array(
					'title'		=> __('Style Manager', 'post-slider-and-carousel'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 		=> 'dropdown',
											'name'		=> 'style_id',
											'value' 	=> array('' => __('Choose Style', 'post-slider-and-carousel')),
											'desc' 		=> __( 'Choose your created style from style manager or create a new one.', 'post-slider-and-carousel' ),
										)
									)
			)
	);
	return $fields;
}

/**
 * Generate 'psac_post_carousel' shortcode fields
 * 
 * @since 1.0
 */
function psac_post_carousel_lite_shortcode_fields( $shortcode = '' ) {
	$fields = array(
			// General Settings
			'general' => array(
					'title'     => __('General & Designs', 'post-slider-and-carousel'),
					'params'   	=>  array(
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Design', 'post-slider-and-carousel' ),
											'name' 		=> 'design',
											'value' 	=> psac_post_carousel_designs(),
											'desc' 		=> __( 'Choose design.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Image Size', 'post-slider-and-carousel' ),
											'name' 			=> 'media_size',
											'value' 		=> 'psacp-medium',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Choose WordPress registered image size. e.g.', 'post-slider-and-carousel' ).' thumbnail, medium, large, full.',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'CSS Class', 'post-slider-and-carousel' ),
											'name' 			=> 'css_class',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Enter an extra CSS class for design customization.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('Note: Extra class added as parent so using extra class you customize your design.', 'post-slider-and-carousel').'"> [?]</label>',
										),
									)
			),

			// Slider Fields
			'slider' => array(
					'title'		=> __('Slider', 'post-slider-and-carousel'),
					'params'    => array(
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slides Column', 'post-slider-and-carousel' ),
											'name' 			=> 'slide_show',
											'value' 		=> 3,
											'desc' 			=> __( 'Enter number of slides to show.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slides to Scroll', 'post-slider-and-carousel' ),
											'name' 			=> 'slide_scroll',
											'value' 		=> 1,
											'desc' 			=> __( 'Enter number of slides to scroll at a time.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Loop', 'post-slider-and-carousel' ),
											'name' 			=> 'loop',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable slider loop.', 'post-slider-and-carousel' ),
										),
										array(
											'type'		=> 'dropdown',
											'heading' 	=> __( 'Show Arrows', 'post-slider-and-carousel' ),
											'name' 		=> 'arrows',
											'value' 	=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc'		=> __( 'Show prev - next arrows.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Show Dots', 'post-slider-and-carousel' ),
											'name' 		=> 'dots',
											'value' 	=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 		=> __( 'Show pagination dots.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Autoplay', 'post-slider-and-carousel' ),
											'name' 			=> 'autoplay',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable slider autoplay.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Autoplay Interval', 'post-slider-and-carousel' ),
											'name' 			=> 'autoplay_interval',
											'value' 		=> 5000,
											'desc' 			=> __( 'Enter autoplay interval.', 'post-slider-and-carousel' ),
											'dependency' 	=> array(
																'element' 	=> 'autoplay',
																'value' 	=> array( 'true' ),
															),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Speed', 'post-slider-and-carousel' ),
											'name' 			=> 'speed',
											'value' 		=> 500,
											'desc' 			=> __( 'Enter slider speed.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slider Height', 'post-slider-and-carousel' ),
											'name' 			=> 'sliderheight',
											'value' 		=> '',
											'desc' 			=> __( 'Enter slider height i.e. 500.', 'post-slider-and-carousel' ),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'post-slider-and-carousel' ),
											'desc'			=> sprintf( __( '%s Unlock more Slider options like Center Mode, Slide Margin etc.', 'post-slider-and-carousel' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Previous Button Text', 'post-slider-and-carousel' ),
											'name' 			=> 'prev_text',
											'value' 		=> '',
											'desc' 			=> __( 'Slider previous button text.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Next Button Text', 'post-slider-and-carousel' ),
											'name' 			=> 'next_text',
											'value' 		=> '',
											'desc' 			=> __( 'Slider next button text.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Autoplay Pause on Hover', 'post-slider-and-carousel' ),
											'name' 			=> 'autoplay_hover_pause',
											'value' 		=> array(
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Autoplay pause on hover.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Slider Center Mode', 'post-slider-and-carousel' ),
											'name' 			=> 'center',
											'value' 		=> array(
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable slider center mode.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Slider Auto Height', 'post-slider-and-carousel' ),
											'name' 			=> 'auto_height',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable slider auto height.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slider Start Position', 'post-slider-and-carousel' ),
											'name' 			=> 'start_position',
											'value' 		=> '',
											'desc' 			=> __( 'Enter slide number to start from that.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slide Margin', 'post-slider-and-carousel' ),
											'name' 			=> 'slide_margin',
											'value' 		=> 20,
											'desc' 			=> __( 'Slide margin.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slider Stage Padding', 'post-slider-and-carousel' ),
											'name' 			=> 'stage_padding',
											'value' 		=> '',
											'desc' 			=> __( 'Enter slider stage padding. A partial slide will be visible at both the end.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'URL Hash Listner', 'post-slider-and-carousel' ),
											'name' 			=> 'url_hash_listener',
											'value' 		=> array(
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable url hash listner of slider.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Lazyload', 'post-slider-and-carousel' ),
											'name' 			=> 'lazyload',
											'value' 		=> array(
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable slider lazyload behaviour.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
								)
			),

			// Meta Fields
			'meta' => array(
					'title'		=> __('Meta & Content', 'post-slider-and-carousel'),
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Post Date', 'post-slider-and-carousel' ),
											'name' 			=> 'show_date',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post date.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Author', 'post-slider-and-carousel' ),
											'name' 			=> 'show_author',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post author.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Tags', 'post-slider-and-carousel' ),
											'name' 			=> 'show_tags',
											'value' 		=> array( 
																	'false'		=> __( 'False', 'post-slider-and-carousel' ),
																	'true'		=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post tags.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Comments', 'post-slider-and-carousel' ),
											'name' 			=> 'show_comments',
											'value' 		=> array(
																	'true'		=> __( 'True', 'post-slider-and-carousel' ),
																	'false'		=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post comment count.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Category', 'post-slider-and-carousel' ),
											'name' 			=> 'show_category',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'post-slider-and-carousel' ),
																	'false'		=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post category.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Content', 'post-slider-and-carousel' ),
											'name' 			=> 'show_content',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post content.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Content Word Limit', 'post-slider-and-carousel' ),
											'name' 			=> 'content_words_limit',
											'value' 		=> 20,
											'desc' 			=> __( 'Enter content word limit.', 'post-slider-and-carousel' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Read More', 'post-slider-and-carousel' ),
											'name' 			=> 'show_read_more',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Show read more.', 'post-slider-and-carousel' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Read More Text', 'post-slider-and-carousel' ),
											'name' 			=> 'read_more_text',
											'value' 		=> __( 'Read More', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Enter read more text.', 'post-slider-and-carousel' ),
											'refresh_time'	=> 1000,
											'dependency' 	=> array(
																	'element' 	=> 'show_read_more',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'post-slider-and-carousel' ),
											'desc'			=> sprintf( __( '%s Unlock more Meta & Content options like Read More Text etc.', 'post-slider-and-carousel' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Post Sub Title', 'post-slider-and-carousel' ),
											'name' 			=> 'show_sub_title',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display post sub title.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Post Link Target', 'post-slider-and-carousel' ),
											'name'		=> 'link_behaviour',
											'value' 	=> array(
																'self'	=> __( 'Same Tab', 'post-slider-and-carousel' ),
																'new'	=> __( 'New Tab', 'post-slider-and-carousel' ),
															),
											'desc'		=> __( 'Choose post link behaviour.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
									)
			),			

			// Data Fields
			'query' => array(
					'title'		=> __('Query', 'post-slider-and-carousel'),
					'params'    => array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Type', 'post-slider-and-carousel' ),
											'name' 			=> 'post_type',
											'value' 		=> psac_get_supported_post_types(),
											'default'		=> PSAC_POST_TYPE,
											'class'			=> 'psacp-post-type-sel',
											'ajax'			=> true,
											'desc' 			=> sprintf( __( 'Choose registered post type. You can enable it from plugin %ssetting%s page.', 'post-slider-and-carousel' ), '<a href="'.esc_url( PSAC_SETTING_PAGE_URL ).'" target="_black">', '</a>' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Post', 'post-slider-and-carousel' ),
											'name' 			=> 'limit',
											'value' 		=> 20,
											'min'			=> -1,
											'validation'	=> 'number',
											'desc' 			=> __( 'Enter total number of post to be displayed. Enter -1 to display all.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Category', 'post-slider-and-carousel' ),
											'name' 			=> 'category',
											'value' 		=> '',
											'class'			=> 'psacp-ajax-select2 psacp-category-sel',
											'multi'			=> true,
											'ajax'			=> true,
											'ajax_action'	=> 'psac_category_sugg',
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Choose categories to display category wise posts.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order By', 'post-slider-and-carousel' ),
											'name' 			=> 'orderby',
											'value' 		=>  array(
																	'date' 			=> __( 'Post Date', 'post-slider-and-carousel' ),
																	'ID' 			=> __( 'Post ID', 'post-slider-and-carousel' ),
																	'author' 		=> __( 'Post Author', 'post-slider-and-carousel' ),
																	'title' 		=> __( 'Post Title', 'post-slider-and-carousel' ),
																	'name' 			=> __( 'Post Slug', 'post-slider-and-carousel' ),
																	'modified' 		=> __( 'Post Modified Date', 'post-slider-and-carousel' ),
																	'menu_order'	=> __( 'Menu Order', 'post-slider-and-carousel' ),
																	'parent'		=> __( 'Parent ID', 'post-slider-and-carousel' ),
																	'rand' 			=> __( 'Random', 'post-slider-and-carousel' ),
																	'comment_count'	=> __( 'Number of Comments', 'post-slider-and-carousel' ),
																	'post__in'		=> __( 'Preserve Post ID Order', 'post-slider-and-carousel' ),
																	'relevance'		=> __( 'Relevance', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Select order type.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order', 'post-slider-and-carousel' ),
											'name' 			=> 'order',
											'value' 		=> array(
																	'desc'	=> __( 'Descending', 'post-slider-and-carousel' ),
																	'asc'	=> __( 'Ascending', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Select sorting order.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 1', 'post-slider-and-carousel' ),
											'name' 			=> 'custom_param_1',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query, HTML etc via Hooks and Filters with the help of this parameter.', 'post-slider-and-carousel').'"> [?]</label>',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 2', 'post-slider-and-carousel' ),
											'name' 			=> 'custom_param_2',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query, HTML etc via Hooks and Filters with the help of this parameter.', 'post-slider-and-carousel').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'post-slider-and-carousel' ),
											'desc'			=> sprintf( __( '%s Unlock more Query options like Cat Taxonomy, Exclude By Category etc.', 'post-slider-and-carousel' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Taxonomy', 'post-slider-and-carousel' ),
											'name' 			=> 'taxonomy',
											'value' 		=> psac_get_post_type_taxonomy( PSAC_POST_TYPE ),
											'class'			=> 'psacp-taxonomy-sel',
											'desc' 			=> __( 'Choose registered taxonomy if you want to display category wise post.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Cat Taxonomy', 'post-slider-and-carousel' ),
											'name' 			=> 'cat_taxonomy',
											'class'			=> 'psacp-cat-taxonomy-sel',
											'value' 		=> array( '' => __('Select Taxonomy', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Choose a category taxonomy just to display categories as meta information.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Tag Taxonomy', 'post-slider-and-carousel' ),
											'name' 			=> 'tag_taxonomy',
											'class'			=> 'psacp-tag-taxonomy-sel',
											'value' 		=> array( '' => __('Select Taxonomy', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Choose a tag taxonomy just to display tags as meta information.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Category Operator', 'post-slider-and-carousel'),
											'name'			=> 'category_operator',
											'value'			=> array( 
																	'IN'	=> __( 'IN', 'post-slider-and-carousel' ),
																	'AND'	=> __( 'AND', 'post-slider-and-carousel' ),
																),
											'desc'			=> __( 'Select category operator. Default value is IN', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Display Child Category Posts', 'post-slider-and-carousel'),
											'name'			=> 'include_cat_child',
											'value'			=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc'			=> __( 'Whether or not to include children category posts if parent category is there.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Category', 'post-slider-and-carousel' ),
											'name' 			=> 'exclude_cat',
											'value' 		=> array('' => __('Select Data', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Choose categories to exclude posts of it.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include Post', 'post-slider-and-carousel' ),
											'name' 			=> 'posts',
											'value' 		=> array('' => __('Select Data', 'post-slider-and-carousel') ),
											'desc' 			=> __('Choose posts which you want to display.', 'post-slider-and-carousel'),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude Post', 'post-slider-and-carousel' ),
											'name' 			=> 'hide_post',
											'value' 		=> array('' => __('Select Data', 'post-slider-and-carousel') ),
											'desc' 			=> __('Choose posts which you do not want to display.', 'post-slider-and-carousel'),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Author', 'post-slider-and-carousel' ),
											'name' 			=> 'author',
											'value' 		=> array('' => __('Select Data', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Choose authors to show posts associated with that.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Author', 'post-slider-and-carousel' ),
											'name' 			=> 'exclude_author',
											'value' 		=> array('' => __('Select Data', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Choose authors to hide posts associated with that. Works only if `Include Author` field is empty.', 'post-slider-and-carousel' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sticky Posts', 'post-slider-and-carousel' ),
											'name' 			=> 'sticky_posts',
											'value' 		=> array(
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Display sticky posts.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__("Note: Slicky post only be displayed at front side. In preview mode sticky post will not be displayed.", 'post-slider-and-carousel').'"> [?]</label>',
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Type', 'post-slider-and-carousel' ),
											'name' 			=> 'type',
											'value' 		=> array(
																	'' 			=> __( 'Select Type', 'post-slider-and-carousel' ),
																	'featured'	=> __( 'Featured', 'post-slider-and-carousel' ),
																	'trending'	=> __( 'Trending', 'post-slider-and-carousel'),
																),
											'desc' 			=> __( 'Select display type of post. Is it Featured or Trending?', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('Note: Be sure you have added valid post type name and post type is enabled from plugin setting.', 'post-slider-and-carousel').'"> [?]</label>',
											'premium'		=> true,
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Query Offset', 'post-slider-and-carousel' ),
											'name' 			=> 'query_offset',
											'value' 		=> '',
											'desc' 			=> __( 'Skip number of posts from starting.', 'post-slider-and-carousel' ) . '<label title="'.esc_attr__('e.g. 5 to skip over 5 posts. Note: Do not use limit=-1 and pagination=true with this.', 'post-slider-and-carousel').'"> [?]</label>',
											'premium'		=> true,
										),
									)
			),
			// Social Sharing
			'social_sharing' => array(
					'title'		=> __('Social Sharing', 'post-slider-and-carousel'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 			=> 'dropdown',
											'name' 			=> 'sharing',
											'value' 		=> array('' => __('No Social Sharing', 'post-slider-and-carousel') ),
											'desc' 			=> __( 'Enable social sharing. You can enable it from plugin setting page.', 'post-slider-and-carousel' ) . '<label> [?]</label>',
										),
									)
			),

			// Filter Settings
			'filter' => array(
					'title'		=> __('Filter', 'post-slider-and-carousel'),
					'premium'	=> true,
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Enable Filter', 'post-slider-and-carousel' ),
											'name' 			=> 'filter',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Enable category filter.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Filter Design', 'post-slider-and-carousel' ),
											'name' 		=> 'filter_design',
											'value' 	=> array( 
																	'design-1'	=> __( 'Design 1', 'post-slider-and-carousel' ),
															),
											'desc' 		=> __( 'Choose filter design.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter All Text', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_all_text',
											'value' 		=> __( 'All', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Enter `ALL` field text. Leave it empty to remove it.', 'post-slider-and-carousel' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter More Text', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_more_text',
											'value' 		=> __( 'More', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Enter filter `More` field text. This will be displayed when the category filter is wider than screen.', 'post-slider-and-carousel' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Position', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_position',
											'value' 		=> array( 
																	'top'		=> __( 'Top', 'post-slider-and-carousel' ),
																	'left'		=> __( 'Left', 'post-slider-and-carousel' ),
																	'right'		=> __( 'Right', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Choose filter position.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Alignment', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_align',
											'value' 		=> array( 
																	'right'		=> __( 'Right', 'post-slider-and-carousel' ),
																	'left'		=> __( 'Left', 'post-slider-and-carousel' ),
																	'center'	=> __( 'Center', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Choose filter alignment.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Filter Responsive Screen', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_res_screen',
											'value' 		=> 768,
											'desc' 			=> __( 'Enter filter responsive screen. Filter will be on top position below this screen resolution.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Categories', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_cat_limit',
											'value' 		=> 10,
											'desc' 			=> __( 'Enter number of categories to display at a time. Enter 0 to display all.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order By', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_cat_orderby',
											'value' 		=>  array(
																	'name' 			=> __( 'Category Name', 'post-slider-and-carousel' ),
																	'slug' 			=> __( 'Category Slug', 'post-slider-and-carousel' ),
																	'term_group' 	=> __( 'Category Group', 'post-slider-and-carousel' ),
																	'term_id' 		=> __( 'Category ID', 'post-slider-and-carousel' ),
																	'id' 			=> __( 'ID', 'post-slider-and-carousel' ),
																	'description' 	=> __( 'Category Description', 'post-slider-and-carousel' ),
																	'parent'		=> __( 'Category Parent', 'post-slider-and-carousel' ),
																	'term_order'	=> __( 'Category Order', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Select filter category order type.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_cat_order',
											'value' 		=> array(
																	'asc'	=> __( 'Ascending', 'post-slider-and-carousel' ),
																	'desc'	=> __( 'Descending', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Select filter category sorting order.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Child of Category', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_cat_child_of',
											'class'			=> 'psacp-select2',
											'value' 		=> array(
																	'' => __('Select Category', 'post-slider-and-carousel')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Select term id to retrieve child terms of.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Parent Categories', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_cat_parent',
											'class'			=> 'psacp-select2',
											'value' 		=> array(
																	'' => __('Select Category', 'post-slider-and-carousel')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'post-slider-and-carousel' ),
											'desc' 			=> __( 'Select parent term id to retrieve direct child terms of. Add 0 to display only parent categories.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Active Filter Category', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_active',
											'value' 		=> '',
											'desc' 			=> __( 'Choose active category. Enter number starting form 1 OR category ID like cat-ID. Default first will be active.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Allow Multiple Filter Categories', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_allow_multiple',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Allow multiple filter category selection at a time.', 'post-slider-and-carousel' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Reload Filter', 'post-slider-and-carousel' ),
											'name' 			=> 'filter_reload',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'post-slider-and-carousel' ),
																	'true'	=> __( 'True', 'post-slider-and-carousel' ),
																),
											'desc' 			=> __( 'Reload page on filter category selection.', 'post-slider-and-carousel' ),
										),
									)
			),

			// Style Manager
			'style_manager' => array(
					'title'		=> __('Style Manager', 'post-slider-and-carousel'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 		=> 'dropdown',
											'name'		=> 'style_id',
											'value' 	=> array('' => __('Choose Style', 'post-slider-and-carousel')),
											'desc' 		=> __( 'Choose your created style from style manager or create a new one.', 'post-slider-and-carousel' ),
										)
									)
			)
	);

	return $fields;
}