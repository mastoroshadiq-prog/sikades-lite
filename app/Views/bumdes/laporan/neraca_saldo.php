<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-list-ol me-2 text-info"></i>Neraca Saldo
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes') ?>">BUMDes</a></li>
                    <li class="breadcrumb-item"><?= esc($unit['nama_unit']) ?></li>
                    <li class="breadcrumb-item active">Neraca Saldo</li>
                </ol>
            </nav>
        </div>
        <div>
            <form method="GET" class="d-flex gap-2">
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                    <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 text-center">
            <h4 class="mb-0"><?= esc($unit['nama_unit']) ?></h4>
            <p class="text-muted mb-0">Neraca Saldo Tahun <?= $tahun ?></p>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Akun</th>
                            <th>Tipe</th>
                            <th class="text-end">Debet</th>
                            <th class="text-end">Kredit</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalDebet = 0;
                        $totalKredit = 0;
                        $tipeColors = [
                            'ASET' => 'success',
                            'KEWAJIBAN' => 'warning',
                            'EKUITAS' => 'info',
                            'PENDAPATAN' => 'primary',
                            'BEBAN' => 'danger',
                        ];
                        
                        foreach ($trialBalance as $row):
                            $totalDebet += $row['total_debet'];
                            $totalKredit += $row['total_kredit'];
                            $color = $tipeColors[$row['tipe']] ?? 'secondary';
                        ?>
                        <tr>
                            <td><?= esc($row['kode_akun']) ?></td>
                            <td><?= esc($row['nama_akun']) ?></td>
                            <td><span class="badge bg-<?= $color ?>"><?= $row['tipe'] ?></span></td>
                            <td class="text-end"><?= $row['total_debet'] > 0 ? number_format($row['total_debet'], 0, ',', '.') : '-' ?></td>
                            <td class="text-end"><?= $row['total_kredit'] > 0 ? number_format($row['total_kredit'], 0, ',', '.') : '-' ?></td>
                            <td class="text-end <?= $row['saldo'] < 0 ? 'text-danger' : '' ?>">
                                <?= number_format(abs($row['saldo']), 0, ',', '.') ?>
                                <?= $row['saldo'] < 0 ? '(Cr)' : '' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="3">TOTAL</td>
                            <td class="text-end"><?= number_format($totalDebet, 0, ',', '.') ?></td>
                            <td class="text-end"><?= number_format($totalKredit, 0, ',', '.') ?></td>
                            <td class="text-end">
                                <?php if (abs($totalDebet - $totalKredit) < 0.01): ?>
                                <span class="badge bg-success"><i class="fas fa-check"></i> Balance</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Selisih: <?= number_format(abs($totalDebet - $totalKredit), 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-center py-3">
            <small class="text-muted">Dicetak pada: <?= date('d/m/Y H:i') ?></small>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
