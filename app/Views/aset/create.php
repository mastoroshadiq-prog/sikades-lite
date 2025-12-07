<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Aset Baru
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/aset') ?>">SIPADES</a></li>
                    <li class="breadcrumb-item active">Tambah Aset</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-circle me-2"></i>Terjadi Kesalahan:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (!empty($prefill['bku_id'])): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Auto-Fill dari BKU:</strong> Data aset telah diisi otomatis dari transaksi Belanja Modal. Silakan lengkapi informasi lainnya.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('/aset/store') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Dasar</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="nama_barang" class="form-control form-control-lg" 
                                       value="<?= old('nama_barang', $prefill['nama_barang'] ?? '') ?>" 
                                       placeholder="Contoh: Komputer Desktop" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Kategori Aset <span class="text-danger">*</span></label>
                                <select name="kategori_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $id => $label): ?>
                                        <option value="<?= $id ?>" <?= old('kategori_id') == $id ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Merk / Type</label>
                                <input type="text" name="merk_type" class="form-control" 
                                       value="<?= old('merk_type') ?>" 
                                       placeholder="Contoh: HP ProDesk 400 G7">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Ukuran / Luas</label>
                                <input type="text" name="ukuran" class="form-control" 
                                       value="<?= old('ukuran') ?>" 
                                       placeholder="Contoh: 500 m2 atau 1 unit">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Bahan</label>
                                <input type="text" name="bahan" class="form-control" 
                                       value="<?= old('bahan') ?>" 
                                       placeholder="Contoh: Besi, Kayu, dll">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Tahun Perolehan <span class="text-danger">*</span></label>
                                <input type="number" name="tahun_perolehan" class="form-control" 
                                       value="<?= old('tahun_perolehan', $prefill['tahun_perolehan'] ?? date('Y')) ?>" 
                                       min="1990" max="2099" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-money-bill me-2 text-success"></i>Nilai & Kondisi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Harga Perolehan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="harga_display" class="form-control currency-input" 
                                           value="<?= old('harga_perolehan', isset($prefill['harga_perolehan']) ? number_format($prefill['harga_perolehan'], 0, ',', '.') : '') ?>" 
                                           placeholder="0" required>
                                    <input type="hidden" name="harga_perolehan" id="harga_perolehan" 
                                           value="<?= old('harga_perolehan', $prefill['harga_perolehan'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Sumber Dana <span class="text-danger">*</span></label>
                                <select name="sumber_dana" class="form-select" required>
                                    <option value="APBDes" <?= old('sumber_dana', $prefill['sumber_dana'] ?? '') == 'APBDes' ? 'selected' : '' ?>>APBDes</option>
                                    <option value="Hibah" <?= old('sumber_dana') == 'Hibah' ? 'selected' : '' ?>>Hibah</option>
                                    <option value="Bantuan Pemerintah" <?= old('sumber_dana') == 'Bantuan Pemerintah' ? 'selected' : '' ?>>Bantuan Pemerintah</option>
                                    <option value="Swadaya" <?= old('sumber_dana') == 'Swadaya' ? 'selected' : '' ?>>Swadaya</option>
                                    <option value="Lainnya" <?= old('sumber_dana') == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                                <select name="kondisi" class="form-select" required>
                                    <option value="Baik" <?= old('kondisi') == 'Baik' ? 'selected' : '' ?>>Baik</option>
                                    <option value="Rusak Ringan" <?= old('kondisi') == 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                                    <option value="Rusak Berat" <?= old('kondisi') == 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Status Penggunaan</label>
                                <select name="status_penggunaan" class="form-select">
                                    <option value="Digunakan">Digunakan</option>
                                    <option value="Tidak Digunakan">Tidak Digunakan</option>
                                    <option value="Dipinjamkan">Dipinjamkan</option>
                                    <option value="Dihapuskan">Dihapuskan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-info"></i>Lokasi & Pengguna</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Lokasi Penempatan</label>
                                <input type="text" name="lokasi" class="form-control" 
                                       value="<?= old('lokasi') ?>" 
                                       placeholder="Contoh: Kantor Desa, Ruang Sekretaris">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Pengguna / Penanggung Jawab</label>
                                <input type="text" name="pengguna" class="form-control" 
                                       value="<?= old('pengguna') ?>" 
                                       placeholder="Contoh: Sekretaris Desa">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Latitude (untuk Peta)</label>
                                <input type="text" name="lat" class="form-control" 
                                       value="<?= old('lat') ?>" 
                                       placeholder="Contoh: -6.5945">
                                <small class="text-muted">Koordinat GPS untuk WebGIS</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Longitude (untuk Peta)</label>
                                <input type="text" name="lng" class="form-control" 
                                       value="<?= old('lng') ?>" 
                                       placeholder="Contoh: 106.7969">
                                <small class="text-muted">Koordinat GPS untuk WebGIS</small>
                            </div>
                            
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="getLocation()">
                                    <i class="fas fa-crosshairs me-1"></i>Ambil Lokasi Saat Ini
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-camera me-2 text-warning"></i>Foto Aset</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" name="foto" class="form-control" accept="image/*" id="fotoInput">
                            <small class="text-muted">Format: JPG, PNG, GIF. Max 2MB</small>
                        </div>
                        <div id="fotoPreview" class="text-center" style="display:none;">
                            <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-sticky-note me-2 text-secondary"></i>Keterangan</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="4" 
                                  placeholder="Catatan tambahan tentang aset ini..."><?= old('keterangan') ?></textarea>
                    </div>
                </div>

                <?php if (!empty($prefill['bku_id'])): ?>
                <input type="hidden" name="bku_id" value="<?= $prefill['bku_id'] ?>">
                <div class="alert alert-success">
                    <i class="fas fa-link me-2"></i>
                    Aset ini akan terhubung dengan transaksi BKU
                </div>
                <?php endif; ?>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Simpan Aset
                    </button>
                    <a href="<?= base_url('/aset') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Currency format with hidden field sync
document.querySelectorAll('.currency-input').forEach(function(input) {
    input.addEventListener('input', function(e) {
        // Remove non-digits
        let rawValue = e.target.value.replace(/\D/g, '');
        
        // Format for display
        e.target.value = new Intl.NumberFormat('id-ID').format(rawValue);
        
        // Update hidden field with raw numeric value
        const hiddenField = document.getElementById('harga_perolehan');
        if (hiddenField) {
            hiddenField.value = rawValue;
        }
    });
    
    // Also trigger on page load to sync initial values
    if (input.value) {
        let rawValue = input.value.replace(/\D/g, '');
        const hiddenField = document.getElementById('harga_perolehan');
        if (hiddenField && rawValue) {
            hiddenField.value = rawValue;
        }
    }
});

// Foto preview
document.getElementById('fotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('#fotoPreview img').src = e.target.result;
            document.getElementById('fotoPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Get current location
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.querySelector('input[name="lat"]').value = position.coords.latitude.toFixed(8);
            document.querySelector('input[name="lng"]').value = position.coords.longitude.toFixed(8);
        }, function(error) {
            alert('Tidak dapat mengambil lokasi: ' + error.message);
        });
    } else {
        alert('Browser tidak mendukung geolocation');
    }
}
</script>

<?= view('layout/footer') ?>
