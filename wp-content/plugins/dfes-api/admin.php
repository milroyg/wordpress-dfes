<?php
// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

// -----------------------------
// Admin Menus
// -----------------------------
add_action('admin_menu', 'dfes_contacts_admin_menu');
add_action('admin_menu', 'dfes_settings_admin_menu');

function dfes_contacts_admin_menu() {
    add_menu_page(
        'DFES Contacts',
        'DFES Contacts',
        'manage_options',
        'dfes-contacts',
        'dfes_contacts_admin_page',
        'dashicons-email-alt2',
        20
    );
}

function dfes_settings_admin_menu() {
    add_submenu_page(
        'dfes-contacts',
        'DFES Settings',
        'Settings',
        'manage_options',
        'dfes-settings',
        'dfes_settings_admin_page'
    );
}

// -----------------------------
// Admin Scripts
// -----------------------------
function dfes_admin_enqueue_scripts($hook) {
    if (strpos($hook, 'dfes-contacts') === false && strpos($hook, 'dfes-settings') === false) {
        return;
    }

    // Bootstrap
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);

    // DataTables
    wp_enqueue_style('datatables-bootstrap5-css', 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css');
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', array('jquery'), null, true);
    wp_enqueue_script('datatables-bootstrap5-js', 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js', array('datatables-js', 'bootstrap-js'), null, true);

    wp_add_inline_script('datatables-bootstrap5-js', '
        jQuery(document).ready(function($) {
            $("#dfes-contacts-table").DataTable({
                "pageLength": 10,
                "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                "order": [[0, "asc"]],
                "responsive": true
            });
        });
    ');
}
add_action('admin_enqueue_scripts', 'dfes_admin_enqueue_scripts');


// =============================
// ACTION HANDLERS (admin_post_*)
// =============================

// ✅ Save Contact
add_action('admin_post_dfes_save_contact', 'dfes_handle_save_contact');
function dfes_handle_save_contact() {
    if (!current_user_can('manage_options')) wp_die('Unauthorized');
    check_admin_referer('dfes_save_contact');

    global $wpdb;
    $table = $wpdb->prefix . 'dfes_contacts';

    $stations = isset($_POST['stations']) ? array_map('sanitize_text_field', (array)$_POST['stations']) : [];
    $stations_str = implode(',', $stations);

    $data = [
        'name'         => sanitize_text_field($_POST['name'] ?? ''),
        'phone_number' => sanitize_text_field($_POST['phone_number'] ?? ''),
        'email'        => sanitize_email($_POST['email'] ?? ''),
        'station'      => $stations_str,
        'status'       => !empty($_POST['status']) ? 1 : 0
    ];

    if (!empty($_POST['contact_id'])) {
        $wpdb->update($table, $data, ['id' => intval($_POST['contact_id'])]);
        $msg = "updated";
    } else {
        $wpdb->insert($table, $data);
        $msg = "added";
    }

    wp_redirect(admin_url('admin.php?page=dfes-contacts&success=' . $msg));
    exit;
}

// ✅ Delete Contact
add_action('admin_post_dfes_delete_contact', 'dfes_handle_delete_contact');
function dfes_handle_delete_contact() {
    if (!current_user_can('manage_options')) wp_die('Unauthorized');

    global $wpdb;
    $table = $wpdb->prefix . 'dfes_contacts';
    $wpdb->delete($table, ['id' => intval($_GET['id'] ?? 0)]);

    wp_redirect(admin_url('admin.php?page=dfes-contacts&success=deleted'));
    exit;
}

// ✅ Bulk Delete
add_action('admin_post_dfes_bulk_delete', 'dfes_handle_bulk_delete');
function dfes_handle_bulk_delete() {
    if (!current_user_can('manage_options')) wp_die('Unauthorized');

    global $wpdb;
    $table = $wpdb->prefix . 'dfes_contacts';

    if (!empty($_POST['delete_ids'])) {
        $ids = array_map('intval', $_POST['delete_ids']);
        $ids_placeholders = implode(',', array_fill(0, count($ids), '%d'));
        $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE id IN ($ids_placeholders)", $ids));
    }

    wp_redirect(admin_url('admin.php?page=dfes-contacts&success=deleted'));
    exit;
}

// ✅ Import CSV
add_action('admin_post_dfes_import_csv', 'dfes_handle_import_csv');
function dfes_handle_import_csv() {
    if (!current_user_can('manage_options')) wp_die('Unauthorized');
    check_admin_referer('dfes_import_csv', 'dfes_import_csv_nonce');

    if (empty($_FILES['import_csv']['name'])) {
        wp_redirect(admin_url('admin.php?page=dfes-contacts&import_done=1'));
        exit;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    $upload_overrides = ['test_form' => false, 'mimes' => ['csv' => 'text/csv']];
    $movefile = wp_handle_upload($_FILES['import_csv'], $upload_overrides);

    $imported = 0; 
    $errors   = 0; 
    $log_url  = '';

    if ($movefile && !isset($movefile['error'])) {
        $file_path = $movefile['file'];
        $file = fopen($file_path, 'r');
        global $wpdb;
        $table = $wpdb->prefix . 'dfes_contacts';
        $row = 0;

        $rows_with_remarks = []; // for error CSV

        // Read header
        $header = fgetcsv($file, 1000, ",");
        if ($header) {
            $header[] = "Remarks"; // add remarks column
        }

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $row++;
            $remark = "";

            $name    = sanitize_text_field($data[0] ?? '');
            $phone   = preg_replace('/\D/', '', $data[1] ?? '');
            $email_raw = trim($data[2] ?? '');
            $email   = sanitize_email($email_raw);
            $station = sanitize_text_field($data[3] ?? '');
            $status  = strtolower(trim($data[4] ?? '')) === 'active' ? 1 : 0;

            // ✅ Validation
            if (empty($name) || empty($phone)) {
                $remark = "Missing name or phone";
            } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
                $remark = "Invalid phone number";
            } elseif (empty($email_raw)) {
                $remark = "Missing email (required)";
            } elseif (!is_email($email_raw)) {
                $remark = "Invalid email";
            }

            // ✅ Check duplicates only if validations passed
            if (empty($remark)) {
                $exists_phone = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE phone_number = %s", $phone));
                if ($exists_phone > 0) {
                    $remark = "Phone exists ($phone)";
                } else {
                    $exists_email = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE email = %s", $email));
                    if ($exists_email > 0) {
                        $remark = "Email exists ($email)";
                    }
                }
            }

            // ✅ Only insert if NO remark
            if (empty($remark)) {
                $inserted = $wpdb->insert($table, [
                    'name'         => $name,
                    'phone_number' => $phone,
                    'email'        => $email,
                    'station'      => $station,
                    'status'       => $status
                ]);
                if ($inserted) {
                    $imported++;
                } else {
                    $remark = "DB insert failed";
                }
            }

            // Record row with remark if skipped
            if (!empty($remark)) {
                $errors++;
                $data[] = $remark;
                $rows_with_remarks[] = $data;
            }
        }
        fclose($file);
        @unlink($file_path);

        // If errors → generate error CSV
        if (!empty($rows_with_remarks)) {
            $upload_dir = wp_upload_dir();
            $csv_file = $upload_dir['basedir'] . '/dfes_import_errors_' . date('Ymd_His') . '.csv';
            $fh = fopen($csv_file, 'w');
            if ($header) {
                fputcsv($fh, $header);
            }
            foreach ($rows_with_remarks as $row) {
                fputcsv($fh, $row);
            }
            fclose($fh);
            $log_url = $upload_dir['baseurl'] . '/' . basename($csv_file);
        }
    } else {
        $errors++;
    }

    set_transient('dfes_csv_import_result', [
        'imported' => $imported, 
        'errors'   => $errors, 
        'log_url'  => $log_url
    ], 30);

    wp_redirect(admin_url('admin.php?page=dfes-contacts&import_done=1'));
    exit;
}





// =============================
// Contacts Admin Page
// =============================
function dfes_contacts_admin_page() {
    if (!current_user_can('manage_options')) return;

    global $wpdb;
    $table = $wpdb->prefix . 'dfes_contacts';
   $stations_list = [
    'Headquarters' => 'Headquarters',
    'Mapusa'       => 'Mapusa',
    'Panaji'       => 'Panaji',
    'Pernem'       => 'Pernem',
    'Pilerne'      => 'Pilerne',
    'Porvorim'     => 'Porvorim',
    'Vasco'        => 'Vasco',
    'Bicholim'     => 'Bicholim',
    'Kundaim'      => 'Kundaim',
    'Old Goa'      => 'Old Goa',
    'Ponda'        => 'Ponda',
    'Valpoi'       => 'Valpoi',
    'Canacona'     => 'Canacona',
    'Cuncolim'     => 'Cuncolim',
    'Curchorem'    => 'Curchorem',
    'Margao'       => 'Margao',
    'Verna'        => 'Verna',
    'Press'        => 'Press',
    'TestStation'  => 'TestStation'
];


    $edit_contact = null;
    if (isset($_GET['edit_contact'])) {
        $edit_contact = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", intval($_GET['edit_contact'])), ARRAY_A);
    }
    $contacts = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);
    $selected_stations = $edit_contact ? explode(',', $edit_contact['station']) : [];
    ?>
    <div class="container-fluid">
        <h1 class="mb-4">🚒 DFES Contacts</h1>

        <?php if (isset($_GET['import_done']) && ($result = get_transient('dfes_csv_import_result'))): ?>
            <div class="notice notice-success"><p>✅ Imported: <?php echo intval($result['imported']); ?>, Errors: <?php echo intval($result['errors']); ?></p></div>
            <?php if ($result['errors'] > 0 && $result['log_url']): ?>
                <div class="notice notice-warning"><p><a href="<?php echo esc_url($result['log_url']); ?>">📄 Download Error Log</a></p></div>
            <?php endif; ?>
            <?php delete_transient('dfes_csv_import_result'); ?>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">✅ <?php echo esc_html($_GET['success']); ?> successfully</div>
        <?php endif; ?>

        <!-- Contact Form -->
        <div class="card mb-4">
            <div class="card-header"><?php echo $edit_contact ? '✏️ Edit Contact' : '➕ Add Contact'; ?></div>
            <div class="card-body">
                <form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('dfes_save_contact'); ?>
                    <input type="hidden" name="action" value="dfes_save_contact">
                    <input type="hidden" name="contact_id" value="<?php echo esc_attr($edit_contact['id'] ?? ''); ?>">
                    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="<?php echo esc_attr($edit_contact['name'] ?? ''); ?>" required></div>
                    <div class="mb-3"><label>Phone</label><input type="text" name="phone_number" class="form-control" value="<?php echo esc_attr($edit_contact['phone_number'] ?? ''); ?>" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo esc_attr($edit_contact['email'] ?? ''); ?>"></div>
                    <div class="mb-3"><label>Stations</label><br>
                        <?php foreach ($stations_list as $key => $label): ?>
                            <label><input type="checkbox" name="stations[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key,$selected_stations,true)); ?>> <?php echo esc_html($label); ?></label><br>
                        <?php endforeach; ?>
                    </div>
                    <div class="mb-3"><label><input type="checkbox" name="status" value="1" <?php checked($edit_contact && !empty($edit_contact['status'])); ?>> Active</label></div>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_contact ? 'Update' : 'Add'; ?></button>
                </form>
            </div>
        </div>

        <!-- Contacts List -->
        <form method="POST" action="<?php echo admin_url('admin-post.php'); ?>" onsubmit="return confirm('Delete selected contacts?');">
            <input type="hidden" name="action" value="dfes_bulk_delete">
            <table id="dfes-contacts-table" class="table table-bordered">
                <thead><tr><th><input type="checkbox" id="select-all"></th><th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Stations</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($contacts as $c): ?>
                    <tr>
                        <td><input type="checkbox" name="delete_ids[]" value="<?php echo intval($c['id']); ?>"></td>
                        <td><?php echo esc_html($c['id']); ?></td>
                        <td><?php echo esc_html($c['name']); ?></td>
                        <td><?php echo esc_html($c['phone_number']); ?></td>
                        <td><?php echo esc_html($c['email']); ?></td>
                        <td><?php echo esc_html($c['station']); ?></td>
                        <td><?php echo !empty($c['status']) ? '✅ Active' : '❌ Inactive'; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=dfes-contacts&edit_contact='.$c['id']); ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=dfes_delete_contact&id='.$c['id']), 'dfes_delete_contact'); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this contact?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-danger mt-2">🗑️ Delete Selected</button>
        </form>

        <!-- Import/Export -->
        <div class="mt-3">
            <a href="<?php echo esc_url(admin_url('admin.php?page=dfes-contacts&export_csv=1')); ?>" class="btn btn-success">⬇️ Export CSV</a>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data" style="display:inline-block;">
                <?php wp_nonce_field('dfes_import_csv','dfes_import_csv_nonce'); ?>
                <input type="hidden" name="action" value="dfes_import_csv">
                <input type="file" name="import_csv" accept=".csv" required>
                <button type="submit" class="btn btn-warning">⬆️ Import CSV</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('select-all').addEventListener('click', function () {
            document.querySelectorAll('input[name="delete_ids[]"]').forEach(cb => cb.checked = this.checked);
        });
    </script>
    <?php
}

// -----------------------------
// Settings Admin Page
// -----------------------------
function dfes_settings_admin_page() {
    if (!current_user_can('manage_options')) return;

    $opts = get_option('dfes_settings', []);
    if (isset($_POST['dfes_save_settings'])) {
        check_admin_referer('dfes_save_settings');
        $opts['msg91_authkey']   = sanitize_text_field($_POST['msg91_authkey'] ?? '');
        $opts['msg91_senderid']  = sanitize_text_field($_POST['msg91_senderid'] ?? '');
        $opts['msg91_dlt_te_id'] = sanitize_text_field($_POST['msg91_dlt_te_id'] ?? '');
        $opts['email_from_name'] = sanitize_text_field($_POST['email_from_name'] ?? '');
        $opts['email_from_address'] = sanitize_email($_POST['email_from_address'] ?? '');
        $opts['notify_all'] = !empty($_POST['notify_all']) ? 1 : 0;
        $opts['logging_enabled'] = !empty($_POST['logging_enabled']) ? 1 : 0;
        update_option('dfes_settings',$opts);
        echo '<div class="notice notice-success"><p>✅ Settings saved.</p></div>';
    }
    ?>
    <div class="container-fluid">
        <h1>⚙️ DFES Settings</h1>
        <form method="post">
            <?php wp_nonce_field('dfes_save_settings'); ?>
            <div class="mb-3"><label>MSG91 Auth Key</label><input type="text" name="msg91_authkey" class="form-control" value="<?php echo esc_attr($opts['msg91_authkey'] ?? ''); ?>"></div>
            <div class="mb-3"><label>MSG91 Sender ID</label><input type="text" name="msg91_senderid" class="form-control" value="<?php echo esc_attr($opts['msg91_senderid'] ?? ''); ?>"></div>
            <div class="mb-3"><label>MSG91 DLT Template ID</label><input type="text" name="msg91_dlt_te_id" class="form-control" value="<?php echo esc_attr($opts['msg91_dlt_te_id'] ?? ''); ?>"></div>
            <div class="mb-3 form-check"><input class="form-check-input" type="checkbox" name="notify_all" value="1" <?php checked(!empty($opts['notify_all'])); ?>> <label>Send to all active contacts</label></div>
            <div class="mb-3 form-check"><input class="form-check-input" type="checkbox" name="logging_enabled" value="1" <?php checked(!empty($opts['logging_enabled'])); ?>> <label>Enable logging</label></div>
            <button type="submit" name="dfes_save_settings" class="btn btn-primary">Save</button>
        </form>
    </div>
    <?php
}
