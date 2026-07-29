<?php
/**
 * Plugin Name: EAC Blocks — Temp Hours (block)
 * Description: Gutenberg block "eac-blocks/meteo" remplaçant le shortcode [temp_meteo_city]. Utilise Nominatim + Open-Meteo via wp_remote_get.
 * Version:     2.5.1
 * Author:      Team EAC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\EAC_Plugin;

// Charger les fonctions helper.
require_once EAC_INCLUDES_PATH . 'blocks/lib-blocks.php';
require_once __DIR__ . '/assets/utils/utils.php';

add_action( 'init', 'eac_meteo_register_block' );
add_action( 'enqueue_block_editor_assets', 'eac_meteo_enqueue_editor_assets' );
add_action( 'enqueue_block_assets', 'eac_meteo_enqueue_assets' );
add_action( 'rest_api_init', 'eac_register_route_language', 30 );

/**
 * eac_meteo_register_block
 *
 * @return void
 */
function eac_meteo_register_block(): void {
	$block_js_handler     = 'eac-meteo-block';
	$frontend_css_handler = 'eac-block-meteo-frontend';
	$chart_js_handler     = 'block-chart';
	$label_js_handler     = 'block-chart-label';
	$eac_chart_js_handler = 'eac-block-chart';

	// Register external Chart.js
	wp_register_script(
		$chart_js_handler,
		'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js',
		array(),
		'3.9.1',
		true
	);

	// chartjs-plugin-datalabels (make it depend on Chart.js)
	wp_register_script(
		$label_js_handler,
		'https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js',
		array( $chart_js_handler ),
		'2.2.0',
		true
	);

	// Your chart handler (attaches the event listener and uses Chart.js)
	wp_register_script(
		$eac_chart_js_handler,
		EAC_Plugin::instance()->get_script_url( 'includes/blocks/meteo/assets/js/eac-block-chart' ),
		array( $chart_js_handler, $label_js_handler, 'wp-i18n' ),
		filemtime( EAC_Plugin::instance()->get_script_path( 'includes/blocks/meteo/assets/js/eac-block-chart' ) ),
		true
	);

	// Block editor script — depend on WP editor libs + your chart handler
	wp_register_script(
		$block_js_handler,
		EAC_Plugin::instance()->get_script_url( 'includes/blocks/meteo/assets/js/block' ),
		array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render', 'wp-data', 'wp-api-fetch', $eac_chart_js_handler ), // ensure chart handler loads before block.js in editor
		filemtime( EAC_Plugin::instance()->get_script_path( 'includes/blocks/meteo/assets/js/block' ) ),
		true
	);

	/** Chaine traduites avec LOCO translate */
	wp_set_script_translations( $block_js_handler, 'eac-components', EAC_PLUGIN_PATH . 'languages' );
	wp_set_script_translations( $eac_chart_js_handler, 'eac-components', EAC_PLUGIN_PATH . 'languages' );

	wp_add_inline_script(
		$block_js_handler,
		'window.eacLocale = ' . wp_json_encode(
			array(
				'locale' => get_user_locale(),
				'tz'     => wp_timezone_string(),
			)
		),
		'before'
	);

	wp_register_style(
		$frontend_css_handler,
		EAC_Plugin::instance()->get_style_url( 'includes/blocks/meteo/assets/css/frontend' ),
		array(),
		filemtime( EAC_Plugin::instance()->get_style_path( 'includes/blocks/meteo/assets/css/frontend' ) ),
	);

	// Register block from block.json and provide server-side render callback
	register_block_type_from_metadata( __DIR__, array(
		'render_callback' => 'eac_meteo_render_block',
	) );
}

/**
 * eac_meteo_enqueue_editor_assets
 *
 * @return void
 */
function eac_meteo_enqueue_editor_assets(): void {
	wp_enqueue_script( 'eac-meteo-block' );
	wp_enqueue_style( 'eac-block-meteo-frontend' );
}

/**
 * eac_meteo_enqueue_assets
 * Enqueue backend and frontend assets
 *
 * @return void
 */
function eac_meteo_enqueue_assets(): void {
	// Frontend: Les libs chart.js et chart label sont déclarés comme dépendances du script 'eac-block-chart', donc ils seront chargés automatiquement avant.
	wp_enqueue_script( 'eac-block-chart' );
	wp_enqueue_style( 'eac-block-meteo-frontend' );
}

/**
 * eac_meteo_render_block
 *
 * @param array $attributes
 *
 * @return string
 */
function eac_meteo_render_block( array $attributes ): string {
	$class_name    = isset( $attributes['className'] ) ? $attributes['className'] : '';
	$meteo_type    = isset( $attributes['meteoType'] ) ? $attributes['meteoType'] : 'day';
	$city          = isset( $attributes['city'] ) ? sanitize_text_field( $attributes['city'] ) : 'Paris';
	$language      = isset( $attributes['selectedLanguage'] ) ? strtoupper( $attributes['selectedLanguage'] ) : '';
	$chart_type    = isset( $attributes['chartType'] ) ? $attributes['chartType'] : 'line';
	$render_type   = isset( $attributes['renderType'] ) ? $attributes['renderType'] : 'chart';
	$temp_unit     = isset( $attributes['tempUnit'] ) ? $attributes['tempUnit'] : 'celsius';
	$format_time   = isset( $attributes['timeFormat'] ) ? $attributes['timeFormat'] : '24';
	$format_date   = isset( $attributes['dateFormat'] ) ? $attributes['dateFormat'] : 'd/m/Y';
	$client_ident  = isset( $attributes['clientIdent'] ) ? $attributes['clientIdent'] : uniqid();
	$x_grid        = isset( $attributes['displayXGrid'] ) ? (bool) $attributes['displayXGrid'] : true;
	$y_grid        = isset( $attributes['displayYGrid'] ) ? (bool) $attributes['displayYGrid'] : true;
	$fill_content  = isset( $attributes['fillLineContent'] ) ? (bool) $attributes['fillLineContent'] : true;
	$line_humidity = isset( $attributes['displayHumidity'] ) ? (bool) $attributes['displayHumidity'] : true;
	$block_spacing = isset( $attributes['marginTopBottom'] ) && is_array( $attributes['marginTopBottom'] ) ? $attributes['marginTopBottom'] : array();
	$badge_style   = isset( $attributes['badgeStyle'] ) ? $attributes['badgeStyle'] : array();
	$chart_colors  = isset( $attributes['chartColors'] ) ? $attributes['chartColors'] : array();
	$bg_image_id   = isset( $attributes['imageCardId'] ) ? intval( $attributes['imageCardId'] ) : 0;
	$chart_label_fs = isset( $attributes['chartLabelFontSize'] ) ? intval( $attributes['chartLabelFontSize'] ) : 10;
	$inline_align  = isset( $attributes['align'] ) ? $attributes['align'] : 'center';
	$nb_hours      = 6;
	$day_by_hour   = array();
	$day_by_week   = array();
	$image_meteo_path = EAC_PLUGIN_URL . 'assets/images/meteo/';

	if ( ! empty( $block_spacing ) ) {
		$unit                       = $block_spacing['unit'];
		$block_spacing['marginSup'] = ! empty( $block_spacing['marginSup'] ) ? (string) sanitize_text_field( $block_spacing['marginSup'] ) . $unit : '0' . $unit;
		$block_spacing['marginInf'] = ! empty( $block_spacing['marginInf'] ) ? (string) sanitize_text_field( $block_spacing['marginInf'] ) . $unit : '0' . $unit;
	} else {
		$block_spacing['marginSup'] = '0';
		$block_spacing['marginInf'] = '0';
	}

	$badge_bg_color    = ! empty( $badge_style['bgColor'] ) ? $badge_style['bgColor'] : '';
	$badge_fg_color    = ! empty( $badge_style['color'] ) ? $badge_style['color'] : '';
	$badge_global_font = ! empty( $badge_style['fontSize'] ) ? $badge_style['fontSize'] : '';
	$badge_city_font   = ! empty( $badge_style['fontSizeCity'] ) ? $badge_style['fontSizeCity'] : '';
	$badge_temp_font   = ! empty( $badge_style['fontSizeTemp'] ) ? $badge_style['fontSizeTemp'] : '';

	$single_color     = ! empty( $chart_colors['singleLine'] ) ? $chart_colors['singleLine'] : '#48ADD8';
	$multiple_color[] = ! empty( $chart_colors['multipleLineLow'] ) ? $chart_colors['multipleLineLow'] : '#36A2EB';
	$multiple_color[] = ! empty( $chart_colors['multipleLineAvg'] ) ? $chart_colors['multipleLineAvg'] : '#FF9F40';
	$multiple_color[] = ! empty( $chart_colors['multipleLineHigh'] ) ? $chart_colors['multipleLineHigh'] : '#FF6384';
	$multiple_colors  = implode( ',', $multiple_color );

	// Alignement des badges en inline : on convertit left/center/right en margin-inline auto pour centrer ou aligner à gauche/droite
	if ( 'left' === $inline_align ) {
		$inline_align = '0 auto';
	} elseif ( 'right' === $inline_align ) {
		$inline_align = 'auto 0';
	} else {
		$inline_align = 'auto';
	}

	if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
		$no_file = sprintf( '<div class="eac-meteo-empty">%s</div>', esc_html__( 'You do not have permission to use this block.', 'eac-components' ) );
		return $no_file;
	}

	// extraction des données API de géocodage pour la ville - Nominatim
	$geo = eac_extract_goecode_data( $city, $language );
	if ( is_wp_error( $geo ) ) {
		$error_code = $geo->get_error_code();
		$error_message = $geo->get_error_message();
		$no_data = sprintf( '<div class="eac-meteo-empty">[%s] %s</div>', esc_html( $error_code ), esc_html( $error_message ) );
		return $no_data;
	}

	// Si la langue n'est pas initialiser, on essaie de l'extraire de geocode Nominatim
	if ( '' === $language ) {
		$ex = eac_extract_country_from_address( $geo['address'] ?? array() );
		if ( null !== $ex ) {
			$language = $ex; // FR, EN, etc. à 2 lettres
		}
	}

	$data = eac_extract_meteo_data( $geo['lat'], $geo['lon'], $city, $language );
	if ( is_wp_error( $data ) ) {
		$error_code = $data->get_error_code();
		$error_message = $data->get_error_message();
		$no_data = sprintf( '<div class="eac-meteo-empty">[%s] %s</div>', esc_html( $error_code ), esc_html( $error_message ) );
		return $no_data;
	}

	$date_format = (string) $format_date;
	$time_format = '24' === $format_time ? 'H:i' : 'g:i A';
	$unit_temp = 'fahrenheit' === $temp_unit ? '°F' : '°C';

	/** Météo pour un jour */
	if ( 'day' === $meteo_type ) {
		$city_tz = isset( $data['timezone'] ) && '' !== $data['timezone'] ? new DateTimeZone( $data['timezone'] ) : wp_timezone();

		$day_by_hour = eac_get_meteo_by_hour( $data, $city_tz, $date_format, $time_format, $nb_hours, $temp_unit );
		if ( empty( $day_by_hour ) ) {
			$no_data = sprintf( '<div class="eac-meteo-empty">%s: %s</div>', esc_html__( 'No data for', 'eac-components' ), esc_html( $geo['display_name'] ) );
			return $no_data;
		}

		$current = isset( $day_by_hour['current'][0] ) && ! empty( $day_by_hour['current'][0] ) ? $day_by_hour['current'][0] : array();
		if ( empty( $current ) ) {
			$no_data = sprintf( '<div class="eac-meteo-empty">%s: %s</div>', esc_html__( 'No current data for', 'eac-components' ), esc_html( $geo['display_name'] ) );
			return $no_data;
		}
	} elseif ( 'week' === $meteo_type ) { /** Météo pour la semaine */
		$day_by_week = eac_get_meteo_by_day( $data, $date_format, $temp_unit, $language );

		if ( empty( $day_by_week ) ) {
			$no_data = sprintf( '<div class="eac-meteo-empty">%s: %s</div>', esc_html__( 'No data for', 'eac-components' ), esc_html( $geo['display_name'] ) );
			return $no_data;
		}
	}

	$display = '';
	if ( 'day' === $meteo_type && 'chart' === $render_type ) {
		$display = 'render-single-chart';
	} elseif ( 'day' === $meteo_type && 'badge-block' === $render_type ) {
		$display = 'render-badge-block';
	} elseif ( 'day' === $meteo_type && 'badge-inline' === $render_type ) {
		$display = 'render-badge-inline';
	} elseif ( 'day' === $meteo_type && 'card' === $render_type ) {
		$display = 'render-card';
	} elseif ( 'week' === $meteo_type ) {
		$display = 'render-multiple-chart';
	} elseif ( 'pollution' === $meteo_type ) {
		$display = 'render-pollution';
	}

	// Sécurité : si $display est vide, log d'erreur
	if ( empty( $display ) ) {
		$no_file = sprintf( '<div class="eac-meteo-empty">%s</div>', esc_html__( 'Invalid block configuration', 'eac-components' ) );
		return $no_file;
	}

	$template_file = sprintf( '%s.php', __DIR__ . '/assets/templates/' . $display );
	if ( is_readable( $template_file ) ) {
		ob_start();
		require $template_file;
		return ob_get_clean();
	} else {
		$no_file = sprintf( '<div class="eac-meteo-empty">%s: /templates/%s.php</div>', esc_html__( 'No such file', 'eac-components' ), $display );
		return $no_file;
	}
}

/**
 * eac_register_route_language
 *
 * @return void
 */
function eac_register_route_language(): void {
	register_rest_route(
		'eac-blocks/v1',
		'/language',
		array(
			'methods'             => WP_REST_Server::READABLE, // GET
			'callback'            => 'eac_rest_get_language',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}

/**
 * eac_rest_get_language
 *
 * @param WP_REST_Request $request
 *
 * @return WP_REST_Response
 */
function eac_rest_get_language( WP_REST_Request $request ): WP_REST_Response { // phpcs:ignore
	$code_lang = 'en';
	$supported_languages = array( 'en', 'fr', 'es', 'it', 'hi' );
	$languages = array();
	$langue    = $code_lang;

	$locale = determine_locale(); // ex: 'fr_FR' ou 'fr'
	$langue = ( strpos( $locale, '_' ) !== false ) ? substr( $locale, 0, strpos( $locale, '_' ) ) : $locale;

	if ( in_array( $langue, $supported_languages, true ) ) {
		$code_lang = $langue;
	}

	$languages = eac_get_langs( $code_lang );
	uasort( $languages, function ( $a, $b ) {
		$na = mb_strtolower( eac_remove_accents( $a ) );
		$nb = mb_strtolower( eac_remove_accents( $b ) );
		return $na <=> $nb;
	} );

	return rest_ensure_response( $languages );
}

/**
 * eac_get_langs
 *
 * @param string $client_lang
 *
 * @return array
 */
function eac_get_langs( string $client_lang ): array {
	$languages = include __DIR__ . '/assets/utils/countries.php';
	$langs     = array();

	foreach ( $languages as $language => $properties ) {
		$val = $properties[ $client_lang ];
		$langs[ esc_attr( strtolower( $language ) ) ] = esc_html( $val );
	}
	return $langs;
}

/**
 * eac_remove_accents
 *
 * @param string $str
 *
 * @return string
 */
function eac_remove_accents( string $str ): string {
	if ( function_exists( 'transliterator_transliterate' ) ) {
		return transliterator_transliterate( 'Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove', $str );
	}

	$chars = array(
		'À' => 'A',
		'Á' => 'A',
		'Â' => 'A',
		'Ã' => 'A',
		'Ä' => 'A',
		'Å' => 'A',
		'Æ' => 'AE',
		'Ç' => 'C',
		'È' => 'E',
		'É' => 'E',
		'Ê' => 'E',
		'Ë' => 'E',
		'Ì' => 'I',
		'Í' => 'I',
		'Î' => 'I',
		'Ï' => 'I',
		'Ð' => 'D',
		'Ñ' => 'N',
		'Ò' => 'O',
		'Ó' => 'O',
		'Ô' => 'O',
		'Õ' => 'O',
		'Ö' => 'O',
		'Ø' => 'O',
		'Ù' => 'U',
		'Ú' => 'U',
		'Û' => 'U',
		'Ü' => 'U',
		'Ý' => 'Y',
		'Þ' => 'Th',
		'ß' => 'ss',
		'à' => 'a',
		'á' => 'a',
		'â' => 'a',
		'ã' => 'a',
		'ä' => 'a',
		'å' => 'a',
		'æ' => 'ae',
		'ç' => 'c',
		'è' => 'e',
		'é' => 'e',
		'ê' => 'e',
		'ë' => 'e',
		'ì' => 'i',
		'í' => 'i',
		'î' => 'i',
		'ï' => 'i',
		'ð' => 'd',
		'ñ' => 'n',
		'ò' => 'o',
		'ó' => 'o',
		'ô' => 'o',
		'õ' => 'o',
		'ö' => 'o',
		'ø' => 'o',
		'ù' => 'u',
		'ú' => 'u',
		'û' => 'u',
		'ü' => 'u',
		'ý' => 'y',
		'þ' => 'th',
		'ÿ' => 'y',
	);

	return strtr( $str, $chars );
}

/**
 * eac_get_uv_color
 *
 * @param float $uv_index
 *
 * @return string
 */
function eac_get_uv_color( float $uv_index ): string {
	if ( $uv_index <= 2 ) {
		return 'rgb(31, 163, 0)'; // Vert pour UV faible
	} elseif ( $uv_index <= 5 ) {
		return 'rgb(255, 187, 0)'; // Jaune pour UV modéré
	} elseif ( $uv_index <= 7 ) {
		return 'rgb(255, 119, 33)'; // Orange pour UV élevé
	} elseif ( $uv_index <= 10 ) {
		return 'rgb(255, 0, 0)'; // Rouge pour UV très élevé
	} else {
		return 'rgb(210, 39, 255)'; // Violet pour UV extrême
	}
}
