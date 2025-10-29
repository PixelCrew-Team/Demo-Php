// AVISO IMPORTANTE:
// --> Este solo es un ejemplo osea que tienes que modificar el código para añadirle más categorías y apis. entre estos solo existe el archivo dl.php los otros tienes que añadirlos tu si quieres te guías del código dl.php.

<?php
// apis.php - Página de APIs (protegida con login)
require_once 'auth_config.php';
require_once 'auth_functions.php';

// Proteger esta página
requireLogin();

$currentUser = getCurrentUser($usersFile);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="APIs Menu - Demo PixelCrew">
  <title>APIs — Demo PixelCrew</title>
  <link rel="icon" href="https://kirito.my/media/images/32162096_k.jpg" type="image/jpeg">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    :root {
      --primary-color: #6c5ce7;
      --primary-hover: #5649d1;
      --secondary-color: #a29bfe;
      --accent-color: #00cec9;
      --background-color: #f8f9fd;
      --card-background: #fff;
      --text-color: #2d3436;
      --text-muted: #636e72;
      --border-color: rgba(0,0,0,0.08);
      --highlight-color: rgba(108,92,231,0.1);
      --border-radius: 18px;
      --shadow: 0 10px 20px rgba(0,0,0,0.05);
      --hover-shadow: 0 15px 30px rgba(0,0,0,0.1);
      --transition: all 0.3s cubic-bezier(0.25,0.8,0.25,1);
    }
    .dark-mode {
      --primary-color: #B98EFF;
      --primary-hover: #8E54E9;
      --secondary-color: #4B6CB7;
      --accent-color: #8E54E9;
      --background-color: #1a1b2e;
      --card-background: #2a2e4d;
      --text-color: #eaeaff;
      --text-muted: #b2becd;
      --border-color: rgba(255,255,255,0.08);
      --highlight-color: rgba(185,142,255,0.2);
      --shadow: 0 10px 25px rgba(0,0,0,0.2);
      --hover-shadow: 0 15px 35px rgba(0,0,0,0.3);
    }
    body {
      font-family: 'Outfit', sans-serif;
      background: var(--background-color);
      color: var(--text-color);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      margin: 0;
      transition: var(--transition);
    }
    .header-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 30px 0 22px 0;
      width: 100%;
      max-width: 1100px;
      margin: 0 auto;
      gap: 10px;
    }
    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--primary-color);
      background: none;
      border: none;
      font-weight: 700;
      font-size: 1.09rem;
      padding: 10px 18px;
      border-radius: 10px;
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
    }
    .back-btn:hover {
      background: var(--highlight-color);
      color: var(--primary-hover);
    }
    .theme-toggle-btn {
      background: var(--card-background);
      border: 1px solid var(--border-color);
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary-color);
      font-size: 1.25rem;
      cursor: pointer;
      box-shadow: var(--shadow);
      transition: var(--transition);
    }
    .theme-toggle-btn:hover, .theme-toggle-btn:focus {
      background: var(--highlight-color);
      color: var(--primary-hover);
      outline: none;
    }
    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 40px 20px 30px 20px;
      flex: 1;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .apis-title {
      font-size: clamp(2.1rem,5vw,3rem);
      line-height: 1.05;
      margin: 0 0 32px 0;
      font-weight: 800;
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(3,1fr);
      gap: 22px;
      width: 100%;
      max-width: 980px;
    }
    .card {
      position: relative;
      background: var(--card-background);
      border: 1.5px solid var(--border-color);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 36px 28px 32px 28px;
      overflow: hidden;
      transition: var(--transition);
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      min-width: 0;
    }
    .card:hover {
      transform: translateY(-6px);
      box-shadow: var(--hover-shadow);
    }
    .card-title {
      font-size: 1.38rem;
      font-weight: 700;
      margin-bottom: 13px;
      color: var(--primary-color);
      letter-spacing: -1.4px;
    }
    .card-desc {
      color: var(--text-muted);
      font-size: 1.05rem;
      margin-bottom: 24px;
      font-weight: 500;
    }
    .card .btn {
      margin-top: auto;
      padding: 12px 22px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1.08rem;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: #fff;
      border: none;
      box-shadow: 0 8px 22px rgba(108,92,231,.18);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: var(--transition);
      text-decoration: none;
    }
    .card .btn:hover {
      transform: translateY(-2px);
      background: linear-gradient(135deg,var(--secondary-color),var(--primary-color));
      color: #fff;
    }
    .main-footer {
      margin-top: auto;
      padding: 30px 40px 22px 40px;
      background-color: var(--card-background);
      color: var(--text-muted);
      border-top: 1px solid var(--border-color);
      text-align: center;
    }
    .footer-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }
    .copyright a, .powered-by a {
      color: var(--primary-color);
      font-weight: 700;
      text-decoration: none;
      transition: color .2s;
    }
    .copyright a:hover, .powered-by a:hover {
      color: var(--primary-hover);
      text-decoration: underline;
    }
    .powered-by {
      display: inline;
    }
    @media (max-width: 900px) {
      .grid {
        grid-template-columns: 1fr;
        gap: 18px;
      }
      .container {
        padding: 28px 8px 18px 8px;
      }
      .header-bar {
        padding: 18px 0 12px 0;
        max-width: 98vw;
      }
      .apis-title {
        font-size: 1.7rem;
        margin-bottom: 22px;
      }
    }
    @media (max-width: 600px) {
      .card {
        padding: 18px 8px 14px 8px;
      }
      .apis-title {font-size: 1.26rem;}
    }
  </style>
</head>
<body>
  <div class="header-bar">
    <a href="index.php" class="back-btn">
      <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
    <button id="themeToggleBtn" class="theme-toggle-btn" title="Cambiar tema" aria-label="Cambiar tema">
      <i class="fa-solid fa-moon"></i>
    </button>
  </div>
  <div class="container">
    <h1 class="apis-title">APIs & Utilidades</h1>
    <div class="grid">
      <div class="card">
        <div class="card-title">Downloaders</div>
        <div class="card-desc">Apis de descargas</div>
        <a class="btn" href="dl.php"><i class="fa-solid fa-arrow-right"></i>Ver</a>
      </div>
      <div class="card">
        <div class="card-title">Search</div>
        <div class="card-desc">Apis de búsqueda</div>
        <a class="btn" href="se.php"><i class="fa-solid fa-arrow-right"></i>Ver</a>
      </div>
      <div class="card">
        <div class="card-title">Inteligencia Artificial</div>
        <div class="card-desc">Apis de IA</div>
        <a class="btn" href="it.php"><i class="fa-solid fa-arrow-right"></i>Ver</a>
      </div>
    </div>
  </div>
  <footer class="main-footer">
    <div class="footer-content">
      <div class="copyright">
        Copyright © 2025
        <a href="index.php">Demo</a>.
        All right reserved.
        Powered By <span class="powered-by"><a href="https://pixelcrew.kesung.com" target="_blank" rel="noopener noreferrer">PixelCrew</a></span>
      </div>
    </div>
  </footer>
  <script>
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