<?php
/**
 * Post Settings MetaBox HTML
 * 
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="meta-pro-badge">
	<h3><?php esc_html_e( 'Unlock all settings by upgrading to the Pro version.', 'post-slider-and-carousel' ); ?></h3>
	<a class="pro-badge" href="<?php echo esc_url( PSAC_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock psacp-shrt-acc-header-pro-icon"></i> <?php esc_html_e( 'Unlock Premium Features', 'post-slider-and-carousel' ); ?></a>
</div>
<div class="psacp-prowrap-content"></div>
<div class="psacp-post-sett-mb-wrp">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="psacp-disable-sharing"><?php esc_html_e( 'Disable Social Sharing', 'post-slider-and-carousel' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="" value="" class="psacp-checkbox" id="psacp-disable-sharing" disabled="disabled" /><br/>
					<span class="description"><?php esc_html_e('Check this box to disable social sharing for this post.', 'post-slider-and-carousel'); ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="psacp-feat-post"><?php esc_html_e( 'Featured Post', 'post-slider-and-carousel' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="" value="" class="psacp-checkbox" id="psacp-feat-post" disabled="disabled" /><br/>
					<span class="description"><?php esc_html_e('Check this box to mark this post as a featured post.', 'post-slider-and-carousel'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="psacp-sub-title"><?php esc_html_e( 'Post Sub Title', 'post-slider-and-carousel' ); ?></label>
				</th>
				<td>
					<input type="text" name="" value="" class="large-text" id="psacp-sub-title" disabled="disabled" /><br/>
					<span class="description"><?php esc_html_e('Enter post sub title.', 'post-slider-and-carousel'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="psacp-read-more-link"><?php esc_html_e( 'Read More Link', 'post-slider-and-carousel' ); ?></label>
				</th>
				<td>
					<input type="text" name="" value="" class="large-text" id="psacp-read-more-link" disabled="disabled" /><br/>
					<span class="description"><?php esc_html_e('Enter custom read more link. Leave empty for default post permalink.', 'post-slider-and-carousel'); ?></span>
				</td>
			</tr>

			<tr>
				<th colspan="2">
					<div class="psacp-sett-sub-title"><?php esc_html_e( 'Trending Post Settings', 'post-slider-and-carousel' ); ?></div>
				</th>
			</tr>

			<tr>
				<th scope="row"><label><?php esc_html_e( 'Post View Count', 'post-slider-and-carousel' ); ?></label></th>
				<td>
					<?php $post_view_count = "1008"; ?>
					
					<span class="psacp-post-count-view"><?php echo esc_html( $post_view_count ); ?></span>
					
					<?php if( $post_view_count ) { ?>
					<input type="button" name="" value="<?php esc_html_e('Reset Post Count', 'post-slider-and-carousel'); ?>" class="button button-secondary" disabled="disabled" />
					<?php } ?>
				</td>
			</tr>
		</tbody>
	</table>
</div><!-- end .psacp-post-sett-mb-wrp -->