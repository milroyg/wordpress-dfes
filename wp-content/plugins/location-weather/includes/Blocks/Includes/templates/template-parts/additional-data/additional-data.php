<?php
/**
 * Additional Data Renderer.
 *
 * Displays the additional data options and layout variations.
 *
 * @since      1.0.0
 * @version    1.0.0
 * @package    Location_Weather
 * @subpackage Location_Weather_Pro/Blocks/templates
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! $attributes['displayAdditionalData'] ) {
	return;
}
use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

$additional_data_layout = $attributes['active_additional_data_layout'];
$display_comport_data   = $attributes['displayComportDataPosition'];
$display_comport_layout = in_array( $additional_data_layout, array( 'center', 'left', 'justified' ), true ) && ! $display_comport_data ? true : false;

$display_colon = in_array( $additional_data_layout, array( 'center', 'column-two', 'left', 'column-two-justified' ), true ) || in_array( $template, array( 'horizontal-one', 'vertical-three', 'vertical-four' ), true );

$additional_data_options = $attributes['additionalDataOptions'];
$additional_data_options = Block_Helpers::process_additional_data_options( $additional_data_options );
// Filter out pressure, humidity, and wind items.
$with_comport_data = array_filter(
	$additional_data_options,
	function ( $item ) {
		return ! in_array( $item, array( 'pressure', 'humidity', 'wind' ), true );
	}
);
// Get only pressure, humidity, and wind items.
$comport_data     = array_filter(
	$additional_data_options,
	function ( $item ) {
		return in_array( $item, array( 'pressure', 'humidity', 'wind' ), true );
	}
);
$is_swiper_layout = in_array( $additional_data_layout, array( 'carousel-simple', 'carousel-flat' ), true );

$additional_data_options = $display_comport_layout ? $with_comport_data : $additional_data_options;
require self::block_template_renderer( 'additional-data/weather-icons.php' );
$is_table_layout = in_array( $block_name, array( 'table', 'tabs' ), true );

?>

<?php if ( ! $is_table_layout ) : ?>
<div class="spl-weather-card-daily-details spl-weather-details-<?php echo esc_attr( $additional_data_layout ); ?>">
	<div class="spl-weather-details-wrapper<?php echo $display_comport_layout ? ' spl-weather-comport-data-enabled' : ''; ?>">
		<?php require self::block_template_renderer( 'additional-data/regular-layout.php' ); ?>
		<?php require self::block_template_renderer( 'additional-data/swiper-layout.php' ); ?>
	</div>
</div>
<?php else : ?>
<!-- table layout weather details -->
<div class="spl-weather-card-daily-details">
	<div class="spl-weather-details-table-data sp-d-grid sp-grid-cols-2">
		<?php foreach ( $additional_data_options as $option ) : ?>
			<?php require self::block_template_renderer( 'additional-data/single-weather.php' ); ?>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>
