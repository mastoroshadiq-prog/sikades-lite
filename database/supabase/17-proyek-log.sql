-- ===========================================
-- SIKADES LITE - PROYEK LOG TABLE
-- Migration: 17-proyek-log.sql
-- Date: 2025-12-25
-- ===========================================
-- Tabel untuk menyimpan riwayat progress proyek pembangunan

-- Drop existing table
DROP TABLE IF EXISTS proyek_log CASCADE;

-- Create proyek_log table
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
COMMENT ON COLUMN proyek_log.volume_terealisasi IS 'Volume fisik yang sudah terealisasi (misal: meter kubik, meter persegi, unit)';
COMMENT ON COLUMN proyek_log.kendala IS 'Kendala yang dihadapi pada periode laporan';
COMMENT ON COLUMN proyek_log.solusi IS 'Solusi yang diterapkan untuk mengatasi kendala';

SELECT 'Tabel proyek_log berhasil dibuat!' AS status;
