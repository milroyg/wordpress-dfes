<?php

namespace MasterAddons\Inc\Classes\Notifications;

use MasterAddons\Inc\Classes\Helper;

// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Promo Pointer Notification
 *
 * WordPress Pointer-style promotional notification that appears
 * attached to the Master Addons menu item.
 *
 * Jewel Theme <support@jeweltheme.com>
 */
class Promo_Pointer
{
	/**
	 * Pointer ID for tracking dismissal
	 *
	 * @var string
	 */
	private $pointer_id = 'jltma_promo_pointer';

	/**
	 * Transient key for dismissal tracking
	 *
	 * @var string
	 */
	private $transient_key = 'jltma_promo_pointer_dismiss';

	/**
	 * Campaign end date/time
	 *
	 * @var string
	 */
	private $campaign_end_date = '11:59:59pm 31st January, 2026';

	/**
	 * Menu selector for the pointer
	 *
	 * @var string
	 */
	private $menu_selector = '#toplevel_page_master-addons-settings';

	/**
	 * Allowed screen IDs where pointer should show
	 *
	 * @var array
	 */
	private $allowed_screens = ['index.php', 'toplevel_page_master-addons-settings'];

	/**
	 * Promo content configuration
	 *
	 * @var array
	 */
	private $promo_config = [];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->set_promo_config();
		$this->init_hooks();
	}

	/**
	 * Set promotional content configuration
	 *
	 * Override this method or use filter to customize content
	 *
	 * @return void
	 */
	protected function set_promo_config()
	{
		$this->promo_config = apply_filters('jltma_promo_pointer_config', [
			'title'       => __('Master Addons: New Year Sale!', 'master-addons'),
			'description' => __('Unlock the full power of Elementor with 70+ advanced widgets. Build faster, design smarter.', 'master-addons'),
			'button_text' => __('Save Up to 50%', 'master-addons'),
			'button_url'  => 'https://master-addons.com/pricing/?utm_source=plugin&utm_medium=pointer&utm_campaign=newyear2026',
		]);
	}

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	private function init_hooks()
	{
		add_action('in_admin_header', [$this, 'render_pointer']);
		add_action('admin_init', [$this, 'handle_dismiss']);
	}

	/**
	 * Check if pointer should be displayed
	 *
	 * @return bool
	 */
	private function should_display()
	{
		// Only for free plan users
		if (Helper::jltma_premium()) {
			return false;
		}

		// Check if campaign has ended
		if (time() > strtotime($this->campaign_end_date)) {
			return false;
		}

		// Check allowed screens
		$current_screen = get_current_screen();
		$current_page = $GLOBALS['pagenow'] ?? '';

		$is_allowed_screen = $current_page === 'index.php' ||
			($current_screen && in_array($current_screen->id, $this->allowed_screens, true));

		if (!$is_allowed_screen) {
			return false;
		}

		// Check if already dismissed
		if (get_transient($this->transient_key)) {
			return false;
		}

		return true;
	}

	/**
	 * Render the pointer notification
	 *
	 * @return void
	 */
	public function render_pointer()
	{
		if (!$this->should_display()) {
			return;
		}

		// Enqueue required scripts
		wp_enqueue_script('jquery');
		wp_enqueue_style('wp-pointer');
		wp_enqueue_script('wp-pointer');

		$pointer_content = $this->get_pointer_content();
		$selector = esc_js($this->menu_selector);
		$nonce = wp_create_nonce('jltma_promo_pointer_dismiss');
?>
		<script>
			jQuery(function($) {
				$('<?php echo $selector; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $selector is already sanitized via esc_js() above ?>').pointer({
					content: <?php echo wp_json_encode($pointer_content); ?>,
					position: {
						edge: 'left',
						align: 'center'
					},
					pointerClass: 'wp-pointer jltma-promo-pointer',
					close: function() {
						$.post(ajaxurl, {
							action: 'jltma_dismiss_promo_pointer',
							_wpnonce: '<?php echo esc_js($nonce); ?>'
						});
					}
				}).pointer('open');

				// Add custom styles
				$('.jltma-promo-pointer').css({
					'max-width': '320px'
				});

				// Handle CTA button click
				$(document).on('click', '.jltma-pointer-cta-btn', function() {
					// Dismiss on CTA click as well
					$.post(ajaxurl, {
						action: 'jltma_dismiss_promo_pointer',
						_wpnonce: '<?php echo esc_js($nonce); ?>'
					});
				});
			});
		</script>
		<style>
			.jltma-promo-pointer .wp-pointer-content h3 {
				background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
				border-color: #6366f1;
				font-weight: 600;
			}

			.jltma-promo-pointer .wp-pointer-content p {
				margin: 1em 0;
				line-height: 1.6;
			}

			.jltma-promo-pointer .jltma-pointer-cta-btn {
				display: inline-block;
				background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
				color: #fff !important;
				padding: 8px 16px;
				border-radius: 4px;
				text-decoration: none;
				font-weight: 600;
				transition: all 0.3s ease;
			}

			.jltma-promo-pointer .jltma-pointer-cta-btn:hover {
				transform: translateY(-1px);
				box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
			}

			.jltma-promo-pointer .wp-pointer-arrow-inner {
				border-right-color: #6366f1;
			}
		</style>
<?php
	}

	/**
	 * Get pointer HTML content
	 *
	 * @return string
	 */
	private function get_pointer_content()
	{
		$title = esc_html($this->promo_config['title']);
		$description = esc_html($this->promo_config['description']);
		$button_text = esc_html($this->promo_config['button_text']);
		$button_url = esc_url($this->promo_config['button_url']);

		return sprintf(
			'<h3 style="font-weight: 600;">%s</h3><p style="margin: 1em 0;">%s</p><p><a class="jltma-pointer-cta-btn" href="%s" target="_blank">%s</a></p>',
			$title,
			$description,
			$button_url,
			$button_text
		);
	}

	/**
	 * Handle pointer dismissal via AJAX
	 *
	 * @return void
	 */
	public function handle_dismiss()
	{
		// Handle standard WP pointer dismiss
		if (
			isset($_POST['action']) &&
			'dismiss-wp-pointer' === $_POST['action'] &&
			isset($_POST['pointer']) &&
			$this->pointer_id === $_POST['pointer']
		) {
			set_transient($this->transient_key, true, DAY_IN_SECONDS * 30);
			return;
		}

		// Handle custom dismiss action
		if (isset($_POST['action']) && 'jltma_dismiss_promo_pointer' === $_POST['action']) {
			if (!wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ), 'jltma_promo_pointer_dismiss')) {
				return;
			}
			set_transient($this->transient_key, true, DAY_IN_SECONDS * 30);
			wp_send_json_success();
		}
	}

	/**
	 * Update campaign end date
	 *
	 * @param string $date Date string parseable by strtotime()
	 * @return void
	 */
	public function set_campaign_end_date($date)
	{
		$this->campaign_end_date = $date;
	}

	/**
	 * Update promo configuration
	 *
	 * @param array $config Configuration array with title, description, button_text, button_url
	 * @return void
	 */
	public function set_promo_config_values($config)
	{
		$this->promo_config = array_merge($this->promo_config, $config);
	}

	/**
	 * Add additional allowed screen
	 *
	 * @param string $screen_id Screen ID to allow
	 * @return void
	 */
	public function add_allowed_screen($screen_id)
	{
		$this->allowed_screens[] = $screen_id;
	}
}
