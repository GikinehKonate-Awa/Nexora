-- =============================================
-- ACTUALIZACIONES BASE DE DATOS NEXORA
-- Añade campos faltantes para funcionalidades completas
-- =============================================

USE nexora_consulting;

-- 1. Añadir campo horas_estimadas a tabla proyectos
ALTER TABLE proyectos ADD COLUMN horas_estimadas INT DEFAULT 0 AFTER descripcion;

-- 2. Crear tabla para registro de horas imputadas por empleado en proyectos
CREATE TABLE IF NOT EXISTS horas_proyectos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT NOT NULL,
    proyecto_id INT NOT NULL,
    fecha DATE NOT NULL,
    horas DECIMAL(4,2) NOT NULL,
    descripcion TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    INDEX idx_fecha (fecha),
    INDEX idx_empleado_proyecto (empleado_id, proyecto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Añadir campo modalidad a fichajes
ALTER TABLE fichajes ADD COLUMN modalidad ENUM('presencial','teletrabajo') DEFAULT 'presencial' AFTER tipo;

-- 4. Crear tabla festivos
CREATE TABLE IF NOT EXISTS festivos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE UNIQUE NOT NULL,
    descripcion VARCHAR(150) NULL,
    nacional TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Añadir campos a tabla notificaciones para incumplimientos
ALTER TABLE notificaciones ADD COLUMN tipo_incumplimiento ENUM('retraso','salida_anticipada','horas_insuficientes','ausencia','fichaje_manual') NULL AFTER leida;
ALTER TABLE notificaciones ADD COLUMN fecha_incumplimiento DATE NULL AFTER tipo_incumplimiento;
ALTER TABLE notificaciones ADD UNIQUE KEY uk_notificacion_incumplimiento (empleado_id, tipo_incumplimiento, fecha_incumplimiento);

-- 6. Añadir campo coste_hora a empleados
ALTER TABLE empleados ADD COLUMN coste_hora DECIMAL(8,2) DEFAULT 0 AFTER modalidad;