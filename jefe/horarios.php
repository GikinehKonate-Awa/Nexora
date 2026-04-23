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
    <title>Gestión de Horarios - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_jefe.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Gestión de Horarios</h2>
            </div>
            
            <div class="page-content">
                <div class="card">
                    <div class="card-title">Horarios empleados</div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Empleado</th>
                                <th style="text-align:left; padding:12px;">Modalidad</th>
                                <th style="text-align:left; padding:12px;">Entrada</th>
                                <th style="text-align:left; padding:12px;">Salida</th>
                                <th style="text-align:left; padding:12px;">Teletrabajo</th>
                                <th style="text-align:left; padding:12px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Marc Puig Ferrer</td>
                                <td style="padding:12px;"><span class="badge badge-info">Híbrido</span></td>
                                <td style="padding:12px;">08:00</td>
                                <td style="padding:12px;">17:00</td>
                                <td style="padding:12px;">Lun / Mie</td>
                                <td style="padding:12px;"><button class="btn" style="padding:6px 12px; font-size:12px;">Editar</button></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Laura Gómez Vidal</td>
                                <td style="padding:12px;"><span class="badge badge-success">Presencial</span></td>
                                <td style="padding:12px;">09:00</td>
                                <td style="padding:12px;">18:00</td>
                                <td style="padding:12px;">-</td>
                                <td style="padding:12px;"><button class="btn" style="padding:6px 12px; font-size:12px;">Editar</button></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Sofía Martín Ros</td>
                                <td style="padding:12px;"><span class="badge badge-warning">Teletrabajo</span></td>
                                <td style="padding:12px;">08:30</td>
                                <td style="padding:12px;">17:30</td>
                                <td style="padding:12px;">Todos</td>
                                <td style="padding:12px;"><button class="btn" style="padding:6px 12px; font-size:12px;">Editar</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>