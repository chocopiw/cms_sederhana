<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validation
    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username minimal 3 karakter";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username hanya boleh berisi huruf, angka, dan underscore";
    }
    
    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }
    
    // Check if username already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = "Username sudah digunakan";
        }
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email sudah digunakan";
        }
    }
    
    // If no errors, create user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);
            
            $success = "Registrasi berhasil! Silakan login.";
        } catch (PDOException $e) {
            $errors[] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CMS Sederhana</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 450px;
            max-width: 90%;
            animation: fadeIn 0.5s ease-out;
            position: relative;
            overflow: hidden;
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-icon {
            font-size: 3rem;
            color: #28a745;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }
        .register-title {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .register-subtitle {
            color: #666;
            font-size: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .form-control {
            border-radius: 8px;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .form-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            transition: all 0.3s ease;
        }
        .form-control:focus + .form-icon {
            color: #28a745;
        }
        .btn-register {
            background: #28a745;
            color: white;
            padding: 0.8rem;
            border-radius: 8px;
            border: none;
            width: 100%;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-register:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-register:active {
            transform: translateY(0);
        }
        .btn-register::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }
        .btn-register:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: none;
        }
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }
        .login-link a {
            color: #28a745;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            transition: all 0.3s ease;
        }
        .password-toggle:hover {
            color: #28a745;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="particles" id="particles-js"></div>
    
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-plus register-icon"></i>
            <h1 class="register-title">Buat Akun Baru</h1>
            <p class="register-subtitle">Daftar untuk memulai perjalanan Anda</p>
        </div>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <form id="registerForm" method="POST" action="">
            <div class="form-group">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                <i class="fas fa-user form-icon"></i>
                <div class="error-message" id="username-error"></div>
            </div>
            
            <div class="form-group">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                <i class="fas fa-envelope form-icon"></i>
                <div class="error-message" id="email-error"></div>
            </div>
            
            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock form-icon"></i>
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                <div class="error-message" id="password-error"></div>
            </div>
            
            <div class="form-group">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                <i class="fas fa-lock form-icon"></i>
                <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                <div class="error-message" id="confirm-password-error"></div>
            </div>
            
            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> Daftar
            </button>
        </form>
        
        <div class="login-link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>

    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Initialize particles
        particlesJS('particles-js', {
            particles: {
                number: {
                    value: 80,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: '#ffffff'
                },
                shape: {
                    type: 'circle'
                },
                opacity: {
                    value: 0.5,
                    random: false
                },
                size: {
                    value: 3,
                    random: true
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#ffffff',
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
                    onhover: {
                        enable: true,
                        mode: 'repulse'
                    },
                    onclick: {
                        enable: true,
                        mode: 'push'
                    },
                    resize: true
                }
            },
            retina_detect: true
        });

        // Form validation
        const form = document.getElementById('registerForm');
        const username = document.getElementById('username');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const usernameError = document.getElementById('username-error');
        const emailError = document.getElementById('email-error');
        const passwordError = document.getElementById('password-error');
        const confirmPasswordError = document.getElementById('confirm-password-error');

        function validateUsername() {
            const value = username.value.trim();
            if (value === '') {
                usernameError.textContent = 'Username tidak boleh kosong';
                usernameError.style.display = 'block';
                username.classList.add('is-invalid');
                return false;
            } else if (value.length < 3) {
                usernameError.textContent = 'Username minimal 3 karakter';
                usernameError.style.display = 'block';
                username.classList.add('is-invalid');
                return false;
            } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
                usernameError.textContent = 'Username hanya boleh berisi huruf, angka, dan underscore';
                usernameError.style.display = 'block';
                username.classList.add('is-invalid');
                return false;
            } else {
                usernameError.style.display = 'none';
                username.classList.remove('is-invalid');
                return true;
            }
        }

        function validateEmail() {
            const value = email.value.trim();
            if (value === '') {
                emailError.textContent = 'Email tidak boleh kosong';
                emailError.style.display = 'block';
                email.classList.add('is-invalid');
                return false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                emailError.textContent = 'Format email tidak valid';
                emailError.style.display = 'block';
                email.classList.add('is-invalid');
                return false;
            } else {
                emailError.style.display = 'none';
                email.classList.remove('is-invalid');
                return true;
            }
        }

        function validatePassword() {
            const value = password.value;
            if (value === '') {
                passwordError.textContent = 'Password tidak boleh kosong';
                passwordError.style.display = 'block';
                password.classList.add('is-invalid');
                return false;
            } else if (value.length < 6) {
                passwordError.textContent = 'Password minimal 6 karakter';
                passwordError.style.display = 'block';
                password.classList.add('is-invalid');
                return false;
            } else {
                passwordError.style.display = 'none';
                password.classList.remove('is-invalid');
                return true;
            }
        }

        function validateConfirmPassword() {
            const value = confirmPassword.value;
            if (value === '') {
                confirmPasswordError.textContent = 'Konfirmasi password tidak boleh kosong';
                confirmPasswordError.style.display = 'block';
                confirmPassword.classList.add('is-invalid');
                return false;
            } else if (value !== password.value) {
                confirmPasswordError.textContent = 'Konfirmasi password tidak cocok';
                confirmPasswordError.style.display = 'block';
                confirmPassword.classList.add('is-invalid');
                return false;
            } else {
                confirmPasswordError.style.display = 'none';
                confirmPassword.classList.remove('is-invalid');
                return true;
            }
        }

        form.addEventListener('submit', function(e) {
            const isUsernameValid = validateUsername();
            const isEmailValid = validateEmail();
            const isPasswordValid = validatePassword();
            const isConfirmPasswordValid = validateConfirmPassword();
            
            if (!isUsernameValid || !isEmailValid || !isPasswordValid || !isConfirmPasswordValid) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Mohon lengkapi semua field dengan benar',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
            }
        });

        // Real-time validation
        username.addEventListener('blur', validateUsername);
        email.addEventListener('blur', validateEmail);
        password.addEventListener('blur', validatePassword);
        confirmPassword.addEventListener('blur', validateConfirmPassword);

        // Password toggle
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Input focus effects
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Show welcome message
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Selamat Datang!',
                text: 'Silakan daftar untuk membuat akun baru',
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745',
                timer: 2000,
                timerProgressBar: true
            });
        });
    </script>
</body>
</html> 