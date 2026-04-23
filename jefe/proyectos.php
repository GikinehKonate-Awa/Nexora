<?php
require_once '../config.php';
require_once '../includes/auth.php';
requireAuth();
requireRole(['jefe_departamento', 'admin']);

$db = getDB();

// Obtener proyectos del departamento
$stmt = $db->prepare("SELECT * FROM proyectos WHERE departamento_id = ? OR ? = 'admin'");
$stmt->execute([$_SESSION['user_departamento'], $_SESSION['user_rol']]);
$proyectos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proyectos - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_jefe.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Gestión de Proyectos</h2>
                <button class="btn btn-primary">Nuevo Proyecto</button>
            </div>
            
            <div class="page-content">
                <div class="grid grid-3">
                    <?php foreach([
                        ['nombre' => 'Plataforma Cliente Alpha', 'horas' => 245, 'total' => 500, 'presupuesto' => '75.000 €', 'gasto' => '38.500 €', 'estado' => 'En curso'],
                        ['nombre' => 'Integración ERP', 'horas' => 120, 'total' => 180, 'presupuesto' => '42.000 €', 'gasto' => '29.200 €', 'estado' => 'En curso'],
                        ['nombre' => 'Auditoría Seguridad', 'horas' => 78, 'total' => 80, 'presupuesto' => '18.000 €', 'gasto' => '17.100 €', 'estado' => 'Finalizando']
                    ] as $proyecto): ?>
                    <div class="card">
                        <h3 style="margin-bottom: 12px;"><?= $proyecto['nombre'] ?></h3>
                        <div style="margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; font-size:13px; color:#6b7280; margin-bottom:4px;">
                                <span>Progreso</span>
                                <span><?= $proyecto['horas'] ?> / <?= $proyecto['total'] ?>h</span>
                            </div>
                            <div style="height:8px; background:#e5e7eb; border-radius:4px; overflow:hidden;">
                                <div style="height:100%; background:#c9a84c; width:<?= round(($proyecto['horas']/$proyecto['total'])*100) ?>%"></div>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                            <div>
                                <div style="font-size:12px; color:#6b7280;">Presupuesto</div>
                                <div style="font-weight: bold; color:#1a2744;"><?= $proyecto['presupuesto'] ?></div>
                            </div>
                            <div>
                                <div style="font-size:12px; color:#6b7280;">Gasto Real</div>
                                <div style="font-weight: bold; color:#c9a84c;"><?= $proyecto['gasto'] ?></div>
                            </div>
                        </div>
                        <span class="badge badge-success"><?= $proyecto['estado'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Horas por empleado</div>
                    </div>
                    <table style="width:100%;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Empleado</th>
                                <th style="text-align:left; padding:12px;">Proyecto</th>
                                <th style="text-align:left; padding:12px;">Horas asignadas</th>
                                <th style="text-align:left; padding:12px;">Horas registradas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Marc Puig Ferrer</td>
                                <td style="padding:12px;">Plataforma Cliente Alpha</td>
                                <td style="padding:12px;">120h</td>
                                <td style="padding:12px;">87h</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Laura Gómez Vidal</td>
                                <td style="padding:12px;">Integración ERP</td>
                                <td style="padding:12px;">80h</td>
                                <td style="padding:12px;">65h</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>