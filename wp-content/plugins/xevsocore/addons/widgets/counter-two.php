<?php

namespace Elementor;



if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly.

}



/**

 * Elementor progress widget.

 *

 * Elementor widget that displays an escalating progress bar.

 *

 * @since 1.0.0

 */

class xevso_counter_tow_Widget extends \Elementor\Widget_Base {



	/**

	 * Get widget name.

	 *

	 * Retrieve progress widget name.

	 *

	 * @since 1.0.0

	 * @access public

	 *

	 * @return string Widget name.

	 */

	public function get_name() {

		return 'xevso-counter-two';

	}



	/**

	 * Get widget title.

	 *

	 * Retrieve progress widget title.

	 *

	 * @since 1.0.0

	 * @access public

	 *

	 * @return string Widget title.

	 */

	public function get_title() {

		return esc_html__( 'xevso Counter Two', 'xevsocore' );

	}
	public function get_categories() {

		return [ 'xevsocore' ];

	}

    

	/**

	 * Get widget icon.

	 *

	 * Retrieve progress widget icon.

	 *

	 * @since 1.0.0

	 * @access public

	 *

	 * @return string Widget icon.

	 */

	public function get_icon() {

		return 'eicon-counter';

	}

	/**

	 * Get widget keywords.

	 *

	 * Retrieve the list of keywords the widget belongs to.

	 *

	 * @since 2.1.0

	 * @access public

	 *

	 * @return array Widget keywords.

	 */

	public function get_keywords() {

		return [ 'xevso', 'counter two' ];

	}



	/**

	 * Register progress widget controls.

	 *

	 * Adds different input fields to allow the user to change and customize the widget settings.

	 *

	 * @since 1.0.0

	 * @access protected

	 */

	protected function _register_controls() {

		

		$this->start_controls_section(

			'xevso_counter_tow_section',

			[

				'label' => esc_html__( 'Content', 'xevsocore' ),

				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,

			]

		);

	
		$this->add_control(
			'xevso_counter_tow_icon',
			[
				'label' => __( 'Icon', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(

			'xevso_counter_tow_title',

			[

			    'label' => esc_html__( 'Title', 'xevsocore' ),

			    'type'          => Controls_Manager::TEXT,

			    'default'       => esc_html__('Happy Client','xevsocore'),

			    'label_block' => true,

			]

		);

		$this->add_control(

			'xevso_counter_tow_number',

			[

			    'label' => esc_html__( 'Number', 'xevsocore' ),

			    'type'          => Controls_Manager::TEXT,

			    'default'       => esc_html__('555','xevsocore'),

			    'label_block' => true,

			]

		);

		$this->add_control(

			'xevso_counter_tow_symbol',

			[

			    'label' => esc_html__( 'Symbol', 'xevsocore' ),

			    'type'          => Controls_Manager::TEXT,

			    'default'       => esc_html__('+','xevsocore'),

			    'label_block' => true,

			]

		);

		$this->end_controls_section();

		$this->start_controls_section(

			'xevso_counter_tow_style_box',

			[

			    'label' => esc_html__( 'Box', 'xevsocore' ),

			    'tab' => Controls_Manager::TAB_STYLE,

			]

		);

		$this->add_responsive_control(
			'xevso_counter_tow_style_box_alingment',
			[
				'label' => esc_html__( 'Alignment', 'xevsocore' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'xevsocore' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'xevsocore' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'xevsocore' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xevso-counters' => 'text-align: {{VALUE}};',
				],
				'default' => 'center',
				'toggle' => true,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_counter_tow_style_box_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .xevso-counters',
			]
		);

		$this->add_group_control(

			\Elementor\Group_Control_Border::get_type(),

			[

				'name' => 'xevso_counter_tow_style_box_border',

				'label' => esc_html__( 'Border', 'xevsocore' ),

				'selector' => '{{WRAPPER}} .xevso-counters',

			]

		);

		$this->add_responsive_control(

			'xevso_counter_tow_style_margin',

			[

			    'label' => esc_html__( 'Margin', 'xevsocore' ),

			    'type' => Controls_Manager::DIMENSIONS,

			    'size_units' => [ 'px', '%', 'em' ],

			    'selectors' => [

				  '{{WRAPPER}} .xevso-counters' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

			    ],

			    'separator' =>'before',

			]

		);

		$this->add_responsive_control(

			'xevso_counter_tow_style_padding',

			[

			    'label' => esc_html__( 'Padding', 'xevsocore' ),

			    'type' => Controls_Manager::DIMENSIONS,

			    'size_units' => [ 'px', '%', 'em' ],

			    'default' => [

				  'top' => '50',

				  'right' => '0',

				  'bottom' => '50',

				  'left' => '0',

				  'isLinked' => false

			    ],

			    'selectors' => [

				  '{{WRAPPER}} .xevso-counters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

			    ],

			    'separator' =>'before',

			]

		);

		$this->end_controls_section();
		$this->start_controls_section(

			'xevso_counter_tow_style_icon',

			[

			    'label' => esc_html__( 'Icon', 'xevsocore' ),

			    'tab' => Controls_Manager::TAB_STYLE,

			]

		);
		$this->add_control(

			'xevso_counter_tow_style_icon_color',

			[

				'label' => esc_html__( 'Color', 'xevsocore' ),

				'type' => Controls_Manager::COLOR,

				'selectors' => [

					'{{WRAPPER}} .counters-icons i' => 'color: {{VALUE}};',

				],

			]

		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_counter_tow_style_icon_typo',
				'selector' => '{{WRAPPER}} .counters-icons i',
			]
		);
		
		$this->add_control(
			'xevso_counter_tow_style_icon_position',
			[
				'label' => __( 'icon position', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => -50,
						'max' => 80,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .counters-icons i' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_buttons_icot_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .xevso-counters-box .counters-icons',
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_buttons_icot_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .xevso-counters-box .counters-icons',
			]
		);
		
		$this->add_responsive_control(
			'xevso_buttons_icot_nradius',
			[
				'label' => esc_html__( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .xevso-counters-box .counters-icons' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(

			'xevso_counter_tow_icot_padding',

			[

			    'label' => esc_html__( 'Padding', 'xevsocore' ),

			    'type' => Controls_Manager::DIMENSIONS,

			    'size_units' => [ 'px', '%', 'em' ],

			    'selectors' => [

				  '{{WRAPPER}} .xevso-counters-box .counters-icons i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

			    ],

			    'separator' =>'before',

			]

		);
		$this->end_controls_section();



		$this->start_controls_section(

			'xevso_counter_tow_style_start',

			[

			    'label' => esc_html__( 'Number', 'xevsocore' ),

			    'tab' => Controls_Manager::TAB_STYLE,

			]

		);
		$this->add_responsive_control(
			'xevso_counter_tow_style_aligment',
			[
				'label' => esc_html__( 'Alignment', 'xevsocore' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'xevsocore' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'xevsocore' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'xevsocore' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .countr_text' => 'text-align: {{VALUE}};',
				],
				'default' => 'left',
				'toggle' => true,
			]
		);
		$this->add_control(

			'xevso_counter_tow_style_number',

			[

				'label' => esc_html__( 'Color', 'xevsocore' ),

				'type' => Controls_Manager::COLOR,

				'default'	=>'#010425',

				'selectors' => [

					'{{WRAPPER}} .countr_text h2 .counter' => 'color: {{VALUE}};',

				],

			]

		); 

		$this->add_control(

			'xevso_counter_tow_style_symble',

			[

				'label' => esc_html__( 'Symbol Color', 'xevsocore' ),

				'type' => Controls_Manager::COLOR,

				'default'	=>'#2d66ff',

				'selectors' => [

					'{{WRAPPER}} .countr_text h2 .symble' => 'color: {{VALUE}};',

				],

			]

		); 
		$this->add_responsive_control(

			'xevso_counter_tow_numer_margin',

			[

			    'label' => esc_html__( 'Margin', 'xevsocore' ),

			    'type' => Controls_Manager::DIMENSIONS,

			    'size_units' => [ 'px', '%', 'em' ],

			    'selectors' => [

				  '{{WRAPPER}} .countr_text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

			    ],

			    'separator' =>'before',

			]

		);
		$this->add_responsive_control(

			'xevso_counter_tow_numer_padding',

			[

			    'label' => esc_html__( 'Padding', 'xevsocore' ),

			    'type' => Controls_Manager::DIMENSIONS,

			    'size_units' => [ 'px', '%', 'em' ],

			    'selectors' => [

				  '{{WRAPPER}} .countr_text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

			    ],

			    'separator' =>'before',

			]

		);
		$this->add_group_control(

			Group_Control_Typography::get_type(),

			[

				'name' => 'xevso_counter_tow_style_n_typo',

				'selector' => '{{WRAPPER}} .countr_text h2',

			]

		);



		$this->end_controls_section();



		$this->start_controls_section(

			'xevso_counter_tow_style_title',

			[

			    'label' => esc_html__( 'Title', 'xevsocore' ),

			    'tab' => Controls_Manager::TAB_STYLE,

			]

		);
		$this->add_responsive_control(
			'xevso_counter_tow_style_title_alignment',
			[
				'label' => esc_html__( 'Alignment', 'xevsocore' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'xevsocore' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'xevsocore' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'xevsocore' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .counters-title' => 'text-align: {{VALUE}};',
				],
				'default' => 'left',
				'toggle' => true,
			]
		);
		$this->add_control(

			'xevso_counter_tow_style_title_c',

			[

				'label' => esc_html__( 'Color', 'xevsocore' ),

				'type' => Controls_Manager::COLOR,

				'default'	=>'#2d66ff',

				'selectors' => [

					'{{WRAPPER}} .counters-title h5' => 'color: {{VALUE}};',

				],

			]

		); 

		$this->add_group_control(

			Group_Control_Typography::get_type(),

			[

				'name' => 'xevso_counter_tow_style_title_typo',

				'selector' => '{{WRAPPER}} .counter-title h5',

			]

		);

		$this->end_controls_section();

	}



	/**

	 * Render progress widget output on the frontend.

	 *

	 * Written in PHP and used to generate the final HTML.

	 *

	 * @since 1.0.0

	 * @access protected

	 */

	protected function render() {

		$settings = $this->get_settings_for_display();
		
		?>
		<div class="xevso-counters">
			<div class="xevso-counters-box " >
				<div class="counters-icons">
					<i class="<?php echo esc_attr($settings['xevso_counter_tow_icon']['value']); ?>"></i>
				</div>
				<div class="counters-content">
					<div class="countr_text">
						<div class="counters-title">
							<h5><?php echo esc_html($settings['xevso_counter_tow_title']) ?></h5>
						</div>
						<h2>
							<div class="timer counter wow fadeInUp" data-wow-delay="1s" data-wow-duration="2s" data-to="<?php echo esc_html($settings['xevso_counter_tow_number']) ?>" data-speed="5000">
								<span class="counters count"><?php echo esc_html($settings['xevso_counter_tow_number']) ?></span>
							</div>
						</h2>
					</div>
				</div>
			</div>
		</div>
			<script>
			jQuery(document).ready(function($) {
				"use strict";
				$(".timer").countTo();
				$(".count").appear(function() {
				    $(".timer").countTo();
				}, {
				    accY: -200
				});
			});
			</script>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_counter_tow_Widget );