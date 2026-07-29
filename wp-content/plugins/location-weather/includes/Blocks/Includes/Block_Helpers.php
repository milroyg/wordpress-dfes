<?php
/**
 * Weather Blocks Helpers.
 *
 * @package    Location_Weather
 * @subpackage Blocks
 * @since      3.2.0
 * @version    3.2.0
 */

namespace ShapedPlugin\Weather\Blocks\Includes;

use ShapedPlugin\Weather\Frontend\Shortcode;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Block_Helpers
 */
class Block_Helpers {

	/**
	 * Processes additional data options and expands special cases.
	 *
	 * @param array   $additional_data_options The input options array.
	 * @param boolean $skip_value return all the items without check is active or inactive.
	 * @return array Processed options array.
	 */
	public static function process_additional_data_options( $additional_data_options, $skip_value = false ) {
		$options = array();

		foreach ( $additional_data_options as $option ) {
			if ( ! $skip_value && ( ! isset( $option['isActive'] ) || ! $option['isActive'] ) ) {
				continue;
			}
			$value = $option['value'] ?? '';

			switch ( $value ) {
				case 'sunriseSunset':
					$options[] = 'sunrise_time';
					$options[] = 'sunset_time';
					break;
				default:
					$options[] = $value;
					break;
			}
		}

		return $options;
	}

	/**
	 * Method process_forecast_data_options
	 *
	 * @param array $forecast_title_array $forecast_title_array.
	 *
	 * @return array Processed options array.
	 */
	public static function process_forecast_data_options( $forecast_title_array ) {
		$options = array();
		foreach ( $forecast_title_array as $option ) {
			if ( $option['value'] ) {
				$options[] = $option['name'];
			}
		}
		return $options;
	}

	/**
	 * Method current_weather_data
	 *
	 * @param object  $data api source data.
	 * @param array   $measurement_units measurement_units.
	 * @param array   $time_settings time_settings.
	 * @param array   $air_pollution air_pollution.
	 * @param boolean $is_editor check is_editor.
	 *
	 * @return void
	 */
	public static function current_weather_data( $data, $measurement_units, $time_settings, $air_pollution = '', $is_editor = false ) {
		if ( ! is_object( $data->city ) ) {
			return;
		}

		// Temperature data.
		$scale = Shortcode::temperature_scale( $measurement_units['temperature_scale'], $measurement_units['weather_unit'] );
		$temp  = round( $data->temperature->now->value );

		// Visibility, wind, pressure, etc.
		$visibility = Shortcode::get_visibility( $measurement_units['visibility_unit'], $data );
		$pressure   = Shortcode::get_pressure( $measurement_units['pressure_unit'], $data );
		$wind       = Shortcode::get_wind_speed( $measurement_units['weather_unit'], $measurement_units['wind_speed_unit'], $data, false );
		$gust       = Shortcode::get_wind_speed( $measurement_units['weather_unit'], $measurement_units['wind_speed_unit'], $data, true );
		// others data.
		$humidity = array(
			'value' => $data->humidity->value,
			'unit'  => '%',
		);
		// Sun times.
		$last_update = $data->last_update;
		$sunrise     = $data->sun->rise;
		$sunset      = $data->sun->set;

		// Date and time formatting.
		$now = new \DateTime();
		if ( $time_settings['time_format'] && null !== $last_update ) {
			$time         = date_i18n( $time_settings['time_format'], strtotime( $now->format( 'Y-m-d g:i:sa' ) ) + $time_settings['time_zone'] );
			$date         = date_i18n( $time_settings['date_format'], strtotime( $now->format( 'Y-m-d g:i:sa' ) ) + $time_settings['time_zone'] );
			$sunrise_time = gmdate( $time_settings['time_format'], strtotime( $sunrise->format( 'Y-m-d g:i:sa' ) ) + $time_settings['weather_time_zone'] );
			$sunset_time  = gmdate( $time_settings['time_format'], strtotime( $sunset->format( 'Y-m-d g:i:sa' ) ) + $time_settings['weather_time_zone'] );
			$updated_time = gmdate( $time_settings['time_format'], strtotime( $last_update->format( 'Y-m-d g:i:sa' ) ) + $time_settings['weather_time_zone'] );
		}
		$sun_position_data = array(
			'time'         => $time,
			'sunrise_time' => $sunrise_time,
			'sunset_time'  => $sunset_time,
		);
		$sun_position      = self::calculate_sun_position_angle( $sun_position_data );
		$weather_clouds    = (int) $data->clouds->value;

		return array(
			'city_id'      => $data->city->id,
			'city'         => $data->city->name,
			'country'      => $data->city->country,
			'temp'         => $temp,
			'pressure'     => $pressure,
			'humidity'     => $humidity,
			'visibility'   => $visibility,
			'clouds'       => $weather_clouds . '%',
			'desc'         => $data->weather->description,
			'icon'         => $data->weather->icon,
			'time'         => $time,
			'date'         => $date,
			'time_zone'    => $time_settings['time_zone'],
			'sunrise_time' => $sunrise_time,
			'sunset_time'  => $sunset_time,
			'wind'         => $is_editor ? $data->wind : $wind,
			'gust'         => $is_editor ? $data->gusts : $gust,
			'weather_unit' => $is_editor ? $measurement_units['weather_unit'] : $scale,
			'updated_time' => $updated_time,
			'ground_label' => $data->ground_label ?? null,
			'sea_label'    => $data->sea_label ?? null,
			'sun_position' => $sun_position,
		);
	}

	/**
	 * Calculates the sun's position angle based on sunrise, sunset, and current time.
	 *
	 * @param array $weather_data Weather data containing:
	 *                            - 'time' (string): Current time ("hh:mm am/pm").
	 *                            - 'sunrise_time' (string): Sunrise time ("hh:mm am/pm").
	 *                            - 'sunset_time' (string): Sunset time ("hh:mm am/pm").
	 *
	 * @return float Sun's position angle (in degrees) relative to sunrise.
	 *               Capped at 170 degrees and returns -100 if the calculated angle exceeds the limit.
	 */
	public static function calculate_sun_position_angle( $weather_data ) {
		$current_time      = $weather_data['time'];
		$sunrise_time_str  = $weather_data['sunrise_time'];
		$sunset_time_str   = $weather_data['sunset_time'];
		$sunrise_timestamp = strtotime( $sunrise_time_str );
		$sunset_timestamp  = strtotime( $sunset_time_str );
		$current_timestamp = strtotime( $current_time );

		// Calculate the time difference and sun position angle.
		$time_difference = $sunset_timestamp - $sunrise_timestamp;
		$time_difference = ( 0 === $time_difference ) ? 1 : $time_difference;
		$angle           = ( ( $current_timestamp - $sunrise_timestamp ) * 170 ) / $time_difference;
		return round( $angle ) > 170 ? -100 : round( $angle );
	}

	/**
	 * Method get_wind_direction_icon
	 *
	 * @param array $single_forecast is a single day or hourly forecast.
	 * @return string direction_icon.
	 */
	public static function get_wind_direction_icon( $single_forecast ) {
		$direction_value        = $single_forecast['wind_direction']->value ?? 270;
		$direction_modification = apply_filters( 'splw_direction_modification', 180 );
		$direction_angle        = ( $direction_value - 90 - $direction_modification );
		$direction_icon         = '<svg data-v-47880d39="" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve" fill="#000" class="icon-wind-direction" width="12px" height="12px" style="transform: rotate(' . $direction_angle . 'deg);margin-left: 6px;"><g data-v-47880d39="" fill="rgba(46, 46, 46, 1)"><path data-v-47880d39="" d="M510.5,749.6c-14.9-9.9-38.1-9.9-53.1,1.7l-262,207.3c-14.9,11.6-21.6,6.6-14.9-11.6L474,48.1c5-16.6,14.9-18.2,21.6,0l325,898.7c6.6,16.6-1.7,23.2-14.9,11.6L510.5,749.6z"></path><path data-v-47880d39="" d="M817.2,990c-8.3,0-16.6-3.3-26.5-9.9L497.2,769.5c-5-3.3-18.2-3.3-23.2,0L210.3,976.7c-19.9,16.6-41.5,14.9-51.4,0c-6.6-9.9-8.3-21.6-3.3-38.1L449.1,39.8C459,13.3,477.3,10,483.9,10c6.6,0,24.9,3.3,34.8,29.8l325,898.7c5,14.9,5,28.2-1.7,38.1C837.1,985,827.2,990,817.2,990z M485.6,716.4c14.9,0,28.2,5,39.8,11.6l255.4,182.4L485.6,92.9l-267,814.2l223.9-177.4C454.1,721.4,469,716.4,485.6,716.4z"></path></g></svg>';
		return $direction_icon;
	}

	/**
	 * Method validate_and_parse_coordinates
	 *
	 * @param string $coordinate is block $coordinate.
	 * @return mixed
	 */
	public static function validate_and_parse_coordinates( $coordinate ) {
		$default_coords = array(
			'lat' => 51.509865,
			'lon' => -0.118092,
		);
		$error_message  = __( 'Invalid coordinates. Please provide a valid latitude and longitude in the format: 51.509865,-0.118092.', 'location-weather' );
		$data           = array(
			'error_message' => null,
			'query'         => array(),
		);
		// if coordinate is empty then return london coordinate as default coords.
		if ( empty( $coordinate ) ) {
			$data['query'] = $default_coords;
			return $data;
		}
		if ( preg_match( '/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/', $coordinate ) ) {
			$coords = explode( ',', trim( $coordinate ) );
			if ( 2 === count( $coords ) ) {
				list($latitude, $longitude) = $coords;
				$data['query']              = array(
					'lat' => $latitude,
					'lon' => $longitude,
				);
			} else {
				$data['error_message'] = $error_message;
			}
		} else {
			$data['error_message'] = $error_message;
		}
		return $data;
	}

	/**
	 * Get AQI Reports Based on condition.
	 *
	 * @return array
	 */
	public static function get_aqi_reports() {
		return array(
			'Good'      => __( 'Air quality is excellent. No health concerns for any group.', 'location-weather' ),
			'Moderate'  => __( 'Air is acceptable, but sensitive individuals should closely monitor their symptoms.', 'location-weather' ),
			'Poor'      => __( 'Air may cause discomfort. Sensitive groups should reduce outdoor exposure.', 'location-weather' ),
			'Unhealthy' => __( 'Health risks increase. Everyone should limit the time spent outdoors.', 'location-weather' ),
			'Severe'    => __( 'Air is very unhealthy. Avoid outdoor activity whenever possible.', 'location-weather' ),
			'Hazardous' => __( 'Serious health threat. Stay indoors and follow public health recommendations.', 'location-weather' ),
		);
	}

	/**
	 * Convert a HEX color to RGB or RGBA format.
	 *
	 * @param string     $hex     The HEX color.
	 * @param float|null $opacity Optional opacity value between 0 and 1.
	 * @return string RGB (r, g, b) or RGBA (r, g, b, a) string.
	 */
	public static function hex_to_rgba( $hex, $opacity = null ) {
		// Remove '#' if present.
		$hex = str_replace( '#', '', $hex );

		// Convert hex to decimal.
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		// Return RGBA or just RGB.
		if ( null !== $opacity ) {
			return "rgba($r, $g, $b, $opacity)";
		} else {
			return "$r, $g, $b";
		}
	}

	/**
	 * Get AQI Reports Based on condition.
	 *
	 * @return array
	 */
	public static function get_aqi_reports_detailed() {
		return array(
			'Good'      => __( 'Air is clean and safe. No health risks expected. Everyone can enjoy outdoor activities without concern.', 'location-weather' ),
			'Moderate'  => __( 'The air quality is generally acceptable for most people. However, sensitive groups may experience minor to moderate symptoms from long-term exposure.', 'location-weather' ),
			'Poor'      => __( 'Air quality is poor. Sensitive individuals may experience symptoms such as coughing or throat irritation. Limit outdoor activity.', 'location-weather' ),
			'Unhealthy' => __( 'Air quality poses health risks for all. Even healthy individuals may experience breathing difficulties and other symptoms. Minimize outdoor activities.', 'location-weather' ),
			'Severe'    => __( 'Air quality poses health risks for all. Even healthy individuals may experience breathing difficulties and other symptoms. Minimize outdoor activities.', 'location-weather' ),
			'Hazardous' => __( 'Air pollution is at emergency levels. Severe health risk for all. Stay indoors and follow public health instructions.', 'location-weather' ),
		);
	}

	/**
	 * Get pollutant breakpoint.
	 *
	 * @return array
	 */
	public static function get_pollutant_breakpoints() {
		return array(
			'pm2_5' => array(
				array(
					'cLow'  => 0.0,
					'cHigh' => 10,
					'iLow'  => 0,
					'iHigh' => 50,
				),
				array(
					'cLow'  => 10,
					'cHigh' => 25,
					'iLow'  => 51,
					'iHigh' => 100,
				),
				array(
					'cLow'  => 25,
					'cHigh' => 50,
					'iLow'  => 101,
					'iHigh' => 150,
				),
				array(
					'cLow'  => 50,
					'cHigh' => 75,
					'iLow'  => 151,
					'iHigh' => 200,
				),
				array(
					'cLow'  => 75,
					'cHigh' => 100,
					'iLow'  => 201,
					'iHigh' => 250,
				),
				array(
					'cLow'  => 100,
					'cHigh' => INF,
					'iLow'  => 251,
					'iHigh' => 300,
				),
			),
			'pm10'  => array(
				array(
					'cLow'  => 0,
					'cHigh' => 20,
					'iLow'  => 0,
					'iHigh' => 50,
				),
				array(
					'cLow'  => 20,
					'cHigh' => 50,
					'iLow'  => 51,
					'iHigh' => 100,
				),
				array(
					'cLow'  => 50,
					'cHigh' => 100,
					'iLow'  => 101,
					'iHigh' => 150,
				),
				array(
					'cLow'  => 100,
					'cHigh' => 200,
					'iLow'  => 151,
					'iHigh' => 200,
				),
				array(
					'cLow'  => 200,
					'cHigh' => 250,
					'iLow'  => 201,
					'iHigh' => 250,
				),
				array(
					'cLow'  => 250,
					'cHigh' => INF,
					'iLow'  => 251,
					'iHigh' => 300,
				),
			),
			'no2'   => array(
				array(
					'cLow'  => 0,
					'cHigh' => 40,
					'iLow'  => 0,
					'iHigh' => 50,
				),
				array(
					'cLow'  => 40,
					'cHigh' => 70,
					'iLow'  => 51,
					'iHigh' => 100,
				),
				array(
					'cLow'  => 70,
					'cHigh' => 150,
					'iLow'  => 101,
					'iHigh' => 150,
				),
				array(
					'cLow'  => 150,
					'cHigh' => 200,
					'iLow'  => 151,
					'iHigh' => 200,
				),
				array(
					'cLow'  => 200,
					'cHigh' => 250,
					'iLow'  => 201,
					'iHigh' => 250,
				),
				array(
					'cLow'  => 250,
					'cHigh' => INF,
					'iLow'  => 251,
					'iHigh' => 300,
				),
			),
			'o3'    => array(
				array(
					'cLow'  => 0,
					'cHigh' => 60,
					'iLow'  => 0,
					'iHigh' => 50,
				),
				array(
					'cLow'  => 60,
					'cHigh' => 100,
					'iLow'  => 51,
					'iHigh' => 100,
				),
				array(
					'cLow'  => 100,
					'cHigh' => 140,
					'iLow'  => 101,
					'iHigh' => 150,
				),
				array(
					'cLow'  => 140,
					'cHigh' => 180,
					'iLow'  => 151,
					'iHigh' => 200,
				),
				array(
					'cLow'  => 180,
					'cHigh' => 220,
					'iLow'  => 201,
					'iHigh' => 250,
				),
				array(
					'cLow'  => 220,
					'cHigh' => INF,
					'iLow'  => 251,
					'iHigh' => 300,
				),
			),
			'co'    => array(
				array(
					'cLow'  => 0,
					'cHigh' => 4400,
					'iLow'  => 0,
					'iHigh' => 50,
				),
				array(
					'cLow'  => 4400,
					'cHigh' => 9400,
					'iLow'  => 51,
					'iHigh' => 100,
				),
				array(
					'cLow'  => 9400,
					'cHigh' => 12400,
					'iLow'  => 101,
					'iHigh' => 150,
				),
				array(
					'cLow'  => 12400,
					'cHigh' => 15400,
					'iLow'  => 151,
					'iHigh' => 200,
				),
				array(
					'cLow'  => 15400,
					'cHigh' => 18000,
					'iLow'  => 201,
					'iHigh' => 250,
				),
				array(
					'cLow'  => 18000,
					'cHigh' => INF,
					'iLow'  => 251,
					'iHigh' => 300,
				),
			),
			'so2'   => array(
				array(
					'cLow'  => 0,
					'cHigh' => 20,
					'iLow'  => 0,
					'iHigh' => 50,
				),
				array(
					'cLow'  => 20,
					'cHigh' => 80,
					'iLow'  => 51,
					'iHigh' => 100,
				),
				array(
					'cLow'  => 80,
					'cHigh' => 250,
					'iLow'  => 101,
					'iHigh' => 150,
				),
				array(
					'cLow'  => 250,
					'cHigh' => 350,
					'iLow'  => 151,
					'iHigh' => 200,
				),
				array(
					'cLow'  => 350,
					'cHigh' => 400,
					'iLow'  => 201,
					'iHigh' => 250,
				),
				array(
					'cLow'  => 400,
					'cHigh' => INF,
					'iLow'  => 251,
					'iHigh' => 300,
				),
			),
		);
	}

	/**
	 * Returns pollutant data with keys, names, and symbols.
	 *
	 * @return array
	 */
	public static function get_pollutant_scale_data() {
		return array(
			array(
				'key'    => 'pm2_5',
				'name'   => __( 'Particulate Matter', 'location-weather' ),
				'symbol' => 'PM2.5',
			),
			array(
				'key'    => 'pm10',
				'name'   => __( 'Particulate Matter', 'location-weather' ),
				'symbol' => 'PM10',
			),
			array(
				'key'    => 'so2',
				'name'   => __( 'Sulphur Dioxide', 'location-weather' ),
				'symbol' => 'SO2',
			),
			array(
				'key'    => 'no2',
				'name'   => __( 'Nitrogen Dioxide', 'location-weather' ),
				'symbol' => 'NO2',
			),
			array(
				'key'    => 'o3',
				'name'   => __( 'Ozone', 'location-weather' ),
				'symbol' => 'O3',
			),
			array(
				'key'    => 'co',
				'name'   => __( 'Carbon Monoxide', 'location-weather' ),
				'symbol' => 'CO',
			),
		);
	}

	/**
	 * Get Pollution data object
	 *
	 * @param  mixed $value selected polluted value.
	 * @param  mixed $pollutant selected pollutant type.
	 * @return array
	 */
	public static function get_pollutant_data( $value, $pollutant ) {
		$breakpoints      = self::get_pollutant_breakpoints();
		$reports          = self::get_aqi_reports();
		$detailed_reports = self::get_aqi_reports_detailed();

		$colors = array(
			'Good'      => '#00B150',
			'Moderate'  => '#EEC631',
			'Poor'      => '#EA8B34',
			'Unhealthy' => '#E95378',
			'Severe'    => '#B33FB9',
			'Hazardous' => '#C91F33',
		);

		$iaqi     = null;
		$segments = array();

		if ( isset( $breakpoints[ $pollutant ] ) ) {
			foreach ( $breakpoints[ $pollutant ] as $bp ) {
				if ( $value >= $bp['cLow'] && $value <= $bp['cHigh'] ) {
					$iaqi = round(
						( ( $bp['iHigh'] - $bp['iLow'] ) / ( $bp['cHigh'] - $bp['cLow'] ) ) *
							( $value - $bp['cLow'] ) + $bp['iLow']
					);
					break;
				}
			}

			// Build scale segments.
			foreach ( $breakpoints[ $pollutant ] as $bp ) {
				$test_value = is_infinite( $bp['cHigh'] ) ? $bp['cLow'] + 1 : $bp['cHigh'];
				$iaqi_test  = null;

				if ( $test_value >= $bp['cLow'] && $test_value <= $bp['cHigh'] ) {
					$iaqi_test = round(
						( ( $bp['iHigh'] - $bp['iLow'] ) / ( $bp['cHigh'] - $bp['cLow'] ) ) *
							( $test_value - $bp['cLow'] ) + $bp['iLow']
					);
				}

				$condition  = self::get_aqi_condition( $iaqi_test );
				$segments[] = array(
					'cLow'      => $bp['cLow'],
					'cHigh'     => $bp['cHigh'],
					'iHigh'     => $bp['iHigh'],
					'condition' => $condition,
					'color'     => $colors[ $condition ],
				);
			}
		}

		$condition              = self::get_aqi_condition( $iaqi );
		$condition_translations = array(
			'Good'      => __( 'Good', 'location-weather' ),
			'Moderate'  => __( 'Moderate', 'location-weather' ),
			'Poor'      => __( 'Poor', 'location-weather' ),
			'Unhealthy' => __( 'Unhealthy', 'location-weather' ),
			'Severe'    => __( 'Severe', 'location-weather' ),
			'Hazardous' => __( 'Hazardous', 'location-weather' ),
			'Unknown'   => __( 'Unknown', 'location-weather' ),
		);

		return array(
			'pollutant'       => $pollutant,
			'iaqi'            => $iaqi,
			'condition'       => $condition,
			'condition_label' => $condition_translations[ $condition ],
			'color'           => $colors[ $condition ],
			'report'          => $reports[ $condition ],
			'detailed_report' => $detailed_reports[ $condition ],
			'scaleSegments'   => $segments,
		);
	}

	/**
	 * Get AQI Condition based on AQI value.
	 *
	 * @param  mixed $iaqi calculate AQI value.
	 * @return string
	 */
	public static function get_aqi_condition( $iaqi ) {
		$ranges = array(
			50  => 'Good',
			100 => 'Moderate',
			150 => 'Poor',
			200 => 'Unhealthy',
			250 => 'Severe',
		);
		foreach ( $ranges as $max => $label ) {
			if ( $iaqi <= $max ) {
				return $label;
			}
		}
		return 'Hazardous';
	}

	/**
	 * Render Air Pollutant Gauge.
	 *
	 * @param  mixed $args pollutant args.
	 * @return void
	 */
	public static function render_aqi_pollutant_gauge( $args = array() ) {
		$defaults = array(
			'value'            => null,
			'pollutant'        => 'pm2_5',
			'segment'          => 'iHigh',
			'size'             => 300,
			'strokeWidth'      => 40,
			'labelPadding'     => 20,
			'isAccordion'      => false,
			'extra'            => false,
			'aqiText'          => '',
			'conditionLabels'  => true,
			'showScaleValues'  => true,
			'pointerBaseWidth' => 12,
		);
		$args     = wp_parse_args( $args, $defaults );

		$raw_value          = $args['value'];
		$pollutant          = $args['pollutant'];
		$segment            = $args['segment'];
		$size               = $args['size'];
		$stroke_width       = $args['strokeWidth'];
		$label_padding      = $args['labelPadding'];
		$is_accordion       = $args['isAccordion'];
		$extra              = $args['extra'];
		$aqi_text           = $args['aqiText'];
		$condition_labels   = $args['conditionLabels'];
		$show_scale_values  = $args['showScaleValues'];
		$pointer_base_width = $args['pointerBaseWidth'];

		// Replace this with your actual data fetcher.
		$data = self::get_pollutant_data( $raw_value, $pollutant );

		$effective_value = ( 'iHigh' === $segment ) ? $data['iaqi'] : $raw_value;

		// maxValue.
		if ( empty( $data['scaleSegments'] ) ) {
			$max_value = 300;
		} else {
			$last      = $data['scaleSegments'][ count( $data['scaleSegments'] ) - 1 ][ $segment ];
			$max_value = ( is_infinite( $last ) || null === $last ) ? 300 : $last;
		}

		$gauge_start_angle = -180;
		$gauge_end_angle   = 0;

		// pointer angle.
		if ( ! is_numeric( $effective_value ) || ! $max_value ) {
			$pointer_angle = $gauge_start_angle;
		} else {
			$clamped       = min( max( $effective_value, 0 ), $max_value );
			$pointer_angle = $gauge_start_angle + ( $clamped / $max_value ) * ( $gauge_end_angle - $gauge_start_angle );
		}

		$radius = ( $size - $stroke_width ) / 2;
		$cx     = $size / 2 + $label_padding;
		$cy     = $size / 2 + $stroke_width / 2;

		if ( empty( $data['scaleSegments'] ) ) {
			echo '<div>No scale data available</div>';
			return;
		}

		// helper.
		$polar_to_cartesian = function ( $angle_deg, $r ) use ( $cx, $cy ) {
			$angle_rad = ( M_PI / 180 ) * $angle_deg;
			return array(
				'x' => $cx + $r * cos( $angle_rad ),
				'y' => $cy + $r * sin( $angle_rad ),
			);
		};

		// Arc segments.
		$prev_angle  = $gauge_start_angle;
		$arcs        = '';
		$gradient_id = 'gaugeGradient-' . sanitize_title( $aqi_text . $pollutant . uniqid() );
		// If AQI Gradient mode is enabled → single gradient arc.
		if ( ! empty( $aqi_text ) ) {
			$arcs .= '
					<defs>
						<linearGradient id="' . $gradient_id . '" gradientUnits="userSpaceOnUse" x1="0%" y1="0%" x2="100%" y2="0%">
							<stop offset="0%" stop-color="#00B150" />
							<stop offset="20%" stop-color="#EEC631" />
							<stop offset="40%" stop-color="#EA8B34" />
							<stop offset="60%" stop-color="#E95378" />
							<stop offset="80%" stop-color="#B33FB9" />
							<stop offset="100%" stop-color="#C91F33" />
						</linearGradient>
					</defs>
				';

			$start = $polar_to_cartesian( $gauge_start_angle, $radius );
			$end   = $polar_to_cartesian( $gauge_end_angle, $radius );

			$d = sprintf(
				'M %f %f A %f %f 0 1 1 %f %f',
				$start['x'],
				$start['y'],
				$radius,
				$radius,
				$end['x'],
				$end['y']
			);

			$arcs .= sprintf(
				'<path d="%s" stroke="url(#' . $gradient_id . ')" stroke-width="%d" stroke-linecap="round" fill="none"></path>',
				esc_attr( $d ),
				$stroke_width
			);
		} else {
			// Otherwise segmented arcs with labels.
			foreach ( $data['scaleSegments'] as $seg ) {
				$seg_max   = ( is_infinite( $seg[ $segment ] ) || null === $seg[ $segment ] ) ? $max_value : $seg[ $segment ];
				$end_angle = $gauge_start_angle + ( $seg_max / $max_value ) * ( $gauge_end_angle - $gauge_start_angle );

				$start          = $polar_to_cartesian( $prev_angle, $radius );
				$end            = $polar_to_cartesian( $end_angle, $radius );
				$large_arc_flag = ( $end_angle - $prev_angle > 180 ) ? 1 : 0;

				$path_id = 'arc-' . sanitize_title( $seg['condition'] ) . '-' . wp_unique_id();

				$d = sprintf(
					'M %f %f A %f %f 0 %d 1 %f %f',
					$start['x'],
					$start['y'],
					$radius,
					$radius,
					$large_arc_flag,
					$end['x'],
					$end['y']
				);

				$arcs .= '<g>';
				$arcs .= sprintf(
					'<path id="%s" d="%s" stroke="%s" stroke-width="%d" fill="none"></path>',
					esc_attr( $path_id ),
					esc_attr( $d ),
					esc_attr( $seg['color'] ),
					$stroke_width
				);

				if ( $condition_labels ) {
					$arcs .= sprintf(
						'<text fill="#fff" font-size="%f" font-weight="bold" text-anchor="middle" dominant-baseline="middle">
					<textPath href="#%s" startOffset="50%%">%s</textPath>
				</text>',
						$size * 0.03,
						esc_attr( $path_id ),
						esc_html( $seg['condition'] )
					);
				}
				$arcs .= '</g>';

				$prev_angle = $end_angle;
			}
		}
		// Pointer color.
		$pointer_color = '#000';
		foreach ( $data['scaleSegments'] as $seg ) {
			$seg_max = $seg[ $segment ] ?? INF;
			if ( $effective_value <= $seg_max ) {
				$pointer_color = $seg['color'];
				break;
			}
		}

		$pointer_length = $radius - $stroke_width / 2 - 10;
		$tip            = $polar_to_cartesian( $pointer_angle, $pointer_length );
		$left_base      = $polar_to_cartesian( $pointer_angle - 90, $pointer_base_width / 2 );
		$right_base     = $polar_to_cartesian( $pointer_angle + 90, $pointer_base_width / 2 );
		$pointer_end    = $polar_to_cartesian( $pointer_angle, $radius );

		// Scale arc.
		$scale_arc_radius = $radius + 25;
		$scale_path_id    = 'scale-arc-' . wp_unique_id();
		$scale_pathd      = sprintf(
			'M %f %f A %f %f 0 0 1 %f %f',
			$polar_to_cartesian( $gauge_start_angle, $scale_arc_radius )['x'],
			$polar_to_cartesian( $gauge_start_angle, $scale_arc_radius )['y'],
			$scale_arc_radius,
			$scale_arc_radius,
			$polar_to_cartesian( $gauge_end_angle, $scale_arc_radius )['x'],
			$polar_to_cartesian( $gauge_end_angle, $scale_arc_radius )['y']
		);

		$scale_values = array( 0 );
		foreach ( $data['scaleSegments'] as $seg ) {
			$seg_max        = ( is_infinite( $seg[ $segment ] ) || null === $seg[ $segment ] ) ? $max_value : $seg[ $segment ];
			$scale_values[] = $seg_max;
		}

		?>
		<svg
			width="<?php echo esc_attr( $size + $label_padding * 2 ); ?>"
			height="<?php echo esc_attr( $size / 1.583 ); ?>"
			viewBox="0 0 <?php echo esc_attr( $size + $label_padding * 2 ); ?> <?php echo esc_attr( $size / 1.583 ); ?>">
			<?php
			echo $arcs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
			?>

			<?php if ( ! $is_accordion ) : ?>
				<path id="<?php echo esc_attr( $scale_path_id ); ?>" d="<?php echo esc_attr( $scale_pathd ); ?>" fill="none"></path>

				<?php
				if ( $show_scale_values ) :
					foreach ( $scale_values as $idx => $val ) :
						$display = ( 0 === $idx ) ? '0' : ( count( $scale_values ) - 1 === $idx ? $val . '+' : $val );
						$offset  = ( count( $scale_values ) - 1 === $idx ) ? '95%' : ( $val / $max_value ) * 100 . '%';
						?>
						<text fill="#333" font-size="<?php echo esc_attr( $size * 0.04 ); ?>" font-weight="bold">
							<textPath href="#<?php echo esc_attr( $scale_path_id ); ?>" startOffset="<?php echo esc_attr( $offset ); ?>" text-anchor="middle" dominant-baseline="middle">
								<?php echo esc_html( $display ); ?>
							</textPath>
						</text>
					<?php endforeach; ?>
				<?php endif; ?>

				<path d="M <?php echo esc_attr( $left_base['x'] ); ?> <?php echo esc_attr( $left_base['y'] ); ?> L <?php echo esc_attr( $tip['x'] ); ?> <?php echo esc_attr( $tip['y'] ); ?> L <?php echo esc_attr( $right_base['x'] ); ?> <?php echo esc_attr( $right_base['y'] ); ?> Z" fill="<?php echo esc_attr( $pointer_color ); ?>" />

				<circle cx="<?php echo esc_attr( $cx ); ?>" cy="<?php echo esc_attr( $cy ); ?>" r="<?php echo esc_attr( $pointer_base_width / 2 ); ?>" fill="#fff" stroke="<?php echo esc_attr( $pointer_color ); ?>" stroke-width="2" />
			<?php endif; ?>

			<?php if ( $is_accordion ) : ?>
				<circle cx="<?php echo esc_attr( $pointer_end['x'] ); ?>" cy="<?php echo esc_attr( $pointer_end['y'] ); ?>" r="5" fill="#fff" stroke="<?php echo esc_attr( $pointer_color ); ?>" stroke-width="2" />
			<?php endif; ?>

			<?php if ( $is_accordion || $extra ) : ?>
				<?php if ( ! empty( $aqi_text ) && 'no' !== $aqi_text ) : ?>
					<text
						x="50%"
						y="<?php echo esc_attr( $extra ? $cy - 30 : $cy - 30 ); ?>"
						text-anchor="middle"
						font-size="<?php echo esc_attr( $size * 0.1 ); ?>"
						font-weight="bold"
						fill="#000"
						class="spl-progress-text">
						<?php echo esc_html( $aqi_text ); ?>
					</text>
				<?php endif; ?>
				<text x="50%" y="<?php echo esc_attr( $extra ? $cy - 30 : $cy ); ?>" text-anchor="middle" font-size="<?php echo esc_attr( $size * 0.2 ); ?>" font-weight="bold" fill="<?php echo esc_attr( $pointer_color ); ?>">
					<?php echo esc_html( $effective_value ); ?>
				</text>
			<?php endif; ?>
		</svg>
		<?php
	}

	/**
	 * Convert digits in a string to subscript Unicode characters.
	 *
	 * @param string $symbol symbol string containing digits.
	 * @return string
	 */
	public static function to_subscript( $symbol ) {
		$sub_map = array(
			'0' => '₀',
			'1' => '₁',
			'2' => '₂',
			'3' => '₃',
			'4' => '₄',
			'5' => '₅',
			'6' => '₆',
			'7' => '₇',
			'8' => '₈',
			'9' => '₉',
		);
		return preg_replace_callback(
			'/\d/',
			function ( $matches ) use ( $sub_map ) {
				return $sub_map[ $matches[0] ];
			},
			$symbol
		);
	}

	/**
	 * Get formatted label for pollutant name and symbol with translation.
	 *
	 * @param string $name   Pollutant name.
	 * @param string $symbol Pollutant symbol.
	 * @param string $format Label format: 'abbreviation', 'name', or 'both'.
	 * @param string $style  Symbol style: 'subscript' or ''.
	 * @return string
	 */
	public static function get_label( $name, $symbol, $format = 'name', $style = '' ) {
		$translated_name = $name;
		$final_symbol    = ( 'subscript' === $style ) ? self::to_subscript( $symbol ) : $symbol;

		switch ( $format ) {
			case 'abbreviation':
				return $final_symbol;
			case 'name':
				return $translated_name;
			case 'both':
				return sprintf( '%s (%s)', $translated_name, $final_symbol );
			default:
				return $translated_name;
		}
	}
}
