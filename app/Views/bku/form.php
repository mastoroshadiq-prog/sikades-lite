<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-book text-primary"></i> <?= isset($bku) ? 'Edit' : 'Tambah' ?> Transaksi BKU</h2>
        <p class="text-muted mb-0">Form pencatatan kas</p>
    </div>
    <a href="<?= base_url('/bku') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Form Card -->
<div class="row">
    <div class="col-lg-10">
        <div class="card">
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
                
                <form action="<?= isset($bku) ? base_url('/bku/update/' . $bku['id']) : base_url('/bku/save') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <!-- Tanggal -->
                        <div class="col-md-4 mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required
                                   value="<?= isset($bku) ? $bku['tanggal'] : date('Y-m-d') ?>">
                        </div>
                        
                        <!-- No Bukti -->
                        <div class="col-md-4 mb-3">
                            <label for="no_bukti" class="form-label">No. Bukti <span class="text-danger">*</span></label>
                            <input type="text" name="no_bukti" id="no_bukti" class="form-control" required 
                                   placeholder="BKU/001/2025"
                                   value="<?= isset($bku) ? esc($bku['no_bukti']) : 'BKU/' . date('Ymd') . '/' ?>">
                        </div>
                        
                        <!-- Jenis Transaksi -->
                        <div class="col-md-4 mb-3">
                            <label for="jenis_transaksi" class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                            <select name="jenis_transaksi" id="jenis_transaksi" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Pendapatan" <?= isset($bku) && $bku['jenis_transaksi'] == 'Pendapatan' ? 'selected' : '' ?>>Pendapatan (Debet)</option>
                                <option value="Belanja" <?= isset($bku) && $bku['jenis_transaksi'] == 'Belanja' ? 'selected' : '' ?>>Belanja (Kredit)</option>
                                <option value="Mutasi" <?= isset($bku) && $bku['jenis_transaksi'] == 'Mutasi' ? 'selected' : '' ?>>Mutasi</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Uraian -->
                    <div class="mb-3">
                        <label for="uraian" class="form-label">Uraian/Keterangan <span class="text-danger">*</span></label>
                        <textarea name="uraian" id="uraian" class="form-control" rows="2" required 
                                  placeholder="Keterangan transaksi..."><?= isset($bku) ? esc($bku['uraian']) : '' ?></textarea>
                    </div>
                    
                    <div class="row">
                        <!-- Rekening -->
                        <div class="col-md-6 mb-3">
                            <label for="ref_rekening_id" class="form-label">Rekening <span class="text-danger">*</span></label>
                            <select name="ref_rekening_id" id="ref_rekening_id" class="form-select" required>
                                <option value="">-- Pilih Rekening --</option>
                                <?php foreach ($rekening as $rek): ?>
                                    <?php $indent = str_repeat('—', $rek['level'] - 1); ?>
                                    <option value="<?= $rek['id'] ?>" 
                                            <?= isset($bku) && $bku['ref_rekening_id'] == $rek['id'] ? 'selected' : '' ?>>
                                        <?= $indent ?> <?= $rek['kode_akun'] ?> - <?= $rek['nama_akun'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- SPP (Optional) -->
                        <div class="col-md-6 mb-3">
                            <label for="spp_id" class="form-label">Link ke SPP <small class="text-muted">(Opsional)</small></label>
                            <select name="spp_id" id="spp_id" class="form-select">
                                <option value="">-- Tidak ada --</option>
                                <?php foreach ($spp_list as $spp): ?>
                                    <option value="<?= $spp['id'] ?>" 
                                            <?= isset($bku) && $bku['spp_id'] == $spp['id'] ? 'selected' : '' ?>>
                                        <?= $spp['nomor_spp'] ?> - Rp <?= number_format($spp['jumlah'], 0, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Debet -->
                        <div class="col-md-6 mb-3">
                            <label for="debet" class="form-label">Debet (Kas Masuk)</label>
                            <input type="number" name="debet" id="debet" class="form-control" min="0" step="0.01"
                                   placeholder="0" value="<?= isset($bku) ? $bku['debet'] : '' ?>">
                            <small class="text-muted">Untuk transaksi Pendapatan</small>
                        </div>
                        
                        <!-- Kredit -->
                        <div class="col-md-6 mb-3">
                            <label for="kredit" class="form-label">Kredit (Kas Keluar)</label>
                            <input type="number" name="kredit" id="kredit" class="form-control" min="0" step="0.01"
                                   placeholder="0" value="<?= isset($bku) ? $bku['kredit'] : '' ?>">
                            <small class="text-muted">Untuk transaksi Belanja</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('/bku') ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?= isset($bku) ? 'Update' : 'Simpan' ?> Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-fill debet/kredit based on jenis transaksi
document.getElementById('jenis_transaksi').addEventListener('change', function() {
    const debetInput = document.getElementById('debet');
    const kreditInput = document.getElementById('kredit');
    
    if (this.value === 'Pendapatan') {
        debetInput.disabled = false;
        kreditInput.disabled = true;
        kreditInput.value = 0;
    } else if (this.value === 'Belanja') {
        debetInput.disabled = true;
        kreditInput.disabled = false;
        debetInput.value = 0;
    } else {
        debetInput.disabled = false;
        kreditInput.disabled = false;
    }
});
</script>

<?= view('layout/footer') ?>
