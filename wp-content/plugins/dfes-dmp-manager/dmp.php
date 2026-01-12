<?php
/*
Plugin Name: DFES DMP Live Calls Manager
*/

defined('ABSPATH') || exit;

register_activation_hook(__FILE__, function () {
    add_role('dmp-data-entry', 'DMP Data Entry', ['read' => true]);
});

add_filter('login_redirect', function ($url, $req, $user) {
    if (isset($user->roles) && in_array('dmp-data-entry', $user->roles)) {
        return admin_url('admin.php?page=dmp-entry');
    }
    return $url;
}, 10, 3);

add_action('admin_menu', function () {
    // Admin Management Page
    add_menu_page('DMP Users', 'DMP Users', 'manage_options', 'dmp-users', 'dmp_users_page');

    // Data Entry User Page
    if (current_user_can('dmp-data-entry')) {
        add_menu_page('DMP Data Entry', 'DMP Data Entry', 'read', 'dmp-entry', 'dmp_entry_page', 'dashicons-phone', 1);

        // Remove standard dashboard items for a cleaner interface
        remove_menu_page('index.php'); // Dashboard
        remove_menu_page('profile.php'); // Profile
        remove_menu_page('edit.php'); // Posts
        remove_menu_page('upload.php'); // Media
        remove_menu_page('edit.php?post_type=page'); // Pages
        remove_menu_page('edit-comments.php'); // Comments
        remove_menu_page('tools.php'); // Tools
        remove_menu_page('separator1');
        remove_menu_page('separator2');
        remove_menu_page('separator-last');
    }
});

// Redirect from default Dashboard to our page if they somehow land there
add_action('load-index.php', function () {
    if (current_user_can('dmp-data-entry')) {
        wp_redirect(admin_url('admin.php?page=dmp-entry'));
        exit;
    }
});

// Enqueue styles for the DMP Entry page
add_action('admin_enqueue_scripts', function ($hook) {
    // Check if we are on the specific admin page
    if (isset($_GET['page']) && $_GET['page'] === 'dmp-entry') {
        wp_enqueue_style('dmp-admin-style', plugins_url('assets/css/style.css', __FILE__), [], '1.0.0');
    }
});

function dmp_entry_page()
{
    $url = get_user_meta(get_current_user_id(), 'dmp_sheet', true);
    if (empty($url)) {
        $url = 'about:blank';
        echo '<div class="notice notice-warning"><p>No Sheet URL mapped for this user.</p></div>';
    }

    echo '<iframe src="' . esc_url($url) . '" class="dmp-iframe"></iframe>';
}

function dmp_users_page()
{
    if (isset($_POST['save'])) {
        foreach ($_POST['sheet'] as $id => $url) {
            update_user_meta($id, 'dmp_sheet', esc_url_raw($url));
        }
        echo "<div class='updated'><p>Saved</p></div>";
    }

    $users = get_users(['role' => 'dmp-data-entry']);
    echo "<div class='wrap'><h2>Station Sheet Mapping</h2><form method='post'><table class='widefat'><tr><th>User</th><th>Sheet URL</th></tr>";
    foreach ($users as $u) {
        $url = get_user_meta($u->ID, 'dmp_sheet', true);
        echo "<tr><td>{$u->user_login}</td><td><input name='sheet[{$u->ID}]' value='" . esc_attr($url) . "' style='width:100%'></td></tr>";
    }
    echo "</table><p><button name='save' class='button button-primary'>Save</button></p></form></div>";
}
