<?php

namespace MasterAddons\Inc\Classes;

if (!defined('ABSPATH')) {
    exit;
}

// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_rmdir, WordPress.WP.AlternativeFunctions.file_system_operations_unlink -- Removing our own cache tree under uploads; WP_Filesystem credential prompts are not acceptable during a background upgrade routine.

/**
 * Removes the local template-library mirror from uploads/master_addons.
 *
 * Client sites read the library from the remote API now, so the mirror is dead
 * weight — on small hosting plans it was filling the disk and failing writes.
 * This runs once per site, in the background, and only for the directories that
 * mirrored remote data.
 *
 * IT MUST NOT RUN ON el.master-addons.com. That site is the SOURCE of the
 * library: the same directories there hold published kits, purchased-kit
 * payloads and generated zips. Three independent guards keep it out:
 *
 *   1. the master-addons-site-importer plugin (which only exists on the
 *      library server) sets jltma_remove_local_template_cache to false;
 *   2. JLTMA_IMPORTER being defined is treated as the same signal, so the
 *      guard holds even if the filter is registered late;
 *   3. any site can opt out with
 *      add_filter('jltma_remove_local_template_cache', '__return_false').
 */
class Local_Cache_Cleanup
{
    const DONE_OPTION = 'jltma_local_cache_removed';
    const EVENT       = 'jltma_remove_local_cache';

    /**
     * Bumped when the cleanup itself changes, so sites that already ran an
     * older pass run the new one once. Version 2 removed the empty directory
     * tree the library cache used to recreate on every load; version 3 takes
     * assets_cache and the master_addons root with it.
     */
    const VERSION = 3;

    /**
     * Everything this plugin used to keep under uploads/master_addons.
     *
     * The template and kit directories are mirrors of the remote library and
     * are refetched from the API on demand. assets_cache holds bundled CSS and
     * JS for this site's own pages, which is regenerated -- into post meta now
     * that the directory is not created -- so it goes as well: the plugin
     * leaves no folders on a client site.
     */
    const MIRROR_DIRS = [
        'templates-library',
        'templates_kits',
        'purchased_kits',
        'templates_kit',
        'assets_cache',
    ];

    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_init', [$this, 'maybe_schedule']);
        add_action(self::EVENT, [$this, 'run']);
    }

    /**
     * Delete the mirror straight away, without waiting for cron.
     *
     * Called from the plugin activation hook: deactivating and reactivating is
     * what a site owner does when they want the plugin to sort itself out, and
     * telling them to wait a minute for WP-Cron -- which only fires on the next
     * page view, and not at all when DISABLE_WP_CRON is set -- is not an
     * answer. Deleting a few thousand files takes well under a second once the
     * first pass is done, and on activation there is no page render to hold up.
     */
    public static function purge_now()
    {
        return self::get_instance()->run();
    }

    /**
     * Is this site allowed to delete its local mirror?
     */
    public static function allowed()
    {
        // The library server ships master-addons-site-importer; its uploads
        // tree is the real library, not a mirror of one.
        if (defined('JLTMA_IMPORTER')) {
            return false;
        }

        return (bool) apply_filters('jltma_remove_local_template_cache', true);
    }

    /**
     * Has this site run the current version of the cleanup?
     */
    private static function needs_run()
    {
        $done = get_option(self::DONE_OPTION);
        if (!$done) {
            return true;
        }
        $version = is_array($done) && isset($done['version']) ? (int) $done['version'] : 1;
        if ($version < self::VERSION) {
            return true;
        }

        // A finished run is not a promise the directories stay gone: any code
        // path that still creates one puts it back, and a one-shot cleanup
        // would then never look again. Four is_dir() calls per admin request is
        // cheap enough to keep checking.
        return self::has_leftovers();
    }

    /**
     * Is any mirror directory still on disk?
     */
    private static function has_leftovers()
    {
        $upload_dir = wp_upload_dir(null, false);
        if (empty($upload_dir['basedir'])) {
            return false;
        }

        $root = trailingslashit($upload_dir['basedir']) . 'master_addons';
        foreach (self::MIRROR_DIRS as $dir) {
            if (is_dir($root . '/' . $dir)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Queue the cleanup once. Deleting a few thousand cached files is not
     * something to do inside an admin page load, so it goes to cron.
     */
    public function maybe_schedule()
    {
        if (!self::allowed() || !self::needs_run()) {
            return;
        }

        // Cron is not guaranteed: DISABLE_WP_CRON, a broken loopback request or
        // a site nobody visits on the front end all leave the event pending
        // forever, and the mirror with it. If an earlier attempt is already
        // overdue, stop waiting for it and delete inline.
        $scheduled = wp_next_scheduled(self::EVENT);
        if ($scheduled) {
            if ($scheduled < time() - 5 * MINUTE_IN_SECONDS) {
                wp_unschedule_event($scheduled, self::EVENT);
                $this->run();
            }
            return;
        }

        wp_schedule_single_event(time() + MINUTE_IN_SECONDS, self::EVENT);
    }

    /**
     * Delete the mirror directories, then mark the site done so this never
     * runs twice.
     *
     * @return array dir => files removed
     */
    public function run()
    {
        if (!self::allowed()) {
            return [];
        }

        $upload_dir = wp_upload_dir(null, false);
        $basedir    = !empty($upload_dir['basedir']) ? $upload_dir['basedir'] : '';
        $removed    = [];

        if ($basedir) {
            $root = trailingslashit($basedir) . 'master_addons';
            foreach (self::MIRROR_DIRS as $dir) {
                $removed[$dir] = $this->delete_tree($root . '/' . $dir);
            }

            // The mirror directories are gone, but master_addons itself may now
            // hold nothing but the index.php/.htaccess stubs the cache used to
            // drop in. A client should not be left with an empty tree, so the
            // root goes too -- unless a directory this plugin did not create is
            // still sitting in it.
            $removed['root'] = $this->remove_if_empty($root) ? 1 : 0;
        }

        $this->clear_mirror_cron();

        update_option(self::DONE_OPTION, [
            'time'    => time(),
            'version' => self::VERSION,
            'removed' => $removed,
        ], false);

        return $removed;
    }

    /**
     * Drop the scheduled events whose only job is to refill the mirror.
     *
     * update_templates_cache() and update_template_kits_cache() exist to walk
     * the remote library and write it to disk. With local caching off they have
     * nothing to write, so leaving them scheduled just wakes the site up to do
     * nothing -- and any future path that forgets its gate would refill the
     * tree from a cron nobody is watching.
     */
    private function clear_mirror_cron()
    {
        foreach (['jltma_templates_cache_update', 'jltma_template_kits_cache_update'] as $hook) {
            if (function_exists('wp_unschedule_hook')) {
                wp_unschedule_hook($hook);
                continue;
            }
            while ($timestamp = wp_next_scheduled($hook)) {
                wp_unschedule_event($timestamp, $hook);
            }
        }
    }

    /**
     * Recursively delete a directory. Returns the number of files removed.
     */
    private function delete_tree($dir)
    {
        if (!is_dir($dir)) {
            return 0;
        }

        // Never step outside uploads, whatever the caller passed in.
        $upload_dir = wp_upload_dir(null, false);
        $basedir    = !empty($upload_dir['basedir']) ? realpath($upload_dir['basedir']) : '';
        $real       = realpath($dir);
        if (!$basedir || !$real || 0 !== strpos($real, $basedir)) {
            return 0;
        }

        $files = 0;
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($real, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } else {
                if (@unlink($item->getPathname())) {
                    $files++;
                }
            }
        }

        @rmdir($real);

        return $files;
    }

    /**
     * Delete a directory that holds nothing of the site's own.
     *
     * The placeholder files the cache wrote (index.php, .htaccess) do not count
     * as content -- they only existed to protect the cache tree that is now
     * gone. Anything else, a real directory above all, keeps the root alive.
     *
     * @return bool whether the directory was removed.
     */
    private function remove_if_empty($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        // index.php/.htaccess were written to protect a tree that is gone;
        // .DS_Store and Thumbs.db are the operating system's litter. None of
        // them is a reason to keep the folder standing.
        $stubs   = ['index.php', 'index.html', '.htaccess', '.DS_Store', 'Thumbs.db'];
        $entries = @scandir($dir);
        if (false === $entries) {
            return false;
        }

        $found = [];
        foreach ($entries as $entry) {
            if ('.' === $entry || '..' === $entry) {
                continue;
            }
            if (is_dir($dir . '/' . $entry) || !in_array($entry, $stubs, true)) {
                return false;
            }
            $found[] = $dir . '/' . $entry;
        }

        foreach ($found as $file) {
            @unlink($file);
        }

        return (bool) @rmdir($dir);
    }
}
