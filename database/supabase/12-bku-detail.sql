-- ===========================================
-- BKU DETAIL - Rincian Item Transaksi
-- Migration: 12-bku-detail.sql
-- Date: 2025-12-25
-- ===========================================
-- Tabel ini menyimpan detail item untuk setiap transaksi BKU
-- Contoh: Belanja ATK bisa memiliki detail: buku tulis, spidol, penggaris

-- Create BKU Detail Table
CREATE TABLE IF NOT EXISTS bku_detail (
    id SERIAL PRIMARY KEY,
    bku_id INTEGER NOT NULL REFERENCES bku(id) ON DELETE CASCADE,
    nama_item VARCHAR(255) NOT NULL,
    spesifikasi VARCHAR(255),
    satuan VARCHAR(50) DEFAULT 'pcs',
    jumlah DECIMAL(10,2) NOT NULL DEFAULT 1,
    harga_satuan DECIMAL(15,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(15,2) GENERATED ALWAYS AS (jumlah * harga_satuan) STORED,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Indexes for better query performance
CREATE INDEX IF NOT EXISTS idx_bku_detail_bku_id ON bku_detail(bku_id);

-- Comment on table
COMMENT ON TABLE bku_detail IS 'Rincian item untuk setiap transaksi BKU (Belanja)';
COMMENT ON COLUMN bku_detail.bku_id IS 'Foreign key ke tabel bku';
COMMENT ON COLUMN bku_detail.nama_item IS 'Nama barang/jasa yang dibeli';
COMMENT ON COLUMN bku_detail.spesifikasi IS 'Spesifikasi atau merk barang';
COMMENT ON COLUMN bku_detail.satuan IS 'Satuan (pcs, unit, rim, dus, dll)';
COMMENT ON COLUMN bku_detail.jumlah IS 'Jumlah/kuantitas barang';
COMMENT ON COLUMN bku_detail.harga_satuan IS 'Harga per satuan';
COMMENT ON COLUMN bku_detail.subtotal IS 'Hasil jumlah x harga_satuan (auto-calculated)';

-- ===========================================
-- Insert sample data for demonstration
-- ===========================================
-- Note: Uncomment and modify bku_id values based on your actual data

-- Example: ATK dan Perlengkapan Kantor (assume bku_id = 1)
-- INSERT INTO bku_detail (bku_id, nama_item, spesifikasi, satuan, jumlah, harga_satuan) VALUES
-- (1, 'Buku Tulis 100 Lembar', 'Sinar Dunia', 'pcs', 50, 5000),
-- (1, 'Spidol Whiteboard', 'Snowman', 'pcs', 20, 15000),
-- (1, 'Penggaris 30cm', 'Butterfly', 'pcs', 30, 8000),
-- (1, 'Pulpen Hitam', 'Pilot', 'pcs', 100, 3000),
-- (1, 'Map Plastik', 'Generic', 'pcs', 50, 2000);

-- Example: Konsumsi Rapat (assume bku_id = 2)
-- INSERT INTO bku_detail (bku_id, nama_item, spesifikasi, satuan, jumlah, harga_satuan) VALUES
-- (2, 'Nasi Kotak', 'Catering Anda', 'kotak', 50, 25000),
-- (2, 'Air Mineral 600ml', 'Aqua', 'botol', 50, 5000),
-- (2, 'Snack Box', 'Catering Anda', 'kotak', 50, 15000);
