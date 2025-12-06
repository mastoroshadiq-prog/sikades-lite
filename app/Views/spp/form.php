<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-file-invoice text-primary"></i>
            <?= isset($spp) ? 'Edit SPP' : 'Buat SPP Baru' ?>
        </h2>
        <p class="text-muted mb-0">Form Surat Permintaan Pembayaran</p>
    </div>
    <div>
        <a href="<?= base_url('/spp') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Form SPP</h5>
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
                
                <form action="<?= isset($spp) ? base_url('/spp/update/' . $spp['id']) : base_url('/spp/save') ?>" 
                      method="POST" 
                      id="formSpp">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <!-- Nomor SPP -->
                        <div class="col-md-4 mb-3">
                            <label for="nomor_spp" class="form-label">
                                Nomor SPP <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="nomor_spp" 
                                   id="nomor_spp" 
                                   class="form-control" 
                                   required 
                                   placeholder="SPP-001/2025"
                                   value="<?= isset($spp) ? esc($spp['nomor_spp']) : 'SPP-' . date('Y') . '-' ?>">
                        </div>
                        
                        <!-- Tanggal SPP -->
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_spp" class="form-label">
                                Tanggal SPP <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="tanggal_spp" 
                                   id="tanggal_spp" 
                                   class="form-control" 
                                   required
                                   value="<?= isset($spp) ? $spp['tanggal_spp'] : date('Y-m-d') ?>">
                        </div>
                        
                        <!-- Total (Display Only) -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total SPP</label>
                            <input type="text" 
                                   id="total_display" 
                                   class="form-control bg-light" 
                                   readonly
                                   value="Rp 0">
                        </div>
                    </div>
                    
                    <!-- Uraian -->
                    <div class="mb-3">
                        <label for="uraian" class="form-label">
                            Uraian/Keterangan <span class="text-danger">*</span>
                        </label>
                        <textarea name="uraian" 
                                  id="uraian" 
                                  class="form-control" 
                                  rows="2" 
                                  required 
                                  placeholder="Keterangan umum SPP..."><?= isset($spp) ? esc($spp['uraian']) : '' ?></textarea>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3"><i class="fas fa-list me-2"></i>Rincian SPP / Detail Item</h6>
                    
                    <!-- Rincian Table -->
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="rincianTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 35%">Anggaran <span class="text-danger">*</span></th>
                                    <th style="width: 35%">Uraian Rincian</th>
                                    <th style="width: 20%">Jumlah (Rp) <span class="text-danger">*</span></th>
                                    <th style="width: 5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="rincianBody">
                                <?php if (isset($rincian) && !empty($rincian)): ?>
                                    <?php foreach ($rincian as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <select name="apbdes_id[]" class="form-select" required>
                                                    <option value="">-- Pilih Anggaran --</option>
                                                    <?php foreach ($anggaran as $ang): ?>
                                                        <option value="<?= $ang['id'] ?>" 
                                                                <?= $item['apbdes_id'] == $ang['id'] ? 'selected' : '' ?>>
                                                            <?= $ang['kode_akun'] ?> - <?= $ang['uraian'] ?> 
                                                            (Rp <?= number_format($ang['anggaran'], 0, ',', '.') ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="uraian_rincian[]" 
                                                       class="form-control" 
                                                       placeholder="Uraian detail..."
                                                       value="<?= esc($item['uraian']) ?>">
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="jumlah_rincian[]" 
                                                       class="form-control jumlah-input" 
                                                       required 
                                                       min="0"
                                                       step="0.01"
                                                       value="<?= $item['jumlah'] ?>">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <select name="apbdes_id[]" class="form-select" required>
                                                <option value="">-- Pilih Anggaran --</option>
                                                <?php foreach ($anggaran as $ang): ?>
                                                    <option value="<?= $ang['id'] ?>">
                                                        <?= $ang['kode_akun'] ?> - <?= $ang['uraian'] ?> 
                                                        (Rp <?= number_format($ang['anggaran'], 0, ',', '.') ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="uraian_rincian[]" class="form-control" placeholder="Uraian detail...">
                                        </td>
                                        <td>
                                            <input type="number" name="jumlah_rincian[]" class="form-control jumlah-input" required min="0" step="0.01">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <button type="button" class="btn btn-success mb-3" onclick="addRow()">
                        <i class="fas fa-plus me-2"></i>Tambah Rincian
                    </button>
                    
                    <hr>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('/spp') ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?= isset($spp) ? 'Update' : 'Simpan' ?> SPP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let rowCount = <?= isset($rincian) ? count($rincian) : 1 ?>;
const anggaranOptions = `<?php 
    $options = '<option value="">-- Pilih Anggaran --</option>';
    foreach ($anggaran as $ang) {
        $options .= '<option value="' . $ang['id'] . '">' 
                 . $ang['kode_akun'] . ' - ' . $ang['uraian'] 
                 . ' (Rp ' . number_format($ang['anggaran'], 0, ',', '.') . ')</option>';
    }
    echo addslashes($options);
?>`;

function addRow() {
    rowCount++;
    const tbody = document.getElementById('rincianBody');
    const newRow = `
        <tr>
            <td>${rowCount}</td>
            <td>
                <select name="apbdes_id[]" class="form-select" required>
                    ${anggaranOptions}
                </select>
            </td>
            <td>
                <input type="text" name="uraian_rincian[]" class="form-control" placeholder="Uraian detail...">
            </td>
            <td>
                <input type="number" name="jumlah_rincian[]" class="form-control jumlah-input" required min="0" step="0.01">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
    updateRowNumbers();
    calculateTotal();
}

function removeRow(btn) {
    if (document.querySelectorAll('#rincianBody tr').length > 1) {
        btn.closest('tr').remove();
        updateRowNumbers();
        calculateTotal();
    } else {
        showToast('warning', 'Perhatian', 'Minimal harus ada 1 rincian');
    }
}

function updateRowNumbers() {
    document.querySelectorAll('#rincianBody tr').forEach((row, index) => {
        row.querySelector('td:first-child').textContent = index + 1;
    });
    rowCount = document.querySelectorAll('#rincianBody tr').length;
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.jumlah-input').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('total_display').value = 'Rp ' + total.toLocaleString('id-ID');
}

// Auto-calculate on input change
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('jumlah-input')) {
        calculateTotal();
    }
});

// Calculate on page load
document.addEventListener('DOMContentLoaded', calculateTotal);
</script>

<?= view('layout/footer') ?>
