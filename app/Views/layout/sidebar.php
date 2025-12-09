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
                        <a class="nav-link <?= (strpos(uri_string(), 'gis') !== false) ? 'active' : '' ?>" href="<?= base_url('/gis') ?>">
                            <i class="fas fa-map-marked-alt"></i> WebGIS
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- DEMOGRAFI - Sistem Data Kependudukan -->
            <?php 
            $demografiActive = strpos(uri_string(), 'demografi') !== false;
            ?>
            <div class="sidebar-section">
                <a class="nav-link section-toggle <?= $demografiActive ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#menuDemografi" role="button"
                   hx-boost="false">
                    <i class="fas fa-users"></i> 
                    <span>Demografi</span>
                    <i class="fas fa-chevron-down toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $demografiActive ? 'show' : '' ?>" id="menuDemografi">
                    <div class="submenu">
                        <a class="nav-link <?= (uri_string() === 'demografi' || uri_string() === 'demografi/') ? 'active' : '' ?>" href="<?= base_url('/demografi') ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'demografi/keluarga') !== false) ? 'active' : '' ?>" href="<?= base_url('/demografi/keluarga') ?>">
                            <i class="fas fa-home"></i> Kartu Keluarga
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'demografi/penduduk') !== false) ? 'active' : '' ?>" href="<?= base_url('/demografi/penduduk') ?>">
                            <i class="fas fa-user"></i> Data Penduduk
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'demografi/mutasi') !== false) ? 'active' : '' ?>" href="<?= base_url('/demografi/mutasi') ?>">
                            <i class="fas fa-exchange-alt"></i> Mutasi
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'demografi/import') !== false) ? 'active' : '' ?>" href="<?= base_url('/demografi/import') ?>">
                            <i class="fas fa-file-import"></i> Import Data
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- BUMDES - Sistem Akuntansi BUMDes -->
            <?php 
            $bumdesActive = strpos(uri_string(), 'bumdes') !== false;
            ?>
            <div class="sidebar-section">
                <a class="nav-link section-toggle <?= $bumdesActive ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#menuBumdes" role="button"
                   hx-boost="false">
                    <i class="fas fa-store"></i> 
                    <span>BUMDes</span>
                    <i class="fas fa-chevron-down toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $bumdesActive ? 'show' : '' ?>" id="menuBumdes">
                    <div class="submenu">
                        <a class="nav-link <?= (uri_string() === 'bumdes' || uri_string() === 'bumdes/') ? 'active' : '' ?>" href="<?= base_url('/bumdes') ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'bumdes/unit') !== false) ? 'active' : '' ?>" href="<?= base_url('/bumdes/unit') ?>">
                            <i class="fas fa-building"></i> Unit Usaha
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- e-POSYANDU - Kesehatan Masyarakat -->
            <?php 
            $posyanduActive = strpos(uri_string(), 'posyandu') !== false;
            ?>
            <div class="sidebar-section">
                <a class="nav-link section-toggle <?= $posyanduActive ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#menuPosyandu" role="button"
                   hx-boost="false">
                    <i class="fas fa-heartbeat"></i> 
                    <span>e-Posyandu</span>
                    <i class="fas fa-chevron-down toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $posyanduActive ? 'show' : '' ?>" id="menuPosyandu">
                    <div class="submenu">
                        <a class="nav-link <?= (uri_string() === 'posyandu' || uri_string() === 'posyandu/') ? 'active' : '' ?>" href="<?= base_url('/posyandu') ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'posyandu/posyandu') !== false) ? 'active' : '' ?>" href="<?= base_url('/posyandu/posyandu') ?>">
                            <i class="fas fa-clinic-medical"></i> Data Posyandu
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'posyandu/stunting') !== false) ? 'active' : '' ?>" href="<?= base_url('/posyandu/stunting') ?>">
                            <i class="fas fa-child"></i> Stunting
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'posyandu/bumil') !== false) ? 'active' : '' ?>" href="<?= base_url('/posyandu/bumil/risti') ?>">
                            <i class="fas fa-user-nurse"></i> Bumil Risti
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- e-PEMBANGUNAN - Infrastructure Monitoring -->
            <?php 
            $pembangunanActive = strpos(uri_string(), 'pembangunan') !== false;
            ?>
            <div class="sidebar-section">
                <a class="nav-link section-toggle <?= $pembangunanActive ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#menuPembangunan" role="button"
                   hx-boost="false">
                    <i class="fas fa-hard-hat"></i> 
                    <span>e-Pembangunan</span>
                    <i class="fas fa-chevron-down toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $pembangunanActive ? 'show' : '' ?>" id="menuPembangunan">
                    <div class="submenu">
                        <a class="nav-link <?= (uri_string() === 'pembangunan' || uri_string() === 'pembangunan/') ? 'active' : '' ?>" href="<?= base_url('/pembangunan') ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'pembangunan/proyek') !== false) ? 'active' : '' ?>" href="<?= base_url('/pembangunan/proyek') ?>">
                            <i class="fas fa-project-diagram"></i> Daftar Proyek
                        </a>
                        <a class="nav-link <?= (strpos(uri_string(), 'pembangunan/monitoring') !== false) ? 'active' : '' ?>" href="<?= base_url('/pembangunan/monitoring') ?>">
                            <i class="fas fa-exclamation-triangle"></i> Monitoring Deviasi
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
