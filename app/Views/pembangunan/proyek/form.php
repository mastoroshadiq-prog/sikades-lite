<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<style>
/* Fullscreen map styles */
.map-fullscreen-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 9999;
}
.map-fullscreen-overlay.active {
    display: flex;
    flex-direction: column;
}
.map-fullscreen-overlay .map-header {
    padding: 15px 20px;
    background: #333;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.map-fullscreen-overlay #mapFullscreen {
    flex: 1;
}
.map-coords-display {
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    font-family: monospace;
}
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="mb-1">
                    <i class="fas fa-plus me-2 text-warning"></i>Tambah Proyek Baru
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('/pembangunan') ?>">e-Pembangunan</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/pembangunan/proyek') ?>">Proyek</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>

            <form action="<?= base_url('/pembangunan/proyek/save') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Basic Info -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-warning text-dark py-3">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Proyek</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Link ke APBDes (Opsional)</label>
                                    <select name="apbdes_id" class="form-select" id="apbdesSelect">
                                        <option value="">-- Pilih Kegiatan APBDes --</option>
                                        <?php foreach ($kegiatanList as $k): ?>
                                            <option value="<?= $k['id'] ?>" 
                                                    data-kode="<?= $k['kode_kegiatan'] ?>"
                                                    data-nama="<?= esc($k['uraian']) ?>"
                                                    data-pagu="<?= $k['pagu_anggaran'] ?>">
                                                <?= $k['kode_kegiatan'] ?> - <?= esc($k['uraian']) ?> 
                                                (Rp <?= number_format($k['pagu_anggaran'], 0, ',', '.') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (!empty($debugInfo)): ?>
                                        <div class="alert alert-info mt-2 py-2 small">
                                            <i class="fas fa-info-circle me-1"></i><?= $debugInfo ?>
                                        </div>
                                    <?php elseif (empty($kegiatanList)): ?>
                                        <small class="text-muted">Tidak ada data APBDes - Anda bisa mengisi anggaran manual</small>
                                    <?php endif; ?>
                                    <input type="hidden" name="kode_kegiatan" id="kodeKegiatan">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nama Proyek <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_proyek" class="form-control form-control-lg" 
                                           placeholder="Contoh: Pembangunan Talud RT 01" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Lokasi Detail</label>
                                    <input type="text" name="lokasi_detail" class="form-control" 
                                           placeholder="Contoh: Dusun Krajan RT 01/01, sebelah barat masjid">
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Volume Target</label>
                                        <input type="number" name="volume_target" class="form-control" 
                                               step="0.01" placeholder="200">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Satuan</label>
                                        <select name="satuan" class="form-select">
                                            <option value="">Pilih</option>
                                            <?php foreach ($satuanOptions as $key => $label): ?>
                                                <option value="<?= $key ?>"><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Anggaran (Rp) <span class="text-danger">*</span></label>
                                        <input type="text" name="anggaran" id="anggaran" class="form-control" 
                                               placeholder="50.000.000" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule & Executor -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Jadwal & Pelaksana</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="date" name="tgl_mulai" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Target Selesai</label>
                                        <input type="date" name="tgl_selesai_target" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pelaksana Kegiatan (TPK)</label>
                                        <input type="text" name="pelaksana_kegiatan" class="form-control" 
                                               placeholder="Nama Ketua TPK">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kontraktor/Penyedia (jika ada)</label>
                                        <input type="text" name="kontraktor" class="form-control" 
                                               placeholder="Nama kontraktor">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="2" 
                                              placeholder="Catatan tambahan"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Location -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Koordinat Lokasi</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openFullscreenMap()">
                                    <i class="fas fa-expand me-1"></i>Perbesar
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="map" style="height: 200px; border-radius: 8px;" class="mb-3"></div>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" name="lat" id="lat" class="form-control form-control-sm" 
                                               placeholder="Latitude" readonly>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="lng" id="lng" class="form-control form-control-sm" 
                                               placeholder="Longitude" readonly>
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i>Klik tombol <strong>Perbesar</strong> untuk memilih lokasi dengan lebih akurat
                                </small>
                            </div>
                        </div>

                        <!-- Initial Photo -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="fas fa-camera me-2 text-info"></i>Foto Awal (0%)</h5>
                            </div>
                            <div class="card-body">
                                <input type="file" name="foto_0" class="form-control" accept="image/*">
                                <small class="text-muted">Foto kondisi lokasi sebelum pembangunan</small>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan Proyek
                            </button>
                            <a href="<?= base_url('/pembangunan/proyek') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fullscreen Map Overlay -->
<div class="map-fullscreen-overlay" id="mapFullscreenOverlay">
    <div class="map-header">
        <div>
            <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Pilih Lokasi Proyek</h5>
            <small class="text-muted">Klik pada peta untuk menentukan koordinat lokasi</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="map-coords-display" id="coordsDisplay">Lat: -, Lng: -</div>
            <button type="button" class="btn btn-success" onclick="confirmLocation()">
                <i class="fas fa-check me-1"></i>Konfirmasi Lokasi
            </button>
            <button type="button" class="btn btn-light" onclick="closeFullscreenMap()">
                <i class="fas fa-times me-1"></i>Batal
            </button>
        </div>
    </div>
    <div id="mapFullscreen"></div>
</div>

<?= view('layout/footer') ?>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Small map
const map = L.map('map').setView([-6.9, 110.4], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

let marker = null;
let tempCoords = null;

// Click on small map
map.on('click', function(e) {
    setCoordinates(e.latlng.lat, e.latlng.lng);
});

function setCoordinates(lat, lng) {
    document.getElementById('lat').value = lat.toFixed(8);
    document.getElementById('lng').value = lng.toFixed(8);
    
    if (marker) {
        marker.setLatLng([lat, lng]);
    } else {
        marker = L.marker([lat, lng]).addTo(map);
    }
    map.setView([lat, lng], 15);
}

// Fullscreen map
let fullscreenMap = null;
let fullscreenMarker = null;

function openFullscreenMap() {
    document.getElementById('mapFullscreenOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Get current coords if set
    const currentLat = document.getElementById('lat').value;
    const currentLng = document.getElementById('lng').value;
    const centerLat = currentLat ? parseFloat(currentLat) : -6.9;
    const centerLng = currentLng ? parseFloat(currentLng) : 110.4;
    const zoom = currentLat ? 16 : 12;
    
    if (!fullscreenMap) {
        fullscreenMap = L.map('mapFullscreen').setView([centerLat, centerLng], zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(fullscreenMap);
        
        // Add search hint
        const searchHint = L.control({position: 'topleft'});
        searchHint.onAdd = function() {
            const div = L.DomUtil.create('div', 'leaflet-control');
            div.innerHTML = '<div style="background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">' +
                '<i class="fas fa-mouse-pointer me-2"></i><strong>Klik</strong> pada peta untuk memilih lokasi</div>';
            return div;
        };
        searchHint.addTo(fullscreenMap);
        
        // Click handler for fullscreen map
        fullscreenMap.on('click', function(e) {
            tempCoords = e.latlng;
            
            if (fullscreenMarker) {
                fullscreenMarker.setLatLng(e.latlng);
            } else {
                fullscreenMarker = L.marker(e.latlng, {
                    draggable: true
                }).addTo(fullscreenMap);
                
                fullscreenMarker.on('dragend', function(e) {
                    tempCoords = e.target.getLatLng();
                    updateCoordsDisplay(tempCoords.lat, tempCoords.lng);
                });
            }
            
            updateCoordsDisplay(e.latlng.lat, e.latlng.lng);
        });
        
        // If already has coords, show marker
        if (currentLat && currentLng) {
            tempCoords = {lat: centerLat, lng: centerLng};
            fullscreenMarker = L.marker([centerLat, centerLng], {draggable: true}).addTo(fullscreenMap);
            fullscreenMarker.on('dragend', function(e) {
                tempCoords = e.target.getLatLng();
                updateCoordsDisplay(tempCoords.lat, tempCoords.lng);
            });
            updateCoordsDisplay(centerLat, centerLng);
        }
    } else {
        fullscreenMap.setView([centerLat, centerLng], zoom);
        setTimeout(() => fullscreenMap.invalidateSize(), 100);
    }
}

function updateCoordsDisplay(lat, lng) {
    document.getElementById('coordsDisplay').innerHTML = 
        'Lat: <strong>' + lat.toFixed(6) + '</strong>, Lng: <strong>' + lng.toFixed(6) + '</strong>';
}

function confirmLocation() {
    if (tempCoords) {
        setCoordinates(tempCoords.lat, tempCoords.lng);
        closeFullscreenMap();
    } else {
        alert('Silakan klik pada peta untuk memilih lokasi terlebih dahulu!');
    }
}

function closeFullscreenMap() {
    document.getElementById('mapFullscreenOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

// Close on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeFullscreenMap();
});

// APBDes autofill
document.getElementById('apbdesSelect').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (this.value) {
        document.getElementById('kodeKegiatan').value = option.dataset.kode;
        document.querySelector('input[name="nama_proyek"]').value = option.dataset.nama;
        document.getElementById('anggaran').value = formatRupiah(option.dataset.pagu);
    }
});

// Format rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka);
}

document.getElementById('anggaran').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = formatRupiah(value);
});
</script>
