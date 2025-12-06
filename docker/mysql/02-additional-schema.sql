-- =====================================================
-- SIKADES LITE - Database Schema
-- This file runs automatically when MySQL container starts
-- =====================================================

-- Create activity_logs table if not exists
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    kode_desa VARCHAR(20) NULL,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT NULL,
    data_before JSON NULL,
    data_after JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_kode_desa (kode_desa),
    INDEX idx_module (module),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add jenis_transaksi column to bku table if not exists
-- Note: This uses a procedure because ALTER TABLE IF NOT EXISTS column syntax doesn't exist in MySQL
DELIMITER //

CREATE PROCEDURE add_column_if_not_exists()
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
    
    -- Try to add jenis_transaksi column
    SET @query = 'ALTER TABLE bku ADD COLUMN jenis_transaksi ENUM(''Pendapatan'', ''Belanja'', ''Lainnya'') DEFAULT ''Lainnya'' AFTER uraian';
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END//

DELIMITER ;

-- Call the procedure and then drop it
CALL add_column_if_not_exists();
DROP PROCEDURE IF EXISTS add_column_if_not_exists;

-- Grant permissions
GRANT ALL PRIVILEGES ON siskeudes.* TO 'siskeudes_user'@'%';
FLUSH PRIVILEGES;

SELECT 'Database schema initialized successfully!' AS status;
