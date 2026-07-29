<?php
/**
 * Class: PDF_Viewer_Widget
 * Name: Visionneuse PDF
 * Slug: eac-addon-pdf-viewer
 *
 * Description: Affiche un fichier PDF avec des otions dans une iFrame ou dans la Fancybox
 *
 * @since 1.8.9
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class PDF_Viewer_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'pdf-viewer';

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
		return array( 'eac-pdf-viewer' );
	}

	/**
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return array CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'eac-pdf-viewer' );
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
		$start = is_rtl() ? 'right' : 'left';
		$end   = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'fv_settings_section',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'fv_settings_type',
				array(
					'label'   => esc_html__( 'Origine', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'file',
					'options' => array(
						'file' => esc_html__( 'Media file', 'eac-components' ),
						'url'  => esc_html( 'URL' ),
					),
				)
			);

			$this->add_control(
				'fv_settings_display_type',
				array(
					'label'   => esc_html__( 'Display type', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'embed',
					'options' => array(
						'embed'    => esc_html__( 'Embedded', 'eac-components' ),
						'fancybox' => esc_html__( 'Modal box', 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'fv_settings_media_file',
				array(
					'label'        => esc_html__( 'Select file', 'eac-components' ),
					'type'         => 'FILE_VIEWER',
					'library_type' => array( 'application/pdf' ), // propiété utilisée par le script 'eac-file-viewer-control.js'
					'description'  => esc_html__( 'Select the file from the media library', 'eac-components' ),
					'condition'    => array( 'fv_settings_type' => 'file' ),
				)
			);

			$this->add_control(
				'fv_settings_media_url',
				array(
					'label'         => esc_html__( 'Select URL', 'eac-components' ),
					'type'          => Controls_Manager::URL,
					'placeholder'   => 'https://your-site-url/example.pdf',
					'dynamic'       => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'default'       => array(
						'url'         => 'http://www.pdf995.com/samples/widgets.pdf',
					),
					'condition'     => array( 'fv_settings_type' => 'url' ),
				)
			);

			$this->add_control(
				'fv_settings_align_file',
				array(
					'label'     => esc_html__( 'Alignment', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-text-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-text-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'left'  => 'start',
						'right' => 'end',
					),
					'default'   => 'center',
					'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper' => 'text-align: {{VALUE}};' ),
					'condition' => array( 'fv_settings_display_type' => 'embed' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'fv_settings_trigger',
			array(
				'label'     => esc_html__( 'Trigger options', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'fv_settings_display_type' => 'fancybox' ),
			)
		);

			$this->add_control(
				'fv_origin_trigger',
				array(
					'label'       => esc_html__( 'Trigger', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'Select a trigger', 'eac-components' ),
					'options'     => array(
						'button' => esc_html__( 'Button', 'eac-components' ),
						'text'   => esc_html__( 'Text', 'eac-components' ),
					),
					'default'     => 'button',
				)
			);

			$this->add_control(
				'fv_display_text_button',
				array(
					'label'       => esc_html__( 'Button text', 'eac-components' ),
					'default'     => esc_html__( 'Open file', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'label_block' => true,
					'condition'   => array( 'fv_origin_trigger' => 'button' ),
				)
			);

			$this->add_control(
				'fv_display_text',
				array(
					'label'       => esc_html__( 'Text', 'eac-components' ),
					'default'     => esc_html__( 'Open file', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'label_block' => true,
					'condition'   => array( 'fv_origin_trigger' => 'text' ),
				)
			);

			$this->add_control(
				'fv_align_trigger',
				array(
					'label'     => esc_html__( 'Alignment', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-text-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-text-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'left'  => 'start',
						'right' => 'end',
					),
					'default'   => 'center',
					'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper' => 'text-align: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'fv_icon_activated',
				array(
					'label'        => esc_html__( 'Add pictogram', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'fv_origin_trigger' => 'button' ),
				)
			);

			$this->add_control(
				'fv_display_icon_button',
				array(
					'label'                  => esc_html__( 'Pictograms', 'eac-components' ),
					'type'                   => Controls_Manager::ICONS,
					'default'                => array(
						'value'   => 'far fa-file-pdf',
						'library' => 'fa-regular',
					),
					'skin'                   => 'inline',
					'condition'              => array(
						'fv_origin_trigger' => 'button',
						'fv_icon_activated' => 'yes',
					),
				)
			);

			$this->add_control(
				'fv_position_icon_button',
				array(
					'label'     => esc_html__( 'Position', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'before',
					'options'   => array(
						'before' => esc_html__( 'Before', 'eac-components' ),
						'after'  => esc_html__( 'After', 'eac-components' ),
					),
					'condition' => array(
						'fv_origin_trigger' => 'button',
						'fv_icon_activated' => 'yes',
					),
				)
			);

			$this->add_control(
				'fv_marge_icon_button',
				array(
					'label'              => esc_html__( 'Margin', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => array( 'left', 'right' ),
					'default'            => array(
						'left'     => 0,
						'right'    => 0,
						'unit'     => 'px',
						'isLinked' => false,
					),
					'range'              => array(
						'px' => array(
							'min'  => 0,
							'max'  => 20,
							'step' => 1,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .button-icon' => 'margin-block: {{TOP}}px {{BOTTOM}}px; margin-inline: {{LEFT}}px {{RIGHT}}px;',
					),
					'condition'          => array(
						'fv_origin_trigger' => 'button',
						'fv_icon_activated' => 'yes',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'fv_settings_content',
			array(
				'label' => esc_html__( 'Viewer options', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'fv_settings_content_toolbar_left',
				array(
					'label'        => esc_html__( 'Display left toolbar', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'fv_settings_content_toolbar_right',
				array(
					'label'        => esc_html__( 'Display right toolbar', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'fv_settings_content_download',
				array(
					'label'        => esc_html__( "Display 'Download' button", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'fv_settings_content_print',
				array(
					'label'        => esc_html__( "Display 'Print' button", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'fv_settings_content_zoom',
				array(
					'label'   => esc_html__( 'Zoom level', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'auto',
					'options' => array(
						'100'        => '100%',
						'75'         => '75%',
						'50'         => '50%',
						'auto'       => esc_html__( 'Automatic', 'eac-components' ),
						'page-fit'   => esc_html__( 'Full page', 'eac-components' ),
						'page-width' => esc_html__( 'Full width', 'eac-components' ),
					),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'fv_modal_box_style',
			array(
				'label'     => esc_html__( 'Modal box', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'fv_settings_display_type' => 'fancybox' ),
			)
		);

			$this->add_responsive_control(
				'fv_modal_box_width',
				array(
					'label'          => esc_html__( 'Width', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( '%', 'vw' ),
					'default'        => array(
						'unit' => '%',
						'size' => 75,
					),
					'tablet_default' => array(
						'unit' => '%',
						'size' => 75,
					),
					'mobile_default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'          => array(
						'%' => array(
							'min'  => 20,
							'max'  => 100,
							'step' => 5,
						),
						'vw'  => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'label_block'    => true,
					'selectors'      => array(
						'.modalbox-visible-{{ID}} .fancybox-content' => 'inline-size: {{SIZE}}{{UNIT}} !important; block-size: 100{{UNIT}} !important',
						'.fancybox-slide.modalbox-visible-{{ID}}' => 'padding-block: 0; padding-inline: 6px;',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'fv_embed_style',
			array(
				'label'     => esc_html__( 'Embedded file', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'fv_settings_display_type' => 'embed' ),
			)
		);

			$this->add_responsive_control(
				'fv_embed_width',
				array(
					'label'       => esc_html__( 'Width', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px', '%', 'vw' ),
					'default'     => array(
						'unit' => 'px',
						'size' => 800,
					),
					'range'       => array(
						'px' => array(
							'min'  => 200,
							'max'  => 1140,
							'step' => 10,
						),
						'%' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
						'vw' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .fv-viewer__wrapper-iframe' => 'inline-size: {{SIZE}}{{UNIT}};' ),
				)
			);

			$this->add_responsive_control(
				'fv_embed_height',
				array(
					'label'       => esc_html__( 'Height', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px', '%', 'vh' ),
					'default'     => array(
						'unit' => 'px',
						'size' => 800,
					),
					'range'       => array(
						'px' => array(
							'min'  => 200,
							'max'  => 2000,
							'step' => 10,
						),
						'%' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
						'vh' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .fv-viewer__wrapper-iframe' => 'block-size: {{SIZE}}{{UNIT}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'fv_embed_border',
					'selector'  => '{{WRAPPER}} .fv-viewer__wrapper-iframe',
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'fv_embed_shadow',
					'label'    => esc_html__( 'Shadow', 'eac-components' ),
					'selector' => '{{WRAPPER}} .fv-viewer__wrapper-iframe',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'fv_button_style',
			array(
				'label'     => esc_html__( 'Trigger button', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'fv_settings_display_type' => 'fancybox',
					'fv_origin_trigger'        => 'button',
				),
			)
		);

		$this->start_controls_tabs( 'fv_controls_tabs' );

			$this->start_controls_tab(
				'fv_controls_tab_normal',
				array(
					'label' => esc_html__( 'Normal', 'eac-components' ),
				)
			);

				$this->add_control(
					'fv_button_color',
					array(
						'label'     => esc_html__( 'Color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array(
							'{{WRAPPER}} .fv-viewer__wrapper-btn' => 'color: {{VALUE}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'fv_button_typography',
						'label'    => esc_html__( 'Typography', 'eac-components' ),
						'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
						'selector' => '{{WRAPPER}} .fv-viewer__wrapper-btn',
					)
				);

				$this->add_control(
					'fv_button_background',
					array(
						'label'     => esc_html__( 'Background color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper-btn' => 'background-color: {{VALUE}};' ),
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'fv_controls_tab_hover',
				array(
					'label' => esc_html__( 'Hover', 'eac-components' ),
				)
			);

				$this->add_control(
					'fv_button_color_hover',
					array(
						'label'     => esc_html__( 'Color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array(
							'{{WRAPPER}} .fv-viewer__wrapper-btn:hover, {{WRAPPER}} .fv-viewer__wrapper-btn:focus' => 'color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'fv_button_background_hover',
					array(
						'label'     => esc_html__( 'Background color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array(
							'{{WRAPPER}} .fv-viewer__wrapper-btn:hover, {{WRAPPER}} .fv-viewer__wrapper-btn:focus' => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'fv_button_border_color_hover',
					array(
						'label'     => esc_html__( 'Border color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'condition' => array( 'fv_button_border_border!' => 'none' ),
						'selectors' => array(
							'{{WRAPPER}} .fv-viewer__wrapper-btn:hover, {{WRAPPER}} .fv-viewer__wrapper-btn:focus' => 'border-color: {{VALUE}};',
						),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'fv_button_border',
					'selector'  => '{{WRAPPER}} .fv-viewer__wrapper-btn',
				)
			);

			$this->add_control(
				'fv_button_radius',
				array(
					'label'              => esc_html__( 'Border radius', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 8,
						'right'    => 8,
						'bottom'   => 8,
						'left'     => 8,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .fv-viewer__wrapper-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'fv_button_padding',
				array(
					'label'     => esc_html__( 'Padding', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .button-icon' => 'padding-block: {{TOP}}px {{BOTTOM}}px; padding-inline: {{LEFT}}px {{RIGHT}}px;',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'fv_button_shadow',
					'label'    => esc_html__( 'Shadow', 'eac-components' ),
					'selector' => '{{WRAPPER}} .fv-viewer__wrapper-btn',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'fv_text_style',
			array(
				'label'     => esc_html__( 'Trigger text', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'fv_settings_display_type' => 'fancybox',
					'fv_origin_trigger'        => 'text',
				),
			)
		);

			$this->add_control(
				'fv_text_style_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper-text' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'fv_text_style_typography',
					'label'    => esc_html__( 'Typography', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .fv-viewer__wrapper-text',
				)
			);

			$this->add_control(
				'fv_text_style_bg',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper-text' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'fv_text_style_marges',
				array(
					'label'     => esc_html__( 'Margin', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper-text' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{LEFT}}{{UNIT}} {{RIGHT}}{{UNIT}};' ),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		if ( empty( $settings['fv_settings_media_file'] ) && empty( $settings['fv_settings_media_url']['url'] ) ) {
			return;
		}
		?>
		<div class="eac-pdf-viewer">
			<input type="hidden" id="pdf_nonce" name="pdf_nonce" value="<?php echo wp_create_nonce( 'eac_file_viewer_nonce_' . esc_attr( $this->get_id() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" />
			<?php $this->render_viewer(); ?>
		</div>
		<?php
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_viewer(): void {
		$settings  = $this->get_settings_for_display();
		$trigger   = $settings['fv_origin_trigger'];
		$origine   = $settings['fv_settings_type'];
		$link_file = ! empty( $settings['fv_settings_media_file'] ) ? $settings['fv_settings_media_file'] : '';
		$link_url  = ! empty( $settings['fv_settings_media_url']['url'] ) ? $settings['fv_settings_media_url']['url'] : '';

		$link         = 'file' === $origine ? $link_file : $link_url;
		$display_type = $settings['fv_settings_display_type'];
		$icon_button  = false;

		// Unique ID du widget
		$id = $this->get_id();

		/** Le déclencheur est un bouton */
		if ( 'button' === $trigger ) {
			if ( 'yes' === $settings['fv_icon_activated'] && ! empty( $settings['fv_display_icon_button'] ) ) {
				$icon_button = true;
			}
			$this->add_render_attribute( 'trigger', 'type', 'button' );
			$this->add_render_attribute( 'trigger', 'class', 'fv-viewer__wrapper-trigger fv-viewer__wrapper-btn' );
			$this->add_render_attribute( 'trigger', 'tabindex', '-1' );
		} elseif ( 'text' === $trigger ) {
			$this->add_render_attribute( 'trigger', 'class', 'fv-viewer__wrapper-trigger fv-viewer__wrapper-text' );
		}

		// Le wrapper global du composant
		$this->add_render_attribute( 'fv_wrapper', 'class', 'fv-viewer__wrapper' );
		$this->add_render_attribute( 'fv_wrapper', 'id', esc_attr( $id ) );
		$this->add_render_attribute( 'fv_wrapper', 'data-settings', $this->get_settings_json( esc_url( $link ) ) );

		// Le lien de la fancybox
		$this->add_render_attribute( 'a_fancybox', 'id', 'fancybox-' . esc_attr( $id ) );
		$this->add_render_attribute( 'a_fancybox', 'class', 'eac-accessible-link' );
		$this->add_render_attribute( 'a_fancybox', 'data-fancybox', '' );
		$this->add_render_attribute( 'a_fancybox', 'data-type', 'iframe' );
		$this->add_render_attribute( 'a_fancybox', 'data-src', '' );
		$this->add_render_attribute( 'a_fancybox', 'data-options', wp_json_encode( array( 'slideClass' => 'modalbox-visible-' . esc_attr( $id ) ) ) );
		$this->add_render_attribute( 'a_fancybox', 'href', '#' );
		$this->add_render_attribute( 'a_fancybox', 'aria-label', esc_attr__( 'Open PDF file in modal box', 'eac-components' ) );
		$this->add_render_attribute( 'a_fancybox', 'aria-expanded', 'false' );
		$this->add_render_attribute( 'a_fancybox', 'aria-haspopup', 'dialog' );
		$this->add_render_attribute( 'a_fancybox', 'role', 'button' );

		// Il y a un lien fichier ou url
		if ( '' !== $link ) {
			?>
			<div <?php $this->print_render_attribute_string( 'fv_wrapper' ); ?>>
				<?php if ( 'fancybox' === $display_type ) : ?>
					<a <?php $this->print_render_attribute_string( 'a_fancybox' ); ?>>
						<?php if ( 'button' === $trigger ) : ?>
							<button <?php $this->print_render_attribute_string( 'trigger' ); ?>>
							<?php
							if ( $icon_button && 'before' === $settings['fv_position_icon_button'] ) { ?>
								<span class='button-icon eac-icon-svg'>
								<?php Icons_Manager::render_icon( $settings['fv_display_icon_button'], array( 'aria-hidden' => 'true' ) ); ?>
								</span>
							<?php }
								echo '<span class="label-icon">' . esc_html( $settings['fv_display_text_button'] ) . '</span>';
							if ( $icon_button && 'after' === $settings['fv_position_icon_button'] ) { ?>
								<span class='button-icon eac-icon-svg'>
								<?php Icons_Manager::render_icon( $settings['fv_display_icon_button'], array( 'aria-hidden' => 'true' ) ); ?>
								</span>
							<?php } ?>
							</button>
						<?php elseif ( 'text' === $trigger ) : ?>
							<div <?php $this->print_render_attribute_string( 'trigger' ); ?>>
								<?php echo esc_html( $settings['fv_display_text'] ); ?>
							</div>
						<?php endif; ?>
					</a>
				<?php else : ?>
					<div id='fv-viewer_loader-wheel' class='eac__loader-spin'></div>
					<iframe 
						id='iframe-<?php echo esc_attr( $id ); ?>' 
						name='iframe-<?php echo esc_attr( $id ); ?>' 
						class='fv-viewer__wrapper-iframe' 
						src='' 
						loading='lazy' 
						type='application/pdf' 
						aria-label="<?php esc_html_e( 'Embedded PDF file', 'eac-components' ); ?>">
					</iframe>
				<?php endif; ?>
			</div>
			<?php
		}
	}

	/**
	 * get_settings_json
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 * Modification de la règles 'data_filtre'
	 *
	 * @uses         wp_json_encode()
	 *
	 * @return   JSON oject
	 *
	 * @access   protected
	 */
	protected function get_settings_json( $url ): string {
		$module_settings = $this->get_settings_for_display();

		$settings = array(
			'data_id'        => esc_attr( $this->get_id() ),
			'data_mobile'    => wp_is_mobile(),
			'data_url'       => esc_url( $url ),
			'data_display'   => $module_settings['fv_settings_display_type'],
			'data_toolleft'  => 'yes' === $module_settings['fv_settings_content_toolbar_left'] ? true : false,
			'data_toolright' => 'yes' === $module_settings['fv_settings_content_toolbar_right'] ? true : false,
			'data_download'  => 'yes' === $module_settings['fv_settings_content_download'] ? true : false,
			'data_print'     => 'yes' === $module_settings['fv_settings_content_print'] ? true : false,
			'data_zoom'      => $module_settings['fv_settings_content_zoom'],
		);

		return wp_json_encode( $settings );
	}

	protected function content_template(): void {}
}
