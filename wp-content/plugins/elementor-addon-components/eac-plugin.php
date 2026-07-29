<?php
/**
 * Class: EAC_Plugin
 *
 * Description:  Active l'administration du plugin
 * Charge la configuration, les widgets et les fonctionnalités
 *
 * @since 1.0.0
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

		/** Charge la configuration du plugin et des composants */
		\EACCustomWidgets\Core\Eac_Load_Config::instance();

		/** Charge les scripts et les styles globaux */
		new \EACCustomWidgets\Core\Eac_Load_Assets();

		/** Page d'administration du plugin */
		if ( current_user_can( 'manage_options' ) ) {
			\EACCustomWidgets\Admin\Settings\EAC_Load_Settings::instance();
		}

		/** Charge les fonctionnalités */
		new \EACCustomWidgets\Core\Eac_Load_Features();

		/** Charge les catégories, les controls et les composants Elementor */
		new \EACCustomWidgets\Core\Eac_Load_Components();

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
		$module_scripts = array( 'eac-relationship-block', 'eac-repeater-block', 'eac-gallery-block', 'eac-meteo-block', 'instant-page', 'eac-acf-repeater', 'eac-acf-relation', 'eac-image-gallery', 'eac-advanced-gallery', 'eac-post-grid', 'eac-rss-reader', 'eac-news-ticker', 'eac-pinterest-rss' );

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
		$module_styles = array( 'eac-fancybox', 'elegant-icons', 'eac-nav-menu' );

		if ( in_array( $handle, $module_styles, true ) ) {
			$html = str_replace( 'media=\'all\'', "media='print' onload=\"this.onload=null; this.media='all';\"", $html );
		}
		return $html;
	}

	/**
	 * register_script
	 *
	 * @param string $handle ID du script
	 * @param string $src chemin du script
	 * @param array $deps les dépendances
	 * @param string $ver la version
	 * @param array $args array d'arguments
	 *
	 * @return void
	 */
	public function register_script( string $handle, string $src, array $deps, string $ver, array $args = array() ): void {
		global $wp_version;
		$url = $this->get_script_url( $src );

		// If WP >= 6.3, re-use wrapper function signature.
		if ( version_compare( $wp_version, '6.3', '>=' ) ) {
			wp_register_script(
				$handle,
				$url,
				$deps,
				$ver,
				$args
			);
		} else {
			// Extract in_footer value for older version usage.
			$in_footer = isset( $args['in_footer'] ) ? $args['in_footer'] : true;
			wp_register_script(
				$handle,
				$url,
				$deps,
				$ver,
				$in_footer
			);
		}
	}
}
EAC_Plugin::instance();
