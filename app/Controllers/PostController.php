<?php
namespace Controllers;

use Core\Controller;
use Core\Database;

class PostController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireAuth();
    }

    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $posts = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, u.username as author_name 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN users u ON p.author_id = u.id 
             ORDER BY p.created_at DESC 
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );

        $totalPosts = $this->db->count('posts');
        $totalPages = ceil($totalPosts / $limit);

        $this->view('posts/index', [
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function create()
    {
        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        $this->view('posts/create', ['categories' => $categories]);
    }

    public function store()
    {
        $data = $this->getPostData();
        
        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');
        $excerpt = trim($data['excerpt'] ?? '');
        $categoryId = $data['category_id'] ?? null;
        $status = $data['status'] ?? 'draft';
        
        if (empty($title) || empty($content)) {
            $_SESSION['error'] = 'Title dan content harus diisi';
            $this->redirect('/dashboard/posts/create');
        }
        
        $slug = $this->createSlug($title);
        
        // Handle file upload
        $featuredImage = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $featuredImage = $this->handleFileUpload($_FILES['featured_image']);
        }
        
        $postData = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $excerpt ?: substr(strip_tags($content), 0, 200),
            'featured_image' => $featuredImage,
            'category_id' => $categoryId ?: null,
            'author_id' => $_SESSION['user_id'],
            'status' => $status
        ];
        
        $this->db->insert('posts', $postData);
        
        $_SESSION['success'] = 'Post berhasil dibuat';
        $this->redirect('/dashboard/posts');
    }

    public function edit($id)
    {
        $post = $this->db->fetch("SELECT * FROM posts WHERE id = ?", [$id]);
        
        if (!$post) {
            $_SESSION['error'] = 'Post tidak ditemukan';
            $this->redirect('/dashboard/posts');
        }
        
        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        
        $this->view('posts/edit', [
            'post' => $post,
            'categories' => $categories
        ]);
    }

    public function update($id)
    {
        $data = $this->getPostData();
        
        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');
        $excerpt = trim($data['excerpt'] ?? '');
        $categoryId = $data['category_id'] ?? null;
        $status = $data['status'] ?? 'draft';
        
        if (empty($title) || empty($content)) {
            $_SESSION['error'] = 'Title dan content harus diisi';
            $this->redirect("/dashboard/posts/edit/{$id}");
        }
        
        $slug = $this->createSlug($title);
        
        // Handle file upload
        $featuredImage = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $featuredImage = $this->handleFileUpload($_FILES['featured_image']);
        }
        
        $postData = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $excerpt ?: substr(strip_tags($content), 0, 200),
            'category_id' => $categoryId ?: null,
            'status' => $status
        ];
        
        // Add featured image if uploaded
        if ($featuredImage) {
            $postData['featured_image'] = $featuredImage;
        }
        
        $this->db->update('posts', $postData, 'id = ?', [$id]);
        
        $_SESSION['success'] = 'Post berhasil diupdate';
        $this->redirect('/dashboard/posts');
    }

    public function delete($id)
    {
        $post = $this->db->fetch("SELECT * FROM posts WHERE id = ?", [$id]);
        
        if (!$post) {
            $_SESSION['error'] = 'Post tidak ditemukan';
        } else {
            // Delete featured image if exists
            if ($post['featured_image']) {
                $imagePath = PUBLIC_PATH . '/uploads/' . $post['featured_image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $this->db->delete('posts', 'id = ?', [$id]);
            $_SESSION['success'] = 'Post berhasil dihapus';
        }
        
        $this->redirect('/dashboard/posts');
    }

    private function createSlug($title)
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check if slug exists
        $existing = $this->db->fetch("SELECT id FROM posts WHERE slug = ?", [$slug]);
        if ($existing) {
            $slug .= '-' . time();
        }
        
        return $slug;
    }

    private function handleFileUpload($file)
    {
        $uploadDir = PUBLIC_PATH . '/uploads/';
        
        // Create upload directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = 'File type not allowed. Only JPG, PNG, and GIF are allowed.';
            return null;
        }
        
        // Validate file size (5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'File size too large. Maximum size is 5MB.';
            return null;
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        } else {
            $_SESSION['error'] = 'Failed to upload file.';
            return null;
        }
    }
} 