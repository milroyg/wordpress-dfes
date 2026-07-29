<?php

namespace MasterAddons\Inc\Controls;

use \Elementor\Control_Select2;
use MasterAddons\Inc\Classes\Assets_Manager;

if (!defined('ABSPATH')) {
	exit;
};

class JLTMA_Query extends Control_Select2
{

	public function get_type()
	{
		return 'jltma_query';
	}

	public function enqueue()
	{
		Assets_Manager::enqueue('module-editor');
	}

	protected function get_default_settings()
	{
		return array_merge(parent::get_default_settings(), ['query' => '']);
	}

	public function get_default_value()
	{
		return parent::get_default_value();
	}
}
