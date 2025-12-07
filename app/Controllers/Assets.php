<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Assets extends BaseController
{
    /**
     * Serve asset images from writable folder
     * Route: /assets/image/{path}
     */
    public function image($path = '')
    {
        // Build full path
        $filePath = WRITEPATH . 'uploads/aset/' . $path;
        
        // Security: prevent directory traversal
        $realPath = realpath($filePath);
        $basePath = realpath(WRITEPATH . 'uploads/aset');
        
        if ($realPath === false || strpos($realPath, $basePath) !== 0) {
            return $this->response->setStatusCode(404);
        }
        
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404);
        }
        
        // Get mime type
        $mimeType = mime_content_type($filePath);
        
        // Return image response
        return $this->response
            ->setContentType($mimeType)
            ->setBody(file_get_contents($filePath));
    }
}
