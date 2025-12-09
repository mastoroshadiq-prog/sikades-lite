<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-exclamation-triangle me-2 text-danger"></i>Monitoring Deviasi
            </h2>
            <p class="text-muted mb-0">Perbandingan Realisasi Keuangan vs Progres Fisik</p>
        </div>
        <a href="<?= base_url('/pembangunan') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Info Card -->
    <div class="card border-0 shadow-sm mb-4 bg-light">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5><i class="fas fa-info-circle text-info me-2"></i>Tentang Monitoring Deviasi</h5>
                    <p class="mb-0">
                        Sistem mendeteksi penyimpangan antara <strong>realisasi keuangan</strong> dan <strong>progres fisik</strong>.
                        Jika keuangan sudah dicairkan lebih dari 20% dibanding progres fisik, akan muncul <span class="badge bg-danger">PERINGATAN</span>.
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="row">
                        <div class="col-6">
                            <div class="bg-success text-white rounded p-2">
                                <i class="fas fa-check-circle fa-2x"></i>
                                <div class="small mt-1">Normal < 20%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-danger text-white rounded p-2">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                                <div class="small mt-1">Alert > 20%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Proyek Dalam Proses</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($projects)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4 class="text-success">Tidak Ada Proyek Dalam Proses</h4>
                    <p class="text-muted">Semua proyek sudah selesai atau masih dalam tahap rencana</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Proyek</th>
                                <th class="text-center">Fisik</th>
                                <th class="text-center">Keuangan</th>
                                <th class="text-center">Deviasi</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $p): ?>
                                <tr class="<?= $p['deviation']['is_alert'] ? 'table-danger' : '' ?>">
                                    <td>
                                        <strong><?= esc($p['nama_proyek']) ?></strong>
                                        <br><small class="text-muted"><?= esc($p['lokasi_detail'] ?: '-') ?></small>
                                    </td>
                                    <td class="text-center">
                                        <div class="h5 mb-0 text-info"><?= $p['persentase_fisik'] ?>%</div>
                                        <div class="progress mx-auto" style="height: 6px; width: 80px;">
                                            <div class="progress-bar bg-info" style="width: <?= $p['persentase_fisik'] ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="h5 mb-0 text-success"><?= number_format($p['persentase_keuangan'], 0) ?>%</div>
                                        <div class="progress mx-auto" style="height: 6px; width: 80px;">
                                            <div class="progress-bar bg-success" style="width: <?= $p['persentase_keuangan'] ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $devClass = match($p['deviation']['level']) {
                                            'danger' => 'bg-danger',
                                            'warning' => 'bg-warning text-dark',
                                            'info' => 'bg-info',
                                            default => 'bg-success'
                                        };
                                        ?>
                                        <span class="badge <?= $devClass ?> fs-6">
                                            <?= $p['deviation']['value'] > 0 ? '+' : '' ?><?= $p['deviation']['value'] ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($p['deviation']['is_alert']): ?>
                                            <div class="text-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                <small><?= $p['deviation']['message'] ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>Normal
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('/pembangunan/proyek/detail/' . $p['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
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

    <?php 
    $alertCount = count(array_filter($projects, fn($p) => $p['deviation']['is_alert']));
    if ($alertCount > 0): 
    ?>
        <!-- Recommendation Card -->
        <div class="card border-0 shadow-sm mt-4 border-start border-danger border-4">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Rekomendasi Tindakan</h5>
            </div>
            <div class="card-body">
                <p><strong>Terdapat <?= $alertCount ?> proyek dengan deviasi tinggi.</strong> Berikut langkah yang disarankan:</p>
                <ul class="mb-0">
                    <li><strong>Verifikasi Lapangan:</strong> Lakukan kunjungan langsung untuk memastikan kondisi fisik sebenarnya.</li>
                    <li><strong>Review SPP:</strong> Periksa dokumen pencairan apakah sudah sesuai dengan progres yang dilaporkan.</li>
                    <li><strong>Koordinasi TPK:</strong> Hubungi Tim Pelaksana Kegiatan untuk klarifikasi.</li>
                    <li><strong>Dokumentasi:</strong> Pastikan foto progres terbaru sudah diupload.</li>
                    <li><strong>Tindak Lanjut:</strong> Jika proyek benar-benar terhenti, pertimbangkan status MANGKRAK.</li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= view('layout/footer') ?>
