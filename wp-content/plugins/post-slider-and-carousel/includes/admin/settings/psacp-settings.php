<?php
/**
 * Settings Page HTML
 * 
 * The code for the plugins main settings page
 * 
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Reset all settings
if( ! empty( $_POST['psacp_reset_settings'] ) && check_admin_referer( 'psacp_reset_settings', 'psacp_reset_setting_nonce' ) ) {
	psac_set_default_settings();
}

$settings_tab_arr 	= psac_settings_tab();
$psacp_active_tab 	= ( ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $settings_tab_arr ) ) ? trim($_GET['tab']) : apply_filters( 'psacp_default_tab', 'welcome' );
?>
<div class="wrap">

	<h2><?php esc_html_e( 'Post Slider and Carousel Settings', 'post-slider-and-carousel' ); ?></h2>

	<?php
	// Check settings updated or not
	if( isset( $_GET['settings-updated'] ) && ! empty( $_GET['settings-updated'] ) ) {
		echo '<div id="message" class="updated notice is-dismissible"><p><strong>' . esc_html__( 'Changes Saved.', 'post-slider-and-carousel') . '</strong></p></div>';
	}

	// Check settings updated or not
	if( isset( $_POST['psacp_reset_settings'] ) && ! empty( $_POST['psacp_reset_settings'] ) ) {
		echo '<div id="message" class="updated notice is-dismissible"><p><strong>' . esc_html__( 'Settings reset successfully.', 'post-slider-and-carousel') . '</strong></p></div>';
	}
	?>

	<!-- Reset settings form -->
	<form action="" method="post" id="psacp-reset-sett-form" class="psacp-right psacp-reset-sett-form">
		<div class="psacp-reset-settings psacp-clearfix">
			<input type="submit" value="<?php esc_html_e('Reset All Settings', 'post-slider-and-carousel'); ?>" name="psacp_reset_settings" id="psacp_reset_settings" class="button button-primary psacp-reset-button" />
			<?php wp_nonce_field( 'psacp_reset_settings', 'psacp_reset_setting_nonce' ); ?>
		</div>
	</form>

	<div class="psacp-sett-wrp">

		<form action="options.php" method="POST" id="psacp-settings-form">

			<?php
			settings_fields( 'psacp_settings' );
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );

			global $psacp_options;
			?>

			<!-- Save Button -->
			<div class="textright psacp-clearfix">
				<input type="submit" name="psacp_sett_submit" class="button button-primary psacp-sett-submit" value="<?php esc_html_e('Save Settings', 'post-slider-and-carousel'); ?>" />
			</div>

			<h2 class="nav-tab-wrapper psacp-nav-tab-wrapper psacp-h2">
				<?php
				if( ! empty( $settings_tab_arr ) ) {
					foreach ($settings_tab_arr as $sett_key => $sett_val) {
						$selected_nav_cls 	= ($psacp_active_tab == $sett_key) ? 'nav-tab-active' : '';
						$tab_url 			= add_query_arg( array( 'page' => 'psacp-settings', 'tab' => $sett_key), admin_url('admin.php') );
				?>
						<a class="nav-tab <?php echo esc_attr( $selected_nav_cls ); ?> psacp-nav-tab-<?php echo esc_attr( $sett_key ); ?>" href="<?php echo esc_url( $tab_url ); ?>"><?php echo esc_html( $sett_val ); ?></a>
				<?php
					} // End of for each
				} // End of if
				?>
			</h2>
			
			<div class="psacp-sett-content-wrp psacp-sett-tab-cnt psacp-<?php echo esc_attr( $psacp_active_tab ); ?>-sett-cnt-wrp">
				<?php do_action( 'psac_settings_tab_' . $psacp_active_tab ); ?>
			</div><!-- end .psacp-sett-content-wrp -->

		</form><!-- end #psacp-settings-form -->

	</div><!-- end .psacp-sett-wrp -->
</div><!-- end .wrap -->