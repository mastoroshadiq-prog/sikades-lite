<?php

namespace App\Helpers;

/**
 * Database Compatibility Helper
 * 
 * Provides cross-database compatible SQL functions
 * Supports both MySQL and PostgreSQL (Supabase)
 */
class DbHelper
{
    /**
     * Get YEAR extraction SQL based on database driver
     * 
     * @param string $column Column name
     * @return string SQL fragment
     */
    public static function EXTRACT(YEAR FROM string $column)::int: string
    {
        $driver = config('Database')->default['DBDriver'] ?? 'MySQLi';
        
        if ($driver === 'Postgre') {
            return "EXTRACT(YEAR FROM {$column})";
        }
        
        return "EXTRACT(YEAR FROM {$column})::int";
    }

    /**
     * Get MONTH extraction SQL based on database driver
     * 
     * @param string $column Column name
     * @return string SQL fragment
     */
    public static function EXTRACT(MONTH FROM string $column)::int: string
    {
        $driver = config('Database')->default['DBDriver'] ?? 'MySQLi';
        
        if ($driver === 'Postgre') {
            return "EXTRACT(MONTH FROM {$column})";
        }
        
        return "EXTRACT(MONTH FROM {$column})::int";
    }

    /**
     * Get DAY extraction SQL based on database driver
     * 
     * @param string $column Column name
     * @return string SQL fragment
     */
    public static function day(string $column): string
    {
        $driver = config('Database')->default['DBDriver'] ?? 'MySQLi';
        
        if ($driver === 'Postgre') {
            return "EXTRACT(DAY FROM {$column})";
        }
        
        return "DAY({$column})";
    }

    /**
     * Check if using PostgreSQL
     */
    public static function isPostgres(): bool
    {
        $driver = config('Database')->default['DBDriver'] ?? 'MySQLi';
        return $driver === 'Postgre';
    }

    /**
     * Get current date SQL function
     */
    public static function currentDate(): string
    {
        return self::isPostgres() ? 'CURRENT_DATE' : 'CURDATE()';
    }

    /**
     * Get NOW() compatible function
     */
    public static function now(): string
    {
        return self::isPostgres() ? 'NOW()' : 'NOW()';
    }
}

/**
 * Helper functions for easy access
 */
if (!function_exists('db_year')) {
    function db_EXTRACT(YEAR FROM string $column)::int: string {
        return \App\Helpers\DbHelper::EXTRACT(YEAR FROM $column)::int;
    }
}

if (!function_exists('db_month')) {
    function db_EXTRACT(MONTH FROM string $column)::int: string {
        return \App\Helpers\DbHelper::EXTRACT(MONTH FROM $column)::int;
    }
}

if (!function_exists('db_is_postgres')) {
    function db_is_postgres(): bool {
        return \App\Helpers\DbHelper::isPostgres();
    }
}
