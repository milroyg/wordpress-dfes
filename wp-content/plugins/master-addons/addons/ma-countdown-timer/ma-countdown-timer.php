<?php

namespace MasterAddons\Addons;

/**
 * Author Name: Liton Arefin
 * Author URL : https: //jeweltheme.com
 * Date       : 6/27/19
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Text_Shadow;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class JLTMA_Countdown_Timer extends Widget_Base
{
	use \MasterAddons\Inc\Traits\Widget_Notice;

	public function get_name()
	{
		return 'ma-el-countdown-timer';
	}
	public function get_title()
	{
		return esc_html__('Countdown Timer', 'master-addons' );
	}
	public function get_icon()
	{
		return 'jltma-icon eicon-countdown';
	}
	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_script_depends()
	{
		return ['master-addons-countdown'];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/countdown-timer/';
	}

	protected function is_dynamic_content(): bool
	{
		return false;
	}

	protected function register_controls()
	{

		/**
		 * Master Addons: Countdown Timer Settings
		 */
		$this->start_controls_section(
			'ma_el_section_countdown_settings_general',
			[
				'label' => esc_html__('Timer', 'master-addons' )
			]
		);


		$this->add_control(
			'ma_el_countdown_time',
			[
				'label'       => esc_html__('Countdown Date & Time', 'master-addons' ),
				'type'        => Controls_Manager::DATE_TIME,
				'default'     => date("Y-m-d H:i:s", strtotime("+ 52 week")),
				'description' => esc_html__('Set Datetime here', 'master-addons' ),
			]
		);
		// Repeat countdown feature - Pro version overrides via filter
		$repeat_countdown_controls = apply_filters('master_addons/addons/countdown/repeat', [
			[
				'control_id' => 'ma_el_countdown_repeat_pro',
				'args'       => [
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => Master_Addons_Helper::upgrade_to_pro('Repeat Countdown available on'),
					'content_classes' => 'jltma-editor-doc-links',
				],
			],
		]);

		// Add all controls from the filter
		foreach ($repeat_countdown_controls as $control) {
			$this->add_control($control['control_id'], $control['args']);
		}
		

		$this->end_controls_section();


		$this->start_controls_section(
			'ma_el_countdown_settings_general',
			[
				'label' => esc_html__('Countdown Settings', 'master-addons' )
			]
		);

		$this->add_control(
			'ma_el_countdown_style',
			[
				'label'   => esc_html__('Style Preset', 'master-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'block',
				'options' => [
					'block'         => esc_html__('Block', 'master-addons' ),
					'inline'        => esc_html__('Inline', 'master-addons' ),
					'block-table'   => esc_html__('Block Table', 'master-addons' ),
					'inline-table+' => esc_html__('Inline Table', 'master-addons' ),
				],
			]
		);

		$this->add_control(
			'ma_el_seperator',
			array(
				'label'   => __('Seperator', 'master-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '/',
				'condition'   => [
					'ma_el_countdown_style!' => 'inline-table+',
				],
			)
		);


		$this->add_control(
			'ma_el_show_year',
			array(
				'label'        => __('Show Years?', 'master-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', 'master-addons' ),
				'label_off'    => __('Off', 'master-addons' ),
				'return_value' => '1',
				'default'      => '1'
			)
		);

		$this->add_control(
			'ma_el_label_year',
			array(
				'label'        => __('Years Label', 'master-addons' ),
				'type'         => Controls_Manager::TEXT,
				'default' => esc_html__( 'Years', 'master-addons' ),
				'description' => __('Set the label for years.', 'master-addons'),
				'condition'   => [
					'ma_el_show_year' => '1',
				],
			)
		);

		$this->add_control(
			'ma_el_show_month',
			array(
				'label'        => __('Show Month?', 'master-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', 'master-addons' ),
				'label_off'    => __('Off', 'master-addons' ),
				'return_value' => '1',
				'default'      => '1'
			)
		);

		$this->add_control(
			'ma_el_label_month',
			array(
				'label'        => __('Month Label', 'master-addons' ),
				'type'         => Controls_Manager::TEXT,
				'default' => esc_html__( 'Month', 'master-addons' ),
				'description' => __('Set the label for month.', 'master-addons'),
				'condition'   => [
					'ma_el_show_month' => '1',
				],
			)
		);

		$this->add_control(
			'ma_el_show_day',
			array(
				'label'        => __('Show Days?', 'master-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', 'master-addons' ),
				'label_off'    => __('Off', 'master-addons' ),
				'return_value' => '1',
				'default'      => '1'
			)
		);

		$this->add_control(
			'ma_el_label_day',
			array(
				'label'        => __('Days Label', 'master-addons' ),
				'type'         => Controls_Manager::TEXT,
				'default' => esc_html__( 'Days', 'master-addons' ),
				'description' => __('Set the label for days.', 'master-addons'),
				'condition'   => [
					'ma_el_show_day' => '1',
				],
			)
		);

		$this->add_control(
			'ma_el_show_hour',
			array(
				'label'        => __('Show Hours?', 'master-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', 'master-addons' ),
				'label_off'    => __('Off', 'master-addons' ),
				'return_value' => '1',
				'default'      => '1'
			)
		);

		$this->add_control(
			'ma_el_label_hour',
			array(
				'label'        => __('Hour Label', 'master-addons' ),
				'type'         => Controls_Manager::TEXT,
				'default' => esc_html__( 'Hours', 'master-addons' ),
				'description' => __('Set the label for hours.', 'master-addons'),
				'condition'   => [
					'ma_el_show_hour' => '1',
				],
			)
		);

		$this->add_control(
			'ma_el_show_min',
			array(
				'label'        => __('Show Minutes?', 'master-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', 'master-addons' ),
				'label_off'    => __('Off', 'master-addons' ),
				'return_value' => '1',
				'default'      => '1'
			)
		);

		$this->add_control(
			'ma_el_label_min',
			array(
				'label'        => __('Minutes Label', 'master-addons' ),
				'type'         => Controls_Manager::TEXT,
				'default' => esc_html__( 'Minutes', 'master-addons' ),
				'description' => __('Set the label for minutes.', 'master-addons'),
				'condition'   => [
					'ma_el_show_min' => '1',
				],
			)
		);

		$this->add_control(
			'ma_el_show_sec',
			array(
				'label'        => __('Show Seconds?', 'master-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('On', 'master-addons' ),
				'label_off'    => __('Off', 'master-addons' ),
				'return_value' => '1',
				'default'      => '1'
			)
		);

		$this->add_control(
			'ma_el_label_sec',
			array(
				'label'        => __('Seconds Label', 'master-addons' ),
				'type'         => Controls_Manager::TEXT,
				'default' => esc_html__( 'Seconds', 'master-addons' ),
				'description' => __('Set the label for seconds.', 'master-addons'),
				'condition'   => [
					'ma_el_show_sec' => '1',
				],
			)
		);

		$this->end_controls_section();


		/*
			 * Countdown Timer Styling Section
			 */

		$this->start_controls_section(
			'ma_el_section_countdown_item_wrapper',
			[
				'label' => esc_html__('Common Style', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'jltma_countdown_item_width',
			[
				'label' => esc_html__( 'Width', 'master-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);		
		
		$this->add_control(
			'jltma_countdown_item_height',
			[
				'label' => esc_html__( 'Height', 'master-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_countdown_alignment',
			[
				'label'       => esc_html__('Alignment', 'master-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => Master_Addons_Helper::jltma_content_alignment(),
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .jltma-widget-countdown .jltma-countdown-wrapper.jltma-countdown-block' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'jltma_section_item_box_shadow',
				'label'    => __('Box Shadow', 'master-addons' ),
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'ma_el_section_item_wrapper_background',
				'label'    => __('Background', 'master-addons' ),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item'
			]
		);


		$this->add_responsive_control(
			'ma_el_item_wrapper_border_radius',
			array(
				'label'      => __('Border Radius', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				)
			)
		);

		$this->add_responsive_control(
			'ma_el_item_wrapper_margin',
			array(
				'label'      => __('Margin', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				)
			)
		);

		$this->add_responsive_control(
			'ma_el_item_wrapper_padding',
			array(
				'label'      => __('Padding', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				)
			)
		);

		$this->end_controls_section();


		/*
			* Value Style
			*/

		$this->start_controls_section(
			'ma_el_value_style_section',
			array(
				'label' => __('Counter Style', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			)
		);


	// Year Item
		$this->add_control(
			'jltma_countdown_item_yrs_bg_heading',
			[
				'label' => __('Years Background', 'master-addons'),
				'type' => Controls_Manager::HEADING,
				'condition'   => [
					'ma_el_show_year' => '1',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'jltma_countdown_item_yrs_bg',
				'label'    => __('Years Background', 'master-addons' ),
				'types'    => ['classic', 'gradient'],
				'separator' => 'after',
				'condition'   => [
					'ma_el_show_year' => '1',
				],
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item-year'
			]
		);

		// Month Item
		$this->add_control(
			'jltma_countdown_item_month_bg_heading',
			[
				'label' => __('Month Background', 'master-addons'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'ma_el_show_month' => '1',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'jltma_countdown_item_month_bg',
				'label'    => __('Month Background', 'master-addons' ),
				'types'    => ['classic', 'gradient'],
				'separator' => 'after',
				'condition'   => [
					'ma_el_show_month' => '1',
				],
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item-month'
			]
		);

		// Day Item
		$this->add_control(
			'jltma_countdown_item_day_bg_heading',
			[
				'label' => __('Day Background', 'master-addons'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'ma_el_show_day' => '1',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'jltma_countdown_item_day_bg',
				'label'    => __('Day Background', 'master-addons' ),
				'types'    => ['classic', 'gradient'],
				'separator' => 'after',
				'condition'   => [
					'ma_el_show_day' => '1',
				],
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item-day'
			]
		);

		// Hour Item
		$this->add_control(
			'jltma_countdown_item_hour_bg_heading',
			[
				'label' => __('Hour Background', 'master-addons'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'ma_el_show_hour' => '1',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'jltma_countdown_item_hour_bg',
				'label'    => __('Hour Background', 'master-addons' ),
				'types'    => ['classic', 'gradient'],
				'separator' => 'after',
				'condition'   => [
					'ma_el_show_hour' => '1',
				],
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item-hour'
			]
		);

		// Minute Item
		$this->add_control(
			'jltma_countdown_item_mins_bg_heading',
			[
				'label' => __('Minute Background', 'master-addons'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'ma_el_show_min' => '1',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'jltma_countdown_item_mins_bg',
				'label'    => __('Minute Background', 'master-addons' ),
				'types'    => ['classic', 'gradient'],
				'separator' => 'after',
				'condition'   => [
					'ma_el_show_min' => '1',
				],
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item-min'
			]
		);

		// Second Item
		$this->add_control(
			'jltma_countdown_item_sec_bg_heading',
			[
				'label' => __('Second Background', 'master-addons'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'ma_el_show_sec' => '1',
				],				
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'jltma_countdown_item_sec_bg',
				'label'    => __('Second Background', 'master-addons' ),
				'types'    => ['classic', 'gradient'],
				'separator' => 'after',
				'condition'   => [
					'ma_el_show_sec' => '1',
				],
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-item-sec'
			]
		);


		$this->start_controls_tabs('ma_el_value_colors', array( 'separator' => 'before', ));

		$this->start_controls_tab(
			'ma_el_value_color_normal',
			array(
				'label' => __('Normal', 'master-addons' ),
			)
		);

		$this->add_control(
			'ma_el_value_color',
			array(
				'label'     => __('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-value' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_value_color_hover',
			array(
				'label' => __('Hover', 'master-addons' ),
			)
		);

		$this->add_control(
			'ma_el_value_hover_color',
			array(
				'label'     => __('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-value:hover' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ma_el_value_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-value',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'ma_el_value_shadow',
				'label'    => __('Text Shadow', 'master-addons' ),
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-value',
			]
		);

		$this->end_controls_section();


		/*
			* Separator Style
			*/


		$this->start_controls_section(
			'ma_el_seperator_style_section',
			array(
				'label' => __('Seperator', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			)
		);

		$this->start_controls_tabs('ma_el_seperator_colors');

		$this->start_controls_tab(
			'ma_el_seperator_color_normal',
			array(
				'label' => __('Normal', 'master-addons' ),
			)
		);

		$this->add_control(
			'ma_el_seperator_color',
			array(
				'label'     => __('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-seperator' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_seperator_color_hover',
			array(
				'label' => __('Hover', 'master-addons' ),
			)
		);

		$this->add_control(
			'ma_el_seperator_hover_color',
			array(
				'label'     => __('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-seperator:hover' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ma_el_seperator_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-seperator',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'ma_el_seperator_shadow',
				'label'    => __('Text Shadow', 'master-addons' ),
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-seperator',
			]
		);

		$this->add_responsive_control(
			'ma_el_seperator_padding',
			array(
				'label'      => __('Padding', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-countdown-wrapper.jltma-countdown-inline .jltma-countdown-seperator' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'   => array(
					'ma_el_countdown_style' => ['inline']
				)
			)
		);

		$this->add_responsive_control(
			'ma_el_seperator_margin',
			array(
				'label'      => __('Margin', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-seperator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				)
			)
		);

		$this->end_controls_section();








		/*
			* Box Style
			*/


		$this->start_controls_section(
			'ma_el_section_countdown_box_style',
			[
				'label'     => esc_html__('Box Style', 'master-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_countdown_preset' => 'block',
				],
			]
		);



		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ma_el_countdown_background',
				'label'     => __('Background', 'master-addons' ),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .jltma-countdown.block .jltma-countdown-container',
				'condition' => [
					'ma_el_countdown_preset' => 'block',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'box_shadow',
				'label'     => __('Box Shadow', 'master-addons' ),
				'selector'  => '{{WRAPPER}} .jltma-countdown-container',
				'condition' => [
					'ma_el_countdown_preset' => 'block',
				],
			]
		);

		$this->add_control(
			'ma_el_before_border',
			[
				'type'      => Controls_Manager::DIVIDER,
				'style'     => 'thin',
				'condition' => [
					'ma_el_countdown_preset' => 'block',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'border',
				'label'     => __('Border', 'master-addons' ),
				'selector'  => '{{WRAPPER}} .jltma-countdown.block .jltma-countdown-container',
				'condition' => [
					'ma_el_countdown_preset' => 'style-1',
				],
			]
		);

		$this->add_control(
			'ma_el_countdown_image_border_radius',
			[
				'label'     => esc_html__('Border Radius', 'master-addons' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .jltma-countdown.style-1 .jltma-countdown-container' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
				'default' => [
					'top'      => 4,
					'right'    => 4,
					'bottom'   => 4,
					'left'     => 4,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'condition' => [
					'ma_el_countdown_preset' => 'style-1',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'ma_el_title_style_section',
			array(
				'label' => __('Label', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			)
		);

		$this->start_controls_tabs('ma_el_title_colors');

		$this->start_controls_tab(
			'ma_el_title_color_normal',
			array(
				'label' => __('Normal', 'master-addons' ),
			)
		);

		$this->add_control(
			'ma_el_title_color',
			array(
				'label'     => __('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-title' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_title_color_hover',
			array(
				'label' => __('Hover', 'master-addons' ),
			)
		);

		$this->add_control(
			'ma_el_title_hover_color',
			array(
				'label'     => __('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-title:hover' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ma_el_title_typography',
                'global'   => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'ma_el_title_shadow',
				'label'    => __('Text Shadow', 'master-addons' ),
				'selector' => '{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-title',
			]
		);

		$this->add_responsive_control(
			'ma_el_title_margin',
			array(
				'label'      => __('Margin', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-countdown-wrapper .jltma-countdown-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				)
			)
		);

		$this->end_controls_section();




		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', 'master-addons' ),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', 'master-addons' ), '<a href="https://master-addons.com/demos/countdown-timer/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', 'master-addons' ), '<a href="https://master-addons.com/docs/addons/count-down-timer/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', 'master-addons' ), '<a href="https://www.youtube.com/watch?v=1lIbOLM9C1I" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();

		$this->upgrade_to_pro_message();

	}





	/**
	 * Calculate current interval end time for repeating countdown
	 * Shows only the interval duration (e.g., 20 seconds) repeatedly until original time is reached
	 */
	private function calculate_current_interval_end($original_datetime, $reset_duration_seconds, $widget_id) {
		// Parse the datetime string properly accounting for WordPress timezone
		$datetime_obj = new \DateTime($original_datetime, wp_timezone());
		$original_timestamp = $datetime_obj->getTimestamp();

		// Get current timestamp in WordPress timezone
		$current_datetime = new \DateTime('now', wp_timezone());
		$current_timestamp = $current_datetime->getTimestamp();

		// If we've passed the original end time, return current time (will show as 0)
		if ($current_timestamp >= $original_timestamp) {
			return $current_timestamp;
		}

		// Get or set the start time for this widget's current interval
		$transient_key = 'jltma_countdown_interval_' . $widget_id;
		$interval_start = get_transient($transient_key);

		// If no interval is set or the interval has expired
		if ($interval_start === false || $current_timestamp >= ($interval_start + $reset_duration_seconds)) {
			// Start a new interval from current time
			$interval_start = $current_timestamp;
			set_transient($transient_key, $interval_start, $reset_duration_seconds + 10);
		}

		// Calculate the end time of the current interval
		// This will be current time + interval duration
		$interval_end = $interval_start + $reset_duration_seconds;

		// Make sure we don't go past the original end time
		if ($interval_end > $original_timestamp) {
			$interval_end = $original_timestamp;
		}

		return $interval_end;
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$countdown_style = $settings['ma_el_countdown_style'];
		$countdown_time  = $settings['ma_el_countdown_time'];
		$seperator       = $settings['ma_el_seperator'];

		$data_value  = '';
		$attr_markup = '';
		$date_attr   = array();
		$data_attr   = '';

		// Check if infinite countdown is enabled
		$is_infinite = !empty($settings['ma_el_countdown_time_infinitive']) && $settings['ma_el_countdown_time_infinitive'] === 'yes';

		// Calculate server-side interval for infinite countdown
		if ($is_infinite) {
			// Calculate reset duration in seconds
			$reset_years = isset($settings['ma_el_countdown_reset_years']) ? intval($settings['ma_el_countdown_reset_years']) : 0;
			$reset_months = isset($settings['ma_el_countdown_reset_months']) ? intval($settings['ma_el_countdown_reset_months']) : 0;
			$reset_days = isset($settings['ma_el_countdown_reset_days']) ? intval($settings['ma_el_countdown_reset_days']) : 0;
			$reset_hours = isset($settings['ma_el_countdown_reset_hour']) ? intval($settings['ma_el_countdown_reset_hour']) : 0;
			$reset_minutes = isset($settings['ma_el_countdown_reset_minute']) ? intval($settings['ma_el_countdown_reset_minute']) : 0;
			$reset_seconds = isset($settings['ma_el_countdown_reset_second']) ? intval($settings['ma_el_countdown_reset_second']) : 0;

			// Convert to seconds (approximate for months/years)
			$reset_duration_seconds = ($reset_years * 365 * 24 * 60 * 60) +
									 ($reset_months * 30 * 24 * 60 * 60) +
									 ($reset_days * 24 * 60 * 60) +
									 ($reset_hours * 60 * 60) +
									 ($reset_minutes * 60) +
									 $reset_seconds;

			// Calculate current interval end time on server
			// Use widget ID to track intervals per widget instance
			$widget_id = $this->get_id();
			$current_interval_end = $this->calculate_current_interval_end($countdown_time, $reset_duration_seconds, $widget_id);

			$datetime_for_display = new \DateTime('@' . $current_interval_end);
			$datetime_for_display->setTimezone(wp_timezone());
			$countdown_time = $datetime_for_display->format('Y-m-d H:i:s');
		}

		$datetime = explode(" ", $countdown_time);

		$date = $datetime[0];
		$time = !empty($datetime[1]) ? $datetime[1] : '';

		$date = explode("-", $date);
		$time = explode(":", $time);


		$date_attr = array(
			'year' => array(
				'value' => $date[0],
				'display' => $settings['ma_el_show_year'],
				'title'   => $settings['ma_el_label_year'] ?? __('Years', 'master-addons'),
			),
			'month' => array(
				'value' => !empty($date[1]) ? $date[1] - 1 : '',
				'display' => $settings['ma_el_show_month'],
				'title'   => $settings['ma_el_label_month'] ?? __('Month', 'master-addons'),
			),
			'day' => array(
				'value' => !empty($date[2]) ? $date[2] : '',
				'display' => $settings['ma_el_show_day'],
				'title'   => $settings['ma_el_label_day'] ?? __('Days', 'master-addons'),
			),
			'hour' => array(
				'value'   => !empty($time[0]) ? $time[0] : '',
				'display' => $settings['ma_el_show_hour'],
				'title'   =>$settings['ma_el_label_hour'] ?? __('Hours', 'master-addons' ),
			),
			'min' => array(
				'value'   => !empty($time[1]) ? $time[1] : '',
				'display' => $settings['ma_el_show_min'],
				'title'   => $settings['ma_el_label_min'] ?? __('Minutes','master-addons'),
			),
			'sec' => array(
				'value'   => !empty($time[2]) ? $time[2] : '',
				'display' => $settings['ma_el_show_sec'],
				'title'   => $settings['ma_el_label_sec'] ?? __('Seconds','master-addons')
			),
		);

		// remove last item separator
		$visible_units = array_filter($date_attr, fn($unit) => !empty($unit['display']));
		$last_unit_key = array_key_last($visible_units);

		foreach ($date_attr as $attr => $key) {

			$this->add_render_attribute(
				'jltma_countdown_keys',
				[
					'data-countdown-' . $attr      => $key['value'],
				]
			);

			if ($key['display']) {
				$is_last_item = ($attr === $last_unit_key);

				$attr_markup .= '<div class="jltma-countdown-item jltma-countdown-item-' . esc_attr( $attr ) . '">';
				$attr_markup .= '<span class="jltma-countdown-value jltma-countdown-' . esc_attr($attr) . '">' . __('0', 'master-addons' ) . '</span>';
				$attr_markup .= ('inline' === $countdown_style || 'inline-table' === $countdown_style) && !empty($seperator) ? '<span class="jltma-countdown-seperator">' . esc_attr($seperator) . '</span>' : '';
				$attr_markup .= '<span class="jltma-countdown-title">' . esc_html($key['title']) . '</span>';
				$attr_markup .= '</div>';

				if (
					!empty($seperator) &&
					('block' === $countdown_style || 'block-table' === $countdown_style) &&
					!$is_last_item
				) {
					$attr_markup .= '<span class="jltma-countdown-seperator">' . esc_html($seperator) . '</span>';
				}
			}
		}
		// Add infinite countdown flag (server handles interval calculation)
		// The current interval end time is already calculated server-side above
		if (!empty($settings['ma_el_countdown_time_infinitive']) && $settings['ma_el_countdown_time_infinitive'] === 'yes') {
			$this->add_render_attribute('jltma_countdown_keys', 'data-countdown-infinite', 'yes');
		}

		$this->add_render_attribute(
			'ma_el_countdown_timer',
			[
				'class'    => [
					'jltma-countdown-wrapper',
					'jltma-countdown',
					'jltma-countdown-' . esc_attr($countdown_style),
				],
				'id' => 'jltma-countdown-' . esc_attr($this->get_id()),
			]
		);

		$output = '<section class="widget-container jltma-widget-countdown"><div ' . $this->get_render_attribute_string('ma_el_countdown_timer')  . ' ' . $this->get_render_attribute_string('jltma_countdown_keys')  . '>';
		$output .= $attr_markup;
		$output .= '</div></section>';

		echo wp_kses_post($output);
	}
}
