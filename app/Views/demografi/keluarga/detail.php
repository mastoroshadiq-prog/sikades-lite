<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-home me-2 text-primary"></i>Detail Kartu Keluarga
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi') ?>">Demografi</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi/keluarga') ?>">Kartu Keluarga</a></li>
                    <li class="breadcrumb-item active"><?= esc($keluarga['no_kk']) ?></li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('/demografi/keluarga/edit/' . $keluarga['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit KK
            </a>
            <a href="<?= base_url('/demografi/penduduk/create/' . $keluarga['id']) ?>" class="btn btn-success ms-2">
                <i class="fas fa-user-plus me-2"></i>Tambah Anggota
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

    <div class="row">
        <!-- KK Info Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient text-white py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>No. KK</h5>
                </div>
                <div class="card-body">
                    <h3 class="text-primary mb-4"><?= esc($keluarga['no_kk']) ?></h3>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Kepala Keluarga</label>
                        <p class="fw-bold mb-0"><?= esc($keluarga['kepala_keluarga']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Alamat</label>
                        <p class="mb-0"><?= esc($keluarga['alamat'] ?? '-') ?></p>
                        <?php if ($keluarga['rt'] || $keluarga['rw']): ?>
                            <small class="text-muted">RT <?= esc($keluarga['rt']) ?> / RW <?= esc($keluarga['rw']) ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <label class="text-muted small">Dusun</label>
                            <p class="mb-0"><?= esc($keluarga['dusun'] ?? '-') ?></p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Kode Pos</label>
                            <p class="mb-0"><?= esc($keluarga['kode_pos'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Jumlah Anggota</span>
                        <span class="badge bg-primary fs-6"><?= count($keluarga['anggota'] ?? []) ?> jiwa</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Anggota Keluarga -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users me-2 text-success"></i>Anggota Keluarga</h5>
                    <a href="<?= base_url('/demografi/penduduk/create/' . $keluarga['id']) ?>" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i>Tambah
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($keluarga['anggota'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>L/P</th>
                                    <th>Hubungan</th>
                                    <th>Umur</th>
                                    <th width="100" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($keluarga['anggota'] as $anggota): ?>
                                <tr>
                                    <td><code><?= esc($anggota['nik']) ?></code></td>
                                    <td>
                                        <strong><?= esc($anggota['nama_lengkap']) ?></strong>
                                        <?php if ($anggota['is_miskin']): ?>
                                            <span class="badge bg-warning ms-1" title="DTKS">DTKS</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $anggota['jenis_kelamin'] == 'L' ? 'info' : 'danger' ?>">
                                            <?= $anggota['jenis_kelamin'] ?>
                                        </span>
                                    </td>
                                    <td><?= esc($anggota['status_hubungan'] ?? '-') ?></td>
                                    <td>
                                        <?php 
                                        if ($anggota['tanggal_lahir']) {
                                            $birthDate = new DateTime($anggota['tanggal_lahir']);
                                            $today = new DateTime();
                                            echo $birthDate->diff($today)->y . ' th';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('/demografi/penduduk/detail/' . $anggota['id']) ?>" 
                                               class="btn btn-outline-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('/demografi/penduduk/edit/' . $anggota['id']) ?>" 
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-users fa-4x mb-3 d-block opacity-50"></i>
                        <h5>Belum Ada Anggota</h5>
                        <p>Kartu Keluarga ini belum memiliki anggota terdaftar</p>
                        <a href="<?= base_url('/demografi/penduduk/create/' . $keluarga['id']) ?>" class="btn btn-success">
                            <i class="fas fa-user-plus me-2"></i>Tambah Anggota Pertama
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row">
        <div class="col-12">
            <a href="<?= base_url('/demografi/keluarga') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar KK
            </a>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
