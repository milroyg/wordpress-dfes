<?php

/**
 * Upgrade script for version 3.0.0
 * Migrates legacy option keys to unified Settings class keys.
 *
 * Runs inside Upgrades::run_updates() via include — $this refers to the Upgrades instance.
 */
namespace MasterAddons\Inc\Classes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use MasterAddons\Inc\Admin\Settings\Settings;

/**
 * Migration map: old key => new key
 */
$legacy_keys = Settings::LEGACY_KEYS;

/**
 * Migrate data from old keys to new keys
 */
foreach ($legacy_keys as $old_key => $new_key) {
    $old_data = get_option($old_key, null);

    if ($old_data !== null && !empty($old_data)) {
        // Only migrate if new key doesn't already have data
        $new_data = get_option($new_key, null);

        if ($new_data === null) {
            update_option($new_key, $old_data);
        }

        // Clean up legacy key
        delete_option($old_key);
    }
}
