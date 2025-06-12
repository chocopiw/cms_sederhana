<?php
namespace Controllers;

use Core\Controller;
use Core\Database;

class HomeController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        // Get recent published posts
        $recentPosts = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, u.username as author_name 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN users u ON p.author_id = u.id 
             ORDER BY p.created_at DESC 
             LIMIT 6"
        );

        // Get categories with post count
        $categories = $this->db->fetchAll(
            "SELECT c.*, COUNT(p.id) as post_count 
             FROM categories c 
             LEFT JOIN posts p ON c.id = p.category_id 
             GROUP BY c.id 
             HAVING post_count > 0 
             ORDER BY post_count DESC 
             LIMIT 8"
        );

        // Track visitor
        $this->trackVisitor();

        $this->view('home/index', [
            'recentPosts' => $recentPosts,
            'categories' => $categories
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