<?php

namespace MasterAddons\Addons;

use MasterAddons\Inc\Classes\Base\Master_Widget;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;

use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Admin\Config;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Progress_Bar extends Master_Widget
{
	use \MasterAddons\Inc\Traits\Widget_Notice;
	use \MasterAddons\Inc\Traits\Widget_Assets_Trait;

	public function get_name()
	{
		return 'ma-progressbar';
	}

	public function get_title()
	{
		return esc_html__('Progressbar', 'master-addons' );
	}

	public function get_icon()
	{
		return 'jltma-icon ' . Config::get_addon_icon($this->get_name());
	}

	protected function register_controls()
	{

		// Progressbar Content Section
		$this->start_controls_section(
			'ma_el_progress_bar_section_content',
			[
				'label' => __('Content', 'master-addons' ),
			]
		);

		$this->add_control(
			'ma_el_progress_bar_title',
			[
				'label' => __('Title', 'master-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __('Progress Bar', 'master-addons' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_progress_bar_value',
			[
				'label' => __('Value', 'master-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 60,
			]
		);

		$this->end_controls_section();

		// Progressbar Style Section
		$this->start_controls_section(
			'ma_el_section_progress_bar_styles_preset',
			[
				'label' => __('General Styles', 'master-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_progress_bar_preset',
			[
				'label'   => __('Style Presets', 'master-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'line'        => __('Line', 'master-addons' ),
					'line-bubble' => __('Line Bubble', 'master-addons' ),
					'circle'      => __('Circle', 'master-addons' ),
					'fan'         => __('Fan', 'master-addons' )
				],
				'default' => 'line',
			]
		);

		$this->add_control(
			'ma_el_progress_bar_bubble_color',
			[
				'label' => __('Bubble Color', 'master-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#61ce70',
				'selectors' => [
					'{{WRAPPER}} [class*="jltma-progress-bar-"].line-bubble .ldBar-label' => 'background: {{VALUE}};',
				],
				'condition' => [
					'ma_el_progress_bar_preset' => 'line-bubble'
				]

			]
		);

		$this->add_control(
			'ma_el_progress_bar_bubble_position',
			[
				'label' => __('Bubble Position', 'master-addons' ),
				'type' => Controls_Manager::SLIDER,
				'unit' => '',
				'size' => '',
				'selectors' => [
					'{{WRAPPER}} [class*="jltma-progress-bar-"].line-bubble .ldBar-label' => 'top: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'ma_el_progress_bar_preset' => 'line-bubble'
				]

			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ma_el_progress_bar_title_styles',
			[
				'label' => __('Title', 'master-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_progress_bar_title_color',
			[
				'label' => __('Title Color', 'master-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .jltma-progress-bar-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .jltma-progress-bar-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ma_el_progress_bar_front_style',
			[
				'label' => __('Front Bar', 'master-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_progress_bar_stroke_color',
			[
				'label' => __('Color', 'master-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#704aff'
			]
		);

		$this->add_control(
			'ma_el_progress_bar_stroke_width',
			[
				'label' => __('Width', 'master-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 5,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ma_el_progress_bar_back_style',
			[
				'label' => __('Back Bar', 'master-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_progress_bar_trail_color',
			[
				'label' => __('Color', 'master-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ddd'
			]
		);

		$this->add_control(
			'ma_el_progress_bar_trail_width',
			[
				'label'   => __('Width', 'master-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
				'default' => 5,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ma_el_progress_bar_value_styles',
			[
				'label' => __('Value', 'master-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_progress_bar_value_color',
			[
				'label' => __('Color', 'master-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} [class*="jltma-progress-bar-"] .ldBar-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'value_typography',
				'selector' => '{{WRAPPER}} .ldBar-label',
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

		$this->add_render_attribute(
			'ma-el-progress-bar',
			[
				'class' => [$settings['ma_el_progress_bar_preset'], 'jltma-progress-bar jltma-progress-bar-' . $this->get_id()],
				'data-id' => $this->get_id(),
				'data-type' => $settings['ma_el_progress_bar_preset'],
				'data-progress-bar-value' => $settings['ma_el_progress_bar_value'],
				'data-stroke-color' => $settings['ma_el_progress_bar_stroke_color'],
				'data-progress-bar-stroke-width' => $settings['ma_el_progress_bar_stroke_width'],
				'data-stroke-trail-color' => $settings['ma_el_progress_bar_trail_color'],
				'data-progress-bar-stroke-trail-width' => $settings['ma_el_progress_bar_trail_width'],
			]
		);

		if ($settings['ma_el_progress_bar_preset'] == 'line' || $settings['ma_el_progress_bar_preset'] == 'line-bubble') {
			$this->add_render_attribute(
				'ma-el-progress-bar',
				[
					'data-preset' => 'line',
					'style' => 'width: 100%; height: 50px'
				]
			);
		}

		if ($settings['ma_el_progress_bar_preset'] == 'circle') {
			$this->add_render_attribute(
				'ma-el-progress-bar',
				[
					'data-preset' => 'circle',
					'style' => 'width: 100%; height: 100%'
				]
			);
		}

		if ($settings['ma_el_progress_bar_preset'] == 'fan') {
			$this->add_render_attribute(
				'ma-el-progress-bar',
				[
					'data-preset' => 'fan',
					'style' => 'width: 100%; height: 100%'
				]
			);
		}
?>

		<div <?php echo $this->get_render_attribute_string('ma-el-progress-bar'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor render attribute string is safe ?> data-progress-bar>
			<?php if(!empty($settings['ma_el_progress_bar_title'])){?>
				<h6 class="jltma-progress-bar-title">
					<?php echo esc_html($this->parse_text_editor($settings['ma_el_progress_bar_title'])); ?>
				</h6>
			<?php } ?>
		</div>

<?php
	}
}
