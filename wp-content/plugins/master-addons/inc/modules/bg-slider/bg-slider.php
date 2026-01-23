<?php

namespace MasterAddons\Modules;

use \Elementor\Element_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;

if (!class_exists('MasterAddons\Modules\JLTMA_Extension_Background_Slider')) {
class JLTMA_Extension_Background_Slider
{
	private static $_instance = null;

	private function __construct()
	{
		add_action('elementor/element/after_section_end', [$this, '_add_controls'], 10, 3);

		add_action('elementor/frontend/element/before_render', [$this, '_before_render'], 10, 1);
		add_action('elementor/frontend/column/before_render', [$this, '_before_render'], 10, 1);
		add_action('elementor/frontend/section/before_render', [$this, '_before_render'], 10, 1);
		add_action('elementor/frontend/container/before_render', [$this, '_before_render'], 10, 1);

		add_action('elementor/element/print_template', [$this, '_print_template'], 10, 2);
		add_action('elementor/section/print_template', [$this, '_print_template'], 10, 2);
		add_action('elementor/column/print_template', [$this, '_print_template'], 10, 2);
		add_action('elementor/container/print_template', [$this, '_print_template'], 10, 2);

		// Enqueue scripts early on frontend when needed
		add_action('wp_enqueue_scripts', [$this, 'maybe_enqueue_bg_slider_for_frontend'], 10);

		// Only enqueue scripts when needed (no longer auto-enqueue in editor)
		add_action('elementor/preview/enqueue_scripts', [$this, 'maybe_enqueue_bg_slider_for_preview']);
	}


	public function ma_el_add_js_css()
	{
		// CSS
		wp_enqueue_style('master-addons-vegas');

		// JS
		wp_enqueue_script('master-addons-vegas');
	}

	public function maybe_enqueue_bg_slider_for_preview()
	{
		// Check if any element on the page has bg slider enabled
		global $post;
		if (!$post) return;

		// Get Elementor data
		$document = \Elementor\Plugin::$instance->documents->get($post->ID);
		if (!$document) return;

		$data = $document->get_elements_data();
		if ($this->has_bg_slider_enabled($data)) {
			$this->ma_el_add_js_css();
		}
	}

	public function maybe_enqueue_bg_slider_for_frontend()
	{
		// Only run on Elementor pages
		if (!did_action('elementor/loaded')) {
			return;
		}

		// Check if we're on a frontend Elementor page
		if (is_admin() || (function_exists('elementorFrontend\is_edit_mode') && \Elementor\Plugin::$instance->editor->is_edit_mode())) {
			return;
		}

		// Check if any element on the page has bg slider enabled
		global $post;
		if (!$post) return;

		// Check if this post uses Elementor
		if (!\Elementor\Plugin::$instance->db->is_built_with_elementor($post->ID)) {
			return;
		}

		// Get Elementor data
		$document = \Elementor\Plugin::$instance->documents->get($post->ID);
		if (!$document) return;

		$data = $document->get_elements_data();
		if ($this->has_bg_slider_enabled($data)) {
			$this->ma_el_add_js_css();
		}
	}

	private function has_bg_slider_enabled($elements)
	{
		foreach ($elements as $element) {
			$settings = $element['settings'] ?? [];

			// Check if this element has bg slider enabled and has images
			if (isset($settings['ma_el_enable_bg_slider']) && $settings['ma_el_enable_bg_slider'] === 'yes' && 
				isset($settings['ma_el_bg_slider_images']) && !empty($settings['ma_el_bg_slider_images'])) {
				return true;
			}

			// Check child elements recursively
			if (!empty($element['elements'])) {
				if ($this->has_bg_slider_enabled($element['elements'])) {
					return true;
				}
			}
		}
		return false;
	}

	public function _add_controls($element, $section_id, $args)
	{
		if (('section' === $element->get_name() && 'section_background' === $section_id) || ('column' === $element->get_name() && 'section_style' === $section_id) || ('container' === $element->get_name() && 'section_background' === $section_id)) {

			$element->start_controls_section(
				'_ma_el_section_bg_slider',
				[
					'label' => __('Background Slider ', 'master-addons' )  . JLTMA_EXTENSION_BADGE,
					'tab'   => Controls_Manager::TAB_STYLE
				]
			);

			$element->add_control(
				'ma_el_enable_bg_slider',
				[
					'type'  => Controls_Manager::SWITCHER,
					'label' => __('Enable Background Slider', 'master-addons' ),
					'default' => '',
					'label_on' => __('Yes', 'master-addons' ),
					'label_off' => __('No', 'master-addons' ),
					'return_value' => 'yes',
					'render_type' => 'template',
				]
			);

			$element->add_control(
				'ma_el_bg_slider_apply_changes',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => '<div class="elementor-update-preview-button editor-ma-bg-slider-preview-update"><span>Update changes to Preview</span><button class="elementor-button elementor-button-success" onclick="elementor.reloadPreview();">Apply</button></div>',
					'separator' => 'after',
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
					],
				]
			);

			$element->add_control(
				'ma_el_bg_slider_images',
				[
					'label'     => __('Add Images', 'master-addons' ),
					'type'      => Controls_Manager::GALLERY,
					'default'   => [],
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
					],
				]
			);

			$element->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' => 'ma_el_thumbnail',
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
					],
				]
			);

			/*$slides_to_show = range( 1, 10 );
			$slides_to_show = array_combine( $slides_to_show, $slides_to_show );

			$element->add_control(
				'slides_to_show',
				[
					'label' => __( 'Slides to Show', 'master-addons' ),
					'type' => Controls_Manager::SELECT,
					'default' => '3',
					'options' => $slides_to_show,
				]
			);*/
			/*$element->add_control(
                'slide',
                [
                    'label' => __( 'Initial Slide', 'master-addons' ),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
					'placeholder' => __( 'Initial Slide', 'master-addons' ),
					'default' => __( '0', 'master-addons' ),
                ]
            );*/

			$element->add_control(
				'ma_el_slider_transition',
				[
					'label'   => __('Transition', 'master-addons' ),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'fade'        => __('Fade', 'master-addons' ),
						'fade2'       => __('Fade2', 'master-addons' ),
						'slideLeft'   => __('slide Left', 'master-addons' ),
						'slideLeft2'  => __('Slide Left 2', 'master-addons' ),
						'slideRight'  => __('Slide Right', 'master-addons' ),
						'slideRight2' => __('Slide Right 2', 'master-addons' ),
						'slideUp'     => __('Slide Up', 'master-addons' ),
						'slideUp2'    => __('Slide Up 2', 'master-addons' ),
						'slideDown'   => __('Slide Down', 'master-addons' ),
						'slideDown2'  => __('Slide Down 2', 'master-addons' ),
						'zoomIn'      => __('Zoom In', 'master-addons' ),
						'zoomIn2'     => __('Zoom In 2', 'master-addons' ),
						'zoomOut'     => __('Zoom Out', 'master-addons' ),
						'zoomOut2'    => __('Zoom Out 2', 'master-addons' ),
						'swirlLeft'   => __('Swirl Left', 'master-addons' ),
						'swirlLeft2'  => __('Swirl Left 2', 'master-addons' ),
						'swirlRight'  => __('Swirl Right', 'master-addons' ),
						'swirlRight2' => __('Swirl Right 2', 'master-addons' ),
						'burn'        => __('Burn', 'master-addons' ),
						'burn2'       => __('Burn 2', 'master-addons' ),
						'blur'        => __('Blur', 'master-addons' ),
						'blur2'       => __('Blur 2', 'master-addons' ),
						'flash'       => __('Flash', 'master-addons' ),
						'flash2'      => __('Flash 2', 'master-addons' ),
						'random'      => __('Random', 'master-addons' )
					],
					'default' => 'fade',
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
					],
				]
			);

			$element->add_control(
				'ma_el_slider_display_random',
				[
					'label'        => __('Random Display?', 'master-addons' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'label_on'     => __('Yes', 'master-addons' ),
					'label_off'    => __('No', 'master-addons' ),
					'return_value' => 'yes',
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
					],
				]
			);

			$element->add_control(
				'ma_el_slider_animation',
				[
					'label'   => __('Animation', 'master-addons' ),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'kenburns'          => __('Kenburns', 'master-addons' ),
						'kenburnsUp'        => __('Kenburns Up', 'master-addons' ),
						'kenburnsDown'      => __('Kenburns Down', 'master-addons' ),
						'kenburnsRight'     => __('Kenburns Right', 'master-addons' ),
						'kenburnsLeft'      => __('Kenburns Left', 'master-addons' ),
						'kenburnsUpLeft'    => __('Kenburns Up Left', 'master-addons' ),
						'kenburnsUpRight'   => __('Kenburns Up Right', 'master-addons' ),
						'kenburnsDownLeft'  => __('Kenburns Down Left', 'master-addons' ),
						'kenburnsDownRight' => __('Kenburns Down Right', 'master-addons' ),
						'random'            => __('Random', 'master-addons' ),
						''                  => __('None', 'master-addons' )
					],
					'default' => 'kenburns',
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
					],
				]
			);

			$element->add_control(
				'ma_el_custom_overlay_switcher',
				[
					'label'        => __('Custom Overlay', 'master-addons' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'label_on'     => __('Show', 'master-addons' ),
					'label_off'    => __('Hide', 'master-addons' ),
					'return_value' => 'yes',
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
					],
				]
			);

			/*$element->add_control(
				'custom_overlay',
				[
					'label' => __( 'Overlay Image', 'master-addons' ),
					'type' => Controls_Manager::MEDIA,
					'condition' => [
						'ma_el_custom_overlay_switcher' => 'yes',
					]
				]
			);*/

			$element->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'ma_el_slider_custom_overlay',
					'label'     => __('Overlay Image', 'master-addons' ),
					'types'     => ['none', 'classic', 'gradient'],
					'selector'  => '{{WRAPPER}} .vegas-overlay',
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
						'ma_el_custom_overlay_switcher' => 'yes',
					]
				]
			);

			$element->add_control(
				'ma_el_slider_overlay',
				[
					'label'     => __('Overlay', 'master-addons' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						''   => __('None', 'master-addons' ),
						'01' => __('Style 1', 'master-addons' ),
						'02' => __('Style 2', 'master-addons' ),
						'03' => __('Style 3', 'master-addons' ),
						'04' => __('Style 4', 'master-addons' ),
						'05' => __('Style 5', 'master-addons' ),
						'06' => __('Style 6', 'master-addons' ),
						'07' => __('Style 7', 'master-addons' ),
						'08' => __('Style 8', 'master-addons' ),
						'09' => __('Style 9', 'master-addons' )
					],
					'default'   => '01',
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
						'ma_el_custom_overlay_switcher' => '',
					]
				]
			);
			$element->add_control(
				'ma_el_slider_cover',
				[
					'label'   => __('Cover', 'master-addons' ),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'true'  => __('True', 'master-addons' ),
						'false' => __('False', 'master-addons' )
					],
					'default' => 'true',
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
					],
				]
			);
			$element->add_control(
				'ma_el_slider_delay',
				[
					'label'       => __('Delay', 'master-addons' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'placeholder' => __('Delay', 'master-addons' ),
					'default'     => __('5000', 'master-addons' ),
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
					],
				]
			);
			$element->add_control(
				'ma_el_slider_timer_bar',
				[
					'label'   => __('Timer', 'master-addons' ),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'true'  => __('True', 'master-addons' ),
						'false' => __('False', 'master-addons' )
					],
					'default' => 'true',
					'condition' => [
						'ma_el_enable_bg_slider' => 'yes',
					],
				]
			);

			$element->end_controls_section();
		}
	}


	function _before_render(\Elementor\Element_Base $element)
	{
		if ($element->get_name() != 'section' && $element->get_name() != 'column' && $element->get_name() != 'container') {
			return;
		}

		$settings = $element->get_settings();

		// Check if bg slider is enabled first
		if (empty($settings['ma_el_enable_bg_slider']) || $settings['ma_el_enable_bg_slider'] !== 'yes') {
			return;
		}

		if (empty($settings['ma_el_bg_slider_images'])) {
			return;
		}

		$element->add_render_attribute('_wrapper', 'class', 'has_ma_el_bg_slider');

		$slides = [];
		foreach ($settings['ma_el_bg_slider_images'] as $attachment) {
			$image_url = Group_Control_Image_Size::get_attachment_image_src(
				$attachment['id'],
				'ma_el_thumbnail',
				$settings
			);
			$slides[] = $image_url;
		}

		if (empty($slides)) {
			return;
		}

		// Make slider random
		if ($settings['ma_el_slider_display_random'] === 'yes') {
			shuffle($slides);
		}

		// Add data attributes for JavaScript to read
		$images_string = implode(',', $slides);
		$element->add_render_attribute('_wrapper', 'data-ma-el-bg-slider-images', $images_string);
		$element->add_render_attribute('_wrapper', 'data-ma-el-bg-slider-transition', $settings['ma_el_slider_transition']);
		$element->add_render_attribute('_wrapper', 'data-ma-el-bg-slider-animation', $settings['ma_el_slider_animation']);
		$element->add_render_attribute('_wrapper', 'data-ma-el-bg-custom-overlay', $settings['ma_el_custom_overlay_switcher']);
		$element->add_render_attribute('_wrapper', 'data-ma-el-bg-slider-overlay', $settings['ma_el_slider_overlay']);
		$element->add_render_attribute('_wrapper', 'data-ma-el-bg-slider-cover', $settings['ma_el_slider_cover']);
		$element->add_render_attribute('_wrapper', 'data-ma-el-bs-slider-delay', $settings['ma_el_slider_delay']);
		$element->add_render_attribute('_wrapper', 'data-ma-el-bs-slider-timer', $settings['ma_el_slider_timer_bar']);

		// Scripts are now enqueued early via wp_enqueue_scripts hook

	}

	function _print_template($template, $widget)
	{
		if ($widget->get_name() != 'section' && $widget->get_name() != 'column' && $widget->get_name() != 'container') {
			return $template;
		}

		$old_template = $template;
		ob_start();
	?>

		<# if(settings.ma_el_enable_bg_slider === 'yes' && !_.isUndefined(settings.ma_el_bg_slider_images) && settings.ma_el_bg_slider_images.length){
			var slides_path_string='', ma_el_transition=settings.ma_el_slider_transition, ma_el_animation=settings.ma_el_slider_animation, ma_el_custom_overlay=settings.ma_el_custom_overlay_switcher, ma_el_overlay='', ma_el_cover=settings.ma_el_slider_cover, ma_el_delay=settings.ma_el_slider_delay, ma_el_timer=settings.ma_el_slider_timer_bar;
			var slider_data=[];
			slides=settings.ma_el_bg_slider_images;
			for(var i in slides){
				slider_data[i]=slides[i].url;
			}
			slides_path_string=slider_data.join();
			if(settings.ma_el_custom_overlay_switcher=='yes' ){
				ma_el_overlay='00.png';
			}else{
				if(settings.ma_el_slider_overlay){
					ma_el_overlay=settings.ma_el_slider_overlay + '.png';
				}else{
					ma_el_overlay='00.png';
				}
			} #>

			<div class="ma-el-section-bs">
				<div class="ma-el-section-bs-inner" data-ma-el-bg-slider="{{ slides_path_string }}" data-ma-el-bg-slider-transition="{{ ma_el_transition }}" data-ma-el-bg-slider-animation="{{ ma_el_animation }}" data-ma-el-bg-custom-overlay="{{ ma_el_custom_overlay }}" data-ma-el-bg-slider-overlay="{{ ma_el_overlay }}" data-ma-el-bg-slider-cover="{{ ma_el_cover }}" data-ma-el-bs-slider-delay="{{ ma_el_delay }}" data-ma-el-bs-slider-timer="{{ ma_el_timer }}"></div>
			</div>

		<# } #>

	<?php
		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;

		return $template;
	}


	public static function get_instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
}

if (class_exists('MasterAddons\Modules\JLTMA_Extension_Background_Slider')) {
	JLTMA_Extension_Background_Slider::get_instance();
}
