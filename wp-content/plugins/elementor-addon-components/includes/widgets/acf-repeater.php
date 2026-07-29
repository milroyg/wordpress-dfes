<?php
/**
 * Class: Acf_Repeater
 * Name: ACF Repeater
 * Slug: eac-addon-acf-repeater
 *
 * Description: Affiche et formate les entrées sélectionnées dans le champ repeater ou Post object
 * d'un articles. Les articles sont affichées sous forme de grille.
 *
 * @since 1.8.2
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Eac_Load_Config;
use EACCustomWidgets\Includes\Acf\Eac_Acf_Lib;
use EACCustomWidgets\Includes\Acf\Eac_Acf_Options_Page;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Plugin;
use Elementor\Utils;

class Acf_Repeater extends Widget_Base {
	use \EACCustomWidgets\Includes\Traits\Slider_Trait;
	use \EACCustomWidgets\Includes\Traits\Button_Read_More_Trait;

	/**
	 * $main_id
	 *
	 * L'ID du template ou de l'article
	 */
	private $main_id;

	/**
	 * Constructeur de la class Acf_Relationship_Widget
	 *
	 * Enregistre les scripts, les styles et initialise l'ID en regard des templates
	 */
	public function __construct( array $data = array(), ?array $args = null ) {
		parent::__construct( $data, $args );

		/** Chargement de la Lib de gestion des balises ACF */
		if ( ! class_exists( Eac_Acf_Lib::class, false ) ) {
			new Eac_Acf_Lib();
		}

		$this->main_id = get_the_ID();
		if ( \Elementor\Plugin::$instance->documents->get_current() !== null ) {
			$this->main_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'acf-repeater';

	/**
	 * Retrieve widget name.
	 *
	 * @access public
	 *
	 * @return string widget name.
	 */
	public function get_name(): string {
		return Eac_Load_Config::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return string widget title.
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
		return array( 'eac-acf-repeater' );
	}

	/**
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return array CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'eac-acf-repeater' );
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls(): void {

		/**
		 * Generale content Section
		 */
		$this->start_controls_section(
			'acf_repeater_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'acf_repeater_key',
				array(
					'label'       => esc_html__( 'Repeater', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => Eac_Acf_Lib::get_acf_repeater_field( $this->main_id ),
					'label_block' => true,
				)
			);

		foreach ( Eac_Acf_Lib::get_acf_repeater_field() as $repeater_key => $repeater_label ) {
			$this->add_control(
				'acf_repeater_subkey_' . $repeater_key,
				array(
					'label'       => esc_html__( 'Fields', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => $this->get_all_sub_field( $repeater_key ),
					'condition'   => array(
						'acf_repeater_key'  => $repeater_key,
						'acf_repeater_key!' => '',
					),
				)
			);
		}

		$this->add_control(
			'acf_repeater_label',
			array(
				'label'        => esc_html__( 'Add label', 'eac-components' ),
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
				'toggle'    => false,
				'condition' => array(
					'acf_repeater_layout_type' => array( 'fitRows', 'list' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'acf_repeater_layout',
			array(
				'label' => esc_html__( 'Layout', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'acf_repeater_layout_type',
				array(
					'label'        => esc_html__( 'Mode', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'fitRows',
					'options'      => array(
						'fitRows' => esc_html__( 'Grid', 'eac-components' ),
						'list'    => esc_html__( 'List', 'eac-components' ),
						'faq'     => esc_html( 'FAQ' ),
						'table'   => esc_html__( 'Table', 'eac-components' ),
					),
					'prefix_class' => 'acf-repeater-',
					'render_type'  => 'template',
				)
			);

			$this->add_responsive_control(
				'acf_repeater_faq_width',
				array(
					'label'          => esc_html__( 'Width (%)', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( '%' ),
					'default'        => array(
						'unit' => '%',
						'size' => 60,
					),
					'tablet_default' => array(
						'unit' => '%',
					),
					'mobile_default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'          => array(
						'%' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'      => array(
						'{{WRAPPER}}.acf-repeater-faq .acf-repeater_container' => 'inline-size: {{SIZE}}%;',
					),
					'condition' => array(
						'acf_repeater_layout_type' => 'faq',
					),
				)
			);

			/**
			 * 'prefix_class' ne fonctionnera qu'avec les flexbox
			 */
			$this->add_responsive_control(
				'acf_repeater_layout_columns',
				array(
					'label'          => esc_html__( 'Columns count', 'eac-components' ),
					'type'           => Controls_Manager::SELECT,
					'default'        => '3',
					'tablet_default' => '2',
					'mobile_default' => '1',
					'options'        => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'prefix_class'   => 'responsive%s-',
					'condition'      => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			$this->add_control(
				'acf_repeater_layout_image',
				array(
					'label'     => esc_html__( 'Image layout', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'acf_repeater_layout_type' => array( 'fitRows', 'list', 'table' ),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'acf_repeater_image_dimension',
					'label'   => esc_html__( 'Size', 'eac-components' ),
					'default' => 'medium',
					'exclude' => array( 'custom' ),
					'condition' => array(
						'acf_repeater_layout_type' => array( 'fitRows', 'list', 'table' ),
					),
				)
			);

			$this->add_responsive_control(
				'acf_repeater_image_width',
				array(
					'label'          => esc_html__( 'Image width (%)', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( '%' ),
					'default'        => array(
						'unit' => '%',
						'size' => 50,
					),
					'tablet_default' => array(
						'unit' => '%',
					),
					'mobile_default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'          => array(
						'%' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'      => array(
						'{{WRAPPER}}.acf-repeater-list .acf-repeater_container .acf-repeater_img' => 'inline-size: {{SIZE}}%;',
					),
					'condition' => array(
						'acf_repeater_layout_type' => 'list',
					),
				)
			);

			$this->add_control(
				'acf_repeater_image_lazy',
				array(
					'label'     => esc_html( 'Lazy load' ),
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
					'default'   => 'yes',
					'toggle'    => false,
					'condition' => array(
						'acf_repeater_layout_type' => array( 'fitRows', 'list', 'table' ),
					),
				)
			);

			$this->add_control(
				'acf_repeater_image_style_ratio_enable',
				array(
					'label'        => esc_html__( 'Enable image ratio', 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'      => 'yes',
					'toggle'       => false,
					'render_type'  => 'template',
					'prefix_class' => 'acf-repeater__ratio-',
					'condition'    => array(
						'acf_repeater_layout_type' => array( 'fitRows', 'list' ),
					),
				)
			);

			$this->add_responsive_control(
				'acf_repeater_image_style_ratio',
				array(
					'label'          => esc_html( 'Ratio' ),
					'type'           => Controls_Manager::SELECT,
					'default'        => '1 / 1',
					'tablet_default' => '1 / 1',
					'mobile_default' => '9 / 16',
					'options'        => array(
						'1 / 1'  => esc_html__( 'Default', 'eac-components' ),
						'9 / 16' => esc_html( '9-16' ),
						'4 / 3'  => esc_html( '4-3' ),
						'3 / 2'  => esc_html( '3-2' ),
						'16 / 9' => esc_html( '16-9' ),
						'21 / 9' => esc_html( '21-9' ),
					),
					'selectors'      => array( '{{WRAPPER}} .acf-repeater_container .acf-repeater_img img' => 'aspect-ratio:{{SIZE}};' ),
					'condition'      => array(
						'acf_repeater_image_style_ratio_enable' => 'yes',
						'acf_repeater_layout_type'              => array( 'fitRows', 'list' ),
					),
				)
			);

			$this->add_responsive_control(
				'acf_repeater_image_ratio_position_y',
				array(
					'label'      => esc_html__( 'Vertical position', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'size' => 50,
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .acf-repeater_container .acf-repeater_img img' => 'object-position: 50% {{SIZE}}%;' ),
					'condition'  => array(
						'acf_repeater_image_style_ratio_enable' => 'yes',
						'acf_repeater_layout_type'              => array( 'fitRows', 'list' ),
					),
				)
			);

			$this->add_control(
				'acf_repeater_content_layout',
				array(
					'label'     => esc_html__( 'Content layout', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'   => array( 'acf_repeater_layout_type!' => 'table' ),
				)
			);

			$this->add_responsive_control(
				'acf_repeater_content_align_v',
				array(
					'label'       => esc_html__( 'Vertical alignment', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'flex-start'    => array(
							'title' => esc_html__( 'Top', 'eac-components' ),
							'icon'  => 'eicon-justify-start-v',
						),
						'center'        => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-justify-center-v',
						),
						'flex-end'      => array(
							'title' => esc_html__( 'Bottom', 'eac-components' ),
							'icon'  => 'eicon-justify-end-v',
						),
						'space-between' => array(
							'title' => esc_html__( 'Space between', 'eac-components' ),
							'icon'  => 'eicon-justify-space-between-v',
						),
						'space-around'  => array(
							'title' => esc_html__( 'Space around', 'eac-components' ),
							'icon'  => 'eicon-justify-space-around-v',
						),
						'space-evenly'  => array(
							'title' => esc_html__( 'Space evenly', 'eac-components' ),
							'icon'  => 'eicon-justify-space-evenly-v',
						),
					),
					'default'     => 'flex-start',
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .acf-repeater_content' => 'justify-content: {{VALUE}};' ),
					'condition'   => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';

			$this->add_responsive_control(
				'acf_repeater_content_align_h',
				array(
					'label'     => esc_html__( 'Horizontal alignment', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'start'  => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'end'    => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'center',
					'toggle'    => false,
					'selectors' => array(
						'{{WRAPPER}} .acf-repeater_content' => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
						'{{WRAPPER}} .buttons-wrapper' => 'justify-content: {{VALUE}};',
					),
					'condition' => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			$this->add_responsive_control(
				'acf_repeater_faq_align_h',
				array(
					'label'     => esc_html__( 'Horizontal alignment', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'start'  => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'end'    => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'start'  => '0 auto',
						'center' => 'auto',
						'end'    => 'auto 0',
					),
					'default'   => 'center',
					'toggle'    => false,
					'selectors' => array( '{{WRAPPER}}.acf-repeater-faq .acf-repeater_container' => 'margin-inline: {{VALUE}};' ),
					'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'acf_repeater_content',
			array(
				'label'     => esc_html__( 'Content', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'acf_repeater_icon',
			array(
				'label'       => esc_html__( 'Pictogram FAQ', 'eac-components' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => 'true',
				'default'     => array(
					'value'   => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				),
				'skin'        => 'inline',
				'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
			)
		);

		$this->add_control(
			'acf_repeater_label_question',
			array(
				'label'   => esc_html__( 'Question label', 'eac-components' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'h2' => array(
						'title' => 'H2',
						'icon'  => 'eicon-editor-h2',
					),
					'h3' => array(
						'title' => 'H3',
						'icon'  => 'eicon-editor-h3',
					),
					'h4' => array(
						'title' => 'H4',
						'icon'  => 'eicon-editor-h4',
					),
					'h5' => array(
						'title' => 'H5',
						'icon'  => 'eicon-editor-h5',
					),
					'h6' => array(
						'title' => 'H6',
						'icon'  => 'eicon-editor-h6',
					),
					'span'  => array(
						'title' => esc_html__( 'Paragraph', 'eac-components' ),
						'icon'  => 'eicon-editor-paragraph',
					),
				),
				'default'     => 'span',
				'toggle'      => false,
				'label_block' => true,
				'condition'   => array( 'acf_repeater_layout_type' => 'faq' ),
			)
		);

		$this->add_control(
			'acf_repeater_content_links_heading',
			array(
				'label'     => esc_html__( 'Link', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array( 'acf_repeater_layout_type!' => 'faq' ),
			)
		);

		$this->add_control(
			'eac_repeater_content_link_info',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'There is a URL or link in one of the repeater fields', 'eac-components' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array( 'acf_repeater_layout_type!' => 'faq' ),
			)
		);

		$this->add_control(
			'eac_repeater_link_nofollow',
			array(
				'label'      => esc_html__( "Add 'nofollow' to links", 'eac-components' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'yes' => array(
						'title' => esc_html__( 'Yes', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'No', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'    => 'no',
				'toggle'     => false,
				'condition'  => array( 'acf_repeater_layout_type!' => 'faq' ),
			)
		);

		$this->add_control(
			'acf_repeater_content_button',
			array(
				'label'        => esc_html__( 'Button', 'eac-components' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'eac-components' ),
				'label_off'    => esc_html__( 'No', 'eac-components' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
			)
		);

		$this->add_control(
			'acf_repeater_content_link',
			array(
				'label'        => esc_html__( 'Enable the link globally', 'eac-components' ),
				'description'  => esc_html__( 'The link wraps each item', 'eac-components' ),
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
				'toggle'    => false,
				'condition' => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
			)
		);

		$this->add_control(
			'acf_repeater_readmore_content',
			array(
				'label'     => esc_html__( 'Button', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'acf_repeater_content_button' => 'yes',
					'acf_repeater_layout_type'    => array( 'fitRows', 'list' ),
				),
			)
		);

			// Trait du contenu du bouton read more
			$this->register_button_more_content_controls(
				array(
					'control_condition' => array(
						'acf_repeater_content_button' => 'yes',
						'acf_repeater_layout_type'    => array( 'fitRows', 'list' ),
					),
				)
			);

		$this->end_controls_section();

		/** Generale Style Section */
		$this->start_controls_section(
			'acf_repeater_general_style',
			array(
				'label' => esc_html__( 'General', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			/** Conteneur */
			$this->add_control(
				'acf_repeater_container_style',
				array(
					'label'     => esc_html__( 'Container', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'acf_repeater_wrapper_style_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .swiper, {{WRAPPER}} .acf-repeater_container' => 'background-color: {{VALUE}};' ),
				)
			);

			/** Articles */
			$this->add_control(
				'acf_repeater_items_style',
				array(
					'label'     => esc_html__( 'Post', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'acf_repeater_layout_type!' => 'table' ),
				)
			);

			$this->add_control(
				'acf_repeater_wrapper_style',
				array(
					'label'        => esc_html__( 'Style', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'style-0',
					'options'      => array(
						'style-0'  => esc_html__( 'Default', 'eac-components' ),
						'style-1'  => 'Style 1',
						'style-2'  => 'Style 2',
						'style-3'  => 'Style 3',
						'style-4'  => 'Style 4',
						'style-5'  => 'Style 5',
						'style-8'  => 'Style 6',
						'style-10' => 'Style 7',
						'style-11' => 'Style 8',
						'style-12' => 'Style 9',
					),
					'prefix_class' => 'acf-repeater_wrapper-',
					'condition'    => array( 'acf_repeater_layout_type!' => 'table' ),
				)
			);

			$this->add_responsive_control(
				'acf_repeater_wrapper_style_gap',
				array(
					'label'      => esc_html__( 'Margin between items', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'size' => 20,
						'unit' => 'px',
					),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 150,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .acf-repeater_container:not(.swiper-wrapper)' => 'gap: {{SIZE}}px; padding: calc({{SIZE}}px / 2);',
						'(mobile) {{WRAPPER}} .acf-repeater_container:not(.swiper-wrapper)' => 'padding: 0 !important;',
					),
					'condition'  => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			$this->add_responsive_control(
				'acf_repeater_faq_style_gap',
				array(
					'label'      => esc_html__( 'Margin between items', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'size' => 10,
						'unit' => 'px',
					),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}}.acf-repeater-faq .acf-repeater_container' => 'row-gap: {{SIZE}}px;',
					),
					'condition'  => array( 'acf_repeater_layout_type' => 'faq' ),
				)
			);

			$this->add_control(
				'acf_repeater_items_bg_color',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .acf-repeater_container article, {{WRAPPER}} .acf-repeater_content' => 'background-color: {{VALUE}};' ),
					'condition' => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'acf_repeater_wrapper_border',
					'selector'  => '{{WRAPPER}} .acf-repeater_container article',
					'condition' => array(
						'acf_repeater_wrapper_style' => 'style-0',
						'acf_repeater_layout_type!' => 'table',
					),
				)
			);

			$this->add_control(
				'acf_repeater_wrapper_radius',
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
						'{{WRAPPER}} .acf-repeater_container article' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array(
						'acf_repeater_wrapper_style' => 'style-0',
						'acf_repeater_layout_type!' => 'table',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'acf_repeater_wrapper_shadow',
					'label'     => esc_html__( 'Shadow', 'eac-components' ),
					'selector'  => '{{WRAPPER}} .acf-repeater_container article',
					'condition' => array(
						'acf_repeater_wrapper_style' => 'style-0',
						'acf_repeater_layout_type!' => 'table',
					),
				)
			);

			/** Images */
			$this->add_control(
				'acf_repeater_images_style',
				array(
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'acf_repeater_image_border',
					'selector'  => '{{WRAPPER}} .acf-repeater_img img',
					'condition' => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			$this->add_control(
				'acf_repeater_image_border_radius',
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
						'{{WRAPPER}} .acf-repeater_img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition' => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			/** Contenu */
			$this->add_control(
				'acf_repeater_content_style',
				array(
					'label'     => esc_html__( 'Content', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			$this->add_control(
				'acf_repeater_content_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .acf-repeater_content, {{WRAPPER}} .acf-repeater_content a' => 'color: {{VALUE}};' ),
					'condition' => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'acf_repeater_content_typography',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'selector'  => '{{WRAPPER}} .acf-repeater_content',
					'condition' => array( 'acf_repeater_layout_type' => array( 'fitRows', 'list' ) ),
				)
			);

			/** Bouton */
			$this->add_control(
				'acf_repeater_readmore_settings',
				array(
					'label'     => esc_html__( 'Button', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array(
						'acf_repeater_content_button' => 'yes',
						'acf_repeater_layout_type'    => array( 'fitRows', 'list' ),
					),
					'separator' => 'before',
				)
			);

				// Trait du style du bouton read more
				$this->register_button_more_style_controls(
					array(
						'control_condition' => array(
							'acf_repeater_content_button' => 'yes',
							'acf_repeater_layout_type'    => array( 'fitRows', 'list' ),
						),
					)
				);

			/** Questions */
			$this->add_control(
				'acf_repeater_quest_style',
				array(
					'label'     => esc_html( 'Question' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
				)
			);

				$this->add_control(
					'acf_repeater_quest_color',
					array(
						'label'     => esc_html__( 'Color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array( '{{WRAPPER}} .acf-repeater_faq-title, {{WRAPPER}} .acf-repeater_faq-toggler' => 'color: {{VALUE}};' ),
						'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'      => 'acf_repeater_quest_typo',
						'label'     => esc_html__( 'Typography', 'eac-components' ),
						'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
						'selector'  => '{{WRAPPER}} .acf-repeater_faq-title, {{WRAPPER}} .acf-repeater_faq-toggler',
						'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
					)
				);

				$this->add_control(
					'acf_repeater_quest_bg',
					array(
						'label'     => esc_html__( 'Background color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
						'selectors' => array( '{{WRAPPER}} .acf-repeater_faq-question' => 'background-color: {{VALUE}};' ),
						'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'      => 'acf_repeater_quest_border',
						'selector'  => '{{WRAPPER}} .acf-repeater_faq-question',
						'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
					)
				);

			/** Réponses */
			$this->add_control(
				'acf_repeater_resp_style',
				array(
					'label'     => esc_html__( 'Answer', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
				)
			);

				$this->add_control(
					'acf_repeater_resp_color',
					array(
						'label'     => esc_html__( 'Color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array( '{{WRAPPER}} .acf-repeater_faq-response' => 'color: {{VALUE}};' ),
						'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'      => 'acf_repeater_resp_typo',
						'label'     => esc_html__( 'Typography', 'eac-components' ),
						'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
						'selector'  => '{{WRAPPER}} .acf-repeater_faq-response',
						'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
					)
				);

				$this->add_control(
					'acf_repeater_resp_bg',
					array(
						'label'     => esc_html__( 'Background color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
						'selectors' => array( '{{WRAPPER}} .acf-repeater_faq-response' => 'background-color: {{VALUE}};' ),
						'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'      => 'acf_repeater_resp_border',
						'selector'  => '{{WRAPPER}} .acf-repeater_faq-response',
						'condition' => array( 'acf_repeater_layout_type' => 'faq' ),
					)
				);

			/** Table */
			$this->add_control(
				'acf_repeater_table_style',
				array(
					'label'     => esc_html__( 'Table', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'acf_repeater_layout_type' => 'table' ),
				)
			);

				$this->add_control(
					'acf_repeater_head_color',
					array(
						'label'     => esc_html__( 'Header color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array( '{{WRAPPER}} .acf-repeater_container table thead' => 'color: {{VALUE}};' ),
						'condition' => array( 'acf_repeater_layout_type' => 'table' ),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'      => 'acf_repeater_head_typo',
						'label'     => esc_html__( 'Header typography', 'eac-components' ),
						'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
						'selector'  => '{{WRAPPER}} .acf-repeater_container table thead th',
						'condition' => array( 'acf_repeater_layout_type' => 'table' ),
					)
				);

				$this->add_control(
					'acf_repeater_head_bg',
					array(
						'label'     => esc_html__( 'Header background color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
						'selectors' => array( '{{WRAPPER}} .acf-repeater_container table thead' => 'background-color: {{VALUE}};' ),
						'condition' => array( 'acf_repeater_layout_type' => 'table' ),
					)
				);

				$this->add_control(
					'acf_repeater_body_color',
					array(
						'label'     => esc_html__( 'Body color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array( '{{WRAPPER}} .acf-repeater_container table tbody' => 'color: {{VALUE}};' ),
						'condition' => array( 'acf_repeater_layout_type' => 'table' ),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'      => 'acf_repeater_body_typo',
						'label'     => esc_html__( 'Body typography', 'eac-components' ),
						'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
						'selector'  => '{{WRAPPER}} .acf-repeater_container table tbody',
						'condition' => array( 'acf_repeater_layout_type' => 'table' ),
					)
				);

				$this->add_control(
					'acf_repeater_body_bg',
					array(
						'label'     => esc_html__( 'Body background color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
						'selectors' => array( '{{WRAPPER}} .acf-repeater_container table tbody' => 'background-color: {{VALUE}};' ),
						'condition' => array( 'acf_repeater_layout_type' => 'table' ),
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
		$settings      = $this->get_settings_for_display();
		$repeater_data = array();

		/**
		 * Le champ de sélection des groupes est vide
		 * Les droits (Location rules) ont changés mais Elementor a enregistré la sélection précédente
		 */
		if ( empty( $settings['acf_repeater_key'] ) ) {
			return;
		}
		?>
		<div class='eac-acf-repeater'>
			<?php
			$repeater_data = $this->get_repeater_data();
			if ( ! empty( $repeater_data ) ) {
				if ( 'faq' === $settings['acf_repeater_layout_type'] ) {
					$this->render_repeater_faq_data( $repeater_data );
				} elseif ( 'table' === $settings['acf_repeater_layout_type'] ) {
					$this->render_repeater_table_data( $repeater_data );
				} else {
					$this->render_repeater_grid_data( $repeater_data );
				}
			}
			?>
		</div>
		<?php
		/** LD+JSON FAQ en dehosr du wrapper */
		if ( ! empty( $repeater_data ) ) {
			if ( 'faq' === $settings['acf_repeater_layout_type'] ) {
				$this->render_faq_schema( $repeater_data );
			}
		}
	}

	/**
	 * get_repeater_data
	 *
	 * @return array
	 */
	private function get_repeater_data(): array {
		$settings       = $this->get_settings_for_display();
		$repeater_key   = $settings['acf_repeater_key'];
		$keys_sub_key   = 'acf_repeater_subkey_' . $settings['acf_repeater_key'];
		$keys           = isset( $settings[ $keys_sub_key ] ) ? $settings[ $keys_sub_key ] : array();
		$sub_key_values = array();
		$is_faq         = 'faq' === $settings['acf_repeater_layout_type'] ? true : false;
		$is_table       = 'table' === $settings['acf_repeater_layout_type'] ? true : false;
		$id             = $this->main_id;

		if ( empty( $keys ) ) {
			return $sub_key_values;
		}

		if ( class_exists( Eac_Acf_Options_Page::class, false ) ) {
			$id_page = Eac_Acf_Options_Page::get_options_page_id( $repeater_key );
			if ( ! empty( $id_page ) ) {
				$id = $id_page;
			}
		}

		$count_of_row  = is_countable( get_field( $repeater_key, $id ) ) ? count( get_field( $repeater_key, $id ) ) : 0;
		$count_of_keys = count( $keys );

		/** Deux sous champs pour la FAQ */
		if ( $is_faq && 2 !== $count_of_keys ) {
			return $sub_key_values;
		}

		while ( have_rows( $repeater_key, $id ) ) {
			$the_row = the_row();
			foreach ( $the_row as $field_key => $any_value ) {
				$field = get_sub_field_object( $field_key );

				foreach ( $keys as $key ) {
					if ( $field && $key === $field['key'] && in_array( $field['type'], $this->get_acf_supported_fields(), true ) ) {
						$field_value = $field['value'];
						$field_label = $field['label'];
						$field_name  = $field['name'];
						$field_name_ = $field['_name'] ?? $field_key;

						if ( 'image' === $field['type'] ) {
							switch ( $field['return_format'] ) {
								case 'array':
									$field_value = $field['value']['ID'];
									break;
								case 'url':
									$field_value = attachment_url_to_postid( $field_value );
									break;
							}
						} elseif ( 'select' === $field['type'] ) {
							$select_values = array();
							foreach ( $field_value as $value ) {
								if ( 'array' === $field['return_format'] ) {
									$select_values[] = $value['value'];
								} else {
									$select_values[] = $value;
								}
							}
							$field_value = implode( ', ', $select_values );
						} elseif ( in_array( $field['type'], array( 'url', 'page_link' ), true ) ) {
							$field_value = is_array( $field_value ) ? $field['value'][0] : $field_value;
						} elseif ( 'link' === $field['type'] ) {
							$field_value  = 'array' === $field['return_format'] ? $field['value']['url'] : $field_value;
							$field_label  = 'array' === $field['return_format'] ? $field['value']['title'] : $field_label;
						} elseif ( 'number' === $field['type'] || 'text' === $field['type'] ) {
							$field_value = sprintf( '%1$s %2$s %3$s', $field['prepend'], $field_value, $field['append'] );
						} elseif ( 'file' === $field['type'] ) {
							switch ( $field['return_format'] ) {
								case 'array':
									$field_value = $field_value['url'];
									break;
								case 'id':
									$field_value = wp_get_attachment_url( $field_value );
									break;
							}
						}

						if ( $is_faq ) {
							$sub_key_values['faqSchema'][ get_row_index() - 1 ][ $field_label ] = $field_value;
						}
						if ( $is_table ) {
							$sub_key_values['thead'][ $field_label ] = $field_label;
						}

						$sub_key_values[ get_row_index() - 1 ][] = array(
							'type'   => trim( $field['type'] ),
							'value'  => trim( $field_value ),
							'label'  => trim( $field_label ),
							'name'   => trim( $field_name ),
							'_name'  => trim( $field_name_ ),
							'format' => isset( $field['return_format'] ) ? $field['return_format'] : '',
						);
						break;
					}
				}
			}
		}
		return $sub_key_values;
	}

	/**
	 * render_repeater_grid_data
	 *
	 * @param array $datas
	 *
	 * @return void
	 */
	public function render_repeater_grid_data( array $datas ): void {
		$settings        = $this->get_settings_for_display();
		$has_global_link = 'yes' === $settings['acf_repeater_content_link'] ? true : false;
		$class           = 'acf-repeater_container';
		$wrapper_id      = 'acf-repeater_container-' . $this->get_id();

		$this->add_render_attribute( 'container_wrapper', 'class', esc_attr( $class ) );
		$this->add_render_attribute( 'container_wrapper', 'id', esc_attr( $wrapper_id ) );

		if ( isset( $datas['thead'] ) ) {
			unset( $datas['thead'] );
		}
		if ( isset( $datas['faqSchema'] ) ) {
			unset( $datas['faqSchema'] );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'container_wrapper' ); ?>>
			<?php
			ob_start( array( '\EACCustomWidgets\Core\Utils\Eac_Tools_Util', 'compress_html_full_output' ), 0, PHP_OUTPUT_HANDLER_REMOVABLE );
			foreach ( $datas as $first_index => $values ) {
				$has_repeater_content = false;
				?>
				<article class='acf-repeater_container-wrapper'>
					<?php foreach ( $values as $index => $data ) {
						if ( 'image' === $data['type'] && ! empty( $data['value'] ) ) {
							if ( $has_repeater_content ) {
								$has_repeater_content = false; ?>
								</div> <!-- .acf-repeater__content -->
							<?php }
							$image = $this->get_image_data( $data['value'], $settings['acf_repeater_image_dimension_size'] );
							if ( ! empty( $image ) ) { ?>
								<div class="acf-repeater_img <?php echo esc_attr( $data['_name'] ); ?>">
									<img <?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								</div>
							<?php }
						} elseif ( in_array( $data['type'], array( 'url', 'link', 'page_link' ), true ) && ! empty( $data['value'] ) ) {
							if ( ! $has_repeater_content ) {
								$has_repeater_content = true; ?>
								<div class='acf-repeater_content'>
							<?php }
							$label_url            = $data['label'];
							$value_url            = $data['value'];
							$value_url_decoded    = rawurldecode( $value_url );
							$has_button           = 'yes' === $settings['acf_repeater_content_button'] ? true : false;
							$has_noffolow         = 'yes' === $settings['eac_repeater_link_nofollow'] ? true : false;
							if ( $has_button ) { ?>
								<div class='buttons-wrapper'>
									<?php $this->render_button_more(
										array(
											'permalink'     => $value_url_decoded,
											'item_title'    => $label_url,
											'global_link'   => $has_global_link,
											'default_label' => esc_html__( 'Open link', 'eac-components' ),
											'nofollow'      => $has_noffolow,
										)
									); ?>
								</div>
							<?php } else {
								if ( $has_global_link ) {
									$this->add_render_attribute( 'post_url', 'class', 'card-link' );
								} else {
									$this->add_render_attribute( 'post_url', 'class', 'eac-accessible-link' );
								}
								$this->add_render_attribute( 'post_url', 'href', esc_url( $value_url ) );
								$this->add_render_attribute( 'post_url', 'aria-label', sprintf( '%1$s - %2$s', esc_html__( 'Open link', 'eac-components' ), esc_html( basename( $value_url_decoded ) ) ) );
								if ( $has_noffolow ) {
									$this->add_render_attribute( 'post_url', 'rel', 'nofollow' );
								}
								?>
								<div class="acf-repeater_url <?php echo esc_attr( sanitize_title( $label_url ) ); ?>">
									<a <?php $this->print_render_attribute_string( 'post_url' ); ?>><?php echo esc_html( $label_url ); ?></a>
								</div>
							<?php }
						} elseif ( 'email' === $data['type'] && ! empty( $data['value'] ) ) {
							if ( ! $has_repeater_content ) {
								$has_repeater_content = true; ?>
								<div class='acf-repeater_content'>
							<?php }
							$label_email = 'yes' === $settings['acf_repeater_label'] ? sprintf( '%1$s: %2$s', $data['label'], $data['value'] ) : $data['label'];
							$email       = $this->get_email_data( $data );
							if ( ! empty( $email ) ) { ?>
								<div class="acf-repeater_email <?php echo esc_attr( $data['_name'] ); ?>">
									<a <?php echo $email; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $label_email ); ?></a>
								</div>
							<?php }
						} elseif ( 'text' === $data['type'] && ! empty( $data['value'] ) ) {
							if ( ! $has_repeater_content ) {
								$has_repeater_content = true; ?>
								<div class='acf-repeater_content'>
							<?php }
							$value_text = 'yes' === $settings['acf_repeater_label'] ? sprintf( '%1$s: %2$s', $data['label'], $data['value'] ) : $data['value'];
							?>
							<div class="global__line-height acf-repeater_text <?php echo esc_attr( $data['_name'] ); ?>"><?php echo esc_html( $value_text ); ?></div>
							<?php
						} elseif ( 'textarea' === $data['type'] && ! empty( $data['value'] ) ) {
							if ( ! $has_repeater_content ) {
								$has_repeater_content = true; ?>
								<div class='acf-repeater_content'>
							<?php }
							$value_textarea = 'yes' === $settings['acf_repeater_label'] ? sprintf( '%1$s: %2$s', $data['label'], $data['value'] ) : $data['value'];
							?>
							<div class="global__line-height acf-repeater_text <?php echo esc_attr( $data['_name'] ); ?>"><?php echo nl2br( esc_html( $value_textarea ) ); ?></div>
							<?php
						} elseif ( 'select' === $data['type'] && ! empty( $data['value'] ) ) {
							if ( ! $has_repeater_content ) {
								$has_repeater_content = true; ?>
								<div class='acf-repeater_content'>
							<?php }
							$value_select = 'yes' === $settings['acf_repeater_label'] ? sprintf( '%1$s: %2$s', $data['label'], $data['value'] ) : $data['value'];
							?>
							<div class="acf-repeater_select <?php echo esc_attr( $data['_name'] ); ?>"><?php echo esc_html( $value_select ); ?></div>
							<?php
						} elseif ( 'number' === $data['type'] && ! empty( $data['value'] ) ) {
							if ( ! $has_repeater_content ) {
								$has_repeater_content = true; ?>
								<div class='acf-repeater_content'>
							<?php }
							$value_number = 'yes' === $settings['acf_repeater_label'] ? sprintf( '%1$s: %2$s', $data['label'], $data['value'] ) : $data['value'];
							?>
							<div class="acf-repeater_number <?php echo esc_attr( $data['_name'] ); ?>"><?php echo esc_html( $value_number ); ?></div>
							<?php
						} elseif ( 'file' === $data['type'] && ! empty( $data['value'] ) ) {
							$label_url   = $data['label'];
							$value_url   = $data['value'];
							$default_img = EAC_PLUGIN_URL . 'assets/images/icon-pdf.svg';
							$file_name   = pathinfo( $value_url )['filename'] . '.' . pathinfo( $value_url )['extension'];

							$this->add_render_attribute( 'post_file', 'class', 'eac-accessible-link' );
							$this->add_render_attribute( 'post_file', 'href', 'javascript:;' );
							$this->add_render_attribute( 'post_file', 'data-fancybox', '' );
							$this->add_render_attribute( 'post_file', 'aria-label', sprintf( '%1$s %2$s %3$s', esc_html__( 'Open file', 'eac-components' ), esc_html( $file_name ), esc_html__( 'in a modal box', 'eac-components' ) ) );
							$this->add_render_attribute( 'post_file', 'role', 'button' );
							$this->add_render_attribute( 'post_file', 'aria-haspopup', 'dialog' );
							$this->add_render_attribute( 'post_file', 'aria-expanded', 'false' );
							$this->add_render_attribute( 'post_file', 'data-options', wp_json_encode(
								array(
									'type'    => 'iframe',
									'caption' => esc_attr( $file_name ),
									'src'     => EAC_PLUGIN_URL . 'assets/js/pdfjs/viewer.html?file=' . esc_url( $value_url ) . '#page=1&pagemode=none&zoom=page-fit',
								)
							) );
							?>
							<div class="acf-repeater_file <?php echo esc_attr( $data['_name'] ); ?>">
								<a <?php $this->print_render_attribute_string( 'post_file' ); ?>>
									<img src="<?php echo esc_url( $default_img ); ?>" width='48' height='48' alt='' aria-hidden='true' />
								</a>
							</div>
							<?php
						} elseif ( 'date_picker' === $data['type'] && ! empty( $data['value'] ) ) {
							if ( ! $has_repeater_content ) {
								$has_repeater_content = true; ?>
								<div class='acf-repeater_content'>
							<?php }
							$value_date = 'yes' === $settings['acf_repeater_label'] ? sprintf( '%1$s: %2$s', $data['label'], $data['value'] ) : $data['value'];
							?>
							<div class="acf-repeater_date <?php echo esc_attr( $data['_name'] ); ?>"><?php echo esc_html( $value_date ); ?></div>
							<?php
						}

						$this->remove_render_attribute( 'post_url' );
						$this->remove_render_attribute( 'post_file' );
					}
					if ( $has_repeater_content ) { ?>
						</div> <!-- Fin div acf-repeater_content -->
					<?php } ?>					
				</article>
				<?php
			}
			ob_end_flush();
			?>
		</div> <!-- Fin div container acf-repeater_container -->
		<?php
	}

	/**
	 * render_repeater_faq_data
	 *
	 * @param array $datas
	 *
	 * @return void
	 */
	public function render_repeater_faq_data( array $datas ): void {
		$settings     = $this->get_settings_for_display();
		$question_tag = Utils::validate_html_tag( $settings['acf_repeater_label_question'] );
		$class        = 'acf-repeater_container';
		$wrapper_id   = 'acf-repeater_container-' . $this->get_id();

		$this->add_render_attribute( 'container_wrapper', 'class', esc_attr( $class ) );
		$this->add_render_attribute( 'container_wrapper', 'id', esc_attr( $wrapper_id ) );

		if ( isset( $datas['thead'] ) ) {
			unset( $datas['thead'] );
		}
		if ( isset( $datas['faqSchema'] ) ) {
			unset( $datas['faqSchema'] );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'container_wrapper' ); ?>>
			<?php
			ob_start( array( '\EACCustomWidgets\Core\Utils\Eac_Tools_Util', 'compress_html_full_output' ), 0, PHP_OUTPUT_HANDLER_REMOVABLE );
			foreach ( $datas as $index => $values ) {
				$question_id = 'acf-repeater_faq-question-' . $index;
				$response_id = 'acf-repeater_faq-response-' . $index;
				?>
				<article class='acf-repeater_container-wrapper'>				
					<?php foreach ( $values as $data ) {
						$this->add_render_attribute(
							'repeater_faq',
							array(
								'class'         => 'acf-repeater_faq-question',
								'id'            => $question_id,
								'role'          => 'button',
								'aria-expanded' => 'false',
								'aria-controls' => $response_id,
								'aria-label'    => sprintf( '%1$s - %2$s', esc_attr__( 'Show/Hide answer for', 'eac-components' ), esc_attr( $data['value'] ) ),
								'tabindex'      => '0',
							)
						);
						if ( 'text' === $data['type'] && ! empty( $data['value'] ) ) {
							$value_text = 'yes' === $settings['acf_repeater_label'] ? sprintf( '%1$s: %2$s', $data['label'], $data['value'] ) : $data['value'];
							?>
							<div <?php $this->print_render_attribute_string( 'repeater_faq' ); ?>>
								<?php printf( '<%1$s class="acf-repeater_faq-title">%2$s</%1$s>', esc_attr( $question_tag ), esc_html( $value_text ) ); ?>
								<span id='acf-repeater_faq-toggler' class='acf-repeater_faq-toggler eac-icon-svg'>
									<?php Icons_Manager::render_icon( $settings['acf_repeater_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								</span>
							</div>
							<?php
						} elseif ( 'textarea' === $data['type'] && ! empty( $data['value'] ) ) {
							$value_textarea = 'yes' === $settings['acf_repeater_label'] ? sprintf( '%1$s: %2$s', $data['label'], $data['value'] ) : $data['value'];
							?>
							<div class='acf-repeater_faq-response' id="<?php echo esc_attr( $response_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $question_id ); ?>">
								<?php echo nl2br( esc_html( $value_textarea ) ); ?>
							</div>
							<?php
						}
					} ?>
				</article>
				<?php
				$this->remove_render_attribute( 'repeater_faq' );
			}
			ob_end_flush();
			?>
		</div> <!-- Fin div container acf-repeater_container -->
		<?php
	}

	/**
	 * render_faq_schema
	 *
	 * @param array $datas
	 *
	 * @return void
	 */
	public function render_faq_schema( array $datas ): void {
		$faq_schema = array();
		$schemas    = $datas['faqSchema'];

		if ( ! empty( $schemas ) ) {
			foreach ( $schemas as $index => $schema ) {
				$faq_schema[] = array(
					'@type' => 'Question',
					'name' => (string) reset( $schema ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text' => (string) array_values( $schema )[1],
					),
				);
			}
			// Convertir le tableau en JSON-LD
			$json_ld = wp_json_encode( array(
				'@context' => 'https://schema.org',
				'@type' => 'FAQPage',
				'mainEntity' => $faq_schema,
			), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

			// Afficher le JSON-LD dans le <head>
			echo '<script type="application/ld+json">' . $json_ld . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * render_repeater_table_data
	 *
	 * @param array $datas
	 *
	 * @return void
	 */
	public function render_repeater_table_data( array $datas ): void {
		$settings   = $this->get_settings_for_display();
		$theads     = array();
		$class      = 'acf-repeater_container';
		$wrapper_id = 'acf-repeater_container-' . $this->get_id();

		$this->add_render_attribute( 'container_wrapper', 'class', esc_attr( $class ) );
		$this->add_render_attribute( 'container_wrapper', 'id', esc_attr( $wrapper_id ) );

		if ( isset( $datas['faqSchema'] ) ) {
			unset( $datas['faqSchema'] );
		}

		ob_start( array( '\EACCustomWidgets\Core\Utils\Eac_Tools_Util', 'compress_html_full_output' ), 0, PHP_OUTPUT_HANDLER_REMOVABLE );
		?>
		<div <?php $this->print_render_attribute_string( 'container_wrapper' ); ?>>
			<table class='widefat' cellspacing='0'>
				<?php if ( isset( $datas['thead'] ) ) {
					$theads = $datas['thead'];
					unset( $datas['thead'] ); ?>
					<thead><tr>
						<?php foreach ( $theads as $thead ) { ?>
							<th><?php echo esc_html( $thead ); ?></th>
						<?php } ?>
					</tr></thead><tbody>
				<?php }
				foreach ( $datas as $index => $values ) { ?>
					<tr>
						<?php foreach ( $values as $data ) {
							if ( 'image' === $data['type'] && ! empty( $data['value'] ) ) {
								$image = $this->get_image_data( $data['value'], $settings['acf_repeater_image_dimension_size'] );
								if ( ! empty( $image ) ) { ?>
									<td>
										<img <?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									</td>
								<?php }
							} elseif ( 'email' === $data['type'] && ! empty( $data['value'] ) ) {
								$label_email = $data['label'];
								$email = $this->get_email_data( $data );
								if ( ! empty( $email ) ) { ?>
									<td>
										<a <?php echo $email; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $label_email ); ?></a>
									</td>
								<?php }
							} elseif ( in_array( $data['type'], array( 'url', 'link', 'page_link' ), true ) && ! empty( $data['value'] ) ) {
								$label_url         = $data['label'];
								$value_url         = $data['value'];
								$value_url_decoded = rawurldecode( $value_url );
								$has_noffolow      = 'yes' === $settings['eac_repeater_link_nofollow'] ? true : false;

								$this->add_render_attribute( 'post_url', 'class', 'eac-accessible-link' );
								$this->add_render_attribute( 'post_url', 'href', esc_url( $value_url ) );
								$this->add_render_attribute( 'post_url', 'aria-label', sprintf( '%1$s - %2$s', esc_html__( 'Open link', 'eac-components' ), esc_html( basename( $value_url_decoded ) ) ) );
								if ( $has_noffolow ) {
									$this->add_render_attribute( 'post_url', 'rel', 'nofollow' );
								}
								?>
								<td>
									<a <?php $this->print_render_attribute_string( 'post_url' ); ?>><?php echo esc_html( $label_url ); ?></a>
								</td>
								<?php
							} elseif ( 'file' === $data['type'] && ! empty( $data['value'] ) ) {
								$label_url   = $data['label'];
								$value_url   = $data['value'];
								$default_img = EAC_PLUGIN_URL . 'assets/images/icon-pdf.svg';
								$file_name   = sprintf( '%1$s.%2$s', pathinfo( $value_url )['filename'], pathinfo( $value_url )['extension'] );

								$this->add_render_attribute( 'post_file', 'class', 'eac-accessible-link' );
								$this->add_render_attribute( 'post_file', 'href', 'javascript:;' );
								$this->add_render_attribute( 'post_file', 'data-fancybox', '' );
								$this->add_render_attribute( 'post_file', 'aria-label', sprintf( '%1$s %2$s %3$s', esc_html__( 'Open file', 'eac-components' ), esc_html( $file_name ), esc_html__( 'in a modal box', 'eac-components' ) ) );
								$this->add_render_attribute( 'post_file', 'aria-haspopup', 'dialog' );
								$this->add_render_attribute( 'post_file', 'aria-expanded', 'false' );
								$this->add_render_attribute( 'post_file', 'data-options', wp_json_encode(
									array(
										'type'    => 'iframe',
										'caption' => esc_attr( $file_name ),
										'src'     => EAC_PLUGIN_URL . 'assets/js/pdfjs/viewer.html?file=' . esc_url( $value_url ) . '#page=1&pagemode=none&zoom=page-fit',
									)
								) );
								?>
								<td>
									<span class='acf-repeater_file'>
										<a <?php $this->print_render_attribute_string( 'post_file' ); ?>>
											<img src="<?php echo esc_url( $default_img ); ?>" width='48' height='48' alt='' aria-hidden='true' />
										</a>
									</span>
								</td>
								<?php
							} else { ?>
								<td>
									<?php echo esc_html( $data['value'] ); ?>
								</td>
							<?php }
						} ?>
					</tr>
					<?php
					$this->remove_render_attribute( 'post_url' );
					$this->remove_render_attribute( 'post_file' );
				} ?>
				</tbody>
			</table>
		</div>
		<?php ob_end_flush();
	}

	/**
	 * get_all_sub_field
	 *
	 * @param string $repeater
	 *
	 * @return array
	 */
	private function get_all_sub_field( string $repeater_key ): array {
		$options       = array();
		$id            = $this->main_id;
		$repeater_obj  = get_field_object( $repeater_key );
		$repeater_name = '';

		if ( $repeater_obj ) {
			$repeater_name = $repeater_obj['name'];
		}
		if ( empty( $repeater_name ) ) {
			return $options;
		}

		if ( class_exists( Eac_Acf_Options_Page::class, false ) ) {
			$id_page = Eac_Acf_Options_Page::get_options_page_id( $repeater_key );
			if ( ! empty( $id_page ) ) {
				$id = $id_page;
			}
		}
		$rows = get_field( $repeater_key, $id );

		if ( $rows ) {
			$index = 0;
			foreach ( $rows as $row ) {
				foreach ( $row as $sub_field_name => $sub_field_value ) {
					// _first_repeater_0_photo = _repeater-name_0_subfield-name 0 = index de chaque ligne du repeater de la table postmeta
					$sub_key = sprintf( '_%1$s_%2$s_%3$s', $repeater_name, $index, $sub_field_name );
					$sub_field_key = get_field( $sub_key, $id );

					if ( $sub_field_key && ! empty( $sub_field_key ) ) {
						$sub_field_object = get_field_object( $sub_field_key );
						if ( $sub_field_object ) {
							$options[ $sub_field_key ] = esc_html( $sub_field_object['label'] );
						}
					}
				}
				++$index;
			}
		}
		return $options;
	}

	/**
	 * get_image_data
	 *
	 * @param mixed $id
	 * @param mixed $size
	 *
	 * @return string
	 */
	private function get_image_data( $id, $size ): string {
		$settings  = $this->get_settings_for_display();
		$lazy_load = 'yes' === $settings['acf_repeater_image_lazy'] ? 'lazy' : 'eager';
		$attach = '';
		$attachment = Eac_Tools_Util::wp_get_attachment_data( $id, $size );
		if ( ! empty( $attachment ) ) {
			$this->add_render_attribute(
				'post_image',
				array(
					'class'  => 'eac-accessible-img',
					'src'    => esc_url( $attachment['src'] ),
					'srcset' => esc_attr( $attachment['srcset'] ),
					'sizes'  => esc_attr( $attachment['srcsize'] ),
					'width'  => esc_attr( $attachment['width'] ),
					'height' => esc_attr( $attachment['height'] ),
					'alt'    => '',
				)
			);
			if ( 'eager' === $lazy_load ) {
				$this->add_render_attribute( 'post_image', 'loading', esc_attr( $lazy_load ) );
			}
			$attach = $this->get_render_attribute_string( 'post_image' );
			$this->remove_render_attribute( 'post_image' );
		}
		return $attach;
	}

	/**
	 * get_email_data
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	private function get_email_data( array $data ): string {
		$the_email   = '';
		$label_email = $data['label'];
		$email       = sanitize_email( $data['value'] );
		$email_obf   = \str_contains( $email, '@' ) ? sprintf( '%1$s#actus.%2$s', explode( '@', $email )[0], explode( '@', $email )[1] ) : '';

		if ( ! empty( $email_obf ) ) {
			$this->add_render_attribute( 'post_email', 'class', 'eac-accessible-link obfuscated-link' );
			$this->add_render_attribute( 'post_email', 'href', '#' );
			$this->add_render_attribute( 'post_email', 'data-link', esc_attr( $email_obf ) );
			$this->add_render_attribute( 'post_email', 'rel', 'nofollow' );
			$this->add_render_attribute( 'post_email', 'aria-label', esc_attr( $label_email ) );
			$the_email = $this->get_render_attribute_string( 'post_email' );
			$this->remove_render_attribute( 'post_email' );
		}
		return $the_email;
	}

	/**
	 * get_acf_supported_fields
	 * La liste des champs ACF supportés
	 *
	 * @return array
	 */
	protected function get_acf_supported_fields(): array {
		return array(
			'repeater',
			'image',
			'text',
			'textarea',
			'email',
			'url',
			'link',
			'page_link',
			'select',
			'number',
			'date_picker',
			'file',
		);
	}

	/**
	 * get_settings_json
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 *
	 * @return string
	 */
	protected function get_settings_json(): string {
		$settings = $this->get_settings_for_display();
		return '';
	}

	protected function content_template(): void {}
}
