<?php
/**
 * Code météo
 * @since 2.5.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mapping des weathercodes → libellés EN avec traductions
 */

$code_meteo = array(
	0  => esc_html__( 'Clear sky', 'eac-components' ),
	1  => esc_html__( 'Mainly clear', 'eac-components' ),
	2  => esc_html__( 'Partly cloudy', 'eac-components' ),
	3  => esc_html__( 'Overcast', 'eac-components' ),
	45  => esc_html__( 'Fog', 'eac-components' ),
	48  => esc_html__( 'Depositing rime fog', 'eac-components' ),
	51  => esc_html__( 'Light drizzle', 'eac-components' ),
	53  => esc_html__( 'Moderate drizzle', 'eac-components' ),
	55  => esc_html__( 'Dense drizzle', 'eac-components' ),
	56  => esc_html__( 'Light freezing drizzle', 'eac-components' ),
	57  => esc_html__( 'Dense freezing drizzle', 'eac-components' ),
	61  => esc_html__( 'Light rain', 'eac-components' ),
	63  => esc_html__( 'Moderate rain', 'eac-components' ),
	65  => esc_html__( 'Heavy rain', 'eac-components' ),
	66  => esc_html__( 'Light freezing rain', 'eac-components' ),
	67  => esc_html__( 'Heavy freezing rain', 'eac-components' ),
	71  => esc_html__( 'Light snow', 'eac-components' ),
	73  => esc_html__( 'Moderate snow', 'eac-components' ),
	75  => esc_html__( 'Heavy snow', 'eac-components' ),
	77  => esc_html__( 'Snow grains', 'eac-components' ),
	80  => esc_html__( 'Slight rain showers', 'eac-components' ),
	81  => esc_html__( 'Moderate rain showers', 'eac-components' ),
	82  => esc_html__( 'Violent rain showers', 'eac-components' ),
	85  => esc_html__( 'Slight snow showers', 'eac-components' ),
	86  => esc_html__( 'Heavy snow showers', 'eac-components' ),
	95  => esc_html__( 'Thunderstorm', 'eac-components' ),
	96  => esc_html__( 'Thunderstorm with slight hail', 'eac-components' ),
	99  => esc_html__( 'Thunderstorm with heavy hail', 'eac-components' ),
);
return $code_meteo;
