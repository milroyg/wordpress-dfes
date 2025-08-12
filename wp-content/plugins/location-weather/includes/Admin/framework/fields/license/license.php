<?php
/**
 * Framework license fields file.
 *
 * @link       https://shapedplugin.com/
 * @since      2.0.0
 *
 * @package    location-weather
 * @subpackage location-weather/framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'SPLWT_Field_license' ) ) {
	/**
	 *
	 * Field: license
	 *
	 * @since 3.3.16
	 * @version 3.3.16
	 */
	class SPLWT_Field_license extends SPLWT_Fields {

		/**
		 * Field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render
		 *
		 * @return void
		 */
		public function render() {
			echo wp_kses_post( $this->field_before() );
			?>
				<div class="splwt-lite-license text-center">
					<h3><?php esc_html_e( 'You\'re using Location Weather Lite - No License Needed. Enjoy', 'location-weather' ); ?>! 🙂</h3>
					<p><?php esc_html_e( 'Upgrade to Location Weather Pro and unlock all the features.', 'location-weather' ); ?></p>
					<div class="splwt-lite-license-area">
						<div class="splwt-lite-license-key">
							<div class="splwt-lite-upgrade-button"><a href="https://locationweather.io/pricing/?ref=1" target="_blank"><?php esc_html_e( 'Upgrade To Pro Now', 'location-weather' ); ?></a></div>
						</div>
					</div>
				</div>
				<?php
				echo wp_kses_post( $this->field_after() );
		}
	}
}
