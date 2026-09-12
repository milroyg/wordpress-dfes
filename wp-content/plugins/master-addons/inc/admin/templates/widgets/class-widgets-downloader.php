<?php

namespace MasterAddons\Inc\Admin\Templates\Widgets;

use MasterAddons\Inc\Admin\WidgetBuilder\Widget_Builder_Init;

defined('ABSPATH') || exit;

/**
 * Installs a Widgets Library payload as a local jltma_widget post.
 */
class Widgets_Downloader
{
    const CATEGORY_SLUG  = 'master-addons-widgets';
    const CATEGORY_TITLE = 'Master Addons Widgets';

    /**
     * Required keys inside the payload's widget definition.
     */
    private static $required_widget_keys = ['title', 'html_code', 'css_code', 'js_code', 'sections'];

    /**
     * @param array $payload Decoded response from /masteraddons/v2/widget/{id}
     * @return true|\WP_Error
     */
    public static function validate($payload)
    {
        if (!is_array($payload) || empty($payload['success'])) {
            return new \WP_Error('bad_payload', __('The server did not return a valid widget.', 'master-addons'));
        }

        if (empty($payload['widget']) || !is_array($payload['widget'])) {
            return new \WP_Error('bad_payload', __('The server did not return a valid widget.', 'master-addons'));
        }

        foreach (self::$required_widget_keys as $key) {
            if (!array_key_exists($key, $payload['widget'])) {
                return new \WP_Error(
                    'bad_payload',
                    // translators: %s is the missing JSON key.
                    sprintf(__('The widget payload is missing "%s".', 'master-addons'), $key)
                );
            }
        }

        return true;
    }

    /**
     * Insert the widget. Every call creates a new post by design — a repeat
     * download must never overwrite edits the user made locally.
     *
     * @param array $payload
     * @return int|\WP_Error New jltma_widget post ID.
     */
    public static function install($payload)
    {
        $valid = self::validate($payload);
        if (is_wp_error($valid)) {
            return $valid;
        }

        $widget = $payload['widget'];
        $title  = !empty($payload['title']) ? $payload['title'] : $widget['title'];

        $post_id = wp_insert_post([
            'post_type'   => 'jltma_widget',
            'post_title'  => self::unique_title($title),
            'post_status' => 'publish',
        ], true);

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        self::register_category();

        $data = [
            'title'     => $widget['title'],
            'icon'      => !empty($widget['icon']) ? $widget['icon'] : 'eicon-code',
            'category'  => self::CATEGORY_SLUG,
            'html_code' => $widget['html_code'],
            'css_code'  => $widget['css_code'],
            'js_code'   => $widget['js_code'],
        ];

        update_post_meta($post_id, '_jltma_widget_data', $data);
        update_post_meta($post_id, '_jltma_widget_sections', is_array($widget['sections']) ? $widget['sections'] : []);
        update_post_meta($post_id, '_jltma_widget_category', self::CATEGORY_SLUG);

        // External CSS/JS libraries are premium-only, same gate the Widget
        // Builder's own save path uses: free builds store an empty set.
        if (!empty($widget['includes']) && is_array($widget['includes'])) {
            $includes = apply_filters(
                'master_addons/widget_builder/persist_includes',
                Widget_Builder_Init::normalize_includes(null),
                Widget_Builder_Init::normalize_includes($widget['includes']),
                $widget
            );

            update_post_meta(
                $post_id,
                '_jltma_widget_includes',
                Widget_Builder_Init::normalize_includes($includes)
            );
        }
        if (!empty($widget['dependencies']) && is_array($widget['dependencies'])) {
            update_post_meta($post_id, '_jltma_widget_dependencies', $widget['dependencies']);
        }
        if (!empty($widget['conditions']) && is_array($widget['conditions'])) {
            update_post_meta($post_id, '_jltma_widget_conditions', $widget['conditions']);
        }

        update_post_meta($post_id, '_jltma_library_source_id', absint($payload['widget_id'] ?? 0));
        update_post_meta($post_id, '_jltma_library_downloaded_at', time());

        if (!empty($payload['thumbnail'])) {
            // Kept even after a successful sideload so the widgets list can
            // re-fetch the image if the attachment is later deleted.
            update_post_meta($post_id, '_jltma_library_thumbnail', esc_url_raw($payload['thumbnail']));
            self::attach_thumbnail($post_id, $payload['thumbnail'], $title);
        }

        return $post_id;
    }

    /**
     * Duplicates are permitted, so disambiguate the title instead of blocking.
     */
    private static function unique_title($title)
    {
        global $wpdb;

        $existing = $wpdb->get_col($wpdb->prepare(
            "SELECT post_title FROM {$wpdb->posts} WHERE post_type = 'jltma_widget' AND post_status != 'trash' AND (post_title = %s OR post_title LIKE %s)",
            $title,
            $wpdb->esc_like($title . ' (') . '%'
        ));

        if (empty($existing) || !in_array($title, $existing, true)) {
            return $title;
        }

        $suffix = 2;
        while (in_array($title . ' (' . $suffix . ')', $existing, true)) {
            $suffix++;
        }

        return $title . ' (' . $suffix . ')';
    }

    /**
     * Make sure the Elementor category exists in the option the widget
     * builder reads when registering custom categories.
     */
    private static function register_category()
    {
        $categories = get_option('jltma_custom_widget_categories', []);
        if (!is_array($categories)) {
            $categories = [];
        }

        if (!isset($categories[self::CATEGORY_SLUG])) {
            $categories[self::CATEGORY_SLUG] = self::CATEGORY_TITLE;
            update_option('jltma_custom_widget_categories', $categories);
        }
    }

    /**
     * Sideload the catalog thumbnail. Failure is non-fatal — the widgets list
     * column falls back to the icon glyph.
     */
    private static function attach_thumbnail($post_id, $url, $title)
    {
        if (!function_exists('media_sideload_image')) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        $attachment_id = media_sideload_image($url, $post_id, $title, 'id');

        if (is_wp_error($attachment_id)) {
            return;
        }

        set_post_thumbnail($post_id, $attachment_id);
    }
}
