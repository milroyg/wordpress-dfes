<?php
/** @since 2.4.3 */

namespace EACCustomWidgets\Includes\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

trait Remote_Response_Body_Trait {
	/**
	 * handle_remote_response_output
	 *
	 * @param string $url
	 * @param int $code
	 *
	 * @return bool
	 */
	protected function handle_remote_response_output( string $url, int $code ): bool {
		header( 'Content-Type: text/plain' );

		switch ( $code ) {
			case 401:
				printf( '"%1$s" => (%2$s) %3$s', esc_url( $url ), esc_html( $code ), esc_html__( 'Unauthorized', 'eac-components' ) );
				break;
			case 403:
				printf( '"%1$s" => (%2$s) %3$s', esc_url( $url ), esc_html( $code ), esc_html__( 'Forbidden', 'eac-components' ) );
				break;
			case 404:
				printf( '"%1$s" => (%2$s) %3$s', esc_url( $url ), esc_html( $code ), esc_html__( 'File not found', 'eac-components' ) );
				break;
			case 405:
				printf( '"%1$s" => (%2$s) %3$s', esc_url( $url ), esc_html( $code ), esc_html__( 'Method not allowed', 'eac-components' ) );
				break;
			case 429:
				printf( '"%1$s" => (%2$s) %3$s', esc_url( $url ), esc_html( $code ), esc_html__( 'Too many requests', 'eac-components' ) );
				break;
			case 495:
				printf( '"%1$s" => (%2$s) %3$s', esc_url( $url ), esc_html( $code ), esc_html__( 'SSL certificate error', 'eac-components' ) );
				break;
			case 496:
				printf( '"%1$s" => (%2$s) %3$s', esc_url( $url ), esc_html( $code ), esc_html__( 'SSL certificate required', 'eac-components' ) );
				break;
			case 500:
				printf( '"%1$s" => (%2$s) %3$s', esc_url( $url ), esc_html( $code ), esc_html__( 'Internal Server Error', 'eac-components' ) );
				break;
			case 503:
				printf( '"%1$s" => (%2$s) %3$s', esc_url( $url ), esc_html( $code ), esc_html__( 'Service unavailable. Retry later.', 'eac-components' ) );
				break;
			default:
				echo esc_html__( 'HTTP: Request failed.', 'eac-components' );
				break;
		}

		return false;
	}
}
