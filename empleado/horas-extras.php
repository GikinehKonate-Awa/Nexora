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
    <title>Horas Extras - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Horas Extras</h2>
                <button class="btn btn-primary">Nueva Solicitud</button>
            </div>
            
            <div class="page-content">
                <div class="grid grid-3">
                    <div class="card stat-card">
                        <div class="stat-value">12h</div>
                        <div class="stat-label">Aprobadas</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">3h</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">47h</div>
                        <div class="stat-label">Total mes</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-title">Historial de solicitudes</div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Fecha</th>
                                <th style="text-align:left; padding:12px;">Horas</th>
                                <th style="text-align:left; padding:12px;">Motivo</th>
                                <th style="text-align:left; padding:12px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">20/04/2026</td>
                                <td style="padding:12px;">3h</td>
                                <td style="padding:12px;">Despliegue producción</td>
                                <td style="padding:12px;"><span class="badge badge-warning">Pendiente</span></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">15/04/2026</td>
                                <td style="padding:12px;">5h</td>
                                <td style="padding:12px;">Corrección errores críticos</td>
                                <td style="padding:12px;"><span class="badge badge-success">Aprobada</span></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">08/04/2026</td>
                                <td style="padding:12px;">4h</td>
                                <td style="padding:12px;">Entrega Sprint 2.0</td>
                                <td style="padding:12px;"><span class="badge badge-success">Aprobada</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>