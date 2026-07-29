<?php
/**
 *
 *
 * @since 2.5.1
 */

$wrapper_id = 'eac-meteo__block-temp-' . uniqid();
$wrapper    = 'eac-meteo__block-temp badge-card';

$wrapper_style_parts = array();
$wrapper_style_parts[] = 'margin-block-start:' . esc_attr( $block_spacing['marginSup'] );
$wrapper_style_parts[] = 'margin-block-end:' . esc_attr( $block_spacing['marginInf'] );
$wrapper_style_parts[] = 'margin-inline:' . esc_attr( $inline_align );
'' !== $badge_bg_color ? $wrapper_style_parts[] = 'background-color:' . esc_attr( $badge_bg_color ) : '';
'' !== $badge_fg_color ? $wrapper_style_parts[] = 'color:' . esc_attr( $badge_fg_color ) : '';

// Passer l'image comme variable CSS au lieu de background-image directe
if ( 0 !== $bg_image_id ) {
	$bg_opacity = isset( $attributes['opacity'] ) ? floatval( $attributes['opacity'] ) : 1;
	$wrapper_style_parts[] = '--bg-opacity:' . esc_attr( $bg_opacity );
	$image_url = wp_get_attachment_image_url( $bg_image_id, 'full' );
	$wrapper_style_parts[] = '--bg-image:url(' . esc_url( $image_url ) . ')';
}

$wrapper_attr = 'style="' . implode( '; ', $wrapper_style_parts ) . ';"';

if ( $current['is_day'] ) {
	$sky_svg = sprintf( '%s%s.svg', $image_meteo_path, eac_get_weather_image( $current['weather_code'] ) );
} elseif ( ! $current['is_day'] ) {
	$sky_svg = sprintf( '%s%s.svg', $image_meteo_path, eac_get_weather_image( 999 ) );
}

$uv_bg_color = 'transparent';
if ( $current['is_day'] ) {
	$uv_bg_color = eac_get_uv_color( $current['uv_index_max'] );
}
?>
<div class='<?php echo esc_attr( $wrapper ); ?>' id='<?php echo esc_attr( $wrapper_id ); ?>' <?php echo $wrapper_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class='eac-meteo__temp-badge' style='font-size:<?php echo esc_attr( $badge_global_font ); ?>;'>
		<div class='eac-meteo__badge-date'>
			<span class='eac-meteo__date-date'><?php printf( '%s', esc_html( $current['day_date'] ) ); ?></span>
			<span class='eac-meteo__date-hour'><?php printf( '%s', esc_html( $current['day_hour'] ) ); ?></span>
		</div>
		<div class='eac-meteo__badge-icon'>
			<img aria-hidden='true' alt='' src="<?php echo esc_url( $sky_svg ); ?>" />
			<div class='eac-meteo__humd-label'><?php echo esc_html( eac_get_weather_label( $current['weather_code'], strtolower( $language ) ) ); ?></div>
		</div>
		<div class='eac-meteo__city-city' style='font-size:<?php echo esc_attr( $badge_city_font ); ?>;'><?php printf( '%s&nbsp;(%s)', esc_html( ucwords( $city ) ), esc_html( $language ) ); ?></div>
		<div class='eac-meteo__city-temp' style='font-size:<?php echo esc_attr( $badge_temp_font ); ?>;'><?php printf( '%s', esc_html( $current['temperature_2m'] ) ); ?></div>
		<div class='eac-meteo__badge-content'>
			<div class='eac-meteo__badge-humd'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'humid' ); ?></span>
				<span><?php printf( '%s: %s', esc_html__( 'Humidity', 'eac-components' ), esc_html( $current['relative_humidity_2m'] ) ); ?></span>
			</div>
			<div class='eac-meteo__badge-lh'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'thermo' ); ?></span>
				<span><?php printf( '%s: %s %s: %s', esc_html__( 'Low', 'eac-components' ), esc_html( $current['temperature_2m_min'] ), esc_html__( 'High', 'eac-components' ), esc_html( $current['temperature_2m_max'] ) ); ?></span>
			</div>
			
			<div class='eac-meteo__badge-sunrise'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'sunrise' ); ?></span>
				<span><?php printf( '%s: %s', esc_html__( 'Sunrise', 'eac-components' ), esc_html( $current['sunrise'] ) ); ?></span>
			</div>
			<div class='eac-meteo__badge-sunset'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'sunset' ); ?></span>
				<span><?php printf( '%s: %s', esc_html__( 'Sunset', 'eac-components' ), esc_html( $current['sunset'] ) ); ?></span>
			</div>

			<div class='eac-meteo__badge-cloud'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'cloud' ); ?></span>
				<span><?php printf( '%s: %s', esc_html__( 'Clouds', 'eac-components' ), esc_html( $current['cloud_cover'] ) ); ?></span>
			</div>
			<div class='eac-meteo__badge-rain'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'rain' ); ?></span>
				<span><?php printf( '%s: %s', esc_html__( 'Rain probability', 'eac-components' ), esc_html( $current['precipitation_prob'] ) ); ?></span>
			</div>
			<div class='eac-meteo__badge-prec'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'precip' ); ?></span>
				<span><?php printf( '%s: %s', esc_html__( 'Precipitation', 'eac-components' ), esc_html( $current['precipitation'] ) ); ?></span>
			</div>
			<div class='eac-meteo__badge-pres'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'press' ); ?></span>
				<span><?php printf( '%s: %s', esc_html__( 'Pressure', 'eac-components' ), esc_html( $current['pressure_msl'] ) ); ?></span>
			</div>
			<div class='eac-meteo__badge-uv'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'uv' ); ?></span>
				<span class='eac-meteo__uv-index' style="background-color: <?php echo esc_attr( $uv_bg_color ); ?>;"><?php printf( '%s: %s', esc_html__( 'UV Index', 'eac-components' ), esc_html( $current['uv_index_max'] ) ); ?></span>
			</div>
			<div class='eac-meteo__badge-wind'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'wind' ); ?></span>
				<span><?php printf( '%s: %s', esc_html__( 'Wind', 'eac-components' ), esc_html( $current['wind_speed_10m'] ), esc_html( $current['wind_direction_10m'] ) ); ?></span>
				<span class='eac-icon-svg' style="rotate:<?php echo esc_html( $current['wind_direction_ico'] ); ?>deg;"><?php eac_print_svg_icon( 'warrow' ); ?></span>
			</div>
			<div class='eac-meteo__badge-gust'>
				<span class='eac-icon-svg'><?php eac_print_svg_icon( 'gust' ); ?></span>
				<span><?php printf( '%s: %s', esc_html__( 'Gust', 'eac-components' ), esc_html( $current['wind_gusts_10m'] ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php
