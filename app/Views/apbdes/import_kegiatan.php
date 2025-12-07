<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/apbdes') ?>">APBDes</a></li>
            <li class="breadcrumb-item active">Import dari RKP</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-download me-2 text-success"></i>Import Kegiatan dari RKP
            </h2>
            <p class="text-muted mb-0">Tahun <?= $tahun ?><?= $rkp ? ' - ' . esc($rkp['tema']) : '' ?></p>
        </div>
        <a href="<?= base_url('/apbdes') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (empty($rkp)): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Tidak ditemukan RKP Desa untuk tahun <?= $tahun ?>. 
        <a href="<?= base_url('/perencanaan/rkp/create') ?>">Buat RKP Desa terlebih dahulu</a>
    </div>
    <?php elseif (empty($kegiatan)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Tidak ada kegiatan dengan status "Prioritas" atau "Disetujui" yang belum di-link ke APBDes.
    </div>
    <?php else: ?>
    
    <!-- Import Form -->
    <form action="<?= base_url('/apbdes/import/process') ?>" method="POST" id="importForm">
        <?= csrf_field() ?>
        <input type="hidden" name="tahun" value="<?= $tahun ?>">
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list-check me-2"></i>
                    Pilih Kegiatan untuk di-Import (<?= count($kegiatan) ?> kegiatan tersedia)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th width="25%">Nama Kegiatan</th>
                                <th width="15%">Bidang</th>
                                <th width="15%">Lokasi</th>
                                <th width="15%" class="text-end">Pagu</th>
                                <th width="25%">Kode Rekening APBDes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kegiatan as $idx => $item): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="kegiatan_ids[]" value="<?= $item['id'] ?>" 
                                           class="form-check-input kegiatan-check" data-idx="<?= $idx ?>">
                                </td>
                                <td>
                                    <strong><?= esc($item['nama_kegiatan']) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        Volume: <?= esc($item['volume'] ?? '-') ?> <?= esc($item['satuan'] ?? '') ?>
                                        | Sumber: <?= esc($item['sumber_dana']) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= esc($item['kode_bidang'] ?? '-') ?></span>
                                    <br>
                                    <small><?= esc($item['nama_bidang'] ?? '-') ?></small>
                                </td>
                                <td><?= esc($item['lokasi'] ?? '-') ?></td>
                                <td class="text-end fw-bold text-success">
                                    Rp <?= number_format($item['pagu_anggaran'], 0, ',', '.') ?>
                                </td>
                                <td>
                                    <select name="rekening_ids[<?= $idx ?>]" class="form-select form-select-sm rekening-select" disabled>
                                        <option value="">-- Pilih Rekening --</option>
                                        <?php foreach ($rekening as $rek): ?>
                                        <option value="<?= $rek['id'] ?>">
                                            <?= esc($rek['kode_akun']) ?> - <?= esc(substr($rek['nama_akun'], 0, 40)) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total Pagu Terpilih:</td>
                                <td class="text-end fw-bold text-success" id="totalPagu">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                    <i class="fas fa-check me-2"></i>Import Kegiatan Terpilih
                </button>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.kegiatan-check');
    const submitBtn = document.getElementById('submitBtn');
    const totalPaguEl = document.getElementById('totalPagu');
    
    // Pagu values
    const paguValues = <?= json_encode(array_column($kegiatan ?? [], 'pagu_anggaran', 'id')) ?>;
    
    function updateUI() {
        let total = 0;
        let selectedCount = 0;
        
        checkboxes.forEach(cb => {
            const idx = cb.dataset.idx;
            const rekeningSelect = document.querySelector(`select[name="rekening_ids[${idx}]"]`);
            
            if (cb.checked) {
                selectedCount++;
                total += parseFloat(paguValues[cb.value]) || 0;
                rekeningSelect.disabled = false;
                rekeningSelect.required = true;
            } else {
                rekeningSelect.disabled = true;
                rekeningSelect.required = false;
                rekeningSelect.value = '';
            }
        });
        
        totalPaguEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        submitBtn.disabled = selectedCount === 0;
    }
    
    selectAll?.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateUI();
    });
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateUI);
    });
});
</script>

<?= view('layout/footer') ?>
