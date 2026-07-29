<?php

namespace MasterAddons\Addons;

use MasterAddons\Inc\Classes\Base\Master_Widget;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Group_Control_Border;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;

use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Admin\Config;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Animated_Headlines extends Master_Widget
{

	use \MasterAddons\Inc\Traits\Widget_Notice;
	use \MasterAddons\Inc\Traits\Widget_Assets_Trait;

	public function get_name()
	{
		return 'ma-headlines';
	}

	public function get_title()
	{
		return esc_html__('Animated Headlines', 'master-addons' );
	}

	public function get_icon()
	{
		return 'jltma-icon ' . Config::get_addon_icon('ma-animated-headlines');
	}

	protected function register_controls()
	{

		/**
		 * Master Headlines Content Section
		 */
		$this->start_controls_section(
			'ma_el_headlines_content',
			[
				'label' => esc_html__('Content', 'master-addons' ),
			]
		);

		// Define default free options - Pro version overrides via filter
		$style_preset_options = apply_filters('master_addons/addons/animated_headlines/style_preset', [
			'rotate-1'         => esc_html__('Rotate 1', 'master-addons'),
			'rotate-2'         => esc_html__('Rotate 2', 'master-addons'),
			'rotate-3'         => esc_html__('Rotate 3', 'master-addons'),
			'loading-bar'      => esc_html__('Loading Bar', 'master-addons'),
			'ma-heading-pro-1' => esc_html__('Typing (Pro)', 'master-addons'),
			'ma-heading-pro-2' => esc_html__('Slide (Pro)', 'master-addons'),
			'ma-heading-pro-3' => esc_html__('Clip (Pro)', 'master-addons'),
			'ma-heading-pro-4' => esc_html__('Zoom (Pro)', 'master-addons'),
			'ma-heading-pro-5' => esc_html__('Scale (Pro)', 'master-addons'),
			'ma-heading-pro-6' => esc_html__('Push (Pro)', 'master-addons'),
		]);

		$this->add_control(
			'ma_el_headlines_style_preset',
			[
				'label'       => esc_html__('Style Preset', 'master-addons'),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'rotate-1',
				'label_block' => false,
				'options'     => $style_preset_options,
				'description' => Helper::upgrade_to_pro('7+ more effects on'),
			]
		);

		$this->add_control(
			'ma_el_headlines_first_heading',
			[
				'label'       => esc_html__('First Heading', 'master-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__('Master Addons', 'master-addons' ),
			]
		);

		$this->add_control(
			'jltma_headlines_second_heading',
			[
				'label'     => __('Second Headings', 'master-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'ma_el_headlines_second_heading',
			[
				'label'   => __('More Titles', 'master-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Minimal Template', 'master-addons' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'tabs',
			[
				'type'    => Controls_Manager::REPEATER,
				'default' => [
					['ma_el_headlines_second_heading' => esc_html__('Ultimate Addons', 'master-addons' )],
					['ma_el_headlines_second_heading' => esc_html__('Elementor Widgets', 'master-addons' )],
					['ma_el_headlines_second_heading' => esc_html__('Unique Design', 'master-addons' )],
					['ma_el_headlines_second_heading' => esc_html__('Unlimited Variations', 'master-addons' )],
					['ma_el_headlines_second_heading' => esc_html__('Unlimited Possibilities', 'master-addons' )],
				],
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{ma_el_headlines_second_heading}}',
			]
		);

		$this->add_responsive_control(
			'ma_el_headlines_alignment',
			[
				'label'   => esc_html__('Alignment', 'master-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__('Left', 'master-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'master-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__('Right', 'master-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .jltma-animated-headline' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'   => __('HTML Tag', 'master-addons' ),
				'type'    => Controls_Manager::SELECT,
				'separator' => 'after',
				'options' => [
					'h1'     => esc_html__('H1', 'master-addons'),
					'h2'     => esc_html__('H2', 'master-addons'),
					'h3'     => esc_html__('H3', 'master-addons'),
					'h4'     => esc_html__('H4', 'master-addons'),
					'h5'     => esc_html__('H5', 'master-addons'),
					'h6'     => esc_html__('H6', 'master-addons'),
					'div'    => esc_html__('div', 'master-addons'),
					'span'   => esc_html__('span', 'master-addons'),
					'p'      => esc_html__('p', 'master-addons'),
					'a'      => esc_html__('a', 'master-addons'),
				],
				'default' => 'h3',
			]
		);

		// Animation Effect
		$this->add_control(
			'anim_delay',
			[
				'label'              => esc_html__('Animation Delay', 'master-addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 2500,
				'frontend_available' => true,
				'condition'          => [
					'ma_el_headlines_style_preset!' => ['loading-bar']
				]
			]
		);

		// Bar Effect
		$this->add_control(
			'bar_anim_delay',
			[
				'label'              => esc_html__('Animation Delay', 'master-addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 3800,
				'frontend_available' => true,
				'condition'          => [
					'ma_el_headlines_style_preset' => ['loading-bar']
				]
			]
		);

		// Letter Effect
		$this->add_control(
			'letters_anim_delay',
			[
				'label'              => esc_html__('Letters Delay', 'master-addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 50,
				'frontend_available' => true,
				'condition'          => [
					'ma_el_headlines_style_preset' => ['rotate-2']
				]
			]
		);

		//Type Effect
		$this->add_control(
			'type_anim_delay',
			[
				'label'              => esc_html__('Type Letters Delay', 'master-addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 150,
				'frontend_available' => true,
				'condition'          => [
					'ma_el_headlines_style_preset' => ['type']
				]
			]
		);

		$this->add_control(
			'type_selection_delay',
			[
				'label'              => esc_html__('Selection Duration', 'master-addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 500,
				'frontend_available' => true,
				'condition'          => [
					'ma_el_headlines_style_preset' => ['type']
				]
			]
		);

		// Clip Effect
		$this->add_control(
			'clip_reveal_delay',
			[
				'label'              => esc_html__('Reveal Duration', 'master-addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 600,
				'frontend_available' => true,
				'condition'          => [
					'ma_el_headlines_style_preset' => ['clip']
				]
			]
		);

		$this->add_control(
			'clip_anim_duration',
			[
				'label'              => esc_html__('Reveal Animation Delay', 'master-addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 1500,
				'frontend_available' => true,
				'condition'          => [
					'ma_el_headlines_style_preset' => ['clip']
				]
			]
		);

		$this->end_controls_section();

		// Help Docs section (links from config.php)
		$this->jltma_help_docs();

		$this->upgrade_to_pro_message();

		/*
	        * Master Headlines First Part Styling Section
	        */

		$this->start_controls_section(
			'ma_el_headlines_first_heading_styles',
			[
				'label' => esc_html__('First Heading', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_headlines_first_text_color',
			[
				'label'		=> esc_html__('Text Color', 'master-addons' ),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors'	=> [
					'{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .first-heading'
					=> 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_headlines_first_bg_color',
			[
				'label'     => __('Background', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#704aff',
				'selectors' => [
					'{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .first-heading'
					=> 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_headlines_first_heading_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .first-heading',
			]
		);

		$this->add_responsive_control(
			'ma_el_headlines_first_heading_padding',
			[
				'label' 		=> esc_html__('Padding', 'master-addons' ),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .first-heading' 	=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_headlines_first_heading_margin',
			[
				'label' 		=> esc_html__('Margin', 'master-addons' ),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .first-heading' 	=> 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'ma_el_headlines_first_heading_border',
				'label' 	=> esc_html__('Border', 'master-addons' ),
				'selector' 	=> '{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .first-heading',
				'separator' => ''
			]
		);

		$this->add_control(
			'ma_el_headlines_first_heading_radius',
			[
				'label' 		=> esc_html__('Border Radius', 'master-addons' ),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .first-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'ma_el_headlines_first_heading_box_shadow',
				'selector' 	=> '{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .first-heading',
				'separator'	=> ''
			]
		);

		$this->end_controls_section();

		/*
			* Master Headlines Second Part Styling Section
			*/
		$this->start_controls_section(
			'ma_el_headlines_second_heading_styles',
			[
				'label' => esc_html__('Second Heading', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_headlines_second_text_color',
			[
				'label'		=> esc_html__('Text Color', 'master-addons' ),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#132C47',
				'selectors'	=> [
					'{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .second-heading' =>
					'color: {{VALUE}}; font-style: normal; font-weight: normal;',
				],
			]
		);

		$this->add_control(
			'ma_el_headlines_second_bg_color',
			[
				'label'     => __('Background', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .second-heading' =>
					'background-color: {{VALUE}}; line-height:1.3;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_headlines_second_heading_typography',
				'global' => [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				// The letter-based presets (rotate-2, rotate-3, type, scale) split each
				// letter into <i> wrappers - and rotate-2 also into <em> - which default
				// to italic in the UA stylesheet. Target them directly so the Typography
				// "Style" control reaches the letters. Same-element classes
				// (.jltma-animated-headline.rotate-2) sit on the title node, so no
				// descendant combinator there.
				'selector' => '{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .second-heading, {{WRAPPER}} .jltma-animated-headline.rotate-2 .second-heading i, {{WRAPPER}} .jltma-animated-headline.rotate-2 .second-heading i em, {{WRAPPER}} .jltma-animated-headline.rotate-3 .second-heading i, {{WRAPPER}} .jltma-animated-headline.type .second-heading i, {{WRAPPER}} .jltma-animated-headline.scale .second-heading i'
			]
		);

		$this->add_responsive_control(
			'ma_el_headlines_second_heading_padding',
			[
				'label' 		=> esc_html__('Padding', 'master-addons' ),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .second-heading' 	=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_headlines_second_heading_margin',
			[
				'label' 		=> esc_html__('Margin', 'master-addons' ),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .second-heading' 	=> 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'ma_el_headlines_second_heading_border',
				'label' 	=> esc_html__('Border', 'master-addons' ),
				'selector' 	=> '{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .second-heading',
				'separator' => ''
			]
		);

		$this->add_control(
			'ma_el_headlines_second_heading_radius',
			[
				'label' 		=> esc_html__('Border Radius', 'master-addons' ),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .second-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'ma_el_headlines_second_heading_box_shadow',
				'selector' 	=> '{{WRAPPER}} .jltma-animated-heading .jltma-animated-heading-wrapper .jltma-animated-heading-title .second-heading',
				'separator'	=> ''
			]
		);

		$this->end_controls_section();

		/*
			* Master Headlines Second Part Styling Section
			*/
		$this->start_controls_section(
			'jltma_headlines_loading_bar_styles',
			[
				'label' => esc_html__('Loading Bar', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'          => [
					'ma_el_headlines_style_preset' => ['loading-bar']
				]
			]
		);

		$this->add_control(
			'jltma_headlines_loading_bar_color',
			[
				'label'		=> esc_html__('Bar Color', 'master-addons' ),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#0096a7',
				'selectors'	=> [
					'{{WRAPPER}} .jltma-animated-headline.loading-bar .jltma-words-wrapper::after' => 'background: {{VALUE}};',
				],
			]
		);

				// Animation Effect
		$this->add_control(
			'jltma_headlines_loading_bar_height',
			[
				'label'              => esc_html__('Height', 'master-addons' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 3,
				'frontend_available' => true,
				'selectors'	=> [
					'{{WRAPPER}} .jltma-animated-headline.loading-bar .jltma-words-wrapper::after' => 'height: {{VALUE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_headlines_loading_bar_position',
			[
				'label' 		=> esc_html__('Bar Position', 'master-addons' ),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px'],
				'allowed_dimensions' => ['bottom', 'left'],
				'default' 		=> [
					'bottom' 	=> 0,
					'left' 		=> 5,
					'unit' 		=> 'px',
					'isLinked' 	=> false,
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-animated-headline.loading-bar .jltma-words-wrapper::after' 	=> 'bottom:{{BOTTOM}}{{UNIT}}; left:{{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

	}

	protected function render()
	{

		$settings = $this->get_settings_for_display();

		// Whitelist allowed HTML tags to prevent XSS
		$allowed_tags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
		$title_html_tag = in_array($settings['title_html_tag'], $allowed_tags, true)
			? $settings['title_html_tag']
			: 'h2';

		switch ($settings['ma_el_headlines_style_preset']) {
			case "rotate-2":
				$letters_class = "letters";
				break;
			case "rotate-3":
				$letters_class = "letters";
				break;
			case "type":
				$letters_class = "letters";
				break;
			case "scale":
				$letters_class = "letters";
				break;
			default:
				$letters_class = "";
		}

		$jltma_animated_headlines_id       = 'jltma-heading-' . $this->get_id();

		$this->add_render_attribute([
			'jltma_animated_headlines' => [
				'id'    => esc_attr($jltma_animated_headlines_id),
				'class' => 'jltma-animated-heading'
			]
		]);

		$this->add_render_attribute('jltma_animated_header_wrapper', [
			'class'	=> [
				'jltma-animated-heading-title',
				'jltma-animated-headline',
				$letters_class,
				$settings['ma_el_headlines_style_preset'],
				'main-title'
			],
			'id' => 'jltma-animated-heading-' . $this->get_id()
		]);

?>
		<div <?php echo $this->get_render_attribute_string('jltma_animated_headlines'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor safe attribute string ?>>
			<div class="jltma-animated-heading-wrapper">
				<<?php echo esc_html($title_html_tag) . ' ' . $this->get_render_attribute_string('jltma_animated_header_wrapper'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor safe attribute string ?>>
					<span class="first-heading">
						<?php echo $this->parse_text_editor($settings['ma_el_headlines_first_heading']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- parse_text_editor returns safe HTML ?>
					</span>
					<span class="jltma-words-wrapper">
						<?php foreach ($settings['tabs'] as $index => $tab) { ?>
							<b class="second-heading <?php echo ($index == 0) ? "is-visible" : ""; ?>">
								<?php echo $this->parse_text_editor($tab['ma_el_headlines_second_heading']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- parse_text_editor returns safe HTML ?>
							</b>
						<?php } ?>
					</span>
				</<?php echo esc_html($title_html_tag); ?>>
			</div>
		</div>

<?php
	}
}
