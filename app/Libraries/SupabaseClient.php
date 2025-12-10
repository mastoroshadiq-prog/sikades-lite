<?php

namespace App\Libraries;

/**
 * Supabase API Client for CodeIgniter 4
 * 
 * This library provides a simple interface to interact with Supabase API
 * without exposing database credentials.
 */
class SupabaseClient
{
    protected string $url;
    protected string $apiKey;
    protected string $serviceKey;
    protected array $headers = [];

    public function __construct()
    {
        $this->url = env('supabase.url', '');
        $this->apiKey = env('supabase.anon_key', '');
        $this->serviceKey = env('supabase.service_key', '');

        if (empty($this->url) || empty($this->serviceKey)) {
            throw new \Exception('Supabase URL and service_key are required. Check your .env file.');
        }

        $this->headers = [
            'apikey: ' . $this->serviceKey,
            'Authorization: Bearer ' . $this->serviceKey,
            'Content-Type: application/json',
            'Prefer: return=representation',
        ];
    }

    /**
     * GET request to Supabase REST API
     */
    public function get(string $table, array $params = []): array
    {
        $url = $this->url . '/rest/v1/' . $table;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $this->request('GET', $url);
    }

    /**
     * SELECT with filters
     * Example: $supabase->select('users', 'id,nama,email', ['role' => 'eq.admin'])
     */
    public function select(string $table, string $columns = '*', array $filters = []): array
    {
        $params = ['select' => $columns];
        
        foreach ($filters as $key => $value) {
            $params[$key] = $value;
        }

        return $this->get($table, $params);
    }

    /**
     * SELECT single row by ID
     */
    public function find(string $table, int $id, string $columns = '*'): ?array
    {
        $result = $this->select($table, $columns, ['id' => 'eq.' . $id]);
        return $result[0] ?? null;
    }

    /**
     * INSERT data
     */
    public function insert(string $table, array $data): array
    {
        $url = $this->url . '/rest/v1/' . $table;
        return $this->request('POST', $url, $data);
    }

    /**
     * UPDATE data
     */
    public function update(string $table, array $data, array $filters): array
    {
        $url = $this->url . '/rest/v1/' . $table;
        
        $queryParams = [];
        foreach ($filters as $key => $value) {
            $queryParams[] = $key . '=' . $value;
        }
        
        if (!empty($queryParams)) {
            $url .= '?' . implode('&', $queryParams);
        }

        return $this->request('PATCH', $url, $data);
    }

    /**
     * DELETE data
     */
    public function delete(string $table, array $filters): bool
    {
        $url = $this->url . '/rest/v1/' . $table;
        
        $queryParams = [];
        foreach ($filters as $key => $value) {
            $queryParams[] = $key . '=' . $value;
        }
        
        if (!empty($queryParams)) {
            $url .= '?' . implode('&', $queryParams);
        }

        $this->request('DELETE', $url);
        return true;
    }

    /**
     * UPSERT (Insert or Update)
     */
    public function upsert(string $table, array $data, string $onConflict = 'id'): array
    {
        $url = $this->url . '/rest/v1/' . $table;
        
        $headers = $this->headers;
        $headers[] = 'Prefer: resolution=merge-duplicates';
        
        return $this->request('POST', $url, $data, $headers);
    }

    /**
     * RPC - Call PostgreSQL function
     */
    public function rpc(string $functionName, array $params = []): array
    {
        $url = $this->url . '/rest/v1/rpc/' . $functionName;
        return $this->request('POST', $url, $params);
    }

    /**
     * Execute raw SQL (via RPC)
     * Note: You need to create a function in Supabase first
     */
    public function rawQuery(string $sql): array
    {
        // This requires a PostgreSQL function to execute raw SQL
        return $this->rpc('exec_sql', ['query' => $sql]);
    }

    /**
     * Count records
     */
    public function count(string $table, array $filters = []): int
    {
        $url = $this->url . '/rest/v1/' . $table . '?select=count';
        
        foreach ($filters as $key => $value) {
            $url .= '&' . $key . '=' . $value;
        }

        $headers = $this->headers;
        $headers[] = 'Prefer: count=exact';

        $response = $this->requestWithHeaders('GET', $url, null, $headers);
        
        // Count is in response header
        return (int) ($response['count'] ?? 0);
    }

    /**
     * Upload file to Supabase Storage
     */
    public function uploadFile(string $bucket, string $path, string $filePath): array
    {
        $url = $this->url . '/storage/v1/object/' . $bucket . '/' . $path;
        
        $fileContent = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath);
        
        $headers = [
            'apikey: ' . $this->serviceKey,
            'Authorization: Bearer ' . $this->serviceKey,
            'Content-Type: ' . $mimeType,
        ];

        return $this->request('POST', $url, $fileContent, $headers, false);
    }

    /**
     * Get public URL for a file
     */
    public function getPublicUrl(string $bucket, string $path): string
    {
        return $this->url . '/storage/v1/object/public/' . $bucket . '/' . $path;
    }

    /**
     * Auth - Sign in with email/password
     */
    public function signIn(string $email, string $password): array
    {
        $url = $this->url . '/auth/v1/token?grant_type=password';
        
        $headers = [
            'apikey: ' . $this->apiKey,
            'Content-Type: application/json',
        ];

        return $this->request('POST', $url, [
            'email' => $email,
            'password' => $password,
        ], $headers);
    }

    /**
     * Auth - Get user by token
     */
    public function getUser(string $accessToken): array
    {
        $url = $this->url . '/auth/v1/user';
        
        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . $accessToken,
        ];

        return $this->request('GET', $url, null, $headers);
    }

    /**
     * Make HTTP request
     */
    protected function request(string $method, string $url, $data = null, ?array $customHeaders = null, bool $jsonEncode = true): array
    {
        $headers = $customHeaders ?? $this->headers;

        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonEncode ? json_encode($data) : $data);
                }
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                if ($data !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);

        if ($error) {
            throw new \Exception('Supabase API Error: ' . $error);
        }

        if ($httpCode >= 400) {
            $errorData = json_decode($response, true);
            $message = $errorData['message'] ?? $errorData['error'] ?? 'Unknown error';
            throw new \Exception('Supabase API Error (' . $httpCode . '): ' . $message);
        }

        return json_decode($response, true) ?? [];
    }

    /**
     * Request with headers returned (for count, etc)
     */
    protected function requestWithHeaders(string $method, string $url, $data = null, ?array $customHeaders = null): array
    {
        $headers = $customHeaders ?? $this->headers;

        $ch = curl_init();
        $responseHeaders = [];
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HEADERFUNCTION => function($curl, $header) use (&$responseHeaders) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) return $len;
                
                $responseHeaders[strtolower(trim($header[0]))] = trim($header[1]);
                return $len;
            }
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true) ?? [];
        
        // Extract count from content-range header
        if (isset($responseHeaders['content-range'])) {
            preg_match('/\/(\d+)$/', $responseHeaders['content-range'], $matches);
            $data['count'] = (int) ($matches[1] ?? 0);
        }

        return $data;
    }

    /**
     * Get Supabase URL
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
