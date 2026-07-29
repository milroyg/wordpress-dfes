<?php
/**
 *  All metabox and options settings.
 *
 * @package    Location_weather_Pro
 * @subpackage Location_weather_Pro/Frontend
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( in_array( $block_name, array( 'owm-map', 'windy-map' ), true ) ) {
	return;
}

use ShapedPlugin\Weather\Frontend\Manage_API;
use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

// attributes for the block.
$show_air_pollution = true;
$temperature_scale  = $attributes['displayTemperatureUnit'] ?? 'metric';
$pressure_unit      = $attributes['displayPressureUnit'] ?? 'hpa';
$visibility_unit    = $attributes['displayVisibilityUnit'] ?? 'km';
$wind_speed_unit    = $attributes['displayWindSpeedUnit'] ?? 'kmh';
$precipitation_unit = $attributes['displayPrecipitationUnit'] ?? 'mm';
$user_time_format   = $attributes['splwTimeFormat'] ?? 'g:i A';
$user_date_format   = $attributes['splwDateFormat'] ?? 'M j, Y';
$custom_date_format = $attributes['splwCustomDateFormat'] ?? 'M j';
$lw_language        = $attributes['splwLanguage'] ?? 'en';
$utc_timezone       = $attributes['splwTimeZone'] ?? 'auto';
$utc_timezone       = 'auto' === $utc_timezone ? '' : (float) str_replace( 'UTC', '', $utc_timezone ) * 3600;
// Weather forecast meta data setup section.
$show_additional_data_icon    = $attributes['additionalDataIcon'] ?? true;
$enable_forecasting_days      = $attributes['displayWeatherForecastData'] ?? true;
$lw_forecast_type             = 'hourly';
$show_one_forecasting_hours   = $attributes['numberOfForecastHours'] ?? '8';
$show_three_forecasting_hours = $attributes['numOfForecastThreeHours'] ?? '40';
$hourly_forecast_type         = $attributes['hourlyForecastType'] ?? '3';
$lw_hourly_type               = $hourly_forecast_type ? ( '1' === $hourly_forecast_type ? 'one-hour' : 'three-hour' ) : 'three-hour';
$forecasting_hours            = 'one-hour' === $lw_hourly_type ? $show_one_forecasting_hours : $show_three_forecasting_hours;
$number_of_hours              = 'three-hour' === $lw_hourly_type ? (int) $show_three_forecasting_hours : (int) $show_one_forecasting_hours;
$forecasting_hours            = $number_of_hours > 8 ? 8 : $number_of_hours;
// Retrieve the method for getting weather information.
$weather_by             = $attributes['searchWeatherBy'] ?? 'city_name';
$weather_by_city_name   = $attributes['getDataByCityName'] ?? 'London, GB';
$weather_by_city_id     = str_replace( ' ', '', $attributes['getDataByCityID'] ?? '2643743' );
$weather_by_zip_code    = str_replace( ' ', '', $attributes['getDataByZIPCode'] ?? '77070,US' );
$weather_by_coordinates = str_replace( ' ', '', $attributes['getDataByCoordinates'] ?? '51.509865,-0.118092' );
$weather_custom_key     = $attributes['customCityName'] ?? '';

// this explain that daily and hourly both forecast data is displayed.
$is_both_forecast = false;
$splw_option      = get_option( 'location_weather_settings', true );
$api_source       = $attributes['lw_api_type'] ?? '';

// block query.
$block_query = array(
	'weather_by'           => $weather_by,
	'city_name'            => $weather_by_city_name,
	'city_id'              => $weather_by_city_id,
	'zip_code'             => $weather_by_zip_code,
	'temperature_scale'    => $temperature_scale,
	'coordinates'          => $weather_by_coordinates,
	'enable_forecast'      => $enable_forecasting_days,
	'forecast_type'        => $lw_forecast_type,
	'forecast_hourly_type' => 'openweather_api' === $api_source ? 'three-hour' : $lw_hourly_type,
	'forecast_hours'       => $forecasting_hours,
	'lw_api_source_type'   => $api_source,
	'openweather_api_type' => 'free',
	'open_weather_api'     => $splw_option['open-api-key'] ?? '',
	'weather_api_key'      => $splw_option['weather-api-key'] ?? '',
);


$shortcode_id = md5( wp_json_encode( $block_query ) );

// Get the weather data.
if ( 'openweather_api' === $api_source ) {
	$open_api_key = $splw_option['open-api-key'] ?? '';
	$appid        = ! empty( $open_api_key ) ? $open_api_key : '';
	// Set default API key if not found any API.
	if ( ! $appid ) {
		$default_api_calls = (int) get_option( 'splw_default_call', 0 );
		if ( $default_api_calls < 20 ) {
			$appid          = 'e930dd32085dea457d1d66d01cd89f50';
			$transient_name = 'sp_open_weather_data' . $shortcode_id;
			$weather_data   = Manage_API::splw_get_transient( $transient_name );
			if ( false === $weather_data ) {
				++$default_api_calls;
				update_option( 'splw_default_call', $default_api_calls );
			}
		}
	}
} else {
	// WeatherAPI API key.
	$weather_api_key = $splw_option['weather-api-key'] ?? '';
	$appid           = ! empty( $weather_api_key ) ? $weather_api_key : '';
	if ( ! $appid ) {
		$default_api_calls = (int) get_option( 'splw_weather_api_default_call', 0 );
		if ( $default_api_calls < 20 ) {
			$appid          = '9f165d0762d94693b9f114007250805';
			$transient_name = 'sp_weather_api_data_' . $shortcode_id;
			$weather_data   = Manage_API::splw_get_transient( $transient_name );
			if ( false === $weather_data ) {
				++$default_api_calls;
				update_option( 'splw_weather_api_default_call', $default_api_calls );
			}
		}
	}
}

switch ( $weather_by ) {
	case 'city_name':
		$query = ! empty( $weather_by_city_name ) ? trim( $weather_by_city_name ) : 'london';
		break;
	case 'city_id':
		$query = ! empty( $weather_by_city_id ) ? $weather_by_city_id : '2643743';
		break;
	case 'latlong':
		$parsed_data = Block_Helpers::validate_and_parse_coordinates( $weather_by_coordinates );
		if ( ! empty( $parsed_data['query'] ) ) {
			$query = $parsed_data['query'];
		} else {
			$error_message = $parsed_data['error_message'];
			return;
		}
		break;
	case 'zip':
		$query = ! empty( $weather_by_zip_code ) ? $weather_by_zip_code : '77070,US';
		break;
	default:
		$query = 'london';
		break;
}

$skip_cache = $skip_cache ?? false;

// Weather units.
$weather_units = $temperature_scale;

// Check if any error exist on api call.
$error_message = false;
if ( 'openweather_api' === $api_source ) {
	// Return error message if no WeatherAPI key is set.
	if ( empty( $appid ) ) {
		$error_message = sprintf( 'Please set your <a href="' . admin_url( 'edit.php?post_type=location_weather&page=splw_admin_dashboard#lw_settings' ) . '" target="_blank">API key</a> for OpenWeather.' );
		return;
	}
	// Get the current weather data from the OpenWeather API.
	$data = Manage_API::get_weather( $query, $weather_units, $lw_language, $appid, $shortcode_id, $skip_cache );
	// open weather api error.
	if ( is_array( $data ) && isset( $data['code'] ) && ( 401 === $data['code'] || 404 === $data['code'] ) ) {
		$error_message = $data['message'];
		return;
	}
} else {
	// Return error message if no WeatherAPI key is set.
	if ( empty( $appid ) ) {
		$error_message = sprintf( 'Please set your <a href="' . admin_url( 'edit.php?post_type=location_weather&page=splw_admin_dashboard#lw_settings' ) . '" target="_blank">WeatherAPI key.</a>' );
		return;
	}
	// Get the current and forecast weather data from the weatherAPI.
	$api_query        = is_array( $query ) ? implode( ',', $query ) : $query;
	$weather_api_data = Manage_API::weather_api_data( $api_query, $weather_units, $lw_language, $appid, $shortcode_id, (int) $forecasting_hours, $lw_hourly_type );
	// weather api error.
	if ( is_array( $weather_api_data ) && isset( $weather_api_data['code'] ) && ( 1006 === $weather_api_data['code'] || 2006 === $weather_api_data['code'] || 404 === $weather_api_data['code'] ) ) {
		$error_message = $weather_api_data['message'];
		return;
	}
	$data = $weather_api_data['current'];
}

// Timezone of the location.
$city_time_zone = '' === $utc_timezone ? (int) $data->timezone : (int) $utc_timezone;

$openweather_api_type = 'free';
$is_weather_api       = ( 'weather_api' === $api_source );
$forecast_settings    = array(
	'type'        => $lw_forecast_type,
	'hours'       => (int) $forecasting_hours,
	'hourly_type' => 'openweather_api' === $api_source ? 'three-hour' : $lw_hourly_type,
);

// Measurement units configuration.
$measurement_units = array(
	'temperature_scale'  => $weather_units,
	'pressure_unit'      => $pressure_unit,
	'visibility_unit'    => $visibility_unit,
	'wind_speed_unit'    => $wind_speed_unit,
	'weather_unit'       => $weather_units,
	'precipitation_unit' => $precipitation_unit,
	'humidity_unit'      => '%',
);

// Time settings configuration.
$weather_time_zone   = '' !== $utc_timezone ? (int) $utc_timezone : ( 'openweather_api' === $api_source ? $city_time_zone : null );
$defined_date_format = 'custom' === $user_date_format ? $custom_date_format : $user_date_format;
$time_settings       = array(
	'time_format'       => $user_time_format,
	'date_format'       => $defined_date_format,
	'time_zone'         => $city_time_zone,
	'weather_time_zone' => $weather_time_zone,
);

// Call the function to get air pollution data if required.
if ( ! $is_weather_api && $show_air_pollution && ( 'latlong' === $weather_by ) ) {
	$aqi_data = Manage_API::get_aqi_data( $query, $appid, $skip_cache, $shortcode_id );
}

// Forecast meta options.
$forecast_data = '';
$show_forecast = $splw_meta['lw-enable-forecast'] ?? true;
if ( $show_forecast ) {
	$forecast_data_sortable        = $splw_meta['lw_forecast_data_sortable'] ?? array(
		'temperature'   => true,
		'precipitation' => true,
		'rainchance'    => true,
		'wind'          => true,
		'humidity'      => true,
		'pressure'      => true,
		'snow'          => false,
	);
	$hourly_forecast_section_title = $splw_meta['hourly-forecast-title'] ?? 'Hourly Forecast';

	if ( 'openweather_api' === $api_source ) {
		$forecast = Manage_API::get_weather_hourly_forecast_data( $query, $weather_units, $lw_language, $appid, $shortcode_id, $forecast_settings );
	} else {
		$forecast = $weather_api_data['forecast'];
	}

	if ( is_object( $forecast ) ) {
		$forecast_data = $forecast->hourly_forecast;
	}
}

// current weather data.
$is_editor          = $is_editor ?? false;
$air_pollution_data = false;
$weather_data       = Block_Helpers::current_weather_data( $data, $measurement_units, $time_settings, $air_pollution_data, $is_editor );
$country_code       = $weather_data['country'];

// weather item labels.
$weather_item_labels = array(
	'temperature'             => __( 'Temperature', 'location-weather' ),
	'pressure'                => __( 'Pressure', 'location-weather' ),
	'humidity'                => __( 'Humidity', 'location-weather' ),
	'wind'                    => __( 'Wind', 'location-weather' ),
	'wind_speed'              => __( 'Wind Speed', 'location-weather' ),
	'wind_direction'          => __( 'Wind Direction', 'location-weather' ),
	'precipitation'           => __( 'Precipitation', 'location-weather' ),
	'clouds'                  => __( 'Clouds', 'location-weather' ),
	'rainchance'              => __( 'Rain Chance', 'location-weather' ),
	'rain_chance'             => __( 'Rain Chances', 'location-weather' ),
	'rain'                    => __( 'Rain Chances', 'location-weather' ),
	'snow'                    => __( 'Snow', 'location-weather' ),
	'gust'                    => __( 'Wind Gust', 'location-weather' ),
	'uv_index'                => __( 'UV Index', 'location-weather' ),
	'dew_point'               => __( 'Dew Point', 'location-weather' ),
	'air_index'               => __( 'Air Quality', 'location-weather' ),
	'visibility'              => __( 'Visibility', 'location-weather' ),
	'sunriseSunset'           => __( 'Sunrise & Sunset', 'location-weather' ),
	'sunrise'                 => __( 'Sunrise', 'location-weather' ),
	'sunset'                  => __( 'Sunset', 'location-weather' ),
	'sunrise_time'            => __( 'Sunrise', 'location-weather' ),
	'sunset_time'             => __( 'Sunset', 'location-weather' ),
	'moonriseMoonset'         => __( 'Moonrise & Moonset', 'location-weather' ),
	'moonrise'                => __( 'Moonrise', 'location-weather' ),
	'moonset'                 => __( 'Moonset', 'location-weather' ),
	'moon_phase'              => __( 'Moon Phase', 'location-weather' ),
	'national_weather_alerts' => __( 'National Alerts', 'location-weather' ),
	'date'                    => __( 'Date', 'location-weather' ),
	'day'                     => __( 'Day', 'location-weather' ),
	'hour'                    => __( 'Hour', 'location-weather' ),
	'weather'                 => __( 'Condition', 'location-weather' ),
	'amount'                  => __( 'Amount', 'location-weather' ),
);
