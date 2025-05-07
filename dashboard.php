<?php
session_start();
require_once 'config/database.php';
require_once 'includes/track_visitor.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Track visitor
trackVisitor($pdo);

// Get visitor statistics
$visitor_stats = getVisitorStats($pdo);

// Get recent visitors
$stmt = $pdo->query("SELECT * FROM visitors ORDER BY visit_date DESC LIMIT 5");
$recent_visitors = $stmt->fetchAll();

// Get recent posts
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT 5");
$recent_posts = $stmt->fetchAll();

// Get visitor data for chart (last 7 days)
$stmt = $pdo->query("SELECT DATE(visit_date) as date, COUNT(DISTINCT ip_address) as count 
                     FROM visitors 
                     WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                     GROUP BY DATE(visit_date)
                     ORDER BY date");
$visitor_chart_data = $stmt->fetchAll();

// Get visitor data by hour
$stmt = $pdo->query("SELECT HOUR(visit_date) as hour, COUNT(DISTINCT ip_address) as count 
                     FROM visitors 
                     WHERE DATE(visit_date) = CURDATE()
                     GROUP BY HOUR(visit_date)
                     ORDER BY hour");
$visitor_hour_data = $stmt->fetchAll();

// Get top visited pages
$stmt = $pdo->query("SELECT page_visited, COUNT(*) as count 
                     FROM visitors 
                     GROUP BY page_visited 
                     ORDER BY count DESC 
                     LIMIT 5");
$top_pages = $stmt->fetchAll();

// Get visitor data by browser
$stmt = $pdo->query("SELECT 
                        CASE 
                            WHEN user_agent LIKE '%Chrome%' THEN 'Chrome'
                            WHEN user_agent LIKE '%Firefox%' THEN 'Firefox'
                            WHEN user_agent LIKE '%Safari%' THEN 'Safari'
                            WHEN user_agent LIKE '%Edge%' THEN 'Edge'
                            ELSE 'Other'
                        END as browser,
                        COUNT(DISTINCT ip_address) as count
                     FROM visitors 
                     GROUP BY browser");
$browser_stats = $stmt->fetchAll();

// Kalender aktivitas: simpan aktivitas di session
if (!isset($_SESSION['activities'])) {
    $_SESSION['activities'] = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activity_date'], $_POST['activity_note'])) {
    $date = $_POST['activity_date'];
    $note = trim($_POST['activity_note']);
    if ($date && $note) {
        $_SESSION['activities'][$date][] = $note;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CMS Sederhana</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Flatpickr Calendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            background: #f8bbd0 !important; /* Pink pastel lebih gelap */
        }
        .main-sidebar, .main-sidebar.sidebar-dark-primary {
            background-color: #f06292 !important; /* Sidebar pink lebih gelap */
        }
        .content-wrapper, .content-header, .content {
            background: #f8bbd0 !important; /* Pink pastel lebih gelap */
        }
        .card, .small-box, .info-box {
            background: #fff !important; /* Card putih */
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 2px 8px rgba(248,187,208,0.15);
        }
        .small-box.bg-info, .card.bg-info {
            background: #64b5f6 !important; /* Blue pastel */
            color: #1a237e !important;
        }
        .small-box.bg-info .icon, .card.bg-info .icon {
            color: #1976d2 !important;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #fce4ec !important;
            color: #1976d2 !important;
        }
        .sidebar .nav-link.active i, .sidebar .nav-link:hover i {
            color: #1976d2 !important;
        }
        .sidebar .nav-link, .sidebar .nav-link i {
            color: #ad1457 !important;
        }
        .brand-link {
            background: #f06292 !important;
            color: #fff !important;
        }
        .main-footer {
            background: #f06292 !important;
            color: #fff !important;
        }
        h1, h2, h3, h4, h5, h6, .info-box-text, .info-box-number, .card-title, .brand-text {
            color: #ad1457 !important;
        }
        .small-box-footer, .uppercase, .btn, a {
            color: #1976d2 !important;
        }
        .btn-primary, .btn-info {
            background: #1976d2 !important;
            border-color: #1976d2 !important;
            color: #fff !important;
        }
        .btn-primary:hover, .btn-info:hover {
            background: #1565c0 !important;
            border-color: #1565c0 !important;
        }
        /* Perjelas warna teks di card dan error */
        .card, .small-box, .info-box, .card-body, .info-box-content, .small-box .inner {
            color: #333 !important;
        }
        .alert, .alert-danger, .error, .swal2-popup, .swal2-title, .swal2-content {
            color: #b71c1c !important;
            background: #fff !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="dashboard.php" class="brand-link">
                <span class="brand-text font-weight-light">CMS Sederhana</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <a href="#" class="d-block"><?php echo htmlspecialchars($user['username']); ?></a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link active">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/posts.php" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Posts</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/categories.php" class="nav-link">
                                <i class="nav-icon fas fa-folder"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/users.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/settings.php" class="nav-link">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Settings</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div id="welcome-sofy" style="font-size:1.5rem;font-weight:bold;color:#007bff;margin-bottom:10px;"></div>
                        </div>
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                            <p style="font-size:1.2rem;font-weight:bold;color:#007bff;margin-top:5px;">SOFY NUR KHOLIFAH</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="container-fluid mt-4">
                        <!-- Welcome Box -->
                        <div class="row justify-content-center mb-4">
                            <div class="col-lg-10">
                                <div style="background:#fff;border-radius:18px;box-shadow:0 2px 16px rgba(0,0,0,0.07);padding:2.5rem 1rem;text-align:center;">
                                    <h1 style="font-size:2.5rem;font-weight:800;color:#333;margin-bottom:0.5rem;">Selamat Datang, SOFY NUR KHOLIFAH!</h1>
                                    <div style="font-size:1.2rem;color:#888;">Selamat datang di Dashboard CMS Sederhana</div>
                                </div>
                            </div>
                        </div>
                        <!-- Statistik Cards -->
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-3 mb-3">
                                <div style="background:#1976d2;color:#fff;border-radius:16px;padding:2rem 1rem;text-align:center;box-shadow:0 2px 8px rgba(25,118,210,0.08);">
                                    <div style="font-size:2.2rem;font-weight:700;"> <?php echo $postCount ?? 0; ?> </div>
                                    <div style="font-size:1.1rem;">Total Posts</div>
                                    <a href="pages/posts.php" class="btn btn-light mt-3" style="font-weight:600;color:#1976d2;">Lihat Posts</a>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div style="background:#e91e63;color:#fff;border-radius:16px;padding:2rem 1rem;text-align:center;box-shadow:0 2px 8px rgba(233,30,99,0.08);">
                                    <div style="font-size:2.2rem;font-weight:700;"> <?php echo $categoryCount ?? 0; ?> </div>
                                    <div style="font-size:1.1rem;">Total Categories</div>
                                    <a href="pages/categories.php" class="btn btn-light mt-3" style="font-weight:600;color:#e91e63;">Lihat Categories</a>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div style="background:#1976d2;color:#fff;border-radius:16px;padding:2rem 1rem;text-align:center;box-shadow:0 2px 8px rgba(25,118,210,0.08);">
                                    <div style="font-size:2.2rem;font-weight:700;"> <?php echo $userCount ?? 0; ?> </div>
                                    <div style="font-size:1.1rem;">Total Users</div>
                                    <a href="pages/users.php" class="btn btn-light mt-3" style="font-weight:600;color:#1976d2;">Lihat Users</a>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div style="background:#e91e63;color:#fff;border-radius:16px;padding:2rem 1rem;text-align:center;box-shadow:0 2px 8px rgba(233,30,99,0.08);">
                                    <div style="font-size:2.2rem;font-weight:700;">0</div>
                                    <div style="font-size:1.1rem;">Total Comments</div>
                                    <a href="#" class="btn btn-light mt-3" style="font-weight:600;color:#e91e63;">Lihat Comments</a>
                                </div>
                            </div>
                            <!-- Card Total Pengunjung -->
                            <div class="col-md-3 mb-3">
                                <div style="background:#1976d2;color:#fff;border-radius:16px;padding:2rem 1rem;text-align:center;box-shadow:0 2px 8px rgba(25,118,210,0.08);">
                                    <div style="font-size:2.2rem;font-weight:700;"> <?php echo isset($visitor_stats['total']) ? $visitor_stats['total'] : 0; ?> </div>
                                    <div style="font-size:1.1rem;">Total Pengunjung</div>
                                    <a href="pages/visitors.php" class="btn btn-light mt-3" style="font-weight:600;color:#1976d2;">Lihat Pengunjung</a>
                                </div>
                            </div>
                            <!-- Card Statistik -->
                            <div class="col-md-3 mb-3">
                                <div style="background:#e91e63;color:#fff;border-radius:16px;padding:2rem 1rem;text-align:center;box-shadow:0 2px 8px rgba(233,30,99,0.08);">
                                    <div style="font-size:2.2rem;font-weight:700;">Statistik</div>
                                    <div style="font-size:1.1rem;">Statistik</div>
                                    <a href="#" class="btn btn-light mt-3" style="font-weight:600;color:#e91e63;">Lihat Statistik</a>
                                </div>
                            </div>
                        </div>
                        <!-- Recent Posts Table -->
                        <div class="row justify-content-center mb-4">
                            <div class="col-lg-10">
                                <div style="background:#fff;border-radius:18px;box-shadow:0 2px 16px rgba(0,0,0,0.07);padding:1.5rem 1rem;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                                        <h5 style="font-weight:700;color:#333;">Recent Posts</h5>
                                        <a href="pages/posts.php" class="btn btn-primary btn-sm" style="background:#1976d2;border:none;">View All</a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead style="background:#f5f5f5;">
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Author</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (!empty($recent_posts)) : foreach ($recent_posts as $post) : ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($post['category_id'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></td>
                                                    <td><span class="badge badge-success">Published</span></td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                                <tr><td colspan="5" class="text-center">No posts found.</td></tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visitor Statistics Row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-pie mr-1"></i>
                                        Statistik Pengunjung
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Pengunjung</span>
                                                    <span class="info-box-number"><?php echo $visitor_stats['total']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fas fa-calendar-day"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Pengunjung Hari Ini</span>
                                                    <span class="info-box-number"><?php echo $visitor_stats['today']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i class="fas fa-calendar-week"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Pengunjung Minggu Ini</span>
                                                    <span class="info-box-number"><?php echo $visitor_stats['week']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-danger"><i class="fas fa-calendar-alt"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Pengunjung Bulan Ini</span>
                                                    <span class="info-box-number"><?php echo $visitor_stats['month']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-line mr-1"></i>
                                        Grafik Pengunjung (7 Hari Terakhir)
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="visitorChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pengunjung Terakhir
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2">
                                        <?php foreach ($recent_visitors as $visitor): ?>
                                        <li class="item">
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">
                                                    <?php echo htmlspecialchars($visitor['ip_address']); ?>
                                                    <span class="badge badge-info float-right">
                                                        <?php echo date('H:i', strtotime($visitor['visit_date'])); ?>
                                                    </span>
                                                </a>
                                                <span class="product-description">
                                                    <?php echo htmlspecialchars($visitor['page_visited']); ?>
                                                </span>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="pages/visitors.php" class="uppercase">Lihat Semua Pengunjung</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Charts Row -->
                    <div class="row">
                        <!-- Visitor by Hour Chart -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-bar mr-1"></i>
                                        Pengunjung per Jam (Hari Ini)
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="visitorHourChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- Browser Statistics Chart -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-pie mr-1"></i>
                                        Statistik Browser
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="browserChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Pages and Recent Posts Row -->
                    <div class="row">
                        <!-- Top Visited Pages -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-star mr-1"></i>
                                        Halaman Terpopuler
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Halaman</th>
                                                    <th>Jumlah Kunjungan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($top_pages as $page): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($page['page_visited']); ?></td>
                                                    <td>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-primary" role="progressbar" 
                                                                 style="width: <?php echo ($page['count'] / $top_pages[0]['count'] * 100); ?>%">
                                                            </div>
                                                        </div>
                                                        <small>
                                                            <?php echo $page['count']; ?> kunjungan
                                                        </small>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Posts -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-file-alt mr-1"></i>
                                        Post Terbaru
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Judul</th>
                                                    <th>Tanggal Dibuat</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_posts as $post): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></td>
                                                    <td>
                                                        <a href="pages/edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-info">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="pages/view_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-success">
                                                            <i class="fas fa-eye"></i> Lihat
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="pages/posts.php" class="uppercase">Lihat Semua Post</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jam digital dan kalender aktivitas -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <div style="background:#fff;border-radius:10px;padding:1rem;box-shadow:0 2px 8px rgba(248,187,208,0.15);display:flex;align-items:center;">
                                <i class="fas fa-clock" style="font-size:1.5rem;color:#1976d2;margin-right:10px;"></i>
                                <span id="digital-clock" style="font-size:1.3rem;font-weight:bold;color:#ad1457;"></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div style="background:#fff;border-radius:10px;padding:1rem;box-shadow:0 2px 8px rgba(248,187,208,0.15);">
                                <i class="fas fa-calendar-alt" style="font-size:1.5rem;color:#1976d2;margin-right:10px;"></i>
                                <input type="text" id="activity-calendar" name="activity_date" class="form-control" style="display:inline-block;width:auto;border:none;font-weight:bold;color:#ad1457;background:transparent;box-shadow:none;" readonly>
                                <form method="POST" class="mt-2" style="display:flex;gap:5px;">
                                    <input type="hidden" id="activity-date-hidden" name="activity_date">
                                    <input type="text" name="activity_note" class="form-control" placeholder="Catatan aktivitas..." required style="flex:1;">
                                    <button type="submit" class="btn btn-primary">Tambah</button>
                                </form>
                                <div class="mt-2" id="activity-list">
                                    <?php
                                    $selectedDate = isset($_POST['activity_date']) ? $_POST['activity_date'] : date('Y-m-d');
                                    if (!empty($_SESSION['activities'][$selectedDate])) {
                                        echo '<ul style="padding-left:18px;">';
                                        foreach ($_SESSION['activities'][$selectedDate] as $act) {
                                            echo '<li style="color:#1976d2;font-weight:500;">'.htmlspecialchars($act).'</li>';
                                        }
                                        echo '</ul>';
                                    } else {
                                        echo '<span style="color:#aaa;">Belum ada aktivitas.</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">CMS Sederhana</a>.</strong> All rights reserved.
        </footer>
    </div>

    <!-- AdminLTE JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- AOS - Animate On Scroll -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <!-- Typed.js -->
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <!-- Custom JavaScript -->
    <script>
    // Loading Screen
    window.addEventListener('load', function() {
        const loader = document.createElement('div');
        loader.className = 'loading-screen';
        loader.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="loading-text">Loading Dashboard...</div>
        `;
        document.body.appendChild(loader);

        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.remove();
            }, 500);
        }, 1000);
    });

    // Add loading screen styles
    const style = document.createElement('style');
    style.textContent = `
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s;
        }
        .loading-text {
            margin-top: 1rem;
            font-size: 1.2rem;
            color: #333;
        }
        .card {
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    `;
    document.head.appendChild(style);

    // Initialize AOS with more options
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false,
        offset: 50
    });

    // Add animation classes to cards with delay
    document.querySelectorAll('.card').forEach((card, index) => {
        card.setAttribute('data-aos', 'fade-up');
        card.setAttribute('data-aos-delay', index * 100);
        
        // Add pulse effect to important numbers
        const numbers = card.querySelectorAll('.info-box-number');
        numbers.forEach(number => {
            number.classList.add('pulse');
        });
    });

    // Typed.js for welcome message
    const welcomeMessage = document.createElement('div');
    welcomeMessage.className = 'welcome-message';
    welcomeMessage.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 15px;
        border-radius: 5px;
        z-index: 1000;
        display: none;
    `;
    document.body.appendChild(welcomeMessage);

    new Typed(welcomeMessage, {
        strings: ['Selamat datang di Dashboard!', 'Semoga hari Anda menyenangkan!'],
        typeSpeed: 50,
        backSpeed: 30,
        loop: true,
        showCursor: false,
        onStart: function() {
            welcomeMessage.style.display = 'block';
        }
    });

    // Real-time visitor counter animation with sound
    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentValue = Math.floor(progress * (end - start) + start);
            obj.innerHTML = currentValue;
            
            // Add sound effect for number changes
            if (currentValue % 10 === 0) {
                const audio = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU');
                audio.volume = 0.1;
                audio.play().catch(() => {});
            }
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Enhanced hover effects for cards
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
            this.style.transition = 'all 0.3s ease';
            
            // Add floating effect to icons
            const icon = this.querySelector('.icon i');
            if (icon) {
                icon.classList.add('floating');
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            
            // Remove floating effect
            const icon = this.querySelector('.icon i');
            if (icon) {
                icon.classList.remove('floating');
            }
        });
    });

    // Enhanced refresh button with animation
    const refreshButton = document.createElement('button');
    refreshButton.className = 'btn btn-primary btn-sm';
    refreshButton.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh Data';
    refreshButton.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        animation: pulse 2s infinite;
    `;
    
    refreshButton.addEventListener('click', function() {
        // Add rotation animation
        this.style.transform = 'rotate(360deg)';
        this.style.transition = 'transform 1s ease';
        
        Swal.fire({
            title: 'Memperbarui Data...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            this.style.transform = 'rotate(0deg)';
            location.reload();
        }, 1000);
    });
    
    document.body.appendChild(refreshButton);

    // Add particle effect to background
    particlesJS('particles-js', {
        particles: {
            number: { value: 80, density: { enable: true, value_area: 800 } },
            color: { value: '#007bff' },
            shape: { type: 'circle' },
            opacity: { value: 0.5, random: false },
            size: { value: 3, random: true },
            line_linked: {
                enable: true,
                distance: 150,
                color: '#007bff',
                opacity: 0.4,
                width: 1
            },
            move: {
                enable: true,
                speed: 2,
                direction: 'none',
                random: false,
                straight: false,
                out_mode: 'out',
                bounce: false
            }
        },
        interactivity: {
            detect_on: 'canvas',
            events: {
                onhover: { enable: true, mode: 'repulse' },
                onclick: { enable: true, mode: 'push' },
                resize: true
            }
        },
        retina_detect: true
    });

    // Add particle container
    const particleContainer = document.createElement('div');
    particleContainer.id = 'particles-js';
    particleContainer.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        pointer-events: none;
    `;
    document.body.appendChild(particleContainer);

    // Enhanced chart hover effects
    function addChartHoverEffects(chart) {
        const canvas = chart.canvas;
        canvas.addEventListener('mousemove', function(e) {
            const points = chart.getElementsAtEventForMode(e, 'nearest', { intersect: true }, true);
            if (points.length) {
                canvas.style.cursor = 'pointer';
                // Add glow effect
                canvas.style.boxShadow = '0 0 10px rgba(0,123,255,0.5)';
            } else {
                canvas.style.cursor = 'default';
                canvas.style.boxShadow = 'none';
            }
        });
    }

    // Charts Initialization with enhanced features
    document.addEventListener('DOMContentLoaded', function() {
        // Visitor Chart (7 Days)
        var ctx = document.getElementById('visitorChart').getContext('2d');
        var visitorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($visitor_chart_data, 'date')); ?>,
                datasets: [{
                    label: 'Jumlah Pengunjung',
                    data: <?php echo json_encode(array_column($visitor_chart_data, 'count')); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        titleColor: '#fff',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    },
                    legend: {
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
        addChartHoverEffects(visitorChart);

        // Visitor by Hour Chart
        var ctxHour = document.getElementById('visitorHourChart').getContext('2d');
        var visitorHourChart = new Chart(ctxHour, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($visitor_hour_data, 'hour')); ?>,
                datasets: [{
                    label: 'Jumlah Pengunjung per Jam',
                    data: <?php echo json_encode(array_column($visitor_hour_data, 'count')); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        titleColor: '#fff',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
        addChartHoverEffects(visitorHourChart);

        // Browser Statistics Chart
        var ctxBrowser = document.getElementById('browserChart').getContext('2d');
        var browserChart = new Chart(ctxBrowser, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($browser_stats, 'browser')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($browser_stats, 'count')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        titleColor: '#fff',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
        addChartHoverEffects(browserChart);
    });

    // Add smooth scroll to top button with animation
    const scrollButton = document.createElement('button');
    scrollButton.className = 'btn btn-primary btn-sm';
    scrollButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollButton.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 20px;
        z-index: 1000;
        display: none;
        transition: all 0.3s ease;
    `;
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 100) {
            scrollButton.style.display = 'block';
            scrollButton.style.opacity = '1';
        } else {
            scrollButton.style.opacity = '0';
            setTimeout(() => {
                if (window.pageYOffset <= 100) {
                    scrollButton.style.display = 'none';
                }
            }, 300);
        }
    });
    
    scrollButton.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    document.body.appendChild(scrollButton);

    // Add notification sound for important updates
    function playNotificationSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU');
        audio.volume = 0.2;
        audio.play().catch(() => {});
    }

    // Simulate real-time updates
    setInterval(() => {
        const randomCard = document.querySelector('.card');
        if (randomCard) {
            randomCard.classList.add('pulse');
            setTimeout(() => {
                randomCard.classList.remove('pulse');
            }, 1000);
            playNotificationSound();
        }
    }, 30000);

    // Welcome message for SOFY NUR KHOLIFAH
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Typed) {
            new Typed('#welcome-sofy', {
                strings: ['Welcome <b>SOFY NUR KHOLIFAH</b>!'],
                typeSpeed: 60,
                backSpeed: 30,
                showCursor: false,
                smartBackspace: false
            });
        } else {
            document.getElementById('welcome-sofy').innerHTML = 'Welcome <b>SOFY NUR KHOLIFAH</b>!';
        }
    });

    // Jam digital
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('digital-clock').textContent = `${h}:${m}:${s}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Kalender aktivitas
    flatpickr('#activity-calendar', {
        inline: true,
        locale: 'id',
        defaultDate: '<?php echo isset($_POST['activity_date']) ? $_POST['activity_date'] : date('Y-m-d'); ?>',
        showMonths: 1,
        disableMobile: true,
        onChange: function(selectedDates, dateStr) {
            document.getElementById('activity-date-hidden').value = dateStr;
            document.getElementById('activity-calendar').value = dateStr;
            document.querySelector('form.mt-2').submit();
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Set hidden input value saat load
        var cal = document.getElementById('activity-calendar');
        var hidden = document.getElementById('activity-date-hidden');
        if (cal && hidden) {
            hidden.value = cal.value;
        }
    });
    </script>
</body>
</html> 