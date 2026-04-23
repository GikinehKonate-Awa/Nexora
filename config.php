<?php
/**
 * NEXORA CONSULTING GROUP
 * Archivo de Configuración Principal
 */

// Configuración Base de Datos
define('DB_HOST', 'localhost:3306');
define('DB_NAME', 'nexora_consulting');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_CHARSET', 'utf8mb4');

// Configuración Aplicación
define('APP_NAME', 'NEXORA CONSULTING GROUP');
define('APP_URL', 'http://localhost/nexora_app_fichaje/Nexora');
define('APP_VERSION', '1.0.0');

// Configuración VPN
define('VPN_NETWORK', '192.168.');
define('VPN_DETECTION_ENABLED', true);

// Configuración Horarios
define('HOUR_ENTRY_START', '07:00');
define('HOUR_ENTRY_END', '10:00');
define('HOUR_EXIT_START', '16:00');
define('HOUR_EXIT_END', '22:00');

// Configuración Sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Zona Horaria
date_default_timezone_set('Europe/Madrid');

// Función obtener IP Usuario
function getUserIP() {
    return $_SERVER['REMOTE_ADDR'];
}

// Función verificar VPN
function isVPNConnected() {
    if(!VPN_DETECTION_ENABLED) return true;
    
    $ip = getUserIP();
    return str_starts_with($ip, VPN_NETWORK);
}

// Función limpiar entrada
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función redireccionar
function redirect($url) {
    header("Location: $url");
    exit;
}
?>