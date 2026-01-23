<?php

namespace MasterAddons\Modules;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 01/26/2025
 */
if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

if (!class_exists('MasterAddons\Modules\JLTMA_Extension_Which_Element')) {
class JLTMA_Extension_Which_Element
{
	private static $instance = null;
	private static $plugins = [];

	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __construct()
	{
		// Enqueue scripts and styles for editor preview
		\add_action('elementor/preview/enqueue_styles', [$this, 'enqueue_preview_styles']);
		\add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_preview_scripts']);
	}

	/**
	 * Enqueue styles in Elementor preview
	 */
	public function enqueue_preview_styles()
	{
		\wp_add_inline_style('elementor-frontend', $this->get_inline_styles());
	}

	/**
	 * Enqueue scripts in Elementor preview
	 */
	public function enqueue_preview_scripts()
	{
		\wp_localize_script(
			'jquery',
			'jltmaElementsName',
			[
				'widgetPluginMap' => self::get_widget_data()
			]
		);

		// Add inline script for Elements Name functionality
		\wp_add_inline_script('jquery', $this->get_inline_script());
	}

	/**
	 * Get inline CSS for Elements Name
	 */
	private function get_inline_styles()
	{
		return "
.jltma-elements-name__label {
	position: absolute;
	z-index: 999;
	display: none;
	padding: 1px 5px;
	border-radius: 2px;
	background-color: #d30c5c;
	color: #fff;
	text-align: center;
	font-weight: normal;
	font-size: 12px;
}

.jltma-elements-name--labelPosition-top-left .jltma-elements-name__label {
	top: 0;
	left: 0;
}

.jltma-elements-name--showLabelOn-hover .elementor-widget:hover .jltma-elements-name__label {
	display: inline-block;
}
		";
	}

	/**
	 * Get inline JavaScript for Elements Name
	 */
	private function get_inline_script()
	{
		return "
;(function($, elementsName) {
	'use strict';

	$(window).on('elementor/frontend/init', function() {
		var isEditMode = elementorFrontend.config.environmentMode.edit;

		// Only enable in editor mode
		if (!isEditMode) {
			return;
		}

		// Add body classes for styling
		var classes = [
			'jltma-elements-name',
			'jltma-elements-name--labelPosition-top-left',
			'jltma-elements-name--showLabelOn-hover'
		];

		elementorFrontend.elements.\$body.addClass(classes.join(' '));

		// Add labels to widgets
		elementorFrontend.hooks.addAction('frontend/element_ready/widget', function(\$scope) {
			var widgetType = \$scope.data('widget_type').split('.')[0],
				widgetData = elementsName.widgetPluginMap[widgetType] || {},
				pluginName = widgetData.plugin || '',
				widgetName = widgetData.widget || '',
				labelText = pluginName ? pluginName + ' (' + widgetName + ')' : widgetName;

			// Only add label if we have plugin/widget info
			if (pluginName || widgetName) {
				\$scope.append('<span class=\"jltma-elements-name__label\">' + labelText + '</span>');
			}
		});
	});
}(jQuery, jltmaElementsName));
		";
	}

	/**
	 * Get plugin directory name from plugin base
	 *
	 * @param $plugin_base_name
	 * @return bool|string
	 */
	private static function get_plugin_slug($plugin_base_name)
	{
		return substr($plugin_base_name, 0, strpos($plugin_base_name, '/'));
	}

	/**
	 * Get the active plugins.
	 *
	 * @return array
	 */
	protected static function get_active_plugins()
	{
		if (empty(self::$plugins)) {
			// Ensure get_plugins function is loaded
			if (!\function_exists('get_plugins')) {
				include \ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$active_plugins = \get_option('active_plugins');
			self::$plugins = \array_intersect_key(\get_plugins(), \array_flip($active_plugins));
		}
		return self::$plugins;
	}

	/**
	 * Get widget data mapping
	 *
	 * @return array
	 */
	public static function get_widget_data()
	{
		$widget_types = \Elementor\Plugin::instance()->widgets_manager->get_widget_types();
		$data_map = [];
		$plugins = self::get_active_plugins();

		foreach ($widget_types as $widget_key => $widget_data) {
			$reflection = new \ReflectionClass($widget_data);

			$widget_file = \plugin_basename($reflection->getFileName());
			$plugin_slug = self::get_plugin_slug($widget_file);

			foreach ($plugins as $plugin_root => $plugin_meta) {
				$_plugin_slug = self::get_plugin_slug($plugin_root);
				if ($plugin_slug === $_plugin_slug) {
					$data_map[$widget_key] = [
						'plugin' => $plugin_meta['Name'],
						'widget' => $widget_data->get_title()
					];
				}
			}
		}

		return $data_map;
	}
}
}

if (class_exists('MasterAddons\Modules\JLTMA_Extension_Which_Element')) {
	JLTMA_Extension_Which_Element::get_instance();
}
