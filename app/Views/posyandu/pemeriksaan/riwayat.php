<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-history me-2 text-primary"></i>Riwayat Pemeriksaan
            </h2>
            <h4 class="text-muted mb-0"><?= esc($penduduk['nama_lengkap']) ?></h4>
        </div>
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row mb-4">
        <!-- Info Balita -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-baby me-2"></i>Data Balita</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">NIK</td>
                            <td><strong><?= esc($penduduk['nik']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama</td>
                            <td><strong><?= esc($penduduk['nama_lengkap']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis Kelamin</td>
                            <td><?= $penduduk['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Lahir</td>
                            <td><?= date('d F Y', strtotime($penduduk['tanggal_lahir'])) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td><?= esc($penduduk['alamat'] ?? '-') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-success"></i>Grafik Pertumbuhan</h5>
                </div>
                <div class="card-body">
                    <canvas id="growthChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-info"></i>Riwayat Pemeriksaan</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($riwayat)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Riwayat Pemeriksaan</h5>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Posyandu</th>
                                <th>Usia</th>
                                <th>BB (kg)</th>
                                <th>TB (cm)</th>
                                <th>LK (cm)</th>
                                <th>Status Gizi</th>
                                <th>Z-Score TB/U</th>
                                <th>Stunting</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat as $r): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($r['tanggal_periksa'])) ?></td>
                                    <td><?= esc($r['nama_posyandu']) ?></td>
                                    <td><?= $r['usia_bulan'] ?> bln</td>
                                    <td><?= number_format($r['berat_badan'], 1) ?></td>
                                    <td><?= number_format($r['tinggi_badan'], 1) ?></td>
                                    <td><?= $r['lingkar_kepala'] ? number_format($r['lingkar_kepala'], 1) : '-' ?></td>
                                    <td>
                                        <?php
                                        $giziClass = match($r['status_gizi']) {
                                            'BURUK' => 'danger',
                                            'KURANG' => 'warning',
                                            'BAIK' => 'success',
                                            'LEBIH' => 'info',
                                            'OBESITAS' => 'dark',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $giziClass ?>"><?= $r['status_gizi'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($r['z_score_tb_u']): ?>
                                            <span class="badge bg-<?= $r['z_score_tb_u'] < -2 ? 'danger' : 'success' ?>">
                                                <?= number_format($r['z_score_tb_u'], 2) ?>
                                            </span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($r['indikasi_stunting']): ?>
                                            <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>YA</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">TIDAK</span>
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

<?= view('layout/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const riwayat = <?= json_encode($riwayat) ?>;

if (riwayat.length > 0) {
    const labels = riwayat.map(r => r.usia_bulan + ' bln');
    const bbData = riwayat.map(r => parseFloat(r.berat_badan));
    const tbData = riwayat.map(r => parseFloat(r.tinggi_badan));

    const ctx = document.getElementById('growthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Berat Badan (kg)',
                    data: bbData,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Tinggi Badan (cm)',
                    data: tbData,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Berat Badan (kg)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Tinggi Badan (cm)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}
</script>
