// auth_config.php

<?php
// auth_config.php - Configuración del sistema de autenticación

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Archivo donde se guardan los usuarios
$usersFile = __DIR__ . '/data/users.json';

// Crear directorio data si no existe
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// Crear archivo de usuarios si no existe con los admins por defecto
if (!file_exists($usersFile)) {
    $defaultUsers = [
        'admin@gmail.com' => [
            'email' => 'admin@gmail.com',
            'password' => password_hash('MANTISMDd1', PASSWORD_DEFAULT),
            'security_question' => '¿Cuál es tu comida favorita?',
            'security_answer' => password_hash('admin', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'nombre' => 'Super Admin',
            'key' => 'admin-master-key',
            'saldo' => 99999,
            'id' => 'admin-master',
            'avatar' => '',
            'role' => 'admin',
            'is_super_admin' => true
        ],
        'abrahanmoises987@gmail.com' => [
            'email' => 'abrahanmoises987@gmail.com',
            'password' => password_hash('92127026', PASSWORD_DEFAULT),
            'security_question' => '¿Cuál es tu comida favorita?',
            'security_answer' => password_hash('admin', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'nombre' => 'Admin',
            'key' => 'admin-key-2',
            'saldo' => 99999,
            'id' => 'admin-2',
            'avatar' => '',
            'role' => 'admin',
            'is_super_admin' => false
        ]
    ];
    file_put_contents($usersFile, json_encode($defaultUsers, JSON_PRETTY_PRINT));
}

// Preguntas de seguridad disponibles
$securityQuestions = [
    '¿Cuál es el nombre de tu primera mascota?',
    '¿En qué ciudad naciste?',
    '¿Cuál es tu comida favorita?',
    '¿Cuál es el nombre de tu mejor amigo de la infancia?',
    '¿Cuál es tu película favorita?',
    '¿Cuál es el nombre de tu escuela primaria?'
];

// Configuración de páginas
$publicPages = ['index.php', 'planes.php', 'login.php', 'recover.php'];
$protectedPages = ['apis.php', 'dl.php', 'perfil.php'];
$adminPages = ['admin.php', 'status.php'];
?>