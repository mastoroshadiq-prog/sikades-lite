<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<style>
    #map {
        height: 600px;
        border-radius: 0.5rem;
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
    .layer-control {
        background: white;
        padding: 10px 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }
    .layer-btn {
        cursor: pointer;
        padding: 5px 12px;
        border-radius: 5px;
        margin-right: 5px;
        transition: all 0.2s;
    }
    .layer-btn.active {
        background: #0d6efd;
        color: white;
    }
    .layer-btn:hover:not(.active) {
        background: #e9ecef;
    }
    .population-card {
        border-radius: 10px;
        transition: all 0.3s;
        cursor: pointer;
    }
    .population-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    .density-legend {
        background: white;
        padding: 12px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }
    .density-bar {
        height: 15px;
        border-radius: 3px;
        background: linear-gradient(to right, #fee5d9, #fcae91, #fb6a4a, #de2d26, #a50f15);
    }
    .info-box {
        background: white;
        padding: 10px 14px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        max-width: 250px;
    }
</style>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-map-marked-alt me-2 text-success"></i>WebGIS - Peta Desa Interaktif
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">WebGIS</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('/gis/wilayah') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-cog me-2"></i>Pengaturan Wilayah
            </a>
            <a href="<?= base_url('/gis/fullscreen') ?>" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-expand me-2"></i>Fullscreen
            </a>
            <a href="<?= base_url('/aset') ?>" class="btn btn-success">
                <i class="fas fa-boxes me-2"></i>Kelola Aset
            </a>
        </div>
    </div>

    <!-- Layer Toggle -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <span class="fw-bold me-3"><i class="fas fa-layer-group me-2"></i>Layer Peta:</span>
                    <div class="layer-control d-inline-flex">
                        <span class="layer-btn active" id="layerAset" onclick="toggleLayer('aset')">
                            <i class="fas fa-warehouse me-1"></i>Aset Desa
                        </span>
                        <span class="layer-btn" id="layerPenduduk" onclick="toggleLayer('penduduk')">
                            <i class="fas fa-users me-1"></i>Kepadatan Penduduk
                        </span>
                        <span class="layer-btn" id="layerProyek" onclick="toggleLayer('proyek')">
                            <i class="fas fa-hard-hat me-1"></i>Proyek Pembangunan
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="text-center">
                        <span class="badge bg-success fs-6"><?= $totalAset ?></span>
                        <small class="text-muted ms-1">Aset Terpetakan</small>
                    </div>
                    <div class="text-center">
                        <span class="badge bg-primary fs-6"><?= $totalPenduduk ?></span>
                        <small class="text-muted ms-1">Total Penduduk</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row - Aset Layer -->
    <div class="row g-3 mb-4" id="asetStats">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col">
                            <span class="badge bg-primary"><i class="fas fa-mountain me-1"></i>Tanah</span>
                            <span id="countTanah" class="ms-1 fw-bold">0</span>
                        </div>
                        <div class="col">
                            <span class="badge bg-success"><i class="fas fa-tools me-1"></i>Peralatan</span>
                            <span id="countPeralatan" class="ms-1 fw-bold">0</span>
                        </div>
                        <div class="col">
                            <span class="badge bg-warning text-dark"><i class="fas fa-building me-1"></i>Gedung</span>
                            <span id="countGedung" class="ms-1 fw-bold">0</span>
                        </div>
                        <div class="col">
                            <span class="badge bg-info text-dark"><i class="fas fa-road me-1"></i>Jalan</span>
                            <span id="countJalan" class="ms-1 fw-bold">0</span>
                        </div>
                        <div class="col">
                            <span class="badge bg-secondary"><i class="fas fa-box me-1"></i>Lainnya</span>
                            <span id="countLainnya" class="ms-1 fw-bold">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row - Penduduk Layer -->
    <div class="row g-3 mb-4" id="pendudukStats" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Distribusi Penduduk per Wilayah</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="dusunCards">
                        <!-- Akan diisi dengan JavaScript -->
                        <div class="col-12 text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Memuat data penduduk...</p>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <!-- Stats Row - Proyek Layer -->
    <div class="row g-3 mb-4" id="proyekStats" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col">
                            <span class="badge bg-secondary"><i class="fas fa-clipboard-list me-1"></i>Rencana</span>
                            <span id="countRencana" class="ms-1 fw-bold">0</span>
                        </div>
                        <div class="col">
                            <span class="badge bg-warning text-dark"><i class="fas fa-spinner me-1"></i>Proses</span>
                            <span id="countProses" class="ms-1 fw-bold">0</span>
                        </div>
                        <div class="col">
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Selesai</span>
                            <span id="countSelesai" class="ms-1 fw-bold">0</span>
                        </div>
                        <div class="col">
                            <span class="badge bg-danger"><i class="fas fa-pause-circle me-1"></i>Mangkrak</span>
                            <span id="countMangkrak" class="ms-1 fw-bold">0</span>
                        </div>
                        <div class="col">
                            <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Alert Deviasi</span>
                            <span id="countAlert" class="ms-1 fw-bold">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="map"></div>
        </div>
    </div>

    <!-- Info -->
    <div class="alert alert-info mt-4" id="asetTip">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Tip:</strong> Klik marker untuk melihat detail aset. Gunakan scroll untuk zoom in/out.
        Aset yang belum memiliki koordinat GPS tidak ditampilkan di peta.
    </div>
    <div class="alert alert-primary mt-4" id="pendudukTip" style="display: none;">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Tip:</strong> Klik pada kartu wilayah untuk melihat detail demografi. 
        Warna menunjukkan kepadatan penduduk (merah = padat, biru = jarang).
    </div>
    <div class="alert alert-warning mt-4" id="proyekTip" style="display: none;">
        <i class="fas fa-hard-hat me-2"></i>
        <strong>Tip:</strong> Klik marker untuk melihat detail proyek dengan foto dokumentasi. 
        Warna marker: 🟢 Selesai, 🟡 Proses, ⚪ Rencana, 🔴 Alert/Mangkrak.
    </div>
</div>

<!-- Modal untuk Detail Dusun -->
<div class="modal fade" id="dusunDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-map-marker-alt me-2"></i><span id="modalDusunName">Dusun</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x"></i>
                    </div>
                </div>
                <h5 class="mb-2">Koordinat Belum Diatur</h5>
                <p class="text-muted mb-3">
                    Wilayah <strong id="modalDusunNameText"></strong> belum memiliki koordinat lokasi di peta.
                </p>
                <div class="alert alert-info text-start mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Anda dapat mengatur koordinat di halaman <strong>Pengaturan Wilayah</strong>, atau langsung melihat data penduduk untuk wilayah ini.
                </div>
            </div>
            <div class="modal-footer justify-content-center gap-2">
                <a href="" id="btnViewDemografi" class="btn btn-primary">
                    <i class="fas fa-users me-2"></i>Lihat Data Penduduk
                </a>
                <a href="<?= base_url('/gis/wilayah') ?>" class="btn btn-outline-success">
                    <i class="fas fa-cog me-2"></i>Atur Koordinat
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
// Initialize map
const map = L.map('map').setView([<?= $centerLat ?>, <?= $centerLng ?>], 13);

// OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// =============================================
// LAYER 1: ASET DESA
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
        content += `<img src="${props.foto}" class="popup-img" alt="Foto Aset">`;
    }
    
    content += `
        <h6 class="mb-1">${props.nama}</h6>
        <p class="text-muted small mb-2">${props.kode_register}</p>
        <table class="table table-sm mb-2">
            <tr><td>Kategori</td><td><strong>${props.kategori}</strong></td></tr>
            <tr><td>Tahun</td><td>${props.tahun || '-'}</td></tr>
            <tr><td>Nilai</td><td>Rp ${props.nilai.toLocaleString('id-ID')}</td></tr>
            <tr><td>Kondisi</td><td><span class="badge ${props.kondisi === 'Baik' ? 'bg-success' : 'bg-warning'}">${props.kondisi}</span></td></tr>
        </table>
        <a href="<?= base_url('/aset/detail/') ?>${props.id}" class="btn btn-sm btn-primary w-100">
            <i class="fas fa-eye me-1"></i>Lihat Detail
        </a>
    `;
    
    content += '</div>';
    return content;
}

let counts = { Tanah: 0, Peralatan: 0, Gedung: 0, Jalan: 0, Lainnya: 0 };

// Load asset data
fetch('<?= base_url('/gis/json') ?>')
    .then(response => response.json())
    .then(data => {
        if (data.features && data.features.length > 0) {
            const bounds = [];
            
            data.features.forEach(feature => {
                const coords = feature.geometry.coordinates;
                const props = feature.properties;
                
                const marker = L.marker([coords[1], coords[0]], {
                    icon: getIcon(props.kategori)
                });
                
                marker.bindPopup(createPopup(props), { maxWidth: 300 });
                markers.addLayer(marker);
                bounds.push([coords[1], coords[0]]);
                
                // Count by category
                if (props.kategori.includes('Tanah')) counts.Tanah++;
                else if (props.kategori.includes('Peralatan')) counts.Peralatan++;
                else if (props.kategori.includes('Gedung')) counts.Gedung++;
                else if (props.kategori.includes('Jalan')) counts.Jalan++;
                else counts.Lainnya++;
            });
            
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
            
            document.getElementById('countTanah').textContent = counts.Tanah;
            document.getElementById('countPeralatan').textContent = counts.Peralatan;
            document.getElementById('countGedung').textContent = counts.Gedung;
            document.getElementById('countJalan').textContent = counts.Jalan;
            document.getElementById('countLainnya').textContent = counts.Lainnya;
        }
        
        // Always add markers layer to map (even if empty)
        map.addLayer(markers);
    })
    .catch(error => {
        console.error('Error loading asset GeoJSON:', error);
        // Still add empty markers layer so switching works
        map.addLayer(markers);
    });

// Asset Legend
const assetLegend = L.control({ position: 'bottomright' });
assetLegend.onAdd = function(map) {
    const div = L.DomUtil.create('div', 'legend');
    div.innerHTML = '<strong class="mb-2 d-block">Kategori Aset</strong>';
    
    for (const [kategori, color] of Object.entries(categoryColors)) {
        const shortName = kategori.split(' ')[0];
        div.innerHTML += `
            <div class="legend-item">
                <div class="legend-color" style="background: ${color}"></div>
                <span class="small">${shortName}</span>
            </div>
        `;
    }
    return div;
};
assetLegend.addTo(map);

// =============================================
// LAYER 2: KEPADATAN PENDUDUK
// =============================================
let populationData = null;
let populationInfoBox = null;

// Population info control
const popInfo = L.control({ position: 'topright' });
popInfo.onAdd = function(map) {
    const div = L.DomUtil.create('div', 'info-box');
    div.innerHTML = `
        <h6><i class="fas fa-users me-2"></i>Kepadatan Penduduk</h6>
        <p class="small text-muted mb-0">Hover pada wilayah untuk detail</p>
    `;
    return div;
};

// Population density legend
const densityLegend = L.control({ position: 'bottomright' });
densityLegend.onAdd = function(map) {
    const div = L.DomUtil.create('div', 'density-legend');
    div.innerHTML = `
        <strong class="d-block mb-2">Kepadatan Penduduk</strong>
        <div class="density-bar mb-1"></div>
        <div class="d-flex justify-content-between small text-muted">
            <span>Jarang</span>
            <span>Padat</span>
        </div>
    `;
    return div;
};

// Get density color (red gradient)
function getDensityColor(value, max) {
    if (max === 0) return '#fee5d9';
    const ratio = value / max;
    
    if (ratio > 0.8) return '#a50f15';
    if (ratio > 0.6) return '#de2d26';
    if (ratio > 0.4) return '#fb6a4a';
    if (ratio > 0.2) return '#fcae91';
    return '#fee5d9';
}

// Load population data
function loadPopulationData() {
    fetch('<?= base_url('/gis/population') ?>')
        .then(response => response.json())
        .then(data => {
            populationData = data;
            renderDusunCards(data);
        })
        .catch(error => console.error('Error loading population data:', error));
}

// Render dusun cards
function renderDusunCards(data) {
    const container = document.getElementById('dusunCards');
    
    if (!data.by_dusun || data.by_dusun.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center text-muted py-4">
                <i class="fas fa-users-slash fa-3x mb-3"></i>
                <p>Belum ada data penduduk terpetakan</p>
                <a href="<?= base_url('/demografi') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Kelola Data Demografi
                </a>
            </div>
        `;
        return;
    }
    
    let html = '';
    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#22c55e', '#06b6d4'];
    let validDusunCount = 0;
    
    data.by_dusun.forEach((dusun, index) => {
        // Skip entries with empty or invalid wilayah
        if (!dusun.wilayah || dusun.wilayah.trim() === '' || dusun.wilayah === 'Tidak Diketahui') {
            return;
        }
        
        const color = colors[validDusunCount % colors.length];
        const percentage = data.total > 0 ? ((dusun.jumlah_penduduk / data.total) * 100).toFixed(1) : 0;
        const encodedDusun = encodeURIComponent(dusun.wilayah);
        const hasCoords = dusun.coordinates && dusun.coordinates.lat && dusun.coordinates.lng;
        validDusunCount++;
        
        html += `
            <div class="col-md-3 col-sm-6">
                <div class="population-card card border-0 h-100 shadow-sm" 
                     style="border-left: 4px solid ${color} !important;"
                     onclick="handleDusunClick('${encodedDusun}', ${hasCoords ? dusun.coordinates.lat : 'null'}, ${hasCoords ? dusun.coordinates.lng : 'null'})"
                     data-dusun="${dusun.wilayah}"
                     data-color="${color}"
                     data-population="${dusun.jumlah_penduduk}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1" style="color: ${color}">${dusun.wilayah}</h5>
                                <p class="text-muted small mb-0">${dusun.jumlah_kk} KK</p>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0">${dusun.jumlah_penduduk}</h4>
                                <span class="badge" style="background: ${color}">${percentage}%</span>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row text-center small">
                            <div class="col-6">
                                <i class="fas fa-male text-primary"></i> ${dusun.laki_laki}
                            </div>
                            <div class="col-6">
                                <i class="fas fa-female text-danger"></i> ${dusun.perempuan}
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: ${(dusun.laki_laki / dusun.jumlah_penduduk * 100)}%"></div>
                            <div class="progress-bar bg-danger" style="width: ${(dusun.perempuan / dusun.jumlah_penduduk * 100)}%"></div>
                        </div>
                        <div class="mt-2 text-center">
                            <small class="text-muted">
                                ${hasCoords ? '<i class="fas fa-map-marker-alt text-success"></i> Klik untuk lihat di peta' : '<i class="fas fa-external-link-alt"></i> Klik untuk detail'}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add circle marker to map if has coordinates
        if (hasCoords) {
            addPopulationCircle(dusun.wilayah, dusun.coordinates.lat, dusun.coordinates.lng, dusun.jumlah_penduduk, data.max, color, dusun);
        }
    });
    
    // Add summary card only if we have valid dusun data
    if (validDusunCount > 0) {
        html += `
            <div class="col-md-3 col-sm-6">
                <div class="population-card card border-0 h-100 shadow" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
                    <div class="card-body text-white">
                        <div class="text-center">
                            <i class="fas fa-globe fa-3x mb-2 opacity-75"></i>
                            <h3 class="mb-0">${data.total}</h3>
                            <p class="mb-0">Total Penduduk</p>
                        </div>
                        <hr class="border-white opacity-25 my-2">
                        <div class="row text-center small">
                            <div class="col-6">
                                <strong>${validDusunCount}</strong><br>Dusun
                            </div>
                            <div class="col-6">
                                <strong>${data.by_rt ? data.by_rt.length : 0}</strong><br>RT
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    container.innerHTML = html;
}

// =============================================
// POPULATION CIRCLE MARKERS
// =============================================
let populationCircles = L.layerGroup();

function addPopulationCircle(name, lat, lng, population, maxPopulation, color, dusunData) {
    // Calculate radius based on population (min 30, max 80)
    const minRadius = 30;
    const maxRadius = 80;
    const ratio = maxPopulation > 0 ? population / maxPopulation : 0;
    const radius = minRadius + (ratio * (maxRadius - minRadius));
    
    const circle = L.circleMarker([lat, lng], {
        radius: radius,
        fillColor: color,
        color: '#fff',
        weight: 2,
        opacity: 1,
        fillOpacity: 0.7
    });
    
    // Popup content
    const popupContent = `
        <div style="min-width: 200px;">
            <h6 class="mb-2" style="color: ${color}"><i class="fas fa-map-marker-alt me-2"></i>${name}</h6>
            <table class="table table-sm table-borderless mb-2">
                <tr><td>Jumlah Penduduk</td><td class="fw-bold">${population}</td></tr>
                <tr><td>Jumlah KK</td><td>${dusunData.jumlah_kk}</td></tr>
                <tr><td>Laki-laki</td><td><i class="fas fa-male text-primary"></i> ${dusunData.laki_laki}</td></tr>
                <tr><td>Perempuan</td><td><i class="fas fa-female text-danger"></i> ${dusunData.perempuan}</td></tr>
            </table>
            <a href="<?= base_url('/demografi/penduduk') ?>?dusun=${encodeURIComponent(name)}" class="btn btn-sm btn-primary w-100">
                <i class="fas fa-users me-1"></i>Lihat Detail Penduduk
            </a>
        </div>
    `;
    
    circle.bindPopup(popupContent, { maxWidth: 300 });
    
    // Hover effect
    circle.on('mouseover', function() {
        this.setStyle({ fillOpacity: 0.9, weight: 3 });
    });
    circle.on('mouseout', function() {
        this.setStyle({ fillOpacity: 0.7, weight: 2 });
    });
    
    populationCircles.addLayer(circle);
}

// Handle dusun card click
function handleDusunClick(encodedDusun, lat, lng) {
    const dusun = decodeURIComponent(encodedDusun);
    
    if (lat && lng) {
        // Pan to location and open popup
        map.setView([lat, lng], 15);
        
        // Find and open the circle's popup
        populationCircles.eachLayer(function(layer) {
            const popup = layer.getPopup();
            if (popup && popup.getContent().includes(dusun)) {
                layer.openPopup();
            }
        });
    } else {
        // No coordinates - show modal with options
        document.getElementById('modalDusunName').textContent = dusun;
        document.getElementById('modalDusunNameText').textContent = dusun;
        document.getElementById('btnViewDemografi').href = `<?= base_url('/demografi/penduduk') ?>?dusun=${encodedDusun}`;
        
        const modal = new bootstrap.Modal(document.getElementById('dusunDetailModal'));
        modal.show();
    }
}

// =============================================
// LAYER 3: PROYEK PEMBANGUNAN
// =============================================
let proyekMarkers = L.markerClusterGroup();
let proyekData = null;

// Proyek status colors
const proyekColors = {
    'RENCANA': '#6c757d',   // Gray
    'PROSES': '#ffc107',    // Yellow
    'SELESAI': '#28a745',   // Green
    'MANGKRAK': '#dc3545',  // Red
    'ALERT': '#dc3545'      // Red for deviation alert
};

function getProyekIcon(status, isAlert) {
    const color = isAlert ? proyekColors.ALERT : (proyekColors[status] || '#6c757d');
    return L.divIcon({
        className: 'proyek-marker',
        html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-hard-hat" style="color: white; font-size: 12px;"></i>
               </div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 15],
        popupAnchor: [0, -15]
    });
}

function createProyekPopup(props) {
    const statusBadge = {
        'RENCANA': 'bg-secondary',
        'PROSES': 'bg-warning text-dark',
        'SELESAI': 'bg-success',
        'MANGKRAK': 'bg-danger'
    };
    
    let photoHtml = '';
    if (props.foto_terbaru || props.foto_0 || props.foto_50 || props.foto_100) {
        photoHtml = `<div class="mb-2">`;
        if (props.foto_terbaru) {
            photoHtml += `<img src="${props.foto_terbaru}" class="popup-img" style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 5px;">`;
        } else if (props.foto_0) {
            photoHtml += `<img src="${props.foto_0}" class="popup-img" style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 5px;">`;
        }
        photoHtml += `</div>`;
    }
    
    let alertHtml = '';
    if (props.is_alert) {
        alertHtml = `<div class="alert alert-danger py-1 px-2 small mb-2">
            <i class="fas fa-exclamation-triangle me-1"></i>Deviasi ${props.deviation}%
        </div>`;
    }
    
    return `
        <div class="popup-content" style="min-width: 250px;">
            ${photoHtml}
            <h6 class="mb-1">${props.nama}</h6>
            <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt me-1"></i>${props.lokasi || 'Lokasi tidak ditentukan'}</p>
            ${alertHtml}
            <div class="row mb-2 small">
                <div class="col-6">
                    <div class="d-flex align-items-center">
                        <span class="me-1">Fisik</span>
                        <div class="progress flex-grow-1" style="height: 10px;">
                            <div class="progress-bar bg-info" style="width: ${props.persentase_fisik}%">${props.persentase_fisik}%</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center">
                        <span class="me-1">Uang</span>
                        <div class="progress flex-grow-1" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: ${props.persentase_keuangan}%">${Math.round(props.persentase_keuangan)}%</div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-sm mb-2">
                <tr><td>Status</td><td><span class="badge ${statusBadge[props.status]}">${props.status}</span></td></tr>
                <tr><td>Anggaran</td><td>Rp ${props.anggaran.toLocaleString('id-ID')}</td></tr>
                <tr><td>Pelaksana</td><td>${props.pelaksana || '-'}</td></tr>
            </table>
            <a href="<?= base_url('/pembangunan/proyek/detail/') ?>${props.id}" class="btn btn-sm btn-warning w-100">
                <i class="fas fa-eye me-1"></i>Lihat Detail Proyek
            </a>
        </div>
    `;
}

// Load proyek data
function loadProyekData() {
    fetch('<?= base_url('/gis/proyek') ?>')
        .then(response => response.json())
        .then(data => {
            proyekData = data;
            proyekMarkers.clearLayers();
            
            if (data.features && data.features.length > 0) {
                const bounds = [];
                
                data.features.forEach(feature => {
                    const coords = feature.geometry.coordinates;
                    const props = feature.properties;
                    
                    const marker = L.marker([coords[1], coords[0]], {
                        icon: getProyekIcon(props.status, props.is_alert)
                    });
                    
                    marker.bindPopup(createProyekPopup(props), { maxWidth: 350 });
                    proyekMarkers.addLayer(marker);
                    bounds.push([coords[1], coords[0]]);
                });
                
                // Update stats
                if (data.stats) {
                    document.getElementById('countRencana').textContent = data.stats.rencana || 0;
                    document.getElementById('countProses').textContent = data.stats.proses || 0;
                    document.getElementById('countSelesai').textContent = data.stats.selesai || 0;
                    document.getElementById('countMangkrak').textContent = data.stats.mangkrak || 0;
                    document.getElementById('countAlert').textContent = data.stats.alert || 0;
                }
                
                if (currentLayer === 'proyek' && bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            }
        })
        .catch(error => console.error('Error loading proyek data:', error));
}

// Proyek Legend
const proyekLegend = L.control({ position: 'bottomright' });
proyekLegend.onAdd = function(map) {
    const div = L.DomUtil.create('div', 'legend');
    div.innerHTML = '<strong class="mb-2 d-block"><i class="fas fa-hard-hat me-1"></i>Status Proyek</strong>';
    
    const items = [
        { label: 'Rencana', color: '#6c757d' },
        { label: 'Proses', color: '#ffc107' },
        { label: 'Selesai', color: '#28a745' },
        { label: 'Alert/Mangkrak', color: '#dc3545' }
    ];
    
    items.forEach(item => {
        div.innerHTML += `
            <div class="legend-item">
                <div class="legend-color" style="background: ${item.color}"></div>
                <span class="small">${item.label}</span>
            </div>
        `;
    });
    return div;
};

// =============================================
// LAYER SWITCHING (Updated with Proyek)
// =============================================
let currentLayer = 'aset';

function toggleLayer(layer) {
    currentLayer = layer;
    
    // Update button states
    document.getElementById('layerAset').classList.toggle('active', layer === 'aset');
    document.getElementById('layerPenduduk').classList.toggle('active', layer === 'penduduk');
    document.getElementById('layerProyek').classList.toggle('active', layer === 'proyek');
    
    // Toggle stats/tips visibility
    document.getElementById('asetStats').style.display = layer === 'aset' ? 'flex' : 'none';
    document.getElementById('pendudukStats').style.display = layer === 'penduduk' ? 'block' : 'none';
    document.getElementById('proyekStats').style.display = layer === 'proyek' ? 'block' : 'none';
    document.getElementById('asetTip').style.display = layer === 'aset' ? 'block' : 'none';
    document.getElementById('pendudukTip').style.display = layer === 'penduduk' ? 'block' : 'none';
    document.getElementById('proyekTip').style.display = layer === 'proyek' ? 'block' : 'none';
    
    // Remove all layers first
    map.removeLayer(markers);
    map.removeLayer(populationCircles);
    map.removeLayer(proyekMarkers);
    if (map.hasLayer(assetLegend)) map.removeControl(assetLegend);
    if (map.hasLayer(densityLegend)) map.removeControl(densityLegend);
    if (map.hasLayer(popInfo)) map.removeControl(popInfo);
    if (map.hasLayer(proyekLegend)) map.removeControl(proyekLegend);
    
    if (layer === 'aset') {
        map.addLayer(markers);
        assetLegend.addTo(map);
    } else if (layer === 'penduduk') {
        map.addLayer(populationCircles);
        densityLegend.addTo(map);
        popInfo.addTo(map);
        
        if (!populationData) {
            loadPopulationData();
        }
    } else if (layer === 'proyek') {
        map.addLayer(proyekMarkers);
        proyekLegend.addTo(map);
        
        if (!proyekData) {
            loadProyekData();
        }
    }
}

// Initial load of population data (background)
loadPopulationData();
// Initial load of proyek data (background)
loadProyekData();
</script>

<?= view('layout/footer') ?>

