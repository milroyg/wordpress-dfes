<?php
/**
 * CSS Settings Page
 * 
 * The code for the plugins css settings page
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function psac_render_css_settings() { ?>

<div id="psacp-css-sett-wrp" class="post-box-container psacp-css-sett-wrp">
	<div class="metabox-holder">
		<div id="psacp-css-sett" class="postbox psacp-postbox">

			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Custom CSS Settings', 'post-slider-and-carousel' ); ?></span>
				</h2>
			</div>

			<div class="inside">
				<table class="form-table psacp-css-sett-tbl">
					<tbody>
						<tr>
							<th scope="row">
								<label for="psacp-cust-css"><?php esc_html_e( 'Custom CSS', 'post-slider-and-carousel' ); ?></label>
							</th>
							<td>
								<textarea name="psacp_opts[custom_css]" id="psacp-cust-css" rows="18" class="large-text psacp-cust-css psacp-code-editor"><?php echo esc_textarea( psac_get_option('custom_css') ); ?></textarea>
								<span class="description"><?php esc_html_e( 'Here you can enter your custom CSS for the plugin. The CSS will automatically be inserted to the header of theme, when you save it.', 'post-slider-and-carousel' ); ?></span><br/>
								<span class="description"><?php esc_html_e( 'Note: Do not add `style` tag.', 'post-slider-and-carousel' ); ?></span>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<input type="submit" name="psacp_sett_submit" class="button button-primary right psacp-sett-submit" value="<?php esc_attr_e('Save Settings', 'post-slider-and-carousel'); ?>" />
							</td>
						</tr>
					</tbody>
				</table><!-- .psacp-css-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- end .metabox-holder -->
</div><!-- #psacp-css-sett-wrp -->

<?php
}

add_action( 'psac_settings_tab_css', 'psac_render_css_settings' );