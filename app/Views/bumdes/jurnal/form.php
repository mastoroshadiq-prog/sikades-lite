<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-plus me-2 text-primary"></i>Tambah Jurnal
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes') ?>">BUMDes</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes/jurnal/' . $unit['id']) ?>">Jurnal</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="<?= base_url('/bumdes/jurnal/' . $unit['id'] . '/save') ?>" method="POST" id="jurnalForm">
        <?= csrf_field() ?>
        
        <div class="row">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Jurnal</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">No. Bukti</label>
                            <input type="text" name="no_bukti" class="form-control" 
                                   value="<?= esc($noBukti) ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" 
                                      placeholder="Keterangan transaksi"></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Balance Check -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-3">Balance Check</h6>
                        <div class="row">
                            <div class="col-6">
                                <h5 class="text-success" id="totalDebet">Rp 0</h5>
                                <small class="text-muted">Total Debet</small>
                            </div>
                            <div class="col-6">
                                <h5 class="text-danger" id="totalKredit">Rp 0</h5>
                                <small class="text-muted">Total Kredit</small>
                            </div>
                        </div>
                        <hr>
                        <div id="balanceStatus">
                            <span class="badge bg-warning fs-6">Belum Balance</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2 text-success"></i>Detail Jurnal (Double Entry)</h5>
                        <button type="button" class="btn btn-success btn-sm" id="addRow">
                            <i class="fas fa-plus me-1"></i>Tambah Baris
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="jurnalTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Akun</th>
                                        <th width="25%">Debet (Rp)</th>
                                        <th width="25%">Kredit (Rp)</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="detail-row">
                                        <td>
                                            <select name="details[0][akun_id]" class="form-select akun-select" required>
                                                <option value="">Pilih Akun</option>
                                                <?php foreach ($akunList as $a): ?>
                                                <option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="details[0][debet]" class="form-control debet-input rupiah-input" value="0"></td>
                                        <td><input type="text" name="details[0][kredit]" class="form-control kredit-input rupiah-input" value="0"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="detail-row">
                                        <td>
                                            <select name="details[1][akun_id]" class="form-select akun-select" required>
                                                <option value="">Pilih Akun</option>
                                                <?php foreach ($akunList as $a): ?>
                                                <option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="details[1][debet]" class="form-control debet-input rupiah-input" value="0"></td>
                                        <td><input type="text" name="details[1][kredit]" class="form-control kredit-input rupiah-input" value="0"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('/bumdes/jurnal/' . $unit['id']) ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                        <i class="fas fa-save me-2"></i>Simpan Jurnal
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?= view('layout/footer') ?>

<script>
let rowIndex = 2;
const akunOptions = `<?php foreach ($akunList as $a): ?><option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option><?php endforeach; ?>`;

// Add row
document.getElementById('addRow').addEventListener('click', function() {
    const tbody = document.querySelector('#jurnalTable tbody');
    const newRow = document.createElement('tr');
    newRow.className = 'detail-row';
    newRow.innerHTML = `
        <td>
            <select name="details[${rowIndex}][akun_id]" class="form-select akun-select" required>
                <option value="">Pilih Akun</option>
                ${akunOptions}
            </select>
        </td>
        <td><input type="text" name="details[${rowIndex}][debet]" class="form-control debet-input rupiah-input" value="0"></td>
        <td><input type="text" name="details[${rowIndex}][kredit]" class="form-control kredit-input rupiah-input" value="0"></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    rowIndex++;
    updateRemoveButtons();
    bindInputEvents();
});

// Remove row
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
        const btn = e.target.classList.contains('remove-row') ? e.target : e.target.closest('.remove-row');
        btn.closest('tr').remove();
        updateRemoveButtons();
        calculateBalance();
    }
});

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.detail-row');
    rows.forEach((row, i) => {
        const btn = row.querySelector('.remove-row');
        btn.disabled = rows.length <= 2;
    });
}

function parseRupiah(str) {
    return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
}

function formatRupiah(num) {
    return 'Rp ' + num.toLocaleString('id-ID');
}

function calculateBalance() {
    let totalDebet = 0;
    let totalKredit = 0;
    
    document.querySelectorAll('.debet-input').forEach(input => {
        totalDebet += parseRupiah(input.value);
    });
    
    document.querySelectorAll('.kredit-input').forEach(input => {
        totalKredit += parseRupiah(input.value);
    });
    
    document.getElementById('totalDebet').textContent = formatRupiah(totalDebet);
    document.getElementById('totalKredit').textContent = formatRupiah(totalKredit);
    
    const isBalance = Math.abs(totalDebet - totalKredit) < 0.01 && totalDebet > 0;
    document.getElementById('balanceStatus').innerHTML = isBalance 
        ? '<span class="badge bg-success fs-6"><i class="fas fa-check me-1"></i>Balance!</span>'
        : '<span class="badge bg-warning fs-6">Belum Balance</span>';
    
    document.getElementById('submitBtn').disabled = !isBalance;
}

function bindInputEvents() {
    document.querySelectorAll('.debet-input, .kredit-input').forEach(input => {
        input.removeEventListener('input', calculateBalance);
        input.addEventListener('input', calculateBalance);
    });
}

bindInputEvents();
</script>
