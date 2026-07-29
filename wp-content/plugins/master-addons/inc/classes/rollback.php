<?php

namespace MasterAddons\Inc\Classes;
use MasterAddons\Inc\Classes\Helper;

if (!defined('ABSPATH')) exit;

/**
 * Master Addons Rollback
 *
 * Allows rolling back to previous versions of the free plugin from WordPress.org.
 * This feature is only available for the free version installed from WordPress.org.
 * Pro versions should use Freemius for version management.
 */
class Rollback
{

	protected $package_url;

	protected $version;

	protected $plugin_name;

	protected $plugin_slug;

	private static $instance = null;

	public function __construct($args = [])
	{
		// Rollback is only available for free version from WordPress.org
		// Pro users should use Freemius account for version management
		if (Helper::jltma_premium()) {
			return;
		}

		$this->plugin_name = JLTMA;
		add_action('admin_post_master_addons_rollback', [$this, 'jltma_post_addons_rollback']);

		foreach ($args as $key => $value) {
			$this->{$key} = $value;
		}
	}


	/**
	 *  Rollback function
	 */
	public function jltma_post_addons_rollback()
	{

		check_admin_referer('master_addons_rollback');

		if (!current_user_can('update_plugins')) {
			wp_die(esc_html__('You are not allowed to roll back this plugin.', 'master-addons'));
		}

		$rollback_versions = $this->get_rollback_versions();

		if (empty($_GET['version']) || !in_array($_GET['version'], $rollback_versions)) {
			wp_die(esc_html__('Error occurred, The version selected is invalid. Try selecting different version.', 'master-addons' ));
		}

		$plugin_slug = basename(JLTMA_BASE, '.php');
		$plugin_version = sanitize_text_field( wp_unslash( $_GET['version'] ) );

		$jltma_rollback = new self(
			[
				'version'     => $plugin_version,
				'plugin_name' => $this->plugin_name,
				'plugin_slug' => $plugin_slug,
				'package_url' => sprintf('https://downloads.wordpress.org/plugin/%s.%s.zip', $plugin_slug, $plugin_version),
			]
		);

		$jltma_rollback->run();

		wp_die('', esc_html__('Rollback to Previous Version', 'master-addons' ), ['response' => 200,]);
	}




	public function get_rollback_versions()
	{
		$rollback_versions = get_transient('master_addons_rollbacks_' . JLTMA_VER);
		if (false === $rollback_versions) {
			$max_versions = 30;

			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

			$plugin_information = plugins_api(
				'plugin_information',
				[
					'slug' => 'master-addons',
				]
			);

			if (empty($plugin_information->versions) || !is_array($plugin_information->versions)) {
				return [];
			}

			krsort($plugin_information->versions);

			$rollback_versions = [];

			$current_index = 0;
			foreach ($plugin_information->versions as $version => $download_link) {
				if ($max_versions <= $current_index) {
					break;
				}

				$lowercase_version = strtolower($version);
				$is_valid_rollback_version = !preg_match('/(trunk|beta|rc|dev)/i', $lowercase_version);

				$is_valid_rollback_version = apply_filters(
					'master_addons/options/rollback/is_valid_rollback_version',
					$is_valid_rollback_version,
					$lowercase_version
				);

				if (!$is_valid_rollback_version) {
					continue;
				}

				if (version_compare($version, JLTMA_VER, '>=')) {
					continue;
				}

				$current_index++;
				$rollback_versions[] = $version;
			}

			set_transient('master_addons_rollbacks_' . JLTMA_VER, $rollback_versions, WEEK_IN_SECONDS);
		}

		return $rollback_versions;
	}



	private function print_inline_style()
	{
?>
		<style>
			.wrap {
				overflow: hidden;
			}

			h1 {
				background: #0347FF;
				text-align: center;
				color: #fff !important;
				padding: 70px !important;
				text-transform: uppercase;
				letter-spacing: 1px;
			}

			h1 img {
				max-width: 300px;
				display: block;
				margin: auto auto 50px;
			}
		</style>

<?php
	}

	protected function apply_package()
	{

		$update_plugins = get_site_transient('update_plugins');

		if (!is_object($update_plugins)) {

			$update_plugins = new \stdClass();
		}

		$plugin_info = new \stdClass();

		$plugin_info->new_version = $this->version;

		$plugin_info->slug = $this->plugin_slug;

		$plugin_info->package = $this->package_url;

		$plugin_info->url = 'https://master-addons.com/';

		$update_plugins->response[$this->plugin_name] = $plugin_info;

		set_site_transient('update_plugins', $update_plugins);
	}

	protected function upgrade()
	{

		require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');

		$logo_url = JLTMA_IMAGE_DIR . 'logo.png';

		$upgrader_args = [
			'url' => 'update.php?action=upgrade-plugin&plugin=' . rawurlencode($this->plugin_name),
			'plugin' => $this->plugin_name,
			'nonce' => 'upgrade-plugin_' . $this->plugin_name,
			'title' => '<img src="' . esc_url($logo_url) . '" alt="Master Addons Version Rollback">' . __('Rollback to Previous Version ', 'master-addons' ),
		];

		$this->print_inline_style();

		$upgrader = new \Plugin_Upgrader(new \Plugin_Upgrader_Skin($upgrader_args));

		$upgrader->upgrade($this->plugin_name);
	}

	public function run()
	{

		$this->apply_package();

		$this->upgrade();
	}

	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

Rollback::get_instance();
