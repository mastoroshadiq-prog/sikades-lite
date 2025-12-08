<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="mb-1">
                    <i class="fas fa-user-plus me-2 text-info"></i>Pendaftaran Ibu Hamil
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('/posyandu') ?>">e-Posyandu</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/posyandu/posyandu/detail/' . $posyandu['id']) ?>"><?= esc($posyandu['nama_posyandu']) ?></a></li>
                        <li class="breadcrumb-item active">Ibu Hamil</li>
                    </ol>
                </nav>
            </div>

            <form action="<?= base_url('/posyandu/bumil/save') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="posyandu_id" value="<?= $posyandu['id'] ?>">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Data Ibu</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Ibu <span class="text-danger">*</span></label>
                            <select name="penduduk_id" class="form-select form-select-lg" required>
                                <option value="">-- Pilih Wanita Usia Subur --</option>
                                <?php foreach ($wusList as $w): ?>
                                    <?php 
                                    $birthDate = new DateTime($w['tanggal_lahir']);
                                    $now = new DateTime();
                                    $age = $birthDate->diff($now)->y;
                                    ?>
                                    <option value="<?= $w['id'] ?>">
                                        <?= esc($w['nama_lengkap']) ?> (<?= $age ?> tahun) - <?= esc($w['dusun']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tinggi Badan (cm)</label>
                                <input type="number" name="tinggi_badan_ibu" class="form-control" 
                                       step="0.1" min="100" max="200" placeholder="Contoh: 155">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">BB Sebelum Hamil (kg)</label>
                                <input type="number" name="berat_badan_sebelum" class="form-control" 
                                       step="0.1" min="30" max="150" placeholder="Contoh: 50">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Golongan Darah</label>
                                <select name="golongan_darah" class="form-select">
                                    <option value="">Pilih</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kehamilan Ke</label>
                                <input type="number" name="kehamilan_ke" class="form-control" 
                                       min="1" max="15" value="1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Data Kehamilan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal HPHT <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_hpht" id="hpht" class="form-control" required>
                                <small class="text-muted">Hari Pertama Haid Terakhir</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pemeriksaan K1</label>
                                <input type="date" name="pemeriksaan_k1" class="form-control">
                            </div>
                        </div>

                        <div class="alert alert-info" id="hplInfo" style="display: none;">
                            <div class="row">
                                <div class="col-6">
                                    <strong>Hari Perkiraan Lahir (HPL):</strong>
                                    <div id="hplDate" class="h5 text-primary mb-0">-</div>
                                </div>
                                <div class="col-6">
                                    <strong>Usia Kandungan:</strong>
                                    <div id="usiaKandungan" class="h5 text-primary mb-0">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning py-3">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Faktor Resiko</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Centang jika ibu memiliki faktor resiko berikut:</p>
                        <div class="row">
                            <?php foreach ($faktorResiko as $idx => $faktor): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="faktor_resiko[]" value="<?= $faktor ?>" id="fr<?= $idx ?>">
                                        <label class="form-check-label" for="fr<?= $idx ?>">
                                            <?= $faktor ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" 
                              placeholder="Catatan tambahan"></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('/posyandu/posyandu/detail/' . $posyandu['id']) ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-info btn-lg text-white px-5">
                        <i class="fas fa-save me-2"></i>Simpan Data Bumil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<script>
document.getElementById('hpht').addEventListener('change', function() {
    if (!this.value) return;
    
    const hpht = new Date(this.value);
    
    // Calculate HPL (Naegele's rule: HPHT + 7 days - 3 months + 1 year)
    const hpl = new Date(hpht);
    hpl.setDate(hpl.getDate() + 7);
    hpl.setMonth(hpl.getMonth() - 3);
    hpl.setFullYear(hpl.getFullYear() + 1);
    
    // Calculate weeks pregnant
    const today = new Date();
    const diffTime = today - hpht;
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    const weeks = Math.floor(diffDays / 7);
    
    document.getElementById('hplInfo').style.display = 'block';
    document.getElementById('hplDate').textContent = hpl.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    document.getElementById('usiaKandungan').textContent = weeks + ' minggu';
});
</script>
