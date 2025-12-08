-- Create gis_wilayah table for storing village boundaries
-- Run this SQL manually if migration fails

CREATE TABLE IF NOT EXISTS `gis_wilayah` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_desa` VARCHAR(20) NOT NULL,
    `nama_wilayah` VARCHAR(100) NOT NULL COMMENT 'Nama dusun/RT/RW',
    `tipe` ENUM('DESA', 'DUSUN', 'RW', 'RT') NOT NULL DEFAULT 'DUSUN',
    `parent_id` INT(11) UNSIGNED NULL COMMENT 'FK ke wilayah parent',
    `center_lat` DECIMAL(10,8) NULL COMMENT 'Latitude titik pusat wilayah',
    `center_lng` DECIMAL(11,8) NULL COMMENT 'Longitude titik pusat wilayah',
    `geojson` LONGTEXT NULL COMMENT 'Polygon boundary dalam format GeoJSON',
    `luas_area` DECIMAL(12,2) NULL COMMENT 'Luas area dalam meter persegi',
    `warna` VARCHAR(7) NULL COMMENT 'Warna hex untuk display (#RRGGBB)',
    `keterangan` TEXT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    INDEX `kode_desa` (`kode_desa`),
    INDEX `tipe` (`tipe`),
    INDEX `nama_wilayah_tipe` (`nama_wilayah`, `tipe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
