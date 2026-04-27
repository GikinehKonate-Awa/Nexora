<?php
require_once '../config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireAuth();
requireRole(['empleado']);

$db = getDB();
$usuario_id = $_SESSION['user_id'];
$hoy = date('Y-m-d');
$vpn_status = isVPNConnected();
$user_ip = getUserIP();

// Procesar fichaje normal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'fichar') {
    $tiene_entrada = $db->query("SELECT id FROM fichajes WHERE empleado_id = $usuario_id AND tipo = 'entrada' AND DATE(fecha_hora) = '$hoy' LIMIT 1")->fetchColumn();
    $tiene_salida = $db->query("SELECT id FROM fichajes WHERE empleado_id = $usuario_id AND tipo = 'salida' AND DATE(fecha_hora) = '$hoy' LIMIT 1")->fetchColumn();
    
    $tipo = !$tiene_entrada ? 'entrada' : 'salida';
    
    if ($tipo === 'salida' && $tiene_salida) {
        header("Location: fichar.php?mensaje=Ya has registrado salida hoy");
        exit;
    }
    
    $stmt = $db->prepare("INSERT INTO fichajes (empleado_id, tipo, fecha_hora, ip_origen, vpn_detectada) VALUES (?, ?, NOW(), ?, ?)");
    $stmt->execute([$usuario_id, $tipo, $user_ip, $vpn_status ? 1 : 0]);
    
    header("Location: fichar.php?mensaje=" . ucfirst($tipo) . " registrada correctamente a las " . date('H:i'));
    exit;
}

// Procesar registro manual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    $comentario = trim($_POST['comentario']);
    
    if (empty($comentario)) {
        $error = "Debes introducir un motivo para el registro manual";
    } else {
        $stmt = $db->prepare("INSERT INTO fichajes (empleado_id, tipo, fecha_hora, ip_origen, vpn_detectada, es_manual, comentario) VALUES (?, 'salida', NOW(), ?, ?, 1, ?)");
        $stmt->execute([$usuario_id, $user_ip, $vpn_status ? 1 : 0, $comentario]);
        
        header("Location: fichar.php?mensaje=Salida manual registrada correctamente. Pendiente de aprobación por RRHH");
        exit;
    }
}

// Obtener estado actual
$fichaje_entrada = $db->query("SELECT fecha_hora FROM fichajes WHERE empleado_id = $usuario_id AND tipo = 'entrada' AND DATE(fecha_hora) = '$hoy' ORDER BY fecha_hora ASC LIMIT 1")->fetchColumn();
$fichaje_salida = $db->query("SELECT fecha_hora FROM fichajes WHERE empleado_id = $usuario_id AND tipo = 'salida' AND DATE(fecha_hora) = '$hoy' ORDER BY fecha_hora DESC LIMIT 1")->fetchColumn();

// Obtener historial de hoy
$fichajes_hoy = $db->query("
    SELECT TIME(fecha_hora) as hora, tipo, es_manual, validado, ip_origen 
    FROM fichajes 
    WHERE empleado_id = $usuario_id AND DATE(fecha_hora) = '$hoy' 
    ORDER BY fecha_hora ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Determinar texto y boton
$estado_texto = "Aún no has fichado hoy";
$boton_texto = "Registrar Entrada";

if ($fichaje_entrada && !$fichaje_salida) {
    $estado_texto = "✅ Fichado correctamente - Trabajando desde " . date('H:i', strtotime($fichaje_entrada));
    $boton_texto = "Registrar Salida";
}

if ($fichaje_entrada && $fichaje_salida) {
    $estado_texto = "✅ Jornada finalizada hoy. Horas: " . gmdate('H:i', strtotime($fichaje_salida) - strtotime($fichaje_entrada));
    $boton_texto = "Ya has fichado hoy";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichar - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="../assets/js/fichar.js"></script>
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Control de Presencia</h2>
                <div>
                    <span class="badge <?= $vpn_status ? 'badge-success' : 'badge-warning' ?>">
                        VPN: <?= $vpn_status ? 'Conectado' : 'No detectado' ?>
                    </span>
                </div>
            </div>
            
            <div class="page-content">
                <div class="card" style="text-align:center; padding: 48px 24px;">
                    <div style="margin-bottom: 32px;">
                        <div style="width:120px; height:120px; border-radius:50%; background:linear-gradient(135deg, #1a2744, #0f172a); display:flex; align-items:center; justify-content:center; margin: 0 auto 24px auto;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="white" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        
                        <h3 id="estadoTexto"><?= $estado_texto ?></h3>
                        <p style="color:#6b7280; margin-top:8px;">IP: <?= $user_ip ?></p>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="accion" value="fichar">
                        <button id="btnFichar" type="submit" class="btn btn-primary btn-large" <?= (!$vpn_status && $_SESSION['user_modalidad'] === 'presencial') || ($fichaje_entrada && $fichaje_salida) ? 'disabled' : '' ?>>
                            <?= $boton_texto ?>
                        </button>
                    </form>
                    
                    <?php if(!$vpn_status && $_SESSION['user_modalidad'] === 'presencial'): ?>
                    <p style="color:#ef4444; margin-top:16px; font-size:14px;">
                        ⚠️ Debes estar conectado a la VPN corporativa para fichar
                    </p>
                    <?php endif; ?>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Registro manual nocturno</div>
                    </div>
                    <p style="color:#6b7280; margin-bottom:16px;">Si olvidaste fichar tu salida, puedes registrarla manualmente añadiendo un comentario obligatorio. Este registro quedará marcado como manual y será revisado por RRHH.</p>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Motivo del registro manual</label>
                            <textarea name="comentario" class="form-input" rows="3" required placeholder="Explica por qué no pudiste registrar la salida en su momento..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary">Registrar Salida Manualmente</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Historial de hoy</div>
                    </div>
                    <table style="width:100%;">
                        <thead>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px; font-size:13px; color:#6b7280;">Hora</th>
                                <th style="text-align:left; padding:12px; font-size:13px; color:#6b7280;">Tipo</th>
                                <th style="text-align:left; padding:12px; font-size:13px; color:#6b7280;">Estado</th>
                                <th style="text-align:left; padding:12px; font-size:13px; color:#6b7280;">IP</th>
                            </tr>
                        </thead>
                        <tbody id="historialFichajes">
                            <?php if (count($fichajes_hoy) > 0): ?>
                                <?php foreach ($fichajes_hoy as $fichaje): ?>
                                <tr>
                                    <td style="padding:12px;"><?= date('H:i', strtotime($fichaje['hora'])) ?></td>
                                    <td style="padding:12px;">
                                        <span class="badge <?= $fichaje['tipo'] == 'entrada' ? 'badge-success' : 'badge-info' ?>">
                                            <?= ucfirst($fichaje['tipo']) ?>
                                            <?= $fichaje['es_manual'] ? ' (Manual)' : '' ?>
                                        </span>
                                    </td>
                                    <td style="padding:12px;">
                                        <span class="badge <?= $fichaje['validado'] ? 'badge-success' : 'badge-warning' ?>">
                                            <?= $fichaje['validado'] ? 'Validado' : ($fichaje['es_manual'] ? 'Pendiente revisión' : 'OK') ?>
                                        </span>
                                    </td>
                                    <td style="padding:12px;"><?= $fichaje['ip_origen'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:24px; color:#6b7280;">No hay registros hoy</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    <?php if(isset($_GET['mensaje'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarNotificacion('✅ <?= $_GET['mensaje'] ?>', 'success');
        });
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarNotificacion('⚠️ <?= $error ?>', 'error');
        });
    <?php endif; ?>
    </script>
</body>
</html>