<?php

namespace MasterAddons\Modules\Utilities;

// Elementor Classes
use \Elementor\Plugin;
use \Elementor\Controls_Manager;

use MasterAddons\Inc\Classes\Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 10/12/19
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Master Addons: Content Reading Progress bar & Scroll Indicator
 */
if (!class_exists('MasterAddons\Modules\Utilities\ReadingProgressBar')) {
class ReadingProgressBar
{

	private static $_instance = null;

	public function __construct()
	{
		add_action('elementor/documents/register_controls', [$this, 'jltma_rpb_register_controls'], 10);
		add_action('wp_footer', [$this, 'jltma_reading_progress_bar_render']);
	}

	public function jltma_rpb_register_controls($element)
	{
		// Skip for popup post types — reading progress bar is irrelevant for popups
		if (method_exists($element, 'get_name') && $element->get_name() === 'jltma_popup') {
			return;
		}

		// Use the document's actual settings tab (Post uses 'content', Library\Page uses 'settings')
		$tabs = $element->get_tabs_controls();
		$tab = isset($tabs[Controls_Manager::TAB_SETTINGS])
			? Controls_Manager::TAB_SETTINGS
			: array_keys($tabs)[0];

		$element->start_controls_section(
			'jltma_reading_progress_bar_section',
			[
				'tab' 			=> $tab,
				'label' 		=> esc_html__('Reading Progress Bar ', 'master-addons' ) . JLTMA_EXTENSION_BADGE
			]
		);

		$element->add_control(
			'jltma_enable_reading_progress_bar',
			[
				'type'  		=> Controls_Manager::SWITCHER,
				'label' 		=> esc_html__('Enable Reading Progress Bar', 'master-addons' ),
				'default' 		=> '',
				'label_on' 		=> esc_html__('Yes', 'master-addons' ),
				'label_off' 	=> esc_html__('No', 'master-addons' ),
				'return_value' 	=> 'yes'
			]
		);


		$element->add_control(
			'jltma_reading_progress_bar_position',
			[
				'label' 		=> esc_html__('Position', 'master-addons' ),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'top',
				'label_block' 	=> false,
				'options' 		=> [
					'top' 		=> esc_html__('Top', 'master-addons' ),
					'bottom' 	=> esc_html__('Bottom', 'master-addons' ),
				],
				'condition' 	=> [
					'jltma_enable_reading_progress_bar' => 'yes',
				],

				'selectors' => [
					'.ma-el-page-scroll-indicator.bottom' => 'top:inherit !important; bottom:0;',
					'.ma-el-page-scroll-indicator.top' => 'top:0px;',
					'.logged-in.admin-bar .ma-el-page-scroll-indicator.top' => 'top:32px;',
				],

			]
		);


		$element->add_control(
			'jltma_reading_progress_bar_height',
			[
				'label' => esc_html__('Height', 'master-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'.ma-el-page-scroll-indicator, .ma-el-scroll-indicator' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'jltma_enable_reading_progress_bar' => 'yes',
				],
			]
		);

		$element->add_control(
			'jltma_reading_progress_bar_bg_color',
			[
				'label' => esc_html__('Background Color', 'master-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'.ma-el-page-scroll-indicator' => 'background: {{VALUE}}',
				],
				'condition' => [
					'jltma_enable_reading_progress_bar' => 'yes',
				],
			]
		);

		$element->add_control(
			'jltma_reading_progress_bar_fill_color',
			[
				'label' => esc_html__('Fill Color', 'master-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#007bff',
				'selectors' => [
					'.ma-el-scroll-indicator' => 'background: {{VALUE}}',
				],
				'condition' => [
					'jltma_enable_reading_progress_bar' => 'yes',
				],
			]
		);

		$element->add_control(
			'jltma_reading_progress_bar_animation_speed',
			[
				'label' => esc_html__('Animation Speed', 'master-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'.ma-el-scroll-indicator' => 'transition: width {{SIZE}}ms ease;',
				],
				'condition' => [
					'jltma_enable_reading_progress_bar' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}


	public function jltma_reading_progress_bar_styles()
	{
		if (did_action('elementor/loaded')) {

			$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');
			$page_settings_model = $page_settings_manager->get_model(get_the_ID());

			$jltma_r_p_b_height  			= $page_settings_model->get_settings('jltma_reading_progress_bar_height');
			$jltma_r_p_b_bg_color  			= $page_settings_model->get_settings('jltma_reading_progress_bar_bg_color');
			$jltma_r_p_b_fill_color  		= $page_settings_model->get_settings('jltma_reading_progress_bar_fill_color');
			$jltma_r_p_b_animation_speed  	= $page_settings_model->get_settings('jltma_reading_progress_bar_animation_speed');
			$jltma_rbp_position  			= $page_settings_model->get_settings('jltma_reading_progress_bar_position');

			// $jltma_r_p_b_custom_css = "";

			$jltma_r_p_b_custom_css = ".ma-el-page-scroll-indicator{ position: sticky;}";
			if ($jltma_r_p_b_fill_color != "") {
				if(!$jltma_r_p_b_bg_color) $jltma_r_p_b_bg_color = 'transparent';
				$jltma_r_p_b_custom_css .= ".ma-el-page-scroll-indicator{ background: {$jltma_r_p_b_bg_color};}
					.ma-el-scroll-indicator{ background: {$jltma_r_p_b_fill_color};}
					.ma-el-page-scroll-indicator, .ma-el-scroll-indicator{ height: {$jltma_r_p_b_height['size']}px;}";
			}

			if (isset($jltma_rbp_position) && $jltma_rbp_position != "") {
				if ($jltma_rbp_position == "top") {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:0px;}';
				} else {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:inherit !important; bottom:0;}';
				}
			}

			if (Helper::jltma_elementor()->editor->is_edit_mode() || Helper::jltma_elementor()->preview->is_preview_mode()) {
				if ($jltma_rbp_position == "top") {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:0px;}';
				} else {
					$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:inherit !important; bottom:0;}';
				}
			}
			// Inline CSS through the enqueue API (late-printed in the footer) instead of a hardcoded <style> tag.
			wp_register_style('jltma-reading-progress-bar', false, array(), JLTMA_VER);
			wp_enqueue_style('jltma-reading-progress-bar');
			wp_add_inline_style('jltma-reading-progress-bar', wp_strip_all_tags($jltma_r_p_b_custom_css));
		}
	}


	public function jltma_reading_progress_bar_render()
	{

		$document = Helper::jltma_elementor()->documents->get(get_the_ID());

		if (!$document) return;

		$reading_progress_bar = $document->get_settings('jltma_enable_reading_progress_bar');

		if (empty($reading_progress_bar)) return;

		if (did_action('elementor/loaded')) {

			$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');
			$page_settings_model = $page_settings_manager->get_model(get_the_ID());

			$scrollbar_position = $page_settings_model->get_settings('jltma_reading_progress_bar_position');
			$jltma_scroll_pos = ($scrollbar_position) ? esc_attr($scrollbar_position) : "";

			if ($page_settings_model->get_settings('jltma_enable_reading_progress_bar') == 'yes') {

				$jltma_rpb_js = '
					(function() {
					    "use strict";
						function initProgressBar() {
							// Remove any existing progress bar
							var existing = document.querySelector(".ma-el-page-scroll-indicator");
							if (existing) existing.remove();

							// Create progress bar directly as child of body
							var progressBar = document.createElement("div");
							progressBar.className = "ma-el-page-scroll-indicator ' . esc_attr($jltma_scroll_pos) . '";
							progressBar.innerHTML = \'<div class="ma-el-scroll-indicator"></div>\';
							document.body.appendChild(progressBar);

							window.onscroll = function () { scrollProgress() };
							scrollProgress(); // Initial call

							function scrollProgress() {
								var indicator = document.querySelector(".ma-el-page-scroll-indicator > .ma-el-scroll-indicator");
								if (!indicator) return;
						        var currentState = document.body.scrollTop || document.documentElement.scrollTop;
						        var pageHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
						        var scrollStatePercentage = pageHeight > 0 ? (currentState / pageHeight) * 100 : 0;
						        indicator.style.width = scrollStatePercentage + "%";
							}
						}

						// Run after DOM is ready
						if (document.readyState === "loading") {
							document.addEventListener("DOMContentLoaded", initProgressBar);
						} else {
							initProgressBar();
						}
					})();
				';
				// Output through the enqueue API (footer) instead of a hardcoded <script> tag.
				wp_register_script('jltma-reading-progress-bar', false, array(), JLTMA_VER, true);
				wp_enqueue_script('jltma-reading-progress-bar');
				wp_add_inline_script('jltma-reading-progress-bar', $jltma_rpb_js);
			} // Enable Progress Bar


			$jltma_r_p_b_height_setting  	= $page_settings_model->get_settings('jltma_reading_progress_bar_height');
			$jltma_r_p_b_height  			= esc_attr($jltma_r_p_b_height_setting['size'] ?? 4);
			$jltma_r_p_b_bg_color  			= esc_attr($page_settings_model->get_settings('jltma_reading_progress_bar_bg_color'));
			$jltma_r_p_b_fill_color  		= esc_attr($page_settings_model->get_settings('jltma_reading_progress_bar_fill_color'));
			$jltma_r_p_b_animation_speed  	= $page_settings_model->get_settings('jltma_reading_progress_bar_animation_speed');
			$jltma_rbp_position  			= sanitize_text_field($page_settings_model->get_settings('jltma_reading_progress_bar_position'));

			$jltma_r_p_b_custom_css = "";
			// Base styles matching Happy Addons' proven approach - high z-index and explicit positioning
			$jltma_r_p_b_custom_css .= ".ma-el-page-scroll-indicator{ position: fixed; left: 0; top: 0; width: 100%; z-index: 999999; opacity: 1 !important; visibility: visible !important;}";
			$jltma_r_p_b_custom_css .= ".ma-el-scroll-indicator{ width: 0%; }";
			if ( $jltma_r_p_b_fill_color != "") {
				if(!$jltma_r_p_b_bg_color) $jltma_r_p_b_bg_color = 'transparent';
				$jltma_r_p_b_custom_css .= ".ma-el-page-scroll-indicator{ background: {$jltma_r_p_b_bg_color};}
					.ma-el-scroll-indicator{ background: {$jltma_r_p_b_fill_color};}
					.ma-el-page-scroll-indicator, .ma-el-scroll-indicator{ height: {$jltma_r_p_b_height}px;}";
			}

			// Position: bottom overrides the default top: 0
			if ($jltma_rbp_position === "bottom") {
				$jltma_r_p_b_custom_css .= '.ma-el-page-scroll-indicator{top:auto; bottom:0;}';
			}

			// Admin bar offset for logged-in users (frontend and editor)
			if ($jltma_rbp_position !== "bottom") {
				$jltma_r_p_b_custom_css .= '.logged-in.admin-bar .ma-el-page-scroll-indicator{top:32px;}';
			}

			// Inline CSS through the enqueue API (late-printed in the footer) instead of a hardcoded <style> tag.
			wp_register_style('jltma-reading-progress-bar', false, array(), JLTMA_VER);
			wp_enqueue_style('jltma-reading-progress-bar');
			wp_add_inline_style('jltma-reading-progress-bar', wp_strip_all_tags($jltma_r_p_b_custom_css));
		}
	}


	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
}

if (class_exists('MasterAddons\Modules\Utilities\ReadingProgressBar')) {
	ReadingProgressBar::instance();
}
