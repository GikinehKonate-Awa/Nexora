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
    <title>Comunicación - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_jefe.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Comunicación</h2>
                <button class="btn btn-primary">Nuevo Comunicado</button>
            </div>
            
            <div class="page-content">
                <div class="card">
                    <div class="card-title">Historial de comunicados</div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Título</th>
                                <th style="text-align:left; padding:12px;">Fecha</th>
                                <th style="text-align:left; padding:12px;">Destinatarios</th>
                                <th style="text-align:left; padding:12px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;"><strong>Recordatorio cierre mes abril</strong></td>
                                <td style="padding:12px;">23/04/2026</td>
                                <td style="padding:12px;">Todo el departamento</td>
                                <td style="padding:12px;"><span class="badge badge-success">Enviado</span></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;"><strong>Actualización protocolo fichaje</strong></td>
                                <td style="padding:12px;">20/04/2026</td>
                                <td style="padding:12px;">Todo el departamento</td>
                                <td style="padding:12px;"><span class="badge badge-success">Enviado</span></td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;"><strong>Aviso festivo 1 mayo</strong></td>
                                <td style="padding:12px;">15/04/2026</td>
                                <td style="padding:12px;">Toda la empresa</td>
                                <td style="padding:12px;"><span class="badge badge-success">Enviado</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="card">
                    <div class="card-title">Enviar nuevo comunicado</div>
                    <div style="margin-top:16px;">
                        <div class="form-group">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Destinatarios</label>
                            <select class="form-input">
                                <option>Todo el departamento</option>
                                <option>Solo empleados</option>
                                <option>Toda la empresa</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mensaje</label>
                            <textarea class="form-input" rows="5"></textarea>
                        </div>
                        <button class="btn btn-primary">Enviar Comunicado</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>