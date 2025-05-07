<?php
function trackVisitor($pdo) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $page_visited = $_SERVER['REQUEST_URI'];
    
    $stmt = $pdo->prepare("INSERT INTO visitors (ip_address, user_agent, page_visited) VALUES (?, ?, ?)");
    $stmt->execute([$ip_address, $user_agent, $page_visited]);
}

function getVisitorStats($pdo) {
    // Total pengunjung hari ini
    $stmt = $pdo->query("SELECT COUNT(DISTINCT ip_address) as today_visitors 
                         FROM visitors 
                         WHERE DATE(visit_date) = CURDATE()");
    $today_visitors = $stmt->fetch()['today_visitors'];

    // Total pengunjung minggu ini
    $stmt = $pdo->query("SELECT COUNT(DISTINCT ip_address) as week_visitors 
                         FROM visitors 
                         WHERE YEARWEEK(visit_date) = YEARWEEK(CURDATE())");
    $week_visitors = $stmt->fetch()['week_visitors'];

    // Total pengunjung bulan ini
    $stmt = $pdo->query("SELECT COUNT(DISTINCT ip_address) as month_visitors 
                         FROM visitors 
                         WHERE MONTH(visit_date) = MONTH(CURDATE()) 
                         AND YEAR(visit_date) = YEAR(CURDATE())");
    $month_visitors = $stmt->fetch()['month_visitors'];

    // Total pengunjung keseluruhan
    $stmt = $pdo->query("SELECT COUNT(DISTINCT ip_address) as total_visitors FROM visitors");
    $total_visitors = $stmt->fetch()['total_visitors'];

    return [
        'today' => $today_visitors,
        'week' => $week_visitors,
        'month' => $month_visitors,
        'total' => $total_visitors
    ];
} 