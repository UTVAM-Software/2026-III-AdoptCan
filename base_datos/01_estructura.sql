-- Creación e inicialización de la base de datos
CREATE DATABASE IF NOT EXISTS nutrimed_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nutrimed_db;

-- 1. Tabla: Nutriólogo
CREATE TABLE IF NOT EXISTS Nutriologo (
    id_nutriologo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    correo VARCHAR(30) NOT NULL UNIQUE,
    contraseña VARCHAR(10) NOT NULL
);

-- 2. Tabla: Paciente
CREATE TABLE IF NOT EXISTS Paciente (
    id_paciente INT AUTO_INCREMENT PRIMARY KEY,
    id_nutriologo INT NOT NULL,
    nombre VARCHAR(30) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    telefono VARCHAR(30),
    correo VARCHAR(80) UNIQUE,
    sexo VARCHAR(20),
    CONSTRAINT fk_paciente_nutriologo
        FOREIGN KEY (id_nutriologo) REFERENCES Nutriologo(id_nutriologo)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 3. Tabla: Cita
CREATE TABLE IF NOT EXISTS Cita (
    id_cita INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    motivo VARCHAR(30),
    estado VARCHAR(90) DEFAULT 'Pendiente',
    CONSTRAINT fk_cita_paciente
        FOREIGN KEY (id_paciente) REFERENCES Paciente(id_paciente)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 4. Tabla: Consulta
CREATE TABLE IF NOT EXISTS Consulta (
    id_consulta INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    fecha DATE NOT NULL,
    peso DECIMAL(5, 2) NOT NULL,
    estatura DECIMAL(4, 2) NOT NULL,
    observaciones TEXT,
    CONSTRAINT fk_consulta_paciente
        FOREIGN KEY (id_paciente) REFERENCES Paciente(id_paciente)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 5. Tabla: Seguimiento
CREATE TABLE IF NOT EXISTS Seguimiento (
    id_seguimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    fecha DATE NOT NULL,
    peso DECIMAL(5, 2) NOT NULL,
    observaciones TEXT,
    avances TEXT,
    CONSTRAINT fk_seguimiento_paciente
        FOREIGN KEY (id_paciente) REFERENCES Paciente(id_paciente)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 6. Tabla: Alimento
CREATE TABLE IF NOT EXISTS Alimento (
    id_alimento INT AUTO_INCREMENT PRIMARY KEY,
    id_seguimiento INT,
    nombre VARCHAR(30) NOT NULL,
    categoria VARCHAR(100),
    calorias DECIMAL(7, 2),
    descripcion TEXT,
    CONSTRAINT fk_alimento_seguimiento
        FOREIGN KEY (id_seguimiento) REFERENCES Seguimiento(id_seguimiento)
        ON DELETE SET NULL ON UPDATE CASCADE
);

-- 7. Tabla: PlanAlimenticio
CREATE TABLE IF NOT EXISTS PlanAlimenticio (
    id_plan INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    objetivo VARCHAR(30),
    observaciones TEXT
);

-- 8. Tabla: DetallePlanAlimenticio
CREATE TABLE IF NOT EXISTS DetallePlanAlimenticio (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_plan INT NOT NULL,
    id_alimento INT NOT NULL,
    cantidad DECIMAL(7, 2) NOT NULL,
    frecuencia VARCHAR(30),
    observaciones TEXT,
    CONSTRAINT fk_detalle_plan
        FOREIGN KEY (id_plan) REFERENCES PlanAlimenticio(id_plan)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detalle_alimento
        FOREIGN KEY (id_alimento) REFERENCES Alimento(id_alimento)
        ON DELETE CASCADE ON UPDATE CASCADE
);