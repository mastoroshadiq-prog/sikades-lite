<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan') ?>">Perencanaan</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan/rkp') ?>">RKP Desa</a></li>
            <li class="breadcrumb-item active"><?= isset($rkp) ? 'Edit' : 'Tambah' ?></li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <h5 class="mb-0">
                        <i class="fas fa-<?= isset($rkp) ? 'edit' : 'plus' ?> me-2"></i>
                        <?= isset($rkp) ? 'Edit RKP Desa' : 'Tambah RKP Desa Baru' ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <form action="<?= isset($rkp) ? base_url('/perencanaan/rkp/update/' . $rkp['id']) : base_url('/perencanaan/rkp/save') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">RPJM Desa <span class="text-danger">*</span></label>
                                <select name="rpjmdesa_id" class="form-select" required>
                                    <option value="">-- Pilih RPJM --</option>
                                    <?php foreach ($rpjmList as $rpjm): ?>
                                    <option value="<?= $rpjm['id'] ?>" <?= (isset($rkp) && $rkp['rpjmdesa_id'] == $rpjm['id']) ? 'selected' : '' ?>>
                                        <?= $rpjm['tahun_awal'] ?> - <?= $rpjm['tahun_akhir'] ?> (<?= $rpjm['status'] ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Pilih RPJM yang menjadi induk RKP ini</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                <select name="tahun" class="form-select" required>
                                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 5; $y++): ?>
                                    <option value="<?= $y ?>" <?= (isset($rkp) ? $rkp['tahun'] : ($tahunSekarang ?? date('Y') + 1)) == $y ? 'selected' : '' ?>>
                                        <?= $y ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tema Pembangunan Tahun Ini</label>
                            <input type="text" name="tema" class="form-control" 
                                   value="<?= esc($rkp['tema'] ?? '') ?>" 
                                   placeholder="Contoh: Peningkatan Infrastruktur dan Kesejahteraan Masyarakat">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prioritas Pembangunan</label>
                            <textarea name="prioritas" class="form-control" rows="3" 
                                      placeholder="Daftar prioritas pembangunan tahun ini..."><?= esc($rkp['prioritas'] ?? '') ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Draft" <?= (isset($rkp) && $rkp['status'] == 'Draft') ? 'selected' : '' ?>>Draft</option>
                                    <option value="Musdes" <?= (isset($rkp) && $rkp['status'] == 'Musdes') ? 'selected' : '' ?>>Musdes</option>
                                    <option value="Ditetapkan" <?= (isset($rkp) && $rkp['status'] == 'Ditetapkan') ? 'selected' : '' ?>>Ditetapkan</option>
                                    <option value="Berjalan" <?= (isset($rkp) && $rkp['status'] == 'Berjalan') ? 'selected' : '' ?>>Berjalan</option>
                                    <option value="Selesai" <?= (isset($rkp) && $rkp['status'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nomor Perdes</label>
                                <input type="text" name="nomor_perdes" class="form-control" 
                                       value="<?= esc($rkp['nomor_perdes'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Perdes</label>
                                <input type="date" name="tanggal_perdes" class="form-control" 
                                       value="<?= $rkp['tanggal_perdes'] ?? '' ?>">
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/perencanaan/rkp') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i><?= isset($rkp) ? 'Update' : 'Simpan' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
