<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="mb-1">
                    <i class="fas fa-user-plus me-2 text-primary"></i>
                    <?= isset($kader) ? 'Edit Kader' : 'Tambah Kader' ?>
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('/posyandu') ?>">e-Posyandu</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/posyandu/posyandu/detail/' . $posyandu['id']) ?>"><?= esc($posyandu['nama_posyandu']) ?></a></li>
                        <li class="breadcrumb-item active"><?= isset($kader) ? 'Edit Kader' : 'Tambah Kader' ?></li>
                    </ol>
                </nav>
            </div>

            <form action="<?= isset($kader) ? base_url('/posyandu/kader/update/' . $kader['id']) : base_url('/posyandu/kader/save') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="posyandu_id" value="<?= $posyandu['id'] ?>">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Data Kader</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kader <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kader" class="form-control form-control-lg" 
                                   value="<?= esc($kader['nama_kader'] ?? '') ?>" 
                                   placeholder="Nama lengkap kader" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link ke Data Penduduk (Opsional)</label>
                            <select name="penduduk_id" class="form-select" id="pendudukSelect">
                                <option value="">-- Tidak ditautkan --</option>
                                <?php foreach ($pendudukList as $p): ?>
                                    <option value="<?= $p['id'] ?>" 
                                            data-nama="<?= esc($p['nama_lengkap']) ?>"
                                            <?= isset($kader) && $kader['penduduk_id'] == $p['id'] ? 'selected' : '' ?>>
                                        <?= esc($p['nama_lengkap']) ?> - <?= esc($p['dusun']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Pilih jika kader terdaftar sebagai penduduk desa</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <select name="jabatan" class="form-select">
                                <option value="">-- Pilih Jabatan --</option>
                                <option value="Ketua" <?= isset($kader) && $kader['jabatan'] == 'Ketua' ? 'selected' : '' ?>>Ketua</option>
                                <option value="Sekretaris" <?= isset($kader) && $kader['jabatan'] == 'Sekretaris' ? 'selected' : '' ?>>Sekretaris</option>
                                <option value="Bendahara" <?= isset($kader) && $kader['jabatan'] == 'Bendahara' ? 'selected' : '' ?>>Bendahara</option>
                                <option value="Anggota" <?= isset($kader) && $kader['jabatan'] == 'Anggota' ? 'selected' : '' ?>>Anggota</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. Telepon/HP</label>
                            <input type="text" name="no_telp" class="form-control" 
                                   value="<?= esc($kader['no_telp'] ?? '') ?>" 
                                   placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="AKTIF" <?= isset($kader) && $kader['status'] == 'AKTIF' ? 'selected' : '' ?>>Aktif</option>
                                <option value="TIDAK_AKTIF" <?= isset($kader) && $kader['status'] == 'TIDAK_AKTIF' ? 'selected' : '' ?>>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('/posyandu/posyandu/detail/' . $posyandu['id']) ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-2"></i><?= isset($kader) ? 'Update' : 'Simpan' ?> Kader
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<script>
// Auto-fill nama kader when selecting penduduk
document.getElementById('pendudukSelect').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option.value) {
        const namaInput = document.querySelector('input[name="nama_kader"]');
        if (!namaInput.value) {
            namaInput.value = option.dataset.nama;
        }
    }
});
</script>
