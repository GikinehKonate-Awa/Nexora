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
    <title>Notificaciones - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Notificaciones</h2>
            </div>
            
            <div class="page-content">
                <div class="card">
                    <div style="padding:16px; border-bottom:1px solid #f5f6fa; background:#dbeafe;">
                        <strong>Solicitud horas extras aprobada</strong>
                        <div style="font-size:13px; color:#6b7280; margin-top:4px;">Tu solicitud de 5h extras del día 15 de abril ha sido aprobada por Elena Torres</div>
                        <div style="font-size:12px; color:#6b7280; margin-top:8px;">Hace 2 horas</div>
                    </div>
                    <div style="padding:16px; border-bottom:1px solid #f5f6fa;">
                        <strong>Recordatorio fichaje salida</strong>
                        <div style="font-size:13px; color:#6b7280; margin-top:4px;">No has registrado tu salida hoy. Recuerda fichar antes de finalizar la jornada</div>
                        <div style="font-size:12px; color:#6b7280; margin-top:8px;">Ayer a las 17:45</div>
                    </div>
                    <div style="padding:16px; border-bottom:1px solid #f5f6fa;">
                        <strong>Actualización horario</strong>
                        <div style="font-size:13px; color:#6b7280; margin-top:4px;">Tu horario ha sido actualizado. Ahora teletrabajas los lunes y miércoles</div>
                        <div style="font-size:12px; color:#6b7280; margin-top:8px;">Hace 3 días</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>