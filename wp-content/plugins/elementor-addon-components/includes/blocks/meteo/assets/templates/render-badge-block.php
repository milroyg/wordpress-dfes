<?php
/**
 *
 *
 * @since 2.5.1
 */

$wrapper_id = 'eac-meteo__block-temp-' . uniqid();
$wrapper    = 'eac-meteo__block-temp badge-block';

$wrapper_style_parts = array();
$wrapper_style_parts[] = 'margin-block-start:' . esc_attr( $block_spacing['marginSup'] );
$wrapper_style_parts[] = 'margin-block-end:' . esc_attr( $block_spacing['marginInf'] );
$wrapper_style_parts[] = 'margin-inline:' . esc_attr( $inline_align );
'' !== $badge_bg_color ? $wrapper_style_parts[] = 'background-color:' . esc_attr( $badge_bg_color ) : '';
'' !== $badge_fg_color ? $wrapper_style_parts[] = 'color:' . esc_attr( $badge_fg_color ) : '';
$wrapper_attr = 'style="' . implode( '; ', $wrapper_style_parts ) . ';"';

if ( $current['is_day'] ) {
	$image_svg = sprintf( '%s%s.svg', $image_meteo_path, eac_get_weather_image( $current['weather_code'] ) );
} elseif ( ! $current['is_day'] ) {
	$image_svg = sprintf( '%s%s.svg', $image_meteo_path, eac_get_weather_image( 999 ) );
}
?>
<div class='<?php echo esc_attr( $wrapper ); ?>' id='<?php echo esc_attr( $wrapper_id ); ?>' <?php echo $wrapper_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class='eac-meteo__temp-badge' style='font-size:<?php echo esc_attr( $badge_global_font ); ?>;'>
		<div class='eac-meteo__badge-date'>
			<span class='eac-meteo__date-date'><?php printf( '%s', esc_html( $current['day_date'] ) ); ?></span>
			<span class='eac-meteo__date-hour'><?php printf( '%s', esc_html( $current['day_hour'] ) ); ?></span>
		</div>
		<div class='eac-meteo__badge-icon'>
			<img aria-hidden='true' alt='' src="<?php echo esc_url( $image_svg ); ?>" />
			<div class='eac-meteo__humd-label'><?php echo esc_html( eac_get_weather_label( $current['weather_code'], strtolower( $language ) ) ); ?></div>
		</div>
		<div class='eac-meteo__city-city' style='font-size:<?php echo esc_attr( $badge_city_font ); ?>;'><?php printf( '%s&nbsp;(%s)', esc_html( ucwords( $city ) ), esc_html( $language ) ); ?></div>
		<div class='eac-meteo__city-temp' style='font-size:<?php echo esc_attr( $badge_temp_font ); ?>;'><?php printf( '%s', esc_html( $current['temperature_2m'] ) ); ?></div>
		<div class='eac-meteo__badge-humd'><?php printf( '%s %s', esc_html__( 'Humidity', 'eac-components' ), esc_html( $current['relative_humidity_2m'] ) ); ?></div>
	</div>
</div>
<?php
