<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-clinic-medical me-2 text-success"></i><?= esc($posyandu['nama_posyandu']) ?>
            </h2>
            <p class="text-muted mb-0">
                <i class="fas fa-map-marker-alt me-1"></i>
                <?= esc($posyandu['alamat_dusun'] ?: 'Desa') ?>
                <?php if ($posyandu['rt'] && $posyandu['rw']): ?>
                    RT <?= $posyandu['rt'] ?>/RW <?= $posyandu['rw'] ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('/posyandu/pemeriksaan/' . $posyandu['id'] . '/create') ?>" class="btn btn-primary">
                <i class="fas fa-stethoscope me-2"></i>Input Pemeriksaan
            </a>
            <a href="<?= base_url('/posyandu/bumil/' . $posyandu['id'] . '/create') ?>" class="btn btn-info text-white">
                <i class="fas fa-user-plus me-2"></i>Tambah Bumil
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tabBalita">
                <i class="fas fa-baby me-2"></i>Pemeriksaan Balita
                <span class="badge bg-primary ms-1"><?= count($pemeriksaanList) ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabBumil">
                <i class="fas fa-user-nurse me-2"></i>Ibu Hamil
                <span class="badge bg-info ms-1"><?= count($bumpilList) ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabKader">
                <i class="fas fa-users me-2"></i>Kader
                <span class="badge bg-secondary ms-1"><?= count($kaderList) ?></span>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Tab Pemeriksaan Balita -->
        <div class="tab-pane fade show active" id="tabBalita">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <?php if (empty($pemeriksaanList)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-baby fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data pemeriksaan</h5>
                            <a href="<?= base_url('/posyandu/pemeriksaan/' . $posyandu['id'] . '/create') ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>Input Pemeriksaan
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama Balita</th>
                                        <th>Usia</th>
                                        <th>BB (kg)</th>
                                        <th>TB (cm)</th>
                                        <th>Status Gizi</th>
                                        <th>Stunting</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pemeriksaanList as $p): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($p['tanggal_periksa'])) ?></td>
                                            <td>
                                                <strong><?= esc($p['nama_lengkap']) ?></strong>
                                                <br><small class="text-muted"><?= $p['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></small>
                                            </td>
                                            <td><?= $p['usia_bulan'] ?> bulan</td>
                                            <td><?= number_format($p['berat_badan'], 1) ?></td>
                                            <td><?= number_format($p['tinggi_badan'], 1) ?></td>
                                            <td>
                                                <?php
                                                $giziClass = match($p['status_gizi']) {
                                                    'BURUK' => 'danger',
                                                    'KURANG' => 'warning',
                                                    'BAIK' => 'success',
                                                    'LEBIH' => 'info',
                                                    'OBESITAS' => 'dark',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span class="badge bg-<?= $giziClass ?>"><?= $p['status_gizi'] ?></span>
                                            </td>
                                            <td>
                                                <?php if ($p['indikasi_stunting']): ?>
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>STUNTING
                                                    </span>
                                                    <br><small class="text-danger">Z-Score: <?= number_format($p['z_score_tb_u'], 2) ?></small>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Normal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('/posyandu/pemeriksaan/riwayat/' . $p['penduduk_id']) ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Lihat Riwayat">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tab Ibu Hamil -->
        <div class="tab-pane fade" id="tabBumil">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <?php if (empty($bumpilList)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-user-nurse fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data ibu hamil</h5>
                            <a href="<?= base_url('/posyandu/bumil/' . $posyandu['id'] . '/create') ?>" class="btn btn-info text-white mt-3">
                                <i class="fas fa-plus me-2"></i>Tambah Ibu Hamil
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Ibu</th>
                                        <th>HPL</th>
                                        <th>Usia Kandungan</th>
                                        <th>Kehamilan Ke</th>
                                        <th>K1-K4</th>
                                        <th>Resiko</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bumpilList as $b): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($b['nama_lengkap']) ?></strong>
                                                <br><small class="text-muted"><?= esc($b['dusun']) ?></small>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($b['taksiran_persalinan'])) ?></td>
                                            <td><?= $b['usia_kandungan'] ?> minggu</td>
                                            <td><?= $b['kehamilan_ke'] ?></td>
                                            <td>
                                                <span class="badge <?= $b['pemeriksaan_k1'] ? 'bg-success' : 'bg-secondary' ?>">K1</span>
                                                <span class="badge <?= $b['pemeriksaan_k2'] ? 'bg-success' : 'bg-secondary' ?>">K2</span>
                                                <span class="badge <?= $b['pemeriksaan_k3'] ? 'bg-success' : 'bg-secondary' ?>">K3</span>
                                                <span class="badge <?= $b['pemeriksaan_k4'] ? 'bg-success' : 'bg-secondary' ?>">K4</span>
                                            </td>
                                            <td>
                                                <?php if ($b['resiko_tinggi']): ?>
                                                    <span class="badge bg-danger">RISTI</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Normal</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tab Kader -->
        <div class="tab-pane fade" id="tabKader">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users me-2 text-secondary"></i>Daftar Kader</h5>
                    <a href="<?= base_url('/posyandu/kader/' . $posyandu['id'] . '/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Tambah Kader
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($kaderList)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data kader</h5>
                            <a href="<?= base_url('/posyandu/kader/' . $posyandu['id'] . '/create') ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>Tambah Kader
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Kader</th>
                                        <th>Jabatan</th>
                                        <th>No. Telepon</th>
                                        <th>Status</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kaderList as $k): ?>
                                        <tr>
                                            <td><strong><?= esc($k['nama_kader']) ?></strong></td>
                                            <td><?= esc($k['jabatan'] ?: '-') ?></td>
                                            <td><?= esc($k['no_telp'] ?: '-') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $k['status'] == 'AKTIF' ? 'success' : 'secondary' ?>">
                                                    <?= $k['status'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('/posyandu/kader/edit/' . $k['id']) ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('/posyandu/kader/delete/' . $k['id']) ?>" 
                                                   class="btn btn-sm btn-outline-danger" title="Hapus"
                                                   onclick="return confirm('Yakin ingin menghapus kader ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
