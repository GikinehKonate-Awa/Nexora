<?php
require_once '../config.php';
require_once '../includes/auth.php';
requireAuth();
requireRole(['empleado']);
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
                    <?php foreach([
                        ['nombre' => 'Plataforma Cliente Alpha', 'horas' => 87, 'total' => 120, 'estado' => 'En curso'],
                        ['nombre' => 'Integración ERP', 'horas' => 32, 'total' => 80, 'estado' => 'En curso']
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
                        <span class="badge badge-success"><?= $proyecto['estado'] ?></span>
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
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">23/04/2026</td>
                                <td style="padding:12px;">Plataforma Cliente Alpha</td>
                                <td style="padding:12px;">7h</td>
                                <td style="padding:12px;">Desarrollo módulo autenticación</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">22/04/2026</td>
                                <td style="padding:12px;">Integración ERP</td>
                                <td style="padding:12px;">6h</td>
                                <td style="padding:12px;">Conexión API REST</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>