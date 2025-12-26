-- ===========================================
-- SIKADES LITE - STRUKTUR ORGANISASI
-- Migration: 19-struktur-organisasi.sql
-- Date: 2025-12-26
-- ===========================================
-- Tabel untuk mencatat perangkat desa dan struktur organisasi

-- Drop existing table
DROP TABLE IF EXISTS struktur_organisasi CASCADE;

-- Create struktur_organisasi table
CREATE TABLE struktur_organisasi (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    nip VARCHAR(50),
    pangkat_golongan VARCHAR(50),
    pendidikan VARCHAR(100),
    tanggal_lahir DATE,
    tanggal_pengangkatan DATE,
    no_sk VARCHAR(100),
    foto VARCHAR(255),
    urutan INTEGER DEFAULT 0,
    aktif BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes
CREATE INDEX IF NOT EXISTS idx_struktur_organisasi_desa ON struktur_organisasi(kode_desa);
CREATE INDEX IF NOT EXISTS idx_struktur_organisasi_jabatan ON struktur_organisasi(jabatan);
CREATE INDEX IF NOT EXISTS idx_struktur_organisasi_aktif ON struktur_organisasi(aktif);

-- Comments
COMMENT ON TABLE struktur_organisasi IS 'Struktur organisasi dan perangkat desa';
COMMENT ON COLUMN struktur_organisasi.urutan IS 'Urutan tampilan di bagan organisasi (makin kecil makin atas)';
COMMENT ON COLUMN struktur_organisasi.aktif IS 'Status aktif perangkat (true=masih bertugas, false=sudah pensiun/pindah)';

SELECT 'Tabel struktur_organisasi berhasil dibuat!' AS status;
