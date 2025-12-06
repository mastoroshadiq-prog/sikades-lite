<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan') ?>">Perencanaan</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan/rkp') ?>">RKP Desa</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan/rkp/detail/' . $rkp['id']) ?>">Tahun <?= $rkp['tahun'] ?></a></li>
            <li class="breadcrumb-item active"><?= isset($kegiatan) ? 'Edit' : 'Tambah' ?> Kegiatan</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0">
                        <i class="fas fa-<?= isset($kegiatan) ? 'edit' : 'plus' ?> me-2"></i>
                        <?= isset($kegiatan) ? 'Edit Kegiatan' : 'Tambah Kegiatan Baru' ?>
                        <small class="ms-2">(RKP <?= $rkp['tahun'] ?>)</small>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <form action="<?= isset($kegiatan) ? base_url('/perencanaan/kegiatan/update/' . $kegiatan['id']) : base_url('/perencanaan/kegiatan/save') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="rkpdesa_id" value="<?= $rkp['id'] ?>">
                        
                        <!-- Basic Info -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Bidang <span class="text-danger">*</span></label>
                                <select name="bidang_id" class="form-select" required>
                                    <option value="">-- Pilih Bidang --</option>
                                    <?php foreach ($bidangList as $bidang): ?>
                                    <option value="<?= $bidang['id'] ?>" <?= (isset($kegiatan) && $kegiatan['bidang_id'] == $bidang['id']) ? 'selected' : '' ?>>
                                        <?= $bidang['kode_bidang'] ?> - <?= $bidang['nama_bidang'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kode Kegiatan</label>
                                <input type="text" name="kode_kegiatan" class="form-control" 
                                       value="<?= esc($kegiatan['kode_kegiatan'] ?? '') ?>" 
                                       placeholder="Contoh: 02.01.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prioritas</label>
                                <input type="number" name="prioritas" class="form-control" 
                                       value="<?= $kegiatan['prioritas'] ?? 1 ?>" min="1" max="100">
                                <small class="text-muted">Urutan prioritas (1 = tertinggi)</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" class="form-control" required
                                   value="<?= esc($kegiatan['nama_kegiatan'] ?? '') ?>" 
                                   placeholder="Contoh: Pembangunan Jalan Desa RT 01">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control" 
                                       value="<?= esc($kegiatan['lokasi'] ?? '') ?>" 
                                       placeholder="Contoh: Dusun Makmur RT 01">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Volume</label>
                                <input type="text" name="volume" class="form-control" 
                                       value="<?= esc($kegiatan['volume'] ?? '') ?>" 
                                       placeholder="Contoh: 500">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Satuan</label>
                                <input type="text" name="satuan" class="form-control" 
                                       value="<?= esc($kegiatan['satuan'] ?? '') ?>" 
                                       placeholder="Contoh: meter">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sasaran / Manfaat</label>
                            <textarea name="sasaran_manfaat" class="form-control" rows="2" 
                                      placeholder="Siapa yang akan merasakan manfaat kegiatan ini?"><?= esc($kegiatan['sasaran_manfaat'] ?? '') ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Waktu Pelaksanaan</label>
                                <input type="text" name="waktu_pelaksanaan" class="form-control" 
                                       value="<?= esc($kegiatan['waktu_pelaksanaan'] ?? '') ?>" 
                                       placeholder="Contoh: Januari - Maret">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sumber Dana</label>
                                <select name="sumber_dana" class="form-select">
                                    <option value="DDS" <?= (isset($kegiatan) && $kegiatan['sumber_dana'] == 'DDS') ? 'selected' : '' ?>>Dana Desa (DDS)</option>
                                    <option value="ADD" <?= (isset($kegiatan) && $kegiatan['sumber_dana'] == 'ADD') ? 'selected' : '' ?>>Alokasi Dana Desa (ADD)</option>
                                    <option value="PAD" <?= (isset($kegiatan) && $kegiatan['sumber_dana'] == 'PAD') ? 'selected' : '' ?>>Pendapatan Asli Desa (PAD)</option>
                                    <option value="Bantuan Keuangan" <?= (isset($kegiatan) && $kegiatan['sumber_dana'] == 'Bantuan Keuangan') ? 'selected' : '' ?>>Bantuan Keuangan</option>
                                    <option value="Swadaya" <?= (isset($kegiatan) && $kegiatan['sumber_dana'] == 'Swadaya') ? 'selected' : '' ?>>Swadaya Masyarakat</option>
                                    <option value="Lainnya" <?= (isset($kegiatan) && $kegiatan['sumber_dana'] == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Usulan" <?= (isset($kegiatan) && $kegiatan['status'] == 'Usulan') ? 'selected' : '' ?>>Usulan</option>
                                    <option value="Prioritas" <?= (isset($kegiatan) && $kegiatan['status'] == 'Prioritas') ? 'selected' : '' ?>>Prioritas</option>
                                    <option value="Disetujui" <?= (isset($kegiatan) && $kegiatan['status'] == 'Disetujui') ? 'selected' : '' ?>>Disetujui</option>
                                    <option value="Ditolak" <?= (isset($kegiatan) && $kegiatan['status'] == 'Ditolak') ? 'selected' : '' ?>>Ditolak</option>
                                    <option value="Berjalan" <?= (isset($kegiatan) && $kegiatan['status'] == 'Berjalan') ? 'selected' : '' ?>>Berjalan</option>
                                    <option value="Selesai" <?= (isset($kegiatan) && $kegiatan['status'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pagu Anggaran (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="pagu_anggaran" id="paguAnggaran" class="form-control" required
                                       value="<?= isset($kegiatan) ? number_format($kegiatan['pagu_anggaran'], 0, ',', '.') : '' ?>" 
                                       placeholder="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" 
                                      placeholder="Catatan tambahan..."><?= esc($kegiatan['keterangan'] ?? '') ?></textarea>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/perencanaan/rkp/detail/' . $rkp['id']) ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i><?= isset($kegiatan) ? 'Update' : 'Simpan' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Format currency input
document.getElementById('paguAnggaran').addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    this.value = new Intl.NumberFormat('id-ID').format(value);
});
</script>

<?= view('layout/footer') ?>
