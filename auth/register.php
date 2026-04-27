<?php
require_once '../config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

if(isLoggedIn()) {
    redirect('../empleado/index.php');
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = cleanInput($_POST['nombre']);
    $apellidos = cleanInput($_POST['apellidos']);
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $rol = cleanInput($_POST['rol']);
    $departamento_id = (int)$_POST['departamento_id'];
    
    // Validaciones
    if($password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden';
    } elseif(strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } else {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM empleados WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0) {
            $error = 'Este correo electrónico ya está registrado';
        } else {
            // Insertar nuevo usuario
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO empleados (nombre, apellidos, email, password, departamento_id, rol) VALUES (?, ?, ?, ?, ?, ?)");
            
            if($stmt->execute([$nombre, $apellidos, $email, $hashed_password, $departamento_id, $rol])) {
                $success = 'Cuenta creada correctamente. Ahora puedes iniciar sesión.';
                header("refresh:2; url=login.php");
            } else {
                $error = 'Error al crear la cuenta. Inténtalo de nuevo.';
            }
        }
    }
}

// Obtener departamentos
$stmt = $pdo->query("SELECT id, nombre FROM departamentos WHERE activo = 1 ORDER BY nombre");
$departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXORA - Crear Cuenta</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a2744 0%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .login-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            padding: 48px;
            width: 100%;
            max-width: 480px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div style="text-align:center; margin-bottom: 32px;">
            <h1 style="font-size:36px; font-weight:900; letter-spacing:6px; color:#1a2744;">NEXORA<span style="color:#c9a84c;">.</span></h1>
            <p style="color:#6b7280; font-size:13px; margin-top:8px;">CONSULTING GROUP</p>
            <p style="color:#6b7280; font-size:14px; margin-top:24px;">Crear Nueva Cuenta</p>
        </div>
        
        <?php if($error): ?>
        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:24px; text-align:center;">
            <?= $error ?>
        </div>
        <?php endif; ?>
        
        <?php if($success): ?>
        <div style="background:#d1fae5; color:#065f46; padding:12px; border-radius:8px; margin-bottom:24px; text-align:center;">
            <?= $success ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Apellidos</label>
                <input type="text" name="apellidos" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Departamento</label>
                <select name="departamento_id" class="form-input" required>
                    <option value="">Seleccionar departamento</option>
                    <?php foreach($departamentos as $dept): ?>
                    <option value="<?= $dept['id'] ?>"><?= $dept['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tipo de Cuenta</label>
                <select name="rol" class="form-input" required>
                    <option value="empleado">Empleado</option>
                    <option value="jefe_departamento">Jefe de Departamento</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirmar Contraseña</label>
                <input type="password" name="password_confirm" class="form-input" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-large" style="width:100%; margin-top:8px;">
                Crear Cuenta
            </button>
        </form>
        
        <div style="text-align:center; margin-top:24px;">
            <a href="login.php" style="color:#1a2744; text-decoration:none; font-size:14px;">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
        
        <div style="text-align:center; margin-top:32px; font-size:12px; color:#6b7280;">
            © <?= date('Y') ?> NEXORA CONSULTING GROUP. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>