<?php

namespace MasterAddons\Inc\Admin\Templates\Includes\Classes;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/8/19
 */



if (!defined('ABSPATH')) exit; // No access of directly access

if (!class_exists(__NAMESPACE__ . '\\Config')) {

	class Config
	{

		private static $instance = null;
		private $config;
		private $slug = 'master-addons-pro-license';
		public function __construct()
		{
			$this->config = array(
				'master_addons_templates'       => esc_html__('Master Addons', 'master-addons'),
				'key'                           => $this->get_license_key(),
				'status'                        => $this->get_license_status(),
				'license_page'                  => $this->get_license_page(),
				'pro_message'                   => $this->get_pro_message(),
				'banner'            => array(
					'enabled'    => true,
					'url'        => 'https://master-addons.com/pricing/?utm_source=dashboard&utm_medium=template_preview&utm_campaign=pricing&utm_content=buy_now&utm_term=upgarde-to-pro',
					'image'      => JLTMA_URL . '/assets/images/banner.png',
					'alt'        => 'Master Addons Template Library - 530+ Redefined Designs',
					'target'     => '_blank'
				),
				'api'               => array(
					'enabled'   => true,
					'base'      => 'https://el.master-addons.com/',
					'path'      => 'wp-json/masteraddons/v2',
					'endpoints' => array(
						'templates'  => '/templates/',
						'keywords'   => '/keywords/',
						'categories' => '/categories/',
						'template'   => '/template/',
						'info'       => '/info/',
					),
				)
			);
		}


		/**
		 * Check if user has an active paid license via Freemius.
		 */
		private function has_active_license()
		{
			if (function_exists('ma_el_fs')) {
				return ma_el_fs()->can_use_premium_code();
			}
			return false;
		}

		public function get_license_key()
		{
			if (!$this->has_active_license()) {
				$key = add_query_arg(array('page'  => $this->slug,), esc_url(admin_url('admin.php?page=master-addons-account')));
			} else {
				$key = "";
			}

			return $key;
		}


		public function get_license_status()
		{
			return $this->has_active_license() ? 'valid' : 'invalid';
		}


		public function get_license_page()
		{
			if ($this->has_active_license()) {
				return esc_url(admin_url('admin.php?page=master-addons-settings-account'));
			}
			return 'https://master-addons.com/pricing/?utm_source=dashboard&utm_medium=template_preview&utm_campaign=pricing&utm_content=buy_now&utm_term=upgrade-to-pro';
		}


		public function get_pro_message()
		{
			return __('Upgrade to Pro', 'master-addons');
		}

		public function get_plugin_status($plugin_file)
		{
			if (is_plugin_active($plugin_file)) {
				return 'active';
			}

			// Check if plugin is installed but not active
			if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
				return 'installed';
			}

			return 'not_installed';
		}



		public function get($key = '')
		{

			return isset($this->config[$key]) ? $this->config[$key] : false;
		}



		public static function get_instance()
		{

			if (self::$instance == null) {

				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}
