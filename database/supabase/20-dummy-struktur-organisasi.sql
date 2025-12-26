-- ===========================================
-- DUMMY DATA - STRUKTUR ORGANISASI
-- File: 20-dummy-struktur-organisasi.sql
-- ===========================================

DO $$
DECLARE
    v_kode_desa VARCHAR(20);
BEGIN
    -- Get kode_desa from existing data
    SELECT kode_desa INTO v_kode_desa 
    FROM data_umum_desa 
    LIMIT 1;
    
    -- If no desa found, use default
    IF v_kode_desa IS NULL THEN
        v_kode_desa := '3201012001';
    END IF;
    
    -- Insert dummy struktur organisasi
    INSERT INTO struktur_organisasi (kode_desa, nama, jabatan, nip, pangkat_golongan, pendidikan, tanggal_lahir, tanggal_pengangkatan, no_sk, urutan, aktif) VALUES
    -- Kepala Desa
    (v_kode_desa, 'Budi Santoso, S.Sos', 'Kepala Desa', '196801151990031001', 'Pembina / IV.a', 'S1 Sosiologi', '1968-01-15', '2019-08-17', 'SK.141/KEP/2019', 1, true),
    
    -- Sekretaris Desa
    (v_kode_desa, 'Siti Nurhaliza, S.AP', 'Sekretaris Desa', '197205101995032002', 'Penata / III.c', 'S1 Administrasi Publik', '1972-05-10', '2018-01-01', 'SK.012/KEP/2018', 2, true),
    
    -- Kaur Keuangan
    (v_kode_desa, 'Ahmad Fauzi, S.E', 'Kaur Keuangan', '198003221999031003', 'Penata Muda / III.a', 'S1 Ekonomi', '1980-03-22', '2020-02-15', 'SK.025/KEP/2020', 3, true),
    
    -- Kaur Perencanaan
    (v_kode_desa, 'Rina Hardiyanti, S.T', 'Kaur Perencanaan', '198506152005032004', 'Pengatur / II.c', 'S1 Teknik Sipil', '1985-06-15', '2021-03-10', 'SK.041/KEP/2021', 4, true),
    
    -- Kaur Tata Usaha dan Umum
    (v_kode_desa, 'Dedi Gunawan', 'Kaur Tata Usaha dan Umum', '197812081998031005', 'Pengatur / II.c', 'SMA', '1978-12-08', '2019-06-01', 'SK.089/KEP/2019', 5, true),
    
    -- Kasi Pemerintahan
    (v_kode_desa, 'Hendra Wijaya, S.IP', 'Kasi Pemerintahan', '198209182003031006', 'Penata Muda / III.a', 'S1 Ilmu Pemerintahan', '1982-09-18', '2020-07-20', 'SK.115/KEP/2020', 6, true),
    
    -- Kasi Kesejahteraan
    (v_kode_desa, 'Dewi Lestari, S.Sos', 'Kasi Kesejahteraan', '198701252006032007', 'Pengatur Muda / II.a', 'S1 Kesejahteraan Sosial', '1987-01-25', '2021-09-01', 'SK.147/KEP/2021', 7, true),
    
    -- Kasi Pelayanan
    (v_kode_desa, 'Andi Saputra', 'Kasi Pelayanan', '199005102010031008', 'Pengatur Muda / II.a', 'D3 Administrasi', '1990-05-10', '2022-01-15', 'SK.008/KEP/2022', 8, true),
    
    -- Kepala Dusun I
    (v_kode_desa, 'Rahmat Hidayat', 'Kepala Dusun I', NULL, NULL, 'SMA', '1975-08-12', '2019-10-01', 'SK.165/KEP/2019', 9, true),
    
    -- Kepala Dusun II
    (v_kode_desa, 'Asep Suryadi', 'Kepala Dusun II', NULL, NULL, 'SMA', '1980-11-20', '2019-10-01', 'SK.166/KEP/2019', 10, true),
    
    -- Kepala Dusun III
    (v_kode_desa, 'Ujang Permana', 'Kepala Dusun III', NULL, NULL, 'SMA', '1977-03-05', '2019-10-01', 'SK.167/KEP/2019', 11, true),
    
    -- Staff Non-Aktif (contoh)
    (v_kode_desa, 'Yusuf Mansur', 'Kaur Keuangan', '196512101985031009', 'Pembina / IV.a', 'S1 Akuntansi', '1965-12-10', '2010-01-01', 'SK.001/KEP/2010', 99, false);
    
END $$;

SELECT 'Dummy data struktur organisasi berhasil dibuat!' AS status;
