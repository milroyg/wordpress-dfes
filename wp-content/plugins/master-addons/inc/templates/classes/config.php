<?php

namespace MasterAddons\Inc\Templates\Classes;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/8/19
 */



if (!defined('ABSPATH')) exit; // No access of directly access

if (!class_exists('Master_Addons_Templates_Core_Config')) {

	class Master_Addons_Templates_Core_Config
	{

		private static $instance = null;
		private $config;
		private $slug = 'master-addons-pro-license';
		public function __construct()
		{
			$this->config = array(
				'master_addons_templates'       => esc_html__('Master Addons', 'master-addons' ),
				'key'                           => $this->get_license_key(),
				'status'                        => $this->get_license_status(),
				'license_page'                  => $this->get_license_page(),
				'pro_message'                   => $this->get_pro_message(),
				'banner'            => array(
					'enabled'    => true,
					'url'        => 'https://master-addons.com/pricing',
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


		public function get_license_key()
		{
			if (!Master_Addons_Helper::jltma_premium()) {
				$key = add_query_arg(array('page'  => $this->slug,), esc_url(admin_url('admin.php?page=master-addons-account')));
			} else {
				$key = "";
			}

			return $key;
		}


		public function get_license_status()
		{
			if (!Master_Addons_Helper::jltma_premium()) {
				$status = 'valid';
			} else {
				$status = 'invalid';
			}

			return $status;
		}


		public function get_license_page()
		{
			if (!Master_Addons_Helper::jltma_premium()) {
				$theme_slug = Master_Addons_Helper::get_installed_theme();
				$url = sprintf('https://master-addons.com/pricing/?utm_source=master-templates&utm_medium=wp-dash&utm_campaign=get-pro&utm_term=%s', $theme_slug);
				return $url;
			} else {
				return add_query_arg(
					array('page'  => $this->slug),
					esc_url(admin_url('admin.php?page=master-addons-account'))
				);
			}
		}


		public function get_pro_message()
		{
			if (Master_Addons_Helper::jltma_premium()) {
				return __('Get Pro', 'master-addons');
			} else {
				return __('Activate License', 'master-addons');
			}
		}

		public function get_plugin_status($plugin_file) {
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
