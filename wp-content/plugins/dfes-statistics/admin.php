<?php
function fetch_csv_from_local_path() {
    $csv_path = get_option('incident_chart_csv_path');
    if ($csv_path && file_exists($csv_path)) {
        $lines = file($csv_path);
        return array_map('str_getcsv', $lines);
    }
    return false;
}

function fetch_google_sheet_csv($csv_url) {
    $response = wp_remote_get($csv_url);
    if (is_wp_error($response)) return false;
    $csv = wp_remote_retrieve_body($response);
    $lines = explode("\n", $csv);
    return array_map('str_getcsv', $lines);
}

// Get last updated timestamp
$last_updated_timestamp = get_option('incident_chart_csv_last_updated');
$last_updated = $last_updated_timestamp 
    ? date_i18n('F j, Y g:i a', $last_updated_timestamp) 
    : 'Not cached yet';

// Local uploaded CSV
$uploaded_data = fetch_csv_from_local_path();

// 👉 Replace with your actual published CSV link
$csv_url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vT3_xKr3khhEIr5QqAKaTs9b4xUnZlgOjyfBMmsclrt2fYfxJfXGlMKMltEnCAKBFDjqm5MkGPntnr4/pub?output=csv';
$google_data = fetch_google_sheet_csv($csv_url);
?>

<div class="wrap">
    <h1>📊 Incident Chart Data Management</h1>

    <!-- Uploaded File Section -->
    <?php if ($uploaded_data && get_option('incident_chart_csv_path')): ?>
        <div class="notice notice-info" style="padding:15px; margin-bottom:20px;">
            <strong>📂 Uploaded File:</strong> 
            <?= esc_html(basename(get_option('incident_chart_csv_path'))); ?>
            <button id="delete-uploaded-csv" class="button button-secondary" style="margin-left:10px;">🗑️ Remove</button>
        </div>
    <?php endif; ?>

    <!-- Google Sheet Section -->
    <div class="card" style="padding:20px; margin-top:20px; max-width:800px;">
        <h2>🌐 Google Sheet Integration</h2>
        
        <p><strong>Last cached:</strong> <?= esc_html($last_updated); ?></p>

        <div style="margin:15px 0;">
            <a href="https://docs.google.com/spreadsheets/d/1SLO1O_1T2HtMTEIFhcTJC6vEZj6frf13zUMkmnYb3EU/edit?gid=1007388713#gid=1007388713" 
               target="_blank" 
               class="button button-secondary">
               ✏️ Edit Google Sheet
            </a>
        </div>

        <form method="post" style="margin-top:15px;">
            <?php wp_nonce_field('refresh_google_csv_action', 'refresh_google_csv_nonce'); ?>
            <input type="submit" name="refresh_google_csv" class="button button-primary" value="🔄 Refresh Google Sheet CSV" />
        </form>

        <?php if (isset($_GET['refreshed']) && $_GET['refreshed'] === '1') : ?>
            <div class="updated notice" style="margin-top:15px;">
                <p>✅ Google Sheet CSV has been refreshed successfully.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle delete uploaded CSV
    $('#delete-uploaded-csv').click(function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete the uploaded CSV file?')) return;

        $.post(ajaxurl, {
            action: 'incident_chart_delete_csv'
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data.message || 'Failed to delete the CSV.');
            }
        });
    });
});
</script>
