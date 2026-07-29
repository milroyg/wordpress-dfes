<?php
/**
 *
 *
 * @since 2.5.1
 */

$wrapper_id = 'eac-meteo__block-temp-' . uniqid();
$wrapper    = 'eac-meteo__block-temp single';

$wrapper_style_parts = array();
$wrapper_style_parts[] = 'margin-block-start:' . esc_attr( $block_spacing['marginSup'] );
$wrapper_style_parts[] = 'margin-block-end:' . esc_attr( $block_spacing['marginInf'] );
$wrapper_attr = 'style="' . implode( '; ', $wrapper_style_parts ) . ';"';

$x_axis = array();
$y_axis = array();
foreach ( $day_by_hour['hourly'] as $row ) {
	$dt = $row['time'];
	$val_c = $row['value'];
	$val_display = $val_c;

	$display = $dt->format( $time_format );
	$x_axis[] = esc_html( $display );
	$y_axis[] = esc_html( null !== $val_display ? (string) $val_display : '—' );

	$module_settings = array(
		'data_type'   => esc_html( $chart_type ),
		'x_axis'      => implode( ',', $x_axis ),
		'y_axis'      => implode( ',', $y_axis ),
		'x_label'     => esc_html__( 'Hour', 'eac-components' ),
		'y_label'     => sprintf( '%s %s', esc_html__( 'Temperature', 'eac-components' ), esc_attr( $unit_temp ) ),
		'data_title'  => sprintf( '%s %s (%s) - %s', esc_attr__( 'Weather forecast for', 'eac-components' ), esc_attr( ucwords( $city ) ), esc_html( $language ), esc_attr( $current['local_time'] ) ),
		'data_color'  => $single_color,
		'data_fill'   => $fill_content,
		'data_x_grid' => $x_grid,
		'data_y_grid' => $y_grid,
		'data_shadow' => false,
		'data_fsize'  => $chart_label_fs,
	);
}

$title_city = '';
if ( isset( $geo['address']['city'] ) ) {
	$title_city = $geo['address']['city'];
} elseif ( isset( $geo['address']['state'] ) ) {
	$title_city = $geo['address']['state'];
} elseif ( isset( $geo['address']['county'] ) ) {
	$title_city = $geo['address']['county'];
}
$title_country = isset( $geo['address']['country'] ) ? $geo['address']['country'] : '';
?>
<div class='<?php echo esc_attr( $wrapper ); ?>' id='<?php echo esc_attr( $wrapper_id ); ?>' <?php echo $wrapper_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class='eac-meteo__temp-title'>
		<?php printf( '%s %s (±6h %s)', esc_html( $title_city ), esc_html( $title_country ), esc_html__( 'hours', 'eac-components' ) ); ?>
		<a href="https://www.openstreetmap.org/?mlat=<?php echo esc_attr( $geo['lat'] ); ?>&mlon=<?php echo esc_attr( $geo['lon'] ); ?>&zoom=13" 
			target="_blank" 
			rel="nofollow noopener noreferrer" 
			aria-label="<?php printf( '%s %s', esc_attr( $geo['display_name'] ), esc_attr__( 'View on OpenStreetMap in a new tab', 'eac-components' ) ); ?>"
			style="display:inline-block; position:relative; margin-left: 8px; top:0.35em;"><?php eac_print_svg_icon( 'marker' ); ?>
		</a>
	</div>
	<div class='eac-meteo__temp-chart' data-client-ident='<?php echo esc_attr( $client_ident ); ?>' data-settings='<?php echo wp_json_encode( $module_settings ); ?>'>
		<canvas></canvas>
	</div>
</div>
<?php
