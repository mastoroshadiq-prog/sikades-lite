<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan') ?>">Perencanaan</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan/rpjm') ?>">RPJM Desa</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-map me-2 text-primary"></i>RPJM Desa <?= $rpjm['tahun_awal'] ?> - <?= $rpjm['tahun_akhir'] ?>
            </h2>
            <p class="text-muted mb-0">
                <span class="badge bg-<?= $rpjm['status'] == 'Aktif' ? 'success' : ($rpjm['status'] == 'Draft' ? 'secondary' : 'dark') ?>">
                    <?= $rpjm['status'] ?>
                </span>
                <?php if ($rpjm['nomor_perdes']): ?>
                <span class="ms-2">Perdes: <?= esc($rpjm['nomor_perdes']) ?></span>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <a href="<?= base_url('/perencanaan/rpjm/edit/' . $rpjm['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="<?= base_url('/perencanaan/rpjm') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Visi Misi Card -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Visi</h6>
                </div>
                <div class="card-body">
                    <p class="lead"><?= nl2br(esc($rpjm['visi'])) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-bullseye me-2"></i>Misi</h6>
                </div>
                <div class="card-body">
                    <?php 
                    $misiList = explode("\n", $rpjm['misi'] ?? '');
                    ?>
                    <ol class="mb-0">
                        <?php foreach ($misiList as $misi): ?>
                            <?php if (trim($misi)): ?>
                            <li><?= esc(trim($misi)) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-flag me-2"></i>Tujuan</h6>
                </div>
                <div class="card-body">
                    <p><?= nl2br(esc($rpjm['tujuan'] ?? '-')) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Sasaran</h6>
                </div>
                <div class="card-body">
                    <p><?= nl2br(esc($rpjm['sasaran'] ?? '-')) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- RKP List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-success"></i>RKP Desa dalam RPJM ini</h5>
            <a href="<?= base_url('/perencanaan/rkp/create?rpjm=' . $rpjm['id']) ?>" class="btn btn-sm btn-success">
                <i class="fas fa-plus me-2"></i>Tambah RKP
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($rkpList)): ?>
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada RKP untuk RPJM ini</p>
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($rkpList as $rkp): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h2 class="text-primary mb-2"><?= $rkp['tahun'] ?></h2>
                            <p class="text-muted small"><?= esc($rkp['tema'] ?? 'Belum ada tema') ?></p>
                            <span class="badge bg-<?= 
                                $rkp['status'] == 'Ditetapkan' ? 'primary' : 
                                ($rkp['status'] == 'Berjalan' ? 'warning' : 
                                ($rkp['status'] == 'Selesai' ? 'success' : 'secondary')) 
                            ?>"><?= $rkp['status'] ?></span>
                            <hr>
                            <a href="<?= base_url('/perencanaan/rkp/detail/' . $rkp['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
