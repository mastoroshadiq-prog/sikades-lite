-- ===========================================
-- SIKADES LITE - DUMMY DATA DEMOGRAFI
-- Jalankan di Supabase SQL Editor
-- ===========================================

-- Gunakan kode desa yang sudah ada (pastikan ada data_umum_desa dengan kode_desa ini)

-- ===========================================
-- 1. DATA DEMOGRAFI - Keluarga
-- ===========================================

INSERT INTO pop_keluarga (kode_desa, no_kk, kepala_keluarga, alamat, rt, rw, dusun, kode_pos, created_at, updated_at) VALUES
('3201010001', '3201010001000001', 'Ahmad Sudirman', 'Jl. Raya Desa No. 1', '001', '001', 'Dusun Cikaret', '16110', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', '3201010001000002', 'Budi Santoso', 'Jl. Merdeka No. 15', '001', '001', 'Dusun Cikaret', '16110', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', '3201010001000003', 'Cahyo Wibowo', 'Jl. Pahlawan No. 23', '002', '001', 'Dusun Cikaret', '16110', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', '3201010001000004', 'Dedi Kurniawan', 'Jl. Mawar No. 8', '001', '002', 'Dusun Ciburial', '16110', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', '3201010001000005', 'Eko Prasetyo', 'Jl. Melati No. 12', '002', '002', 'Dusun Ciburial', '16110', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', '3201010001000006', 'Fajar Hidayat', 'Jl. Kenanga No. 5', '001', '003', 'Dusun Cibeureum', '16110', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', '3201010001000007', 'Gunawan Setiawan', 'Jl. Dahlia No. 18', '002', '003', 'Dusun Cibeureum', '16110', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', '3201010001000008', 'Hendra Wijaya', 'Jl. Anggrek No. 3', '003', '003', 'Dusun Cibeureum', '16110', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', '3201010001000009', 'Irwan Susanto', 'Jl. Cemara No. 7', '001', '004', 'Dusun Cipari', '16110', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3201010001', '3201010001000010', 'Joko Widodo', 'Jl. Pinus No. 21', '002', '004', 'Dusun Cipari', '16110', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON CONFLICT DO NOTHING;

-- ===========================================
-- 2. DATA DEMOGRAFI - Penduduk
-- ===========================================

-- Keluarga 1 - Ahmad Sudirman (4 orang)
INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000101', 'Ahmad Sudirman', 'Bogor', '1975-05-15', 'L', 'Islam', 'Kawin', 'SLTA/Sederajat', 'Petani', 'KEPALA KELUARGA', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000001' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000102', 'Siti Aminah', 'Bogor', '1978-08-20', 'P', 'Islam', 'Kawin', 'SLTA/Sederajat', 'Ibu Rumah Tangga', 'ISTRI', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000001' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000103', 'Andi Sudirman', 'Bogor', '2005-03-10', 'L', 'Islam', 'Belum Kawin', 'SLTP/Sederajat', 'Pelajar/Mahasiswa', 'ANAK', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000001' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000104', 'Dewi Sudirman', 'Bogor', '2010-07-25', 'P', 'Islam', 'Belum Kawin', 'Tamat SD/Sederajat', 'Pelajar/Mahasiswa', 'ANAK', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000001' LIMIT 1;

-- Keluarga 2 - Budi Santoso (3 orang)
INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000201', 'Budi Santoso', 'Jakarta', '1980-01-12', 'L', 'Islam', 'Kawin', 'Diploma IV/Strata I', 'PNS', 'KEPALA KELUARGA', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000002' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000202', 'Ratna Dewi', 'Bandung', '1982-04-18', 'P', 'Islam', 'Kawin', 'Diploma IV/Strata I', 'Guru', 'ISTRI', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000002' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000203', 'Rizky Santoso', 'Bogor', '2008-09-05', 'L', 'Islam', 'Belum Kawin', 'SLTP/Sederajat', 'Pelajar/Mahasiswa', 'ANAK', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000002' LIMIT 1;

-- Keluarga 3 - Cahyo Wibowo (Keluarga Miskin, 3 orang)
INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000301', 'Cahyo Wibowo', 'Bogor', '1970-11-22', 'L', 'Islam', 'Kawin', 'Tamat SD/Sederajat', 'Buruh Tani/Perkebunan', 'KEPALA KELUARGA', 'HIDUP', true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000003' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000302', 'Sumiati', 'Bogor', '1972-06-14', 'P', 'Islam', 'Kawin', 'Tamat SD/Sederajat', 'Ibu Rumah Tangga', 'ISTRI', 'HIDUP', true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000003' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000303', 'Lutfi Wibowo', 'Bogor', '2022-02-28', 'L', 'Islam', 'Belum Kawin', 'Tidak/Belum Sekolah', 'Belum/Tidak Bekerja', 'ANAK', 'HIDUP', true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000003' LIMIT 1;

-- Keluarga 4 - Dedi Kurniawan (2 orang)
INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000401', 'Dedi Kurniawan', 'Bogor', '1985-03-08', 'L', 'Islam', 'Kawin', 'SLTA/Sederajat', 'Wiraswasta', 'KEPALA KELUARGA', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000004' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000402', 'Maya Sari', 'Bogor', '1988-07-15', 'P', 'Islam', 'Kawin', 'SLTA/Sederajat', 'Ibu Rumah Tangga', 'ISTRI', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000004' LIMIT 1;

-- Keluarga 5 - Eko Prasetyo (3 orang)
INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000501', 'Eko Prasetyo', 'Surabaya', '1978-12-01', 'L', 'Islam', 'Kawin', 'Diploma IV/Strata I', 'Karyawan Swasta', 'KEPALA KELUARGA', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000005' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000502', 'Nurul Hidayah', 'Surabaya', '1982-04-12', 'P', 'Islam', 'Kawin', 'Diploma IV/Strata I', 'Ibu Rumah Tangga', 'ISTRI', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000005' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000503', 'Azzam Prasetyo', 'Bogor', '2021-09-15', 'L', 'Islam', 'Belum Kawin', 'Tidak/Belum Sekolah', 'Belum/Tidak Bekerja', 'ANAK', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000005' LIMIT 1;

-- Keluarga 6 - Fajar Hidayat (2 orang)
INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000601', 'Fajar Hidayat', 'Bogor', '1990-07-17', 'L', 'Islam', 'Kawin', 'SLTA/Sederajat', 'Pedagang', 'KEPALA KELUARGA', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000006' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000602', 'Sri Wahyuni', 'Bogor', '1992-08-20', 'P', 'Islam', 'Kawin', 'SLTA/Sederajat', 'Ibu Rumah Tangga', 'ISTRI', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000006' LIMIT 1;

-- Keluarga 7 - Gunawan Setiawan (2 orang, Kristen)
INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000701', 'Gunawan Setiawan', 'Yogyakarta', '1982-09-25', 'L', 'Kristen', 'Kawin', 'Strata II', 'Dosen', 'KEPALA KELUARGA', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000007' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000702', 'Maria Susanti', 'Semarang', '1985-03-10', 'P', 'Kristen', 'Kawin', 'Strata II', 'Dokter', 'ISTRI', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000007' LIMIT 1;

-- Keluarga 8 - Hendra Wijaya (1 orang, Buddha, belum kawin)
INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000801', 'Hendra Wijaya', 'Semarang', '1988-04-12', 'L', 'Buddha', 'Belum Kawin', 'Diploma IV/Strata I', 'Programmer', 'KEPALA KELUARGA', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000008' LIMIT 1;

-- Keluarga 9 - Irwan Susanto (Keluarga Miskin, Lansia)
INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000901', 'Irwan Susanto', 'Bogor', '1955-02-20', 'L', 'Islam', 'Kawin', 'SLTP/Sederajat', 'Petani', 'KEPALA KELUARGA', 'HIDUP', true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000009' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001000902', 'Siti Rokayah', 'Bogor', '1958-05-15', 'P', 'Islam', 'Kawin', 'Tamat SD/Sederajat', 'Ibu Rumah Tangga', 'ISTRI', 'HIDUP', true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000009' LIMIT 1;

-- Keluarga 10 - Joko Widodo (2 orang)
INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001001001', 'Joko Widodo', 'Solo', '1961-06-21', 'L', 'Islam', 'Kawin', 'Diploma IV/Strata I', 'Wiraswasta', 'KEPALA KELUARGA', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000010' LIMIT 1;

INSERT INTO pop_penduduk (kode_desa, keluarga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pendidikan_terakhir, pekerjaan, status_hubungan, status_dasar, is_miskin, created_at, updated_at)
SELECT '3201010001', id, '3201010001001002', 'Iriana Jokowi', 'Solo', '1963-08-17', 'P', 'Islam', 'Kawin', 'Diploma IV/Strata I', 'Ibu Rumah Tangga', 'ISTRI', 'HIDUP', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM pop_keluarga WHERE no_kk = '3201010001000010' LIMIT 1;

SELECT 'Demografi data inserted! Total: 10 KK, 22 Penduduk' AS status;
