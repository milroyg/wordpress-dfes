<?php
/**
 * Upgrade routine for 3.2.2.
 *
 * Carries widget settings across control keys renamed in this release --
 * see inc/classes/upgrades/renames/3.2.2/ for the list, one file per widget.
 * A renamed control leaves its value under a key nothing reads any more, so a
 * page built before the rename would quietly fall back to the control's
 * default.
 *
 * Idempotent: pages with nothing left to rename are read and left alone.
 */

use MasterAddons\Inc\Classes\Upgrades\Control_Key_Migrator;

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('MasterAddons\Inc\Classes\Upgrades\Control_Key_Migrator')) {
	// Only this release's renames -- the files under renames/3.2.2/.
	Control_Key_Migrator::run('3.2.2');
}
