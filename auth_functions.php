<?php
// auth_functions.php - Funciones del sistema de autenticación

require_once 'auth_config.php';

// Verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// Verificar si el usuario es admin
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Verificar si es super admin
function isSuperAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    return isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] === true;
}

// Obtener email del usuario actual
function getCurrentUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

// Obtener datos del usuario actual
function getCurrentUser($usersFile) {
    if (!isLoggedIn()) {
        return null;
    }
    
    $email = getCurrentUserEmail();
    $users = json_decode(file_get_contents($usersFile), true);
    
    return $users[$email] ?? null;
}

// Registrar nuevo usuario
function registerUser($usersFile, $email, $password, $securityQuestion, $securityAnswer) {
    // Validaciones
    if (empty($email) || empty($password)) {
        return ['success' => false, 'error' => 'Email y contraseña son obligatorios'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Email inválido'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres'];
    }
    
    if (empty($securityQuestion) || empty($securityAnswer)) {
        return ['success' => false, 'error' => 'Pregunta y respuesta de seguridad son obligatorias'];
    }
    
    // Cargar usuarios existentes
    $users = json_decode(file_get_contents($usersFile), true);
    
    // Verificar si el usuario ya existe
    if (isset($users[$email])) {
        return ['success' => false, 'error' => 'Este email ya está registrado'];
    }
    
    // Crear nuevo usuario
    $users[$email] = [
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'security_question' => $securityQuestion,
        'security_answer' => password_hash(strtolower(trim($securityAnswer)), PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s'),
        'nombre' => 'Usuario',
        'key' => 'demo-api',
        'saldo' => 0,
        'id' => generateUserId(),
        'avatar' => '',
        'role' => 'user' // Por defecto todos son usuarios normales
    ];
    
    // Guardar
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    
    return ['success' => true, 'message' => 'Cuenta creada exitosamente'];
}

// Login de usuario
function loginUser($usersFile, $email, $password) {
    // Validaciones
    if (empty($email) || empty($password)) {
        return ['success' => false, 'error' => 'Email y contraseña son obligatorios'];
    }
    
    // Cargar usuarios
    $users = json_decode(file_get_contents($usersFile), true);
    
    // Verificar si el usuario existe
    if (!isset($users[$email])) {
        return ['success' => false, 'error' => 'Email o contraseña incorrectos'];
    }
    
    $user = $users[$email];
    
    // Verificar contraseña
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'error' => 'Email o contraseña incorrectos'];
    }
    
    // Iniciar sesión
    $_SESSION['user_logged_in'] = true;
    $_SESSION['user_email'] = $email;
    $_SESSION['login_time'] = time();
    $_SESSION['user_role'] = $user['role'] ?? 'user';
    $_SESSION['is_super_admin'] = $user['is_super_admin'] ?? false;
    
    return ['success' => true];
}

// Cerrar sesión
function logoutUser() {
    $_SESSION = [];
    session_destroy();
}

// Recuperar contraseña
function recoverPassword($usersFile, $email, $securityAnswer, $newPassword) {
    // Validaciones
    if (empty($email) || empty($securityAnswer) || empty($newPassword)) {
        return ['success' => false, 'error' => 'Todos los campos son obligatorios'];
    }
    
    if (strlen($newPassword) < 6) {
        return ['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres'];
    }
    
    // Cargar usuarios
    $users = json_decode(file_get_contents($usersFile), true);
    
    // Verificar si el usuario existe
    if (!isset($users[$email])) {
        return ['success' => false, 'error' => 'Email no encontrado'];
    }
    
    $user = $users[$email];
    
    // Verificar respuesta de seguridad
    if (!password_verify(strtolower(trim($securityAnswer)), $user['security_answer'])) {
        return ['success' => false, 'error' => 'Respuesta de seguridad incorrecta'];
    }
    
    // Actualizar contraseña
    $users[$email]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    
    return ['success' => true, 'message' => 'Contraseña actualizada exitosamente'];
}

// Generar ID único para usuario
function generateUserId() {
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $sufijo = "";
    for ($i = 0; $i < 8; $i++) {
        $sufijo .= $chars[rand(0, strlen($chars) - 1)];
    }
    return "demo-" . $sufijo;
}

// Proteger página (redirige al login si no está autenticado)
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Proteger página de admin (redirige si no es admin)
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        die('Acceso denegado. Solo para administradores.');
    }
}

// Actualizar perfil de usuario
function updateUserProfile($usersFile, $email, $data) {
    $users = json_decode(file_get_contents($usersFile), true);
    
    if (!isset($users[$email])) {
        return ['success' => false, 'error' => 'Usuario no encontrado'];
    }
    
    // Actualizar datos permitidos
    if (isset($data['nombre'])) {
        $users[$email]['nombre'] = trim($data['nombre']) ?: 'Usuario';
    }
    if (isset($data['key'])) {
        $users[$email]['key'] = trim($data['key']) ?: 'demo-api';
    }
    if (isset($data['avatar'])) {
        $users[$email]['avatar'] = $data['avatar'];
    }
    
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    
    return ['success' => true, 'message' => 'Perfil actualizado'];
}
?>