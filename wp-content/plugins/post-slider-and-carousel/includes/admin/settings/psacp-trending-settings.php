<?php
/**
 * Trending Settings Page
 * 
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function psac_render_trending_settings() {

	$reg_post_types = psac_get_post_types();
?>

<div id="psacp-trending-sett-wrp" class="post-box-container psacp-trending-sett-wrp">
	<div class="metabox-holder">
		<div id="psacp-trending-sett" class="postbox psacp-postbox">

			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Trending Post Type Settings', 'post-slider-and-carousel' ); ?> <a class="pro-badge" href="<?php echo esc_url( PSAC_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock psacp-shrt-acc-header-pro-icon"></i> <?php esc_html_e( 'Unlock Premium Features', 'post-slider-and-carousel' ); ?></a></span>
				</h2>
			</div>

			<div class="inside">
				<div class="psacp-prowrap-content"></div>
				<table class="form-table psacp-trending-sett-tbl">
					<tbody>
						<tr>
							<th scope="row"><label><?php esc_html_e( 'Select Post Type', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<?php if( ! empty( $reg_post_types ) ) {
									foreach ( $reg_post_types as $post_key => $post_label ) {
										$taxonomy_objects = psac_get_taxonomies( $post_key, 'list' );
								?>
									<div class="psacp-post-type-wrap">
										<label>
											<input type="checkbox" value="<?php echo esc_attr( $post_key ); ?>" name="" class="psacp-checkbox" disabled="disabled" />
											<?php echo esc_html( $post_label ); ?>
												( <?php echo esc_html__('Post Type', 'post-slider-and-carousel').' : '. esc_html( $post_key );

												if( ! empty( $taxonomy_objects ) ) {
													echo ' | '.esc_html__('Taxonomy', 'post-slider-and-carousel').' : '. esc_html( $taxonomy_objects );
												} ?>
												)
										</label>
									</div>
								<?php }
								} ?>
								<span class="description"><?php esc_html_e('Select post type box to enable trending post functionality. When someone visits a single page then its visit will be counted. Based on the number of visits you can display the posts. e.g.', 'post-slider-and-carousel'); ?> [psac_post_slider post_type="post" type="trending"]</span> <br/>
								<span class="description"><?php esc_html_e('You can see the total visit count and reset it at post meta settings once you enable it.', 'post-slider-and-carousel'); ?></span> <br/>
							</td>
						</tr>
					</tbody>
				</table><!-- .psacp-trending-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #psacp-trending-sett-wrp -->

<?php
}

// Action to add trending settings
add_action( 'psac_settings_tab_trending', 'psac_render_trending_settings' );