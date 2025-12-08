<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-<?= isset($unit) ? 'edit' : 'plus' ?> me-2 text-success"></i>
                <?= isset($unit) ? 'Edit' : 'Tambah' ?> Unit Usaha
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes') ?>">BUMDes</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes/unit') ?>">Unit Usaha</a></li>
                    <li class="breadcrumb-item active"><?= isset($unit) ? 'Edit' : 'Tambah' ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Data Unit Usaha</h5>
                </div>
                <div class="card-body">
                    <form action="<?= isset($unit) ? base_url('/bumdes/unit/update/' . $unit['id']) : base_url('/bumdes/unit/save') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Unit Usaha <span class="text-danger">*</span></label>
                                <input type="text" name="nama_unit" class="form-control" 
                                       value="<?= old('nama_unit', $unit['nama_unit'] ?? '') ?>" required
                                       placeholder="Contoh: Unit Toko, Unit Wisata">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Jenis Usaha</label>
                                <input type="text" name="jenis_usaha" class="form-control"
                                       value="<?= old('jenis_usaha', $unit['jenis_usaha'] ?? '') ?>"
                                       placeholder="Contoh: Perdagangan, Jasa, Produksi">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Penanggung Jawab</label>
                                <input type="text" name="penanggung_jawab" class="form-control"
                                       value="<?= old('penanggung_jawab', $unit['penanggung_jawab'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="no_telp" class="form-control"
                                       value="<?= old('no_telp', $unit['no_telp'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Modal Awal (Rp)</label>
                                <input type="text" name="modal_awal" class="form-control rupiah-input"
                                       value="<?= old('modal_awal', number_format($unit['modal_awal'] ?? 0, 0, '', '.')) ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control"
                                       value="<?= old('tanggal_mulai', $unit['tanggal_mulai'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="AKTIF" <?= ($unit['status'] ?? 'AKTIF') == 'AKTIF' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="TIDAK_AKTIF" <?= ($unit['status'] ?? '') == 'TIDAK_AKTIF' ? 'selected' : '' ?>>Tidak Aktif</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2"><?= old('alamat', $unit['alamat'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/bumdes/unit') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
