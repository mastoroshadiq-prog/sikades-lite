<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan') ?>">Perencanaan</a></li>
            <li class="breadcrumb-item active">RKP Desa</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-calendar-alt me-2 text-success"></i>RKP Desa
            </h2>
            <p class="text-muted mb-0">Rencana Kerja Pemerintah Desa (Tahunan)</p>
        </div>
        <a href="<?= base_url('/perencanaan/rkp/create') ?>" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Tambah RKP
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- RKP List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (empty($rkpList)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada RKP Desa</h5>
                <p class="text-muted">Pastikan sudah membuat RPJM Desa terlebih dahulu</p>
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($rkpList as $rkp): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm card-hover">
                        <div class="card-header bg-gradient text-white text-center py-3" 
                             style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                            <h2 class="mb-0"><?= $rkp['tahun'] ?></h2>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">
                                <i class="fas fa-tag me-2"></i>
                                <?= esc($rkp['tema'] ?? 'Belum ada tema') ?>
                            </p>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Kegiatan:</span>
                                <span class="badge bg-info"><?= $rkp['jumlah_kegiatan'] ?? 0 ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted small">Total Pagu:</span>
                                <strong>Rp <?= number_format($rkp['total_anggaran'] ?? 0, 0, ',', '.') ?></strong>
                            </div>
                            
                            <?php
                            $statusColors = [
                                'Draft' => 'secondary',
                                'Musdes' => 'info',
                                'Ditetapkan' => 'primary',
                                'Berjalan' => 'warning',
                                'Selesai' => 'success'
                            ];
                            $color = $statusColors[$rkp['status']] ?? 'secondary';
                            ?>
                            <div class="text-center mb-3">
                                <span class="badge bg-<?= $color ?> px-3 py-2"><?= $rkp['status'] ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('/perencanaan/rkp/detail/' . $rkp['id']) ?>" 
                                   class="btn btn-primary flex-fill">
                                    <i class="fas fa-eye me-1"></i>Detail
                                </a>
                                <a href="<?= base_url('/perencanaan/rkp/edit/' . $rkp['id']) ?>" 
                                   class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.card-hover {
    transition: all 0.3s ease;
}
.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>

<?= view('layout/footer') ?>
