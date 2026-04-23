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
    <title>Mi Horario - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Mi Horario</h2>
                <span class="badge badge-info">Modalidad Híbrido</span>
            </div>
            
            <div class="page-content">
                <div class="card">
                    <div class="card-title">Horario semanal</div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Día</th>
                                <th style="text-align:left; padding:12px;">Entrada</th>
                                <th style="text-align:left; padding:12px;">Salida</th>
                                <th style="text-align:left; padding:12px;">Modalidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Lunes</td>
                                <td style="padding:12px;">08:30</td>
                                <td style="padding:12px;">17:30</td>
                                <td style="padding:12px;"><span class="badge badge-warning">Teletrabajo</span></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Martes</td>
                                <td style="padding:12px;">08:00</td>
                                <td style="padding:12px;">17:00</td>
                                <td style="padding:12px;"><span class="badge badge-success">Presencial</span></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Miércoles</td>
                                <td style="padding:12px;">08:30</td>
                                <td style="padding:12px;">17:30</td>
                                <td style="padding:12px;"><span class="badge badge-warning">Teletrabajo</span></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Jueves</td>
                                <td style="padding:12px;">08:00</td>
                                <td style="padding:12px;">17:00</td>
                                <td style="padding:12px;"><span class="badge badge-success">Presencial</span></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Viernes</td>
                                <td style="padding:12px;">08:00</td>
                                <td style="padding:12px;">15:00</td>
                                <td style="padding:12px;"><span class="badge badge-success">Presencial</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="card">
                    <div class="card-title">Próximos festivos</div>
                    <div style="margin-top:16px;">
                        <div style="padding:12px; background:#dbeafe; border-radius:8px; margin-bottom:8px;">
                            📅 1 Mayo - Día del Trabajador
                        </div>
                        <div style="padding:12px; background:#dbeafe; border-radius:8px;">
                            📅 24 Junio - San Juan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>