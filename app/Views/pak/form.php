<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/pak') ?>">PAK</a></li>
            <li class="breadcrumb-item active">Buat PAK Baru</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-edit me-2 text-warning"></i>Buat Perubahan Anggaran (PAK)
            </h2>
            <p class="text-muted mb-0">Tahun Anggaran <?= $tahun ?></p>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('/pak/save') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="tahun" value="<?= $tahun ?>">

        <!-- Header Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Informasi PAK</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Nomor PAK <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_pak" class="form-control" 
                                   value="<?= old('nomor_pak', $nomorPak) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Tanggal PAK <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pak" class="form-control" 
                                   value="<?= old('tanggal_pak', date('Y-m-d')) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Tahun Anggaran</label>
                            <input type="text" class="form-control" value="<?= $tahun ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2"
                              placeholder="Alasan perubahan anggaran..."><?= old('keterangan') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Detail Perubahan -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detail Perubahan Anggaran</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Ubah nilai anggaran yang ingin direvisi. Item yang tidak diubah tidak akan disimpan.
                </p>
                
                <?php if (empty($anggaran)): ?>
                <div class="alert alert-warning">
                    Tidak ada data APBDes untuk tahun <?= $tahun ?>. Silakan input APBDes terlebih dahulu.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="pakTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Kode Rekening</th>
                                <th width="25%">Uraian</th>
                                <th width="10%">Sumber Dana</th>
                                <th width="15%">Anggaran Semula</th>
                                <th width="15%">Anggaran Menjadi</th>
                                <th width="18%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anggaran as $idx => $item): ?>
                            <tr>
                                <td class="text-center"><?= $idx + 1 ?></td>
                                <td><code><?= esc($item['kode_akun']) ?></code></td>
                                <td><?= esc($item['uraian'] ?? $item['nama_akun']) ?></td>
                                <td>
                                    <span class="badge bg-<?= ['DDS' => 'success', 'ADD' => 'primary', 'PAD' => 'info', 'Bankeu' => 'warning'][$item['sumber_dana']] ?? 'secondary' ?>">
                                        <?= esc($item['sumber_dana']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <input type="hidden" name="apbdes_id[]" value="<?= $item['id'] ?>">
                                    <span class="anggaran-semula">Rp <?= number_format($item['anggaran'], 0, ',', '.') ?></span>
                                    <input type="hidden" name="anggaran_sebelum[]" value="<?= $item['anggaran'] ?>">
                                </td>
                                <td>
                                    <input type="number" name="anggaran_sesudah[]" 
                                           class="form-control form-control-sm text-end anggaran-input"
                                           value="<?= $item['anggaran'] ?>" min="0" step="1"
                                           data-original="<?= $item['anggaran'] ?>">
                                </td>
                                <td>
                                    <input type="text" name="keterangan_item[]" 
                                           class="form-control form-control-sm"
                                           placeholder="Alasan perubahan...">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Submit -->
        <div class="d-flex justify-content-between">
            <a href="<?= base_url('/pak') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Batal
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i>Simpan PAK
            </button>
        </div>
    </form>
</div>

<style>
.anggaran-input:not([data-changed="true"]) {
    background-color: #fff;
}
.anggaran-input[data-changed="true"] {
    background-color: #fff3cd !important;
    border-color: #ffc107 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.anggaran-input');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            const original = parseFloat(this.dataset.original);
            const current = parseFloat(this.value) || 0;
            
            if (current !== original) {
                this.dataset.changed = 'true';
            } else {
                this.dataset.changed = 'false';
            }
        });
    });
});
</script>

<?= view('layout/footer') ?>
