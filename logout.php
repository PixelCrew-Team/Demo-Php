<?php
// logout.php - Cerrar sesión
require_once 'auth_config.php';
require_once 'auth_functions.php';

logoutUser();

header('Location: login.php');
exit;
?>