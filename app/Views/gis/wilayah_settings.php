<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    #settingsMap {
        height: 400px;
        border-radius: 0.5rem;
    }
    .wilayah-card {
        transition: all 0.2s;
        cursor: pointer;
    }
    .wilayah-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    .wilayah-card.selected {
        border: 2px solid #0d6efd !important;
        background: #e7f1ff;
    }
    .coord-input {
        width: 120px;
    }
</style>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-cog me-2 text-primary"></i>Pengaturan Wilayah GIS
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/gis') ?>">WebGIS</a></li>
                    <li class="breadcrumb-item active">Pengaturan Wilayah</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('/gis') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Peta
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left: Wilayah List -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Wilayah (Dusun)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Klik wilayah untuk memilih, lalu klik pada peta untuk menentukan titik pusat.
                    </p>
                    
                    <?php if (empty($wilayahs)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-map-marker-alt fa-3x mb-3 opacity-50"></i>
                        <p>Belum ada data wilayah</p>
                        <p class="small">Data wilayah akan otomatis dibuat dari data keluarga (dusun)</p>
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($wilayahs as $w): ?>
                        <div class="list-group-item wilayah-card d-flex justify-content-between align-items-center" 
                             data-id="<?= $w['id'] ?>"
                             data-name="<?= esc($w['nama_wilayah']) ?>"
                             data-lat="<?= $w['center_lat'] ?>"
                             data-lng="<?= $w['center_lng'] ?>"
                             onclick="selectWilayah(this)">
                            <div>
                                <h6 class="mb-0"><?= esc($w['nama_wilayah']) ?></h6>
                                <small class="text-muted">
                                    <?php if ($w['center_lat'] && $w['center_lng']): ?>
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    <?= number_format($w['center_lat'], 6) ?>, <?= number_format($w['center_lng'], 6) ?>
                                    <?php else: ?>
                                    <i class="fas fa-times-circle text-danger me-1"></i>
                                    Koordinat belum diatur
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div>
                                <?php if ($w['geojson']): ?>
                                <span class="badge bg-success"><i class="fas fa-draw-polygon"></i></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upload GeoJSON -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Upload Batas Wilayah</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Upload file GeoJSON berisi polygon batas wilayah dusun. 
                        File harus memiliki properti <code>nama</code> atau <code>name</code> untuk setiap feature.
                    </p>
                    
                    <form action="<?= base_url('/gis/wilayah/upload') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">File GeoJSON</label>
                            <input type="file" name="geojson_file" class="form-control" accept=".json,.geojson" required>
                            <div class="form-text">Format: .json atau .geojson</div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload me-2"></i>Upload & Import
                        </button>
                    </form>
                    
                    <hr>
                    
                    <h6>Contoh Format GeoJSON:</h6>
                    <pre class="bg-light p-2 rounded small"><code>{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "properties": {
        "nama": "Dusun I"
      },
      "geometry": {
        "type": "Polygon",
        "coordinates": [[[lng,lat], ...]]
      }
    }
  ]
}</code></pre>
                    
                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tip:</strong> Anda bisa menggunakan tools seperti 
                        <a href="https://geojson.io" target="_blank">geojson.io</a> atau 
                        <a href="https://qgis.org" target="_blank">QGIS</a> untuk membuat file GeoJSON dari shapefile.
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Map -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-map me-2"></i>Peta Lokasi</h5>
                    <span class="badge bg-primary" id="selectedWilayah">Pilih wilayah terlebih dahulu</span>
                </div>
                <div class="card-body p-0">
                    <div id="settingsMap"></div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <label class="form-label mb-0">Koordinat:</label>
                        </div>
                        <div class="col-auto">
                            <input type="text" id="inputLat" class="form-control form-control-sm coord-input" placeholder="Latitude" readonly>
                        </div>
                        <div class="col-auto">
                            <input type="text" id="inputLng" class="form-control form-control-sm coord-input" placeholder="Longitude" readonly>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary btn-sm" onclick="saveCoordinates()" id="btnSave" disabled>
                                <i class="fas fa-save me-1"></i>Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <h6><i class="fas fa-question-circle me-2"></i>Cara Menggunakan:</h6>
                <ol class="mb-0 small">
                    <li>Pilih wilayah (dusun) dari daftar di sebelah kiri</li>
                    <li>Klik pada peta untuk menentukan titik pusat wilayah tersebut</li>
                    <li>Klik tombol "Simpan" untuk menyimpan koordinat</li>
                    <li>Ulangi untuk semua wilayah</li>
                    <li>Atau upload file GeoJSON untuk mengimpor batas wilayah sekaligus</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Initialize map
const map = L.map('settingsMap').setView([-6.2088, 106.8456], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

let selectedWilayahId = null;
let currentMarker = null;
let existingMarkers = [];

// Load existing markers
<?php foreach ($wilayahs as $w): ?>
<?php if ($w['center_lat'] && $w['center_lng']): ?>
existingMarkers.push(
    L.marker([<?= $w['center_lat'] ?>, <?= $w['center_lng'] ?>])
        .addTo(map)
        .bindPopup('<strong><?= esc($w['nama_wilayah']) ?></strong>')
);
<?php endif; ?>
<?php endforeach; ?>

// Fit bounds to existing markers
if (existingMarkers.length > 0) {
    const group = new L.featureGroup(existingMarkers);
    map.fitBounds(group.getBounds().pad(0.1));
}

// Select wilayah
function selectWilayah(el) {
    // Remove previous selection
    document.querySelectorAll('.wilayah-card').forEach(c => c.classList.remove('selected'));
    
    // Select this one
    el.classList.add('selected');
    
    selectedWilayahId = el.dataset.id;
    const name = el.dataset.name;
    const lat = el.dataset.lat;
    const lng = el.dataset.lng;
    
    document.getElementById('selectedWilayah').textContent = name;
    document.getElementById('btnSave').disabled = true;
    
    // If has coordinates, show marker and pan
    if (lat && lng && lat !== '' && lng !== '') {
        document.getElementById('inputLat').value = lat;
        document.getElementById('inputLng').value = lng;
        
        if (currentMarker) {
            currentMarker.remove();
        }
        currentMarker = L.marker([parseFloat(lat), parseFloat(lng)], {
            draggable: true,
            icon: L.divIcon({
                className: 'custom-marker',
                html: '<div style="background: #dc3545; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            })
        }).addTo(map);
        
        currentMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            document.getElementById('inputLat').value = pos.lat.toFixed(8);
            document.getElementById('inputLng').value = pos.lng.toFixed(8);
            document.getElementById('btnSave').disabled = false;
        });
        
        map.panTo([parseFloat(lat), parseFloat(lng)]);
    } else {
        document.getElementById('inputLat').value = '';
        document.getElementById('inputLng').value = '';
        if (currentMarker) {
            currentMarker.remove();
            currentMarker = null;
        }
    }
}

// Map click handler
map.on('click', function(e) {
    if (!selectedWilayahId) {
        alert('Pilih wilayah terlebih dahulu!');
        return;
    }
    
    const lat = e.latlng.lat.toFixed(8);
    const lng = e.latlng.lng.toFixed(8);
    
    document.getElementById('inputLat').value = lat;
    document.getElementById('inputLng').value = lng;
    document.getElementById('btnSave').disabled = false;
    
    if (currentMarker) {
        currentMarker.setLatLng(e.latlng);
    } else {
        currentMarker = L.marker(e.latlng, {
            draggable: true,
            icon: L.divIcon({
                className: 'custom-marker',
                html: '<div style="background: #dc3545; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            })
        }).addTo(map);
        
        currentMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            document.getElementById('inputLat').value = pos.lat.toFixed(8);
            document.getElementById('inputLng').value = pos.lng.toFixed(8);
        });
    }
});

// Save coordinates
function saveCoordinates() {
    if (!selectedWilayahId) return;
    
    const lat = document.getElementById('inputLat').value;
    const lng = document.getElementById('inputLng').value;
    
    fetch('<?= base_url('/gis/wilayah/coordinates') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `id=${selectedWilayahId}&lat=${lat}&lng=${lng}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update card
            const card = document.querySelector(`.wilayah-card[data-id="${selectedWilayahId}"]`);
            card.dataset.lat = lat;
            card.dataset.lng = lng;
            card.querySelector('small').innerHTML = `
                <i class="fas fa-check-circle text-success me-1"></i>
                ${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}
            `;
            
            document.getElementById('btnSave').disabled = true;
            alert('Koordinat berhasil disimpan!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan');
    });
}
</script>

<?= view('layout/footer') ?>
