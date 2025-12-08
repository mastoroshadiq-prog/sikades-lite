<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-chart-line me-2 text-success"></i>Laporan Laba Rugi
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes') ?>">BUMDes</a></li>
                    <li class="breadcrumb-item"><?= esc($unit['nama_unit']) ?></li>
                    <li class="breadcrumb-item active">Laba Rugi</li>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 text-center">
                    <h4 class="mb-0"><?= esc($unit['nama_unit']) ?></h4>
                    <p class="text-muted mb-0">Laporan Laba Rugi Periode <?= $tahun ?></p>
                </div>
                <div class="card-body">
                    <!-- Pendapatan -->
                    <h5 class="text-success border-bottom pb-2 mb-3">
                        <i class="fas fa-arrow-down me-2"></i>PENDAPATAN
                    </h5>
                    <table class="table table-sm mb-4">
                        <?php foreach ($labaRugi['pendapatan'] as $p): ?>
                        <tr>
                            <td class="ps-4"><?= esc($p['kode_akun']) ?> - <?= esc($p['nama_akun']) ?></td>
                            <td class="text-end">Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-success fw-bold">
                            <td>Total Pendapatan</td>
                            <td class="text-end">Rp <?= number_format($labaRugi['total_pendapatan'], 0, ',', '.') ?></td>
                        </tr>
                    </table>
                    
                    <!-- Beban -->
                    <h5 class="text-danger border-bottom pb-2 mb-3">
                        <i class="fas fa-arrow-up me-2"></i>BEBAN
                    </h5>
                    <table class="table table-sm mb-4">
                        <?php foreach ($labaRugi['beban'] as $b): ?>
                        <tr>
                            <td class="ps-4"><?= esc($b['kode_akun']) ?> - <?= esc($b['nama_akun']) ?></td>
                            <td class="text-end">Rp <?= number_format($b['jumlah'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-danger fw-bold">
                            <td>Total Beban</td>
                            <td class="text-end">Rp <?= number_format($labaRugi['total_beban'], 0, ',', '.') ?></td>
                        </tr>
                    </table>
                    
                    <hr class="my-4">
                    
                    <!-- Laba/Rugi -->
                    <table class="table table-lg">
                        <tr class="<?= $labaRugi['laba_rugi'] >= 0 ? 'table-success' : 'table-danger' ?> fs-5">
                            <td class="fw-bold">
                                <?= $labaRugi['laba_rugi'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' ?>
                            </td>
                            <td class="text-end fw-bold">
                                Rp <?= number_format(abs($labaRugi['laba_rugi']), 0, ',', '.') ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <small class="text-muted">Dicetak pada: <?= date('d/m/Y H:i') ?></small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Summary Card -->
            <div class="card border-0 shadow-sm mb-4 <?= $labaRugi['laba_rugi'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
                <div class="card-body text-center py-4">
                    <i class="fas fa-<?= $labaRugi['laba_rugi'] >= 0 ? 'arrow-up' : 'arrow-down' ?> fa-3x mb-3"></i>
                    <h5><?= $labaRugi['laba_rugi'] >= 0 ? 'LABA' : 'RUGI' ?></h5>
                    <h2>Rp <?= number_format(abs($labaRugi['laba_rugi']), 0, ',', '.') ?></h2>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">Laporan Lainnya</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= base_url('/bumdes/laporan/neraca/' . $unit['id']) ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-balance-scale me-2 text-primary"></i>Neraca
                    </a>
                    <a href="<?= base_url('/bumdes/laporan/neraca-saldo/' . $unit['id']) ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-list-ol me-2 text-info"></i>Neraca Saldo
                    </a>
                    <a href="<?= base_url('/bumdes/jurnal/' . $unit['id']) ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-book me-2 text-success"></i>Jurnal Umum
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>
