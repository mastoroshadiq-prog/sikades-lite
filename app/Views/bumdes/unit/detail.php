<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-building me-2 text-success"></i><?= esc($unit['nama_unit']) ?>
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes') ?>">BUMDes</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes/unit') ?>">Unit</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('/bumdes/jurnal/' . $unit['id'] . '/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Jurnal
            </a>
            <a href="<?= base_url('/bumdes/unit/edit/' . $unit['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Unit Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Unit</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Nama Unit</td>
                            <td><strong><?= esc($unit['nama_unit']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis Usaha</td>
                            <td><?= esc($unit['jenis_usaha'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Penanggung Jawab</td>
                            <td><?= esc($unit['penanggung_jawab'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Modal Awal</td>
                            <td>Rp <?= number_format($unit['modal_awal'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Mulai</td>
                            <td><?= $unit['tanggal_mulai'] ? date('d/m/Y', strtotime($unit['tanggal_mulai'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                <span class="badge bg-<?= $unit['status'] === 'AKTIF' ? 'success' : 'secondary' ?>">
                                    <?= $unit['status'] ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">Laporan Keuangan</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= base_url('/bumdes/jurnal/' . $unit['id']) ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-book me-2 text-primary"></i>Jurnal Umum
                    </a>
                    <a href="<?= base_url('/bumdes/laporan/laba-rugi/' . $unit['id']) ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-line me-2 text-success"></i>Laporan Laba Rugi
                    </a>
                    <a href="<?= base_url('/bumdes/laporan/neraca/' . $unit['id']) ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-balance-scale me-2 text-info"></i>Neraca
                    </a>
                    <a href="<?= base_url('/bumdes/laporan/neraca-saldo/' . $unit['id']) ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-list-ol me-2 text-warning"></i>Neraca Saldo
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Jurnal -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Jurnal Terbaru - <?= $tahun ?></h5>
                    <a href="<?= base_url('/bumdes/jurnal/' . $unit['id']) ?>" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No Bukti</th>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($jurnalList)): ?>
                                    <?php foreach (array_slice($jurnalList, 0, 10) as $j): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                                        <td><code><?= esc($j['no_bukti']) ?></code></td>
                                        <td><?= esc($j['deskripsi']) ?></td>
                                        <td class="text-end">Rp <?= number_format($j['total'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Belum ada jurnal
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
