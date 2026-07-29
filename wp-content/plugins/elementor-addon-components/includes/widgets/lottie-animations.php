<?php
/**
 * Class: Lottie_Animations_Widget
 * Name: Lottie animation
 * Slug: eac-addon-lottie-animations
 *
 * Description: Implémente les animations Lottie
 *
 * @since 1.9.3
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Lottie_Animations_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'lottie-animations';

	/**
	 * Retrieve widget name
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name(): string {
		return Eac_Load_Config::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title(): string {
		return Eac_Load_Config::get_widget_title( $this->slug );
	}

	/**
	 * Retrieve widget icon.
	 *
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon(): string {
		return Eac_Load_Config::get_widget_icon( $this->slug );
	}

	/**
	 * Affecte le composant à la catégorie définie dans plugin.php
	 *
	 * @access public
	 *
	 * @return array widget category.
	 */
	public function get_categories(): array {
		return Eac_Load_Config::get_widget_categories( $this->slug );
	}

	/**
	 * Load dependent libraries
	 *
	 * @access public
	 *
	 * @return array libraries list.
	 */
	public function get_script_depends(): array {
		return array( 'eac-lottie-anim' );
	}

	/**
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return array CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'eac-lottie-anim' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords(): array {
		return Eac_Load_Config::get_widget_keywords( $this->slug );
	}

	/**
	 * Get help widget get_custom_help_url.
	 *
	 * @access public
	 *
	 * @return string URL help center
	 */
	public function get_custom_help_url(): string {
		return Eac_Load_Config::get_widget_help_url( $this->slug );
	}

	/**
	 * has_widget_inner_wrapper
	 *
	 * @return bool
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * is_dynamic_content
	 *
	 * @return bool
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls(): void {

		$this->start_controls_section(
			'lottie_settings_section',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'lottie_is_json_enable',
				array(
					'label'        => 'JSON URL',
					'type'         => Controls_Manager::HIDDEN,
					'default'      => \EACCustomWidgets\Core\Utils\Eac_Tools_Util::is_json_enable() ? 'true' : 'false',
					'save_default' => false,
				)
			);

			$this->add_control(
				'lottie_settings_source',
				array(
					'label'   => esc_html__( 'Origine', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'file' => array(
							'title' => esc_html__( 'Media file', 'eac-components' ),
							'icon'  => 'eicon-document-file',
						),
						'url'  => array(
							'title' => esc_html( 'URL' ),
							'icon'  => 'eicon-link',
						),
					),
					'default' => 'file',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'lottie_settings_media_file',
				array(
					'label'        => esc_html__( 'Select file', 'eac-components' ),
					'type'         => 'FILE_VIEWER',
					'library_type' => array( 'application/json' ), // propriété utilisée par le script 'eac-file-viewer-control.js'
					'description'  => esc_html__( 'Select the file from the media library', 'eac-components' ),
					'condition'    => array( 'lottie_settings_source' => 'file' ),
				)
			);

			$this->add_control(
				'lottie_settings_media_url',
				array(
					'label'         => esc_html( 'URL' ),
					'type'          => Controls_Manager::URL,
					'description'   => esc_html__( "Animation URL <a href='https://lottiefiles.com/' target='_blank' rel='nofollow noopener noreferrer'>here</a>", 'eac-components' ),
					'placeholder'   => 'https://lottiefiles.com/anim.json/',
					'dynamic'       => array(
						'active' => true,
					),
					'condition'     => array(
						'lottie_settings_source' => 'url',
						'lottie_is_json_enable'  => 'true',
					),
				)
			);

			$this->add_control(
				'lottie_settings_media_url_info',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'raw'             => esc_html__( "Elementor/Advanced settings, activate option 'Enable Unfiltered File Uploads'", 'eac-components' ),
					'condition'     => array(
						'lottie_settings_source' => 'url',
						'lottie_is_json_enable'  => 'false',
					),
				)
			);

			$this->add_control(
				'lottie_settings_render',
				array(
					'label'        => esc_html__( 'Render type', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'description'  => esc_html__( "Performance issues ? Try the 'Canvas' method", 'eac-components' ),
					'default'      => 'svg',
					'options'      => array(
						'canvas' => esc_html( 'Canvas' ),
						'svg'    => esc_html( 'SVG' ),
					),
					'render_type'  => 'template',
					'prefix_class' => 'lottie-anim_render-',
					'separator'    => 'before',
				)
			);

			$this->add_responsive_control(
				'lottie_settings_animation_size',
				array(
					'label'       => esc_html__( 'Size', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px', '%' ),
					'default'     => array(
						'unit' => 'px',
						'size' => 200,
					),
					'range'       => array(
						'px' => array(
							'min'  => 50,
							'max'  => 1000,
							'step' => 50,
						),
						'%' => array(
							'min'  => 5,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'   => array(
						'{{WRAPPER}}.lottie-anim_render-canvas .lottie-anim_wrapper, {{WRAPPER}}.lottie-anim_render-svg .lottie-anim_wrapper' => 'inline-size: {{SIZE}}{{UNIT}}; block-size: auto;',
					),
				)
			);

			$this->add_control(
				'lottie_settings_animation_rotate',
				array(
					'label'     => esc_html__( 'Rotation', 'eac-components' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 0,
						'unit' => 'px',
					),
					'range'     => array(
						'px' => array(
							'min'  => -180,
							'max'  => 180,
							'step' => 10,
						),
					),
					'selectors' => array( '{{WRAPPER}} .lottie-anim_wrapper' => 'transform: rotate({{SIZE}}deg);' ),
				)
			);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_control(
				'lottie_settings_animation_align',
				array(
					'label'     => esc_html__( 'Alignment', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'flex-start'  => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'space-around' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'flex-end'    => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'space-around',
					'selectors_dictionary' => array(
						'flex-start'   => 'start',
						'space-around' => 'center',
						'flex-end'     => 'end',
					),
					'selectors' => array( '{{WRAPPER}} .eac-lottie-animations' => 'justify-content: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'lottie_settings_link_display',
				array(
					'label'     => esc_html__( 'Add a link to the animation', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'no',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'lottie_settings_link',
				array(
					'label'         => esc_html( 'URL' ),
					'type'          => Controls_Manager::URL,
					'placeholder'   => esc_html__( 'Type or paste your URL', 'eac-components' ),
					'dynamic'       => array(
						'active' => true,
					),
					'autocomplete'  => true,
					'condition'     => array( 'lottie_settings_link_display' => 'yes' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'lottie_settings_animation',
			array(
				'label' => esc_html__( 'Animation', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'lottie_settings_loop',
				array(
					'label'       => esc_html__( 'Loop', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'yes' => array(
							'title' => esc_html__( 'Loop', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'Only once', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'     => 'yes',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'lottie_settings_reverse',
				array(
					'label'       => esc_html__( 'Reverse direction', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'     => 'no',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'lottie_settings_speed',
				array(
					'label'       => esc_html__( 'Speed', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 1,
					'min'         => 0.1,
					'max'         => 3,
					'step'        => 0.1,
				)
			);

			$this->add_control(
				'lottie_settings_trigger',
				array(
					'label'       => esc_html__( 'Trigger', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'Animation trigger', 'eac-components' ),
					'default'     => 'none',
					'options'     => array(
						'none'     => esc_html__( 'None', 'eac-components' ),
						'hover'    => esc_html__( 'Hover', 'eac-components' ),
						'viewport' => esc_html__( 'Viewport', 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'lottie_settings_trigger_info',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'raw'             => esc_html__( 'When the animation is in the visible part of the window', 'eac-components' ),
					'condition'       => array( 'lottie_settings_trigger' => 'viewport' ),
				)
			);

			/**
			$this->add_control('lottie_settings_viewport',
				[
					'label'     => esc_html__('Viewport', 'eac-components'),
					'type'      => Controls_Manager::SLIDER,
					'default'   => ['sizes' => ['start' => 0, 'end' => 200], 'unit'  => 'px'],
					'labels'    => [
						esc_html__('Bottom', 'eac-components'),
						esc_html__('Top', 'eac-components'),
					],
					'scales'    => 1,
					'handles'   => 'range',
					'condition' => ['lottie_settings_trigger' => 'viewport'],
				]
			);*/

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'lottie_style_animation',
			array(
				'label' => esc_html__( 'General', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'lottie_style_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .lottie-anim_wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'lottie_style_opacity',
				array(
					'label'     => esc_html__( 'Opacity', 'eac-components' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array( 'size' => 1 ),
					'range'     => array(
						'px' => array(
							'max'  => 1,
							'min'  => 0.1,
							'step' => 0.1,
						),
					),
					'selectors' => array( '{{WRAPPER}} .lottie-anim_wrapper' => 'opacity: {{SIZE}};' ),
				)
			);

			$this->add_responsive_control(
				'lottie_style_padding',
				array(
					'label'              => esc_html__( 'Padding', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 0,
						'right'    => 0,
						'bottom'   => 0,
						'left'     => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'separator' => 'before',
					'selectors'          => array(
						'{{WRAPPER}} .lottie-anim_wrapper' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'lottie_style_border',
					'selector'  => '{{WRAPPER}} .lottie-anim_wrapper',
				)
			);

			$this->add_control(
				'lottie_style_border_radius',
				array(
					'label'              => esc_html__( 'Border radius', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 0,
						'right'    => 0,
						'bottom'   => 0,
						'left'     => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .lottie-anim_wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'lottie_style_shadow',
					'label'    => esc_html__( 'Shadow', 'eac-components' ),
					'selector' => '{{WRAPPER}} .lottie-anim_wrapper',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 * https://assets7.lottiefiles.com/packages/lf20_bsatc9vq.json
	 *
	 * @access protected
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		if ( empty( $settings['lottie_settings_media_file'] ) && empty( $settings['lottie_settings_media_url']['url'] ) ) {
			return;
		}
		?>
		<div class='eac-lottie-animations'>
			<?php $this->render_lottie(); ?>
		</div>
		<?php
	}

	protected function render_lottie(): void {
		$settings = $this->get_settings_for_display();
		$has_link = false;
		$url = '';

		if ( 'file' === $settings['lottie_settings_source'] ) {
			$url = $settings['lottie_settings_media_file'];
		} elseif ( 'true' === $settings['lottie_is_json_enable'] && 'url' === $settings['lottie_settings_source'] && ! empty( $settings['lottie_settings_media_url']['url'] ) ) {
			$url = $settings['lottie_settings_media_url']['url'];
		} else {
			return;
		}

		$this->add_render_attribute(
			'lottie_anime',
			array(
				'class'         => 'lottie-anim_wrapper',
				'role'          => 'img',
				'data-src'      => esc_url( $url ),
				'data-autoplay' => 'none' === $settings['lottie_settings_trigger'] ? 'true' : 'false', // Pas d'autoplay pour 'hover' et 'viewport'
				'data-loop'     => 'yes' === $settings['lottie_settings_loop'] ? 'true' : 'false',
				'data-speed'    => ! empty( $settings['lottie_settings_speed'] ) ? $settings['lottie_settings_speed'] : '1',
				'data-reverse'  => 'yes' === $settings['lottie_settings_reverse'] ? '-1' : '1',
				'data-renderer' => $settings['lottie_settings_render'],
				'data-trigger'  => $settings['lottie_settings_trigger'],
				'data-name'     => 'lottie_' . esc_attr( $this->get_id() ),
				'data-elem-id'  => esc_attr( $this->get_id() ),
				/**
				'data-start'    => isset($settings['lottie_settings_viewport']['sizes']['start']) ? $settings['lottie_settings_viewport']['sizes']['start'] : '0',
				'data-end'      => isset($settings['lottie_settings_viewport']['sizes']['end']) ? 100 - $settings['lottie_settings_viewport']['sizes']['end'] : '100',
				*/
			)
		);

		if ( 'yes' === $settings['lottie_settings_link_display'] && ! empty( $settings['lottie_settings_link']['url'] ) ) {
			$has_link = true;
			$this->add_link_attributes( 'lottie_url', $settings['lottie_settings_link'] );
			if ( $settings['lottie_settings_link']['is_external'] ) {
				$this->add_render_attribute( 'lottie_url', 'rel', 'noopener noreferrer' );
			}
		}
		?>
		<div <?php $this->print_render_attribute_string( 'lottie_anime' ); ?>>
			<?php if ( $has_link ) : ?>
				<a <?php $this->print_render_attribute_string( 'lottie_url' ); ?>>
					<span class='lottie-anim_wrapper-url'></span>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function content_template(): void {}
}
