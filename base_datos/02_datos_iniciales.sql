USE nutrimed_db;
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE DetallePlanAlimenticio;
TRUNCATE TABLE PlanAlimenticio;
TRUNCATE TABLE Alimento;
TRUNCATE TABLE Seguimiento;
TRUNCATE TABLE Consulta;
TRUNCATE TABLE Cita;
TRUNCATE TABLE Paciente;
TRUNCATE TABLE Nutriologo;

SET FOREIGN_KEY_CHECKS = 1;
-- Inserción en Nutriologo
INSERT INTO Nutriologo (nombre, correo, contraseña) VALUES
('Dra. Ana López', 'ana.lopez@nutrimed.com', 'Admin123'),
('Dr. Carlos Gómez', 'carlos.g@nutrimed.com', 'Nutri2026');

-- Inserción en Paciente
INSERT INTO Paciente (id_nutriologo, nombre, apellidos, fecha_nacimiento, telefono, correo, sexo) VALUES
(1, 'Juan', 'Pérez Hernández', '1995-04-12', '5551234567', 'juan.perez@email.com', 'Masculino'),
(1, 'María', 'García López', '1998-08-25', '5559876543', 'maria.garcia@email.com', 'Femenino'),
(2, 'Roberto', 'Sánchez Díaz', '1990-11-03', '5554567890', 'roberto.s@email.com', 'Masculino');

-- Inserción en Cita
INSERT INTO Cita (id_paciente, fecha, hora, motivo, estado) VALUES
(1, '2026-10-01', '10:00:00', 'Consulta inicial', 'Confirmada'),
(2, '2026-10-01', '11:30:00', 'Seguimiento mensual', 'Pendiente');

-- Inserción en Consulta
INSERT INTO Consulta (id_paciente, fecha, peso, estatura, observaciones) VALUES
(1, '2026-09-15', 78.50, 1.75000, 'Paciente inicia tratamiento para pérdida de grasa.'),
(2, '2026-09-20', 62.00, 1.62000, 'Control regular, presenta buena evolución.');

-- Inserción en Seguimiento
INSERT INTO Seguimiento (id_paciente, fecha, peso, observaciones, avances) VALUES
(1, '2026-09-22', 1.75000, 'Cumplió dieta al 90%.', 'Reducción de 1kg en primera semana.');

-- Inserción en Alimento
INSERT INTO Alimento (id_seguimiento, nombre, categoria, calorias, descripcion) VALUES
(1, 'Pechuga de Pollo', 'Proteínas', 165.00, 'Pechuga a la plancha sin piel'),
(1, 'Avena en Hojuelas', 'Carbohidratos', 150.00, 'Avena natural cocida en agua'),
(NULL, 'Manzana Verde', 'Frutas', 95.00, 'Manzana mediana fresca');

-- Inserción en PlanAlimenticio
INSERT INTO PlanAlimenticio (nombre, fecha_inicio, fecha_fin, objetivo, observaciones) VALUES
('Plan Deficit Calórico', '2026-09-15', '2026-10-15', 'Perder Peso', 'Tomar al menos 2L de agua al día.'),
('Plan Mantenimiento', '2026-09-20', '2026-10-20', 'Mantener consumo equilibrado.', 'Sin observaciones adicionales.');

-- Inserción en DetallePlanAlimenticio
INSERT INTO DetallePlanAlimenticio (id_plan, id_alimento, cantidad, frecuencia, observaciones) VALUES
(1, 1, 200.00, 'Diaria', 'Consumir en la comida principal'),
(1, 2, 50.00, 'Diaria', 'Consumir en el desayuno'),
(2, 3, 1.00, 'Cada 2 días', 'Colación a media mañana');