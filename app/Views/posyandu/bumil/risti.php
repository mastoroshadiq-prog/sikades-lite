<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Ibu Hamil Resiko Tinggi
            </h2>
            <p class="text-muted mb-0">Daftar ibu hamil yang memerlukan perhatian khusus</p>
        </div>
        <a href="<?= base_url('/posyandu') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <?php if (empty($ristiCases)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h4 class="text-success">Tidak Ada Ibu Hamil Resiko Tinggi</h4>
                <p class="text-muted">Semua ibu hamil dalam kondisi normal</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($ristiCases as $case): ?>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
                        <div class="card-header bg-warning bg-opacity-10 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-nurse me-2 text-warning"></i>
                                    <?= esc($case['nama_lengkap']) ?>
                                </h5>
                                <span class="badge bg-danger">RISTI</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">Hari Perkiraan Lahir</small>
                                    <strong class="text-primary">
                                        <?= date('d M Y', strtotime($case['taksiran_persalinan'])) ?>
                                    </strong>
                                </div>
                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">Usia Kandungan</small>
                                    <strong><?= $case['usia_kandungan'] ?> minggu</strong>
                                </div>
                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">Kehamilan Ke</small>
                                    <strong><?= $case['kehamilan_ke'] ?></strong>
                                </div>
                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">Posyandu</small>
                                    <strong><?= esc($case['nama_posyandu']) ?></strong>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Faktor Resiko:</small>
                                <?php 
                                $risiko = explode(', ', $case['faktor_resiko']);
                                foreach ($risiko as $r): 
                                    if (trim($r)):
                                ?>
                                    <span class="badge bg-warning text-dark me-1 mb-1"><?= esc($r) ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Pemeriksaan K1-K4:</small>
                                <span class="badge <?= $case['pemeriksaan_k1'] ? 'bg-success' : 'bg-secondary' ?> me-1">K1</span>
                                <span class="badge <?= $case['pemeriksaan_k2'] ? 'bg-success' : 'bg-secondary' ?> me-1">K2</span>
                                <span class="badge <?= $case['pemeriksaan_k3'] ? 'bg-success' : 'bg-secondary' ?> me-1">K3</span>
                                <span class="badge <?= $case['pemeriksaan_k4'] ? 'bg-success' : 'bg-secondary' ?>">K4</span>
                            </div>

                            <div class="text-muted small">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?= esc($case['dusun']) ?> - <?= esc($case['alamat']) ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="tel:<?= esc($case['no_telp'] ?? '') ?>" class="btn btn-outline-primary flex-grow-1">
                                    <i class="fas fa-phone me-2"></i>Hubungi
                                </a>
                                <a href="<?= base_url('/demografi/penduduk/detail/' . $case['penduduk_id']) ?>" 
                                   class="btn btn-outline-secondary flex-grow-1">
                                    <i class="fas fa-user me-2"></i>Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Info Card -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-info text-white py-3">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Penting</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Ibu Hamil Resiko Tinggi (RISTI)</strong> memerlukan:</p>
                <ul class="mb-0">
                    <li>Pemeriksaan lebih sering (minimal 2x per bulan)</li>
                    <li>Perencanaan persalinan di fasilitas kesehatan dengan kemampuan PONED/PONEK</li>
                    <li>Pendampingan oleh bidan/tenaga kesehatan terlatih</li>
                    <li>Kesiapan rujukan darurat (transportasi, donor darah, biaya)</li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= view('layout/footer') ?>
