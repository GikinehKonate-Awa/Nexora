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
    <title>Mis Nóminas - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Mis Nóminas</h2>
            </div>
            
            <div class="page-content">
                <div class="card">
                    <div class="card-title">Historial de nóminas</div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Mes</th>
                                <th style="text-align:left; padding:12px;">Año</th>
                                <th style="text-align:left; padding:12px;">Estado</th>
                                <th style="text-align:left; padding:12px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Marzo</td>
                                <td style="padding:12px;">2026</td>
                                <td style="padding:12px;"><span class="badge badge-success">Disponible</span></td>
                                <td style="padding:12px;"><button class="btn btn-primary" style="padding:6px 12px; font-size:12px;">Descargar PDF</button></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Febrero</td>
                                <td style="padding:12px;">2026</td>
                                <td style="padding:12px;"><span class="badge badge-success">Disponible</span></td>
                                <td style="padding:12px;"><button class="btn btn-primary" style="padding:6px 12px; font-size:12px;">Descargar PDF</button></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Enero</td>
                                <td style="padding:12px;">2026</td>
                                <td style="padding:12px;"><span class="badge badge-success">Disponible</span></td>
                                <td style="padding:12px;"><button class="btn btn-primary" style="padding:6px 12px; font-size:12px;">Descargar PDF</button></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Abril</td>
                                <td style="padding:12px;">2026</td>
                                <td style="padding:12px;"><span class="badge badge-warning">Pendiente</span></td>
                                <td style="padding:12px;">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>