<?php
/**
 * Upgrade routine for 3.1.4.
 *
 * Renames legacy ma_* popup data to the jltma_* prefix:
 *   - custom table {$prefix}ma_popups -> {$prefix}jltma_popups
 *   - option        ma_popup_settings -> jltma_popup_settings
 *
 * Idempotent: safe if the legacy names are already absent.
 */

if (!defined('ABSPATH')) {
	exit;
}

global $wpdb;

$old_table  = $wpdb->prefix . 'ma_popups';
$new_table  = $wpdb->prefix . 'jltma_popups';
$old_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $old_table));
$new_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $new_table));

if ($old_exists === $old_table && $new_exists !== $new_table) {
	// Identifiers cannot be bound; both names are server-generated (prefix + literal), not user input.
	$wpdb->query("RENAME TABLE `{$old_table}` TO `{$new_table}`");
}

$legacy_settings = get_option('ma_popup_settings', null);
if (null !== $legacy_settings && false === get_option('jltma_popup_settings', false)) {
	update_option('jltma_popup_settings', $legacy_settings);
	delete_option('ma_popup_settings');
}
