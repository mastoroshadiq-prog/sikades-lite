-- ===========================================
-- SIKADES LITE - DUMMY DATA E-POSYANDU
-- Jalankan di Supabase SQL Editor
-- ===========================================

-- Pastikan tabel posyandu sudah ada
CREATE TABLE IF NOT EXISTS posyandu (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    alamat TEXT,
    kader VARCHAR(255),
    jadwal_kegiatan VARCHAR(100),
    status VARCHAR(50) DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS kes_ibu_hamil (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    penduduk_id INTEGER,
    posyandu_id INTEGER,
    nama VARCHAR(255) NOT NULL,
    nik VARCHAR(20),
    tanggal_lahir DATE,
    alamat TEXT,
    nama_suami VARCHAR(255),
    golongan_darah VARCHAR(5),
    hpht DATE, -- Hari Pertama Haid Terakhir
    taksiran_persalinan DATE,
    kehamilan_ke INTEGER DEFAULT 1,
    status VARCHAR(50) DEFAULT 'Hamil', -- Hamil, Melahirkan, Keguguran
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS kes_balita (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    penduduk_id INTEGER,
    posyandu_id INTEGER,
    nama VARCHAR(255) NOT NULL,
    nik VARCHAR(20),
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin VARCHAR(1), -- L/P
    nama_ibu VARCHAR(255),
    nama_ayah VARCHAR(255),
    alamat TEXT,
    status VARCHAR(50) DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS kes_pemeriksaan (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    posyandu_id INTEGER,
    balita_id INTEGER,
    ibu_hamil_id INTEGER,
    tanggal_periksa DATE NOT NULL,
    berat_badan DECIMAL(5,2),
    tinggi_badan DECIMAL(5,2),
    lingkar_kepala DECIMAL(5,2),
    lingkar_lengan DECIMAL(5,2),
    tekanan_darah VARCHAR(20),
    suhu DECIMAL(4,1),
    catatan TEXT,
    petugas VARCHAR(255),
    indikasi_stunting BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Posyandu
INSERT INTO posyandu (kode_desa, nama, alamat, kader, jadwal_kegiatan, status) VALUES
('3201010001', 'Posyandu Melati', 'Balai Dusun Cikaret RT 001/001', 'Ibu Siti, Ibu Aminah, Ibu Dewi', 'Setiap Rabu Minggu ke-1', 'Aktif'),
('3201010001', 'Posyandu Mawar', 'Balai Dusun Ciburial RT 001/002', 'Ibu Ratna, Ibu Wati, Ibu Ningsih', 'Setiap Rabu Minggu ke-2', 'Aktif'),
('3201010001', 'Posyandu Anggrek', 'Balai Dusun Cibeureum RT 001/003', 'Ibu Kartini, Ibu Suryani', 'Setiap Rabu Minggu ke-3', 'Aktif')
ON CONFLICT DO NOTHING;

-- Insert Ibu Hamil
INSERT INTO kes_ibu_hamil (kode_desa, posyandu_id, nama, nik, tanggal_lahir, alamat, nama_suami, golongan_darah, hpht, taksiran_persalinan, kehamilan_ke, status)
SELECT '3201010001', id, 'Nurul Hidayah', '3201010001000501', '1995-04-12', 'Jl. Melati No. 12, Ciburial', 'Eko Prasetyo', 'A', '2024-06-15', '2025-03-22', 2, 'Hamil'
FROM posyandu WHERE nama = 'Posyandu Mawar' LIMIT 1;

INSERT INTO kes_ibu_hamil (kode_desa, posyandu_id, nama, nik, tanggal_lahir, alamat, nama_suami, golongan_darah, hpht, taksiran_persalinan, kehamilan_ke, status)
SELECT '3201010001', id, 'Sri Wahyuni', '3201010001000601', '1992-08-20', 'Jl. Kenanga No. 5, Cibeureum', 'Fajar Hidayat', 'B', '2024-08-01', '2025-05-08', 1, 'Hamil'
FROM posyandu WHERE nama = 'Posyandu Anggrek' LIMIT 1;

INSERT INTO kes_ibu_hamil (kode_desa, posyandu_id, nama, nik, tanggal_lahir, alamat, nama_suami, golongan_darah, hpht, taksiran_persalinan, kehamilan_ke, status)
SELECT '3201010001', id, 'Mega Lestari', '3201010001000701', '1998-01-15', 'Jl. Dahlia No. 18, Cibeureum', 'Gunawan Setiawan', 'O', '2024-09-10', '2025-06-17', 1, 'Hamil'
FROM posyandu WHERE nama = 'Posyandu Anggrek' LIMIT 1;

-- Insert Balita
INSERT INTO kes_balita (kode_desa, posyandu_id, nama, nik, tanggal_lahir, jenis_kelamin, nama_ibu, nama_ayah, alamat, status)
SELECT '3201010001', id, 'Azzam Sudirman', '3201010001000105', '2022-09-15', 'L', 'Siti Aminah', 'Ahmad Sudirman', 'Jl. Raya Desa No. 1, Cikaret', 'Aktif'
FROM posyandu WHERE nama = 'Posyandu Melati' LIMIT 1;

INSERT INTO kes_balita (kode_desa, posyandu_id, nama, nik, tanggal_lahir, jenis_kelamin, nama_ibu, nama_ayah, alamat, status)
SELECT '3201010001', id, 'Zahra Santoso', '3201010001000204', '2023-02-28', 'P', 'Ratna Dewi', 'Budi Santoso', 'Jl. Merdeka No. 15, Cikaret', 'Aktif'
FROM posyandu WHERE nama = 'Posyandu Melati' LIMIT 1;

INSERT INTO kes_balita (kode_desa, posyandu_id, nama, nik, tanggal_lahir, jenis_kelamin, nama_ibu, nama_ayah, alamat, status)
SELECT '3201010001', id, 'Lutfi Wibowo', '3201010001000303', '2023-06-10', 'L', 'Sumiati', 'Cahyo Wibowo', 'Jl. Pahlawan No. 23, Cikaret', 'Aktif'
FROM posyandu WHERE nama = 'Posyandu Melati' LIMIT 1;

INSERT INTO kes_balita (kode_desa, posyandu_id, nama, nik, tanggal_lahir, jenis_kelamin, nama_ibu, nama_ayah, alamat, status)
SELECT '3201010001', id, 'Alya Kurniawan', '3201010001000402', '2022-11-05', 'P', 'Maya Sari', 'Dedi Kurniawan', 'Jl. Mawar No. 8, Ciburial', 'Aktif'
FROM posyandu WHERE nama = 'Posyandu Mawar' LIMIT 1;

INSERT INTO kes_balita (kode_desa, posyandu_id, nama, nik, tanggal_lahir, jenis_kelamin, nama_ibu, nama_ayah, alamat, status)
SELECT '3201010001', id, 'Muhammad Fikri', '3201010001000502', '2024-01-20', 'L', 'Nurul Hidayah', 'Eko Prasetyo', 'Jl. Melati No. 12, Ciburial', 'Aktif'
FROM posyandu WHERE nama = 'Posyandu Mawar' LIMIT 1;

-- Insert Pemeriksaan Balita
-- Azzam Sudirman (6 bulan terakhir)
INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, balita_id, tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan, catatan, petugas, indikasi_stunting)
SELECT '3201010001', p.id, b.id, '2024-07-03', 12.5, 85.0, 47.0, 15.5, 'Tumbuh kembang baik', 'Bidan Ani', false
FROM posyandu p, kes_balita b WHERE p.nama = 'Posyandu Melati' AND b.nama = 'Azzam Sudirman' LIMIT 1;

INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, balita_id, tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan, catatan, petugas, indikasi_stunting)
SELECT '3201010001', p.id, b.id, '2024-08-07', 12.8, 86.0, 47.5, 15.7, 'Tumbuh kembang baik', 'Bidan Ani', false
FROM posyandu p, kes_balita b WHERE p.nama = 'Posyandu Melati' AND b.nama = 'Azzam Sudirman' LIMIT 1;

INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, balita_id, tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan, catatan, petugas, indikasi_stunting)
SELECT '3201010001', p.id, b.id, '2024-09-04', 13.0, 87.0, 47.8, 15.8, 'Tumbuh kembang baik', 'Bidan Ani', false
FROM posyandu p, kes_balita b WHERE p.nama = 'Posyandu Melati' AND b.nama = 'Azzam Sudirman' LIMIT 1;

INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, balita_id, tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan, catatan, petugas, indikasi_stunting)
SELECT '3201010001', p.id, b.id, '2024-10-02', 13.3, 88.0, 48.0, 16.0, 'Tumbuh kembang baik', 'Bidan Ani', false
FROM posyandu p, kes_balita b WHERE p.nama = 'Posyandu Melati' AND b.nama = 'Azzam Sudirman' LIMIT 1;

INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, balita_id, tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan, catatan, petugas, indikasi_stunting)
SELECT '3201010001', p.id, b.id, '2024-11-06', 13.5, 89.0, 48.2, 16.2, 'Tumbuh kembang baik', 'Bidan Ani', false
FROM posyandu p, kes_balita b WHERE p.nama = 'Posyandu Melati' AND b.nama = 'Azzam Sudirman' LIMIT 1;

INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, balita_id, tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan, catatan, petugas, indikasi_stunting)
SELECT '3201010001', p.id, b.id, '2024-12-04', 13.8, 90.0, 48.5, 16.3, 'Tumbuh kembang baik', 'Bidan Ani', false
FROM posyandu p, kes_balita b WHERE p.nama = 'Posyandu Melati' AND b.nama = 'Azzam Sudirman' LIMIT 1;

-- Lutfi Wibowo (indikasi stunting - keluarga miskin)
INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, balita_id, tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan, catatan, petugas, indikasi_stunting)
SELECT '3201010001', p.id, b.id, '2024-10-02', 9.5, 70.0, 43.0, 13.0, 'BB kurang, indikasi gizi buruk', 'Bidan Ani', true
FROM posyandu p, kes_balita b WHERE p.nama = 'Posyandu Melati' AND b.nama = 'Lutfi Wibowo' LIMIT 1;

INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, balita_id, tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan, catatan, petugas, indikasi_stunting)
SELECT '3201010001', p.id, b.id, '2024-11-06', 9.8, 71.0, 43.5, 13.2, 'Masih dalam pengawasan, pemberian PMT', 'Bidan Ani', true
FROM posyandu p, kes_balita b WHERE p.nama = 'Posyandu Melati' AND b.nama = 'Lutfi Wibowo' LIMIT 1;

INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, balita_id, tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan, catatan, petugas, indikasi_stunting)
SELECT '3201010001', p.id, b.id, '2024-12-04', 10.2, 72.0, 44.0, 13.5, 'BB mulai naik setelah PMT', 'Bidan Ani', true
FROM posyandu p, kes_balita b WHERE p.nama = 'Posyandu Melati' AND b.nama = 'Lutfi Wibowo' LIMIT 1;

-- Pemeriksaan Ibu Hamil
INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, ibu_hamil_id, tanggal_periksa, berat_badan, tekanan_darah, lingkar_lengan, catatan, petugas)
SELECT '3201010001', p.id, h.id, '2024-10-09', 58.5, '110/70', 25.0, 'Kehamilan 16 minggu, normal', 'Bidan Sari'
FROM posyandu p, kes_ibu_hamil h WHERE p.nama = 'Posyandu Mawar' AND h.nama = 'Nurul Hidayah' LIMIT 1;

INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, ibu_hamil_id, tanggal_periksa, berat_badan, tekanan_darah, lingkar_lengan, catatan, petugas)
SELECT '3201010001', p.id, h.id, '2024-11-13', 60.0, '115/75', 25.5, 'Kehamilan 20 minggu, normal', 'Bidan Sari'
FROM posyandu p, kes_ibu_hamil h WHERE p.nama = 'Posyandu Mawar' AND h.nama = 'Nurul Hidayah' LIMIT 1;

INSERT INTO kes_pemeriksaan (kode_desa, posyandu_id, ibu_hamil_id, tanggal_periksa, berat_badan, tekanan_darah, lingkar_lengan, catatan, petugas)
SELECT '3201010001', p.id, h.id, '2024-12-11', 61.5, '112/72', 26.0, 'Kehamilan 24 minggu, janin aktif bergerak', 'Bidan Sari'
FROM posyandu p, kes_ibu_hamil h WHERE p.nama = 'Posyandu Mawar' AND h.nama = 'Nurul Hidayah' LIMIT 1;

SELECT 'Posyandu data inserted!' AS status;
