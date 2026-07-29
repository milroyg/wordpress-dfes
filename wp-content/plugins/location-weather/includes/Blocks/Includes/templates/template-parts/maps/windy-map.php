<?php
/**
 * Windy Map Template Renderer File.
 *
 * This template displays the Windy.com weather map.
 *
 * @since 3.2.0
 * @version 1.0.0
 *
 * @package Location_Weather_Pro/Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$lat_lon = isset( $attributes['getDataByCoordinates'] ) ? explode( ',', $attributes['getDataByCoordinates'] ) : array( 0, 0 );
$lat     = trim( $lat_lon[0] ?? 0 );
$lon     = trim( $lat_lon[1] ?? 0 );

$query_params = array(
	'lat'        => $lat,
	'lon'        => $lon,
	'detailLat'  => $lat,
	'detailLon'  => $lon,
	'zoom'       => $attributes['mapZoomLevel'] ?? 5,
	'overlay'    => $attributes['defaultDataLayerSelection'] ?? 'wind',
	'marker'     => $attributes['showMarker'] ?? 'true',
	'pressure'   => $attributes['airflowPressureLines'] ?? '',
	'metricRain' => ( $attributes['displayPrecipitationUnit'] ?? '' ) === 'inch' ? 'in' : 'mm',
	'metricSnow' => ( $attributes['displayPrecipitationUnit'] ?? '' ) === 'inch' ? 'in' : 'mm',
	'metricWind' => $attributes['displayWindSpeedUnit'] ?? 'km/h',
	'metricTemp' => ( $attributes['displayTemperatureUnit'] ?? '' ) === 'imperial' ? '°F' : '°C',
	'detail'     => $attributes['spotForecast'] ?? 'true',
	'calendar'   => $attributes['forecastFrom'] ?? '',
	'product'    => $attributes['forecastModel'] ?? '',
	'level'      => $attributes['defaultElevation'] ?? '',
	'message'    => $attributes['weatherAttribution'] ?? '',
	'radarRange' => '-1',
);

$query_string = http_build_query(
	array_filter(
		$query_params,
		function ( $v ) {
			return null !== $v && '' !== $v;
		}
	)
);

$iframe_src = 'https://embed.windy.com/embed.html?type=map&location=coordinates&' . $query_string;

?>
<div class="spl-weather-map-template spl-weather-windy-map">
	<iframe
		class="sp-location-weather-windy"
		style="width: 100%; height: 100%;"
		title="Location Weather"
		loading="lazy"
		src="<?php echo esc_url( $iframe_src ); ?>"
		frameBorder="0">
	</iframe>
</div>
