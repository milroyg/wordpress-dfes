<?php

namespace MasterAddons\Lib;

// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
	exit;
}

/*
 * Recommended global class
 */

if (!class_exists('Recommended')) {

	/**
	 * Recommended Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 */
	class Recommended
	{


		public $menu_items;
		public $plugins_list;
		public $sub_menu;
		public $menu_order;


		/**
		 * Constructor method
		 *
		 * @param integer $menu_order .
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct($menu_order = 99)
		{
			$this->menu_order   = $menu_order;
			$this->menu_items   = $this->menu_items();
			$this->plugins_list = $this->plugins_list();

			$this->includes();

			add_action('admin_menu', array($this, 'admin_menu'), $this->menu_order);
			add_action('wp_ajax_jltma_recommended_upgrade_plugin', array($this, 'jltma_recommended_upgrade_plugin'));
			add_action('wp_ajax_jltma_recommended_activate_plugin', array($this, 'jltma_recommended_activate_plugin'));
		}

		/**
		 * Includes
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function includes()
		{
			if (!function_exists('install_plugin_install_status')) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			}
		}

		/**
		 * Exact allowlist of installed plugin files that belong to our known slugs.
		 *
		 * Returns real plugin basenames (e.g. "elementor/elementor.php") taken from the
		 * actual installed plugin set, filtered to the slugs we manage. Used to validate
		 * any $_POST plugin-file value before it reaches activate_plugin()/upgrade(), so
		 * arbitrary input can never select an unrelated plugin file.
		 *
		 * @return string[]
		 */
		protected function get_allowed_plugin_files()
		{
			if (!function_exists('get_plugins')) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$known_slugs = array_values(wp_list_pluck($this->plugins_list, 'slug'));
			$allowed     = array();

			foreach (array_keys(get_plugins()) as $plugin_file) {
				if (in_array(dirname($plugin_file), $known_slugs, true)) {
					$allowed[] = $plugin_file;
				}
			}

			return $allowed;
		}

		/**
		 * Menu Items
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function menu_items()
		{
			return array();
		}

		/**
		 * Plugins list
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function plugins_list()
		{
			return array();
		}

		/**
		 * Admin submenu
		 */
		public function admin_menu()
		{
		}

		/**
		 * Render recommended plugins body
		 */
		public function render_recommended_plugins()
		{ ?>
			<div class='jltma-recommended-wrapper'>
				<?php $this->header(); ?>
				<?php $this->body(); ?>
			</div>
			<style>
				/* Hide WordPress default admin notices on this page */
				.jltma-recommended-wrapper ~ .notice,
				.jltma-recommended-wrapper ~ .error,
				.jltma-recommended-wrapper ~ .updated {
					display: none;
				}
			</style>
		<?php
		}

		/**
		 * Header
		 */
		public function header()
		{
		?>
			<div class='jltma-recommended-header'>
				<div class='jltma-recommended-header-top'>
					<div class='jltma-recommended-title'>
						<h2><?php echo esc_html__('Recommended Plugins', 'master-addons'); ?></h2>
						<p><?php echo esc_html__('Starter and recommended plugins to extend your WordPress experience.', 'master-addons'); ?></p>
					</div>
					<div class='jltma-recommended-search'>
						<form class="search-form jltma-search-plugins" method="get">
							<input type="hidden" name="tab" value="search">
							<label class="screen-reader-text" for="search-plugins">
								<?php echo esc_html__('Search Plugins', 'master-addons'); ?>
							</label>
							<span class="jltma-search-icon">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
							</span>
							<input type="search" name="s" id="search-plugins" value="" class="jltma-search-input" placeholder="<?php echo esc_html__('Search plugins...', 'master-addons'); ?>">
						</form>
					</div>
				</div>
				<div class='jltma-recommended-tabs'>
					<ul class="jltma-filter-links">
						<?php
						$i = 0;
						foreach ($this->menu_items as $menu) {
							$class = str_replace(' ', '-', strtolower($menu['key']));
						?>
							<li>
								<a href="#" class="jltma-tab-link <?php echo esc_attr(0 === $i ? 'current' : ''); ?>" data-type="<?php echo esc_attr($menu['key']); ?>"><?php echo esc_html($menu['label']); ?></a>
							</li>
						<?php
							$i++;
						}
						?>
					</ul>
				</div>
			</div>
		<?php
		}

		/**
		 * Body
		 */
		public function body()
		{
		?>
			<div class="jltma-plugins-grid">
				<?php $this->plugins(); ?>
			</div>
			<?php
		}

		/**
		 * Body
		 */
		public function plugins()
		{
			foreach ($this->plugins_list as $key => $plugin) {
				$plugin_api = (object) $plugin;
				if (!isset($plugin_api->version)) {
					$plugin_api->version = '';
				}
				$install_status = \install_plugin_install_status($plugin_api);
				$classes        = implode(' ', $plugin['type']);

				$more_details = self_admin_url(
					'plugin-install.php?tab=plugin-information&amp;plugin=' . esc_attr($plugin['slug']) .
						'&amp;TB_iframe=true&amp;width=600&amp;height=550'
				);
			?>
				<div class="jltma-plugin-card <?php echo esc_attr($classes); ?>" data-plugin="<?php echo esc_attr($key); ?>">
					<div class="jltma-plugin-card-body">
						<div class="jltma-plugin-icon">
							<img src="<?php echo esc_url($plugin['icon']); ?>" alt="<?php echo esc_attr($plugin['name']); ?>">
						</div>
						<div class="jltma-plugin-info">
							<h3 class="jltma-plugin-name">
								<a href="<?php echo esc_url($more_details); ?>" class="thickbox open-plugin-details-modal"><?php echo esc_html($plugin['name']); ?></a>
							</h3>
							<p class="jltma-plugin-desc"><?php echo wp_kses_post($plugin['short_description']); ?></p>
						</div>
					</div>
					<div class="jltma-plugin-card-footer">
						<span class="jltma-plugin-status">
							<?php
							if ('install' === $install_status['status']) {
							?>
								<span class="jltma-status-badge jltma-status-not-installed" data-plugin-url="<?php echo esc_attr($plugin['download_link']); ?>"><?php echo esc_html__('Not Installed', 'master-addons'); ?></span>
							<?php
							} elseif ('update_available' === $install_status['status']) {
								if (is_plugin_active($install_status['file'])) {
							?>
									<span class="jltma-status-badge jltma-status-active"><?php echo esc_html__('Active', 'master-addons'); ?></span>
								<?php
								} else {
								?>
									<span class="jltma-status-badge jltma-status-inactive" data-plugin-file="<?php echo esc_attr($install_status['file']); ?>"><?php echo esc_html__('Inactive', 'master-addons'); ?></span>
							<?php
								}
							} elseif (('latest_installed' === $install_status['status']) || ('newer_installed' === $install_status['status'])) {
								if (is_plugin_active($install_status['file'])) {
							?>
									<span class="jltma-status-badge jltma-status-active"><?php echo esc_html__('Active', 'master-addons'); ?></span>
								<?php
								} else {
								?>
									<span class="jltma-status-badge jltma-status-inactive" data-plugin-file="<?php echo esc_attr($install_status['file']); ?>"><?php echo esc_html__('Inactive', 'master-addons'); ?></span>
							<?php
								}
							}
							?>
						</span>
						<div class="jltma-plugin-action">
							<?php
							if ('install' === $install_status['status']) {
							?>
								<button class="install-now jltma-btn jltma-btn-primary" data-install-url="<?php echo esc_attr($plugin['download_link']); ?>">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
									<?php echo esc_html__('Install', 'master-addons'); ?>
								</button>
							<?php
							} elseif ('update_available' === $install_status['status']) {
							?>
								<button class="update-now jltma-btn jltma-btn-warning" data-plugin="<?php echo esc_attr($install_status['file']); ?>" data-slug="<?php echo esc_attr($plugin['slug']); ?>" data-update-url="<?php echo esc_attr($install_status['url']); ?>">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/></svg>
									<?php echo esc_html__('Update', 'master-addons'); ?>
								</button>
							<?php
							} elseif (('latest_installed' === $install_status['status']) || ('newer_installed' === $install_status['status'])) {
								if (is_plugin_active($install_status['file'])) {
							?>
									<button type="button" class="jltma-btn jltma-btn-activated" disabled="disabled">
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
										<?php echo esc_html__('Activated', 'master-addons'); ?>
									</button>
								<?php
								} elseif (current_user_can('activate_plugin', $install_status['file'])) {
								?>
									<button class="activate-now jltma-btn jltma-btn-success" data-plugin-file="<?php echo esc_attr($install_status['file']); ?>">
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
										<?php echo esc_html__('Activate', 'master-addons'); ?>
									</button>
								<?php
								} else {
								?>
									<button type="button" class="jltma-btn jltma-btn-activated" disabled="disabled">
										<?php echo esc_html__('Installed', 'master-addons'); ?>
									</button>
							<?php
								}
							}
							?>
						</div>
					</div>
				</div>
<?php
			}
		}

		/**
		 * Activate Plugins
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function jltma_recommended_activate_plugin()
		{
			try {
				if (isset($_POST['file'])) {
					$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

					if (!wp_verify_nonce($nonce, 'jltma_recommended_nonce')) {
						wp_send_json_error(array('mess' => __('Nonce is invalid', 'master-addons')));
					}

					if ((is_multisite() && !is_network_admin()) || !current_user_can('install_plugins')) {
						wp_send_json_error(array('mess' => __('Invalid access', 'master-addons')));
					}

					$file = sanitize_text_field(wp_unslash($_POST['file']));

					// Enforce an exact allowlist of real installed plugin files that
					// belong to our known slugs. Arbitrary input (or a crafted basename
					// whose dirname happens to match a slug) must never reach
					// activate_plugin(); only an exact installed plugin file passes.
					if (!in_array($file, $this->get_allowed_plugin_files(), true)) {
						wp_send_json_error(array('mess' => __('Invalid plugin', 'master-addons')));
					}

					$result = activate_plugin($file);

					if (is_wp_error($result)) {
						wp_send_json_error(
							array(
								'mess' => $result->get_error_message(),
							)
						);
					}
					wp_send_json_success(
						array(
							'mess' => __('Activate success', 'master-addons'),
						)
					);
				}
			} catch (\Exception $ex) {
				wp_send_json_error(
					array(
						'mess' => $ex->getMessage(),
					)
				);
			} catch (\Error $ex) {
				wp_send_json_error(
					array(
						'mess' => $ex->getMessage(),
					)
				);
			}
		}

		/**
		 * Upgrade Plugins required Libraries
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function jltma_recommended_upgrade_plugin()
		{
			try {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
				require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

				if (isset($_POST['plugin'])) {
					$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

					if (!wp_verify_nonce($nonce, 'jltma_recommended_nonce')) {
						wp_send_json_error(array('mess' => __('Nonce is invalid', 'master-addons')));
					}

					if ((is_multisite() && !is_network_admin()) || !current_user_can('install_plugins')) {
						wp_send_json_error(array('mess' => __('Invalid access', 'master-addons')));
					}

					$plugin = sanitize_text_field(wp_unslash($_POST['plugin']));
					$type   = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : 'install';

					// Validate against an exact allowlist that depends on the operation:
					//  - install: $plugin is a package source; must be exactly one of our
					//    known download links.
					//  - upgrade: $plugin is a plugin file; must be exactly one of the real
					//    installed plugin files under our known slugs.
					// dirname()-based matching is intentionally NOT used — it would accept a
					// crafted value whose directory merely resembles a known slug.
					if ('install' === $type) {
						$plugin_links = array_values(wp_list_pluck($this->plugins_list, 'download_link'));
						if (!in_array($plugin, $plugin_links, true)) {
							wp_send_json_error(array('mess' => __('Invalid plugin', 'master-addons')));
						}
					} else {
						if (!in_array($plugin, $this->get_allowed_plugin_files(), true)) {
							wp_send_json_error(array('mess' => __('Invalid plugin', 'master-addons')));
						}
					}

					$skin     = new \WP_Ajax_Upgrader_Skin();
					$upgrader = new \Plugin_Upgrader($skin);

					if ('install' === $type) {
						$result = $upgrader->install($plugin);

						if (is_wp_error($result)) {
							wp_send_json_error(
								array(
									'mess' => $result->get_error_message(),
								)
							);
						}
						$args        = array(
							'slug'   => $upgrader->result['destination_name'],
							'fields' => array(
								'short_description' => true,
								'icons'             => true,
								'banners'           => false,
								'added'             => false,
								'reviews'           => false,
								'sections'          => false,
								'requires'          => false,
								'rating'            => false,
								'ratings'           => false,
								'downloaded'        => false,
								'last_updated'      => false,
								'added'             => false,
								'tags'              => false,
								'compatibility'     => false,
								'homepage'          => false,
								'donate_link'       => false,
							),
						);
						$plugin_data = plugins_api('plugin_information', $args);

						if ($plugin_data && !is_wp_error($plugin_data)) {
							$install_status = \install_plugin_install_status($plugin_data);
							$active_plugin  = activate_plugin($install_status['file']);

							if (is_wp_error($active_plugin)) {
								wp_send_json_error(
									array(
										'mess' => $active_plugin->get_error_message(),
									)
								);
							} else {
								wp_send_json_success(
									array(
										'mess' => __('Install success', 'master-addons'),
									)
								);
							}
						} else {
							wp_send_json_error(
								array(
									'mess' => 'Error',
								)
							);
						}
					} else {
						$is_active = is_plugin_active($plugin);
						$result    = $upgrader->upgrade($plugin);

						if (is_wp_error($result)) {
							wp_send_json_error(
								array(
									'mess' => $result->get_error_message(),
								)
							);
						} else {
							activate_plugin($plugin);
							wp_send_json_success(
								array(
									'mess'   => __('Update success', 'master-addons'),
									'active' => $is_active,
								)
							);
						}
					}
				}
			} catch (\Exception $ex) {
				wp_send_json_error(
					array(
						'mess' => $ex->getMessage(),
					)
				);
			} catch (\Error $ex) {
				wp_send_json_error(
					array(
						'mess' => $ex->getMessage(),
					)
				);
			}
		}
	}
}
