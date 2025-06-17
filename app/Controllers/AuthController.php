<?php
namespace Controllers;

use Core\Controller;
use Core\Database;

class AuthController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function loginForm()
    {
        if ($this->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        
        $this->view('auth/login', ['title' => 'Login']);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Username dan password harus diisi';
            header('Location: /login');
            exit;
        }
        
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE username = ?",
            [$username]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Track visitor
            $this->trackVisitor();
            
            header('Location: /dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Username atau password salah';
            header('Location: /login');
            exit;
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    private function trackVisitor()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $page = $_SERVER['REQUEST_URI'] ?? '/';
        
        try {
            $this->db->insert('visitors', [
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'page_visited' => $page
            ]);
        } catch (Exception $e) {
            // Ignore visitor tracking errors
        }
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
} 