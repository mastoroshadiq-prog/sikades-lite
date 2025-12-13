-- ===========================================
-- SIKADES LITE - FIX REF_BIDANG TABLE
-- Run this in Supabase SQL Editor
-- ===========================================

-- Create ref_bidang table (Reference for Bidang Kegiatan)
CREATE TABLE IF NOT EXISTS ref_bidang (
    id SERIAL PRIMARY KEY,
    kode_bidang VARCHAR(20) NOT NULL,
    nama_bidang VARCHAR(255) NOT NULL,
    urutan INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default data for ref_bidang (sesuai Permendagri 20/2018)
INSERT INTO ref_bidang (kode_bidang, nama_bidang, urutan, is_active) VALUES
('01', 'Bidang Penyelenggaraan Pemerintahan Desa', 1, true),
('02', 'Bidang Pelaksanaan Pembangunan Desa', 2, true),
('03', 'Bidang Pembinaan Kemasyarakatan Desa', 3, true),
('04', 'Bidang Pemberdayaan Masyarakat Desa', 4, true),
('05', 'Bidang Penanggulangan Bencana, Keadaan Darurat dan Mendesak', 5, true)
ON CONFLICT (id) DO NOTHING;

-- Add bidang_id column to kegiatan if not exists
ALTER TABLE kegiatan ADD COLUMN IF NOT EXISTS bidang_id INTEGER REFERENCES ref_bidang(id);

-- Verify
SELECT * FROM ref_bidang ORDER BY urutan;
