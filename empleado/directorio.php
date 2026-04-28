<?php
require_once '../config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireAuth();
requireRole(['empleado']);

$db = getDB();
$busqueda = '';
$resultados = [];

// Por defecto mostrar TODOS los empleados activos
$sql = "SELECT e.nombre, e.apellidos, e.email, e.modalidad, d.nombre as departamento 
        FROM empleados e 
        LEFT JOIN departamentos d ON e.departamento_id = d.id 
        WHERE e.activo = 1 ";
$params = [];

if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))) {
    $busqueda = trim($_GET['buscar']);
    $sql .= "AND (e.nombre LIKE ? OR e.apellidos LIKE ?) ";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

$sql .= "ORDER BY e.apellidos ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio Empleados</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Directorio Empresa</h2>
            </div>
            
            <div class="page-content">
                <div class="card">
                    <div class="card-title">Buscar empleados</div>
                    <form method="GET" style="display:flex; gap:12px; margin-top:16px;">
                        <input type="text" name="buscar" class="form-input" placeholder="Introduce nombre o apellidos..." value="<?= htmlspecialchars($busqueda) ?>" style="flex:1;">
                        <button type="submit" class="btn btn-primary">🔍 Buscar</button>
                    </form>
                </div>

        <div class="card" style="margin-top:24px;">
            <div class="card-title">
                <?php if ($busqueda): ?>
                    Resultados de búsqueda (<?= count($resultados) ?>)
                <?php else: ?>
                    Todos los empleados (<?= count($resultados) ?>)
                <?php endif; ?>
            </div>
            
            <?php if (count($resultados) > 0): ?>
                    <table style="width:100%; margin-top:16px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left; padding:12px;">Nombre</th>
                                <th style="text-align:left; padding:12px;">Departamento</th>
                                <th style="text-align:left; padding:12px;">Email</th>
                                <th style="text-align:left; padding:12px;">Modalidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultados as $empleado): ?>
                            <tr style="border-bottom:1px solid #f5f6fa;">
                                <td style="padding:12px; font-weight:500;"><?= $empleado['nombre'] ?> <?= $empleado['apellidos'] ?></td>
                                <td style="padding:12px;"><?= $empleado['departamento'] ?></td>
                                <td style="padding:12px;"><?= $empleado['email'] ?></td>
                                <td style="padding:12px;">
                                    <span class="badge badge-info"><?= ucfirst($empleado['modalidad']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div style="text-align:center; padding:32px; color:#6b7280;">
                        No se han encontrado empleados con el término "<?= htmlspecialchars($busqueda) ?>"
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</body>
</html>