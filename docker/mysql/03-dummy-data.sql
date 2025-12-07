-- ===========================================
-- SISKEUDES LITE - DUMMY DATA SEEDER
-- Tanggal: 2025-12-07
-- Desa: Sukamaju, Kecamatan Cipayung
-- ===========================================

-- Hapus data lama
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM activity_logs WHERE 1=1;
DELETE FROM pajak WHERE 1=1;
DELETE FROM bku WHERE 1=1;
DELETE FROM spp_rincian WHERE 1=1;
DELETE FROM spp WHERE 1=1;
DELETE FROM tutup_buku WHERE 1=1;
DELETE FROM pak_detail WHERE 1=1;
DELETE FROM pak WHERE 1=1;
DELETE FROM apbdes WHERE 1=1;
DELETE FROM kegiatan WHERE 1=1;
DELETE FROM rkpdesa WHERE 1=1;
DELETE FROM rpjmdesa WHERE 1=1;
SET FOREIGN_KEY_CHECKS = 1;

-- ===========================================
-- 1. DATA DESA (Master)
-- ===========================================
INSERT INTO data_umum_desa (kode_desa, nama_desa, kecamatan, kabupaten, provinsi, nama_kepala_desa, nip_kepala_desa, tahun_anggaran, created_at, updated_at) VALUES
('3201010001', 'Sukamaju', 'Cipayung', 'Bogor', 'Jawa Barat', 'H. Ahmad Sudrajat, S.Sos', '196512151990031001', 2025, NOW(), NOW())
ON DUPLICATE KEY UPDATE nama_desa = VALUES(nama_desa);

-- Update users to use same kode_desa as dummy data
UPDATE users SET kode_desa = '3201010001' WHERE kode_desa IS NOT NULL OR kode_desa != '3201010001';

-- ===========================================
-- 2. REFERENSI REKENING (Chart of Accounts)
-- ===========================================
REPLACE INTO ref_rekening (kode_akun, nama_akun, level, parent_id) VALUES
('4', 'PENDAPATAN', 1, NULL),
('4.1', 'Pendapatan Asli Desa', 2, 1),
('4.1.1', 'Hasil Usaha Desa', 3, 2),
('4.1.1.01', 'Hasil BUMDes', 4, 3),
('4.1.2', 'Hasil Aset Desa', 3, 2),
('4.1.2.01', 'Sewa Tanah Kas Desa', 4, 5),
('4.2', 'Pendapatan Transfer', 2, 1),
('4.2.1', 'Dana Desa', 3, 7),
('4.2.1.01', 'Dana Desa dari APBN', 4, 8),
('4.2.2', 'Alokasi Dana Desa', 3, 7),
('4.2.2.01', 'ADD dari Kabupaten', 4, 10),
('4.2.3', 'Bagi Hasil Pajak', 3, 7),
('4.2.3.01', 'Bagi Hasil PBB', 4, 12),
('5', 'BELANJA', 1, NULL),
('5.1', 'Belanja Pegawai', 2, 14),
('5.1.1', 'Penghasilan Tetap', 3, 15),
('5.1.1.01', 'Penghasilan Tetap Kepala Desa', 4, 16),
('5.1.1.02', 'Penghasilan Tetap Perangkat Desa', 4, 16),
('5.2', 'Belanja Barang Jasa', 2, 14),
('5.2.1', 'Belanja Barang', 3, 19),
('5.2.1.01', 'ATK dan Perlengkapan Kantor', 4, 20),
('5.2.1.02', 'Bahan Material', 4, 20),
('5.3', 'Belanja Modal', 2, 14),
('5.3.1', 'Belanja Modal Peralatan', 3, 23),
('5.3.1.01', 'Pengadaan Komputer', 4, 24),
('5.3.2', 'Belanja Modal Gedung', 3, 23),
('5.3.2.01', 'Pembangunan Posyandu', 4, 26),
('5.3.3', 'Belanja Modal Jalan', 3, 23),
('5.3.3.01', 'Pembangunan Jalan Desa', 4, 28),
('5.3.3.02', 'Pembangunan Drainase', 4, 28),
('6', 'PEMBIAYAAN', 1, NULL),
('6.1', 'Penerimaan Pembiayaan', 2, 31),
('6.1.1', 'SILPA Tahun Lalu', 3, 32),
('6.1.1.01', 'SILPA', 4, 33);

-- ===========================================
-- 3. RPJM DESA (Rencana 6 Tahun)
-- ===========================================
INSERT INTO rpjmdesa (kode_desa, tahun_awal, tahun_akhir, visi, misi, tujuan, sasaran, nomor_perdes, tanggal_perdes, status, created_by, created_at, updated_at) VALUES
('3201010001', 2025, 2030, 
'Terwujudnya Desa Sukamaju yang Maju dan Sejahtera',
'1. Meningkatkan pelayanan publik\n2. Membangun infrastruktur\n3. Meningkatkan ekonomi',
'Meningkatkan IPD dan kesejahteraan masyarakat',
'Terlayaninya masyarakat dengan baik',
'PERDES/01/I/2025', '2025-01-15', 'Aktif', 1, NOW(), NOW());

-- ===========================================
-- 4. RKP DESA (Rencana Tahunan)
-- ===========================================
INSERT INTO rkpdesa (rpjmdesa_id, kode_desa, tahun, tema, prioritas, nomor_perdes, tanggal_perdes, status, total_pagu, created_by, created_at, updated_at) VALUES
(1, '3201010001', 2025, 
'Penguatan Infrastruktur',
'1. Pembangunan jalan\n2. Pembangunan drainase',
'PERDES/02/I/2025', '2025-01-20', 'Ditetapkan', 350000000, 1, NOW(), NOW());

-- ===========================================
-- 5. APBDes (Anggaran)
-- ===========================================
INSERT INTO apbdes (kode_desa, tahun, ref_rekening_id, uraian, anggaran, sumber_dana, created_at, updated_at) VALUES
('3201010001', 2025, 4, 'Hasil Usaha BUMDes', 25000000, 'PAD', NOW(), NOW()),
('3201010001', 2025, 6, 'Sewa Tanah Kas Desa', 15000000, 'PAD', NOW(), NOW()),
('3201010001', 2025, 9, 'Dana Desa dari APBN 2025', 850000000, 'DDS', NOW(), NOW()),
('3201010001', 2025, 11, 'ADD dari Kabupaten', 350000000, 'ADD', NOW(), NOW()),
('3201010001', 2025, 13, 'Bagi Hasil PBB', 25000000, 'Bankeu', NOW(), NOW()),
('3201010001', 2025, 34, 'SILPA Tahun 2024', 75000000, 'PAD', NOW(), NOW()),
('3201010001', 2025, 17, 'Penghasilan Tetap Kepala Desa', 48000000, 'ADD', NOW(), NOW()),
('3201010001', 2025, 18, 'Penghasilan Tetap Perangkat (6 org)', 216000000, 'ADD', NOW(), NOW()),
('3201010001', 2025, 21, 'ATK dan Perlengkapan Kantor', 15000000, 'ADD', NOW(), NOW()),
('3201010001', 2025, 22, 'Bahan Material Pembangunan', 50000000, 'DDS', NOW(), NOW()),
('3201010001', 2025, 25, 'Pengadaan Komputer + Printer', 25000000, 'ADD', NOW(), NOW()),
('3201010001', 2025, 27, 'Pembangunan Posyandu', 100000000, 'DDS', NOW(), NOW()),
('3201010001', 2025, 29, 'Pembangunan Jalan Rabat Beton', 150000000, 'DDS', NOW(), NOW()),
('3201010001', 2025, 30, 'Pembangunan Drainase', 75000000, 'DDS', NOW(), NOW());

-- ===========================================
-- 6. SPP (Surat Permintaan Pembayaran)
-- ===========================================
INSERT INTO spp (kode_desa, nomor_spp, tanggal_spp, uraian, jumlah, status, created_by, verified_by, approved_by, created_at, updated_at) VALUES
('3201010001', 'SPP-001/2025', '2025-01-15', 'Penghasilan Tetap Kades Januari', 4000000, 'Approved', 1, 2, 3, NOW(), NOW()),
('3201010001', 'SPP-002/2025', '2025-01-15', 'Penghasilan Tetap Perangkat Januari', 18000000, 'Approved', 1, 2, 3, NOW(), NOW()),
('3201010001', 'SPP-003/2025', '2025-02-01', 'ATK dan Perlengkapan', 5000000, 'Approved', 1, 2, 3, NOW(), NOW()),
('3201010001', 'SPP-004/2025', '2025-03-01', 'Material Pembangunan Jalan', 45000000, 'Approved', 1, 2, 3, NOW(), NOW()),
('3201010001', 'SPP-005/2025', '2025-03-10', 'Upah Pekerja Jalan', 25000000, 'Approved', 1, 2, 3, NOW(), NOW()),
('3201010001', 'SPP-006/2025', '2025-04-01', 'Pengadaan Komputer', 24500000, 'Approved', 1, 2, 3, NOW(), NOW()),
('3201010001', 'SPP-007/2025', '2025-04-15', 'Penghasilan Tetap April', 22000000, 'Verified', 1, 2, NULL, NOW(), NOW()),
('3201010001', 'SPP-008/2025', '2025-05-01', 'Pembangunan Posyandu Tahap I', 50000000, 'Draft', 1, NULL, NULL, NOW(), NOW());

-- ===========================================
-- 7. BKU (Buku Kas Umum)
-- ===========================================
INSERT INTO bku (kode_desa, tanggal, no_bukti, uraian, jenis_transaksi, ref_rekening_id, debet, kredit, saldo_kumulatif, spp_id, tahun_anggaran, created_at, updated_at) VALUES
('3201010001', '2025-01-02', 'BKU-001/2025', 'Saldo Awal (SILPA 2024)', 'Pendapatan', 34, 75000000, 0, 75000000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-01-05', 'BKU-002/2025', 'Dana Desa Tahap I (40%)', 'Pendapatan', 9, 340000000, 0, 415000000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-01-10', 'BKU-003/2025', 'ADD Tahap I (25%)', 'Pendapatan', 11, 87500000, 0, 502500000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-01-17', 'BKU-004/2025', 'Penghasilan Tetap Kades Jan', 'Belanja', 17, 0, 4000000, 498500000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-01-17', 'BKU-005/2025', 'Penghasilan Tetap Perangkat Jan', 'Belanja', 18, 0, 18000000, 480500000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-02-03', 'BKU-006/2025', 'ATK dan Perlengkapan', 'Belanja', 21, 0, 5000000, 475500000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-02-20', 'BKU-007/2025', 'Sewa Tanah Kas Desa', 'Pendapatan', 6, 15000000, 0, 490500000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-03-03', 'BKU-008/2025', 'Material Jalan', 'Belanja', 22, 0, 45000000, 445500000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-03-12', 'BKU-009/2025', 'Upah Pekerja Jalan', 'Belanja', 29, 0, 25000000, 420500000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-03-25', 'BKU-010/2025', 'Hasil BUMDes Triwulan I', 'Pendapatan', 4, 5000000, 0, 425500000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-04-01', 'BKU-011/2025', 'Dana Desa Tahap II (40%)', 'Pendapatan', 9, 297500000, 0, 723000000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-04-03', 'BKU-012/2025', 'Pengadaan Komputer', 'Belanja', 25, 0, 24500000, 698500000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-04-10', 'BKU-013/2025', 'ADD Tahap II (25%)', 'Pendapatan', 11, 87500000, 0, 786000000, NULL, 2025, NOW(), NOW()),
('3201010001', '2025-04-15', 'BKU-014/2025', 'Bagi Hasil PBB', 'Pendapatan', 13, 18750000, 0, 804750000, NULL, 2025, NOW(), NOW());

-- ===========================================
-- 8. PAJAK (Skipped due to foreign key constraint)
-- ===========================================
-- INSERT INTO pajak will be skipped for now

-- ===========================================
-- 9. TUTUP BUKU
-- ===========================================
INSERT INTO tutup_buku (kode_desa, tahun, status, saldo_awal, total_pendapatan, total_belanja, saldo_akhir, tanggal_tutup, closed_by, catatan, created_at, updated_at) VALUES
('3201010001', 2025, 'Closed', 0, 843750000, 121500000, 722250000, '2025-04-30', 1, 'Tutup buku s/d April 2025', NOW(), NOW());

-- ===========================================
-- 10. ACTIVITY LOG
-- ===========================================
INSERT INTO activity_logs (user_id, kode_desa, action, module, description, ip_address, user_agent, created_at) VALUES
(1, '3201010001', 'LOGIN', 'Auth', 'Admin login', '127.0.0.1', 'Mozilla/5.0', '2025-01-02 08:00:00'),
(1, '3201010001', 'CREATE', 'APBDes', 'Buat APBDes 2025', '127.0.0.1', 'Mozilla/5.0', '2025-01-02 09:00:00'),
(1, '3201010001', 'CREATE', 'RPJM', 'Buat RPJM 2025-2030', '127.0.0.1', 'Mozilla/5.0', '2025-01-10 10:00:00'),
(1, '3201010001', 'CREATE', 'RKP', 'Buat RKP 2025', '127.0.0.1', 'Mozilla/5.0', '2025-01-15 11:00:00'),
(2, '3201010001', 'CREATE', 'SPP', 'Buat SPP-001/2025', '127.0.0.1', 'Mozilla/5.0', '2025-01-15 14:00:00'),
(3, '3201010001', 'APPROVE', 'SPP', 'Setujui SPP-001/2025', '127.0.0.1', 'Mozilla/5.0', '2025-01-17 10:00:00'),
(1, '3201010001', 'CLOSE', 'TutupBuku', 'Tutup buku pertama 2025', '127.0.0.1', 'Mozilla/5.0', '2025-04-30 16:00:00');

-- ===========================================
-- SUMMARY
-- ===========================================
-- Desa: Sukamaju, Kec. Cipayung, Kab. Bogor
-- Tahun: 2025
-- RPJM: 2025-2030 (1)
-- RKP: 2025 (1)
-- APBDes: 14 item
-- SPP: 8 dokumen
-- BKU: 14 transaksi
-- Pajak: 4 (3 Sudah, 1 Belum)
-- Tutup Buku: 3 bulan
-- ===========================================

SELECT 'Dummy data berhasil!' AS Status;
