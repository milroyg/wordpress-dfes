<?php
/**
 * Plugin Name: Elementor Addon Components
 * Description: This amazing plugin includes free components, such as image, item and product grid. A header and footer builder, custom CSS, dynamic tags including ACF, element display conditions and much more.
 * Plugin URI: https://elementor-addon-components.com/
 * Update URI: https://elementor-addon-components.com
 * Author: Team EAC
 * Author URI: https://elementor-addon-components.com/
 * Version: 2.5.2
 * Requires at least: 6.5.0
 * Tested up to: 7.0.0
 * Requires PHP: 7.4
 * Elementor tested up to: 4.1.3
 * WC requires at least: 8.0.0
 * WC tested up to: 9.9.3
 * Text Domain: eac-components
 * Domain Path: /languages
 * License: GPLv3 or later License
 * URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 'Elementor Addon Components' is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GPL General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'EAC_DOMAIN_NAME', 'eac-components' );
define( 'EAC_PLUGIN_NAME', 'Elementor Addon Components' );
define( 'EAC_PLUGIN_SLUG', 'elementor-addon-components' );
define( 'EAC_PLUGIN_SITE', 'https://elementor-addon-components.com' );
define( 'EAC_PLUGIN_VERSION', '2.5.2' );
define( 'EAC_PLUGIN_FILE', __FILE__ );
define( 'EAC_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
define( 'EAC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EAC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( 'EAC_ADMIN_NAMESPACE', 'EACCustomWidgets\\Admin\\Settings\\' );
define( 'EAC_INCLUDES_NAMESPACE', 'EACCustomWidgets\\Includes\\' );
define( 'EAC_INCLUDES_URL', EAC_PLUGIN_URL . 'includes/' );
define( 'EAC_INCLUDES_PATH', EAC_PLUGIN_PATH . 'includes/' );
define( 'EAC_ACF_JSON_PATH', EAC_INCLUDES_PATH . 'acf/acf-json' );

define( 'EAC_SCRIPT_DEBUG', false ); // true = .js ou false = .min.js
define( 'EAC_STYLE_DEBUG', false );  // true = .css ou false = .min.css

function eac_plugin_activation() {
	update_option( 'eac_options_plugin_activated', 'yes', false );
}
register_activation_hook( EAC_PLUGIN_FILE, 'eac_plugin_activation' );

function eac_plugin_deactivation() {
	update_option( 'eac_options_plugin_activated', 'no', false );
}
register_deactivation_hook( EAC_PLUGIN_FILE, 'eac_plugin_deactivation' );

/** Vérifie les compatibilités et instancie le plugin */
function eac_load_plugin(): void {
	load_plugin_textdomain( EAC_DOMAIN_NAME, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Test 1: Vérifier la compatibilité WordPress/PHP
	if ( ! eac_is_wp_compatible() ) {
		// Marquer le plugin comme incompatible (stocké en transient)
		set_transient( 'eac_plugin_activation_error', true, 60 );
		deactivate_plugins( EAC_PLUGIN_BASENAME );
		return;
	}

	// Test 2: Seulement si WP/PHP sont OK, tester Elementor ou ACF
	if ( did_action( 'elementor/loaded' ) && eac_is_elementor_compatible() ) {
		add_filter( 'plugin_action_links_' . EAC_PLUGIN_BASENAME, 'eac_add_settings_link' );
		add_action( 'elementor/init', function () {
			require_once __DIR__ . '/eac-plugin.php';
		} );
	} elseif ( eac_is_acf_compatible() ) {
		require_once __DIR__ . '/eac-plugin-block.php';
	} else {
		// Marquer le plugin comme incompatible (stocké en transient)
		set_transient( 'eac_plugin_activation_error', true, 60 );
		deactivate_plugins( EAC_PLUGIN_BASENAME );
	}

	add_filter( 'plugin_row_meta', 'eac_add_help_detail_links', 10, 2 );
}
add_action( 'plugins_loaded', 'eac_load_plugin', 999 );

/**
 * eac_admin_init_plugin
 * Empêche le message "Plugin activated"
 *
 * @return void
 */
function eac_admin_init_plugin(): void {
	if ( get_transient( 'eac_plugin_activation_error' ) ) {
		delete_transient( 'eac_plugin_activation_error' );

		// Supprime le message "Plugin activated" de WordPress
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			unset( $_GET['activate'] );
		}
	}
}
add_action( 'admin_init', 'eac_admin_init_plugin' );

/**
 * eac_is_wp_compatible
 * Vérification des compatibilités pour les versions WordPress et PHP
 *
 * @return bool
 */
function eac_is_wp_compatible(): bool {
	$compatible        = true;
	$wp_version        = '6.5.0';
	$php_version       = '7.4';

	if ( version_compare( get_bloginfo( 'version' ), $wp_version, '<' ) ) {
		$compatible = false;
		add_action(
			'admin_notices',
			function () use ( $wp_version ) {
				$message = sprintf(
					/* translators: 1: WordPress version minimale */
					esc_html__( 'Elementor Addon Components minimum version WordPress', 'eac-components' ) . ' %1$s',
					$wp_version
				); ?>
				<div class='notice notice-error is-dismissible'>
					<p><?php echo esc_html( $message ); ?></p>
				</div>
			<?php } );
		/** Notification PHP n'est pas à la bonne version */
	} elseif ( version_compare( PHP_VERSION, $php_version, '<' ) ) {
		$compatible = false;
		add_action(
			'admin_notices',
			function () use ( $php_version ) {
				$message = sprintf(
					/* translators: 1: PHP version minimale */
					esc_html__( 'Elementor Addon Components minimum version PHP', 'eac-components' ) . ' %1$s',
					$php_version
				); ?>
				<div class='notice notice-error is-dismissible'>
					<p><?php echo esc_html( $message ); ?></p>
				</div>
			<?php } );
	}
	return $compatible;
}

/**
 * eac_is_elementor_compatible
 * Vérification des compatibilités pour la version Elementor
 *
 * @return bool
 */
function eac_is_elementor_compatible(): bool {
	$compatible        = true;
	$elementor_version = '3.28.0';

	if ( version_compare( ELEMENTOR_VERSION, $elementor_version, '<' ) ) {
		$compatible = false;
		add_action(
			'admin_notices',
			function () use ( $elementor_version ) {
				$message = sprintf(
					/* translators: 1: Elementor version minimale */
					esc_html__( 'Elementor Addon Components minimum version Elementor', 'eac-components' ) . ' %1$s',
					$elementor_version
				); ?>
				<div class='notice notice-error is-dismissible'>
					<p><?php echo esc_html( $message ); ?></p>
				</div>
			<?php } );
	}
	return $compatible;
}

/**
 * eac_is_acf_compatible
 * Vérification des compatibilités pour ACF (Advanced Custom Fields)
 *
 * @return bool
 */
function eac_is_acf_compatible(): bool {
	$compatible  = true;
	$acf_version = '6.0';

	if ( ! function_exists( 'acf_get_field_groups' ) ) {
		$compatible = false;
		add_action(
			'admin_notices',
			function () {
				$message = esc_html__( 'Elementor Addon Components ACF not installed', 'eac-components' );
				?>
				<div class='notice notice-error is-dismissible'>
					<p><?php echo esc_html( $message ); ?></p>
				</div>
			<?php } );
	} elseif ( function_exists( 'acf_register_block_type' ) ) {
		$compatible = false;
		add_action(
			'admin_notices',
			function () {
				$message = esc_html__( "Elementor Addon Components doesn't support ACF Pro", 'eac-components' );
				?>
				<div class='notice notice-error is-dismissible'>
					<p><?php echo esc_html( $message ); ?></p>
				</div>
			<?php } );
	} elseif ( version_compare( ACF_VERSION, $acf_version, '<' ) ) {
		$compatible = false;
		add_action(
			'admin_notices',
			function () use ( $acf_version ) {
				$message = sprintf(
					/* translators: 1: ACF version minimale */
					esc_html__( 'Elementor Addon Components minimum version ACF', 'eac-components' ) . ' %1$s',
					$acf_version
				); ?>
				<div class='notice notice-error is-dismissible'>
					<p><?php echo esc_html( $message ); ?></p>
				</div>
			<?php } );
	}
	return $compatible;
}

/** Ajout du lien vers la page de réglages du plugin */
function eac_add_settings_link( array $links ): array {
	$setting_link = array( '<a href="' . esc_url( admin_url( 'admin.php?page=eac-components' ) ) . '">' . esc_html__( 'Settings', 'eac-components' ) . '</a>' );
	return array_merge( $setting_link, $links );
}

/** Ajout du lien vers la page du centre d'aide et voir les détails du plugin */
function eac_add_help_detail_links( array $meta_links, string $plugin_file ): array {
	if ( EAC_PLUGIN_BASENAME === $plugin_file ) {
		// Lien view détails
		$meta_links[2] = sprintf(
			'<a href="%1$s" class="thickbox open-plugin-details-modal">%2$s</a>',
			esc_url( add_query_arg(
				array(
					'tab'       => 'plugin-information',
					'plugin'    => EAC_PLUGIN_SLUG,
					'TB_iframe' => true,
					'width'     => 600,
					'height'    => 550,
				),
				admin_url( 'plugin-install.php' )
			) ),
			esc_html__( 'View details', 'eac-components' )
		);

		// Help Center
		$setting_link = array(
			'<a href="' . EAC_PLUGIN_SITE . '/help-center/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Help center', 'eac-components' ) . '</a>',
			/**'<a href="' . EAC_PLUGIN_SITE . '/donate.php?for=' . EAC_PLUGIN_SLUG . '" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-smiley"></span> <span>Buy me a coffee!</span></a>'*/
		);
		$meta_links = array_merge( $meta_links, $setting_link );
	}
	return $meta_links;
}
