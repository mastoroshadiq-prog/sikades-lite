<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-users me-2 text-primary"></i>Data Penduduk
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi') ?>">Demografi</a></li>
                    <li class="breadcrumb-item active">Data Penduduk</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('/demografi/penduduk/create') ?>" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Tambah Penduduk
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

    <!-- Search & Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small">Cari NIK / Nama / No KK</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Ketik minimal 3 karakter..." 
                               value="<?= esc($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select">
                        <option value="HIDUP" <?= ($filters['status_dasar'] ?? '') == 'HIDUP' ? 'selected' : '' ?>>Hidup</option>
                        <option value="MATI" <?= ($filters['status_dasar'] ?? '') == 'MATI' ? 'selected' : '' ?>>Meninggal</option>
                        <option value="PINDAH" <?= ($filters['status_dasar'] ?? '') == 'PINDAH' ? 'selected' : '' ?>>Pindah</option>
                        <option value="" <?= empty($filters['status_dasar']) ? 'selected' : '' ?>>Semua</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Jenis Kelamin</label>
                    <select name="gender" class="form-select">
                        <option value="">Semua</option>
                        <option value="L" <?= ($filters['jenis_kelamin'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($filters['jenis_kelamin'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Dusun</label>
                    <select name="dusun" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($dusunList as $d): ?>
                        <option value="<?= esc($d['dusun']) ?>" <?= ($filters['dusun'] ?? '') == $d['dusun'] ? 'selected' : '' ?>>
                            <?= esc($d['dusun']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?= base_url('/demografi/penduduk') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="pendudukTable">
                    <thead class="table-light">
                        <tr>
                            <th>NIK</th>
                            <th>Nama Lengkap</th>
                            <th>L/P</th>
                            <th>Umur</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pendudukList)): ?>
                            <?php foreach ($pendudukList as $p): ?>
                            <tr>
                                <td>
                                    <code class="text-primary"><?= esc($p['nik']) ?></code>
                                    <?php if ($p['is_miskin']): ?>
                                        <span class="badge bg-warning ms-1" title="DTKS/Keluarga Miskin">DTKS</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= esc($p['nama_lengkap']) ?></strong>
                                    <br><small class="text-muted">KK: <?= esc($p['no_kk']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $p['jenis_kelamin'] == 'L' ? 'info' : 'danger' ?>">
                                        <?= $p['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if ($p['tanggal_lahir']) {
                                        $birthDate = new DateTime($p['tanggal_lahir']);
                                        $today = new DateTime();
                                        echo $birthDate->diff($today)->y . ' tahun';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <small>
                                        <?= esc($p['dusun'] ?? '') ?>
                                        <?php if ($p['rt'] && $p['rw']): ?>
                                            RT <?= esc($p['rt']) ?>/RW <?= esc($p['rw']) ?>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'HIDUP' => 'success',
                                        'MATI' => 'dark',
                                        'PINDAH' => 'warning',
                                        'HILANG' => 'danger',
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusClass[$p['status_dasar']] ?? 'secondary' ?>">
                                        <?= $p['status_dasar'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('/demografi/penduduk/detail/' . $p['id']) ?>" 
                                           class="btn btn-outline-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('/demografi/penduduk/edit/' . $p['id']) ?>" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-users fa-4x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Tidak ada data</h5>
                                    <p class="text-muted">Belum ada penduduk yang sesuai filter</p>
                                    <a href="<?= base_url('/demografi/penduduk/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-user-plus me-2"></i>Tambah Penduduk
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($pendudukList)): ?>
        <div class="card-footer bg-white">
            <small class="text-muted">Menampilkan <?= count($pendudukList) ?> penduduk</small>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= view('layout/footer') ?>
