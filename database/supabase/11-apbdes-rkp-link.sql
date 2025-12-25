-- ===========================================
-- SIKADES LITE - ADD RKP REFERENCE TO APBDES
-- Version: 1.0.1
-- Date: 2025-12-25
-- ===========================================
-- This migration adds optional link from APBDes to RKP Desa
-- to comply with regulation that APBDes should be based on RKP
-- ===========================================

-- Add rkpdesa_id column to apbdes table (optional reference)
ALTER TABLE apbdes 
ADD COLUMN IF NOT EXISTS rkpdesa_id INTEGER REFERENCES rkpdesa(id) ON DELETE SET NULL;

-- Create index for performance
CREATE INDEX IF NOT EXISTS idx_apbdes_rkpdesa ON apbdes(rkpdesa_id);

-- Add kegiatan_id to optionally link to specific activity from RKP
ALTER TABLE apbdes 
ADD COLUMN IF NOT EXISTS kegiatan_id INTEGER REFERENCES kegiatan(id) ON DELETE SET NULL;

-- Create index for kegiatan reference
CREATE INDEX IF NOT EXISTS idx_apbdes_kegiatan ON apbdes(kegiatan_id);

-- ===========================================
-- COMMENTS
-- ===========================================
COMMENT ON COLUMN apbdes.rkpdesa_id IS 'Optional reference to RKP Desa for regulatory compliance';
COMMENT ON COLUMN apbdes.kegiatan_id IS 'Optional reference to specific Kegiatan from RKP Desa';

-- ===========================================
-- SUCCESS MESSAGE
-- ===========================================
SELECT 'APBDes RKP reference columns added successfully!' AS status;
