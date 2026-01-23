<?php

namespace MasterAddons\Addons;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/26/19
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Stack;
use \Elementor\Utils;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class JLTMA_Infobox extends Widget_Base
{
	use \MasterAddons\Inc\Traits\Widget_Notice;

	public function get_name()
	{
		return 'jltma-infobox';
	}
	public function get_title()
	{
		return esc_html__('Info Box', 'master-addons');
	}
	public function get_icon()
	{
		return 'jltma-icon eicon-info-box';
	}
	public function get_categories()
	{
		return ['master-addons'];
	}
	public function get_style_depends()
	{
		return [
			'font-awesome-5-all',
			'font-awesome-4-shim'
		];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/infobox/';
	}

	protected function is_dynamic_content(): bool
	{
		return false;
	}

	protected function register_controls()
	{

		/*
			* Master Addons: Infobox Image
			*/
		$this->start_controls_section(
			'ma_el_section_infobox_content',
			[
				'label' => esc_html__('Content', 'master-addons')
			]
		);


		// Define default free options - Pro version overrides via filter
		$preset_options = apply_filters('master_addons/addons/infobox/preset', [
			'one'        => esc_html__('Variation One', 'master-addons'),
			'two'        => esc_html__('Variation Two', 'master-addons'),
			'three'      => esc_html__('Variation Three', 'master-addons'),
			'four'       => esc_html__('Variation Four', 'master-addons'),
			'five'       => esc_html__('Variation Five', 'master-addons'),
			'info-pro-1' => esc_html__('Variation Six (Pro)', 'master-addons'),
			'info-pro-2' => esc_html__('Variation Seven (Pro)', 'master-addons'),
			'info-pro-3' => esc_html__('Variation Eight (Pro)', 'master-addons'),
			'info-pro-4' => esc_html__('Variation Nine (Pro)', 'master-addons'),
			'info-pro-5' => esc_html__('Variation Ten (Pro)', 'master-addons'),
		]);

		$this->add_control(
			'ma_el_infobox_preset',
			[
				'label'       => esc_html__('Style Preset', 'master-addons'),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'one',
				'options'     => $preset_options,
				'description' => Master_Addons_Helper::upgrade_to_pro('5+ more Variations on'),
			]
		);

		// Gradient controls - Pro version overrides via filter
		$gradient_controls = apply_filters('master_addons/addons/infobox/gradient', [
			[
				'type'       => 'control',
				'control_id' => 'ma_el_infobox_gradient_pro',
				'args'       => [
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => Master_Addons_Helper::upgrade_to_pro('Gradient Icon Color available on'),
					'content_classes' => 'jltma-editor-doc-links',
					'condition'       => [
						'ma_el_infobox_preset' => 'six'
					],
				],
			],
		]);

		// Add all controls from the filter
		foreach ($gradient_controls as $control) {
			if ($control['type'] === 'control') {
				$this->add_control($control['control_id'], $control['args']);
			} elseif ($control['type'] === 'group_control') {
				$this->add_group_control($control['group_type'], $control['args']);
			}
		}


		$this->add_control(
			'ma_el_infobox_img_or_icon',
			[
				'label' => esc_html__('Image or Icon', 'master-addons'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options' => [
					'none' => [
						'title' => esc_html__('None', 'master-addons'),
						'icon' => 'eicon-ban',
					],
					'icon' => [
						'title' => esc_html__('Icon', 'master-addons'),
						'icon' => 'eicon-icon-box',
					],
					'img' => [
						'title' => esc_html__('Image', 'master-addons'),
						'icon' => 'eicon-image',
					]
				],
				'default' => 'icon',
			]
		);


		$this->add_control(
			'ma_el_infobox_icon',
			[
				'label'         	=> esc_html__('Icon', 'master-addons'),
				'description' 		=> esc_html__('Please choose an icon from the list.', 'master-addons'),
				'type'          	=> Controls_Manager::ICONS,
				// 'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'eicon-tags',
					'library'   => 'eicon',
				],
				'render_type'      => 'template',
				'condition' => [
					'ma_el_infobox_img_or_icon' => 'icon'
				]
			]
		);

		$this->add_control(
			'ma_el_infobox_image',
			[
				'label' => esc_html__('Image', 'master-addons'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'ma_el_infobox_img_or_icon' => 'img'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'condition' => [
					'ma_el_infobox_img_or_icon' => 'img'
				]
			]
		);


		$this->add_control(
			'ma_el_infobox_title',
			[
				'label' => esc_html__('Title', 'master-addons'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('Infobox Title', 'master-addons'),
			]
		);

		$this->add_control(
			'ma_el_infobox_title_link',
			[
				'label' => __('Title URL', 'master-addons'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'master-addons'),
				'label_block' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_description',
			[
				'label' 		=> esc_html__('Description', 'master-addons'),
				'type' 			=> Controls_Manager::TEXTAREA,
				'default' 		=> esc_html__('Basic description about the Infobox', 'master-addons'),
			]
		);

		$this->add_control(
			'ma_el_infobox_readmore_text',
			[
				'label' 		=> esc_html__('Read More Text', 'master-addons'),
				'type' 			=> Controls_Manager::TEXT,
				'label_block' 	=> true,
				'default' 		=> esc_html__('Learn More', 'master-addons'),
				'condition' 	=> [
					'ma_el_infobox_preset' => 'six'
				]
			]
		);

		$this->add_control(
			'ma_el_infobox_readmore_link',
			[
				'label' 		=> __('Read More Link', 'master-addons'),
				'type' 			=> Controls_Manager::URL,
				'placeholder' 	=> __('https://master-addons.com/demo', 'master-addons'),
				'label_block' 	=> true,
				'default' 		=> [
					'url' 			=> '#',
					'is_external' 	=> true,
				],
				'condition' 	=> [
					'ma_el_infobox_preset' => 'six'
				]
			]
		);

		$this->end_controls_section();



		/*
			* Infobox Styling Section
			*/
		$this->start_controls_section(
			'ma_el_section_infobox_styles_preset',
			[
				'label' => esc_html__('General Styles', 'master-addons'),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'info_box_icon_border',
				'label' => __('Box Border', 'master-addons'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .jltma-infobox .jltma-infobox-item',
				'label_block' => true,
			]
		);

		$this->add_control(
			'info_box_border_radius',
			[
				'label' => esc_html__('Border Radius', 'master-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'info_box_padding',
			[
				'label'                 => esc_html__('Padding', 'master-addons'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'info_box_margin',
			[
				'label'                 => esc_html__('Margin', 'master-addons'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		// Variation Nine - 3D Flip Box Height Control
		$this->add_responsive_control(
			'info_box_nine_height',
			[
				'label' => esc_html__('Flip Box Height', 'master-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 600,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 220,
				],
				'selectors' => [
					// Set critical 3D flip layout styles for the container - width 100% needed because children are absolute
					'{{WRAPPER}} .jltma-infobox.nine' => 'width: 100%;',
					'{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item' => 'position: relative; width: 100%; height: {{SIZE}}{{UNIT}}; perspective: 800px; overflow: visible;',
					// Icon (front face) - visible by default, absolutely positioned using left/right instead of width
					'{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item .jltma-infobox-icon' => 'position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; height: {{SIZE}}{{UNIT}} !important; display: flex !important; align-items: center; justify-content: center; padding: 20px; overflow: hidden; transition: all 0.5s; backface-visibility: hidden; z-index: 2; opacity: 1 !important; transform: translateY(0) rotateX(0);',
					// Content (back face) - hidden by default, absolutely positioned using left/right instead of width
					'{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item .jltma-infobox-content' => 'position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; height: {{SIZE}}{{UNIT}} !important; display: flex !important; align-items: center; justify-content: center; padding: 20px; overflow: hidden; transition: all 0.5s; backface-visibility: hidden; z-index: 1; opacity: 0 !important; transform: translateY(110px) rotateX(-90deg) !important;',
					// Inner content positioning - centered with text-align
					'{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item .jltma-inner-content' => 'display: block; position: absolute; top: 50%; left: 0; right: 0; margin: 0 auto; transform: translateY(-50%); text-align: center;',
					// Hover states - flip animation
					'{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item:hover .jltma-infobox-icon' => 'opacity: 0 !important; transform: translateY(-110px) rotateX(90deg) !important;',
					'{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item:hover .jltma-infobox-content' => 'opacity: 1 !important; transform: rotateX(0) !important;',
				],
				'condition' => [
					'ma_el_infobox_preset' => 'nine',
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_bg_heading',
			[
				'label' => __('Background', 'master-addons'),
				'type' => Controls_Manager::HEADING,
			]
		);


		$this->start_controls_tabs('ma_el_infobox_style');

		$this->start_controls_tab(
			'ma_el_infobox_normal',
			[
				'label'                 => __('Normal', 'master-addons'),
			]
		);

		// $this->add_control(
		// 	'ma_el_infobox_bg',
		// 	[
		// 		'label'                 => esc_html__('Background Color', 'master-addons' ),
		// 		'type'                  => Controls_Manager::COLOR,
		// 		'default' => '#fff',
		// 		'selectors'	=> [
		// 			'{{WRAPPER}} .jltma-infobox-item' => 'border-color: {{VALUE}}'
		// 		],
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ma_el_infobox_gradient_bg',
				'label' => __('Background', 'master-addons'),
				'types' => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .jltma-infobox .jltma-infobox-item',

			]
		);


		$this->end_controls_tab();


		$this->start_controls_tab(
			'ma_el_infobox_hover',
			[
				'label'                 => __('Hover', 'master-addons'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ma_el_infobox_hover_gradient_bg',
				'label' => __('Backgroundß', 'master-addons'),
				'types' => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .jltma-infobox .jltma-infobox-item:hover',

			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_control(
			'ma_el_infobox_hover_animation',
			[
				'label'        => __('Hover Animation', 'master-addons'),
				'type'         => Controls_Manager::HOVER_ANIMATION,
				'separator'    => 'before',
				'prefix_class' => 'elementor-animation-',

			]
		);


		$this->end_controls_section();






		// Icon Style
		$this->start_controls_section(
			'section_infobox_icon',
			[
				'label' => __('Icon Style', 'master-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_infobox_img_or_icon' => 'icon'
				]
			]
		);


		$this->add_control(
			'ma_el_infobox_icon_size',
			[
				'label' => __('Icon Size', 'master-addons'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item .jltma-infobox-icon i,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content i,
					{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-icon i,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item .jltma-infobox-icon svg,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content svg,
					{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-icon svg,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					// One, Two, Seven - icon container scales with padding (icon size + 30px padding gives container size)
					'{{WRAPPER}} .jltma-infobox.one .jltma-infobox-item .jltma-infobox-icon,
					{{WRAPPER}} .jltma-infobox.two .jltma-infobox-item .jltma-infobox-icon,
					{{WRAPPER}} .jltma-infobox.seven .jltma-infobox-item .jltma-infobox-icon' => 'display: inline-flex; align-items: center; justify-content: center; height: calc({{SIZE}}{{UNIT}} + 30px); width: calc({{SIZE}}{{UNIT}} + 30px); line-height: calc({{SIZE}}{{UNIT}} + 30px);',
					// Four - icon container scales proportionally (original: font-size 32px → height/width/line-height 90px, ratio 90/32)
					'{{WRAPPER}} .jltma-infobox.four .jltma-infobox-item .jltma-infobox-icon' => 'font-size: {{SIZE}}{{UNIT}}; height: calc({{SIZE}}{{UNIT}} * 90 / 32); width: calc({{SIZE}}{{UNIT}} * 90 / 32); line-height: calc({{SIZE}}{{UNIT}} * 90 / 32);',
					// Five - hexagon container scales proportionally (original: icon 64px → container 180x165)
					'{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content' => 'width: calc({{SIZE}}{{UNIT}} * 180 / 64); height: calc({{SIZE}}{{UNIT}} * 165 / 64);',
					// Five - icon centered using translate to stay centered regardless of icon size, override fixed 80px dimensions from SCSS
					'{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content i,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content svg' => 'left: 50%; top: 50%; transform: translate(-50%, -50%) skewX(-30deg) rotate(30deg); height: auto; width: auto; line-height: normal;',
					// Six - icon container scales proportionally (original: icon 32px → container 90x90, ratio 90/32) with flexbox centering
					'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-icon' => 'height: calc({{SIZE}}{{UNIT}} * 90 / 32); width: calc({{SIZE}}{{UNIT}} * 90 / 32); display: flex; align-items: center; justify-content: center;',
					'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content' => 'display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;',
					// Ten - icon container scales proportionally based on icon size
					'{{WRAPPER}} .jltma-infobox.ten .jltma-infobox-item .jltma-infobox-icon' => 'height: {{SIZE}}{{UNIT}}; width: calc({{SIZE}}{{UNIT}} * 58 / 90); line-height: {{SIZE}}{{UNIT}};',
					// Ten - center icon using translate instead of fixed left/top values
					'{{WRAPPER}} .jltma-infobox.ten .jltma-infobox-item .jltma-infobox-icon i,
					{{WRAPPER}} .jltma-infobox.ten .jltma-infobox-item .jltma-infobox-icon svg,
					{{WRAPPER}} .jltma-infobox.ten .jltma-infobox-item .jltma-infobox-icon img' => 'font-size: {{SIZE}}{{UNIT}}; left: 50% !important; top: 50% !important; transform: translate(-50%, -50%) rotate(-30deg) !important; height: auto; width: auto; line-height: normal;',
				],
			]
		);



		// $this->add_responsive_control(
		// 	'ma_el_infobox_icon_padding',
		// 	[
		// 		'label'         => __('Padding', 'master-addons'),
		// 		'type'          => Controls_Manager::DIMENSIONS,
		// 		'size_units'    => ['px', 'em', '%'],
		// 		'selectors'     => [
		// 			// Generic variations (one, two, three, four, seven) - apply padding to icon container
		// 			'{{WRAPPER}} .jltma-infobox:not(.five):not(.six):not(.eight):not(.nine):not(.ten) .jltma-infobox-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 			// Five - padding on hexagon container (icon is centered with translate)
		// 			'{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 			// Six - padding on icon container (uses flexbox centering)
		// 			'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 			// Eight - padding on hexagon container (icon is centered with translate)
		// 			'{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 			// Ten - padding on hexagon container only (icon is absolutely positioned and centered)
		// 			'{{WRAPPER}} .jltma-infobox.ten .jltma-infobox-item .jltma-infobox-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 	]
		// );

		// Icon Style Tabs
		$this->start_controls_tabs('ma_el_infobox_icon_color_style');

		$this->start_controls_tab(
			'ma_el_infobox_icon_color_normal',
			[
				'label'                 => __('Normal', 'master-addons'),
			]
		);


		$this->add_control(
			'ma_el_infobox_bg_fade_icon_size',
			[
				'label' => __('BG Icon Size', 'master-addons'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item .bg-fade-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item .bg-fade-icon svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ma_el_infobox_preset' => ['two', 'three'],
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_icon_color_scheme',
			[
				'label' => __('Icon Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item .jltma-infobox-icon i,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content i,
					{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-icon i,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item .jltma-infobox-icon svg,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content svg,
					{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-icon svg,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_icon_bg_fade_color_scheme',
			[
				'label' => __('BG Icon Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bg-fade-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bg-fade-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'ma_el_infobox_preset' => ['two', 'three'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ma_el_infobox_icon_bg',
				'label' => __('Background', 'master-addons'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .jltma-infobox:not(.five):not(.six):not(.eight):not(.nine) .jltma-infobox-icon,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content .jltma-hexagon-shape:before,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content .jltma-shape-inner,
					{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-icon,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content .jltma-hexagon-shape:before,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content .jltma-shape-inner,
					{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item .jltma-infobox-icon',
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .jltma-infobox:not(.five):not(.six):not(.eight):not(.nine) .jltma-infobox-icon,
							{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content .jltma-hexagon-shape:before,
							{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content .jltma-shape-inner,
							{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content .jltma-hexagon-shape:before,
							{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item .jltma-infobox-icon .jltma-inner-content .jltma-shape-inner,
							{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item .jltma-infobox-icon' => 'background-color: {{VALUE}} !important;',
							'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-icon' => 'background-image: none; background-color: {{VALUE}} !important;',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_icon_alignment',
			[
				'label'       => esc_html__('Alignment', 'master-addons'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => Master_Addons_Helper::jltma_content_alignment(),
				'default'     => 'center',
				'selectors'   => [
					// Default for most variations (exclude nine - uses absolute positioning for 3D flip)
					'{{WRAPPER}} .jltma-infobox:not(.nine) .jltma-infobox-item .jltma-infobox-icon,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item' => 'text-align: {{VALUE}};',
					// Three - override absolute positioning, use inline-block with text-align
					'{{WRAPPER}} .jltma-infobox.three .jltma-infobox-item' => 'text-align: {{VALUE}} !important;',
					'{{WRAPPER}} .jltma-infobox.three .jltma-infobox-item .jltma-infobox-icon' => 'position: relative !important; left: auto !important; top: auto !important; display: inline-block !important;',
					'{{WRAPPER}} .jltma-infobox.three .jltma-infobox-item .jltma-infobox-content' => 'text-align: center;',
					// Four - override margin: 0 auto, use inline-block with text-align
					'{{WRAPPER}} .jltma-infobox.four .jltma-infobox-item' => 'text-align: {{VALUE}} !important;',
					'{{WRAPPER}} .jltma-infobox.four .jltma-infobox-item .jltma-infobox-icon' => 'display: inline-block !important; margin: 0 !important;',
					'{{WRAPPER}} .jltma-infobox.four .jltma-infobox-item .jltma-infobox-content,
					{{WRAPPER}} .jltma-infobox.four .jltma-infobox-item .jltma-inner-content' => 'text-align: center;',
					// Five uses flexbox - need justify-content
					'{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item' => 'justify-content: {{VALUE}};',
					// Six - set text-align on parent, make icon inline-block
					'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-icon' => 'display: inline-block; margin: 0;',
					// Reset content area to center (preserve default)
					'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-content,
					{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item .jltma-infobox-content .jltma-inner-content' => 'text-align: center;',
				],
			]
		);


		$this->add_responsive_control(
			'ma_el_infobox_bg_fade_icon_margin',
			[
				'label'         => __('Margin', 'master-addons'),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .bg-fade-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ma_el_infobox_preset' => ['two', 'three'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_infobox_icon_hover',
			[
				'label'                 => __('Hover', 'master-addons'),
			]
		);

		$this->add_control(
			'ma_el_infobox_icon_hover_color_scheme',
			[
				'label' => __('Icon Hover Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item:hover .jltma-infobox-icon i,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content i,
					{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item:hover .jltma-infobox-icon i,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item:hover .jltma-infobox-icon svg,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content svg,
					{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item:hover .jltma-infobox-icon svg,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_icon_hover_fade_color_scheme',
			[
				'label' => __('BG Icon Hover Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox-item:hover .bg-fade-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .jltma-infobox-item:hover .bg-fade-icon svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'ma_el_infobox_preset' => ['two', 'three'],
				],
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ma_el_infobox_icon_hover_bg',
				'label' => __('Background', 'master-addons'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .jltma-infobox:not(.five):not(.six):not(.eight):not(.nine) .jltma-infobox-item:hover .jltma-infobox-icon,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content .jltma-hexagon-shape:before,
					{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content .jltma-shape-inner,
					{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item:hover .jltma-infobox-icon,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content .jltma-hexagon-shape:before,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content .jltma-shape-inner,
					{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item:hover .jltma-infobox-icon',
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .jltma-infobox:not(.five):not(.six):not(.eight):not(.nine) .jltma-infobox-item:hover .jltma-infobox-icon,
							{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content .jltma-hexagon-shape:before,
							{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content .jltma-shape-inner,
							{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content .jltma-hexagon-shape:before,
							{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content .jltma-shape-inner,
							{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item:hover .jltma-infobox-icon' => 'background-color: {{VALUE}} !important;',
							'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item:hover .jltma-infobox-icon' => 'background-image: none !important; background-color: {{VALUE}} !important;',
						],
					],
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_icon_bg_fade_hover_color_scheme',
			[
				'label' => __('BG Icon Hover Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item:hover .bg-fade-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-item:hover .bg-fade-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'ma_el_infobox_preset' => ['two', 'three'],
				],
			]
		);


		$this->add_responsive_control(
			'ma_el_infobox_icon_hover_alignment',
			[
				'label'       => esc_html__('Alignment', 'master-addons'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => Master_Addons_Helper::jltma_content_alignment(),
				'default'     => 'center',
				'selectors'   => [
					// Default for most variations (exclude nine - uses absolute positioning for 3D flip)
					'{{WRAPPER}} .jltma-infobox:not(.nine) .jltma-infobox-item:hover .jltma-infobox-icon,
					{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover' => 'text-align: {{VALUE}};',
					// Three - override absolute positioning, use inline-block with text-align
					'{{WRAPPER}} .jltma-infobox.three .jltma-infobox-item:hover' => 'text-align: {{VALUE}} !important;',
					'{{WRAPPER}} .jltma-infobox.three .jltma-infobox-item:hover .jltma-infobox-icon' => 'position: relative !important; left: auto !important; top: auto !important; display: inline-block !important;',
					'{{WRAPPER}} .jltma-infobox.three .jltma-infobox-item:hover .jltma-infobox-content' => 'text-align: center;',
					// Four - override margin: 0 auto, use inline-block with text-align
					'{{WRAPPER}} .jltma-infobox.four .jltma-infobox-item:hover' => 'text-align: {{VALUE}} !important;',
					'{{WRAPPER}} .jltma-infobox.four .jltma-infobox-item:hover .jltma-infobox-icon' => 'display: inline-block !important; margin: 0 !important;',
					'{{WRAPPER}} .jltma-infobox.four .jltma-infobox-item:hover .jltma-infobox-content,
					{{WRAPPER}} .jltma-infobox.four .jltma-infobox-item:hover .jltma-inner-content' => 'text-align: center;',
					// Five uses flexbox
					'{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item:hover' => 'justify-content: {{VALUE}};',
					// Six - set text-align on parent, make icon inline-block
					'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item:hover' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item:hover .jltma-infobox-icon' => 'display: inline-block; margin: 0;',
					// Reset content area to center
					'{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item:hover .jltma-infobox-content,
					{{WRAPPER}} .jltma-infobox.six .jltma-infobox-item:hover .jltma-infobox-content .jltma-inner-content' => 'text-align: center;',
				],
			]
		);

		// $this->add_responsive_control(
		// 	'ma_el_infobox_icon_hover_padding',
		// 	[
		// 		'label'         => __('Padding', 'master-addons'),
		// 		'type'          => Controls_Manager::DIMENSIONS,
		// 		'size_units'    => ['px', 'em', '%'],
		// 		'selectors'     => [
		// 			// All variations - icons are inside .jltma-inner-content (exclude nine - uses absolute positioning for 3D flip)
		// 			'{{WRAPPER}} .jltma-infobox:not(.nine) .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content i,
		// 			{{WRAPPER}} .jltma-infobox:not(.nine) .jltma-infobox-item:hover .jltma-infobox-icon .jltma-inner-content svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 			// Also apply to the icon container (exclude nine)
		// 			'{{WRAPPER}} .jltma-infobox:not(.nine) .jltma-infobox-item:hover .jltma-infobox-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 			// Eight - icons are absolutely positioned, need padding on the icon itself
		// 			'{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover .jltma-infobox-icon i,
		// 			{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover .jltma-infobox-icon svg,
		// 			{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover .jltma-infobox-icon img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
		// 			// Ten - apply padding to icon container and icon elements
		// 			'{{WRAPPER}} .jltma-infobox.ten .jltma-infobox-item:hover .jltma-infobox-icon,
		// 			{{WRAPPER}} .jltma-infobox.ten .jltma-infobox-item:hover .jltma-infobox-icon i,
		// 			{{WRAPPER}} .jltma-infobox.ten .jltma-infobox-item:hover .jltma-infobox-icon svg,
		// 			{{WRAPPER}} .jltma-infobox.ten .jltma-infobox-item:hover .jltma-infobox-icon img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
		// 		],
		// 	]
		// );

		$this->end_controls_tab();

		$this->end_controls_tabs();




		$this->end_controls_section();






		// Title , Description Font Color and Typography
		$this->start_controls_section(
			'section_infobox_title',
			[
				'label' => __('Title', 'master-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_title_typography',
				'selector' => '{{WRAPPER}} .jltma-infobox-content-title',
			]
		);


		$this->start_controls_tabs('ma_el_infobox_title_color_style');

		$this->start_controls_tab(
			'ma_el_infobox_title_color_normal',
			[
				'label'                 => __('Normal', 'master-addons'),
			]
		);

		$this->add_control(
			'ma_el_title_color',
			[
				'label' => __('Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#132c47',
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox-content-title' => 'color: {{VALUE}};',

				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_title_alignment',
			[
				'label' => esc_html__('Alignment', 'master-addons'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => Master_Addons_Helper::jltma_content_alignment(),
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox-content-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_title_padding',
			[
				'label'         => __('Padding', 'master-addons'),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .jltma-infobox-content-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_infobox_title_hover',
			[
				'label'                 => __('Hover', 'master-addons'),
			]
		);


		$this->add_control(
			'ma_el_infobox_title_color_hover',
			[
				'label' => __('Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox-item:hover .jltma-infobox-content-title' => 'color: {{VALUE}};',

				],
			]
		);


		$this->add_responsive_control(
			'ma_el_infobox_title_hover_alignment',
			[
				'label'       => esc_html__('Alignment', 'master-addons'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => Master_Addons_Helper::jltma_content_alignment(),
				'default'     => 'center',
				'selectors'   => [
					'{{WRAPPER}} .jltma-infobox-item:hover .jltma-infobox-content-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_title_hover_padding',
			[
				'label'         => __('Padding', 'master-addons'),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .jltma-infobox-item:hover .jltma-infobox-content-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();



		// Description Style
		$this->start_controls_section(
			'section_infobox_description',
			[
				'label' => __('Description', 'master-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_description_typography',
				'selector' => '{{WRAPPER}} .jltma-infobox-content-description',
			]
		);

		$this->start_controls_tabs('ma_el_infobox_desc_color_style');

		$this->start_controls_tab(
			'ma_el_infobox_desc_color_normal',
			[
				'label'                 => __('Normal', 'master-addons'),
			]
		);

		$this->add_control(
			'ma_el_description_color',
			[
				'label' => __('Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#797c80',
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-content-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_desc_alignment',
			[
				'label'       => esc_html__('Alignment', 'master-addons'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => Master_Addons_Helper::jltma_content_alignment(),
				'default'     => 'center',
				'selectors'   => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-content-description' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_desc_padding',
			[
				'label'         => __('Padding', 'master-addons'),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .jltma-infobox .jltma-infobox-content-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_infobox_desc_hover',
			[
				'label'                 => __('Hover', 'master-addons'),
			]
		);


		$this->add_control(
			'ma_el_infobox_desc_color_hover',
			[
				'label' => __('Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .jltma-infobox-item:hover .jltma-infobox-content-description' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jltma-infobox.four .jltma-infobox-item:hover .jltma-infobox-content-description,
							{{WRAPPER}} .jltma-infobox.five .jltma-infobox-item:hover .jltma-infobox-content-description,
							{{WRAPPER}} .jltma-infobox.seven .jltma-infobox-item:hover .jltma-infobox-content-description,
							{{WRAPPER}} .jltma-infobox.eight .jltma-infobox-item:hover .jltma-infobox-content-description,
							{{WRAPPER}} .jltma-infobox.nine .jltma-infobox-item:hover .jltma-infobox-content-description' =>
					'color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'ma_el_infobox_desc_hover_alignment',
			[
				'label'       => esc_html__('Alignment', 'master-addons'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => Master_Addons_Helper::jltma_content_alignment(),
				'default'     => 'center',
				'selectors'   => [
					'{{WRAPPER}} .jltma-infobox-item:hover .jltma-infobox-content-description' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_desc_hover_padding',
			[
				'label'         => __('Padding', 'master-addons'),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .jltma-infobox-item:hover .jltma-infobox-content-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();





		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', 'master-addons'),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', 'master-addons'), '<a href="https://master-addons.com/demos/infobox/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', 'master-addons'), '<a href="https://master-addons.com/docs/addons/infobox-element/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', 'master-addons'), '<a href="https://www.youtube.com/watch?v=2-ymXAZfrF0" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();

		$this->upgrade_to_pro_message();
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();


		// Read more Link
		// if( $settings['ma_el_infobox_readmore_link']['is_external'] ) {
		// 	$this->add_render_attribute( 'ma_el_infobox_readmore', 'target', '_blank' );
		// }

		// if( $settings['ma_el_infobox_readmore_link']['nofollow'] ) {
		// 	$this->add_render_attribute( 'ma_el_infobox_readmore', 'rel', 'nofollow' );
		// }

		if (!empty($settings['ma_el_infobox_readmore_link']['url'])) {
			$this->add_render_attribute('ma_el_infobox_readmore_link', 'href', $settings['ma_el_infobox_readmore_link']['url']);

			if (!empty($settings['ma_el_infobox_readmore_link']['is_external'])) {
				$this->add_render_attribute('ma_el_infobox_readmore_link', 'target', '_blank');
			}

			if ($settings['ma_el_infobox_readmore_link']['nofollow']) {
				$this->add_render_attribute('ma_el_infobox_readmore_link', 'rel', 'nofollow');
			}
		}


		// Infobox Link
		if ($settings['ma_el_infobox_title_link']['is_external']) {
			$this->add_render_attribute('ma_el_infobox_title_link_attr', 'target', '_blank');
		}

		if ($settings['ma_el_infobox_title_link']['nofollow']) {
			$this->add_render_attribute('ma_el_infobox_title_link_attr', 'rel', 'nofollow');
		}


		$infobox_image = $this->get_settings_for_display('ma_el_infobox_image');
		if (!empty($infobox_image['url'])) {
			$infobox_image_url = Group_Control_Image_Size::get_attachment_image_src($infobox_image['id'], 'thumbnail', $settings);

			if (empty($infobox_image_url)) {
				$infobox_image_url = $infobox_image['url'];
			} else {
				$infobox_image_url = $infobox_image_url;
			}
		}


?>

		<div id="jltma-infobox-<?php echo esc_attr($this->get_id()); ?>" class="jltma-infobox <?php echo esc_attr($settings['ma_el_infobox_preset']); ?>">
			<div class="jltma-infobox-item">

				<?php if ($settings['ma_el_infobox_img_or_icon'] != 'none') : ?>

					<?php if (($settings['ma_el_infobox_preset'] === "two") || ($settings['ma_el_infobox_preset'] === "three")) { ?>
						<div class="bg-fade-icon">
							<?php if ('img' == $settings['ma_el_infobox_img_or_icon']) { ?>
								<img src="<?php echo esc_url($infobox_image_url); ?>" alt="<?php echo get_post_meta($infobox_image['id'], '_wp_attachment_image_alt', true); ?>">
								<?php } else {
								$migrated = isset($settings['__fa4_migrated']['ma_el_infobox_icon']);
								$is_new   = empty($settings['icon']) && \Elementor\Icons_Manager::is_migration_allowed();

								if ($is_new || $migrated) {
									\Elementor\Icons_Manager::render_icon($settings['ma_el_infobox_icon'], ['aria-hidden' => 'true']);
								} else { ?>
									<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
							<?php }
							} ?>
						</div>
					<?php } ?>

					<div class="jltma-infobox-icon <? echo esc_attr(('img' == $settings['ma_el_infobox_img_or_icon']) ? 'image' : ''); ?>">

						<div class="jltma-inner-content">

							<?php if ('icon' == $settings['ma_el_infobox_img_or_icon']) {
								$migrated = isset($settings['__fa4_migrated']['ma_el_infobox_icon']);
								$is_new   = empty($settings['icon']) && \Elementor\Icons_Manager::is_migration_allowed();

								if ($is_new || $migrated) {
									\Elementor\Icons_Manager::render_icon($settings['ma_el_infobox_icon'], ['aria-hidden' => 'true']);
								} else { ?>
									<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
								<?php }
							}

							if ('img' == $settings['ma_el_infobox_img_or_icon']) { ?>
								<img src="<?php echo esc_url($infobox_image_url); ?>" alt="<?php echo get_post_meta($infobox_image['id'], '_wp_attachment_image_alt', true); ?>">
							<?php }

							if ($settings['ma_el_infobox_preset'] == "nine") { ?>

								<?php if ($settings['ma_el_infobox_title_link']['url']) { ?>
									<a href="<?php echo esc_url_raw($settings['ma_el_infobox_title_link']['url']); ?>" <?php echo $this->get_render_attribute_string('ma_el_infobox_title_link_attr'); ?>>
										<h3 class="jltma-infobox-content-title">
											<?php echo $this->parse_text_editor($settings['ma_el_infobox_title']); ?>
										</h3>
									</a>
								<?php } else { ?>
									<h3 class="jltma-infobox-content-title">
										<?php echo $this->parse_text_editor($settings['ma_el_infobox_title']); ?>
									</h3>
								<?php } ?>

							<?php } ?>

							<?php if ($settings['ma_el_infobox_preset'] == "five" || $settings['ma_el_infobox_preset'] == "eight") { ?>
								<div class="jltma-hexagon-shape">
									<div class="jltma-shape-inner"></div>
								</div>
							<?php } ?>
						</div><!-- /.jltma-inner-content -->
					</div>
				<?php endif; ?>

				<div class="jltma-infobox-content">
					<div class="jltma-inner-content">
						<?php if ($settings['ma_el_infobox_title_link']['url']) { ?>
							<a href="<?php echo esc_url_raw($settings['ma_el_infobox_title_link']['url']); ?>" <?php echo $this->get_render_attribute_string('ma_el_infobox_title_link_attr'); ?>>
								<h3 class="jltma-infobox-content-title">
									<?php echo $this->parse_text_editor($settings['ma_el_infobox_title']); ?>
								</h3>
							</a>
						<?php } else { ?>
							<h3 class="jltma-infobox-content-title">
								<?php echo $this->parse_text_editor($settings['ma_el_infobox_title']); ?>
							</h3>
						<?php } ?>

						<p class="jltma-infobox-content-description">
							<?php echo $this->parse_text_editor($settings['ma_el_infobox_description']); ?>
						</p>

						<?php if ($settings['ma_el_infobox_preset'] == "six") { ?>
							<a <?php echo $this->get_render_attribute_string('ma_el_infobox_readmore_link'); ?> class="jltma-btn-learn" <?php echo $this->get_render_attribute_string('ma_el_infobox_readmore'); ?>>
								<?php echo esc_html($settings['ma_el_infobox_readmore_text']); ?>
								<i class="ti-arrow-right"></i>
							</a>
						<?php } ?>
					</div><!-- /.jltma-inner-content -->
				</div>
			</div>
		</div>

<?php
	}
}
