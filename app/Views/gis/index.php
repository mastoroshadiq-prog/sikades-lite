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
</style>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-map-marked-alt me-2 text-success"></i>WebGIS - Peta Aset Desa
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">WebGIS</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('/gis/fullscreen') ?>" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-expand me-2"></i>Fullscreen
            </a>
            <a href="<?= base_url('/aset') ?>" class="btn btn-success">
                <i class="fas fa-boxes me-2"></i>Kelola Aset
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0" id="totalMarkers"><?= $totalAset ?></h3>
                    <small>Aset Terpetakan</small>
                </div>
            </div>
        </div>
        <div class="col-md-9">
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

    <!-- Map -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="map"></div>
        </div>
    </div>

    <!-- Info -->
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Tip:</strong> Klik marker untuk melihat detail aset. Gunakan scroll untuk zoom in/out.
        Aset yang belum memiliki koordinat GPS tidak ditampilkan di peta.
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

// Marker cluster group
const markers = L.markerClusterGroup();

// Category colors
const categoryColors = {
    'Tanah': '#0d6efd',
    'Peralatan dan Mesin': '#198754',
    'Gedung dan Bangunan': '#ffc107',
    'Jalan, Irigasi, dan Jaringan': '#0dcaf0',
    'Aset Tetap Lainnya': '#6c757d',
    'Konstruksi Dalam Pengerjaan': '#dc3545'
};

// Custom icon
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

// Create popup content
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

// Counters
let counts = {
    Tanah: 0,
    Peralatan: 0,
    Gedung: 0,
    Jalan: 0,
    Lainnya: 0
};

// Load GeoJSON data
fetch('<?= base_url('/gis/json') ?>')
    .then(response => response.json())
    .then(data => {
        if (data.features && data.features.length > 0) {
            const bounds = [];
            
            data.features.forEach(feature => {
                const coords = feature.geometry.coordinates;
                const props = feature.properties;
                
                // Create marker
                const marker = L.marker([coords[1], coords[0]], {
                    icon: getIcon(props.kategori)
                });
                
                // Bind popup
                marker.bindPopup(createPopup(props), {
                    maxWidth: 300
                });
                
                markers.addLayer(marker);
                bounds.push([coords[1], coords[0]]);
                
                // Count by category
                if (props.kategori.includes('Tanah')) counts.Tanah++;
                else if (props.kategori.includes('Peralatan')) counts.Peralatan++;
                else if (props.kategori.includes('Gedung')) counts.Gedung++;
                else if (props.kategori.includes('Jalan')) counts.Jalan++;
                else counts.Lainnya++;
            });
            
            map.addLayer(markers);
            
            // Fit bounds if we have markers
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
            
            // Update counters
            document.getElementById('countTanah').textContent = counts.Tanah;
            document.getElementById('countPeralatan').textContent = counts.Peralatan;
            document.getElementById('countGedung').textContent = counts.Gedung;
            document.getElementById('countJalan').textContent = counts.Jalan;
            document.getElementById('countLainnya').textContent = counts.Lainnya;
        }
    })
    .catch(error => console.error('Error loading GeoJSON:', error));

// Legend control
const legend = L.control({ position: 'bottomright' });
legend.onAdd = function(map) {
    const div = L.DomUtil.create('div', 'legend');
    div.innerHTML = '<strong class="mb-2 d-block">Kategori</strong>';
    
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
legend.addTo(map);
</script>

<?= view('layout/footer') ?>
