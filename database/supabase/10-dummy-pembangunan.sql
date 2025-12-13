-- ===========================================
-- SIKADES LITE - DUMMY DATA E-PEMBANGUNAN
-- Jalankan di Supabase SQL Editor
-- ===========================================

-- Pastikan tabel proyek sudah ada
CREATE TABLE IF NOT EXISTS proyek_pembangunan (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    lokasi TEXT,
    volume VARCHAR(100),
    anggaran DECIMAL(15,2) DEFAULT 0,
    sumber_dana VARCHAR(50),
    tahun INTEGER,
    tanggal_mulai DATE,
    tanggal_selesai DATE,
    pelaksana VARCHAR(255),
    status VARCHAR(50) DEFAULT 'Perencanaan',
    progress INTEGER DEFAULT 0,
    keterangan TEXT,
    foto VARCHAR(255),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    created_by INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS proyek_log (
    id SERIAL PRIMARY KEY,
    proyek_id INTEGER REFERENCES proyek_pembangunan(id),
    tanggal DATE NOT NULL,
    progress INTEGER DEFAULT 0,
    catatan TEXT,
    foto VARCHAR(255),
    created_by INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Proyek Pembangunan
INSERT INTO proyek_pembangunan (kode_desa, nama, lokasi, volume, anggaran, sumber_dana, tahun, tanggal_mulai, tanggal_selesai, pelaksana, status, progress, keterangan, lat, lng) VALUES
-- Proyek Selesai
('3201010001', 'Pembangunan Jalan Desa RT 001/001', 'Dusun Cikaret RT 001/001', '500 meter x 3 meter', 150000000, 'DDS', 2024, '2024-03-01', '2024-06-30', 'CV Karya Mandiri', 'Selesai', 100, 'Pengaspalan jalan desa menggunakan hotmix', -6.5945, 106.8060),
('3201010001', 'Rehabilitasi Balai Desa', 'Kantor Desa', '1 unit', 85000000, 'DDS', 2024, '2024-02-15', '2024-05-15', 'CV Bangun Jaya', 'Selesai', 100, 'Renovasi gedung balai desa termasuk atap dan cat', -6.5950, 106.8070),
('3201010001', 'Pembangunan MCK Umum', 'Dusun Ciburial', '2 unit', 45000000, 'ADD', 2024, '2024-04-01', '2024-06-01', 'Swakelola', 'Selesai', 100, 'Pembangunan 2 unit MCK untuk warga kurang mampu', -6.5960, 106.8080),

-- Proyek Berjalan
('3201010001', 'Pembangunan Drainase', 'Dusun Cibeureum', '300 meter', 120000000, 'DDS', 2024, '2024-09-01', '2025-02-28', 'CV Mitra Sejahtera', 'Pelaksanaan', 65, 'Pembangunan saluran drainase untuk mencegah banjir', -6.5955, 106.8085),
('3201010001', 'Pembangunan Posyandu', 'Dusun Cipari', '1 unit 6x8 meter', 95000000, 'DDS', 2024, '2024-10-01', '2025-01-31', 'CV Bangun Jaya', 'Pelaksanaan', 45, 'Pembangunan gedung posyandu baru', -6.5965, 106.8095),
('3201010001', 'Pengadaan Lampu Jalan Tenaga Surya', 'Seluruh Dusun', '30 unit', 75000000, 'ADD', 2024, '2024-11-01', '2024-12-31', 'CV Terang Jaya', 'Pelaksanaan', 80, 'Pemasangan lampu PJU tenaga surya', -6.5970, 106.8065),

-- Proyek Perencanaan
('3201010001', 'Pembangunan Jembatan Desa', 'Dusun Cikaret - Ciburial', '15 meter x 4 meter', 250000000, 'DDS', 2025, '2025-02-01', '2025-06-30', 'Belum ditentukan', 'Perencanaan', 0, 'Pembangunan jembatan penghubung antar dusun', -6.5940, 106.8075),
('3201010001', 'Pembangunan Pasar Desa', 'Dusun Cibeureum', '20 kios', 350000000, 'DDS', 2025, '2025-03-01', '2025-08-31', 'Belum ditentukan', 'Perencanaan', 0, 'Pembangunan pasar desa dengan 20 kios', -6.5958, 106.8090),
('3201010001', 'Rehabilitasi Irigasi', 'Dusun Cipari', '1000 meter', 180000000, 'Bankeu', 2025, '2025-04-01', '2025-07-31', 'Belum ditentukan', 'Perencanaan', 0, 'Perbaikan saluran irigasi pertanian', -6.5975, 106.8055)
ON CONFLICT DO NOTHING;

-- Insert Log Progress Proyek
-- Log Pembangunan Drainase
INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-09-15', 10, 'Survei lokasi dan pemasangan patok'
FROM proyek_pembangunan WHERE nama = 'Pembangunan Drainase' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-10-01', 25, 'Penggalian selesai 100 meter pertama'
FROM proyek_pembangunan WHERE nama = 'Pembangunan Drainase' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-10-15', 40, 'Pemasangan buis beton 100 meter'
FROM proyek_pembangunan WHERE nama = 'Pembangunan Drainase' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-11-01', 55, 'Penggalian dan pemasangan meter 100-200'
FROM proyek_pembangunan WHERE nama = 'Pembangunan Drainase' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-11-15', 65, 'Pemasangan buis beton meter 200-250, cuaca hujan menghambat'
FROM proyek_pembangunan WHERE nama = 'Pembangunan Drainase' LIMIT 1;

-- Log Pembangunan Posyandu
INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-10-07', 5, 'Pembersihan lahan dan pemasangan bouwplank'
FROM proyek_pembangunan WHERE nama = 'Pembangunan Posyandu' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-10-21', 15, 'Galian pondasi dan pemasangan batu kali'
FROM proyek_pembangunan WHERE nama = 'Pembangunan Posyandu' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-11-04', 25, 'Sloof dan kolom lantai 1'
FROM proyek_pembangunan WHERE nama = 'Pembangunan Posyandu' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-11-18', 35, 'Dinding bata merah 50%'
FROM proyek_pembangunan WHERE nama = 'Pembangunan Posyandu' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-12-02', 45, 'Dinding bata merah selesai, mulai ring balok'
FROM proyek_pembangunan WHERE nama = 'Pembangunan Posyandu' LIMIT 1;

-- Log Lampu Jalan
INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-11-05', 10, 'Survei titik pemasangan'
FROM proyek_pembangunan WHERE nama = 'Pengadaan Lampu Jalan Tenaga Surya' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-11-15', 30, 'Pemasangan tiang 10 unit pertama (Dusun Cikaret)'
FROM proyek_pembangunan WHERE nama = 'Pengadaan Lampu Jalan Tenaga Surya' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-11-25', 50, 'Pemasangan tiang 10 unit kedua (Dusun Ciburial)'
FROM proyek_pembangunan WHERE nama = 'Pengadaan Lampu Jalan Tenaga Surya' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-12-05', 70, 'Pemasangan tiang 8 unit (Dusun Cibeureum)'
FROM proyek_pembangunan WHERE nama = 'Pengadaan Lampu Jalan Tenaga Surya' LIMIT 1;

INSERT INTO proyek_log (proyek_id, tanggal, progress, catatan)
SELECT id, '2024-12-10', 80, 'Instalasi panel surya dan testing 24 unit, sisa 6 unit di Dusun Cipari'
FROM proyek_pembangunan WHERE nama = 'Pengadaan Lampu Jalan Tenaga Surya' LIMIT 1;

SELECT 'Pembangunan data inserted!' AS status;
