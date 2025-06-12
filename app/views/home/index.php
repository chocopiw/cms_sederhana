<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Content Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1976d2;
            --secondary-color: #e91e63;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .category-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            display: inline-block;
            margin: 0.25rem;
            transition: transform 0.3s ease;
        }
        
        .category-badge:hover {
            transform: scale(1.05);
            color: white;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 50px 0 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-tachometer-alt me-2"></i>
                <?php echo APP_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/posts">Posts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Welcome to <?php echo APP_NAME; ?></h1>
            <p class="lead mb-4">A modern Content Management System built with custom MVC framework</p>
            <a href="/posts" class="btn btn-light btn-lg">
                <i class="fas fa-file-alt me-2"></i>Browse Posts
            </a>
        </div>
    </section>

    <!-- Recent Posts Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h2 class="mb-4">
                        <i class="fas fa-file-alt me-2"></i>Recent Posts
                    </h2>
                    
                    <?php if (!empty($recentPosts)): ?>
                        <div class="row">
                            <?php foreach ($recentPosts as $post): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <a href="/posts/<?php echo $post['id']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($post['title']); ?>
                                                </a>
                                            </h5>
                                            <p class="card-text text-muted">
                                                <?php echo htmlspecialchars(substr($post['excerpt'] ?? $post['content'], 0, 100)); ?>...
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    <?php echo htmlspecialchars($post['author_name']); ?>
                                                </small>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                                </small>
                                            </div>
                                            <?php if ($post['category_name']): ?>
                                                <div class="mt-2">
                                                    <span class="badge bg-info"><?php echo htmlspecialchars($post['category_name']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="/posts" class="btn btn-primary">
                                <i class="fas fa-list me-2"></i>View All Posts
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No posts available</h5>
                            <p class="text-muted">Check back later for new content.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <h3 class="mb-4">
                        <i class="fas fa-folder me-2"></i>Categories
                    </h3>
                    
                    <?php if (!empty($categories)): ?>
                        <div class="card">
                            <div class="card-body">
                                <?php foreach ($categories as $category): ?>
                                    <a href="/categories/<?php echo $category['id']; ?>" class="category-badge">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                        <span class="badge bg-light text-dark ms-2"><?php echo $category['post_count']; ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-folder fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No categories available</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card mt-4">
                        <div class="card-body text-center">
                            <h5 class="card-title">Admin Access</h5>
                            <p class="card-text">Access the admin dashboard to manage content.</p>
                            <a href="/login" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo APP_NAME; ?></h5>
                    <p>A modern Content Management System built with custom MVC framework.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="/posts" class="text-white text-decoration-none">Posts</a></li>
                        <li><a href="/login" class="text-white text-decoration-none">Admin Login</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 