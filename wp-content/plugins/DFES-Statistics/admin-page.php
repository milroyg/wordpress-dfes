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

$last_updated_timestamp = get_option('incident_chart_csv_last_updated');
$last_updated = $last_updated_timestamp ? date_i18n('F j, Y g:i a', $last_updated_timestamp) : 'Not cached yet';

$uploaded_data = fetch_csv_from_local_path();

// 👉 Replace this with your actual published CSV link (not the edit link)
$csv_url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vT3_xKr3khhEIr5QqAKaTs9b4xUnZlgOjyfBMmsclrt2fYfxJfXGlMKMltEnCAKBFDjqm5MkGPntnr4/pub?output=csv';
$google_data = fetch_google_sheet_csv($csv_url);
?>
<?php if ($uploaded_data && get_option('incident_chart_csv_path')): ?>
    <div style="margin-top: 20px;">
        <strong>📂 Uploaded File:</strong> <?php echo basename(get_option('incident_chart_csv_path')); ?>
        <button id="delete-uploaded-csv" class="button button-secondary" style="margin-left:10px;">🗑️ Remove</button>
    </div>
<?php endif; ?>
<hr>
    <h2>🌐 Google Sheet Data</h2>

    <div style="margin-bottom: 15px;">
        <a href="https://docs.google.com/spreadsheets/d/1SLO1O_1T2HtMTEIFhcTJC6vEZj6frf13zUMkmnYb3EU/edit?gid=1007388713#gid=1007388713" 
           target="_blank" 
           class="button button-primary">
           ✏️ Edit Google Sheet
        </a>
    </div>
    <div class="wrap">
    <h1>Incident Chart CSV</h1>

    <form method="post">
        <?php wp_nonce_field('refresh_google_csv_action', 'refresh_google_csv_nonce'); ?>
        <input type="submit" name="refresh_google_csv" class="button button-primary" value="Refresh Google Sheet CSV" />
    </form>

    <?php if (isset($_GET['refreshed']) && $_GET['refreshed'] === '1') : ?>
        <div class="updated notice"><p>Google Sheet CSV has been refreshed.</p></div>
    <?php endif; ?>
</div>

<div class="wrap">
    <h1>Incident Chart CSV</h1>

    <p><strong>Last cached:</strong> <?= esc_html($last_updated); ?></p>

    <form method="post">
        <?php wp_nonce_field('refresh_google_csv_action', 'refresh_google_csv_nonce'); ?>
        <input type="submit" name="refresh_google_csv" class="button button-primary" value="Refresh Google Sheet CSV" />
    </form>

    <?php if (isset($_GET['refreshed']) && $_GET['refreshed'] === '1') : ?>
        <div class="updated notice"><p>Google Sheet CSV has been refreshed successfully.</p></div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    $('#csv-upload-form').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'incident_chart_upload_csv');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#upload-result').html('<div class="updated"><p>' + response.data.message + '</p></div>');
                    location.reload();
                } else {
                    $('#upload-result').html('<div class="error"><p>' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $('#upload-result').html('<div class="error">Failed to upload.</div>');
            }
        });
    });

    // ✅ Move this part inside!
    $('#delete-uploaded-csv').click(function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete the uploaded CSV file?')) return;

        $.post(ajaxurl, {
            action: 'incident_chart_delete_csv'
        }, function(response) {
            if (response.success) {
                $('#upload-result').html('<div class="updated"><p>' + response.data.message + '</p></div>');
                location.reload();
            } else {
                $('#upload-result').html('<div class="error"><p>' + response.data.message + '</p></div>');
            }
        });
    });
});
</script>