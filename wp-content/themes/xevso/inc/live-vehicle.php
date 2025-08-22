<?php
// inc/live-map.php

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
