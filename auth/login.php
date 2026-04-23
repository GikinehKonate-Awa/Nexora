<?php
require_once '../config.php';
require_once '../includes/auth.php';

if(isLoggedIn()) {
    redirect('../empleado/index.php');
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    
    if(login($email, $password)) {
        if($_SESSION['user_rol'] === 'empleado') {
            redirect('../empleado/index.php');
        } else {
            redirect('../jefe/index.php');
        }
    } else {
        $error = 'Credenciales incorrectas';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXORA - Acceso Sistema</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a2744 0%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .login-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            padding: 48px;
            width: 100%;
            max-width: 420px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div style="text-align:center; margin-bottom: 32px;">
            <h1 style="font-size:36px; font-weight:900; letter-spacing:6px; color:#1a2744;">NEXORA<span style="color:#c9a84c;">.</span></h1>
            <p style="color:#6b7280; font-size:13px; margin-top:8px;">CONSULTING GROUP</p>
            <p style="color:#6b7280; font-size:14px; margin-top:24px;">Acceso al Sistema de Control de Presencia</p>
        </div>
        
        <?php if($error): ?>
        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:24px; text-align:center;">
            <?= $error ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-input" required autofocus>
            </div>
            
            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-large" style="width:100%; margin-top:8px;">
                Acceder al Sistema
            </button>
        </form>
        
        <div style="text-align:center; margin-top:32px; font-size:12px; color:#6b7280;">
            © <?= date('Y') ?> NEXORA CONSULTING GROUP. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>