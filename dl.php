<?php
// dl.php - Downloaders (protegida con login)
require_once 'auth_config.php';
require_once 'auth_functions.php';

// Proteger esta página
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Downloader API - Demo PixelCrew">
  <title>Downloaders — Demo PixelCrew</title>
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
      max-width: 700px;
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
      max-width: 620px;
      margin: 0 auto;
      padding: 32px 10px 30px 10px;
      flex: 1;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .apis-title {
      font-size: clamp(1.4rem,4vw,2.1rem);
      font-weight: 800;
      margin-bottom: 28px;
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .single-card {
      background: var(--card-background);
      border: 1.5px solid var(--border-color);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 32px 18px 26px 18px;
      width: 100%;
      max-width: 520px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 12px;
      margin: 0 auto 34px auto;
      transition: var(--transition);
    }
    .single-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--hover-shadow);
    }
    .card-title {
      font-size: 1.22rem;
      font-weight: 700;
      margin-bottom: 7px;
      color: var(--primary-color);
      letter-spacing: -1px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .card-desc {
      color: var(--text-muted);
      font-size: 1.05rem;
      margin-bottom: 18px;
      font-weight: 500;
    }
    .endpoint-row {
      display: flex;
      align-items: center;
      width: 100%;
      gap: 10px;
      margin-bottom: 9px;
    }
    .endpoint {
      font-family: monospace;
      font-size: 13px;
      color: var(--text-muted);
      background: var(--highlight-color);
      border: 1px dashed var(--border-color);
      padding: 10px;
      border-radius: 12px;
      word-break: break-all;
      flex: 1;
      min-width: 0;
    }
    .copy-btn {
      background: none;
      border: none;
      color: var(--primary-color);
      font-size: 1.2rem;
      border-radius: 50%;
      cursor: pointer;
      padding: 7px 9px;
      transition: var(--transition);
    }
    .copy-btn:hover {
      background: var(--highlight-color);
      color: var(--primary-hover);
    }
    .try-row {
      display: flex;
      width: 100%;
      gap: 10px;
      margin-bottom: 10px;
    }
    .try-row input {
      flex: 1;
      padding: 12px 14px;
      border-radius: 12px;
      border: 1px solid var(--border-color);
      background: var(--card-background);
      color: var(--text-color);
      outline: none;
      font-size: 14px;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .try-row input:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 10px var(--highlight-color);
    }
    .btn-main {
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
    .btn-main:hover {
      transform: translateY(-2px);
      background: linear-gradient(135deg,var(--secondary-color),var(--primary-color));
      color: #fff;
    }
    .result-block {
      font-family: monospace;
      background: var(--highlight-color);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      padding: 12px 10px;
      font-size: 13px;
      color: var(--text-color);
      width: 100%;
      min-height: 36px;
      margin-top: 10px;
      white-space: pre-wrap;
      word-break: break-all;
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
    @media (max-width: 600px) {
      .container {
        padding: 16px 2vw 18px 2vw;
      }
      .single-card {
        padding: 14px 5px 12px 5px;
        max-width: 100%;
      }
      .header-bar {
        padding: 12px 0 10px 0;
      }
      .apis-title {font-size: 1.18rem;}
    }
  </style>
</head>
<body>
  <div class="header-bar">
    <a href="apis.php" class="back-btn">
      <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
    <button id="themeToggleBtn" class="theme-toggle-btn" title="Cambiar tema" aria-label="Cambiar tema">
      <i class="fa-solid fa-moon"></i>
    </button>
  </div>
  <div class="container">
    <h1 class="apis-title">Endpoint Downloader - Demo PixelCrew</h1>
    <div class="single-card">
      <div class="card-title"><i class="fa-solid fa-bolt"></i> YouTube MP3</div>
      <div class="card-desc">Descarga audios de videos de YouTube con título, metadata y enlace directo para descargar en la calidad elegida.</div>
      <div class="endpoint-row">
        <div id="endpoint" class="endpoint">/api/download/youtube/mp3?url=</div>
        <button class="copy-btn" onclick="copyText()" title="Copiar endpoint"><i class="fa-regular fa-copy"></i></button>
      </div>
      <div class="try-row">
        <input id="urlInput" placeholder="https://youtu.be/VIDEO_ID" />
        <button class="btn-main" onclick="testAPI()"><i class="fa-solid fa-paper-plane"></i> Probar</button>
      </div>
      <div id="apiUrlShow" style="display:none; font-size:12px; margin:6px 0 0 0; color:var(--primary-hover)"></div>
      <div id="result" class="result-block">El resultado aparecerá aquí...</div>
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

    function copyText() {
      const el = document.getElementById('endpoint');
      navigator.clipboard.writeText(el.textContent.trim());
      const button = el.nextElementSibling;
      const originalIcon = button.innerHTML;
      button.innerHTML = '<i class="fa-solid fa-check"></i>';
      setTimeout(() => { button.innerHTML = originalIcon; }, 1500);
    }

    async function testAPI() {
      const base = window.location.origin;
      const val = document.getElementById('urlInput').value.trim();
      const out = document.getElementById('result');
      const endpoint = "/api/download/youtube/mp3?url=";
      out.textContent = "Consultando...";
      out.style.color = "var(--text-color)";
      document.getElementById('apiUrlShow').style.display = 'none';

      if (!val) {
        out.textContent = "Por favor, ingresa una URL para probar.";
        out.style.color = "#f96b6b";
        return;
      }

      try {
        const fullUrl = base + endpoint + encodeURIComponent(val);
        document.getElementById('apiUrlShow').style.display = 'block';
        document.getElementById('apiUrlShow').textContent = fullUrl;
        const res = await fetch(fullUrl);
        if (!res.ok) throw new Error(`Error HTTP: ${res.status} ${res.statusText}`);
        const contentType = res.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
          const data = await res.json();
          out.textContent = JSON.stringify(data, null, 2);
        } else {
          const text = await res.text();
          out.textContent = text || "La respuesta no es JSON y no tiene contenido visible.";
        }
        out.style.color = "var(--text-color)";
      } catch (e) {
        out.textContent = "Error: " + e.message;
        out.style.color = "#f96b6b";
      }
    }
  </script>
</body>
</html>