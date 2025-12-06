<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan') ?>">Perencanaan</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan/rkp') ?>">RKP Desa</a></li>
            <li class="breadcrumb-item active">Tahun <?= $rkp['tahun'] ?></li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-calendar-alt me-2 text-success"></i>RKP Desa Tahun <?= $rkp['tahun'] ?>
            </h2>
            <p class="text-muted mb-0">
                <?= esc($rkp['tema'] ?? 'Belum ada tema') ?>
                <span class="badge bg-<?= 
                    $rkp['status'] == 'Ditetapkan' ? 'primary' : 
                    ($rkp['status'] == 'Berjalan' ? 'warning' : 
                    ($rkp['status'] == 'Selesai' ? 'success' : 'secondary')) 
                ?> ms-2"><?= $rkp['status'] ?></span>
            </p>
        </div>
        <div>
            <a href="<?= base_url('/perencanaan/kegiatan/create/' . $rkp['id']) ?>" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Tambah Kegiatan
            </a>
            <a href="<?= base_url('/perencanaan/rkp/edit/' . $rkp['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit RKP
            </a>
            <a href="<?= base_url('/perencanaan/rkp') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="text-primary mb-0"><?= array_sum(array_column($kegiatanGrouped, 'total_pagu')) > 0 ? 'Rp ' . number_format(array_sum(array_map(fn($g) => $g['total_pagu'], $kegiatanGrouped)), 0, ',', '.') : 'Rp 0' ?></h3>
                    <small class="text-muted">Total Pagu Kegiatan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="text-success mb-0"><?= array_sum(array_map(fn($g) => count($g['kegiatan']), $kegiatanGrouped)) ?></h3>
                    <small class="text-muted">Total Kegiatan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="text-info mb-0"><?= count($kegiatanGrouped) ?></h3>
                    <small class="text-muted">Bidang</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <?php 
                    $disetujui = 0;
                    foreach ($kegiatanGrouped as $g) {
                        foreach ($g['kegiatan'] as $k) {
                            if ($k['status'] == 'Disetujui') $disetujui++;
                        }
                    }
                    ?>
                    <h3 class="text-warning mb-0"><?= $disetujui ?></h3>
                    <small class="text-muted">Kegiatan Disetujui</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Kegiatan by Bidang -->
    <?php if (empty($kegiatanGrouped)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-tasks fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada kegiatan</h5>
            <p class="text-muted">Tambahkan kegiatan untuk RKP tahun ini</p>
            <a href="<?= base_url('/perencanaan/kegiatan/create/' . $rkp['id']) ?>" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Tambah Kegiatan Pertama
            </a>
        </div>
    </div>
    <?php else: ?>
    
    <?php foreach ($kegiatanGrouped as $bidangId => $group): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-folder me-2 text-primary"></i>
                <strong><?= $group['kode_bidang'] ?></strong> - <?= $group['nama_bidang'] ?>
            </h6>
            <span class="badge bg-primary">
                <?= count($group['kegiatan']) ?> kegiatan | Rp <?= number_format($group['total_pagu'], 0, ',', '.') ?>
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Kegiatan</th>
                            <th>Lokasi</th>
                            <th>Volume</th>
                            <th>Sumber Dana</th>
                            <th class="text-end">Pagu Anggaran</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($group['kegiatan'] as $idx => $kegiatan): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td>
                                <strong><?= esc($kegiatan['nama_kegiatan']) ?></strong>
                                <?php if ($kegiatan['kode_kegiatan']): ?>
                                <br><small class="text-muted"><?= esc($kegiatan['kode_kegiatan']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($kegiatan['lokasi'] ?? '-') ?></td>
                            <td><?= esc($kegiatan['volume'] ?? '-') ?> <?= esc($kegiatan['satuan'] ?? '') ?></td>
                            <td>
                                <?php
                                $danaColors = [
                                    'DDS' => 'success',
                                    'ADD' => 'info',
                                    'PAD' => 'warning',
                                    'Bantuan Keuangan' => 'primary',
                                    'Swadaya' => 'secondary',
                                    'Lainnya' => 'dark'
                                ];
                                ?>
                                <span class="badge bg-<?= $danaColors[$kegiatan['sumber_dana']] ?? 'secondary' ?>">
                                    <?= $kegiatan['sumber_dana'] ?>
                                </span>
                            </td>
                            <td class="text-end">Rp <?= number_format($kegiatan['pagu_anggaran'], 0, ',', '.') ?></td>
                            <td>
                                <?php
                                $statusColors = [
                                    'Usulan' => 'secondary',
                                    'Prioritas' => 'info',
                                    'Disetujui' => 'success',
                                    'Ditolak' => 'danger',
                                    'Berjalan' => 'warning',
                                    'Selesai' => 'dark'
                                ];
                                ?>
                                <span class="badge bg-<?= $statusColors[$kegiatan['status']] ?? 'secondary' ?>">
                                    <?= $kegiatan['status'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('/perencanaan/kegiatan/edit/' . $kegiatan['id']) ?>" 
                                       class="btn btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="confirmDelete(<?= $kegiatan['id'] ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php endif; ?>

    <!-- Summary by Sumber Dana -->
    <?php if (!empty($summaryDana)): ?>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Rekapitulasi per Sumber Dana</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Sumber Dana</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Total Pagu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($summaryDana as $sd): ?>
                            <tr>
                                <td><?= $sd['sumber_dana'] ?></td>
                                <td class="text-center"><?= $sd['jumlah'] ?></td>
                                <td class="text-end">Rp <?= number_format($sd['total'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-success"></i>Rekapitulasi per Status</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Total Pagu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($summaryStatus as $ss): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-<?= $statusColors[$ss['status']] ?? 'secondary' ?>">
                                        <?= $ss['status'] ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= $ss['jumlah'] ?></td>
                                <td class="text-end">Rp <?= number_format($ss['total'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Yakin hapus kegiatan ini?',
        text: 'Data akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('/perencanaan/kegiatan/delete/') ?>' + id, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(() => window.location.reload());
        }
    });
}
</script>

<?= view('layout/footer') ?>
