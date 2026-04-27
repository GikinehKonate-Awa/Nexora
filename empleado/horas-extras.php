<?php
require_once '../config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireAuth();
requireRole(['empleado']);

$db = getDB();
$usuario_id = $_SESSION['user_id'];
$mes_actual = date('m');
$anio_actual = date('Y');

// Procesar nueva solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['horas']) && isset($_POST['fecha'])) {
    $horas = floatval($_POST['horas']);
    $fecha = $_POST['fecha'];
    $motivo = trim($_POST['motivo']);
    
    if ($horas <= 0 || empty($fecha) || empty($motivo)) {
        $error = "Debes completar todos los campos correctamente";
    } else {
        $stmt = $db->prepare("INSERT INTO horas_extras (empleado_id, fecha, cantidad, motivo, estado) VALUES (?, ?, ?, ?, 'pendiente')");
        $stmt->execute([$usuario_id, $fecha, $horas, $motivo]);
        
        header("Location: horas-extras.php?mensaje=Solicitud registrada correctamente. Pendiente de aprobación");
        exit;
    }
}

// Calcular KPIs
$aprobadas = $db->query("SELECT ROUND(SUM(cantidad),1) FROM horas_extras WHERE empleado_id = $usuario_id AND estado = 'aprobada' AND MONTH(fecha) = $mes_actual AND YEAR(fecha) = $anio_actual")->fetchColumn() ?: 0;
$pendientes = $db->query("SELECT ROUND(SUM(cantidad),1) FROM horas_extras WHERE empleado_id = $usuario_id AND estado = 'pendiente' AND MONTH(fecha) = $mes_actual AND YEAR(fecha) = $anio_actual")->fetchColumn() ?: 0;
$total_mes = $db->query("SELECT ROUND(SUM(cantidad),1) FROM horas_extras WHERE empleado_id = $usuario_id AND MONTH(fecha) = $mes_actual AND YEAR(fecha) = $anio_actual")->fetchColumn() ?: 0;

// Obtener historial
$solicitudes = $db->query("
    SELECT fecha, cantidad, motivo, estado, created_at 
    FROM horas_extras 
    WHERE empleado_id = $usuario_id 
    ORDER BY fecha DESC 
    LIMIT 15
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horas Extras - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Horas Extras</h2>
                <button class="btn btn-primary">Nueva Solicitud</button>
            </div>
            
            <div class="page-content">
                <div class="grid grid-3">
                    <div class="card stat-card">
                        <div class="stat-value"><?= $aprobadas ?>h</div>
                        <div class="stat-label">Aprobadas</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value"><?= $pendientes ?>h</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value"><?= $total_mes ?>h</div>
                        <div class="stat-label">Total mes</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-title">Historial de solicitudes</div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Fecha</th>
                                <th style="text-align:left; padding:12px;">Horas</th>
                                <th style="text-align:left; padding:12px;">Motivo</th>
                                <th style="text-align:left; padding:12px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($solicitudes) > 0): ?>
                                <?php foreach ($solicitudes as $s): ?>
                                <tr style="border-bottom:1px solid #f5f6fa;">
                                    <td style="padding:12px;"><?= date('d/m/Y', strtotime($s['fecha'])) ?></td>
                                    <td style="padding:12px;"><?= $s['cantidad'] ?>h</td>
                                    <td style="padding:12px;"><?= $s['motivo'] ?></td>
                                    <td style="padding:12px;">
                                        <span class="badge 
                                            <?= $s['estado'] == 'aprobada' ? 'badge-success' : ($s['estado'] == 'rechazada' ? 'badge-danger' : 'badge-warning') ?>">
                                            <?= ucfirst($s['estado']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:24px; color:#6b7280;">No tienes solicitudes de horas extras</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Nueva Solicitud -->
    <div id="modal-nueva-solicitud" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:99999; align-items:center; justify-content:center;">
        <div class="card" style="width:500px; max-width:90vw; padding:30px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3>📝 Nueva Solicitud de Horas Extras</h3>
                <button onclick="document.getElementById('modal-nueva-solicitud').style.display='none'" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-input" required max="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Horas extras</label>
                    <input type="number" name="horas" class="form-input" min="0.5" max="12" step="0.5" required placeholder="Ej: 2.5">
                </div>
                <div class="form-group">
                    <label class="form-label">Motivo / Descripción</label>
                    <textarea name="motivo" class="form-input" rows="3" required placeholder="Explica el motivo de estas horas extras..."></textarea>
                </div>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:15px;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-nueva-solicitud').style.display='none'">CANCELAR</button>
                    <button type="submit" class="btn btn-primary">✅ ENVIAR SOLICITUD</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    document.querySelector('.btn-primary').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('modal-nueva-solicitud').style.display = 'flex';
    });
    
    <?php if(isset($_GET['mensaje'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        mostrarNotificacion('✅ <?= $_GET['mensaje'] ?>', 'success');
    });
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
    document.addEventListener('DOMContentLoaded', function() {
        mostrarNotificacion('⚠️ <?= $error ?>', 'error');
        document.getElementById('modal-nueva-solicitud').style.display = 'flex';
    });
    <?php endif; ?>
    </script>
</body>
</html>
