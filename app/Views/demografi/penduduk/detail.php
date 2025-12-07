<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-user me-2 text-primary"></i>Detail Penduduk
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi') ?>">Demografi</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi/penduduk') ?>">Data Penduduk</a></li>
                    <li class="breadcrumb-item active"><?= esc($penduduk['nama_lengkap']) ?></li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('/demografi/penduduk/edit/' . $penduduk['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <?php if ($penduduk['status_dasar'] == 'HIDUP'): ?>
            <div class="btn-group ms-2">
                <button type="button" class="btn btn-outline-danger dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-exchange-alt me-1"></i>Mutasi
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= base_url('/demografi/mutasi/kematian/' . $penduduk['id']) ?>">
                            <i class="fas fa-cross me-2 text-danger"></i>Catat Kematian
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('/demografi/mutasi/pindah/' . $penduduk['id']) ?>">
                            <i class="fas fa-truck-moving me-2 text-warning"></i>Catat Pindah
                        </a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
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
        <!-- Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <div class="avatar-placeholder mx-auto mb-3" style="width: 100px; height: 100px; background: linear-gradient(135deg, <?= $penduduk['jenis_kelamin'] == 'L' ? '#667eea, #764ba2' : '#f093fb, #f5576c' ?>); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-<?= $penduduk['jenis_kelamin'] == 'L' ? 'male' : 'female' ?> fa-3x text-white"></i>
                    </div>
                    <h4 class="mb-1"><?= esc($penduduk['nama_lengkap']) ?></h4>
                    <p class="text-muted mb-2"><?= esc($penduduk['nik']) ?></p>
                    
                    <?php
                    $statusClass = [
                        'HIDUP' => 'success',
                        'MATI' => 'dark',
                        'PINDAH' => 'warning',
                        'HILANG' => 'danger',
                    ];
                    ?>
                    <span class="badge bg-<?= $statusClass[$penduduk['status_dasar']] ?? 'secondary' ?> fs-6 mb-3">
                        <?= $penduduk['status_dasar'] ?>
                    </span>
                    
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <?php if ($penduduk['is_miskin']): ?>
                        <span class="badge bg-warning">DTKS</span>
                        <?php endif; ?>
                        <?php if ($penduduk['is_disabilitas']): ?>
                        <span class="badge bg-info">Disabilitas</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Umur</span>
                        <strong><?= $penduduk['umur'] ?? '-' ?> tahun</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Jenis Kelamin</span>
                        <strong><?= $penduduk['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Gol. Darah</span>
                        <strong><?= esc($penduduk['golongan_darah'] ?? '-') ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detail Information -->
        <div class="col-lg-8 mb-4">
            <!-- Identitas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Data Identitas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">NIK</label>
                            <p class="mb-0"><code class="fs-5"><?= esc($penduduk['nik']) ?></code></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">No. Kartu Keluarga</label>
                            <p class="mb-0">
                                <a href="<?= base_url('/demografi/keluarga/detail/' . $penduduk['keluarga_id']) ?>">
                                    <code class="fs-5"><?= esc($penduduk['no_kk']) ?></code>
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tempat, Tanggal Lahir</label>
                            <p class="mb-0">
                                <?= esc($penduduk['tempat_lahir'] ?? '-') ?>, 
                                <?= $penduduk['tanggal_lahir'] ? date('d F Y', strtotime($penduduk['tanggal_lahir'])) : '-' ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Agama</label>
                            <p class="mb-0"><?= esc($penduduk['agama'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status Perkawinan</label>
                            <p class="mb-0"><?= esc($penduduk['status_perkawinan'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status dalam Keluarga</label>
                            <p class="mb-0"><?= esc($penduduk['status_hubungan'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alamat -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Alamat</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><?= esc($penduduk['alamat'] ?? '-') ?></p>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="text-muted small">RT</label>
                            <p class="mb-0"><?= esc($penduduk['rt'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">RW</label>
                            <p class="mb-0"><?= esc($penduduk['rw'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Dusun</label>
                            <p class="mb-0"><?= esc($penduduk['dusun'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Kode Pos</label>
                            <p class="mb-0"><?= esc($penduduk['kode_pos'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pendidikan & Pekerjaan -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-briefcase me-2 text-info"></i>Pendidikan & Pekerjaan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Pendidikan Terakhir</label>
                            <p class="mb-0"><?= esc($penduduk['pendidikan_terakhir'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Pekerjaan</label>
                            <p class="mb-0"><?= esc($penduduk['pekerjaan'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Kewarganegaraan</label>
                            <p class="mb-0"><?= esc($penduduk['kewarganegaraan'] ?? 'WNI') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Orang Tua -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user-friends me-2 text-success"></i>Data Orang Tua</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small">Nama Ayah</label>
                            <p class="mb-0"><?= esc($penduduk['nama_ayah'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Nama Ibu</label>
                            <p class="mb-0"><?= esc($penduduk['nama_ibu'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Riwayat Mutasi -->
            <?php if (!empty($mutasiHistory)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-warning"></i>Riwayat Mutasi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mutasiHistory as $m): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($m['tanggal_peristiwa'])) ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = [
                                            'KELAHIRAN' => 'success',
                                            'KEMATIAN' => 'danger',
                                            'PINDAH_MASUK' => 'info',
                                            'PINDAH_KELUAR' => 'warning',
                                            'PERUBAHAN_DATA' => 'secondary',
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $badgeClass[$m['jenis_mutasi']] ?? 'secondary' ?>">
                                            <?= str_replace('_', ' ', $m['jenis_mutasi']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($m['keterangan'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row">
        <div class="col-12">
            <a href="<?= base_url('/demografi/penduduk') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
            <a href="<?= base_url('/demografi/keluarga/detail/' . $penduduk['keluarga_id']) ?>" class="btn btn-outline-primary ms-2">
                <i class="fas fa-home me-2"></i>Lihat KK
            </a>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
