<?php
/**
 * Weather Block Grid Two And Three Additional Data Renderer.
 *
 * @since      3.2.0
 * @version    1.0.0
 *
 * @package Location_Weather_Pro/Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! $attributes['displayAdditionalData'] ) {
	return;
}

use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

$additional_data_options = $attributes['additionalDataOptions'];
$additional_data_options = Block_Helpers::process_additional_data_options( $additional_data_options );
// air index.
$air_quality_index = $air_quality_data['index'] ?? 0;
$air_clamped       = max( 0, min( $air_quality_index, 5 ) );
$air_position      = ( $air_clamped / 5 ) * 92;
// uv_index.
$uv_index       = $weather_data['uv_index']['index'] ?? 0;
$uv_description = $weather_data['uv_index']['desc'] ?? '';
$uv_clamped     = max( 0, min( $uv_index, 11 ) );
$uv_position    = ( $uv_clamped / 11 ) * 100;
// other options.
$sunrise_time   = $weather_data['sunrise_time'] ?? '';
$sunset_time    = $weather_data['sunset_time'] ?? '';
$moon_phase     = $weather_data['moon_phase'] ?? '';
$wind           = $weather_data['wind'] ?? '';
$wind_direction = $weather_data['wind_direction']->unit ?? '';
$wind_gust      = $weather_data['gust'] ?? '';
$humidity       = $weather_data['humidity']['value'] . ' %' ?? '';
$dew_point      = $weather_data['dew_point'] ?? '0';
$pressure       = $weather_data['pressure'] ?? null;
$precipitation  = $weather_data['precipitation'] ?? '';
$rainchance     = $weather_data['rainchance'] ?? '';
$snow           = $weather_data['snow'] ?? '';
$visibility     = $weather_data['visibility'] ?? null;

require self::block_template_renderer( 'additional-data/weather-icons.php' );

?>
<?php foreach ( $additional_data_options as $option ) : ?>
	<?php if ( 'uv_index' === $option ) : ?>
	<div class="spl-weather-grid-item sp-d-flex sp-flex-col sp-justify-between">
		<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
			<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
				<i class="<?php echo esc_attr( $block_weather_icons['uv_index'] ); ?>"></i>
			</span>
			<span class="spl-weather-options-title">
				<?php echo esc_attr( $weather_item_labels['uv_index'] ); ?>
			</span>
		</div>
		<div class="spl-weather-main-value sp-d-flex sp-flex-col">
			<span class="spl-weather-uv-report sp-d-flex sp-flex-col">
				<span class="spl-weather-uv_index">
					<?php echo esc_attr( $uv_index ); ?>
				</span>
				<span class="spl-weather-uv_details">
					<?php echo esc_html( $uv_description ); ?>
				</span>
			</span>
		</div>
		<div class="spl-weather-uv-gradient-bar">
			<div class="spl-weather-uv-indicator" style="left:<?php echo esc_attr( $uv_position ); ?>%"></div>
		</div>
	</div>
	<?php elseif ( 'sunrise_time' === $option && $sunrise_time && $sunset_time ) : ?>
	<!-- sunrise and sunset details -->
	<div class="spl-weather-grid-item sp-d-flex sp-flex-col sp-justify-between">
		<div class="spl-weather-sunrise">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
					<i class="<?php echo esc_attr( $block_weather_icons['sunrise_time'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['sunrise'] ); ?>
			</div>
			<div class="spl-weather-main-value sp-font-500">
				<?php echo esc_html( $sunrise_time ); ?>
			</div>
		</div>
		<div class="splw-sunset-scale-wrapper">
			<svg viewBox="0 0 320 60" class="splw-sunset-scale-svg" preserveAspectRatio="none">
				<path
					d="M0,60 Q160,0 320,60"
					fill="none"
					stroke="url(#sunsetGradient)"
					stroke-width="4"
				/>
				<defs>
					<linearGradient id="sunsetGradient" x1="0%" y1="0%" x2="100%" y2="0%">
						<stop offset="0%" stop-color="#B2A9A9" />
						<stop offset="17.01%" stop-color="#B2A9A9" />
						<stop offset="24.99%" stop-color="#F1BB94" />
						<stop offset="74.39%" stop-color="#F1BB94" />
						<stop offset="82.54%" stop-color="#B2A9A9" />
						<stop offset="100%" stop-color="#B2A9A9" />
					</linearGradient>
				</defs>
			</svg>
		</div>
		<div class="spl-weather-sunset sp-d-flex sp-align-i-center sp-gap-4px">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
					<i class="<?php echo esc_attr( $block_weather_icons['sunset_time'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['sunset'] ); ?>
			</div>
			<div class="spl-weather-bottom-value">
				<?php echo esc_html( $sunset_time ); ?>
			</div>
		</div>
	</div>
	<?php elseif ( 'moonrise' === $option && ( $moonrise || $moonset || $moon_phase ) ) : ?>
	<!-- moonrise and moonset details -->
	<div class="spl-weather-grid-item sp-d-flex sp-flex-col sp-justify-between">
		<div class="spl-weather-moonrise">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
					<i class="<?php echo esc_attr( $block_weather_icons['moonrise'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['moonrise'] ); ?>
			</div>
			<div class="spl-weather-main-value sp-font-500">
				<?php echo esc_html( $moonrise ); ?>
			</div>
		</div>
		<div class="spl-weather-moonrise-moonset">
			<div class="spl-weather-moonset sp-d-flex sp-align-i-center sp-gap-4px">
				<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
					<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
						<i class="<?php echo esc_attr( $block_weather_icons['moonset'] ); ?>"></i>
					</span>
					<?php echo esc_attr( $weather_item_labels['moonset'] ); ?>
				</div>
				<div class="spl-weather-bottom-value">
					<?php echo esc_html( $moonset ); ?>
				</div>
			</div>
			<?php if ( in_array( 'moon_phase', $additional_data_options, true ) ) : ?>
			<div class="spl-weather-moon_phase sp-d-flex sp-align-i-center sp-gap-4px">
				<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
					<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
						<i class="<?php echo esc_attr( $block_weather_icons['moon_phase'] ); ?>"></i>
					</span>
					<?php echo esc_attr( $weather_item_labels['moon_phase'] ); ?>
				</div>
				<div class="spl-weather-bottom-value">
					<?php echo esc_html( $moon_phase ); ?>%
				</div>
			</div>
			<?php endif ?>
		</div>
	</div>
	<?php elseif ( 'wind' === $option && ( $wind || $wind_gust ) ) : ?>
	<!-- wind and wind gust details -->
	<div class="spl-weather-grid-item sp-d-flex sp-flex-col sp-justify-between">
		<div class="spl-weather-wind">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
					<i class="<?php echo esc_attr( $block_weather_icons['wind'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['wind'] ); ?>
			</div>
			<div class="spl-weather-main-value sp-font-500">
				<?php
					echo wp_kses_post( $wind . ' ' );
					echo esc_html( $wind_direction );
				?>
			</div>
		</div>
		<?php if ( in_array( 'gust', $additional_data_options, true ) && $wind_gust ) : ?>
		<div class="spl-weather-wind-gust sp-d-flex sp-align-i-center sp-gap-4px">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
				<i class="<?php echo esc_attr( $block_weather_icons['gust'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['gust'] ); ?>
			</div>
			<div class="spl-weather-bottom-value">
				<?php echo wp_kses_post( $wind_gust ); ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php elseif ( 'humidity' === $option && $humidity ) : ?>
	<!-- humidity details -->
	<div class="spl-weather-grid-item sp-d-flex sp-flex-col sp-justify-between">
		<div class="spl-weather-humidity">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
					<i class="<?php echo esc_attr( $block_weather_icons['humidity'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['humidity'] ); ?>
			</div>
			<div class="spl-weather-main-value sp-font-500">
				<?php echo esc_html( $humidity ); ?>
			</div>
		</div>
		<?php
		if ( in_array( 'dew_point', $additional_data_options, true ) ) {
			?>
			<div class="spl-weather-dew-point sp-d-flex sp-align-i-center sp-gap-4px">
				<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
					<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
						<i class="<?php echo esc_attr( $block_weather_icons['dew_point'] ); ?>"></i>
					</span>
					<?php echo esc_attr( $weather_item_labels['dew_point'] ); ?>
				</div>
				<div class="spl-weather-bottom-value">
					<span class="spl-weather-dew-point-value">
						<?php echo esc_html( $dew_point ); ?>
					</span>°
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php elseif ( 'pressure' === $option && $pressure ) : ?>
	<!-- pressure details -->
	<div class="spl-weather-grid-item sp-d-flex sp-flex-col sp-justify-between">
		<div class="spl-weather-pressure">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
					<i class="<?php echo esc_attr( $block_weather_icons['pressure'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['pressure'] ); ?>
			</div>
			<div class="spl-weather-main-value sp-font-500">
				<?php echo esc_html( $pressure['pressure'] ); ?>
			</div>
		</div>
		<div class="spl-weather-pressure-details sp-d-flex sp-align-i-center sp-gap-4px">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<?php echo esc_html( $pressure['pressure_desc'] ); ?>
			</div>
		</div>
	</div>
	<?php elseif ( 'precipitation' === $option && ( $precipitation || $rainchance || $snow ) ) : ?>
	<!-- precipitation, rainchance, snow option details -->
	<div class="spl-weather-grid-item sp-d-flex sp-flex-col sp-justify-between">
		<div class="spl-weather-precipitation">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
					<i class="<?php echo esc_attr( $block_weather_icons['precipitation'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['precipitation'] ); ?>
			</div>
			<div class="spl-weather-main-value sp-font-500">
				<?php echo esc_html( $precipitation ); ?>
			</div>
		</div>
		<div class="spl-weather-rainchance-snow">
			<?php if ( in_array( 'rainchance', $additional_data_options, true ) && $rainchance ) : ?>
			<div class="spl-weather-rainchance sp-d-flex sp-align-i-center sp-gap-4px">
				<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
					<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
						<i class="<?php echo esc_attr( $block_weather_icons['rainchance'] ); ?>"></i>
					</span>
					<?php echo esc_attr( $weather_item_labels['rainchance'] ); ?>
				</div>
				<div class="spl-weather-bottom-value">
					<?php echo esc_html( $rainchance ); ?>
				</div>
			</div>
			<?php endif; ?>
			<?php if ( in_array( 'snow', $additional_data_options, true ) && $snow ) : ?>
			<div class="spl-weather-snow sp-d-flex sp-align-i-center sp-gap-4px">
				<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
					<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
						<i class="<?php echo esc_attr( $block_weather_icons['snow'] ); ?>"></i>
					</span>
					<?php echo esc_attr( $weather_item_labels['snow'] ); ?>
				</div>
				<div class="spl-weather-bottom-value">
					<?php echo esc_html( $snow ); ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php elseif ( 'air_index' === $option ) : ?>
	<!-- air quality index -->
	<div class="spl-weather-grid-item sp-d-flex sp-flex-col sp-justify-between">
		<div class="spl-weather-air-quality">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
					<i class="<?php echo esc_attr( $block_weather_icons['air_index'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['air_index'] ); ?>
			</div>
			<div class="spl-weather-main-value sp-font-500 sp-d-flex sp-flex-col">
				<span class="spl-weather-air-index">
					<?php echo esc_html( $air_quality_index ); ?>
				</span>
				<span class="spl-weather-air_quality_name">
					<?php echo esc_html( $weather_data['air_quality_name'] ); ?>
				</span>
			</div>
		</div>
		<div class="spl-weather-uv-gradient-bar">
			<div class="spl-weather-uv-indicator" style="left:<?php echo esc_attr( round( $air_position, 1 ) ); ?>%;">
			</div>
		</div>
	</div>
	<?php elseif ( 'visibility' === $option && $visibility ) : ?>
	<!-- visibility details -->
	<div class="spl-weather-grid-item sp-d-flex sp-flex-col sp-justify-between">
		<div class="spl-weather-visibility">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
					<i class="<?php echo esc_attr( $block_weather_icons['visibility'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['visibility'] ); ?>
			</div>
			<div class="spl-weather-main-value sp-font-500">
				<?php echo esc_html( $visibility['visibility'] ); ?>
			</div>
		</div>
		<div class="spl-weather-visibility-desc sp-d-flex sp-align-i-center sp-gap-4px">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<?php echo esc_html( $visibility['visibility_desc'] ); ?>
			</div>
		</div>
	</div>
	<?php elseif ( 'clouds' === $option && $weather_data['clouds'] ) : ?>
	<!-- clouds details -->
	<div class="spl-weather-grid-item sp-d-flex sp-flex-col sp-justify-between">
		<div class="spl-weather-clouds">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
					<i class="<?php echo esc_attr( $block_weather_icons['clouds'] ); ?>"></i>
				</span>
				<?php echo esc_attr( $weather_item_labels['clouds'] ); ?>
			</div>
			<div class="spl-weather-main-value sp-font-500">
				<?php echo esc_html( $weather_data['clouds'] ); ?>
			</div>
		</div>
		<div class="spl-weather-clouds-desc sp-d-flex sp-align-i-center sp-gap-4px">
			<div class="spl-weather-details-title sp-d-flex sp-align-i-center sp-gap-4px">
				<?php echo esc_html( $weather_data['clouds_desc'] ); ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
<?php endforeach; ?>
