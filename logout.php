<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - CMS Sederhana</title>
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
        }
        .logout-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 90%;
            animation: fadeIn 0.5s ease-out;
        }
        .logout-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
            animation: bounce 1s infinite;
        }
        .logout-title {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        .logout-message {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-login {
            background: #007bff;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 0.5rem;
        }
        .btn-login:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-home {
            background: #28a745;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 0.5rem;
        }
        .btn-home:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="particles" id="particles-js"></div>
    
    <div class="logout-container">
        <i class="fas fa-sign-out-alt logout-icon"></i>
        <h1 class="logout-title">Anda Telah Logout</h1>
        <p class="logout-message">
            Terima kasih telah menggunakan CMS Sederhana.<br>
            Anda telah berhasil keluar dari sistem.
        </p>
        <div class="button-group">
            <a href="login.php" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login Kembali
            </a>
            <a href="index.php" class="btn-home">
                <i class="fas fa-home"></i> Kembali ke Home
            </a>
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

        // Show logout message
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Logout Berhasil!',
                text: 'Anda telah berhasil keluar dari sistem.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#007bff',
                timer: 2000,
                timerProgressBar: true
            });
        });
    </script>
</body>
</html> 