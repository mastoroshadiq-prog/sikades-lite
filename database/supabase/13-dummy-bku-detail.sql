-- ===========================================
-- BKU DETAIL - DUMMY DATA
-- Migration: 13-dummy-bku-detail.sql
-- Date: 2025-12-25
-- ===========================================
-- Rincian item untuk setiap transaksi Belanja di BKU
-- Run AFTER 12-bku-detail.sql

-- ===========================================
-- Get BKU IDs for reference (based on 02-dummy-data.sql):
-- BKU ID 4: Penghasilan Tetap Kades Jan (2025-01-17) - 4.000.000
-- BKU ID 5: Penghasilan Tetap Perangkat Jan (2025-01-17) - 18.000.000
-- BKU ID 6: ATK dan Perlengkapan (2025-02-03) - 5.000.000
-- BKU ID 8: Material Jalan (2025-03-03) - 45.000.000
-- BKU ID 9: Upah Pekerja Jalan (2025-03-12) - 25.000.000
-- BKU ID 12: Pengadaan Komputer (2025-04-03) - 24.500.000
-- ===========================================

-- Clear existing dummy data (if any)
DELETE FROM bku_detail WHERE bku_id IN (
    SELECT id FROM bku WHERE jenis_transaksi = 'Belanja' AND kode_desa = '3201010001'
);

-- ===========================================
-- JANUARI: Penghasilan Tetap (BKU ID 4 & 5)
-- ===========================================
-- Note: Penghasilan tetap biasanya tidak perlu detail item
-- Tapi kita tambahkan untuk demo

INSERT INTO bku_detail (bku_id, nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
SELECT 
    b.id,
    'Penghasilan Tetap Kepala Desa',
    'Bulan Januari 2025',
    'bulan',
    1,
    4000000,
    'Penghasilan tetap bulanan Kepala Desa'
FROM bku b 
WHERE b.no_bukti = 'BKU-004/2025' AND b.kode_desa = '3201010001'
LIMIT 1;

INSERT INTO bku_detail (bku_id, nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
SELECT 
    b.id,
    item.nama_item,
    item.spesifikasi,
    item.satuan,
    item.jumlah,
    item.harga_satuan,
    item.keterangan
FROM bku b
CROSS JOIN (VALUES
    ('Penghasilan Sekdes', 'Bulan Januari 2025', 'bulan', 1, 3500000, 'Sekretaris Desa'),
    ('Penghasilan Kaur Keuangan', 'Bulan Januari 2025', 'bulan', 1, 3000000, 'Kaur Keuangan'),
    ('Penghasilan Kaur Umum', 'Bulan Januari 2025', 'bulan', 1, 3000000, 'Kaur Umum'),
    ('Penghasilan Kasi Pemerintahan', 'Bulan Januari 2025', 'bulan', 1, 2800000, 'Kasi Pemerintahan'),
    ('Penghasilan Kasi Kesra', 'Bulan Januari 2025', 'bulan', 1, 2800000, 'Kasi Kesejahteraan'),
    ('Penghasilan Kasi Pelayanan', 'Bulan Januari 2025', 'bulan', 1, 2900000, 'Kasi Pelayanan')
) AS item(nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
WHERE b.no_bukti = 'BKU-005/2025' AND b.kode_desa = '3201010001';

-- ===========================================
-- FEBRUARI: ATK dan Perlengkapan (BKU ID 6) - 5.000.000
-- ===========================================
INSERT INTO bku_detail (bku_id, nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
SELECT 
    b.id,
    item.nama_item,
    item.spesifikasi,
    item.satuan,
    item.jumlah,
    item.harga_satuan,
    item.keterangan
FROM bku b
CROSS JOIN (VALUES
    ('Buku Tulis 100 Lembar', 'Sinar Dunia A4', 'pcs', 50, 5500, 'Untuk keperluan kantor'),
    ('Pulpen Hitam', 'Pilot BP-1 RT', 'pcs', 60, 4500, 'Pilot original'),
    ('Pulpen Biru', 'Pilot BP-1 RT', 'pcs', 40, 4500, 'Pilot original'),
    ('Spidol Whiteboard', 'Snowman WB-6', 'pcs', 24, 15000, 'Hitam, Biru, Merah'),
    ('Penghapus Whiteboard', 'Joyko', 'pcs', 6, 12000, 'Dengan magnet'),
    ('Map Plastik L-Folder', 'Generic A4', 'pcs', 100, 2000, 'Warna campur'),
    ('Kertas HVS A4 70gsm', 'Sinar Dunia', 'rim', 10, 52000, '500 lembar/rim'),
    ('Stapler HD-10', 'Joyko', 'pcs', 4, 35000, 'Kapasitas 20 lembar'),
    ('Isi Staples HD-10', 'Joyko', 'kotak', 20, 5000, '1000 pcs/kotak'),
    ('Binder Clip 107', 'Joyko', 'kotak', 10, 15000, '12 pcs/kotak'),
    ('Gunting Besar', 'Joyko', 'pcs', 5, 18000, 'Stainless steel'),
    ('Lem Stick', 'UHU Stic', 'pcs', 20, 12500, '21 gram'),
    ('Correction Tape', 'Joyko CT-533', 'pcs', 15, 8500, '5mm x 6m'),
    ('Penggaris Plastik 30cm', 'Butterfly', 'pcs', 20, 3500, 'Transparan'),
    ('Tinta Stempel', 'Artline', 'botol', 5, 15000, 'ESK-20 Hitam')
) AS item(nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
WHERE b.no_bukti = 'BKU-006/2025' AND b.kode_desa = '3201010001';

-- ===========================================
-- MARET: Material Jalan (BKU ID 8) - 45.000.000
-- ===========================================
INSERT INTO bku_detail (bku_id, nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
SELECT 
    b.id,
    item.nama_item,
    item.spesifikasi,
    item.satuan,
    item.jumlah,
    item.harga_satuan,
    item.keterangan
FROM bku b
CROSS JOIN (VALUES
    ('Semen Portland', 'Tiga Roda 50kg', 'sak', 300, 62000, 'Semen type I'),
    ('Pasir Pasang', 'Lumajang halus', 'm3', 25, 350000, 'Pasir ayak'),
    ('Batu Split 2/3', 'Batu pecah', 'm3', 30, 450000, 'Untuk cor'),
    ('Batu Belah', 'Lokal', 'm3', 15, 250000, 'Untuk pondasi'),
    ('Besi Beton 8mm', 'SNI', 'batang', 100, 45000, 'Full panjang 12m'),
    ('Kawat Bendrat', 'BWG 22', 'kg', 20, 18000, 'Untuk ikat besi'),
    ('Papan Cor/Bekisting', 'Kayu sengon', 'lembar', 50, 35000, 'Ukuran 200x20x2'),
    ('Paku 5cm', 'Cap Kunci', 'kg', 10, 20000, 'Untuk bekisting'),
    ('Gerobak Cor', 'Lokal', 'unit', 2, 450000, 'Kapasitas 60L'),
    ('Sekop', 'Cap Gajah', 'pcs', 5, 85000, 'Gagang kayu'),
    ('Cangkul', 'Cap Gajah', 'pcs', 5, 75000, 'Gagang kayu')
) AS item(nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
WHERE b.no_bukti = 'BKU-008/2025' AND b.kode_desa = '3201010001';

-- ===========================================
-- MARET: Upah Pekerja Jalan (BKU ID 9) - 25.000.000
-- ===========================================
INSERT INTO bku_detail (bku_id, nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
SELECT 
    b.id,
    item.nama_item,
    item.spesifikasi,
    item.satuan,
    item.jumlah,
    item.harga_satuan,
    item.keterangan
FROM bku b
CROSS JOIN (VALUES
    ('Upah Mandor', 'Pak Joko', 'HOK', 20, 200000, 'Koordinator pekerja'),
    ('Upah Tukang Batu', 'Tim Tukang', 'HOK', 50, 180000, '5 orang x 10 hari'),
    ('Upah Pekerja', 'Tenaga Lokal', 'HOK', 100, 120000, '10 orang x 10 hari'),
    ('Upah Tukang Besi', 'Pak Ahmad', 'HOK', 20, 170000, '2 orang x 10 hari'),
    ('Konsumsi Pekerja', 'Makan Siang', 'porsi', 150, 20000, 'Nasi kotak'),
    ('Air Minum', 'Galon Aqua', 'galon', 30, 20000, 'Untuk pekerja')
) AS item(nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
WHERE b.no_bukti = 'BKU-009/2025' AND b.kode_desa = '3201010001';

-- ===========================================
-- APRIL: Pengadaan Komputer (BKU ID 12) - 24.500.000
-- ===========================================
INSERT INTO bku_detail (bku_id, nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
SELECT 
    b.id,
    item.nama_item,
    item.spesifikasi,
    item.satuan,
    item.jumlah,
    item.harga_satuan,
    item.keterangan
FROM bku b
CROSS JOIN (VALUES
    ('PC Desktop Core i5', 'HP ProDesk 400 G7 i5-10500/8GB/512SSD', 'unit', 2, 8500000, 'Komputer utama'),
    ('Monitor LED 24 inch', 'HP P24h G4 FHD IPS', 'unit', 2, 2200000, 'Full HD 1920x1080'),
    ('Keyboard USB', 'Logitech K120', 'unit', 2, 95000, 'Standard layout'),
    ('Mouse USB', 'Logitech B100', 'unit', 2, 65000, 'Optical mouse'),
    ('Printer Epson L3210', 'Inkjet Tank A4', 'unit', 1, 2150000, 'Print Scan Copy'),
    ('UPS 650VA', 'APC BX650LI-MS', 'unit', 2, 750000, 'Backup power'),
    ('Kabel LAN Cat6', 'Belden Original', 'meter', 50, 5500, 'Untuk jaringan'),
    ('Stop Kontak 5 lubang', 'Broco', 'pcs', 2, 85000, 'Dengan switch'),
    ('Flash Disk 32GB', 'Sandisk Cruzer Blade', 'pcs', 5, 65000, 'USB 2.0')
) AS item(nama_item, spesifikasi, satuan, jumlah, harga_satuan, keterangan)
WHERE b.no_bukti = 'BKU-012/2025' AND b.kode_desa = '3201010001';

-- ===========================================
-- VERIFY INSERTED DATA
-- ===========================================
SELECT 
    b.tanggal,
    b.no_bukti,
    b.uraian AS transaksi,
    b.kredit AS total_bku,
    COUNT(d.id) AS jumlah_item,
    COALESCE(SUM(d.subtotal), 0) AS total_detail
FROM bku b
LEFT JOIN bku_detail d ON d.bku_id = b.id
WHERE b.jenis_transaksi = 'Belanja' 
    AND b.kode_desa = '3201010001'
GROUP BY b.id, b.tanggal, b.no_bukti, b.uraian, b.kredit
ORDER BY b.tanggal;

SELECT 'BKU Detail dummy data inserted successfully!' AS status;
