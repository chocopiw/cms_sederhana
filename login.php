<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CMS Sederhana</title>
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
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 400px;
            max-width: 90%;
            animation: fadeIn 0.5s ease-out;
            position: relative;
            overflow: hidden;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-icon {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }
        .login-title {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .login-subtitle {
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
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
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
            color: #007bff;
        }
        .btn-login {
            background: #007bff;
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
        .btn-login:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .btn-login::after {
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
        .btn-login:focus:not(:active)::after {
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
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="particles" id="particles-js"></div>
    
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-user-circle login-icon"></i>
            <h1 class="login-title">Welcome Back!</h1>
            <p class="login-subtitle">Silakan login untuk melanjutkan</p>
        </div>
        
        <form id="loginForm" method="POST" action="">
            <div class="form-group">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <i class="fas fa-user form-icon"></i>
                <div class="error-message" id="username-error"></div>
            </div>
            
            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock form-icon"></i>
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                <div class="error-message" id="password-error"></div>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
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
        const form = document.getElementById('loginForm');
        const username = document.getElementById('username');
        const password = document.getElementById('password');
        const usernameError = document.getElementById('username-error');
        const passwordError = document.getElementById('password-error');

        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Username validation
            if (username.value.trim() === '') {
                usernameError.textContent = 'Username tidak boleh kosong';
                usernameError.style.display = 'block';
                username.classList.add('is-invalid');
                isValid = false;
            } else {
                usernameError.style.display = 'none';
                username.classList.remove('is-invalid');
            }
            
            // Password validation
            if (password.value.trim() === '') {
                passwordError.textContent = 'Password tidak boleh kosong';
                passwordError.style.display = 'block';
                password.classList.add('is-invalid');
                isValid = false;
            } else {
                passwordError.style.display = 'none';
                password.classList.remove('is-invalid');
            }
            
            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Mohon lengkapi semua field yang diperlukan',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#007bff'
                });
            }
        });

        // Password toggle
        const togglePassword = document.getElementById('togglePassword');
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
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
                text: 'Silakan login untuk melanjutkan',
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#007bff',
                timer: 2000,
                timerProgressBar: true
            });
        });
    </script>
</body>
</html> 