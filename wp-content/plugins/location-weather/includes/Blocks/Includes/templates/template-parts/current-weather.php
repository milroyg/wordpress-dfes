<?php
/**
 * Current Weather Template
 *
 * Displays the current weather information including location name, date, time, and weather icon.
 * The layout adapts based on template settings and display preferences.
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

?>

<div class="spl-weather-card-current-weather sp-d-flex">
	<div class="spl-weather-header-info-wrapper sp-d-flex sp-w-full">
		<!-- location name component -->
		<?php require self::block_template_renderer( 'location-name.php' ); ?>
		<!-- date time component -->
		<?php require self::block_template_renderer( 'date-time.php' ); ?>
	</div>
	<div class="spl-weather-current-weather-icon-wrapper sp-d-flex sp-justify-center">
		<!-- weather image -->
		<?php
			$image_type = 'weather-condition';
			require self::block_template_renderer( 'weather-image.php' );
		?>
		<!-- current temp -->
		<?php require self::block_template_renderer( 'current-temp.php' ); ?>

	</div>
	<?php
	require self::block_template_renderer( 'weather-description.php' );
	?>
</div>
