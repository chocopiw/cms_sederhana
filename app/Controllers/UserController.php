<?php
namespace Controllers;

use Core\Controller;
use Core\Database;

class UserController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $users = $this->db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");
        $this->view('dashboard/users/index', ['users' => $users, 'title' => 'User Management']);
    }

    public function create()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $this->view('dashboard/users/create', ['title' => 'Create User']);
    }

    public function store()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Username and password are required';
                header('Location: /dashboard/users/create');
                exit;
            }

            // Check if username already exists
            $existingUser = $this->db->fetch("SELECT id FROM users WHERE username = ?", [$username]);
            if ($existingUser) {
                $_SESSION['error'] = 'Username already exists';
                header('Location: /dashboard/users/create');
                exit;
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $result = $this->db->insert('users', [
                'username' => $username,
                'password' => $hashedPassword
            ]);

            if ($result) {
                $_SESSION['success'] = 'User created successfully';
                header('Location: /dashboard/users');
            } else {
                $_SESSION['error'] = 'Failed to create user';
                header('Location: /dashboard/users/create');
            }
            exit;
        }
    }

    public function edit($id)
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            header('Location: /dashboard/users');
            exit;
        }

        $this->view('dashboard/users/edit', ['user' => $user, 'title' => 'Edit User']);
    }

    public function update($id)
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username)) {
                $_SESSION['error'] = 'Username is required';
                header('Location: /dashboard/users/edit/' . $id);
                exit;
            }

            // Check if username already exists for other users
            $existingUser = $this->db->fetch("SELECT id FROM users WHERE username = ? AND id != ?", [$username, $id]);
            if ($existingUser) {
                $_SESSION['error'] = 'Username already exists';
                header('Location: /dashboard/users/edit/' . $id);
                exit;
            }

            $data = ['username' => $username];
            
            // Update password only if provided
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $result = $this->db->update('users', $data, ['id' => $id]);

            if ($result) {
                $_SESSION['success'] = 'User updated successfully';
                header('Location: /dashboard/users');
            } else {
                $_SESSION['error'] = 'Failed to update user';
                header('Location: /dashboard/users/edit/' . $id);
            }
            exit;
        }
    }

    public function delete($id)
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        // Prevent deleting admin user
        if ($id == 1) {
            $_SESSION['error'] = 'Cannot delete admin user';
            header('Location: /dashboard/users');
            exit;
        }

        $result = $this->db->delete('users', ['id' => $id]);

        if ($result) {
            $_SESSION['success'] = 'User deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete user';
        }

        header('Location: /dashboard/users');
        exit;
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
} 