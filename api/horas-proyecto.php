<?php
require_once '../config.php';
require_once '../includes/auth.php';
requireAuth();
requireRole(['jefe_departamento', 'admin']);

header('Content-Type: application/json');

try {
    $departamento_id = $_SESSION['user_departamento_id'];
    
    $stmt = $pdo->prepare("
        SELECT 
            p.nombre,
            SUM(hp.horas) as total_horas,
            COUNT(DISTINCT hp.empleado_id) as empleados,
            p.horas_estimadas
        FROM horas_proyectos hp
        INNER JOIN proyectos p ON hp.proyecto_id = p.id
        WHERE p.departamento_id = ? AND p.activo = 1
        GROUP BY hp.proyecto_id
        ORDER BY total_horas DESC
    ");
    
    $stmt->execute([$departamento_id]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $resultados,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}