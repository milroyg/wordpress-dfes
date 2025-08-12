<?php
/**
 * Featured and Trending Post Pro Shortcode Mapper Page 
 *
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$valid					= true;
$meta_prefix			= PSAC_META_PREFIX;
$registered_shortcodes 	= psac_registered_shortcodes();
$shortcodes_arr 		= psac_registered_shortcodes( false );
$allowed_reg_shortcodes	= psac_allowed_reg_shortcodes();
$preview_shortcode 		= ! empty( $_GET['shortcode'] ) ? $_GET['shortcode'] : apply_filters('psacp_default_preview_shortcode', 'psac_post_slider' );
$action					= ( ! empty( $_GET['action'] ) && 'edit' == $_GET['action'] ) ? 'edit' : 'add';
$layout_id				= ( ! empty( $_GET['id'] ) && 'edit' == $action ) ? psac_clean_number( $_GET['id'] ) : false;
$preview_url 			= add_query_arg( array('page' => 'psacp-shortcode-preview', 'shortcode' => $preview_shortcode), admin_url('admin.php') );
$page_url				= add_query_arg( array('page' => 'psacp-layout'), admin_url('admin.php') );

// Instantiate the shortcode builder
if( ! class_exists( 'PSAC_Shortcode_Builder' ) ) {
	include_once( PSAC_DIR . '/includes/admin/shortcode-builder/class-psacp-shortcode-builder.php' );
}

// Getting layout temp data when we change the shortcode
if( isset( $_COOKIE['psacp_layout_temp_data'] ) ) {
	$layout_temp_data = json_decode( wp_unslash( $_COOKIE['psacp_layout_temp_data'] ), true );
}

$shortcode_val		= "[{$preview_shortcode}]";
$layout_title		= '';
$layout_desc		= '';
$layout_enable		= 1;
$shortcode_fields 	= array();
$shortcode_sanitize = str_replace('-', '_', $preview_shortcode);
$page_title			= __( 'Add New Layout', 'post-slider-and-carousel' );
$save_btn_text		= __('Publish', 'post-slider-and-carousel');

if( 'edit' == $action ) {
	$page_title		= __( 'Edit Layout', 'post-slider-and-carousel' );
	$save_btn_text	= __('Update', 'post-slider-and-carousel');
	$trash_url		= add_query_arg( array('page' => 'psacp-layouts', 'action' => 'delete', 'psacp_layout' => $layout_id, '_wpnonce' => wp_create_nonce('bulk-psacp_layouts') ), admin_url('admin.php') );
	$duplicate_url	= add_query_arg( array('page' => 'psacp-layouts', 'shortcode' => $preview_shortcode, 'action' => 'duplicate_layout', 'id' => $layout_id, '_wpnonce' => wp_create_nonce("psacp-duplicate-layout-{$layout_id}") ), admin_url('admin.php') );
}
?>
<div class="wrap psacp-layout-wrap">

	<h1 class="wp-heading-inline"><?php echo esc_html( $page_title ); ?></h1>
	<?php if( 'edit' == $action ) { ?>
	<a href="<?php echo esc_url( $page_url ); ?>" class="page-title-action"><?php esc_html_e('Add Layout', 'post-slider-and-carousel'); ?></a>
	<?php } ?>
	<hr class="wp-header-end">

	<?php
	// If invalid shortcode or data is passed then simply return
	if( ( ! empty( $_GET['shortcode'] ) && ! isset( $registered_shortcodes[ $_GET['shortcode'] ] ) )
		|| ( 'edit' == $action && ( empty( $layout_id ) || empty( $_GET['shortcode'] ) ) )
		|| ( 'add' == $action && isset( $_GET['id'] ) )
	) {

		$valid = false;

		echo '<div id="message" class="error notice">
				<p><strong>'.__('Sorry, Something happened wrong.', 'post-slider-and-carousel').'</strong></p>
			 </div>';
	}

	// Check valid shortcode template is there
	if( ! empty( $layout_id ) ) {

		// Get layout post
		$layout_data = get_post( $layout_id );

		if( $layout_data && isset( $layout_data->post_type ) && $layout_data->post_type == PSAC_LAYOUT_POST_TYPE ) {

			$layout_title		= $layout_data->post_title;
			$layout_desc		= $layout_data->post_content;
			$layout_enable		= ( 'publish' == $layout_data->post_status ) ? 1 : 0;
			$layout_shrt_type	= get_post_meta( $layout_id, $meta_prefix.'layout_shrt_type', true );

			if( $preview_shortcode == $layout_shrt_type ) {
				$shortcode_val = get_post_meta( $layout_id, $meta_prefix.'layout_shrt', true );
			}

		} else {

			$valid = false;

			echo '<div id="message" class="error notice">
					<p><strong>'.__('Sorry, No shortcode layout found.', 'post-slider-and-carousel').'</strong></p>
				</div>';
		}
	}

	// Set layout temp data if it is there
	$layout_title	= isset( $layout_temp_data['title'] )		? $layout_temp_data['title']		: $layout_title;
	$layout_desc	= isset( $layout_temp_data['description'] ) ? $layout_temp_data['description']	: $layout_desc;
	$layout_enable	= ! empty( $layout_temp_data['enable'] )	? 1									: $layout_enable;

	// Messages
	if( isset( $_GET['message'] ) && 1 == $_GET['message'] ) {
		
		echo '<div class="notice-success notice psacp-notice is-dismissible">
				<p><strong>'.__('Layout saved successfully.', 'post-slider-and-carousel').'</strong></p>
			 </div>';

	} else if( isset( $_GET['message'] ) && 0 == $_GET['message'] ) {
		
		echo '<div class="notice-error notice psacp-notice is-dismissible">
				<p><strong>'.__('Sorry, Something happened wrong. Layout data has not been saved.', 'post-slider-and-carousel').'</strong></p>
			 </div>';

	} else if( isset( $_GET['message'] ) && 2 == $_GET['message'] ) {
		
		echo '<div class="notice-success notice psacp-notice is-dismissible">
				<p><strong>'.__('Layout data copied successfully.', 'post-slider-and-carousel').'</strong></p>
			 </div>';
	}

	if( $valid ) : ?>

	<form action="" method="post" class="psacp-layout-submit-form" id="psacp-layout-submit-form">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<table class="form-table">
						<tr>
							<th><label for="psacp-layout-title"><?php esc_html_e('Layout Name', 'post-slider-and-carousel'); ?></label></th>
							<td><input type="text" id="psacp-layout-title" name="psacp_layout_title" class="large-text psacp-layout-title" value="<?php echo esc_attr( $layout_title ); ?>" spellcheck="true" autocomplete="off" /></td>
						</tr>
						<tr>
							<th><label for="psacp-layout-desc"><?php esc_html_e('Layout Description', 'post-slider-and-carousel'); ?></label></th>
							<td>
								<textarea name="psacp_layout_desc" id="psacp-layout-desc" class="large-text psacp-layout-desc"><?php echo esc_textarea( $layout_desc ); ?></textarea>
								<span class="description"><?php esc_html_e('Enter layout description. This is just for administrator purpose.'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="psacp-shrt-switcher"><?php esc_html_e('Choose Layout', 'post-slider-and-carousel'); ?></label></th>
							<td>
								<?php if( ! empty( $registered_shortcodes ) ) { ?>
									<select class="regular-text psacp-shrt-switcher" id="psacp-shrt-switcher" name="psacp_layout_shrt_type">
										<option value=""><?php esc_html_e('-- Choose Layout --', 'post-slider-and-carousel'); ?></option>
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

													$shrt_val 		= ! empty( $shrt_val ) ? $shrt_val : $shrt_key;
													$shortcode_url 	= add_query_arg( array('shortcode' => $shrt_key, 'action' => $action, 'id' => $layout_id), $page_url );
												?>
													<option value="<?php echo esc_attr( $shrt_key ); ?>" <?php disabled( ! in_array( $shrt_key, $allowed_reg_shortcodes ), true ); ?> <?php selected( $preview_shortcode, $shrt_key); ?> data-url="<?php echo esc_url( $shortcode_url ); ?>"><?php echo esc_html( $shrt_val ); ?></option>
												<?php } ?>
												</optgroup>

											<?php } else { 
													$shrt_val 		= ! empty( $shrt_grp_val ) ? $shrt_grp_val : $shrt_grp_key;
													$shortcode_url 	= add_query_arg( array('shortcode' => $shrt_grp_key, 'action' => $action, 'id' => $layout_id), $page_url );
											?>
												<option value="<?php echo esc_attr( $shrt_grp_key ); ?>" <?php disabled( ! in_array( $shrt_grp_key, $allowed_reg_shortcodes ), true ); ?> <?php selected( $preview_shortcode, $shrt_grp_key); ?> data-url="<?php echo esc_url( $shortcode_url ); ?>"><?php echo esc_html( $shrt_grp_val ); ?></option>
										<?php } // End of else
										} ?>
									</select>
								<?php } ?>
							</td>
						</tr>
						<?php if( $layout_id ) { ?>
						<tr>
							<th><label><?php esc_html_e('Shortcode', 'post-slider-and-carousel'); ?></label></th>
							<td>
								<div class="psacp-layout-shrt-preview-wrap">
									<?php esc_html_e('Kindly add below shortcode to any page or post to get the output.', 'post-slider-and-carousel'); ?>
									<div class="psacp-layout-shrt-preview">[psacp_tmpl layout_id="<?php echo esc_attr( $layout_id ); ?>"]
										<span class="psacp-copy psacp-layout-shrt-copy" title="<?php esc_attr_e('Copy', 'post-slider-and-carousel'); ?>" data-clipboard-text="<?php echo esc_attr( '[psacp_tmpl layout_id="'.$layout_id.'"]' ); ?>">
											<i class="dashicons dashicons-admin-page"></i>
											<span class="psacp-copy-success psacp-hide"><?php esc_html_e('Copied!', 'post-slider-and-carousel'); ?></span>
										</span>
									</div>
								</div>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>

				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables">
						<div id="submitdiv" class="postbox">
							<div class="postbox-header"><h2 class="hndle"><?php esc_html_e('Publish', 'post-slider-and-carousel'); ?></h2></div>
							<div class="inside">
								<div id="submitpost" class="submitbox">
									<div id="misc-publishing-actions">
										<?php if( 'edit' == $action ) { ?>
											<div class="misc-pub-section">
												<input type="checkbox" name="psacp_layout_enable" id="psacp-layout-enable" class="psacp-layout-enable" value="1" <?php checked( $layout_enable, 1 ); ?> />
												<label for="psacp-layout-enable"><?php esc_html_e('Enable Layout', 'post-slider-and-carousel'); ?></label>
												<p><?php esc_html_e('Note: If layout is not enabled then no result will be displayed at front.', 'post-slider-and-carousel'); ?></p>
											</div>
										<?php } else { ?>
											<div class="misc-pub-section">
												<p><?php esc_html_e('Choose your desired layout and check various parameters from left panel.', 'post-slider-and-carousel'); ?></p>
												<input type="hidden" name="psacp_layout_enable" id="psacp-layout-enable" class="psacp-layout-enable" value="1" />
											</div>
										<?php } ?>
									</div>
									<div id="major-publishing-actions">
										<?php if( 'edit' == $action ) { ?>
										<div id="duplicate-action"><a class="submitduplicate duplication psacp-confirm" href="<?php echo esc_url( $duplicate_url ); ?>"><?php esc_html_e('Copy to a new layout', 'post-slider-and-carousel'); ?></a></div>
										<div id="delete-action"><a class="submitdelete deletion psacp-confirm" href="<?php echo esc_url( $trash_url ); ?>"><?php esc_html_e('Delete Permanently', 'post-slider-and-carousel'); ?></a></div>
										<?php } ?>

										<div id="publishing-action">
											<span class="spinner"></span>
											<input type="submit" name="psacp_layout_save" class="button button-primary button-large psacp-layout-save" value="<?php echo esc_html( $save_btn_text ); ?>" />
											<input type="hidden" name="psacp_layout_save_nonce" value="<?php echo esc_attr( wp_create_nonce( 'psacp-layout-save-nonce' ) ); ?>" />
											<input type="hidden" name="psacp_layout_shrt_val" class="psacp-layout-shrt-val" value="" />
										</div>
										<div class="clear"></div>
									</div>									
								</div>								
							</div>
						</div>	
						
						<a class="psacp-pro-inline-button" target="_blank" href="<?php echo esc_url( PSAC_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock psacp-shrt-acc-header-pro-icon"></i> <?php esc_html_e('Unlock Premium Features', 'post-slider-and-carousel'); ?></a>
						
					</div>
				</div><!-- end .postbox-container-1 -->
			</div><!-- end .metabox-holder -->
			<br class="clear">
		</div><!-- end #poststuff -->
	</form>

	<div class="psacp-shrt-invalid-param-notice psacp-shrt-alert psacp-shrt-alert-error psacp-hide">
		<p><?php esc_html_e('Sorry, The shortcode contains some invalid parameters. These parameters may be missing, obsolete, or incompatible with the current selection. Please verify and correct the parameters to ensure the shortcode functions correctly.', 'post-slider-and-carousel'); ?></p>
		<p><?php esc_html_e('The parameters are:', 'post-slider-and-carousel'); ?> <span class="psacp-shrt-invalid-params"></span></p>
	</div>

	<div class="psacp-customizer psacp-clearfix" data-shortcode="<?php echo esc_attr( $preview_shortcode ); ?>" data-template="<?php echo esc_attr( $layout_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce('psacp-shortcode-builder') ); ?>">
		<div class="psacp-shrt-fields-panel psacp-clearfix">
			<div class="psacp-shrt-heading"><?php esc_html_e('Layout Options', 'post-slider-and-carousel'); ?></div>
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
			<div class="psacp-shrt-box-wrp psacp-hide">
				<div class="psacp-shrt-heading">
					<?php esc_html_e('Shortcode', 'post-slider-and-carousel'); ?> <span class="psacp-cust-heading-info psacp-tooltip" title="<?php esc_attr_e("Paste below shortcode to any page or post to get output as preview. \n\nYou can paste shortcode to below and press Generate button to preview so each and every time you do not have to choose each parameters!!!", "post-slider-and-carousel"); ?>">[?]</span>
					<div class="psacp-shrt-tool-wrap">
						<button type="button" class="button button-secondary button-small psacp-cust-shrt-generate"><?php esc_html_e('Generate', 'post-slider-and-carousel') ?></button>
					</div>
				 </div>
				<form action="<?php echo esc_url( $preview_url ); ?>" method="post" class="psacp-customizer-shrt-form" id="psacp-customizer-shrt-form" target="psacp_shortcode_preview_frame">
					<textarea name="psacp_customizer_shrt" class="psacp-shrt-box" id="psacp-shrt-box" placeholder="<?php esc_attr_e('Copy or Paste Shortcode', 'post-slider-and-carousel'); ?>"><?php echo esc_textarea( $shortcode_val ); ?></textarea>
					<input type="hidden" class="psacp-customizer-old-shrt" name="psacp_customizer_old_shrt" value="<?php echo esc_attr( $shortcode_val ); ?>" />
				</form>
			</div>
			<div class="psacp-shrt-heading">
				<?php esc_html_e('Preview Window', 'post-slider-and-carousel'); ?> <span class="psacp-cust-heading-info psacp-tooltip" title="<?php esc_attr_e('Preview will be displayed according to responsive layout mode. You can check with `Full Preview` mode for better visualization.', 'post-slider-and-carousel'); ?>">[?]</span>
				<div class="psacp-shrt-tool-wrap">
					<i title="<?php esc_attr_e('Full Preview Mode', 'post-slider-and-carousel'); ?>" class="psacp-tooltip psacp-shrt-dwp dashicons dashicons-editor-expand"></i>
					<a class="psacp-layout-debug psacp-layout-debug-js" href="#" title="<?php esc_html_e('Want to debug layout which parameters are being used?', 'post-slider-and-carousel'); ?>"><?php esc_html_e('Debug?', 'post-slider-and-carousel') ?></a>
				</div>
			</div>
			<div class="psacp-shrt-preview-window">
				<iframe class="psacp-shrt-preview-frame" name="psacp_shortcode_preview_frame" src="<?php echo esc_url( $preview_url ); ?>" scrolling="auto" frameborder="0"></iframe>
				<div class="psacp-shrt-loader"></div>
				<div class="psacp-shrt-error"><?php esc_html_e('Sorry, Something happened wrong.', 'post-slider-and-carousel'); ?></div>
			</div>
		</div>
	</div><!-- psacp-customizer -->

	<br/>
	<div class="psacp-cust-footer-note"><span class="description"><?php esc_html_e('Note: Preview will be displayed according to responsive layout mode. Live preview may display differently when added to your page based on inheritance from some styles.', 'post-slider-and-carousel'); ?></span></div>
	<?php endif ?>

</div><!-- end .wrap -->