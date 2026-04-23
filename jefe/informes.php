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
    <title>Informes y KPIs - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="../assets/js/charts.js"></script>
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_jefe.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Informes y KPIs</h2>
                <div>
                    <button class="btn btn-primary" style="margin-right:8px;">Exportar PDF</button>
                    <button class="btn btn-secondary">Exportar CSV</button>
                </div>
            </div>
            
            <div class="page-content">
                <div class="grid grid-4">
                    <div class="card stat-card">
                        <div class="stat-value">96%</div>
                        <div class="stat-label">Tasa asistencia</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">89%</div>
                        <div class="stat-label">Puntualidad</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">4,2%</div>
                        <div class="stat-label">% Horas extras</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">1,3</div>
                        <div class="stat-label">Incidencias media</div>
                    </div>
                </div>
                
                <div class="grid grid-2">
                    <div class="card">
                        <div class="card-title">Evolución mensual asistencia</div>
                        <canvas id="chartAsistencia" width="600" height="300" style="width:100%;"></canvas>
                    </div>
                    
                    <div class="card">
                        <div class="card-title">Distribución por modalidad</div>
                        <div style="text-align:center;">
                            <canvas id="chartModalidad" width="300" height="300"></canvas>
                            <div style="margin-top:24px; text-align:left;">
                                <div style="display:flex; align-items:center; margin-bottom:12px;">
                                    <div style="width:16px; height:16px; background:#1a2744; border-radius:4px; margin-right:12px;"></div>
                                    <span>Presencial 62%</span>
                                </div>
                                <div style="display:flex; align-items:center; margin-bottom:12px;">
                                    <div style="width:16px; height:16px; background:#c9a84c; border-radius:4px; margin-right:12px;"></div>
                                    <span>Híbrido 28%</span>
                                </div>
                                <div style="display:flex; align-items:center;">
                                    <div style="width:16px; height:16px; background:#6b7280; border-radius:4px; margin-right:12px;"></div>
                                    <span>Teletrabajo 10%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script>
                    window.addEventListener('load', function() {
                        drawBarChart('chartAsistencia', [92, 95, 94, 96, 97, 96], ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun']);
                        drawDonutChart('chartModalidad', [62, 28, 10], ['#1a2744', '#c9a84c', '#6b7280']);
                    });
                </script>
            </div>
        </div>
    </div>
</body>
</html>