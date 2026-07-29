<?php
/**
 * Admin file
 *
 * @package Location_Weather.
 */

namespace ShapedPlugin\Weather;

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 * The admin handler class.
 */
class Admin {

	/**
	 * The Constructor of the class.
	 */
	public function __construct() {
		new Admin\AdminDashboard\Splw_Blocks_Page_Wrapper();
		new Admin\Post_Type();
		new Admin\Admin_Notices();
		new Admin\Scripts();
		new Admin\Updater();
		new Admin\Preview\LW_Preview();
		new Admin\Cron();
		$this->init_filters_actions();
	}

	/**
	 * Initialize WordPress filter hooks
	 *
	 * @return void
	 */
	public function init_filters_actions() {
		add_filter( 'manage_location_weather_posts_columns', array( $this, 'add_lw_shortcode_column' ), 10 );
		add_action( 'manage_location_weather_posts_custom_column', array( $this, 'add_lw_shortcode_form' ), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'admin_publish_update_notice' ) );
		add_action( 'admin_head-edit.php', array( $this, 'render_start_with_block_editor_button' ) );
	}

	/**
	 * Inject a "Start with Block Editor" action button next to the default
	 * "Add New" link on the Manage Weather list table. Clicking it opens
	 * post-new.php for the spl_weather_template (Saved Templates) post type,
	 * which is forced into the block editor by LW_Saved_Templates.
	 *
	 * @return void
	 */
	public function render_start_with_block_editor_button() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'edit-location_weather' !== $screen->id ) {
			return;
		}

		$url   = admin_url( 'post-new.php?post_type=spl_weather_template&splwblock_inserter' );
		$label = __( 'Start with Block Editor', 'location-weather' );
		?>
		<style>
			.splw-start-block-editor.page-title-action {
				background: #1a74e4;
				border-color: #1a74e4;
				color: #fff;
				border-radius: 2px;
				padding: 6px 14px;
				font: 400 13px/18px Roboto, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
				box-shadow: none;
				text-shadow: none;
				margin-left: 10px;
				transition: all .3s;
			}
			.splw-start-block-editor.page-title-action:hover,
			.splw-start-block-editor.page-title-action:focus {
				background: #1565c0;
				border-color: #1565c0;
				color: #fff;
			}
		</style>
		<script>
			( function() {
				document.addEventListener( 'DOMContentLoaded', function() {
					var heading = document.querySelector( '.wrap .wp-heading-inline' );
					if ( ! heading ) {
						return;
					}
					var existing = heading.parentNode.querySelector( '.splw-start-block-editor' );
					if ( existing ) {
						return;
					}
					var anchor = heading.parentNode.querySelector( '.page-title-action' ) || heading;
					var button = document.createElement( 'a' );
					button.href = <?php echo wp_json_encode( esc_url_raw( $url ) ); ?>;
					button.className = 'page-title-action splw-start-block-editor';
					button.textContent = <?php echo wp_json_encode( $label ); ?>;
					anchor.parentNode.insertBefore( button, anchor.nextSibling );
				} );
			} )();
		</script>
		<?php
	}

	/**
	 * ShortCode Column
	 *
	 * @return array
	 */
	public function add_lw_shortcode_column() {
		$new_columns['cb']        = '<input type="checkbox" />';
		$new_columns['title']     = __( 'Title', 'location-weather' );
		$new_columns['shortcode'] = __( 'Shortcode', 'location-weather' );
		$new_columns['layout']    = __( 'Layout', 'location-weather' );
		$new_columns['date']      = __( 'Date', 'location-weather' );

		return $new_columns;
	}

	/**
	 * Display admin columns for the carousels.
	 *
	 * @param mix    $column The columns.
	 * @param string $post_id The post ID.
	 * @return void
	 */
	public function add_lw_shortcode_form( $column, $post_id ) {
		$upload_data = get_post_meta( $post_id, 'sp_location_weather_layout', true );
		$layout      = isset( $upload_data['weather-view'] ) ? $upload_data['weather-view'] : '';

		switch ( $column ) {

			case 'shortcode':
				echo '<div class="splw-after-copy-text"><i class="fa fa-check-circle"></i>  Shortcode  Copied to Clipboard! </div><input class="splw__shortcode" style="width:205px;padding:6px;text-align:left;padding-left:15px;cursor:pointer;" type="text" onClick="this.select();" readonly="readonly" value="[location-weather id=&quot;' . esc_attr( $post_id ) . '&quot;]"/>';
				break;
			case 'layout':
				echo ucwords( str_replace( '-', ' ', $layout ) ); //phpcs:ignore
				break;
			default:
				break;

		} // end switch
	}

	/**
	 * Default button hide from the plugin.
	 *
	 * @param string $messages give custom notice of publish and update.
	 */
	public function admin_publish_update_notice( $messages ) {
		$messages['location_weather'][6] = __( 'The shortcode has been published.', 'location-weather' );
		$messages['location_weather'][1] = __( 'Weather updated.', 'location-weather' );
		return $messages;
	}
}
