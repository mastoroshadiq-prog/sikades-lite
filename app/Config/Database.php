<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;
    public string $defaultGroup = 'default';

    /**
     * Default database connection
     * Can be overridden by .env file
     * Supports both MySQL and PostgreSQL (Supabase)
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'siskeudes',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_unicode_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
    ];

    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => 'utf8_general_ci',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
    ];

    public function __construct()
    {
        parent::__construct();

        // Override with values from .env if exists
        if (env('database.default.hostname')) {
            $this->default['hostname'] = env('database.default.hostname');
        }
        if (env('database.default.database')) {
            $this->default['database'] = env('database.default.database');
        }
        if (env('database.default.username')) {
            $this->default['username'] = env('database.default.username');
        }
        if (env('database.default.password')) {
            $this->default['password'] = env('database.default.password');
        }
        if (env('database.default.DBDriver')) {
            $this->default['DBDriver'] = env('database.default.DBDriver');
        }
        if (env('database.default.port')) {
            $this->default['port'] = (int) env('database.default.port');
        }
        
        // PostgreSQL specific settings (for Supabase)
        if ($this->default['DBDriver'] === 'Postgre') {
            // Set PostgreSQL charset
            $this->default['charset'] = 'utf8';
            
            // Remove MySQL-specific settings
            unset($this->default['DBCollat']);
            unset($this->default['compress']);
            unset($this->default['strictOn']);
            
            // Add PostgreSQL SSL settings for Supabase
            if (env('database.default.sslmode')) {
                $this->default['sslmode'] = env('database.default.sslmode');
            }
        }
    }
}

