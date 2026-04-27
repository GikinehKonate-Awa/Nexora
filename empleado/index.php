<?php
require_once '../config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireAuth();
requireRole(['empleado']);

$db = getDB();
$usuario_id = $_SESSION['user_id'];
$hoy = date('Y-m-d');
$inicio_semana = date('Y-m-d', strtotime('monday this week'));
$fin_semana = date('Y-m-d', strtotime('sunday this week'));

// Obtener fichajes de hoy
$fichaje_entrada = $db->query("SELECT fecha_hora FROM fichajes WHERE empleado_id = $usuario_id AND tipo = 'entrada' AND DATE(fecha_hora) = '$hoy' ORDER BY fecha_hora ASC LIMIT 1")->fetchColumn();
$fichaje_salida = $db->query("SELECT fecha_hora FROM fichajes WHERE empleado_id = $usuario_id AND tipo = 'salida' AND DATE(fecha_hora) = '$hoy' ORDER BY fecha_hora DESC LIMIT 1")->fetchColumn();

// Calcular horas trabajadas hoy
$horas_hoy = 0;
if ($fichaje_entrada && $fichaje_salida) {
    $entrada = new DateTime($fichaje_entrada);
    $salida = new DateTime($fichaje_salida);
    $diff = $entrada->diff($salida);
    $horas_hoy = $diff->h + ($diff->i / 60);
} elseif ($fichaje_entrada && !$fichaje_salida) {
    $entrada = new DateTime($fichaje_entrada);
    $ahora = new DateTime();
    $diff = $entrada->diff($ahora);
    $horas_hoy = $diff->h + ($diff->i / 60);
}

// Calcular horas esta semana
$horas_semana = $db->query("
    SELECT ROUND(
        SUM(TIMESTAMPDIFF(MINUTE, f_entrada.fecha_hora, IFNULL(f_salida.fecha_hora, NOW()))) / 60
    , 1)
    FROM fichajes f_entrada
    LEFT JOIN fichajes f_salida ON 
        f_salida.empleado_id = f_entrada.empleado_id 
        AND f_salida.tipo = 'salida' 
        AND DATE(f_salida.fecha_hora) = DATE(f_entrada.fecha_hora)
        AND f_salida.fecha_hora > f_entrada.fecha_hora
    WHERE f_entrada.empleado_id = $usuario_id 
    AND f_entrada.tipo = 'entrada' 
    AND DATE(f_entrada.fecha_hora) BETWEEN '$inicio_semana' AND '$fin_semana'
    GROUP BY f_entrada.empleado_id
")->fetchColumn();

// Estado actual
$estado_actual = 'Sin fichar';
if ($fichaje_entrada && !$fichaje_salida) $estado_actual = 'Fichado - Trabajando';
if ($fichaje_entrada && $fichaje_salida) $estado_actual = 'Finalizado';

// Obtener ultimos 5 fichajes
$ultimos_fichajes = $db->query("
    SELECT DATE(fecha_hora) as fecha, tipo, TIME(fecha_hora) as hora 
    FROM fichajes 
    WHERE empleado_id = $usuario_id 
    ORDER BY fecha_hora DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener notificaciones
$notificaciones = $db->query("
    SELECT titulo, mensaje, leida 
    FROM notificaciones 
    WHERE empleado_id = $usuario_id 
    ORDER BY created_at DESC 
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

function formatearHoras($horas) {
    if (!$horas) return '0h 0m';
    $h = floor($horas);
    $m = round(($horas - $h) * 60);
    return "{$h}h {$m}m";
}
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
                    <div class="stat-value" id="horasHoy"><?= formatearHoras($horas_hoy) ?></div>
                    <div class="stat-label">Horas hoy</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-value" id="horasSemana"><?= formatearHoras($horas_semana) ?></div>
                    <div class="stat-label">Horas esta semana</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-value" id="estadoFichaje"><?= $estado_actual ?></div>
                    <div class="stat-label">Estado actual</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-value"><?= ucfirst($_SESSION['user_modalidad']) ?></div>
                    <div class="stat-label">Modalidad</div>
                </div>
            </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Resumen de hoy</div>
                    </div>
                <div style="display:flex; gap: 24px; margin-top: 16px;">
                    <div style="flex:1; text-align:center; padding: 16px; background:#f5f6fa; border-radius:8px;">
                        <div style="font-size:28px; font-weight:700; color:#1a2744;"><?= $fichaje_entrada ? date('H:i', strtotime($fichaje_entrada)) : '--:--' ?></div>
                        <div style="font-size:13px; color:#6b7280;">Entrada</div>
                    </div>
                    <div style="flex:1; text-align:center; padding: 16px; background:#f5f6fa; border-radius:8px;">
                        <div style="font-size:28px; font-weight:700; color:#1a2744;"><?= $fichaje_salida ? date('H:i', strtotime($fichaje_salida)) : '--:--' ?></div>
                        <div style="font-size:13px; color:#6b7280;">Salida</div>
                    </div>
                    <div style="flex:1; text-align:center; padding: 16px; background:#f5f6fa; border-radius:8px;">
                        <div style="font-size:28px; font-weight:700; color:#1a2744;"><?= formatearHoras($horas_hoy) ?></div>
                        <div style="font-size:13px; color:#6b7280;">Horas trabajadas</div>
                    </div>
                </div>
                </div>
                
                <div class="grid grid-2">
                    <div class="card">
                        <div class="card-title">Últimos fichajes</div>
                        <table id="ultimosFichajes" style="width:100%; margin-top:16px;">
                            <thead>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="text-align:left; padding:8px; font-size:13px; color:#6b7280;">Fecha</th>
                                    <th style="text-align:left; padding:8px; font-size:13px; color:#6b7280;">Tipo</th>
                                    <th style="text-align:left; padding:8px; font-size:13px; color:#6b7280;">Hora</th>
                                </tr>
                            </thead>
                        <tbody>
                            <?php if (count($ultimos_fichajes) > 0): ?>
                                <?php foreach ($ultimos_fichajes as $fichaje): ?>
                                <tr style="border-bottom:1px solid #f5f6fa;">
                                    <td style="padding:12px 8px;"><?= date('d/m/Y', strtotime($fichaje['fecha'])) ?></td>
                                    <td style="padding:12px 8px;">
                                        <span class="badge <?= $fichaje['tipo'] == 'entrada' ? 'badge-success' : 'badge-info' ?>">
                                            <?= ucfirst($fichaje['tipo']) ?>
                                        </span>
                                    </td>
                                    <td style="padding:12px 8px;"><?= date('H:i', strtotime($fichaje['hora'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px 8px;">No hay registros</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        </table>
                    </div>
                    
                    <div class="card">
                        <div class="card-title">Avisos y notificaciones</div>
                    <div style="margin-top:16px;">
                        <?php if (count($notificaciones) > 0): ?>
                            <?php foreach ($notificaciones as $notif): ?>
                            <div style="padding:12px; background:<?= $notif['leida'] ? '#f3f4f6' : '#dbeafe' ?>; border-radius:8px; margin-bottom:8px;">
                                <strong><?= $notif['titulo'] ?></strong><br>
                                <small style="color:#4b5563;"><?= $notif['mensaje'] ?></small>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div style="padding:12px; background:#dbeafe; border-radius:8px; margin-bottom:8px;">
                            ✓ Sistema funcionando correctamente
                        </div>
                        <div style="padding:12px; background:#d1fae5; border-radius:8px;">
                            ✓ Tu horario está actualizado
                        </div>
                        <?php endif; ?>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>