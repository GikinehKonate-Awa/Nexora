<?php
require_once '../config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireAuth();
requireRole(['empleado']);

$db = getDB();
$usuario_id = $_SESSION['user_id'];

// Descargar nomina
if (isset($_GET['descargar']) && is_numeric($_GET['descargar'])) {
    $nomina_id = intval($_GET['descargar']);
    
    $stmt = $db->prepare("SELECT * FROM nominas WHERE id = ? AND empleado_id = ? AND estado = 'generada'");
    $stmt->execute([$nomina_id, $usuario_id]);
    $nomina = $stmt->fetch();
    
    if ($nomina) {
        // Si existe archivo fisico
        if ($nomina['archivo'] && file_exists('../nominas/' . $nomina['archivo'])) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="Nomina_' . $nomina['mes'] . '_' . $nomina['anio'] . '.pdf"');
            readfile('../nominas/' . $nomina['archivo']);
            exit;
        } else {
            // Generar PDF al vuelo
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="Nomina_' . $nomina['mes'] . '_' . $nomina['anio'] . '.pdf"');
            
            $contenido = "%PDF-1.4
%NEXORA CONSULTING GROUP
═══════════════════════════════════════
RECIBO DE PAGO NÓMINA
═══════════════════════════════════════

📅 Mes: {$nomina['mes']} / {$nomina['anio']}
👤 Empleado: {$_SESSION['user_nombre']} {$_SESSION['user_apellidos']}
🏢 Departamento: {$_SESSION['user_departamento']}
📅 Fecha generación: " . date('d/m/Y H:i') . "

✅ NÓMINA GENERADA CORRECTAMENTE
═══════════════════════════════════════
Todos los datos corresponden al periodo indicado.

Generado automaticamente por el sistema Nexora
";
            echo $contenido;
            exit;
        }
    } else {
        header("Location: nominas.php?error=Nómina no disponible");
        exit;
    }
}

// Obtener nominas
$nominas = $db->query("
    SELECT id, mes, anio, estado, archivo 
    FROM nominas 
    WHERE empleado_id = $usuario_id 
    ORDER BY anio DESC, mes DESC 
    LIMIT 12
")->fetchAll(PDO::FETCH_ASSOC);

$meses_nombres = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Nóminas - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Mis Nóminas</h2>
            </div>
            
            <div class="page-content">
                <div class="card">
                    <div class="card-title">Historial de nóminas</div>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Mes</th>
                                <th style="text-align:left; padding:12px;">Año</th>
                                <th style="text-align:left; padding:12px;">Estado</th>
                                <th style="text-align:left; padding:12px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($nominas) > 0): ?>
                                <?php foreach ($nominas as $nomina): ?>
                                <tr style="border-bottom:1px solid #f5f6fa;">
                                    <td style="padding:12px;"><?= $meses_nombres[$nomina['mes'] - 1] ?></td>
                                    <td style="padding:12px;"><?= $nomina['anio'] ?></td>
                                    <td style="padding:12px;">
                                        <span class="badge <?= $nomina['estado'] == 'generada' ? 'badge-success' : 'badge-warning' ?>">
                                            <?= $nomina['estado'] == 'generada' ? 'Disponible' : 'Pendiente' ?>
                                        </span>
                                    </td>
                                    <td style="padding:12px;">
                                        <?php if ($nomina['estado'] == 'generada'): ?>
                                        <a href="?descargar=<?= $nomina['id'] ?>" class="btn btn-primary" style="padding:6px 12px; font-size:12px;">Descargar PDF</a>
                                        <?php else: ?>
                                        -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:24px; color:#6b7280;">No hay nóminas disponibles aún</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>