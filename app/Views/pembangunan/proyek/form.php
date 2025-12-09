<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

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
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Koordinat Lokasi</h5>
                            </div>
                            <div class="card-body">
                                <div id="map" style="height: 200px; border-radius: 8px;" class="mb-3"></div>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" name="lat" id="lat" class="form-control form-control-sm" 
                                               placeholder="Latitude">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="lng" id="lng" class="form-control form-control-sm" 
                                               placeholder="Longitude">
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block">Klik peta untuk menentukan lokasi</small>
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

<?= view('layout/footer') ?>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Map
const map = L.map('map').setView([-6.9, 110.4], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

let marker = null;
map.on('click', function(e) {
    document.getElementById('lat').value = e.latlng.lat.toFixed(8);
    document.getElementById('lng').value = e.latlng.lng.toFixed(8);
    if (marker) {
        marker.setLatLng(e.latlng);
    } else {
        marker = L.marker(e.latlng).addTo(map);
    }
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
