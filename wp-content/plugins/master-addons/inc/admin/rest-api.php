<?php
/**
 * REST API endpoints for admin settings.
 *
 * @package MasterAddons
 */

namespace MasterAddons\Inc\Admin;

use MasterAddons\Inc\Admin\Settings\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class REST_API
 *
 * Registers REST API endpoints for settings management.
 */
class REST_API {

	/**
	 * Instance of this class.
	 *
	 * @var REST_API
	 */
	private static $instance = null;

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	private $namespace = 'master-addons/v1';

	/**
	 * Option key for setup wizard completion status.
	 *
	 * @var string
	 */
	const SETUP_COMPLETE_OPTION = 'jltma_setup_wizard_complete';

	/**
	 * Option key for setup wizard current step.
	 *
	 * @var string
	 */
	const SETUP_STEP_OPTION = 'jltma_setup_wizard_step';

	/**
	 * Option key for setup wizard site type selection.
	 *
	 * @var string
	 */
	const SETUP_SITE_TYPE_OPTION = 'jltma_setup_wizard_site_type';

	/**
	 * Get instance of this class.
	 *
	 * @return REST_API
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST routes.
	 */
	public function register_routes() {
		// Get/save admin settings.
		register_rest_route(
			$this->namespace,
			'/jltma-options',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_settings_options' ),
					'permission_callback' => array( $this, 'check_permission' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'save_settings_options' ),
					'permission_callback' => array( $this, 'check_permission' ),
				),
			)
		);

		// Reset settings.
		register_rest_route(
			$this->namespace,
			'/reset',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'reset_settings' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Export settings.
		register_rest_route(
			$this->namespace,
			'/export',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'export_settings' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Import settings.
		register_rest_route(
			$this->namespace,
			'/import',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'import_settings' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Get system info.
		register_rest_route(
			$this->namespace,
			'/system-info',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_system_info' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Get spotlight items.
		register_rest_route(
			$this->namespace,
			'/spotlight',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_spotlight' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Save dark mode preference.
		register_rest_route(
			$this->namespace,
			'/user/dark-mode',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_dark_mode' ),
				'permission_callback' => array( $this, 'check_basic_permission' ),
			)
		);

		// Setup Wizard Routes.
		register_rest_route(
			$this->namespace,
			'/setup-complete',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_setup_complete' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/setup-status',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_setup_status' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Theme management routes for setup wizard.
		register_rest_route(
			$this->namespace,
			'/theme-status',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_theme_status' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/theme-install',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'install_theme' ),
				'permission_callback' => function () {
					return current_user_can( 'install_themes' );
				},
			)
		);

		register_rest_route(
			$this->namespace,
			'/theme-activate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'activate_theme' ),
				'permission_callback' => function () {
					return current_user_can( 'switch_themes' );
				},
			)
		);


	}

	/**
	 * Check permission for REST requests.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function check_permission( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Check basic permission (logged in).
	 *
	 * @return bool
	 */
	public function check_basic_permission() {
		return is_user_logged_in();
	}

	/**
	 * Get all settings options.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_settings_options( $request ) {
		// Get option key from query param.
		$option_key   = $request->get_param( 'key' );
		$allowed_keys = $this->get_allowed_option_keys();

		// If no key provided, return all allowed options.
		if ( empty( $option_key ) ) {
			$getters     = $this->get_option_getters();
			$all_options = array();
			foreach ( $getters as $key => $getter ) {
				$all_options[ $key ] = $getter();
			}

			return new \WP_REST_Response(
				array(
					'success' => true,
					'data'    => $all_options,
				)
			);
		}

		// Validate option key against whitelist.
		if ( ! in_array( $option_key, array_keys($allowed_keys), true ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'invalid_option_key',
						'message' => __( 'Invalid option key.', 'master-addons' ),
					),
				),
				400
			);
		}

		// Get option value via lazy dispatch.
		$getters      = $this->get_option_getters();
		$option_value = isset( $getters[ $option_key ] ) ? $getters[ $option_key ]() : [];

		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => $option_value,
			)
		);
	}

	/**
	 * Get allowed option keys whitelist.
	 *
	 * @return array
	 */
	private function get_allowed_option_keys() {
		return Settings::get_option_keys();
	}

	/**
	 * Get option getter callbacks keyed by short name.
	 *
	 * On first install (option doesn't exist in DB), seeds defaults from Config
	 * so the admin page always shows correct initial state — even if Elementor
	 * is not active and the activated_*() hooks never fired.
	 *
	 * Shared between GET and POST endpoints to avoid duplication.
	 *
	 * @return array<string, callable>
	 */
	private function get_option_getters() {
		return [
			'addons'      => function () {
				// get_addons() checks legacy keys and migrates automatically.
				// Only seed defaults on a genuine first install (no new key AND no legacy key).
				$data = Settings::get_addons();
				if ( empty( $data ) ) {
					$data = Settings::get_default_addon_settings();
					Settings::save_addons( $data );
				}
				return $data;
			},
			'extensions'  => function () {
				$data = Settings::get_extensions();
				if ( empty( $data ) ) {
					$data = Settings::get_default_extension_settings();
					Settings::save_extensions( $data );
				}
				return $data;
			},
			'plugins'     => function () {
				$data = Settings::get_plugins();
				if ( empty( $data ) ) {
					$data = Settings::get_default_plugin_settings();
					Settings::save_plugins( $data );
				}
				return $data;
			},
			'icons'       => function () {
				$data = Settings::get_icons();
				if ( empty( $data ) ) {
					$data = Settings::get_default_icon_settings();
					Settings::save_icons( $data );
				}
				return $data;
			},
			'api'         => fn() => Settings::get_api(),
			'white_label' => fn() => get_option( Settings::WHITE_LABEL, [] ),
		];
	}

	/**
	 * Save all settings options.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function save_settings_options( $request ) {
		// Get and validate request body.
		$body = $request->get_json_params();

		if ( empty( $body ) || ! isset( $body['key'] ) || ! isset( $body['data'] ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'invalid_request',
						'message' => __( 'Missing required fields: key and data.', 'master-addons' ),
					),
				),
				400
			);
		}

		$option_key  = $body['key'];
		$option_data = $body['data'];

		// Validate option key against whitelist.
		$allowed_keys = $this->get_allowed_option_keys();
		if ( ! in_array( $option_key, array_keys($allowed_keys), true ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'invalid_option_key',
						'message' => __( 'Invalid option key.', 'master-addons' ),
					),
				),
				400
			);
		}

		// Sanitize the option key.
		$option_key = sanitize_key( $option_key );

		// Recursively sanitize data.
		$sanitized_data = $this->sanitize_option_data( $option_data );

		// Lazy dispatch — only execute the saver for the requested key.
		$savers = [
			'addons'      => fn( $d ) => Settings::save_addons( $d ),
			'extensions'  => fn( $d ) => Settings::save_extensions( $d ),
			'plugins'     => fn( $d ) => Settings::save_plugins( $d ),
			'icons'       => fn( $d ) => Settings::save_icons( $d ),
			'api'         => fn( $d ) => Settings::save_api( $d ),
			'white_label' => fn( $d ) => update_option( Settings::WHITE_LABEL, $d ),
		];

		if ( ! isset( $savers[ $option_key ] ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'unsupported_option_key',
						'message' => __( 'This option key cannot be saved.', 'master-addons' ),
					),
				),
				400
			);
		}

		$savers[ $option_key ]( $sanitized_data );

		// Return the freshly saved data using the matching getter.
		$getters = $this->get_option_getters();

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Settings saved successfully.', 'master-addons' ),
				'data'    => $getters[ $option_key ](),
			)
		);
	}

	/**
	 * Recursively sanitize option data.
	 *
	 * @param mixed $data Data to sanitize.
	 * @return mixed Sanitized data.
	 */
	private function sanitize_option_data( $data ) {
		if ( is_array( $data ) ) {
			$sanitized = array();
			foreach ( $data as $key => $value ) {
				$sanitized_key               = sanitize_key( $key );
				$sanitized[ $sanitized_key ] = $this->sanitize_option_data( $value );
			}
			return $sanitized;
		}

		if ( is_string( $data ) ) {
			// Allow safe HTML for WYSIWYG fields.
			return wp_kses_post( $data );
		}

		if ( is_numeric( $data ) || is_bool( $data ) || is_null( $data ) ) {
			return $data;
		}

		return '';
	}

	/**
	 * Reset all settings to defaults.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function reset_settings( $request ) {
		// Get and validate request body.
		$body = $request->get_json_params();

		$allowed_keys = $this->get_allowed_option_keys();
		$options_key  = isset( $body['key'] ) ? $body['key'] : '';

		if ( ! empty( $options_key ) && isset( $allowed_keys[ $options_key ] ) ) {
			// Remove one section by short name mapped to DB key.
			$db_key = $allowed_keys[ $options_key ];
			delete_option( $db_key );

			return new \WP_REST_Response(
				array(
					'success'   => true,
					'reset_key' => $options_key,
					'message'   => __( 'Reset settings successfully!', 'master-addons' ),
				)
			);
		}

		// Remove all settings options.
		if ( empty( $options_key ) ) {
			foreach ( $allowed_keys as $short_name => $db_key ) {
				delete_option( $db_key );
			}

			return new \WP_REST_Response(
				array(
					'success'   => true,
					'reset_key' => 'all',
					'message'   => __( 'Reset all settings successfully!', 'master-addons' ),
				)
			);
		}

		// Handle error messages.
		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => __( 'Something went wrong', 'master-addons' ),
			)
		);
	}

	/**
	 * Export settings as JSON.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function export_settings( $request ) {
		$plugin_slug = 'master-addons';
		$getters     = $this->get_option_getters();

		// Build settings data keyed by short name (addons, extensions, etc.)
		// to match the frontend OPTIONS_KEYS mapping.
		$settings = array();
		foreach ( $getters as $short_name => $getter ) {
			$value = $getter();
			if ( ! empty( $value ) ) {
				$settings[ $short_name ] = $value;
			}
		}

		// Build export data with metadata.
		$export_data = array(
			'plugin'    => $plugin_slug,
			'version'   => defined( 'JLTMA_VER' ) ? JLTMA_VER : '1.0.0',
			'timestamp' => current_time( 'c' ),
			'settings'  => $settings,
		);

		return new \WP_REST_Response(
			array(
				'success'  => true,
				'data'     => $export_data,
				'filename' => $plugin_slug . '-settings-' . gmdate( 'Y-m-d' ) . '.json',
			)
		);
	}

	/**
	 * Import settings from JSON.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function import_settings( $request ) {
		$import_data  = $request->get_json_params();
		$allowed_keys = $this->get_allowed_option_keys();

		// Validate import data structure.
		if ( empty( $import_data ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'empty_data',
						'message' => __( 'No import data provided.', 'master-addons' ),
					),
				),
				400
			);
		}

		// Check for settings key in import data.
		if ( ! isset( $import_data['settings'] ) || ! is_array( $import_data['settings'] ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'invalid_format',
						'message' => __( 'Invalid import file format. Missing settings data.', 'master-addons' ),
					),
				),
				400
			);
		}

		$settings       = $import_data['settings'];
		$imported_count = 0;
		$skipped_keys   = array();

		// Import each setting: key is a short name (addons, extensions, etc.)
		// mapped to the full DB option key via $allowed_keys.
		foreach ( $settings as $key => $value ) {
			if ( isset( $allowed_keys[ $key ] ) ) {
				$db_key          = $allowed_keys[ $key ];
				$sanitized_value = $this->sanitize_option_data( $value );
				update_option( $db_key, $sanitized_value );
				$imported_count++;
			} else {
				$skipped_keys[] = $key;
			}
		}

		// Return response.
		if ( $imported_count > 0 ) {
			return new \WP_REST_Response(
				array(
					'success'        => true,
					'message'        => sprintf(
						/* translators: %d: number of imported settings */
						__( 'Successfully imported %d setting(s).', 'master-addons' ),
						$imported_count
					),
					'imported_count' => $imported_count,
					'skipped_keys'   => $skipped_keys,
				)
			);
		}

		return new \WP_REST_Response(
			array(
				'success' => false,
				'error'   => array(
					'code'    => 'no_valid_settings',
					'message' => __( 'No valid settings found in import file.', 'master-addons' ),
				),
			),
			400
		);
	}

	/**
	 * Get spotlight items.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_spotlight( $request ) {
		// Return empty spotlight items - can be extended later.
		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(),
			)
		);
	}

	/**
	 * Get system info data for the System Info page.
	 *
	 * Uses a transient cache (5 min) to avoid repeated expensive operations
	 * like reading plugin file headers on every request.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_system_info( $request ) {
		$refresh    = $request->get_param( 'refresh' );
		$cache_key  = 'jltma_system_info';
		$cache_time = 5 * MINUTE_IN_SECONDS;

		// Return cached data unless refresh is requested.
		if ( ! $refresh ) {
			$cached = get_transient( $cache_key );
			if ( false !== $cached ) {
				return new \WP_REST_Response(
					array(
						'success' => true,
						'data'    => $cached,
					)
				);
			}
		}

		global $wp_version, $wpdb;

		// WordPress Environment
		$memory_limit   = ini_get( 'memory_limit' ) ?: 'N/A';
		$uploads        = wp_upload_dir( null, false ); // false = skip creating dirs.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_is_writable -- Read-only writability probe of the uploads directory for a diagnostics report.
		$upload_writable = is_writable( $uploads['basedir'] );

		$wordpress = array(
			array(
				'label'  => 'Home URL',
				'value'  => home_url(),
				'status' => 'none',
			),
			array(
				'label'  => 'Site URL',
				'value'  => site_url(),
				'status' => 'none',
			),
			array(
				'label'  => 'WP Version',
				'value'  => $wp_version,
				'status' => version_compare( $wp_version, '4.0', '>=' ) ? 'ok' : 'error',
			),
			array(
				'label'  => 'WP Multisite',
				'value'  => is_multisite() ? 'Enabled' : 'Disabled',
				'status' => 'none',
			),
			array(
				'label'  => 'WP Memory Limit',
				'value'  => $memory_limit,
				'status' => (int) $memory_limit >= 256 ? 'ok' : 'error',
			),
			array(
				'label'  => 'WP Path',
				'value'  => ABSPATH,
				'status' => 'none',
			),
			array(
				'label'  => 'Writable Uploads Folder',
				'value'  => $upload_writable ? 'Writable' : 'Not Writable',
				'status' => $upload_writable ? 'ok' : 'error',
			),
			array(
				'label'  => 'WP Debug Mode',
				'value'  => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'Enabled' : 'Disabled',
				'status' => 'none',
			),
			array(
				'label'  => 'Language',
				'value'  => get_locale(),
				'status' => 'none',
			),
		);

		// Server Requirements
		$php_version      = function_exists( 'phpversion' ) ? phpversion() : 'N/A';
		$php_memory_limit = ini_get( 'memory_limit' ) ?: 'N/A';
		$post_max_size    = ini_get( 'post_max_size' ) ?: 'N/A';
		$time_limit       = ini_get( 'max_execution_time' ) ?: 'N/A';
		$max_input_vars   = ini_get( 'max_input_vars' ) ?: 'N/A';
		$mysql_version    = $wpdb->db_version();
		$max_upload_size  = size_format( wp_max_upload_size() );

		$server = array(
			array(
				'label'  => 'Server Info',
				'value'  => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : 'N/A',
				'status' => 'none',
			),
			array(
				'label'  => 'PHP Version',
				'value'  => $php_version,
				'status' => version_compare( $php_version, '5.6', '>=' ) ? 'ok' : 'error',
			),
			array(
				'label'  => 'PHP Memory Limit',
				'value'  => $php_memory_limit,
				'status' => (int) $php_memory_limit >= 256 ? 'ok' : 'error',
			),
			array(
				'label'  => 'PHP Post Max Size',
				'value'  => $post_max_size,
				'status' => (int) $post_max_size >= 32 ? 'ok' : 'error',
			),
			array(
				'label'  => 'PHP Time Limit',
				'value'  => $time_limit,
				'status' => ( (int) $time_limit >= 120 || (int) $time_limit === 0 ) ? 'ok' : 'error',
			),
			array(
				'label'  => 'PHP Max Input Vars',
				'value'  => $max_input_vars,
				'status' => (int) $max_input_vars >= 1000 ? 'ok' : 'error',
			),
			array(
				'label'  => 'MySQL Version',
				'value'  => $mysql_version,
				'status' => version_compare( $mysql_version, '5.3', '>=' ) ? 'ok' : 'error',
			),
			array(
				'label'  => 'Max Upload Size',
				'value'  => $max_upload_size,
				'status' => (int) $max_upload_size >= 20 ? 'ok' : 'error',
			),
		);

		// PHP Extensions
		$curl_ok = function_exists( 'curl_init' );
		$fsock_ok = function_exists( 'fsockopen' );
		$soap_ok = class_exists( 'SoapClient' );
		$suhosin_ok = extension_loaded( 'suhosin' );

		$php_extensions = array(
			array(
				'label'  => 'cURL',
				'value'  => $curl_ok ? 'Supported' : 'Not Installed',
				'status' => $curl_ok ? 'ok' : 'error',
			),
			array(
				'label'  => 'fsockopen',
				'value'  => $fsock_ok ? 'Supported' : 'Not Installed',
				'status' => $fsock_ok ? 'ok' : 'error',
			),
			array(
				'label'  => 'SOAP Client',
				'value'  => $soap_ok ? 'Supported' : 'Not Installed',
				'status' => $soap_ok ? 'ok' : 'error',
			),
			array(
				'label'  => 'Suhosin',
				'value'  => $suhosin_ok ? 'Supported' : 'Not Installed',
				'status' => $suhosin_ok ? 'ok' : 'error',
			),
		);

		// Active Plugins
		// Use get_plugins() + active list instead of get_plugin_data() per file.
		// get_plugins() is internally cached by WP and reads all headers in one pass.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins         = get_plugins();
		$active_plugins_list = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$network_plugins     = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins_list = array_merge( $active_plugins_list, $network_plugins );
		}

		$active_plugins = array();
		foreach ( $active_plugins_list as $plugin_file ) {
			if ( ! isset( $all_plugins[ $plugin_file ] ) ) {
				continue;
			}

			$p = $all_plugins[ $plugin_file ];

			$active_plugins[] = array(
				'name'       => $p['Name'],
				'author'     => wp_strip_all_tags( $p['Author'] ),
				'version'    => $p['Version'],
				'plugin_url' => ! empty( $p['PluginURI'] ) ? $p['PluginURI'] : '',
				'author_url' => ! empty( $p['AuthorURI'] ) ? $p['AuthorURI'] : '',
			);
		}

		$data = array(
			'wordpress'      => $wordpress,
			'server'         => $server,
			'php_extensions' => $php_extensions,
			'active_plugins' => $active_plugins,
		);

		// Cache the result for 5 minutes.
		set_transient( $cache_key, $data, $cache_time );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => $data,
			)
		);
	}

	/**
	 * Save dark mode preference.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function save_dark_mode( $request ) {
		$mode    = $request->get_param( 'mode' );
		$user_id = get_current_user_id();

		if ( ! in_array( $mode, array( 'dark', 'light', 'auto' ), true ) ) {
			$mode = 'auto';
		}

		update_user_meta( $user_id, 'jltma_dark_mode', $mode );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => array( 'mode' => $mode ),
			)
		);
	}

	/**
	 * Handle setup complete REST API request.
	 *
	 * Saves the current wizard step progress and optional site type.
	 * When step is "done", marks the wizard as fully complete.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function handle_setup_complete( $request ) {
		$step      = sanitize_text_field( $request->get_param( 'step' ) );
		$site_type = sanitize_text_field( $request->get_param( 'siteType' ) );

		// Save current step progress.
		if ( ! empty( $step ) ) {
			update_option( self::SETUP_STEP_OPTION, $step );
		}

		// Save site type if provided.
		if ( ! empty( $site_type ) ) {
			update_option( self::SETUP_SITE_TYPE_OPTION, $site_type );
		}

		// Mark setup as complete when step is "done".
		if ( 'done' === $step ) {
			update_option( self::SETUP_COMPLETE_OPTION, true );
		}

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'Setup progress saved.', 'master-addons' ),
			)
		);
	}

	/**
	 * Get setup status REST API handler.
	 *
	 * Returns the current step, site type, and completion status.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_setup_status() {
		return rest_ensure_response(
			array(
				'step'       => get_option( self::SETUP_STEP_OPTION, null ),
				'siteType'   => get_option( self::SETUP_SITE_TYPE_OPTION, null ),
				'isComplete' => self::is_setup_complete(),
			)
		);
	}

	/**
	 * Check if setup wizard is complete.
	 *
	 * Returns true if the saved step is "done" or the legacy complete flag is set.
	 *
	 * @return bool
	 */
	public static function is_setup_complete() {
		$step = get_option( self::SETUP_STEP_OPTION, null );
		if ( 'done' === $step ) {
			return true;
		}
		return (bool) get_option( self::SETUP_COMPLETE_OPTION, false );
	}

	/**
	 * Get theme status (not_installed, installed, or active).
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_theme_status( $request ) {
		$slug = sanitize_text_field( $request->get_param( 'slug' ) );

		if ( empty( $slug ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'missing_slug',
						'message' => __( 'Theme slug is required.', 'master-addons' ),
					),
				),
				400
			);
		}

		$theme        = wp_get_theme( $slug );
		$active_theme = wp_get_theme();

		if ( $active_theme->get_stylesheet() === $slug ) {
			$status = 'active';
		} elseif ( $theme->exists() ) {
			$status = 'installed';
		} else {
			$status = 'not_installed';
		}

		return rest_ensure_response(
			array(
				'success' => true,
				'data'    => array( 'status' => $status ),
			)
		);
	}

	/**
	 * Install a theme from wordpress.org by slug.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function install_theme( $request ) {
		$slug = sanitize_text_field( $request->get_param( 'slug' ) );

		if ( empty( $slug ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'missing_slug',
						'message' => __( 'Theme slug is required.', 'master-addons' ),
					),
				),
				400
			);
		}

		// Check if already installed.
		$theme = wp_get_theme( $slug );
		if ( $theme->exists() ) {
			return rest_ensure_response(
				array(
					'success' => true,
					'message' => __( 'Theme is already installed.', 'master-addons' ),
					'data'    => array( 'status' => 'installed' ),
				)
			);
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/theme.php';

		// Get theme info from wordpress.org API.
		$api = themes_api(
			'theme_information',
			array(
				'slug'   => $slug,
				'fields' => array( 'sections' => false ),
			)
		);

		if ( is_wp_error( $api ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'api_error',
						'message' => $api->get_error_message(),
					),
				),
				500
			);
		}

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Theme_Upgrader( $skin );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'install_error',
						'message' => $result->get_error_message(),
					),
				),
				500
			);
		}

		if ( ! $result ) {
			// Check skin for errors.
			$errors = $skin->get_errors();
			$msg    = is_wp_error( $errors ) ? $errors->get_error_message() : __( 'Theme installation failed.', 'master-addons' );

			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'install_failed',
						'message' => $msg,
					),
				),
				500
			);
		}

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'Theme installed successfully.', 'master-addons' ),
				'data'    => array( 'status' => 'installed' ),
			)
		);
	}

	/**
	 * Activate an installed theme by slug.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function activate_theme( $request ) {
		$slug = sanitize_text_field( $request->get_param( 'slug' ) );

		if ( empty( $slug ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'missing_slug',
						'message' => __( 'Theme slug is required.', 'master-addons' ),
					),
				),
				400
			);
		}

		$theme = wp_get_theme( $slug );

		if ( ! $theme->exists() ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => array(
						'code'    => 'not_installed',
						'message' => __( 'Theme is not installed.', 'master-addons' ),
					),
				),
				404
			);
		}

		switch_theme( $slug );

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'Theme activated successfully.', 'master-addons' ),
				'data'    => array( 'status' => 'active' ),
			)
		);
	}

}
