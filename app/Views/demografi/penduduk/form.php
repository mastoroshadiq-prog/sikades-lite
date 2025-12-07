<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-<?= isset($penduduk) ? 'edit' : 'user-plus' ?> me-2 text-<?= isset($penduduk) ? 'warning' : 'primary' ?>"></i>
                <?= isset($penduduk) ? 'Edit' : 'Tambah' ?> Data Penduduk
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi') ?>">Demografi</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi/penduduk') ?>">Data Penduduk</a></li>
                    <li class="breadcrumb-item active"><?= isset($penduduk) ? 'Edit' : 'Tambah' ?></li>
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

    <form action="<?= isset($penduduk) ? base_url('/demografi/penduduk/update/' . $penduduk['id']) : base_url('/demografi/penduduk/save') ?>" method="POST">
        <?= csrf_field() ?>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Data Utama -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Data Identitas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kartu Keluarga <span class="text-danger">*</span></label>
                                <select name="keluarga_id" class="form-select select2-search" required data-placeholder="-- Pilih KK --">
                                    <option value=""></option>
                                    <?php foreach ($keluargaList as $kk): ?>
                                    <option value="<?= $kk['id'] ?>" <?= old('keluarga_id', $penduduk['keluarga_id'] ?? ($keluarga['id'] ?? '')) == $kk['id'] ? 'selected' : '' ?>>
                                        <?= esc($kk['no_kk']) ?> - <?= esc($kk['kepala_keluarga']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" name="nik" class="form-control" 
                                       value="<?= old('nik', $penduduk['nik'] ?? '') ?>" 
                                       maxlength="16" minlength="16" pattern="[0-9]{16}"
                                       placeholder="16 digit NIK" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control form-control-lg" 
                                       value="<?= old('nama_lengkap', $penduduk['nama_lengkap'] ?? '') ?>" 
                                       placeholder="Nama sesuai KTP" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" 
                                       value="<?= old('tempat_lahir', $penduduk['tempat_lahir'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" 
                                       value="<?= old('tanggal_lahir', $penduduk['tanggal_lahir'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L" <?= old('jenis_kelamin', $penduduk['jenis_kelamin'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= old('jenis_kelamin', $penduduk['jenis_kelamin'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Agama</label>
                                <select name="agama" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($agamaOptions as $agama): ?>
                                    <option value="<?= $agama ?>" <?= old('agama', $penduduk['agama'] ?? '') == $agama ? 'selected' : '' ?>><?= $agama ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Golongan Darah</label>
                                <select name="golongan_darah" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($darahOptions as $gol): ?>
                                    <option value="<?= $gol ?>" <?= old('golongan_darah', $penduduk['golongan_darah'] ?? '') == $gol ? 'selected' : '' ?>><?= $gol ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Status Hubungan dalam Keluarga</label>
                                <select name="status_hubungan" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($hubunganOptions as $hub): ?>
                                    <option value="<?= $hub ?>" <?= old('status_hubungan', $penduduk['status_hubungan'] ?? '') == $hub ? 'selected' : '' ?>><?= $hub ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pendidikan & Pekerjaan -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2 text-info"></i>Pendidikan & Pekerjaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($refPendidikan as $edu): ?>
                                    <option value="<?= esc($edu['nama']) ?>" <?= old('pendidikan_terakhir', $penduduk['pendidikan_terakhir'] ?? '') == $edu['nama'] ? 'selected' : '' ?>><?= esc($edu['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Pekerjaan</label>
                                <select name="pekerjaan" class="form-select select2-search" data-placeholder="-- Pilih Pekerjaan --">
                                    <option value=""></option>
                                    <?php foreach ($refPekerjaan as $job): ?>
                                    <option value="<?= esc($job['nama']) ?>" <?= old('pekerjaan', $penduduk['pekerjaan'] ?? '') == $job['nama'] ? 'selected' : '' ?>><?= esc($job['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Status Perkawinan</label>
                                <select name="status_perkawinan" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($kawinOptions as $status): ?>
                                    <option value="<?= $status ?>" <?= old('status_perkawinan', $penduduk['status_perkawinan'] ?? '') == $status ? 'selected' : '' ?>><?= $status ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Kewarganegaraan</label>
                                <select name="kewarganegaraan" class="form-select">
                                    <option value="WNI" <?= old('kewarganegaraan', $penduduk['kewarganegaraan'] ?? 'WNI') == 'WNI' ? 'selected' : '' ?>>WNI</option>
                                    <option value="WNA" <?= old('kewarganegaraan', $penduduk['kewarganegaraan'] ?? '') == 'WNA' ? 'selected' : '' ?>>WNA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Data Orang Tua -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-user-friends me-2 text-success"></i>Data Orang Tua</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Ayah</label>
                                <input type="text" name="nama_ayah" class="form-control" 
                                       value="<?= old('nama_ayah', $penduduk['nama_ayah'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Ibu</label>
                                <input type="text" name="nama_ibu" class="form-control" 
                                       value="<?= old('nama_ibu', $penduduk['nama_ibu'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status Khusus -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-tag me-2 text-warning"></i>Status Khusus</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_miskin" value="1" id="isMiskin"
                                   <?= old('is_miskin', $penduduk['is_miskin'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isMiskin">
                                <strong>Keluarga Miskin (DTKS)</strong>
                                <br><small class="text-muted">Terdaftar dalam Data Terpadu Kesejahteraan Sosial</small>
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_disabilitas" value="1" id="isDisabilitas"
                                   <?= old('is_disabilitas', $penduduk['is_disabilitas'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isDisabilitas">
                                <strong>Penyandang Disabilitas</strong>
                            </label>
                        </div>
                        
                        <div id="disabilitasField" style="<?= old('is_disabilitas', $penduduk['is_disabilitas'] ?? 0) ? '' : 'display:none;' ?>">
                            <label class="form-label">Jenis Disabilitas</label>
                            <input type="text" name="jenis_disabilitas" class="form-control" 
                                   value="<?= old('jenis_disabilitas', $penduduk['jenis_disabilitas'] ?? '') ?>"
                                   placeholder="Contoh: Tuna Rungu, Tuna Netra">
                        </div>
                        
                        <?php if (!isset($penduduk)): ?>
                        <hr>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_kelahiran" value="1" id="isKelahiran">
                            <label class="form-check-label" for="isKelahiran">
                                <strong>Registrasi Kelahiran Baru</strong>
                                <br><small class="text-muted">Catat sebagai mutasi kelahiran</small>
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Simpan Data
                    </button>
                    <a href="<?= base_url('/demografi/penduduk') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<?= view('layout/footer') ?>

<script>
document.getElementById('isDisabilitas').addEventListener('change', function() {
    document.getElementById('disabilitasField').style.display = this.checked ? 'block' : 'none';
});
</script>
