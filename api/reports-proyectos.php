<?php
require_once '../config.php';
require_once '../includes/auth.php';
requireAuth();
requireRole(['jefe_departamento', 'admin']);

header('Content-Type: application/json');

try {
    $departamento_id = $_SESSION['user_departamento_id'];
    $periodo = $_GET['periodo'] ?? 'semana';
    
    $fecha_inicio = date('Y-m-d', strtotime('-8 weeks'));
    $fecha_fin = date('Y-m-d');
    
    // 1. Horas por proyecto
    $stmt = $pdo->prepare("
        SELECT 
            p.nombre,
            SUM(hp.horas) as total_horas
        FROM horas_proyectos hp
        INNER JOIN proyectos p ON hp.proyecto_id = p.id
        WHERE p.departamento_id = ? AND p.activo = 1 AND hp.fecha BETWEEN ? AND ?
        GROUP BY hp.proyecto_id
        ORDER BY total_horas DESC
    ");
    $stmt->execute([$departamento_id, $fecha_inicio, $fecha_fin]);
    $horas_por_proyecto = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Horas reales vs estimadas
    $stmt = $pdo->prepare("
        SELECT 
            p.nombre,
            p.horas_estimadas,
            COALESCE(SUM(hp.horas), 0) as horas_reales
        FROM proyectos p
        LEFT JOIN horas_proyectos hp ON p.id = hp.proyecto_id AND hp.fecha BETWEEN ? AND ?
        WHERE p.departamento_id = ? AND p.activo = 1
        GROUP BY p.id
        ORDER BY p.nombre
    ");
    $stmt->execute([$fecha_inicio, $fecha_fin, $departamento_id]);
    $horas_vs_estimadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Evolucion semanal ultimas 8 semanas
    $evolucion_semanal = [];
    for ($i = 7; $i >= 0; $i--) {
        $inicio_semana = date('Y-m-d', strtotime("monday -$i week"));
        $fin_semana = date('Y-m-d', strtotime("sunday -$i week"));
        
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(horas), 0) as total
            FROM horas_proyectos hp
            INNER JOIN proyectos p ON hp.proyecto_id = p.id
            WHERE p.departamento_id = ? AND hp.fecha BETWEEN ? AND ?
        ");
        $stmt->execute([$departamento_id, $inicio_semana, $fin_semana]);
        $total = $stmt->fetchColumn();
        
        $evolucion_semanal[] = [
            'semana' => date('d/m', strtotime($inicio_semana)) . ' - ' . date('d/m', strtotime($fin_semana)),
            'horas' => (float)$total
        ];
    }
    
    echo json_encode([
        'success' => true,
        'horas_por_proyecto' => $horas_por_proyecto,
        'horas_vs_estimadas' => $horas_vs_estimadas,
        'evolucion_semanal' => $evolucion_semanal,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}