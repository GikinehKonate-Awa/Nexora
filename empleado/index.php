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
    <title>Inicio - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <div>
                    <h2>Bienvenido, <?= $_SESSION['user_nombre'] ?></h2>
                    <p style="color:#6b7280;"><?= date('l, d \d\e F \d\e Y') ?></p>
                </div>
                <div>
                    <span class="badge badge-success">Conectado</span>
                </div>
            </div>
            
            <div class="page-content">
                <div class="grid grid-4">
                    <div class="card stat-card">
                        <div class="stat-value" id="horasHoy">0h 0m</div>
                        <div class="stat-label">Horas hoy</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value" id="horasSemana">0h</div>
                        <div class="stat-label">Horas esta semana</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value" id="estadoFichaje">Sin fichar</div>
                        <div class="stat-label">Estado actual</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value"><?= $_SESSION['user_modalidad'] ?></div>
                        <div class="stat-label">Modalidad</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Resumen de hoy</div>
                    </div>
                    <div style="display:flex; gap: 24px; margin-top: 16px;">
                        <div style="flex:1; text-align:center; padding: 16px; background:#f5f6fa; border-radius:8px;">
                            <div style="font-size:28px; font-weight:700; color:#1a2744;">--:--</div>
                            <div style="font-size:13px; color:#6b7280;">Entrada</div>
                        </div>
                        <div style="flex:1; text-align:center; padding: 16px; background:#f5f6fa; border-radius:8px;">
                            <div style="font-size:28px; font-weight:700; color:#1a2744;">--:--</div>
                            <div style="font-size:13px; color:#6b7280;">Salida</div>
                        </div>
                        <div style="flex:1; text-align:center; padding: 16px; background:#f5f6fa; border-radius:8px;">
                            <div style="font-size:28px; font-weight:700; color:#1a2744;">0h 0m</div>
                            <div style="font-size:13px; color:#6b7280;">Horas trabajadas</div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-2">
                    <div class="card">
                        <div class="card-title">Últimos fichajes</div>
                        <table style="width:100%; margin-top:16px;">
                            <thead>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="text-align:left; padding:8px; font-size:13px; color:#6b7280;">Fecha</th>
                                    <th style="text-align:left; padding:8px; font-size:13px; color:#6b7280;">Tipo</th>
                                    <th style="text-align:left; padding:8px; font-size:13px; color:#6b7280;">Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom:1px solid #f5f6fa;">
                                    <td style="padding:12px 8px;">No hay registros</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card">
                        <div class="card-title">Avisos y notificaciones</div>
                        <div style="margin-top:16px;">
                            <div style="padding:12px; background:#dbeafe; border-radius:8px; margin-bottom:8px;">
                                ✓ Sistema funcionando correctamente
                            </div>
                            <div style="padding:12px; background:#d1fae5; border-radius:8px;">
                                ✓ Tu horario está actualizado
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>