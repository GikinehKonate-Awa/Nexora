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
    <title>Directorio - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Directorio Empleados</h2>
                <input type="text" class="form-input" style="max-width: 300px;" placeholder="Buscar empleado...">
            </div>
            
            <div class="page-content">
                <div class="card">
                    <div class="card-title">Departamento Desarrollo</div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Nombre</th>
                                <th style="text-align:left; padding:12px;">Email</th>
                                <th style="text-align:left; padding:12px;">Puesto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Elena Torres Bravo</td>
                                <td style="padding:12px;">elena.torres@nexora.com</td>
                                <td style="padding:12px;">Jefe Departamento</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Marc Puig Ferrer</td>
                                <td style="padding:12px;">marc.puig@nexora.com</td>
                                <td style="padding:12px;">Desarrollador Senior</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Álvaro Ruiz López</td>
                                <td style="padding:12px;">alvaro.ruiz@nexora.com</td>
                                <td style="padding:12px;">Desarrollador</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Marta García Soler</td>
                                <td style="padding:12px;">marta.garcia@nexora.com</td>
                                <td style="padding:12px;">Diseñadora UX</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>