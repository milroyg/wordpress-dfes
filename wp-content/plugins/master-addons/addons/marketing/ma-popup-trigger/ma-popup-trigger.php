<?php

namespace MasterAddons\Addons;

// Elementor Classes
use MasterAddons\Inc\Classes\Base\Master_Widget;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use MasterAddons\Inc\Admin\Config;
use MasterAddons\Inc\Admin\Settings\Settings;
use MasterAddons\Inc\Classes\Helper;

/**
 * Author Name: Liton Arefin
 * Author URL : https://jeweltheme.com
 * Date       : 10/6/25
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Popup Trigger Widget
 */
class Popup_Trigger extends Master_Widget
{
	use \MasterAddons\Inc\Traits\Widget_Notice;
	use \MasterAddons\Inc\Traits\Widget_Assets_Trait;

	public function get_name()
	{
		return 'ma-popup-trigger';
	}
	public function get_title()
	{
		return __('Popup Trigger', 'master-addons');
	}

	public function get_icon()
	{
		return 'jltma-icon ' . Config::get_addon_icon($this->get_name());
	}

	public function get_keywords()
	{
		return ['popup', 'trigger', 'button', 'action', 'close', 'modal'];
	}

protected function register_controls()
	{
		// Tab: Content ==============
		// Section: Settings ---------
		$this->start_controls_section(
			'section_popup_trigger',
			[
				'label' => __('Settings', 'master-addons'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'popup_trigger_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<strong>Please Note:</strong> This widget only works if it is placed inside a Popup. To create a Popup, please navigate to <a href="'. admin_url('edit.php?post_type=jltma_popup') .'" target="_blank">Dashboard > Master Addons > Popups</a>.',
				'separator' => 'after',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->add_control(
			'popup_trigger_type',
			[
				'label'   => __('Button Action', 'master-addons'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'close',
				'options' => [
					'close' => __('Close Popup', 'master-addons'),
					'close-permanently' => __('Close Permanently', 'master-addons'),
					'back' => __('Go Back to Referrer', 'master-addons'),
				]
			]
		);

		$this->add_control(
			'popup_trigger_show_again_delay',
			[
				'label'   => __('Show Again Delay', 'master-addons'),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => [
					'0' => __('No Delay', 'master-addons'),
					'60000' => __('1 Minute', 'master-addons'),
					'180000' => __('3 Minutes', 'master-addons'),
					'300000' => __('5 Minutes', 'master-addons'),
					'600000' => __('10 Minutes', 'master-addons'),
					'1800000' => __('30 Minutes', 'master-addons'),
					'3600000' => __('1 Hour', 'master-addons'),
					'10800000' => __('3 Hours', 'master-addons'),
					'21600000' => __('6 Hours', 'master-addons'),
					'43200000' => __('12 Hours', 'master-addons'),
					'86400000' => __('1 Day', 'master-addons'),
					'259200000' => __('3 Days', 'master-addons'),
					'432000000' => __('5 Days', 'master-addons'),
					'604800000' => __('7 Days', 'master-addons'),
					'864000000' => __('10 Days', 'master-addons'),
					'1296000000' => __('15 Days', 'master-addons'),
					'1728000000' => __('20 Days', 'master-addons'),
					'2628000000' => __('1 Month', 'master-addons'),
				],
				'description' => __('This option determines when to show popup again to a visitor after it is closed.', 'master-addons'),
				'separator' => 'before',
				'condition' => [
					'popup_trigger_type!' => 'close-permanently'
				]
			]
		);

		$this->add_control(
			'popup_trigger_redirect',
			[
				'label' => __('Redirect to URL when Closed', 'master-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition' => [
					'popup_trigger_type!' => 'back'
				]
			]
		);

		$this->add_control(
			'popup_trigger_redirect_url',
			[
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'popup_trigger_redirect' => 'yes',
					'popup_trigger_type!' => 'back'
				]
			]
		);

		$this->add_control(
			'popup_trigger_text',
			[
				'label' => __('Button Text', 'master-addons'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => 'Close Popup',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'popup_trigger_extra_icon_pos',
			[
				'label' => __('Icon Position', 'master-addons'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __('None', 'master-addons'),
					'before' => __('Before Element', 'master-addons'),
					'after' => __('After Element', 'master-addons'),
				],
				'default' => 'none',
			]
		);

		$this->add_control(
			'popup_trigger_extra_icon',
			[
				'label' => __('Select Icon', 'master-addons'),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
				'default' => [
					'value' => 'fas fa-times',
					'library' => 'fa-solid',
				],
				'condition' => [
					'popup_trigger_extra_icon_pos!' => 'none'
				]
			]
		);

		$this->add_responsive_control(
            'popup_trigger_align',
            [
                'label' => __('Button Align', 'master-addons'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default' => 'center',
                'options' => [
                    'left' => [
                        'title' => __('Left', 'master-addons'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'master-addons'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'master-addons'),
                        'icon' => 'eicon-h-align-right',
                    ]
                ],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}}',
				],
				'separator' => 'before'
            ]
        );

		$this->end_controls_section(); // End Controls Section

		// Tab: Styles ===============
		// Section: General ----------
		$this->start_controls_section(
			'section_popup_trigger_styles',
			[
				'label' => __('General', 'master-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_popup_trigger_style' );

		$this->start_controls_tab(
			'tab_popup_trigger_normal',
			[
				'label' => __('Normal', 'master-addons'),
			]
		);

		$this->add_control(
			'popup_trigger_color',
			[
				'label'  => __('Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .jltma-popup-trigger-button svg' => 'fill: {{VALUE}}'
				],
			]
		);

		$this->add_control(
			'popup_trigger_bg_color',
			[
				'label'  => __('Background Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#605BE5',
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button' => 'background-color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'popup_trigger_border_color',
			[
				'label'  => __('Border Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'popup_trigger_box_shadow',
				'selector' => '{{WRAPPER}} .jltma-popup-trigger-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_popup_trigger_hover',
			[
				'label' => __('Hover', 'master-addons'),
			]
		);

		$this->add_control(
			'popup_trigger_color_hr',
			[
				'label'  => __('Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'popup_trigger_bg_color_hr',
			[
				'label'  => __('Background Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#4A45D2',
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button:hover' => 'background-color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'popup_trigger_border_color_hr',
			[
				'label'  => __('Border Color', 'master-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button:hover' => 'border-color: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'popup_trigger_box_shadow_hr',
				'selector' => '{{WRAPPER}} .jltma-popup-trigger-button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'popup_trigger_divider',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_control(
			'popup_trigger_transition_duration',
			[
				'label' => __('Transition Duration', 'master-addons'),
				'type' => Controls_Manager::NUMBER,
				'default' => 0.1,
				'min' => 0,
				'max' => 5,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button' => 'transition-duration: {{VALUE}}s',
				],
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'popup_trigger_typography',
				'selector' => '{{WRAPPER}} .jltma-popup-trigger-button'
			]
		);

		$this->add_control(
			'popup_trigger_border_type',
			[
				'label' => __('Border Type', 'master-addons'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __('None', 'master-addons'),
					'solid' => __('Solid', 'master-addons'),
					'double' => __('Double', 'master-addons'),
					'dotted' => __('Dotted', 'master-addons'),
					'dashed' => __('Dashed', 'master-addons'),
					'groove' => __('Groove', 'master-addons'),
				],
				'default' => 'none',
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'popup_trigger_border_width',
			[
				'label' => __('Border Width', 'master-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 1,
					'left' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'popup_trigger_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'popup_trigger_svg_icon_size',
			[
				'label' => __('SVG Icon Size', 'master-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'popup_trigger_icon_spacing',
			[
				'label' => __('Extra Icon Spacing', 'master-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button .jltma-extra-icon-left' => 'padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jltma-popup-trigger-button .jltma-extra-icon-right' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'popup_trigger_padding',
			[
				'label' => __('Padding', 'master-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', ],
				'default' => [
					'top' => 6,
					'right' => 15,
					'bottom' => 6,
					'left' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'popup_trigger_margin',
			[
				'label' => __('Margin', 'master-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', ],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'popup_trigger_radius',
			[
				'label' => __('Border Radius', 'master-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => 3,
					'right' => 3,
					'bottom' => 3,
					'left' => 3,
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-popup-trigger-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// Help Docs section (links from config.php)
		$this->jltma_help_docs();

		$this->upgrade_to_pro_message();
	}

	protected function render()
	{
		// Get Settings
		$settings = $this->get_settings_for_display();

		// Get Icon HTML
		ob_start();
		\Elementor\Icons_Manager::render_icon( $settings['popup_trigger_extra_icon'], [ 'aria-hidden' => 'true' ] );
		$icon_html = ob_get_clean();

		$popup_show_delay = $settings['popup_trigger_show_again_delay'];

		if ( 'close-permanently' === $settings['popup_trigger_type'] ) {
			$popup_show_delay = 10000000000000;
		}

		$redirect_url = isset($settings['popup_trigger_redirect_url']['url']) ? $settings['popup_trigger_redirect_url']['url'] : '';

		echo '<div class="jltma-popup-trigger-button" data-trigger="'. esc_attr($settings['popup_trigger_type']) .'" data-show-delay="'. esc_attr($popup_show_delay) .'" data-redirect="'. esc_attr($settings['popup_trigger_redirect']) .'" data-redirect-url="'. esc_url($redirect_url) .'">';

			// Icon: Before
			if ( 'before' === $settings['popup_trigger_extra_icon_pos'] && '' !== $settings['popup_trigger_extra_icon']['value'] ) {
				echo '<span class="jltma-extra-icon-left">'. $icon_html .'</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '<span>'. esc_html($settings['popup_trigger_text']) .'</span>';

			// Icon: After
			if ( 'after' === $settings['popup_trigger_extra_icon_pos'] ) {
				echo '<span class="jltma-extra-icon-right">'. $icon_html .'</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

		echo '</div>';
	}
}
