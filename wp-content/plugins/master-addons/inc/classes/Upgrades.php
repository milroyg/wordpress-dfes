<?php

namespace MasterAddons\Inc\Classes;

class Upgrades
{

	/**
	 * Plugin version option key
	 *
	 * @var string $option_name
	 */
	protected $option_name = '_master_addons_version';

	/**
	 * Lists of upgrades
	 *
	 * @var string[] $upgrades
	 */
	protected $upgrades = [
		'2.0.8.9'   => 'Upgrades/upgrade-2.0.8.9.php',
	];

	/**
	 * Get plugin installed version
	 *
	 * @return string
	 */
	protected function get_installed_version()
	{
		return get_option($this->option_name, '1.0.0');
	}

	/**
	 * Check if plugin's update is available
	 *
	 * @return bool
	 */
	public function if_updates_available()
	{
		if (version_compare($this->get_installed_version(), JLTMA_VER, '<')) {
			return true;
		}

		return false;
	}

	/**
	 * Run plugin updates
	 *
	 * @return void
	 */
	public function run_updates()
	{
		$installed_version = $this->get_installed_version();
		$path              = trailingslashit(__DIR__);

		foreach ($this->upgrades as $version => $file) {
			if (version_compare($installed_version, $version, '<')) {
				include $path . $file;
			}
		}

		// update_option( $this->option_name, JLTMA_VER );
	}
}
