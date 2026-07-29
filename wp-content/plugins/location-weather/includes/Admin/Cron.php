<?php
/**
 * Cron scheduler for Location Weather events.
 *
 * @package location-weather
 */

namespace ShapedPlugin\Weather\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Cron
 */
class Cron {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );
		add_action( 'wp', array( $this, 'schedule_events' ) );
	}

	/**
	 * Registers new cron schedules
	 *
	 * @since 2.0.0
	 *
	 * @param array $schedules cron schedules.
	 * @return array
	 */
	public function add_schedules( $schedules = array() ) {
		// Adds once weekly to the existing schedules.
		$schedules['weekly'] = array(
			'interval' => WEEK_IN_SECONDS,
			'display'  => __( 'Once Weekly', 'location-weather' ),
		);

		return $schedules;
	}

	/**
	 * Schedules our events
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function schedule_events() {
		$this->weekly_events();
	}

	/**
	 * Schedule weekly events
	 *
	 * @access private
	 * @since 2.0.0
	 * @return void
	 */
	private function weekly_events() {
		if ( ! wp_next_scheduled( 'location_weather_weekly_scheduled_events' ) ) {
			wp_schedule_event( time(), 'weekly', 'location_weather_weekly_scheduled_events' );
		}
	}
}
