<?php
namespace Controllers;

use Core\Controller;
use Core\Database;

class DashboardController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireAuth();
    }

    public function index()
    {
        // Get statistics
        $stats = [
            'posts' => $this->db->count('posts'),
            'categories' => $this->db->count('categories'),
            'users' => $this->db->count('users'),
            'visitors' => $this->db->count('visitors')
        ];

        // Get recent posts
        $recentPosts = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, u.username as author_name 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN users u ON p.author_id = u.id 
             ORDER BY p.created_at DESC 
             LIMIT 5"
        );

        // Get visitor statistics
        $visitorStats = $this->getVisitorStats();

        // Get recent visitors
        $recentVisitors = $this->db->fetchAll(
            "SELECT * FROM visitors ORDER BY visit_date DESC LIMIT 5"
        );

        $this->view('dashboard/index', [
            'stats' => $stats,
            'recentPosts' => $recentPosts,
            'visitorStats' => $visitorStats,
            'recentVisitors' => $recentVisitors
        ]);
    }

    private function getVisitorStats()
    {
        $today = $this->db->count('visitors', 'DATE(visit_date) = CURDATE()');
        $week = $this->db->count('visitors', 'YEARWEEK(visit_date) = YEARWEEK(CURDATE())');
        $month = $this->db->count('visitors', 'MONTH(visit_date) = MONTH(CURDATE()) AND YEAR(visit_date) = YEAR(CURDATE())');
        $total = $this->db->count('visitors');

        return [
            'today' => $today,
            'week' => $week,
            'month' => $month,
            'total' => $total
        ];
    }
} 