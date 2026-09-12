<?php
/**
 * Post Scrolling Widget Template 1
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
?>
<li class="psac-post-li <?php echo esc_attr( $atts['wrp_cls'] ); ?>">
	<div class="psac-post-list-content psacp-clearfix">
		<?php if ( $atts['feat_img'] && $atts['show_image'] ) { ?>
		<div class="psacp-post-list-left psacp-col-s-5 psacp-columns">
			<div class="psacp-post-img-bg">
				<a href="<?php echo esc_url( $atts['post_link'] ); ?>">
					<img src="<?php echo esc_url( $atts['feat_img'] ); ?>" alt="<?php the_title_attribute(); ?>" />
				</a>
			</div>
		</div>
		<?php } ?>

		<div class="psacp-post-list-right <?php if ( $atts['feat_img'] && $atts['show_image'] ) { echo 'psacp-col-s-7'; } else { echo 'psacp-col-s-12'; } ?> psacp-columns">
			<?php if( $atts['show_category'] && $atts['cate_name'] ) { ?>
			<div class="psacp-post-cats"><?php echo wp_kses_post( $atts['cate_name'] ); ?></div>
			<?php } ?>

			<h4 class="psacp-post-title">
				<a href="<?php echo esc_url( $atts['post_link'] ); ?>"><?php the_title(); ?></a>
			</h4>		

			<?php if( $atts['show_date'] ) { ?>
			<div class="psacp-post-meta">
				<?php echo psac_post_meta_data( array( 'post_date' => $atts['show_date'] ) ); // WPCS: XSS ok. ?>
			</div>
			<?php }

			if( $atts['show_content'] ) { ?>
			<div class="psacp-post-desc"><?php echo psac_get_post_excerpt( $post->ID, get_the_content(), $atts['content_words_limit'] ); // WPCS: XSS ok. ?></div>
			<?php } ?>
		</div>
	</div>
</li>