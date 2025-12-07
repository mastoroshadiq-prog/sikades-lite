<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-warehouse me-2 text-primary"></i>SIPADES
            </h2>
            <p class="text-muted mb-0">Sistem Pengelolaan Aset Desa</p>
        </div>
        <div>
            <a href="<?= base_url('/aset/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Aset
            </a>
            <a href="<?= base_url('/aset/list') ?>" class="btn btn-outline-primary ms-2">
                <i class="fas fa-list me-2"></i>Lihat Semua
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

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-boxes text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= number_format($summary['total_aset']) ?></h3>
                            <small class="text-muted">Total Aset</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-money-bill-wave text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Rp <?= number_format($summary['total_nilai'], 0, ',', '.') ?></h6>
                            <small class="text-muted">Total Nilai Aset</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= number_format($summary['by_kondisi']['Baik'] ?? 0) ?></h3>
                            <small class="text-muted">Kondisi Baik</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= number_format(($summary['by_kondisi']['Rusak Ringan'] ?? 0) + ($summary['by_kondisi']['Rusak Berat'] ?? 0)) ?></h3>
                            <small class="text-muted">Perlu Perbaikan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Aset by Kategori -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2 text-primary"></i>Aset per Kategori
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Total Nilai</th>
                                    <th width="80"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary"><?= esc($cat['kode_golongan']) ?></span>
                                        </td>
                                        <td>
                                            <strong><?= esc($cat['nama_golongan']) ?></strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill"><?= $cat['jumlah_aset'] ?? 0 ?></span>
                                        </td>
                                        <td class="text-end">
                                            <?php 
                                            $nilai = 0;
                                            if (!empty($summary['by_kategori'])) {
                                                foreach ($summary['by_kategori'] as $k) {
                                                    if (isset($k['nama_golongan']) && $k['nama_golongan'] === $cat['nama_golongan']) {
                                                        $nilai = $k['nilai'] ?? 0;
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                            Rp <?= number_format($nilai, 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('/aset/list?kategori=' . $cat['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            Belum ada kategori aset
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats - Kondisi Aset -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2 text-success"></i>Kondisi Aset
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $totalKondisi = ($summary['by_kondisi']['Baik'] ?? 0) + 
                                    ($summary['by_kondisi']['Rusak Ringan'] ?? 0) + 
                                    ($summary['by_kondisi']['Rusak Berat'] ?? 0);
                    ?>
                    <?php if ($totalKondisi > 0): ?>
                    <!-- Chart Container with fixed height -->
                    <div style="height: 180px; position: relative;">
                        <canvas id="kondisiChart"></canvas>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-chart-pie fa-3x mb-3 d-block opacity-50"></i>
                        <p>Belum ada data aset</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-circle text-success me-2"></i>Baik</span>
                            <strong><?= $summary['by_kondisi']['Baik'] ?? 0 ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-circle text-warning me-2"></i>Rusak Ringan</span>
                            <strong><?= $summary['by_kondisi']['Rusak Ringan'] ?? 0 ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-circle text-danger me-2"></i>Rusak Berat</span>
                            <strong><?= $summary['by_kondisi']['Rusak Berat'] ?? 0 ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Assets -->
    <?php if (!empty($recentAset)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-clock me-2 text-info"></i>Aset Terbaru
            </h5>
            <a href="<?= base_url('/aset/list') ?>" class="btn btn-sm btn-outline-primary">
                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Register</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Tahun</th>
                            <th>Kondisi</th>
                            <th class="text-end">Nilai</th>
                            <th width="80"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 0;
                        foreach ($recentAset as $aset): 
                            if ($count >= 5) break;
                            $count++;
                        ?>
                        <tr>
                            <td>
                                <code class="text-primary"><?= esc($aset['kode_register']) ?></code>
                            </td>
                            <td>
                                <strong><?= esc($aset['nama_barang']) ?></strong>
                                <?php if ($aset['merk_type']): ?>
                                    <br><small class="text-muted"><?= esc($aset['merk_type']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= esc($aset['nama_golongan'] ?? '-') ?></span>
                            </td>
                            <td><?= $aset['tahun_perolehan'] ?></td>
                            <td>
                                <?php
                                $kondisiClass = [
                                    'Baik' => 'success',
                                    'Rusak Ringan' => 'warning',
                                    'Rusak Berat' => 'danger',
                                ];
                                ?>
                                <span class="badge bg-<?= $kondisiClass[$aset['kondisi']] ?? 'secondary' ?>">
                                    <?= $aset['kondisi'] ?>
                                </span>
                            </td>
                            <td class="text-end">Rp <?= number_format($aset['harga_perolehan'], 0, ',', '.') ?></td>
                            <td>
                                <a href="<?= base_url('/aset/detail/' . $aset['id']) ?>" class="btn btn-sm btn-outline-info">
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
            <i class="fas fa-warehouse fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Data Aset</h5>
            <p class="text-muted mb-4">Mulai dengan menambahkan inventaris aset desa</p>
            <a href="<?= base_url('/aset/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Aset Pertama
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kondisi Chart
    const ctx = document.getElementById('kondisiChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
                datasets: [{
                    data: [
                        <?= $summary['by_kondisi']['Baik'] ?? 0 ?>,
                        <?= $summary['by_kondisi']['Rusak Ringan'] ?? 0 ?>,
                        <?= $summary['by_kondisi']['Rusak Berat'] ?? 0 ?>
                    ],
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '70%'
            }
        });
    }
});
</script>

<?= view('layout/footer') ?>
