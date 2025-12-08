<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'WebGIS Fullscreen' ?></title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #fullscreenMap {
            width: 100%;
            height: 100vh;
        }
        .leaflet-popup-content {
            min-width: 250px;
        }
        .popup-img {
            width: 100%;
            max-height: 150px;
            object-fit: cover;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }
        .map-controls {
            position: absolute;
            top: 10px;
            left: 60px;
            z-index: 1000;
            background: white;
            padding: 8px 12px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .layer-btn {
            cursor: pointer;
            padding: 5px 12px;
            border-radius: 5px;
            margin-right: 5px;
            transition: all 0.2s;
            display: inline-block;
        }
        .layer-btn.active {
            background: #0d6efd;
            color: white;
        }
        .layer-btn:hover:not(.active) {
            background: #e9ecef;
        }
        .exit-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        .legend {
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .stats-box {
            position: absolute;
            bottom: 30px;
            left: 10px;
            z-index: 1000;
            background: white;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            max-width: 300px;
        }
    </style>
</head>
<body>

<div id="fullscreenMap"></div>

<!-- Controls -->
<div class="map-controls">
    <span class="layer-btn active" id="layerAset" onclick="toggleLayer('aset')">
        <i class="fas fa-warehouse me-1"></i>Aset
    </span>
    <span class="layer-btn" id="layerPenduduk" onclick="toggleLayer('penduduk')">
        <i class="fas fa-users me-1"></i>Penduduk
    </span>
</div>

<!-- Exit Button -->
<a href="<?= base_url('/gis') ?>" class="btn btn-dark exit-btn">
    <i class="fas fa-compress me-2"></i>Keluar Fullscreen
</a>

<!-- Stats Box -->
<div class="stats-box" id="statsBox">
    <div id="asetStatsContent">
        <h6 class="mb-2"><i class="fas fa-warehouse text-success me-2"></i>Aset Desa</h6>
        <div class="d-flex gap-3 small">
            <span><strong id="totalMarkers">0</strong> terpetakan</span>
        </div>
    </div>
    <div id="pendudukStatsContent" style="display: none;">
        <h6 class="mb-2"><i class="fas fa-users text-primary me-2"></i>Penduduk</h6>
        <div class="small" id="populationStats">Memuat...</div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
// Initialize fullscreen map
const map = L.map('fullscreenMap').setView([<?= $centerLat ?>, <?= $centerLng ?>], 13);

// OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

// =============================================
// LAYER 1: ASET
// =============================================
const markers = L.markerClusterGroup();

const categoryColors = {
    'Tanah': '#0d6efd',
    'Peralatan dan Mesin': '#198754',
    'Gedung dan Bangunan': '#ffc107',
    'Jalan, Irigasi, dan Jaringan': '#0dcaf0',
    'Aset Tetap Lainnya': '#6c757d',
    'Konstruksi Dalam Pengerjaan': '#dc3545'
};

function getIcon(kategori) {
    const color = categoryColors[kategori] || '#6c757d';
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
        iconSize: [24, 24],
        iconAnchor: [12, 12],
        popupAnchor: [0, -12]
    });
}

function createPopup(props) {
    let content = '<div class="popup-content">';
    if (props.foto) {
        content += `<img src="${props.foto}" class="popup-img" alt="Foto">`;
    }
    content += `
        <h6 class="mb-1">${props.nama}</h6>
        <p class="text-muted small mb-2">${props.kode_register}</p>
        <table class="table table-sm mb-0">
            <tr><td>Kategori</td><td><strong>${props.kategori}</strong></td></tr>
            <tr><td>Nilai</td><td>Rp ${props.nilai.toLocaleString('id-ID')}</td></tr>
            <tr><td>Kondisi</td><td><span class="badge ${props.kondisi === 'Baik' ? 'bg-success' : 'bg-warning'}">${props.kondisi}</span></td></tr>
        </table>
    `;
    content += '</div>';
    return content;
}

// Load asset data
let totalAset = 0;
fetch('<?= base_url('/gis/json') ?>')
    .then(response => response.json())
    .then(data => {
        if (data.features && data.features.length > 0) {
            const bounds = [];
            totalAset = data.features.length;
            
            data.features.forEach(feature => {
                const coords = feature.geometry.coordinates;
                const props = feature.properties;
                
                const marker = L.marker([coords[1], coords[0]], {
                    icon: getIcon(props.kategori)
                });
                marker.bindPopup(createPopup(props), { maxWidth: 300 });
                markers.addLayer(marker);
                bounds.push([coords[1], coords[0]]);
            });
            
            map.addLayer(markers);
            document.getElementById('totalMarkers').textContent = totalAset;
            
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }
    })
    .catch(error => console.error('Error loading assets:', error));

// Asset Legend
const assetLegend = L.control({ position: 'bottomright' });
assetLegend.onAdd = function(map) {
    const div = L.DomUtil.create('div', 'legend');
    div.innerHTML = '<strong class="mb-2 d-block">Kategori Aset</strong>';
    for (const [kategori, color] of Object.entries(categoryColors)) {
        const shortName = kategori.split(' ')[0];
        div.innerHTML += `<div class="legend-item"><div class="legend-color" style="background: ${color}"></div><span class="small">${shortName}</span></div>`;
    }
    return div;
};
assetLegend.addTo(map);

// =============================================
// LAYER 2: PENDUDUK
// =============================================
let populationCircles = L.layerGroup();
let populationData = null;

function loadPopulationData() {
    fetch('<?= base_url('/gis/population') ?>')
        .then(response => response.json())
        .then(data => {
            populationData = data;
            
            // Update stats
            let statsHtml = `<strong>${data.total}</strong> jiwa<br>`;
            if (data.by_dusun && data.by_dusun.length > 0) {
                statsHtml += `<span class="text-muted">${data.by_dusun.length} dusun</span>`;
            }
            document.getElementById('populationStats').innerHTML = statsHtml;
            
            // Add circles
            const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316', '#eab308'];
            data.by_dusun.forEach((dusun, index) => {
                if (dusun.coordinates && dusun.coordinates.lat && dusun.coordinates.lng) {
                    const color = colors[index % colors.length];
                    const ratio = data.max > 0 ? dusun.jumlah_penduduk / data.max : 0;
                    const radius = 30 + (ratio * 50);
                    
                    const circle = L.circleMarker([dusun.coordinates.lat, dusun.coordinates.lng], {
                        radius: radius,
                        fillColor: color,
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.7
                    });
                    
                    circle.bindPopup(`
                        <div style="min-width: 180px;">
                            <h6 style="color: ${color}">${dusun.wilayah}</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td>Penduduk</td><td class="fw-bold">${dusun.jumlah_penduduk}</td></tr>
                                <tr><td>KK</td><td>${dusun.jumlah_kk}</td></tr>
                                <tr><td>L / P</td><td>${dusun.laki_laki} / ${dusun.perempuan}</td></tr>
                            </table>
                        </div>
                    `);
                    
                    populationCircles.addLayer(circle);
                }
            });
        })
        .catch(error => console.error('Error loading population:', error));
}

// Population Legend
const popLegend = L.control({ position: 'bottomright' });
popLegend.onAdd = function(map) {
    const div = L.DomUtil.create('div', 'legend');
    div.innerHTML = `
        <strong class="d-block mb-2">Kepadatan</strong>
        <div style="background: linear-gradient(to right, #fee5d9, #fb6a4a, #a50f15); height: 12px; border-radius: 3px; margin-bottom: 5px;"></div>
        <div class="d-flex justify-content-between small text-muted"><span>Jarang</span><span>Padat</span></div>
    `;
    return div;
};

// =============================================
// LAYER SWITCHING
// =============================================
let currentLayer = 'aset';

function toggleLayer(layer) {
    currentLayer = layer;
    
    document.getElementById('layerAset').classList.toggle('active', layer === 'aset');
    document.getElementById('layerPenduduk').classList.toggle('active', layer === 'penduduk');
    document.getElementById('asetStatsContent').style.display = layer === 'aset' ? 'block' : 'none';
    document.getElementById('pendudukStatsContent').style.display = layer === 'penduduk' ? 'block' : 'none';
    
    if (layer === 'aset') {
        map.addLayer(markers);
        map.removeLayer(populationCircles);
        if (map.hasLayer(popLegend)) map.removeControl(popLegend);
        assetLegend.addTo(map);
    } else {
        map.removeLayer(markers);
        map.addLayer(populationCircles);
        if (map.hasLayer(assetLegend)) map.removeControl(assetLegend);
        popLegend.addTo(map);
        
        if (!populationData) {
            loadPopulationData();
        }
    }
}

// Preload population data
loadPopulationData();
</script>

</body>
</html>
