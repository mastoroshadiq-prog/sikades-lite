<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="mb-1">
                    <i class="fas fa-plus me-2 text-success"></i>Tambah Posyandu
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('/posyandu') ?>">e-Posyandu</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/posyandu/posyandu') ?>">Posyandu</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>

            <form action="<?= base_url('/posyandu/posyandu/save') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Posyandu</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Posyandu <span class="text-danger">*</span></label>
                            <input type="text" name="nama_posyandu" class="form-control form-control-lg" 
                                   placeholder="Contoh: Posyandu Melati RT 01" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dusun/Wilayah</label>
                                <input type="text" name="alamat_dusun" class="form-control" 
                                       placeholder="Contoh: Dusun Krajan">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">RT</label>
                                <input type="text" name="rt" class="form-control" placeholder="01">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">RW</label>
                                <input type="text" name="rw" class="form-control" placeholder="01">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Ketua Posyandu</label>
                                <input type="text" name="ketua_posyandu" class="form-control" 
                                       placeholder="Nama ketua posyandu">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="no_telp" class="form-control" 
                                       placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Koordinat Lokasi (Opsional)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="lat" class="form-control" id="lat"
                                       placeholder="-6.123456">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="lng" class="form-control" id="lng"
                                       placeholder="110.123456">
                            </div>
                        </div>
                        <div id="map" style="height: 300px; border-radius: 8px;" class="mb-2"></div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>Klik pada peta untuk menentukan lokasi posyandu
                        </small>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('/posyandu/posyandu') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-save me-2"></i>Simpan Posyandu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const map = L.map('map').setView([-6.9, 110.4], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

let marker = null;

map.on('click', function(e) {
    const lat = e.latlng.lat.toFixed(8);
    const lng = e.latlng.lng.toFixed(8);
    
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
    
    if (marker) {
        marker.setLatLng(e.latlng);
    } else {
        marker = L.marker(e.latlng).addTo(map);
    }
});
</script>
