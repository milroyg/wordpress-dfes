<?php
/**
 * General Settings Page
 * 
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function psac_render_general_settings() {

	$reg_post_types 		= psac_get_post_types();
	$saved_post_types 		= psac_get_option( 'post_types', array() );
	$post_default_feat_img	= psac_get_option( 'post_default_feat_img' );
?>

<div id="psacp-general-sett-wrp" class="post-box-container psacp-general-sett-wrp">
	<div class="metabox-holder">
		<div id="psacp-general-sett" class="postbox psacp-postbox">
			
			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'General Settings', 'post-slider-and-carousel' ); ?></span>
				</h2>
			</div>

			<div class="inside">
				<table class="form-table psacp-general-sett-tbl">
					<tbody>
						<tr>
							<th scope="row"><label><?php esc_html_e( 'Select Post Type', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<div class="psacp-post-type-wrap">
									<label>
										<input type="checkbox" value="<?php echo esc_attr( PSAC_POST_TYPE ); ?>" name="psacp_opts[post_types][]" class="psacp-checkbox" <?php checked( in_array(PSAC_POST_TYPE, $saved_post_types), true ); ?> disabled="disabled" />
										<?php echo isset( $reg_post_types[ PSAC_POST_TYPE ] ) ? esc_html( $reg_post_types[ PSAC_POST_TYPE ] ) : PSAC_POST_TYPE; ?>
											( <?php echo esc_html__('Post Type', 'post-slider-and-carousel').' : '.esc_html( PSAC_POST_TYPE );

											$taxonomy_objects = psac_get_taxonomies( PSAC_POST_TYPE, 'list' );

											if( ! empty( $taxonomy_objects ) ) {
												echo ' | '.esc_html__('Taxonomy', 'post-slider-and-carousel').' : '.esc_html( $taxonomy_objects );
											} ?>
											)
									</label>
								</div>

								<?php if( ! empty( $reg_post_types ) ) { ?>
									<div class="psacp-other-post-type-wrap">
										<div class="psacp-pro-features"><i class="dashicons dashicons-lock"></i> <?php esc_html_e('Premium Features', 'post-slider-and-carousel'); ?>  </div>
										<span class="description"><?php esc_html_e('Bellow are custom post types(CPTs) and custom Taxonomies.', 'post-slider-and-carousel'); ?> <a href="<?php echo esc_url( PSAC_PRO_TAB_URL ); ?>"><?php esc_html_e('Unlock Custom Post Types & Taxonomies!', 'post-slider-and-carousel'); ?></a></span>
										<?php foreach ($reg_post_types as $post_key => $post_label) {

											if( PSAC_POST_TYPE == $post_key ) {
												continue;
											}

											$taxonomy_objects = psac_get_taxonomies( $post_key, 'list' );
										?>
											<div class="psacp-post-type-wrap">
												<label>
													<input type="checkbox" value="<?php echo esc_attr( $post_key ); ?>" name="psacp_opts[post_types][]" class="psacp-checkbox" <?php checked( in_array($post_key, $saved_post_types), true ); ?> disabled="disabled" />
													<?php echo esc_html( $post_label ); ?>
														( <?php echo esc_html__('Post Type', 'post-slider-and-carousel').' : '.esc_html( $post_key );

														if( ! empty( $taxonomy_objects ) ) {
															echo ' | '.esc_html__('Taxonomy', 'post-slider-and-carousel').' : '.esc_html( $taxonomy_objects );
														} ?>
														)
												</label>
											</div>
										<?php } ?>
									</div>
								<?php } ?>
								<span class="description"><?php esc_html_e('Note: `post` will be remain enabled by default.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>

						<tr>
							<th colspan="2">
								<div class="psacp-sett-sub-title"><?php esc_html_e( 'General Settings', 'post-slider-and-carousel' ); ?></div>
							</th>
						</tr>
						<tr>
							<th><label for="psacp-enable-post-first-img"><?php esc_html_e( 'First Image From Post Content', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<input type="checkbox" name="psacp_opts[post_first_img]" value="1" class="psacp-checkbox psacp-enable-post-first-img" id="psacp-enable-post-first-img" <?php checked(1, psac_get_option('post_first_img')); ?>/><br/>
								<span class="description"><?php esc_html_e('Check this box to take the first image from post content when the post featured image is not available.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="psacp-default-post-feat-img"><?php esc_html_e('Post Default Featured Image', 'post-slider-and-carousel'); ?></label>
							</th>
							<td>
								<input type="text" name="psacp_opts[post_default_feat_img]" value="<?php echo esc_url( $post_default_feat_img ); ?>" class="regular-text psacp-default-post-feat-img psacp-img-upload-input" />
								<input type="button" id="psacp-default-post-feat-img" class="button button-secondary psacp-img-upload psacp-default-post-feat-img" value="<?php esc_html_e( 'Choose', 'post-slider-and-carousel'); ?>" />
								<input type="button" class="button button-secondary psacp-default-post-feat-img-clear psacp-image-clear" value="<?php esc_html_e( 'Clear', 'post-slider-and-carousel'); ?>" />
								<p class="description"><?php esc_html_e( 'Upload / Choose default post featured image.', 'post-slider-and-carousel' ); ?></p>
								
								<div class="psacp-img-preview psacp-img-view">
									<?php if( ! empty( $post_default_feat_img ) ) { ?>
									<img src="<?php echo esc_url( $post_default_feat_img ); ?>" alt="" />
									<?php } ?>
								</div>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<input type="submit" name="psacp_sett_submit" class="button button-primary right psacp-sett-submit" value="<?php esc_attr_e('Save Settings', 'post-slider-and-carousel'); ?>" />
							</td>
						</tr>
					</tbody>
				</table><!-- .psacp-general-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #psacp-general-sett-wrp -->

<?php
}

// Action to add general settings
add_action( 'psac_settings_tab_general', 'psac_render_general_settings' );