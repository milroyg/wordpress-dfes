<?php
/**
 * Class: Eac_Load_Assets
 *
 * Description: Enregistre les scripts/styles
 * Filtre les attributs des balises HTML autorisées
 * Ajout et valorisation des colonnes dans les vues Elementor
 *
 * @since 1.9.2
 * @since 2.4.1 Chargement des scripts, des styles et de leurs dépendances
 * @since 2.4.3 Chargement des scripts et styles dans le preview d'Elementor du fait de l'utilisation du paramètre 'assets' dans certain control
 */

namespace EACCustomWidgets\Core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Eac_Load_Config;
use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Includes\TemplatesLib\Documents\Site_Header;
use EACCustomWidgets\Includes\TemplatesLib\Documents\Site_Footer;

use Elementor\TemplateLibrary\Source_Local;

class Eac_Load_Assets {

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
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		/** Ajout des shortcodes ACF gallery, repeater... */
		require_once __DIR__ . '/utils/eac-global.php';

		/** @since 2.4.1 Actions pour charger les styles et scripts */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_global_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_global_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 11 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 11 );

		/** Action pour insérer les styles dans le panel Elementor */
		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'enqueue_panel_styles' ) );

		/**
		 * Actions pour enqueuer les scripts et les styles dans le preview Elementor
		 * Mais je ne comprends pas pourquoi !!
		 *
		 * @since 2.4.3
		 */
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_preview_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );

		/** Filtre les attributs des balises HTML autorisées */
		add_filter( 'wp_kses_allowed_html', array( $this, 'add_allowed_attribute_element' ), 10, 2 );

		/** Ajout et valorisation des colonnes des vues Elementor */
		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			add_filter( 'manage_' . Source_Local::CPT . '_posts_columns', array( $this, 'add_columns' ) );
			add_action( 'manage_' . Source_Local::CPT . '_posts_custom_column', array( $this, 'data_columns' ), 10, 2 );
			add_shortcode( 'eac_elementor_tmpl', array( $this, 'display_elementor_template' ) );
		}

		/** Priorité 99 pour que le contenu des shortcodes soit affiché avant */
		if ( \EACCustomWidgets\Core\Eac_Load_Config::is_widget_active( 'author-infobox' ) ) {
			add_filter( 'the_content', array( $this, 'embed_author_infobox' ), 99 );
		}

		/** !!!! */
		/**$this->trigger_group_acf();*/
	}

	/** Singleton de la class */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Imbitable si la fonction n'est pas appelée, la colonne ACF groups ne sera pas valorisée */
	public function trigger_group_acf() {
		if ( function_exists( 'acf_get_field_groups' ) ) {
			$groups = count( acf_get_field_groups() );
		}
	}

	/**
	 * enqueue_panel_styles
	 * Enregistre les styles dans le panel de l'éditeur Elementor
	 *
	 * @return void
	 */
	public function enqueue_panel_styles(): void {
		wp_enqueue_style( 'eac-editor-panel', EAC_Plugin::instance()->get_style_url( 'assets/css/eac-editor-panel' ), false, EAC_PLUGIN_VERSION );

		// Experiment 'Inline font icons' activé
		$is_shim_active       = 'yes' === get_option( \Elementor\Icons_Manager::LOAD_FA4_SHIM_OPTION_KEY, false ) ? true : false;
		$is_inlinefont_active = \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' );
	}

	/**
	 * enqueue_preview_scripts
	 * Enregistre les scripts dans le preview de l'éditeur Elementor
	 *
	 * @return void
	 */
	public function enqueue_preview_scripts(): void {
		wp_enqueue_script( 'swiper-bundle' );
		wp_enqueue_script( 'isotope' );
		wp_enqueue_script( 'fit-rows' );
		wp_enqueue_script( 'infinite-scroll' );
		wp_enqueue_script( 'fj-gallery' );
	}

	/**
	 * enqueue_preview_styles
	 * Enregistre les styles dans le preview de l'éditeur Elementor
	 *
	 * @return void
	 */
	public function enqueue_preview_styles(): void {
		wp_enqueue_style( 'swiper-bundle' );
		wp_enqueue_style( 'eac-swiper' );
		wp_enqueue_style( 'fj-gallery' );
	}

	/**
	 * enqueue_scripts
	 *
	 * Enqueue les styles et scripts globaux
	 */
	public function enqueue_global_scripts(): void {
		// Le CSS/JS global
		wp_register_script( 'eac-frontend', EAC_Plugin::instance()->get_script_url( 'assets/js/eac-frontend' ), array( 'jquery' ), EAC_PLUGIN_VERSION, true );

		/** Passe les URLs absolues de certains composants aux objects javascript */
		wp_add_inline_script(
			'eac-frontend',
			'var eacElementsPath = ' . wp_json_encode(
				array(
					'proxies'   => EAC_PLUGIN_URL . 'includes/proxy/',
					'pdfJs'     => EAC_PLUGIN_URL . 'assets/js/pdfjs/',
					'osmImages' => EAC_PLUGIN_URL . 'assets/images/',
					'osmConfig' => EAC_PLUGIN_URL . 'includes/config/osm/',
				)
			),
			'before'
		);
		wp_enqueue_script( 'eac-frontend' );

		// Le JS de la Fancybox
		EAC_Plugin::instance()->register_script( 'eac-fancybox', 'assets/js/fancybox/jquery.fancybox', array( 'jquery' ), '3.5.7', $this->args );
		wp_enqueue_script( 'eac-fancybox' );

		// https://instant.page/tech
		if ( Eac_Load_Config::is_feature_active( 'preload-page' ) && ! wp_script_is( 'instant-page', 'enqueued' ) ) {
			add_action( 'wp_footer', function () {
				EAC_Plugin::instance()->register_script( 'instant-page', 'assets/js/instantpage/instantpage', array(), '5.2.0', $this->args );
				wp_enqueue_script( 'instant-page' );
			} );
		}
	}

	/**
	 * enqueue_styles
	 *
	 * Enqueue les styles et scripts globaux
	 */
	public function enqueue_global_styles(): void {
		// Le CSS/JS global
		wp_enqueue_style( 'eac-frontend', EAC_Plugin::instance()->get_style_url( 'assets/css/eac-frontend' ), array(), EAC_PLUGIN_VERSION );

		// Le CSS de la Fancybox
		wp_enqueue_style( 'eac-fancybox', EAC_Plugin::instance()->get_style_url( 'assets/css/jquery.fancybox' ), array( 'eac-frontend' ), '3.5.7' );

		/**
		 * Charge le fichier de style du header - footer si au moins une des widgets est active
		 * Les styles du mega-menu sont gérés dans manager.php
		 * @since 2.3.4
		 * @since 2.4.6 le CSS du widget count-down est déplacé dans le CSS header-footer
		 */
		foreach ( Eac_Load_Config::get_widgets_ehf_active() as $element => $active ) {
			if ( Eac_Load_Config::is_feature_active( $element ) || Eac_Load_Config::is_widget_active( 'count-down' ) ) {
				wp_enqueue_style( 'eac-header-footer', EAC_Plugin::instance()->get_style_url( 'includes/templates-lib/assets/css/eac-header-footer' ), array( 'eac-frontend' ), EAC_PLUGIN_VERSION );
				break;
			}
		}

		/**
		 * Enqueue impérativement dans le header les styles du mega-menu
		 * Elementor ne le fait pas
		 *
		 * @since 2.3.4
		 */
		if ( Eac_Load_Config::is_widget_active( 'mega-menu' ) ) {
			wp_enqueue_style( 'eac-mega-menu', EAC_Plugin::instance()->get_style_url( 'includes/templates-lib/assets/css/mega-menu' ), array( 'eac-frontend' ), EAC_PLUGIN_VERSION );
		}
	}

	/**
	 * register_scripts
	 * Enregistre tous les scripts
	 *
	 * @return void
	 */
	public function register_scripts(): void {
		wp_register_script( 'swiper-bundle', EAC_PLUGIN_URL . 'assets/js/swiper/swiper-bundle.min.js', array( 'jquery' ), '9.4.1', true );
		wp_register_script( 'imagesloaded', includes_url( '/js/imagesloaded.min.js' ), array(), '5.0.0', true );
		wp_register_script( 'isotope', EAC_Plugin::instance()->get_script_url( 'assets/js/isotope/isotope.pkgd' ), array( 'jquery' ), '3.0.6', true );
		wp_register_script( 'fit-rows', EAC_Plugin::instance()->get_script_url( 'assets/js/isotope/fit-rows' ), array( 'jquery' ), '1.0.0', true );
		wp_register_script( 'infinite-scroll', EAC_PLUGIN_URL . 'assets/js/isotope/infinite-scroll.pkgd.min.js', array( 'jquery' ), '4.0.1', true );
		wp_register_script( 'fj-gallery', EAC_PLUGIN_URL . 'assets/js/elementor/eac-image-fjgallery.min.js', array( 'jquery' ), '2.2.0', true );
		wp_register_script( 'chart-src', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js', array(), '2.9.3', true );
		wp_register_script( 'chart-color', 'https://cdnjs.cloudflare.com/ajax/libs/randomcolor/0.6.1/randomColor.min.js', array(), '0.6.1', true );
		wp_register_script( 'chart-label', 'https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/0.7.0/chartjs-plugin-datalabels.min.js', array(), '0.7.0', true );
		wp_register_script( 'chart-style', EAC_PLUGIN_URL . 'assets/js/chart/chartjs-plugin-style.min.js', array(), '0.5.0', true );
		wp_register_script( 'lottie-animation', 'https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.8.1/lottie.min.js', array(), '5.8.1', true );
		wp_register_script( 'osm-leaflet', EAC_PLUGIN_URL . 'assets/js/osm/leaflet.min.js', array(), '1.9.4', true );
		wp_register_script( 'osm-marker-cluster', EAC_PLUGIN_URL . 'assets/js/osm/eac-osm-markercluster.min.js', array(), '1.5.3', true );
		wp_register_script( 'osm-fullscreen', EAC_Plugin::instance()->get_script_url( 'assets/js/osm/eac-osm-fullscreen' ), array(), '2.1.0', true );

		wp_register_script( 'eac-acf-relation', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/acf-relationship' ), array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-image-gallery', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-image-gallery' ), array( 'jquery', 'imagesloaded', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-advanced-gallery', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-advanced-gallery' ), array( 'jquery', 'imagesloaded', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-post-grid', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-post-grid' ), array( 'jquery', 'imagesloaded', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-chart', EAC_Plugin::instance()->get_script_url( 'assets/js/chart/eac-chart' ), array( 'jquery', 'chart-src', 'chart-color', 'chart-label', 'chart-style', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'images-comparison', EAC_Plugin::instance()->get_script_url( 'assets/js/comparison/images-comparison' ), array( 'jquery' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-images-comparison', EAC_Plugin::instance()->get_script_url( 'assets/js/comparison/eac-images-comparison' ), array( 'jquery', 'imagesloaded', 'images-comparison', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-lottie-anim', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-lottie-animations' ), array( 'jquery', 'lottie-animation', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-leaflet', EAC_Plugin::instance()->get_script_url( 'assets/js/osm/eac-osm-leaflet' ), array( 'jquery', 'osm-leaflet', 'osm-marker-cluster', 'osm-fullscreen', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-pdf-viewer', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-pdf-viewer' ), array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-pinterest-rss', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-pinterest-rss' ), array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-rss-reader', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-rss-reader' ), array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-social-share', EAC_Plugin::instance()->get_script_url( 'assets/js/socialshare/floating-social-share' ), array( 'jquery' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-share-post', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-share-post' ), array( 'jquery', 'eac-social-share', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-player', EAC_Plugin::instance()->get_script_url( 'assets/js/audioplayer/player' ), array( 'jquery' ), EAC_PLUGIN_VERSION, true );
		wp_register_script( 'eac-acf-repeater', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/acf-repeater' ), array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, true );

		EAC_Plugin::instance()->register_script( 'eac-count-down', 'assets/js/elementor/eac-count-down', array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, $this->args );
		EAC_Plugin::instance()->register_script( 'eac-html-sitemap', 'assets/js/elementor/eac-html-sitemap', array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, $this->args );
		EAC_Plugin::instance()->register_script( 'eac-modalbox', 'assets/js/elementor/eac-modal-box', array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, $this->args );
		EAC_Plugin::instance()->register_script( 'eac-news-ticker', 'assets/js/elementor/eac-news-ticker', array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, $this->args );
		EAC_Plugin::instance()->register_script( 'eac-off-canvas', 'assets/js/elementor/eac-off-canvas', array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, $this->args );
		EAC_Plugin::instance()->register_script( 'eac-jquery-toc', 'assets/js/toc/jquery.toc', array( 'jquery' ), EAC_PLUGIN_VERSION, $this->args );
		EAC_Plugin::instance()->register_script( 'eac-table-content', 'assets/js/toc/eac-table-content', array( 'jquery', 'eac-jquery-toc', 'elementor-frontend' ), EAC_PLUGIN_VERSION, $this->args );
		EAC_Plugin::instance()->register_script( 'eac-webradio-player', 'assets/js/audioplayer/eac-webradio-player', array( 'jquery', 'eac-player', 'elementor-frontend' ), EAC_PLUGIN_VERSION, $this->args );
		EAC_Plugin::instance()->register_script( 'eac-reader-progress', 'includes/templates-lib/assets/js/reader-progress', array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, $this->args );

		// La widget de recherche
		if ( Eac_Load_Config::is_widget_active( 'site-search' ) ) {
			if ( ! wp_script_is( 'jquery-ui-autocomplete', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui-autocomplete' );
			}

			EAC_Plugin::instance()->register_script( 'eac-site-search', 'includes/templates-lib/assets/js/site-search', array( 'jquery', 'jquery-ui-autocomplete', 'elementor-frontend' ), EAC_PLUGIN_VERSION, $this->args );
			wp_add_inline_script(
				'eac-site-search',
				'var eacAutocompleteSearch = ' . wp_json_encode(
					array(
						'ajax_url'    => esc_url( admin_url( 'admin-ajax.php' ) ),
						'ajax_nonce'  => wp_create_nonce( 'autocomplete_search_nonce' ),
						'ajax_action' => 'autocomplete_search',
					)
				),
				'before'
			);
		}

		// Le menu
		if ( Eac_Load_Config::is_widget_active( 'mega-menu' ) ) {
			EAC_Plugin::instance()->register_script( 'eac-mega-menu', 'includes/templates-lib/assets/js/mega-menu', array( 'jquery', 'elementor-frontend' ), EAC_PLUGIN_VERSION, $this->args );

			if ( class_exists( 'woocommerce', false ) ) {
				wp_add_inline_script(
					'eac-mega-menu',
					'var eacUpdateCounter = ' . wp_json_encode(
						array(
							'ajax_url'    => esc_url( admin_url( 'admin-ajax.php' ) ),
							'ajax_nonce'  => wp_create_nonce( 'eac_update_minicart_counter' ),
							'ajax_action' => 'update_mini_cart_counter',
						)
					),
					'before'
				);
			}
		}
	}

	/**
	 * register_styles
	 * Enregistre tous les styles
	 *
	 * @return void
	 */
	public function register_styles(): void {
		wp_register_style( 'swiper-bundle', EAC_PLUGIN_URL . 'assets/css/swiper-bundle.min.css', array(), '9.4.1' );
		wp_register_style( 'fj-gallery', EAC_PLUGIN_URL . 'assets/css/image-fjgallery.min.css', array(), '2.2.0' );
		wp_register_style( 'osm-marker-cluster', EAC_PLUGIN_URL . 'assets/css/markercluster.min.css', array(), '1.5.3' );
		wp_register_style( 'osm-marker-cluster-default', EAC_PLUGIN_URL . 'assets/css/markercluster.default.min.css', array(), '1.5.3' );

		wp_register_style( 'eac-swiper', EAC_Plugin::instance()->get_style_url( 'assets/css/eac-swiper' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-acf-relation', EAC_Plugin::instance()->get_style_url( 'assets/css/acf-relationship' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-image-gallery', EAC_Plugin::instance()->get_style_url( 'assets/css/image-gallery' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-advanced-gallery', EAC_Plugin::instance()->get_style_url( 'assets/css/advanced-gallery' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-post-grid', EAC_Plugin::instance()->get_style_url( 'assets/css/post-grid' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-product-grid', EAC_Plugin::instance()->get_style_url( 'assets/css/product-grid' ), array( 'eac-post-grid' ), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-author-infobox', EAC_Plugin::instance()->get_style_url( 'assets/css/author-infobox' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-chart', EAC_Plugin::instance()->get_style_url( 'assets/css/eac-chart' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-count-down', EAC_Plugin::instance()->get_style_url( 'assets/css/count-down' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-html-sitemap', EAC_Plugin::instance()->get_style_url( 'assets/css/html-sitemap' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-image-hotspots', EAC_Plugin::instance()->get_style_url( 'assets/css/image-hotspots' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-images-comparison', EAC_Plugin::instance()->get_style_url( 'assets/css/images-comparison' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-lottie-anim', EAC_Plugin::instance()->get_style_url( 'assets/css/lottie-animations' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-modalbox', EAC_Plugin::instance()->get_style_url( 'assets/css/modal-box' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-news-ticker', EAC_Plugin::instance()->get_style_url( 'assets/css/news-ticker' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-off-canvas', EAC_Plugin::instance()->get_style_url( 'assets/css/off-canvas' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-leaflet', EAC_Plugin::instance()->get_style_url( 'assets/css/eac-osm-leaflet' ), array( 'osm-marker-cluster', 'osm-marker-cluster-default' ), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-pdf-viewer', EAC_Plugin::instance()->get_style_url( 'assets/css/pdf-viewer' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-pinterest-rss', EAC_Plugin::instance()->get_style_url( 'assets/css/pinterest-rss' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-image-ribbon', EAC_Plugin::instance()->get_style_url( 'assets/css/image-ribbon' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-product-promotion', EAC_Plugin::instance()->get_style_url( 'assets/css/product-promotion' ), array( 'eac-image-ribbon' ), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-rss-reader', EAC_Plugin::instance()->get_style_url( 'assets/css/rss-reader' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-share-post', EAC_Plugin::instance()->get_style_url( 'assets/css/share-post' ), array(), '0.0.9' );
		wp_register_style( 'eac-site-thumbnail', EAC_Plugin::instance()->get_style_url( 'assets/css/site-thumbnail' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-syntax-highlight', EAC_Plugin::instance()->get_style_url( 'assets/css/prism' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-table-content', EAC_Plugin::instance()->get_style_url( 'assets/css/table-content' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-team-members', EAC_Plugin::instance()->get_style_url( 'assets/css/team-members' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-webradio-player', EAC_Plugin::instance()->get_style_url( 'assets/css/webradio-player' ), array(), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-acf-repeater', EAC_Plugin::instance()->get_style_url( 'assets/css/acf-repeater' ), array(), EAC_PLUGIN_VERSION );
	}

	/**
	 * add_allowed_attribute_element
	 *
	 * @param  array $allowed_tags Toutes les balises et leurs attributs
	 * @param  string $context Le type de post type
	 *
	 * @return array La liste des balises amendée
	 */
	public function add_allowed_attribute_element( array $allowed_tags, string $context ): array {
		if ( 'post' !== $context ) {
			return $allowed_tags;
		}

		if ( isset( $allowed_tags['div'] ) ) {
			$allowed_tags['div']['tabindex'] = true;
		}

		if ( isset( $allowed_tags['ul'] ) ) {
			$allowed_tags['ul']['aria-orientation'] = true;
			$allowed_tags['ul']['aria-haspopup']    = true;
		}

		if ( isset( $allowed_tags['a'] ) ) {
			$allowed_tags['a']['aria-haspopup'] = true;
			$allowed_tags['a']['aria-expanded'] = true;
		}

		if ( isset( $allowed_tags['button'] ) ) {
			$allowed_tags['button']['tabindex'] = true;
		}

		if ( isset( $allowed_tags['img'] ) ) {
			$allowed_tags['img']['srcset'] = true;
			$allowed_tags['img']['sizes']  = true;
		}

		if ( isset( $allowed_tags['nav'] ) ) {
			$allowed_tags['nav']['itemtype']  = true;
			$allowed_tags['nav']['itemscope'] = true;
		}

		$allowed_tags['svg'] = array(
			'class'           => true,
			'aria-hidden'     => true,
			'aria-labelledby' => true,
			'role'            => true,
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
		);

		$allowed_tags['path'] = array(
			'd'    => true,
			'fill' => true,
		);

		$allowed_tags['g'] = array(
			'fill' => true,
		);

		/** display-conditions/controller.php/should_render */
		if ( ! isset( $allowed_tags['br'] ) ) {
			$allowed_tags['br'] = true;
		}

		return $allowed_tags;
	}

	/**
	 * add_columns
	 *
	 * Ajout de la colonne 'Shortcode' dans la vue Elementor Templates
	 * Ajout des colonnes 'Show on/ACF groups' dans les vues Header/Footer
	 *
	 * @param  array $columns La liste des colonnes de la vue
	 * @return array La liste des colonnes amendées
	 */
	public function add_columns( array $columns ): array {
		$hft_column = array();

		if ( class_exists( Site_Header::class, false ) && ! empty( get_query_var( Source_Local::TAXONOMY_TYPE_SLUG ) ) && ( Site_Header::TYPE === get_query_var( Source_Local::TAXONOMY_TYPE_SLUG ) || Site_Footer::TYPE === get_query_var( Source_Local::TAXONOMY_TYPE_SLUG ) ) ) {
			if ( ! isset( $columns['eac_ehf_show_on'] ) ) {
				$hft_column['eac_ehf_show_on'] = esc_html__( 'Show on', 'eac-components' );
			}

			/**if ( function_exists( 'acf_get_field_groups' ) ) {
				$hft_column['eac_ehf_acf']  = esc_html__( 'ACF groupes', 'eac-components' );
			}*/
		} elseif ( ! empty( get_query_var( Source_Local::TAXONOMY_TYPE_SLUG ) ) ) { // Pour sauter l'onglet 'Saved templates'
			if ( ! isset( $columns['eac_shortcode'] ) ) {
				$hft_column['eac_shortcode'] = esc_html__( 'Shortcode', 'eac-components' );
			}
		}

		return array_merge( $columns, $hft_column );
	}

	/**
	 * data_columns
	 *
	 * Affiche la valeur des colonnes dans la vue Elementor Templates
	 *
	 * @param  string $column_name
	 * @param  mixed $post_id
	 *
	 * @return void
	 */
	public function data_columns( $column_name, $post_id ): void {
		?><style type="text/css">
			th#eac_ehf_acf { width: 13%; }
			th#elementor_library_type { width: 10%; }
		</style>
		<?php

		if ( 'eac_shortcode' === $column_name && 'publish' === get_post_status( $post_id ) ) {
			echo '<input type="text" class="widefat" onfocus="this.select()" value=\'[eac_elementor_tmpl id="' . esc_attr( $post_id ) . '"]\' readonly>';
		} elseif ( 'eac_ehf_acf' === $column_name ) {
			$have_acf = array();
			$groups   = acf_get_field_groups( array( 'post_id' => $post_id ) );

			foreach ( $groups as $group ) {
				if ( $group['active'] ) {
					$local_groups = acf_have_local_field_groups() ? acf_count_local_field_groups() : 0;
					$local        = acf_have_local_field_groups() && acf_is_local_field_group( $group['key'] ) ? true : false;
					if ( $local ) {
						$have_acf[] = 'Local::' . $group['title'];
					} else {
						$have_acf[] = $group['title'];
					}
				}
			}
			if ( empty( $have_acf ) ) {
				$have_acf[] = 'No';
			}

			echo esc_html( implode( ', ', $have_acf ) );
		} elseif ( 'eac_ehf_show_on' === $column_name ) {
			$meta = get_post_meta( $post_id, '_elementor_page_settings', true );

			if ( isset( $meta['show_on'] ) ) {
				if ( 'singular' === $meta['show_on'] ) {
					if ( ! empty( $meta['singular_pages'] ) ) {
						echo esc_html( implode( ', ', array_map( 'ucfirst', $meta['singular_pages'] ) ) );
					} else {
						esc_html_e( 'singular', 'eac-components' );
					}
				} elseif ( 'archive' === $meta['show_on'] ) {
					if ( ! empty( $meta['archive_pages'] ) ) {
						echo esc_html( implode( ', ', array_map( 'ucfirst', $meta['archive_pages'] ) ) );
					} else {
						esc_html_e( 'Archives', 'eac-components' );
					}
				} elseif ( 'custom' === $meta['show_on'] ) {
					if ( ! empty( $meta['singular_pages'] ) ) {
						echo esc_html( implode( ', ', array_map( 'ucfirst', $meta['singular_pages'] ) ) );
					}
					if ( ! empty( $meta['archive_pages'] ) ) {
						echo esc_html( ', ' . implode( ', ', array_map( 'ucfirst', $meta['archive_pages'] ) ) );
					}
				} elseif ( 'global' === $meta['show_on'] ) {
					echo esc_html( 'Global' );
				} elseif ( 'blog' === $meta['show_on'] || 'index' === $meta['show_on'] ) {
					echo esc_html( 'Blog' );
				} elseif ( 'front' === $meta['show_on'] ) {
					esc_html_e( 'Front page', 'eac-components' );
				} elseif ( 'search' === $meta['show_on'] ) {
					esc_html_e( 'Search result page', 'eac-components' );
				} elseif ( 'wc_shop' === $meta['show_on'] ) {
					esc_html_e( 'WooCommerce shop page', 'eac-components' );
				} elseif ( 'err404' === $meta['show_on'] ) {
					esc_html_e( 'Error 404 page', 'eac-components' );
				} elseif ( 'privacy' === $meta['show_on'] ) {
					esc_html_e( 'Privacy policy page', 'eac-components' );
				} else {
					echo esc_html( '---' );
				}
			} else {
				echo esc_html( '---' );
			}
		}
	}

	/**
	 * display_elementor_template
	 * Shortcode d'intégration d'un modèle Elementor Ex: [eac_elementor_tmpl id="XXXXX"]
	 *
	 * @param array $params
	 * @return string
	 */
	public function display_elementor_template( $params = array() ): string {
		$args = shortcode_atts(
			array(
				'id'  => '',
				'css' => 'false',
			),
			$params,
			'eac_elementor_tmpl'
		);

		$id_tmpl  = absint( sanitize_text_field( trim( $args['id'] ) ) );
		if ( empty( $id_tmpl ) ) {
			return '';
		}
		$css_tmpl = 'false' === sanitize_text_field( trim( $args['css'] ) ) ? false : true;
		$post_tmpl = get_posts(
			array(
				'post_type' => get_post_type( $id_tmpl ),
				'post__in' => array( $id_tmpl ),
			)
		);

		if ( is_wp_error( $post_tmpl ) || empty( $post_tmpl ) ) {
			return '';
		}

		// Évite la récursivité
		if ( get_the_ID() === $id_tmpl ) {
			return esc_html__( 'The Template ID cannot be the same as the currently edited template', 'eac-components' );
		}

		$id_tmpl = apply_filters( 'wpml_object_id', $id_tmpl, \Elementor\TemplateLibrary\Source_Local::CPT, true );

		return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id_tmpl );
	}

	/**
	 * embed_author_infobox
	 * Ajoute le contenu du template Author infobox au contenu d'un post_type/posts
	 *
	 * @param string $content Le contenu du post_type/post
	 * @return string
	 */
	public function embed_author_infobox( string $content ): string {
		$options           = get_option( 'eac_options_infobox' );
		$current_id        = get_the_ID();
		$current_post_type = get_post_type( $current_id );

		if ( ! \Elementor\Plugin::$instance->preview->is_preview_mode( $current_id ) && ! is_preview() ) {
			remove_filter( 'the_content', array( $this, 'embed_author_infobox' ), 99 );
		}

		// Boucle principale, pas la page d'accueil et l'option existe
		if ( ! ( in_the_loop() && is_main_query() ) || is_front_page() || false === $options ) {
			return $content;
		}

		/**
		 * Les options de l'infobox
		 *
		 * @since 2.1.0 Sanitize les options
		 */
		$template_id         = absint( $options['post_id'] );     // ID du modèle Elementor
		$template_post_types = esc_html( $options['post_type'] ); // Le post_type qui peut afficher le contenu du template
		$template_position   = esc_html( $options['position'] );  // La position du contenu du template
		$template_post_ids   = array_map( 'absint', $options['post_ids'] ); // La liste des IDs qui peuvent afficher le contenu du template

		// ID de l'article courant n'est pas dans la liste des articles qui peuvent afficher le template
		if ( is_array( $template_post_ids ) && ! empty( $template_post_ids ) && ! in_array( $current_id, $template_post_ids, true ) ) {
			return $content;
		}

		/**
		$categories = get_the_category($current_id);
		$category_list = wp_list_pluck($categories, 'name');
		console_log($category_list);
		*/

		// Le template Elementor est publié ou le post_type de l'article courant n'est pas le post_type attendu et évite la récursivité
		$template = get_post( $template_id );
		if ( null === $template || 'publish' !== $template->post_status || $current_post_type !== $template_post_types || $current_id === $template_id ) {
			return $content;
		}

		// Filtre wpml
		$template_id = apply_filters( 'wpml_object_id', $template_id, \Elementor\TemplateLibrary\Source_Local::CPT, true );

		// Ajoute le contenu du template selon sa position
		if ( 'before' === $template_position ) {
			return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id ) . $content;
		} else {
			return $content . \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id );
		}
	}
}
