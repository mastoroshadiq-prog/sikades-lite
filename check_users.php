<?php
// Check users in database

require __DIR__ . '/vendor/autoload.php';

// Boot CodeIgniter
$paths = new Config\Paths();
require SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

$db = \Config\Database::connect();

echo "Checking users table...\n\n";

try {
    $result = $db->query("SELECT id, username, role FROM users LIMIT 10");
    $users = $result->getResultArray();
    
    if (empty($users)) {
        echo "No users found! You need to run the SQL migration.\n";
        echo "Run database/supabase/01-schema.sql and 02-dummy-data.sql in Supabase SQL Editor.\n";
    } else {
        echo "Users found:\n";
        foreach ($users as $user) {
            echo "- ID: {$user['id']}, Username: {$user['username']}, Role: {$user['role']}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nTable might not exist. Please run the SQL migration first.\n";
}
