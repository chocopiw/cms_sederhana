<?php
// Handle post actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'delete':
            if (isset($_GET['id'])) {
                $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                header('Location: index.php?page=posts');
                exit();
            }
            break;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    if (isset($_POST['id'])) {
        // Update existing post
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$title, $content, $_POST['id']]);
    } else {
        // Create new post
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$title, $content]);
    }
    header('Location: index.php?page=posts');
    exit();
}

// Get post for editing
$post = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $post = $stmt->fetch();
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Posts</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo isset($post) ? 'Edit Post' : 'Add New Post'; ?></h3>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <?php if (isset($post)): ?>
                                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                <?php endif; ?>
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($post) ? htmlspecialchars($post['title']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="content">Content</label>
                                    <textarea class="form-control" id="content" name="content" rows="5" required><?php echo isset($post) ? htmlspecialchars($post['content']) : ''; ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><?php echo isset($post) ? 'Update' : 'Create'; ?> Post</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Posts</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
                                    while ($row = $stmt->fetch()) {
                                        echo '<tr>
                                            <td>' . htmlspecialchars($row['title']) . '</td>
                                            <td>' . date('M d, Y H:i', strtotime($row['created_at'])) . '</td>
                                            <td>
                                                <a href="index.php?page=posts&action=edit&id=' . $row['id'] . '" class="btn btn-sm btn-info">Edit</a>
                                                <a href="index.php?page=posts&action=delete&id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>
                                            </td>
                                        </tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 