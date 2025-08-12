<?php
/**
 * Class: Eac_Plugin_Updater
 *
 * Description: Application des filtres nécessaires pour la mise à jour du plugin
 * Le fichier "info.xml" est chargé à partir du serveur de prod qui maintient toutes les clés/valeurs
 * nécessaires pour renseigner l'API plugin de WordPress à travers ses filtres
 *
 * Inspired by: https://github.com/rudrastyh/misha-update-checker/blob/main/misha-update-checker.php
 *
 * @since 1.6.5
 */

namespace EACCustomWidgets\Core\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Plugin_Updater {

	private $plugin_slug;
	private $plugin_name;
	private $plugin_site;
	private $cache_key;
	private $old_plugin_update;
	private $old_plugin_upgrade;
	private $info_xml;
	private $author;
	private $timeout;

	public function __construct() {
		$this->plugin_slug        = EAC_PLUGIN_SLUG;
		$this->plugin_name        = 'elementor-addon-components/elementor-addon-components.php';
		$this->plugin_site        = EAC_PLUGIN_SITE;
		$this->cache_key          = 'eac_options_update';
		$this->old_plugin_update  = 'eac_update_elementor-addon-components';
		$this->old_plugin_upgrade = 'eac_upgrade_elementor-addon-components';
		$this->info_xml           = EAC_PLUGIN_SITE . '/wp-content/uploads/elementor-addon-components/info.xml';
		$this->author             = '<a href="' . EAC_PLUGIN_SITE . '">EAC Team</a>';
		$this->timeout            = 30;

		add_filter( 'plugins_api', array( $this, 'get_plugin_information' ), 9999, 3 );
		add_filter( 'update_plugins_elementor-addon-components.com', array( $this, 'update_plugin' ), 9999, 3 );
		add_filter( 'plugin_auto_update_setting_html', array( $this, 'auto_update_setting_html' ), 14, 3 );
		add_action( 'upgrader_process_complete', array( $this, 'purge_transient' ), 10, 2 );
	}

	/**
	 * auto_update_setting_html
	 *
	 * Modifie le message de mise à jour automatique du plugin
	 *
	 * @return Le message mis à jour
	 */
	public function auto_update_setting_html( $html, $plugin_file, $plugin_data ) { // phpcs:ignore

		if ( $this->plugin_name === $plugin_file ) {
			$html = esc_html__( 'Les mises à jour automatiques ne sont pas disponibles pour ce plugin', 'eac-components' );
		}
		return $html;
	}

	/**
	 * load_xml_file
	 *
	 * Récupère le contenu du fichier de configuration 'XML' du transient ou du site distant
	 *
	 * @since 2.3.5 Changer le timeout
	 *
	 * @return Le corps (body) du fichier de configuration 'XML'
	 */
	public function load_xml_file() {
		$remote = get_transient( $this->cache_key );

		// Pas de transient
		if ( false === $remote ) {
			$remote = wp_remote_get(
				esc_url_raw( $this->info_xml ),
				array(
					'timeout' => $this->timeout,
					'headers' => array( 'Accept' => 'application/xml' ),
				)
			);

			// Une erreur
			if ( is_wp_error( $remote ) || 200 !== wp_remote_retrieve_response_code( $remote ) || empty( wp_remote_retrieve_body( $remote ) ) ) {
				return false;
			}

			// Crée le transient
			set_transient( $this->cache_key, $remote, 43200 ); // 12 * HOUR_IN_SECONDS || DAY_IN_SECONDS 43200 = 12 heures
		}

		// Parse le fichier XML
		$remote = SimpleXML_Load_String( wp_remote_retrieve_body( $remote ), 'SimpleXMLElement', LIBXML_NOCDATA );

		// Erreur de parsing
		if ( false === $remote ) {
			return false;
		}

		if ( wp_parse_url( (string) $remote->document->download_url )['host'] !== wp_parse_url( $this->info_xml )['host'] ) { // phpcs:ignore WordPress.PHP.YodaConditions.NotYoda
			return false;
		}

		return $remote;
	}

	/**
	 * get_plugin_information
	 *
	 * @return Les données pour afficher le détail du lien 'View details'
	 *
	 * @param $response L'objet response
	 * @param $action   L'action = 'plugin_information'
	 * @param $args     Une liste d'arguments notamment pour vérifier si c'est notre plugin
	 */
	public function get_plugin_information( $response, $action, $args ) {
		// Retourne si c'est pas une demande d'info
		if ( 'plugin_information' !== $action ) {
			return $response;
		}
		// Retourne si c'est pas notre plugin
		if ( $this->plugin_slug !== $args->slug ) {
			return $response;
		}

		// Données du fichier XML
		$remote = $this->load_xml_file();
		if ( ! $remote ) {
			return $response;
		}

		// Les champs d'informations du lien 'view details'
		$obj = new \stdClass();
		$obj->name            = esc_html( (string) $remote->document->name );
		$obj->homepage        = esc_url( (string) $remote->document->homepage );
		$obj->slug            = $this->plugin_slug;
		$obj->version         = esc_html( (string) $remote->document->new_version );
		$obj->tested          = esc_html( (string) $remote->document->tested );
		$obj->requires        = esc_html( (string) $remote->document->requires );
		$obj->author          = esc_url( $this->author );
		$obj->author_profile  = esc_url( $this->author );
		$obj->download_link   = esc_url( (string) $remote->document->download_url );
		$obj->requires_php    = esc_html( (string) $remote->document->requires_php );
		$obj->last_updated    = esc_html( (string) $remote->document->last_updated );
		$obj->added           = esc_html( (string) $remote->document->added );
		$obj->active_installs = esc_html( (int) $remote->document->active_installs );
		$obj->sections        = array(
			'description'  => wp_kses_post( (string) $remote->document->sections->description ),
			'installation' => wp_kses_post( (string) $remote->document->sections->installation ),
			'changelog'    => wp_kses_post( (string) $remote->document->sections->changelog ),
			'screenshots'  => wp_kses_post( (string) $remote->document->sections->screenshots ),
		);
		$obj->banners         = array( 'high' => esc_url( (string) $remote->document->banners->high ) );

		return $obj;
	}

	/**
	 * update_plugin
	 *
	 * @param mixed  $update The plugin update data with the latest details. Default false
	 * @param array  $plugin_data Plugin headers
	 * @param string $plugin_file Plugin filename
	 *
	 * @return Array|false
	 */
	public function update_plugin( $update, array $plugin_data, string $plugin_file ) {
		/**error_log( print_r( $plugin_data, true ) );*/
		if ( $plugin_file !== $this->plugin_name ) {
			return $update;
		}
		if ( ! empty( $update ) ) {
			return $update;
		}

		// Données du fichier XML
		$remote = $this->load_xml_file();
		if ( ! $remote ) {
			return $update;
		}

		/** La version actuelle du plugin est différente */
		if ( version_compare( $plugin_data['Version'], (string) $remote->document->new_version, '<' ) ) {
			return array(
				'slug'    => $this->plugin_slug,
				'version' => esc_html( (string) $remote->document->new_version ),
				'url'     => esc_url( $this->plugin_site ),
				'package' => esc_url( (string) $remote->document->download_url ),
			);
		}
		return $update;
	}

	/**
	 * purge_transient
	 *
	 * Supprime le transient après mise à jour du plugin
	 * This function runs when WordPress completes its upgrade process
	 * It iterates through each plugin updated to see if ours is included
	 *
	 * TODO tester $options['item'] === $this->plugin_name pour éviter la bouclee
	 *
	 * @param $upgrader Array
	 * @param $options Array
	 */
	public function purge_transient( $upgrader, $options ) {
		// Quelques tests avant de supprimer le transient
		if ( 'update' === $options['action'] && 'plugin' === $options['type'] && isset( $options['plugins'] ) ) {
			foreach ( $options['plugins'] as $plugin ) {
				if ( $plugin === $this->plugin_name ) {
					// just clean the cache when new plugin version is installed
					delete_transient( $this->cache_key );
					// Supprime les anciens transients
					delete_transient( $this->old_plugin_update );
					delete_transient( $this->old_plugin_upgrade );
				}
			}
		}
	}
}
