-- ===========================================
-- SIKADES LITE - FIX DEMOGRAFI & PROYEK
-- Run this in Supabase SQL Editor
-- ===========================================

-- ============================================
-- FIX POP_PENDUDUK TABLE
-- ============================================
ALTER TABLE pop_penduduk ADD COLUMN IF NOT EXISTS pendidikan_terakhir VARCHAR(100);
ALTER TABLE pop_penduduk ADD COLUMN IF NOT EXISTS pekerjaan_utama VARCHAR(255);
ALTER TABLE pop_penduduk ADD COLUMN IF NOT EXISTS status_perkawinan VARCHAR(50);
ALTER TABLE pop_penduduk ADD COLUMN IF NOT EXISTS no_hp VARCHAR(20);
ALTER TABLE pop_penduduk ADD COLUMN IF NOT EXISTS email VARCHAR(255);
ALTER TABLE pop_penduduk ADD COLUMN IF NOT EXISTS alamat_ktp TEXT;
ALTER TABLE pop_penduduk ADD COLUMN IF NOT EXISTS status_warga VARCHAR(50) DEFAULT 'TETAP';

-- ============================================
-- FIX POP_KELUARGA TABLE
-- ============================================
ALTER TABLE pop_keluarga ADD COLUMN IF NOT EXISTS status_kesejahteraan VARCHAR(50);
ALTER TABLE pop_keluarga ADD COLUMN IF NOT EXISTS luas_bangunan DECIMAL(10,2);
ALTER TABLE pop_keluarga ADD COLUMN IF NOT EXISTS sumber_air VARCHAR(100);
ALTER TABLE pop_keluarga ADD COLUMN IF NOT EXISTS jamban VARCHAR(100);
ALTER TABLE pop_keluarga ADD COLUMN IF NOT EXISTS penerangan VARCHAR(100);

-- ============================================
-- CREATE PROYEK_LOG TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS proyek_log (
    id SERIAL PRIMARY KEY,
    proyek_id INTEGER REFERENCES proyek_pembangunan(id) ON DELETE CASCADE,
    tanggal_laporan DATE NOT NULL,
    persentase_fisik INTEGER DEFAULT 0,
    persentase_keuangan DECIMAL(5,2) DEFAULT 0,
    volume_terlaksana DECIMAL(15,2) DEFAULT 0,
    keterangan TEXT,
    foto VARCHAR(255),
    kendala TEXT,
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_proyek_log_proyek_id ON proyek_log(proyek_id);

-- ============================================
-- FIX PROYEK_PEMBANGUNAN TABLE
-- ============================================
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS tahun INTEGER;
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS kode_kegiatan VARCHAR(50);
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS apbdes_id INTEGER;
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS pelaksana_kegiatan VARCHAR(255);
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS kontraktor VARCHAR(255);
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS foto_0 VARCHAR(255);
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS foto_50 VARCHAR(255);
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS foto_100 VARCHAR(255);
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS persentase_fisik INTEGER DEFAULT 0;
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS persentase_keuangan DECIMAL(5,2) DEFAULT 0;
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS tgl_mulai DATE;
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS tgl_selesai_target DATE;
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS tgl_selesai_aktual DATE;
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS volume_target DECIMAL(15,2);
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS satuan VARCHAR(50);
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS lokasi_detail TEXT;

-- ============================================
-- VERIFY
-- ============================================
SELECT 'All columns and tables created successfully!' AS status;
