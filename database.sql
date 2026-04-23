SET FOREIGN_KEY_CHECKS=0;

-- =============================================
-- NEXORA CONSULTING GROUP
-- Base de Datos Sistema de Fichaje
-- =============================================

DROP DATABASE IF EXISTS nexora_consulting;
CREATE DATABASE IF NOT EXISTS nexora_consulting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexora_consulting;

-- 1. Tabla departamentos
CREATE TABLE IF NOT EXISTS departamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabla empleados
CREATE TABLE IF NOT EXISTS empleados (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    departamento_id INT NOT NULL,
    rol ENUM('empleado','jefe_departamento','admin') DEFAULT 'empleado',
    modalidad ENUM('presencial','teletrabajo','hibrido') DEFAULT 'presencial',
    foto VARCHAR(255) DEFAULT NULL,
    fecha_ingreso DATE NULL,
    activo TINYINT(1) DEFAULT 1,
    ultimo_acceso TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla horarios
CREATE TABLE IF NOT EXISTS horarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT NOT NULL,
    dia_semana TINYINT(1) NOT NULL COMMENT '0=Domingo, 1=Lunes ... 6=Sabado',
    hora_entrada TIME NULL,
    hora_salida TIME NULL,
    teletrabajo TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabla proyectos
CREATE TABLE IF NOT EXISTS proyectos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    departamento_id INT NOT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabla empleados_proyectos
CREATE TABLE IF NOT EXISTS empleados_proyectos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT NOT NULL,
    proyecto_id INT NOT NULL,
    fecha_asignacion DATE NULL,
    horas_asignadas INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    UNIQUE KEY uk_empleado_proyecto (empleado_id, proyecto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabla fichajes
CREATE TABLE IF NOT EXISTS fichajes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT NOT NULL,
    tipo ENUM('entrada','salida') NOT NULL,
    fecha_hora DATETIME NOT NULL,
    ip_origen VARCHAR(45) NOT NULL,
    vpn_detectada TINYINT(1) DEFAULT 0,
    es_manual TINYINT(1) DEFAULT 0,
    comentario TEXT NULL,
    validado TINYINT(1) DEFAULT 0,
    validador_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (validador_id) REFERENCES empleados(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Tabla horas_extras
CREATE TABLE IF NOT EXISTS horas_extras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT NOT NULL,
    fecha DATE NOT NULL,
    cantidad DECIMAL(4,2) NOT NULL,
    motivo TEXT NULL,
    estado ENUM('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
    validador_id INT NULL,
    comentario_validador TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (validador_id) REFERENCES empleados(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Tabla solicitudes
CREATE TABLE IF NOT EXISTS solicitudes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT NOT NULL,
    tipo ENUM('horario','modalidad','permiso','vacaciones') NOT NULL,
    datos JSON NULL,
    estado ENUM('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
    validador_id INT NULL,
    comentario_validador TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (validador_id) REFERENCES empleados(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Tabla notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    leida TINYINT(1) DEFAULT 0,
    fecha_leida TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Tabla nominas
CREATE TABLE IF NOT EXISTS nominas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT NOT NULL,
    mes TINYINT(2) NOT NULL,
    anio INT NOT NULL,
    archivo VARCHAR(255) NULL,
    estado ENUM('pendiente','generada','enviada') DEFAULT 'pendiente',
    fecha_generacion TIMESTAMP NULL,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    UNIQUE KEY uk_nomina_mes (empleado_id, mes, anio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Tabla logs_sistema
CREATE TABLE IF NOT EXISTS logs_sistema (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT NULL,
    accion VARCHAR(150) NOT NULL,
    detalle TEXT NULL,
    ip_origen VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- DATOS DE PRUEBA
-- =============================================

-- 1. Departamentos
INSERT INTO departamentos (id, nombre, descripcion, activo) VALUES
(1, 'Dirección', 'Departamento de Dirección General', 1),
(2, 'Desarrollo', 'Departamento de Desarrollo de Software', 1),
(3, 'Contabilidad', 'Departamento de Contabilidad y Finanzas', 1),
(4, 'RRHH', 'Recursos Humanos', 1);

-- 2. Empleados
-- Contraseñas:
-- Nexora2025! -> $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- NexoraJefe2025! -> $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO empleados (id, nombre, apellidos, email, password, departamento_id, rol, modalidad) VALUES
(1, 'Marc', 'Puig Ferrer', 'marc.puig@nexora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'empleado', 'hibrido'),
(2, 'Laura', 'Gómez Vidal', 'laura.gomez@nexora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'empleado', 'presencial'),
(3, 'Sofía', 'Martín Ros', 'sofia.martin@nexora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 'empleado', 'teletrabajo'),
(4, 'Andreu', 'Sala Mas', 'andreu.sala@nexora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'empleado', 'presencial'),

(5, 'Elena', 'Torres Bravo', 'elena.torres@nexora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'jefe_departamento', 'presencial'),
(6, 'Víctor', 'Ruiz Camps', 'victor.ruiz@nexora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'jefe_departamento', 'hibrido'),
(7, 'Núria', 'Costa Prat', 'nuria.costa@nexora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 'jefe_departamento', 'teletrabajo'),
(8, 'Jordi', 'Font Molina', 'jordi.font@nexora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'jefe_departamento', 'presencial'),

(9, 'Admin', 'Sistema Nexora', 'admin@nexora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 'admin', 'presencial');

SET FOREIGN_KEY_CHECKS=1;