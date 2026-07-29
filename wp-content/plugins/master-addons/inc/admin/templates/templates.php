<?php

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/8/19
 */

namespace MasterAddons\Inc\Admin\Templates;

use MasterAddons\Inc\Admin\Templates\Includes\Types;
use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Classes\Template_Library_Cache;
use MasterAddons\Inc\Classes\Template_Kit_Cache;

if (!defined('ABSPATH')) exit;


if (!class_exists(__NAMESPACE__ . '\\Master_Templates')) {


	class Master_Templates
	{

		private static $instance = null;
		public $api;
		public $config;
		public $assets;
		public $temp_manager;
		public $types;

		public function __construct()
		{
			// Initialize Template Kits system
			$this->load_template_kits();
			add_action('init', array($this, 'init'));
		}

		public function init()
		{

			$this->load_files();

			$this->set_config();

			$this->set_assets();

			$this->set_api();

			$this->set_types();

			$this->set_templates_manager();
		}

		private function load_files()
		{
			// Template Library (no namespace, needs manual require)
			$plugin_path = defined('JLTMA_PATH') ? JLTMA_PATH : (defined('JLTMA_PRO_PATH') ? JLTMA_PRO_PATH : '');
			require_once $plugin_path . 'inc/admin/templates/library/template-library.php';
		}


		private function set_config()
		{

			$this->config       = new Includes\Classes\Config();
		}


		private function set_assets()
		{

			$this->assets       = new Includes\Classes\Assets();
		}


		private function set_api()
		{

			$this->api       = new Includes\Classes\API();
		}


		private function set_types()
		{

			$this->types        = new Includes\Types\Manager();
		}


		private function set_templates_manager()
		{

			$this->temp_manager = new Includes\Classes\Manager();
		}


		/**
		 * Load Template Kits system
		 */
		private function load_template_kits() {
			Kits\Kits_Loader::get_instance();
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

if (!function_exists('master_addons_templates')) {
	function master_addons_templates()
	{
		return Master_Templates::get_instance();
	}
}
master_addons_templates();
