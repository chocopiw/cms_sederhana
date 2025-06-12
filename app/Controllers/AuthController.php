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
            $this->redirect('/dashboard');
        }
        
        $this->view('auth/login');
    }

    public function login()
    {
        $data = $this->getPostData();
        
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Username dan password harus diisi';
            $this->redirect('/login');
        }
        
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE username = ? OR email = ?",
            [$username, $username]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Track visitor
            $this->trackVisitor();
            
            $this->redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Username atau password salah';
            $this->redirect('/login');
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }

    private function trackVisitor()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $page = $_SERVER['REQUEST_URI'];
        
        $this->db->insert('visitors', [
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'page_visited' => $page
        ]);
    }
} 