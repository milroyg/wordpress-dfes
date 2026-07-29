<?php
/**
 *
 *
 * @since 2.5.1
 */

$wrapper_id = 'eac-meteo__block-temp-' . uniqid();
$wrapper    = 'eac-meteo__block-temp badge-inline';

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
		<div class='eac-meteo__badge-icon'><img aria-hidden='true' alt='' src="<?php echo esc_url( $image_svg ); ?>" /></div>
		<div class='eac-meteo__badge-city'>
			<div class='eac-meteo__city-city' style='font-size:<?php echo esc_attr( $badge_city_font ); ?>;'><?php printf( '%s&nbsp;(%s)', esc_html( ucwords( $city ) ), esc_html( $language ) ); ?></div>
			<div class='eac-meteo__city-temp' style='font-size:<?php echo esc_attr( $badge_temp_font ); ?>;'><?php printf( '%s', esc_html( $current['temperature_2m'] ) ); ?></div>
		</div>
	</div>
	<style>
		/** Mode responsive mobile */
		div.eac-meteo__block-temp.badge-inline#<?php echo esc_attr( $wrapper_id ); ?> .eac-meteo__temp-badge {
			grid-template-columns: 1fr;
			gap: 0;
		}
		div.eac-meteo__block-temp.badge-inline#<?php echo esc_attr( $wrapper_id ); ?> .eac-meteo__temp-badge .eac-meteo__badge-date {
			flex-direction: row;
			inline-size: 100%;
		}

		/** Mode responsive desktop */
		@media (min-width: 576px) {
			div.eac-meteo__block-temp.badge-inline#<?php echo esc_attr( $wrapper_id ); ?> .eac-meteo__temp-badge {
				grid-template-columns: 1fr 1fr 2fr;
				max-inline-size: 400px;
				gap: 5px;
			}
			div.eac-meteo__block-temp.badge-inline#<?php echo esc_attr( $wrapper_id ); ?> .eac-meteo__temp-badge>div {
				margin-block-end: 0;
			}
			div.eac-meteo__block-temp.badge-inline#<?php echo esc_attr( $wrapper_id ); ?> .eac-meteo__temp-badge .eac-meteo__badge-date {
				flex-direction: column;
				inline-size: auto;
			}
		}
	</style>
</div>
<?php
