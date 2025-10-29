<?php
// index.php - Página principal (pública)
require_once 'auth_config.php';
require_once 'auth_functions.php';

$isLoggedIn = isLoggedIn();
$isAdminUser = isAdmin();
$currentUser = $isLoggedIn ? getCurrentUser($usersFile) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Landing Demo PixelCrew">
    <link rel="icon" href="https://kirito.my/media/images/32162096_k.jpg" type="https://kirito.my/media/images/32162096_k.jpg">
    <title>Demo PixelCrew</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="/styles.css">
    <style>
        /* Bash/cuadro debajo del banner */
        .bash-card {
            background: #222a36;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.13);
            padding: 2.2rem 2rem 1.7rem 2rem;
            margin-top: 2.2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .bash-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.2rem;
        }
        .bash-label {
            font-size: 1.65rem;
            font-weight: 700;
            color: #fff;
            margin-right: 1rem;
            margin-bottom: 0.3rem;
        }
        .bash-btn {
            font-size: 1.18rem;
            font-weight: 600;
            padding: 0.8rem 2.1rem;
            border-radius: 10px;
            border: none;
            margin-right: 0.8rem;
            margin-bottom: 0.7rem;
            background: #1a90ff;
            color: #fff;
            transition: background 0.18s;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .bash-btn:hover, .bash-btn:focus {
            background: #0066cc;
            color: #fff;
        }
        .bash-btn:active {
            background: #004c99;
        }
        @media(max-width: 700px){
            .bash-card {max-width: 98vw;}
            .bash-label {font-size:1.22rem;}
            .bash-btn {font-size:1rem;}
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loadingScreen">
        <div class="spinner-wrapper">
            <svg class="spinner-logo" width="80" height="80" viewBox="0 0 100 100">
                <circle class="spinner-path" cx="50" cy="50" r="40" fill="none" stroke-width="8" />
                <circle class="spinner-animation" cx="50" cy="50" r="40" fill="none" stroke-width="8" />
            </svg>
            <p>Loading<span class="loading-dots">...</span></p>
        </div>
    </div>

    <!-- Side Navigation -->
    <aside class="side-nav">
        <div class="side-nav-logo">
            <span id="sideNavName">Demo PixelCrew</span>
        </div>
        <nav class="side-nav-links">
            <a href="index.php" class="side-nav-link active">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="planes.php" class="side-nav-link">
                <i class="fas fa-tags"></i>
                <span>Planes</span>
            </a>
            <?php if ($isLoggedIn): ?>
                <a href="apis.php" class="side-nav-link">
                    <i class="fas fa-tv"></i>
                    <span>Apis</span>
                </a>
                <a href="perfil.php" class="side-nav-link">
                    <i class="fas fa-user"></i>
                    <span>Perfil</span>
                </a>
            <?php endif; ?>
            <a href="https://instagram.com/hng_gts" target="_blank" class="side-nav-link">
                <i class="fa-brands fa-instagram"></i>
                <span>Instagram</span>
            </a>
            <?php if ($isLoggedIn): ?>
                <a href="logout.php" class="side-nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            <?php else: ?>
                <a href="login.php" class="side-nav-link">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Iniciar Sesión</span>
                </a>
            <?php endif; ?>
        </nav>
        <div class="nav-collapse-btn" aria-label="Toggle navigation">
            <i class="fas fa-angle-left"></i>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-wrapper">
        <header class="main-header">
            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Botón de cambio de tema -->
            <div class="header-actions">
                <?php if ($isLoggedIn): ?>
                    <span style="color: var(--text-muted); font-size: 0.9rem;">
                        Bienvenido, <strong style="color: var(--primary-color);"><?php echo htmlspecialchars($currentUser['nombre']); ?></strong>
                    </span>
                    <?php if ($isAdminUser): ?>
                        <a href="admin.php" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; background: linear-gradient(135deg, #ff6b6b, #ee5a6f); color: #fff; text-decoration: none; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(255,107,107,0.3); transition: all 0.3s;">
                            <i class="fas fa-user-shield"></i> Panel Admin
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <button id="themeToggleBtn" class="theme-toggle-btn" title="Cambiar tema" aria-label="Cambiar tema">
                    <i class="fa-solid fa-moon"></i>
                </button>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="home" class="hero-section">
            <div class="hero-content">
                <div class="hero-heading">
                    <h1 class="gradient-text">Bienvenido a Demo PixelCrew</h1>
                </div>
                <p class="hero-description">Servicios de calidad, planes accesibles y canal de atención a tu disposición.</p>
            </div>
            <div class="hero-visual">
                <div class="banner-container">
                    <img id="dynamicImage" class="banner" src="https://kirito.my/media/images/32162096_k.jpg" alt="Banner Demo PixelCrew">
                </div>
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
        </section>

        <!-- CUADRO BASH -->
        <div class="bash-card">
            <div class="bash-row">
                <div class="bash-label">
                    <?php if ($isLoggedIn): ?>
                        Perfil
                    <?php else: ?>
                        ¡Únete ahora!
                    <?php endif; ?>
                </div>
                <?php if ($isLoggedIn): ?>
                    <a href="perfil.php" class="bash-btn">
                        <i class="fa fa-user"></i> Ver perfil
                    </a>
                <?php else: ?>
                    <a href="login.php" class="bash-btn">
                        <i class="fa fa-sign-in-alt"></i> Iniciar Sesión
                    </a>
                    <a href="login.php" class="bash-btn" style="background: #28a745;">
                        <i class="fa fa-user-plus"></i> Registrarse
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="footer-content">
                <div class="copyright">
                    Copyright © 2025
                    <a href="index.php" target="_blank" rel="noopener noreferrer">Demo</a>.
                    All right reserved.
                    Powered By <span class="powered-by"><a href="https://demo-pixelcrew.xo.je/channel.php" target="_blank" rel="noopener noreferrer">PixelCrew</a></span>
                </div>
            </div>
        </footer>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Quitar loading screen al cargar
        $(window).on('load', function() {
            $('#loadingScreen').fadeOut();
        });

        // Side nav collapse/expand
        document.querySelector('.nav-collapse-btn').onclick = function() {
            document.querySelector('.side-nav').classList.toggle('collapsed');
            document.querySelector('.main-wrapper').classList.toggle('nav-collapsed');
        };
        // Responsive menu toggle
        document.querySelector('.menu-toggle').onclick = function() {
            document.querySelector('.side-nav').classList.toggle('active');
        };

        // Tema claro/oscuro con icono luna/sol
        function setTheme(mode) {
            if (mode === 'dark') {
                document.body.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark');
                themeBtnIcon.classList.remove('fa-moon');
                themeBtnIcon.classList.add('fa-sun');
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('theme', 'light');
                themeBtnIcon.classList.remove('fa-sun');
                themeBtnIcon.classList.add('fa-moon');
            }
        }
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeBtnIcon = themeToggleBtn.querySelector('i');
        let preferredTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        setTheme(preferredTheme);
        themeToggleBtn.onclick = function() {
            setTheme(document.body.classList.contains('dark-mode') ? 'light' : 'dark');
        };
    </script>
</body>
</html>