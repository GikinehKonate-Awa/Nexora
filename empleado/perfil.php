<?php
require_once '../config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireAuth();
requireRole(['empleado']);

$db = getDB();
$usuario_id = $_SESSION['user_id'];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    
    if (empty($nombre) || empty($apellidos)) {
        $error = "Debes completar todos los campos";
    } else {
        $stmt = $db->prepare("UPDATE empleados SET nombre = ?, apellidos = ? WHERE id = ?");
        $stmt->execute([$nombre, $apellidos, $usuario_id]);
        
        // Actualizar sesión
        $_SESSION['user_nombre'] = $nombre;
        $_SESSION['user_apellidos'] = $apellidos;
        
        header("Location: perfil.php?mensaje=Datos actualizados correctamente");
        exit;
    }
}

$stmt = $db->prepare("SELECT e.*, d.nombre as departamento FROM empleados e LEFT JOIN departamentos d ON e.departamento_id = d.id WHERE e.id = ?");
$stmt->execute([$usuario_id]);
$empleado = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="app-container">
        <?php include '../includes/sidebar_empleado.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h2>Mi Perfil</h2>
            </div>
            
            <div class="page-content">
                <div class="grid grid-2">
                    <div class="card" style="text-align:center;">
                        <div style="width:120px; height:120px; border-radius:50%; background:linear-gradient(135deg, #1a2744, #c9a84c); margin:0 auto 24px auto; display:flex; align-items:center; justify-content:center;">
                            <span style="color:white; font-size:48px; font-weight:bold;"><?= substr($_SESSION['user_nombre'], 0, 1) ?><?= substr($_SESSION['user_apellidos'], 0, 1) ?></span>
                        </div>
                        <h3><?= $_SESSION['user_nombre'] ?> <?= $_SESSION['user_apellidos'] ?></h3>
                        <p style="color:#6b7280; margin-bottom:16px;"><?= $_SESSION['user_email'] ?></p>
                        <span class="badge badge-info"><?= $_SESSION['user_modalidad'] ?></span>
                    </div>
                    
                    <div class="card">
                        <div class="card-title">Datos personales</div>
                        <div style="margin-top:16px;">
                            <form method="POST">
                                <div class="form-group">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-input" value="<?= $empleado['nombre'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Apellidos</label>
                                    <input type="text" name="apellidos" class="form-input" value="<?= $empleado['apellidos'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Departamento</label>
                                    <input type="text" class="form-input" value="<?= $empleado['departamento'] ?>" disabled readonly>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
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
