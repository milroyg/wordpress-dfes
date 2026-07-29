<?php

namespace MasterAddons\Addons;

use MasterAddons\Inc\Classes\Base\Master_Widget;
use \Elementor\Controls_Manager;
use \Elementor\Utils;
use \Elementor\Repeater;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Colors;

use MasterAddons\Inc\Admin\Config;

/**
 * Author Name: Liton Arefin
 * Author URL : https: //jeweltheme.com
 * Date       : 6/27/19
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.
class Counter_Up extends Master_Widget
{
	use \MasterAddons\Inc\Traits\Widget_Notice;
	use \MasterAddons\Inc\Traits\Widget_Assets_Trait;

	public function get_name()
	{
		return 'jltma-counter-up';
	}
	public function get_title()
	{
		return esc_html__('Counter Up', 'master-addons' );
	}
	public function get_icon()
	{
		return 'jltma-icon ' . Config::get_addon_icon('ma-counter-up');
	}

	protected function register_controls()
	{

		$this->start_controls_section(
			'ma_el_counterup_section_start',
			[
				'label' => __('Counter Up Content', 'master-addons' )
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon_type',
			[
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'label'       => esc_html__('Type', 'master-addons' ),
				'default'     => 'icon',
				'options'     => [
					'none'    => [
						'title' => esc_html__('None', 'master-addons' ),
						'icon'  => 'eicon-ban',
					],
					'icon'      => [
						'title' => esc_html__('Icon', 'master-addons' ),
						'icon'  => 'eicon-icon-box',
					],
					'custom'    => [
						'title' => esc_html__('Image', 'master-addons' ),
						'icon'  => 'eicon-image',
					],
				]
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label'   => esc_html__('Icon', 'master-addons' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-star',
					'library' => 'solid',
				],
				'condition'     => [
					'icon_type' => 'icon',
				],
			]
		);

		$repeater->add_control(
			'custom',
			[
				'label'       => esc_html__('Image', 'master-addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::MEDIA,
				'condition'   => [
					'icon_type' => 'custom',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'label'       => esc_html__('Title', 'master-addons' ),
				'default'     => 'Type your title',
			]
		);

		$repeater->add_control(
			'number_prefix',
			[
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'label'       => esc_html__('Number Prefix', 'master-addons' ),
				'placeholder' => '',
				'default'     => '',
			]
		);

		$repeater->add_control(
			'number_suffix',
			[
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'label'       => esc_html__('Number Suffix', 'master-addons' ),
				'default'     => '+',
			]
		);

		$repeater->add_control(
			'number',
			[
				'type'    => Controls_Manager::NUMBER,
				'label'   => esc_html__('Number', 'master-addons' ),
				'default' => 123456,
			]
		);

		$repeater->add_control(
			'background',
			[
				'type'  => Controls_Manager::COLOR,
				'label' => esc_html__('Background', 'master-addons' )
			]
		);

		$this->add_control(
			'jltma_counterup_contents',
			[
				'label'   => esc_html__('Items', 'master-addons' ),
				'type'    => Controls_Manager::REPEATER,
				'default' => [
					[
						'number'     => 6000,
						'icon'       => ['value' => 'far fa-thumbs-up', 'library'  => 'regular'],
						'title'      => esc_html__('Happy Clients', 'master-addons' ),
						'background' => ''
					],
					[
						'number'     => 9560,
						'icon'       => ['value' => 'far fa-check-circle', 'library'  => 'regular'],
						'title'      => esc_html__('Projects Completed', 'master-addons' ),
						'background' => ''
					],
					[
						'number'     => 165893,
						'icon'       => ['value' => 'fas fa-coffee', 'library'  => 'solid'],
						'title'      => esc_html__('Cups of Coffee', 'master-addons' ),
						'background' => ''
					],
					[
						'number'     => 12356789,
						'icon'       => ['value' => 'fas fa-trophy', 'library'  => 'solid'],
						'title'      => esc_html__('Awards Won', 'master-addons' ),
						'background' => ''
					],
				],
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{title}}',
			]
		);

		$this->add_responsive_control(
			'column',
			[
				'label'          => esc_html__('Columns', 'master-addons' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'options' => [
					'6' => esc_html__('6 Columns', 'master-addons' ),
					'4' => esc_html__('4 Columns', 'master-addons' ),
					'3' => esc_html__('3 Columns', 'master-addons' ),
					'2' => esc_html__('2 Columns', 'master-addons' ),
					'1' => esc_html__('1 Column', 'master-addons' ),
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup-column' => 'width: calc(100% / {{VALUE}});',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_counterup_content_direction',
			[
				'label'   => esc_html__('Content Position', 'master-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'column-reverse' => [
						'title' => esc_html__('Top', 'master-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'row-reverse' => [
						'title' => esc_html__('Left', 'master-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'row' => [
						'title' => esc_html__('Right', 'master-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
					'column' => [
						'title' => esc_html__('Bottom', 'master-addons' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'   => 'column',
				'toggle'    => false,
				'description' => esc_html__('Position of the title relative to the number.', 'master-addons' ),
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup-content' => 'display: flex; flex-direction: {{VALUE}}; align-items: center; justify-content: center; gap: 5px;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'jltma_counterup_icons_section',
			[
				'label' => esc_html__('Icon Settings', 'master-addons' )
			]
		);

		// counnterup icon align
		$this->add_control(
			'jltma_counterup_icon_align',
			[
				'label'   => esc_html__('Icon/Image Position', 'master-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'      => [
						'title' => esc_html__('Left', 'master-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'    => [
						'title' => esc_html__('Top', 'master-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'right'     => [
						'title' => esc_html__('Right', 'master-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default' => 'center',
			]
		);

		// Icon Size
		$this->add_control(
			'icon_size',
			[
				'label' => esc_html__('Icon Size', 'master-addons' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px'    => [
						'min' => 6,
						'max' => 300,
					],
				],
				'default'   => [
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup .jltma-counterup-icon i, {{WRAPPER}} .jltma-counterup .jltma-counterup-icon svg' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jltma-counterup .jltma-counterup-icon img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_size',
			[
				'label' => esc_html__('Icon Circle or Image Size', 'master-addons' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px'    => [
						'min' => 6,
						'max' => 300,
					],
				],
				'default'   => [
					'size' => 70,
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup .jltma-counterup-icon.jltma-counterup-icon--icon'   => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jltma-counterup .jltma-counterup-icon img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * counterup style
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'jltma_counterup_box_style_section',
			[
				'label' => esc_html__('Counter Up Box Style', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		// counterup background color
		$this->add_control(
			'jltma_counterup_bg_color',
			[
				'label'     => esc_html__('Background Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup' => 'background-color: {{VALUE}};'
				],
			]
		);

		// counterup box padding
		$this->add_responsive_control(
			'jltma_counterup_padding',
			[
				'label'      => esc_html__('Padding', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .jltma-counterup' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// counterup margin
		$this->add_responsive_control(
			'jltma_counterup_margin',
			[
				'label'      => esc_html__('Margin', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .jltma-counterup' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// counterup border
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'jltma_counterup_border',
				'label'    => esc_html__('Border Type', 'master-addons' ),
				'selector' => '{{WRAPPER}} .jltma-counterup',
			]
		);

		$this->add_responsive_control(
			'jltma_counterup_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'master-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// counterup box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'jltma_counterup_box_shadow',
				'selector' => '{{WRAPPER}} .jltma-counterup-column .jltma-counterup'
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * color & typography
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'jltma_counterup_typography_section',
			[
				'label' => esc_html__('Content Style', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		// counterup icon style
		$this->add_control(
			'jltma_counterup_icon_style',
			[
				'label' => esc_html__('Icon Style', 'master-addons' ),
				'type'  => Controls_Manager::HEADING
			]
		);

		// counterup icon color
		$this->add_control(
			'jltma_counterup_icon_color',
			[
				'label'     => esc_html__('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .jltma-counterup svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		// counterup icon color
		$this->add_control(
			'jltma_counterup_icon_bg_color',
			[
				'label'     => esc_html__('Background Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b00e7',
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup-icon.jltma-counterup-icon--icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		// counterup number style
		$this->add_control(
			'jltma_counterup_number_style',
			[
				'label'     => esc_html__('Number Style', 'master-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		//  counterup number color
		$this->add_control(
			'jltma_counterup_number_color',
			[
				'type'   => Controls_Manager::COLOR,
				'label'  => esc_html__('Color', 'master-addons' ),
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'   => '#333',
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup .jltma-counter-up-number' => 'color: {{VALUE}};'
				],
			]
		);

		// counterup number typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'jltma_counterup_number_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .jltma-counterup .jltma-counter-up-number'
			]
		);

		// counterup number Prefix
		$this->add_control(
			'jltma_counterup_number_prefix_style',
			[
				'label'     => esc_html__('Number Prefix', 'master-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		//  counterup number color
		$this->add_control(
			'jltma_counterup_number_prefix_color',
			[
				'type'   => Controls_Manager::COLOR,
				'label'  => esc_html__('Color', 'master-addons' ),
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'   => '#333',
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup .jltma-counter-up-number-prefix' => 'color: {{VALUE}};'
				],
			]
		);

		// counterup number prefix typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'jltma_counterup_number_prefix_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .jltma-counterup .jltma-counter-up-number-prefix'
			]
		);

		// Counter Up number typography
		$this->add_control(
			'jltma_counterup_number_prefix_size',
			[
				'label'  => esc_html__('Size', 'master-addons' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .jltma-counterup .jltma-counter-up-number-prefix' => 'font-size: {{SIZE}}{{UNIT}};'
				]
			]
		);

		// counterup number margin
		$this->add_control(
			'jltma_counterup_number_margin',
			[
				'label'      => esc_html__('Margin', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .jltma-counterup .jltma-counter-up-number' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// counterup title style
		$this->add_control(
			'jltma_counterup_title_style',
			[
				'label'     => esc_html__('Title Style', 'master-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		//  counterup title color
		$this->add_control(
			'jltma_counterup_title_color',
			[
				'type'   => Controls_Manager::COLOR,
				'label'  => esc_html__('Title color', 'master-addons' ),
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default'   => '#525151',
				'selectors' => [
					'{{WRAPPER}} .jltma-counterup-title' => 'color: {{VALUE}};'
				],
			]
		);

		//  counterup title typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'jltma_counterup_title_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .jltma-counterup-title'
			]
		);

		// counterup title margin
		$this->add_control(
			'jltma_counterup_title_margin',
			[
				'label'      => esc_html__('Margin', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .jltma-counterup-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Help Docs section (links from config.php)
		$this->jltma_help_docs();

		$this->upgrade_to_pro_message();

	}

	protected function render()
	{

		$settings = $this->get_settings_for_display();
		$id_int   = substr($this->get_id_int(), 0, 3);

		if (is_array($settings['jltma_counterup_contents'])) :
			echo '<div class="jltma-counterup-items jltma-row">';

			foreach ($settings['jltma_counterup_contents'] as $index => $list) :

				$title_count        = $index + 1;
				$title_settings_key = $this->get_repeater_setting_key('title', 'jltma_counterup_contents', $index);

				$this->add_render_attribute($title_settings_key, [
					'id'    => $id_int . $title_count,
					'style' => ['background-color:', $list['background']]
				]);

				echo '<div class="jltma-counterup-column">';
				echo '<div class="jltma-counterup jltma-counterup-icon-' . esc_attr($settings['jltma_counterup_icon_align']) . '"  ' . $this->get_render_attribute_string($title_settings_key) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_render_attribute_string returns sanitized HTML attributes
				$cup_icon_type = !empty($list['icon_type']) ? $list['icon_type'] : 'icon';
				echo '<span class="jltma-counterup-icon jltma-counterup-icon--' . esc_attr($cup_icon_type) . ' counterup-icon-text-' . esc_attr($settings['jltma_counterup_icon_align']) . '">';

				if (!empty($list['icon']) && ($list['icon_type'] == 'icon')) {
					$this->render_icon($list['icon'], ['aria-hidden' => 'true']);
				}elseif(!empty($list['custom']) && ($list['icon_type'] == 'custom')){
					$svg_src = wp_get_attachment_url($list["custom"]["id"], 'full');
					echo '<img src="' . esc_url_raw($svg_src) .  '"/>';
				}

				echo '</span>';
				if (!empty($list['number']) || ($list['title'])) :
					echo '<div class="jltma-counterup-content">';
					$list['number'] ? printf('<div class="jltma-counter-up-number-section"><h3 class="jltma-counter-up-number-wrap"><span class="jltma-counter-up-number-prefix">%1$s</span><span class="jltma-counter-up-number">%2$s</span><span class="jltma-counter-up-number-prefix">%3$s</span></h3></div>', wp_kses_post( $this->parse_text_editor($list['number_prefix']) ), esc_attr($list['number']), wp_kses_post( $this->parse_text_editor($list['number_suffix']) ) ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- parse_text_editor output wrapped in wp_kses_post
					$list['title'] ? printf('<span class="jltma-counterup-title">%s</span>', wp_kses_post( $this->parse_text_editor($list['title']) ) ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- parse_text_editor output wrapped in wp_kses_post
					echo '</div>';
				endif;
				echo '</div>';
				echo '</div>';
			endforeach;
			echo '</div>';
		endif;
	}
}
