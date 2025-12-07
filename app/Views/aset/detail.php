<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-box me-2 text-primary"></i>Detail Aset
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/aset') ?>">SIPADES</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/aset/list') ?>">Daftar Aset</a></li>
                    <li class="breadcrumb-item active"><?= esc($aset['kode_register']) ?></li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('/aset/edit/' . $aset['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="<?= base_url('/aset/list') ?>" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
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

    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            <!-- Kode Register Badge -->
            <div class="card border-0 shadow-sm mb-4 bg-gradient text-white" 
                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-qrcode fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h6 class="text-white-50 mb-1">Kode Register</h6>
                            <h3 class="mb-0"><?= esc($aset['kode_register']) ?></h3>
                        </div>
                        <div class="col-auto">
                            <?php
                            $kondisiClass = [
                                'Baik' => 'success',
                                'Rusak Ringan' => 'warning',
                                'Rusak Berat' => 'danger',
                            ];
                            ?>
                            <span class="badge bg-<?= $kondisiClass[$aset['kondisi']] ?? 'secondary' ?> fs-6 px-3 py-2">
                                <?= $aset['kondisi'] ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Barang</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small">Nama Barang</label>
                            <p class="fw-bold mb-0"><?= esc($aset['nama_barang']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Kategori</label>
                            <p class="mb-0">
                                <span class="badge bg-secondary"><?= esc($aset['kode_golongan']) ?></span>
                                <?= esc($aset['nama_golongan']) ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Merk / Type</label>
                            <p class="mb-0"><?= esc($aset['merk_type']) ?: '-' ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Ukuran</label>
                            <p class="mb-0"><?= esc($aset['ukuran']) ?: '-' ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Bahan</label>
                            <p class="mb-0"><?= esc($aset['bahan']) ?: '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-money-bill me-2 text-success"></i>Nilai & Sumber</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="text-muted small">Tahun Perolehan</label>
                            <p class="fw-bold mb-0 fs-5"><?= $aset['tahun_perolehan'] ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Harga Perolehan</label>
                            <p class="fw-bold mb-0 fs-5 text-success">
                                Rp <?= number_format($aset['harga_perolehan'], 0, ',', '.') ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Nilai Sisa</label>
                            <p class="mb-0 fs-5">
                                Rp <?= number_format($aset['nilai_sisa'], 0, ',', '.') ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Sumber Dana</label>
                            <p class="mb-0">
                                <span class="badge bg-info"><?= esc($aset['sumber_dana']) ?></span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Status Penggunaan</label>
                            <p class="mb-0">
                                <?php
                                $statusClass = [
                                    'Digunakan' => 'success',
                                    'Tidak Digunakan' => 'secondary',
                                    'Dipinjamkan' => 'warning',
                                    'Dihapuskan' => 'danger',
                                ];
                                ?>
                                <span class="badge bg-<?= $statusClass[$aset['status_penggunaan']] ?? 'secondary' ?>">
                                    <?= $aset['status_penggunaan'] ?>
                                </span>
                            </p>
                        </div>
                        <?php if ($aset['masa_manfaat']): ?>
                        <div class="col-12">
                            <label class="text-muted small">Masa Manfaat (Penyusutan)</label>
                            <p class="mb-0"><?= $aset['masa_manfaat'] ?> Tahun</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Location Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-info"></i>Lokasi & Pengguna</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small">Lokasi Penempatan</label>
                            <p class="mb-0"><?= esc($aset['lokasi']) ?: '-' ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Pengguna / Penanggung Jawab</label>
                            <p class="mb-0"><?= esc($aset['pengguna']) ?: '-' ?></p>
                        </div>
                        <?php if ($aset['lat'] && $aset['lng']): ?>
                        <div class="col-12">
                            <label class="text-muted small">Koordinat GPS</label>
                            <p class="mb-2">
                                <i class="fas fa-map-pin me-1"></i>
                                <?= $aset['lat'] ?>, <?= $aset['lng'] ?>
                                <a href="https://www.google.com/maps?q=<?= $aset['lat'] ?>,<?= $aset['lng'] ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-external-link-alt me-1"></i>Buka di Google Maps
                                </a>
                            </p>
                            <div id="mapContainer" style="height: 250px; border-radius: 8px;"></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Linked BKU -->
            <?php if ($linkedBku): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-link me-2 text-warning"></i>Transaksi BKU Terkait</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="text-muted small">No Bukti</label>
                            <p class="fw-bold mb-0"><?= esc($linkedBku['no_bukti']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Tanggal</label>
                            <p class="mb-0"><?= date('d/m/Y', strtotime($linkedBku['tanggal'])) ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Jumlah</label>
                            <p class="mb-0 fw-bold text-success">
                                Rp <?= number_format(max($linkedBku['debet'], $linkedBku['kredit']), 0, ',', '.') ?>
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Uraian</label>
                            <p class="mb-0"><?= esc($linkedBku['uraian']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Photo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-camera me-2 text-warning"></i>Foto Aset</h5>
                </div>
                <div class="card-body text-center">
                    <?php if ($aset['foto']): ?>
                        <img src="<?= base_url('writable/' . $aset['foto']) ?>" 
                             class="img-fluid rounded" 
                             style="max-height: 300px; cursor: pointer;"
                             onclick="openImageModal(this.src)"
                             alt="Foto Aset">
                    <?php else: ?>
                        <div class="py-5 text-muted">
                            <i class="fas fa-image fa-4x mb-3 d-block"></i>
                            <p>Tidak ada foto</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes -->
            <?php if ($aset['keterangan']): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2 text-secondary"></i>Keterangan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(esc($aset['keterangan'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Audit Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-muted"></i>Informasi Audit</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Dibuat</label>
                        <p class="mb-0"><?= date('d/m/Y H:i', strtotime($aset['created_at'])) ?></p>
                    </div>
                    <div>
                        <label class="text-muted small">Terakhir Diupdate</label>
                        <p class="mb-0"><?= date('d/m/Y H:i', strtotime($aset['updated_at'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-grid gap-2">
                <a href="<?= base_url('/aset/edit/' . $aset['id']) ?>" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit Aset
                </a>
                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash me-2"></i>Hapus Aset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" 
                        data-bs-dismiss="modal" style="z-index: 1;"></button>
                <img src="" id="modalImage" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus aset:</p>
                <p class="fw-bold"><?= esc($aset['nama_barang']) ?></p>
                <p class="text-muted">Kode: <?= esc($aset['kode_register']) ?></p>
                <p class="text-danger">
                    <small><i class="fas fa-warning me-1"></i>Tindakan ini tidak dapat dibatalkan!</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= base_url('/aset/delete/' . $aset['id']) ?>" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($aset['lat'] && $aset['lng']): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('mapContainer').setView([<?= $aset['lat'] ?>, <?= $aset['lng'] ?>], 17);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);
    
    L.marker([<?= $aset['lat'] ?>, <?= $aset['lng'] ?>])
        .addTo(map)
        .bindPopup('<strong><?= esc($aset['nama_barang']) ?></strong>')
        .openPopup();
});
</script>
<?php endif; ?>

<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function confirmDelete() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?= view('layout/footer') ?>
