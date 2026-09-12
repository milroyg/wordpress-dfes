<?php

namespace MasterAddons\Inc\Classes\Upgrades;

use Elementor\Plugin as Elementor_Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Renamed widget control keys.
 *
 * A control's name is the key its value is saved under, so renaming one leaves
 * every page already built with that widget holding a value nothing reads any
 * more -- the widget falls back to the control's default and the page changes
 * under the user. This carries those values across.
 *
 * Two layers, because a value has two readers:
 *
 *   - The saved page data, rewritten once by the upgrade routine that calls
 *     run(). This is the one that matters for the CSS Elementor generates from
 *     a control's `selectors` and `prefix_class`, which is built from the saved
 *     data and never passes through the widget's PHP.
 *   - The widget's own reading of its settings, patched on the fly by
 *     rename_settings(). That covers a page the routine has not reached yet, a
 *     template imported from an older export, and anything restored from a
 *     revision.
 *
 * The renames themselves live beside this file, one file per widget per
 * release: renames/<release>/<widget>-<release>.php, each returning the map for
 * that widget and documenting the shape of an entry. Adding a rename is a line
 * in the file for that release -- a new folder when the release is new -- plus
 * registering an upgrade file for it (see inc/classes/upgrades.php). This class
 * only applies what it finds there, so it stays the same size however many
 * releases rename controls.
 */
class Control_Key_Migrator
{
    /**
     * A control whose value is dropped rather than carried anywhere.
     */
    const DROPPED = null;

    /**
     * The keys a group control saves under, as <name>_<field>.
     *
     * A group control is one control in the panel and a handful of keys in the
     * saved data, so renaming one is renaming every field it writes. Listed
     * rather than matched by prefix: `dropdown_item_border` and
     * `dropdown_item_border_radius` are two different controls, and a prefix
     * would carry the second away with the first.
     */
    const GROUP_FIELDS = array(
        'typography' => array(
            'typography',
            'font_family',
            'font_size',
            'font_weight',
            'text_transform',
            'font_style',
            'text_decoration',
            'line_height',
            'letter_spacing',
            'word_spacing',
        ),
        'border' => array('border', 'width', 'color'),
        'box_shadow' => array('box_shadow_type', 'box_shadow', 'box_shadow_position'),
        'text_shadow' => array('text_shadow_type', 'text_shadow'),
    );

    /**
     * Every side of a dimensions control, in Elementor's order.
     */
    const ALL_SIDES = array('top', 'right', 'bottom', 'left');

    /**
     * Where the rename files live: renames/<release>/<widget>-<release>.php,
     * each returning the map for one widget in one release.
     *
     * Kept out of this class so a release's renames are a file of their own
     * rather than another screenful here -- the logic that applies them does
     * not grow when a control is renamed, and neither should this file.
     *
     * @return string
     */
    private static function renames_dir()
    {
        return __DIR__ . '/renames';
    }

    /**
     * Every rename file, read once, as release => widget => map.
     *
     * Releases come back oldest first, so a key renamed twice is carried the
     * whole way: a => b from one release and b => c from a later one are
     * applied in that order, landing on c.
     *
     * @return array
     */
    private static function rename_files()
    {
        static $releases = null;

        if (null !== $releases) {
            return $releases;
        }

        $releases = array();

        foreach ((array) glob(self::renames_dir() . '/*', GLOB_ONLYDIR) as $dir) {
            $release = basename($dir);
            $suffix = '-' . $release;

            foreach ((array) glob($dir . '/*.php') as $file) {
                $name = basename($file, '.php');

                // The file names the widget and repeats the release it belongs
                // to, so one dropped in the wrong folder is skipped rather than
                // migrating a site under a release it was not written for.
                if ($suffix !== substr($name, -strlen($suffix))) {
                    continue;
                }

                $widget = substr($name, 0, -strlen($suffix));
                $map = require $file;

                if ('' === $widget || !is_array($map)) {
                    continue;
                }

                $releases[$release][$widget] = isset($releases[$release][$widget])
                    ? array_merge($releases[$release][$widget], $map)
                    : $map;
            }
        }

        uksort($releases, 'version_compare');

        return $releases;
    }


    /**
     * Every rename ever made, as widget name => [ old key => new key ].
     *
     * Ordered oldest release first, so a key renamed twice is carried the whole
     * way: a => b from one release and b => c from a later one are applied in
     * that order, landing on c.
     *
     * @param string|null $version Only this release's renames, when given.
     *
     * @return array
     */
    public static function get_renames($version = null)
    {
        $renames = array();

        foreach (self::rename_files() as $release => $widgets) {
            if (null !== $version && $release !== $version) {
                continue;
            }

            foreach ($widgets as $widget => $map) {
                $map = self::expand_groups($map);

                $renames[$widget] = isset($renames[$widget]) ? array_merge($renames[$widget], $map) : $map;
            }
        }

        return $renames;
    }

    /**
     * Turn every group-control entry into one entry per field it saves.
     *
     * A group control is one line in the map and several keys in the saved
     * data, and the rest of this class only ever moves one key at a time --
     * so the map is flattened once, here, rather than special-cased in each
     * of the three places that walk it.
     *
     * @param array $map
     *
     * @return array
     */
    private static function expand_groups($map)
    {
        $expanded = array();

        foreach ($map as $old_name => $target) {
            if (!is_array($target) || empty($target['group'])) {
                $expanded[$old_name] = $target;
                continue;
            }

            $group = $target['group'];

            if (!isset(self::GROUP_FIELDS[$group])) {
                continue;
            }

            $new_name = isset($target['to']) ? $target['to'] : self::DROPPED;

            foreach (self::GROUP_FIELDS[$group] as $field) {
                $expanded[$old_name . '_' . $field] = null === $new_name
                    ? self::DROPPED
                    : $new_name . '_' . $field;
            }
        }

        return $expanded;
    }

    /**
     * The suffixes a responsive control's value can be saved under.
     *
     * Read from Elementor when it is loaded, since a site can add breakpoints
     * of its own; the list is the fallback for when it is not.
     *
     * @return string[]
     */
    private static function device_suffixes()
    {
        static $suffixes = null;

        if (null !== $suffixes) {
            return $suffixes;
        }

        $suffixes = array('widescreen', 'laptop', 'tablet_extra', 'tablet', 'mobile_extra', 'mobile');

        if (class_exists('Elementor\Plugin') && isset(Elementor_Plugin::$instance->breakpoints)) {
            $devices = Elementor_Plugin::$instance->breakpoints->get_active_devices_list();

            if (!empty($devices)) {
                $suffixes = array_values(array_diff($devices, array('desktop')));
            }
        }

        return $suffixes;
    }

    /**
     * Every saved key a control's value can live under: the key itself, and one
     * per device for a responsive control.
     *
     * Matching the suffixes rather than any key that starts with the old name
     * is what keeps a rename of `layout` away from a neighbouring `layout_align`.
     *
     * @return string[]
     */
    private static function keys_for($name)
    {
        $keys = array($name);

        foreach (self::device_suffixes() as $suffix) {
            $keys[] = $name . '_' . $suffix;
        }

        return $keys;
    }

    /**
     * One entry of the map, read into its three parts.
     *
     * @param string|null|array $target
     *
     * @return array [ new control name or null, old value => new value, companion keys ]
     */
    private static function target_parts($target)
    {
        if (is_array($target)) {
            return array(
                isset($target['to']) ? $target['to'] : null,
                isset($target['values']) ? $target['values'] : array(),
                isset($target['with']) ? $target['with'] : array(),
                isset($target['sides']) ? $target['sides'] : array(),
            );
        }

        return array($target, array(), array(), array());
    }

    /**
     * Whether a value is one a slider saved: a size and the unit it is in.
     *
     * What tells a value still in the old shape from one already carried over,
     * which is what lets a conversion run twice without spoiling its own work.
     *
     * @param mixed $value
     *
     * @return bool
     */
    private static function is_slider($value)
    {
        return is_array($value) && array_key_exists('size', $value) && !isset($value['top']);
    }

    /**
     * A slider's one number, written into the named sides of a dimensions
     * value -- merged into whatever is already there rather than replacing it,
     * since a pair of sliders (horizontal, vertical) becomes one Padding and
     * each half has to find the other's work still in place.
     *
     * A value not in the slider shape is already a dimensions value: it is
     * passed through untouched.
     *
     * @param mixed $value
     * @param array $sides
     * @param mixed $existing
     *
     * @return mixed
     */
    private static function to_dimensions($value, $sides, $existing)
    {
        if (!self::is_slider($value)) {
            return self::has_value($existing) ? $existing : $value;
        }

        $out = is_array($existing) ? $existing : array();

        if (!isset($out['unit']) || '' === $out['unit']) {
            $out['unit'] = isset($value['unit']) && '' !== $value['unit'] ? $value['unit'] : 'px';
        }

        foreach (self::ALL_SIDES as $side) {
            if (!isset($out[$side])) {
                $out[$side] = '';
            }
        }

        foreach ($sides as $side) {
            if ('' === $out[$side]) {
                $out[$side] = (string) $value['size'];
            }
        }

        // The two sides of a pair are set separately, so they are no longer
        // one number the panel can keep linked.
        $out['isLinked'] = false;

        return $out;
    }

    /**
     * Whether a saved value is one a control would actually draw from.
     *
     * A control left at its default saves an empty string, an empty array or
     * nothing at all -- none of which is worth carrying, and none of which
     * should pull a companion default along with it.
     *
     * @param mixed $value
     *
     * @return bool
     */
    private static function has_value($value)
    {
        if (null === $value || '' === $value) {
            return false;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if (self::has_value($item)) {
                    return true;
                }
            }

            // A dimensions control saves its unit and link flag whether or not
            // any side was set, so an array is only a value if a side is.
            return false;
        }

        return true;
    }

    /**
     * Fill in the keys a moved value has to be joined by, without ever
     * overwriting one that a move of its own has already landed on.
     *
     * @param array $settings
     * @param array $companions
     *
     * @return array
     */
    private static function apply_companions($settings, $companions, $occupied = null)
    {
        // What counts as taken is not always the array being written to: live
        // settings carry every control's default, so a group border sitting at
        // its default reads as set when nothing has actually been chosen. The
        // saved data is the honest answer there, and is passed in as such.
        if (null === $occupied) {
            $occupied = $settings;
        }

        // Literals first, so a companion copied from another key sees whatever
        // this pass has already put there.
        $copies = array();

        foreach ($companions as $key => $value) {
            if (is_array($value) && isset($value['from'])) {
                $copies[$key] = $value['from'];
                continue;
            }

            if (!isset($occupied[$key]) || !self::has_value($occupied[$key])) {
                $settings[$key] = $value;
            }
        }

        foreach ($copies as $key => $source) {
            if (isset($occupied[$key]) && self::has_value($occupied[$key])) {
                continue;
            }

            // Nothing to copy is an answer of its own: the state this companion
            // belongs to had no border to take a colour before the rename
            // either, so it gets none now.
            if (!isset($settings[$source]) || !self::has_value($settings[$source])) {
                continue;
            }

            $settings[$key] = $settings[$source];
        }

        return $settings;
    }

    /**
     * Move a widget's renamed values onto their new keys, and drop the values
     * of controls that are simply gone.
     *
     * A value already saved under the new key wins: it was set after the
     * rename, so it is the newer of the two.
     *
     * @param string $widget_name
     * @param array  $settings
     *
     * @return array
     */
    public static function rename_settings($widget_name, $settings)
    {
        $renames = self::get_renames();

        if (!is_array($settings) || empty($renames[$widget_name])) {
            return $settings;
        }

        // Held back until every move is done: a companion default must never
        // stand where a value of the user's own is on its way.
        $companions = array();

        foreach ($renames[$widget_name] as $old_name => $target) {
            list($new_name, $values, $with, $sides) = self::target_parts($target);

            $old_keys = self::keys_for($old_name);
            $new_keys = null === $new_name ? array() : self::keys_for($new_name);

            foreach ($old_keys as $index => $old_key) {
                if (!isset($settings[$old_key])) {
                    continue;
                }

                $value = $settings[$old_key];
                unset($settings[$old_key]);

                if (null === $new_name) {
                    continue;
                }

                if (is_string($value) && array_key_exists($value, $values)) {
                    $value = $values[$value];
                }

                $new_key = $new_keys[$index];

                if (!empty($sides)) {
                    $settings[$new_key] = self::to_dimensions(
                        $value,
                        $sides,
                        isset($settings[$new_key]) ? $settings[$new_key] : array()
                    );

                    continue;
                }

                if (!isset($settings[$new_key]) || '' === $settings[$new_key]) {
                    $settings[$new_key] = $value;
                }

                if (!empty($with) && self::has_value($value)) {
                    $companions = array_merge($companions, $with);
                }
            }
        }

        return self::apply_companions($settings, $companions);
    }

    /**
     * Fill a widget's live settings from values still saved under the old keys.
     *
     * The settings a widget reads are built from the controls it registers, so
     * a value left under a name no longer registered never reaches it. The
     * saved data still holds it though, which is what this reads from -- until
     * the upgrade routine rewrites the page, or the user saves it again.
     *
     * @param string $widget_name
     * @param array  $settings Live settings, as the widget sees them.
     * @param array  $saved    Raw saved settings for the element.
     *
     * @return array
     */
    public static function restore_from_saved($widget_name, $settings, $saved)
    {
        $renames = self::get_renames();

        if (!is_array($settings) || !is_array($saved) || empty($renames[$widget_name])) {
            return $settings;
        }

        $companions = array();
        $restored = array();

        foreach ($renames[$widget_name] as $old_name => $target) {
            list($new_name, $values, $with, $sides) = self::target_parts($target);

            // A dropped control has nowhere to be restored to.
            if (null === $new_name) {
                continue;
            }

            $old_keys = self::keys_for($old_name);
            $new_keys = self::keys_for($new_name);

            foreach ($old_keys as $index => $old_key) {
                $new_key = $new_keys[$index];

                if (!isset($saved[$old_key]) || !self::has_value($saved[$old_key])) {
                    continue;
                }

                $value = $saved[$old_key];

                if (is_string($value) && array_key_exists($value, $values)) {
                    $value = $values[$value];
                }

                if (!empty($sides)) {
                    // A control whose shape changed under its own name reads
                    // here as already set, so the guard below cannot be the
                    // one that decides: what is live is the old value in a
                    // control that no longer understands it.
                    $existing = isset($settings[$new_key]) ? $settings[$new_key] : array();

                    if (self::is_slider($existing)) {
                        $existing = array();
                    }

                    $settings[$new_key] = self::to_dimensions($value, $sides, $existing);
                    $restored[$new_key] = $settings[$new_key];

                    continue;
                }

                if (!empty($with) && self::has_value($value)) {
                    $companions = array_merge($companions, $with);
                }

                // Again the saved data, not the live settings: a control the
                // user never touched still reaches here holding its default.
                if (isset($saved[$new_key]) && self::has_value($saved[$new_key])) {
                    continue;
                }

                $settings[$new_key] = $value;
                $restored[$new_key] = $value;
            }
        }

        // A companion gives way both to what the page saved and to what this
        // pass has just restored -- the border style carried over from the old
        // control is the user's, and outranks the one a colour implies.
        return self::apply_companions($settings, $companions, $restored + $saved);
    }

    /**
     * The same, for one element of a saved page.
     *
     * @param array $element
     *
     * @return array
     */
    public static function rename_element($element)
    {
        if (empty($element['widgetType']) || empty($element['settings'])) {
            return $element;
        }

        $element['settings'] = self::rename_settings($element['widgetType'], $element['settings']);

        return $element;
    }

    /**
     * Walk a page's element tree, renaming as it goes.
     *
     * @param array $elements
     * @param bool  $changed  Set when something was actually renamed.
     *
     * @return array
     */
    private static function rename_elements($elements, &$changed)
    {
        if (!is_array($elements)) {
            return $elements;
        }

        foreach ($elements as $index => $element) {
            if (!is_array($element)) {
                continue;
            }

            $before = isset($element['settings']) ? $element['settings'] : null;
            $element = self::rename_element($element);

            if ($before !== (isset($element['settings']) ? $element['settings'] : null)) {
                $changed = true;
            }

            if (!empty($element['elements'])) {
                $element['elements'] = self::rename_elements($element['elements'], $changed);
            }

            $elements[$index] = $element;
        }

        return $elements;
    }

    /**
     * Rewrite every saved page that holds one of the renamed widgets.
     *
     * Called from an upgrade file, so it runs once per site on the release that
     * ships the rename. Idempotent: a page with nothing left to rename is left
     * untouched, so running it again costs a read and nothing else.
     *
     * @param string|null $version Narrows the scan to the widgets renamed in
     *                             one release -- what an upgrade file passes,
     *                             so a site upgrading across several releases
     *                             does not rescan for renames already done.
     *
     * @return int Number of pages rewritten.
     */
    public static function run($version = null)
    {
        global $wpdb;

        $widgets = array_keys(self::get_renames($version));

        if (empty($widgets)) {
            return 0;
        }

        // Narrow the scan to pages that actually contain one of the widgets --
        // the element tree of a whole site is far too much to decode blindly.
        $clauses = array();
        $params = array();

        foreach ($widgets as $widget) {
            $clauses[] = 'meta_value LIKE %s';
            $params[] = '%' . $wpdb->esc_like('"widgetType":"' . $widget . '"') . '%';
        }

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- the interpolated part is placeholders built above, values are bound.
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND (" . implode(' OR ', $clauses) . ')',
                $params
            )
        );
        // phpcs:enable

        if (empty($rows)) {
            return 0;
        }

        $migrated = 0;

        foreach ($rows as $row) {
            $elements = json_decode($row->meta_value, true);

            if (!is_array($elements)) {
                continue;
            }

            $changed = false;
            $elements = self::rename_elements($elements, $changed);

            if (!$changed) {
                continue;
            }

            // Elementor stores this JSON slashed; update_metadata() unslashes
            // whatever it is given, so it has to go back in the same state.
            update_metadata('post', $row->post_id, '_elementor_data', wp_slash(wp_json_encode($elements)));
            $migrated++;
        }

        if ($migrated > 0 && class_exists('Elementor\Plugin') && isset(Elementor_Plugin::$instance->files_manager)) {
            // The generated CSS still names the old keys' values.
            Elementor_Plugin::$instance->files_manager->clear_cache();
        }

        return $migrated;
    }
}
