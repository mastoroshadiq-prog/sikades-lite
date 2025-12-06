-- ============================================
-- Siskeudes Lite - Manual Table Creation
-- Missing Tables: data_umum_desa, apbdes, spp, spp_rincian, bku, pajak
-- ============================================

USE siskeudes;

-- Table 3: data_umum_desa
CREATE TABLE IF NOT EXISTS `data_umum_desa` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_desa` VARCHAR(20) NOT NULL,
    `nama_desa` VARCHAR(100) NOT NULL,
    `kecamatan` VARCHAR(100) NOT NULL,
    `kabupaten` VARCHAR(100) NOT NULL,
    `provinsi` VARCHAR(100) NOT NULL,
    `nama_kepala_desa` VARCHAR(100) NOT NULL,
    `nip_kepala_desa` VARCHAR(50) NULL,
    `tahun_anggaran` YEAR NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `kode_desa_tahun` (`kode_desa`, `tahun_anggaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table 4: apbdes (Budget)
CREATE TABLE IF NOT EXISTS `apbdes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_desa` VARCHAR(20) NOT NULL,
    `tahun` YEAR NOT NULL,
    `ref_rekening_id` INT UNSIGNED NOT NULL,
    `uraian` TEXT NOT NULL,
    `anggaran` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `sumber_dana` ENUM('DDS','ADD','PAD','Bankeu') NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_kode_desa` (`kode_desa`),
    KEY `idx_tahun` (`tahun`),
    KEY `fk_apbdes_rekening` (`ref_rekening_id`),
    CONSTRAINT `fk_apbdes_rekening` FOREIGN KEY (`ref_rekening_id`) 
        REFERENCES `ref_rekening` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table 5: spp (Payment Request)
CREATE TABLE IF NOT EXISTS `spp` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_desa` VARCHAR(20) NOT NULL,
    `nomor_spp` VARCHAR(50) NOT NULL,
    `tanggal_spp` DATE NOT NULL,
    `uraian` TEXT NOT NULL,
    `jumlah` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('Draft','Verified','Approved') NOT NULL DEFAULT 'Draft',
    `created_by` INT UNSIGNED NOT NULL,
    `verified_by` INT UNSIGNED NULL,
    `approved_by` INT UNSIGNED NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nomor_spp_unique` (`kode_desa`, `nomor_spp`),
    KEY `idx_kode_desa` (`kode_desa`),
    KEY `idx_tanggal` (`tanggal_spp`),
    KEY `idx_status` (`status`),
    KEY `fk_spp_created_by` (`created_by`),
    KEY `fk_spp_verified_by` (`verified_by`),
    KEY `fk_spp_approved_by` (`approved_by`),
    CONSTRAINT `fk_spp_created_by` FOREIGN KEY (`created_by`) 
        REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_spp_verified_by` FOREIGN KEY (`verified_by`) 
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_spp_approved_by` FOREIGN KEY (`approved_by`) 
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table 6: spp_rincian (SPP Details)
CREATE TABLE IF NOT EXISTS `spp_rincian` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `spp_id` INT UNSIGNED NOT NULL,
    `apbdes_id` INT UNSIGNED NOT NULL,
    `uraian` TEXT NOT NULL,
    `jumlah` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_spp_rincian_spp` (`spp_id`),
    KEY `fk_spp_rincian_apbdes` (`apbdes_id`),
    CONSTRAINT `fk_spp_rincian_spp` FOREIGN KEY (`spp_id`) 
        REFERENCES `spp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_spp_rincian_apbdes` FOREIGN KEY (`apbdes_id`) 
        REFERENCES `apbdes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table 7: bku (General Cash Book)
CREATE TABLE IF NOT EXISTS `bku` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_desa` VARCHAR(20) NOT NULL,
    `tanggal` DATE NOT NULL,
    `no_bukti` VARCHAR(50) NOT NULL,
    `uraian` TEXT NOT NULL,
    `ref_rekening_id` INT UNSIGNED NOT NULL,
    `debet` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `kredit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `saldo_kumulatif` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `spp_id` INT UNSIGNED NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_kode_desa` (`kode_desa`),
    KEY `idx_tanggal` (`tanggal`),
    KEY `fk_bku_rekening` (`ref_rekening_id`),
    KEY `fk_bku_spp` (`spp_id`),
    CONSTRAINT `fk_bku_rekening` FOREIGN KEY (`ref_rekening_id`) 
        REFERENCES `ref_rekening` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_bku_spp` FOREIGN KEY (`spp_id`) 
        REFERENCES `spp` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table 8: pajak (Tax)
CREATE TABLE IF NOT EXISTS `pajak` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bku_id` INT UNSIGNED NOT NULL,
    `jenis_pajak` ENUM('PPN','PPh') NOT NULL,
    `tarif` DECIMAL(5,2) NOT NULL,
    `nilai_pajak` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `npwp` VARCHAR(30) NULL,
    `nama_wajib_pajak` VARCHAR(100) NULL,
    `status_pembayaran` ENUM('Belum','Sudah') NOT NULL DEFAULT 'Belum',
    `tanggal_setor` DATE NULL,
    `no_bukti_setor` VARCHAR(50) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_pajak_bku` (`bku_id`),
    KEY `idx_jenis_pajak` (`jenis_pajak`),
    KEY `idx_status` (`status_pembayaran`),
    CONSTRAINT `fk_pajak_bku` FOREIGN KEY (`bku_id`) 
        REFERENCES `bku` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Verify tables created
SHOW TABLES;

SELECT 
    TABLE_NAME as 'Table', 
    TABLE_ROWS as 'Rows',
    CREATE_TIME as 'Created'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'siskeudes' 
ORDER BY TABLE_NAME;
