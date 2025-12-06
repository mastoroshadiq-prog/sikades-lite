<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-building text-primary"></i> Data Umum Desa</h2>
        <p class="text-muted mb-0">Kelola informasi desa</p>
    </div>
</div>

<!-- Form Card -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Form Data Desa</h5>
            </div>
            <div class="card-body">
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan:</h6>
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('/master/desa/save') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kode_desa" class="form-label">
                                Kode Desa <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="kode_desa" 
                                   id="kode_desa" 
                                   class="form-control" 
                                   required 
                                   placeholder="3201012001"
                                   value="<?= isset($desa) ? esc($desa['kode_desa']) : old('kode_desa') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="nama_desa" class="form-label">
                                Nama Desa <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_desa" 
                                   id="nama_desa" 
                                   class="form-control" 
                                   required 
                                   placeholder="Desa..."
                                   value="<?= isset($desa) ? esc($desa['nama_desa']) : old('nama_desa') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kecamatan" class="form-label">
                                Kecamatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="kecamatan" 
                                   id="kecamatan" 
                                   class="form-control" 
                                   required 
                                   placeholder="Kecamatan..."
                                   value="<?= isset($desa) ? esc($desa['kecamatan']) : old('kecamatan') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="kabupaten" class="form-label">
                                Kabupaten/Kota <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="kabupaten" 
                                   id="kabupaten" 
                                   class="form-control" 
                                   required 
                                   placeholder="Kabupaten/Kota..."
                                   value="<?= isset($desa) ? esc($desa['kabupaten']) : old('kabupaten') ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="provinsi" class="form-label">
                            Provinsi <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="provinsi" 
                               id="provinsi" 
                               class="form-control" 
                               required 
                               placeholder="Provinsi..."
                               value="<?= isset($desa) ? esc($desa['provinsi']) : old('provinsi') ?>">
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label for="nama_kepala_desa" class="form-label">
                            Nama Kepala Desa <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="nama_kepala_desa" 
                               id="nama_kepala_desa" 
                               class="form-control" 
                               required 
                               placeholder="Nama lengkap Kepala Desa"
                               value="<?= isset($desa) ? esc($desa['nama_kepala_desa']) : old('nama_kepala_desa') ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nip_kepala_desa" class="form-label">
                                NIP Kepala Desa
                            </label>
                            <input type="text" 
                                   name="nip_kepala_desa" 
                                   id="nip_kepala_desa" 
                                   class="form-control" 
                                   placeholder="NIP (opsional)"
                                   value="<?= isset($desa) ? esc($desa['nip_kepala_desa']) : old('nip_kepala_desa') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tahun_anggaran" class="form-label">
                                Tahun Anggaran <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   name="tahun_anggaran" 
                                   id="tahun_anggaran" 
                                   class="form-control" 
                                   required 
                                   min="2020" 
                                   max="2030"
                                   placeholder="<?= date('Y') ?>"
                                   value="<?= isset($desa) ? esc($desa['tahun_anggaran']) : old('tahun_anggaran', date('Y')) ?>">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Data Desa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Info Card -->
    <div class="col-lg-4">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
            </div>
            <div class="card-body">
                <p class="small mb-3">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Data desa digunakan untuk kop laporan dan identitas desa dalam sistem.
                </p>
                
                <p class="small mb-3">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Kode desa sesuai dengan standar Kemendagri (10 digit).
                </p>
                
                <p class="small mb-0">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Pastikan nama Kepala Desa sesuai dengan SK penetapan.
                </p>
            </div>
        </div>
        
        <?php if (isset($desa)): ?>
        <div class="card mt-3 border-success">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-database me-2"></i>Data Tersimpan</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Terakhir Update:</td>
                    </tr>
                    <tr>
                        <td><strong><?= date('d M Y H:i', strtotime($desa['updated_at'])) ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= view('layout/footer') ?>
