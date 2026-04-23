<?php
/**
 * NEXORA CONSULTING GROUP
 * Instalador Automático del Sistema
 */

$lockFile = __DIR__ . '/installed.lock';

// Verificar si ya está instalado
if(file_exists($lockFile)) {
    die("<h1 style='color:#1a2744;text-align:center;margin-top:100px;'>✅ Sistema ya instalado correctamente</h1><p style='text-align:center;'><a href='index.php' style='color:#c9a84c;font-weight:bold;'>Ir a la aplicación</a></p>");
}

$steps = [];
$allOk = true;

// Paso 1: Verificación de requisitos
$steps[] = [
    'name' => 'Verificación PHP 7.4+',
    'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? true : false,
    'message' => 'PHP versión ' . PHP_VERSION
];

$steps[] = [
    'name' => 'Extensión PDO',
    'status' => extension_loaded('pdo') ? true : false,
    'message' => extension_loaded('pdo') ? 'Extensión cargada' : 'Extensión no disponible'
];

$steps[] = [
    'name' => 'Extensión pdo_mysql',
    'status' => extension_loaded('pdo_mysql') ? true : false,
    'message' => extension_loaded('pdo_mysql') ? 'Extensión cargada' : 'Extensión no disponible'
];

$steps[] = [
    'name' => 'Extensión GD',
    'status' => extension_loaded('gd') ? true : false,
    'message' => extension_loaded('gd') ? 'Extensión cargada' : 'Extensión no disponible'
];

// Verificar todos los requisitos
foreach($steps as $step) {
    if(!$step['status']) $allOk = false;
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Paso 2: Crear base de datos e importar estructura
if($allOk && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $db_user = isset($_POST['db_user']) ? cleanInput($_POST['db_user']) : 'root';
    $db_pass = isset($_POST['db_pass']) ? $_POST['db_pass'] : '';
    
    try {
        // Conectar a MySQL sin base de datos
        $pdo = new PDO("mysql:host=localhost:3306;charset=utf8mb4", $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Crear base de datos
        $pdo->exec("CREATE DATABASE IF NOT EXISTS nexora_consulting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo->exec("USE nexora_consulting;");
        
        $steps[] = [
            'name' => 'Base de datos creada',
            'status' => true,
            'message' => 'Base de datos nexora_consulting creada correctamente'
        ];
        
        // Cargar archivo SQL
        $sqlContent = file_get_contents(__DIR__ . '/database.sql');
        
        // Ejecutar comandos SQL
        $pdo->exec($sqlContent);
        
        $steps[] = [
            'name' => 'Estructura y datos importados',
            'status' => true,
            'message' => 'Todas las tablas y datos de prueba importados'
        ];
        
        // Crear carpeta perfiles
        $profileDir = __DIR__ . '/assets/img/perfiles';
        if(!file_exists($profileDir)) {
            mkdir($profileDir, 0755, true);
        }
        
        $steps[] = [
            'name' => 'Directorios creados',
            'status' => true,
            'message' => 'Carpetas de assets y perfiles creadas'
        ];
        
        // Crear archivo .htaccess
        $htaccess = <<<HTACCESS
Options -Indexes

<FilesMatch "config\.php|installed\.lock">
    Order Allow,Deny
    Deny from all
</FilesMatch>

<Directory "includes">
    Order Allow,Deny
    Deny from all
</Directory>

RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
HTACCESS;
        
        file_put_contents(__DIR__ . '/.htaccess', $htaccess);
        
        $steps[] = [
            'name' => 'Archivo .htaccess creado',
            'status' => true,
            'message' => 'Protección de archivos sensibles activada'
        ];
        
        // Actualizar archivo config.php con credenciales
        $configContent = file_get_contents(__DIR__ . '/config.php');
        $configContent = preg_replace("/define\('DB_USER', '.*?'\);/", "define('DB_USER', '$db_user');", $configContent);
        $configContent = preg_replace("/define\('DB_PASS', '.*?'\);/", "define('DB_PASS', '$db_pass');", $configContent);
        file_put_contents(__DIR__ . '/config.php', $configContent);
        
        $steps[] = [
            'name' => 'Archivo de configuración actualizado',
            'status' => true,
            'message' => 'Credenciales guardadas correctamente'
        ];
        
        // Crear archivo de bloqueo
        file_put_contents($lockFile, date('Y-m-d H:i:s') . " - Instalación completada");
        
        $steps[] = [
            'name' => '✅ Instalación finalizada',
            'status' => true,
            'message' => 'Sistema listo para utilizar'
        ];
        
        $installed = true;
        
    } catch(Exception $e) {
        $steps[] = [
            'name' => 'Error durante la instalación',
            'status' => false,
            'message' => $e->getMessage()
        ];
        $allOk = false;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXORA - Instalación</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2744 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .installer-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            padding: 48px;
            max-width: 600px;
            width: 100%;
        }
        
        .logo {
            text-align: center;
            font-size: 32px;
            font-weight: 900;
            letter-spacing: 4px;
            color: #1a2744;
            margin-bottom: 8px;
        }
        
        .logo span {
            color: #c9a84c;
        }
        
        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 40px;
            font-size: 14px;
        }
        
        .step-item {
            display: flex;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .step-status {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            margin-right: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .status-ok {
            background: #10b981;
            color: white;
        }
        
        .status-error {
            background: #ef4444;
            color: white;
        }
        
        .step-text {
            flex: 1;
        }
        
        .step-name {
            font-weight: 600;
            color: #1a2744;
        }
        
        .step-message {
            font-size: 13px;
            color: #6b7280;
            margin-top: 2px;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 32px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #c9a84c;
            color: white;
        }
        
        .btn-primary:hover {
            background: #b89840;
            transform: translateY(-1px);
        }
        
        .credentials {
            background: #f5f6fa;
            border-radius: 12px;
            padding: 24px;
            margin-top: 32px;
        }
        
        .credentials h3 {
            color: #1a2744;
            margin-bottom: 16px;
        }
        
        .credential-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="installer-box">
        <div class="logo">NEXORA<span>.</span></div>
        <div class="subtitle">CONSULTING GROUP - Instalador del Sistema</div>
        
        <?php foreach($steps as $step): ?>
        <div class="step-item">
            <div class="step-status <?= $step['status'] ? 'status-ok' : 'status-error' ?>">
                <?= $step['status'] ? '✓' : '✗' ?>
            </div>
            <div class="step-text">
                <div class="step-name"><?= $step['name'] ?></div>
                <div class="step-message"><?= $step['message'] ?></div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(!isset($installed)): ?>
            <?php if($allOk): ?>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Usuario MySQL</label>
                    <input type="text" name="db_user" class="form-input" value="root" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña MySQL</label>
                    <input type="password" name="db_pass" class="form-input">
                </div>
                <button type="submit" class="btn btn-primary">Iniciar Instalación Completa</button>
            </form>
            <?php else: ?>
            <p style="color:#ef4444; margin-top:24px; text-align:center;">⚠️ Corrige los requisitos antes de continuar</p>
            <?php endif; ?>
        <?php else: ?>
        
        <div class="credentials">
            <h3>✅ Credenciales de acceso</h3>
            
            <div class="credential-row">
                <span><strong>Administrador:</strong></span>
                <span>admin@nexora.com</span>
            </div>
            <div class="credential-row">
                <span><strong>Contraseña:</strong></span>
                <span>NexoraJefe2025!</span>
            </div>
            
            <div class="credential-row" style="margin-top:12px;">
                <span><strong>Empleado:</strong></span>
                <span>marc.puig@nexora.com</span>
            </div>
            <div class="credential-row">
                <span><strong>Contraseña:</strong></span>
                <span>Nexora2025!</span>
            </div>
        </div>
        
        <a href="index.php" class="btn btn-primary" style="text-align:center; text-decoration:none;">Acceder a la aplicación</a>
        
        <?php endif; ?>
        
    </div>
</body>
</html>