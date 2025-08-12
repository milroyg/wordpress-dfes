<?php
/**
 * Class: Eac_Load_Components
 *
 * Description: Charge les groups, controls et les composants actifs Pour Elementor
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

class Eac_Load_Components {

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/**
	 * Constructeur de la class
	 *
	 * Ajoute les actions pour enregsitrer les goupes, controls et widgets Elementor
	 *
	 * @param $elements La liste des composants et leur état
	 */
	public function __construct() {
		/** Initialize le module Header & Footer dans le dashboard */
		if ( Eac_Config_Elements::is_widget_active( 'header-footer' ) ) {
			new \EACCustomWidgets\Includes\TemplatesLib\Documents\Manager();
		}

		/** Filtres WooCommerce. Le mega menu intègre le filtre 'woocommerce_add_to_cart_fragments' pour le mini-cart */
		if ( Eac_Config_Elements::is_widget_active( 'woo-product-grid' ) || Eac_Config_Elements::is_widget_active( 'mega-menu' ) ) {
			\EACCustomWidgets\Includes\Woocommerce\Eac_Woo_Filters::instance();
		} else {
			// On force la suppression de l'option des filtres WC par sécurité
			delete_option( Eac_Config_Elements::get_woo_hooks_option_name() );
		}

		/** Utils pour tous les composants et les extensions */
		new \EACCustomWidgets\Core\Utils\Eac_Tools_Util();

		/** Création des catégories de composants */
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_categories' ) );

		/** Charge les controls. Enregistre les class des controls */
		add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );

		/** Charge les widgets. Enregistre les class des composants */
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/** Singleton de la class */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Crée les catégories des composants */
	public function register_categories( $elements_manager ) {
		$elements_manager->add_category(
			'eac-advanced',
			array(
				'title' => esc_html__( 'EAC Avancés', 'eac-components' ),
				'icon'  => 'fa fa-plug',
			)
		);
		$elements_manager->add_category(
			'eac-basic',
			array(
				'title' => esc_html__( 'EAC Basiques', 'eac-components' ),
				'icon'  => 'fa fa-plug',
			)
		);
		$elements_manager->add_category(
			'eac-ehf',
			array(
				'title' => esc_html__( 'EAC Entête & Pied de page', 'eac-components' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Enregistre les nouveaux controls
	 *
	 * @args $controls_manager Gestionnaire des controls
	 */
	public function register_controls( $controls_manager ) {
		// Enregistre le control 'file-viewer' pour le composant 'PDF viewer'
		$controls_manager->register( new \EACCustomWidgets\Includes\Elementor\Controls\File_Viewer_Control() );

		// Enregistre le control 'eac-select2' pour le control select2
		$controls_manager->register( new \EACCustomWidgets\Includes\Elementor\Controls\Eac_Select2_Control() );
	}

	/**
	 * register_widgets
	 * Enregistre les composants actifs
	 *
	 * @param mixed $widgets_manager Gestionnaire des widgets
	 *
	 * @return void
	 */
	public function register_widgets( $widgets_manager ): void {
		foreach ( Eac_Config_Elements::get_widgets_active() as $element => $active ) {
			if ( Eac_Config_Elements::is_widget_active( $element ) ) {
				$full_class_name = Eac_Config_Elements::get_widget_namespace( $element );
				if ( $full_class_name ) {
					$widgets_manager->register( new $full_class_name() );
				}
			}
		}
	}
}
