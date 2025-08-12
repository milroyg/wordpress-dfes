<?php
/**
 * Shortcode Builder
 *
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$valid					= true;
$registered_shortcodes 	= psac_registered_shortcodes();
$shortcodes_arr 		= psac_registered_shortcodes( false );
$allowed_reg_shortcodes	= psac_allowed_reg_shortcodes();
$preview_shortcode 		= ! empty( $_GET['shortcode'] ) ? $_GET['shortcode'] : apply_filters('psacp_default_preview_shortcode', 'psac_post_slider' );
$preview_url 			= add_query_arg( array( 'page' => 'psacp-shortcode-preview', 'shortcode' => $preview_shortcode), admin_url('admin.php') );
$shrt_builder_url 		= add_query_arg( array('page' => 'psacp-shrt-builder'), admin_url('admin.php') );

// Instantiate the shortcode builder
if( ! class_exists( 'PSAC_Shortcode_Builder' ) ) {
	include_once( PSAC_DIR . '/includes/admin/shortcode-builder/class-psacp-shortcode-builder.php' );
}

$shortcode_val		= "[{$preview_shortcode}]";
$shortcode_fields 	= array();
$shortcode_sanitize = str_replace('-', '_', $preview_shortcode);
?>
<div class="wrap psacp-customizer-settings">
	<div class="psacp-pro-main-wrap psacp-clearfix" style="text-align:left; margin:0 -15px 20px -15px;">
		<div class="psacp-cnt-grid-8 psacp-columns">
			<h2><?php esc_html_e( 'Shortcode Builder (Alternate Option For Layouts)', 'post-slider-and-carousel' ); ?></h2>
			<p><?php esc_html_e( 'Shortcode builder is an alternate option for those who do not want to create the layout & store the data in to database. It will help you to understand all the parameters in the details.', 'post-slider-and-carousel' ); ?></p>
		</div>
		<div class="psacp-cnt-grid-4 psacp-columns" style="text-align:right; padding-top:20px;">
			<a class="pro-badge" href="<?php echo esc_url( PSAC_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock psacp-shrt-acc-header-pro-icon"></i> <?php esc_html_e( 'Unlock Premium Features', 'post-slider-and-carousel' ); ?></a>
		</div>
	</div>
	<?php
	// If invalid shortcode is passed then simply return
	if( ! empty( $_GET['shortcode'] ) && ! isset( $registered_shortcodes[ $_GET['shortcode'] ] ) ) {

		$valid = false;

		echo '<div id="message" class="error notice">
				<p><strong>'.__('Sorry, Something happened wrong.', 'post-slider-and-carousel').'</strong></p>
			 </div>';
	}

	if( $valid ) : ?>

	<div class="psacp-shrt-invalid-param-notice psacp-shrt-alert psacp-shrt-alert-error psacp-hide">
		<p><?php esc_html_e('Sorry, The shortcode contains some invalid parameters. These parameters may be missing, obsolete, or incompatible with the current selection. Please verify and correct the parameters to ensure the shortcode functions correctly.', 'post-slider-and-carousel'); ?></p>
		<p><?php esc_html_e('The parameters are:', 'post-slider-and-carousel'); ?> <span class="psacp-shrt-invalid-params"></span></p>
	</div>

	<div class="psacp-shrt-toolbar">
		<div class="psacp-shrt-heading"><?php esc_html_e('Choose Shortcode', 'post-slider-and-carousel'); ?></div>
		<?php if( ! empty( $registered_shortcodes ) ) { ?>
			<select class="psacp-shrt-switcher" id="psacp-shrt-switcher">
				<option value=""><?php esc_html_e('-- Choose Shortcode --', 'post-slider-and-carousel'); ?></option>
				<?php foreach ($shortcodes_arr as $shrt_grp_key => $shrt_grp_val) {

					// Creating OPT group
					if( is_array( $shrt_grp_val ) && ! empty( $shrt_grp_val['shortcodes'] ) ) {

						$option_grp_name = !empty( $shrt_grp_val['name'] ) ? $shrt_grp_val['name'] : __('General', 'post-slider-and-carousel');
				?>
						<optgroup label="<?php echo esc_attr( $option_grp_name ); ?>">
						<?php foreach ($shrt_grp_val['shortcodes'] as $shrt_key => $shrt_val) {

							if( empty($shrt_key) ) {
								continue;
							}

							$shrt_val 		= !empty($shrt_val) ? $shrt_val : $shrt_key;
							$shortcode_url 	= add_query_arg( array('shortcode' => $shrt_key), $shrt_builder_url );
						?>
							<option value="<?php echo esc_attr( $shrt_key ); ?>" <?php disabled( ! in_array( $shrt_key, $allowed_reg_shortcodes ), true ); ?> <?php selected( $preview_shortcode, $shrt_key); ?> data-url="<?php echo esc_url( $shortcode_url ); ?>"><?php echo esc_html( $shrt_val ); ?></option>
						<?php } ?>
						</optgroup>

					<?php } else {
							$shrt_val 		= !empty($shrt_grp_val) ? $shrt_grp_val : $shrt_grp_key;
							$shortcode_url 	= add_query_arg( array('shortcode' => $shrt_grp_key), $shrt_builder_url );
					?>
						<option value="<?php echo esc_attr( $shrt_grp_key ); ?>" <?php disabled( ! in_array( $shrt_grp_key, $allowed_reg_shortcodes ), true ); ?> <?php selected( $preview_shortcode, $shrt_grp_key); ?> data-url="<?php echo esc_url( $shortcode_url ); ?>"><?php echo esc_html( $shrt_grp_val ); ?></option>
				<?php } // End of else
				} ?>
			</select>
		<?php } ?>

		<span class="psacp-shrt-generate-help psacp-tooltip" title="<?php esc_attr_e("The shortcode builder allows you to preview plugin shortcode. You can choose your desired shortcode from the dropdown and check various parameters from left panel. \n\nYou can paste shortcode to below textarea and press Generate button to preview so each and every time you do not have to choose each parameters!!!", 'post-slider-and-carousel'); ?>"><i class="dashicons dashicons-editor-help"></i></span>
	</div><!-- end .psacp-shrt-toolbar -->

	<div class="psacp-customizer psacp-clearfix" data-shortcode="<?php echo esc_attr( $preview_shortcode ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce('psacp-shortcode-builder') ); ?>" data-template="">
		<div class="psacp-shrt-fields-panel psacp-clearfix">
			<div class="psacp-shrt-heading"><?php esc_html_e('Shortcode Parameters', 'post-slider-and-carousel'); ?></div>
			<div class="psacp-shrt-accordion-wrap">
				<?php
					if ( function_exists( $shortcode_sanitize.'_lite_shortcode_fields' ) ) {
						$shortcode_fields = call_user_func( $shortcode_sanitize.'_lite_shortcode_fields', $preview_shortcode );
					}

					$shortcode_mapper = new PSAC_Shortcode_Builder();
					$shortcode_mapper->render( $shortcode_fields );
				?>
				<div class="psacp-shrt-loader"></div>
			</div>
		</div>

		<div class="psacp-shrt-preview-wrap psacp-clearfix">
			<div class="psacp-shrt-box-wrp">
				<div class="psacp-shrt-heading"><?php esc_html_e('Shortcode', 'post-slider-and-carousel'); ?> <span class="psacp-cust-heading-info psacp-tooltip" title="<?php esc_attr_e('Paste below shortcode to any page or post to get output as preview.', 'post-slider-and-carousel'); ?>">[?]</span>
					<div class="psacp-shrt-tool-wrap">
						<button type="button" class="button button-primary button-small psacp-cust-shrt-generate"><?php esc_html_e('Generate', 'post-slider-and-carousel') ?></button>
				 		<i title="<?php esc_attr_e('Full Preview Mode', 'post-slider-and-carousel'); ?>" class="psacp-tooltip psacp-shrt-dwp dashicons dashicons-editor-expand"></i>
				 	</div>
				 </div>
				<form action="<?php echo esc_url($preview_url); ?>" method="post" class="psacp-customizer-shrt-form" id="psacp-customizer-shrt-form" target="psacp_shortcode_preview_frame">
					<textarea name="psacp_customizer_shrt" class="psacp-shrt-box" id="psacp-shrt-box" placeholder="<?php esc_attr_e('Copy or Paste Shortcode', 'post-slider-and-carousel'); ?>"><?php echo esc_textarea( $shortcode_val ); ?></textarea>
					<input type="hidden" class="psacp-customizer-old-shrt" name="psacp_customizer_old_shrt" value="<?php echo esc_attr( $shortcode_val ); ?>" />
				</form>
			</div>
			<div class="psacp-shrt-heading"><?php esc_html_e('Preview Window', 'post-slider-and-carousel'); ?> <span class="psacp-cust-heading-info psacp-tooltip" title="<?php esc_attr_e('Preview will be displayed according to responsive layout mode. You can check with `Full Preview` mode for better visualization.', 'post-slider-and-carousel'); ?>">[?]</span></div>
			<div class="psacp-shrt-preview-window">
				<iframe class="psacp-shrt-preview-frame" name="psacp_shortcode_preview_frame" src="<?php echo esc_url($preview_url); ?>" scrolling="auto" frameborder="0"></iframe>
				<div class="psacp-shrt-loader"></div>
				<div class="psacp-shrt-error"><?php esc_html_e('Sorry, Something happened wrong.', 'post-slider-and-carousel'); ?></div>
			</div>
		</div>
	</div><!-- psacp-customizer -->

	<br/>
	<div class="psacp-cust-footer-note"><span class="description"><?php esc_html_e('Note: Preview will be displayed according to responsive layout mode. Live preview may display differently when added to your page based on inheritance from some styles.', 'post-slider-and-carousel'); ?></span></div>
	<?php endif ?>

</div><!-- end .wrap -->