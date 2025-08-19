<?php
// Get last updated timestamp
$last_updated_timestamp = get_option('incident_chart_csv_last_updated');
$last_updated = $last_updated_timestamp 
    ? date_i18n('F j, Y g:i a', $last_updated_timestamp) 
    : 'Not cached yet';
?>

<div class="wrap">
    <h1>📊 Incident Chart Data Management</h1>

    <div class="card">
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
