<?php
require_once '../config.php';
require_once '../includes/auth.php';
requireAuth();
requireRole(['jefe_departamento', 'admin']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobación Solicitudes - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_jefe.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Aprobación de Solicitudes</h2>
            </div>
            
            <div class="page-content">
                <div class="grid grid-3">
                    <div class="card stat-card">
                        <div class="stat-value">7</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">42</div>
                        <div class="stat-label">Aprobadas</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">3</div>
                        <div class="stat-label">Rechazadas</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Solicitudes pendientes de aprobación</div>
                    </div>
                    
                    <?php foreach([
                        ['empleado' => 'Marc Puig Ferrer', 'tipo' => 'Horas extras', 'fecha' => '23/04/2026', 'cantidad' => '3h'],
                        ['empleado' => 'Laura Gómez Vidal', 'tipo' => 'Cambio modalidad', 'fecha' => '22/04/2026', 'cantidad' => 'Teletrabajo'],
                        ['empleado' => 'Sofía Martín Ros', 'tipo' => 'Permiso', 'fecha' => '21/04/2026', 'cantidad' => '1 día']
                    ] as $solicitud): ?>
                    <div style="padding:16px; border-bottom:1px solid #f5f6fa; display:flex; align-items:center;">
                        <div style="flex:1;">
                            <strong><?= $solicitud['empleado'] ?></strong>
                            <div style="font-size:13px; color:#6b7280;"><?= $solicitud['tipo'] ?> - <?= $solicitud['cantidad'] ?></div>
                        </div>
                        <div style="font-size:13px; color:#6b7280; margin-right:24px;"><?= $solicitud['fecha'] ?></div>
                        <div>
                            <button class="btn btn-success" style="padding:8px 16px; margin-right:8px;">Aprobar</button>
                            <button class="btn btn-danger" style="padding:8px 16px;">Rechazar</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>