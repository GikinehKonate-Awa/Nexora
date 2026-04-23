<?php
require_once '../config.php';
require_once '../includes/auth.php';
require_once '../includes/alertas.php';

requireAuth();
requireRole(['jefe_departamento', 'admin', 'rrhh']);

header('Content-Type: application/json');

try {
    $fecha = date('Y-m-d');
    $incumplimientos = check_incumplimientos($fecha);
    
    // Filtrar por departamento del jefe
    $departamento_id = $_SESSION['user_departamento_id'];
    if ($_SESSION['user_rol'] !== 'admin') {
        $incumplimientos = array_filter($incumplimientos, fn($i) => $i['departamento_id'] == $departamento_id);
    }
    
    // Registrar notificaciones automaticamente (una sola vez por incumplimiento)
    foreach ($incumplimientos as $inc) {
        registrar_notificacion_incumplimiento(
            $inc['empleado_id'],
            $inc['tipo'],
            $fecha,
            $inc['descripcion']
        );
    }
    
    echo json_encode([
        'success' => true,
        'data' => array_values($incumplimientos),
        'total' => count($incumplimientos),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}