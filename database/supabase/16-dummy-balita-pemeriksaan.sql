-- ===========================================
-- SIKADES LITE - DUMMY BALITA DAN PEMERIKSAAN
-- File: 16-dummy-balita-pemeriksaan.sql
-- Date: 2025-12-25
-- ===========================================
-- Script ini akan membuat dummy balita jika tidak ada,
-- lalu mengisi data pemeriksaannya

DO $$
DECLARE
    v_kode_desa VARCHAR(20);
    v_keluarga_id INTEGER;
    v_balita_ids INTEGER[];
    balita_id INTEGER;
    v_count INTEGER;
BEGIN
    -- Ambil kode_desa dari data yang sudah ada
    SELECT kode_desa INTO v_kode_desa
    FROM pop_keluarga
    LIMIT 1;

    -- Cek apakah ada balita
    SELECT COUNT(*) INTO v_count
    FROM pop_penduduk p
    JOIN pop_keluarga k ON k.id = p.keluarga_id
    WHERE p.status_dasar = 'HIDUP'
    AND EXTRACT(YEAR FROM AGE(CURRENT_DATE, p.tanggal_lahir))::int < 5;

    RAISE NOTICE 'Jumlah balita yang ada di database: %', v_count;

    -- Jika tidak ada balita, buat dummy balita
    IF v_count < 5 THEN
        RAISE NOTICE 'Membuat dummy balita karena data kurang...';
        
        -- Ambil/buat keluarga untuk dummy balita
        SELECT id INTO v_keluarga_id
        FROM pop_keluarga
        WHERE kode_desa = v_kode_desa
        LIMIT 1;

        -- Jika tidak ada keluarga, buat satu
        IF v_keluarga_id IS NULL THEN
            INSERT INTO pop_keluarga (kode_desa, no_kk, kepala_keluarga, alamat, rt, rw, dusun, created_at, updated_at)
            VALUES (v_kode_desa, '3201010001999999', 'Keluarga Test Posyandu', 'Jl. Posyandu No. 1', '01', '01', 'Krajan', NOW(), NOW())
            RETURNING id INTO v_keluarga_id;
        END IF;

        -- Buat 5 dummy balita
        INSERT INTO pop_penduduk (
            kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, 
            jenis_kelamin, agama, status_hubungan, status_dasar, 
            created_at, updated_at
        ) VALUES
        (v_kode_desa, v_keluarga_id, '3201010001010201', 'Anak Test 1 (Normal)', 'Jakarta', '2022-06-15', 'L', 'Islam', 'Anak', 'HIDUP', NOW(), NOW()),
        (v_kode_desa, v_keluarga_id, '3201010001010202', 'Anak Test 2 (Stunting Ringan)', 'Jakarta', '2022-06-15', 'L', 'Islam', 'Anak', 'HIDUP', NOW(), NOW()),
        (v_kode_desa, v_keluarga_id, '3201010001010203', 'Anak Test 3 (Stunting Berat)', 'Jakarta', '2021-06-15', 'L', 'Islam', 'Anak', 'HIDUP', NOW(), NOW()),
        (v_kode_desa, v_keluarga_id, '3201010001010204', 'Anak Test 4 (Gizi Lebih)', 'Jakarta', '2023-10-15', 'P', 'Islam', 'Anak', 'HIDUP', NOW(), NOW()),
        (v_kode_desa, v_keluarga_id, '3201010001010205', 'Anak Test 5 (ASI Eksklusif)', 'Jakarta', '2024-05-15', 'P', 'Islam', 'Anak', 'HIDUP', NOW(), NOW());

        RAISE NOTICE 'Berhasil membuat 5 dummy balita';
    END IF;

    -- Ambil ID balita (dari yang ada atau yang baru dibuat)
    SELECT ARRAY(
        SELECT p.id 
        FROM pop_penduduk p
        JOIN pop_keluarga k ON k.id = p.keluarga_id
        WHERE p.status_dasar = 'HIDUP'
        AND EXTRACT(YEAR FROM AGE(CURRENT_DATE, p.tanggal_lahir))::int < 5
        ORDER BY p.id
        LIMIT 5
    ) INTO v_balita_ids;

    RAISE NOTICE 'ID Balita yang akan digunakan: %', v_balita_ids;

    -- Buat data pemeriksaan untuk setiap balita
    IF array_length(v_balita_ids, 1) >= 5 THEN
        
        -- BALITA 1: Normal (7 pemeriksaan)
        balita_id := v_balita_ids[1];
        RAISE NOTICE 'Membuat pemeriksaan untuk balita 1 (ID: %)', balita_id;
        
        INSERT INTO kes_pemeriksaan (
            posyandu_id, penduduk_id, tanggal_periksa, usia_bulan, 
            berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan,
            vitamin_a, imunisasi, asi_eksklusif, 
            status_gizi, z_score_bb_u, z_score_tb_u, z_score_bb_tb,
            indikasi_stunting, indikasi_gizi_buruk, keterangan, 
            created_by, created_at, updated_at
        )
        SELECT 
            1, balita_id, 
            ('2024-06-15'::date + (seq * INTERVAL '1 month'))::date,
            18 + seq,
            10.50 + (seq * 0.30), 
            78.00 + (seq * 1.20), 
            45.5 + (seq * 0.25), 
            14.2 + (seq * 0.10),
            (seq = 0 OR seq = 6), 
            CASE WHEN seq = 0 THEN 'Campak, DPT' WHEN seq = 6 THEN 'Booster' ELSE NULL END,
            (seq < 3),
            'BAIK', 
            0.2 + (seq * 0.05), 
            0.1 + (seq * 0.05), 
            0.3 + (seq * 0.05),
            false, false,
            CASE WHEN seq = 0 THEN 'Pertumbuhan normal' 
                 WHEN seq = 6 THEN 'Perkembangan optimal' 
                 ELSE NULL END,
            1, NOW(), NOW()
        FROM generate_series(0, 6) seq;

        -- BALITA 2: Stunting ringan → Recovery (7 pemeriksaan)
        balita_id := v_balita_ids[2];
        RAISE NOTICE 'Membuat pemeriksaan untuk balita 2 (ID: %)', balita_id;
        
        INSERT INTO kes_pemeriksaan (
            posyandu_id, penduduk_id, tanggal_periksa, usia_bulan,
            berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan,
            vitamin_a, imunisasi, asi_eksklusif,
            status_gizi, z_score_bb_u, z_score_tb_u, z_score_bb_tb,
            indikasi_stunting, indikasi_gizi_buruk, keterangan,
            created_by, created_at, updated_at
        )
        SELECT 
            1, balita_id,
            ('2024-06-15'::date + (seq * INTERVAL '1 month'))::date,
            24 + seq,
            9.80 + (seq * 0.30), 
            78.00 + (seq * 0.80), 
            44.5 + (seq * 0.20), 
            13.0 + (seq * 0.20),
            (seq = 1 OR seq = 6), NULL, false,
            CASE WHEN seq >= 3 THEN 'BAIK' ELSE 'KURANG' END,
            -1.5 + (seq * 0.10), 
            -2.3 + (seq * 0.10), 
            -0.8 + (seq * 0.05),
            (seq < 3), false,
            CASE WHEN seq = 0 THEN '⚠️ Terindikasi STUNTING - tinggi kurang' 
                 WHEN seq = 1 THEN 'Intervensi gizi diberikan'
                 WHEN seq = 6 THEN 'Keluar dari kategori stunting'
                 ELSE NULL END,
            1, NOW(), NOW()
        FROM generate_series(0, 6) seq;

        -- BALITA 3: Stunting berat + gizi buruk → Pemulihan (7 pemeriksaan)
        balita_id := v_balita_ids[3];
        RAISE NOTICE 'Membuat pemeriksaan untuk balita 3 (ID: %)', balita_id;
        
        INSERT INTO kes_pemeriksaan (
            posyandu_id, penduduk_id, tanggal_periksa, usia_bulan,
            berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan,
            vitamin_a, imunisasi, asi_eksklusif,
            status_gizi, z_score_bb_u, z_score_tb_u, z_score_bb_tb,
            indikasi_stunting, indikasi_gizi_buruk, keterangan,
            created_by, created_at, updated_at
        )
        SELECT 
            2, balita_id,
            ('2024-06-15'::date + (seq * INTERVAL '1 month'))::date,
            36 + seq,
            10.50 + (seq * 0.40), 
            82.00 + (seq * 0.70), 
            44.0 + (seq * 0.20), 
            12.5 + (seq * 0.25),
            (seq = 1 OR seq = 6), 
            CASE WHEN seq = 0 THEN 'Lengkap' ELSE NULL END, 
            false,
            CASE WHEN seq < 2 THEN 'BURUK' 
                 WHEN seq < 5 THEN 'KURANG' 
                 ELSE 'BAIK' END,
            -2.8 + (seq * 0.15), 
            -3.5 + (seq * 0.15), 
            -2.1 + (seq * 0.15),
            true, 
            (seq < 2),
            CASE WHEN seq = 0 THEN '🚨 STUNTING BERAT + GIZI BURUK - butuh penanganan segera!'
                 WHEN seq = 1 THEN 'Program pemulihan gizi dimulai'
                 WHEN seq = 6 THEN 'Perkembangan menggembirakan'
                 ELSE NULL END,
            1, NOW(), NOW()
        FROM generate_series(0, 6) seq;

        -- BALITA 4: Gizi lebih → Terkontrol (3 pemeriksaan)
        balita_id := v_balita_ids[4];
        RAISE NOTICE 'Membuat pemeriksaan untuk balita 4 (ID: %)', balita_id;
        
        INSERT INTO kes_pemeriksaan (
            posyandu_id, penduduk_id, tanggal_periksa, usia_bulan,
            berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan,
            vitamin_a, imunisasi, asi_eksklusif,
            status_gizi, z_score_bb_u, z_score_tb_u, z_score_bb_tb,
            indikasi_stunting, indikasi_gizi_buruk, keterangan,
            created_by, created_at, updated_at
        )
        SELECT 
            2, balita_id,
            ('2024-10-15'::date + (seq * INTERVAL '1 month'))::date,
            12 + seq,
            11.20 + (seq * 0.15), 
            74.50 + (seq * 1.25), 
            45.0 + (seq * 0.25), 
            15.5 - (seq * 0.05),
            (seq = 2), 
            CASE WHEN seq = 0 THEN 'BCG, Polio, DPT' ELSE NULL END, 
            (seq = 0),
            CASE WHEN seq < 2 THEN 'LEBIH' ELSE 'BAIK' END,
            2.3 - (seq * 0.15), 
            0.5 + (seq * 0.05), 
            2.8 - (seq * 0.25),
            false, false,
            CASE WHEN seq = 0 THEN 'Berat badan berlebih, perlu pengaturan MPASI'
                 WHEN seq = 2 THEN 'Mulai terkontrol'
                 ELSE NULL END,
            1, NOW(), NOW()
        FROM generate_series(0, 2) seq;

        -- BALITA 5: Normal ASI eksklusif (2 pemeriksaan)
        balita_id := v_balita_ids[5];
        RAISE NOTICE 'Membuat pemeriksaan untuk balita 5 (ID: %)', balita_id;
        
        INSERT INTO kes_pemeriksaan (
            posyandu_id, penduduk_id, tanggal_periksa, usia_bulan,
            berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan,
            vitamin_a, imunisasi, asi_eksklusif,
            status_gizi, z_score_bb_u, z_score_tb_u, z_score_bb_tb,
            indikasi_stunting, indikasi_gizi_buruk, keterangan,
            created_by, created_at, updated_at
        )
        SELECT 
            2, balita_id,
            ('2024-11-15'::date + (seq * INTERVAL '1 month'))::date,
            6 + seq,
            7.80 + (seq * 0.30), 
            66.50 + (seq * 1.30), 
            42.5 + (seq * 0.30), 
            13.8 + (seq * 0.20),
            false, 
            CASE WHEN seq = 0 THEN 'BCG, Polio 1' 
                 WHEN seq = 1 THEN 'DPT 1' 
                 ELSE NULL END, 
            true,
            'BAIK', 
            0.2 + (seq * 0.10), 
            0.3 + (seq * 0.10), 
            0.1 + (seq * 0.10),
            false, false,
            CASE WHEN seq = 0 THEN 'ASI Eksklusif lancar' 
                 ELSE 'Sehat' END,
            1, NOW(), NOW()
        FROM generate_series(0, 1) seq;

        RAISE NOTICE 'Semua data pemeriksaan balita berhasil diinput!';
    ELSE
        RAISE NOTICE 'Tidak ada balita yang tersedia. Silakan jalankan script ini lagi.';
    END IF;

END $$;

-- Summary
SELECT 
    'Data pemeriksaan balita berhasil diinput!' AS status,
    (SELECT COUNT(DISTINCT penduduk_id) FROM kes_pemeriksaan) AS total_balita_terpantau,
    (SELECT COUNT(*) FROM kes_pemeriksaan) AS total_pemeriksaan,
    (SELECT COUNT(*) FROM kes_pemeriksaan WHERE indikasi_stunting = true) AS kasus_stunting,
    (SELECT COUNT(*) FROM kes_pemeriksaan WHERE status_gizi = 'BURUK') AS kasus_gizi_buruk;
