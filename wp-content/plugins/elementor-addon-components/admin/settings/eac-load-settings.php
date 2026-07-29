<?php
/**
 * Class: EAC_Load_Settings
 *
 * Description: Gère l'interface d'administration des composantrs EAC 'EAC Components'
 * et des options de la BDD.
 * Cette class est instanciée dans 'plugin.php' par le rôle administrateur.
 *
 * Charge le css 'eac-admin' et le script 'eac-admin' d'administration des composants.
 * Ajoute l'item 'EAC Components' dans le menu de la barre latérale
 * Charge le formulaire HTML de la page d'admin.
 *
 * @since 1.0.0
 */

namespace EACCustomWidgets\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Load_Config;

class EAC_Load_Settings {

	/**
	 * $options_elements
	 *
	 * @var string
	 */
	private $options_elements = '';

	/**
	 * $wc_integration_nonce
	 * nonce pour le formulaire d'intégration WC
	 *
	 * @var string
	 */
	private $wc_integration_nonce = 'eac_settings_wc_integration_nonce';

	/**
	 * $elements_nonce
	 * nonce pour le formulaire global
	 *
	 * @var string
	 */
	private $elements_nonce = 'eac_settings_elements_nonce';

	/**
	 * $elements_count_nonce
	 *
	 * @var string
	 */
	private $elements_count_nonce = 'elements_count_nonce';

	/**
	 * $elements_keys
	 * La liste de tous les éléments par leur slug
	 *
	 * @var array
	 */
	private $elements_keys = array();

	/** L'instance de la class */
	private static $instance = null;

	/** __construct */
	private function __construct() {
		/** Le gestionnaire OPcache  */
		new \EACCustomWidgets\Admin\Settings\Eac_Opcache_Manager();

		/** Le libellé des options de la BDD */
		$this->options_elements = Eac_Load_Config::get_elements_option_name();

		/** Affecte le tableau des éléments */
		$this->elements_keys = Eac_Load_Config::get_elements_active();

		/** Enregistre les actions de création du sous-menu et de sauvegarde des formulaires */
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_page_scripts' ) );
		add_action( 'wp_ajax_save_elements', array( $this, 'save_elements' ) );
		add_action( 'wp_ajax_save_wc_integration', array( $this, 'save_wc_integration' ) );
	}

	/** Singleton de la class */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * add_admin_menu
	 * Création du nouveau menu dans le dashboard
	 * Le menu est visible pour les utilisateurs qui ont la capacité 'manage_options' ou la capacité définie dans l'option 'eac_components_menu_capability'
	 *
	 * @return void
	 */
	public function add_admin_menu(): void {
		$plugin_name = esc_html__( 'EAC components', 'eac-components' );

		add_menu_page( $plugin_name, $plugin_name, 'manage_options', EAC_DOMAIN_NAME, array( $this, 'admin_page' ), EAC_Plugin::instance()->get_dashboard_icon_url(), '58.2' );
	}

	/**
	 * admin_page_scripts
	 * Charge le css 'eac-admin' et le script 'eac-admin' d'administration des composants
	 *
	 * @return void
	 */
	public function admin_page_scripts(): void {
		/** Le CSS/JS de la page de configuration du plugin */
		wp_register_script( 'eac-admin', EAC_Plugin::instance()->get_script_url( 'admin/js/eac-admin' ), array( 'jquery', 'jquery-ui-dialog' ), EAC_PLUGIN_VERSION, true );
		wp_enqueue_style( 'eac-admin', EAC_Plugin::instance()->get_style_url( 'admin/css/eac-admin' ), array( 'wp-jquery-ui-dialog' ), EAC_PLUGIN_VERSION );

		// Le CSS/JS global
		wp_enqueue_script( 'eac-frontend', EAC_Plugin::instance()->get_script_url( 'assets/js/eac-frontend' ), array( 'jquery' ), EAC_PLUGIN_VERSION, true );
		wp_enqueue_style( 'eac-frontend', EAC_Plugin::instance()->get_style_url( 'assets/css/eac-frontend' ), array(), EAC_PLUGIN_VERSION );

		// Le CSS/JS de la Fancybox
		wp_enqueue_script( 'eac-fancybox', EAC_Plugin::instance()->get_script_url( 'assets/js/fancybox/jquery.fancybox' ), array( 'jquery' ), '3.5.7', true );
		wp_enqueue_style( 'eac-fancybox', EAC_Plugin::instance()->get_style_url( 'assets/css/jquery.fancybox' ), array( 'eac-frontend' ), '3.5.7' );
	}

	/**
	 * admin_page
	 * * Passe les paramètres au script 'eac-admin => eac-admin.js'
	 * Charge les templates de la page d'administration
	 * Les templates sont chargés dans l'ordre d'affichage de la page d'administration
	 * Les templates sont dans le dossier 'templates' de ce même dossier 'settings'
	 * Les templates sont des fichiers PHP qui contiennent du HTML et du PHP pour afficher les différentes sections de la page d'administration
	 *
	 * @return void
	 */
	public function admin_page(): void {
		/** Options intégration WC */
		if ( Eac_Load_Config::is_widget_active( 'woo-product-grid' ) ) {
			$settings_wc_integration = array(
				'ajax_url'    => esc_url( admin_url( 'admin-ajax.php' ) ),
				'ajax_action' => 'save_wc_integration',
				'ajax_nonce'  => wp_create_nonce( $this->wc_integration_nonce ),
			);
			wp_add_inline_script( 'eac-admin', 'var wcintegration = ' . wp_json_encode( $settings_wc_integration ), 'before' );
		}

		/** Options éléments */
		$settings_elements = array(
			'ajax_url'    => esc_url( admin_url( 'admin-ajax.php' ) ),
			'ajax_action' => 'save_elements',
			'ajax_nonce'  => wp_create_nonce( $this->elements_nonce ),
		);
		wp_add_inline_script( 'eac-admin', 'var settingsElements = ' . wp_json_encode( $settings_elements ), 'before' );

		/** Compte du nombre d'éléments dans la page de paramétrage */
		$elements_count = array(
			'ajax_url'     => esc_url( admin_url( 'admin-ajax.php' ) ),
			'ajax_content' => esc_url( EAC_PLUGIN_URL . 'admin/settings/templates/eac-admin-popup-elements-count.php' ) . '?ajax_nonce=' . wp_create_nonce( $this->elements_count_nonce ),
		);
		wp_add_inline_script( 'eac-admin', 'var elementsCount = ' . wp_json_encode( $elements_count ), 'before' );

		/** charge le script 'eac-admin' */
		wp_enqueue_script( 'eac-admin' );

		/** Charge les templates */
		require_once 'templates/eac-components-header.php';
		require_once 'templates/eac-components-tabs-nav.php';
		?>
		<div class='tabs-stage'>
			<?php
			require_once 'templates/eac-components-tab1.php';
			if ( Eac_Load_Config::is_widget_active( 'woo-product-grid' ) ) {
				require_once 'templates/eac-components-tab6.php';
			}
			require_once 'templates/eac-components-tab7.php';
			?>
		</div>
		<?php
		require_once 'templates/eac-admin-popup-acf.php';
		require_once 'templates/eac-admin-popup-elements-help.php';
	}

	/**
	 * save_elements
	 *
	 * Action appelée depuis le script 'eac-admin'
	 * Sauvegarde les éléments et leur état dans la table Options de la BDD
	 *
	 * @return void
	 */
	public function save_elements(): void {
		// Vérification du nonce pour cette action
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), $this->elements_nonce ) ) {
			wp_send_json_error( esc_html__( 'Settings could not be saved (nonce)', 'eac-components' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You cannot change the settings', 'eac-components' ) );
		}

		// Les champs 'fields' sélectionnés 'on' sont serialisés dans 'eac-admin.js'
		if ( isset( $_POST['fields'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_POST['fields'] ) ), $fields_on );
		} else {
			wp_send_json_error( esc_html__( 'Settings could not be saved (fields)', 'eac-components' ) );
		}

		$settings_keys = array();
		$keys          = array_keys( $this->elements_keys );

		// La liste des options de tous les composants activés
		foreach ( $keys as $key ) {
			$key                   = sanitize_text_field( $key );
			$settings_keys[ $key ] = boolval( isset( $fields_on[ $key ] ) ? 1 : 0 );
		}

		// Update de la BDD
		update_option( $this->options_elements, $settings_keys, false );

		// Met à jour les options pour le template template 'tab1'
		$this->elements_keys = get_option( $this->options_elements );

		// Supprime l'option de l'usage des éléménts
		delete_option( Eac_Load_Config::get_usage_count_option_name() );

		// retourne 'success' au script JS
		wp_send_json_success( esc_html__( 'Settings saved', 'eac-components' ) );
	}

	/**
	 * save_wc_integration
	 *
	 * Action appelée depuis le script 'eac-admin'
	 * Sauvegarde les options de l'intégration WC dans la table Options de la BDD
	 *
	 * @return void
	 */
	public function save_wc_integration(): void {
		$woo_shop_args = array(
			'product-page'   => array(
				'shop'             => array(
					'url' => '',
					'id'  => 0,
				),
				'redirect_buttons' => false,
				'breadcrumb'       => false,
				'metas'            => false,
			),
			'catalog'        => array(
				'active'        => false,
				'request_quote' => false,
			),
			'redirect_pages' => false,
			'mini_cart'      => false,
		);

		/** Vérification du nonce pour cette action */
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), $this->wc_integration_nonce ) ) {
			wp_send_json_error( esc_html__( 'Settings could not be saved (nonce)', 'eac-components' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You cannot change the settings', 'eac-components' ) );
		}

		/** Les champs 'fields' sont serialisés dans 'eac-admin.js' */
		if ( isset( $_POST['fields'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_POST['fields'] ) ), $fields_on );
		} else {
			wp_send_json_error( esc_html__( 'Settings could not be saved (fields)', 'eac-components' ) );
		}

		/** WooCommerce n'est pas installé */
		if ( ! class_exists( 'WooCommerce' ) ) {
			wp_send_json_error( esc_html__( 'WooCommerce is not installed/enabled on your site', 'eac-components' ) );
		}

		/** ID et URL de la page grille de produit */
		if ( isset( $fields_on['wc_product_select_page'] ) && ! empty( $fields_on['wc_product_select_page'] ) ) {
			$woo_shop_args['product-page']['shop']['url'] = esc_url_raw( get_permalink( absint( $fields_on['wc_product_select_page'] ) ) );
			$woo_shop_args['product-page']['shop']['id']  = absint( $fields_on['wc_product_select_page'] );
		} else {
			$woo_shop_args['product-page']['shop']['url'] = '';
			$woo_shop_args['product-page']['shop']['id']  = (int) 0;
		}

		/**
		 * Les boutons de la page panier
		 * Les URLs du breadcrumb de la page product
		 * Les URLs des métas de la page produit
		 */
		if ( ! empty( $woo_shop_args['product-page']['shop']['url'] ) ) {
			$woo_shop_args['product-page']['redirect_buttons']   = boolval( isset( $fields_on['wc_product_redirect_url'] ) ? 1 : 0 );
			$woo_shop_args['product-page']['breadcrumb']         = boolval( isset( $fields_on['wc_product_breadcrumb'] ) ? 1 : 0 );
			$woo_shop_args['product-page']['metas']              = boolval( isset( $fields_on['wc_product_metas'] ) ? 1 : 0 );
		} else {
			$woo_shop_args['product-page']['redirect_buttons']   = boolval( 0 );
			$woo_shop_args['product-page']['breadcrumb']         = boolval( 0 );
			$woo_shop_args['product-page']['metas']              = boolval( 0 );
		}

		/** Le site en catalogue */
		$woo_shop_args['catalog']['active'] = boolval( isset( $fields_on['wc_product_catalog'] ) ? 1 : 0 );

		/** Message dans la page du produit 'request a quote' et redirection des pages */
		if ( $woo_shop_args['catalog']['active'] ) {
			$woo_shop_args['catalog']['request_quote'] = boolval( isset( $fields_on['wc_product_request'] ) ? 1 : 0 );
			if ( '' !== $woo_shop_args['product-page']['shop']['url'] ) {
				$woo_shop_args['redirect_pages'] = boolval( isset( $fields_on['wc_product_redirect_pages'] ) ? 1 : 0 );
			} else {
				$woo_shop_args['redirect_pages'] = boolval( 0 );
			}
		} else {
			$woo_shop_args['catalog']['request_quote'] = boolval( 0 );
			$woo_shop_args['redirect_pages']           = boolval( 0 );
		}

		/** Update de la BDD */
		update_option( Eac_Load_Config::get_woo_hooks_option_name(), $woo_shop_args, false );

		/** retourne 'success' au script JS */
		wp_send_json_success( esc_html__( 'Settings saved', 'eac-components' ) );
	}
}
