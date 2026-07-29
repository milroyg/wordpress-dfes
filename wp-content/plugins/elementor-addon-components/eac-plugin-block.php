<?php
/**
 * Class: EAC_Plugin
 *
 * Description:  Active l'administration du plugin pour les blocks Gutenberg et charge les fonctionnalités liées aux blocks
 *
 * @since 2.4.9
 * @since 2.5.1 Ajout des fonctions get_script_path et get_style_path
 */

namespace EACCustomWidgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main Plugin Class
 */
class EAC_Plugin {

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/** @var $args */
	private $args = array(
		'strategy' => 'defer',
		'in_footer' => true,
	);

	/**
	 * @var suffix_css
	 *
	 * Debug des fichiers CSS
	 */
	private $suffix_css = EAC_STYLE_DEBUG ? '.css' : '.min.css';

	/**
	 * @var suffix_js
	 *
	 * Debug des fichiers JS
	 */
	private $suffix_js = EAC_SCRIPT_DEBUG ? '.js' : '.min.js';

	/**
	 * Constructeur
	 * L'ordre de chargement des modules est important
	 */
	private function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );

		/** Ajouter le type 'module' ES6 à certains scripts */
		add_filter( 'script_loader_tag', array( $this, 'add_script_attribute_module' ), 10, 2 );

		/** Defer certains fichiers de styles */
		add_filter( 'style_loader_tag', array( $this, 'add_style_attribute' ), 10, 2 );

		/** Compatibilité du plugin avec la fonctionnalité HPOS de Woocommerce */
		add_action(
			'before_woocommerce_init',
			function () {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', EAC_PLUGIN_BASENAME, true );
				}
			}
		);

		// Charge les champs ACF et les blocks
		require_once EAC_INCLUDES_PATH . 'acf/fields/gallery/eac-acf-gallery-field.php';
		require_once EAC_INCLUDES_PATH . 'acf/fields/repeater/eac-acf-repeater-field.php';
		require_once EAC_INCLUDES_PATH . 'blocks/gallery/block.php';
		require_once EAC_INCLUDES_PATH . 'blocks/repeater/block.php';
		require_once EAC_INCLUDES_PATH . 'blocks/relationship/block.php';
		require_once EAC_INCLUDES_PATH . 'blocks/meteo/block.php';

		// Charge les page d'options
		new \EACCustomWidgets\Includes\Acf\Eac_Acf_Options_Page();

		if ( current_user_can( 'update_plugins' ) && is_admin() && ! wp_doing_ajax() ) {
			new \EACCustomWidgets\Core\Utils\Eac_Plugin_Updater();
		}
	}

	/**
	 * instance.
	 *
	 * Garantir une seule instance de la class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * autoload
	 * Charge le fichier php relativement à namespace + class name
	 * Le nom de la classe doit être égal au nom du fichier (sans l'extension) et respecter la structure des dossiers
	 *
	 * @param string $class_to_load namespace + class name
	 *
	 * @return void
	 */
	public function autoload( string $class_to_load ): void {
		// namespace prefixe
		$prefix = 'EACCustomWidgets';

		// Ce n'est pas notre plugin
		if ( 0 !== strpos( $class_to_load, $prefix ) ) {
			return;
		}

		if ( ! class_exists( $class_to_load, false ) ) {
			// Conversion namespace en path
			$filename = strtolower(
				preg_replace(
					array( '/^EACCustomWidgets\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
					array( '', '$1-$2', '-', DIRECTORY_SEPARATOR ),
					$class_to_load
				)
			);

			$file = wp_normalize_path( EAC_PLUGIN_PATH . str_replace( '-widget', '', $filename ) . '.php' );

			if ( is_readable( $file ) ) {
				require_once $file;
			} else {
				error_log( 'FILE NOT READABLE: ' . $file ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		}
	}

	/** Singletons should not be cloneable */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( 'Il y a quelque chose de pourri au Royaume du Danemark' ), '1.0.0' );
	}

	/** Singletons should not be restorable from strings */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( 'Il y a quelque chose de pourri au Royaume du Danemark' ), '1.0.0' );
	}

	/**
	 * get_script_url
	 *
	 * Construit l'url du fichier et ajoute l'extension relative à la constant globale
	 *
	 * @param string $file
	 *
	 * @return String Chemin absolu du fichier JS passé en paramètre
	 */
	public function get_script_url( string $file ): string {
		return esc_url( EAC_PLUGIN_URL . $file . $this->suffix_js );
	}

	/**
	 * get_style_url
	 *
	 * Construit l'url du fichier et ajoute l'extension relative à la constant globale
	 *
	 * @param string $file
	 *
	 * @return String Chemin absolu du fichier CSS passé en paramètre
	 */
	public function get_style_url( string $file ): string {
			return esc_url( EAC_PLUGIN_URL . $file . $this->suffix_css );
	}

	/**
	 * get_script_path
	 *
	 * Construit le chemin du fichier
	 *
	 * @param string $file
	 *
	 * @return String Chemin absolu du fichier JS passé en paramètre
	 */
	public function get_script_path( string $file ): string {
		return wp_normalize_path( EAC_PLUGIN_PATH . $file . '.js' );
	}

	/**
	 * get_style_path
	 *
	 * Construit le chemin du fichier
	 *
	 * @param string $file
	 *
	 * @return String Chemin absolu du fichier CSS passé en paramètre
	 */
	public function get_style_path( string $file ): string {
			return wp_normalize_path( EAC_PLUGIN_PATH . $file . '.css' );
	}

	/**
	 * get_dashboard_icon_url
	 *
	 * Retourne le chemin de l'icône du plugin pour la page d'administration
	 *
	 * @return string
	 */
	public function get_dashboard_icon_url(): string {
		return esc_url( EAC_PLUGIN_URL . 'admin/images/logos/dashboard-icon.svg' );
	}

	/**
	 * add_script_attribute_module
	 *
	 * Ajout de l'attribut type="module" pour les scripts qui en ont besoin
	 * Le type "module" permet d'utiliser les fonctionnalités modernes de JavaScript, comme les imports et exports, et d'assurer un chargement différé des scripts.
	 * Certains scripts du plugin nécessitent cet attribut pour fonctionner correctement, notamment ceux qui utilisent des fonctionnalités ES6 ou qui sont conçus pour être chargés en tant que modules.
	 *
	 * @param string $tag
	 * @param string $handle
	 *
	 * @return string
	 */
	public function add_script_attribute_module( string $tag, string $handle ): string {
		$module_scripts = array( 'eac-relationship-block', 'eac-repeater-block', 'eac-gallery-block', 'eac-meteo-block' );

		if ( in_array( $handle, $module_scripts, true ) ) {
			$tag = str_replace( '<script ', '<script type="module" ', $tag );
		}
		return $tag;
	}

	/**
	 * add_style_attribute
	 *
	 * defer le chargement des styles
	 *
	 * @param string $html le contenu du tag
	 * @param string $handle ID du style
	 *
	 * @return string
	 */
	public function add_style_attribute( string $html, string $handle ): string {
		$module_styles = array( 'eac-fancybox' );

		if ( in_array( $handle, $module_styles, true ) ) {
			$html = str_replace( 'media=\'all\'', "media='print' onload=\"this.onload=null; this.media='all';\"", $html );
		}
		return $html;
	}
}
EAC_Plugin::instance();
