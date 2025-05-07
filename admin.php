<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$new_password]);
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Admin Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <style>
        body.login-page {
            background-color: #ffb6c1 !important; /* light pink */
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <b>Reset Admin Password</b>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">Password admin berhasil direset ke <b>admin123</b>.</div>
                <?php endif; ?>
                <form method="post">
                    <button type="submit" class="btn btn-danger btn-block">Reset Password Admin</button>
                </form>
                <a href="login.php" class="btn btn-link btn-block">Kembali ke Login</a>
            </div>
        </div>
    </div>
</body>
</html> 