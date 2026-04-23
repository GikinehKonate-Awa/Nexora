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
    <title>Dashboard Equipo - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_jefe.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <div>
                    <h2>Panel de Control</h2>
                    <p style="color:#6b7280;">Vista general del departamento</p>
                </div>
                <div>
                    <span class="badge badge-success">Conectado</span>
                </div>
            </div>
            
            <div class="page-content">
            
                <!-- SECCION INCUMPLIMIENTOS -->
                <div class="card" id="incumplimientosCard" style="background: #fef2f2; border-left: 4px solid #dc2626; display: none;">
                    <div class="card-header" style="background: #dc2626; color: white; border-radius: 6px 6px 0 0; margin: -16px -16px 16px -16px; padding: 16px;">
                        <div class="card-title" style="color: white; font-weight: 600;">⚠️ Incumplimientos detectados hoy</div>
                        <span id="contadorIncumplimientos" style="background: white; color: #dc2626; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">0</span>
                    </div>
                    <div id="listaIncumplimientos">
                    </div>
                </div>
            
                <div class="grid grid-4">
                    <div class="card stat-card">
                        <div class="stat-value">42</div>
                        <div class="stat-label">Empleados</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">39</div>
                        <div class="stat-label">Presentes hoy</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">2</div>
                        <div class="stat-label">Ausencias</div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-value">7</div>
                        <div class="stat-label">Pendientes revisar</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Estado en tiempo real</div>
                    </div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px; font-size:13px; color:#6b7280;">Empleado</th>
                                <th style="text-align:left; padding:12px; font-size:13px; color:#6b7280;">Entrada</th>
                                <th style="text-align:left; padding:12px; font-size:13px; color:#6b7280;">Estado</th>
                                <th style="text-align:left; padding:12px; font-size:13px; color:#6b7280;">Horas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Marc Puig Ferrer</td>
                                <td style="padding:12px;">08:15</td>
                                <td style="padding:12px;"><span class="badge badge-success">Trabajando</span></td>
                                <td style="padding:12px;">7h 32m</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Laura Gómez Vidal</td>
                                <td style="padding:12px;">08:47</td>
                                <td style="padding:12px;"><span class="badge badge-success">Trabajando</span></td>
                                <td style="padding:12px;">6h 58m</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px;">Sofía Martín Ros</td>
                                <td style="padding:12px;">09:02</td>
                                <td style="padding:12px;"><span class="badge badge-warning">Ausente</span></td>
                                <td style="padding:12px;">4h 12m</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="grid grid-2">
                    <div class="card">
                        <div class="card-title">Fichajes manuales pendientes</div>
                        <div style="margin-top:16px;">
                            <div style="padding:12px; background:#fef3c7; border-radius:8px; margin-bottom:8px;">
                                <strong>Marc Puig</strong> - Registro salida manual <span style="float:right; cursor:pointer; color:#92400e;">Revisar</span>
                            </div>
                            <div style="padding:12px; background:#fef3c7; border-radius:8px;">
                                <strong>Laura Gómez</strong> - Registro entrada manual <span style="float:right; cursor:pointer; color:#92400e;">Revisar</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-title">Últimas solicitudes</div>
                        <div style="margin-top:16px;">
                            <div style="padding:12px; background:#dbeafe; border-radius:8px; margin-bottom:8px;">
                                <strong>Sofía Martín</strong> - Solicitud horas extras <span style="float:right; cursor:pointer; color:#1e40af;">Aprobar</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    async function cargarIncumplimientos() {
        try {
            const res = await fetch('../api/incumplimientos.php');
            const data = await res.json();
            
            const card = document.getElementById('incumplimientosCard');
            const lista = document.getElementById('listaIncumplimientos');
            const contador = document.getElementById('contadorIncumplimientos');
            
            if (data.success && data.data.length > 0) {
                card.style.display = 'block';
                contador.textContent = data.total;
                
                let html = '';
                data.data.forEach(inc => {
                    let tipoTexto = {
                        'retraso': 'Retraso',
                        'salida_anticipada': 'Salida Anticipada',
                        'ausencia': 'Ausencia Sin Justificar',
                        'horas_insuficientes': 'Horas Insuficientes'
                    };
                    
                    html += `
                    <div style="padding: 12px; background: white; border-radius: 8px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>${inc.nombre}</strong>
                            <div style="color: #dc2626; font-size: 13px;">${tipoTexto[inc.tipo] || inc.tipo} ${inc.minutos ? '(' + inc.minutos + ' min)' : ''}</div>
                        </div>
                        <button class="btn btn-small" style="background: #dc2626; color: white; border: none; font-size: 12px;">Notificar</button>
                    </div>
                    `;
                });
                
                lista.innerHTML = html;
            } else {
                card.style.display = 'none';
            }
        } catch (e) {
            console.error('Error cargando incumplimientos', e);
        }
    }
    
    // Cargar inmediatamente y luego cada 5 minutos
    cargarIncumplimientos();
    setInterval(cargarIncumplimientos, 300000);
    </script>
</body>
</html>
