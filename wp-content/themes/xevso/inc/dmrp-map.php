 <?php
/**
 * DMRP Leaflet Map Shortcode and Assets
 */

// Load Leaflet and MarkerCluster only when shortcode is present
add_action('wp_enqueue_scripts', function () {
    if (is_singular() && has_shortcode(get_post()->post_content, 'dmrp_map')) {
//         Leaflet core
       wp_enqueue_style('leaflet-css', get_template_directory_uri() . '/assets/css/leaflet.css', [], '1.9.4');
        wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true);

        // MarkerCluster plugin
        wp_enqueue_style('leaflet-markercluster-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css', [], '1.5.3');
        wp_enqueue_script('leaflet-markercluster-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js', ['leaflet-js'], '1.5.3', true);

	}
});

// Register the shortcode
add_shortcode('dmrp_map', function () {
    ob_start(); ?>
    <div id="map-container" style="display: flex; height: 600px; width: 100%;">
        <div id="filter-container" style="width: 350px; background-color: #f8f9fa; padding: 20px; border-right: 1px solid #ccc; box-shadow: 2px 0 5px rgba(0,0,0,0.1); overflow-y: auto; font-family: Arial, sans-serif;">
            <div id="filter-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;">Filter Categories</h3>
                <button id="select-none" style="background: none; border: none; color: #2072AF; cursor: pointer; font-size: 14px;">Select None</button>
            </div>
            <div id="filter-controls" style="margin-top: 10px;"></div>
            <button id="apply-filter" style="margin-top: 15px; display: block; width: 100%; padding: 12px; background: #2072AF; color: white; border: none; font-size: 16px; cursor: pointer; transition: 0.3s;">Apply</button>
        </div>
        <div id="dmrpmap" style="flex: 1;"></div>
    </div>
    <?php
    return ob_get_clean();
});
 