<?php
/**
 * Slider Template 2
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

// Post Meta Data
$meta_data = array(
				'author'	=> $atts['show_author'],
				'post_date'	=> $atts['show_date'],
				'comments'	=> $atts['show_comments'],
			);
?>
<div class="psacp-post-slide <?php echo esc_attr( $atts['wrp_cls'] ); ?> psacp-clearfix">
	<div class="psacp-post-slider-content">	
		<div class="psacp-col-left psacp-col-2 psacp-columns">
			<div class="psacp-featured-meta">
				<div class="psac-alignmiddle">
					<?php if( $atts['show_category'] && $atts['cate_name'] ) { ?>
					<div class="psacp-post-cats"><?php echo wp_kses_post( $atts['cate_name'] ); ?></div>
					<?php } ?>

					<h2 class="psacp-post-title">
						<a href="<?php echo esc_url( $atts['post_link'] ); ?>"><?php the_title(); ?></a>
					</h2>

					<?php if( $atts['show_date'] || $atts['show_author'] || $atts['show_comments'] ) { ?>
					<div class="psacp-post-meta-outer">
						<div class="psacp-post-meta psacp-post-meta-up">
							<?php echo psac_post_meta_data( $meta_data ); // WPCS: XSS ok. ?>
						</div>
					</div>
					<?php }

					if( $atts['show_content'] ) { ?>
					<div class="psacp-post-content">
						<div class="psacp-post-desc"><?php echo psac_get_post_excerpt( $post->ID, get_the_content(), $atts['content_words_limit'] ); // WPCS: XSS ok. ?></div>
						<?php if( $atts['show_read_more'] ) { ?>
						<a href="<?php echo esc_url( $atts['post_link'] ); ?>" class="psacp-rdmr-btn"><?php echo wp_kses_post( $atts['read_more_text'] ); ?></a>
						<?php } ?>
					</div>
					<?php }

					if( $atts['show_tags'] && $atts['tags'] ) { ?>
					<div class="psacp-post-meta psacp-post-meta-down"><?php echo wp_kses_post( $atts['tags'] ); ?></div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="psacp-col-right psacp-col-2 psacp-columns">
			<a class="psacp-post-linkoverlay" href="<?php echo esc_url( $atts['post_link'] ); ?>"></a>
			<div class="psacp-post-img-bg" style="<?php echo esc_attr( $atts['image_style'] ); ?>">
				<?php if( $atts['format'] == 'video' ) { echo psac_post_format_html( $atts['format'] ); } // WPCS: XSS ok. ?>
			</div>
		</div>
	</div>
</div>