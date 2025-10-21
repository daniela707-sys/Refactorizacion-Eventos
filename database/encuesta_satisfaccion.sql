-- Tabla de encuesta de satisfacción para eventos
USE eventos_db;

-- Crear tabla si no existe
CREATE TABLE IF NOT EXISTS encuesta_satisfaccion_evento (
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