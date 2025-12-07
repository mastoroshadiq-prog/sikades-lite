<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-edit me-2 text-warning"></i>Edit Aset
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/aset') ?>">SIPADES</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/aset/list') ?>">Daftar Aset</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('/aset/update/' . $aset['id']) ?>" method="POST" enctype="multipart/form-data">
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
                            <div class="col-md-6">
                                <label class="form-label">Kode Register</label>
                                <input type="text" class="form-control" value="<?= esc($aset['kode_register']) ?>" readonly disabled>
                                <small class="text-muted">Kode register tidak dapat diubah</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" disabled>
                                    <?php foreach ($categories as $id => $label): ?>
                                        <option value="<?= $id ?>" <?= $aset['kategori_id'] == $id ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Kategori tidak dapat diubah</small>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="nama_barang" class="form-control form-control-lg" 
                                       value="<?= old('nama_barang', $aset['nama_barang']) ?>" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Merk / Type</label>
                                <input type="text" name="merk_type" class="form-control" 
                                       value="<?= old('merk_type', $aset['merk_type']) ?>">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Ukuran / Luas</label>
                                <input type="text" name="ukuran" class="form-control" 
                                       value="<?= old('ukuran', $aset['ukuran']) ?>">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Bahan</label>
                                <input type="text" name="bahan" class="form-control" 
                                       value="<?= old('bahan', $aset['bahan']) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-sliders-h me-2 text-info"></i>Kondisi & Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                                <select name="kondisi" class="form-select" required>
                                    <option value="Baik" <?= $aset['kondisi'] == 'Baik' ? 'selected' : '' ?>>Baik</option>
                                    <option value="Rusak Ringan" <?= $aset['kondisi'] == 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                                    <option value="Rusak Berat" <?= $aset['kondisi'] == 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Status Penggunaan</label>
                                <select name="status_penggunaan" class="form-select">
                                    <option value="Digunakan" <?= $aset['status_penggunaan'] == 'Digunakan' ? 'selected' : '' ?>>Digunakan</option>
                                    <option value="Tidak Digunakan" <?= $aset['status_penggunaan'] == 'Tidak Digunakan' ? 'selected' : '' ?>>Tidak Digunakan</option>
                                    <option value="Dipinjamkan" <?= $aset['status_penggunaan'] == 'Dipinjamkan' ? 'selected' : '' ?>>Dipinjamkan</option>
                                    <option value="Dihapuskan" <?= $aset['status_penggunaan'] == 'Dihapuskan' ? 'selected' : '' ?>>Dihapuskan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-success"></i>Lokasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Lokasi Penempatan</label>
                                <input type="text" name="lokasi" class="form-control" 
                                       value="<?= old('lokasi', $aset['lokasi']) ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Pengguna</label>
                                <input type="text" name="pengguna" class="form-control" 
                                       value="<?= old('pengguna', $aset['pengguna']) ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="lat" class="form-control" 
                                       value="<?= old('lat', $aset['lat']) ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="lng" class="form-control" 
                                       value="<?= old('lng', $aset['lng']) ?>">
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
                        <?php if ($aset['foto']): ?>
                        <div class="mb-3 text-center">
                            <?php $fotoFilename = basename($aset['foto']); ?>
                            <img src="<?= base_url('/assets/image/' . $fotoFilename) ?>" 
                                 class="img-fluid rounded" style="max-height: 200px;">
                            <p class="text-muted small mt-2">Foto saat ini</p>
                        </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Upload Foto Baru</label>
                            <input type="file" name="foto" class="form-control" accept="image/*" id="fotoInput">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
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
                        <textarea name="keterangan" class="form-control" rows="4"><?= old('keterangan', $aset['keterangan']) ?></textarea>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                    <a href="<?= base_url('/aset/detail/' . $aset['id']) ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
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
