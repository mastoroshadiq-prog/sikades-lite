<?php
// Test Supabase PostgreSQL Connection

$host = 'aws-0-ap-southeast-1.pooler.supabase.com';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres.tyhwxgggqzwjkbvgibut';
$password = 'PASTE_YOUR_PASSWORD_HERE'; // <-- GANTI DENGAN PASSWORD ANDA

echo "Testing connection to Supabase...\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "User: $user\n";
echo "Database: $dbname\n\n";

$connectionString = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";

$conn = @pg_connect($connectionString);

if ($conn) {
    echo "SUCCESS! Connected to Supabase PostgreSQL!\n\n";
    
    // Test query
    $result = pg_query($conn, "SELECT current_database(), current_user, version()");
    if ($result) {
        $row = pg_fetch_assoc($result);
        echo "Database: " . $row['current_database'] . "\n";
        echo "User: " . $row['current_user'] . "\n";
        echo "Version: " . substr($row['version'], 0, 50) . "...\n";
    }
    
    // Check tables
    $tables = pg_query($conn, "SELECT tablename FROM pg_tables WHERE schemaname = 'public' LIMIT 10");
    if ($tables && pg_num_rows($tables) > 0) {
        echo "\nTables found:\n";
        while ($row = pg_fetch_assoc($tables)) {
            echo "- " . $row['tablename'] . "\n";
        }
    } else {
        echo "\nNo tables found. Please run the migration SQL first!\n";
    }
    
    pg_close($conn);
} else {
    echo "FAILED! Could not connect.\n";
    echo "Error: " . pg_last_error() . "\n";
}
