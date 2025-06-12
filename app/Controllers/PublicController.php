<?php
namespace Controllers;

use Core\Controller;
use Core\Database;

class PublicController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function posts()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 6;
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

        // Get categories
        $categories = $this->db->fetchAll(
            "SELECT c.*, COUNT(p.id) as post_count 
             FROM categories c 
             LEFT JOIN posts p ON c.id = p.category_id 
             GROUP BY c.id 
             HAVING post_count > 0 
             ORDER BY post_count DESC"
        );

        // Track visitor
        $this->trackVisitor();

        $this->view('public/posts', [
            'posts' => $posts,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function showPost($id)
    {
        $post = $this->db->fetch(
            "SELECT p.*, c.name as category_name, u.username as author_name 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN users u ON p.author_id = u.id 
             WHERE p.id = ?",
            [$id]
        );

        if (!$post) {
            http_response_code(404);
            echo "Post not found";
            return;
        }

        // Get related posts
        $relatedPosts = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, u.username as author_name 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN users u ON p.author_id = u.id 
             WHERE p.id != ? 
             ORDER BY p.created_at DESC 
             LIMIT 3",
            [$id]
        );

        // Track visitor
        $this->trackVisitor();

        $this->view('public/show_post', [
            'post' => $post,
            'relatedPosts' => $relatedPosts
        ]);
    }

    public function categoryPosts($id)
    {
        $category = $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
        
        if (!$category) {
            http_response_code(404);
            echo "Category not found";
            return;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $posts = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, u.username as author_name 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN users u ON p.author_id = u.id 
             WHERE p.category_id = ? 
             ORDER BY p.created_at DESC 
             LIMIT ? OFFSET ?",
            [$id, $limit, $offset]
        );

        $totalPosts = $this->db->count('posts', "category_id = ?", [$id]);
        $totalPages = ceil($totalPosts / $limit);

        // Get all categories
        $categories = $this->db->fetchAll(
            "SELECT c.*, COUNT(p.id) as post_count 
             FROM categories c 
             LEFT JOIN posts p ON c.id = p.category_id 
             GROUP BY c.id 
             HAVING post_count > 0 
             ORDER BY post_count DESC"
        );

        // Track visitor
        $this->trackVisitor();

        $this->view('public/category_posts', [
            'category' => $category,
            'posts' => $posts,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
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