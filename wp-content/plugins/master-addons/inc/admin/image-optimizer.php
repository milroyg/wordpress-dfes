<?php
/**
 * Image Optimizer (Free Version)
 *
 * Shows potential optimization savings in media library with upgrade prompt.
 * Actual optimization functionality is in premium/admin/image-optimizer/
 *
 * @package MasterAddons\Inc\Admin
 * @since 2.1.0
 */

namespace MasterAddons\Inc\Admin;

if (!defined('ABSPATH')) {
	exit;
}

class Image_Optimizer
{
	private static $instance = null;

	/**
	 * Get singleton instance
	 */
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Only show for free users (not premium)
		if ($this->is_premium_active()) {
			return;
		}

		// Enqueue base JS for media library display
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

		// Add display to media library
		add_filter('attachment_fields_to_edit', [$this, 'add_optimization_fields'], 10, 2);
	}

	/**
	 * Enqueue admin assets for free version
	 */
	public function enqueue_admin_assets($hook)
	{
		// Only load on media pages
		$media_pages = ['upload.php', 'post.php', 'post-new.php', 'media-upload.php'];
		if (!in_array($hook, $media_pages) && strpos($hook, 'media') === false) {
			return;
		}

		// Use Assets_Manager for enqueue (single source of truth)
		\MasterAddons\Inc\Classes\Assets_Manager::enqueue('image-optimizer-base');

		// Localize script with settings
		$pricing_url = 'https://master-addons.com/pricing/';

		wp_localize_script('jltma-image-optimizer-base', 'jltmaImageOptimizerBase', [
			'isPremium'  => false,
			'pricingUrl' => $pricing_url,
			'settings'   => [],
			'i18n'       => [
				/* translators: %d is the percentage of savings. */
				'savePercent'    => __('Save ~%d%%', 'master-addons'),
				'autoOptimize'   => __('Auto-optimize images on upload', 'master-addons'),
				'upgradeToPro'   => __('Upgrade to Pro', 'master-addons'),
			],
		]);
	}

	/**
	 * Check if premium is active
	 */
	protected function is_premium_active()
	{
		return function_exists('ma_el_fs') && ma_el_fs()->can_use_premium_code__premium_only();
	}

	/**
	 * Add optimization fields to attachment edit screen
	 */
	public function add_optimization_fields($form_fields, $post)
	{
		if (!wp_attachment_is_image($post->ID)) {
			return $form_fields;
		}

		// Skip on attachment edit page
		global $pagenow;
		if ($pagenow === 'post.php' && isset($_GET['action']) && $_GET['action'] === 'edit' && !wp_doing_ajax()) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only admin context detection, no data processed
			return $form_fields;
		}

		// Get file info for potential savings estimate
		$file_path = get_attached_file($post->ID);
		$file_size = $file_path && file_exists($file_path) ? filesize($file_path) : 0;

		// Estimate potential savings (~60-80% for WebP conversion)
		$savings_percent = wp_rand(65, 85);
		$potential_savings = $file_size > 0 ? round($file_size * ($savings_percent / 100)) : 0;
		$potential_size = $file_size - $potential_savings;

		// Build upsell card
		$html = '<div class="jltma-optimization-card jltma-upsell-card" style="display: block; margin: 8px 0; padding: 10px 12px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fbbf24; border-radius: 8px; font-family: -apple-system, BlinkMacSystemFont, sans-serif;">';

		if ($file_size > 0) {
			$html .= '<div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; font-size: 12px; color: #92400e;">';
			$html .= '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10" stroke="#d97706" stroke-width="2"/><path d="M12 6v6l4 2" stroke="#d97706" stroke-width="2" stroke-linecap="round"/></svg>';
			/* translators: %d is the percentage of savings. */
			$html .= '<span style="font-weight: 600;">' . sprintf(__('Save ~%d%%', 'master-addons'), $savings_percent) . '</span>';
			$html .= '<span style="color: #b45309;">(' . size_format($file_size) . ' → ~' . size_format($potential_size) . ')</span>';
			$html .= '</div>';
		} else {
			$html .= '<div style="font-size: 12px; color: #92400e; font-weight: 600;">';
			$html .= __('Auto-optimize images on upload', 'master-addons');
			$html .= '</div>';
		}

		// Upgrade link
		$pricing_url = function_exists('ma_el_fs') ? ma_el_fs()->get_upgrade_url() : 'https://master-addons.com/pricing/';
		$html .= '<a href="' . esc_url($pricing_url) . '" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; margin-top: 6px; font-size: 11px; font-weight: 600; color: #d97706; text-decoration: none;">';
		$html .= '<span class="dashicons dashicons-star-filled" style="font-size: 12px; width: 12px; height: 12px;"></span>';
		$html .= __('Upgrade to Pro', 'master-addons');
		$html .= '</a>';
		$html .= '</div>';

		$form_fields['jltma_optimization'] = [
			'label' => __('MA Optimization', 'master-addons'),
			'input' => 'html',
			'html'  => $html,
		];

		return $form_fields;
	}
}
