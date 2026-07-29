<?php
/**
 * Class: Author_Infobox_Widget
 * Name: Boîte auteur
 * Slug: eac-addon-author-infobox
 *
 * Description: Affiche les informations de l'auteur de l'article courant avec sa photo
 * sa bio et ses réseaux sociaux.
 * 4 habillages différents peuvent être appliqués ansi qu'une multitude de paramétrages.
 * Le contenu peut être ajouter automatiquement dans le type d'article sélectionné.
 *
 * @since 1.9.1
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\TemplateLibrary\Source_Local;

class Author_Infobox_Widget extends Widget_Base {
	use \EACCustomWidgets\Includes\Traits\Button_Read_More_Trait;

	/**
	 * Le libellé de l'option pour enregistrer les données d'intégration du modèle
	 *
	 * @access private
	 *
	 * @return string widget name.
	 */
	private $option_infobox = 'eac_options_infobox';

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'author-infobox';

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
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 *
	 * @access public
	 *
	 * @return array CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'eac-author-infobox' );
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
			'aib_general_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			/**
			 * Champ caché pour déterminer si c'est un modèle Elementor
			 * Utilisé pour afficher/cacher certaines sections
			 */
			$this->add_control(
				'aib_is_a_template',
				array(
					'label'   => 'Template hidden',
					'type'    => Controls_Manager::HIDDEN,
					'default' => get_post_type( get_the_ID() ) === Source_Local::CPT,
				)
			);

			$this->add_control(
				'aib_settings_name_tag',
				array(
					'label'   => esc_html__( 'Name tag', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'h2',
					'options' => array(
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
						'p'   => 'p',
					),
				)
			);

			$this->add_control(
				'aib_settings_title_tag',
				array(
					'label'   => esc_html__( 'Role tag', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'h3',
					'options' => array(
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
						'p'   => 'p',
					),
				)
			);

			$this->add_control(
				'aib_settings_skin_style',
				array(
					'label'        => esc_html__( 'Skin', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'skin-1',
					'options'      => array(
						'skin-1' => 'Skin 1',
						'skin-2' => 'Skin 2',
						'skin-3' => 'Skin 3',
						'skin-4' => 'Skin 4',
					),
					'prefix_class' => 'author-infobox_global-',
				)
			);

			$this->add_responsive_control(
				'aib_settings_box_width',
				array(
					'label'          => esc_html__( 'Container width', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( 'px', '%' ),
					'default'        => array(
						'unit' => '%',
						'size' => 100,
					),
					'tablet_default' => array(
						'unit' => '%',
					),
					'mobile_default' => array(
						'unit' => '%',
					),
					'range'          => array(
						'px' => array(
							'min'  => 200,
							'max'  => 1500,
							'step' => 50,
						),
						'%'  => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .author-infobox_content' => 'inline-size: {{SIZE}}{{UNIT}};' ),
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'aib_settings_box_alignment',
				array(
					'label'                => esc_html__( 'Alignment', 'eac-components' ),
					'type'                 => Controls_Manager::CHOOSE,
					'options'              => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'              => 'center',
					'toggle'               => false,
					'selectors_dictionary' => array(
						'left'   => '0 auto',
						'center' => 'auto',
						'right'  => 'auto 0',
					),
					'selectors'            => array( '{{WRAPPER}} .author-infobox_content' => 'margin-inline: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_content_settings',
			array(
				'label' => esc_html__( 'Content', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'aib_content_prefix_name',
				array(
					'label'        => esc_html__( "Add 'About' to name", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'aib_content_role',
				array(
					'label'        => esc_html__( 'Display role', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'aib_content_bio',
				array(
					'label'        => esc_html__( 'Display biography', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'aib_content_social',
				array(
					'label'        => esc_html__( 'Display social medias', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'aib_content_readmore',
				array(
					'label'        => esc_html__( 'Button Consult archives', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'aib_readmore_alert',
				array(
					'type'       => Controls_Manager::ALERT,
					'alert_type' => 'warning',
					'heading'    => esc_html__( 'Security Alert', 'eac-components' ),
					'content'    => esc_html__( 'It is not recommended to expose the name (nickname) of your users, as this can facilitate targeting by malicious individuals, increasing the risk of intrusion attempts.', 'eac-components' ),
					'condition' => array( 'aib_content_readmore' => 'yes' ),
				)
			);

			$this->add_control(
				'aib_content_box_alignment',
				array(
					'label'                => esc_html__( 'Alignment', 'eac-components' ),
					'type'                 => Controls_Manager::CHOOSE,
					'options'              => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'              => 'center',
					'toggle'               => false,
					'selectors_dictionary' => array(
						'left'   => 'start',
						'right'  => 'end',
					),
					'selectors'            => array(
						'{{WRAPPER}} .author-infobox_info-content' => 'align-items: {{VALUE}};',
						'{{WRAPPER}} .author-infobox_name-content, {{WRAPPER}} .author-infobox_biography p' => 'text-align: {{VALUE}};',
						'{{WRAPPER}} .buttons-wrapper' => 'justify-content: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_settings_social_profile',
			array(
				'label'     => esc_html__( 'Social medias', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'aib_content_social' => 'yes' ),
			)
		);

			$this->add_control(
				'aib_settings_social_network',
				array(
					'label'       => esc_html__( 'Social medias', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( "Dynamic tags 'Author/Author social medias'", 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
				)
			);

			$this->add_control(
				'aib_settings_social_info',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'raw'             => sprintf( "<a href='https://elementor-addon-components.com/elementor-dynamic-social-medias/' target='_blank' rel='noopener noreferrer'>%s</a> %s", esc_html__( 'Follow this link', 'eac-components' ), esc_html__( 'to add social medias to user profiles.', 'eac-components' ) ),
				)
			);

			$this->add_responsive_control(
				'aib_settings_social_width',
				array(
					'label'      => esc_html__( 'Container width', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'      => array(
						'%' => array(
							'min'  => 20,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .author-infobox_social' => 'inline-size: {{SIZE}}%;',
					),
				)
			);

			$this->add_control(
				'aib_settings_social_space_h',
				array(
					'label'       => esc_html__( 'Horizontal spacing', 'eac-components' ),
					'description' => esc_html__( 'Horizontal spacing between icons', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'space-between' => array(
							'title' => esc_html__( 'Space between', 'eac-components' ),
							'icon'  => 'eicon-justify-space-between-h',
						),
						'space-around'  => array(
							'title' => esc_html__( 'Space around', 'eac-components' ),
							'icon'  => 'eicon-justify-space-around-h',
						),
						'space-evenly'  => array(
							'title' => esc_html__( 'Space evenly', 'eac-components' ),
							'icon'  => 'eicon-justify-space-evenly-h',
						),
					),
					'default'     => 'space-around',
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'justify-content: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_settings_avatar_content',
			array(
				'label' => esc_html( 'Avatar' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'aib_image_style_shape',
				array(
					'label'        => esc_html__( 'Round image', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'round',
					'default'      => 'round',
					'prefix_class' => 'author-infobox_image-',
				)
			);

			$this->add_responsive_control(
				'aib_image_style_width',
				array(
					'label'      => esc_html__( 'Image width', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'unit' => 'px',
						'size' => 120,
					),
					'range'      => array(
						'px' => array(
							'min'  => 50,
							'max'  => 250,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}}.author-infobox_global-skin-1 .author-infobox_content .author-infobox_image img' => 'inline-size:{{SIZE}}{{UNIT}}; block-size:{{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.author-infobox_global-skin-2 .author-infobox_content .author-infobox_image img' => 'inline-size:{{SIZE}}{{UNIT}}; block-size:{{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.author-infobox_global-skin-3 .author-infobox_content .author-infobox_image img' => 'inline-size:{{SIZE}}{{UNIT}}; block-size:{{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.author-infobox_global-skin-4 .author-infobox_content .author-infobox_image img' => 'inline-size:{{SIZE}}{{UNIT}}; block-size:{{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_relation_more_settings',
			array(
				'label'     => esc_html__( 'Button', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'aib_content_readmore' => 'yes' ),
			)
		);

			$this->add_control(
				'aib_relation_more_label',
				array(
					'label'       => esc_html__( 'Label', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => esc_html__( 'View archives', 'eac-components' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_settings_integrate',
			array(
				'label'     => esc_html__( 'Embedding', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'aib_is_a_template' => true ),
			)
		);

			$this->add_control(
				'aib_settings_integrate_display',
				array(
					'label'        => esc_html__( 'Enable embedding', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'aib_settings_integrate_posttype',
				array(
					'label'       => esc_html__( 'Post type', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'label_block' => true,
					'default'     => 'post',
					'options'     => Eac_Tools_Util::get_filter_post_types(),
					'condition'   => array( 'aib_settings_integrate_display' => 'yes' ),
				)
			);

		foreach ( Eac_Tools_Util::get_filter_post_types() as $post_type_name => $post_type_label ) {
			$this->add_control(
				'aib_settings_integrate_postid_' . $post_type_name,
				array(
					'label'       => esc_html__( 'Key', 'eac-components' ),
					'description' => esc_html__( 'Leave the field blank to embed the template content in all documents for the selected post type', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => Eac_Tools_Util::get_all_post_by_id( array( $post_type_name ) ),
					'condition'   => array(
						'aib_settings_integrate_posttype' => $post_type_name,
						'aib_settings_integrate_display'  => 'yes',
					),
				)
			);
		}

			$this->add_control(
				'aib_settings_integrate_position',
				array(
					'label'     => esc_html__( 'Position', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'before' => esc_html__( 'Before content', 'eac-components' ),
						'after'  => esc_html__( 'After content', 'eac-components' ),
					),
					'default'   => 'after',
					'condition' => array( 'aib_settings_integrate_display' => 'yes' ),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'aib_section_global_style',
			array(
				'label' => esc_html__( 'General', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'aib_global_style',
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
						'style-10' => 'Style 5',
						'style-11' => 'Style 6',
						'style-12' => 'Style 7',
					),
					'prefix_class' => 'author-infobox_wrapper-',
				)
			);

			$this->add_control(
				'aib_global_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .author-infobox_content' => 'background-color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_image_section_style',
			array(
				'label' => esc_html( 'Avatar' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'aib_image_style__border',
					'fields_options' => array(
						'border' => array( 'default' => 'solid' ),
						'width'  => array(
							'default' => array(
								'top'      => 5,
								'right'    => 5,
								'bottom'   => 5,
								'left'     => 5,
								'isLinked' => true,
							),
						),
						'color'      => array( 'default' => '#FFC72F' ),
					),
					'selector'       => '{{WRAPPER}} .author-infobox_content .author-infobox_image img',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'aib_image_style_shadow',
					'label'    => esc_html__( 'Shadow', 'eac-components' ),
					'selector' => '{{WRAPPER}} .author-infobox_content .author-infobox_image img',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_name_section_style',
			array(
				'label' => esc_html__( 'Name', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'aib_name_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .author-infobox_name .author-infobox_name-content' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'aib_name_typography',
					'label'          => esc_html__( 'Typography', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.8,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .author-infobox_name .author-infobox_name-content',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_job_section_style',
			array(
				'label'     => esc_html__( 'Role', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'aib_content_role' => 'yes' ),
			)
		);

			$this->add_control(
				'aib_job_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .author-infobox_role' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'aib_job_typography',
					'label'          => esc_html__( 'Typography', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.2,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .author-infobox_role .author-infobox_role-content',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_biography_section_style',
			array(
				'label'     => esc_html__( 'Biography', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'aib_content_bio' => 'yes' ),
			)
		);

			$this->add_control(
				'aib_biography_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .author-infobox_biography p' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'aib_biography_typography',
					'label'    => esc_html__( 'Typography', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
					'selector' => '{{WRAPPER}} .author-infobox_biography p',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_icon_section_style',
			array(
				'label'     => esc_html__( 'Social medias', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'aib_content_social' => 'yes' ),
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'aib_icon_typography',
					'label'          => esc_html__( 'Typography', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.5,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .dynamic-tags_social-container .dynamic-tags_social-icon',
				)
			);

			$this->add_control(
				'aib_icon_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'aib_style_social_padding',
				array(
					'label'     => esc_html__( 'Padding', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 5,
						'right'    => 5,
						'bottom'   => 5,
						'left'     => 5,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors' => array(
						'{{WRAPPER}} .dynamic-tags_social-container' => 'padding-block: {{TOP}}px {{BOTTOM}}px; padding-inline: {{LEFT}}px {{RIGHT}}px;',
					),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'aib_style_social_border',
					'selector' => '{{WRAPPER}} .dynamic-tags_social-container',
				)
			);

			$this->add_control(
				'aib_style_social_radius',
				array(
					'label'      => esc_html__( 'Border radius', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .dynamic-tags_social-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_readmore_section_style',
			array(
				'label'     => esc_html__( 'Button', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'aib_content_readmore' => 'yes' ),
			)
		);

			// Trait Style du bouton read more
			$this->register_button_more_style_controls();

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
		$id = $this->get_id();

		// Le wrapper du container
		$this->add_render_attribute( 'container_wrapper', 'class', 'author-infobox_container' );
		$this->add_render_attribute( 'container_wrapper', 'id', esc_attr( $id ) );

		?>
		<div class="eac-author-infobox">
			<div <?php $this->print_render_attribute_string( 'container_wrapper' ); ?>>
				<?php $this->render_infobox(); ?>
			</div>
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
	protected function render_infobox(): void {
		global $authordata;
		$settings = $this->get_settings_for_display();

		/** La variable globale n'est pas définie */
		if ( ! isset( $authordata->ID ) ) {
			$post = get_post();
			if ( $post ) {
				$authordata = get_userdata( $post->post_author ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		if ( ! isset( $authordata->ID ) ) {
			return;
		}

		$name       = '';
		$prefix     = 'yes' === $settings['aib_content_prefix_name'] ? esc_html__( 'About', 'eac-components' ) : '';
		$role       = '';
		$bio        = '';
		$avatar_url = '';
		$name_tag   = ! empty( $settings['aib_settings_name_tag'] ) ? Utils::validate_html_tag( $settings['aib_settings_name_tag'] ) : 'div';
		$role_tag   = ! empty( $settings['aib_settings_title_tag'] ) ? Utils::validate_html_tag( $settings['aib_settings_title_tag'] ) : 'div';

		// La classe du titre/texte
		$this->add_render_attribute( 'content_wrapper', 'class', 'author-infobox_content' );

		// L'avatar de l'auteur
		$avatar_url = get_avatar_url( $authordata->ID, array( 'size' => 150 ) );

		$has_role   = 'yes' === $settings['aib_content_role'] ? true : false;
		$has_bio    = 'yes' === $settings['aib_content_bio'] ? true : false;
		$has_social = 'yes' === $settings['aib_content_social'] && ! empty( $settings['aib_settings_social_network'] ) && '#' !== $settings['aib_settings_social_network'] ? true : false;

		// Le nom complet de l'auteur
		if ( ! empty( get_the_author_meta( 'display_name', $authordata->ID ) ) ) {
			$name = sprintf( '<%1$s class="author-infobox_name-content">%2$s %3$s</%1$s>', $name_tag, $prefix, esc_html( get_the_author_meta( 'display_name', $authordata->ID ) ) );
		}

		// Le/les rôles de l'auteur
		if ( $has_role ) {
			$list_role = array();
			$user_info  = new \WP_User( get_the_author_meta( 'ID', $authordata->ID ) );
			if ( ! empty( $user_info->roles ) && is_array( $user_info->roles ) ) {
				global $wp_roles;
				foreach ( $user_info->roles as $role_slug ) {
					$role_name = $wp_roles->roles[ $role_slug ]['name'] ?? $role_slug;
					$list_role[] = translate_user_role( $role_name );
				}
				sort( $list_role, SORT_STRING );
				$list_role = implode( ', ', array_map( 'esc_html', $list_role ) );
				$role = sprintf( '<%1$s class="author-infobox_role-content">%2$s</%1$s>', esc_attr( $role_tag ), $list_role );
			}
		}

		// La description/biographie de l'auteur
		if ( $has_bio ) {
			$bio = get_the_author_meta( 'description', $authordata->ID );
		}

		// Le bouton 'View archives'
		$author_nicename  = get_the_author_meta( 'display_name', $authordata->ID );
		$has_button       = 'yes' === $settings['aib_content_readmore'] ? true : false;
		$id               = get_the_ID();
		$main_id          = get_the_ID();
		if ( \Elementor\Plugin::$instance->documents->get_current() !== null ) {
			$main_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}

		/**
		 * Création de l'option pour ajouter le widget au contenu du post_type et des post_id sélectionnés
		 * Ajout de l'option uniquement dans un template Elementor
		 */
		if ( true === $settings['aib_is_a_template'] && $id === $main_id ) {
			if ( 'yes' === $settings['aib_settings_integrate_display'] ) {
				$args = array(
					'post_id'   => '',      // ID du modèle Elementor
					'post_type' => '',      // Le post_type qui peut afficher le contenu du template
					'position'  => '',      // La position du contenu du template
					'post_ids'  => array(), // La liste des IDs qui peuvent afficher le contenu du template. Format: [index::id] = title
				);

				$args['post_id']   = absint( get_post()->ID );
				$args['post_type'] = sanitize_text_field( $settings['aib_settings_integrate_posttype'] );
				$args['position']  = sanitize_text_field( $settings['aib_settings_integrate_position'] );
				$post_ids          = $settings[ 'aib_settings_integrate_postid_' . $settings['aib_settings_integrate_posttype'] ];

				if ( is_array( $post_ids ) && ! empty( $post_ids ) ) {
					foreach ( $post_ids as $post_id ) {
						if ( \str_contains( $post_id, '::' ) ) {
							$post_id_id = explode( '::', $post_id )[1];
							array_push( $args['post_ids'], absint( $post_id_id ) );
						}
					}
				}
				update_option( $this->option_infobox, $args, false );
			} else {
				delete_option( $this->option_infobox ); // Supprime systématiquement l'option
			}
		} ?>
		<div <?php $this->print_render_attribute_string( 'content_wrapper' ); ?>>
			<?php if ( ! empty( $avatar_url ) ) : ?>
				<div class='author-infobox_image'>
					<img class='avatar photo' src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php printf( 'Avatar of %1$s', esc_attr( ucfirst( $author_nicename ) ) ); ?>"  loading='lazy' width='150' height='150' />
				</div>
			<?php endif; ?>

			<div class='author-infobox_wrapper-info'>
				<div class='author-infobox_info-content'>
					<?php if ( ! empty( $name ) ) : ?>
						<div class='author-infobox_name'>
							<?php echo trim( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $role ) ) : ?>
						<div class='author-infobox_role'>
							<?php echo $role; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $bio ) ) : ?>
						<div class='author-infobox_biography'>
							<p><?php echo nl2br( esc_html( $bio ) ); ?></p>
						</div>
					<?php endif; ?>
					<?php if ( $has_social ) : ?>
						<div class='author-infobox_social'>
							<?php echo wp_kses_post( $settings['aib_settings_social_network'] ); ?>
						</div>
					<?php endif; ?>
					<?php if ( $has_button ) :
						$label = ! empty( $settings['aib_relation_more_label'] ) ? $settings['aib_relation_more_label'] : esc_html__( 'View archives', 'eac-components' );
						$this->add_render_attribute( 'button_readmore', 'class', 'button-readmore eac-accessible-link' );
						$this->add_render_attribute( 'button_readmore', 'aria-label', sprintf( '%s - %s', esc_attr__( 'View archives', 'eac-components' ), esc_attr( $author_nicename ) ) );
						$this->add_render_attribute( 'button_readmore', 'href', esc_url( get_author_posts_url( $authordata->ID ) ) );
						$this->add_inline_editing_attributes( 'aib_relation_more_label', 'none' );
						$this->add_render_attribute( 'aib_relation_more_label', 'class', 'label-icon' ); ?>
						<div class='buttons-wrapper'>
							<a <?php $this->print_render_attribute_string( 'button_readmore' ); ?>>
								<span class='button__readmore-wrapper'>
									<span <?php $this->print_render_attribute_string( 'aib_relation_more_label' ); ?>><?php echo esc_html( $label ); ?></span>
								</span>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function content_template(): void {}
}
