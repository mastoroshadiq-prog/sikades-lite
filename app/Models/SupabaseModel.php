<?php

namespace App\Models;

use App\Libraries\SupabaseClient;

/**
 * Base Model for Supabase API
 * 
 * This model provides a unified interface that works with both
 * Supabase API and traditional CodeIgniter database.
 */
class SupabaseModel
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $allowedFields = [];
    protected bool $useTimestamps = true;
    protected string $createdField = 'created_at';
    protected string $updatedField = 'updated_at';
    
    protected ?SupabaseClient $supabase = null;
    protected bool $useSupabaseApi = false;

    public function __construct()
    {
        // Check if Supabase API is configured
        if (env('supabase.url') && env('supabase.service_key')) {
            $this->useSupabaseApi = true;
            $this->supabase = new SupabaseClient();
        }
    }

    /**
     * Get all records
     */
    public function findAll(?int $limit = null, ?int $offset = null): array
    {
        if (!$this->useSupabaseApi) {
            return $this->getDb()->findAll($limit, $offset);
        }

        $params = ['select' => '*'];
        
        if ($limit) {
            $params['limit'] = $limit;
        }
        if ($offset) {
            $params['offset'] = $offset;
        }

        return $this->supabase->get($this->table, $params);
    }

    /**
     * Find by ID
     */
    public function find($id): ?array
    {
        if (!$this->useSupabaseApi) {
            return $this->getDb()->find($id);
        }

        return $this->supabase->find($this->table, $id);
    }

    /**
     * Get records with filters
     */
    public function where(string $field, $value): self
    {
        $this->whereFilters[$field] = $value;
        return $this;
    }

    /**
     * Execute query and get results
     */
    public function get(): array
    {
        if (!$this->useSupabaseApi) {
            $builder = $this->getDb();
            foreach ($this->whereFilters ?? [] as $field => $value) {
                $builder->where($field, $value);
            }
            $this->whereFilters = [];
            return $builder->findAll();
        }

        $filters = [];
        foreach ($this->whereFilters ?? [] as $field => $value) {
            $filters[$field] = 'eq.' . $value;
        }
        $this->whereFilters = [];

        return $this->supabase->select($this->table, '*', $filters);
    }

    /**
     * Insert new record
     */
    public function insert(array $data): int|bool
    {
        // Filter to allowed fields
        $data = array_intersect_key($data, array_flip($this->allowedFields));
        
        // Add timestamps
        if ($this->useTimestamps) {
            $now = date('Y-m-d H:i:s');
            $data[$this->createdField] = $now;
            $data[$this->updatedField] = $now;
        }

        if (!$this->useSupabaseApi) {
            return $this->getDb()->insert($data);
        }

        $result = $this->supabase->insert($this->table, $data);
        return $result[0][$this->primaryKey] ?? false;
    }

    /**
     * Update record
     */
    public function update($id, array $data): bool
    {
        // Filter to allowed fields
        $data = array_intersect_key($data, array_flip($this->allowedFields));
        
        // Add timestamp
        if ($this->useTimestamps) {
            $data[$this->updatedField] = date('Y-m-d H:i:s');
        }

        if (!$this->useSupabaseApi) {
            return $this->getDb()->update($id, $data);
        }

        $this->supabase->update($this->table, $data, [$this->primaryKey => 'eq.' . $id]);
        return true;
    }

    /**
     * Delete record
     */
    public function delete($id): bool
    {
        if (!$this->useSupabaseApi) {
            return $this->getDb()->delete($id);
        }

        return $this->supabase->delete($this->table, [$this->primaryKey => 'eq.' . $id]);
    }

    /**
     * Count records
     */
    public function countAll(): int
    {
        if (!$this->useSupabaseApi) {
            return $this->getDb()->countAll();
        }

        return $this->supabase->count($this->table);
    }

    /**
     * Count with filter
     */
    public function countAllResults(bool $reset = true): int
    {
        if (!$this->useSupabaseApi) {
            return $this->getDb()->countAllResults($reset);
        }

        $filters = [];
        foreach ($this->whereFilters ?? [] as $field => $value) {
            $filters[$field] = 'eq.' . $value;
        }
        
        if ($reset) {
            $this->whereFilters = [];
        }

        return $this->supabase->count($this->table, $filters);
    }

    /**
     * Get first result
     */
    public function first(): ?array
    {
        if (!$this->useSupabaseApi) {
            return $this->getDb()->first();
        }

        $filters = [];
        foreach ($this->whereFilters ?? [] as $field => $value) {
            $filters[$field] = 'eq.' . $value;
        }
        $this->whereFilters = [];

        $filters['limit'] = 1;
        $result = $this->supabase->select($this->table, '*', $filters);
        return $result[0] ?? null;
    }

    /**
     * Order by
     */
    public function orderBy(string $field, string $direction = 'ASC'): self
    {
        $this->orderField = $field;
        $this->orderDirection = strtolower($direction);
        return $this;
    }

    /**
     * Get CodeIgniter Model for fallback
     */
    protected function getDb()
    {
        // Return a CodeIgniter model instance
        // This should be implemented in child classes
        return db_connect()->table($this->table);
    }

    /**
     * Direct access to Supabase client
     */
    public function supabase(): ?SupabaseClient
    {
        return $this->supabase;
    }

    /**
     * Check if using Supabase API
     */
    public function isUsingSupabase(): bool
    {
        return $this->useSupabaseApi;
    }
}
