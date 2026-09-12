<?php
/**
 * Social Sharing Settings Page
 * 
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function psac_render_sharing_settings() {

	$reg_post_types 	= psac_get_post_types();
	$general_post_types = psac_get_option( 'post_types', array() );
?>

<div id="psacp-sharing-sett-wrp" class="post-box-container psacp-sharing-sett-wrp">
	<div class="metabox-holder">
		<div id="psacp-sharing-sett" class="postbox psacp-postbox">

			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Social Sharing Settings', 'post-slider-and-carousel' ); ?> <a class="pro-badge" href="<?php echo esc_url( PSAC_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock psacp-shrt-acc-header-pro-icon"></i> <?php esc_html_e( 'Unlock Premium Features', 'post-slider-and-carousel' ); ?></a></span>
				</h2>
			</div>

			<div class="inside">
				<div class="psacp-prowrap-content"></div>
				<table class="form-table psacp-sharing-sett-tbl">
					<tbody>
						<tr>
							<th><label for="psacp-enable-sharing"><?php esc_html_e( 'Enable Social Sharing', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<input type="checkbox" name="" value="" class="psacp-checkbox psacp-enable-sharing" id="psacp-enable-sharing" /><br/>
								<span class="description"><?php esc_html_e('Check this box to enable social sharing.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label><?php esc_html_e( 'Social Services', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<div class="psacp-social-service-wrap">
									<div class="psacp-social-service-row">
										<select name="" class="psacp-select psacp-social-service">
											<option value=""><?php esc_html_e('Facebook', 'post-slider-and-carousel'); ?></option>
										</select>
										<span class="psacp-social-service-act">
											<button type="button" class="button button-secondary psacp-social-service-btn psacp-social-service-add"><?php esc_html_e('Add', 'post-slider-and-carousel'); ?></button>
											<button type="button" class="button button-secondary psacp-social-service-btn psacp-social-service-del"><?php esc_html_e('Remove', 'post-slider-and-carousel'); ?></button>
										</span>
									</div>
									<div class="psacp-social-service-row">
										<select name="" class="psacp-select psacp-social-service">
											<option value=""><?php esc_html_e('Twitter', 'post-slider-and-carousel'); ?></option>
										</select>
										<span class="psacp-social-service-act">
											<button type="button" class="button button-secondary psacp-social-service-btn psacp-social-service-add"><?php esc_html_e('Add', 'post-slider-and-carousel'); ?></button>
											<button type="button" class="button button-secondary psacp-social-service-btn psacp-social-service-del"><?php esc_html_e('Remove', 'post-slider-and-carousel'); ?></button>
										</span>
									</div>
									<div class="psacp-social-service-row">
										<select name="" class="psacp-select psacp-social-service">
											<option value=""><?php esc_html_e('WhatsApp', 'post-slider-and-carousel'); ?></option>
										</select>
										<span class="psacp-social-service-act">
											<button type="button" class="button button-secondary psacp-social-service-btn psacp-social-service-add"><?php esc_html_e('Add', 'post-slider-and-carousel'); ?></button>
											<button type="button" class="button button-secondary psacp-social-service-btn psacp-social-service-del"><?php esc_html_e('Remove', 'post-slider-and-carousel'); ?></button>
										</span>
									</div>
								</div>
								<span class="description"><?php esc_html_e('Choose social sharing service. Social sharing will be displayed in same order in which you save.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>
						<tr>
							<th colspan="2">
								<div class="psacp-sett-sub-title"><?php esc_html_e( 'Social Sharing on Single Post Pages', 'post-slider-and-carousel' ); ?></div>
							</th>
						</tr>
						<tr>
							<th><label for="psacp-sharing-lbl"><?php esc_html_e( 'Sharing Label', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<input type="text" name="" value="" class="regular-text psacp-sharing-label" id="psacp-sharing-lbl" /><br/>
								<span class="description"><?php esc_html_e('Enter sharing label.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="psacp-sharing-design"><?php esc_html_e( 'Sharing Theme', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<select name="" class="psacp-select psacp-sharing-design" id="psacp-sharing-design">
									<option value=""><?php esc_html_e('Theme 1', 'post-slider-and-carousel'); ?></option>
								</select><br/>
								<span class="description"><?php esc_html_e('Choose sharing theme.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label><?php esc_html_e( 'Select Post Type', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<?php if( ! empty($general_post_types) ) {
									foreach ($general_post_types as $post_key => $post_val) {

										// If saved post type is not in general post type
										if( ! array_key_exists( $post_val, $reg_post_types ) ) {
											continue;
										}

										$post_label = isset( $reg_post_types[ $post_val ] ) ? $reg_post_types[ $post_val ] : '--';
								?>
									<div class="psacp-post-type-wrap">
										<label>
											<input type="checkbox" value="" class="psacp-checkbox" />
											<?php echo esc_html( $post_label ); ?>
											( <?php echo esc_html__('Post Type', 'post-slider-and-carousel').' : '. esc_html( $post_val ); ?> )
										</label>
									</div>
								<?php }
								} ?>
								<span class="description"><?php esc_html_e('Select post type box to enable social sharing on single post pages. Did not find the post type? Make sure you have enabled it from general setting.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>
					</tbody>
				</table><!-- .psacp-sharing-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #psacp-sharing-sett-wrp -->

<?php
}

// Action to add social sharing settings
add_action( 'psac_settings_tab_sharing', 'psac_render_sharing_settings' );