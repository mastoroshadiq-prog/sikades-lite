-- ===========================================
-- SIKADES LITE - SUPABASE POSTGRESQL SCHEMA
-- Version: 1.0.0
-- Date: 2025-12-10
-- ===========================================
-- Run this script in Supabase SQL Editor
-- Project Settings > SQL Editor > New Query
-- ===========================================

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ===========================================
-- 1. USERS TABLE
-- ===========================================
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'Operator Desa' CHECK (role IN ('Administrator', 'Operator Desa', 'Kepala Desa', 'Bendahara')),
    kode_desa VARCHAR(20),
    is_active BOOLEAN DEFAULT true,
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_users_kode_desa ON users(kode_desa);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);

-- ===========================================
-- 2. DATA UMUM DESA TABLE
-- ===========================================
CREATE TABLE IF NOT EXISTS data_umum_desa (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) UNIQUE NOT NULL,
    nama_desa VARCHAR(255) NOT NULL,
    kecamatan VARCHAR(255),
    kabupaten VARCHAR(255),
    provinsi VARCHAR(255),
    nama_kepala_desa VARCHAR(255),
    nip_kepala_desa VARCHAR(50),
    nama_bendahara VARCHAR(255),
    npwp VARCHAR(30),
    tahun_anggaran INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_desa_kode ON data_umum_desa(kode_desa);

-- ===========================================
-- 3. REF REKENING (Chart of Accounts)
-- ===========================================
CREATE TABLE IF NOT EXISTS ref_rekening (
    id SERIAL PRIMARY KEY,
    kode_akun VARCHAR(50) NOT NULL,
    nama_akun VARCHAR(255) NOT NULL,
    level SMALLINT CHECK (level BETWEEN 1 AND 4),
    parent_id INTEGER REFERENCES ref_rekening(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_ref_rekening_kode ON ref_rekening(kode_akun);
CREATE INDEX IF NOT EXISTS idx_ref_rekening_parent ON ref_rekening(parent_id);

-- ===========================================
-- 4. RPJM DESA (6-Year Plan)
-- ===========================================
CREATE TABLE IF NOT EXISTS rpjmdesa (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    tahun_awal INTEGER NOT NULL,
    tahun_akhir INTEGER NOT NULL,
    visi TEXT,
    misi TEXT,
    tujuan TEXT,
    sasaran TEXT,
    nomor_perdes VARCHAR(50),
    tanggal_perdes DATE,
    status VARCHAR(20) DEFAULT 'Draft',
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_rpjmdesa_kode_desa ON rpjmdesa(kode_desa);

-- ===========================================
-- 5. RKP DESA (Annual Plan)
-- ===========================================
CREATE TABLE IF NOT EXISTS rkpdesa (
    id SERIAL PRIMARY KEY,
    rpjmdesa_id INTEGER REFERENCES rpjmdesa(id) ON DELETE SET NULL,
    kode_desa VARCHAR(20) NOT NULL,
    tahun INTEGER NOT NULL,
    tema VARCHAR(255),
    prioritas TEXT,
    nomor_perdes VARCHAR(50),
    tanggal_perdes DATE,
    status VARCHAR(20) DEFAULT 'Draft',
    total_pagu DECIMAL(15,2) DEFAULT 0,
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_rkpdesa_kode_desa ON rkpdesa(kode_desa);
CREATE INDEX IF NOT EXISTS idx_rkpdesa_tahun ON rkpdesa(tahun);

-- ===========================================
-- 6. APBDes (Budget)
-- ===========================================
CREATE TABLE IF NOT EXISTS apbdes (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    tahun INTEGER NOT NULL,
    ref_rekening_id INTEGER REFERENCES ref_rekening(id),
    uraian VARCHAR(500) NOT NULL,
    anggaran DECIMAL(15,2) DEFAULT 0,
    realisasi DECIMAL(15,2) DEFAULT 0,
    sumber_dana VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_apbdes_kode_desa ON apbdes(kode_desa);
CREATE INDEX IF NOT EXISTS idx_apbdes_tahun ON apbdes(tahun);

-- ===========================================
-- 7. SPP (Payment Request)
-- ===========================================
CREATE TABLE IF NOT EXISTS spp (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nomor_spp VARCHAR(50) NOT NULL,
    tanggal_spp DATE NOT NULL,
    uraian TEXT,
    jumlah DECIMAL(15,2) DEFAULT 0,
    status VARCHAR(20) DEFAULT 'Draft' CHECK (status IN ('Draft', 'Verified', 'Approved', 'Rejected')),
    created_by INTEGER REFERENCES users(id),
    verified_by INTEGER REFERENCES users(id),
    approved_by INTEGER REFERENCES users(id),
    rejected_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_spp_kode_desa ON spp(kode_desa);
CREATE INDEX IF NOT EXISTS idx_spp_status ON spp(status);

-- ===========================================
-- 8. SPP RINCIAN
-- ===========================================
CREATE TABLE IF NOT EXISTS spp_rincian (
    id SERIAL PRIMARY KEY,
    spp_id INTEGER NOT NULL REFERENCES spp(id) ON DELETE CASCADE,
    ref_rekening_id INTEGER REFERENCES ref_rekening(id),
    uraian VARCHAR(500),
    jumlah DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_spp_rincian_spp ON spp_rincian(spp_id);

-- ===========================================
-- 9. BKU (Cash Book)
-- ===========================================
CREATE TABLE IF NOT EXISTS bku (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    tanggal DATE NOT NULL,
    no_bukti VARCHAR(50),
    uraian TEXT,
    jenis_transaksi VARCHAR(20) DEFAULT 'Lainnya' CHECK (jenis_transaksi IN ('Pendapatan', 'Belanja', 'Lainnya')),
    ref_rekening_id INTEGER REFERENCES ref_rekening(id),
    debet DECIMAL(15,2) DEFAULT 0,
    kredit DECIMAL(15,2) DEFAULT 0,
    saldo_kumulatif DECIMAL(15,2) DEFAULT 0,
    spp_id INTEGER REFERENCES spp(id),
    tahun_anggaran INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_bku_kode_desa ON bku(kode_desa);
CREATE INDEX IF NOT EXISTS idx_bku_tanggal ON bku(tanggal);

-- ===========================================
-- 10. PAJAK (Tax)
-- ===========================================
CREATE TABLE IF NOT EXISTS pajak (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    bku_id INTEGER REFERENCES bku(id),
    jenis_pajak VARCHAR(50),
    nilai_dpp DECIMAL(15,2) DEFAULT 0,
    tarif DECIMAL(5,2) DEFAULT 0,
    nilai_pajak DECIMAL(15,2) DEFAULT 0,
    ntpn VARCHAR(50),
    tanggal_setor DATE,
    status VARCHAR(20) DEFAULT 'Belum' CHECK (status IN ('Belum', 'Sudah')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_pajak_kode_desa ON pajak(kode_desa);

-- ===========================================
-- 11. PAK (Budget Amendment)
-- ===========================================
CREATE TABLE IF NOT EXISTS pak (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    tahun INTEGER NOT NULL,
    nomor_pak VARCHAR(50),
    tanggal_pak DATE,
    keterangan TEXT,
    status VARCHAR(20) DEFAULT 'Draft',
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_pak_kode_desa ON pak(kode_desa);

-- ===========================================
-- 12. PAK DETAIL
-- ===========================================
CREATE TABLE IF NOT EXISTS pak_detail (
    id SERIAL PRIMARY KEY,
    pak_id INTEGER NOT NULL REFERENCES pak(id) ON DELETE CASCADE,
    apbdes_id INTEGER REFERENCES apbdes(id),
    anggaran_semula DECIMAL(15,2) DEFAULT 0,
    anggaran_menjadi DECIMAL(15,2) DEFAULT 0,
    selisih DECIMAL(15,2) DEFAULT 0,
    alasan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_pak_detail_pak ON pak_detail(pak_id);

-- ===========================================
-- 13. TUTUP BUKU (Closing Book)
-- ===========================================
CREATE TABLE IF NOT EXISTS tutup_buku (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    tahun INTEGER NOT NULL,
    status VARCHAR(20) DEFAULT 'Open',
    saldo_awal DECIMAL(15,2) DEFAULT 0,
    total_pendapatan DECIMAL(15,2) DEFAULT 0,
    total_belanja DECIMAL(15,2) DEFAULT 0,
    saldo_akhir DECIMAL(15,2) DEFAULT 0,
    tanggal_tutup DATE,
    closed_by INTEGER REFERENCES users(id),
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_tutup_buku_kode_desa ON tutup_buku(kode_desa);

-- ===========================================
-- 14. KEGIATAN (Activities)
-- ===========================================
CREATE TABLE IF NOT EXISTS kegiatan (
    id SERIAL PRIMARY KEY,
    rkpdesa_id INTEGER REFERENCES rkpdesa(id),
    kode_desa VARCHAR(20) NOT NULL,
    kode_bidang VARCHAR(10),
    nama_kegiatan VARCHAR(255) NOT NULL,
    lokasi VARCHAR(255),
    volume_target DECIMAL(10,2),
    satuan VARCHAR(50),
    anggaran DECIMAL(15,2) DEFAULT 0,
    sumber_dana VARCHAR(50),
    status VARCHAR(20) DEFAULT 'Rencana',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_kegiatan_kode_desa ON kegiatan(kode_desa);

-- ===========================================
-- 15. ASET DESA (Village Assets)
-- ===========================================
CREATE TABLE IF NOT EXISTS aset_desa (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    kode_aset VARCHAR(50),
    nama VARCHAR(255) NOT NULL,
    kategori VARCHAR(100),
    tahun_perolehan INTEGER,
    nilai_perolehan DECIMAL(15,2) DEFAULT 0,
    kondisi VARCHAR(50),
    lokasi VARCHAR(255),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    foto VARCHAR(255),
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_aset_kode_desa ON aset_desa(kode_desa);

-- ===========================================
-- 16. PENDUDUK (Population)
-- ===========================================
CREATE TABLE IF NOT EXISTS penduduk (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nik VARCHAR(16) UNIQUE,
    nama VARCHAR(255) NOT NULL,
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    jenis_kelamin CHAR(1) CHECK (jenis_kelamin IN ('L', 'P')),
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_penduduk_kode_desa ON penduduk(kode_desa);
CREATE INDEX IF NOT EXISTS idx_penduduk_nik ON penduduk(nik);
CREATE INDEX IF NOT EXISTS idx_penduduk_dusun ON penduduk(dusun);

-- ===========================================
-- 17. KELUARGA (Family)
-- ===========================================
CREATE TABLE IF NOT EXISTS keluarga (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    no_kk VARCHAR(16) UNIQUE,
    kepala_keluarga_id INTEGER REFERENCES penduduk(id),
    alamat TEXT,
    rt VARCHAR(10),
    rw VARCHAR(10),
    dusun VARCHAR(100),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_keluarga_kode_desa ON keluarga(kode_desa);
CREATE INDEX IF NOT EXISTS idx_keluarga_no_kk ON keluarga(no_kk);

-- ===========================================
-- 18. GIS WILAYAH
-- ===========================================
CREATE TABLE IF NOT EXISTS gis_wilayah (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    type VARCHAR(20) DEFAULT 'dusun' CHECK (type IN ('desa', 'dusun', 'rt', 'rw')),
    geojson TEXT,
    center_lat DECIMAL(10,8),
    center_lng DECIMAL(11,8),
    luas DECIMAL(10,2),
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_gis_wilayah_kode_desa ON gis_wilayah(kode_desa);

-- ===========================================
-- 19. POSYANDU
-- ===========================================
CREATE TABLE IF NOT EXISTS posyandu (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    alamat TEXT,
    dusun VARCHAR(100),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_posyandu_kode_desa ON posyandu(kode_desa);

-- ===========================================
-- 20. KADER POSYANDU
-- ===========================================
CREATE TABLE IF NOT EXISTS kader_posyandu (
    id SERIAL PRIMARY KEY,
    posyandu_id INTEGER NOT NULL REFERENCES posyandu(id) ON DELETE CASCADE,
    nama VARCHAR(255) NOT NULL,
    jabatan VARCHAR(100),
    no_hp VARCHAR(20),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_kader_posyandu ON kader_posyandu(posyandu_id);

-- ===========================================
-- 21. BALITA
-- ===========================================
CREATE TABLE IF NOT EXISTS balita (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    penduduk_id INTEGER REFERENCES penduduk(id),
    nama VARCHAR(255) NOT NULL,
    tanggal_lahir DATE,
    jenis_kelamin CHAR(1) CHECK (jenis_kelamin IN ('L', 'P')),
    nama_ibu VARCHAR(255),
    nama_ayah VARCHAR(255),
    posyandu_id INTEGER REFERENCES posyandu(id),
    status_gizi VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_balita_kode_desa ON balita(kode_desa);

-- ===========================================
-- 22. PENIMBANGAN BALITA
-- ===========================================
CREATE TABLE IF NOT EXISTS penimbangan_balita (
    id SERIAL PRIMARY KEY,
    balita_id INTEGER NOT NULL REFERENCES balita(id) ON DELETE CASCADE,
    posyandu_id INTEGER REFERENCES posyandu(id),
    tanggal DATE NOT NULL,
    berat_badan DECIMAL(5,2),
    tinggi_badan DECIMAL(5,2),
    lingkar_kepala DECIMAL(5,2),
    status_gizi VARCHAR(50),
    keterangan TEXT,
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_penimbangan_balita ON penimbangan_balita(balita_id);

-- ===========================================
-- 23. IMUNISASI
-- ===========================================
CREATE TABLE IF NOT EXISTS imunisasi (
    id SERIAL PRIMARY KEY,
    balita_id INTEGER NOT NULL REFERENCES balita(id) ON DELETE CASCADE,
    jenis_imunisasi VARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
    posyandu_id INTEGER REFERENCES posyandu(id),
    keterangan TEXT,
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_imunisasi_balita ON imunisasi(balita_id);

-- ===========================================
-- 24. BUMDES
-- ===========================================
CREATE TABLE IF NOT EXISTS bumdes (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    nomor_sk VARCHAR(100),
    tanggal_sk DATE,
    direktur VARCHAR(255),
    alamat TEXT,
    modal_awal DECIMAL(15,2) DEFAULT 0,
    status VARCHAR(50) DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_bumdes_kode_desa ON bumdes(kode_desa);

-- ===========================================
-- 25. BUMDES UNIT
-- ===========================================
CREATE TABLE IF NOT EXISTS bumdes_unit (
    id SERIAL PRIMARY KEY,
    bumdes_id INTEGER NOT NULL REFERENCES bumdes(id) ON DELETE CASCADE,
    nama VARCHAR(255) NOT NULL,
    jenis VARCHAR(100),
    manajer VARCHAR(255),
    status VARCHAR(50) DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_bumdes_unit ON bumdes_unit(bumdes_id);

-- ===========================================
-- 26. PROYEK PEMBANGUNAN
-- ===========================================
CREATE TABLE IF NOT EXISTS proyek_pembangunan (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    apbdes_id INTEGER REFERENCES apbdes(id),
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
    status VARCHAR(20) DEFAULT 'RENCANA' CHECK (status IN ('RENCANA', 'PROSES', 'SELESAI', 'MANGKRAK')),
    foto_0 VARCHAR(255),
    foto_50 VARCHAR(255),
    foto_100 VARCHAR(255),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    keterangan TEXT,
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_proyek_kode_desa ON proyek_pembangunan(kode_desa);
CREATE INDEX IF NOT EXISTS idx_proyek_status ON proyek_pembangunan(status);

-- ===========================================
-- 27. PROGRESS PROYEK
-- ===========================================
CREATE TABLE IF NOT EXISTS progress_proyek (
    id SERIAL PRIMARY KEY,
    proyek_id INTEGER NOT NULL REFERENCES proyek_pembangunan(id) ON DELETE CASCADE,
    tanggal_laporan DATE NOT NULL,
    persentase_fisik DECIMAL(5,2) DEFAULT 0,
    volume_terealisasi DECIMAL(10,2) DEFAULT 0,
    biaya_terealisasi DECIMAL(15,2) DEFAULT 0,
    kendala TEXT,
    solusi TEXT,
    foto VARCHAR(255),
    pelapor VARCHAR(255),
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_progress_proyek ON progress_proyek(proyek_id);

-- ===========================================
-- 28. ACTIVITY LOGS
-- ===========================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id),
    kode_desa VARCHAR(20),
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT,
    data_before JSONB,
    data_after JSONB,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_activity_user ON activity_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_activity_kode_desa ON activity_logs(kode_desa);
CREATE INDEX IF NOT EXISTS idx_activity_module ON activity_logs(module);
CREATE INDEX IF NOT EXISTS idx_activity_created ON activity_logs(created_at);

-- ===========================================
-- 29. MIGRATIONS (for CodeIgniter)
-- ===========================================
CREATE TABLE IF NOT EXISTS migrations (
    id SERIAL PRIMARY KEY,
    version VARCHAR(255) NOT NULL,
    class VARCHAR(255) NOT NULL,
    "group" VARCHAR(255) NOT NULL,
    namespace VARCHAR(255) NOT NULL,
    time INTEGER NOT NULL,
    batch INTEGER NOT NULL
);

-- ===========================================
-- SUCCESS MESSAGE
-- ===========================================
SELECT 'All tables created successfully!' AS status;
