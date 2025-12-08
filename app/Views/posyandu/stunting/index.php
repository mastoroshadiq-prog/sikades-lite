<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-child me-2 text-danger"></i>Monitoring Stunting
            </h2>
            <p class="text-muted mb-0">Pemantauan kasus stunting di wilayah desa</p>
        </div>
        <a href="<?= base_url('/posyandu') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-danger text-white">
                <div class="card-body text-center">
                    <h1 class="display-4 fw-bold mb-0"><?= $stuntingStats['stunting'] ?></h1>
                    <p class="mb-0">Kasus Stunting</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-success text-white">
                <div class="card-body text-center">
                    <h1 class="display-4 fw-bold mb-0"><?= $stuntingStats['normal'] ?></h1>
                    <p class="mb-0">Pertumbuhan Normal</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                <div class="card-body text-center">
                    <h1 class="display-4 fw-bold mb-0"><?= $stuntingStats['total_balita'] ?></h1>
                    <p class="mb-0">Total Balita</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h1 class="display-4 fw-bold text-danger mb-0"><?= $stuntingStats['percentage'] ?>%</h1>
                    <p class="mb-0 text-muted">Prevalensi Stunting</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Map -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2 text-danger"></i>Peta Sebaran Stunting</h5>
                </div>
                <div class="card-body p-0">
                    <div id="stuntingMap" style="height: 500px;"></div>
                </div>
            </div>
        </div>

        <!-- List -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Kasus Stunting</h5>
                </div>
                <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                    <?php if (empty($stuntingCases)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h5 class="text-success">Tidak Ada Kasus Stunting</h5>
                            <p class="text-muted">Semua balita dalam kondisi pertumbuhan normal</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($stuntingCases as $case): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?= esc($case['nama_lengkap']) ?></h6>
                                            <small class="text-muted">
                                                <?= $case['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>, 
                                                <?= $case['usia_bulan'] ?> bulan
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i><?= esc($case['dusun']) ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-danger">
                                                Z: <?= number_format($case['z_score_tb_u'], 2) ?>
                                            </span>
                                            <br>
                                            <small class="text-muted">TB: <?= $case['tinggi_badan'] ?> cm</small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-clinic-medical me-1"></i><?= esc($case['nama_posyandu']) ?>
                                        </small>
                                        <a href="<?= base_url('/posyandu/pemeriksaan/riwayat/' . $case['penduduk_id']) ?>" 
                                           class="btn btn-sm btn-outline-primary float-end">
                                            <i class="fas fa-history"></i> Riwayat
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check me-2 text-info"></i>Rekomendasi Intervensi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-success text-white rounded-circle p-3">
                                        <i class="fas fa-apple-alt fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Pemberian Makanan Tambahan (PMT)</h6>
                                    <p class="text-muted small mb-0">Program PMT untuk balita stunting selama 90 hari dengan makanan bergizi tinggi.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle p-3">
                                        <i class="fas fa-hospital fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Rujukan ke Puskesmas</h6>
                                    <p class="text-muted small mb-0">Balita dengan Z-Score &lt; -3 SD perlu dirujuk untuk pemeriksaan lebih lanjut.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning text-white rounded-circle p-3">
                                        <i class="fas fa-users fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Penyuluhan Orang Tua</h6>
                                    <p class="text-muted small mb-0">Edukasi tentang pola makan seimbang dan stimulasi tumbuh kembang anak.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const map = L.map('stuntingMap').setView([-6.9, 110.4], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

// Fetch stunting data
fetch('<?= base_url('/posyandu/stunting/gis') ?>')
    .then(response => response.json())
    .then(data => {
        if (data.features && data.features.length > 0) {
            const bounds = [];
            
            data.features.forEach(feature => {
                const coords = feature.geometry.coordinates;
                const props = feature.properties;
                
                const marker = L.circleMarker([coords[1], coords[0]], {
                    radius: 10,
                    fillColor: props.z_score < -3 ? '#dc3545' : '#fd7e14',
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map);
                
                marker.bindPopup(`
                    <div style="min-width: 200px;">
                        <h6 class="mb-2"><i class="fas fa-child text-danger me-2"></i>${props.nama}</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td>Usia</td><td><strong>${props.usia_bulan} bulan</strong></td></tr>
                            <tr><td>Tinggi Badan</td><td><strong>${props.tinggi_badan} cm</strong></td></tr>
                            <tr><td>Z-Score</td><td><span class="badge bg-danger">${props.z_score}</span></td></tr>
                            <tr><td>Dusun</td><td>${props.dusun}</td></tr>
                            <tr><td>Posyandu</td><td>${props.posyandu}</td></tr>
                        </table>
                    </div>
                `);
                
                bounds.push([coords[1], coords[0]]);
            });
            
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }
    });

// Add legend
const legend = L.control({position: 'bottomright'});
legend.onAdd = function(map) {
    const div = L.DomUtil.create('div', 'bg-white p-2 rounded shadow-sm');
    div.innerHTML = `
        <strong class="d-block mb-2">Keterangan</strong>
        <div class="d-flex align-items-center mb-1">
            <span style="width:15px;height:15px;background:#dc3545;border-radius:50%;display:inline-block;margin-right:8px;"></span>
            <small>Sangat Pendek (Z &lt; -3)</small>
        </div>
        <div class="d-flex align-items-center">
            <span style="width:15px;height:15px;background:#fd7e14;border-radius:50%;display:inline-block;margin-right:8px;"></span>
            <small>Pendek (-3 ≤ Z &lt; -2)</small>
        </div>
    `;
    return div;
};
legend.addTo(map);
</script>
