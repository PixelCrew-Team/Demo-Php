<?php
// recover.php - Recuperar contraseña
require_once 'auth_config.php';
require_once 'auth_functions.php';

// Si ya está logueado, redirigir
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = '';
$step = 1; // Paso 1: mostrar pregunta, Paso 2: restablecer contraseña
$userEmail = '';
$userQuestion = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'get_question') {
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            $message = 'Por favor ingresa tu email';
            $messageType = 'error';
        } else {
            $users = json_decode(file_get_contents($usersFile), true);
            
            if (isset($users[$email])) {
                $userEmail = $email;
                $userQuestion = $users[$email]['security_question'];
                $step = 2;
            } else {
                $message = 'Email no encontrado';
                $messageType = 'error';
            }
        }
    }
    
    elseif ($action === 'reset_password') {
        $email = $_POST['email'] ?? '';
        $securityAnswer = $_POST['security_answer'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if ($newPassword !== $confirmPassword) {
            $message = 'Las contraseñas no coinciden';
            $messageType = 'error';
            $userEmail = $email;
            $users = json_decode(file_get_contents($usersFile), true);
            $userQuestion = $users[$email]['security_question'] ?? '';
            $step = 2;
        } else {
            $result = recoverPassword($usersFile, $email, $securityAnswer, $newPassword);
            
            if ($result['success']) {
                $message = $result['message'] . ' Ya puedes iniciar sesión.';
                $messageType = 'success';
                $step = 1;
            } else {
                $message = $result['error'];
                $messageType = 'error';
                $userEmail = $email;
                $users = json_decode(file_get_contents($usersFile), true);
                $userQuestion = $users[$email]['security_question'] ?? '';
                $step = 2;
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
    <title>Recuperar Contraseña - Demo PixelCrew</title>
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
        
        .recover-container {
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
        input[type="text"] {
            width: 100%;
            background: #000;
            border: 1px solid #333;
            border-radius: 6px;
            padding: 12px;
            color: #fff;
            font-size: 14px;
            font-family: 'Outfit', sans-serif;
        }
        
        input:focus {
            outline: none;
            border-color: #0066cc;
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
        
        .back-login {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }
        
        .link {
            color: #0066cc;
            text-decoration: none;
            font-size: 13px;
        }
        
        .link:hover {
            text-decoration: underline;
        }
        
        .info-box {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 16px;
            font-size: 13px;
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="recover-container">
        <div class="logo">🔑</div>
        <h1 class="title">Recuperar Contraseña</h1>
        <p class="subtitle">Restablece tu contraseña de forma segura</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($step === 1): ?>
            <!-- Paso 1: Ingresar email -->
            <form method="POST" action="">
                <input type="hidden" name="action" value="get_question">
                
                <div class="form-group">
                    <label>Correo electrónico:</label>
                    <input type="email" name="email" placeholder="tu@email.com" required>
                </div>
                
                <button type="submit" class="btn">Continuar</button>
            </form>
        <?php else: ?>
            <!-- Paso 2: Responder pregunta y nueva contraseña -->
            <div class="info-box">
                <strong>Pregunta de seguridad:</strong><br>
                <?php echo htmlspecialchars($userQuestion); ?>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($userEmail); ?>">
                
                <div class="form-group">
                    <label>Respuesta:</label>
                    <input type="text" name="security_answer" placeholder="Tu respuesta" required>
                </div>
                
                <div class="form-group">
                    <label>Nueva contraseña:</label>
                    <input type="password" name="new_password" placeholder="Mínimo 6 caracteres" minlength="6" required>
                </div>
                
                <div class="form-group">
                    <label>Confirmar contraseña:</label>
                    <input type="password" name="confirm_password" placeholder="Repite tu contraseña" required>
                </div>
                
                <button type="submit" class="btn">Restablecer Contraseña</button>
            </form>
        <?php endif; ?>
        
        <div class="back-login">
            <a href="login.php" class="link">← Volver al login</a>
        </div>
    </div>
</body>
</html>