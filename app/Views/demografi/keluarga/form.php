<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-<?= isset($keluarga) ? 'edit' : 'plus' ?> me-2 text-<?= isset($keluarga) ? 'warning' : 'primary' ?>"></i>
                <?= isset($keluarga) ? 'Edit' : 'Tambah' ?> Kartu Keluarga
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi') ?>">Demografi</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi/keluarga') ?>">Kartu Keluarga</a></li>
                    <li class="breadcrumb-item active"><?= isset($keluarga) ? 'Edit' : 'Tambah' ?></li>
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

    <form action="<?= isset($keluarga) ? base_url('/demografi/keluarga/update/' . $keluarga['id']) : base_url('/demografi/keluarga/save') ?>" method="POST">
        <?= csrf_field() ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Data Kartu Keluarga</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor KK <span class="text-danger">*</span></label>
                                <input type="text" name="no_kk" class="form-control form-control-lg" 
                                       value="<?= old('no_kk', $keluarga['no_kk'] ?? '') ?>" 
                                       maxlength="16" minlength="16"
                                       pattern="[0-9]{16}"
                                       placeholder="16 digit nomor KK" required>
                                <small class="text-muted">Nomor Kartu Keluarga 16 digit</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Nama Kepala Keluarga <span class="text-danger">*</span></label>
                                <input type="text" name="kepala_keluarga" class="form-control form-control-lg" 
                                       value="<?= old('kepala_keluarga', $keluarga['kepala_keluarga'] ?? '') ?>" 
                                       placeholder="Nama lengkap kepala keluarga" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2" 
                                          placeholder="Alamat lengkap"><?= old('alamat', $keluarga['alamat'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">RT</label>
                                <input type="text" name="rt" class="form-control" 
                                       value="<?= old('rt', $keluarga['rt'] ?? '') ?>" 
                                       maxlength="3" placeholder="001">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">RW</label>
                                <input type="text" name="rw" class="form-control" 
                                       value="<?= old('rw', $keluarga['rw'] ?? '') ?>" 
                                       maxlength="3" placeholder="001">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Dusun / Kampung</label>
                                <input type="text" name="dusun" class="form-control" 
                                       value="<?= old('dusun', $keluarga['dusun'] ?? '') ?>" 
                                       placeholder="Nama dusun">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" name="kode_pos" class="form-control" 
                                       value="<?= old('kode_pos', $keluarga['kode_pos'] ?? '') ?>" 
                                       maxlength="5" placeholder="12345">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Informasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Tips:</strong> Setelah membuat KK, Anda dapat menambahkan anggota keluarga melalui halaman detail KK.
                        </div>
                        
                        <?php if (isset($keluarga)): ?>
                        <div class="mb-3">
                            <small class="text-muted">Dibuat:</small>
                            <p class="mb-1"><?= date('d/m/Y H:i', strtotime($keluarga['created_at'])) ?></p>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">Diupdate:</small>
                            <p class="mb-0"><?= date('d/m/Y H:i', strtotime($keluarga['updated_at'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                    <a href="<?= base_url('/demografi/keluarga') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<?= view('layout/footer') ?>
