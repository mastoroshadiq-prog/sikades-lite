<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-home me-2 text-primary"></i>Data Kartu Keluarga
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi') ?>">Demografi</a></li>
                    <li class="breadcrumb-item active">Kartu Keluarga</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('/demografi/keluarga/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah KK
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari No KK atau Nama Kepala Keluarga..." 
                               value="<?= esc($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Cari
                    </button>
                    <a href="<?= base_url('/demografi/keluarga') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No KK</th>
                            <th>Kepala Keluarga</th>
                            <th>Alamat</th>
                            <th>Dusun</th>
                            <th class="text-center">Anggota</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($keluargaList)): ?>
                            <?php foreach ($keluargaList as $kk): ?>
                            <tr>
                                <td>
                                    <code class="text-primary fw-bold"><?= esc($kk['no_kk']) ?></code>
                                </td>
                                <td>
                                    <strong><?= esc($kk['kepala_keluarga']) ?></strong>
                                </td>
                                <td>
                                    <?= esc($kk['alamat'] ?? '-') ?>
                                    <?php if ($kk['rt'] || $kk['rw']): ?>
                                        <br><small class="text-muted">RT <?= esc($kk['rt']) ?> / RW <?= esc($kk['rw']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= esc($kk['dusun'] ?? '-') ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $kk['jumlah_anggota'] ?? 0 ?> jiwa</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('/demografi/keluarga/detail/' . $kk['id']) ?>" 
                                           class="btn btn-outline-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('/demografi/keluarga/edit/' . $kk['id']) ?>" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('/demografi/penduduk/create/' . $kk['id']) ?>" 
                                           class="btn btn-outline-success" title="Tambah Anggota">
                                            <i class="fas fa-user-plus"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Tidak ada data</h5>
                                    <p class="text-muted">Belum ada Kartu Keluarga terdaftar</p>
                                    <a href="<?= base_url('/demografi/keluarga/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah KK Baru
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($keluargaList)): ?>
        <div class="card-footer bg-white">
            <small class="text-muted">Total: <?= count($keluargaList) ?> Kartu Keluarga</small>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= view('layout/footer') ?>
