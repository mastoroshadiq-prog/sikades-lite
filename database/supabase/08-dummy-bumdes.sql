-- ===========================================
-- SIKADES LITE - DUMMY DATA BUMDES
-- Jalankan di Supabase SQL Editor
-- ===========================================

-- STEP 1: Fix tabel - drop NOT NULL constraints yang menghalangi
ALTER TABLE bumdes_unit ALTER COLUMN bumdes_id DROP NOT NULL;
ALTER TABLE bumdes_unit ALTER COLUMN nama DROP NOT NULL;

-- STEP 2: Insert Unit Usaha BUMDes (dengan kolom nama juga diisi)
INSERT INTO bumdes_unit (kode_desa, nama, nama_unit, jenis_usaha, penanggung_jawab, modal_awal, tanggal_mulai, status, alamat, no_telp, created_at, updated_at) VALUES
('3201010001', 'Toko Sembako Desa', 'Toko Sembako Desa', 'Perdagangan', 'Ahmad Sudirman', 50000000, '2020-03-15', 'Aktif', 'Jl. Raya Desa No. 1, Cikaret', '081234567890', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', 'Pengelolaan Sampah', 'Pengelolaan Sampah', 'Jasa', 'Budi Santoso', 30000000, '2021-01-10', 'Aktif', 'Jl. Melati No. 5, Ciburial', '081234567891', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', 'Air Minum Isi Ulang', 'Air Minum Isi Ulang', 'Produksi', 'Dedi Kurniawan', 40000000, '2021-06-20', 'Aktif', 'Jl. Mawar No. 8, Ciburial', '081234567892', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', 'Simpan Pinjam', 'Simpan Pinjam', 'Keuangan', 'Eko Prasetyo', 80000000, '2020-05-01', 'Aktif', 'Kantor Desa Contoh', '081234567893', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', 'Pasar Desa', 'Pasar Desa', 'Perdagangan', 'Fajar Hidayat', 25000000, '2022-02-15', 'Aktif', 'Jl. Kenanga No. 5, Cibeureum', '081234567894', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON CONFLICT DO NOTHING;

SELECT 'BUMDes Unit data inserted! Total: 5 units' AS status;
