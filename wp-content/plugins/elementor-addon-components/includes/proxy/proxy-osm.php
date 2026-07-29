<?php
/**
 * Description: Collecte le contenu d'un fichier au format GeoJSON d'échange de données
 *
 * @param {string} $_REQUEST['url'] l'url du flux à analyser
 * @param {string} $_REQUEST['nonce'] le nonce à tester
 * @return {Object[]} Le contenu du fichier GeoJSON
 * @since 1.8.8
 */

namespace EACCustomWidgets\Includes\Proxy;

$parse_uri = isset( $_SERVER['SCRIPT_FILENAME'] ) ? explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] ) : '';
if ( ! empty( $parse_uri ) ) {
	require_once $parse_uri[0] . 'wp-load.php';
} else {
	header( 'Content-Type: text/plain' );
	echo esc_html__( "Unable to load 'wp-load'", 'eac-components' );
	exit;
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Eac_Load_Config;

if ( ! isset( $_REQUEST['url'] ) || ! isset( $_REQUEST['id'] ) || ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'eac_file_osm_nonce_' . sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) ) ) {
	header( 'Content-Type: text/plain' );
	echo esc_html__( 'Invalid token. Refresh the current page...', 'eac-components' );
	exit;
}

$file = filter_var( urldecode( $_REQUEST['url'] ), FILTER_SANITIZE_URL );
if ( ! $file ) {
	header( 'Content-Type: text/plain' );
	echo esc_html__( 'Invalid URL', 'eac-components' );
	exit;
}

$file_source = wp_safe_remote_get(
	$file,
	array(
		'timeout' => 10,
		'headers' => array( 'Accept' => 'application/json' ),
	)
);

if ( is_wp_error( $file_source ) || 200 !== wp_remote_retrieve_response_code( $file_source ) ) {
	header( 'Content-Type: text/plain' );
	$error_code = wp_remote_retrieve_response_code( $file_source );

	if ( 401 === $error_code ) {
		printf( '"%1$s" => (%2$s) %3$s', esc_url( $file ), esc_html( $error_code ), esc_html__( 'Unauthorized', 'eac-components' ) );
	} elseif ( 403 === $error_code ) {
		printf( '"%1$s" => (%2$s) %3$s', esc_url( $file ), esc_html( $error_code ), esc_html__( 'Forbidden', 'eac-components' ) );
	} elseif ( 404 === $error_code ) {
		printf( '"%1$s" => (%2$s) %3$s', esc_url( $file ), esc_html( $error_code ), esc_html__( 'File not found', 'eac-components' ) );
	} elseif ( 405 === $error_code ) {
		printf( '"%1$s" => (%2$s) %3$s', esc_url( $file ), esc_html( $error_code ), esc_html__( 'Method not allowed', 'eac-components' ) );
	} elseif ( 429 === $error_code ) {
		printf( '"%1$s" => (%2$s) %3$s', esc_url( $file ), esc_html( $error_code ), esc_html__( 'Too many requests', 'eac-components' ) );
	} elseif ( 495 === $error_code ) {
		printf( '"%1$s" => (%2$s) %3$s', esc_url( $file ), esc_html( $error_code ), esc_html__( 'SSL certificate error', 'eac-components' ) );
	} elseif ( 496 === $error_code ) {
		printf( '"%1$s" => (%2$s) %3$s', esc_url( $file ), esc_html( $error_code ), esc_html__( 'SSL certificate required', 'eac-components' ) );
	} elseif ( 500 === $error_code ) {
		printf( '"%1$s" => (%2$s) %3$s', esc_url( $file ), esc_html( $error_code ), esc_html__( 'Internal Server Error', 'eac-components' ) );
	} elseif ( 503 === $error_code ) {
		printf( '"%1$s" => (%2$s) %3$s', esc_url( $file ), esc_html( $error_code ), esc_html__( 'Service unavailable. Retry later', 'eac-components' ) );
	} else {
		echo esc_html__( 'HTTP: Request failed.', 'eac-components' );
	}

	return false;
} elseif ( empty( wp_remote_retrieve_body( $file_source ) ) ) {
	header( 'Content-Type: text/plain' );
	printf( '"%1$s" => %2$s', esc_url( $file ), esc_html__( 'Content empty', 'eac-components' ) );
	return false;
}

$json = wp_remote_retrieve_body( $file_source );

header( 'Content-Type: application/json' );
echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
