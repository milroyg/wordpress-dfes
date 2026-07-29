<?php

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/8/19
 */

namespace MasterAddons\Inc\Admin\Templates\Includes\Documents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Section extends Base
{

	public function get_name()
	{
		return 'master_page';
	}

	public static function get_title()
	{
		return __('Section', 'master-addons' );
	}

	public function has_conditions()
	{
		return false;
	}
}
