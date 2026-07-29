<?php
/**
 * Weather AQI Card Block Template File.
 *
 * This template displays AQI summary file.
 *
 * @since      3.2.0
 * @version    1.0.0
 *
 * @package Location_Weather_Pro/Blocks
 */

// Prevent direct file access for security.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

$show_update_time   = $attributes['displayDateUpdateTime'] ?? true;
$show_aqi_condition = $attributes['enableSummaryAqiCondition'] ?? true;
$show_aqi_desc      = $attributes['enableSummaryAqiDesc'] ?? true;
$pollutant_data     = Block_Helpers::get_pollutant_data( $current_aqi_data->pm2_5, 'pm2_5' );
$color              = Block_Helpers::hex_to_rgba( $pollutant_data['color'] );
?>

<div class="spl-weather-aqi-card-summary sp-d-flex sp-flex-col sp-w-full spl-aqi-condition-<?php echo esc_attr( strtolower( $pollutant_data['condition_label'] ) ); ?>" style="--spl-aqi-condition-color: <?php echo esc_attr( $color ); ?>;">
	<div class="spl-aqi-card-header-section sp-d-flex sp-flex-col sp-w-full sp-justify-center sp-align-i-center">
		<div class="spl-aqi-card-heading"><?php echo esc_html( $aqi_summary_heading_label ); ?></div>
		<?php
			require self::block_template_renderer( 'aqi-minimal/parts/location-name.php', true );
		?>
	</div>
	<!-- AQI Section -->
	<div class="spl-aqi-card-aqi-condition sp-d-flex sp-w-full sp-justify-between sp-align-i-center">
		<!-- Render AQI Gauge Based On template condition -->
		<div class="spl-aqi-card-progress-bar">
			<?php
				$aqi_gauge = Block_Helpers::render_aqi_pollutant_gauge(
					array(
						'value'           => $current_aqi_data->pm2_5,
						'size'            => 140,
						'strokeWidth'     => 8,
						'labelPadding'    => 5,
						'isAccordion'     => true,
						'conditionLabels' => false,
						'aqiText'         => __( 'AQI', 'location-weather' ),
					)
				);
				echo $aqi_gauge; // phpcs:ignore
				?>
		</div>
		<!-- Template: AQI Minimal One And Four -->
		<div class="spl-aqi-card-condition sp-d-flex sp-flex-col sp-justify-center sp-align-i-center sp-gap-4px" role="status" aria-label="Air Quality">
			<span class="title"><?php echo esc_html__( 'Air Quality', 'location-weather' ); ?></span>
			<span class="condition spl-aqi-card-aqi-condition-<?php echo esc_attr( strtolower( $pollutant_data['condition_label'] ) ); ?>"><?php echo esc_html( $pollutant_data['condition'] ); ?></span>
		</div>
	</div>

	<!-- AQI Description (Minimal One Only) -->
	<?php if ( $show_aqi_desc ) : ?>
	<div class="spl-aqi-card-aqi-description sp-d-flex sp-w-full"><?php echo esc_html( $pollutant_data['detailed_report'] ); ?></div>
	<?php endif; ?>
</div>
