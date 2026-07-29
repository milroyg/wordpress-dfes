<?php
/**
 * Functions file
 *
 * @package Location_Weather.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin dashboard access capability.
 *
 * @return string
 */
function location_weather_dashboard_capability() {
	return apply_filters( 'location_weather_access_capability', 'manage_options' );
}

/**
 * Verifies the current user has the required capability for admin AJAX actions.
 * Sends a 403 JSON error and halts execution if the check fails.
 */
function location_weather_verify_capability() {
	if ( ! current_user_can( location_weather_dashboard_capability() ) ) {
		wp_send_json_error( __( 'Unauthorized access.', 'location-weather' ), 403 );
	}
}
