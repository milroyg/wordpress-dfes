<?php
/**
 * This is to plugin blocks page.
 *
 * @package location-weather
 */

namespace ShapedPlugin\Weather\Admin\AdminDashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Splw_Blocks_Page_Wrapper Class.
 */
class Splw_Blocks_Page_Wrapper {


	/**
	 * Instance of the class.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Plugins Path variable.
	 *
	 * @var array
	 */
	protected static $plugins = array(
		'woo-product-slider'             => 'main.php',
		'gallery-slider-for-woocommerce' => 'woo-gallery-slider.php',
		'post-carousel'                  => 'main.php',
		'easy-accordion-free'            => 'plugin-main.php',
		'woo-quickview'                  => 'woo-quick-view.php',
		'wp-expand-tabs-free'            => 'plugin-main.php',
		'logo-carousel-free'             => 'main.php',
	);

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'blocks_page_wrapper' ), 2 );
		add_action( 'wp_ajax_splw_update_block_options', array( $this, 'splw_update_block_options' ) );
		add_action( 'wp_ajax_splw_update_integrations_options', array( $this, 'splw_update_integrations_options' ) );
		add_action( 'wp_ajax_splw_changelog_data', array( $this, 'splw_changelog_data' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_ajax_splw_get_user_consent', array( $this, 'splw_get_user_consent' ) );
		add_action( 'init', array( $this, 'splw_submit_user_consent' ) );
		add_action( 'admin_notices', array( $this, 'maybe_show_user_consent_notice' ) );
		add_action( 'admin_print_scripts', array( $this, 'disable_admin_notices' ) );
		add_action( 'wp_ajax_splw_update_setting_options', array( $this, 'splw_update_setting_options' ) );
		add_action( 'wp_ajax_lwp_clean_weather_transients', array( $this, 'lwp_clean_weather_transients' ) );
	}

	/**
	 * Add submenu page for blocks.
	 */
	public function blocks_page_wrapper() {
		add_submenu_page(
			'edit.php?post_type=location_weather',
			__( 'Location Weather Dashboard', 'location-weather' ),
			__( 'Getting Started', 'location-weather' ),
			apply_filters( 'location_weather_access_capability', 'manage_options' ),
			'splw_admin_dashboard',
			array( $this, 'blocks_page_wrapper_callback' )
		);
		add_submenu_page(
			'edit.php?post_type=location_weather',
			__( 'Location Weather Blocks', 'location-weather' ),
			'Blocks <span class="eap-menu-new-indicator" style="color: #f18200;font-size: 9px; padding-left: 3px;"> NEW!</span>',
			apply_filters( 'location_weather_access_capability', 'manage_options' ),
			'edit.php?post_type=location_weather&page=splw_admin_dashboard#blocks'
		);
		add_submenu_page(
			'edit.php?post_type=location_weather',
			__( 'Location Weather Pro Saved Templates', 'location-weather' ),
			__( 'Saved Templates', 'location-weather' ),
			apply_filters( 'location_weather_access_capability', 'manage_options' ),
			'edit.php?post_type=location_weather&page=splw_admin_dashboard#saved_templates'
		);
		add_submenu_page(
			'edit.php?post_type=location_weather',
			__( 'Location Weather Settings', 'location-weather' ),
			__( 'Settings', 'location-weather' ),
			apply_filters( 'location_weather_access_capability', 'manage_options' ),
			'edit.php?post_type=location_weather&page=splw_admin_dashboard#lw_settings'
		);
		add_submenu_page(
			'edit.php?post_type=location_weather',
			__( 'Lite vs Pro', 'location-weather' ),
			'<span style="color: #FF980F;">' . __( 'Lite vs Pro', 'location-weather' ) . '</span>',
			apply_filters( 'location_weather_access_capability', 'manage_options' ),
			'edit.php?post_type=location_weather&page=splw_admin_dashboard#lite_vs_pro'
		);
		add_submenu_page(
			'edit.php?post_type=location_weather',
			__( 'Upgrade to Pro', 'location-weather' ),
			'<a class="splw-upgrade-btn-wrapper" href="https://locationweather.io/pricing/?ref=1" target="_blank">
        		<span class="splw-upgrade-btn">
            		<span class="sp-go-pro-icon"></span> Upgrade to Pro
        		</span>
    		</a>',
			'manage_options',
			'splw_upgrade_to_pro',
			'__return_null'
		);

		// Reorder submenu items.
		add_action(
			'admin_menu',
			function () {
				global $submenu;

				$menu_key = 'edit.php?post_type=location_weather';

				if ( isset( $submenu[ $menu_key ] ) ) {

					$items = $submenu[ $menu_key ];

					// Index items by slug for easier sorting.
					$indexed = array();
					foreach ( $items as $item ) {
						$indexed[ $item[2] ] = $item;
					}

					$modified_order = array(
						'splw_admin_dashboard',
						'edit.php?post_type=location_weather&page=splw_admin_dashboard#blocks',
						'edit.php?post_type=location_weather&page=splw_admin_dashboard#saved_templates',
						'edit.php?post_type=location_weather&page=splw_admin_dashboard#lw_settings',
						'lw-settings',
						'edit.php?post_type=location_weather',
						'post-new.php?post_type=location_weather',
						'lw-tools',
						'edit.php?post_type=location_weather&page=splw_admin_dashboard#lite_vs_pro',
						'splw_upgrade_to_pro',
					);

					// Build new submenu.
					$new = array();

					foreach ( $modified_order as $slug ) {
						if ( isset( $indexed[ $slug ] ) ) {
							$new[] = $indexed[ $slug ];
							unset( $indexed[ $slug ] );
						}
					}

					// Add any extra items not included above.
					foreach ( $indexed as $item ) {
						$new[] = $item;
					}

					// Save final sorted list.
					// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
					$submenu[ $menu_key ] = $new;
					// phpcs:enable
				}
			},
			999
		);
	}

	/**
	 * Callback function for the blocks page.
	 */
	public function blocks_page_wrapper_callback() {
		// Handle activation/deactivation requests that come back via the action links.
		$action   = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		$plugin   = isset( $_GET['plugin'] ) ? sanitize_text_field( wp_unslash( $_GET['plugin'] ) ) : '';
		$_wpnonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

		if ( isset( $action, $plugin ) && 'activate' === $action && wp_verify_nonce( $_wpnonce, 'activate-plugin_' . $plugin ) ) {
			if ( current_user_can( 'activate_plugins' ) ) {
				activate_plugin( $plugin, '', false, true );
			}
		}

		?>
		<div id="spl-weather-pro-block-admin-page" class="spl-weather-pro-block-admin-page">
			<div class="splw-recommended-plugins-wrapper" style="display: none;">
				<h2 class="splw-section-title"><?php esc_html_e( 'Supercharge Your Website with Our Free Plugins — Trusted by 360,050+ Users', 'location-weather' ); ?></h2>
				<div class="splw-wp-list-table plugin-install-php">
					<div class="splw-recommended-plugins" id="the-list">
						<?php
						$this->splw_plugins_info_api_help_page();
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue admin scripts and styles.
	 */
	public function enqueue_admin_assets() {
		if ( ! isset( $_GET['page'] ) || 'splw_admin_dashboard' !== $_GET['page'] ) {
			return;
		}
		// Enqueue the default ThickBox js and css.
		add_thickbox();

		wp_enqueue_script(
			'splw-block-setting-page',
			LOCATION_WEATHER_URL . '/includes/Admin/AdminDashboard/build/index.js',
			array( 'wp-element', 'react-jsx-runtime', 'wp-components' ),
			LOCATION_WEATHER_VERSION,
			true
		);
		wp_enqueue_style(
			'splw-block-setting-page',
			LOCATION_WEATHER_URL . '/includes/Admin/AdminDashboard/build/style-index.css',
			array(),
			LOCATION_WEATHER_VERSION,
			'all'
		);

		wp_localize_script(
			'splw-block-setting-page',
			'splw_admin_settings_localize',
			array(
				'homeUrl'                => home_url( '/' ),
				'pluginVersion'          => LOCATION_WEATHER_VERSION,
				'pluginUrl'              => LOCATION_WEATHER_URL,
				'settings'               => get_option( 'location_weather_settings' ),
				'sp_ua_site_type'        => get_option( 'sp_ua_site_type' ) ?? '',
				'splw_user_consent'      => get_option( 'splw_allow_anonymous_data', 'undefined' ),
				'splw_editor_preference' => get_option( 'splw_blocks_promo_modal_choice', '' ),
				'nonce'                  => wp_create_nonce( 'splw_admin_settings_nonce' ),
				'current_user'           => wp_get_current_user()->display_name ?? '',
				'blocks_visibility'      => get_option( 'splw_blocks_visibility_options', array() ),
				'integrations_options'   => get_option( 'splw_integrations_options', array() ),
			)
		);
	}

	/**
	 * Disable admin notices on this page.
	 */
	public function disable_admin_notices() {

		global $wp_filter;

		if (isset($_GET['page']) && in_array(wp_unslash($_GET['page']), ['splw_admin_dashboard'])) { // @codingStandardsIgnoreLine

			if ( isset( $wp_filter['user_admin_notices'] ) ) {
				unset( $wp_filter['user_admin_notices'] );
			}
			if ( isset( $wp_filter['admin_notices'] ) ) {
				unset( $wp_filter['admin_notices'] );
			}
			if ( isset( $wp_filter['all_admin_notices'] ) ) {
				unset( $wp_filter['all_admin_notices'] );
			}
		}
	}

	/**
	 * Handle AJAX request to update block settings.
	 */
	public function splw_update_block_options() {
		// Check user capabilities, current_user_can() is called internally.
		location_weather_verify_capability();

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'splw_admin_settings_nonce' ) ) {
			wp_send_json_error( __( 'Invalid nonce.', 'location-weather' ) );
		}

		$options = isset( $_POST['optionData'] ) ? sanitize_text_field( wp_unslash( $_POST['optionData'] ) ) : '';
		$options = (array) json_decode( $options, true );
		if ( ! empty( $options ) ) {
			update_option( 'splw_blocks_visibility_options', $options );
		}
		wp_send_json(
			array(
				'options' => get_option( 'splw_blocks_visibility_options' ),
			)
		);
	}

	/**
	 * Handle AJAX request to update integrations options.
	 */
	public function splw_update_integrations_options() {
		// Check user capabilities, current_user_can() is called internally.
		location_weather_verify_capability();

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'splw_admin_settings_nonce' ) ) {
			wp_send_json_error( __( 'Invalid nonce.', 'location-weather' ) );
		}

		$options = isset( $_POST['optionData'] ) ? sanitize_text_field( wp_unslash( $_POST['optionData'] ) ) : '';
		$options = (array) json_decode( $options, true );
		if ( ! empty( $options ) ) {
			update_option( 'splw_integrations_options', $options );
		}
		wp_send_json(
			array(
				'options' => get_option( 'splw_integrations_options' ),
			)
		);
	}

	/**
	 * Handle AJAX request to update block settings.
	 */
	public function splw_update_setting_options() {
		// Check user capabilities, current_user_can() is called internally.
		location_weather_verify_capability();

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'splw_admin_settings_nonce' ) ) {
			wp_send_json_error( __( 'Invalid nonce.', 'location-weather' ) );
		}

		$options_json = isset( $_POST['optionData'] ) ? wp_unslash( $_POST['optionData'] ) : '';// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON validated via json_decode() and json_last_error().

		$options = (array) json_decode( $options_json, true );

		// Validate JSON was properly decoded.
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error( __( 'Invalid JSON data.', 'location-weather' ) );
		}
		if ( isset( $_POST['shareData'] ) ) {
			$share_data = wp_unslash( $_POST['shareData'] );// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON validated via json_decode() and json_last_error().
			$consent    = filter_var( $share_data, FILTER_VALIDATE_BOOLEAN );
			update_option( 'splw_allow_anonymous_data', $consent );
		}

		if ( isset( $_POST['editorPreference'] ) ) {
			$editor_preference = sanitize_key( wp_unslash( $_POST['editorPreference'] ) );
			$allowed_editors   = array( 'block_editor', 'classic_shortcode' );
			if ( in_array( $editor_preference, $allowed_editors, true ) ) {
				update_option( 'splw_blocks_promo_modal_choice', $editor_preference, false );
			} else {
				delete_option( 'splw_blocks_promo_modal_choice' );
			}
		}

		if ( is_array( $options ) ) {

			// Get existing settings.
			$existing_options = get_option( 'location_weather_settings', array() );

			// Safety check.
			if ( ! is_array( $existing_options ) ) {
				$existing_options = array();
			}

			// Merge new values over old ones.
			$merged_options = array_merge( $existing_options, $options );

			// Save merged result.
			update_option( 'location_weather_settings', $merged_options );
		}
		wp_send_json_success(
			array(
				'options' => get_option( 'location_weather_settings' ),
			)
		);
	}

	/**
	 * Clean transients related to the Open Weather API.
	 *
	 * This function deletes transients and their timeout values associated with the Open Weather API.
	 * It performs nonce verification to ensure the request is legitimate.
	 */
	public function lwp_clean_weather_transients() {
		// Check user capabilities, current_user_can() is called internally.
		location_weather_verify_capability();

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'splw_admin_settings_nonce' ) ) {
			wp_send_json_error( __( 'Invalid nonce.', 'location-weather' ) );
		}

		global $wpdb;

		$wp_options = $wpdb->prefix . 'options';
		if ( is_multisite() ) {
			$wp_sitemeta = $wpdb->get_blog_prefix( BLOG_ID_CURRENT_SITE ) . 'sitemeta';
			// Delete OpenWeather transients.
			$wpdb->query("DELETE FROM {$wp_sitemeta} WHERE `meta_key` LIKE ('%\_site_transient_sp_open_weather_%')"); // phpcs:ignore -- intentionally ignored to remove specific transients.
			$wpdb->query("DELETE FROM {$wp_sitemeta} WHERE `meta_key` LIKE ('%\_transient_timeout_sp_open_weather_%')");  // phpcs:ignore -- intentionally ignored to remove specific transients.

			// Delete WeatherApi transients.
			$wpdb->query("DELETE FROM {$wp_sitemeta} WHERE `meta_key` LIKE ('%\_site_transient_sp_weather_api_%')"); // phpcs:ignore -- intentionally ignored to remove specific transients.
			$wpdb->query("DELETE FROM {$wp_sitemeta} WHERE `meta_key` LIKE ('%\_transient_timeout_sp_weather_api_%')");  // phpcs:ignore -- intentionally ignored to remove specific transients.
			wp_send_json_success();
		} else {
			// Delete OpenWeather transients.
			$wpdb->query("DELETE FROM {$wp_options} WHERE `option_name` LIKE ('%\_transient_sp_open_weather_%')"); // phpcs:ignore -- intentionally ignored to remove specific transients.
			$wpdb->query("DELETE FROM {$wp_options} WHERE `option_name` LIKE ('%\_transient_timeout_sp_open_weather_%')"); // phpcs:ignore -- intentionally ignored to remove specific transients.

			// Delete WeatherApi transients.
			$wpdb->query("DELETE FROM {$wp_options} WHERE `option_name` LIKE ('%\_transient_sp_weather_api_%')"); // phpcs:ignore -- intentionally ignored to remove specific transients.
			$wpdb->query("DELETE FROM {$wp_options} WHERE `option_name` LIKE ('%\_transient_timeout_sp_weather_api_%')"); // phpcs:ignore -- intentionally ignored to remove specific transients.
			wp_send_json_success();
		}
	}

	/**
	 * Handles the AJAX request to retrieve the plugin changelog.
	 *
	 * - Attempts to retrieve the changelog data from a cached transient.
	 * - If no cached data exists, fetches the changelog from the remote API.
	 * - Returns the changelog data as a JSON response.
	 *
	 * @return void
	 */
	public function splw_changelog_data() {
		// Check user capabilities, current_user_can() is called internally.
		location_weather_verify_capability();
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'splw_admin_settings_nonce' ) ) {
			return;
		}

		$changelog = get_transient( 'splw_changelogs' );

		if ( empty( $changelog ) ) {
			$changelog = $this->fetch_changelog_from_api();
		}

		wp_send_json(
			array(
				'changelog' => $changelog,
			)
		);
	}

	/**
	 * Fetches changelog from the remote API.
	 * Stores the changelog in a transient for caching (1 day).
	 *
	 * @return string The changelog content or empty string on failure.
	 */
	protected function fetch_changelog_from_api() {
		$api_url  = 'https://api.wordpress.org/plugins/info/1.0/location-weather.json';
		$response = wp_safe_remote_get(
			esc_url_raw( $api_url ),
			array(
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return '';
		}

		$api_data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! isset( $api_data['sections']['changelog'] ) ) {
			return '';
		}

		$changelog = wp_kses_post( $api_data['sections']['changelog'] );
		set_transient( 'splw_changelogs', $changelog, DAY_IN_SECONDS );

		return $changelog;
	}
	/**
	 * Splw_ajax_help_page function.
	 *
	 * @return void
	 */
	public function splw_plugins_info_api_help_page() {
		$plugins_arr = get_transient( 'splw_plugins' );
		if ( false === $plugins_arr ) {
			$args = array(
				'author'   => 'shapedplugin',
				'per_page' => '120',
				'page'     => '1',
				'fields'   => array(
					'slug',
					'name',
					'version',
					'downloaded',
					'active_installs',
					'last_updated',
					'rating',
					'num_ratings',
					'short_description',
					'author',
					'icons',
				),
			);

			if ( ! function_exists( 'plugins_api' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			}

			$plugin_info = plugins_api( 'query_plugins', $args );

			if ( ! is_wp_error( $plugin_info ) ) {

				$plugins_arr = array();

				if ( isset( $plugin_info->plugins ) && ( count( $plugin_info->plugins ) > 0 ) ) {
					foreach ( $plugin_info->plugins as $pl ) {
						$plugins_arr[] = array(
							'slug'              => $pl['slug'],
							'name'              => $pl['name'],
							'version'           => $pl['version'],
							'downloaded'        => $pl['downloaded'],
							'active_installs'   => $pl['active_installs'],
							'last_updated'      => strtotime( $pl['last_updated'] ),
							'rating'            => $pl['rating'],
							'num_ratings'       => $pl['num_ratings'],
							'short_description' => $pl['short_description'],
							'icons'             => isset( $pl['icons']['2x'] ) ? $pl['icons']['2x'] : '',
						);
					}
				}

				set_transient( 'splw_plugins', $plugins_arr, 24 * HOUR_IN_SECONDS );
			}
		}

		if ( is_array( $plugins_arr ) && ( count( $plugins_arr ) > 0 ) ) {
			$active_installs = array_column( $plugins_arr, 'active_installs' );
			array_multisort( $active_installs, SORT_DESC, $plugins_arr );
			foreach ( $plugins_arr as $plugin ) {
				$plugin_slug = $plugin['slug'];
				$plugin_icon = $plugin['icons'];
				if ( isset( self::$plugins[ $plugin_slug ] ) ) {
					$plugin_file = self::$plugins[ $plugin_slug ];
				} else {
					$plugin_file = $plugin_slug . '.php';
				}
				// Skip the plugin if it is not installed.
				if ( 'location-weather' === $plugin_slug ) {
					continue;
				}

				$details_link = network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] . '&amp;TB_iframe=true&amp;width=745&amp;height=550' );
				?>
				<div class="plugin-card <?php echo esc_attr( $plugin_slug ); ?>" id="<?php echo esc_attr( $plugin_slug ); ?>">
					<div class="plugin-card-top">
						<div class="name column-name">
							<h3>
								<a class="thickbox" title="<?php echo esc_attr( $plugin['name'] ); ?>" href="<?php echo esc_url( $details_link ); ?>">
									<?php echo esc_html( $plugin['name'] ); ?>
									<img src="<?php echo esc_url( $plugin_icon ); ?>" class="plugin-icon" />
								</a>
							</h3>
						</div>
						<div class="action-links">
							<ul class="plugin-action-buttons">
								<li>
									<?php
									if ( $this->is_plugin_installed( $plugin_slug, $plugin_file ) ) {
										if ( $this->is_plugin_active( $plugin_slug, $plugin_file ) ) {
											?>
											<button type="button" class="button button-disabled" disabled="disabled">Active</button>
											<?php
										} else {
											?>
											<a href="<?php echo esc_url( $this->activate_plugin_link( $plugin_slug, $plugin_file ) ); ?>" class="button button-primary activate-now">
												<?php esc_html_e( 'Activate', 'location-weather' ); ?>
											</a>
											<?php
										}
									} else {
										?>
										<a href="<?php echo esc_url( $this->install_plugin_link( $plugin_slug ) ); ?>" class="button install-now">
											<?php esc_html_e( 'Install Now', 'location-weather' ); ?>
										</a>
									<?php } ?>
								</li>
								<li>
									<?php /* translators: %s: plugin name */ ?>
									<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal" aria-label="<?php echo esc_attr( sprintf( esc_html__( 'More information about %s', 'location-weather' ), $plugin['name'] ) ); ?>" title="<?php echo esc_attr( $plugin['name'] ); ?>">
										<?php esc_html_e( 'More Details', 'location-weather' ); ?>
									</a>
								</li>
							</ul>
						</div>
						<div class="desc column-description">
							<p><?php echo esc_html( isset( $plugin['short_description'] ) ? $plugin['short_description'] : '' ); ?></p>
						</div>
					</div>
					<?php
					echo '<div class="plugin-card-bottom">';

					if ( isset( $plugin['rating'], $plugin['num_ratings'] ) ) {
						?>
						<div class="vers column-rating">
							<?php
							wp_star_rating(
								array(
									'rating' => $plugin['rating'],
									'type'   => 'percent',
									'number' => $plugin['num_ratings'],
								)
							);
							?>
							<span class="num-ratings">(<?php echo esc_html( number_format_i18n( $plugin['num_ratings'] ) ); ?>)</span>
						</div>
						<?php
					}
					if ( isset( $plugin['version'] ) ) {
						?>
						<div class="column-updated">
							<strong><?php esc_html_e( 'Version:', 'location-weather' ); ?></strong>
							<span><?php echo esc_html( $plugin['version'] ); ?></span>
						</div>
						<?php
					}

					if ( isset( $plugin['active_installs'] ) ) {
						?>
						<div class="column-downloaded">
							<?php echo esc_html( number_format_i18n( $plugin['active_installs'] ) ) . esc_html__( '+ Active Installations', 'location-weather' ); ?>
						</div>
						<?php
					}

					if ( isset( $plugin['last_updated'] ) ) {
						?>
						<div class="column-compatibility">
							<strong><?php esc_html_e( 'Last Updated:', 'location-weather' ); ?></strong>
							<span>
								<?php
								printf(
									/* translators: %s: time ago */
									esc_html__( '%s ago', 'location-weather' ),
									esc_html( human_time_diff( $plugin['last_updated'] ) )
								);
								?>
							</span>
						</div>
						<?php
					}

					echo '</div>';
					?>
				</div>
				<?php
			}
		}
	}

	/**
	 * Check plugins installed function.
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return boolean
	 */
	public function is_plugin_installed( $plugin_slug, $plugin_file ) {
		return file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Check active plugin function
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return boolean
	 */
	public function is_plugin_active( $plugin_slug, $plugin_file ) {
		return is_plugin_active( $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Install plugin link.
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @return string
	 */
	public function install_plugin_link( $plugin_slug ) {
		return wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
	}

	/**
	 * Active Plugin Link function
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return string
	 */
	public function activate_plugin_link( $plugin_slug, $plugin_file ) {
		return wp_nonce_url( admin_url( 'edit.php?post_type=location_weather&page=splw_admin_dashboard&action=activate&plugin=' . $plugin_slug . '/' . $plugin_file . '#about_us' ), 'activate-plugin_' . $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Handle AJAX request to get and save user consent.
	 *
	 * This function processes user consent preferences for anonymous data sharing.
	 * It requires user authentication and nonce verification for security.
	 * Updates options for setup wizard visit status, anonymous data sharing preference,
	 * and website type. If consent is granted, it sends collected site data to the
	 * Location Weather SDR service.
	 *
	 * @return void Sends JSON response with success or error message.
	 */
	public function splw_get_user_consent() {

		// Check user capabilities, current_user_can() is called internally.
		location_weather_verify_capability();

		// Verify nonce.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'splw_admin_settings_nonce' ) ) {
			wp_send_json_error( __( 'Invalid nonce.', 'location-weather' ) );
		}

		update_option( 'splw_setup_wizard_visited', true );
		// Get consent.
		$consent      = isset( $_POST['shareData'] ) ? json_decode( wp_unslash( $_POST['shareData'] ), true ) : false;// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON validated via json_decode() and json_last_error().
		$website_type = isset( $_POST['website_type'] ) ? sanitize_text_field( wp_unslash( $_POST['website_type'] ) ) : '';
		update_option( 'sp_ua_site_type', $website_type );
		if ( $consent ) {
			update_option( 'splw_allow_anonymous_data', true );
			$this->splw_send_data_to_lw_sdr( $website_type );
		} else {
			update_option( 'splw_allow_anonymous_data', false );
		}

		wp_send_json_success(
			array(
				'message' => 'Successfully done',
			)
		);
	}

	/**
	 * Send data lw-sdr.
	 *
	 * This function deletes transients and their timeout values associated with the Open Weather API.
	 * It performs nonce verification to ensure the request is legitimate.
	 *
	 *  @param string $website_type Type of the website.
	 */
	public function splw_send_data_to_lw_sdr( $website_type ) {
		// Prevent sending data from local/dev sites.
		$site_url = home_url();
		$host     = wp_parse_url( $site_url, PHP_URL_HOST );
		// Check for local sites.
		if (
		'localhost' === $host ||
		'127.0.0.1' === $host ||
		'::1' === $host ||
		str_ends_with( $host, '.local' ) ||
		str_ends_with( $host, '.test' ) ||
		str_ends_with( $host, '.dev' ) ||
		1 === preg_match( '/^192\.168\./', $host ) ||
		1 === preg_match( '/^10\./', $host ) ||
		1 === preg_match( '/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $host )
		) {
			return;
		}

		$theme = wp_get_theme();
		// PHP version.
		$php_version = phpversion();

		// Database version.
		$db_version = get_option( 'location_weather_db_version' );

		$site_language = get_locale();

		// Active plugins list.
		$active_plugins = array();
		$plugins        = get_plugins();

		foreach ( (array) get_option( 'active_plugins', array() ) as $plugin_path ) {
			if ( isset( $plugins[ $plugin_path ] ) ) {
				$active_plugins[] = array(
					'name'    => $plugins[ $plugin_path ]['Name'],
					'version' => $plugins[ $plugin_path ]['Version'],
				);
			}
		}

		// Get used blocks.
		$used_blocks = array();
		global $wpdb;

		// Search in posts, pages, custom post types, templates, etc.
		$post_contents = $wpdb->get_col( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_content LIKE %s", '%<!-- wp:sp-location-weather-pro/%' ) );
		foreach ( $post_contents as $post_content ) {
			if ( has_blocks( $post_content ) ) {
				$blocks = parse_blocks( $post_content );
				$this->find_splw_blocks_recursive( $blocks, $used_blocks );
			}
		}

		// Search in widgets.
		$widget_blocks = get_option( 'widget_block' );
		if ( ! empty( $widget_blocks ) && is_array( $widget_blocks ) ) {
			foreach ( $widget_blocks as $widget_block ) {
				if ( is_array( $widget_block ) && isset( $widget_block['content'] ) ) {
					if ( has_blocks( $widget_block['content'] ) ) {
						$blocks = parse_blocks( $widget_block['content'] );
						$this->find_splw_blocks_recursive( $blocks, $used_blocks );
					}
				}
			}
		}

		$used_blocks = array_values( $used_blocks );

		// Collect data.
		$data = array(
			'user_email'     => get_option( 'admin_email' ),
			'site_url'       => get_option( 'siteurl' ),
			'site_type'      => $website_type,
			'site_language'  => $site_language,
			'theme_name'     => $theme->get( 'Name' ),
			'plugin_version' => LOCATION_WEATHER_VERSION,
			'wp_version'     => get_bloginfo( 'version' ),
			'php_version'    => $php_version,
			'db_version'     => $db_version,
			'active_plugins' => $active_plugins,
			'used_blocks'    => $used_blocks,
		);

		wp_remote_post(
			'https://api.shapedplugin.com/wp-json/spda/v1/location-weather-free-collect',
			array(
				'headers' => array(
					'Content-Type' => 'application/json',
					'x-api-key'    => '1wr4h53bP1YbavU1rYLq9Y02huCSLpWPgfe2Ds6b',
					'User-Agent'   => 'lw-sdr-collector/' . home_url(),
				),
				'body'    => wp_json_encode( $data ),
			)
		);
	}

	/**
	 * Find blocks by name recursively in a parsed block structure.
	 *
	 * @param array $blocks The array of blocks to search.
	 * @param array $used_blocks The array to store the found block names.
	 * @return void
	 */
	private function find_splw_blocks_recursive( $blocks, &$used_blocks ) {
		foreach ( $blocks as $block ) {
			if ( ! empty( $block['blockName'] ) && strpos( $block['blockName'], 'sp-location-weather-pro/' ) === 0 ) {
				$used_blocks[] = $block['blockName'];
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->find_splw_blocks_recursive( $block['innerBlocks'], $used_blocks );
			}
		}
	}

	/**
	 * Handle user consent submission for anonymous data collection.
	 *
	 * Processes the admin notice form submission, verifies the nonce,
	 * stores the user’s consent choice, and prevents form resubmission
	 * on page refresh.
	 *
	 * @return void
	 */
	public function splw_submit_user_consent() {
		// Handle POST action for the notice buttons.
		if (
			isset( $_POST['splw_anonymous_data_action'] ) && check_admin_referer( 'splw_anonymous_data_action', 'splw_anonymous_data_nonce' )
			) {
			if ( 'allow' === $_POST['splw_anonymous_data_action'] ) {
				update_option( 'splw_allow_anonymous_data', true );
			} elseif ( 'deny' === $_POST['splw_anonymous_data_action'] ) {
				update_option( 'splw_ignored_consent_notice', true );
			}

			// Avoid resubmission on refresh.
			echo '<script>window.location = window.location.href;</script>';
			exit;
		}
	}

	/**
	 * Check whether the consent notice delay period has passed.
	 *
	 * Compares the stored notice start time with the current time
	 * to determine if the specified delay duration has elapsed.
	 *
	 * @param int $days Number of days to wait before showing the notice.
	 * @return bool True if the delay has passed, false otherwise.
	 */
	private function has_notice_delay_passed( $days = 7 ) {
		$start_time = get_option( 'splw_consent_notice_start_time' );

		if ( ! $start_time ) {
			return false;
		}

		return ( time() - $start_time ) >= ( DAY_IN_SECONDS * $days );
	}

	/**
	 * Set the consent notice start time if it does not already exist.
	 *
	 * Stores the current timestamp to track when the consent notice
	 * delay period begins.
	 *
	 * @return void
	 */
	private function maybe_set_notice_start_time() {
		if ( ! get_option( 'splw_consent_notice_start_time' ) ) {
			update_option( 'splw_consent_notice_start_time', time() );
		}
	}

	/**
	 * Conditionally display the anonymous data consent notice.
	 *
	 * Ensures the current user has sufficient permissions, the notice
	 * period has passed before showing the notice.
	 *
	 * @return void
	 */
	public function maybe_show_user_consent_notice() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$ignored_consent_notice = get_option( 'splw_ignored_consent_notice', false );
		$allow_anonymous_data   = get_option( 'splw_allow_anonymous_data', false );

		// Do not show if already allowed or ignored.
		if ( $allow_anonymous_data || $ignored_consent_notice ) {
			return;
		}

		// delay logic (7 days).
		$this->maybe_set_notice_start_time();

		if ( ! $this->has_notice_delay_passed( 7 ) ) {
			return;
		}

		// Finally show notice.
		$this->splw_notice_for_user_consent();
	}

	/**
	 * Render the anonymous data consent admin notice.
	 *
	 * Displays an admin notice prompting the user to allow or deny
	 * anonymous data collection if consent has not yet been given.
	 *
	 * @return void
	 */
	public function splw_notice_for_user_consent() {

		?>
				<style>
					.splw-anonymous-data-notice {
						background-color: #ffffff;
						border: none;
						border: 1px solid rgba(204, 204, 204, 1);
						border-left: 4px solid #FF980F;
						margin-bottom: 20px;
						display: flex;
						padding: 14px;
						align-items: flex-start;
						gap: 16px;
						box-shadow: 0 16px 32px -4px rgba(12, 12, 13, 0.05), 0 4px 4px -4px rgba(12, 12, 13, 0.02);
						position: relative;
						border-radius: 4px;
					}

					button.splw_anonymous_data_cross {
						border: none;
						background: transparent;
						position: absolute;
						top: 0;
						right: 7px;
						cursor: pointer;
						color: #b6b6b6;
						font-size: 16px;
					}

					.splw-anonymous-data-notice-wrapper {
						display: flex;
						gap: 26px;
					}

					.splw-anonymous-data-notice img {
						width: 52px;
						border-radius: 4px;
					}
					.splw-anonymous-data-notice h3 {
						font-weight: 600;
						font-size: 18px;
						color: #2C2D2F;
						margin: 0 0 8px 0;
					}
					.splw-anonymous-data-notice p, .splw-anonymous-data-notice a {
						color: #6E6F72;
						font-size: 14px;
						margin: 0 0 2px 0;
					}
					.splw-anonymous-data-notice a {
						text-decoration: underline;
					}
					.splw-anonymous-data-notice .button {
						font-size: 14px;
						font-weight: 500;
					}
					.splw-anonymous-data-notice .button {
						font-size: 14px;
						font-weight: 500;
						background-color: #ffffff;
						color: #6E6F72;
						border: 1px solid #ECEDF0;
						transition: all 0.2s ease;
						margin-top: 8px;
					}
					.splw-anonymous-data-notice .button:hover {
						border: 1px solid #b7b8bb;
						color: #6E6F72;
						background-color: #ffffff;
					}
					.splw-anonymous-data-notice .splw_anonymous_data_connect {
						background-color: rgba(30, 30, 30, 1);
						color: #ffffff;
						line-height: 14px;
						border-radius: 4px;
						font-size: 13px;
					}
					.splw-anonymous-data-notice .splw_anonymous_data_connect:hover {
						background-color: rgb(46, 46, 46);
						color: #ffffff;
					}
					.splw-anonymous-data-notice .splw_anonymous_data_connect:focus {
						border: none;
						box-shadow: none;
						out-line: none;
					}
				</style>

				<div class="notice notice-info splw-anonymous-data-notice">
					<img src="<?php echo esc_url( LOCATION_WEATHER_ASSETS . '/images/location-weather-icon.gif' ); ?>" alt="Location Weather"/>
					<div class="splw-anonymous-data-notice-wrapper">
						<div>
							<h3>
							<?php esc_html_e( 'Help us make Location Weather even more awesome?', 'location-weather' ); ?>
							</h3>
							<p>
							<?php
							esc_html_e(
								'Allow us to collect non-sensitive diagnostic data to resolve problems faster and improve performance.',
								'location-weather'
							);
							?>
							<a href="https://locationweather.io/information-we-collect/" target="_blank"><?php esc_html_e( 'Learn More', 'location-weather' ); ?></a>
							</p>
						</div>
						<div style="display:flex; gap:10px;">
							<form method="post" style="display:inline;">
							<?php wp_nonce_field( 'splw_anonymous_data_action', 'splw_anonymous_data_nonce' ); ?>
								<input type="hidden" name="splw_anonymous_data_action" value="allow" />
								<button type="submit" class="splw_anonymous_data_connect button">
								<?php esc_html_e( 'Accept & Close', 'location-weather' ); ?>
								</button>
							</form>

							<form method="post" style="display:inline;">
							<?php wp_nonce_field( 'splw_anonymous_data_action', 'splw_anonymous_data_nonce' ); ?>
								<input type="hidden" name="splw_anonymous_data_action" value="deny" />
								<button type="submit" class="splw_anonymous_data_cross dashicons dashicons-dismiss">
								</button>
							</form>
						</div>
					</div>
				</div>

				<?php
	}

	// /**
	// * Initialize saved templates.
	// */
	// public function init_saved_templates() {
	// new \ShapedPlugin\Weather\Admin\LW_Saved_Templates();
	// }
}
