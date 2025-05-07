<?php
// Handle user actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'delete':
            if (isset($_GET['id'])) {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                header('Location: index.php?page=users');
                exit();
            }
            break;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    if (isset($_POST['id'])) {
        // Update existing user
        if (!empty($_POST['password'])) {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $password, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$username, $_POST['id']]);
        }
    } else {
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
    }
    header('Location: index.php?page=users');
    exit();
}

// Get user for editing
$user = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch();
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
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
                            <h3 class="card-title"><?php echo isset($user) ? 'Edit User' : 'Add New User'; ?></h3>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <?php if (isset($user)): ?>
                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                <?php endif; ?>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($user) ? htmlspecialchars($user['username']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password<?php echo isset($user) ? ' (leave blank to keep current)' : ''; ?></label>
                                    <input type="password" class="form-control" id="password" name="password" <?php echo isset($user) ? '' : 'required'; ?>>
                                </div>
                                <button type="submit" class="btn btn-primary"><?php echo isset($user) ? 'Update' : 'Create'; ?> User</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Users</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM users ORDER BY username");
                                    while ($row = $stmt->fetch()) {
                                        echo '<tr>
                                            <td>' . htmlspecialchars($row['username']) . '</td>
                                            <td>
                                                <a href="index.php?page=users&action=edit&id=' . $row['id'] . '" class="btn btn-sm btn-info">Edit</a>
                                                <a href="index.php?page=users&action=delete&id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>
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