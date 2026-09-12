document.addEventListener("DOMContentLoaded", function () {
    fetch('https://dfes.goa.gov.in/disaster-management/live-calls/data')
        .then(response => response.json())
        .then(data => {
            const colorMap = {
                "Fire related": "rgba(255, 0, 0)",
                "Emergency/ Accidents": "rgb(0, 0, 255)",
                "Meteorological": "rgb(255, 149, 0)",
                "Biological": "rgb(0, 255, 238)",
                "Climatological": "rgb(5, 15, 64)",
                "Hydrological": "rgb(255, 247, 0)",
                "Geophysical": "rgb(92, 17, 12)",
                "Others": "rgb(0, 0, 0)"
            };

            // === Station Chart ===
            if (document.getElementById('stationChart')) {
                const stationTypeCounts = {};
                data.forEach(call => {
                    const station = call.station || 'Unknown Station';
                    const type = call.type || 'Others';
                    stationTypeCounts[station] = stationTypeCounts[station] || { types: {} };
                    stationTypeCounts[station].types[type] = (stationTypeCounts[station].types[type] || 0) + 1;
                });
                const stationLabels = Object.keys(stationTypeCounts);
                const stationDatasets = Object.keys(colorMap).map(type => ({
                    label: type,
                    data: stationLabels.map(station => stationTypeCounts[station].types[type] || 0),
                    backgroundColor: colorMap[type],
                    borderColor: colorMap[type],
                    borderWidth: 1
                }));
                new Chart(document.getElementById('stationChart').getContext('2d'), {
                    type: 'bar',
                    data: { labels: stationLabels, datasets: stationDatasets },
                    options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
                });
            }

            // === Taluka Chart ===
            if (document.getElementById('talukaChart')) {
                const talukaTypeCounts = {};
                data.forEach(call => {
                    const taluka = call.taluka || 'Unknown Taluka';
                    const type = call.type || 'Others';
                    talukaTypeCounts[taluka] = talukaTypeCounts[taluka] || { types: {} };
                    talukaTypeCounts[taluka].types[type] = (talukaTypeCounts[taluka].types[type] || 0) + 1;
                });
                const talukaLabels = Object.keys(talukaTypeCounts);
                const talukaDatasets = Object.keys(colorMap).map(type => ({
                    label: type,
                    data: talukaLabels.map(taluka => talukaTypeCounts[taluka].types[type] || 0),
                    backgroundColor: colorMap[type],
                    borderColor: colorMap[type],
                    borderWidth: 1
                }));
                new Chart(document.getElementById('talukaChart').getContext('2d'), {
                    type: 'bar',
                    data: { labels: talukaLabels, datasets: talukaDatasets },
                    options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
                });
            }

            // === Category Chart ===
            if (document.getElementById('categoryChart')) {
                // Define fixed categories to always show
                const predefinedTypes = [
                    "Fire related",
                    "Emergency/ Accidents",
                    "Meteorological",
                    "Biological",
                    "Climatological",
                    "Hydrological",
                    "Geophysical",
                    "Others"
                ];

                // Initialize all counts to 0
                const categoryCounts = {};
                predefinedTypes.forEach(type => categoryCounts[type] = 0);

                // Only increment counts if type matches one of the predefined
                data.forEach(call => {
                    const type = call.call_type;
                    if (predefinedTypes.includes(type)) {
                        categoryCounts[type]++;
                    } else {
                        categoryCounts["Others"]++; // fallback if type doesn't match
                    }
                });

                // Use the predefined order and colors
                new Chart(document.getElementById('categoryChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: predefinedTypes,
                        datasets: [{
                            label: 'Incident Count',
                            data: predefinedTypes.map(type => categoryCounts[type]),
                            backgroundColor: predefinedTypes.map(label => colorMap[label] || 'gray'),
                            borderColor: predefinedTypes.map(label => colorMap[label] || 'gray'),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'top' } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'No. of Incidents' }
                            }
                        }
                    }
                });
            }

        })
        .catch(error => console.error('Error fetching API data:', error));
});
