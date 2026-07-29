<?php

namespace MasterAddons\Inc\Classes;

use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Classes\Notifications\Base\Date;


// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Upgrade to Pro Class
 *
 * Jewel Theme <support@jeweltheme.com>
 */
if (!class_exists('MasterAddons\Inc\Classes\Pro_Upgrade')) {
	class Pro_Upgrade
	{

		use Date;

		public $slug;

		protected $data = array();

		/**
		 * Construct method
		 */
		public function __construct()
		{

			$this->slug = Helper::jltma_slug_cleanup();

			$this->maybe_sync_remote_data();
			$this->register_sync_hook();
			$this->set_data();

			add_action('admin_footer', array($this, 'display_popup'));

			// Add popup to Elementor editor (runs in iframe, needs separate hook)
			add_action('elementor/editor/footer', array($this, 'display_popup'));

			add_action('wp_dashboard_setup', array($this, 'dashboard_widget'), 999);
		}

		/**
		 * Register Dashboard widget
		 *
		 * @return void
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function dashboard_widget()
		{
			
			wp_add_dashboard_widget(
				'jltma_dashboard_widget',
				esc_html__('Master Addons - News & Updates', 'master-addons'),
				array($this, 'dashboard_widget_render')
			);

			// Force widget to very first position on the dashboard
			global $wp_meta_boxes;

			// Get the regular dashboard widgets array
			// (which already has our new widget but appended at the end).
			$default_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

			// Backup and delete our new dashboard widget from the end of the array.
			$default_widget_backup = array( 'jltma_dashboard_widget' => $default_dashboard['jltma_dashboard_widget'] );
			unset( $default_dashboard['jltma_dashboard_widget'] );

			// Merge the two arrays together so our widget is at the beginning.
			$sorted_dashboard = array_merge( $default_widget_backup, $default_dashboard );

			// Save the sorted array back into the original metaboxes .
			$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
		}

		/**
		 * Render dashboard widget
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function dashboard_widget_render()
		{
			// Fetch feed with descriptions (master-addons.com/feed has content, jeweltheme doesn't)
			$feed_items = $this->get_dashboard_feed_items(5);

			echo '<div class="jltma-overview-widget">';

			if (wp_validate_boolean($this->get_content('is_campaign'))) { ?>
				<div class="jltma-dashboard-promo" style="--jltma-popup-color: <?php echo esc_attr($this->get_content('btn_color')); ?>;">
					<a target="_blank" href="<?php echo esc_url($this->get_content('button_url')); ?>">
						<img src="<?php echo esc_url($this->get_content('image_url')); ?>" alt="<?php esc_html__('Master Addons Promo Image', 'master-addons'); ?>" style="width: 100%; height: auto;">
					</a>
					<a class="jltma-popup-button" target="_blank" href="<?php echo esc_url($this->get_content('button_url')); ?>">
						<?php echo esc_html($this->get_content('button_text')); ?>
					</a>
				</div>
			<?php } ?>


			<?php if (!empty($feed_items)) { ?>
				<ul class="jltma-overview-list">
					<?php foreach ($feed_items as $feed_item) { ?>
						<li>
							<a href="<?php echo esc_url($feed_item['link']); ?>" target="_blank">
								<?php echo esc_html($feed_item['title']); ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>

			<div class="jltma-overview-footer">
				<ul>
					<li>
						<a href="https://master-addons.com/blog/" target="_blank">
							<?php echo esc_html__('Blog', 'master-addons'); ?>
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</li>
					<li>
						<a href="https://master-addons.com/pricing" target="_blank">
							<?php echo esc_html__('40% Off Bundle', 'master-addons'); ?>
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</li>
					<li class="jltma-overview-offers">
						<a href="https://master-addons.com/pricing/" target="_blank">
							<?php echo esc_html__('Latest Offers', 'master-addons'); ?>
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</li>
				</ul>
			</div>

			<style>
				/* Master Addons Overview Widget — match WP core dashboard style */
				#jltma_dashboard_widget .inside {
					padding: 0;
					margin: 0;
				}

				.jltma-overview-widget {
					padding: 0;
				}

				.jltma-overview-subtitle {
					margin: 0;
					padding: 12px;
					font-size: 14px;
					font-weight: 600;
					color: #1d2327;
					border-bottom: 1px solid #f0f0f1;
				}

				.jltma-overview-list {
					margin: 12px 0;
					padding: 0;
					list-style: none;
				}

				.jltma-overview-list li {
					padding: 6px 12px;
					margin: 0;
				}

				.jltma-overview-list li:last-child {
					border-bottom: none;
				}

				.jltma-overview-list li a {
					display: block;
					font-size: 13px;
					font-weight: 400;
					line-height: 1.4;
					color: #2271b1;
					text-decoration: none;
					margin-bottom: 4px;
				}

				.jltma-overview-list li a:hover {
					color: #135e96;
					text-decoration: underline;
				}

				.jltma-overview-excerpt {
					margin: 0;
					padding: 0;
					font-size: 13px;
					line-height: 1.5;
					color: #50575e;
				}

				/* Footer */
				.jltma-overview-footer {
					margin: 0;
					padding: 0;
					border-top: 1px solid #f0f0f1;
					background: #f6f7f7;
				}

				.jltma-overview-footer ul {
					display: flex;
					list-style: none;
					margin: 0;
					padding: 0;
				}

				.jltma-overview-footer ul li {
					padding: 0;
					margin: 0;
					border-right: 1px solid #dcdcde;
				}

				.jltma-overview-footer ul li:last-child {
					border-right: none;
				}

				.jltma-overview-footer ul li a {
					display: inline-flex;
					align-items: center;
					gap: 3px;
					padding: 10px 14px;
					font-size: 13px;
					font-weight: 400;
					color: #2271b1;
					text-decoration: none;
				}

				.jltma-overview-footer ul li a:hover {
					color: #135e96;
					text-decoration: underline;
				}

				.jltma-overview-footer ul li a .dashicons {
					font-size: 16px;
					width: 16px;
					height: 16px;
					text-decoration: none;
				}

				.jltma-overview-offers a {
					color: #b32d2e !important;
					font-weight: 500 !important;
				}

				.jltma-overview-offers a:hover {
					color: #a00 !important;
				}
			</style>

		<?php
			echo '</div>';
		}


		/**
		 * Fetch RSS feed items for dashboard widget
		 * Handles master-addons.com/feed/ which has a script tag before XML declaration
		 *
		 * @param int $count Number of items to fetch
		 * @return array Array of items with title, link, excerpt keys
		 */
		public function get_dashboard_feed_items($count = 5)
		{
			$transient_key = 'jltma_dashboard_feed_items';
			$cached = get_transient($transient_key);

			if (false !== $cached) {
				return array_slice($cached, 0, $count);
			}

			$items = array();

			// Try master-addons.com/feed/ first (has descriptions)
			$response = wp_remote_get('https://master-addons.com/feed/', array('timeout' => 10));

			if (!is_wp_error($response)) {
				$body = wp_remote_retrieve_body($response);
				// Strip script tag injected before XML declaration
				$body = preg_replace('/^.*?(<\?xml)/s', '$1', $body);

				// Parse with SimpleXML
				libxml_use_internal_errors(true);
				$xml = simplexml_load_string($body);
				libxml_clear_errors();

				if ($xml && isset($xml->channel->item)) {
					foreach ($xml->channel->item as $xml_item) {
						$title = (string) $xml_item->title;
						$link  = (string) $xml_item->link;
						$desc  = (string) $xml_item->description;
						$desc  = wp_strip_all_tags($desc);
						$excerpt = mb_strlen($desc) > 200 ? mb_substr($desc, 0, 200) . '…' : $desc;

						$items[] = array(
							'title'   => $title,
							'link'    => $link,
							'excerpt' => $excerpt,
						);
					}
				}
			}

			// Fallback to jeweltheme feed (no descriptions)
			if (empty($items)) {
				include_once ABSPATH . WPINC . '/feed.php';
				$rss = fetch_feed('https://master-addons.com/feed/');

				if (!is_wp_error($rss)) {
					$rss_items = $rss->get_items(0, 10);
					foreach ($rss_items as $rss_item) {
						$items[] = array(
							'title'   => $rss_item->get_title(),
							'link'    => $rss_item->get_permalink(),
							'excerpt' => '',
						);
					}
				}
			}

			if (!empty($items)) {
				set_transient($transient_key, $items, 6 * HOUR_IN_SECONDS);
			}

			return array_slice($items, 0, $count);
		}

		/**
		 * Set merged data
		 *
		 * @return void
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function set_data()
		{
			$this->data = Helper::get_merged_data(self::get_data());
		}

		/**
		 * Get Sheet data
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function get_data()
		{
			return get_option('jltma_sheet_promo_data');
		}

		/**
		 * Get Contents
		 *
		 * @param [type] $key .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_content($key)
		{
			if( empty( $this->data ) ) return;
			return $this->data[$key] ?? null;
		}

		/**
		 * Get Option has data
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_data_hash()
		{
			return get_option('jltma_sheet_promo_data_hash');
		}

		/**
		 * Sync to remote data
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function maybe_sync_remote_data()
		{
			$data = self::get_data();

			if (empty($data)) {
				$this->sheet_data_remote_sync();
			}
		}

		/**
		 * Register Sync hook
		 *
		 * @return void
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function register_sync_hook()
		{
			$hook_action = 'jltma_sheet_promo_data_remote_sync';
			add_action($hook_action, array($this, 'sheet_data_remote_sync'));

			if (!wp_next_scheduled($hook_action)) {
				wp_schedule_event(time(), 'daily', $hook_action);
			}

			register_deactivation_hook(JLTMA_FILE, array($this, 'clear_register_sync_hook'));
		}

		/**
		 * Clear register sync hook
		 *
		 * @return void
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function clear_register_sync_hook()
		{
			wp_clear_scheduled_hook('jltma_sheet_promo_data_remote_sync');
		}

		/**
		 * Data sync with remote
		 *
		 * @return void
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function sheet_data_remote_sync()
		{
			$data  = self::get_data();
			$force = empty( $data );

			$remote_data = $this->get_update_remote_data();

			// Don't clobber stored data when the API is unreachable / returned nothing.
			if ( false === $remote_data ) {
				return;
			}

			$stored_hash = $this->get_data_hash();
			$remote_hash = base64_encode( json_encode( $remote_data ) );

			if ( $force || $stored_hash !== $remote_hash ) {
				update_option('jltma_sheet_promo_data', $remote_data);
				update_option('jltma_sheet_promo_data_hash', $remote_hash);
				do_action('jltma_sheet_promo_data_reset');
			}
		}

		/**
		 * Update endpoint URL for this plugin
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_update_url() {
			return esc_url( Helper::api_endpoint() . 'api/plugin/update/' . Helper::jltma_slug_cleanup() );
		}

		/**
		 * Promotional remote data (REST API)
		 *
		 * Expects a single JSON object describing the update, e.g.
		 * { product_name, product_slug, image_url, start_date, end_date,
		 *   button_text, button_url, btn_color, notice, show_for_premium, is_live }
		 *
		 * @return array|false
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_update_remote_data() {
			$response = wp_remote_get(
				$this->get_update_url(),
				array(
					'timeout' => 15,
					'headers' => array( 'Accept' => 'application/json' ),
				)
			);

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				return false;
			}

			$body = wp_remote_retrieve_body( $response );

			if ( ! $body ) {
				return false;
			}

			$data = json_decode( $body, true );

			if ( ! is_array( $data ) || JSON_ERROR_NONE !== json_last_error() ) {
				return false;
			}

			// API may return the update directly, or wrapped (e.g. { data: {...} }) / as a list.
			if ( isset( $data['data'] ) && is_array( $data['data'] ) ) {
				$data = $data['data'];
			}

			if ( isset( $data[0] ) && is_array( $data[0] ) ) {
				$data = wp_list_filter( $data, array( 'product_slug' => Helper::jltma_slug_cleanup() ) );
				$data = $data ? array_values( $data )[0] : array();
			}

			if ( empty( $data ) || empty( $data['product_slug'] ) ) {
				return false;
			}

			// Only accept a update that targets this plugin.
			if ( Helper::jltma_slug_cleanup() !== $data['product_slug'] ) {
				return false;
			}

			// API sends dates as "d/m/Y h:i a" — normalise so PHP/JS date parsers don't choke.
			foreach ( array( 'start_date', 'end_date' ) as $key ) {
				if ( ! empty( $data[ $key ] ) ) {
					$data[ $key ] = $this->normalize_date( $data[ $key ] );
				}
			}

			return $data;
		}

		/**
		 * Display popup contents
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function display_popup()
		{
			if (Helper::jltma_premium()) {
				if (!$this->get_content('show_for_premium')) {
					return;
				}
			}

			if (empty( $this->get_content('is_live') )) {
				$image_url = JLTMA_IMAGE_DIR . 'ma-fallback.png';
				$notice = 'Use "SPECIAL40" to Get Flat 40% OFF';
				$btn_url = 'https://master-addons.com/pricing/';
				$btn_text = 'GET THE DEAL';
			} else {
				$image_url = $this->get_content('image_url');
				$notice = $this->get_content('notice');
				$btn_url = $this->get_content('button_url');
				$btn_text = $this->get_content('button_text');
			}

		?>

			<div class="jltma-popup jltma-upgrade-popup" id="jltma-popup" data-plugin="<?php echo esc_attr($this->slug); ?>" tabindex="1" style="display: none;">

				<div class="jltma-popup-overlay"></div>

				<div class="jltma-popup-modal" style="background-image: url('<?php echo esc_url($image_url); ?>'); --jltma-popup-color: <?php echo esc_attr($this->get_content('btn_color')); ?>;">

					<!-- close  -->
					<div class="jltma-popup-modal-close popup-dismiss">×</div>

					<!-- content section  -->
					<div class="jltma-popup-modal-footer">

						<!-- countdown  -->
						<div class="jltma-popup-countdown" style="display: none;">
							<?php if (!empty($notice)) { ?>
								<span data-counter="notice" style="color:#F4B740; font-size:14px; padding-bottom:20px; font-style:italic;">
									<?php echo esc_html__('Notice:', 'master-addons'); ?> <?php echo esc_html( $notice ); ?>
								</span>
							<?php } ?>
							<span class="jltma-popup-countdown-text"><?php echo esc_html__('Offer Ends In', 'master-addons'); ?></span>
							<div class="jltma-popup-countdown-time">
								<div>
									<span data-counter="days">00</span>
									<span><?php echo esc_html__('Days', 'master-addons'); ?></span>
								</div>
								<span>:</span>
								<div>
									<span data-counter="hours">00</span>
									<span><?php echo esc_html__('Hours', 'master-addons'); ?></span>
								</div>
								<span>:</span>
								<div>
									<span data-counter="minutes">00</span>
									<span><?php echo esc_html__('Minutes', 'master-addons'); ?></span>
								</div>
								<span>:</span>
								<div>
									<span data-counter="seconds">00</span>
									<span><?php echo esc_html__('Seconds', 'master-addons'); ?></span>
								</div>
							</div>
						</div>

						<!-- button  -->
						<a class="jltma-popup-button" target="_blank" href="<?php echo esc_url($btn_url); ?>"><?php echo esc_html($btn_text); ?></a>
					</div>
				</div>
			</div>

			<script>
				var $container = jQuery('#jltma-popup'),
					plugin_data = <?php echo wp_json_encode( $this->data ); ?>,
					events = {}; //Events

				// Update Counter
				function updateCounter(seconds) {
					const $counter = $container.find(".jltma-popup-countdown-time");
					const $days = $counter.find("[data-counter='days']");
					const $hours = $counter.find("[data-counter='hours']");
					const $minutes = $counter.find("[data-counter='minutes']");
					const $seconds = $counter.find("[data-counter='seconds']");
					const days = Math.floor(seconds / (3600 * 24));
					seconds -= days * 3600 * 24;
					const hrs = Math.floor(seconds / 3600);
					seconds -= hrs * 3600;
					const mnts = Math.floor(seconds / 60);
					seconds -= mnts * 60;

					$days.text(days);
					$hours.text(hrs);
					$minutes.text(mnts);
					$seconds.text(seconds);
				}

				// Trigger Event
				function trigger(event, args = []) {
					if (typeof(events[event]) !== 'undefined') {
						events[event].forEach(callback => {
							callback.apply(this, args);
						});
					}
				}

				// initCounter
				function initCounter(last_date) {
					$container.find(".jltma-popup-countdown-time").show();

					const countdown = () => {

						// system time
						const now = new Date().getTime();

						// set end time to 11:59:59 PM
						const endDate = new Date(last_date);
						endDate.setHours(23);
						endDate.setMinutes(59);
						endDate.setSeconds(59);

						const seconds = Math.floor((endDate.getTime() - now) / 1000);

						if (seconds < 0) {
							return false;
						}

						updateCounter(seconds);

						return true;
					}

					let result = countdown();


					if (result) {
						trigger("countdownStart", [plugin_data]);
						$container.find(".jltma-popup-countdown").show(0);
					} else {
						trigger("countdownFinish", [plugin_data]);
						$container.find(".jltma-popup-countdown").hide(0);
					}

					// update counter every 1 second
					const counter = setInterval(() => {

						const result = countdown();

						if (!result) {
							clearInterval(counter);
							trigger("counter_end", [plugin_data]);
							$container.find(".jltma-popup-countdown").hide(0);
						}

					}, 1000);
				}

				initCounter('<?php echo esc_attr($this->counter_date()); ?>');
			</script>

			<?php
		}

		/**
		 * Counter Date
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function counter_date()
		{
			$endDate = $this->get_content('end_date');

			$is_active = $this->date_is_current_or_next($endDate);

			if ($is_active) {
				return $endDate;
			}

			return $this->date_increment($this->current_time(), 3);
		}

		/**
		 * Normalise a update date string to 'Y-m-d H:i:s'
		 *
		 * Accepts d/m/Y (optionally with 12h time) and a few common fallbacks.
		 * Returns the original value untouched if it cannot be parsed.
		 *
		 * @param string $value .
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function normalize_date( $value ) {
			$value   = trim( (string) $value );
			$formats = array( 'd/m/Y h:i a', 'd/m/Y H:i', 'd/m/Y', 'd-m-Y H:i:s', 'd-m-Y' );

			foreach ( $formats as $format ) {
				$dt = \DateTime::createFromFormat( $format, $value );

				if ( $dt instanceof \DateTime ) {
					return $dt->format( 'Y-m-d H:i:s' );
				}
			}

			$ts = strtotime( $value );

			return $ts ? gmdate( 'Y-m-d H:i:s', $ts ) : $value;
		}
	} // End class Pro_Upgrade
} // End class_exists check
