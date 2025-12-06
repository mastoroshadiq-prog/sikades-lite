<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-file-invoice-dollar text-primary"></i> <?= isset($pajak) ? 'Edit' : 'Tambah' ?> Pajak</h2>
        <p class="text-muted mb-0">Form pencatatan pajak</p>
    </div>
    <a href="<?= base_url('/pajak') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Form Card -->
<div class="row">
    <div class="col-lg-8">
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
                
                <form action="<?= isset($pajak) ? base_url('/pajak/update/' . $pajak['id']) : base_url('/pajak/save') ?>" method="POST" id="formPajak">
                    <?= csrf_field() ?>
                    
                    <?php if (!isset($pajak)): ?>
                    <!-- BKU Selection (Only for create) -->
                    <div class="mb-3">
                        <label for="bku_id" class="form-label">Transaksi BKU <span class="text-danger">*</span></label>
                        <select name="bku_id" id="bku_id" class="form-select" required>
                            <option value="">-- Pilih Transaksi BKU --</option>
                            <?php foreach ($bku_entries as $bku): ?>
                                <option value="<?= $bku['id'] ?>" data-jumlah="<?= $bku['kredit'] ?>">
                                    <?= date('d/m/Y', strtotime($bku['tanggal'])) ?> - <?= $bku['no_bukti'] ?> - 
                                    <?= esc(substr($bku['uraian'], 0, 40)) ?> - Rp <?= number_format($bku['kredit'], 0, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Pilih transaksi belanja yang akan dicatat pajaknya</small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <!-- Jenis Pajak -->
                        <div class="col-md-6 mb-3">
                            <label for="jenis_pajak" class="form-label">Jenis Pajak <span class="text-danger">*</span></label>
                            <select name="jenis_pajak" id="jenis_pajak" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="PPN" <?= isset($pajak) && $pajak['jenis_pajak'] == 'PPN' ? 'selected' : '' ?>>PPN (Pajak Pertambahan Nilai)</option>
                                <option value="PPh" <?= isset($pajak) && $pajak['jenis_pajak'] == 'PPh' ? 'selected' : '' ?>>PPh (Pajak Penghasilan)</option>
                            </select>
                        </div>
                        
                        <!-- Tarif -->
                        <div class="col-md-6 mb-3">
                            <label for="tarif" class="form-label">Tarif (%) <span class="text-danger">*</span></label>
                            <input type="number" name="tarif" id="tarif" class="form-control" required 
                                   min="0" max="100" step="0.1"
                                   placeholder="10"
                                   value="<?= isset($pajak) ? $pajak['tarif'] : '' ?>">
                            <small class="text-muted">Contoh: PPN = 11%, PPh = 2%</small>
                        </div>
                    </div>
                    
                    <!-- Display calculated pajak -->
                    <div class="alert alert-info" id="pajakInfo" style="display: none;">
                        <strong>Perhitungan Pajak:</strong><br>
                        Nilai Transaksi: Rp <span id="nilaiTransaksi">0</span><br>
                        Tarif: <span id="displayTarif">0</span>%<br>
                        <strong>Jumlah Pajak: Rp <span id="jumlahPajak">0</span></strong>
                    </div>
                    
                    <div class="row">
                        <!-- NPWP -->
                        <div class="col-md-6 mb-3">
                            <label for="npwp" class="form-label">NPWP</label>
                            <input type="text" name="npwp" id="npwp" class="form-control" 
                                   placeholder="00.000.000.0-000.000"
                                   value="<?= isset($pajak) ? esc($pajak['npwp']) : '' ?>">
                            <small class="text-muted">Opsional</small>
                        </div>
                        
                        <!-- Nama Wajib Pajak -->
                        <div class="col-md-6 mb-3">
                            <label for="nama_wajib_pajak" class="form-label">Nama Wajib Pajak <span class="text-danger">*</span></label>
                            <input type="text" name="nama_wajib_pajak" id="nama_wajib_pajak" class="form-control" required
                                   placeholder="PT. Contoh Indonesia"
                                   value="<?= isset($pajak) ? esc($pajak['nama_wajib_pajak']) : '' ?>">
                        </div>
                    </div>
                    
                    <?php if (isset($pajak)): ?>
                    <!-- Payment Status (Edit only) -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="form-select">
                                <option value="Belum" <?= $pajak['status_pembayaran'] == 'Belum' ? 'selected' : '' ?>>Belum</option>
                                <option value="Sudah" <?= $pajak['status_pembayaran'] == 'Sudah' ? 'selected' : '' ?>>Sudah</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3" id="tanggalSetorDiv" style="display: <?= $pajak['status_pembayaran'] == 'Sudah' ? 'block' : 'none' ?>;">
                            <label for="tanggal_setor" class="form-label">Tanggal Setor</label>
                            <input type="date" name="tanggal_setor" id="tanggal_setor" class="form-control"
                                   value="<?= $pajak['tanggal_setor'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3" id="buktiSetorDiv" style="display: <?= $pajak['status_pembayaran'] == 'Sudah' ? 'block' : 'none' ?>;">
                            <label for="nomor_bukti_setor" class="form-label">No. Bukti Setor</label>
                            <input type="text" name="nomor_bukti_setor" id="nomor_bukti_setor" class="form-control"
                                   placeholder="SSP/NTPN"
                                   value="<?= isset($pajak) ? esc($pajak['nomor_bukti_setor'] ?? '') : '' ?>">
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('/pajak') ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?= isset($pajak) ? 'Update' : 'Simpan' ?> Pajak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate pajak on input change
function calculatePajak() {
    const bkuSelect = document.getElementById('bku_id');
    const tarifInput = document.getElementById('tarif');
    
    if (bkuSelect && tarifInput.value) {
        const selectedOption = bkuSelect.options[bkuSelect.selectedIndex];
        const jumlahBKU = parseFloat(selectedOption.getAttribute('data-jumlah')) || 0;
        const tarif = parseFloat(tarifInput.value) || 0;
        const jumlahPajak = jumlahBKU * (tarif / 100);
        
        if (jumlahBKU > 0) {
            document.getElementById('pajakInfo').style.display = 'block';
            document.getElementById('nilaiTransaksi').textContent = jumlahBKU.toLocaleString('id-ID');
            document.getElementById('displayTarif').textContent = tarif;
            document.getElementById('jumlahPajak').textContent = jumlahPajak.toLocaleString('id-ID');
        }
    }
}

<?php if (!isset($pajak)): ?>
document.getElementById('bku_id')?.addEventListener('change', calculatePajak);
<?php endif; ?>
document.getElementById('tarif')?.addEventListener('input', calculatePajak);

// Show/hide payment fields
document.getElementById('status_pembayaran')?.addEventListener('change', function() {
    const showPayment = this.value === 'Sudah';
    document.getElementById('tanggalSetorDiv').style.display = showPayment ? 'block' : 'none';
    document.getElementById('buktiSetorDiv').style.display = showPayment ? 'block' : 'none';
});
</script>

<?= view('layout/footer') ?>
