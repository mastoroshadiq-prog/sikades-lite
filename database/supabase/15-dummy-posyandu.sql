-- ===========================================
-- SIKADES LITE - DUMMY DATA E-POSYANDU (AUTO)
-- Migration: 15-dummy-posyandu.sql
-- Date: 2025-12-25
-- ===========================================
-- Script ini otomatis mengambil penduduk_id dari data yang sudah ada

-- ===========================================
-- 1. KADER POSYANDU
-- ===========================================
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
-- Otomatis mengambil 6 wanita usia subur (15-49 tahun) dari database

DO $$
DECLARE
    v_wus_ids INTEGER[];
    v_posyandu_id INTEGER := 1;
BEGIN
    -- Ambil ID wanita usia subur yang belum hamil
    SELECT ARRAY(
        SELECT p.id 
        FROM pop_penduduk p
        JOIN pop_keluarga k ON k.id = p.keluarga_id
        WHERE p.jenis_kelamin = 'P' 
        AND p.status_dasar = 'HIDUP'
        AND EXTRACT(YEAR FROM AGE(CURRENT_DATE, p.tanggal_lahir))::int BETWEEN 15 AND 49
        AND p.id NOT IN (SELECT penduduk_id FROM kes_ibu_hamil WHERE status = 'HAMIL')
        ORDER BY p.tanggal_lahir DESC
        LIMIT 6
    ) INTO v_wus_ids;

    -- Jika ada minimal 6 WUS
    IF array_length(v_wus_ids, 1) >= 6 THEN
        -- Ibu hamil normal 1
        INSERT INTO kes_ibu_hamil (posyandu_id, penduduk_id, tanggal_hpht, taksiran_persalinan, usia_kandungan, 
            kehamilan_ke, tinggi_badan_ibu, berat_badan_sebelum, golongan_darah, resiko_tinggi, faktor_resiko,
            pemeriksaan_k1, pemeriksaan_k2, pemeriksaan_k3, pemeriksaan_k4, status, keterangan, created_at, updated_at)
        VALUES (1, v_wus_ids[1], '2024-09-15', '2025-06-22', 28, 2, 155.00, 52.00, 'O', false, NULL,
                '2024-10-10', '2024-12-15', '2025-02-10', NULL, 'HAMIL', 'Kondisi baik', NOW(), NOW());

        -- Ibu hamil normal 2
        INSERT INTO kes_ibu_hamil (posyandu_id, penduduk_id, tanggal_hpht, taksiran_persalinan, usia_kandungan,
            kehamilan_ke, tinggi_badan_ibu, berat_badan_sebelum, golongan_darah, resiko_tinggi, faktor_resiko,
            pemeriksaan_k1, pemeriksaan_k2, status, keterangan, created_at, updated_at)
        VALUES (1, v_wus_ids[2], '2024-10-20', '2025-07-27', 23, 1, 160.00, 55.00, 'A', false, NULL,
                '2024-11-15', '2025-01-10', 'HAMIL', 'Kehamilan pertama', NOW(), NOW());

        -- Ibu hamil normal 3
        INSERT INTO kes_ibu_hamil (posyandu_id, penduduk_id, tanggal_hpht, taksiran_persalinan, usia_kandungan,
            kehamilan_ke, tinggi_badan_ibu, berat_badan_sebelum, golongan_darah, resiko_tinggi, faktor_resiko,
            pemeriksaan_k1, pemeriksaan_k2, status, created_at, updated_at)
        VALUES (2, v_wus_ids[3], '2024-11-01', '2025-08-08', 20, 3, 158.00, 58.00, 'B', false, NULL,
                '2024-11-25', '2025-01-20', 'HAMIL', NOW(), NOW());

        -- Ibu hamil RISTI 1
        INSERT INTO kes_ibu_hamil (posyandu_id, penduduk_id, tanggal_hpht, taksiran_persalinan, usia_kandungan,
            kehamilan_ke, tinggi_badan_ibu, berat_badan_sebelum, golongan_darah, resiko_tinggi, faktor_resiko,
            pemeriksaan_k1, pemeriksaan_k2, pemeriksaan_k3, pemeriksaan_k4, status, keterangan, created_at, updated_at)
        VALUES (1, v_wus_ids[4], '2024-08-10', '2025-05-17', 32, 1, 142.00, 45.00, 'AB', true,
                'Tinggi badan < 145 cm, Usia < 20 tahun', '2024-09-05', '2024-11-10', '2025-01-15', '2025-03-10',
                'HAMIL', '⚠️ RISTI: Tinggi badan kurang, usia muda', NOW(), NOW());

        -- Ibu hamil RISTI 2
        INSERT INTO kes_ibu_hamil (posyandu_id, penduduk_id, tanggal_hpht, taksiran_persalinan, usia_kandungan,
            kehamilan_ke, tinggi_badan_ibu, berat_badan_sebelum, golongan_darah, resiko_tinggi, faktor_resiko,
            pemeriksaan_k1, pemeriksaan_k2, pemeriksaan_k3, status, keterangan, created_at, updated_at)
        VALUES (2, v_wus_ids[5], '2024-07-20', '2025-04-26', 35, 5, 152.00, 62.00, 'O', true,
                'Kehamilan > 4, Usia > 35 tahun, Anemia (Hb < 11)', '2024-08-15', '2024-10-20', '2024-12-25',
                'HAMIL', '⚠️ RISTI: Grande multipara, anemia', NOW(), NOW());

        -- Ibu hamil RISTI 3
        INSERT INTO kes_ibu_hamil (posyandu_id, penduduk_id, tanggal_hpht, taksiran_persalinan, usia_kandungan,
            kehamilan_ke, tinggi_badan_ibu, berat_badan_sebelum, golongan_darah, resiko_tinggi, faktor_resiko,
            pemeriksaan_k1, pemeriksaan_k2, status, keterangan, created_at, updated_at)
        VALUES (1, v_wus_ids[6], '2024-09-25', '2025-07-01', 26, 2, 148.00, 68.00, 'A', true,
                'Diabetes gestasional, Pre-eklampsia', '2024-10-20', '2024-12-18',
                'HAMIL', '⚠️ RISTI: Diabetes, tekanan darah tinggi', NOW(), NOW());

        RAISE NOTICE 'Data ibu hamil berhasil diinput: % record', array_length(v_wus_ids, 1);
    ELSE
        RAISE NOTICE 'Tidak cukup data WUS (butuh minimal 6, tersedia: %)', COALESCE(array_length(v_wus_ids, 1), 0);
    END IF;
END $$;

-- ===========================================  
-- 3. DATA PEMERIKSAAN BALITA
-- ===========================================
-- Otomatis mengambil balita (< 5 tahun) dari database

DO $$
DECLARE
    v_balita_ids INTEGER[];
    balita_id INTEGER;
    idx INTEGER := 1;
BEGIN
    -- Ambil ID balita yang belum pernah diperiksa atau sudah lama
    SELECT ARRAY(
        SELECT p.id 
        FROM pop_penduduk p
        JOIN pop_keluarga k ON k.id = p.keluarga_id
        WHERE p.status_dasar = 'HIDUP'
        AND EXTRACT(YEAR FROM AGE(CURRENT_DATE, p.tanggal_lahir))::int < 5
        ORDER BY p.tanggal_lahir DESC
        LIMIT 5
    ) INTO v_balita_ids;

    IF array_length(v_balita_ids, 1) >= 5 THEN
        -- BALITA 1: Normal
        balita_id := v_balita_ids[1];
        INSERT INTO kes_pemeriksaan (posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, berat_badan, tinggi_badan,
            lingkar_kepala, lingkar_lengan, vitamin_a, imunisasi, asi_eksklusif, status_gizi, z_score_bb_u, z_score_tb_u,
            z_score_bb_tb, indikasi_stunting, indikasi_gizi_buruk, keterangan, created_by, created_at, updated_at)
        SELECT 1, balita_id, d::date, 
               EXTRACT(MONTH FROM AGE(d::date, p.tanggal_lahir))::int,
               10.50 + (seq * 0.30), 78.00 + (seq * 1.20), 45.5 + (seq * 0.25), 14.2 + (seq * 0.10),
               (seq = 0 OR seq = 6), 
               CASE WHEN seq = 0 THEN 'Campak, DPT' WHEN seq = 6 THEN 'Booster' ELSE NULL END,
               (seq < 3),
               'BAIK', 0.2 + (seq * 0.05), 0.1 + (seq * 0.05), 0.3 + (seq * 0.05),
               false, false,
               CASE WHEN seq = 0 THEN 'Pertumbuhan normal' WHEN seq = 6 THEN 'Perkembangan optimal' ELSE NULL END,
               1, NOW(), NOW()
        FROM generate_series(0, 6) seq,
             LATERAL (SELECT '2024-06-15'::date + (seq * INTERVAL '1 month')) AS d(d),
             pop_penduduk p
        WHERE p.id = balita_id;

        -- BALITA 2: Stunting ringan → Recovery
        balita_id := v_balita_ids[2];
        INSERT INTO kes_pemeriksaan (posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, berat_badan, tinggi_badan,
            lingkar_kepala, lingkar_lengan, vitamin_a, imunisasi, asi_eksklusif, status_gizi, z_score_bb_u, z_score_tb_u,
            z_score_bb_tb, indikasi_stunting, indikasi_gizi_buruk, keterangan, created_by, created_at, updated_at)
        SELECT 1, balita_id, d::date,
               EXTRACT(MONTH FROM AGE(d::date, p.tanggal_lahir))::int,
               9.80 + (seq * 0.30), 78.00 + (seq * 0.80), 44.5 + (seq * 0.20), 13.0 + (seq * 0.20),
               (seq = 1 OR seq = 6), NULL, false,
               CASE WHEN seq >= 3 THEN 'BAIK' ELSE 'KURANG' END,
               -1.5 + (seq * 0.10), -2.3 + (seq * 0.10), -0.8 + (seq * 0.05),
               (seq < 3), false,
               CASE WHEN seq = 0 THEN '⚠️ Terindikasi STUNTING' 
                    WHEN seq = 1 THEN 'Intervensi gizi diberikan'
                    WHEN seq = 6 THEN 'Keluar dari kategori stunting' ELSE NULL END,
               1, NOW(), NOW()
        FROM generate_series(0, 6) seq,
             LATERAL (SELECT '2024-06-15'::date + (seq * INTERVAL '1 month')) AS d(d),
             pop_penduduk p
        WHERE p.id = balita_id;

        -- BALITA 3: Stunting berat + gizi buruk → Pemulihan
        balita_id := v_balita_ids[3];
        INSERT INTO kes_pemeriksaan (posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, berat_badan, tinggi_badan,
            lingkar_kepala, lingkar_lengan, vitamin_a, imunisasi, asi_eksklusif, status_gizi, z_score_bb_u, z_score_tb_u,
            z_score_bb_tb, indikasi_stunting, indikasi_gizi_buruk, keterangan, created_by, created_at, updated_at)
        SELECT 2, balita_id, d::date,
               EXTRACT(MONTH FROM AGE(d::date, p.tanggal_lahir))::int,
               10.50 + (seq * 0.40), 82.00 + (seq * 0.70), 44.0 + (seq * 0.20), 12.5 + (seq * 0.25),
               (seq = 1 OR seq = 6), CASE WHEN seq = 0 THEN 'Lengkap' ELSE NULL END, false,
               CASE WHEN seq < 2 THEN 'BURUK' WHEN seq < 5 THEN 'KURANG' ELSE 'BAIK' END,
               -2.8 + (seq * 0.15), -3.5 + (seq * 0.15), -2.1 + (seq * 0.15),
               true, (seq < 2),
               CASE WHEN seq = 0 THEN '🚨 STUNTING BERAT + GIZI BURUK'
                    WHEN seq = 1 THEN 'Program pemulihan dimulai'
                    WHEN seq = 6 THEN 'Perkembangan menggembirakan' ELSE NULL END,
               1, NOW(), NOW()
        FROM generate_series(0, 6) seq,
             LATERAL (SELECT '2024-06-15'::date + (seq * INTERVAL '1 month')) AS d(d),
             pop_penduduk p
        WHERE p.id = balita_id;

        -- BALITA 4: Gizi lebih → Terkontrol
        balita_id := v_balita_ids[4];
        INSERT INTO kes_pemeriksaan (posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, berat_badan, tinggi_badan,
            lingkar_kepala, lingkar_lengan, vitamin_a, imunisasi, asi_eksklusif, status_gizi, z_score_bb_u, z_score_tb_u,
            z_score_bb_tb, indikasi_stunting, indikasi_gizi_buruk, keterangan, created_by, created_at, updated_at)
        SELECT 2, balita_id, d::date,
               EXTRACT(MONTH FROM AGE(d::date, p.tanggal_lahir))::int,
               11.20 + (seq * 0.15), 74.50 + (seq * 1.25), 45.0 + (seq * 0.25), 15.5 - (seq * 0.05),
               (seq = 2), CASE WHEN seq = 0 THEN 'BCG, Polio, DPT' ELSE NULL END, (seq = 0),
               CASE WHEN seq < 2 THEN 'LEBIH' ELSE 'BAIK' END,
               2.3 - (seq * 0.15), 0.5 + (seq * 0.05), 2.8 - (seq * 0.25),
               false, false,
               CASE WHEN seq = 0 THEN 'Berat badan berlebih' WHEN seq = 2 THEN 'Mulai terkontrol' ELSE NULL END,
               1, NOW(), NOW()
        FROM generate_series(0, 2) seq,
             LATERAL (SELECT '2024-10-15'::date + (seq * INTERVAL '1 month')) AS d(d),
             pop_penduduk p
        WHERE p.id = balita_id;

        -- BALITA 5: Normal ASI eksklusif
        balita_id := v_balita_ids[5];
        INSERT INTO kes_pemeriksaan (posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, berat_badan, tinggi_badan,
            lingkar_kepala, lingkar_lengan, vitamin_a, imunisasi, asi_eksklusif, status_gizi, z_score_bb_u, z_score_tb_u,
            z_score_bb_tb, indikasi_stunting, indikasi_gizi_buruk, keterangan, created_by, created_at, updated_at)
        SELECT 2, balita_id, d::date,
               EXTRACT(MONTH FROM AGE(d::date, p.tanggal_lahir))::int,
               7.80 + (seq * 0.30), 66.50 + (seq * 1.30), 42.5 + (seq * 0.30), 13.8 + (seq * 0.20),
               false, CASE WHEN seq = 0 THEN 'BCG, Polio 1' WHEN seq = 1 THEN 'DPT 1' ELSE NULL END, true,
               'BAIK', 0.2 + (seq * 0.10), 0.3 + (seq * 0.10), 0.1 + (seq * 0.10),
               false, false,
               CASE WHEN seq = 0 THEN 'ASI Eksklusif lancar' ELSE 'Sehat' END,
               1, NOW(), NOW()
        FROM generate_series(0, 1) seq,
             LATERAL (SELECT '2024-11-15'::date + (seq * INTERVAL '1 month')) AS d(d),
             pop_penduduk p
        WHERE p.id = balita_id;

        RAISE NOTICE 'Data pemeriksaan balita berhasil diinput untuk % balita', array_length(v_balita_ids, 1);
    ELSE
        RAISE NOTICE 'Tidak cukup data balita (butuh minimal 5, tersedia: %)', COALESCE(array_length(v_balita_ids, 1), 0);
    END IF;
END $$;

-- ===========================================
-- 4. STANDAR WHO (Sample untuk Z-Score)
-- ===========================================
INSERT INTO kes_standar_who (jenis_kelamin, usia_bulan, indikator, median, sd_min3, sd_min2, sd_min1, sd_plus1, sd_plus2, sd_plus3) VALUES
-- TB/U Laki-laki  
('L', 6, 'TB_U', 67.6, 61.2, 63.3, 65.5, 69.8, 72.0, 74.2),
('L', 12, 'TB_U', 75.7, 69.0, 71.0, 73.4, 78.1, 80.5, 82.9),
('L', 18, 'TB_U', 82.3, 75.0, 77.2, 79.6, 85.1, 87.7, 90.4),
('L', 24, 'TB_U', 87.8, 79.9, 82.3, 85.1, 90.4, 93.2, 96.1),
('L', 30, 'TB_U', 92.3, 84.1, 86.7, 89.5, 95.2, 98.1, 101.1),
('L', 36, 'TB_U', 96.1, 87.8, 90.5, 93.4, 98.8, 101.9, 105.0),
('L', 42, 'TB_U', 99.6, 91.2, 94.1, 97.0, 102.3, 105.5, 108.8),
-- TB/U Perempuan
('P', 6, 'TB_U', 65.7, 59.6, 61.7, 63.7, 67.8, 69.9, 72.0),
('P', 12, 'TB_U', 74.0, 67.3, 69.2, 71.4, 76.6, 78.9, 81.2),
('P', 18, 'TB_U', 80.7, 73.7, 75.8, 78.0, 83.4, 85.9, 88.4),
('P', 24, 'TB_U', 86.4, 79.3, 81.7, 84.0, 88.9, 91.4, 93.9),
('P', 30, 'TB_U', 91.3, 83.9, 86.4, 88.9, 93.7, 96.4, 99.0),
('P', 36, 'TB_U', 95.6, 87.9, 90.7, 93.2, 98.1, 100.9, 103.7),
('P', 42, 'TB_U', 99.3, 91.5, 94.4, 97.0, 101.7, 104.6, 107.5),
-- BB/U Laki-laki
('L', 6, 'BB_U', 7.9, 5.7, 6.4, 7.1, 8.8, 9.8, 10.9),
('L', 12, 'BB_U', 9.6, 7.1, 7.8, 8.6, 10.6, 11.8, 13.0),
('L', 18, 'BB_U', 10.9, 8.1, 8.9, 9.8, 12.0, 13.3, 14.8),
('L', 24, 'BB_U', 12.2, 9.1, 10.0, 11.0, 13.5, 15.0, 16.7),
('L', 30, 'BB_U', 13.3, 9.9, 10.9, 12.0, 14.7, 16.3, 18.2),
('L', 36, 'BB_U', 14.3, 10.7, 11.8, 13.0, 15.8, 17.6, 19.7),
('L', 42, 'BB_U', 15.2, 11.4, 12.6, 13.9, 16.8, 18.8, 21.1),
-- BB/U Perempuan
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
       (SELECT COUNT(*) FROM kes_ibu_hamil WHERE status = 'HAMIL') AS total_ibu_hamil,
       (SELECT COUNT(DISTINCT penduduk_id) FROM kes_pemeriksaan) AS total_balita_terpantau,
       (SELECT COUNT(*) FROM kes_pemeriksaan) AS total_pemeriksaan,
       (SELECT COUNT(*) FROM kes_standar_who) AS total_standar_who;
