<?php

namespace MasterAddons\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use MasterAddons\Inc\Classes\Helper;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Archive_URL extends Data_Tag
{

	public function get_name()
	{
		return 'jltma-archive-url';
	}

	public function get_group()
	{
		return 'archive';
	}

	public function get_categories()
	{
		return [TagsModule::URL_CATEGORY];
	}

	public function get_title()
	{
		return esc_html__('Archive URL', 'master-addons' );
	}

	public function get_panel_template()
	{
		return ' ({{ url }})';
	}

	public function get_value(array $options = [])
	{
		return Helper::jltma_get_the_archive_url();
	}
}
