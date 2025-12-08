<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-clinic-medical me-2 text-success"></i>Data Posyandu
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/posyandu') ?>">e-Posyandu</a></li>
                    <li class="breadcrumb-item active">Posyandu</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('/posyandu/posyandu/create') ?>" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Tambah Posyandu
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if (empty($posyanduList)): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-clinic-medical fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Belum Ada Data Posyandu</h4>
                        <p class="text-muted">Mulai dengan menambahkan posyandu pertama</p>
                        <a href="<?= base_url('/posyandu/posyandu/create') ?>" class="btn btn-success btn-lg">
                            <i class="fas fa-plus me-2"></i>Tambah Posyandu
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($posyanduList as $p): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-success text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-clinic-medical me-2"></i><?= esc($p['nama_posyandu']) ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <?= esc($p['alamat_dusun'] ?: 'Desa') ?>
                                <?php if ($p['rt'] && $p['rw']): ?>
                                    RT <?= $p['rt'] ?>/RW <?= $p['rw'] ?>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($p['ketua_posyandu']): ?>
                                <div class="mb-3">
                                    <i class="fas fa-user text-muted me-2"></i>
                                    Ketua: <?= esc($p['ketua_posyandu']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <hr>
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="h4 text-primary mb-0"><?= $p['jumlah_kader'] ?></div>
                                    <small class="text-muted">Kader</small>
                                </div>
                                <div class="col-4">
                                    <div class="h4 text-info mb-0"><?= $p['jumlah_balita'] ?></div>
                                    <small class="text-muted">Balita</small>
                                </div>
                                <div class="col-4">
                                    <div class="h4 text-warning mb-0"><?= $p['jumlah_bumil'] ?></div>
                                    <small class="text-muted">Bumil</small>
                                </div>
                            </div>
                            
                            <?php if ($p['jumlah_stunting'] > 0): ?>
                                <div class="alert alert-danger mt-3 mb-0 py-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong><?= $p['jumlah_stunting'] ?></strong> kasus stunting terdeteksi
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="<?= base_url('/posyandu/posyandu/detail/' . $p['id']) ?>" class="btn btn-outline-success w-100">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= view('layout/footer') ?>
