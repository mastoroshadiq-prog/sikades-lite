<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-building me-2 text-success"></i>Unit Usaha BUMDes
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes') ?>">BUMDes</a></li>
                    <li class="breadcrumb-item active">Unit Usaha</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('/bumdes/unit/create') ?>" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Tambah Unit
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Unit</th>
                            <th>Jenis Usaha</th>
                            <th>Penanggung Jawab</th>
                            <th>Modal Awal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($units)): ?>
                            <?php foreach ($units as $u): ?>
                            <tr>
                                <td><strong><?= esc($u['nama_unit']) ?></strong></td>
                                <td><?= esc($u['jenis_usaha'] ?? '-') ?></td>
                                <td><?= esc($u['penanggung_jawab'] ?? '-') ?></td>
                                <td>Rp <?= number_format($u['modal_awal'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-<?= $u['status'] === 'AKTIF' ? 'success' : 'secondary' ?>">
                                        <?= $u['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('/bumdes/unit/detail/' . $u['id']) ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('/bumdes/unit/edit/' . $u['id']) ?>" class="btn btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('/bumdes/jurnal/' . $u['id']) ?>" class="btn btn-outline-success">
                                            <i class="fas fa-book"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-store fa-4x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Belum Ada Unit Usaha</h5>
                                    <a href="<?= base_url('/bumdes/unit/create') ?>" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Tambah Unit Usaha
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
