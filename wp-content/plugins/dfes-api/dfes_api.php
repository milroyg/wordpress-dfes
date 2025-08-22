<?php
/*
Plugin Name: DFES API
Description: Custom REST API to handle DFES incident data with async notifications and admin UI.
Version: 1.1
Author: Milroy Gomes
*/

// =============================
// 1️⃣ ACTIVATION/DEACTIVATION
// =============================
register_activation_hook(__FILE__, 'dfes_api_on_activate');
register_deactivation_hook(__FILE__, 'dfes_api_on_deactivate');

function dfes_api_on_activate() {
    dfes_api_create_tables();
    dfes_api_create_log_table();
    dfes_api_seed_default_options();

    // Schedule purge event
    if (!wp_next_scheduled('dfes_purge_old_records')) {
        wp_schedule_event(time(), 'hourly', 'dfes_purge_old_records');
    }

     // Schedule log cleanup daily
    if (!wp_next_scheduled('dfes_notifications_log_cleanup')) {
        wp_schedule_event(time(), 'daily', 'dfes_notifications_log_cleanup');
    }
}

function dfes_api_on_deactivate() {
    wp_clear_scheduled_hook('dfes_purge_old_records');
}

// =============================
// 2️⃣ MAIN HOOK REGISTRATION
// =============================
add_action('plugins_loaded', 'dfes_api_register_hooks');

function dfes_api_register_hooks() {
    // Rewrite rules
    add_action('init', 'dfes_api_add_rewrite_rules');

    // Custom query vars
    add_filter('query_vars', 'dfes_api_add_query_vars');

    // Template redirects
    add_action('template_redirect', 'dfes_api_template_redirect_handler');

    // Cron purge
    add_action('dfes_purge_old_records', 'dfes_api_purge_old_records');

    // Async notifications
    add_action('dfes_send_notifications_event', 'dfes_send_notifications_async', 10, 2);
}

// =============================
// 3️⃣ DB TABLE CREATION
// =============================
function dfes_api_create_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $prefix = $wpdb->prefix;

    // 🚒 Incidents Table
    $incidents_table = $prefix . 'dfes_incidents';
    $sql1 = "CREATE TABLE $incidents_table (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        dsr_id VARCHAR(50) NOT NULL,
        date VARCHAR(50) NOT NULL,
        outtime VARCHAR(10),
        intime VARCHAR(10),
        station VARCHAR(100),
        call_type VARCHAR(100),
        activity_live VARCHAR(255),
        near VARCHAR(255),
        at VARCHAR(255),
        vehicle VARCHAR(100),
        taluka VARCHAR(100),
        village VARCHAR(100),
        activity_sms VARCHAR(255),
        PRIMARY KEY (id),
        KEY dsr_id (dsr_id),
        KEY station (station),
        KEY date_idx (date)
    ) $charset_collate;";

    // 👥 Contacts Table
    $contacts_table = $prefix . 'dfes_contacts';
    $sql2 = "CREATE TABLE $contacts_table (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        email VARCHAR(150) DEFAULT NULL,
        status TINYINT(1) DEFAULT 1,
        PRIMARY KEY (id),
        KEY status (status)
    ) $charset_collate;";

    $stations_table = $prefix . 'dfes_contact_stations';
$sql3 = "CREATE TABLE $stations_table (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    contact_id BIGINT(20) UNSIGNED NOT NULL,
    station VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    KEY contact_station (contact_id, station)
) $charset_collate;";


    // Include dbDelta
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql1);
    dbDelta($sql2);
    dbDelta($sql3);
}


function dfes_api_create_log_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'dfes_notifications_log';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        created_at DATETIME NOT NULL,
        channel VARCHAR(20) NOT NULL,               -- 'sms' or 'email' or 'system'
        recipient VARCHAR(191) NOT NULL,            -- phone or email or 'none'
        station VARCHAR(100) DEFAULT NULL,          -- matched station
        dsr_id VARCHAR(50) DEFAULT NULL,
        message TEXT,
        status VARCHAR(20) NOT NULL,                -- 'success' | 'error'
        error_message TEXT,
        PRIMARY KEY (id),
        KEY created_at (created_at),
        KEY channel (channel),
        KEY recipient (recipient)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function dfes_api_seed_default_options() {
    $defaults = array(
        // HydGW defaults
        'hydgw_username'       => '',
        'hydgw_password'       => '',
        'hydgw_senderid'       => 'GOFIRE',
        'hydgw_dlt_entity_id'  => '',
        'hydgw_dlt_template_id'=> '',
        'hydgw_route'          => '',
        'hydgw_base_url'       => 'https://hydgw.sms.gov.in/failsafe/MLink',

        // Other defaults
        'notify_all'           => 0,
        'logging_enabled'      => 1,
    );

    $current = get_option('dfes_settings', array());
    update_option('dfes_settings', wp_parse_args($current, $defaults));
}

// =============================
// 4️⃣ REWRITE RULES & QUERY VARS
// =============================
function dfes_api_add_rewrite_rules() {
    add_rewrite_rule('^dfes/data/live/update/?$', 'index.php?dfes_update=1', 'top');
    add_rewrite_rule('^disaster-management/live-calls/data/?$', 'index.php?dfes_live_calls=1', 'top');
}

function dfes_api_add_query_vars($vars) {
    $vars[] = 'dfes_update';
    $vars[] = 'dfes_live_calls';
    return $vars;
}

// =============================
// 5️⃣ TEMPLATE REDIRECT HANDLER
// =============================
function dfes_api_template_redirect_handler() {
    if (get_query_var('dfes_update')) {
        $request = new WP_REST_Request('GET');
        foreach ($_REQUEST as $key => $value) {
            $request->set_param($key, $value);
        }
        $response = dfes_api_handle_request($request);
        wp_send_json($response->get_data(), $response->get_status());
    }

    if (get_query_var('dfes_live_calls')) {
        $response = dfes_api_fetch_live_calls(new WP_REST_Request('GET'));
        wp_send_json($response->get_data(), $response->get_status());
    }
}

// =============================
// 6️⃣ API HANDLER - INSERT/UPDATE
// =============================
function dfes_api_handle_request(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dfes_incidents';
    $params = $request->get_params();

    // Sanitize
    $dsr_id        = sanitize_text_field($params['dsr_id'] ?? '');
    $date          = sanitize_text_field($params['date'] ?? '');
    $outtime       = sanitize_text_field($params['outtime'] ?? '');
    $intime        = sanitize_text_field($params['intime'] ?? '');
    $station       = sanitize_text_field($params['station'] ?? '');
    $call_type     = sanitize_text_field($params['call_type'] ?? '');
    $activity_live = sanitize_text_field($params['activity_live'] ?? '');
    $near          = sanitize_text_field($params['near'] ?? '');
    $at            = sanitize_text_field($params['at'] ?? '');
    $vehicle       = sanitize_text_field($params['vehicle'] ?? '');
    $taluka        = sanitize_text_field($params['taluka'] ?? '');
    $village       = sanitize_text_field($params['village'] ?? '');
    $activity_sms  = sanitize_text_field($params['activity_sms'] ?? '');

    // Time validation
    date_default_timezone_set('Asia/Kolkata');
    $current_timestamp = time();
    $input_timestamp   = intval($date);
    $one_hour_before   = $current_timestamp - 3600;

    if ($input_timestamp < $one_hour_before) {
        return new WP_REST_Response(['status' => 'error', 'message' => 'OUTDATED TIME - Data not stored.'], 400);
    }
    if ($input_timestamp > $current_timestamp) {
        return new WP_REST_Response(['status' => 'error', 'message' => 'FUTURE TIME - Data not stored.'], 400);
    }

    // Insert or update
    $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE dsr_id = %s", $dsr_id));

    if ($existing) {
        $wpdb->update(
            $table_name,
            compact('date', 'outtime', 'intime', 'station', 'call_type', 'activity_live', 'near', 'at', 'vehicle', 'taluka', 'village', 'activity_sms'),
            ['dsr_id' => $dsr_id]
        );

        // Do NOT send notifications on update
        return new WP_REST_Response(['status'=>'success','message'=>'Data updated','data'=>$params],200);
    } else {
        $wpdb->insert(
            $table_name,
            compact('dsr_id','date','outtime','intime','station','call_type','activity_live','near','at','vehicle','taluka','village','activity_sms')
        );

        // ✅ Schedule async notifications on insert (non-blocking)
        $payload = array(
            'station'       => $station,
            'outtime'       => $outtime,
            'activity_live' => $activity_live,
            'near'          => $near,
            'at'            => $at,
            'village'       => $village,
            'dsr_id'        => $dsr_id,
        );
        wp_schedule_single_event(time(), 'dfes_send_notifications_event', array($payload, time()));

        return new WP_REST_Response(['status'=>'success','message'=>'Data inserted'],201);
    }
}

// =============================
// 7️⃣ ASYNC NOTIFICATIONS + HELPERS
// =============================
function dfes_send_notifications_async($payload, $scheduled_at) {
    $settings = get_option('dfes_settings', array());
    $notify_all = !empty($settings['notify_all']);

    $station       = sanitize_text_field($payload['station'] ?? '');
    $outtime       = sanitize_text_field($payload['outtime'] ?? '');
    $activity_live = sanitize_text_field($payload['activity_live'] ?? '');
    $near          = sanitize_text_field($payload['near'] ?? '');
    $at            = sanitize_text_field($payload['at'] ?? '');
    $village       = sanitize_text_field($payload['village'] ?? '');
    $dsr_id        = sanitize_text_field($payload['dsr_id'] ?? '');

    // Compose one message
    $message = "Fire Station: $station\nTime: $outtime\nIncident: $activity_live\nNear: $near\nAt: $at\nArea: $village\nDFES,Goa.";

    $contacts = dfes_get_contacts_for_station($station, $notify_all);

    if (!$contacts) {
        dfes_log_notification_event('system', 'none', $station, $dsr_id, $message, 'error', 'No contacts found for notification criteria.');
        return;
    }

    foreach ($contacts as $c) {
        $mobile = $c['phone_number'];
        $email  = $c['email'];

        if (!empty($mobile)) {
           dfes_send_sms_hydgw($mobile, $message, $station, $dsr_id);
        }
        if (!empty($email)) {
            dfes_send_email_wp($email, "DFES Incident Alert - $station", $message, $station, $dsr_id);
        }
    }
}  // Fetch contacts (notify_all or station-specific with FIND_IN_SET)
  

/**
 * Return active contacts. If $notify_all is true, returns all active contacts.
 * 
 * 
 */
function dfes_get_contacts_for_station($station, $notify_all = false) {
    global $wpdb;
    $contacts_table = $wpdb->prefix . 'dfes_contacts';
    $stations_table = $wpdb->prefix . 'dfes_contact_stations';

    if ($notify_all) {
        // If notify all, send to all active contacts (with any station)
        return $wpdb->get_results(
            "SELECT c.*
             FROM $contacts_table c
             WHERE c.status = 1",
            ARRAY_A
        );
    }

    // Station-specific: join with stations table
    return $wpdb->get_results(
        $wpdb->prepare(
            "SELECT c.*
             FROM $contacts_table c
             INNER JOIN $stations_table s ON c.id = s.contact_id
             WHERE c.status = 1 AND s.station = %s",
            $station
        ),
        ARRAY_A
    );
}

function dfes_send_sms_hydgw($mobile, $message, $station, $dsr_id) {
    $settings  = get_option('dfes_settings', array());

    $username     = trim($settings['hydgw_username'] ?? '');
    $password     = trim($settings['hydgw_password'] ?? '');
    $senderid     = trim($settings['hydgw_senderid'] ?? 'GOFIRE');
    $dlt_entity   = trim($settings['hydgw_dlt_entity_id'] ?? '');
    $dlt_template = trim($settings['hydgw_dlt_template_id'] ?? '');
    $route        = trim($settings['hydgw_route'] ?? ''); 
    $base_url     = trim($settings['hydgw_base_url'] ?? 'https://hydgw.sms.gov.in/failsafe/MLink');
    $log_on       = !empty($settings['logging_enabled']);

    if (!$username || !$senderid) {
        if ($log_on) {
            dfes_log_notification_event('sms', $mobile, $station, $dsr_id, $message, 'error', 'HydGW settings missing.');
        }
        return false;
    }

    $body = array(
        'username'       => $username,
        'pin'            => $password,
        'signature'      => $senderid,
        'mnumber'        => '91' . preg_replace('/\D/', '', $mobile),
        'message'        => $message,
        'dlt_entity_id'  => $dlt_entity,
        'dlt_template_id'=> $dlt_template,
        'hydgw_route'           => $hydgw_route, 
    );

    $response = wp_remote_post($base_url, array(
        'body'    => $body,
        'timeout' => 20,
    ));

    if (is_wp_error($response)) {
        if ($log_on) {
            dfes_log_notification_event('sms', $mobile, $station, $dsr_id, $message, 'error', $response->get_error_message());
        }
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    $body_response = wp_remote_retrieve_body($response);

    $success = ($code >= 200 && $code < 300);

    if ($log_on) {
        dfes_log_notification_event(
            'sms',
            $mobile,
            $station,
            $dsr_id,
            $message,
            $success ? 'success' : 'error',
            $success ? '' : ("HTTP $code: " . substr($body_response, 0, 500))
        );
    }

    return $success;
}



function dfes_send_email_wp($to, $subject, $message, $station, $dsr_id) {
    $settings = get_option('dfes_settings', array());
    $log_on     = !empty($settings['logging_enabled']);

    $headers = array();
    if ($from_email) {
        $headers[] = 'From: ' . ($from_name ? $from_name : 'DFES Goa') . " <{$from_email}>";
    }
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';

    $ok = wp_mail($to, $subject, $message, $headers);

    if ($log_on) {
        dfes_log_notification_event('email', $to, $station, $dsr_id, $message, $ok ? 'success' : 'error', $ok ? '' : 'wp_mail returned false');
    }

    return $ok;
}

function dfes_log_notification_event($channel, $recipient, $station, $dsr_id, $message, $status, $error_message = '') {
    $settings = get_option('dfes_settings', array());
    if (empty($settings['logging_enabled'])) {
        return;
    }

    // ✅ Only log errors
    if (strtolower($status) === 'success') {
        return; 
    }

    global $wpdb;
    $table = $wpdb->prefix . 'dfes_notifications_log';

    $wpdb->insert(
        $table,
        array(
            'created_at'    => current_time('mysql', true),
            'channel'       => sanitize_text_field($channel),
            'recipient'     => sanitize_text_field($recipient),
            'station'       => sanitize_text_field($station),
            'dsr_id'        => sanitize_text_field($dsr_id),
            'message'       => $message, // Keep raw in case HTML/debug needed
            'status'        => sanitize_text_field($status),
            'error_message' => $error_message,
        )
    );
}



// =============================
// 8️⃣ API HANDLER - LAST 24 HOURS
// =============================
function dfes_api_fetch_live_calls(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dfes_incidents';

    date_default_timezone_set('Asia/Kolkata');
    $now = time();
    $past_24_hours = $now - (24 * 60 * 60);

    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_name WHERE date >= %d ORDER BY date DESC", $past_24_hours),
        ARRAY_A
    );

    if (empty($results)) {
        return new WP_REST_Response([], 200);
    }

    // Transform keys
    $retitled = array_map(function($row) {
        return [
            'dsr_id'      => $row['dsr_id'],
            'date'        => $row['date'],
            'outtime'     => $row['outtime'],
            'intime'      => $row['intime'],
            'station'     => $row['station'],
            'type'        => $row['call_type'],
            'description' => $row['activity_live'],
            'near'        => $row['near'],
            'at'          => $row['at'],
            'vehicle'     => $row['vehicle'],
            'taluka'      => $row['taluka'],
            'village'     => $row['village'],
            'activity_sms'=> $row['activity_sms']
        ];
    }, $results);

    return new WP_REST_Response($retitled, 200);
}

// =============================
// 9️⃣ CRON - PURGE OLD RECORDS
// =============================
function dfes_api_purge_old_records() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dfes_incidents';

    date_default_timezone_set('Asia/Kolkata');
    $cutoff = time() - (24 * 60 * 60);

    $wpdb->query(
        $wpdb->prepare("DELETE FROM $table_name WHERE date < %d", $cutoff)
    );
}
// =============================
//  CRON - CLEANUP NOTIFICATION LOGS
// =============================
add_action('dfes_notifications_log_cleanup', 'dfes_cleanup_old_logs');

function dfes_cleanup_old_logs() {
    global $wpdb;
    $table = $wpdb->prefix . 'dfes_notifications_log';

    // Delete records older than 30 days
    $wpdb->query("DELETE FROM $table WHERE created_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY)");
}

// =============================
// 🔟 ADMIN UI (separate file)
// =============================
// Keep all admin UI in admin.php
require_once plugin_dir_path(__FILE__) . 'admin.php';
