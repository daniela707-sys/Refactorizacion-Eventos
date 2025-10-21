-- Base de datos completa para sistema de eventos
CREATE DATABASE IF NOT EXISTS eventos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventos_db;

-- Tabla de categorías
CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    estado TINYINT(1) DEFAULT 1
);

-- Tabla de municipios
CREATE TABLE municipio (
    idmuni INT AUTO_INCREMENT PRIMARY KEY,
    municipio VARCHAR(100) NOT NULL,
    departamento VARCHAR(100) NOT NULL,
    estado TINYINT(1) DEFAULT 1
);

-- Tabla de eventos (actualizada)
CREATE TABLE eventos (
    id_evento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    ubicacion VARCHAR(255),
    municipio INT,
    departamento VARCHAR(100),
    categoria INT,
    tipo ENUM('presencial', 'virtual', 'hibrido') DEFAULT 'presencial',
    fecha_inicio DATETIME,
    fecha_fin DATETIME,
    aforo_maximo INT DEFAULT 100,
    cupos_disponibles INT DEFAULT 100,
    es_gratuito TINYINT(1) DEFAULT 1,
    precio_entrada DECIMAL(10,2) DEFAULT 0.00,
    imagen VARCHAR(500),
    fotos JSON,
    contador_visitas INT DEFAULT 0,
    estado ENUM('activo', 'inactivo', 'cancelado') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (municipio) REFERENCES municipio(idmuni),
    FOREIGN KEY (categoria) REFERENCES categoria(id_categoria)
);

-- Tabla de productos (para categorías)
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    categoria INT,
    estado TINYINT(1) DEFAULT 1,
    FOREIGN KEY (categoria) REFERENCES categoria(id_categoria)
);

-- Tabla de registro de asistencia
CREATE TABLE registro_asistencia_evento (
    id_registro INT AUTO_INCREMENT PRIMARY KEY,
    id_evento INT NOT NULL,
    id_usuario VARCHAR(50) NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    tipo_documento ENUM('CC', 'TI', 'CE', 'PP') DEFAULT 'CC',
    tipo_poblacion INT,
    FOREIGN KEY (tipo_poblacion) REFERENCES tipos_poblacion(id_tipo),
    fecha_nacimiento DATE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    asistio TINYINT(1) DEFAULT 0,
    qr VARCHAR(255) UNIQUE,
    FOREIGN KEY (id_evento) REFERENCES eventos(id_evento) ON DELETE CASCADE
);

-- Tabla de tipos de población
CREATE TABLE tipos_poblacion (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    estado TINYINT(1) DEFAULT 1
);

-- Insertar tipos de población
INSERT INTO tipos_poblacion (nombre) VALUES
('General'),
('Estudiante'),
('Adulto Mayor'),
('Persona con Discapacidad'),
('Madre Cabeza de Familia'),
('Víctima del Conflicto'),
('Comunidad LGBTI'),
('Población Indígena'),
('Población Afrodescendiente');

-- Insertar categorías de ejemplo
INSERT INTO categoria (nombre) VALUES
('Tecnología'),
('Educación'),
('Negocios'),
('Arte y Cultura'),
('Salud'),
('Deportes');

-- Insertar municipios de ejemplo
INSERT INTO municipio (municipio, departamento) VALUES
('Bogotá', 'Cundinamarca'),
('Medellín', 'Antioquia'),
('Cali', 'Valle del Cauca'),
('Barranquilla', 'Atlántico'),
('Cartagena', 'Bolívar');

-- Insertar productos de ejemplo
INSERT INTO productos (nombre, categoria) VALUES
('Curso de Programación', 1),
('Taller de Marketing', 3),
('Conferencia de Arte', 4);

-- Tabla de encuesta de satisfacción
CREATE TABLE encuesta_satisfaccion_evento (
    id_encuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_registro INT NOT NULL,
    id_usuario VARCHAR(50) NOT NULL,
    experiencia_general TINYINT(1) NOT NULL CHECK (experiencia_general BETWEEN 1 AND 5),
    calidad_ponentes TINYINT(1) NOT NULL CHECK (calidad_ponentes BETWEEN 1 AND 5),
    proceso_registro TINYINT(1) NOT NULL CHECK (proceso_registro BETWEEN 1 AND 5),
    recomendaria TINYINT(1) NOT NULL CHECK (recomendaria BETWEEN 1 AND 5),
    sugerencias TEXT,
    fecha_respuesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_registro) REFERENCES registro_asistencia_evento(id_registro) ON DELETE CASCADE
);

-- Insertar eventos de ejemplo
INSERT INTO eventos (nombre, descripcion, ubicacion, municipio, departamento, categoria, tipo, fecha_inicio, fecha_fin, aforo_maximo, cupos_disponibles) VALUES
('Taller de Emprendimiento Digital', 'Aprende las bases del emprendimiento en la era digital', 'Auditorio Principal', 1, 'Cundinamarca', 1, 'presencial', '2024-12-15 09:00:00', '2024-12-15 17:00:00', 50, 50),
('Conferencia de Innovación', 'Las últimas tendencias en innovación tecnológica', 'Sala de Conferencias', 2, 'Antioquia', 1, 'virtual', '2024-12-20 14:00:00', '2024-12-20 18:00:00', 100, 100),
('Workshop de Marketing Digital', 'Estrategias efectivas de marketing digital', 'Laboratorio de Cómputo', 1, 'Cundinamarca', 3, 'hibrido', '2024-12-25 08:00:00', '2024-12-25 12:00:00', 30, 30);