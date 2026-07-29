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

use EACCustomWidgets\Core\Eac_Load_Config;

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
	 * Ajoute les actions pour enregistrer les goupes, controls et widgets Elementor
	 */
	public function __construct() {
		/** Initialize le module Header & Footer dans le dashboard */
		if ( Eac_Load_Config::is_widget_active( 'header-footer' ) ) {
			new \EACCustomWidgets\Includes\TemplatesLib\Documents\Manager();
		}

		/** Filtres WooCommerce. Le mega menu intègre le filtre 'woocommerce_add_to_cart_fragments' pour le mini-cart */
		if ( Eac_Load_Config::is_widget_active( 'woo-product-grid' ) || Eac_Load_Config::is_widget_active( 'mega-menu' ) ) {
			\EACCustomWidgets\Includes\Woocommerce\Eac_Woo_Filters::instance();
		} else {
			// On force la suppression de l'option des filtres WC par sécurité
			delete_option( Eac_Load_Config::get_woo_hooks_option_name() );
		}

		/** Les actions AJAX 'wp_ajax_xxxxxx' pour les widgets Mini cart du Menu et la barre de recherche */
		if ( Eac_Load_Config::is_widget_active( 'mega-menu' ) || Eac_Load_Config::is_widget_active( 'site-search' ) ) {
			new \EACCustomWidgets\Includes\TemplatesLib\Widgets\Classes\Class_Templates_Lib_Actions();
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

	/**
	 * register_categories
	 * Crée les catégories des composants
	 *
	 * @param \Elementor\Elements_Manager $elements_manager
	 *
	 * @return void
	 */
	public function register_categories( \Elementor\Elements_Manager $elements_manager ): void {
		$elements_manager->add_category(
			'eac-advanced',
			array(
				'title' => esc_html__( 'EAC Advanced', 'eac-components' ),
				'icon'  => 'fa fa-plug',
			)
		);
		$elements_manager->add_category(
			'eac-basic',
			array(
				'title' => esc_html__( 'EAC Basic', 'eac-components' ),
				'icon'  => 'fa fa-plug',
			)
		);
		$elements_manager->add_category(
			'eac-ehf',
			array(
				'title' => esc_html__( 'EAC Header & Footer', 'eac-components' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * register_controls
	 * Enregistre les nouveaux controls
	 *
	 * @param \Elementor\Controls_Manager $controls_manager
	 *
	 * @return void
	 */
	public function register_controls( \Elementor\Controls_Manager $controls_manager ): void {
		// Enregistre le control 'file-viewer' pour le composant 'PDF viewer'
		$controls_manager->register( new \EACCustomWidgets\Includes\Elementor\Controls\File_Viewer_Control() );

		// Enregistre le control 'eac-select2' pour le control select2
		$controls_manager->register( new \EACCustomWidgets\Includes\Elementor\Controls\Eac_Select2_Control() );
	}

	/**
	 * register_widgets
	 * Enregistre les composants actifs
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 *
	 * @return void
	 */
	public function register_widgets( \Elementor\Widgets_Manager $widgets_manager ): void {
		foreach ( Eac_Load_Config::get_widgets_active() as $element => $active ) {
			if ( Eac_Load_Config::is_widget_active( $element ) ) {
				$full_class_name = Eac_Load_Config::get_widget_namespace( $element );
				if ( $full_class_name ) {
					$widgets_manager->register( new $full_class_name() );
				}
			}
		}
	}
}
