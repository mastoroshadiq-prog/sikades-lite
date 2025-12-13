<?= view('layout/htmx_layout_start', get_defined_vars()) ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-file-invoice-dollar text-primary"></i> Pajak - Pencatatan Pajak</h2>
        <p class="text-muted mb-0">Pencatatan PPN dan PPh</p>
    </div>
    <?php if (in_array($user['role'], ['Administrator', 'Operator Desa'])): ?>
    <div>
        <a href="<?= base_url('/pajak/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Pajak
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-1">Total PPN</h6>
                <h4 class="mb-0">Rp <?= number_format($total_ppn, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-1">Total PPh</h6>
                <h4 class="mb-0">Rp <?= number_format($total_pph, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-1">Belum Bayar</h6>
                <h4 class="mb-0">Rp <?= number_format($total_belum_bayar, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="text-muted mb-2">Tahun:</h6>
                <select class="form-select form-select-sm" onchange="window.location.href='<?= base_url('/pajak') ?>?tahun='+this.value">
                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Pajak Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Pajak</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Tanggal</th>
                        <th width="12%">No. Bukti BKU</th>
                        <th width="25%">Uraian</th>
                        <th width="8%">Jenis</th>
                        <th width="8%">Tarif</th>
                        <th width="12%" class="text-end">Jumlah</th>
                        <th width="10%">Status</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pajak_list)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fs-2 mb-2 d-block"></i>
                                Belum ada data pajak
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($pajak_list as $pajak): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($pajak['tanggal'])) ?></td>
                            <td><small><?= esc($pajak['no_bukti']) ?></small></td>
                            <td><?= esc($pajak['uraian']) ?></td>
                            <td>
                                <span class="badge <?= $pajak['jenis_pajak'] == 'PPN' ? 'bg-info' : 'bg-success' ?>">
                                    <?= $pajak['jenis_pajak'] ?>
                                </span>
                            </td>
                            <td><?= number_format($pajak['tarif'], 1) ?>%</td>
                            <td class="text-end"><strong>Rp <?= number_format($pajak['jumlah_pajak'], 0, ',', '.') ?></strong></td>
                            <td>
                                <span class="badge <?= $pajak['status_pembayaran'] == 'Sudah' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $pajak['status_pembayaran'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if (in_array($user['role'], ['Administrator', 'Operator Desa'])): ?>
                                    <a href="<?= base_url('/pajak/edit/' . $pajak['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($pajak['status_pembayaran'] == 'Belum'): ?>
                                    <button type="button" class="btn btn-outline-success" 
                                            onclick="bayarPajak(<?= $pajak['id'] ?>)"
                                            title="Bayar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($user['role'] == 'Administrator'): ?>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="confirmDelete('<?= base_url('/pajak/delete/' . $pajak['id']) ?>', 'pajak')"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function bayarPajak(id) {
    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: 'Tandai pajak sebagai sudah dibayar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Sudah Bayar',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('/pajak/bayar/') ?>' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal memproses pembayaran', 'error');
            });
        }
    });
}
</script>

<?= view('layout/htmx_layout_end', get_defined_vars()) ?>
