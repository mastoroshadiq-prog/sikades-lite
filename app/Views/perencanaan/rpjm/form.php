<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan') ?>">Perencanaan</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan/rpjm') ?>">RPJM Desa</a></li>
            <li class="breadcrumb-item active"><?= isset($rpjm) ? 'Edit' : 'Tambah' ?></li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0">
                        <i class="fas fa-<?= isset($rpjm) ? 'edit' : 'plus' ?> me-2"></i>
                        <?= isset($rpjm) ? 'Edit RPJM Desa' : 'Tambah RPJM Desa Baru' ?>
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

                    <form action="<?= isset($rpjm) ? base_url('/perencanaan/rpjm/update/' . $rpjm['id']) : base_url('/perencanaan/rpjm/save') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-calendar me-2"></i>Periode RPJM</h6>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Tahun Awal <span class="text-danger">*</span></label>
                                        <select name="tahun_awal" class="form-select" required>
                                            <?php for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++): ?>
                                            <option value="<?= $y ?>" <?= (isset($rpjm) ? $rpjm['tahun_awal'] : ($tahunSekarang ?? date('Y'))) == $y ? 'selected' : '' ?>>
                                                <?= $y ?>
                                            </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Tahun Akhir <span class="text-danger">*</span></label>
                                        <select name="tahun_akhir" class="form-select" required>
                                            <?php for ($y = date('Y'); $y <= date('Y') + 10; $y++): ?>
                                            <option value="<?= $y ?>" <?= (isset($rpjm) ? $rpjm['tahun_akhir'] : ($tahunSekarang ?? date('Y')) + 5) == $y ? 'selected' : '' ?>>
                                                <?= $y ?>
                                            </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-file-alt me-2"></i>Dokumen Perdes</h6>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Nomor Perdes</label>
                                        <input type="text" name="nomor_perdes" class="form-control" 
                                               value="<?= esc($rpjm['nomor_perdes'] ?? '') ?>" placeholder="Nomor Peraturan Desa">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Tanggal Perdes</label>
                                        <input type="date" name="tanggal_perdes" class="form-control" 
                                               value="<?= $rpjm['tanggal_perdes'] ?? '' ?>">
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Draft" <?= (isset($rpjm) && $rpjm['status'] == 'Draft') ? 'selected' : '' ?>>Draft</option>
                                        <option value="Aktif" <?= (isset($rpjm) && $rpjm['status'] == 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                                        <option value="Selesai" <?= (isset($rpjm) && $rpjm['status'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-primary mb-3"><i class="fas fa-eye me-2"></i>Visi Desa <span class="text-danger">*</span></h6>
                        <div class="mb-4">
                            <textarea name="visi" class="form-control" rows="3" required 
                                      placeholder="Masukkan visi pembangunan desa..."><?= esc($rpjm['visi'] ?? '') ?></textarea>
                            <small class="text-muted">Contoh: "Terwujudnya Desa Mandiri, Sejahtera, dan Berbudaya"</small>
                        </div>

                        <h6 class="text-primary mb-3"><i class="fas fa-bullseye me-2"></i>Misi Desa</h6>
                        <div class="mb-4">
                            <textarea name="misi" class="form-control" rows="5" 
                                      placeholder="Masukkan misi pembangunan desa (satu misi per baris)..."><?= esc($rpjm['misi'] ?? '') ?></textarea>
                            <small class="text-muted">Masukkan misi, satu misi per baris</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-flag me-2"></i>Tujuan</h6>
                                <div class="mb-4">
                                    <textarea name="tujuan" class="form-control" rows="4" 
                                              placeholder="Tujuan pembangunan desa..."><?= esc($rpjm['tujuan'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-users me-2"></i>Sasaran</h6>
                                <div class="mb-4">
                                    <textarea name="sasaran" class="form-control" rows="4" 
                                              placeholder="Sasaran pembangunan desa..."><?= esc($rpjm['sasaran'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/perencanaan/rpjm') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i><?= isset($rpjm) ? 'Update' : 'Simpan' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
