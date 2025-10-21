-- Base de datos para sistema de eventos
CREATE DATABASE IF NOT EXISTS eventos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventos_db;

-- Tabla de eventos
CREATE TABLE eventos (
    id_evento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    ubicacion VARCHAR(255),
    fecha_inicio DATETIME,
    fecha_fin DATETIME,
    aforo_maximo INT DEFAULT 100,
    cupos_disponibles INT DEFAULT 100,
    es_gratuito TINYINT(1) DEFAULT 1,
    precio_entrada DECIMAL(10,2) DEFAULT 0.00,
    imagen VARCHAR(500),
    fotos JSON,
    estado ENUM('activo', 'inactivo', 'cancelado') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
    fecha_nacimiento DATE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    asistio TINYINT(1) DEFAULT 0,
    qr VARCHAR(255) UNIQUE,
    FOREIGN KEY (id_evento) REFERENCES eventos(id_evento) ON DELETE CASCADE,
    INDEX idx_evento (id_evento),
    INDEX idx_usuario (id_usuario),
    INDEX idx_email (email),
    INDEX idx_qr (qr)
);

-- Tabla de encuesta de satisfacción
CREATE TABLE encuesta_satisfaccion (
    id_encuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_registro INT NOT NULL,
    id_evento INT NOT NULL,
    id_usuario VARCHAR(50) NOT NULL,
    experiencia_general TINYINT(1) CHECK (experiencia_general BETWEEN 1 AND 5),
    calidad_ponentes TINYINT(1) CHECK (calidad_ponentes BETWEEN 1 AND 5),
    proceso_registro TINYINT(1) CHECK (proceso_registro BETWEEN 1 AND 5),
    recomendaria TINYINT(1) CHECK (recomendaria BETWEEN 1 AND 5),
    sugerencias TEXT,
    fecha_encuesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_registro) REFERENCES registro_asistencia_evento(id_registro) ON DELETE CASCADE,
    FOREIGN KEY (id_evento) REFERENCES eventos(id_evento) ON DELETE CASCADE,
    INDEX idx_evento_encuesta (id_evento),
    INDEX idx_usuario_encuesta (id_usuario)
);

-- Tabla de tipos de población
CREATE TABLE tipos_poblacion (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Insertar tipos de población
INSERT INTO tipos_poblacion (id_tipo, nombre) VALUES
(1, 'Estudiante'),
(2, 'Emprendedor'),
(3, 'Empresario'),
(4, 'Profesional'),
(5, 'Otro');

-- Insertar eventos de ejemplo
INSERT INTO eventos (nombre, descripcion, ubicacion, fecha_inicio, fecha_fin, aforo_maximo, cupos_disponibles) VALUES
('Taller de Emprendimiento Digital', 'Aprende las bases del emprendimiento en la era digital', 'Auditorio Principal', '2024-02-15 09:00:00', '2024-02-15 17:00:00', 50, 50),
('Conferencia de Innovación', 'Las últimas tendencias en innovación tecnológica', 'Sala de Conferencias', '2024-02-20 14:00:00', '2024-02-20 18:00:00', 100, 100),
('Workshop de Marketing Digital', 'Estrategias efectivas de marketing digital para emprendedores', 'Laboratorio de Cómputo', '2024-02-25 08:00:00', '2024-02-25 12:00:00', 30, 30);