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
class xevso_project_two_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-project-two';
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
		return esc_html__( 'xevso Project Two', 'xevsocore' );
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
		return [ 'xevso', 'project Two' ];
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
			'xevso_projects_contnet',
			[
				'label' => esc_html__( 'Options', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_project_two_manu',
			[
				'label' => esc_html__( 'Enable Manu', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'xevso_project_column',
			[
				'label' => esc_html__( 'Select Style', 'xevsocore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'12'  => esc_html__( 'One Column', 'xevsocore' ),
					'6'  => esc_html__( 'Two Column', 'xevsocore' ),
					'4'  => esc_html__( 'Four Column', 'xevsocore' ),
				],
			]
		);
		$this->add_control(
			'xevso_project_two_per_post',
			[
			    'label' => esc_html__( 'Show Items', 'xevsocore' ),
			   'type' 	=> Controls_Manager::NUMBER,
			    	'min' 	=> 1,
			    	'max' 	=> 8,
			    	'step' 	=> 1,
			    	'default' 	=> -1,
			]
		);
		$this->add_control(
			'xevso_project_two_pagi',
			[
				'label' => esc_html__( 'Enable Pagination', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_project_two_item_menu_style',
			[
				'label' => esc_html__( 'Item Menu', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
            'xevso_project_two_menu',
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
				'justify' => [
					'title' => esc_html__( 'Justified', 'xevsocore' ),
					'icon' => 'fa fa-align-justify',
				],
			],
			'selectors' => [
				'{{WRAPPER}} ul.project-shorting' => 'text-align: {{VALUE}};',
			],
			'default' => 'center',
			'separator' =>'before',
			]
		);
		$this->add_control(
			'xevso_project_two_menu_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .protfolio-menus ul li' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'xevso_project_two_menu_hcolor',
			[
				'label' => esc_html__( 'Hover Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .protfolio-menus ul li:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'xevso_project_two_menu_bgcolor',
			[
				'label' => esc_html__( 'background Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#2d66ff',
				'selectors' => [
					'{{WRAPPER}} .blob-btn__inner' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_bproj_icot_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .protfolio-menus li.blob-btn.active',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_project_two_stypo',
			    'label' => esc_html__( 'Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .protfolio-menus ul li',
			]
		);
		$this->add_responsive_control(
			'xevso_project_two_mpadding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'default' => [
				  'top' => '10',
				  'right' => '45',
				  'bottom' => '10',
				  'left' => '45',
				  'isLinked' => false
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .protfolio-menus ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->add_responsive_control(
			'xevso_project_two_mmargin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'default' => [
				  'top' => '5',
				  'right' => '5',
				  'bottom' => '5',
				  'left' => '5',
				  'isLinked' => false
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .protfolio-menus ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_project_two_item_style',
			[
				'label' => esc_html__( 'Item', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_project_two_item_img_blur',
			[
				'label' => esc_html__( 'Image Blur', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px',],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 3,
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-img:hover img' => 'filter: blur({{SIZE}}{{UNIT}});',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_project_two_item_cat_typo',
				'label' => esc_html__( 'Category Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .pt-portfolio-link span',
			]
		);
		$this->add_responsive_control(
			'xevso_project_two_item_cat_color',
			[
				'label' => esc_html__( 'Categry Color Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-portfolio-link span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_project_two_item_title_typo',
				'label' => esc_html__( 'Title Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .pt-portfolio-info h5 a',
			]
		);
		$this->add_responsive_control(
			'xevso_project_two_item_title_color',
			[
				'label' => esc_html__( 'Title Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-portfolio-info h5 a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_project_two_item_title_hcolor',
			[
				'label' => esc_html__( 'Title Hover Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-portfolio-info h5 a:hover' => 'color: {{VALUE}}',
				],
			]
		);
	
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_prject_two_item_bg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pt-portfolio-info',
			]
		);
	$this->end_controls_section();
		
		$this->start_controls_section(
			'xevso_project_icons_style',
			[
				'label' => esc_html__( 'Icon', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(

			'xevso_counter_tow_style_icon_color',

			[

				'label' => esc_html__( 'Color', 'xevsocore' ),

				'type' => Controls_Manager::COLOR,

				'selectors' => [

					'{{WRAPPER}} .pt-portfolio-info i' => 'color: {{VALUE}};',

				],

			]

		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_counter_tow_style_icon_typo',
				'selector' => '{{WRAPPER}} .pt-portfolio-info i',
			]
		);
		
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_buttons_icot_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .pt-portfolio-info i',
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
					'{{WRAPPER}} .pt-portfolio-info i' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'xevso_project_two_item_margin',
			[
				'label' => esc_html__( 'Margin', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-portfolio-info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_project_two_item_padding',
			[
				'label' => esc_html__( 'Padding', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '20',
					'right' => '30',
					'bottom' => '20',
					'left' => '30',
					'isLinked' => true
					],
				'selectors' => [
					'{{WRAPPER}} .pt-portfolio-info i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();
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
		$dynamic_num = rand(35245545, 541541745);
			?>
			<script>
				jQuery(window).ready(function($) {
				'use strict';
				jQuery('.project-shorting li').on('click',function(){
					jQuery('.project-shorting li').removeClass('active');
					jQuery(this).addClass('active');
					var selector = jQuery(this).attr('data-filter');
					jQuery('.projectlist-<?php echo esc_attr($dynamic_num) ?>').isotope({
						filter:selector,
					});
				});
				});
				jQuery(window).on('load',function($) {
					'use strict';
					jQuery(".projectlist-<?php echo esc_attr($dynamic_num) ?>").isotope();
				});
			</script>
			<?php

		?>
		<div class="xevso-portfolio2-section">
			<?php $xevso_portfolios = get_terms( 'project_cat' ); 
			
			if( $settings['xevso_project_two_manu'] == 'yes' && !empty($xevso_portfolios) && ! is_wp_error( $xevso_portfolios ) ) : ?>
			<div class="protfolio-menus">
				<ul class="project-shorting">
					<li class="active blob-btn" data-filter="*"><?php esc_html_e('all','xevsocore'); ?>
					<span class="blob-btn__inner">
						<span class="blob-btn__blobs">
							<span class="blob-btn__blob"></span>
							<span class="blob-btn__blob"></span>
							<span class="blob-btn__blob"></span>
							<span class="blob-btn__blob"></span>
						</span>
					</span>
					</li>
					<?php foreach($xevso_portfolios as $xevso_portfolio) : ?>
					<li class="blob-btn" data-filter=".<?php echo esc_attr($xevso_portfolio->slug) ?>">
					<?php echo esc_html($xevso_portfolio->name) ?>
					<span class="blob-btn__inner">
						<span class="blob-btn__blobs">
							<span class="blob-btn__blob"></span>
							<span class="blob-btn__blob"></span>
							<span class="blob-btn__blob"></span>
							<span class="blob-btn__blob"></span>
						</span>
					</span>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
			<div class="protfolio-content-section">
				<div class="row projectlist-<?php echo esc_attr($dynamic_num); ?>">
					<?php  global $post;
					$paged = get_query_var('paged') ? get_query_var('paged') : 1;
					$p = new \WP_Query(array(
					'posts_per_page' => esc_attr($settings['xevso_project_two_per_post']),
					'post_type' => 'project',
					'paged' => $paged
					));
					while($p->have_posts()) : $p->the_post();
						$xevso_idd = get_the_ID();
						$xevso_pro_meta = get_post_meta($xevso_idd, 'xevso_project_meta', true);
						$project_catagory = get_the_terms( get_the_ID(), 'project_cat' );
					if ( $project_catagory && ! is_wp_error( $project_catagory ) ) {
						$project_cat_list = array();
						foreach ( $project_catagory as $project_cat ) {
							$project_cat_list[] = $project_cat->slug;
						}
						$project_catshow = join( ", ", $project_cat_list );
						$project_catshows = join( " ", $project_cat_list );
						
					}else{
						$project_catshow = '';
					}
					?>
					<div class="col-12 col-sm-12 col-md-6 col-lg-<?php echo esc_attr($settings['xevso_project_column']); ?>  <?php echo esc_attr($project_catshows); ?> single-item">
						<div class="protfolio-box">
							<div class="portfolio-img">
								<?php the_post_thumbnail('xevso-project-image'); ?>
								<div class="pt-portfolio-info">
								<div class="folio-info-text">
								<h5><?php the_title(); ?></h5>
									<div class="pt-portfolio-link">
										<span><?php echo esc_html($project_catshow); ?></span>
									</div>
									<a href="<?php the_permalink(); ?>" class="theme-button">
											<i class="flaticon flaticon-arrow-pointing-to-right"></i>
										</a>
								</div>
								</div>
							</div>
						</div>
					</div>
				<?php endwhile; wp_reset_postdata(); wp_reset_query();?>
				</div>
				<?php if($settings['xevso_project_two_pagi'] == 'yes') : ?>
				<div class="cpaginations">
					<?php xevso_paginate_nav( $p ); ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php 
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_project_two_Widget );
