<?php
namespace Controllers;

use Core\Controller;
use Core\Database;

class CategoryController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireAuth();
    }

    public function index()
    {
        $categories = $this->db->fetchAll(
            "SELECT c.*, COUNT(p.id) as post_count 
             FROM categories c 
             LEFT JOIN posts p ON c.id = p.category_id 
             GROUP BY c.id 
             ORDER BY c.name"
        );

        $this->view('categories/index', ['categories' => $categories]);
    }

    public function create()
    {
        $this->view('categories/create');
    }

    public function store()
    {
        $data = $this->getPostData();
        
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');
        
        if (empty($name)) {
            $_SESSION['error'] = 'Nama kategori harus diisi';
            $this->redirect('/dashboard/categories/create');
        }
        
        $slug = $this->createSlug($name);
        
        // Check if slug exists
        $existing = $this->db->fetch("SELECT id FROM categories WHERE slug = ?", [$slug]);
        if ($existing) {
            $_SESSION['error'] = 'Kategori dengan nama tersebut sudah ada';
            $this->redirect('/dashboard/categories/create');
        }
        
        $categoryData = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description
        ];
        
        $this->db->insert('categories', $categoryData);
        
        $_SESSION['success'] = 'Kategori berhasil dibuat';
        $this->redirect('/dashboard/categories');
    }

    public function edit($id)
    {
        $category = $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
        
        if (!$category) {
            $_SESSION['error'] = 'Kategori tidak ditemukan';
            $this->redirect('/dashboard/categories');
        }
        
        $this->view('categories/edit', ['category' => $category]);
    }

    public function update($id)
    {
        $data = $this->getPostData();
        
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');
        
        if (empty($name)) {
            $_SESSION['error'] = 'Nama kategori harus diisi';
            $this->redirect("/dashboard/categories/edit/{$id}");
        }
        
        $slug = $this->createSlug($name);
        
        // Check if slug exists for other categories
        $existing = $this->db->fetch("SELECT id FROM categories WHERE slug = ? AND id != ?", [$slug, $id]);
        if ($existing) {
            $_SESSION['error'] = 'Kategori dengan nama tersebut sudah ada';
            $this->redirect("/dashboard/categories/edit/{$id}");
        }
        
        $categoryData = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description
        ];
        
        $this->db->update('categories', $categoryData, 'id = ?', [$id]);
        
        $_SESSION['success'] = 'Kategori berhasil diupdate';
        $this->redirect('/dashboard/categories');
    }

    public function delete($id)
    {
        $category = $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
        
        if (!$category) {
            $_SESSION['error'] = 'Kategori tidak ditemukan';
        } else {
            // Check if category has posts
            $postCount = $this->db->count('posts', 'category_id = ?', [$id]);
            if ($postCount > 0) {
                $_SESSION['error'] = 'Kategori tidak dapat dihapus karena masih memiliki posts';
            } else {
                $this->db->delete('categories', 'id = ?', [$id]);
                $_SESSION['success'] = 'Kategori berhasil dihapus';
            }
        }
        
        $this->redirect('/dashboard/categories');
    }

    private function createSlug($name)
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }

    public function parseURL() {
        $basePath = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $url = trim($_SERVER['REQUEST_URI'], '/');
        if ($basePath && strpos($url, $basePath) === 0) {
            $url = substr($url, strlen($basePath));
            $url = ltrim($url, '/');
        }
        if (isset($url)) {
            return explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));
        }
        return []; // Penting agar root URL tidak error
    }
} 