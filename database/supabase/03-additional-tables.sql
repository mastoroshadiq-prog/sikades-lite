-- ===========================================
-- SIKADES LITE - ADDITIONAL TABLES FOR SUPABASE
-- Run after 01-schema.sql
-- ===========================================

-- ===========================================
-- ASET KATEGORI
-- ===========================================
CREATE TABLE IF NOT EXISTS aset_kategori (
    id SERIAL PRIMARY KEY,
    kode_golongan VARCHAR(20),
    nama_golongan VARCHAR(255) NOT NULL,
    masa_manfaat INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===========================================
-- ASET INVENTARIS (detailed asset inventory)
-- ===========================================
CREATE TABLE IF NOT EXISTS aset_inventaris (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    kategori_id INTEGER REFERENCES aset_kategori(id),
    kode_register VARCHAR(50),
    nama_barang VARCHAR(255) NOT NULL,
    merk_type VARCHAR(255),
    ukuran VARCHAR(100),
    bahan VARCHAR(100),
    tahun_perolehan INTEGER,
    asal_perolehan VARCHAR(100),
    nilai_perolehan DECIMAL(15,2) DEFAULT 0,
    kondisi VARCHAR(50),
    lokasi VARCHAR(255),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    foto VARCHAR(255),
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_aset_inventaris_kode_desa ON aset_inventaris(kode_desa);

-- ===========================================
-- DEMOGRAFI - ADDITIONAL TABLES
-- ===========================================

-- Mutasi penduduk
CREATE TABLE IF NOT EXISTS pop_mutasi (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    penduduk_id INTEGER REFERENCES penduduk(id),
    jenis_mutasi VARCHAR(50),
    tanggal_peristiwa DATE,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===========================================
-- KESEHATAN TABLES
-- ===========================================
CREATE TABLE IF NOT EXISTS kes_pemeriksaan (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL,
    balita_id INTEGER REFERENCES balita(id),
    posyandu_id INTEGER REFERENCES posyandu(id),
    tanggal_periksa DATE,
    jenis_pemeriksaan VARCHAR(100),
    hasil TEXT,
    keterangan TEXT,
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===========================================
-- RKP DETAIL (Activities per RKP)
-- ===========================================
CREATE TABLE IF NOT EXISTS rkp_kegiatan (
    id SERIAL PRIMARY KEY,
    rkpdesa_id INTEGER REFERENCES rkpdesa(id) ON DELETE CASCADE,
    nama_kegiatan VARCHAR(255) NOT NULL,
    lokasi VARCHAR(255),
    volume VARCHAR(100),
    satuan VARCHAR(50),
    anggaran DECIMAL(15,2) DEFAULT 0,
    sumber_dana VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===========================================
-- Sample data for aset_kategori
-- ===========================================
INSERT INTO aset_kategori (kode_golongan, nama_golongan, masa_manfaat) VALUES
('1.1', 'Tanah', 0),
('2.1', 'Peralatan dan Mesin', 5),
('3.1', 'Gedung dan Bangunan', 20),
('4.1', 'Jalan, Irigasi dan Jaringan', 10),
('5.1', 'Aset Tetap Lainnya', 5),
('6.1', 'Konstruksi Dalam Pengerjaan', 0)
ON CONFLICT DO NOTHING;

SELECT 'Additional tables created successfully!' AS status;
