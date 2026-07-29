<?php
/**
 * Utils pour le bloc Temp Hour.
 *
 * @since 2.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * next_eac_get_weather_label
 *
 * @param int $code
 * @param string $lang La langue de la ville/région. 'en' par défaut
 *
 * @return string
 */
function eac_get_weather_label( int $code ): string {
	$label_code = require __DIR__ . '/code-meteo.php';
	$label = isset( $label_code[ $code ] ) ? $label_code[ $code ] : '--';
	return $label;
}

/**
 * eac_get_weather_image
 * Mapper les codes météo avec le nom des fichiers svg
 *
 * @param int $code
 *
 * @return string
 */
function eac_get_weather_image( int $code ): string {
	$category_map = array(
		'soleil'           => array( 0, 1 ),
		'soleil-nuages'    => array( 2, 3, 45, 48 ),
		'nuages'           => array(),
		'pluie-faible'     => array( 51, 53, 55, 61, 80 ),
		'pluie-moyenne'    => array( 63, 81 ),
		'pluie-forte'      => array( 65, 82 ),
		'neige-faible'     => array( 71, 77, 85 ),
		'neige-forte'      => array( 73, 75, 86 ),
		'neige-pluie'      => array( 56, 57, 66, 67 ),
		'orage'            => array( 95, 96, 99 ),
		'lune'             => array( 999 ),
	);

	foreach ( $category_map as $label => $codes ) {
		if ( in_array( $code, $codes, true ) ) {
			return $label;
		}
	}
	return 'nuages'; // Fallback
}

/**
 * sanitize_geocode_address
 *
 * @param array $addr
 *
 * @return array
 */
function sanitize_geocode_address( array $addr ): array {
	$out = array();
	foreach ( $addr as $k => $v ) {
		$key = sanitize_key( $k );
		$out[ $key ] = is_string( $v ) ? sanitize_text_field( $v ) : $v;
	}
	return $out;
}

/**
 * eac_extract_goecode_data
 * Géocode une ville/région/pays via Nominatim (OpenStreetMap) en utilisant countrycodes.
 *
 * @param string $city    Nom de la ville.
 * @param string $country Code pays ISO alpha-2 (optionnel, ex: 'FR').
 *
 * @return array|\WP_Error ['lat'=>float,'lon'=>float,'display_name'=>string,'address'=>array] ou WP_Error.
 */
function eac_extract_goecode_data( string $city, string $country = '' ) {
	$key = sprintf( 'eac_meteo_nomi_%s_%s', sanitize_title( $city ), sanitize_title( $country ) );
	$cached = get_transient( $key );

	if ( false !== $cached ) {
		return $cached;
	}

	$query = trim( $city );
	$args  = array(
		'q'              => rawurlencode( $query ),
		'format'         => 'json',
		'addressdetails' => '1',
		'limit'          => '1',
	);

	if ( '' !== $country ) {
		$args['countrycodes'] = rawurlencode( strtolower( $country ) );
	}

	$url = add_query_arg( $args, 'https://nominatim.openstreetmap.org/search' );

	$user_agent = sprintf(
		'%s/%s (+%s) %s',
		get_bloginfo( 'name' ),
		get_bloginfo( 'version' ),
		get_bloginfo( 'url' ),
		get_option( 'admin_email' )
	);

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 10,
			'headers' => array(
				'User-Agent'      => $user_agent,
				'From'            => get_option( 'admin_email' ),
				'Accept-Language' => get_locale(),
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'request_failed', $response->get_error_message() );
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = wp_remote_retrieve_body( $response );

	if ( 403 === $code ) {
		return new WP_Error( 'forbidden', esc_html__( '403 Forbidden from Nominatim.', 'eac-components' ) );
	}

	if ( 200 !== $code ) {
		return new WP_Error( 'bad_response', 'HTTP: ' . $code );
	}

	$data = json_decode( $body, true );

	if ( json_last_error() !== JSON_ERROR_NONE ) {
		return new WP_Error( 'json_error', json_last_error_msg() );
	}

	if ( empty( $data ) || ! isset( $data[0]['lat'], $data[0]['lon'] ) ) {
		return new WP_Error( 'no_result', esc_html__( 'Geocoding coordinates not found.', 'eac-components' ) );
	}

	$lat = filter_var( $data[0]['lat'], FILTER_VALIDATE_FLOAT );
	$lon = filter_var( $data[0]['lon'], FILTER_VALIDATE_FLOAT );
	if ( false === $lat || false === $lon ) {
		return new WP_Error( 'no_result', esc_html__( 'Geocoding coordinates not found.', 'eac-components' ) );
	}

	$result = array(
		'lat'          => (float) $lat,
		'lon'          => (float) $lon,
		'display_name' => isset( $data[0]['display_name'] ) ? sanitize_text_field( $data[0]['display_name'] ) : '',
		'address'      => isset( $data[0]['address'] ) && is_array( $data[0]['address'] ) ? sanitize_geocode_address( $data[0]['address'] ) : array(),
	);

	// Cache 30 jours
	set_transient( $key, $result, 30 * DAY_IN_SECONDS );

	return $result;
}

/**
 * eac_extract_meteo_data
 * Récupère températures horaires + humidité via Open-Meteo (timezone auto).
 *
 * @param float $lat
 * @param float $lon
 * @param string $city
 * @param string $country
 *
 * @return array|\WP_Error
 */
function eac_extract_meteo_data( float $lat, float $lon, string $city, string $country ) {
	$days = 7;

	// Utiliser timezone WP pour construire les dates de départ (mais on demandera timezone=auto à l'API)
	$tz    = wp_timezone();
	$now   = new DateTimeImmutable( 'now', $tz );
	$start = $now->format( 'Y-m-d' );
	$end_dt = $now->add( new DateInterval( 'P' . ( $days - 1 ) . 'D' ) );
	$end   = $end_dt->format( 'Y-m-d' );
	$transient_key = sprintf( 'eac_meteo_temp_%s_%s_%d', sanitize_title( $city ), sanitize_title( $country ), $days );

	$cached = get_transient( $transient_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$hourly_fields  = array( 'temperature_2m', 'relative_humidity_2m', 'precipitation_probability', 'weather_code', 'rain', 'wind_speed_10m', 'wind_direction_10m', 'wind_gusts_10m', 'cloud_cover', 'surface_pressure', 'apparent_temperature' );
	$daily_fields   = array( 'weather_code', 'temperature_2m_min', 'temperature_2m_max', 'sunrise', 'sunset', 'precipitation_sum', 'uv_index_max', 'windspeed_10m_max', 'windgusts_10m_max', 'winddirection_10m_dominant' );
	$current_fields = array( 'is_day' );
	$url = add_query_arg(
		array(
			'latitude'   => rawurlencode( (string) $lat ),
			'longitude'  => rawurlencode( (string) $lon ),
			'hourly'     => implode( ',', $hourly_fields ),
			'daily'      => implode( ',', $daily_fields ),
			'current'    => implode( ',', $current_fields ),
			'start_date' => $start,
			'end_date'   => $end,
			'timezone'   => rawurlencode( 'auto' ),
		),
		'https://api.open-meteo.com/v1/forecast'
	);

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 10,
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'request_failed', $response->get_error_message() );
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = wp_remote_retrieve_body( $response );

	if ( 200 !== $code ) {
		return new WP_Error( 'bad_response', 'HTTP: ' . $code );
	}

	$data = json_decode( $body, true );

	if ( json_last_error() !== JSON_ERROR_NONE ) {
		return new WP_Error( 'json_error', json_last_error_msg() );
	}

	if ( empty( $data['hourly']['time'] ) || empty( $data['hourly']['temperature_2m'] ) ) {
		return new WP_Error( 'no_data', esc_html__( 'No hourly data available.', 'eac-components' ) );
	}

	// Nettoyage des données
	array_walk_recursive( $data, function ( &$value ) {
		if ( is_string( $value ) ) {
			$value = sanitize_text_field( $value ); // Seulement les chaînes
		}
	} );

	// Cache 30 minutes
	set_transient( $transient_key, $data, 30 * MINUTE_IN_SECONDS );

	return $data;
}

/**
 * eac_find_closest_time_index
 * Recherche l'index le plus proche de la date dans la table des times
 *
 * @param DateTimeImmutable $target_date
 * @param array $times_array
 *
 * @return int
 */
function eac_find_closest_time_index( DateTimeImmutable $target_date, array $times_array ): int {
	$target_timezone = $target_date->getTimezone();
	$minutes = (int) $target_date->format( 'i' );

	// Avant 30 min : utiliser l'heure courante
	// À partir de 30 min : utiliser l'heure suivante
	if ( $minutes < 30 ) {
		$target_hour = (int) $target_date->format( 'H' );
		$target_day = $target_date->format( 'Y-m-d' );
	} else {
		// Passer à l'heure suivante
		$next_hour = $target_date->add( new DateInterval( 'PT1H' ) );
		$target_hour = (int) $next_hour->format( 'H' );
		$target_day = $next_hour->format( 'Y-m-d' );
	}

	// Chercher l'index correspondant dans le tableau
	foreach ( $times_array as $index => $time_str ) {
		$date_time = new DateTimeImmutable( $time_str, $target_timezone );
		if ( $date_time->format( 'Y-m-d' ) === $target_day && (int) $date_time->format( 'H' ) === $target_hour ) {
			return $index;
		}
	}

	return 0; // Pas trouvé
}

/**
 * eac_get_meteo_by_hour
 * Sélectionne les entrées horaires pour ne garder que celles dans la fenêtre ±6h autour de l'heure locale de la ville.
 *
 * @param array        $times Array of ISO time strings (UTC).
 * @param array        $values Corresponding temperature values.
 * @param DateTimeZone $city_tz Timezone of the city.
 * @param int          $hours Window hours (default 6).
 *
 * @return array [['time'=>DateTimeImmutable, 'value'=>...], ...]
 */
function eac_get_meteo_by_hour( array $data, DateTimeZone $city_tz, string $date_format, string $time_format, int $hours = 6, string $temp_unit = 'celsius' ): array {
	$hourly  = $data['hourly'];
	$times   = $hourly['time'];
	$temp    = $hourly['temperature_2m'];
	$daily   = $data['daily'];
	$current = $data['current'];

	$result  = array();
	$current_data = array();

	// Heure locale "maintenant" dans le fuseau horaire de la ville.
	try {
		$now_city = new DateTimeImmutable( 'now', $city_tz );
	} catch ( Exception $e ) {
		return $result;
	}

	$start = $now_city->sub( new DateInterval( 'PT' . ( $hours ) . 'H' ) );
	$end   = $now_city->add( new DateInterval( 'PT' . ( $hours ) . 'H' ) );

	if ( is_array( $hourly ) && ! empty( $hourly ) ) {
		$time_index = eac_find_closest_time_index( $now_city, $times );
		$local_time = $now_city->format( $date_format . ' ' . $time_format );
		$day_date   = $now_city->format( $date_format );
		$day_hour   = $now_city->format( $time_format );

		$precipitation_chance = isset( $hourly['precipitation_probability'] ) ? $hourly['precipitation_probability'][ $time_index ] . $data['hourly_units']['precipitation_probability'] : '--';
		$temperature          = isset( $hourly['temperature_2m'][ $time_index ] ) ? $hourly['temperature_2m'][ $time_index ] : '--';
		$apparent_temp        = isset( $hourly['apparent_temperature'][ $time_index ] ) ? $hourly['apparent_temperature'][ $time_index ] : '--';
		$humidity             = isset( $hourly['relative_humidity_2m'] ) ? $hourly['relative_humidity_2m'][ $time_index ] . $data['hourly_units']['relative_humidity_2m'] : '--';
		$weather_code         = isset( $hourly['weather_code'][ $time_index ] ) ? $hourly['weather_code'][ $time_index ] : '--';
		$rain                 = isset( $hourly['rain'] ) ? $hourly['rain'][ $time_index ] . $data['hourly_units']['rain'] : '--';
		$wind_speed           = isset( $hourly['wind_speed_10m'] ) ? sprintf( '%s %s', $hourly['wind_speed_10m'][ $time_index ], $data['hourly_units']['wind_speed_10m'] ) : '--';
		$wind_dir             = isset( $hourly['wind_direction_10m'] ) ? $hourly['wind_direction_10m'][ $time_index ] . $data['hourly_units']['wind_direction_10m'] : '--';
		$gust                 = isset( $hourly['wind_gusts_10m'] ) ? sprintf( '%s %s', $hourly['wind_gusts_10m'][ $time_index ], $data['hourly_units']['wind_gusts_10m'] ) : '--';
		$cloud                = isset( $hourly['cloud_cover'] ) ? $hourly['cloud_cover'][ $time_index ] . $data['hourly_units']['cloud_cover'] : '--';
		$pressure             = isset( $hourly['surface_pressure'] ) ? sprintf( '%d mbar', $hourly['surface_pressure'][ $time_index ], $data['hourly_units']['surface_pressure'] ) : '--';
		$temperature_2m_min   = '--';
		$temperature_2m_max   = '--';
		$sunrise_f            = '--';
		$sunset_f             = '--';
		$uv                   = '--';

		if ( is_array( $daily ) && ! empty( $daily ) ) {
			$temperature_2m_min   = isset( $daily['temperature_2m_min'][0] ) ? $daily['temperature_2m_min'][0] : '--';
			$temperature_2m_max   = isset( $daily['temperature_2m_max'][0] ) ? $daily['temperature_2m_max'][0] : '--';
			$sunrise              = is_array( $daily['sunrise'] ) && isset( $daily['sunrise'][0] ) ? new DateTimeImmutable( $daily['sunrise'][0], $city_tz ) : '--';
			$sunset               = is_array( $daily['sunset'] ) && isset( $daily['sunset'][0] ) ? new DateTimeImmutable( $daily['sunset'][0], $city_tz ) : '--';
			$sunrise_f            = $sunrise instanceof DateTimeImmutable ? $sunrise->format( $time_format ) : $sunrise;
			$sunset_f             = $sunset instanceof DateTimeImmutable ? $sunset->format( $time_format ) : $sunset;
			$uv                   = isset( $daily['uv_index_max'][0] ) ? number_format( $daily['uv_index_max'][0], 1 ) : '--';
		}

		$current_data = array(
			'local_time'           => $local_time,
			'day_date'             => $day_date,
			'day_hour'             => $day_hour,
			'temperature_2m'       => '--' !== $temperature && 'fahrenheit' === $temp_unit ? eac_c_to_f( $temperature ) . '°F' : $temperature . '°C',
			'apparent_temperature' => '--' !== $apparent_temp && 'fahrenheit' === $temp_unit ? eac_c_to_f( $apparent_temp ) . '°F' : $apparent_temp . '°C',
			'relative_humidity_2m' => $humidity,
			'is_day'               => $current['is_day'] ?? true,
			'weather_code'         => $weather_code,
			'wind_speed_10m'       => $wind_speed,
			'wind_direction_10m'   => $wind_dir,
			'wind_direction_ico'   => ( (int) $wind_dir + 180 ) % 360,
			'wind_gusts_10m'       => $gust,
			'precipitation'        => $rain,
			'cloud_cover'          => $cloud,
			'rain'                 => $rain,
			'pressure_msl'         => $pressure,
			'precipitation_prob'   => $precipitation_chance,
			'temperature_2m_min'   => '--' !== $temperature_2m_min && 'fahrenheit' === $temp_unit ? eac_c_to_f( $temperature_2m_min ) . '°F' : $temperature_2m_min . '°C',
			'temperature_2m_max'   => '--' !== $temperature_2m_max && 'fahrenheit' === $temp_unit ? eac_c_to_f( $temperature_2m_max ) . '°F' : $temperature_2m_max . '°C',
			'sunrise'              => $sunrise_f,
			'sunset'               => $sunset_f,
			'uv_index_max'         => $uv,
		);
	}
	$result['current'][] = $current_data;

	foreach ( $times as $index => $date_iso ) {
		try {
			$dt_city = new DateTimeImmutable( $date_iso, new DateTimeZone( $city_tz->getName() ) );

			if ( $dt_city >= $start && $dt_city <= $end ) {
				$value = 'fahrenheit' === $temp_unit ? eac_c_to_f( $temp[ $index ] ) : round( (float) $temp[ $index ], 1 );
				$result['hourly'][] = array(
					'time'  => $dt_city,
					'value' => $value,
				);
			}
		} catch ( Exception $e ) {
			// Invalid date string from API — skip this entry.
			continue;
		}
	}

	return $result;
}

function format_date_for_nominatim_location( $date, $country_code, $timezone = 'UTC' ) {
	// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.ArrayItemNoNewLine
	$locale_map = array(
		// Europe
		'es' => 'es_ES', 'fr' => 'fr_FR', 'de' => 'de_DE', 'it' => 'it_IT',
		'gb' => 'en_GB', 'pt' => 'pt_PT', 'pl' => 'pl_PL', 'nl' => 'nl_NL',
		'be' => 'nl_BE', 'ch' => 'de_CH', 'at' => 'de_AT', 'cz' => 'cs_CZ',
		'se' => 'sv_SE', 'no' => 'nb_NO', 'dk' => 'da_DK', 'gr' => 'el_GR',
		'ru' => 'ru_RU', 'ua' => 'uk_UA', 'hu' => 'hu_HU', 'ro' => 'ro_RO',
		// Amérique
		'us' => 'en_US', 'ca' => 'en_CA', 'mx' => 'es_MX', 'br' => 'pt_BR',
		'ar' => 'es_AR', 'cl' => 'es_CL', 'co' => 'es_CO', 'pe' => 'es_PE',
		// Asie
		'jp' => 'ja_JP', 'cn' => 'zh_CN', 'in' => 'hi_IN', 'kr' => 'ko_KR',
		'th' => 'th_TH', 'vn' => 'vi_VN', 'id' => 'id_ID', 'ph' => 'fil_PH',
		// Océanie & Afrique
		'au' => 'en_AU', 'nz' => 'en_NZ', 'za' => 'en_ZA', 'eg' => 'ar_EG',
		// Moyen-Orient
		'ae' => 'ar_AE', 'sa' => 'ar_SA', 'il' => 'he_IL',
	);
	// phpcs:enable WordPress.Arrays.ArrayDeclarationSpacing.ArrayItemNoNewLine

	$country_code = strtolower( trim( $country_code ?? '' ) );
	$locale = $locale_map[ $country_code ] ?? 'en_US'; // Fallback sûr

	$date_obj = new DateTime( $date, new DateTimeZone( $timezone ) );

	$formatter = new IntlDateFormatter(
		$locale,
		IntlDateFormatter::LONG,
		IntlDateFormatter::NONE
	);
	// Pattern personnalisé sans virgule et point pour éviter les problèmes d'affichage dans certains pays (ex: "22 may 2026" au lieu de "May 22, 2026" ou "22.05.2026")
	$formatter->setPattern( 'dd MMM yyyy' );  // "22 may 2026"
	/**
	$formatter->setPattern('dd/MM/yyyy');   // "22/05/2026"
	$formatter->setPattern('dd-MM-yyyy');   // "22-05-2026"
	$formatter->setPattern('yyyy-MM-dd');   // "2026-05-22" (ISO)
	$formatter->setPattern('MM/dd/yyyy');   // "05/22/2026" (format US)
	$formatter->setPattern('dd.MM.yyyy');   // "22.05.2026"
	 */
	return $formatter->format( $date_obj );
}

/**
 * eac_get_meteo_by_day
 *
 * @param array $data
 * @param string $date_format
 * @param string $temp_unit
 *
 * @return array
 */
function eac_get_meteo_by_day( array $data, string $date_format, string $temp_unit = 'celsius', string $country_code = 'FR' ): array { // phpcs:ignore
	$daily_times       = isset( $data['daily']['time'] ) ? $data['daily']['time'] : array();
	$daily_temps_min   = isset( $data['daily']['temperature_2m_min'] ) ? $data['daily']['temperature_2m_min'] : array();
	$daily_temps_max   = isset( $data['daily']['temperature_2m_max'] ) ? $data['daily']['temperature_2m_max'] : array();
	$hourly_temps      = isset( $data['hourly']['temperature_2m'] ) ? $data['hourly']['temperature_2m'] : array();
	$hourly_humidities = isset( $data['hourly']['relative_humidity_2m'] ) ? $data['hourly']['relative_humidity_2m'] : array();
	$hourly_times      = isset( $data['hourly']['time'] ) ? $data['hourly']['time'] : array();
	$timezone_name     = isset( $data['timezone'] ) && '' !== $data['timezone'] ? $data['timezone'] : '';
	$rows              = array();

	foreach ( $daily_times as $index => $date_iso ) {
		try {
			// Créer d'abord en UTC (les dates API sont en UTC)
			$dt = new DateTimeImmutable( $date_iso, new DateTimeZone( '' !== $timezone_name ? $timezone_name : 'UTC' ) );
			/**$date_intl = format_date_for_nominatim_location( $date_iso, $country_code, $timezone_name );*/
			$date = $dt->format( $date_format );
		} catch ( Exception $e ) {
			continue;
		}

		// Récupérer min et max des données quotidiennes
		if ( ! isset( $daily_temps_min[ $index ] ) || ! isset( $daily_temps_max[ $index ] ) ) {
			$rows[] = array(
				'date'     => $date,
				'avg'      => null,
				'min'      => null,
				'max'      => null,
				'humidity' => null,
			);
			continue;
		}

		$min_c = (float) $daily_temps_min[ $index ];
		$max_c = (float) $daily_temps_max[ $index ];

		// Convertir en Fahrenheit si nécessaire
		$min = 'fahrenheit' === $temp_unit ? eac_c_to_f( $min_c ) : round( $min_c, 1 );
		$max = 'fahrenheit' === $temp_unit ? eac_c_to_f( $max_c ) : round( $max_c, 1 );

		// Récupérer la moyenne réelle et l'humidité depuis les données horaires
		$daily_data = eac_get_daily_stats_from_hourly( $date_iso, $hourly_times, $hourly_temps, $hourly_humidities, $temp_unit );

		$rows[] = array(
			'date'     => $date,
			'avg'      => $daily_data['avg'],
			'min'      => $min,
			'max'      => $max,
			'humidity' => $daily_data['humidity'],
		);
	}

	return $rows;
}

/**
 * eac_get_daily_stats_from_hourly
 * Récupère la température moyenne et l'humidité moyenne pour un jour spécifique
 * depuis les données horaires
 *
 * @param string $date_iso
 * @param array $hourly_times
 * @param array $hourly_temps
 * @param array $hourly_humidities
 * @param string $temp_unit
 *
 * @return array
 */
function eac_get_daily_stats_from_hourly( string $date_iso, array $hourly_times, array $hourly_temps, array $hourly_humidities, string $temp_unit = 'celsius' ): array {
	$temps      = array();
	$humidities = array();

	foreach ( $hourly_times as $index => $time_iso ) {
		// Vérifier si l'heure appartient au jour cible
		if ( strpos( $time_iso, $date_iso ) === 0 ) {
			// Températures
			if ( isset( $hourly_temps[ $index ] ) && '' !== $hourly_temps[ $index ] ) {
				$temps[] = (float) $hourly_temps[ $index ];
			}

			// Humidités
			if ( isset( $hourly_humidities[ $index ] ) && '' !== $hourly_humidities[ $index ] ) {
				$humidities[] = (float) $hourly_humidities[ $index ];
			}
		}
	}

	$result = array(
		'avg'      => null,
		'humidity' => null,
	);

	// Calculer la moyenne des températures horaires
	if ( count( $temps ) > 0 ) {
		$avg_c = array_sum( $temps ) / count( $temps );
		$result['avg'] = 'fahrenheit' === $temp_unit ? eac_c_to_f( $avg_c ) : round( $avg_c, 1 );
	}

	// Calculer la moyenne de l'humidité
	if ( count( $humidities ) > 0 ) {
		$result['humidity'] = (int) round( array_sum( $humidities ) / count( $humidities ), 0 );
	}

	return $result;
}

/**
 * eac_extract_country_from_address
 * Tente d'extraire le code pays ISO alpha-2 depuis le tableau 'address' retourné par Nominatim.
 *
 * @param array $address
 *
 * @return string|null
 */
function eac_extract_country_from_address( array $address ) {
	if ( empty( $address ) || ! is_array( $address ) ) {
		return null;
	}

	if ( isset( $address['country_code'] ) ) {
		return strtoupper( $address['country_code'] );
	}

	return null;
}

/**
 * eac_c_to_f
 * Convertit Celsius en Fahrenheit.
 *
 * @param float|null $c
 *
 * @return float|null
 */
function eac_c_to_f( $c ) {
	if ( null === $c ) {
		return null;
	}
	return round( ( ( (float) $c * 9 ) / 5 ) + 32, 1 );
}
