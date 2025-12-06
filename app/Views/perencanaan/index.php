<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-project-diagram me-2 text-primary"></i>Modul Perencanaan
            </h2>
            <p class="text-muted mb-0">Perencanaan Pembangunan Desa (RPJMDesa & RKPDesa)</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-map text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= $totalRpjm ?></h3>
                            <small class="text-muted">RPJM Desa</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-alt text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= $totalRkp ?></h3>
                            <small class="text-muted">RKP Desa</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-tasks text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= $totalKegiatan ?></h3>
                            <small class="text-muted">Total Kegiatan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-money-bill-wave text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Rp <?= number_format($totalPagu, 0, ',', '.') ?></h6>
                            <small class="text-muted">Total Pagu Indikatif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <a href="<?= base_url('/perencanaan/rpjm') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-map fa-3x text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-1">RPJM Desa</h5>
                                <p class="text-muted mb-0">Rencana Pembangunan Jangka Menengah (6 Tahun)</p>
                                <small class="text-muted">Visi, Misi, Tujuan, dan Sasaran Pembangunan</small>
                            </div>
                            <div class="ms-auto">
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 mb-3">
            <a href="<?= base_url('/perencanaan/rkp') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-alt fa-3x text-success"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-1">RKP Desa</h5>
                                <p class="text-muted mb-0">Rencana Kerja Pemerintah Desa (Tahunan)</p>
                                <small class="text-muted">Daftar Kegiatan dan Pagu Anggaran Tahunan</small>
                            </div>
                            <div class="ms-auto">
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <?php if ($rpjmAktif): ?>
    <!-- Active RPJM -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h5 class="mb-0">
                <i class="fas fa-star me-2"></i>RPJM Desa Aktif: <?= $rpjmAktif['tahun_awal'] ?> - <?= $rpjmAktif['tahun_akhir'] ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fas fa-eye me-2"></i>Visi:</h6>
                    <p class="ms-4"><?= nl2br(esc($rpjmAktif['visi'])) ?></p>
                    
                    <h6 class="text-primary mt-3"><i class="fas fa-bullseye me-2"></i>Misi:</h6>
                    <p class="ms-4"><?= nl2br(esc($rpjmAktif['misi'] ?? '-')) ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fas fa-flag me-2"></i>Tujuan:</h6>
                    <p class="ms-4"><?= nl2br(esc($rpjmAktif['tujuan'] ?? '-')) ?></p>
                    
                    <h6 class="text-primary mt-3"><i class="fas fa-users me-2"></i>Sasaran:</h6>
                    <p class="ms-4"><?= nl2br(esc($rpjmAktif['sasaran'] ?? '-')) ?></p>
                </div>
            </div>
            <?php if ($rpjmAktif['nomor_perdes']): ?>
            <hr>
            <small class="text-muted">
                <i class="fas fa-file-alt me-1"></i>Perdes: <?= esc($rpjmAktif['nomor_perdes']) ?>
                <?php if ($rpjmAktif['tanggal_perdes']): ?>
                 | Tanggal: <?= date('d/m/Y', strtotime($rpjmAktif['tanggal_perdes'])) ?>
                <?php endif; ?>
            </small>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- RKP List -->
    <?php if (!empty($rkpList)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-success"></i>Daftar RKP Desa</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tahun</th>
                            <th>Tema</th>
                            <th>Jumlah Kegiatan</th>
                            <th>Total Pagu</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rkpList as $rkp): ?>
                        <tr>
                            <td>
                                <strong class="text-primary"><?= $rkp['tahun'] ?></strong>
                            </td>
                            <td><?= esc($rkp['tema'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-info"><?= $rkp['jumlah_kegiatan'] ?? 0 ?> kegiatan</span>
                            </td>
                            <td>Rp <?= number_format($rkp['total_anggaran'] ?? 0, 0, ',', '.') ?></td>
                            <td>
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
                                <span class="badge bg-<?= $color ?>"><?= $rkp['status'] ?></span>
                            </td>
                            <td>
                                <a href="<?= base_url('/perencanaan/rkp/detail/' . $rkp['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada data perencanaan</h5>
            <p class="text-muted">Mulai dengan membuat RPJM Desa terlebih dahulu</p>
            <a href="<?= base_url('/perencanaan/rpjm/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Buat RPJM Desa
            </a>
        </div>
    </div>
    <?php endif; ?>
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
