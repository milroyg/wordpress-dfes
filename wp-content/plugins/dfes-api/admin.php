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
// ✅ Save Contact (normalized schema)
add_action('admin_post_dfes_save_contact', 'dfes_handle_save_contact');
function dfes_handle_save_contact() {
    if (!current_user_can('manage_options')) wp_die('Unauthorized');
    check_admin_referer('dfes_save_contact');

    global $wpdb;
    $contacts_table = $wpdb->prefix . 'dfes_contacts';
    $stations_table = $wpdb->prefix . 'dfes_contact_stations';

    $stations = isset($_POST['stations']) ? array_map('sanitize_text_field', (array)$_POST['stations']) : [];

    $data = [
        'name'         => sanitize_text_field($_POST['name'] ?? ''),
        'phone_number' => sanitize_text_field($_POST['phone_number'] ?? ''),
        'email'        => sanitize_email($_POST['email'] ?? ''),
        'status'       => !empty($_POST['status']) ? 1 : 0
    ];

    if (!empty($_POST['contact_id'])) {
        $contact_id = intval($_POST['contact_id']);
        $wpdb->update($contacts_table, $data, ['id' => $contact_id]);
        $msg = "updated";
    } else {
        $wpdb->insert($contacts_table, $data);
        $contact_id = $wpdb->insert_id;
        $msg = "added";
    }

    // 🔄 Update stations
    $wpdb->delete($stations_table, ['contact_id' => $contact_id]);
    foreach ($stations as $s) {
        $wpdb->insert($stations_table, [
            'contact_id' => $contact_id,
            'station'    => $s
        ]);
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

            // Insert contact
if (empty($remark)) {
    $inserted = $wpdb->insert($table, [
        'name'         => $name,
        'phone_number' => $phone,
        'email'        => $email,
        'status'       => $status
    ]);

    if ($inserted) {
        $contact_id = $wpdb->insert_id;

       // Split stations if multiple (comma separated)
$stations_table = $wpdb->prefix . 'dfes_contact_stations';

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
    'Reinforcement'=> 'Reinforcement',
    'TestStation'  => 'TestStation'
];


if (strtolower($station) === 'all fire station') {
    // Assign all stations except Press and TestStation
    $stations_arr = array_diff(array_keys($stations_list), ['Press', 'TestStation']);
} else {
    $stations_arr = array_map('trim', explode(',', $station));
}

// Insert each station
foreach ($stations_arr as $s) {
    if (!empty($s)) {
        $wpdb->insert($stations_table, [
            'contact_id' => $contact_id,
            'station'    => $s
        ]);
    }
}


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
    $tmp_file = tmpfile(); 
    $meta = stream_get_meta_data($tmp_file);
    $csv_file = $meta['uri'];

    if ($header) {
        fputcsv($tmp_file, $header);
    }
    foreach ($rows_with_remarks as $row) {
        fputcsv($tmp_file, $row);
    }
    rewind($tmp_file);

    // Send for download immediately
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="dfes_import_errors.csv"');
    fpassthru($tmp_file);

    fclose($tmp_file); // auto deletes
    exit;
}
    }
}

//✅ CSV Export
if (isset($_GET['export_csv']) && $_GET['export_csv'] == 1) {
    global $wpdb;
    $contacts_table = $wpdb->prefix . 'dfes_contacts';
    $stations_table = $wpdb->prefix . 'dfes_contact_stations';

    // Fetch contacts with stations
    $contacts = $wpdb->get_results(
        "SELECT c.id, c.name, c.phone_number, c.email, c.status, 
                GROUP_CONCAT(s.station ORDER BY s.station SEPARATOR ', ') AS stations
         FROM $contacts_table c
         LEFT JOIN $stations_table s ON s.contact_id = c.id
         GROUP BY c.id
         ORDER BY c.id ASC",
        ARRAY_A
    );

    // Send CSV headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=dfes_contacts_' . date('Ymd_His') . '.csv');

    $output = fopen('php://output', 'w');

    // CSV Header Row
    fputcsv($output, ['Name', 'Phone Number', 'Email']);

    // Rows
    foreach ($contacts as $contact) {
        fputcsv($output, [
            $contact['name'],
            $contact['phone_number'],
            $contact['email'],
        ]);
    }

    fclose($output);
    exit; // stop rendering admin page
}




// =============================
// Contacts Admin Page
// =============================
function dfes_contacts_admin_page() {
    if (!current_user_can('manage_options')) return;

    global $wpdb;
    $contacts_table = $wpdb->prefix . 'dfes_contacts';
    $stations_table = $wpdb->prefix . 'dfes_contact_stations';


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
        'Reinforcement'=> 'Reinforcement',
        'TestStation'  => 'TestStation'
    ];

    $edit_contact = null;
    $selected_stations = [];
    if (isset($_GET['edit_contact'])) {
        $contact_id = intval($_GET['edit_contact']);
        $edit_contact = $wpdb->get_row($wpdb->prepare("SELECT * FROM $contacts_table WHERE id = %d", $contact_id), ARRAY_A);
        if ($edit_contact) {
            $selected_stations = $wpdb->get_col($wpdb->prepare("SELECT station FROM $stations_table WHERE contact_id = %d", $contact_id));
        }
    }

    // fetch all contacts with stations
  $contacts = $wpdb->get_results(
    "SELECT c.*, GROUP_CONCAT(s.station ORDER BY s.station SEPARATOR ', ') AS stations
     FROM $contacts_table c
     LEFT JOIN $stations_table s ON s.contact_id = c.id
     GROUP BY c.id
     ORDER BY c.id ASC",
    ARRAY_A
);


    ?>
    <div class="container-fluid">
        <h1 class="mb-4">🚒 DFES Contacts</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">✅ <?php echo esc_html($_GET['success']); ?> successfully</div>
        <?php endif; ?>

        <!-- Contact Form -->
        <div class="card border-success mb-3">
            <div class="card-header"><?php echo $edit_contact ? 'Edit Contact' : 'Add Contact'; ?></div>
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
                            <label><input type="checkbox" name="stations[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $selected_stations, true)); ?>> <?php echo esc_html($label); ?></label><br>
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
                       <td><?php echo esc_html($c['stations']); ?></td>
                        <td><?php echo !empty($c['status']) ? '✅ Active' : '❌ Inactive'; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=dfes-contacts&edit_contact='.$c['id']); ?>" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Edit</a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=dfes_delete_contact&id='.$c['id']), 'dfes_delete_contact'); ?>" class="link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" onclick="return confirm('Delete this contact?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-outline-danger">Delete Selected</button>
        </form>
        <!-- Import/Export CSV -->
<div class="mt-3">
    <a href="<?php echo esc_url(admin_url('admin.php?page=dfes-contacts&export_csv=1')); ?>" class="btn btn-outline-success">Export CSV</a>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data" style="display:inline-block;">
        <?php wp_nonce_field('dfes_import_csv','dfes_import_csv_nonce'); ?>
        <input type="hidden" name="action" value="dfes_import_csv">
        <input type="file" name="import_csv" accept=".csv" required>
        <button type="submit" class="btn btn-outline-primary">Import CSV</button>
    </form>
</div>

<?php if (isset($_GET['import_done']) && ($result = get_transient('dfes_csv_import_result'))): ?>
    <div class="notice notice-success mt-2"><p>✅ Imported: <?php echo intval($result['imported']); ?>, Errors: <?php echo intval($result['errors']); ?></p></div>
    <?php if ($result['errors'] > 0 && $result['log_url']): ?>
        <div class="notice notice-warning"><p><a href="<?php echo esc_url($result['log_url']); ?>">📄 Download Error Log</a></p></div>
    <?php endif; ?>
    <?php delete_transient('dfes_csv_import_result'); ?>
<?php endif; ?>

    </div>

    <script>
        document.getElementById('select-all').addEventListener('click', function () {
            document.querySelectorAll('input[name="delete_ids[]"]').forEach(cb => cb.checked = this.checked);
        });
    </script>
    <?php
}

// =============================
// SMS Gateway
// =============================       
function dfes_settings_admin_page() {
    if (!current_user_can('manage_options')) return;

    // Seed defaults if not present
    dfes_api_seed_default_options();

    $all = (array) get_option('dfes_settings', ['active'=>'','gateways'=>[]]);
    $all['gateways'] = $all['gateways'] ?? [];

    $edit_id   = '';
    $edit_data = ['name'=>'','base_url'=>'','params'=>[]];


    // -------------------------------
    // Detect edit
    // -------------------------------
    if (isset($_POST['dfes_edit_gateway'])) {
        $edit_id = sanitize_key($_POST['edit_gateway']);
        if (!empty($all['gateways'][$edit_id])) {
            $edit_data = $all['gateways'][$edit_id];

            // Convert old-style params (assoc array) to new-style if needed
            if (!empty($edit_data['params']) && isset($edit_data['params']['keys'])) {
                $converted = [];
                foreach ($edit_data['params']['keys'] as $i => $key) {
                    $converted[] = [
                        'key' => $key,
                        'value' => $edit_data['params']['values'][$i] ?? ''
                    ];
                }
                $edit_data['params'] = $converted;
            }
        }
    }

    // -------------------------------
    // Save gateway
    // -------------------------------
    if (isset($_POST['dfes_save_gateway'])) {
        check_admin_referer('dfes_save_gateway');

        $gateway_id = !empty($_POST['gateway_id']) ? sanitize_key($_POST['gateway_id']) : uniqid('gw_');
        $gateway_name = sanitize_text_field($_POST['gateway_name'] ?? '');
        $base_url     = esc_url_raw($_POST['base_url'] ?? '');

        $params = [];
        if (!empty($_POST['params']['keys'])) {
            foreach ($_POST['params']['keys'] as $i => $key) {
                $k = sanitize_text_field($key);
                $v = sanitize_text_field($_POST['params']['values'][$i] ?? '');
                if ($k || $v) { // save even if key is empty
                    $params[] = ['key'=>$k,'value'=>$v];
                }
            }
        }

        $all['gateways'][$gateway_id] = [
            'name' => $gateway_name,
            'base_url' => $base_url,
            'params' => $params
        ];

        update_option('dfes_settings', $all);
        echo '<div class="notice notice-success"><p>✅ Gateway saved.</p></div>';

        $edit_id = '';
        $edit_data = ['name'=>'','base_url'=>'','params'=>[]];
    }

    // -------------------------------
    // Activate gateway
    // -------------------------------
    if (isset($_POST['dfes_activate_gateway'])) {
        check_admin_referer('dfes_activate_gateway');
        $activate = sanitize_key($_POST['activate_gateway']);
        if (!empty($all['gateways'][$activate])) {
            $all['active'] = $activate;
            update_option('dfes_settings', $all);
            echo '<div class="notice notice-success"><p>✅ Gateway activated.</p></div>';
        }
    }

    // -------------------------------
    // Delete gateway
    // -------------------------------
    if (isset($_POST['dfes_delete_gateway'])) {
        check_admin_referer('dfes_delete_gateway');
        $delete = sanitize_key($_POST['delete_gateway']);
        if (isset($all['gateways'][$delete])) {
            unset($all['gateways'][$delete]);
            if ($all['active'] === $delete) $all['active'] = '';
            update_option('dfes_settings', $all);
            echo '<div class="notice notice-warning"><p>🗑️ Gateway deleted.</p></div>';
        }
    }

    // -------------------------------
    // Render Form
    // -------------------------------
    ?>
    <div class="wrap">
        <h1>⚙️ DFES SMS Gateway Settings</h1>
        <h2>Active Gateway: <?php echo $all['active'] ? esc_html($all['gateways'][$all['active']]['name']) : '❌ None'; ?></h2>
        <h2><?php echo $edit_id ? '✏️ Edit Gateway' : '➕ Add Gateway'; ?></h2>
        <form method="post">
            <?php wp_nonce_field('dfes_save_gateway'); ?>
            <input type="hidden" name="gateway_id" value="<?php echo esc_attr($edit_id); ?>">

            <table class="form-table">
                <tr>
                    <th><label>Gateway Name</label></th>
                    <td><input type="text" name="gateway_name" value="<?php echo esc_attr($edit_data['name']); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label>Base URL</label></th>
                    <td><input type="text" name="base_url" value="<?php echo esc_attr($edit_data['base_url']); ?>" class="regular-text" required></td>
                </tr>
            </table>

            <h3>Parameters</h3>
            <div id="gateway-params">
                <?php if (!empty($edit_data['params'])): ?>
                    <?php foreach ($edit_data['params'] as $param): ?>
                        <div class="param" style="margin-bottom:6px;">
                            <input type="text" name="params[keys][]" value="<?php echo esc_attr($param['key']); ?>" placeholder="param_name" class="regular-text" style="width:25%;">
                            <input type="text" name="params[values][]" value="<?php echo esc_attr($param['value']); ?>" placeholder="param_value (use {mobile}, {message})" class="regular-text" style="width:50%;">
                            <button type="button" class="button button-small" onclick="this.parentNode.remove()">❌</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="param" style="margin-bottom:6px;">
                        <input type="text" name="params[keys][]" placeholder="param_name" class="regular-text" style="width:25%;">
                        <input type="text" name="params[values][]" placeholder="param_value (use {mobile}, {message})" class="regular-text" style="width:50%;">
                        <button type="button" class="button button-small" onclick="this.parentNode.remove()">❌</button>
                    </div>
                <?php endif; ?>
            </div>
            <p><button type="button" class="button" onclick="addParam()">+ Add Param</button></p>
            <p><button type="submit" name="dfes_save_gateway" class="button button-primary">Save Gateway</button></p>
        </form>

        <hr>
        <h2>📋 Existing Gateways</h2>
        <?php if (!empty($all['gateways'])): ?>
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Base URL</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all['gateways'] as $id => $gw): ?>
                        <tr>
                            <td><strong><?php echo esc_html($gw['name']); ?></strong></td>
                            <td><?php echo esc_url($gw['base_url']); ?></td>
                            <td><?php echo ($all['active'] === $id) ? '✅ Active' : '❌ Inactive'; ?></td>
                            <td>
                                <?php if ($all['active'] !== $id): ?>
                                    <form method="post" style="display:inline">
                                        <?php wp_nonce_field('dfes_activate_gateway'); ?>
                                        <input type="hidden" name="activate_gateway" value="<?php echo esc_attr($id); ?>">
                                        <button type="submit" name="dfes_activate_gateway" class="btn btn-outline-success">Activate</button>
                                    </form>
                                <?php endif; ?>

                                <form method="post" style="display:inline">
                                    <input type="hidden" name="edit_gateway" value="<?php echo esc_attr($id); ?>">
                                    <button type="submit" name="dfes_edit_gateway" class="btn btn-outline-primary">Edit</button>
                                </form>

                                <form method="post" style="display:inline" onsubmit="return confirm('Delete this gateway?');">
                                    <?php wp_nonce_field('dfes_delete_gateway'); ?>
                                    <input type="hidden" name="delete_gateway" value="<?php echo esc_attr($id); ?>">
                                    <button type="submit" name="dfes_delete_gateway" class="btn btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No gateways configured yet.</p>
        <?php endif; ?>
    </div>

    <script>
    function addParam() {
        let div = document.createElement('div');
        div.className = 'param';
        div.style.marginBottom = "6px";
        div.innerHTML = `
            <input type="text" name="params[keys][]" placeholder="param_name" class="regular-text" style="width:25%;">
            <input type="text" name="params[values][]" placeholder="param_value (use {mobile}, {message})" class="regular-text" style="width:50%;">
            <button type="button" class="button button-small" onclick="this.parentNode.remove()">❌</button>
        `;
        document.getElementById('gateway-params').appendChild(div);
    }
    </script>
    <?php
}
