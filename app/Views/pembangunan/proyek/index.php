<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-list me-2 text-primary"></i>Daftar Proyek Fisik
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/pembangunan') ?>">e-Pembangunan</a></li>
                    <li class="breadcrumb-item active">Proyek</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('/pembangunan/proyek/create') ?>" class="btn btn-warning">
            <i class="fas fa-plus me-2"></i>Tambah Proyek
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('/pembangunan/proyek') ?>" 
                           class="btn btn-<?= !$status ? 'primary' : 'outline-primary' ?>">Semua</a>
                        <a href="<?= base_url('/pembangunan/proyek?status=RENCANA') ?>" 
                           class="btn btn-<?= $status == 'RENCANA' ? 'secondary' : 'outline-secondary' ?>">Rencana</a>
                        <a href="<?= base_url('/pembangunan/proyek?status=PROSES') ?>" 
                           class="btn btn-<?= $status == 'PROSES' ? 'warning' : 'outline-warning' ?>">Proses</a>
                        <a href="<?= base_url('/pembangunan/proyek?status=SELESAI') ?>" 
                           class="btn btn-<?= $status == 'SELESAI' ? 'success' : 'outline-success' ?>">Selesai</a>
                        <a href="<?= base_url('/pembangunan/proyek?status=MANGKRAK') ?>" 
                           class="btn btn-<?= $status == 'MANGKRAK' ? 'danger' : 'outline-danger' ?>">Mangkrak</a>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted"><?= count($projects) ?> proyek ditemukan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Grid -->
    <div class="row g-4">
        <?php if (empty($projects)): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-hard-hat fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Belum Ada Proyek</h4>
                        <p class="text-muted">Mulai dengan menambahkan proyek baru</p>
                        <a href="<?= base_url('/pembangunan/proyek/create') ?>" class="btn btn-warning btn-lg">
                            <i class="fas fa-plus me-2"></i>Tambah Proyek
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($projects as $p): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 <?= $p['deviation']['is_alert'] ? 'border-start border-danger border-4' : '' ?>">
                        <!-- Status Header -->
                        <?php
                        $headerClass = match($p['status']) {
                            'RENCANA' => 'bg-secondary',
                            'PROSES' => 'bg-warning',
                            'SELESAI' => 'bg-success',
                            'MANGKRAK' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                        ?>
                        <div class="card-header <?= $headerClass ?> text-white py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-white text-dark"><?= $p['status'] ?></span>
                                <?php if ($p['deviation']['is_alert']): ?>
                                    <span class="badge bg-danger" title="Deviasi tinggi">
                                        <i class="fas fa-exclamation-triangle"></i> Deviasi <?= $p['deviation']['value'] ?>%
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($p['nama_proyek']) ?></h5>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-map-marker-alt me-1"></i><?= esc($p['lokasi_detail'] ?: 'Lokasi belum ditentukan') ?>
                            </p>
                            
                            <!-- Progress Bars -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Fisik</span>
                                    <span><?= $p['persentase_fisik'] ?>%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: <?= $p['persentase_fisik'] ?>%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Keuangan</span>
                                    <span><?= number_format($p['persentase_keuangan'], 0) ?>%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?= $p['persentase_keuangan'] ?>%"></div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row text-center small">
                                <div class="col-6">
                                    <div class="text-muted">Anggaran</div>
                                    <strong>Rp <?= number_format($p['anggaran'], 0, ',', '.') ?></strong>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted">Target</div>
                                    <strong><?= $p['volume_target'] ? $p['volume_target'] . ' ' . $p['satuan'] : '-' ?></strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white border-0">
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="<?= base_url('/pembangunan/proyek/detail/' . $p['id']) ?>" 
                                   class="btn btn-outline-primary flex-grow-1">
                                    <i class="fas fa-eye me-2"></i>Detail
                                </a>
                                <?php if ($p['status'] == 'PROSES'): ?>
                                    <a href="<?= base_url('/pembangunan/progress/' . $p['id']) ?>" 
                                       class="btn btn-warning flex-grow-1">
                                        <i class="fas fa-plus me-2"></i>Progres
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= view('layout/footer') ?>
