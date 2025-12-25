<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-file-invoice-dollar text-primary"></i>
            <?= isset($anggaran) ? 'Edit Anggaran' : 'Tambah Anggaran' ?>
        </h2>
        <p class="text-muted mb-0">Form Input APBDes</p>
    </div>
    <div>
        <a href="<?= base_url('/apbdes') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Form <?= isset($anggaran) ? 'Edit' : 'Input' ?> Anggaran
                </h5>
            </div>
            <div class="card-body">
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan:</h6>
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= isset($anggaran) ? base_url('/apbdes/update/' . $anggaran['id']) : base_url('/apbdes/save') ?>" 
                      method="POST" 
                      id="formAnggaran">
                    <?= csrf_field() ?>
                    
                    <!-- Tahun Anggaran -->
                    <div class="mb-3">
                        <label for="tahun" class="form-label">
                            Tahun Anggaran <span class="text-danger">*</span>
                        </label>
                        <?php $selectedTahun = isset($anggaran) ? $anggaran['tahun'] : ($tahun ?? date('Y')); ?>
                        <select name="tahun" 
                                id="tahun" 
                                class="form-select" 
                                required>
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++): ?>
                                <option value="<?= $y ?>" <?= $y == $selectedTahun ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>Tahun anggaran yang dipilih: <strong><?= $selectedTahun ?></strong>
                        </small>
                    </div>
                    
                    <!-- Rekening -->
                    <div class="mb-3">
                        <label for="ref_rekening_id" class="form-label">
                            Kode Rekening <span class="text-danger">*</span>
                        </label>
                        <select name="ref_rekening_id" 
                                id="ref_rekening_id" 
                                class="form-select" 
                                required>
                            <option value="">-- Pilih Rekening --</option>
                            <?php foreach ($rekening as $rek): ?>
                                <?php
                                $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $rek['level'] - 1);
                                $prefix = str_repeat('—', $rek['level'] - 1);
                                $levelLabel = ['', 'Akun', 'Kelompok', 'Jenis', 'Objek'][$rek['level']] ?? '';
                                ?>
                                <option value="<?= $rek['id'] ?>" 
                                        data-level="<?= $rek['level'] ?>"
                                        <?= (isset($anggaran) && $anggaran['ref_rekening_id'] == $rek['id']) ? 'selected' : '' ?>>
                                    <?= $prefix ?> <?= $rek['kode_akun'] ?> - <?= $rek['nama_akun'] ?> (<?= $levelLabel ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            Pilih kode rekening sesuai jenis anggaran
                        </small>
                    </div>
                    
                    <!-- Uraian -->
                    <div class="mb-3">
                        <label for="uraian" class="form-label">
                            Uraian/Keterangan <span class="text-danger">*</span>
                        </label>
                        <textarea name="uraian" 
                                  id="uraian" 
                                  class="form-control" 
                                  rows="3" 
                                  required 
                                  placeholder="Uraian detail anggaran..."><?= isset($anggaran) ? esc($anggaran['uraian']) : old('uraian') ?></textarea>
                    </div>
                    
                    <!-- Nominal Anggaran -->
                    <div class="mb-3">
                        <label for="anggaran" class="form-label">
                            Nominal Anggaran (Rp) <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               name="anggaran" 
                               id="anggaran" 
                               class="form-control" 
                               required 
                               min="0"
                               step="0.01"
                               placeholder="0"
                               value="<?= isset($anggaran) ? $anggaran['anggaran'] : old('anggaran') ?>">
                        <small class="form-text text-muted">
                            Nominal anggaran tidak boleh bernilai minus
                        </small>
                    </div>
                    
                    <!-- Sumber Dana -->
                    <div class="mb-3">
                        <label for="sumber_dana" class="form-label">
                            Sumber Dana <span class="text-danger">*</span>
                        </label>
                        <select name="sumber_dana" 
                                id="sumber_dana" 
                                class="form-select" 
                                required>
                            <option value="">-- Pilih Sumber Dana --</option>
                            <option value="DDS" <?= (isset($anggaran) && $anggaran['sumber_dana'] == 'DDS') ? 'selected' : '' ?>>
                                DDS (Dana Desa)
                            </option>
                            <option value="ADD" <?= (isset($anggaran) && $anggaran['sumber_dana'] == 'ADD') ? 'selected' : '' ?>>
                                ADD (Alokasi Dana Desa)
                            </option>
                            <option value="PAD" <?= (isset($anggaran) && $anggaran['sumber_dana'] == 'PAD') ? 'selected' : '' ?>>
                                PAD (Pendapatan Asli Desa)
                            </option>
                            <option value="Bankeu" <?= (isset($anggaran) && $anggaran['sumber_dana'] == 'Bankeu') ? 'selected' : '' ?>>
                                Bantuan Keuangan
                            </option>
                        </select>
                    </div>
                    
                    <!-- Optional: Link to RKP Desa -->
                    <?php if (isset($rkp) && $rkp): ?>
                    <div class="card bg-light border-info mb-3">
                        <div class="card-header bg-info text-white py-2">
                            <small class="mb-0"><i class="fas fa-link me-2"></i>Hubungkan dengan RKP Desa (Opsional)</small>
                        </div>
                        <div class="card-body py-3">
                            <p class="small text-muted mb-2">
                                <i class="fas fa-info-circle me-1"></i>
                                RKP Desa Tahun <?= $rkp['tahun'] ?> tersedia. Anda dapat menghubungkan anggaran ini dengan kegiatan dari RKP.
                            </p>
                            
                            <input type="hidden" name="rkpdesa_id" value="<?= $rkp['id'] ?>">
                            
                            <div class="mb-0">
                                <label for="kegiatan_id" class="form-label small mb-1">
                                    Kegiatan dari RKP <span class="badge bg-secondary">Opsional</span>
                                </label>
                                <select name="kegiatan_id" id="kegiatan_id" class="form-select form-select-sm">
                                    <option value="">-- Tidak terhubung dengan kegiatan --</option>
                                    <?php if (!empty($kegiatanList)): ?>
                                        <?php foreach ($kegiatanList as $keg): ?>
                                        <option value="<?= $keg['id'] ?>" 
                                                data-pagu="<?= $keg['pagu_anggaran'] ?? 0 ?>"
                                                <?= (isset($anggaran) && ($anggaran['kegiatan_id'] ?? null) == $keg['id']) ? 'selected' : '' ?>>
                                            <?= esc($keg['nama_kegiatan']) ?> 
                                            (Pagu: Rp <?= number_format($keg['pagu_anggaran'] ?? 0, 0, ',', '.') ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Belum ada kegiatan di RKP ini</option>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">
                                    Pilih kegiatan jika anggaran ini merupakan penjabaran dari kegiatan RKP
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning py-2 mb-3">
                        <small>
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Perhatian:</strong> Belum ada RKP Desa untuk tahun <?= $tahun ?? date('Y') ?>. 
                            Anggaran akan dibuat tanpa hubungan ke RKP Desa.
                            <a href="<?= base_url('/perencanaan/rkp/create') ?>" class="alert-link">Buat RKP Desa</a>
                        </small>
                    </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('/apbdes') ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?= isset($anggaran) ? 'Update' : 'Simpan' ?> Anggaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Info Sidebar -->
    <div class="col-lg-4">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
            </div>
            <div class="card-body">
                <h6 class="text-primary">Struktur Kode Rekening:</h6>
                <ul class="small">
                    <li><strong>Level 1 (Akun):</strong> 4, 5, 6</li>
                    <li><strong>Level 2 (Kelompok):</strong> 4.1, 5.1</li>
                    <li><strong>Level 3 (Jenis):</strong> 4.1.1, 5.1.1</li>
                    <li><strong>Level 4 (Objek):</strong> 4.1.1.01, 5.1.1.01</li>
                </ul>
                
                <hr>
                
                <h6 class="text-primary">Jenis Rekening:</h6>
                <ul class="small">
                    <li><strong>4.x:</strong> Pendapatan</li>
                    <li><strong>5.x:</strong> Belanja</li>
                    <li><strong>6.x:</strong> Pembiayaan</li>
                </ul>
                
                <hr>
                
                <h6 class="text-primary">Sumber Dana:</h6>
                <ul class="small mb-0">
                    <li><strong>DDS:</strong> Dana Desa dari APBN</li>
                    <li><strong>ADD:</strong> Alokasi Dana Desa</li>
                    <li><strong>PAD:</strong> Pendapatan Asli Desa</li>
                    <li><strong>Bankeu:</strong> Bantuan Keuangan Prov/Kab</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Format number input with thousand separator on blur
    document.getElementById('anggaran').addEventListener('blur', function(e) {
        let value = parseFloat(this.value.replace(/,/g, ''));
        if (!isNaN(value)) {
            this.value = value;
        }
    });
    
    // Validate negative values
    document.getElementById('formAnggaran').addEventListener('submit', function(e) {
        const anggaran = parseFloat(document.getElementById('anggaran').value);
        
        if (anggaran < 0) {
            e.preventDefault();
            showToast('error', 'Validasi Gagal', 'Anggaran tidak boleh bernilai minus');
            return false;
        }
    });
</script>

<?= view('layout/footer') ?>
