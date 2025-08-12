<?php
/**
 * Class: Eac_Injection_Links
 *
 * Description:  Créer les controls pour ajouter un lien sur une colonne/section
 *
 * @since 1.8.4
 */

namespace EACCustomWidgets\Includes\Elementor\Injection;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\EAC_Plugin;
use Elementor\Controls_Manager;
use Elementor\Element_Base;

class Eac_Injection_Links {

	/**
	 * Constructeur de la class
	 */
	public function __construct() {
		add_action( 'elementor/element/column/layout/before_section_end', array( $this, 'inject_section_column' ), 10, 1 );
		add_action( 'elementor/element/section/section_layout/before_section_end', array( $this, 'inject_section_column' ), 10, 1 );
		add_action( 'elementor/element/container/section_layout_container/before_section_end', array( $this, 'inject_section_column' ), 10, 1 );

		add_action( 'elementor/frontend/section/before_render', array( $this, 'render_link' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'render_link' ) );
		add_action( 'elementor/frontend/container/before_render', array( $this, 'render_link' ) );

		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_scripts' ), 12 );
	}

	/**
	 * enqueue_scripts
	 *
	 * Mets le script dans le file
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'eac-element-link', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-element-link' ), array( 'jquery', 'elementor-frontend' ), '1.8.4', true );
	}

	/**
	 * inject_section_column
	 *
	 * Inject le control en fin de section 'layout'
	 * pour les sections et colonnes
	 *
	 * @param Element_Base $element    The edited element.
	 * @param array        $args       Element arguments.
	 */
	public function inject_section_column( $element ) {

		$element->add_control(
			'eac_element_link',
			array(
				'label'        => esc_html__( 'Lien container', 'eac-components' ),
				'type'         => Controls_Manager::URL,
				'placeholder'  => esc_html__( 'Coller une URL ou taper', 'eac-components' ),
				'dynamic'      => array(
					'active' => true,
				),
				'autocomplete' => true,
				'label_block'  => true,
				'render_type'  => 'none',
				'separator'    => 'before',
			)
		);

		$element->add_control(
			'eac_element_link_usage',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf(
					/* translators: 1: Description, 2: Link opening tag, 3: Link closing tag. */
					esc_html__( '%1$s %2$sConsulter la documentation%3$s', 'eac-components' ),
					esc_html__( "Le lien n'est pas actif dans l'éditeur.", 'eac-components' ),
					'<a href="https://elementor-addon-components.com/add-link-to-a-section-column-using-elementor/" target="_blank" rel="noopener noreferrer">',
					'</a>',
				),
				'content_classes' => 'elementor-descriptor',
			)
		);
	}


	/**
	 * render_link
	 *
	 * Ajoute les propriétés dans l'objet avant le rendu
	 *
	 * @param $element  Element_Base
	 */
	public function render_link( $element ) {
		$settings  = $element->get_settings_for_display();
		$attributs = '';

		// Le control existe et il est renseigné
		if ( isset( $settings['eac_element_link'] ) && ! empty( $settings['eac_element_link']['url'] ) && '#' !== $settings['eac_element_link']['url'] ) {
			$element->add_link_attributes( 'attributes_link', $settings['eac_element_link'] );
			$element->add_render_attribute( 'attributes_link', 'class', 'eac-accessible-link eac-element-link' );
			if ( $settings['eac_element_link']['is_external'] ) {
				$element->add_render_attribute( 'attributes_link', 'rel', 'noopener noreferrer' );
			}

			// Elementor utilise data-settings dans les sections/conteneur
			$element->add_render_attribute(
				'_wrapper',
				array(
					'data-eac_settings-link' => wp_json_encode(
						array(
							'url' => $element->get_render_attribute_string( 'attributes_link' ),
						)
					),
				)
			);
		}
	}
}
