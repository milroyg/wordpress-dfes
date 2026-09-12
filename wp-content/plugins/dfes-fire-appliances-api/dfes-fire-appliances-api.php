<?php
/**
 Plugin Name: DFES Fire Appliances API
 Description: Fetches data from the Goa Fire Department GPS API and saves it
 to a GeoJSON file.
 Version: 1.1
 Author: Milroy Gomes
 */

if (!defined('ABSPATH')) {
  exit;
}

class DFES_Fire_Appliances_API {

  private $api_base_url = 'https://3.7.238.246/webservice';

  private $username = 'cnt-fire.goa@nic.in';

  private $password = 'cnt@123';

  private $company_name = 'Directorate of Fire Emergency Services';

  private $project_id = 37;

  private $option_name = 'dfes_fire_trucks_geojson';

  public function __construct() {
    add_action('dfes_fire_appliances_cron_event', [
      $this,
      'fetch_and_cache_data',
    ]);
    add_filter('cron_schedules', [$this, 'add_cron_schedules']);

    add_action('init', [$this, 'add_rewrite_rules']);
    add_filter('query_vars', [$this, 'add_query_vars']);
    add_action('template_redirect', [$this, 'handle_geojson_output']);

    if (is_admin()) {
      add_action('admin_menu', [$this, 'add_admin_menu']);
      add_action('admin_post_dfes_refresh_data', [
        $this,
        'handle_manual_refresh',
      ]);
    }
  }

  public function activate() {
    $this->add_rewrite_rules();
    flush_rewrite_rules();

    // Ensure the schedule is added to the list before scheduling
    add_filter('cron_schedules', [$this, 'add_cron_schedules']);

    if (!wp_next_scheduled('dfes_fire_appliances_cron_event')) {
      wp_schedule_event(time(), 'every_minute', 'dfes_fire_appliances_cron_event');
    }
    $this->fetch_and_cache_data();
  }

  public function deactivate() {
    wp_clear_scheduled_hook('dfes_fire_appliances_cron_event');
    flush_rewrite_rules();
  }

  public static function plugin_activate() {
    $instance = new self();
    $instance->activate();
  }

  public static function plugin_deactivate() {
    $instance = new self();
    $instance->deactivate();
  }

  public function add_cron_schedules($schedules) {
    if (!isset($schedules['every_minute'])) {
      $schedules['every_minute'] = [
        'interval' => 60,
        'display' => __('Every Minute'),
      ];
    }
    return $schedules;
  }

  public function add_rewrite_rules() {
    add_rewrite_rule('^dfes-goa-fire-appliance-api\.geojson$', 'index.php?dfes_fire_appliance_geojson=1', 'top');
  }

  public function add_query_vars($vars) {
    $vars[] = 'dfes_fire_appliance_geojson';
    return $vars;
  }

  public function handle_geojson_output() {
    if (get_query_var('dfes_fire_appliance_geojson')) {
      $data = get_option($this->option_name);
      if (!$data) {
        $data = [
          'type' => 'FeatureCollection',
          'features' => [],
          'metadata' => ['error' => 'No data found'],
        ];
      }
      header('Content-Type: application/json; charset=utf-8');
      echo is_string($data) ? $data : json_encode($data);
      exit;
    }
  }

  public function add_admin_menu() {
    add_options_page(
      'DFES Fire Appliances',
      'DFES Fire Appliances',
      'manage_options',
      'dfes-fire-appliances',
      [$this, 'admin_page']
    );
  }

  public function admin_page() {
    ?>
    <div class="wrap">
      <h1>DFES Fire Appliances API</h1>
      <p>This plugin fetches fire truck data and saves it to the WordPress
        database.</p>
      <p><strong>Fixed URL:</strong> <a
          href="<?php echo home_url('/goa-fire-trucks.geojson'); ?>"
          target="_blank"><?php echo home_url('/goa-fire-trucks.geojson'); ?></a>
      </p>
      <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
        <input type="hidden" name="action" value="dfes_refresh_data">
        <?php wp_nonce_field('dfes_refresh_nonce'); ?>
        <?php submit_button('Refresh Data Now'); ?>
      </form>
    </div>
    <?php
  }

  public function handle_manual_refresh() {
    check_admin_referer('dfes_refresh_nonce');
    if (!current_user_can('manage_options')) {
      wp_die('Unauthorized');
    }
    $this->fetch_and_cache_data();
    wp_redirect(admin_url('options-general.php?page=dfes-fire-appliances&refreshed=1'));
    exit;
  }

  private function is_valid_goa_coordinate($value, $type) {
    if (!$value) {
      return FALSE;
    }

    $num = (float) str_replace(',', '.', $value);

    if ($type === 'lat') {
      return $num >= 14.5 && $num <= 16.0;
    }
    elseif ($type === 'lng') {
      return $num >= 73.5 && $num <= 74.5;
    }
    return ($num >= 14.5 && $num <= 16.0) || ($num >= 73.5 && $num <= 74.5);
  }

  private function generate_access_token() {
    $token_url = "{$this->api_base_url}?token=generateAccessToken";

    $request_variations = [
      ['Username' => $this->username, 'password' => $this->password],
      ['username' => $this->username, 'password' => $this->password],
      ['Username' => $this->username, 'Password' => $this->password],
      ['username' => $this->username, 'Password' => $this->password],
      ['user' => $this->username, 'pass' => $this->password],
      ['User' => $this->username, 'Pass' => $this->password],
    ];

    foreach ($request_variations as $i => $request_body) {
      $response = wp_remote_post($token_url, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($request_body),
        'sslverify' => FALSE,
        'timeout' => 30,
      ]);

      if (is_wp_error($response) or wp_remote_retrieve_response_code($response) !== 200) {
        continue;
      }

      $body = wp_remote_retrieve_body($response);
      $token_data = json_decode($body, TRUE);

      if (!$token_data || (isset($token_data['result']) && ($token_data['result'] == 0))) {
        continue;
      }

      $token = $token_data['token'] ?? $token_data['Token'] ?? $token_data['access_token'] ?? $token_data['accessToken'] ?? NULL;

      if (!$token && isset($token_data['data'])) {
        $token = $token_data['data']['token'] ?? $token_data['data']['Token'] ?? $token_data['data'];
      }

      if (!$token && is_string($token_data)) {
        $token = $token_data;
      }

      if (!$token && isset($token_data['result']) && $token_data['result'] == 1) {
        foreach ($token_data as $key => $value) {
          if ($key !== 'result' && $key !== 'message' && is_string($value)) {
            $token = $value;
            break;
          }
        }
      }

      if ($token) {
        return $token;
      }
    }

    throw new Exception('All authentication attempts failed.');
  }

  private function fetch_live_data($auth_token) {
    $data_url = "{$this->api_base_url}?token=getTokenBaseLiveData&ProjectId={$this->project_id}";

    $response = wp_remote_post($data_url, [
      'headers' => [
        'Content-Type' => 'application/json',
        'auth-code' => $auth_token,
      ],
      'body' => json_encode([
        'company_names' => $this->company_name,
        'format' => 'json',
      ]),
      'sslverify' => FALSE,
      'timeout' => 30,
    ]);

    if (is_wp_error($response)) {
      throw new Exception("Live data fetch failed: " . $response->get_error_message());
    }

    $body = wp_remote_retrieve_body($response);

    return json_decode($body, TRUE);
  }

  public function fetch_and_cache_data() {
    error_log('DFES Cron: Running fetch_and_cache_data');
    try {
      $auth_token = $this->generate_access_token();
      $json_data = $this->fetch_live_data($auth_token);

      $rows = $json_data['root']['VehicleData'] ?? [];

      $features = [];
      foreach ($rows as $row) {
        $lat = (float) str_replace(',', '.', $row['Latitude'] ?? 0);
        $lng = (float) str_replace(',', '.', $row['Longitude'] ?? 0);

        if ($this->is_valid_goa_coordinate($lat, 'lat') && $this->is_valid_goa_coordinate($lng, 'lng')) {
          $properties = $row;
          unset($properties['Latitude'], $properties['Longitude']);

          $features[] = [
            'type' => 'Feature',
            'geometry' => [
              'type' => 'Point',
              'coordinates' => [$lng, $lat],
            ],
            'properties' => $properties,
          ];
        }
      }

      $result = [
        'type' => 'FeatureCollection',
        'metadata' => [
          'timestamp' => current_time('c'),
          'source' => 'Directorate of Fire Emergency Services, Govt. of Goa',
          'count' => count($features),
        ],
        'features' => $features,
      ];

      update_option($this->option_name, $result);
    }
    catch (Exception $e) {
    }
  }

}

new DFES_Fire_Appliances_API();

register_activation_hook(__FILE__, ['DFES_Fire_Appliances_API', 'plugin_activate']);
register_deactivation_hook(__FILE__, ['DFES_Fire_Appliances_API', 'plugin_deactivate']);

// =============================
// Live Vehicle Data & Map Shortcode
// =============================
add_action('wp_ajax_dfes_get_vehicle_data', 'dfes_get_vehicle_data_ajax');
add_action('wp_ajax_nopriv_dfes_get_vehicle_data', 'dfes_get_vehicle_data_ajax');

function dfes_get_vehicle_data_ajax() {
    $cache_key = 'dfes_vehicle_data_ajax';
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        wp_send_json_success($cached);
    }

    $url = 'https://gpsmiles.live//webservice?token=getLiveData&user=cnt-fire.goa@nic.in&pass=cnt@123&company=Directorate of Fire Emergency Services&format=json';
    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        wp_send_json_error('Unable to fetch vehicle data.');
    }

    $body = wp_remote_retrieve_body($response);
    $json = json_decode($body, true);
    $vehicles = $json['root']['VehicleData'] ?? [];

    set_transient($cache_key, $vehicles, 30);
    wp_send_json_success($vehicles);
}

add_shortcode('live_vehicle_map', 'dfes_live_vehicle_map_shortcode');

function dfes_live_vehicle_map_shortcode() {
    ob_start();
    ?>
    <div id="vehicle-map" style="width: 100%; height: 100vh; margin: 20px 0;"></div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('vehicle-map').setView([15.5, 73.8], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

   // Define custom icons
    const redIcon = new L.Icon({
        iconUrl: '/wp-content/uploads/vehicle-markers/fire-brigade.png',
        iconSize: [40, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        shadowSize: [41, 41]
    });

    const yellowIcon = new L.Icon({
        iconUrl: '/wp-content/uploads/vehicle-markers/firebrigade_inactive.png',
        iconSize: [40, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        shadowSize: [41, 41]
    });

    const greenIcon = new L.Icon({
        iconUrl: '/wp-content/uploads/vehicle-markers/firebrigade_running.png',
        iconSize: [40, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        shadowSize: [41, 41]
    });
    let markers = [];

function getStatusIcon(status) {
    switch (status) {
        case 'STOP': return redIcon;
        case 'INACTIVE': return yellowIcon;
        case 'Running': return greenIcon;
        case 'RUNNING': return greenIcon; // just in case
        default: return redIcon;
    }
}

    function updateMap(vehicleData) {
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        vehicleData.forEach(vehicle => {
            const lat = parseFloat(vehicle.Latitude);
            const lng = parseFloat(vehicle.Longitude);
            const name = vehicle.Vehicle_Name || "Unnamed";
            const vehicle_name= vehicle.Vehicle_No || "Unamed";
            const status = vehicle.Status || "stop";

            if (!isNaN(lat) && !isNaN(lng)) {
                const marker = L.marker([lat, lng], {
                    icon: getStatusIcon(status)
                }).addTo(map)
                  .bindPopup(`<strong>${name}</strong><br>${vehicle_name}<br>${vehicle.Location}<br>Status: ${status}`);
              
                  markers.push(marker);
            }
        });
    }

    function fetchVehicleData() {
        fetch('<?php echo admin_url('admin-ajax.php?action=dfes_get_vehicle_data'); ?>')
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    updateMap(res.data);
                } else {
                    console.error('Vehicle data error:', res.data);
                }
            })
            .catch(err => console.error('Fetch error:', err));
    }

    fetchVehicleData();
    setInterval(fetchVehicleData, 30000);
});
</script>

    <?php
    return ob_get_clean();
}
