<?php
namespace Elementor;

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor progress widget.
 *
 * Elementor widget that displays an escalating progress bar.
 *
 * @since 1.0.0
 */
class xevso_pricing_two_Widget extends \Elementor\Widget_Base {

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
        return 'xevso-pricing-two';
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
        return esc_html__( 'xevso Pricing Table Two', 'xevsocore' );
    }

    public function get_categories() {
        return ['xevsocore'];
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
        return ['xevso', 'Pricing two'];
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
            'xevso_pricing_two_section',
            [
                'label' => esc_html__( 'Monthly', 'xevsocore' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'xevso_pricing_two_monthly_title',
            [
                'label'   => esc_html__( 'Monthly', 'xevsocore' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Monthly', 'xevsocore' ),
            ]
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'xevso_pricing_two_title',
            [
                'label'   => esc_html__( 'Title', 'xevsocore' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( 'Pricing Title', 'xevsocore' ),
            ]
        );
        $repeater->add_control(
            'xevso_pricing_two_month',
            [
                'label'   => esc_html__( 'Month', 'xevsocore' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( 'Per Month', 'xevsocore' ),
            ]
        );
        $repeater->add_control(
            'xevso_pricing_two_money',
            [
                'label'   => esc_html__( 'Amount with Currency ', 'xevsocore' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '$99.99',
            ]
        );
        $repeater->add_control(
            'xevso_pricing_two_dec',
            [
                'label'   => esc_html__( 'Pricing Description', 'xevsocore' ),
                'type'    => Controls_Manager::WYSIWYG,
                'default' => wp_kses(
                    __( '
				    <ul>
				    	<li>Brandwidth: <strong>2GB</strong></li>
				    	<li>Clint & Product: <strong>2GB</strong></li>
				    	<li>onlinespace: <strong>2GB</strong></li>
				    	<li>Domain: <strong>2</strong></li>
				    	<li>Hidden Fees: <strong>0</strong></li>
				    </ul>', 'xevsocore' ),
                    array(
                        'ul'     => array(),
                        'li'     => array(),
                        'strong' => array(),
                        'span'   => array(),
                    )
                ),
            ]
        );
        $repeater->add_control(
            'xevso_pricing_two_link',
            [
                'label'   => esc_html__( 'button Link', 'xevsocore' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '#',
            ]
        );
        $repeater->add_control(
            'xevso_pricing_two_link_text',
            [
                'label'   => esc_html__( 'Button Text', 'xevsocore' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html( 'Select plan', 'xevsocore' ),
            ]
        );
        $this->add_control(
            'xevso_pricing_two_lists',
            [
                'label'   => esc_html__( 'Monthly Pricing List', 'xevsocore' ),
                'type'    => \Elementor\Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'xevso_pricing_two_title'     => esc_html__( 'Premium', 'xevsocore' ),
                        'xevso_pricing_two_month'     => esc_html__( 'Per Month', 'xevsocore' ),
                        'xevso_pricing_two_money'     => esc_html__( '$30', 'xevsocore' ),
                        'xevso_pricing_two_dec'       => wp_kses(
                            __( '
                            <ul>
                                <li>Brandwidth: <strong>2GB</strong></li>
                                <li>Clint & Product: <strong>2GB</strong></li>
                                <li>onlinespace: <strong>2GB</strong></li>
                                <li>Domain: <strong>2</strong></li>
                                <li>Hidden Fees: <strong>0</strong></li>
                            </ul>', 'xevsocore' ),
                            array(
                                'ul'     => array(),
                                'li'     => array(),
                                'strong' => array(),
                                'span'   => array(),
                            )
                        ),
                        'xevso_pricing_two_year_link' => '#',
                        'xevso_pricing_two_link_text' => esc_html( 'Select plan', 'xevsocore' ),
                    ],
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_pricing_two_section_year',
            [
                'label' => esc_html__( 'Yearly', 'xevsocore' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'xevso_pricing_two_menu_title_year',
            [
                'label'   => esc_html__( 'Yearly', 'xevsocore' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Yearly', 'xevsocore' ),
            ]
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'xevso_pricing_two_title_year',
            [
                'label'   => esc_html__( 'Title', 'xevsocore' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( 'Pricing Title', 'xevsocore' ),
            ]
        );
        $repeater->add_control(
            'xevso_pricing_two_per_year',
            [
                'label'   => esc_html__( 'Year', 'xevsocore' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( 'Per year', 'xevsocore' ),
            ]
        );
        $repeater->add_control(
            'xevso_pricing_two_money_year',
            [
                'label'   => esc_html__( 'Amount with Currency ', 'xevsocore' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '$99.99',
            ]
        );
        $repeater->add_control(
            'xevso_pricing_two_dec_year',
            [
                'label'   => esc_html__( 'Amount with Description ', 'xevsocore' ),
                'type'    => Controls_Manager::WYSIWYG,
                'default' => wp_kses(
                    __( '
				    <ul>
				    	<li>Brandwidth: <strong>2GB</strong></li>
				    	<li>Clint & Product: <strong>2GB</strong></li>
				    	<li>onlinespace: <strong>2GB</strong></li>
				    	<li>Domain: <strong>2</strong></li>
				    	<li>Hidden Fees: <strong>0</strong></li>
				    </ul>', 'xevsocore' ),
                    array(
                        'ul'     => array(),
                        'li'     => array(),
                        'strong' => array(),
                        'span'   => array(),
                    )
                ),
            ]
        );
        $repeater->add_control(
            'xevso_pricing_two_year_link',
            [
                'label'   => esc_html__( 'button Link', 'xevsocore' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '#',
            ]
        );
        $repeater->add_control(
            'xevso_pricing_two_link_text_year',
            [
                'label'   => esc_html__( 'Button Text', 'xevsocore' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html( 'Select plan', 'xevsocore' ),
            ]
        );
        $this->add_control(
            'xevso_pricing_two_year_lists',
            [
                'label'   => esc_html__( 'Yearly Pricing List', 'xevsocore' ),
                'type'    => \Elementor\Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'xevso_pricing_two_title_year' => esc_html__( 'Premium', 'xevsocore' ),
                        'xevso_pricing_two_per_year'   => esc_html__( 'Per year', 'xevsocore' ),
                        'xevso_pricing_two_money_year' => esc_html__( '$10', 'xevsocore' ),
                        'xevso_pricing_two_dec_year'   => wp_kses(
                            __( '
                            <ul>
                                <li>Brandwidth: <strong>2GB</strong></li>
                                <li>Clint & Product: <strong>2GB</strong></li>
                                <li>onlinespace: <strong>2GB</strong></li>
                                <li>Domain: <strong>2</strong></li>
                                <li>Hidden Fees: <strong>0</strong></li>
                            </ul>', 'xevsocore' ),
                            array(
                                'ul'     => array(),
                                'li'     => array(),
                                'strong' => array(),
                                'span'   => array(),
                            )
                        ),
                        'xevso_pricing_two_link_text'  => esc_html( 'Select plan', 'xevsocore' ),
                    ],
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
			'xevso_pricing_two_box_style',
			[
				'label' => esc_html__( 'Box Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'xevso_pricing_two_box_alignment',
			[
				'label' => __( 'Alignment', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'xevsocore' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'xevsocore' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'xevsocore' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
				  '{{WRAPPER}} .pricing-table-content' => 'text-align: {{VALUE}}',
			    ],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_box_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .pricing-table-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);        
		  $this->add_responsive_control(
			'xevso_pricing_two_box_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .pricing-table-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);  
		$this->start_controls_tabs(
			'xevso_pricing_two_box_tabs'
		);

		$this->start_controls_tab(
			'xevso_pricing_two_boxtab_normal',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing_two_box_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pricing-table-content',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_box_radius',
			[
				'label' => __( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				
				'selectors' => [
					'{{WRAPPER}} .pricing-table-content' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'xevso_pricing_two_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .pricing-table-box',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'xevso_pricing_two_boxtab_hover',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing_two_boxh_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pricing-table-content:hover',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_boxh_radius',
			[
				'label' => __( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				
				'selectors' => [
					'{{WRAPPER}} .pricing-table-content:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'xevso_pricing_two_boxh_shadow',
				'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .pricing-table-box:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_pricing_two_menu_CSS',
			[
				'label' => esc_html__( 'Menu', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'xevso_pricing_two_box_menu_alignment',
			[
				'label' => __( 'Alignment', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'xevsocore' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'xevsocore' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'xevsocore' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
				  '{{WRAPPER}} .pr-menu' => 'text-align: {{VALUE}}',
			    ],
			]
		);
		$this->add_control(
			'xevso_pricing_two_menu_boxn',
			[
				'label' => __( 'Menu Box CSS', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_menu_box_margin',
			[
				'label' => esc_html__( 'Margin', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '35',
					'left' => '0',
					'isLinked' => true
					],
				'selectors' => [
					'{{WRAPPER}} .pricing-tow-section ul.nav.nav-tabs' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_menu_box_padding',
			[
				'label' => esc_html__( 'Padding', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '8',
					'right' => '10',
					'bottom' => '10',
					'left' => '10',
					'isLinked' => true
					],
				'selectors' => [
					'{{WRAPPER}} .pricing-tow-section ul.nav.nav-tabs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_menu_box_bg',
			[
				'label' => esc_html__( 'Background Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pricing-tow-section ul.nav.nav-tabs' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_menu_box_border_radus',
			[
				'label' => esc_html__( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-tow-section ul.nav.nav-tabs' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'xevso_pricing_two_menu_box_item_n',
			[
				'label' => __( 'Menu Item CSS', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_two_menu_item_typo',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .pricing-tow-section .nav-tabs .nav-link',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_menu_item_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pricing-tow-section .nav-tabs .nav-link' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_menu_item_bgcolor',
			[
				'label' => esc_html__( 'BG Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pricing-tow-section .nav-tabs .nav-link' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_menu_item_radius',
			[
				'label' => esc_html__( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-tow-section .nav-tabs .nav-link' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_menu_item_height',
			[
				'label' => esc_html__( 'Height', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-tow-section .nav-tabs .nav-link' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'xevso_pricing_two_menu_box_item_nh',
			[
				'label' => __( 'Menu Active Item CSS', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_menu_item_hcolor',
			[
				'label' => esc_html__( 'Active Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pricing-tow-section .nav-tabs .nav-link.active' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_menu_item_bghcolor',
			[
				'label' => esc_html__( 'Active BG Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pricing-tow-section .nav-tabs .nav-link.active' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'xevso_pricing_two_menu_item_shadow',
				'label' => esc_html__( 'Shadow', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .pricing-tow-section .nav-tabs .nav-item.show .nav-link, .pricing-tow-section .nav-tabs .nav-link.active',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_pricing_two_header_style',
			[
				'label' => esc_html__( 'Header Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_header_color',
			[
				'label' => esc_html__( 'Title Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price-header h2' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_headerh_color',
			[
				'label' => esc_html__( 'Title Hover Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pricing-table-box:hover h2' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_two_header_typo',
				'label' => esc_html__( 'Title Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .price-header h2',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_headers_color',
			[
				'label' => esc_html__( 'Sub Title Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price-header p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_headersh_color',
			[
				'label' => esc_html__( 'Sub Title Hover Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pricing-table-box:hover p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_two_headers_typo',
				'label' => esc_html__( 'Sub Title Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .price-header p',
			]
		);

		$this->add_responsive_control(
			'xevso_pricing_two_price_color',
			[
				'label' => esc_html__( 'Price Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price-header h3' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_priceh_color',
			[
				'label' => esc_html__( 'Price Hover Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pricing-table-box:hover h3' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_two_price_typo',
				'label' => esc_html__( 'Price Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .price-header h3',
			]
		);

		$this->add_responsive_control(
			'xevso_pricing_two_header_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .price-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		  );        
		  $this->add_responsive_control(
			'xevso_pricing_two_header_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			   
			    'selectors' => [
				  '{{WRAPPER}} .price-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing_two_header_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .price-header',
			]
		);
		$this->add_control(
			'xevso_pricing_two_header_bgt',
			[
				'label' => __( 'Hover Background', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing_two_header_hbg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .pricing-table-box:hover .price-header',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_pricing_two_dec_style',
			[
				'label' => esc_html__( 'Content Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_dec_color',
			[
				'label' => esc_html__( 'Content Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price-dec ul li' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_dech_color',
			[
				'label' => esc_html__( 'Content Hover Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pricing-table-box:hover li' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_two_dec_typo',
				'label' => esc_html__( 'Price Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .price-dec ul li',
			]
		);

		$this->add_responsive_control(
			'xevso_pricing_two_dec_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .price-dec' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		  );        
		  $this->add_responsive_control(
			'xevso_pricing_two_dec_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .price-dec' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_pricing_two_footer_style',
			[
				'label' => esc_html__( 'Footer Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_footer_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pric-footer a.blob-btn' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing_two_footer_btnbgcolor',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn',
			]
		);
		$this->add_control(
			'xevso_pricing_two_note',
			[
				'label' => __( 'Hover Button Color and Background Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_footer_btnhcolor',
			[
				'label' => esc_html__( 'Hover Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pric-footer a.blob-btn:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing_two_footer_hbgcolor',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn:hover',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_two_footer_typo',
				'label' => esc_html__( 'Button Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .pric-footer a.blob-btn',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_two_footer_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .pric-footer a.blob-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);  
		$this->add_responsive_control(
			'xevso_pricing_two_footer_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .pric-footer a.blob-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);  
		$this->add_control(
			'xevso_pricing_two_footer_radius',
			[
				'label' => __( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .blob-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
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
        <div class="pricing-tow-section">
			<div class="pr-menu">
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" href="#intech_monthly" role="tab" data-toggle="tab"><?php echo esc_html( $settings['xevso_pricing_two_monthly_title'] ); ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#intech_yearly" role="tab" data-toggle="tab"><?php echo esc_html( $settings['xevso_pricing_two_menu_title_year'] ); ?></a>
					</li>
				</ul>
			</div>
                <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active show" id="intech_monthly">
                    <div class="row">
                        <?php foreach ( $settings['xevso_pricing_two_lists'] as $monthly_pricing ): ?>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-4">

                        <div class="pricing-table-box pricing-tow-box monthly">
                            <div class="price-header d-flex justify-content-between align-items-center">
                                <div class="pricing-left">
                                    <h2><?php echo esc_html( $monthly_pricing['xevso_pricing_two_title'] ); ?></h2>
                                    <p><?php echo esc_html( $monthly_pricing['xevso_pricing_two_month'] ); ?></p>
                                </div>
                                <div class="pricing-right">
                                    <h3><?php echo esc_html( $monthly_pricing['xevso_pricing_two_money'] ) ?></h3>
                                </div>
                            </div>
                            <div class="pricing-table-content">
                                <div class="pric-box">
                                    <div class="price-dec">
                                        <?php echo wp_kses_post( wpautop( $monthly_pricing['xevso_pricing_two_dec'] ) ); ?>
                                    </div>
                                    <div class="pric-footer">
                                        <a href="<?php echo esc_url( $monthly_pricing['xevso_pricing_two_link'] ) ?>" class="blob-btn"><?php echo esc_html( $monthly_pricing['xevso_pricing_two_link_text'] ); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>
                        <?php endforeach;?>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="intech_yearly">
                    <div class="row">
                        <?php foreach ( $settings['xevso_pricing_two_year_lists'] as $yearly_pricing ): ?>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                            	<div class="pricing-table-box pricing-tow-box yearly">
                                    <div class="price-header d-flex justify-content-between align-items-center">
                                        <div class="pricing-left">
                                            <h2><?php echo esc_html( $yearly_pricing['xevso_pricing_two_title_year'] ); ?></h2>
                                            <p><?php echo esc_html( $yearly_pricing['xevso_pricing_two_per_year'] ); ?></p>
                                        </div>
                                        <div class="pricing-right">
                                            <h3><?php echo esc_html( $yearly_pricing['xevso_pricing_two_money_year'] ) ?></h3>
                                        </div>
                                    </div>
                                    <div class="pricing-table-content">
                                        <div class="pric-box">
                                            <div class="price-dec">
                                                <?php echo wp_kses_post( wpautop( $yearly_pricing['xevso_pricing_two_dec_year'] ) ); ?>
                                            </div>
                                            <div class="pric-footer">
                                                <a href="<?php echo esc_url( $yearly_pricing['xevso_pricing_two_year_link'] ) ?>" class="blob-btn"><?php echo esc_html( $yearly_pricing['xevso_pricing_two_link_text_year'] ); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;?>
                    <div>
                </div>
            </div>
        </div>
		<?php
		
		
		
		
		
		
		
}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_pricing_two_Widget );