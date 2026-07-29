<?php
/**
 *
 *
 * @since 2.5.1
 */

$wrapper_id = 'eac-meteo__block-temp-' . uniqid();
$wrapper    = 'eac-meteo__block-temp multiple';

$wrapper_style_parts = array();
$wrapper_style_parts[] = 'margin-block-start:' . esc_attr( $block_spacing['marginSup'] );
$wrapper_style_parts[] = 'margin-block-end:' . esc_attr( $block_spacing['marginInf'] );
$wrapper_attr = 'style="' . implode( '; ', $wrapper_style_parts ) . ';"';

foreach ( $day_by_week as $day ) {
	$x_axis[]          = esc_html( $day['date'] );
	$y_axis[]          = esc_html( $day['avg'] );
	$y_axis_min[]      = esc_html( $day['min'] );
	$y_axis_max[]      = esc_html( $day['max'] );
	$y_axis_humidity[] = esc_html( $day['humidity'] );
}

$module_settings = array(
	'data_type'   => $chart_type,
	'x_axis'      => implode( ',', $x_axis ),
	'y_axis'      => implode( ',', $y_axis ),
	'y_axis_min'  => implode( ',', $y_axis_min ),
	'y_axis_max'  => implode( ',', $y_axis_max ),
	'y_axis_humidity' => $line_humidity ? implode( ',', $y_axis_humidity ) : array(),
	'x_label'     => esc_html__( 'Date', 'eac-components' ),
	'y_label'     => sprintf( '%s %s', esc_html__( 'Temperature', 'eac-components' ), esc_attr( $unit_temp ) ),
	'data_title'  => sprintf( '%s %s (%s)', esc_attr__( 'Weekly weather forecast for', 'eac-components' ), esc_attr( ucwords( $city ) ), esc_html( $language ) ),
	'data_colors' => $multiple_colors, //'#66BB6A, #FFC334, #FF4444'
	'data_fill'   => $fill_content,
	'data_x_grid' => $x_grid,
	'data_y_grid' => $y_grid,
	'data_shadow' => false,
	'data_fsize'  => $chart_label_fs,
);
?>
<div class='<?php echo esc_attr( $wrapper ); ?>' id='<?php echo esc_attr( $wrapper_id ); ?>' <?php echo $wrapper_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class='eac-meteo__temp-chart' data-client-ident='<?php echo esc_attr( $client_ident ); ?>' data-settings='<?php echo wp_json_encode( $module_settings ); ?>'>
		<canvas></canvas>
	</div>
</div>
<?php
