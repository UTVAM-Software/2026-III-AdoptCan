
-- =====================================================
-- PROYECTO: NUTRISALUD
-- BASE DE DATOS: nutrisalud
-- =====================================================

CREATE DATABASE IF NOT EXISTS nutrisalud
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE nutrisalud;

-- =====================================================
-- TABLA DE ROLES
-- =====================================================

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(150)
) ENGINE=InnoDB;

-- =====================================================
-- TABLA DE USUARIOS
-- =====================================================

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(120) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    estado TINYINT(1) DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- =====================================================
-- TABLA DE PERMISOS
-- =====================================================

CREATE TABLE IF NOT EXISTS permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    descripcion VARCHAR(150)
) ENGINE=InnoDB;

-- =====================================================
-- RELACION ENTRE ROLES Y PERMISOS
-- =====================================================

CREATE TABLE IF NOT EXISTS rol_permiso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol_id INT NOT NULL,
    permiso_id INT NOT NULL,

    UNIQUE (rol_id, permiso_id),

    FOREIGN KEY (rol_id) REFERENCES roles(id),
    FOREIGN KEY (permiso_id) REFERENCES permisos(id)
) ENGINE=InnoDB;

-- =====================================================
-- TABLA DE SERVICIOS
-- =====================================================

CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NULL,
    estado TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABLA DE CITAS
-- =====================================================

CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    servicio_id INT NULL,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(120) NOT NULL,
    telefono VARCHAR(20),
    fecha_preferida DATETIME NULL,
    mensaje TEXT,
    estado VARCHAR(30) DEFAULT 'Pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE SET NULL,

    FOREIGN KEY (servicio_id) REFERENCES servicios(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- TABLA DE SOLICITUDES DE CONSEJOS
-- =====================================================

CREATE TABLE IF NOT EXISTS solicitudes_consejo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(120) NOT NULL,
    objetivo VARCHAR(50) NOT NULL,
    mensaje TEXT NOT NULL,
    estado VARCHAR(30) DEFAULT 'Pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- INSERTAR ROLES INICIALES
-- =====================================================

INSERT IGNORE INTO roles (nombre, descripcion) VALUES
('Administrador', 'Acceso completo al sistema'),
('Empleado', 'Gestion de citas y solicitudes');

-- =====================================================
-- INSERTAR PERMISOS
-- =====================================================

INSERT IGNORE INTO permisos (nombre, descripcion) VALUES
('gestionar_usuarios', 'Administrar usuarios'),
('gestionar_roles', 'Administrar roles'),
('gestionar_permisos', 'Administrar permisos'),
('gestionar_servicios', 'Administrar servicios'),
('gestionar_citas', 'Administrar citas'),
('gestionar_consejos', 'Administrar solicitudes'),
('ver_listados', 'Consultar listados');

-- =====================================================
-- PERMISOS DEL ADMINISTRADOR
-- =====================================================

INSERT IGNORE INTO rol_permiso (rol_id, permiso_id)
SELECT 1, id FROM permisos;

-- =====================================================
-- PERMISOS DEL EMPLEADO
-- =====================================================

INSERT IGNORE INTO rol_permiso (rol_id, permiso_id)
SELECT 2, id FROM permisos
WHERE nombre IN (
    'gestionar_citas',
    'gestionar_consejos',
    'ver_listados'
);
