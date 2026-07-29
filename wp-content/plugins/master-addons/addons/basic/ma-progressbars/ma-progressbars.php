<?php

namespace MasterAddons\Addons;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/26/19
 */

use MasterAddons\Inc\Classes\Base\Master_Widget;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Group_Control_Typography;

use MasterAddons\Inc\Admin\Config;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Progress_Bars extends Master_Widget
{
	use \MasterAddons\Inc\Traits\Widget_Notice;
	use \MasterAddons\Inc\Traits\Widget_Assets_Trait;

	public function get_name()
	{
		return 'ma-progressbars';
	}

	public function get_title()
	{
		return esc_html__('Progressbars', 'master-addons' );
	}

	public function get_icon()
	{
		return 'jltma-icon ' . Config::get_addon_icon($this->get_name());
	}


	protected function register_controls()
	{

		$this->start_controls_section(
			'section_stats_bars',
			[
				'label' => __('Stats Bars', 'master-addons' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'stats_title',
			[
				'label'       => __('Stats Title', 'master-addons' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __('The title for the stats bar', 'master-addons' ),
				'default'     => __('My stats title', 'master-addons' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'percentage_value',
			[
				'label'       => __('Percentage Value', 'master-addons' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 100,
				'step'        => 1,
				'default'     => 30,
				'description' => __('The percentage value for the stats.', 'master-addons' ),
			]
		);

		$repeater->add_control(
			'bar_color',
			[
				'label'   => __('Bar Color', 'master-addons' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .jltma-stats-bars {{CURRENT_ITEM}}.jltma-stats-bar .jltma-stats-bar-content' => 'background: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'stats_bars',
			[
				'type' => Controls_Manager::REPEATER,
				'default' => [
					[
						'stats_title' => __('Web Design', 'master-addons' ),
						'percentage_value' => 87,
					],

					[
						'stats_title' => __('SEO Services', 'master-addons' ),
						'percentage_value' => 76,
					],

					[
						'stats_title' => __('Brand Marketing', 'master-addons' ),
						'percentage_value' => 40,
					],
				],
				'fields' 				=> $repeater->get_controls(),
				'title_field' => '{{{ stats_title }}}',
			]
		);

		$this->end_controls_section();

		// Help Docs section (links from config.php)
		$this->jltma_help_docs();

		$this->upgrade_to_pro_message();

		$this->start_controls_section(
			'section_stats_bar_styling',
			[
				'label' => esc_html__('Stats Bar', 'master-addons' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'stats_bar_bg_color',
			[
				'label' => esc_html__('Bar BG Color', 'master-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar .jltma-stats-bar-bg' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'stats_bar_active_bar_bg_color',
			[
				'label' 		=> esc_html__('Active Bar Color', 'master-addons' ),
				'type' 			=> Controls_Manager::COLOR,
				'default'		=> '#704aff',
				'selectors' => [
					'{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar .jltma-stats-bar-content' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'stats_bar_spacing',
			[
				'label' => esc_html__('Vertical Spacing', 'master-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => 18,
				],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 128,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'stats_bar_height',
			[
				'label' 		=> __('Bar Height', 'master-addons' ),
				'type' 			=> Controls_Manager::SLIDER,
				'size_units' 	=> ['px'],
				'default' 		=> [
					'size' 	=> 10,
				],
				'range' 		=> [
					'px' 		=> [
						'min' => 1,
						'max' => 96,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar .jltma-stats-bar-bg, {{WRAPPER}} .jltma-stats-bars .jltma-stats-bar .jltma-stats-bar-content' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar .jltma-stats-bar-bg' => 'margin-top: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'stats_bar_border_radius',
			[
				'label' => __('Stats Bar Border Radius', 'master-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar .jltma-stats-bar-bg, {{WRAPPER}} .jltma-stats-bars .jltma-stats-bar .jltma-stats-bar-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_stats_title',
			[
				'label' => __('Stats Title', 'master-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'stats_title_align',
			[
				'label' 		=> __('Alignment', 'master-addons' ),
				'type' 			=> Controls_Manager::CHOOSE,
				'label_block' 	=> false,
				'options' 		=> [
					'left' 	=> [
						'title' 	=> __('Left', 'master-addons' ),
						'icon' 		=> 'eicon-h-align-left',
					],
					'center' 	=> [
						'title' 	=> __('Default', 'master-addons' ),
						'icon' 		=> 'eicon-h-align-center',
					],
					'right' 		=> [
						'title' 		=> __('Right', 'master-addons' ),
						'icon' 			=> 'eicon-h-align-right',
					],
				],
				'default' 		 => 'left',
				'style_transfer' => true,
				'selectors' => [
					'{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar .jltma-stats-title' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'stats_title_color',
			[
				'label' => __('Color', 'master-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar .jltma-stats-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'stats_title_typography',
				'selector' => '{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar .jltma-stats-title',
			]
		);

		$this->end_controls_section();

		// Stats Percentage
		$this->start_controls_section(
			'section_stats_percentage',
			[
				'label' => __('Stats Percentage', 'master-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'stats_percentage_color',
			[
				'label' => __('Color', 'master-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar span.jltma-stats-percentage' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'stats_percentage_typography',
				'selector' => '{{WRAPPER}} .jltma-stats-bars .jltma-stats-bar span.jltma-stats-percentage',
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{

		$settings = $this->get_settings_for_display();

		$settings = apply_filters('ma_el_stats_bars_' . esc_attr( $this->get_id() ) . '_settings', $settings);

		$output = '<div class="jltma-stats-bars">';

		foreach ($settings['stats_bars'] as $stats_bar) :

			$color_style = '';
			$color = '';
			$bar_color = !empty($stats_bar['bar_color']) ? $stats_bar['bar_color'] : '';
			$stats_bar_active_bar_bg_color = !empty($stats_bar['stats_bar_active_bar_bg_color']) ? $stats_bar['stats_bar_active_bar_bg_color'] : '';

			if(!empty($bar_color)){
				$color = $bar_color;
			}else{
				$color = $stats_bar_active_bar_bg_color;
			}

			if ($color){
				$color_style = ' style="background:' . esc_attr($color) . ';"';
			}

			$child_output = '<div class="jltma-stats-bar elementor-repeater-item-' . esc_attr( $stats_bar['_id'] ) .'">';

			$child_output .= '<div class="jltma-stats-title">';

			$child_output .= $this->parse_text_editor($stats_bar['stats_title']);

			$child_output .= '</div>';

			$child_output .= '<div class="jltma-stats-bar-wrap">';

			$child_output .= '<div ' . esc_attr($color_style) . ' class="jltma-stats-bar-content" data-perc="' . esc_attr($stats_bar['percentage_value']) . '">';
			$child_output .= '<span class="jltma-stats-percentage">' . esc_attr($stats_bar['percentage_value']) . '%</span>';
			$child_output .= '</div>';

			$child_output .= '<div class="jltma-stats-bar-bg"></div>';

			$child_output .= '</div>';

			$child_output .= '</div><!-- .jltma-stats-bar -->';

			$output .= apply_filters('ma_el_stats_bar_output', $child_output, $stats_bar, $settings);

		endforeach;

		$output .= '</div><!-- .jltma-stats-bars -->';

		echo apply_filters('ma_el_stats_bars_output', $output, $settings); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $output is plugin-constructed HTML built with escaped values; filter follows WP convention for HTML output filters
	}

	protected function content_template()
	{
	}
}
