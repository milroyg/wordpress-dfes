<?php
/**
 * Weather All Icons Template File
 *
 * Includes all svg icons for the frontend view.
 *
 * @package Location_Weather_Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

wp_enqueue_style( 'splw-fontello' );

// Additional data icon set.
$show_weather_icons  = $attributes['additionalDataIcon'];
$additional_icon_set = $attributes['additionalDataIconType'];
$icon_sets           = array(
	'icon_set_one'   => 1,
	'icon_set_two'   => 2,
	'icon_set_three' => 3,
	'icon_set_four'  => 4,
);

$block_weather_icons = array(
	'humidity'      => 'splwp-icon-humidity-' . $icon_sets[ $additional_icon_set ],
	'pressure'      => 'splwp-icon-pressure-' . $icon_sets[ $additional_icon_set ],
	'wind'          => 'splwp-icon-wind-' . $icon_sets[ $additional_icon_set ],
	'gust'          => 'splwp-icon-wind-gust-' . $icon_sets[ $additional_icon_set ],
	'clouds'        => 'splwp-icon-clouds-' . $icon_sets[ $additional_icon_set ],
	'visibility'    => 'splwp-icon-visibility-' . $icon_sets[ $additional_icon_set ],
	'sunrise_time'  => 'splwp-icon-sunrise-' . $icon_sets[ $additional_icon_set ],
	'sunset_time'   => 'splwp-icon-sunset-' . $icon_sets[ $additional_icon_set ],
	'temperature'   => 'splwp-icon-temperature-' . $icon_sets[ $additional_icon_set ],
	'precipitation' => 'splwp-icon-precipitation-' . $icon_sets[ $additional_icon_set ],
	'rainchance' => 'splwp-icon-rain-chance-' . $icon_sets[ $additional_icon_set ],
);
