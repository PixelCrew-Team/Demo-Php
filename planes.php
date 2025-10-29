<?php
// planes.php - Página de planes (pública)
require_once 'auth_config.php';
require_once 'auth_functions.php';

$isLoggedIn = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Planes — Demo PixelCrew</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/styles.css">
  <style>
    body {
      background: var(--background-color);
      min-height: 100vh;
    }
    .menu-bar {
      display: flex;
      align-items: center;
      gap: 14px;
      background: var(--card-background);
      border-bottom: 1px solid var(--border-color);
      padding: 22px 24px 10px 24px;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .menu-bar a {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--primary-color);
      font-weight: 700;
      font-size: 1.1rem;
      text-decoration: none;
      background: none;
      border: none;
      padding: 7px 15px;
      border-radius: 8px;
      transition: var(--transition);
    }
    .menu-bar a:hover {
      background: var(--highlight-color);
      color: var(--primary-hover);
    }
    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 40px 20px 30px 20px;
    }
    .plans-title {
      font-size: clamp(2.1rem, 5vw, 3rem);
      line-height: 1.05;
      margin: 0 0 10px;
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .plans-subtitle {
      color: var(--text-muted);
      font-size: 1.05rem;
      margin-bottom: 24px;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px;
    }
    @media(max-width:900px){
      .grid{grid-template-columns:1fr}
    }
    .card {
      position: relative;
      background: var(--card-background);
      border: 1px solid var(--border-color);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 22px;
      overflow: hidden;
      transition: var(--transition);
    }
    .card:hover {
      transform: translateY(-6px);
      box-shadow: var(--hover-shadow);
    }
    .ribbon {
      position: absolute;
      top: 14px;
      right: -30px;
      transform: rotate(35deg);
      background: linear-gradient(135deg,var(--primary-color),var(--secondary-color));
      color: #fff;
      font-size: .78rem;
      padding: 6px 44px;
      z-index: 2;
      letter-spacing: .4px;
    }
    .icon {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      display: grid;
      place-items: center;
      background: linear-gradient(135deg,rgba(108,92,231,.18),rgba(0,206,201,.18));
      color: var(--primary-color);
      font-size: 1.35rem;
      margin-bottom: 14px;
    }
    .card h3 {
      margin: 6px 0 6px;
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-color);
    }
    .benefits {
      color: var(--text-muted);
      line-height: 1.65;
      font-size: .98rem;
      margin: 10px 0 14px;
    }
    .price {
      display: flex;
      align-items: baseline;
      gap: 8px;
      margin-top: 6px;
    }
    .amount {
      font-weight: 800;
      font-size: 1.6rem;
      color: var(--primary-color);
      transition: transform .25s ease, opacity .25s ease;
    }
    .amount.pulse {
      transform: scale(1.05);
      opacity: .85;
    }
    .period {
      color: var(--text-muted);
      font-size: .95rem;
    }
    .cta {
      margin-top: 16px;
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
    }
    .btn {
      position: relative;
      overflow: hidden;
      border: none;
      cursor: pointer;
      padding: 12px 18px;
      border-radius: 12px;
      font-weight: 700;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }
    .btn-primary {
      color: #fff;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      box-shadow: 0 8px 22px rgba(108,92,231,.35);
    }
    .btn-primary:hover {
      transform: translateY(-2px);
    }
    .btn-ghost {
      background: transparent;
      color: var(--primary-color);
      border: 1px solid var(--primary-color);
    }
    @media (max-width: 576px){
      .container{padding:24px 8px}
      .menu-bar{padding:15px 10px 8px 10px;}
      .grid{gap:16px;}
      .card{padding:14px;}
    }
  </style>
</head>
<body>
  <nav class="menu-bar">
    <a href="index.php"><i class="fas fa-arrow-left"></i> Volver</a>
    <span style="font-weight:800;color:var(--primary-color);font-size:1.1rem;letter-spacing:-1px;">Demo PixelCrew</span>
  </nav>
  <div class="container">
    <h1 class="plans-title">Planes</h1>
    <div class="plans-subtitle">Selecciona un plan server y realiza el pago con PayPal.</div>
    <section class="grid">
      <article class="card">
        <div class="icon"><i class="fa-solid fa-seedling"></i></div>
        <h3>Plan 1</h3>
        <p class="benefits">✅ 700 solicitudes diarias por 1 mes<br>🔁 Cambios de key: 0</p>
        <div class="price"><span class="amount" data-usd="1.11">$1.11</span><span class="period" data-code="USD">USD</span></div>
        <div class="cta"><button class="btn btn-primary buy" data-plan="mini"><i class="fa-solid fa-cart-shopping"></i>Comprar</button></div>
      </article>
      <article class="card">
        <div class="icon"><i class="fa-solid fa-sparkles"></i></div>
        <h3>Plan 2</h3>
        <p class="benefits">✅ 1.5k solicitudes diarias por 1 mes<br>🔁 Cambios de key: 2</p>
        <div class="price"><span class="amount" data-usd="1.90">$1.90</span><span class="period" data-code="USD">USD</span></div>
        <div class="cta"><button class="btn btn-primary buy" data-plan="starter"><i class="fa-solid fa-cart-shopping"></i>Comprar</button></div>
      </article>
      <article class="card">
        <div class="icon"><i class="fa-solid fa-layer-group"></i></div>
        <h3>Plan 3</h3>
        <p class="benefits">✅ 5k solicitudes diarias por 1 mes<br>🔁 Cambios de key: 5</p>
        <div class="price"><span class="amount" data-usd="3.49">$3.49</span><span class="period" data-code="USD">USD</span></div>
        <div class="cta"><button class="btn btn-primary buy" data-plan="plus"><i class="fa-solid fa-cart-shopping"></i>Comprar</button></div>
      </article>
      <article class="card">
        <div class="icon"><i class="fa-solid fa-rocket"></i></div>
        <h3>Plan 4</h3>
        <p class="benefits">✅ 1k solicitudes diarias por 1 mes</p>
        <div class="price"><span class="amount" data-usd="1">$1</span><span class="period" data-code="USD">USD</span></div>
        <div class="cta"><button class="btn btn-primary buy" data-plan="basic"><i class="fa-solid fa-cart-shopping"></i>Comprar</button></div>
      </article>
      <article class="card">
        <span class="ribbon">Popular</span>
        <div class="icon"><i class="fa-solid fa-key"></i></div>
        <h3>Plan 5</h3>
        <p class="benefits">✅ 2k solicitudes diarias por 1 mes<br>✨ Key personalizada<br>🔐 Key personalizada temporal</p>
        <div class="price"><span class="amount" data-usd="2">$2</span><span class="period" data-code="USD">USD</span></div>
        <div class="cta"><button class="btn btn-primary buy" data-plan="pro"><i class="fa-solid fa-cart-shopping"></i>Comprar</button></div>
      </article>
      <article class="card">
        <div class="icon"><i class="fa-solid fa-briefcase"></i></div>
        <h3>Plan 6</h3>
        <p class="benefits">✅ 25k solicitudes diarias por 1 mes<br>🔁 Cambios de key: 8</p>
        <div class="price"><span class="amount" data-usd="8.78">$8.78</span><span class="period" data-code="USD">USD</span></div>
        <div class="cta"><button class="btn btn-primary buy" data-plan="business"><i class="fa-solid fa-cart-shopping"></i>Comprar</button></div>
      </article>
      <article class="card">
        <div class="icon"><i class="fa-solid fa-gem"></i></div>
        <h3>Plan 7</h3>
        <p class="benefits">✅ 100k solicitudes diarias durante 3 meses<br>✨ Key personalizada permanente</p>
        <div class="price"><span class="amount" data-usd="5">$5</span><span class="period" data-code="USD">USD</span></div>
        <div class="cta"><button class="btn btn-primary buy" data-plan="ultra"><i class="fa-solid fa-cart-shopping"></i>Comprar</button></div>
      </article>
      <article class="card">
        <div class="icon"><i class="fa-solid fa-crown"></i></div>
        <h3>Plan 8</h3>
        <p class="benefits">✅ 200k solicitudes diarias por 6 meses<br>🔁 Cambios de key: 15</p>
        <div class="price"><span class="amount" data-usd="13.01">$13.01</span><span class="period" data-code="USD">USD</span></div>
        <div class="cta"><button class="btn btn-primary buy" data-plan="enterprise"><i class="fa-solid fa-cart-shopping"></i>Comprar</button></div>
      </article>
    </section>
  </div>
  <script>
    document.querySelectorAll('.buy').forEach(b=>b.onclick=async()=>{
      const plan=b.dataset.plan;
      alert('Redirigiendo a PayPal para '+plan.toUpperCase());
    });
  </script>
</body>
</html>