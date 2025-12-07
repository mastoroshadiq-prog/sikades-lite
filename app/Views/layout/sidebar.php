    <!-- Sidebar -->
    <div class="sidebar position-fixed" style="width: 250px; top: 56px; left: 0; overflow-y: auto; max-height: calc(100vh - 56px);">
        <nav class="nav flex-column py-3">
            <!-- Dashboard -->
            <a class="nav-link <?= (uri_string() == 'dashboard') ? 'active' : '' ?>" href="<?= base_url('/dashboard') ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            
            <!-- PERENCANAAN - Collapsible -->
            <?php 
            $perencanaanActive = strpos(uri_string(), 'perencanaan') !== false;
            ?>
            <div class="sidebar-section">
                <a class="nav-link section-toggle <?= $perencanaanActive ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#menuPerencanaan" role="button"
                   hx-boost="false">
                    <i class="fas fa-project-diagram"></i> 
                    <span>Perencanaan</span>
                    <i class="fas fa-chevron-down toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $perencanaanActive ? 'show' : '' ?>" id="menuPerencanaan">
                    <div class="submenu">
                        <a class="nav-link <?= (strpos(uri_string(), 'perencanaan') !== false && strpos(uri_string(), 'rpjm') === false && strpos(uri_string(), 'rkp') === false) ? 'active' : '' ?>" href="<?= base_url('/perencanaan') ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'perencanaan/rpjm') !== false) ? 'active' : '' ?>" href="<?= base_url('/perencanaan/rpjm') ?>">
                            <i class="fas fa-map"></i> RPJM Desa
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'perencanaan/rkp') !== false) ? 'active' : '' ?>" href="<?= base_url('/perencanaan/rkp') ?>">
                            <i class="fas fa-calendar-alt"></i> RKP Desa
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- PENGANGGARAN - Collapsible -->
            <?php 
            $penganggaranActive = strpos(uri_string(), 'apbdes') !== false || strpos(uri_string(), 'pak') !== false;
            ?>
            <div class="sidebar-section">
                <a class="nav-link section-toggle <?= $penganggaranActive ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#menuPenganggaran" role="button"
                   hx-boost="false">
                    <i class="fas fa-wallet"></i> 
                    <span>Penganggaran</span>
                    <i class="fas fa-chevron-down toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $penganggaranActive ? 'show' : '' ?>" id="menuPenganggaran">
                    <div class="submenu">
                        <a class="nav-link <?= (strpos(uri_string(), 'apbdes') !== false && strpos(uri_string(), 'pak') === false) ? 'active' : '' ?>" href="<?= base_url('/apbdes') ?>">
                            <i class="fas fa-file-invoice-dollar"></i> APBDes
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'pak') !== false) ? 'active' : '' ?>" href="<?= base_url('/pak') ?>">
                            <i class="fas fa-edit"></i> Perubahan (PAK)
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- PENATAUSAHAAN - Collapsible -->
            <?php 
            $penatausahaanActive = strpos(uri_string(), 'spp') !== false || 
                                   strpos(uri_string(), 'bku') !== false || 
                                   strpos(uri_string(), 'pajak') !== false ||
                                   strpos(uri_string(), 'tutup-buku') !== false;
            ?>
            <div class="sidebar-section">
                <a class="nav-link section-toggle <?= $penatausahaanActive ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#menuPenatausahaan" role="button"
                   hx-boost="false">
                    <i class="fas fa-cash-register"></i> 
                    <span>Penatausahaan</span>
                    <i class="fas fa-chevron-down toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $penatausahaanActive ? 'show' : '' ?>" id="menuPenatausahaan">
                    <div class="submenu">
                        <a class="nav-link <?= (strpos(uri_string(), 'spp') !== false) ? 'active' : '' ?>" href="<?= base_url('/spp') ?>">
                            <i class="fas fa-file-invoice"></i> SPP
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'bku') !== false && strpos(uri_string(), 'report') === false) ? 'active' : '' ?>" href="<?= base_url('/bku') ?>">
                            <i class="fas fa-book"></i> BKU
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'pajak') !== false && strpos(uri_string(), 'report') === false) ? 'active' : '' ?>" href="<?= base_url('/pajak') ?>">
                            <i class="fas fa-receipt"></i> Pajak
                        </a>
                        <?php if (isset($user['role']) && in_array($user['role'], ['Administrator', 'Operator Desa'])): ?>
                        <a class="nav-link <?= (strpos(uri_string(), 'tutup-buku') !== false) ? 'active' : '' ?>" href="<?= base_url('/tutup-buku') ?>">
                            <i class="fas fa-book-reader"></i> Tutup Buku
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- SIPADES - Sistem Pengelolaan Aset Desa -->
            <?php 
            $asetActive = strpos(uri_string(), 'aset') !== false;
            ?>
            <div class="sidebar-section">
                <a class="nav-link section-toggle <?= $asetActive ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#menuAset" role="button"
                   hx-boost="false">
                    <i class="fas fa-warehouse"></i> 
                    <span>SIPADES</span>
                    <i class="fas fa-chevron-down toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $asetActive ? 'show' : '' ?>" id="menuAset">
                    <div class="submenu">
                        <a class="nav-link <?= (uri_string() === 'aset' || uri_string() === 'aset/') ? 'active' : '' ?>" href="<?= base_url('/aset') ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard Aset
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'aset/list') !== false) ? 'active' : '' ?>" href="<?= base_url('/aset/list') ?>">
                            <i class="fas fa-list"></i> Daftar Inventaris
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'aset/create') !== false) ? 'active' : '' ?>" href="<?= base_url('/aset/create') ?>">
                            <i class="fas fa-plus"></i> Tambah Aset
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- LAPORAN - Collapsible -->
            <?php 
            $laporanActive = strpos(uri_string(), 'report') !== false || strpos(uri_string(), 'lpj') !== false;
            ?>
            <div class="sidebar-section">
                <a class="nav-link section-toggle <?= $laporanActive ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#menuLaporan" role="button"
                   hx-boost="false">
                    <i class="fas fa-chart-bar"></i> 
                    <span>Laporan</span>
                    <i class="fas fa-chevron-down toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $laporanActive ? 'show' : '' ?>" id="menuLaporan">
                    <div class="submenu">
                        <a class="nav-link <?= (uri_string() === 'report' || uri_string() === 'report/') ? 'active' : '' ?>" href="<?= base_url('/report') ?>">
                            <i class="fas fa-file-alt"></i> Semua Laporan
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'report/bku') !== false) ? 'active' : '' ?>" href="<?= base_url('/report/bku') ?>">
                            <i class="fas fa-book"></i> Laporan BKU
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'report/lra') !== false) ? 'active' : '' ?>" href="<?= base_url('/report/lra') ?>">
                            <i class="fas fa-chart-line"></i> Realisasi Anggaran
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'lpj') !== false) ? 'active' : '' ?>" href="<?= base_url('/lpj') ?>">
                            <i class="fas fa-file-signature"></i> Laporan LPJ
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- PENGATURAN - Collapsible (Admin/Operator only) -->
            <?php if (isset($user['role']) && in_array($user['role'], ['Administrator', 'Operator Desa'])): ?>
            <?php 
            $pengaturanActive = strpos(uri_string(), 'master') !== false || 
                                strpos(uri_string(), 'activity-log') !== false ||
                                strpos(uri_string(), 'backup') !== false;
            ?>
            <div class="sidebar-section">
                <a class="nav-link section-toggle <?= $pengaturanActive ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#menuPengaturan" role="button"
                   hx-boost="false">
                    <i class="fas fa-cog"></i> 
                    <span>Pengaturan</span>
                    <i class="fas fa-chevron-down toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $pengaturanActive ? 'show' : '' ?>" id="menuPengaturan">
                    <div class="submenu">
                        <a class="nav-link <?= (strpos(uri_string(), 'master/desa') !== false) ? 'active' : '' ?>" href="<?= base_url('/master/desa') ?>">
                            <i class="fas fa-building"></i> Data Desa
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'master/rekening') !== false) ? 'active' : '' ?>" href="<?= base_url('/master/rekening') ?>">
                            <i class="fas fa-list-alt"></i> Ref. Rekening
                        </a>
                        <?php if ($user['role'] == 'Administrator'): ?>
                        <a class="nav-link <?= (strpos(uri_string(), 'master/users') !== false) ? 'active' : '' ?>" href="<?= base_url('/master/users') ?>">
                            <i class="fas fa-users"></i> Manajemen User
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'activity-log') !== false) ? 'active' : '' ?>" href="<?= base_url('/activity-log') ?>">
                            <i class="fas fa-history"></i> Activity Log
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'backup') !== false) ? 'active' : '' ?>" href="<?= base_url('/backup') ?>">
                            <i class="fas fa-database"></i> Backup
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="content-wrapper">
