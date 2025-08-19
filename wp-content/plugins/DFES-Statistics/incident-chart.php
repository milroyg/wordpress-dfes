<?php
/*
Plugin Name: Incident Chart CSV
Description: Displays line charts from CSV data with category filtering.
Version: 1.0
Author: Milroy Gomes
*/
if (!defined('ABSPATH')) exit; // No direct access

// 🔹 Shared helper: fetch Google Sheet CSV
if ( ! function_exists('fetch_google_sheet_csv') ) {
    function fetch_google_sheet_csv($csv_url) {
        $response = wp_remote_get($csv_url);
        if (is_wp_error($response)) {
            return false;
        }
        $csv = wp_remote_retrieve_body($response);
        $lines = explode("\n", $csv);
        return array_map('str_getcsv', $lines);
    }
}


// 🔹 Include admin and shortcode logic
require_once plugin_dir_path(__FILE__) . 'incident-chart.php';

class IncidentChartCSV {

    // Your published Google Sheets CSV link
    private $csv_url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vT3_xKr3khhEIr5QqAKaTs9b4xUnZlgOjyfBMmsclrt2fYfxJfXGlMKMltEnCAKBFDjqm5MkGPntnr4/pub?output=csv';

    public function __construct() {
        // Core hooks
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_menu', [$this, 'register_admin_page']);
        add_shortcode('incident_chart', [$this, 'render_shortcode']);
    }

    /** -------------------------
     * FRONTEND
     * ------------------------- */
    public function enqueue_assets() {
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'incident_chart')) {
            wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
            wp_enqueue_style('incident-chart-style', plugin_dir_url(__FILE__) . 'assets/style.css', [], '1.0');
        }
    }

   

    /** -------------------------
     * ADMIN
     * ------------------------- */
    public function register_admin_page() {
        add_menu_page(
            'Incident Chart CSV',
            'Incident Chart CSV',
            'manage_options',
            'incident-chart-csv',
            [$this, 'render_admin_page']
        );
    }

    public function render_admin_page() {
        // Handle refresh request
        if (isset($_POST['refresh_google_csv']) && check_admin_referer('refresh_google_csv_action', 'refresh_google_csv_nonce')) {
            $this->fetch_and_cache_csv();
            wp_redirect(admin_url('admin.php?page=incident-chart-csv&refreshed=1'));
            exit;
        }

        // Include your existing admin page UI
        include plugin_dir_path(__FILE__) . 'admin-page.php';
    }

    public function fetch_and_cache_csv() {
        $response = wp_remote_get($this->csv_url);
        if (is_wp_error($response)) return false;

        $csv_content = wp_remote_retrieve_body($response);
        $upload_dir = wp_upload_dir();
        $csv_file_path = $upload_dir['basedir'] . '/incident-data.csv';
        file_put_contents($csv_file_path, $csv_content);

        update_option('incident_chart_csv_path', $csv_file_path);
        update_option('incident_chart_csv_last_updated', current_time('timestamp'));

        return true;
    }

     /** -------------------------
     * SHORTCODE
     * ------------------------- */
 public function render_shortcode() {
    // ✅ Always use cached CSV set from admin-page.php
    $csv_file_path = get_option('incident_chart_csv_path');

    if (!$csv_file_path || !file_exists($csv_file_path)) {
        return '<p>No cached data available. Please refresh from the admin panel.</p>';
    }

    $csv = file_get_contents($csv_file_path);
    $csv_lines = array_map('str_getcsv', explode("\n", trim($csv)));

    if (empty($csv_lines)) {
        return '<p>No data found in cached CSV.</p>';
    }

    // Extract headers
    $headers = array_shift($csv_lines);

    $data = [];
    $tableData = [];

    foreach ($csv_lines as $row) {
        if (count($row) < 3) continue;

        $stat_type = esc_html(trim($row[0]));
        $year      = esc_html(trim($row[1]));
        $figure    = (float) trim($row[2]);

        if (!isset($data[$stat_type])) {
            $data[$stat_type] = [];
        }

        $data[$stat_type][] = [
            'year'   => $year,
            'figure' => $figure
        ];

        // Keep latest year for table
        if (
            !isset($tableData[$stat_type]) ||
            (isset($tableData[$stat_type]['year']) && $year > $tableData[$stat_type]['year'])
        ) {
            $tableData[$stat_type] = [
                'year'   => $year,
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

    private function get_cached_csv() {
        $csv_file_path = get_option('incident_chart_csv_path');
        if ($csv_file_path && file_exists($csv_file_path)) {
            return file_get_contents($csv_file_path);
        }
        return false;
    }
}

// Initialize plugin
new IncidentChartCSV();

