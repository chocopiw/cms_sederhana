<style>
    .content-wrapper {
        background-color: #ffd1dc !important; /* pink pastel */
    }
</style>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                    <h5 class="text-secondary">SOFY NUR KHOLIFAH</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM posts");
                            $postCount = $stmt->fetchColumn();
                            ?>
                            <h3><?php echo $postCount; ?></h3>
                            <p>Total Posts</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <a href="index.php?page=posts" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                            $userCount = $stmt->fetchColumn();
                            ?>
                            <h3><?php echo $userCount; ?></h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="index.php?page=users" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE DATE(created_at) = CURDATE()");
                            $todayPosts = $stmt->fetchColumn();
                            ?>
                            <h3><?php echo $todayPosts; ?></h3>
                            <p>Posts Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()");
                            $todayUsers = $stmt->fetchColumn();
                            ?>
                            <h3><?php echo $todayUsers; ?></h3>
                            <p>New Users Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Posts</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card pl-2 pr-2">
                                <?php
                                $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT 5");
                                while ($post = $stmt->fetch()) {
                                    echo '<li class="item">
                                        <div class="product-info">
                                            <a href="index.php?page=posts&action=edit&id=' . $post['id'] . '" class="product-title">
                                                ' . htmlspecialchars($post['title']) . '
                                                <span class="badge badge-info float-right">' . date('M d, Y', strtotime($post['created_at'])) . '</span>
                                            </a>
                                        </div>
                                    </li>';
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="index.php?page=posts" class="uppercase">View All Posts</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Users</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card pl-2 pr-2">
                                <?php
                                $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
                                while ($user = $stmt->fetch()) {
                                    echo '<li class="item">
                                        <div class="product-info">
                                            <span class="product-title">' . htmlspecialchars($user['username']) . '
                                                <span class="badge badge-success float-right">' . date('M d, Y', strtotime($user['created_at'])) . '</span>
                                            </span>
                                        </div>
                                    </li>';
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="index.php?page=users" class="uppercase">View All Users</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 