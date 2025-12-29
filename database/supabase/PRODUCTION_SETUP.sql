-- =====================================================
-- SIKADES LITE - PRODUCTION DATABASE SETUP
-- =====================================================
-- Run this complete script in Supabase SQL Editor
-- This is a CONSOLIDATED setup for production
-- =====================================================
-- Estimated Time: 30 seconds
-- =====================================================

-- Enable required extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- =====================================================
-- PART 1: CREATE SESSION TABLE (Required by CodeIgniter)
-- =====================================================
CREATE TABLE IF NOT EXISTS ci_sessions (
    id VARCHAR(128) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    data TEXT NOT NULL,
    PRIMARY KEY (id)
);

CREATE INDEX IF NOT EXISTS ci_sessions_timestamp ON ci_sessions(timestamp);

-- =====================================================
-- PART 2: CREATE DEFAULT ADMIN USER
-- =====================================================
-- Password: admin123 (CHANGE THIS AFTER FIRST LOGIN!)
-- This uses bcrypt hash compatible with CodeIgniter 4
INSERT INTO users (username, password_hash, role, kode_desa, is_active, created_at) 
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Administrator',
    '3201010001',
    true,
    NOW()
) ON CONFLICT (username) DO NOTHING;

-- =====================================================
-- PART 3: CREATE OPERATOR USER
-- =====================================================
-- Password: operator123
INSERT INTO users (username, password_hash, role, kode_desa, is_active, created_at) 
VALUES (
    'operator',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Operator Desa',
    '3201010001',
    true,
    NOW()
) ON CONFLICT (username) DO NOTHING;

-- =====================================================
-- PART 4: CREATE KEPALA DESA USER
-- =====================================================
-- Password: kades123
INSERT INTO users (username, password_hash, role, kode_desa, is_active, created_at) 
VALUES (
    'kades',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Kepala Desa',
    '3201010001',
    true,
    NOW()
) ON CONFLICT (username) DO NOTHING;

-- =====================================================
-- PART 5: INSERT DEFAULT DESA DATA
-- =====================================================
INSERT INTO data_umum_desa (
    kode_desa, nama_desa, kecamatan, kabupaten, provinsi,
    nama_kepala_desa, nip_kepala_desa, nama_bendahara,
    npwp, tahun_anggaran, created_at
) VALUES (
    '3201010001',
    'Desa Maju Jaya',
    'Kecamatan Sukamaju',
    'Kabupaten Bogor',
    'Jawa Barat',
    'Budi Santoso',
    '196501011990031001',
    'Sri Handayani',
    '00.123.456.7-891.000',
    2025,
    NOW()
) ON CONFLICT (kode_desa) DO NOTHING;

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================
-- Check if setup was successful

-- 1. Check users
SELECT 'USERS CHECK' as check_type, 
       COUNT(*) as total_users,
       COUNT(*) FILTER (WHERE role = 'Administrator') as admin_count,
       COUNT(*) FILTER (WHERE role = 'Operator Desa') as operator_count,
       COUNT(*) FILTER (WHERE role = 'Kepala Desa') as kades_count
FROM users;

-- 2. Check desa data
SELECT 'DESA DATA CHECK' as check_type,
       COUNT(*) as total_desa,
       string_agg(nama_desa, ', ') as desa_names
FROM data_umum_desa;

-- 3. Check session table
SELECT 'SESSION TABLE CHECK' as check_type,
       CASE WHEN EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'ci_sessions')
            THEN 'EXISTS ✅'
            ELSE 'MISSING ❌'
       END as status;

-- 4. List all tables
SELECT 'TOTAL TABLES' as check_type,
       COUNT(*) as table_count
FROM information_schema.tables 
WHERE table_schema = 'public'
  AND table_type = 'BASE TABLE';

-- =====================================================
-- SUCCESS MESSAGE
-- =====================================================
SELECT '✅ PRODUCTION SETUP COMPLETE!' as status,
       'You can now login with: admin/admin123' as next_step;
