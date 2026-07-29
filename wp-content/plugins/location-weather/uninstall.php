<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://shapedplugin.com/
 * @since      1.1.0
 *
 * @package    Location_weather
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete plugin data function.
 *
 * @return void
 */
function sp_lw_delete_plugin_data() {

	// Delete plugin option settings.
	$option_name = 'location_weather_settings';
	delete_option( $option_name );
	delete_site_option( $option_name ); // For site options in Multisite.

	// Delete consent notice options.
	delete_option( 'splw_ignored_consent_notice' );
	delete_site_option( 'splw_ignored_consent_notice' );
	delete_option( 'splw_consent_notice_start_time' );
	delete_site_option( 'splw_consent_notice_start_time' );
	// Delete setup wizard visited option.
	delete_option( 'splw_setup_wizard_visited' );
	delete_site_option( 'splw_setup_wizard_visited' );
	// Delete anonymous data option (user consent for tracking user data).
	delete_option( 'splw_allow_anonymous_data' );
	delete_site_option( 'splw_allow_anonymous_data' );
	// Delete site type option.
	delete_option( 'sp_ua_site_type' );
	delete_site_option( 'sp_ua_site_type' );
	// Delete blocks visibility options.
	delete_option( 'splw_blocks_visibility_options' );
	delete_site_option( 'splw_blocks_visibility_options' );

	// Delete weather post type.
	$lw_posts = get_posts(
		array(
			'numberposts' => -1,
			'post_type'   => 'location_weather',
			'post_status' => 'any',
		)
	);
	foreach ( $lw_posts as $post ) {
		wp_delete_post( $post->ID, true );
	}

	// Delete saved templates post type.
	$lw_saved_templates = get_posts(
		array(
			'numberposts' => -1, // Retrieve all posts.
			'post_type'   => 'spl_weather_template',
			'post_status' => 'any',
		)
	);
	foreach ( $lw_saved_templates as $template ) {
		wp_delete_post( $template->ID, true );
	}

	// Delete weather post meta.
	delete_post_meta_by_key( 'sp_location_weather_generator' );

	// Unschedule previously scheduled event.
	$timestamp = wp_next_scheduled( 'location_weather_weekly_scheduled_events' );
	wp_unschedule_event( $timestamp, 'location_weather_weekly_scheduled_events', array() );
}

// Load splw file.
require plugin_dir_path( __FILE__ ) . '/main.php';
$sp_lw_options     = get_option( 'location_weather_settings' );
$sp_lw_plugin_data = isset( $sp_lw_options['splw_delete_on_remove'] ) ? $sp_lw_options['splw_delete_on_remove'] : false;
if ( $sp_lw_plugin_data ) {
	sp_lw_delete_plugin_data();
}
