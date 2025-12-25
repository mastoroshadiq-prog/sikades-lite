-- ===========================================
-- SIKADES LITE - PEMBANGUNAN (E-PEMBANGUNAN) TABLES
-- Migration: 18-pembangunan-tables.sql
-- Date: 2025-12-25
-- ===========================================
-- Tabel untuk modul e-Pembangunan: Monitoring proyek fisik desa

-- Drop existing tables
DROP TABLE IF EXISTS proyek_log CASCADE;
DROP TABLE IF EXISTS proyek CASCADE;

-- ===========================================
-- 1. TABEL PROYEK (Master Table)
-- ===========================================
CREATE TABLE proyek (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    apbdes_id INTEGER, -- FK to apbdes (optional, will be set later)
    rkpdesa_id INTEGER, -- FK to rkpdesa (optional, table may not exist yet)
    kegiatan_id INTEGER, -- FK to rkpdesa_kegiatan (optional, table may not exist yet)
    
    -- Identitas Proyek
    nama_proyek VARCHAR(255) NOT NULL,
    jenis_proyek VARCHAR(50) DEFAULT 'FISIK' CHECK (jenis_proyek IN ('FISIK', 'NON_FISIK')),
    bidang VARCHAR(50),
    
    -- Lokasi
    lokasi_dusun VARCHAR(100),
    lokasi_detail TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    
    -- Perencanaan
    anggaran DECIMAL(15,2) NOT NULL DEFAULT 0,
    sumber_dana VARCHAR(100),
    volume DECIMAL(12,2),
    satuan VARCHAR(50),
    tgl_mulai_rencana DATE,
    tgl_selesai_rencana DATE,
    
    -- Pelaksanaan
    penyedia_jasa VARCHAR(200),
    no_kontrak VARCHAR(100),
    nilai_kontrak DECIMAL(15,2),
    tgl_kontrak DATE,
    tgl_mulai_aktual DATE,
    tgl_selesai_aktual DATE,
    
    -- Progress
    persentase_fisik INTEGER DEFAULT 0 CHECK (persentase_fisik >= 0 AND persentase_fisik <= 100),
    persentase_keuangan DECIMAL(5,2) DEFAULT 0,
    realisasi_keuangan DECIMAL(15,2) DEFAULT 0,
    
    -- Dokumentasi
    foto_0 VARCHAR(255),
    foto_50 VARCHAR(255),
    foto_100 VARCHAR(255),
    
    -- Status & Keterangan
    status VARCHAR(20) DEFAULT 'RENCANA' CHECK (status IN ('RENCANA', 'PROSES', 'SELESAI', 'MANGKRAK')),
    keterangan TEXT,
    
    -- Metadata
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes
CREATE INDEX idx_proyek_kode_desa ON proyek(kode_desa);
CREATE INDEX idx_proyek_status ON proyek(status);
CREATE INDEX idx_proyek_apbdes ON proyek(apbdes_id);
CREATE INDEX idx_proyek_rkpdesa ON proyek(rkpdesa_id);
CREATE INDEX idx_proyek_location ON proyek(latitude, longitude);

-- Comments
COMMENT ON TABLE proyek IS 'Tabel master proyek pembangunan fisik desa';
COMMENT ON COLUMN proyek.persentase_fisik IS 'Persentase penyelesaian fisik (0-100%)';
COMMENT ON COLUMN proyek.persentase_keuangan IS 'Persentase realisasi keuangan terhadap anggaran';

-- ===========================================
-- 2. TABEL PROYEK_LOG (Progress History)
-- ===========================================
CREATE TABLE proyek_log (
    id SERIAL PRIMARY KEY,
    proyek_id INTEGER NOT NULL REFERENCES proyek(id) ON DELETE CASCADE,
    tanggal_laporan DATE NOT NULL,
    persentase_fisik INTEGER NOT NULL CHECK (persentase_fisik >= 0 AND persentase_fisik <= 100),
    volume_terealisasi DECIMAL(12,2),
    kendala TEXT,
    solusi TEXT,
    foto VARCHAR(255),
    pelapor VARCHAR(100),
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes
CREATE INDEX idx_proyek_log_proyek ON proyek_log(proyek_id);
CREATE INDEX idx_proyek_log_tanggal ON proyek_log(tanggal_laporan);

-- Comments
COMMENT ON TABLE proyek_log IS 'Riwayat progress dan laporan proyek pembangunan';
COMMENT ON COLUMN proyek_log.volume_terealisasi IS 'Volume fisik yang sudah terealisasi';

-- ===========================================
-- SUCCESS MESSAGE
-- ===========================================
SELECT 'Tabel Pembangunan (proyek, proyek_log) berhasil dibuat!' AS status,
       (SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'proyek') AS tabel_proyek,
       (SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'proyek_log') AS tabel_proyek_log;
