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
    <title>Resúmenes Semanales - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="../assets/js/charts.js"></script>
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_jefe.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Resúmenes Semanales</h2>
            </div>
            
            <div class="page-content">
                <div class="grid grid-4">
                    <div class="card stat-card">
                        <div class="stat-value">1.247h</div>
                        <div class="stat-label">Total horas semana</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">39,5h</div>
                        <div class="stat-label">Media empleado</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">47h</div>
                        <div class="stat-label">Horas extras</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">2</div>
                        <div class="stat-label">Incidencias</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Horas por día</div>
                    </div>
                    <canvas id="chartHorasSemana" width="700" height="250" style="width:100%;"></canvas>
                </div>
                
                <script>
                    window.addEventListener('load', function() {
                        drawBarChart('chartHorasSemana', [235, 262, 248, 271, 231], ['Lun', 'Mar', 'Mie', 'Jue', 'Vie']);
                    });
                </script>
                
                <div class="card">
                    <div class="card-title">Resumen por empleado</div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Empleado</th>
                                <th style="text-align:left; padding:12px;">Horas</th>
                                <th style="text-align:left; padding:12px;">Extras</th>
                                <th style="text-align:left; padding:12px;">Faltas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Marc Puig Ferrer</td>
                                <td style="padding:12px;">41,5h</td>
                                <td style="padding:12px;"><span class="badge badge-warning">+3h</span></td>
                                <td style="padding:12px;">0</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Laura Gómez Vidal</td>
                                <td style="padding:12px;">39h</td>
                                <td style="padding:12px;">0h</td>
                                <td style="padding:12px;">0</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Sofía Martín Ros</td>
                                <td style="padding:12px;">37h</td>
                                <td style="padding:12px;">0h</td>
                                <td style="padding:12px;"><span class="badge badge-danger">1</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>