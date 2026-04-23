<?php
/**
 * NEXORA CONSULTING GROUP
 * Sistema de Autenticación
 */

session_start();
require_once __DIR__ . '/db.php';

// Función login
function login($email, $password) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM empleados WHERE email = ? AND activo = 1");
    $stmt->execute([$email]);
    $empleado = $stmt->fetch();
    
    if(!$empleado) return false;
    
    if(password_verify($password, $empleado['password'])) {
        $_SESSION['user_id'] = $empleado['id'];
        $_SESSION['user_nombre'] = $empleado['nombre'];
        $_SESSION['user_apellidos'] = $empleado['apellidos'];
        $_SESSION['user_email'] = $empleado['email'];
        $_SESSION['user_rol'] = $empleado['rol'];
        $_SESSION['user_departamento'] = $empleado['departamento_id'];
        $_SESSION['user_modalidad'] = $empleado['modalidad'];
        $_SESSION['logged_in'] = true;
        
        // Actualizar último acceso
        $stmt = $db->prepare("UPDATE empleados SET ultimo_acceso = NOW() WHERE id = ?");
        $stmt->execute([$empleado['id']]);
        
        // Registrar log
        registrarLog('login', 'Inicio de sesión correcto');
        
        return true;
    }
    
    return false;
}

// Función verificar si está logueado
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Función requerir autenticación
function requireAuth() {
    if(!isLoggedIn()) {
        redirect('../auth/login.php');
    }
}

// Función requerir rol
function requireRole($roles) {
    if(!in_array($_SESSION['user_rol'], $roles)) {
        die("Acceso denegado");
    }
}

// Función logout
function logout() {
    registrarLog('logout', 'Cierre de sesión');
    session_destroy();
    session_unset();
    redirect('../auth/login.php');
}

// Función registrar log
function registrarLog($accion, $detalle = '') {
    if(!isset($_SESSION['user_id'])) return;
    
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO logs_sistema (empleado_id, accion, detalle, ip_origen) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $accion,
        $detalle,
        getUserIP()
    ]);
}
?>