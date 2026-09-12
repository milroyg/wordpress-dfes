<?php
/**
 * Misc Settings Page
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function psac_render_misc_settings() {
?>

<div id="psacp-misc-sett-wrp" class="post-box-container psacp-misc-sett-wrp">
	<div class="metabox-holder">
		<div id="psacp-misc-sett" class="postbox psacp-postbox">

			<!-- Settings box title -->
			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Misc Settings', 'post-slider-and-carousel' ); ?></span>
				</h2>
			</div>

			<div class="inside">
				<table class="form-table psacp-misc-sett-tbl">
					<tbody>
						<tr>
							<th scope="row"><label for="psacp-post-cnt-fix"><?php esc_html_e( 'Enable Post Content Fix', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<input type="checkbox" name="psacp_opts[post_content_fix]" value="1" class="psacp-post-cnt-fix" id="psacp-post-cnt-fix" <?php checked(1, psac_get_option('post_content_fix')); ?>/><br/>
								<span class="description"><?php esc_html_e('Check this box to apply a fix to get text from post content when shortcodes are there.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="psacp-dsbl-font-awsm"><?php esc_html_e( 'Disable Font Awesome CSS', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<input type="checkbox" name="psacp_opts[disable_font_awsm_css]" value="1" class="psacp-dsbl-font-awsm" id="psacp-dsbl-font-awsm" <?php checked(1, psac_get_option('disable_font_awsm_css', 0)); ?> /><br/>
								<span class="description"><?php esc_html_e('Check this box if your theme or any other plugins uses the font awsome css. Plugin will not use it\'s own font awsome css with respect to site speed.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="psacp-dsbl-owl-css"><?php esc_html_e( 'Disable Owl Carousel CSS', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<input type="checkbox" name="psacp_opts[disable_owl_css]" value="1" class="psacp-dsbl-owl-css" id="psacp-dsbl-owl-css" <?php checked(1, psac_get_option('disable_owl_css', 0)); ?> /><br/>
								<span class="description"><?php esc_html_e('Check this box if your theme or any other plugins uses the Owl Carousel css. Plugin will not use it\'s own Owl Carousel css with respect to site speed.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="psacp-fix-owl-init-issue"><?php esc_html_e( 'Fix Slider Initialization Issue', 'post-slider-and-carousel' ); ?></label></th>
							<td>
								<input type="checkbox" name="psacp_opts[fix_owl_conflict]" value="1" class="psacp-fix-owl-init-issue" id="psacp-fix-owl-init-issue" <?php checked(1, psac_get_option('fix_owl_conflict', 0)); ?> /><br/>
								<span class="description"><?php esc_html_e('Check this box only if Owl Carousel slider is not initializing properly. Some theme or plugin initialize the slider on a common class so it breaks the plugin slider functionality. By enabling this it will apply a fix to work it properly.', 'post-slider-and-carousel'); ?></span>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<input type="submit" name="psacp_sett_submit" class="button button-primary right psacp-sett-submit" value="<?php esc_attr_e('Save Settings', 'post-slider-and-carousel'); ?>" />
							</td>
						</tr>
					</tbody>
				</table><!-- .psacp-misc-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #psacp-misc-sett-wrp -->

<?php
}

// Action to add misc settings
add_action( 'psac_settings_tab_misc', 'psac_render_misc_settings' );