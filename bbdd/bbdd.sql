-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS db_restaurante;
USE db_restaurante;

-- Tabla de salas
CREATE TABLE salas (
    id_sala INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    capacidad_total INT NOT NULL
);

-- Tabla de mesas
CREATE TABLE mesas (
    id_mesa INT AUTO_INCREMENT PRIMARY KEY,
    id_sala INT NOT NULL,
    capacidad INT NOT NULL,
    estado ENUM('libre','ocupada') DEFAULT 'libre'
);

-- Tabla de camareros
CREATE TABLE camareros (
    id_camarero INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    user VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Tabla de ocupaciones
CREATE TABLE ocupaciones (
    id_ocupacion INT AUTO_INCREMENT PRIMARY KEY,
    id_mesa INT NOT NULL,
    id_camarero INT NOT NULL,
    hora_inicio DATETIME NOT NULL,
    hora_final DATETIME
);

-- Relación mesas -> salas
ALTER TABLE mesas
ADD CONSTRAINT fk_mesas_salas
FOREIGN KEY (id_sala) REFERENCES salas(id_sala);

-- Relación ocupaciones -> taules
ALTER TABLE ocupaciones
ADD CONSTRAINT fk_ocupaciones_mesas
FOREIGN KEY (id_mesa) REFERENCES mesas(id_mesa);

-- Relación ocupaciones -> camareros
ALTER TABLE ocupaciones
ADD CONSTRAINT fk_ocupaciones_camareros
FOREIGN KEY (id_camarero) REFERENCES camareros(id_camarero);

-- Inserts

-- CAMAREROS
INSERT INTO camareros (nombre, apellidos, user, password) VALUES ('Luis', 'García', 'lgarcia', '$2b$12$4ee5sJ4ff0PVR8rpjs0sOOowdOOskwYw5MTrLAr3OnMWxmchllKDi');
INSERT INTO camareros (nombre, apellidos, user, password) VALUES ('María', 'Fernández', 'mfernandez', '$2b$12$9WJoB.8fO15PVIMRighTp.K3Qh53cgjAQw2nX7wbRgJoMI41n73VW');
INSERT INTO camareros (nombre, apellidos, user, password) VALUES ('Carlos', 'Sánchez', 'csanchez', '$2b$12$cWNR7D./WIlfn0bEjs9rke3Nk1GWj9pkwRMicj0j5.yVcJAyBOSou');
INSERT INTO camareros (nombre, apellidos, user, password) VALUES ('Ana', 'López', 'alopez', '$2b$12$n7oau1k1eAFWNvNmbG1KhOoZ0m.z7aU6oc1xg/VFK5Fd36QlY5qX.');
INSERT INTO camareros (nombre, apellidos, user, password) VALUES ('Jorge', 'Martínez', 'jmartinez', '$2b$12$4Jd.eYaP4HWJbYP.ZY4HmOdBE4XWSvDoAGciJlvoW75s9PI8Htz8u');
INSERT INTO camareros (nombre, apellidos, user, password) VALUES ('Lucía', 'Rodríguez', 'lrodriguez', '$2b$12$/bawJmMRtrch1yTAK0ha4OEyE7VyCoUppMHzADaWfqEEgkGvZIxRG');
INSERT INTO camareros (nombre, apellidos, user, password) VALUES ('Miguel', 'Hernández', 'mhernandez', '$2b$12$7BIkiyFaSuwvzakbkOmb1ODgVNzcvbdPFp7G6MooXxJjhgww9KJKO');
INSERT INTO camareros (nombre, apellidos, user, password) VALUES ('Sara', 'Gómez', 'sgomez', '$2b$12$6rxlnldztGOAX0cmNR7EPu6CRc27og4cjJeSRoo3809AOee2WspwK');
INSERT INTO camareros (nombre, apellidos, user, password) VALUES ('Diego', 'Pérez', 'dperez', '$2b$12$TUbMklKCZvg0Thuah11EouN7giZ2VkwQ04dEfOAqb6K1KvpDEH3FS');
INSERT INTO camareros (nombre, apellidos, user, password) VALUES ('Elena', 'Ruiz', 'eruiz', '$2b$12$5YRmtFNPs8BDe9FodGJ.FeQmEVs2P3ltSEU9q1ZCQOR09mJgRf70e');
-- lgarcia	Camarero1#2025
-- mfernandez	Camarero2#2025
-- csanchez	Camarero3#2025
-- alopez	Camarero4#2025
-- jmartinez	Camarero5#2025
-- lrodriguez	Camarero6#2025
-- mhernandez	Camarero7#2025
-- sgomez	Camarero8#2025
-- dperez	Camarero9#2025
-- eruiz	Camarero10#2025
-- SALAS
INSERT INTO salas (nombre, capacidad_total) VALUES
('Patio 1', 8 * 2),
('Patio 2', 8 * 2),
('Patio 3', 8 * 2),
('Comedor 1', 8 * 4),
('Comedor 2', 8 * 4),
('Privado 1', 1 * 12),
('Privado 2', 1 * 12),
('Privado 3', 2 * 10),
('Privado 4', 2 * 10);
--  MESAS
--   Patio 1: 8 mesas de 2
INSERT INTO mesas (id_sala, capacidad, estado) VALUES
(1, 2, 'libre'), (1, 2, 'libre'), (1, 2, 'libre'), (1, 2, 'libre'),
(1, 2, 'libre'), (1, 2, 'libre'), (1, 2, 'libre'), (1, 2, 'libre');

--  Patio 2: 8 mesas de 2
INSERT INTO mesas (id_sala, capacidad, estado) VALUES
(2, 2, 'libre'), (2, 2, 'libre'), (2, 2, 'libre'), (2, 2, 'libre'),
(2, 2, 'libre'), (2, 2, 'libre'), (2, 2, 'libre'), (2, 2, 'libre');

--  Patio 3: 8 mesas de 2
INSERT INTO mesas (id_sala, capacidad, estado) VALUES
(3, 2, 'libre'), (3, 2, 'libre'), (3, 2, 'libre'), (3, 2, 'libre'),
(3, 2, 'libre'), (3, 2, 'libre'), (3, 2, 'libre'), (3, 2, 'libre');

--  Comedor 1: 8 mesas de 4
INSERT INTO mesas (id_sala, capacidad, estado) VALUES
(4, 4, 'libre'), (4, 4, 'libre'), (4, 4, 'libre'), (4, 4, 'libre'),
(4, 4, 'libre'), (4, 4, 'libre'), (4, 4, 'libre'), (4, 4, 'libre');

--  Comedor 2: 8 mesas de 4
INSERT INTO mesas (id_sala, capacidad, estado) VALUES
(5, 4, 'libre'), (5, 4, 'libre'), (5, 4, 'libre'), (5, 4, 'libre'),
(5, 4, 'libre'), (5, 4, 'libre'), (5, 4, 'libre'), (5, 4, 'libre');

--  Privado 1: 1 mesa de 12
INSERT INTO mesas (id_sala, capacidad, estado) VALUES
(6, 12, 'libre');

--  Privado 2: 1 mesa de 12
INSERT INTO mesas (id_sala, capacidad, estado) VALUES
(7, 12, 'libre');

--  Privado 3: 2 mesas de 10
INSERT INTO mesas (id_sala, capacidad, estado) VALUES
(8, 10, 'libre'), (8, 10, 'libre');


--  Privado 4: 2 mesas de 10
INSERT INTO mesas (id_sala, capacidad, estado) VALUES
(9, 10, 'libre'), (9, 10, 'libre');


