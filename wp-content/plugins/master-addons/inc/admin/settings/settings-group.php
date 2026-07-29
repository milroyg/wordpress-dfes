<?php

namespace MasterAddons\Inc\Admin\Settings;

/**
 * Group-scoped terminal query object
 *
 * Returned by SettingsProxy::group('basic'). Provides
 * scoped queries and bulk operations on a single sub-group:
 *
 *   Settings::instance()->addons->group('basic')->enabled()
 *   Settings::instance()->addons->group('basic')->enable()
 *
 * @package MasterAddons
 * @since 2.1.0
 */
class SettingsGroup
{
    /**
     * Parent proxy
     *
     * @var SettingsProxy
     */
    private $proxy;

    /**
     * Top-level group name (addons, extensions)
     *
     * @var string
     */
    private $type;

    /**
     * Sub-group key (e.g. 'basic', 'marketing')
     *
     * @var string
     */
    private $group_key;

    /**
     * @param SettingsProxy $proxy     Parent proxy
     * @param string        $type      Top-level group (addons, extensions)
     * @param string        $group_key Sub-group key
     */
    public function __construct(SettingsProxy $proxy, $type, $group_key)
    {
        $this->proxy     = $proxy;
        $this->type      = $type;
        $this->group_key = $group_key;
    }

    /**
     * Get all items in this sub-group from Config
     *
     * @return array
     */
    public function all()
    {
        return $this->proxy->get_config_items_by_group($this->group_key);
    }

    /**
     * Get enabled items in this sub-group
     *
     * @return array
     */
    public function enabled()
    {
        $enabled_settings = $this->proxy->get() ?: [];
        $group_items      = $this->all();
        $enabled          = [];

        foreach ($group_items as $key => $item) {
            if (!empty($enabled_settings[$key])) {
                $enabled[$key] = $item;
            }
        }

        return $enabled;
    }

    /**
     * Get counts for this sub-group
     *
     * @return array ['total' => n, 'enabled' => n]
     */
    public function counts()
    {
        $enabled_settings = $this->proxy->get() ?: [];
        $group_items      = $this->all();
        $enabled          = 0;

        foreach (array_keys($group_items) as $key) {
            if (!empty($enabled_settings[$key])) {
                $enabled++;
            }
        }

        return [
            'total'   => count($group_items),
            'enabled' => $enabled,
        ];
    }

    /**
     * Enable all items in this sub-group
     *
     * @return bool
     */
    public function enable()
    {
        return $this->proxy->enable_group($this->group_key);
    }

    /**
     * Disable all items in this sub-group
     *
     * @return bool
     */
    public function disable()
    {
        return $this->proxy->disable_group($this->group_key);
    }
}
