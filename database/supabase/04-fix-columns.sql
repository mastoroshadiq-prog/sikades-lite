-- ===========================================
-- SIKADES LITE - FIX MISSING COLUMNS
-- Run this in Supabase SQL Editor
-- ===========================================

-- Add missing columns to kegiatan table
ALTER TABLE kegiatan ADD COLUMN IF NOT EXISTS pagu_anggaran DECIMAL(15,2) DEFAULT 0;
ALTER TABLE kegiatan ADD COLUMN IF NOT EXISTS waktu_pelaksanaan VARCHAR(100);
ALTER TABLE kegiatan ADD COLUMN IF NOT EXISTS sasaran TEXT;
ALTER TABLE kegiatan ADD COLUMN IF NOT EXISTS bidang VARCHAR(100);

-- Add missing columns to rkpdesa
ALTER TABLE rkpdesa ADD COLUMN IF NOT EXISTS jumlah_kegiatan INTEGER DEFAULT 0;

-- Fix aset_inventaris - add missing columns
ALTER TABLE aset_inventaris ADD COLUMN IF NOT EXISTS harga_perolehan DECIMAL(15,2) DEFAULT 0;
ALTER TABLE aset_inventaris ADD COLUMN IF NOT EXISTS kategori VARCHAR(100);

-- Add tahun_anggaran to kegiatan if not exists (alias for consistency)
-- kegiatan already has rkpdesa_id which links to year

-- Fix proyek_pembangunan - add missing columns
ALTER TABLE proyek_pembangunan ADD COLUMN IF NOT EXISTS tahun INTEGER;

-- Create demografi alias view or table for compatibility
-- The app uses 'demografi' route but the table is 'penduduk'
-- This is handled by the controller, no table change needed

-- Verify tables
SELECT 'Columns added successfully!' AS status;

-- Show kegiatan columns for verification
SELECT column_name, data_type FROM information_schema.columns 
WHERE table_name = 'kegiatan' ORDER BY ordinal_position;
