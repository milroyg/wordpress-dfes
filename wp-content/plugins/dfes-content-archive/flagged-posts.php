<?php
/*
Plugin Name: Flagged Posts
Description: Allows you to flag any post type and display flagged items via shortcode.
Version: 1.0
Author: You
*/

// Register admin menu for Flagged Posts
add_action('admin_menu', function () {
    if (!current_user_can('manage_options')) return; // Double check permission

    add_menu_page(
        'Flagged Posts',
        'Flagged Posts',
        'manage_options', // ← Only admins
        'flagged-posts',
        'render_flagged_posts_admin_page',
        'dashicons-flag',
        25
    );
});


// Render the admin page
function render_flagged_posts_admin_page() {
    $post_types = array_merge(
        get_post_types(['public' => true], 'names'),
        ['attachment']
    );
    $post_types = array_unique($post_types);

    echo '<div class="wrap"><h1>Flagged Posts by Type</h1>';

    foreach ($post_types as $type) {
        $post_type_obj = get_post_type_object($type);
        $label = $post_type_obj->labels->name;

        $query = new WP_Query([
            'post_type'      => $type,
            'posts_per_page' => -1,
            'post_status'    => ['publish', 'draft', 'private', 'inherit'],
            'meta_query'     => [
                [
                    'key'     => '_is_flagged',
                    'value'   => '1',
                    'compare' => '=',
                ]
            ],
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        if ($query->have_posts()) {
            echo "<h2>{$label}</h2><ul>";
            foreach ($query->posts as $post) {
                $link = (get_post_type($post) === 'attachment') ? wp_get_attachment_url($post->ID) : get_edit_post_link($post->ID);
                $title = esc_html(get_the_title($post));
                $status = get_post_status($post);
                $date = get_the_date('', $post);
                echo "<li><strong>{$date}</strong> – <a href='" . esc_url($link) . "'>{$title}</a> <em>({$status})</em></li>";
            }
            echo "</ul>";
        }
        wp_reset_postdata();
    }

    echo '</div>';
}


// Make the flagging meta box available for all post types
add_action('add_meta_boxes', function () {
    $post_types = get_post_types(['public' => true], 'names');
    foreach ($post_types as $post_type) {
        add_meta_box('flag_post', 'Archive this Post', function ($post) {
            $value = get_post_meta($post->ID, '_is_flagged', true);
            echo '<label><input type="checkbox" name="is_flagged" value="1" ' . checked($value, '1', false) . '> Archive</label>';
        }, $post_type, 'side');
    }
});


add_action('save_post', 'flagged_post_save_handler');
function flagged_post_save_handler($post_id) {
    // Prevent auto-saves, revisions, AJAX saves, and post revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (defined('DOING_AJAX') && DOING_AJAX) return;
    if (wp_is_post_revision($post_id)) return;

    // Check user capabilities
    if (!current_user_can('edit_post', $post_id)) return;

    // ✅ Handle Bulk Edit Flagged Status
    if (isset($_REQUEST['_is_flagged_bulk_edit'])) {
        $flag_value = sanitize_text_field($_REQUEST['_is_flagged_bulk_edit']);
        if ($flag_value === '1' || $flag_value === '0') {
            update_post_meta($post_id, '_is_flagged', $flag_value);
        }
        return; // Exit early to avoid conflicts with regular edit logic
    }

    // ✅ Handle Regular Edit Screen (post.php)
    if (is_admin() && isset($_POST['_wpnonce'])) {
        if (isset($_POST['is_flagged'])) {
            update_post_meta($post_id, '_is_flagged', '1');
        } else {
            update_post_meta($post_id, '_is_flagged', '0');
        }
    }
}



// Add Flagged column and bulk actions
add_action('current_screen', function($screen) {
    if (strpos($screen->id, 'edit-') !== 0) return;

    $post_type = substr($screen->id, strlen('edit-'));
    if (!in_array($post_type, array_merge(get_post_types(['public' => true]), ['attachment']))) return;

    // Add flagged column
    add_filter("manage_{$post_type}_posts_columns", function($columns) {
        $columns['flagged'] = 'Archive';
        return $columns;
    });

    // Display status
    add_action("manage_{$post_type}_posts_custom_column", function($column_name, $post_id) {
        if ($column_name === 'flagged') {
            
            $flagged = get_post_meta($post_id, '_is_flagged', true);
            echo $flagged === '1' ? '✅' : '❌';
        }
    }, 10, 2);


    // Handle bulk actions
    add_action('bulk_edit_custom_box', function($column_name, $post_type) {
    if ($column_name !== 'flagged') return;
    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <label class="alignleft">
                <span class="title">Archive</span>
                <select name="_is_flagged">
                    <option value="">— No Change —</option>
                    <option value="1">Archive</option>
                    <option value="0">Unarchive</option>
                </select>
            </label>
        </div>
    </fieldset>
    <?php
}, 10, 2);
});


add_action('admin_footer-edit.php', function () {
    $nonce = wp_create_nonce('bulk_flag_nonce');
    ?>
    <script>
    jQuery(function($) {
        function addFlagAjax(postIds, flagValue) {
            $.post(ajaxurl, {
                action: 'save_bulk_flag_status',
                post_ids: postIds,
                _is_flagged: flagValue,
                _ajax_nonce: '<?php echo esc_js($nonce); ?>'
            });
        }

        function handleBulkFlagging() {
            var bulkRow = $('#bulk-edit');
            var flagValue = bulkRow.find('select[name="_is_flagged"]').val();

            if (flagValue === "") return;

            var postIds = [];
            $('#the-list input[type="checkbox"]:checked').each(function() {
                postIds.push($(this).val());
            });

            if (postIds.length > 0) {
                addFlagAjax(postIds, flagValue);
            }
        }

        // Bind to both Bulk Apply buttons
        $('#doaction, #doaction2').on('click', function() {
            if ($('select[name="action"]').val() === 'edit' || $('select[name="action2"]').val() === 'edit') {
                setTimeout(handleBulkFlagging, 200); // Delay to wait for DOM render
            }
        });
    });
    </script>
    <?php
});


add_action('wp_ajax_save_bulk_flag_status', function () {
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Permission denied.');
    }

    check_ajax_referer('bulk_flag_nonce');

    $post_ids = isset($_POST['post_ids']) ? array_map('intval', $_POST['post_ids']) : [];
    $flag_value = sanitize_text_field($_POST['_is_flagged'] ?? '');

    if ($flag_value === '1' || $flag_value === '0') {
        foreach ($post_ids as $post_id) {
            update_post_meta($post_id, '_is_flagged', $flag_value);
        }
    }

    wp_send_json_success('Flag status updated.');
});

 
// Hook into all front-end queries except inside flagged_posts shortcode
add_filter('posts_where', function ($where, $query) {
    global $wpdb;

    // Skip in admin, REST API, and shortcode context
    if (is_admin() || defined('REST_REQUEST') || !empty($GLOBALS['flagged_posts_shortcode'])) {
        return $where;
    }

    // Allow single post/page/permalink view
    if ($query->is_singular()) {
        return $where;
    }

    // Exclude flagged posts
    $where .= " AND {$wpdb->posts}.ID NOT IN (
        SELECT post_id FROM {$wpdb->postmeta}
        WHERE meta_key = '_is_flagged' AND meta_value = '1'
    )";

    return $where;
}, 10, 2);
add_filter('posts_clauses', function ($clauses, $query) {
    global $wpdb;

    if (is_admin() || defined('REST_REQUEST') || !empty($GLOBALS['flagged_posts_shortcode']) || $query->is_singular()) {
        return $clauses;
    }

    // Ensure WHERE clause exists
    $clauses['where'] .= " AND {$wpdb->posts}.ID NOT IN (
        SELECT post_id FROM {$wpdb->postmeta}
        WHERE meta_key = '_is_flagged' AND meta_value = '1'
    )";

    return $clauses;
}, 10, 2);


// Admin notices (only shown in admin screens)
add_action('admin_notices', function () {
    if (isset($_GET['flagged_count']) && !isset($GLOBALS['flagged_notice_shown'])) {
        $GLOBALS['flagged_notice_shown'] = true;
        echo '<div class="updated notice is-dismissible fadeout"><p>Flagged ' . intval($_GET['flagged_count']) . ' items.</p></div>';
    }
    if (isset($_GET['unflagged_count']) && !isset($GLOBALS['unflagged_notice_shown'])) {
        $GLOBALS['unflagged_notice_shown'] = true;
        echo '<div class="updated notice is-dismissible fadeout"><p>Unflagged ' . intval($_GET['unflagged_count']) . ' items.</p></div>';
    }
});



 
// Script to fade out notices
add_shortcode('flagged_posts', function () {
    global $wpdb;
	 // ✅ Enqueue style right away
    wp_enqueue_style(
        'flagged-posts-style',
        plugins_url('assets/style.css', __FILE__),
        [],
        filemtime(plugin_dir_path(__FILE__) . 'assets/style.css')
    );
    $GLOBALS['flagged_posts_shortcode'] = true;


    $current_lang = function_exists('pll_current_language') ? pll_current_language() : '';

    // Get all flagged post IDs
    $flagged_ids = $wpdb->get_col("
        SELECT post_id FROM {$wpdb->postmeta}
        WHERE meta_key = '_is_flagged' AND meta_value = '1'
    ");

    if (empty($flagged_ids)) {
        return '<div class="archive-list-style">No flagged posts found.</div>';
    }

    // Filter flagged posts by current language
    $posts = [];
    foreach ($flagged_ids as $post_id) {
        if (function_exists('pll_get_post_language')) {
            $lang = pll_get_post_language($post_id);
            if ($lang !== $current_lang) {
                continue;
            }
        }

        $post = get_post($post_id);
        if (!$post) continue;
        $posts[] = $post;
    }

    if (empty($posts)) {
        return '<div class="archive-list-style">No flagged posts in this language.</div>';
    }

    // Group by year
    $by_year = [];
    foreach ($posts as $post) {
        $year = date('Y', strtotime($post->post_date));
        $by_year[$year][] = $post;
    }

    krsort($by_year);

    // Output
    ob_start();
    echo '<div class="archive-list-style"><ul class="archive-year-list">';
    foreach ($by_year as $year => $items) {
        echo '<li class="year-item">';
        echo '<span class="toggle-year" data-target="year-' . $year . '">▶</span> <strong>' . esc_html($year) . '</strong> (' . count($items) . ')';
        echo '<ul id="year-' . $year . '" class="posts hidden">';

        foreach ($items as $post) {
            $post_id = $post->ID;
            $date = get_the_date('F j, Y', $post);
            $title = get_the_title($post);
            $link = get_permalink($post);


            // Adjust link based on post type
            if ($post->post_type === 'tam_pdf') {
                $link = get_post_meta($post_id, 'tam_pdf_link', true);
            } elseif ($post->post_type === 'post_timeline') {
                $link = get_post_meta($post_id, 'ptl_post_link', true);
            } elseif ($post->post_type === 'attachment') {
                $link = wp_get_attachment_url($post_id);
            }
$categories_tam = get_the_terms($post_id, 'tam_category');
$categories_default = get_the_terms($post_id, 'category');

// Optional: get custom taxonomy if Cool Timeline uses it (change if needed)
$categories_timeline = get_the_terms($post_id, 'cool_timeline_category');

$categories = [];

// Merge all taxonomies safely
if (!empty($categories_tam) && !is_wp_error($categories_tam)) {
    $categories = array_merge($categories, $categories_tam);
}
if (!empty($categories_default) && !is_wp_error($categories_default)) {
    $categories = array_merge($categories, $categories_default);
}
if (!empty($categories_timeline) && !is_wp_error($categories_timeline)) {
    $categories = array_merge($categories, $categories_timeline);
}

// Add "Events" manually if post type is 'cool_timeline'
if (get_post_type($post_id) === 'cool_timeline') {
    $categories[] = (object)[
        'name' => 'Events',
        'slug' => 'events'
    ];
}

// Display category names
$cat_names = '';
if (!empty($categories)) {
    $cat_list = wp_list_pluck($categories, 'name');
    $cat_names = ' — ' . esc_html(implode(', ', $cat_list));
}

// Output
echo '<li style="margin-bottom: 15px;">';
echo '<div class="post-date" style="color: black; font-weight: bold;">' . esc_html($date) . '</div>';

if (!empty($cat_list)) {
    echo '<div class="post-category" style="margin: 5px 0;">';
    foreach ($cat_list as $cat_name) {
        echo '<span style="display: inline-block; background-color: #f1f1f1; color: black; border-radius: 4px; padding: 2px 8px; margin: 2px; font-size: 18px;">' . esc_html($cat_name) . '</span>';
    }
    echo '</div>';
}

echo '<a href="' . esc_url($link) . '" target="_blank" style="font-size: 16px; color: #0066cc; text-decoration: none;">' . esc_html($title) . '</a>';
echo '</li>';


        }

        echo '</ul></li>';
    }
    echo '</ul></div>';

    wp_reset_postdata();

    ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.toggle-year').forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const target = document.getElementById(toggle.dataset.target);
                    const isOpen = !target.classList.contains('hidden');
                    target.classList.toggle('hidden');
                    toggle.textContent = isOpen ? '▶' : '▼';
                });
            });
        });
    </script>
    <?php

    unset($GLOBALS['flagged_posts_shortcode']);
    return ob_get_clean();
});



