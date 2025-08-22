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
class xevso_screenshot_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-screenshot';
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
		return esc_html__( 'xevso screenshot', 'xevsocore' );
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
		return [ 'xevso', 'screenshot' ];
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
			'xevso_clients_contens',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
            $repeater = new \Elementor\Repeater();
            
            $repeater->add_control(
			'xevso_client_logo', [
				'label' => esc_html__( 'Client Logo', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'xevso_clili_enable',
			[
			    'label'         => esc_html__( 'Enable Link', 'xevsocore' ),
			    'type'          => Controls_Manager::SWITCHER,
			    'label_on'      => esc_html__( 'On', 'xevsocore' ),
			    'label_off'     => esc_html__( 'Off', 'xevsocore' ),
			    'return_value'  => 'yes',
			    'default'       => 'yes',
			]
		);
		$repeater->add_control(
			'xevso_client_links',
			[
				'label' => esc_html__( 'Select Link', 'xevsocore' ),
				'type'  => Controls_Manager::SELECT,
				'default'	=> 'extranal',
				'options' => [
					'no' => esc_html__( 'Select Options', 'xevsocore' ),
					'extranal' => esc_html__( 'Extranal', 'xevsocore' ),
					'page' =>  esc_html__( 'Page', 'xevsocore' ),
				],
				'condition' 	=> [
                        	'xevso_clili_enable' => 'yes',
                  	]
			]
		);
		$repeater->add_control(
			'xevso_client_extranal',
			[
				'label' => esc_html__( 'Extranal Link', 'xevsocore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => array(
					'xevso_client_links' => 'extranal',
					'xevso_clili_enable' => 'yes',
				),
				'placeholder' => esc_html__( 'Add Extranal Link', 'xevsocore' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'xevso_client_page_link',
			[
				'label' => esc_html__( 'Page Link', 'xevsocore' ),
				'type' => Controls_Manager::SELECT,
				'options' => xevso_page_list(),
				'condition' => array(
					'xevso_client_links' => 'page',
					'xevso_clili_enable' => 'yes',
				),
			]
		);
		$this->add_control(
			'xevso_client_slides',
			[
				'label' => esc_html__( 'Clients', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'xevso_client_logo' => esc_html__( 'Client Iamge', 'xevsocore' ),
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_clients_config',
			[
				'label' => esc_html__( 'Slide Configaration', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_client_slide_on',
			[
				'label'         => esc_html__( 'Enable Slider ', 'xevsocore' ),
			    	'type'          => Controls_Manager::SWITCHER,
			    	'label_on'      => esc_html__( 'On', 'xevsocore' ),
			    	'label_off'     => esc_html__( 'Off', 'xevsocore' ),
			    	'return_value'  => 'yes',
			    	'default'       => 'no',
			]
		);
		$this->add_control(
			'xevso_clsl_loop',
			[
			    'label'         => esc_html__( 'Enable Loop ', 'xevsocore' ),
			    'type'          => Controls_Manager::SWITCHER,
			    'label_on'      => esc_html__( 'On', 'xevsocore' ),
			    'label_off'     => esc_html__( 'Off', 'xevsocore' ),
			    'return_value'  => 'yes',
			    'default'       => 'no',
			    'condition' 	=> [
                        	'xevso_client_slide_on' => 'yes',
                  	]
			]
		);
		$this->add_control(
			'xevso_clsl_speed',
			[
				'label' 	=> esc_html__( 'Slide Speed', 'xevsocore' ),
			    	'type' 	=> Controls_Manager::NUMBER,
			    	'min' 	=> 500,
			    	'max' 	=> 5000,
			    	'step' 	=> 10,
			    	'default' 	=> 1000,
				'condition' => array(
					'xevso_client_slide_on' 	=> 'yes',
					'xevso_clsl_loop' 		=> 'yes',
					
				)
			]
		);
		$this->add_control(
			'xevso_clsl_aloop',
			[
			    'label'         => esc_html__( 'Enable Auto Loop ', 'xevsocore' ),
			    'type'          => Controls_Manager::SWITCHER,
			    'label_on'      => esc_html__( 'On', 'xevsocore' ),
			    'label_off'     => esc_html__( 'Off', 'xevsocore' ),
			    'return_value'  => 'yes',
			    'default'       => 'no',
			    'condition' 	=> [
                        	'xevso_clsl_loop' => 'yes',
                  	]
			]
		);
		$this->add_control(
			'xevso_clsl_aspeed',
			[
				'label' 	=> esc_html__( 'Slide auto Speed', 'xevsocore' ),
			    	'type' 	=> Controls_Manager::NUMBER,
			    	'min' 	=> 500,
			    	'max' 	=> 5000,
			    	'step' 	=> 50,
			    	'default' 	=> 1000,
			   	'condition' => array(
					'xevso_clsl_aloop' => 'yes',
					'xevso_clsl_loop' 	=> 'yes',
					'xevso_client_slide_on' 	=> 'yes',
					
				)
			]
		);
		$this->add_control(
			'xevso_clsl_nav',
			[
			    'label'         => esc_html__( 'Enable Nav ', 'xevsocore' ),
			    'type'          => Controls_Manager::SWITCHER,
			    'label_on'      => esc_html__( 'On', 'xevsocore' ),
			    'label_off'     => esc_html__( 'Off', 'xevsocore' ),
			    'return_value'  => 'yes',
			    'default'       => 'no',
			    'condition' 	=> [
                        	'xevso_client_slide_on' => 'yes',
                  	]
			]
		);
		$this->add_control(
			'xevso_clsl_dot',
			[
			    'label'         => esc_html__( 'Enable Dots ', 'xevsocore' ),
			    'type'          => Controls_Manager::SWITCHER,
			    'label_on'      => esc_html__( 'On', 'xevsocore' ),
			    'label_off'     => esc_html__( 'Off', 'xevsocore' ),
			    'return_value'  => 'yes',
			    'default'       => 'no',
			    'condition' 	=> [
                        	'xevso_client_slide_on' => 'yes',
                  	]
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_client_styles',
			[
				'label' => esc_html__( 'Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_client_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .client-section' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		  );        
		  $this->add_responsive_control(
			'xevso_client_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .client-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		  );   
		  $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .client-section',
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
		$dynamic_id = rand(1241, 3256);
		if($settings['xevso_client_slide_on'] == 'yes' ){
			if($settings['xevso_clsl_dot'] == 'yes' ){
				$dots = 'true';
			}else{
				$dots = 'false';
			}
			if($settings['xevso_clsl_nav'] == 'yes' ){
				$nav = 'true';
			}else{
				$nav = 'false';
			}
			if($settings['xevso_clsl_aloop'] == 'yes' ){
				$aloop = 'true';
			}else{
				$aloop = 'false';
			}
			if($settings['xevso_clsl_loop'] == 'yes' ){
				$loop = 'true';
			}else{
				$loop = 'false';
			}
			echo '
			<script>
			jQuery(document).ready(function($) {
				"use strict";
				$("#clients-'.esc_attr($dynamic_id).'").slick({
					slidesToShow: 5,
					slidesToScroll: 1,
					dots: '.esc_attr($dots).',
					infinite: '.esc_attr($loop).',
					autoplay: '.esc_attr($aloop).',
					arrows: '.esc_attr($nav).',';
					if($aloop == 'true'){
					echo 'speed: '.esc_attr($settings['xevso_clsl_speed']).',';
					}
					if($aloop == 'true'){
					echo 'autoplaySpeed: '.esc_attr($settings['xevso_clsl_aspeed']).',';
					}
					echo '
					responsive: [
						{
						breakpoint: 1024,
							settings: {
								slidesToShow: 3,
								slidesToScroll: 3,
							}
						},
						{
							breakpoint: 600,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 2
							}
						},
						{
							breakpoint: 480,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						}
					]
				});
			});
			</script>';
            }
		?>
		<?php if( !empty( $settings['xevso_client_slides']) ) : ?>
		<div class="client-screenshot">
			<div class="client-items" id="clients-<?php echo esc_attr($dynamic_id); ?>">
			<?php foreach ( $settings['xevso_client_slides'] as $xevso_client_slide  ) :
			if( $xevso_client_slide['xevso_client_links'] == 'page' ){
				$clientource = get_page_link( $xevso_client_slide['xevso_client_page_link'] );
			}else{
				$clientource =  $xevso_client_slide['xevso_client_extranal'];
			}
			?>
			<div class="item">
				<?php if(!empty( $xevso_client_slide['xevso_clili_enable'] == true )) : ?>
				<a href="<?php echo esc_url($clientource); ?>">
					<?php echo wp_get_attachment_image( $xevso_client_slide['xevso_client_logo']['id'], 'large' ); ?>
				</a>
				<?php else : ?>
					<?php echo wp_get_attachment_image( $xevso_client_slide['xevso_client_logo']['id'], 'large' ); ?>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_screenshot_Widget );