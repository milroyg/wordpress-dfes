<?php
/**
 * Class: Eac_Opcache_Manager
 *
 * Description:
 *
 * @since 2.3.7
 */

namespace EACCustomWidgets\Admin\Settings;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Opcache_Manager {

	/**
	 * $plugin_path
	 *
	 * @var string
	 */
	private $plugin_path;

	/**
	 * $opcache_data
	 *
	 * @var array
	 */
	private static $opcache_data = array();

	public function __construct() {
		// Définit le chemin du plugin
		$this->plugin_path = untrailingslashit( EAC_PLUGIN_PATH );

		self::$opcache_data = $this->set_opcache_data();
	}

	/**
	 * set_opcache_data
	 *
	 * @since 2.3.8 suppression de l'appel à la fonction 'opcache_get_configuration' et check de la directive 'opcache.restrict_api'
	 *
	 * @return array
	 */
	private function set_opcache_data(): array {
		$op_data = array( 'enable' => false );

		if ( function_exists( 'opcache_get_status' ) ) {
			$status  = opcache_get_status();

			// La directive restrict_api est valorisée, les infos avec opcache_get_status sont limitées
			if ( $status['opcache_enabled'] && empty( ini_get( 'opcache.restrict_api' ) ) ) {
				$eac_scripts  = 0;
				$last_restart = isset( $status['opcache_statistics']['last_restart_time'] ) && 0 === $status['opcache_statistics']['last_restart_time'] ? esc_html__( 'Never', 'eac-components' ) : date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $status['opcache_statistics']['last_restart_time'] );
				$allo_memory  = is_numeric( ini_get( 'opcache.memory_consumption' ) ) ? size_format( ini_get( 'opcache.memory_consumption' ) * 1024 * 1024 ) : ini_get( 'opcache.memory_consumption' );

				if ( isset( $status['scripts'] ) && ! empty( $status['scripts'] ) ) {
					foreach ( $status['scripts'] as $script ) {
						// Le script appartient à votre plugin
						if ( 0 === strpos( $script['full_path'], $this->plugin_path ) ) {
							++$eac_scripts;
						}
					}
				}

				$op_data = array(
					'enable'           => $status['opcache_enabled'],
					'version'          => phpversion(),
					'last_restart'     => $last_restart,
					'jit'              => false !== ini_get( 'opcache.jit' ) ? ini_get( 'opcache.jit' ) : 'failed',
					'timestamp'        => ini_get( 'opcache.validate_timestamps' ),
					'freq'             => ini_get( 'opcache.revalidate_freq' ),
					'max_scripts'      => ini_get( 'opcache.max_accelerated_files' ),
					'total_scripts'    => isset( $status['opcache_statistics']['num_cached_scripts'] ) ? $status['opcache_statistics']['num_cached_scripts'] : 0,
					'misses_scripts'   => isset( $status['opcache_statistics']['misses'] ) ? $status['opcache_statistics']['misses'] : 0,
					'eac_scripts'      => $eac_scripts,
					'allocated_memory' => $allo_memory,
					'used_memory'      => isset( $status['memory_usage']['used_memory'] ) ? size_format( $status['memory_usage']['used_memory'] ) : 0,
					'free_memory'      => isset( $status['memory_usage']['free_memory'] ) ? size_format( $status['memory_usage']['free_memory'] ) : 0,
				);
			}
		}
		return $op_data;
	}

	/**
	 * get_opcache_data
	 *
	 * @return array
	 */
	public static function get_opcache_data(): array {
		return self::$opcache_data;
	}
}
