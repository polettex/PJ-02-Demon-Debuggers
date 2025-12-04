-- =========================================
-- CREACIÓN DE LA BASE DE DATOS
-- =========================================
DROP DATABASE IF EXISTS db_restaurante;
CREATE DATABASE IF NOT EXISTS db_restaurante;
USE db_restaurante;

-- =========================================
-- 1. CREACIÓN DE TABLAS
-- =========================================

-- Tabla de roles
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) UNIQUE NOT NULL
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    user VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL
);

-- Tabla de recursos
CREATE TABLE recursos (
    id_recurso INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('sala','mesa','silla') NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    capacidad INT,
    estado ENUM('libre','ocupado') DEFAULT 'libre',
    imagen VARCHAR(255)
);

-- Tabla de reservas
CREATE TABLE reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_recurso INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_final TIME
);

-- Tabla de imágenes (opcional, para múltiples imágenes por recurso)
CREATE TABLE imagenes (
    id_imagen INT AUTO_INCREMENT PRIMARY KEY,
    id_recurso INT NOT NULL,
    ruta VARCHAR(255) NOT NULL
);

-- Tabla de jerarquía de recursos (para relacionar mesas con salas)
CREATE TABLE recursos_jerarquia (
    id_jerarquia INT AUTO_INCREMENT PRIMARY KEY,
    id_recurso_hijo INT NOT NULL,
    id_recurso_padre INT NOT NULL
);


-- =========================================
-- 2. RELACIONES ENTRE TABLAS (ALTER TABLE)
-- =========================================

-- Relación usuarios -> roles
ALTER TABLE usuarios
ADD CONSTRAINT fk_usuarios_roles
FOREIGN KEY (id_rol) REFERENCES roles(id_rol);

-- Relación reservas -> usuarios
ALTER TABLE reservas
ADD CONSTRAINT fk_reservas_usuarios
FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario);

-- Relación reservas -> recursos
ALTER TABLE reservas
ADD CONSTRAINT fk_reservas_recursos
FOREIGN KEY (id_recurso) REFERENCES recursos(id_recurso);

-- Relación imagenes -> recursos
ALTER TABLE imagenes
ADD CONSTRAINT fk_imagenes_recursos
FOREIGN KEY (id_recurso) REFERENCES recursos(id_recurso);

-- Relación recursos_jerarquia -> recursos (hijo)
ALTER TABLE recursos_jerarquia
ADD CONSTRAINT fk_jerarquia_hijo
FOREIGN KEY (id_recurso_hijo) REFERENCES recursos(id_recurso);

-- Relación recursos_jerarquia -> recursos (padre)
ALTER TABLE recursos_jerarquia
ADD CONSTRAINT fk_jerarquia_padre
FOREIGN KEY (id_recurso_padre) REFERENCES recursos(id_recurso);


-- =========================================
-- 3. INSERTS DE PRUEBA
-- =========================================

-- Roles
INSERT INTO roles (nombre) VALUES
('camarero'),
('gerente'),
('mantenimiento'),
('administrador');

-- Usuarios de prueba con roles y contraseña BCRYPT (qazQAZ123)

INSERT INTO usuarios (nombre, apellidos, user, password, id_rol) VALUES
-- Administrador
('Admin', 'Principal', 'admin', '$2a$12$cn4R87re7RrNCkxq4sB68ux1ZekkPJrCN0BYT2h8EZr/a4vghrOQS', 4),

-- Gerente
('Gerardo', 'López', 'gerente', '$2a$12$cn4R87re7RrNCkxq4sB68ux1ZekkPJrCN0BYT2h8EZr/a4vghrOQS', 2),

-- Mantenimiento
('Manuel', 'Torres', 'mantenimiento1', '$2a$12$cn4R87re7RrNCkxq4sB68ux1ZekkPJrCN0BYT2h8EZr/a4vghrOQS', 3),
('Marta', 'Serrano', 'mantenimiento2', '$2a$12$cn4R87re7RrNCkxq4sB68ux1ZekkPJrCN0BYT2h8EZr/a4vghrOQS', 3),

-- Camareros
('Luis', 'García', 'camarero1', '$2a$12$cn4R87re7RrNCkxq4sB68ux1ZekkPJrCN0BYT2h8EZr/a4vghrOQS', 1),
('María', 'Fernández', 'camarero2', '$2a$12$cn4R87re7RrNCkxq4sB68ux1ZekkPJrCN0BYT2h8EZr/a4vghrOQS', 1),
('Carlos', 'Sánchez', 'camarero3', '$2a$12$cn4R87re7RrNCkxq4sB68ux1ZekkPJrCN0BYT2h8EZr/a4vghrOQS', 1),
('Ana', 'López', 'camarero4', '$2a$12$cn4R87re7RrNCkxq4sB68ux1ZekkPJrCN0BYT2h8EZr/a4vghrOQS', 1);


-- Recursos (salas y mesas de ejemplo)
-- Primero insertamos las salas (IDs 1-9)
INSERT INTO recursos (tipo, nombre, capacidad, estado) VALUES
('sala', 'Patio 1', 16, 'libre'),      -- id_recurso = 1
('sala', 'Patio 2', 16, 'libre'),      -- id_recurso = 2
('sala', 'Patio 3', 16, 'libre'),      -- id_recurso = 3
('sala', 'Comedor 1', 32, 'libre'),    -- id_recurso = 4
('sala', 'Comedor 2', 32, 'libre'),    -- id_recurso = 5
('sala', 'Privado 1', 12, 'libre'),    -- id_recurso = 6
('sala', 'Privado 2', 12, 'libre'),    -- id_recurso = 7
('sala', 'Privado 3', 20, 'libre'),    -- id_recurso = 8
('sala', 'Privado 4', 20, 'libre');    -- id_recurso = 9

-- Ahora insertamos las mesas (IDs 10 en adelante)
-- Patio 1: 8 mesas de 2 personas
INSERT INTO recursos (tipo, nombre, capacidad, estado) VALUES
('mesa', 'Mesa 1', 2, 'libre'), ('mesa', 'Mesa 2', 2, 'libre'),
('mesa', 'Mesa 3', 2, 'libre'), ('mesa', 'Mesa 4', 2, 'libre'),
('mesa', 'Mesa 5', 2, 'libre'), ('mesa', 'Mesa 6', 2, 'libre'),
('mesa', 'Mesa 7', 2, 'libre'), ('mesa', 'Mesa 8', 2, 'libre');

-- Patio 2: 8 mesas de 2 personas
INSERT INTO recursos (tipo, nombre, capacidad, estado) VALUES
('mesa', 'Mesa 9', 2, 'libre'), ('mesa', 'Mesa 10', 2, 'libre'),
('mesa', 'Mesa 11', 2, 'libre'), ('mesa', 'Mesa 12', 2, 'libre'),
('mesa', 'Mesa 13', 2, 'libre'), ('mesa', 'Mesa 14', 2, 'libre'),
('mesa', 'Mesa 15', 2, 'libre'), ('mesa', 'Mesa 16', 2, 'libre');

-- Patio 3: 8 mesas de 2 personas
INSERT INTO recursos (tipo, nombre, capacidad, estado) VALUES
('mesa', 'Mesa 17', 2, 'libre'), ('mesa', 'Mesa 18', 2, 'libre'),
('mesa', 'Mesa 19', 2, 'libre'), ('mesa', 'Mesa 20', 2, 'libre'),
('mesa', 'Mesa 21', 2, 'libre'), ('mesa', 'Mesa 22', 2, 'libre'),
('mesa', 'Mesa 23', 2, 'libre'), ('mesa', 'Mesa 24', 2, 'libre');

-- Comedor 1: 8 mesas de 4 personas
INSERT INTO recursos (tipo, nombre, capacidad, estado) VALUES
('mesa', 'Mesa 25', 4, 'libre'), ('mesa', 'Mesa 26', 4, 'libre'),
('mesa', 'Mesa 27', 4, 'libre'), ('mesa', 'Mesa 28', 4, 'libre'),
('mesa', 'Mesa 29', 4, 'libre'), ('mesa', 'Mesa 30', 4, 'libre'),
('mesa', 'Mesa 31', 4, 'libre'), ('mesa', 'Mesa 32', 4, 'libre');

-- Comedor 2: 8 mesas de 4 personas
INSERT INTO recursos (tipo, nombre, capacidad, estado) VALUES
('mesa', 'Mesa 33', 4, 'libre'), ('mesa', 'Mesa 34', 4, 'libre'),
('mesa', 'Mesa 35', 4, 'libre'), ('mesa', 'Mesa 36', 4, 'libre'),
('mesa', 'Mesa 37', 4, 'libre'), ('mesa', 'Mesa 38', 4, 'libre'),
('mesa', 'Mesa 39', 4, 'libre'), ('mesa', 'Mesa 40', 4, 'libre');

-- Privado 1: 1 mesa de 12 personas
INSERT INTO recursos (tipo, nombre, capacidad, estado) VALUES
('mesa', 'Mesa 41', 12, 'libre');

-- Privado 2: 1 mesa de 12 personas
INSERT INTO recursos (tipo, nombre, capacidad, estado) VALUES
('mesa', 'Mesa 42', 12, 'libre');

-- Privado 3: 2 mesas de 10 personas
INSERT INTO recursos (tipo, nombre, capacidad, estado) VALUES
('mesa', 'Mesa 43', 10, 'libre'), ('mesa', 'Mesa 44', 10, 'libre');

-- Privado 4: 2 mesas de 10 personas
INSERT INTO recursos (tipo, nombre, capacidad, estado) VALUES
('mesa', 'Mesa 45', 10, 'libre'), ('mesa', 'Mesa 46', 10, 'libre');

-- Jerarquía de recursos (relacionar mesas con salas)
-- Patio 1 (id_sala=1): mesas 10-17
INSERT INTO recursos_jerarquia (id_recurso_hijo, id_recurso_padre) VALUES
(10, 1), (11, 1), (12, 1), (13, 1), (14, 1), (15, 1), (16, 1), (17, 1);

-- Patio 2 (id_sala=2): mesas 18-25
INSERT INTO recursos_jerarquia (id_recurso_hijo, id_recurso_padre) VALUES
(18, 2), (19, 2), (20, 2), (21, 2), (22, 2), (23, 2), (24, 2), (25, 2);

-- Patio 3 (id_sala=3): mesas 26-33
INSERT INTO recursos_jerarquia (id_recurso_hijo, id_recurso_padre) VALUES
(26, 3), (27, 3), (28, 3), (29, 3), (30, 3), (31, 3), (32, 3), (33, 3);

-- Comedor 1 (id_sala=4): mesas 34-41
INSERT INTO recursos_jerarquia (id_recurso_hijo, id_recurso_padre) VALUES
(34, 4), (35, 4), (36, 4), (37, 4), (38, 4), (39, 4), (40, 4), (41, 4);

-- Comedor 2 (id_sala=5): mesas 42-49
INSERT INTO recursos_jerarquia (id_recurso_hijo, id_recurso_padre) VALUES
(42, 5), (43, 5), (44, 5), (45, 5), (46, 5), (47, 5), (48, 5), (49, 5);

-- Privado 1 (id_sala=6): mesa 50
INSERT INTO recursos_jerarquia (id_recurso_hijo, id_recurso_padre) VALUES
(50, 6);

-- Privado 2 (id_sala=7): mesa 51
INSERT INTO recursos_jerarquia (id_recurso_hijo, id_recurso_padre) VALUES
(51, 7);

-- Privado 3 (id_sala=8): mesas 52-53
INSERT INTO recursos_jerarquia (id_recurso_hijo, id_recurso_padre) VALUES
(52, 8), (53, 8);

-- Privado 4 (id_sala=9): mesas 54-55
INSERT INTO recursos_jerarquia (id_recurso_hijo, id_recurso_padre) VALUES
(54, 9), (55, 9);

-- Reserva de ejemplo
INSERT INTO reservas (id_usuario, id_recurso, fecha, hora_inicio, hora_final) VALUES
(1, 10, '2025-12-05', '20:00:00', '22:00:00');

