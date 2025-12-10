-- ===========================================
-- SIKADES LITE - COMPLETE DATABASE SCHEMA
-- Creates all required tables
-- ===========================================

SET FOREIGN_KEY_CHECKS = 0;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'bendahara', 'operator', 'viewer') DEFAULT 'operator',
    kode_desa VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- APBDes table
CREATE TABLE IF NOT EXISTS apbdes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    tahun YEAR(4) NOT NULL,
    ref_rekening_id INT UNSIGNED,
    uraian VARCHAR(500) NOT NULL,
    anggaran DECIMAL(15,2) DEFAULT 0,
    realisasi DECIMAL(15,2) DEFAULT 0,
    sumber_dana VARCHAR(50),
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa),
    INDEX idx_tahun (tahun),
    INDEX idx_ref_rekening (ref_rekening_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SPP table
CREATE TABLE IF NOT EXISTS spp (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nomor_spp VARCHAR(50) NOT NULL,
    tanggal_spp DATE NOT NULL,
    uraian TEXT,
    jumlah DECIMAL(15,2) DEFAULT 0,
    status ENUM('Draft', 'Verified', 'Approved', 'Rejected') DEFAULT 'Draft',
    created_by INT UNSIGNED,
    verified_by INT UNSIGNED,
    approved_by INT UNSIGNED,
    rejected_by INT UNSIGNED,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SPP Rincian table
CREATE TABLE IF NOT EXISTS spp_rincian (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    spp_id INT UNSIGNED NOT NULL,
    ref_rekening_id INT UNSIGNED,
    uraian VARCHAR(500),
    jumlah DECIMAL(15,2) DEFAULT 0,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_spp_id (spp_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BKU table
CREATE TABLE IF NOT EXISTS bku (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    tanggal DATE NOT NULL,
    no_bukti VARCHAR(50),
    uraian TEXT,
    jenis_transaksi ENUM('Pendapatan', 'Belanja', 'Lainnya') DEFAULT 'Lainnya',
    ref_rekening_id INT UNSIGNED,
    debet DECIMAL(15,2) DEFAULT 0,
    kredit DECIMAL(15,2) DEFAULT 0,
    saldo_kumulatif DECIMAL(15,2) DEFAULT 0,
    spp_id INT UNSIGNED,
    tahun_anggaran YEAR(4),
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa),
    INDEX idx_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pajak table
CREATE TABLE IF NOT EXISTS pajak (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    bku_id INT UNSIGNED,
    jenis_pajak VARCHAR(50),
    nilai_dpp DECIMAL(15,2) DEFAULT 0,
    tarif DECIMAL(5,2) DEFAULT 0,
    nilai_pajak DECIMAL(15,2) DEFAULT 0,
    ntpn VARCHAR(50),
    tanggal_setor DATE,
    status ENUM('Belum', 'Sudah') DEFAULT 'Belum',
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- RPJM Desa table
CREATE TABLE IF NOT EXISTS rpjmdesa (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    tahun_awal YEAR(4) NOT NULL,
    tahun_akhir YEAR(4) NOT NULL,
    visi TEXT,
    misi TEXT,
    tujuan TEXT,
    sasaran TEXT,
    nomor_perdes VARCHAR(50),
    tanggal_perdes DATE,
    status VARCHAR(20) DEFAULT 'Draft',
    created_by INT UNSIGNED,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- RKP Desa table
CREATE TABLE IF NOT EXISTS rkpdesa (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rpjmdesa_id INT UNSIGNED,
    kode_desa VARCHAR(20) NOT NULL,
    tahun YEAR(4) NOT NULL,
    tema VARCHAR(255),
    prioritas TEXT,
    nomor_perdes VARCHAR(50),
    tanggal_perdes DATE,
    status VARCHAR(20) DEFAULT 'Draft',
    total_pagu DECIMAL(15,2) DEFAULT 0,
    created_by INT UNSIGNED,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa),
    INDEX idx_tahun (tahun)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kegiatan table
CREATE TABLE IF NOT EXISTS kegiatan (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rkpdesa_id INT UNSIGNED,
    kode_desa VARCHAR(20) NOT NULL,
    kode_bidang VARCHAR(10),
    nama_kegiatan VARCHAR(255) NOT NULL,
    lokasi VARCHAR(255),
    volume_target DECIMAL(10,2),
    satuan VARCHAR(50),
    anggaran DECIMAL(15,2) DEFAULT 0,
    sumber_dana VARCHAR(50),
    status VARCHAR(20) DEFAULT 'Rencana',
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PAK table
CREATE TABLE IF NOT EXISTS pak (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    tahun YEAR(4) NOT NULL,
    nomor_pak VARCHAR(50),
    tanggal_pak DATE,
    keterangan TEXT,
    status VARCHAR(20) DEFAULT 'Draft',
    created_by INT UNSIGNED,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PAK Detail table
CREATE TABLE IF NOT EXISTS pak_detail (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pak_id INT UNSIGNED NOT NULL,
    apbdes_id INT UNSIGNED,
    anggaran_semula DECIMAL(15,2) DEFAULT 0,
    anggaran_menjadi DECIMAL(15,2) DEFAULT 0,
    selisih DECIMAL(15,2) DEFAULT 0,
    alasan TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_pak_id (pak_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tutup Buku table  
CREATE TABLE IF NOT EXISTS tutup_buku (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    tahun YEAR(4) NOT NULL,
    status VARCHAR(20) DEFAULT 'Open',
    saldo_awal DECIMAL(15,2) DEFAULT 0,
    total_pendapatan DECIMAL(15,2) DEFAULT 0,
    total_belanja DECIMAL(15,2) DEFAULT 0,
    saldo_akhir DECIMAL(15,2) DEFAULT 0,
    tanggal_tutup DATE,
    closed_by INT UNSIGNED,
    catatan TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Aset Desa table
CREATE TABLE IF NOT EXISTS aset_desa (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    kode_aset VARCHAR(50),
    nama VARCHAR(255) NOT NULL,
    kategori VARCHAR(100),
    tahun_perolehan YEAR(4),
    nilai_perolehan DECIMAL(15,2) DEFAULT 0,
    kondisi VARCHAR(50),
    lokasi VARCHAR(255),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    foto VARCHAR(255),
    keterangan TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Penduduk table
CREATE TABLE IF NOT EXISTS penduduk (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nik VARCHAR(16) UNIQUE,
    nama VARCHAR(255) NOT NULL,
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    jenis_kelamin ENUM('L', 'P'),
    alamat TEXT,
    rt VARCHAR(10),
    rw VARCHAR(10),
    dusun VARCHAR(100),
    agama VARCHAR(50),
    status_perkawinan VARCHAR(50),
    pekerjaan VARCHAR(100),
    pendidikan VARCHAR(50),
    kewarganegaraan VARCHAR(50) DEFAULT 'WNI',
    foto VARCHAR(255),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa),
    INDEX idx_nik (nik),
    INDEX idx_dusun (dusun)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Keluarga table
CREATE TABLE IF NOT EXISTS keluarga (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    no_kk VARCHAR(16) UNIQUE,
    kepala_keluarga_id INT UNSIGNED,
    alamat TEXT,
    rt VARCHAR(10),
    rw VARCHAR(10),
    dusun VARCHAR(100),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa),
    INDEX idx_no_kk (no_kk)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- GIS Wilayah table
CREATE TABLE IF NOT EXISTS gis_wilayah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    type ENUM('desa', 'dusun', 'rt', 'rw') DEFAULT 'dusun',
    geojson LONGTEXT,
    center_lat DECIMAL(10,8),
    center_lng DECIMAL(11,8),
    luas DECIMAL(10,2),
    keterangan TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Posyandu table
CREATE TABLE IF NOT EXISTS posyandu (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    alamat TEXT,
    dusun VARCHAR(100),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kader Posyandu table
CREATE TABLE IF NOT EXISTS kader_posyandu (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    posyandu_id INT UNSIGNED NOT NULL,
    nama VARCHAR(255) NOT NULL,
    jabatan VARCHAR(100),
    no_hp VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_posyandu_id (posyandu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Balita table
CREATE TABLE IF NOT EXISTS balita (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    penduduk_id INT UNSIGNED,
    nama VARCHAR(255) NOT NULL,
    tanggal_lahir DATE,
    jenis_kelamin ENUM('L', 'P'),
    nama_ibu VARCHAR(255),
    nama_ayah VARCHAR(255),
    posyandu_id INT UNSIGNED,
    status_gizi VARCHAR(50),
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Penimbangan Balita table
CREATE TABLE IF NOT EXISTS penimbangan_balita (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    balita_id INT UNSIGNED NOT NULL,
    posyandu_id INT UNSIGNED,
    tanggal DATE NOT NULL,
    berat_badan DECIMAL(5,2),
    tinggi_badan DECIMAL(5,2),
    lingkar_kepala DECIMAL(5,2),
    status_gizi VARCHAR(50),
    keterangan TEXT,
    created_by INT UNSIGNED,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_balita_id (balita_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Imunisasi table
CREATE TABLE IF NOT EXISTS imunisasi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    balita_id INT UNSIGNED NOT NULL,
    jenis_imunisasi VARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
    posyandu_id INT UNSIGNED,
    keterangan TEXT,
    created_by INT UNSIGNED,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_balita_id (balita_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BUMDes table
CREATE TABLE IF NOT EXISTS bumdes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    nomor_sk VARCHAR(100),
    tanggal_sk DATE,
    direktur VARCHAR(255),
    alamat TEXT,
    modal_awal DECIMAL(15,2) DEFAULT 0,
    status VARCHAR(50) DEFAULT 'Aktif',
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BUMDes Unit Usaha
CREATE TABLE IF NOT EXISTS bumdes_unit (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bumdes_id INT UNSIGNED NOT NULL,
    nama VARCHAR(255) NOT NULL,
    jenis VARCHAR(100),
    manajer VARCHAR(255),
    status VARCHAR(50) DEFAULT 'Aktif',
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_bumdes_id (bumdes_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Proyek Pembangunan table
CREATE TABLE IF NOT EXISTS proyek_pembangunan (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    apbdes_id INT UNSIGNED,
    kode_kegiatan VARCHAR(50),
    nama_proyek VARCHAR(255) NOT NULL,
    lokasi_detail VARCHAR(500),
    volume_target DECIMAL(10,2),
    satuan VARCHAR(50),
    anggaran DECIMAL(15,2) DEFAULT 0,
    realisasi DECIMAL(15,2) DEFAULT 0,
    persentase_fisik DECIMAL(5,2) DEFAULT 0,
    persentase_keuangan DECIMAL(5,2) DEFAULT 0,
    tgl_mulai DATE,
    tgl_selesai_target DATE,
    tgl_selesai_aktual DATE,
    pelaksana_kegiatan VARCHAR(255),
    kontraktor VARCHAR(255),
    status ENUM('RENCANA', 'PROSES', 'SELESAI', 'MANGKRAK') DEFAULT 'RENCANA',
    foto_0 VARCHAR(255),
    foto_50 VARCHAR(255),
    foto_100 VARCHAR(255),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    keterangan TEXT,
    created_by INT UNSIGNED,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_kode_desa (kode_desa),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Progress Proyek table
CREATE TABLE IF NOT EXISTS progress_proyek (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    proyek_id INT UNSIGNED NOT NULL,
    tanggal_laporan DATE NOT NULL,
    persentase_fisik DECIMAL(5,2) DEFAULT 0,
    volume_terealisasi DECIMAL(10,2) DEFAULT 0,
    biaya_terealisasi DECIMAL(15,2) DEFAULT 0,
    kendala TEXT,
    solusi TEXT,
    foto VARCHAR(255),
    pelapor VARCHAR(255),
    created_by INT UNSIGNED,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_proyek_id (proyek_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Insert default admin user
INSERT IGNORE INTO users (nama, email, password, role, kode_desa, is_active, created_at, updated_at)
VALUES ('Administrator', 'admin@desa.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '3201010001', 1, NOW(), NOW());

SELECT 'All tables created successfully!' AS status;
