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
    <title>Control de Presencia - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_jefe.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Control de Presencia</h2>
                <button class="btn btn-primary">Exportar CSV</button>
            </div>
            
            <div class="page-content">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Fichajes de hoy</div>
                    </div>
                    <table style="width:100%;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Empleado</th>
                                <th style="text-align:left; padding:12px;">Entrada</th>
                                <th style="text-align:left; padding:12px;">Salida</th>
                                <th style="text-align:left; padding:12px;">Horas</th>
                                <th style="text-align:left; padding:12px;">Estado</th>
                                <th style="text-align:left; padding:12px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Marc Puig Ferrer</td>
                                <td style="padding:12px;">08:15</td>
                                <td style="padding:12px;">--:--</td>
                                <td style="padding:12px;">7h 35m</td>
                                <td style="padding:12px;"><span class="badge badge-success">Presente</span></td>
                                <td style="padding:12px;"><button class="btn" style="padding:6px 12px; font-size:12px;">Ver</button></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Laura Gómez Vidal</td>
                                <td style="padding:12px;">08:47</td>
                                <td style="padding:12px;">17:32</td>
                                <td style="padding:12px;">8h 45m</td>
                                <td style="padding:12px;"><span class="badge badge-info">Finalizado</span></td>
                                <td style="padding:12px;"><button class="btn" style="padding:6px 12px; font-size:12px;">Ver</button></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Sofía Martín Ros</td>
                                <td style="padding:12px;">09:12</td>
                                <td style="padding:12px;">--:--</td>
                                <td style="padding:12px;">6h 12m</td>
                                <td style="padding:12px;"><span class="badge badge-warning">Manual</span></td>
                                <td style="padding:12px;"><button class="btn btn-success" style="padding:6px 12px; font-size:12px;">Validar</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>