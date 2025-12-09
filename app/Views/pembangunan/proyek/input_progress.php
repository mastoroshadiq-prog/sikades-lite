<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="mb-1">
                    <i class="fas fa-chart-line me-2 text-primary"></i>Input Progres
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('/pembangunan') ?>">e-Pembangunan</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/pembangunan/proyek/detail/' . $project['id']) ?>"><?= esc($project['nama_proyek']) ?></a></li>
                        <li class="breadcrumb-item active">Input Progres</li>
                    </ol>
                </nav>
            </div>

            <!-- Current Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><?= esc($project['nama_proyek']) ?></h5>
                            <small class="text-muted"><?= esc($project['lokasi_detail']) ?></small>
                        </div>
                        <div class="text-end">
                            <div class="h3 mb-0 text-info"><?= $project['persentase_fisik'] ?>%</div>
                            <small class="text-muted">Progres saat ini</small>
                        </div>
                    </div>
                </div>
            </div>

            <form action="<?= base_url('/pembangunan/progress/save') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="proyek_id" value="<?= $project['id'] ?>">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Laporan Progres</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Laporan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_laporan" class="form-control" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pelapor</label>
                                <input type="text" name="pelapor" class="form-control" 
                                       placeholder="Nama anggota TPK" value="<?= esc($user['nama'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Persentase Fisik <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center">
                                <input type="range" name="persentase_fisik" id="progressSlider" 
                                       class="form-range flex-grow-1 me-3" 
                                       min="0" max="100" step="5" 
                                       value="<?= $project['persentase_fisik'] ?>">
                                <div class="h3 mb-0 text-primary" id="progressValue" style="min-width: 60px;">
                                    <?= $project['persentase_fisik'] ?>%
                                </div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-1">
                                <span>0%</span>
                                <span>25%</span>
                                <span>50%</span>
                                <span>75%</span>
                                <span>100%</span>
                            </div>
                        </div>

                        <?php if ($project['volume_target']): ?>
                            <div class="mb-3">
                                <label class="form-label">
                                    Volume Terealisasi 
                                    <small class="text-muted">(Target: <?= $project['volume_target'] ?> <?= $project['satuan'] ?>)</small>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="volume_terealisasi" class="form-control" 
                                           step="0.01" placeholder="0">
                                    <span class="input-group-text"><?= $project['satuan'] ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Foto Dokumentasi</label>
                            <input type="file" name="foto" class="form-control" accept="image/*" id="fotoInput">
                            <small class="text-muted">
                                <?php if ($project['persentase_fisik'] < 10): ?>
                                    📷 Foto ini akan menjadi foto 0% (kondisi awal)
                                <?php elseif ($project['persentase_fisik'] >= 45 && $project['persentase_fisik'] <= 55): ?>
                                    📷 Foto ini akan menjadi foto 50% (pertengahan)
                                <?php elseif ($project['persentase_fisik'] >= 90): ?>
                                    📷 Foto ini akan menjadi foto 100% (selesai)
                                <?php else: ?>
                                    📷 Lampirkan foto kondisi terkini
                                <?php endif; ?>
                            </small>
                            <div id="previewContainer" class="mt-2 d-none">
                                <img id="preview" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kendala (jika ada)</label>
                            <textarea name="kendala" class="form-control" rows="2" 
                                      placeholder="Jelaskan kendala yang dihadapi"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Solusi/Tindak Lanjut</label>
                            <textarea name="solusi" class="form-control" rows="2" 
                                      placeholder="Langkah penyelesaian yang diambil"></textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('/pembangunan/proyek/detail/' . $project['id']) ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-2"></i>Simpan Progres
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<script>
// Progress slider
document.getElementById('progressSlider').addEventListener('input', function() {
    document.getElementById('progressValue').textContent = this.value + '%';
});

// Photo preview
document.getElementById('fotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('previewContainer').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>
