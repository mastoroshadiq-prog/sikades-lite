<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-book me-2 text-info"></i>Chart of Accounts (COA)
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes') ?>">BUMDes</a></li>
                    <li class="breadcrumb-item active">Chart of Accounts</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Chart of Accounts ini mengikuti standar <strong>SAK EMKM</strong> (Standar Akuntansi Keuangan Entitas Mikro, Kecil, dan Menengah).
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Akun</th>
                            <th>Tipe</th>
                            <th>Saldo Normal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $currentTipe = '';
                        foreach ($akunList as $a): 
                            $tipeColors = [
                                'ASET' => 'success',
                                'KEWAJIBAN' => 'warning',
                                'EKUITAS' => 'info',
                                'PENDAPATAN' => 'primary',
                                'BEBAN' => 'danger',
                            ];
                            $color = $tipeColors[$a['tipe']] ?? 'secondary';
                        ?>
                        <tr class="<?= $a['is_header'] ? 'table-light fw-bold' : '' ?>">
                            <td>
                                <?php if ($a['is_header']): ?>
                                <strong><?= esc($a['kode_akun']) ?></strong>
                                <?php else: ?>
                                <span class="ms-3"><?= esc($a['kode_akun']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($a['is_header']): ?>
                                <strong><?= esc($a['nama_akun']) ?></strong>
                                <?php else: ?>
                                <span class="ms-3"><?= esc($a['nama_akun']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $color ?>"><?= $a['tipe'] ?></span>
                            </td>
                            <td>
                                <?= $a['saldo_normal'] ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
