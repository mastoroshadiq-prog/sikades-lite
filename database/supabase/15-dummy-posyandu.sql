-- ===========================================
-- SIKADES LITE - DUMMY DATA E-POSYANDU
-- Migration: 15-dummy-posyandu.sql
-- Date: 2025-12-25
-- ===========================================

-- ===========================================
-- 1. KADER POSYANDU
-- ===========================================
-- Asumsi: Posyandu sudah ada dengan id 1 dan 2 dari migrasi sebelumnya

INSERT INTO kes_kader (posyandu_id, nama_kader, jabatan, no_telp, status, created_at, updated_at) VALUES
-- Posyandu Mawar (id: 1)
(1, 'Bu Siti Aminah', 'Ketua', '081234567801', 'AKTIF', NOW(), NOW()),
(1, 'Bu Dewi Rahayu', 'Sekretaris', '081234567802', 'AKTIF', NOW(), NOW()),
(1, 'Bu Rina Susanti', 'Bendahara', '081234567803', 'AKTIF', NOW(), NOW()),
(1, 'Bu Ani Lestari', 'Anggota', '081234567804', 'AKTIF', NOW(), NOW()),

-- Posyandu Melati (id: 2)
(2, 'Bu Dewi Lestari', 'Ketua', '081234567805', 'AKTIF', NOW(), NOW()),
(2, 'Bu Sri Handayani', 'Sekretaris', '081234567806', 'AKTIF', NOW(), NOW()),
(2, 'Bu Tuti Rahmawati', 'Bendahara', '081234567807', 'AKTIF', NOW(), NOW()),
(2, 'Bu Lina Marlina', 'Anggota', '081234567808', 'AKTIF', NOW(), NOW());

-- ===========================================
-- 2. DATA IBU HAMIL
-- ===========================================
-- CATATAN: Sesuaikan penduduk_id dengan data pop_penduduk wanita usia subur di database Anda
-- Contoh di bawah menggunakan ID dummy, silakan sesuaikan

INSERT INTO kes_ibu_hamil (
    posyandu_id, penduduk_id, tanggal_hpht, taksiran_persalinan, usia_kandungan, 
    kehamilan_ke, tinggi_badan_ibu, berat_badan_sebelum, golongan_darah, 
    resiko_tinggi, faktor_resiko, pemeriksaan_k1, pemeriksaan_k2, pemeriksaan_k3, 
    pemeriksaan_k4, status, keterangan, created_at, updated_at
) VALUES
-- Ibu hamil normal
(1, 5, '2024-09-15', '2025-06-22', 28, 2, 155.00, 52.00, 'O', false, NULL, 
 '2024-10-10', '2024-12-15', '2025-02-10', NULL, 'HAMIL', 'Kondisi baik', NOW(), NOW()),

(1, 7, '2024-10-20', '2025-07-27', 23, 1, 160.00, 55.00, 'A', false, NULL,
 '2024-11-15', '2025-01-10', NULL, NULL, 'HAMIL', 'Kehamilan pertama', NOW(), NOW()),

(2, 9, '2024-11-01', '2025-08-08', 20, 3, 158.00, 58.00, 'B', false, NULL,
 '2024-11-25', '2025-01-20', NULL, NULL, 'HAMIL', NULL, NOW(), NOW()),

-- Ibu hamil RESIKO TINGGI
(1, 11, '2024-08-10', '2025-05-17', 32, 1, 142.00, 45.00, 'AB', true, 
 'Tinggi badan < 145 cm, Usia < 20 tahun', '2024-09-05', '2024-11-10', '2025-01-15', '2025-03-10', 
 'HAMIL', '⚠️ RISTI: Tinggi badan kurang, usia muda', NOW(), NOW()),

(2, 13, '2024-07-20', '2025-04-26', 35, 5, 152.00, 62.00, 'O', true,
 'Kehamilan > 4, Usia > 35 tahun, Anemia (Hb < 11)', '2024-08-15', '2024-10-20', '2024-12-25', NULL,
 'HAMIL', '⚠️ RISTI: Grande multipara, anemia', NOW(), NOW()),

(1, 15, '2024-09-25', '2025-07-01', 26, 2, 148.00, 68.00, 'A', true,
 'Diabetes gestasional, Pre-eklampsia', '2024-10-20', '2024-12-18', NULL, NULL,
 'HAMIL', '⚠️ RISTI: Diabetes, tekanan darah tinggi', NOW(), NOW());

-- ===========================================  
-- 3. DATA PEMERIKSAAN BALITA
-- ===========================================
-- CATATAN: Sesuaikan penduduk_id dengan balita (anak < 5 tahun) di database Anda
-- Data ini mensimulasikan pemeriksaan rutin bulanan

-- Balita 1: Normal (penduduk_id: 20)
INSERT INTO kes_pemeriksaan (
    posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, berat_badan, tinggi_badan,
    lingkar_kepala, lingkar_lengan, vitamin_a, imunisasi, asi_eksklusif,
    status_gizi, z_score_bb_u, z_score_tb_u, z_score_bb_tb,
    indikasi_stunting, indikasi_gizi_buruk, keterangan, created_by, created_at, updated_at
) VALUES
(1, 20, '2024-06-15', 18, 10.50, 78.00, 45.5, 14.2, true, 'Campak, DPT', true, 
 'BAIK', 0.2, 0.1, 0.3, false, false, 'Pertumbuhan normal', 1, NOW(), NOW()),
(1, 20, '2024-07-15', 19, 10.80, 79.20, 45.8, 14.3, false, NULL, true,
 'BAIK', 0.3, 0.2, 0.3, false, false, 'Pertumbuhan baik', 1, NOW(), NOW()),
(1, 20, '2024-08-15', 20, 11.10, 80.50, 46.0, 14.5, false, NULL, true,
 'BAIK', 0.3, 0.2, 0.4, false, false, 'Sehat', 1, NOW(), NOW()),
(1, 20, '2024-09-15', 21, 11.40, 81.80, 46.2, 14.6, false, NULL, false,
 'BAIK', 0.4, 0.3, 0.4, false, false, 'Sudah MPASI', 1, NOW(), NOW()),
(1, 20, '2024-10-15', 22, 11.70, 83.00, 46.5, 14.8, false, NULL, false,
 'BAIK', 0.4, 0.3, 0.5, false, false, NULL, 1, NOW(), NOW()),
(1, 20, '2024-11-15', 23, 12.00, 84.20, 46.7, 15.0, false, NULL, false,
 'BAIK', 0.5, 0.4, 0.5, false, false, NULL, 1, NOW(), NOW()),
(1, 20, '2024-12-15', 24, 12.30, 85.50, 47.0, 15.2, true, 'Booster', false,
 'BAIK', 0.5, 0.4, 0.6, false, false, 'Perkembangan optimal', 1, NOW(), NOW());

-- Balita 2: STUNTING RINGAN (penduduk_id: 22)
INSERT INTO kes_pemeriksaan (
    posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, berat_badan, tinggi_badan,
    lingkar_kepala, lingkar_lengan, vitamin_a, imunisasi, asi_eksklusif,
    status_gizi, z_score_bb_u, z_score_tb_u, z_score_bb_tb,
    indikasi_stunting, indikasi_gizi_buruk, keterangan, created_by, created_at, updated_at
) VALUES
(1, 22, '2024-06-15', 24, 9.80, 78.00, 44.5, 13.0, false, 'DPT', false,
 'KURANG', -1.5, -2.3, -0.8, true, false, '⚠️ Terindikasi STUNTING - tinggi kurang', 1, NOW(), NOW()),
(1, 22, '2024-07-15', 25, 10.00, 78.80, 44.7, 13.2, true, NULL, false,
 'KURANG', -1.4, -2.2, -0.7, true, false, 'Intervensi gizi diberikan', 1, NOW(), NOW()),
(1, 22, '2024-08-15', 26, 10.30, 79.50, 44.9, 13.4, false, NULL, false,
 'KURANG', -1.3, -2.1, -0.7, true, false, 'Progres sedikit membaik', 1, NOW(), NOW()),
(1, 22, '2024-09-15', 27, 10.60, 80.20, 45.1, 13.6, false, NULL, false,
 'BAIK', -1.2, -2.0, -0.6, false, false, 'Mulai menunjukkan perbaikan', 1, NOW(), NOW()),
(1, 22, '2024-10-15', 28, 10.90, 81.00, 45.3, 13.8, false, NULL, false,
 'BAIK', -1.1, -1.9, -0.5, false, false, 'Terus monitoring', 1, NOW(), NOW()),
(1, 22, '2024-11-15', 29, 11.20, 81.80, 45.5, 14.0, false, NULL, false,
 'BAIK', -1.0, -1.8, -0.4, false, false, 'Perbaikan signifikan', 1, NOW(), NOW()),
(1, 22, '2024-12-15', 30, 11.50, 82.50, 45.7, 14.2, true, NULL, false,
 'BAIK', -0.9, -1.7, -0.3, false, false, 'Keluar dari kategori stunting', 1, NOW(), NOW());

-- Balita 3: STUNTING BERAT (penduduk_id: 24)
INSERT INTO kes_pemeriksaan (
    posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, berat_badan, tinggi_badan,
    lingkar_kepala, lingkar_lengan, vitamin_a, imunisasi, asi_eksklusif,
    status_gizi, z_score_bb_u, z_score_tb_u, z_score_bb_tb,
    indikasi_stunting, indikasi_gizi_buruk, keterangan, created_by, created_at, updated_at
) VALUES
(2, 24, '2024-06-15', 36, 10.50, 82.00, 44.0, 12.5, false, 'Lengkap', false,
 'BURUK', -2.8, -3.5, -2.1, true, true, '🚨 STUNTING BERAT + GIZI BURUK - butuh penanganan segera!', 1, NOW(), NOW()),
(2, 24, '2024-07-15', 37, 10.80, 82.50, 44.2, 12.7, true, NULL, false,
 'BURUK', -2.7, -3.4, -2.0, true, true, 'Program pemulihan gizi dimulai', 1, NOW(), NOW()),
(2, 24, '2024-08-15', 38, 11.20, 83.20, 44.4, 13.0, false, NULL, false,
 'KURANG', -2.5, -3.2, -1.8, true, false, 'Respon positif terhadap intervensi', 1, NOW(), NOW()),
(2, 24, '2024-09-15', 39, 11.60, 84.00, 44.6, 13.3, false, NULL, false,
 'KURANG', -2.3, -3.0, -1.6, true, false, 'Terus membaik', 1, NOW(), NOW()),
(2, 24, '2024-10-15', 40, 12.00, 84.80, 44.8, 13.6, false, NULL, false,
 'KURANG', -2.1, -2.8, -1.4, true, false, 'Progres baik', 1, NOW(), NOW()),
(2, 24, '2024-11-15', 41, 12.40, 85.50, 45.0, 13.9, false, NULL, false,
 'BAIK', -1.9, -2.6, -1.2, true, false, 'Masih monitoring ketat', 1, NOW(), NOW()),
(2, 24, '2024-12-15', 42, 12.80, 86.20, 45.2, 14.2, true, NULL, false,
 'BAIK', -1.7, -2.4, -1.0, true, false, 'Perkembangan menggembirakan', 1, NOW(), NOW());

-- Balita 4: Normal dengan gizi lebih (penduduk_id: 26)
INSERT INTO kes_pemeriksaan (
    posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, berat_badan, tinggi_badan,
    lingkar_kepala, lingkar_lengan, vitamin_a, imunisasi, asi_eksklusif,
    status_gizi, z_score_bb_u, z_score_tb_u, z_score_bb_tb,
    indikasi_stunting, indikasi_gizi_buruk, keterangan, created_by, created_at, updated_at
) VALUES
(2, 26, '2024-10-15', 12, 11.20, 74.50, 45.0, 15.5, false, 'BCG, Polio, DPT', true,
 'LEBIH', 2.3, 0.5, 2.8, false, false, 'Berat badan berlebih, perlu pengaturan MPASI', 1, NOW(), NOW()),
(2, 26, '2024-11-15', 13, 11.40, 75.80, 45.3, 15.6, false, NULL, false,
 'LEBIH', 2.2, 0.6, 2.6, false, false, 'Edukasi gizi untuk orangtua', 1, NOW(), NOW()),
(2, 26, '2024-12-15', 14, 11.50, 77.00, 45.5, 15.5, true, NULL, false,
 'BAIK', 2.0, 0.7, 2.3, false, false, 'Mulai terkontrol', 1, NOW(), NOW());

-- Balita 5: Normal (penduduk_id: 28)
INSERT INTO kes_pemeriksaan (
    posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, berat_badan, tinggi_badan,
    lingkar_kepala, lingkar_lengan, vitamin_a, imunisasi, asi_eksklusif,
    status_gizi, z_score_bb_u, z_score_tb_u, z_score_bb_tb,
    indikasi_stunting, indikasi_gizi_buruk, keterangan, created_by, created_at, updated_at
) VALUES
(2, 28, '2024-11-15', 6, 7.80, 66.50, 42.5, 13.8, false, 'BCG, Polio 1', true,
 'BAIK', 0.2, 0.3, 0.1, false, false, 'ASI Eksklusif lancar', 1, NOW(), NOW()),
(2, 28, '2024-12-15', 7, 8.10, 67.80, 42.8, 14.0, false, 'DPT 1', true,
 'BAIK', 0.3, 0.4, 0.2, false, false, 'Sehat', 1, NOW(), NOW());

-- ===========================================
-- 4. STANDAR WHO (Sample data untuk perhitungan Z-Score)
-- ===========================================
-- Data ini adalah sampel sederhana. Untuk implementasi lengkap,
-- gunakan tabel standar WHO resmi yang lebih komprehensif

-- Standar TB/U (Tinggi Badan untuk Usia) - Laki-laki
INSERT INTO kes_standar_who (jenis_kelamin, usia_bulan, indikator, median, sd_min3, sd_min2, sd_min1, sd_plus1, sd_plus2, sd_plus3) VALUES
('L', 6, 'TB_U', 67.6, 61.2, 63.3, 65.5, 69.8, 72.0, 74.2),
('L', 12, 'TB_U', 75.7, 69.0, 71.0, 73.4, 78.1, 80.5, 82.9),
('L', 18, 'TB_U', 82.3, 75.0, 77.2, 79.6, 85.1, 87.7, 90.4),
('L', 24, 'TB_U', 87.8, 79.9, 82.3, 85.1, 90.4, 93.2, 96.1),
('L', 30, 'TB_U', 92.3, 84.1, 86.7, 89.5, 95.2, 98.1, 101.1),
('L', 36, 'TB_U', 96.1, 87.8, 90.5, 93.4, 98.8, 101.9, 105.0),
('L', 42, 'TB_U', 99.6, 91.2, 94.1, 97.0, 102.3, 105.5, 108.8);

-- Standar TB/U - Perempuan  
INSERT INTO kes_standar_who (jenis_kelamin, usia_bulan, indikator, median, sd_min3, sd_min2, sd_min1, sd_plus1, sd_plus2, sd_plus3) VALUES
('P', 6, 'TB_U', 65.7, 59.6, 61.7, 63.7, 67.8, 69.9, 72.0),
('P', 12, 'TB_U', 74.0, 67.3, 69.2, 71.4, 76.6, 78.9, 81.2),
('P', 18, 'TB_U', 80.7, 73.7, 75.8, 78.0, 83.4, 85.9, 88.4),
('P', 24, 'TB_U', 86.4, 79.3, 81.7, 84.0, 88.9, 91.4, 93.9),
('P', 30, 'TB_U', 91.3, 83.9, 86.4, 88.9, 93.7, 96.4, 99.0),
('P', 36, 'TB_U', 95.6, 87.9, 90.7, 93.2, 98.1, 100.9, 103.7),
('P', 42, 'TB_U', 99.3, 91.5, 94.4, 97.0, 101.7, 104.6, 107.5);

-- Standar BB/U (Berat Badan untuk Usia) - Laki-laki
INSERT INTO kes_standar_who (jenis_kelamin, usia_bulan, indikator, median, sd_min3, sd_min2, sd_min1, sd_plus1, sd_plus2, sd_plus3) VALUES
('L', 6, 'BB_U', 7.9, 5.7, 6.4, 7.1, 8.8, 9.8, 10.9),
('L', 12, 'BB_U', 9.6, 7.1, 7.8, 8.6, 10.6, 11.8, 13.0),
('L', 18, 'BB_U', 10.9, 8.1, 8.9, 9.8, 12.0, 13.3, 14.8),
('L', 24, 'BB_U', 12.2, 9.1, 10.0, 11.0, 13.5, 15.0, 16.7),
('L', 30, 'BB_U', 13.3, 9.9, 10.9, 12.0, 14.7, 16.3, 18.2),
('L', 36, 'BB_U', 14.3, 10.7, 11.8, 13.0, 15.8, 17.6, 19.7),
('L', 42, 'BB_U', 15.2, 11.4, 12.6, 13.9, 16.8, 18.8, 21.1);

-- Standar BB/U - Perempuan
INSERT INTO kes_standar_who (jenis_kelamin, usia_bulan, indikator, median, sd_min3, sd_min2, sd_min1, sd_plus1, sd_plus2, sd_plus3) VALUES
('P', 6, 'BB_U', 7.3, 5.3, 5.9, 6.5, 8.2, 9.3, 10.4),
('P', 12, 'BB_U', 9.0, 6.6, 7.3, 8.1, 10.0, 11.2, 12.5),
('P', 18, 'BB_U', 10.2, 7.6, 8.4, 9.3, 11.3, 12.6, 14.1),
('P', 24, 'BB_U', 11.5, 8.5, 9.4, 10.4, 12.7, 14.2, 15.8),
('P', 30, 'BB_U', 12.7, 9.4, 10.4, 11.5, 14.0, 15.7, 17.5),
('P', 36, 'BB_U', 13.9, 10.2, 11.3, 12.5, 15.3, 17.1, 19.2),
('P', 42, 'BB_U', 14.9, 10.9, 12.1, 13.4, 16.4, 18.4, 20.7);

-- ===========================================
-- SUMMARY
-- ===========================================
SELECT 'Dummy data e-Posyandu berhasil diinput!' AS status,
       (SELECT COUNT(*) FROM kes_kader) AS total_kader,
       (SELECT COUNT(*) FROM kes_ibu_hamil) AS total_ibu_hamil,
       (SELECT COUNT(*) FROM kes_pemeriksaan) AS total_pemeriksaan,
       (SELECT COUNT(*) FROM kes_standar_who) AS total_standar_who;

-- ===========================================
-- CATATAN PENTING:
-- ===========================================
-- 1. Sesuaikan penduduk_id dengan data balita dan WUS yang ada di database Anda
-- 2. Data standar WHO di atas hanya sampel. Untuk produksi, gunakan data lengkap WHO
--    dari https://www.who.int/tools/child-growth-standards
-- 3. Pastikan posyandu_id sesuai dengan data yang sudah diinput sebelumnya
-- 4. created_by menggunakan user_id = 1 (admin), sesuaikan dengan user Anda
