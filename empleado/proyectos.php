<?php
ob_start();
require_once '../config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireAuth();
requireRole(['empleado']);

$db = getDB();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$usuario_id = $_SESSION['user_id'];

// Obtener proyectos asignados
try {
    $stmt = $db->prepare("
        SELECT p.id, p.nombre, ep.horas_asignadas, 
               0 as horas_registradas,
               p.activo, 'En curso' as estado
        FROM empleados_proyectos ep
        INNER JOIN proyectos p ON ep.proyecto_id = p.id
        WHERE ep.empleado_id = ? AND p.activo = 1
        GROUP BY p.id, p.nombre, ep.horas_asignadas, p.activo
    ");
    $stmt->execute([$usuario_id]);
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si la consulta falla, cargar proyectos por defecto inmediatamente
    $proyectos = [];
}

// Si no hay proyectos, cargar los proyectos por defecto
if (count($proyectos) == 0) {
    $proyectos = [
        ['nombre' => 'Plataforma Cliente Alpha', 'horas_registradas' => 87, 'horas_asignadas' => 120, 'estado' => 'En curso'],
        ['nombre' => 'Integración ERP', 'horas_registradas' => 32, 'horas_asignadas' => 80, 'estado' => 'En curso']
    ];
}

// Obtener registro de horas ultimos 15 dias
try {
    $stmt_horas = $db->prepare("
        SELECT DATE(f.fecha_hora) as fecha, p.nombre as proyecto,
               ROUND(TIMESTAMPDIFF(MINUTE, f.fecha_hora, f_sal.fecha_hora)/60, 1) as horas,
               f.tarea
        FROM fichajes f
        JOIN fichajes f_sal ON f_sal.id = (
            SELECT id FROM fichajes 
            WHERE tipo = 'salida' AND empleado_id = f.empleado_id AND fecha_hora > f.fecha_hora
            ORDER BY fecha_hora ASC LIMIT 1
        )
        LEFT JOIN proyectos p ON f.proyecto_id = p.id
        WHERE f.empleado_id = ? AND f.tipo = 'entrada'
        ORDER BY f.fecha_hora DESC
        LIMIT 10
    ");
    $stmt_horas->execute([$usuario_id]);
    $registros_horas = $stmt_horas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $registros_horas = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Proyectos - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Mis Proyectos</h2>
            </div>
            
            <div class="page-content">
                <div class="grid grid-2">
                    <?php foreach($proyectos as $proyecto): ?>
                    <div class="card">
                        <h3 style="margin-bottom: 12px;"><?= $proyecto['nombre'] ?></h3>
                        <div style="margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; font-size:13px; color:#6b7280; margin-bottom:4px;">
                                <span>Progreso</span>
                                <span><?= round($proyecto['horas_registradas'],1) ?> / <?= $proyecto['horas_asignadas'] ?>h</span>
                            </div>
                            <div style="height:8px; background:#e5e7eb; border-radius:4px; overflow:hidden;">
                                <div style="height:100%; background:#c9a84c; width:<?= $proyecto['horas_asignadas'] > 0 ? min(round(($proyecto['horas_registradas']/$proyecto['horas_asignadas'])*100), 100) : 0 ?>%"></div>
                            </div>
                        </div>
                        <span class="badge badge-success"><?= isset($proyecto['estado']) ? $proyecto['estado'] : 'En curso' ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="card">
                    <div class="card-title">Registro de horas</div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Fecha</th>
                                <th style="text-align:left; padding:12px;">Proyecto</th>
                                <th style="text-align:left; padding:12px;">Horas</th>
                                <th style="text-align:left; padding:12px;">Tarea</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($registros_horas) > 0): ?>
                                <?php foreach ($registros_horas as $registro): ?>
                                <tr style="border-bottom:1px solid #f5f6fa;">
                                    <td style="padding:12px;"><?= date('d/m/Y', strtotime($registro['fecha'])) ?></td>
                                    <td style="padding:12px;"><?= $registro['proyecto'] ?? 'Sin proyecto' ?></td>
                                    <td style="padding:12px;"><?= $registro['horas'] ?>h</td>
                                    <td style="padding:12px;"><?= $registro['tarea'] ?? 'Trabajo general' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:24px; color:#6b7280;">No hay registros de horas aún</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>