<?php
require_once '../config.php';
require_once '../includes/auth.php';
requireAuth();
requireRole(['empleado']);

$vpn_status = isVPNConnected();
$user_ip = getUserIP();
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
                        
                        <h3 id="estadoTexto">Aún no has fichado hoy</h3>
                        <p style="color:#6b7280; margin-top:8px;">IP: <?= $user_ip ?></p>
                    </div>
                    
                    <button id="btnFichar" class="btn btn-primary btn-large" onclick="registrarFichaje()" <?= !$vpn_status && $_SESSION['user_modalidad'] === 'presencial' ? 'disabled' : '' ?>>
                        Registrar Entrada
                    </button>
                    
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
                            <tr>
                                <td colspan="4" style="text-align:center; padding:24px; color:#6b7280;">No hay registros hoy</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function registrarFichaje() {
            alert('Fichaje registrado correctamente');
        }
    </script>
</body>
</html>