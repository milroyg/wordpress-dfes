<?php
/**
 * Class: Breadcrumb_Widget
 * Slug: eac-addon-breadcrumbs
 *
 * Description: Ajoute un breadcrumb
 *
 * @since 2.1.1
 * @since 2.3.5 Ajout du shortcode 'eac_breadcrumb'
 */

namespace EACCustomWidgets\Includes\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\TemplatesLib\Widgets\Classes\Class_Breadcrumb;
use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Icons_Manager;
use Elementor\Utils;

class Breadcrumb_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'breadcrumbs';

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
	 * @return widget category.
	 */
	public function get_categories(): array {
		return Eac_Load_Config::get_widget_categories( $this->slug );
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

		$this->start_controls_section(
			'bdc_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'bdc_home_title',
				array(
					'label'       => esc_html__( 'Home label', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Home', 'eac-components' ),
					'ai'          => array( 'active' => false ),
					'label_block' => false,
				)
			);

			$this->add_control(
				'bdc_display_title',
				array(
					'label'   => esc_html__( 'Display current title', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default' => 'yes',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'bdc_title_length',
				array(
					'label'       => esc_html__( 'Number of words', 'eac-components' ),
					'description' => esc_html__( 'Number of words in the title. 0 = all words', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 0,
					'max'         => 50,
					'step'        => 1,
					'default'     => 0,
					'condition'   => array( 'bdc_display_title' => 'yes' ),
				)
			);

			$this->add_control(
				'bdc_title_tag',
				array(
					'label'       => esc_html__( 'Item tag', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'h2'   => array(
							'title' => 'H2',
							'icon'  => 'eicon-editor-h2',
						),
						'h3'   => array(
							'title' => 'H3',
							'icon'  => 'eicon-editor-h3',
						),
						'h4'   => array(
							'title' => 'H4',
							'icon'  => 'eicon-editor-h4',
						),
						'h5'   => array(
							'title' => 'H5',
							'icon'  => 'eicon-editor-h5',
						),
						'h6'   => array(
							'title' => 'H6',
							'icon'  => 'eicon-editor-h6',
						),
						'span' => array(
							'title' => esc_html__( 'Paragraph', 'eac-components' ),
							'icon'  => 'eicon-editor-paragraph',
						),
					),
					'default'     => 'span',
					'toggle'      => false,
					'label_block' => true,
				)
			);

			$this->add_control(
				'dbc_icon_separator',
				array(
					'label'                  => esc_html__( 'Separator', 'eac-components' ),
					'type'                   => Controls_Manager::ICONS,
					'default'                => array(
						'value'   => 'fas fa-angle-right',
						'library' => 'fa-solid',
					),
					'label_block'            => 'true',
					'skin'                   => 'inline',
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'bdc_style',
			array(
				'label' => esc_html__( 'Breadcrumbs', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_responsive_control(
				'bdc_alignment',
				array(
					'label'     => esc_html__( 'Alignment', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'flex-start' => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center'     => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'flex-end'   => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'flex-start',
					'separator' => 'before',
					'selectors' => array(
						'{{WRAPPER}} .eac-breadcrumbs' => 'justify-content: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'bdc_bg_color',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .eac-breadcrumbs' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'bdc_padding',
				array(
					'label'     => esc_html__( 'Padding', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .eac-breadcrumbs' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{LEFT}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'bdc_item_style',
			array(
				'label' => esc_html( 'Items' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'bdc_item_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
					'default'   => '#000000',
					'selectors' => array(
						'{{WRAPPER}} nav .eac-breadcrumbs-item, {{WRAPPER}} nav .eac-breadcrumbs-item a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'bdc_item_typography',
					'label'          => esc_html__( 'Typography', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1,
							),
						),
					),
					'selector'       => '{{WRAPPER}} nav .eac-breadcrumbs-item, {{WRAPPER}} nav .eac-breadcrumbs-separator, {{WRAPPER}} nav .eac-breadcrumbs-separator svg',
				)
			);

			$this->add_control(
				'bdc_item_color_separator',
				array(
					'label'     => esc_html__( 'Color separator', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
					'default'   => '#000000',
					'selectors' => array(
						'{{WRAPPER}} nav .eac-breadcrumbs-separator' => 'color: {{VALUE}};',
					),
					'condition' => array( 'dbc_icon_separator!' => '' ),
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
		$settings          = $this->get_settings_for_display();
		$default_separator = ' ';
		$breadcrumb        = null;

		if ( ! empty( $settings['dbc_icon_separator'] ) ) {
			ob_start();
			Icons_Manager::render_icon( $settings['dbc_icon_separator'], array( 'aria-hidden' => 'true' ) );
			$icon_separator = ob_get_clean();
		}

		$args = array(
			'separator'     => ! empty( $icon_separator ) ? " $icon_separator " : $default_separator,
			'item_tag'      => ! empty( $settings['bdc_title_tag'] ) ? Utils::validate_html_tag( $settings['bdc_title_tag'] ) : 'span',
			'show_title'    => 'yes' === $settings['bdc_display_title'] ? true : false,
			'trunk_title'   => isset( $settings['bdc_title_length'] ) ? absint( $settings['bdc_title_length'] ) : 0,
			'post_taxonomy' => array(
				'post' => '',
			),
			'labels'        => array(
				'home'       => ! empty( $settings['bdc_home_title'] ) ? sanitize_text_field( $settings['bdc_home_title'] ) : esc_html__( 'Home', 'eac-components' ),
				'page_title' => '',
			),
		);

		$breadcrumb = new Class_Breadcrumb( $args );
		/**if ( ! empty( $breadcrumb->items ) ) {
			//write_log( $breadcrumb->items );
			$this->extract_breadcrumb_data( $breadcrumb->items );
		}*/
		echo wp_kses_post( $breadcrumb->trail() );
	}

	private function extract_breadcrumb_data( $items = array() ) {
		$results = array();

		foreach ( $items as $html ) {
			$dom = new \DOMDocument; // phpcs:ignore PSR12.Classes.ClassInstantiation.MissingParentheses

			// Charger le HTML
			libxml_use_internal_errors( true ); // Pour éviter les erreurs de parsing
			$dom->loadHTML( $html );
			libxml_clear_errors();

			// Récupérer tous les éléments <a>
			$links = $dom->getElementsByTagName( 'a' );

			if ( $links->length > 0 ) {
				$link = $links->item( 0 ); // On prend le premier lien

				// Extraire l'attribut href
				$href = $link->getAttribute( 'href' );
				// Extraire le texte du lien
				$text = $link->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

				// Ajouter les informations au tableau de résultats
				$results[ $text ] = array(
					'href' => $href,
					'text' => $text,
				);
			}
		}
	}

	protected function content_template(): void {}
}
