<?php
/**
 * Class: Eac_Opcache_Manager
 *
 * Description:
 *
 * @since 2.3.7
 */

/**
	[opcache_enabled] => 1
	[cache_full] =>
	[restart_pending] =>
	[restart_in_progress] =>
	[memory_usage] => Array
		(
			[used_memory] => 112366880
			[free_memory] => 19446208
			[wasted_memory] => 2404640
			[current_wasted_percentage] => 1.7915964126587
		)

	[interned_strings_usage] => Array
		(
			[buffer_size] => 8388608
			[used_memory] => 8388608
			[free_memory] => 0
			[number_of_strings] => 97995
		)

	[opcache_statistics] => Array
		(
			[num_cached_scripts] => 3057
			[num_cached_keys] => 6116
			[max_cached_keys] => 16229
			[hits] => 1440831
			[start_time] => 1747735475
			[last_restart_time] => 0
			[oom_restarts] => 0
			[hash_restarts] => 0
			[manual_restarts] => 0
			[misses] => 3184
			[blacklist_misses] => 0
			[blacklist_miss_ratio] => 0
			[opcache_hit_rate] => 99.779503675516
		)
	[scripts] => Array(
		[C:\wamp64\www\eac234\wp-content\plugins\advanced-custom-fields\includes\locations\class-acf-location-nav-menu.php] => Array(
				[full_path] => C:\wamp64\www\eac234\wp-content\plugins\advanced-custom-fields\includes\locations\class-acf-location-nav-menu.php
				[hits] => 630
				[memory_consumption] => 8536
				[last_used] => Tue May 20 15:20:22 2025
				[last_used_timestamp] => 1747747222
				[timestamp] => 1745492423
				[revalidate] => 1747747224
			)
		)
 */
namespace EACCustomWidgets\Admin\Settings;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Opcache_Manager {

	private $plugin_path;

	private static $opcache_data = array();

	public function __construct() {
		// Définir le chemin de votre plugin
		$this->plugin_path = untrailingslashit( EAC_PLUGIN_PATH );

		self::$opcache_data = $this->set_opcache_data();

		// Vérifier l'état d'OPcache à l'activation du plugin
		//$this->check_opcache_status();
	}

	// Vérifie si OPcache est activé et affiche un message dans le tableau de bord
	public function check_opcache_status() {
		if ( function_exists( 'opcache_get_status' ) ) {
			$status = opcache_get_status();
			if ( $status['opcache_enabled'] ) {
				// Lire les valeurs des paramètres OPcache
				$opcache_jit = ini_get( 'opcache.jit' );
				$opcache_revalidate = ini_get( 'opcache.revalidate' );
				$opcache_validate_timestamps = ini_get( 'opcache.validate_timestamps' );
				$opcache_revalidate_freq = ini_get( 'opcache.revalidate_freq' );
				$this->filter_plugin_scripts( $status['scripts'], $status['opcache_statistics'], $status['memory_usage'] );
			}
		}
	}

	/**
	 * set_opcache_data
	 *
	 * @return array
	 */
	private function set_opcache_data(): array {
		$op_data = array( 'enable' => false );

		$cache_config = opcache_get_configuration(); // Peut être null si restrict_api n'est pas vide
		if ( $cache_config && isset( $cache_config['directives']['opcache.file_cache_only'] ) && 1 === $cache_config['directives']['opcache.file_cache_only'] ) {
			return $op_data;
		}

		if ( function_exists( 'opcache_get_status' ) ) {
			$eac_scripts = 0;
			$status = opcache_get_status();
			$last_restart = 0 === $status['opcache_statistics']['last_restart_time'] ? esc_html__( 'Jamais', 'eac-components' ) : date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $status['opcache_statistics']['last_restart_time'] );

			foreach ( $status['scripts'] as $script ) {
				// Vérifier si le script appartient à votre plugin
				if ( 0 === strpos( $script['full_path'], $this->plugin_path ) ) {
					++$eac_scripts;
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
				'total_scripts'    => $status['opcache_statistics']['num_cached_scripts'],
				'misses_scripts'   => $status['opcache_statistics']['misses'],
				'eac_scripts'      => $eac_scripts,
				'allocated_memory' => size_format( $cache_config['directives']['opcache.memory_consumption'] ),
				'used_memory'      => size_format( $status['memory_usage']['used_memory'] ),
				'free_memory'      => size_format( $status['memory_usage']['free_memory'] ),
			);
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

	// Filtrer les scripts de votre plugin
	private function filter_plugin_scripts( $scripts, $statistics, $mem_usage ) {
		$plugin_scripts         = array();
		$eac_memory_consumption = 0;

		foreach ( $scripts as $script ) {
			// Vérifier si le script appartient à votre plugin
			if ( 0 === strpos( $script['full_path'], $this->plugin_path ) ) {
				$eac_memory_consumption += $script['memory_consumption'];
				$memory_consumption = size_format( $script['memory_consumption'] );
				$memory_percentage_total = round( 100 * ( ( $mem_usage['used_memory'] + $mem_usage['wasted_memory'] ) / $script['memory_consumption'] ) );
				$memory_percentage_eac = size_format( $mem_usage['used_memory'] ) . ' <= used::wasted => ' . size_format( $mem_usage['wasted_memory'] ) . ' ::script consuption => ' . size_format( $script['memory_consumption'] );

				$key = pathinfo( $script['full_path'] )['basename'];
				$last_used_time = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $script['last_used'] );
				$last_start = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $statistics['start_time'] );
				$last_restart = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $statistics['last_restart_time'] );

				$plugin_scripts[ $script['full_path'] ]['script']                  = $key;
				$plugin_scripts[ $script['full_path'] ]['hits']                    = $script['hits'];
				$plugin_scripts[ $script['full_path'] ]['memory_ko']               = $memory_consumption;
				$plugin_scripts[ $script['full_path'] ]['memory_oc']               = $script['memory_consumption'];
				$plugin_scripts[ $script['full_path'] ]['memory_percentage_total'] = $memory_percentage_total;
				$plugin_scripts[ $script['full_path'] ]['last_used']               = $last_used_time;
			}
			$plugin_scripts['eac_memory_usage']  = size_format( $eac_memory_consumption ) . '=' . $eac_memory_consumption;
			$plugin_scripts['last_start']        = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $statistics['start_time'] );
			$plugin_scripts['last_restart']      = 0 === $statistics['last_restart_time'] ? 'Never' : $last_restart;
		}

		// Afficher les scripts de votre plugin
		if ( ! empty( $plugin_scripts ) ) {
			\write_log( $plugin_scripts );
		} else {
			\write_log( 'OPcache -- pas de scripts: ' . $this->plugin_path );
		}
	}
}
