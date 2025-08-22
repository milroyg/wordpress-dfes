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
class xevso_blog_three_Widget extends \Elementor\Widget_Base {

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
        return 'blogthree';
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
        return esc_html__( ' Blog Three', 'xevsocore' );
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
        return ['xevso', 'blog'];
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
            'xevso_blog_section',
            [
                'label' => esc_html__( 'content', 'xevsocore' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'xevso_blog_style',
            [
                'label'   => esc_html__( 'Select Style', 'xevsocore' ),
                'type'    => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => esc_html__( 'Style One', 'xevsocore' ),
                    '2' => esc_html__( 'Style Two', 'xevsocore' ),
                ],
            ]
        );
        $this->add_control(
            'xevso_blog_column',
            [
                'label'   => esc_html__( 'Select Column', 'xevsocore' ),
                'type'    => Controls_Manager::SELECT,
                'default' => '4',
                'options' => [
                    '12' => esc_html__( '1', 'xevsocore' ),
                    '6' => esc_html__( '2', 'xevsocore' ),
                    '4' => esc_html__( '3', 'xevsocore' ),
                ],
                'condition' => [
                    'xevso_blog_style' => '1',
                ],
            ]
        );
        $this->add_control(
            'xevso_blog_show',
            [
                'label' => esc_html__( 'Show Items', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'default' => 3,
            ]
        );
        $this->add_control(
            'xevso_blog_limit',
            [
                'label' => esc_html__( 'Limit Text', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 5,
                'max' => 50,
                'step' => 1,
                'default' => 19,
            ]
        );
        $this->add_control(
            'xevso_blog_navication',
            [
                'label' => esc_html__( 'Show Navication', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
            ]
        );
        $this->add_responsive_control(
            'xevso_blog_navication_alinge',
            [
                'label'     => esc_html__( 'Aligment', 'xevsocore' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__( 'Left', 'xevsocore' ),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'xevsocore' ),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__( 'Right', 'xevsocore' ),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'separator' => 'before',
                'default'   => 'left',
                'selectors' => [
                    '{{WRAPPER}} .cpaginations' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'xevso_blog_navication' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_blog_styles',
            [
                'label' => esc_html__( 'All Area', 'xevsocore' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_bg_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blog-contents-ares',
			]
		);
		
        $this->add_responsive_control(
            'xevso_blog_top_padding',
            [
                'label'      => esc_html__( 'Padding', 'xevsocore' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .blog-contents-ares' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );
        $this->add_responsive_control(
            'xevso_blog_top_margin',
            [
                'label'      => esc_html__( 'Margin', 'xevsocore' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .blog-contents-ares' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );
        $this->add_responsive_control(
            'xevso_blog_top_aline',
            [
                'label'     => esc_html__( 'aligment', 'xevsocore' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__( 'Left', 'xevsocore' ),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'xevsocore' ),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__( 'Right', 'xevsocore' ),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'separator' => 'before',
                'default'   => 'left',
                'selectors' => [
                    '{{WRAPPER}} .blog-top' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_blog_titles_styles',
            [
                'label' => esc_html__( 'Title', 'xevsocore' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'xevso_blog_p_right',
            [
                'label'      => esc_html__( 'Padding Right', 'xevsocore' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min'  => 10,
                        'max'  => 200,
                        'step' => 1,
                    ],
                    '%'  => [
                        'min' => 5,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => 37,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .blog-boxs.blog-post-2 .col-lg-7.col-xl-7 .blog-title h2' => 'padding-right: {{SIZE}}{{UNIT}};',
                ],
                'condition'  => [
                    'xevso_blog_style' => '2',
                ],
            ]
        );
        $this->add_control(
            'xevso_blog_title_color',
            [
                'label'     => esc_html__( 'Color', 'xevsocore' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blog-title h2 a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'xevso_blog_title_hcolor',
            [
                'label'     => esc_html__( 'Hover Color', 'xevsocore' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blog-title h2 a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'xevso_blog_title__typo',
                'label'    => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .blog-title h2 a',
            ]
        );
        $this->add_responsive_control(
            'xevso_blog_title_padding',
            [
                'label'      => esc_html__( 'Padding', 'xevsocore' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .blog-title h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );
        $this->add_responsive_control(
            'xevso_blog_title_margin',
            [
                'label'      => esc_html__( 'Margin', 'xevsocore' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .blog-title h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_blog_content_styles',
            [
                'label' => esc_html__( 'Content', 'xevsocore' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->add_control(
            'xevso_blog_top_use_color',
            [
                'label'     => esc_html__( ' User Color', 'xevsocore' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blog-top ul li a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'xevso_blog_top_color',
            [
                'label'     => esc_html__( 'Date Color', 'xevsocore' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blog-top ul li' => 'color: {{VALUE}};',
                ],
            ]
        );
		
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'xevso_blog_top__typo',
                'label'    => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .blog-top ul li,.blog-top ul li a',
            ]
        );
		
		
		
        $this->add_responsive_control(
            'xevso_blog_dec_padding',
            [
                'label'      => esc_html__( 'Padding', 'xevsocore' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .blog-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );
        $this->add_responsive_control(
            'xevso_blog_dec_margin',
            [
                'label'      => esc_html__( 'Margin', 'xevsocore' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .blog-body' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );
        $this->add_responsive_control(
            'xevso_blog_dec_lineheight',
            [
                'label'      => esc_html__( 'Line Height', 'xevsocore' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => 24,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .blog-body p' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
			'xevso_blcon_ncolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .blog-body p' => 'color: {{VALUE}}',
				],
			]
		);
		
		
		
		
        $this->end_controls_section();
        // Button Start
		$this->start_controls_section(
			'xevso_blogs_btns',
			[
				'label' => esc_html__( 'Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_blogs_btns_typo',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn',
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_margin',
			[
				'label' => esc_html__( 'Margin', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .blob-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_padding',
			[
				'label' => esc_html__( 'Padding', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '15',
					'right' => '30',
					'bottom' => '15',
					'left' => '30',
					'isLinked' => true
				],
				'selectors' => [
					'{{WRAPPER}} .blob-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'xevso_blogs_btns_tabs'
		);
		$this->start_controls_tab(
			'xevso_blogs_btns_ntab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_ncolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .blob-btn' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_blogs_btns_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blog-footer .left a',
			]
		);
		

		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_blogs_btns_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_nradius',
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
					'{{WRAPPER}} .blob-btn__inner' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'xevso_blogs_btns_htab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_hcolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .blob-btn:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_blogs_btns_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blog-footer .left a:hover',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_blogs_btns_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn:hover .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_hradius',
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
					'{{WRAPPER}} .blob-btn:hover .blob-btn__inner' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		// Button End
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
	<div class="blog-boxs blog-post">
		<div class="blog-box">
            <div class="blog-items row">
		        <?php
                global $post;
                $count = 0;
                $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                $p = new \WP_Query( array( 
                'posts_per_page' => esc_attr($settings['xevso_blog_show'] ),
                'post_type' => 'post',
                'paged' => $paged
                ));
                while ( $p->have_posts() ): $p->the_post();
                    $count++;
                ?>
                <div class="item <?php if ( $settings['xevso_blog_style'] == '2' ): ?>col-12 col-md-6 <?php if ( $count == 1 ): ?>col-lg-7 <?php else: ?>col-lg-5 col-xl-5<?php endif;?><?php else: ?>col-12 col-md-6 col-lg-<?php echo esc_attr($settings['xevso_blog_column']); ?><?php endif;?>">
                    <div id="post-<?php the_ID();?>" <?php post_class( 'post-single' );?>> 
                        <div class="blog-contents-ares">
                           
                            <div class="blog-title">
                                <h2><a href="<?php echo esc_url( the_permalink() ); ?>"><?php the_title();?></a></h2>
                            </div>
                            <div class="blog-body">
                                <?php if ( $settings['xevso_blog_style'] == '2' && $count == 1 ): ?>
                                    <p><?php echo wp_trim_words( get_the_content(), 25 ); ?></p>
                                <?php else: ?>
                                    <p><?php echo wp_trim_words( get_the_content(), esc_attr($settings['xevso_blog_limit']) ); ?></p>
                                <?php endif;?>
                            </div>
                            <div class="blog-footer">
                                <div class="left">
                                    <div class="theme-blogs_btns">
                                        <a href="<?php echo esc_url( the_permalink() ); ?>" class="blob-btn"><?php esc_html_e( 'Read More', 'xevsocore' );?>
                                        </a>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; wp_reset_query();?>
            </div>
            <?php if($settings['xevso_blog_navication'] == 'yes') : ?>
            <div class="cpaginations">
                <?php xevso_paginate_nav( $p ); ?>
            </div>
            <?php endif; ?>
        </div>
	</div>
	<?php
}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_blog_three_Widget );