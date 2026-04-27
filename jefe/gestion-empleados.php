<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Solo permitir acceso a jefes y administradores
if(!isLoggedIn()) {
    redirect('../auth/login.php');
    exit;
}

if(!in_array($_SESSION['user_rol'], ['jefe_departamento', 'admin'])) {
    die("Acceso denegado. Esta página solo es para jefes y administradores.");
}

// Obtener conexión PDO
$pdo = getDB();

$error = '';
$success = '';

// Acciones: Añadir empleado
if(isset($_POST['accion']) && $_POST['accion'] === 'añadir') {
    $nombre = cleanInput($_POST['nombre']);
    $apellidos = cleanInput($_POST['apellidos']);
    $email = cleanInput($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $departamento_id = (int)$_POST['departamento_id'];
    $rol = cleanInput($_POST['rol']);
    
    $stmt = $pdo->prepare("INSERT INTO empleados (nombre, apellidos, email, password, departamento_id, rol) VALUES (?, ?, ?, ?, ?, ?)");
    if($stmt->execute([$nombre, $apellidos, $email, $password, $departamento_id, $rol])) {
        $success = 'Empleado añadido correctamente';
    } else {
        $error = 'Error al añadir empleado';
    }
}

// Acción: Eliminar empleado
if(isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $empleado_id = (int)$_POST['empleado_id'];
    
    // No permitir eliminarse a si mismo
    if($empleado_id == $_SESSION['user_id']) {
        $error = 'No puedes eliminarte a ti mismo';
    } else {
        $stmt = $pdo->prepare("DELETE FROM empleados WHERE id = ?");
        if($stmt->execute([$empleado_id])) {
            $success = 'Empleado eliminado correctamente';
        } else {
            $error = 'Error al eliminar empleado';
        }
    }
}

// Obtener todos los empleados
if($_SESSION['user_rol'] === 'admin') {
    // Admin ve todos los empleados
    $stmt = $pdo->query("SELECT e.*, d.nombre as departamento FROM empleados e LEFT JOIN departamentos d ON e.departamento_id = d.id WHERE e.activo = 1 ORDER BY e.apellidos");
} else {
    // Jefe solo ve empleados de su departamento
    $stmt = $pdo->prepare("SELECT e.*, d.nombre as departamento FROM empleados e LEFT JOIN departamentos d ON e.departamento_id = d.id WHERE e.departamento_id = ? AND e.activo = 1 ORDER BY e.apellidos");
    $stmt->execute([$_SESSION['user_departamento']]);
}
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener departamentos
$stmt = $pdo->query("SELECT id, nombre FROM departamentos WHERE activo = 1 ORDER BY nombre");
$departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados - NEXORA</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <?php include '../includes/sidebar_jefe.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Gestión de Empleados</h1>
                <p>Administra los empleados del sistema</p>
            </div>
            
            <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <!-- Formulario para añadir empleado -->
            <div class="card">
                <div class="card-header">
                    <h3>Añadir Nuevo Empleado</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="accion" value="añadir">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-input" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Apellidos</label>
                                    <input type="text" name="apellidos" class="form-input" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-input" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Departamento</label>
                                    <select name="departamento_id" class="form-input" required>
                                        <?php foreach($departamentos as $dept): ?>
                                        <option value="<?= $dept['id'] ?>"><?= $dept['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Rol</label>
                                    <select name="rol" class="form-input" required>
                                        <option value="empleado">Empleado</option>
                                        <option value="jefe_departamento">Jefe Departamento</option>
                                        <?php if($_SESSION['user_rol'] === 'admin'): ?>
                                        <option value="admin">Administrador</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Contraseña</label>
                                    <input type="password" name="password" class="form-input" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary" style="width:100%">Añadir Empleado</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Lista de empleados -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3>Listado de Empleados (<?= count($empleados) ?>)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Departamento</th>
                                    <th>Rol</th>
                                    <th>Último Acceso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($empleados as $emp): ?>
                                <tr>
                                    <td><?= $emp['nombre'] ?> <?= $emp['apellidos'] ?></td>
                                    <td><?= $emp['email'] ?></td>
                                    <td><?= $emp['departamento'] ?></td>
                                    <td>
                                        <?php 
                                            switch($emp['rol']) {
                                                case 'empleado': echo '<span class="badge badge-secondary">Empleado</span>'; break;
                                                case 'jefe_departamento': echo '<span class="badge badge-primary">Jefe</span>'; break;
                                                case 'admin': echo '<span class="badge badge-danger">Admin</span>'; break;
                                            }
                                        ?>
                                    </td>
                                    <td><?= $emp['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($emp['ultimo_acceso'])) : 'Nunca' ?></td>
                                    <td>
                                        <?php if($emp['id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este empleado?');" style="display:inline;">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="empleado_id" value="<?= $emp['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>