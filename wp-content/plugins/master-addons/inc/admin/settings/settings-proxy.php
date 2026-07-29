<?php

namespace MasterAddons\Inc\Admin\Settings;

use MasterAddons\Inc\Admin\Config;

/**
 * Per-group fluent proxy for Settings
 *
 * Provides chainable access to a single settings group:
 *   Settings::instance()->addons->get('key')
 *   Settings::instance()->addons->is_enabled('key')
 *   Settings::instance()->addons->group('basic')->enabled()
 *
 * @package MasterAddons
 * @since 2.1.0
 */
class SettingsProxy
{
    /**
     * Group name (addons, extensions, plugins, icons, api)
     *
     * @var string
     */
    private $group;

    /**
     * Option key for this group
     *
     * @var string
     */
    private $option_key;

    /**
     * Groups that support sub-group operations
     *
     * @var array
     */
    private const GROUPABLE = ['addons', 'extensions'];

    /**
     * @param string $group      Group name
     * @param string $option_key WordPress option key
     */
    public function __construct($group, $option_key)
    {
        $this->group      = $group;
        $this->option_key = $option_key;
    }

    /**
     * Get setting value(s)
     *
     * @param string|null $key     Specific key or null for all
     * @param mixed       $default Default value
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        return Settings::get_option($this->option_key, $key, $default);
    }

    /**
     * Save settings
     *
     * @param array $data Settings array
     * @return bool
     */
    public function save(array $data)
    {
        return Settings::save_option($this->option_key, $data);
    }

    /**
     * Check if an item is enabled
     *
     * @param string $key Item key
     * @return bool
     */
    public function is_enabled($key)
    {
        return (bool) $this->get($key, true);
    }

    /**
     * Get all enabled items with full config data
     *
     * Merges DB enabled state with Config definitions.
     *
     * @return array
     */
    public function enabled()
    {
        $enabled_settings = $this->get() ?: [];
        $all_items        = $this->get_config_items();
        $enabled          = [];

        foreach ($all_items as $key => $item) {
            if (!empty($enabled_settings[$key])) {
                $enabled[$key] = $item;
            }
        }

        return $enabled;
    }

    /**
     * Get default settings array
     *
     * @return array
     */
    public function defaults()
    {
        $all_items = $this->get_config_items();
        $defaults  = [];

        foreach (array_keys($all_items) as $key) {
            // Mega menu disabled by default for extensions
            if ($this->group === 'extensions' && $key === 'mega-menu') {
                $defaults[$key] = false;
            } else {
                $defaults[$key] = true;
            }
        }

        return $defaults;
    }

    /**
     * Get counts by sub-group (addons/extensions only)
     *
     * @return array ['group_key' => ['total' => n, 'enabled' => n]]
     */
    public function counts()
    {
        $this->require_groupable('counts');

        $enabled_settings = $this->get() ?: [];
        $groups           = $this->get_config_groups();
        $counts           = [];

        foreach (array_keys($groups) as $group_key) {
            $group_items = $this->get_config_items_by_group($group_key);
            $enabled     = 0;

            foreach (array_keys($group_items) as $key) {
                if (!empty($enabled_settings[$key])) {
                    $enabled++;
                }
            }

            $counts[$group_key] = [
                'total'   => count($group_items),
                'enabled' => $enabled,
            ];
        }

        return $counts;
    }

    /**
     * Get a group-scoped query object
     *
     * @param string $group_key Sub-group key (e.g. 'basic', 'marketing')
     * @return SettingsGroup
     */
    public function group($group_key)
    {
        $this->require_groupable('group');
        $group_key = sanitize_key($group_key);

        return new SettingsGroup($this, $this->group, $group_key);
    }

    /**
     * Enable all items in a sub-group
     *
     * @param string $group_key Sub-group key
     * @return bool
     */
    public function enable_group($group_key)
    {
        $this->require_groupable('enable_group');
        $group_key = sanitize_key($group_key);

        $current     = $this->get() ?: [];
        $group_items = $this->get_config_items_by_group($group_key);

        foreach (array_keys($group_items) as $key) {
            $current[$key] = true;
        }

        return $this->save($current);
    }

    /**
     * Disable all items in a sub-group
     *
     * @param string $group_key Sub-group key
     * @return bool
     */
    public function disable_group($group_key)
    {
        $this->require_groupable('disable_group');
        $group_key = sanitize_key($group_key);

        $current     = $this->get() ?: [];
        $group_items = $this->get_config_items_by_group($group_key);

        foreach (array_keys($group_items) as $key) {
            $current[$key] = false;
        }

        return $this->save($current);
    }

    /**
     * Get all config items for this group
     *
     * @return array
     */
    public function get_config_items()
    {
        switch ($this->group) {
            case 'addons':
                return Config::get_addons();
            case 'extensions':
                return Config::get_extensions();
            default:
                return [];
        }
    }

    /**
     * Get config items by sub-group
     *
     * @param string $group_key
     * @return array
     */
    public function get_config_items_by_group($group_key)
    {
        switch ($this->group) {
            case 'addons':
                return Config::get_addons_by_group($group_key);
            case 'extensions':
                return Config::get_extensions_by_group($group_key);
            default:
                return [];
        }
    }

    /**
     * Get config groups
     *
     * @return array
     */
    private function get_config_groups()
    {
        switch ($this->group) {
            case 'addons':
                return Config::get_addons_grouped();
            case 'extensions':
                return Config::get_extensions_grouped();
            default:
                return [];
        }
    }

    /**
     * Guard: throw if this group doesn't support sub-group operations
     *
     * @param string $method Method name for error message
     * @throws \BadMethodCallException
     */
    private function require_groupable($method)
    {
        if (!in_array($this->group, self::GROUPABLE, true)) {
            throw new \BadMethodCallException(
                sprintf('%s() is not available for the "%s" settings group.', $method, $this->group) // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message, not direct output
            );
        }
    }
}
