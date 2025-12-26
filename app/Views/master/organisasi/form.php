<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/master/organisasi') ?>">Struktur Organisasi</a></li>
            <li class="breadcrumb-item active"><?= isset($perangkat) ? 'Edit' : 'Tambah' ?> Perangkat</li>
        </ol>
    </nav>
    <h2 class="mb-1">
        <i class="fas fa-user-plus text-primary"></i> 
        <?= isset($perangkat) ? 'Edit' : 'Tambah' ?> Perangkat Desa
    </h2>
</div>

<!-- Form Card -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-form me-2"></i>Form Data Perangkat</h5>
            </div>
            <div class="card-body">
                <form action="<?= isset($perangkat) ? base_url('/master/organisasi/update/' . $perangkat['id']) : base_url('/master/organisasi/store') ?>" 
                      method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                   value="<?= old('nama', $perangkat['nama'] ?? '') ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <select class="form-select" id="jabatan" name="jabatan" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <option value="Kepala Desa" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kepala Desa' ? 'selected' : '' ?>>Kepala Desa</option>
                                <option value="Sekretaris Desa" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Sekretaris Desa' ? 'selected' : '' ?>>Sekretaris Desa</option>
                                <option value="Kaur Keuangan" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kaur Keuangan' ? 'selected' : '' ?>>Kaur Keuangan</option>
                                <option value="Kaur Perencanaan" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kaur Perencanaan' ? 'selected' : '' ?>>Kaur Perencanaan</option>
                                <option value="Kaur Tata Usaha dan Umum" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kaur Tata Usaha dan Umum' ? 'selected' : '' ?>>Kaur Tata Usaha dan Umum</option>
                                <option value="Kasi Pemerintahan" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kasi Pemerintahan' ? 'selected' : '' ?>>Kasi Pemerintahan</option>
                                <option value="Kasi Kesejahteraan" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kasi Kesejahteraan' ? 'selected' : '' ?>>Kasi Kesejahteraan</option>
                                <option value="Kasi Pelayanan" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kasi Pelayanan' ? 'selected' : '' ?>>Kasi Pelayanan</option>
                                <option value="Kepala Dusun I" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kepala Dusun I' ? 'selected' : '' ?>>Kepala Dusun I</option>
                                <option value="Kepala Dusun II" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kepala Dusun II' ? 'selected' : '' ?>>Kepala Dusun II</option>
                                <option value="Kepala Dusun III" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kepala Dusun III' ? 'selected' : '' ?>>Kepala Dusun III</option>
                                <option value="Kepala Dusun IV" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Kepala Dusun IV' ? 'selected' : '' ?>>Kepala Dusun IV</option>
                                <option value="Staff" <?= old('jabatan', $perangkat['jabatan'] ?? '') == 'Staff' ? 'selected' : '' ?>>Staff</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" 
                                   value="<?= old('nip', $perangkat['nip'] ?? '') ?>" 
                                   placeholder="18 digit">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="pangkat_golongan" class="form-label">Pangkat / Golongan</label>
                            <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan" 
                                   value="<?= old('pangkat_golongan', $perangkat['pangkat_golongan'] ?? '') ?>" 
                                   placeholder="Contoh: Penata / III.c">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="pendidikan" class="form-label">Pendidikan</label>
                            <input type="text" class="form-control" id="pendidikan" name="pendidikan" 
                                   value="<?= old('pendidikan', $perangkat['pendidikan'] ?? '') ?>" 
                                   placeholder="Contoh: S1 Administrasi">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" 
                                   value="<?= old('tanggal_lahir', $perangkat['tanggal_lahir'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_pengangkatan" class="form-label">Tanggal Pengangkatan</label>
                            <input type="date" class="form-control" id="tanggal_pengangkatan" name="tanggal_pengangkatan" 
                                   value="<?= old('tanggal_pengangkatan', $perangkat['tanggal_pengangkatan'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-8 mb-3">
                            <label for="no_sk" class="form-label">Nomor SK</label>
                            <input type="text" class="form-control" id="no_sk" name="no_sk" 
                                   value="<?= old('no_sk', $perangkat['no_sk'] ?? '') ?>" 
                                   placeholder="Contoh: SK.141/KEP/2019">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="urutan" class="form-label">Urutan Tampil</label>
                            <input type="number" class="form-control" id="urutan" name="urutan" 
                                   value="<?= old('urutan', $perangkat['urutan'] ?? '0') ?>" 
                                   min="0">
                            <small class="text-muted">Makin kecil makin atas</small>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="aktif" name="aktif" 
                                       value="1" <?= old('aktif', $perangkat['aktif'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="aktif">
                                    Status Aktif (Masih bertugas)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('/master/organisasi') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Info Panel -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Panduan Pengisian:</strong></p>
                <ul class="small mb-0">
                    <li>Nama dan Jabatan wajib diisi</li>
                    <li>NIP tidak wajib untuk Kepala Dusun</li>
                    <li>Urutan tampil: angka kecil = tampil di atas</li>
                    <li>Uncheck "Status Aktif" untuk perangkat yang sudah pensiun/pindah</li>
                </ul>
            </div>
        </div>
        
        <?php if (isset($perangkat)): ?>
        <div class="card mt-3">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Info Data</h6>
            </div>
            <div class="card-body">
                <p class="small mb-1"><strong>Dibuat:</strong><br><?= date('d/m/Y H:i', strtotime($perangkat['created_at'])) ?></p>
                <p class="small mb-0"><strong>Terakhir Update:</strong><br><?= date('d/m/Y H:i', strtotime($perangkat['updated_at'])) ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= view('layout/footer') ?>
