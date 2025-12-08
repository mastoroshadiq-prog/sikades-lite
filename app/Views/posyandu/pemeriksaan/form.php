<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="mb-1">
                    <i class="fas fa-stethoscope me-2 text-primary"></i>Input Pemeriksaan Balita
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('/posyandu') ?>">e-Posyandu</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/posyandu/posyandu/detail/' . $posyandu['id']) ?>"><?= esc($posyandu['nama_posyandu']) ?></a></li>
                        <li class="breadcrumb-item active">Pemeriksaan</li>
                    </ol>
                </nav>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('/posyandu/pemeriksaan/save') ?>" method="POST" id="pemeriksaanForm">
                <?= csrf_field() ?>
                <input type="hidden" name="posyandu_id" value="<?= $posyandu['id'] ?>">

                <div class="row">
                    <div class="col-lg-6">
                        <!-- Pilih Balita -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0"><i class="fas fa-baby me-2"></i>Data Balita</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Pilih Balita <span class="text-danger">*</span></label>
                                    <select name="penduduk_id" id="penduduk_id" class="form-select form-select-lg" required>
                                        <option value="">-- Pilih Balita --</option>
                                        <?php foreach ($balitaList as $b): ?>
                                            <?php 
                                            $birthDate = new DateTime($b['tanggal_lahir']);
                                            $now = new DateTime();
                                            $diff = $birthDate->diff($now);
                                            $months = ($diff->y * 12) + $diff->m;
                                            ?>
                                            <option value="<?= $b['id'] ?>" 
                                                    data-jk="<?= $b['jenis_kelamin'] ?>"
                                                    data-lahir="<?= $b['tanggal_lahir'] ?>"
                                                    data-usia="<?= $months ?>">
                                                <?= esc($b['nama_lengkap']) ?> 
                                                (<?= $b['jenis_kelamin'] == 'L' ? 'L' : 'P' ?>, <?= $months ?> bulan)
                                                - <?= esc($b['dusun']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div id="balitaInfo" class="alert alert-info d-none">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Jenis Kelamin</small>
                                            <div id="infoJK" class="fw-bold">-</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Usia</small>
                                            <div id="infoUsia" class="fw-bold">-</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tanggal Pemeriksaan <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_periksa" class="form-control" 
                                           value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Pengukuran -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="fas fa-ruler me-2 text-success"></i>Hasil Pengukuran</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Berat Badan (kg) <span class="text-danger">*</span></label>
                                        <input type="number" name="berat_badan" id="berat_badan" 
                                               class="form-control form-control-lg" step="0.1" min="1" max="50" required
                                               placeholder="0.0">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Tinggi Badan (cm) <span class="text-danger">*</span></label>
                                        <input type="number" name="tinggi_badan" id="tinggi_badan" 
                                               class="form-control form-control-lg" step="0.1" min="30" max="150" required
                                               placeholder="0.0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Lingkar Kepala (cm)</label>
                                        <input type="number" name="lingkar_kepala" class="form-control" 
                                               step="0.1" min="20" max="60" placeholder="0.0">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Lingkar Lengan (cm)</label>
                                        <input type="number" name="lingkar_lengan" class="form-control" 
                                               step="0.1" min="5" max="30" placeholder="0.0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <!-- Hasil Kalkulasi Otomatis -->
                        <div class="card border-0 shadow-sm mb-4" id="resultCard">
                            <div class="card-header bg-dark text-white py-3">
                                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Hasil Analisis (Otomatis)</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-secondary text-center" id="resultPlaceholder">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Pilih balita dan masukkan hasil pengukuran untuk melihat analisis
                                </div>
                                
                                <div id="resultContent" class="d-none">
                                    <div class="row text-center mb-4">
                                        <div class="col-6">
                                            <div class="p-3 rounded" id="stuntingBox">
                                                <h6 class="text-muted mb-2">Status Stunting</h6>
                                                <div id="stuntingResult" class="h4 mb-0">-</div>
                                                <small id="stuntingZScore" class="text-muted">Z-Score: -</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 rounded" id="giziBox">
                                                <h6 class="text-muted mb-2">Status Gizi</h6>
                                                <div id="giziResult" class="h4 mb-0">-</div>
                                                <small id="giziZScore" class="text-muted">Z-Score: -</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-warning d-none" id="stuntingAlert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>PERHATIAN!</strong> Anak terindikasi <span id="stuntingKategori"></span>.
                                        Segera konsultasikan ke Puskesmas untuk penanganan lebih lanjut.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Tambahan -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2 text-info"></i>Data Tambahan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="vitamin_a" id="vitaminA">
                                            <label class="form-check-label" for="vitaminA">
                                                <i class="fas fa-capsules text-warning me-1"></i>Vitamin A
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="asi_eksklusif" id="asiEksklusif">
                                            <label class="form-check-label" for="asiEksklusif">
                                                <i class="fas fa-baby-carriage text-success me-1"></i>ASI Eksklusif
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Imunisasi</label>
                                    <input type="text" name="imunisasi" class="form-control" 
                                           placeholder="Contoh: BCG, Polio 1, DPT 1">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="2"
                                              placeholder="Catatan tambahan"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/posyandu/posyandu/detail/' . $posyandu['id']) ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-save me-2"></i>Simpan Pemeriksaan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<script>
// Simplified WHO standards for client-side preview (actual calculation done on server)
const whoStandards = {
    L: {
        TB_U: {
            0: {median: 49.9, sd: 1.9}, 6: {median: 67.6, sd: 2.1}, 12: {median: 75.7, sd: 2.4},
            18: {median: 82.3, sd: 2.6}, 24: {median: 87.8, sd: 2.8}, 36: {median: 96.1, sd: 3.2},
            48: {median: 102.9, sd: 3.5}, 60: {median: 109.4, sd: 3.7}
        },
        BB_U: {
            0: {median: 3.3, sd: 0.4}, 6: {median: 7.9, sd: 0.7}, 12: {median: 9.6, sd: 0.9},
            18: {median: 10.9, sd: 1.1}, 24: {median: 12.2, sd: 1.1}, 36: {median: 14.3, sd: 1.3},
            48: {median: 16.3, sd: 1.7}, 60: {median: 18.3, sd: 1.9}
        }
    },
    P: {
        TB_U: {
            0: {median: 49.1, sd: 1.9}, 6: {median: 65.7, sd: 2.1}, 12: {median: 74.0, sd: 2.5},
            18: {median: 80.7, sd: 2.9}, 24: {median: 86.4, sd: 3.0}, 36: {median: 95.1, sd: 3.4},
            48: {median: 102.7, sd: 4.0}, 60: {median: 109.4, sd: 4.4}
        },
        BB_U: {
            0: {median: 3.2, sd: 0.4}, 6: {median: 7.3, sd: 0.8}, 12: {median: 8.9, sd: 0.9},
            18: {median: 10.2, sd: 1.1}, 24: {median: 11.5, sd: 1.2}, 36: {median: 13.9, sd: 1.5},
            48: {median: 16.1, sd: 1.8}, 60: {median: 18.2, sd: 2.1}
        }
    }
};

function getClosestAge(age, standards) {
    const ages = Object.keys(standards).map(Number).sort((a, b) => a - b);
    let closest = ages[0];
    for (let a of ages) {
        if (a <= age) closest = a;
    }
    return closest;
}

function calculateZScore(value, median, sd) {
    return (value - median) / sd;
}

function updateResults() {
    const select = document.getElementById('penduduk_id');
    const bb = parseFloat(document.getElementById('berat_badan').value);
    const tb = parseFloat(document.getElementById('tinggi_badan').value);
    
    if (!select.value || !bb || !tb) {
        document.getElementById('resultPlaceholder').classList.remove('d-none');
        document.getElementById('resultContent').classList.add('d-none');
        return;
    }
    
    const option = select.options[select.selectedIndex];
    const jk = option.dataset.jk;
    const usia = parseInt(option.dataset.usia);
    
    if (!whoStandards[jk]) return;
    
    // Get standards
    const tbuAge = getClosestAge(usia, whoStandards[jk].TB_U);
    const bbuAge = getClosestAge(usia, whoStandards[jk].BB_U);
    
    const tbuStd = whoStandards[jk].TB_U[tbuAge];
    const bbuStd = whoStandards[jk].BB_U[bbuAge];
    
    // Calculate Z-scores
    const zTBU = calculateZScore(tb, tbuStd.median, tbuStd.sd);
    const zBBU = calculateZScore(bb, bbuStd.median, bbuStd.sd);
    
    // Update UI
    document.getElementById('resultPlaceholder').classList.add('d-none');
    document.getElementById('resultContent').classList.remove('d-none');
    
    // Stunting result
    const stuntingBox = document.getElementById('stuntingBox');
    const stuntingResult = document.getElementById('stuntingResult');
    const stuntingZScore = document.getElementById('stuntingZScore');
    const stuntingAlert = document.getElementById('stuntingAlert');
    
    stuntingZScore.textContent = 'Z-Score: ' + zTBU.toFixed(2);
    
    if (zTBU < -3) {
        stuntingBox.className = 'p-3 rounded bg-danger text-white';
        stuntingResult.textContent = 'SANGAT PENDEK';
        stuntingAlert.classList.remove('d-none');
        document.getElementById('stuntingKategori').textContent = 'SANGAT PENDEK (Severely Stunted)';
    } else if (zTBU < -2) {
        stuntingBox.className = 'p-3 rounded bg-warning';
        stuntingResult.textContent = 'PENDEK';
        stuntingAlert.classList.remove('d-none');
        document.getElementById('stuntingKategori').textContent = 'PENDEK (Stunted)';
    } else {
        stuntingBox.className = 'p-3 rounded bg-success text-white';
        stuntingResult.textContent = 'NORMAL';
        stuntingAlert.classList.add('d-none');
    }
    
    // Gizi result
    const giziBox = document.getElementById('giziBox');
    const giziResult = document.getElementById('giziResult');
    const giziZScore = document.getElementById('giziZScore');
    
    giziZScore.textContent = 'Z-Score: ' + zBBU.toFixed(2);
    
    if (zBBU < -3) {
        giziBox.className = 'p-3 rounded bg-danger text-white';
        giziResult.textContent = 'BURUK';
    } else if (zBBU < -2) {
        giziBox.className = 'p-3 rounded bg-warning';
        giziResult.textContent = 'KURANG';
    } else if (zBBU > 2) {
        giziBox.className = 'p-3 rounded bg-info text-white';
        giziResult.textContent = 'LEBIH';
    } else {
        giziBox.className = 'p-3 rounded bg-success text-white';
        giziResult.textContent = 'BAIK';
    }
}

document.getElementById('penduduk_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (this.value) {
        document.getElementById('balitaInfo').classList.remove('d-none');
        document.getElementById('infoJK').textContent = option.dataset.jk == 'L' ? 'Laki-laki' : 'Perempuan';
        document.getElementById('infoUsia').textContent = option.dataset.usia + ' bulan';
    } else {
        document.getElementById('balitaInfo').classList.add('d-none');
    }
    updateResults();
});

document.getElementById('berat_badan').addEventListener('input', updateResults);
document.getElementById('tinggi_badan').addEventListener('input', updateResults);
</script>
