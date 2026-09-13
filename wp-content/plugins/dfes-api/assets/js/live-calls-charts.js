(function () {
  'use strict';

  // ==========================================
  // CONSTANTS & CONFIGURATION
  // ==========================================
  const LIVE_CALLS_API_URL = 'https://dfes.goa.gov.in/disaster-management/live-calls/data';
  const DMRP_CSV_URL = 'https://docs.google.com/spreadsheets/d/1LdJOrKWD9xBi1tMWHNhpi6_7m-c-h7lhsQvII1UoubY/export?format=csv&gid=719938600';

  const COLOR_MAP = {
    'Fire related': 'rgba(255, 0, 0)',
    'Emergency/ Accidents': 'rgb(0, 0, 255)',
    'Meteorological': 'rgb(255, 149, 0)',
    'Biological': 'rgb(0, 255, 238)',
    'Climatological': 'rgb(5, 15, 64)',
    'Hydrological': 'rgb(255, 247, 0)',
    'Geophysical': 'rgb(92, 17, 12)',
    'Others': 'rgb(0, 0, 0)',
  };

  const PREDEFINED_CATEGORIES = [
    'Fire related',
    'Emergency/ Accidents',
    'Meteorological',
    'Biological',
    'Climatological',
    'Hydrological',
    'Geophysical',
    'Others',
  ];

  const CALL_ICON_URLS = {
    'Fire related': '/wp-content/uploads/markers/marker_fire.svg',
    'Emergency/ Accidents': '/wp-content/uploads/markers/marker_emergency.svg',
    'Meteorological': '/wp-content/uploads/markers/marker_meteorological.svg',
    'Biological': '/wp-content/uploads/markers/marker_biological.svg',
    'Climatological': '/wp-content/uploads/markers/marker_climatological.svg',
    'Hydrological': '/wp-content/uploads/markers/marker_hydrological.svg',
    'Geophysical': '/wp-content/uploads/markers/marker_geophysical.svg',
    'Others': '/wp-content/uploads/markers/marker-other.svg',
  };

  const DMRP_ICON_URLS = {
    'Apada Mitra': '/wp-content/uploads/markers/Apada%20Mitra.png',
    'Apada Sakhi': '/wp-content/uploads/markers/Apada%20Sakhi.png',
    'Fire Station': '/wp-content/uploads/markers/Fire%20Station.png',
    'Fire Hydrant': '/wp-content/uploads/markers/Fire%20Hydrant.png',
    'Ground Level Reservoir': '/wp-content/uploads/markers/Ground%20Level%20Reservoir.png',
    'Overhead Reservoir': '/wp-content/uploads/markers/Overhead%20Reservoir.png',
    'Open Water Source': '/wp-content/uploads/markers/Open%20Water%20Source.png',
    'Mutual Aid Agency': '/wp-content/uploads/markers/Mutual%20Aid%20Agency.png',
    'Major Hazardous Unit': '/wp-content/uploads/markers/Major%20Hazardous%20Unit.png',
    'Cyclone Shelter': '/wp-content/uploads/markers/Cyclone%20Shelter.png',
    'Default': 'https://cdn-icons-png.flaticon.com/512/854/854878.png',
  };

  const WATER_RESOURCES = ['Fire Hydrant', 'Ground Level Reservoir', 'Overhead Reservoir', 'Open Water Source'];
  const MONTH_NAMES = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const DMS_REGEX = /(\d+)°\s*(\d+)'?\s*(\d+(?:\.\d+)?)"?\s*([NSEW])/;

  // Shared live calls fetch promise (deduplicates requests across charts, map, and table)
  let liveCallsPromise = null;
  function getLiveCallsData() {
    if (!liveCallsPromise) {
      liveCallsPromise = fetch(LIVE_CALLS_API_URL)
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        });
    }
    return liveCallsPromise;
  }

  // ==========================================
  // 1. CHARTS INITIALIZATION (Station, Taluka, Category)
  // ==========================================
  function initCharts() {
    const stationCanvas = document.getElementById('stationChart');
    const talukaCanvas = document.getElementById('talukaChart');
    const categoryCanvas = document.getElementById('categoryChart');

    if (!stationCanvas && !talukaCanvas && !categoryCanvas) {
      return;
    }

    if (typeof Chart === 'undefined') {
      console.warn('Chart.js library is not loaded.');
      return;
    }

    getLiveCallsData()
      .then(data => {
        if (!Array.isArray(data)) {
          return;
        }

        const stationTypeCounts = {};
        const talukaTypeCounts = {};
        const categoryCounts = {};
        PREDEFINED_CATEGORIES.forEach(type => {
          categoryCounts[type] = 0;
        });

        // Single pass over the data for all three charts
        data.forEach(call => {
          const type = call.type || call.call_type || 'Others';
          const callType = call.call_type || call.type;

          if (stationCanvas) {
            const station = call.station || 'Unknown Station';
            if (!stationTypeCounts[station]) {
              stationTypeCounts[station] = { types: {} };
            }
            stationTypeCounts[station].types[type] = (stationTypeCounts[station].types[type] || 0) + 1;
          }

          if (talukaCanvas) {
            const taluka = call.taluka || 'Unknown Taluka';
            if (!talukaTypeCounts[taluka]) {
              talukaTypeCounts[taluka] = { types: {} };
            }
            talukaTypeCounts[taluka].types[type] = (talukaTypeCounts[taluka].types[type] || 0) + 1;
          }

          if (categoryCanvas) {
            if (categoryCounts.hasOwnProperty(callType)) {
              categoryCounts[callType]++;
            } else {
              categoryCounts['Others']++;
            }
          }
        });

        // Render Station Chart
        if (stationCanvas) {
          const stationLabels = Object.keys(stationTypeCounts);
          const stationDatasets = PREDEFINED_CATEGORIES.map(type => ({
            label: type,
            data: stationLabels.map(station => stationTypeCounts[station].types[type] || 0),
            backgroundColor: COLOR_MAP[type],
            borderColor: COLOR_MAP[type],
            borderWidth: 1,
          }));

          new Chart(stationCanvas.getContext('2d'), {
            type: 'bar',
            data: { labels: stationLabels, datasets: stationDatasets },
            options: {
              responsive: true,
              plugins: { legend: { position: 'top' } },
              scales: { y: { beginAtZero: true } },
            },
          });
        }

        // Render Taluka Chart
        if (talukaCanvas) {
          const talukaLabels = Object.keys(talukaTypeCounts);
          const talukaDatasets = PREDEFINED_CATEGORIES.map(type => ({
            label: type,
            data: talukaLabels.map(taluka => talukaTypeCounts[taluka].types[type] || 0),
            backgroundColor: COLOR_MAP[type],
            borderColor: COLOR_MAP[type],
            borderWidth: 1,
          }));

          new Chart(talukaCanvas.getContext('2d'), {
            type: 'bar',
            data: { labels: talukaLabels, datasets: talukaDatasets },
            options: {
              responsive: true,
              plugins: { legend: { position: 'top' } },
              scales: { y: { beginAtZero: true } },
            },
          });
        }

        // Render Category Chart
        if (categoryCanvas) {
          new Chart(categoryCanvas.getContext('2d'), {
            type: 'bar',
            data: {
              labels: PREDEFINED_CATEGORIES,
              datasets: [
                {
                  label: 'Incident Count',
                  data: PREDEFINED_CATEGORIES.map(type => categoryCounts[type]),
                  backgroundColor: PREDEFINED_CATEGORIES.map(label => COLOR_MAP[label] || 'gray'),
                  borderColor: PREDEFINED_CATEGORIES.map(label => COLOR_MAP[label] || 'gray'),
                  borderWidth: 1,
                },
              ],
            },
            options: {
              responsive: true,
              plugins: { legend: { position: 'top' } },
              scales: {
                y: {
                  beginAtZero: true,
                  title: { display: true, text: 'No. of Incidents' },
                },
              },
            },
          });
        }
      })
      .catch(error => console.error('Error fetching API data for charts:', error));
  }

  // ==========================================
  // 2. DMRP MAP INITIALIZATION
  // ==========================================
  function dmsToDecimal(value) {
    if (!value) return null;

    value = value.trim();
    if (!isNaN(value)) {
      return parseFloat(value);
    }

    value = value.replace(/[\uFFFD˚]/g, '°');
    const match = value.match(DMS_REGEX);

    if (!match) {
      return null;
    }

    const degrees = parseFloat(match[1]);
    const minutes = parseFloat(match[2]);
    const seconds = parseFloat(match[3]);
    const direction = match[4];

    let decimal = degrees + minutes / 60 + seconds / 3600;
    if (direction === 'S' || direction === 'W') {
      decimal *= -1;
    }

    return decimal;
  }

  function openImagePreview(src) {
    let overlay = document.getElementById('imgPreviewOverlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.id = 'imgPreviewOverlay';
      overlay.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.92);display:flex;align-items:center;justify-content:center;z-index:999999;cursor:zoom-out;padding:20px;';
      overlay.onclick = () => overlay.remove();
      document.body.appendChild(overlay);
    }
    overlay.innerHTML = `<img src="${src}" style="max-width:95%;max-height:95%;border-radius:14px;box-shadow:0 20px 50px rgba(0,0,0,0.7);">`;
  }

  function initDmrpMap() {
    const mapContainer = document.getElementById('dmrpmap');
    if (!mapContainer || typeof L === 'undefined') {
      return;
    }

    const map = L.map('dmrpmap').setView([15.4271947, 73.9293256], 10.5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
    }).addTo(map);

    // Cache Leaflet icon instances to prevent duplicate object allocations
    const iconCache = {};
    function getDmrpIcon(categoryKey) {
      const url = DMRP_ICON_URLS[categoryKey] || DMRP_ICON_URLS['Default'];
      if (!iconCache[url]) {
        iconCache[url] = L.icon({
          iconUrl: url,
          iconSize: [30, 30],
          iconAnchor: [15, 30],
          popupAnchor: [0, -30],
        });
      }
      return iconCache[url];
    }

    const markers = [];
    const categoryCounts = {};

    // Delegated event listener for image preview on popup clicks
    mapContainer.addEventListener('click', function (e) {
      const target = e.target;
      if (target && target.classList.contains('id-preview')) {
        const src = target.dataset.src || target.src;
        if (src) {
          openImagePreview(src);
        }
      }
    });

    fetch(DMRP_CSV_URL)
      .then(response => response.text())
      .then(csvText => {
        const rows = csvText.split('\n');
        if (rows.length <= 1) return;

        for (let i = 1; i < rows.length; i++) {
          const row = rows[i];
          if (!row.trim()) continue;

          const cols = row.split(',').map(col => col.trim());
          if (cols.length < 7) continue;

          let category = cols[2];
          if (WATER_RESOURCES.includes(category)) {
            category = `Water Resources > ${category}`;
          }

          categoryCounts[category] = (categoryCounts[category] || 0) + 1;

          const name = ` ${cols[3]}`;
          const latDMS = cols[cols.length - 3].replace(/"/g, '').replace(/[\uFFFD˚]/g, '°');
          const lonDMS = cols[cols.length - 2].replace(/"/g, '').replace(/[\uFFFD˚]/g, '°');

          const latitude = dmsToDecimal(latDMS);
          const longitude = dmsToDecimal(lonDMS);

          if (latitude !== null && longitude !== null && !isNaN(latitude) && !isNaN(longitude)) {
            const markerIcon = getDmrpIcon(cols[2]);
            const marker = L.marker([latitude, longitude], { icon: markerIcon }).addTo(map);

            const idCard = cols[6] || '';
            const phone = cols[1] || '';

            let popupHTML = '';
            if (category === 'Apada Mitra' || category === 'Apada Sakhi') {
              const imgURL = idCard
                ? idCard.replace('github.com', 'raw.githubusercontent.com').replace('/blob/', '/')
                : '';

              popupHTML = `
<div style="width:min(260px,85vw);background:#fff;border-radius:16px;text-align:center;font-family:system-ui, sans-serif;">
    <div style="font-size:14px;font-weight:700;margin-bottom:6px;color:#222;">${category}</div>
    ${imgURL ? `
        <div style="overflow:hidden;border-radius:14px;margin-bottom:10px;">
            <img src="${imgURL}" class="id-preview" data-src="${imgURL}" style="width:100%;max-height:280px;object-fit:cover;display:block;cursor:pointer;transition:transform .25s ease;" onmouseover="this.style.transform='scale(1.06)'" onmouseout="this.style.transform='scale(1)'">
        </div>
    ` : '<div style="color:#999;margin-bottom:8px;">No ID image</div>'}
    ${phone ? `
        <a href="tel:${phone}" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px 14px;border-radius:999px;background:#0d6efd;color:#FFFFFF;text-decoration:none;font-weight:600;font-size:14px;box-shadow:0 4px 10px rgba(0,0,0,0.25);transition:.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.3 1.2.4 2.5.6 3.8.6.6 0 1 .4 1 1V21c0 .6-.4 1-1 1C10.1 22 2 13.9 2 3c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.6.6 3.8.1.4 0 .8-.3 1.1l-2.2 2.2z"/></svg> ${phone}
        </a>
    ` : ''}
</div>`;
            } else {
              popupHTML = `<div style="font-size:10px;">Location: ${name}</div>`;
            }

            marker.bindPopup(popupHTML);
            marker.category = category;
            markers.push(marker);
          }
        }

        createFilterControls(categoryCounts, markers, map);
      })
      .catch(error => console.error('Error loading DMRP map data:', error));
  }

  function createFilterControls(categoryCounts, markers, map) {
    const filterContainer = document.getElementById('filter-controls');
    if (!filterContainer) return;

    let waterResourcesTotal = 0;
    WATER_RESOURCES.forEach(sub => {
      waterResourcesTotal += (categoryCounts[`Water Resources > ${sub}`] || 0);
    });

    let waterResourcesHTML = `
      <details open>
         <summary><strong>Water Resources (${waterResourcesTotal})</strong></summary>
          <ul style="list-style: none; padding-left: 20px;">`;

    let generalCategoriesHTML = '';

    Object.entries(categoryCounts).forEach(([category, count]) => {
      const iconKey = category.split('> ').pop();
      const iconUrl = DMRP_ICON_URLS[iconKey] || DMRP_ICON_URLS['Default'];

      if (category.startsWith('Water Resources >')) {
        waterResourcesHTML += `
              <li>
                  <label style="display: inline-flex; align-items: center; color:#000;">
                      <input type="checkbox" value="${category}" checked style="margin-right: 8px;">
                      <img src="${iconUrl}" alt="${category}" style="width: 20px; height: 20px; margin-right: 8px;">
                      ${category.split('> ')[1]} (${count})
                  </label>
              </li>`;
      } else {
        generalCategoriesHTML += `
              <label style="display: inline-flex; align-items: center; margin-bottom: 8px; color:#000;">
                  <input type="checkbox" value="${category}" checked style="margin-right: 8px;">
                  <img src="${iconUrl}" alt="${category}" style="width: 20px; height: 20px; margin-right: 8px;">
                  ${category} (${count})
              </label><br>`;
      }
    });

    waterResourcesHTML += '</ul></details>';
    // Update DOM in a single operation
    filterContainer.innerHTML = generalCategoriesHTML + waterResourcesHTML;

    const applyBtn = document.getElementById('apply-filter');
    if (applyBtn) {
      applyBtn.onclick = function () {
        const checkedSet = new Set(
          Array.from(filterContainer.querySelectorAll('input:checked')).map(cb => cb.value)
        );

        markers.forEach(marker => {
          if (checkedSet.has(marker.category)) {
            if (!map.hasLayer(marker)) {
              map.addLayer(marker);
            }
          } else {
            if (map.hasLayer(marker)) {
              map.removeLayer(marker);
            }
          }
        });
      };
    }

    const selectNoneBtn = document.getElementById('select-none');
    if (selectNoneBtn) {
      selectNoneBtn.onclick = function () {
        filterContainer.querySelectorAll('input').forEach(cb => {
          cb.checked = false;
        });
      };
    }
  }

  // ==========================================
  // 3. LIVE CALLS MAP INITIALIZATION
  // ==========================================
  function initLiveCallsMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement || typeof L === 'undefined') {
      return;
    }

    const map = L.map('map', {
      center: [15.4909, 73.8278],
      zoom: 11,
      minZoom: 9,
      maxZoom: 13,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: 'Map data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
    }).addTo(map);

    const callIconCache = {};
    function getCallMarkerIcon(callType) {
      const url = CALL_ICON_URLS[callType] || 'https://example.com/default-icon.png';
      if (!callIconCache[url]) {
        callIconCache[url] = L.icon({
          iconUrl: url,
          iconSize: [32, 32],
          iconAnchor: [16, 32],
          popupAnchor: [0, -32],
        });
      }
      return callIconCache[url];
    }

    const markers = (typeof L.markerClusterGroup === 'function')
      ? L.markerClusterGroup({ maxClusterRadius: 50, disableClusteringAtZoom: 11 })
      : L.layerGroup();

    getLiveCallsData()
      .then(callData => {
        if (!Array.isArray(callData)) return;

        const markerList = [];
        callData.forEach(call => {
          const lat = parseFloat(call.lat);
          const lon = parseFloat(call.lon);

          if (isNaN(lat) || isNaN(lon)) {
            return;
          }

          const markerIcon = getCallMarkerIcon(call.call_type);
          const marker = L.marker([lat, lon], { icon: markerIcon })
            .bindPopup(`
              <strong>${call.station || ''}</strong><br>
              Desc: ${call.activity_live || 'Details unavailable'}<br>
              Near: ${call.near || ''}<br>
              Village: ${call.village || ''}<br>
              Time: ${call.outtime || ''}
            `);
          markerList.push(marker);
        });

        if (typeof markers.addLayers === 'function') {
          markers.addLayers(markerList);
        } else {
          markerList.forEach(m => markers.addLayer(m));
        }

        map.addLayer(markers);
      })
      .catch(error => console.error('Error fetching data for live calls map:', error));
  }

  // ==========================================
  // 4. FIRE DATA TABLE INITIALIZATION
  // ==========================================
  function initFireDataTable() {
    const tableBody = document.querySelector('#fire-data-table tbody');
    const timeHeader = document.querySelector('th[data-column="time"]');
    const loadMoreBtn = document.getElementById('load-more-btn');

    if (!tableBody) {
      return;
    }

    let tableData = [];
    let currentPage = 1;
    const rowsPerPage = 10;

    function parseDateTime(item) {
      const dateStr = item.date || '01-01-1970';
      const timeStr = item.outtime || '00:00';
      const parts = dateStr.split('-');
      if (parts.length === 3) {
        const [dd, mm, yyyy] = parts;
        return new Date(`${yyyy}-${mm}-${dd}T${timeStr}:00`).getTime();
      }
      return 0;
    }

    function formatDisplayDate(dateStr) {
      const parts = dateStr.split('-');
      if (parts.length === 3) {
        const [dd, mm] = parts;
        const monthIndex = parseInt(mm, 10) - 1;
        const month = MONTH_NAMES[monthIndex] || '';
        return `${dd} ${month}`;
      }
      return dateStr;
    }

    function renderTable() {
      const endIndex = currentPage * rowsPerPage;
      const dataToShow = tableData.slice(0, endIndex);

      const rowsHtml = dataToShow.map(item => {
        const timeHtml = `<div><span style="display:block; white-space:nowrap;">${item.date ? formatDisplayDate(item.date) : '-'}</span><span style="display:block;">${item.outtime || '-'}</span></div>`;
        const description = `${item.description || '-'} near ${item.near || '-'} at ${item.at || '-'}`;
        return `
          <tr>
            <td>${item.taluka || '-'}</td>
            <td>${item.village || '-'}</td>
            <td>${item.call_type || '-'}</td>
            <td>${item.station || '-'}</td>
            <td>${timeHtml}</td>
            <td style="white-space: normal; word-break: break-word;">${description}</td>
          </tr>`;
      }).join('');

      tableBody.innerHTML = rowsHtml;

      if (loadMoreBtn) {
        loadMoreBtn.style.display = tableData.length > endIndex ? 'inline-block' : 'none';
      }
    }

    function sortTable(column, order) {
      tableData.sort((a, b) => {
        let valA, valB;
        if (column === 'time') {
          valA = a._timestamp || 0;
          valB = b._timestamp || 0;
        } else {
          valA = (a[column] || '').toString().toLowerCase();
          valB = (b[column] || '').toString().toLowerCase();
        }
        if (valA === valB) return 0;
        return order === 'asc' ? (valA > valB ? 1 : -1) : (valA < valB ? 1 : -1);
      });
      renderTable();
    }

    document.querySelectorAll('#fire-data-table thead th').forEach(header => {
      header.addEventListener('click', function () {
        const column = this.getAttribute('data-column');
        const currentOrder = this.getAttribute('data-order');
        const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
        this.setAttribute('data-order', newOrder);
        sortTable(column, newOrder);
      });
    });

    if (loadMoreBtn) {
      loadMoreBtn.addEventListener('click', function () {
        currentPage++;
        renderTable();
      });
    }

    getLiveCallsData()
      .then(data => {
        if (!Array.isArray(data)) return;
        // Pre-parse timestamp on each item for fast O(1) comparison during sorting
        data.forEach(item => {
          item._timestamp = parseDateTime(item);
        });
        tableData = data;
        sortTable('time', 'desc');
        if (timeHeader) {
          timeHeader.setAttribute('data-order', 'desc');
        }
      })
      .catch(err => {
        console.error('Error fetching data for table:', err);
        tableBody.innerHTML = '<tr><td colspan="6">Error loading data</td></tr>';
      });
  }

  // ==========================================
  // MAIN INITIALIZATION ON DOM READY
  // ==========================================
  function init() {
    initCharts();
    initDmrpMap();
    initLiveCallsMap();
    initFireDataTable();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
