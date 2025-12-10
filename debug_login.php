<?php
/**
 * Simple Debug Login Script
 */

echo "=== DEBUG LOGIN ===\n\n";

// Direct PostgreSQL connection
$host = 'aws-1-ap-southeast-1.pooler.supabase.com';
$port = '6543';
$dbname = 'postgres';
$user = 'postgres.tyhwxgggqzwjkbvgibut';
$password = 'sikades@Jaya2026';

$connStr = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";

echo "Connecting to database...\n";
$conn = @pg_connect($connStr);

if (!$conn) {
    echo "✗ Connection failed: " . pg_last_error() . "\n";
    exit(1);
}

echo "✓ Connected!\n\n";

// Get admin user
echo "Looking for admin user...\n";
$result = pg_query($conn, "SELECT id, username, password_hash, role FROM users WHERE username = 'admin'");

if (!$result) {
    echo "✗ Query failed: " . pg_last_error($conn) . "\n";
    exit(1);
}

$admin = pg_fetch_assoc($result);

if (!$admin) {
    echo "✗ Admin user not found!\n";
    exit(1);
}

echo "✓ Admin found:\n";
echo "  ID: {$admin['id']}\n";
echo "  Username: {$admin['username']}\n";
echo "  Role: {$admin['role']}\n";
echo "  Password Hash: {$admin['password_hash']}\n\n";

// Test password
echo "Testing password 'admin123'...\n";
$isValid = password_verify('admin123', $admin['password_hash']);

if ($isValid) {
    echo "✓ Password is VALID! Login should work.\n";
} else {
    echo "✗ Password is INVALID!\n\n";
    
    // Generate new hash
    $newHash = password_hash('admin123', PASSWORD_BCRYPT);
    echo "New hash generated: $newHash\n\n";
    
    echo "Run this SQL in Supabase to fix:\n";
    echo "UPDATE users SET password_hash = '$newHash' WHERE username = 'admin';\n";
}

pg_close($conn);
echo "\n=== END ===\n";
