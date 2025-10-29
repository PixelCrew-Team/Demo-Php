<?php
// login.php - Página de login y registro
require_once 'auth_config.php';
require_once 'auth_functions.php';

// Si ya está logueado, redirigir
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Procesar formularios
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $result = loginUser($usersFile, $email, $password);
        
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $message = $result['error'];
            $messageType = 'error';
        }
    }
    
    elseif ($action === 'register') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $securityQuestion = $_POST['security_question'] ?? '';
        $securityAnswer = $_POST['security_answer'] ?? '';
        
        if ($password !== $confirmPassword) {
            $message = 'Las contraseñas no coinciden';
            $messageType = 'error';
        } else {
            $result = registerUser($usersFile, $email, $password, $securityQuestion, $securityAnswer);
            
            if ($result['success']) {
                $message = $result['message'] . ' Ahora puedes iniciar sesión.';
                $messageType = 'success';
            } else {
                $message = $result['error'];
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Demo PixelCrew</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: #000;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            max-width: 400px;
            width: 100%;
            background: #111;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 30px;
        }
        
        .logo {
            text-align: center;
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .title {
            text-align: center;
            font-size: 24px;
            color: #fff;
            margin-bottom: 8px;
        }
        
        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #888;
            margin-bottom: 30px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }
        
        .tab-btn {
            flex: 1;
            background: #1a1a1a;
            color: #888;
            border: 1px solid #333;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 600;
        }
        
        .tab-btn.active {
            background: #0066cc;
            color: #fff;
            border-color: #0066cc;
        }
        
        .form-section {
            display: none;
        }
        
        .form-section.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        label {
            display: block;
            font-size: 13px;
            color: #ccc;
            margin-bottom: 6px;
        }
        
        input[type="email"],
        input[type="password"],
        input[type="text"],
        select {
            width: 100%;
            background: #000;
            border: 1px solid #333;
            border-radius: 6px;
            padding: 12px;
            color: #fff;
            font-size: 14px;
            font-family: 'Outfit', sans-serif;
        }
        
        input:focus,
        select:focus {
            outline: none;
            border-color: #0066cc;
        }
        
        select {
            cursor: pointer;
        }
        
        .btn {
            width: 100%;
            background: #0066cc;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
            font-family: 'Outfit', sans-serif;
        }
        
        .btn:hover {
            background: #0052a3;
        }
        
        .message {
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        
        .message.error {
            background: #2d1b1b;
            border: 1px solid #5c2626;
            color: #ff6b6b;
        }
        
        .message.success {
            background: #1b2d1b;
            border: 1px solid #265c26;
            color: #51cf66;
        }
        
        .link {
            color: #0066cc;
            text-decoration: none;
            font-size: 13px;
            cursor: pointer;
        }
        
        .link:hover {
            text-decoration: underline;
        }
        
        .text-center {
            text-align: center;
            margin-top: 16px;
        }
        
        .back-home {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }
        
        .info-text {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🔐</div>
        <h1 class="title">Demo PixelCrew</h1>
        <p class="subtitle">Inicia sesión para gestionar tus APIs</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <button class="tab-btn active" onclick="showTab('login')">Iniciar Sesión</button>
            <button class="tab-btn" onclick="showTab('register')">Crear Cuenta</button>
        </div>
        
        <!-- Formulario de Login -->
        <div id="login-form" class="form-section active">
            <form method="POST" action="">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label>Correo electrónico:</label>
                    <input type="email" name="email" placeholder="tu@email.com" required>
                </div>
                
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn">Iniciar Sesión</button>
                
                <div class="text-center">
                    <a href="recover.php" class="link">¿Olvidaste tu contraseña?</a>
                </div>
            </form>
        </div>
        
        <!-- Formulario de Registro -->
        <div id="register-form" class="form-section">
            <form method="POST" action="">
                <input type="hidden" name="action" value="register">
                
                <div class="form-group">
                    <label>Correo electrónico:</label>
                    <input type="email" name="email" placeholder="tu@email.com" required>
                </div>
                
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" placeholder="Mínimo 6 caracteres" minlength="6" required>
                </div>
                
                <div class="form-group">
                    <label>Confirmar contraseña:</label>
                    <input type="password" name="confirm_password" placeholder="Repite tu contraseña" required>
                </div>
                
                <div class="form-group">
                    <label>Pregunta de seguridad:</label>
                    <select name="security_question" required>
                        <option value="">Selecciona una pregunta</option>
                        <?php foreach ($securityQuestions as $question): ?>
                            <option value="<?php echo htmlspecialchars($question); ?>">
                                <?php echo htmlspecialchars($question); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="info-text">Para recuperar tu contraseña si la olvidas</p>
                </div>
                
                <div class="form-group">
                    <label>Respuesta de seguridad:</label>
                    <input type="text" name="security_answer" placeholder="Tu respuesta" required>
                </div>
                
                <button type="submit" class="btn">Crear Cuenta</button>
            </form>
        </div>
        
        <div class="back-home">
            <a href="index.php" class="link">← Volver al inicio</a>
        </div>
    </div>
    <script>
        function showTab(tab) {
            // Ocultar todos los formularios
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Mostrar el formulario seleccionado
            document.getElementById(tab + '-form').classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>