<?php
/*
Plugin Name: Incident Chart CSV
Description: Displays line charts from CSV data with category filtering.
Version: 1.0
Author: Milroy Gomes
*/

// Enqueue scripts and styles
function incident_chart_enqueue_scripts() {
    // wp_enqueue_style('incident-chart-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
    // wp_enqueue_script('incident-chart-script', plugin_dir_url(__FILE__) . 'js/chart-script.js', array('jquery', 'chart-js'), null, true);
    wp_localize_script('incident-chart-script', 'incidentChart', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'incident_chart_enqueue_scripts');

add_action('admin_menu', 'incident_chart_admin_menu');

function incident_chart_admin_menu() {
    add_menu_page(
        'Incident Chart CSV',
        'Incident Chart CSV',
        'manage_options',
        'incident-chart-csv',
        'incident_chart_admin_page'
    );
}

function incident_chart_admin_page() {
    if (isset($_POST['refresh_google_csv']) && check_admin_referer('refresh_google_csv_action', 'refresh_google_csv_nonce')) {
        incident_chart_fetch_and_cache_csv();
        wp_redirect(admin_url('admin.php?page=incident-chart-csv&refreshed=1'));
        exit;
    }

    include(plugin_dir_path(__FILE__) . 'admin-page.php');
}

function incident_chart_fetch_and_cache_csv() {
    $google_csv_url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vT3_xKr3khhEIr5QqAKaTs9b4xUnZlgOjyfBMmsclrt2fYfxJfXGlMKMltEnCAKBFDjqm5MkGPntnr4/pub?output=csv';

    $response = wp_remote_get($google_csv_url);
    if (is_wp_error($response)) return false;

    $csv_content = wp_remote_retrieve_body($response);

    $upload_dir = wp_upload_dir();
    $csv_file_path = $upload_dir['basedir'] . '/incident-data.csv';

    file_put_contents($csv_file_path, $csv_content);

    // Save file path and timestamp
    update_option('incident_chart_csv_path', $csv_file_path);
    update_option('incident_chart_csv_last_updated', current_time('timestamp'));

    return true;
}


add_action('wp_ajax_incident_chart_upload_csv', 'incident_chart_upload_csv');

function incident_chart_upload_csv() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized']);
        return;
    }

    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(['message' => 'No file uploaded or error occurred']);
        return;
    }

    $file = $_FILES['csv_file'];

    // Validate CSV file type
    $file_type = mime_content_type($file['tmp_name']);
    if ($file_type !== 'text/csv' && $file_type !== 'text/plain') {
        wp_send_json_error(['message' => 'Invalid file type. Only CSV files are allowed.']);
        return;
    }

    // Move the uploaded file
    $upload_dir = wp_upload_dir();
    wp_mkdir_p($upload_dir['path']);
    $target_file = $upload_dir['path'] . '/' . uniqid('incident_', true) . '_' . basename($file['name']);

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        update_option('incident_chart_csv_path', $target_file);
        wp_send_json_success(['message' => 'File uploaded successfully!', 'file' => $target_file]);
    } else {
        wp_send_json_error(['message' => 'Failed to upload file']);
    }
}



// Shortcode to display CSV data with chart, category dropdown, and table
function incident_chart_shortcode() {
    // 1. Check if a cached/uploaded CSV exists
$csv_file_path = get_option('incident_chart_csv_path');
$csv = '';

if ($csv_file_path && file_exists($csv_file_path)) {
    // Load local file
    $csv = file_get_contents($csv_file_path);
} else {
    // 2. Fallback: Fetch directly from Google Sheets
    $google_csv_url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vT3_xKr3khhEIr5QqAKaTs9b4xUnZlgOjyfBMmsclrt2fYfxJfXGlMKMltEnCAKBFDjqm5MkGPntnr4/pub?output=csv';
    $response = wp_remote_get($google_csv_url);
    if (is_wp_error($response)) {
        return '<p>Unable to fetch data from Google Sheet.</p>';
    }
    $csv = wp_remote_retrieve_body($response);

    // Optionally save it as cache
    incident_chart_fetch_and_cache_csv();
}
    $csv_lines = array_map('str_getcsv', explode("\n", trim($csv)));

    if (empty($csv_lines)) {
        return '<p>No data found in Google Sheet.</p>';
    }

    // Extract headers
    $headers = array_shift($csv_lines);

    $data = [];
    $tableData = [];

    foreach ($csv_lines as $row) {
        if (count($row) < 3) continue;

        $stat_type = esc_html(trim($row[0]));
        $year = esc_html(trim($row[1]));
        $figure = (float)trim($row[2]);

        if (!isset($data[$stat_type])) {
            $data[$stat_type] = [];
        }

        $data[$stat_type][] = [
            'year' => $year,
            'figure' => $figure
        ];

        if (
            !isset($tableData[$stat_type]) ||
            (isset($tableData[$stat_type]['year']) && $year > $tableData[$stat_type]['year'])
        ) {
            $tableData[$stat_type] = [
                'year' => $year,
                'figure' => $figure
            ];
        }
        
    }

    $first_category = array_keys($data)[0] ?? '';

    ob_start();
    ?>
    <div class="incident-chart-container">
        <h3>Select Criteria</h3>
        <label for="category-filter" class="screen-reader-text">Select Incident Category</label>
        <select id="category-filter" name="category-filter">
            <?php foreach (array_keys($data) as $stat_type): ?>
                <option value="<?= esc_attr($stat_type) ?>" <?= $stat_type === $first_category ? 'selected' : '' ?>>
                    <?= esc_html($stat_type) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <canvas id="incident-chart" width="800" height="400"></canvas>
    </div>

    <div class="incident-table-container">
        <table id="incident-table" class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>Incident Type</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('incident-chart').getContext('2d');

        // Chart and table data from PHP
        const chartData = <?php echo json_encode($data); ?>;
        const tableData = <?php echo json_encode($tableData); ?>;

        let chart;

        // Function to dynamically round up based on figure size
// Function to round up the maximum value based on range
function roundUp(value) {
    if (value <= 100) {
        return Math.ceil(value / 10) * 10;         // Round up to nearest 10
    } else if (value <= 1000) {
        return Math.ceil(value / 100) * 100;       // Round up to nearest 100
    } else if (value <= 10000) {
        return Math.ceil(value / 500) * 500;       // Round up to nearest 500
    } else if (value <= 100000) {
        return Math.ceil(value / 1000) * 1000;     // Round up to nearest 1000
    } else {
        return Math.ceil(value / 5000) * 5000;     // Round up to nearest 5000
    }
}

// Function to round down the minimum value based on range
function roundDown(value) {
    if (value <= 100) {
        return Math.floor(value / 10) * 10;         // Round down to nearest 10
    } else if (value <= 1000) {
        return Math.floor(value / 100) * 100;       // Round down to nearest 100
    } else if (value <= 10000) {
        return Math.floor(value / 500) * 500;       // Round down to nearest 500
    } else if (value <= 100000) {
        return Math.floor(value / 1000) * 1000;     // Round down to nearest 1000
    } else {
        return Math.floor(value / 5000) * 5000;     // Round down to nearest 5000
    }
}

// Function to render the chart with dynamic Y-axis scaling
function renderChart(category) {
    if (chart) {
        chart.destroy();
    }

    const selectedData = chartData[category] || [];

    const labels = selectedData.map(item => item.year);
    const tend = selectedData.map(item => item.trend);
    const figures = selectedData.map(item => item.figure);

    // Determine the min and max values
    const minFigure = Math.min(...figures);
    const maxFigure = Math.max(...figures);

    // Round the Y-axis scale to fit the data range
    const roundedMin = roundDown(minFigure);
    const roundedMax = roundUp(maxFigure);

    // Dynamic step size based on the range
    const range = roundedMax - roundedMin;
    let stepSize;

    if (range <= 100) {
        stepSize = 10;                         // Small range: step by 10
    } else if (range <= 1000) {
        stepSize = 100;                        // Medium range: step by 100
    } else if (range <= 10000) {
        stepSize = 500;                        // Larger range: step by 500
    } else if (range <= 50000) {
        stepSize = 1000;                       // Even larger range: step by 1000
    } else {
        stepSize = 5000;                       // Huge range: step by 5000
    }

    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: `Figures for ${category}`,
                data: figures,
                borderColor: 'rgba(0, 123, 255, 1)',    // Blue line color
                 backgroundColor: 'rgba(255, 255, 255, 0)', // Light fill color
                borderWidth: 3,
                pointRadius: 5,                          // Circle size
                pointBackgroundColor: 'rgba(0, 123, 255, 1)', 
                pointHoverRadius: 7,                     // Hover effect size
                fill: true,
                tension: 0.2                             // Slightly curved line
            }]
        },
        
        
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true                      // Hides the legend
                },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItems) {
                            return `${tooltipItems[0].label}`;  // Year in tooltip
                        },
                        label: function(tooltipItem) {
                            return `Figure: ${tooltipItem.raw.toLocaleString()}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Year',
                        color: '#555',
                        font: {
                            size: 14,
                            style: 'italic',
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        autoSkip: false,
                        maxTicksLimit: 20,
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
               y: {
    title: {
        display: true,
        text: 'No. of Units',
        color: '#555',
        font: {
            size: 14,
            style: 'italic',
            weight: 'bold'
        }
    },
    ticks: {
        stepSize: stepSize,               
        callback: function(value) {
            // ✅ Only show whole numbers
            if (Number.isInteger(value)) {
                return value.toLocaleString();
            }
            return null; // hides decimal tick labels
        }
    },
    min: roundedMin,                      
    max: roundedMax                       
}

            }
        }
    });

    // Update the table
    renderTable();
}

function renderTable() {
    const tbody = document.querySelector('#incident-table tbody');
    tbody.innerHTML = '';



    const rows = [
    {
        label: 'Number of Fire Calls',
        keys: ['1. Number of Fire Calls'],
        percentage: '136% ',
        arrow: '<span style="color:green">▲</span>',
        color :' #990000'
    },
    {
        label: 'Emergencies',
        keys: ['2. Number of Non-Fire Emergencies'],
        percentage: '436%',
        arrow: '<span style="color:#990000">▲</span>',
        color :' #990000'
    },
    {
        label: 'Lives Lost (H)',
        keys: [
            '3(a). Number of Human Lives Lost due to Fire',
            '3(c). Number of Human Lives Lost due to other Accidents',
            '4(a). Number of Human Lives injured due to Fire'
        ],
        percentage: '-10%',
         arrow: '<span style="color:green">▼</span>',
        color : 'green'
    },
    {
        label: 'Lives Saved (H)',
        keys: [
            '5(a). Number of Human Lives Saved due to Fires',
            '5(c). Number of Human Lives Saved due to other Accidents'
        ],
        percentage: '257%',
         arrow: '<span style="color:green">▲</span>',
        color : 'green'
    },
    {
        label: 'Property Lost',
        keys: ['7. Amount of property gutted in Fire and other incidents'],
        percentage: '241%',
         arrow: '<span style="color:#990000">▲</span>',
        color :' #990000'
    },
    {
        label: 'Property Saved',
        keys: ['6. Amount of property saved from Fires and other incidents'],
        percentage: '-41%',
         arrow: '<span style="color:#990000">▼</span>',
        color :' #990000'
    },
];


    if (!tableData || typeof tableData !== 'object') {
        tbody.innerHTML = `<tr><td colspan="6">No data available.</td></tr>`;
        return;
    }

   for (const row of rows) {
    let totalFigure = 0;

    for (const key of row.keys) {
        const record = tableData[key];
        if (record) {
            let figure = record.figure || 0;

            if (typeof figure === 'string' && !isNaN(figure)) {
                figure = parseFloat(figure);
            }

            totalFigure += figure;
        }
    }


        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.label}</td>
            <td>${totalFigure.toLocaleString()}<br>2003-2024</td>
              <td style="color: ${row.color}; font-weight: bold;">${row.percentage} ${row.arrow}</td>
        `;
        tbody.appendChild(tr);
    }
}



        // ✅ Render the chart and table for the first category by default
renderChart("<?php echo $first_category; ?>");

        // Update chart and table when category is changed
        document.getElementById('category-filter').addEventListener('change', (e) => {
            const selectedCategory = e.target.value;
            renderChart(selectedCategory);
        });
    });
</script>

    <?php
    return ob_get_clean();
}
add_shortcode('incident_chart', 'incident_chart_shortcode');

add_action('wp_enqueue_scripts', function () {
    global $post;

    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'incident_chart')) {
        wp_enqueue_style(
            'incident-chart-style',
            plugin_dir_url(__FILE__) . 'assets/style.css',
            [],
            '1.0'
        );
    }
});


add_action('wp_ajax_incident_chart_delete_csv', 'incident_chart_delete_csv_file');

function incident_chart_delete_csv_file() {
    $file_path = get_option('incident_chart_csv_path');

    if ($file_path && file_exists($file_path)) {
        unlink($file_path); // Delete the file
        delete_option('incident_chart_csv_path'); // Clear the option

        wp_send_json_success(['message' => 'CSV file deleted successfully.']);
    } else {
        wp_send_json_error(['message' => 'CSV file not found.']);
    }
}


