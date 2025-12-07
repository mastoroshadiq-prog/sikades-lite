<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *  * Extend this class in any new controllers:
 *     class Home extends BaseController
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['url', 'form', 'session'];

    /**
     * Session instance
     *
     * @var \CodeIgniter\Session\Session
     */
    protected $session;

    /**
     * Database instance
     *
     * @var \CodeIgniter\Database\BaseConnection
     */
    protected $db;

    /**
     * Validation instance
     *
     * @var \CodeIgniter\Validation\Validation
     */
    protected $validation;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
        
        // Detect HTMX request
        $isHtmxRequest = $this->request->hasHeader('HX-Request');
        
        // Set common view data
        $this->data = [
            'title' => 'Siskeudes Lite',
            'session' => $this->session,
            'isHtmxRequest' => $isHtmxRequest,
            'user' => [
                'id' => $this->session->get('user_id'),
                'username' => $this->session->get('username'),
                'role' => $this->session->get('role'),
                'kode_desa' => $this->session->get('kode_desa'),
            ],
        ];
    }

    /**
     * Check if this is an HTMX request
     */
    protected function isHtmxRequest(): bool
    {
        return $this->request->hasHeader('HX-Request');
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    protected function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') ?? false;
    }

    /**
     * Get logged-in user ID
     *
     * @return int|null
     */
    protected function getUserId(): ?int
    {
        return $this->session->get('user_id');
    }

    /**
     * Get logged-in user role
     *
     * @return string|null
     */
    protected function getUserRole(): ?string
    {
        return $this->session->get('role');
    }

    /**
     * Check if user has specific role
     *
     * @param string|array $roles
     * @return bool
     */
    protected function hasRole($roles): bool
    {
        $userRole = $this->getUserRole();
        
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        
        return $userRole === $roles;
    }

    /**
     * Render JSON response
     *
     * @param mixed $data
     * @param int $statusCode
     * @return ResponseInterface
     */
    protected function respondJson($data, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($data);
    }

    /**
     * Render success JSON response
     *
     * @param mixed $data
     * @param string $message
     * @return ResponseInterface
     */
    protected function respondSuccess($data = null, string $message = 'Success'): ResponseInterface
    {
        return $this->respondJson([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Render error JSON response
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @return ResponseInterface
     */
    protected function respondError(string $message, int $statusCode = 400, $errors = null): ResponseInterface
    {
        return $this->respondJson([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
