-- ===========================================
-- SIKADES LITE - KESEHATAN (POSYANDU) TABLES
-- Migration: 14-kesehatan-posyandu.sql
-- Date: 2025-12-25
-- ===========================================
-- Tables for e-Posyandu module: Stunting monitoring, pregnancy tracking

-- Drop existing tables to ensure clean slate (in case of malformed previous tables)
DROP TABLE IF EXISTS kes_standar_who CASCADE;
DROP TABLE IF EXISTS kes_ibu_hamil CASCADE;
DROP TABLE IF EXISTS kes_pemeriksaan CASCADE;
DROP TABLE IF EXISTS kes_kader CASCADE;
DROP TABLE IF EXISTS kes_posyandu CASCADE;

-- ===========================================
-- 1. POSYANDU (Health Post)
-- ===========================================
CREATE TABLE IF NOT EXISTS kes_posyandu (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nama_posyandu VARCHAR(100) NOT NULL,
    alamat_dusun VARCHAR(100),
    rt VARCHAR(5),
    rw VARCHAR(5),
    ketua_posyandu VARCHAR(100),
    no_telp VARCHAR(20),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_kes_posyandu_kode_desa ON kes_posyandu(kode_desa);

-- ===========================================
-- 2. KADER (Health Cadre)
-- ===========================================
CREATE TABLE IF NOT EXISTS kes_kader (
    id SERIAL PRIMARY KEY,
    posyandu_id INTEGER NOT NULL REFERENCES kes_posyandu(id) ON DELETE CASCADE,
    penduduk_id INTEGER REFERENCES pop_penduduk(id),
    nama_kader VARCHAR(100) NOT NULL,
    jabatan VARCHAR(50),
    no_telp VARCHAR(20),
    status VARCHAR(20) DEFAULT 'AKTIF' CHECK (status IN ('AKTIF', 'TIDAK_AKTIF')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_kes_kader_posyandu ON kes_kader(posyandu_id);

-- ===========================================
-- 3. PEMERIKSAAN BALITA (Child Health Check)
-- ===========================================
CREATE TABLE IF NOT EXISTS kes_pemeriksaan (
    id SERIAL PRIMARY KEY,
    posyandu_id INTEGER NOT NULL REFERENCES kes_posyandu(id),
    penduduk_id INTEGER NOT NULL REFERENCES pop_penduduk(id),
    tanggal_periksa DATE NOT NULL,
    usia_bulan INTEGER NOT NULL,
    berat_badan DECIMAL(5,2) NOT NULL,
    tinggi_badan DECIMAL(5,2) NOT NULL,
    lingkar_kepala DECIMAL(5,2),
    lingkar_lengan DECIMAL(5,2),
    vitamin_a BOOLEAN DEFAULT FALSE,
    imunisasi VARCHAR(255),
    asi_eksklusif BOOLEAN DEFAULT FALSE,
    status_gizi VARCHAR(20) DEFAULT 'BAIK' CHECK (status_gizi IN ('BURUK', 'KURANG', 'BAIK', 'LEBIH', 'OBESITAS')),
    z_score_bb_u DECIMAL(4,2),
    z_score_tb_u DECIMAL(4,2),
    z_score_bb_tb DECIMAL(4,2),
    indikasi_stunting BOOLEAN DEFAULT FALSE,
    indikasi_gizi_buruk BOOLEAN DEFAULT FALSE,
    keterangan TEXT,
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_kes_pemeriksaan_posyandu ON kes_pemeriksaan(posyandu_id);
CREATE INDEX IF NOT EXISTS idx_kes_pemeriksaan_penduduk ON kes_pemeriksaan(penduduk_id);
CREATE INDEX IF NOT EXISTS idx_kes_pemeriksaan_stunting ON kes_pemeriksaan(indikasi_stunting);
CREATE INDEX IF NOT EXISTS idx_kes_pemeriksaan_tanggal ON kes_pemeriksaan(tanggal_periksa);

-- ===========================================
-- 4. IBU HAMIL (Pregnant Mother)
-- ===========================================
CREATE TABLE IF NOT EXISTS kes_ibu_hamil (
    id SERIAL PRIMARY KEY,
    posyandu_id INTEGER NOT NULL REFERENCES kes_posyandu(id),
    penduduk_id INTEGER NOT NULL REFERENCES pop_penduduk(id),
    tanggal_hpht DATE,
    taksiran_persalinan DATE,
    usia_kandungan INTEGER,
    kehamilan_ke INTEGER DEFAULT 1,
    tinggi_badan_ibu DECIMAL(5,2),
    berat_badan_sebelum DECIMAL(5,2),
    golongan_darah VARCHAR(5),
    resiko_tinggi BOOLEAN DEFAULT FALSE,
    faktor_resiko TEXT,
    pemeriksaan_k1 DATE,
    pemeriksaan_k2 DATE,
    pemeriksaan_k3 DATE,
    pemeriksaan_k4 DATE,
    status VARCHAR(20) DEFAULT 'HAMIL' CHECK (status IN ('HAMIL', 'MELAHIRKAN', 'KEGUGURAN', 'BATAL')),
    tanggal_persalinan DATE,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_kes_ibu_hamil_posyandu ON kes_ibu_hamil(posyandu_id);
CREATE INDEX IF NOT EXISTS idx_kes_ibu_hamil_penduduk ON kes_ibu_hamil(penduduk_id);
CREATE INDEX IF NOT EXISTS idx_kes_ibu_hamil_status ON kes_ibu_hamil(status);
CREATE INDEX IF NOT EXISTS idx_kes_ibu_hamil_risti ON kes_ibu_hamil(resiko_tinggi);

-- ===========================================
-- 5. STANDAR WHO (WHO Growth Standards)
-- ===========================================
CREATE TABLE IF NOT EXISTS kes_standar_who (
    id SERIAL PRIMARY KEY,
    jenis_kelamin CHAR(1) NOT NULL CHECK (jenis_kelamin IN ('L', 'P')),
    usia_bulan INTEGER NOT NULL,
    indikator VARCHAR(20) NOT NULL,
    median DECIMAL(6,2) NOT NULL,
    sd_min3 DECIMAL(6,2) NOT NULL,
    sd_min2 DECIMAL(6,2) NOT NULL,
    sd_min1 DECIMAL(6,2) NOT NULL,
    sd_plus1 DECIMAL(6,2) NOT NULL,
    sd_plus2 DECIMAL(6,2) NOT NULL,
    sd_plus3 DECIMAL(6,2) NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_kes_standar_who_lookup ON kes_standar_who(jenis_kelamin, usia_bulan, indikator);

-- ===========================================
-- SAMPLE DATA
-- ===========================================
-- Insert sample posyandu (Only if clean slate)
INSERT INTO kes_posyandu (kode_desa, nama_posyandu, alamat_dusun, rt, rw, ketua_posyandu, lat, lng)
VALUES 
    ('3201010001', 'Posyandu Mawar', 'Dusun Krajan', '01', '01', 'Bu Siti Aminah', -6.5770, 106.8460),
    ('3201010001', 'Posyandu Melati', 'Dusun Sukamaju', '02', '01', 'Bu Dewi Lestari', -6.5795, 106.8478);

SELECT 'Kesehatan/Posyandu tables (re)created successfully!' AS status;
