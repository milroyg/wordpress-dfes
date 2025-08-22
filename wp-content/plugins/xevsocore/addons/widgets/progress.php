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
class xevso_progress_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-progress';
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
		return esc_html__( 'xevso Progress', 'xevsocore' );
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
		return 'eicon-t-letter';
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
		return [ 'xevso', 'progress' ];
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
			'xevso_progress_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_progress_hadding',
			[
				'label' => esc_html__( 'Progress Hadding', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'You can check out our work. Are you ready for a better, more productive business?', 'xevsocore' ),
				'placeholder' => esc_html__( 'Type your Progress Hadding here', 'xevsocore' ),
			]
		);
		$this->add_control(
			'hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'xevso_progress_title',
			[
			    'label' => esc_html__( 'Progress Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Progress Title','xevsocore'),
			]
		);
		$repeater->add_control(
			'xevso_progress_num',
			[
			    'label' => esc_html__( 'Number', 'xevsocore' ),
			    'type' => Controls_Manager::NUMBER,
			    'default' => 90,
			]
		);
		$repeater->add_control(
			'xevso_progress_color',
			[
			    'label' => esc_html__( 'Opacity Color', 'xevsocore' ),
			    'type' => Controls_Manager::COLOR,
			    'default' => '#3575de', 
			]
		);
		$repeater->add_control(
			'xevso_progress_color2',
			[
			    'label' => esc_html__( 'Border Color', 'xevsocore' ),
			    'type' => Controls_Manager::COLOR,
			    'default' => '#ffffff',
			]
		);
		$this->add_control(
			'xevso_progress_items',
			[
				'label' => esc_html__( 'Progress Items', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'xevso_progress_title' => esc_html__( 'Title', 'xevsocore' ),
						'xevso_progress_num' => 90,
						'xevso_progress_color' => esc_html__( '+', 'xevsocore' ),
					],
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_progress_hadding_styles',
			[
				'label' => esc_html__( 'Hadding Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_progress_htypro',
			    'label' => __( 'Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .progress-hadding h2',
			]
		  );
		$this->add_control(
			'xevso_progress_hadding_color',
			[
				'label' => esc_html__( 'Hadding Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress-hadding h2' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_progress_hadding_aline',
			[
			    'label' => __( 'Alignment', 'xevsocore' ),
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
				  ]
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .progress-hadding h2' => 'text-align: {{VALUE}};',
			    ],
			    'separator' =>'before',
			]
		  );
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_progress_tab_section',
			[
			    'label' => esc_html__( 'Contents Style', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs('xevso_progress_tabs');

        	$this->start_controls_tab( 'xevso_progress_title_tabs',
			[
				'label' => esc_html__( 'Title', 'xevsocore' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_progress_ttle_typro',
			    'label' => __( 'Title Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .progress-title h3',
			]
		  );
		$this->add_control(
			'xevso_progress_title_color',
			[
				'label' => esc_html__( 'Title Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress-title h3' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'xevso_progress_num_tabs',
			[
				'label' => esc_html__( 'Number', 'xevsocore' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_progress_num_typro',
			    'label' => __( 'Title Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .progress-bar div span',
			]
		  );
		$this->add_control(
			'xevso_progress_num_color',
			[
				'label' => esc_html__( 'Number Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress-bar div span' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_progress_box_styles',
			[
				'label' => esc_html__( 'box Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_progress_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .progress-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);        
		$this->add_responsive_control(
			'xevso_progress_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .progress-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
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
		echo '
			<script>
			jQuery(document).ready(function($) {
				"use strict";
				$(".progress-bar").loading();
			});
			</script>';
		?>
		<div class="progress-box">
			<div class="porgree-hadding-section">
				<div class="progress-hadding">
					<h2><?php echo wp_kses_post(wpautop($settings['xevso_progress_hadding'])); ?></h2>
				</div>
			</div>
			<div class="progress-items row">
			<?php foreach ( $settings['xevso_progress_items'] as $xevso_progress_item  ) : ?>
				<div class="col-sm-6 col-md-3">
				<div class="item">
					<div class="progress-bar position" data-percent="<?php echo esc_attr($xevso_progress_item['xevso_progress_num']) ?>" data-duration="1000" data-color="<?php echo esc_html($xevso_progress_item['xevso_progress_color']); ?>,<?php echo esc_html($xevso_progress_item['xevso_progress_color2']); ?>"></div>
					<div class="progress-title">
						<h3><?php echo esc_html($xevso_progress_item['xevso_progress_title']); ?></h3>
					</div>
				</div>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	
		<?php
		
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_progress_Widget );