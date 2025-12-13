<?php
/**
 * Debug script untuk PAK - Direct PostgreSQL
 * Akses: http://localhost:8080/debug_pak.php
 */

// Read .env file
$envFile = dirname(__DIR__) . '/.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value, " \t\n\r\0\x0B'\"");
        }
    }
}

// Database config
$host = $env['database.default.hostname'] ?? 'localhost';
$port = $env['database.default.port'] ?? '5432';
$dbname = $env['database.default.database'] ?? 'postgres';
$user = $env['database.default.username'] ?? 'postgres';
$pass = $env['database.default.password'] ?? '';

echo "<h2>PAK Debug Tool</h2>";
echo "<p>Host: $host:$port | DB: $dbname</p>";

// Connect
$connStr = "host=$host port=$port dbname=$dbname user=$user password=$pass";
$conn = @pg_connect($connStr);

if (!$conn) {
    echo "<div style='color:red'>Connection failed!</div>";
    exit;
}

echo "<div style='color:green'>Connected to PostgreSQL!</div><hr>";

// 1. Check PAK table
echo "<h3>1. PAK Records</h3>";
$result = @pg_query($conn, "SELECT * FROM pak ORDER BY id");
if ($result) {
    $rows = pg_fetch_all($result);
    if ($rows) {
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Nomor PAK</th><th>Tanggal</th><th>Status</th><th>Action</th></tr>";
        foreach ($rows as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nomor_pak']}</td>";
            echo "<td>{$row['tanggal_pak']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "<td><a href='?delete_id={$row['id']}' onclick='return confirm(\"Hapus PAK ini?\")'>Hapus</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Tidak ada data PAK.";
    }
} else {
    echo "<div style='color:red'>Error: " . pg_last_error($conn) . "</div>";
}

// 2. Delete single PAK
if (isset($_GET['delete_id'])) {
    $id = (int) $_GET['delete_id'];
    pg_query($conn, "DELETE FROM pak_detail WHERE pak_id = $id");
    pg_query($conn, "DELETE FROM pak WHERE id = $id");
    echo "<div style='color:green'>PAK ID $id deleted!</div>";
    echo "<script>window.location.href='debug_pak.php';</script>";
}

// 3. Delete all PAK
echo "<h3>2. Hapus Semua PAK</h3>";
if (isset($_GET['delete_all'])) {
    pg_query($conn, "DELETE FROM pak_detail");
    pg_query($conn, "DELETE FROM pak");
    echo "<div style='color:green'>Semua PAK dihapus!</div>";
    echo "<script>window.location.href='debug_pak.php';</script>";
} else {
    echo "<a href='?delete_all=1' onclick='return confirm(\"Hapus SEMUA PAK?\")' style='color:red;font-weight:bold'>Click to delete ALL PAK</a>";
}

// 4. Check pak_detail
echo "<h3>3. PAK Detail Records</h3>";
$result = @pg_query($conn, "SELECT COUNT(*) as count FROM pak_detail");
if ($result) {
    $row = pg_fetch_assoc($result);
    echo "Total pak_detail: " . $row['count'];
}

pg_close($conn);
echo "<hr><p><a href='/pak'>Back to PAK</a></p>";

