<?php

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/8/19
 */

namespace MasterAddons\Inc\Admin\Templates\Includes\Types;

use MasterAddons\Inc\Admin\Templates\Includes\Documents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


if (!class_exists(__NAMESPACE__ . '\\Section')) {

	class Section extends Base
	{

		public function get_id()
		{
			return 'master_section';
		}

		public function get_single_label()
		{
			return __('Section', 'master-addons' );
		}

		public function get_plural_label()
		{
			return __('Sections', 'master-addons' );
		}

		public function get_sources()
		{
			return array('master-api');
		}

		public function get_document_type()
		{
			return Documents\Section::class;
		}

		public function library_settings()
		{

			return array(
				'show_title'    => true,
				'show_keywords' => true,
			);
		}
	}
}
