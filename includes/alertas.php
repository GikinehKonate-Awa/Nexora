<?php

define('WORK_HOURS_PER_DAY', 8);
define('MINUTOS_RETRASO_PERMITIDO', 15);
define('MINUTOS_SALIDA_ANTICIPADA_PERMITIDO', 30);

function check_incumplimientos($fecha = null) {
    global $pdo;
    
    if ($fecha === null) {
        $fecha = date('Y-m-d');
    }
    
    $dia_semana = date('w', strtotime($fecha));
    $incumplimientos = [];
    
    // Verificar si es festivo
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM festivos WHERE fecha = ?");
    $stmt->execute([$fecha]);
    if ($stmt->fetchColumn() > 0) {
        return [];
    }
    
    // Obtener todos los empleados activos
    $empleados = $pdo->query("SELECT id, nombre, apellidos, departamento_id FROM empleados WHERE activo = 1")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($empleados as $empleado) {
        $empleado_id = $empleado['id'];
        
        // Obtener horario del empleado para este dia
        $stmt = $pdo->prepare("SELECT hora_entrada, hora_salida FROM horarios WHERE empleado_id = ? AND dia_semana = ? AND activo = 1");
        $stmt->execute([$empleado_id, $dia_semana]);
        $horario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$horario || empty($horario['hora_entrada'])) {
            continue; // Empleado no trabaja este dia
        }
        
        // Obtener fichajes del dia
        $stmt = $pdo->prepare("
            SELECT tipo, fecha_hora 
            FROM fichajes 
            WHERE empleado_id = ? AND DATE(fecha_hora) = ?
            ORDER BY fecha_hora ASC
        ");
        $stmt->execute([$empleado_id, $fecha]);
        $fichajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $hora_entrada_horario = strtotime($fecha . ' ' . $horario['hora_entrada']);
        $hora_salida_horario = strtotime($fecha . ' ' . $horario['hora_salida']);
        
        // 1. AUSENCIA SIN JUSTIFICAR
        if (count($fichajes) == 0) {
            $incumplimientos[] = [
                'empleado_id' => $empleado_id,
                'nombre' => $empleado['nombre'] . ' ' . $empleado['apellidos'],
                'tipo' => 'ausencia',
                'descripcion' => 'Ausencia sin justificar',
                'departamento_id' => $empleado['departamento_id']
            ];
            continue;
        }
        
        // 2. RETRASO
        $entrada = array_filter($fichajes, fn($f) => $f['tipo'] == 'entrada');
        if (count($entrada) > 0) {
            $entrada = reset($entrada);
            $hora_entrada_real = strtotime($entrada['fecha_hora']);
            $minutos_retraso = ($hora_entrada_real - $hora_entrada_horario) / 60;
            
            if ($minutos_retraso > MINUTOS_RETRASO_PERMITIDO) {
                $incumplimientos[] = [
                    'empleado_id' => $empleado_id,
                    'nombre' => $empleado['nombre'] . ' ' . $empleado['apellidos'],
                    'tipo' => 'retraso',
                    'minutos' => round($minutos_retraso),
                    'descripcion' => 'Retraso de ' . round($minutos_retraso) . ' minutos',
                    'departamento_id' => $empleado['departamento_id']
                ];
            }
        }
        
        // 3. SALIDA ANTICIPADA
        $salida = array_filter($fichajes, fn($f) => $f['tipo'] == 'salida');
        if (count($salida) > 0) {
            $salida = end($salida);
            $hora_salida_real = strtotime($salida['fecha_hora']);
            $minutos_anticipados = ($hora_salida_horario - $hora_salida_real) / 60;
            
            if ($minutos_anticipados > MINUTOS_SALIDA_ANTICIPADA_PERMITIDO) {
                $incumplimientos[] = [
                    'empleado_id' => $empleado_id,
                    'nombre' => $empleado['nombre'] . ' ' . $empleado['apellidos'],
                    'tipo' => 'salida_anticipada',
                    'minutos' => round($minutos_anticipados),
                    'descripcion' => 'Salida anticipada ' . round($minutos_anticipados) . ' minutos',
                    'departamento_id' => $empleado['departamento_id']
                ];
            }
        }
    }
    
    return $incumplimientos;
}

function registrar_notificacion_incumplimiento($empleado_id, $tipo, $fecha, $mensaje) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO notificaciones 
            (empleado_id, titulo, mensaje, tipo_incumplimiento, fecha_incumplimiento)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $titulos = [
            'retraso' => '⚠️ Retraso detectado',
            'salida_anticipada' => '⚠️ Salida anticipada',
            'horas_insuficientes' => '⚠️ Horas insuficientes',
            'ausencia' => '❌ Ausencia detectada',
            'fichaje_manual' => '⚠️ Fichaje manual pendiente'
        ];
        
        $stmt->execute([
            $empleado_id,
            $titulos[$tipo] ?? 'Incumplimiento detectado',
            $mensaje,
            $tipo,
            $fecha
        ]);
        
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}