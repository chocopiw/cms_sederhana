<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header('Location: login.php');
    exit();
}

// Basic routing
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Header
include 'includes/header.php';

// Navigation
include 'includes/navbar.php';
include 'includes/sidebar.php';

// Content
switch ($page) {
    case 'dashboard':
        include 'pages/dashboard.php';
        break;
    case 'posts':
        include 'pages/posts.php';
        break;
    case 'users':
        include 'pages/users.php';
        break;
    default:
        include 'pages/dashboard.php';
}

// Footer
include 'includes/footer.php';
?> 