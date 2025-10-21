-- Insertar eventos con fechas futuras para pruebas
USE eventos_db;

INSERT INTO eventos (nombre, descripcion, ubicacion, municipio, departamento, categoria, tipo, fecha_inicio, fecha_fin, aforo_maximo, cupos_disponibles) VALUES
('Conferencia Tech 2025', 'Últimas tendencias en tecnología', 'Centro de Convenciones', 1, 'Cundinamarca', 1, 'presencial', '2025-11-15 09:00:00', '2025-11-15 17:00:00', 200, 200),
('Workshop IA Práctica', 'Taller práctico de inteligencia artificial', 'Laboratorio Tech', 2, 'Antioquia', 1, 'virtual', '2025-11-20 14:00:00', '2025-11-20 18:00:00', 50, 50),
('Feria de Emprendimiento', 'Muestra de proyectos emprendedores', 'Plaza Central', 1, 'Cundinamarca', 3, 'hibrido', '2025-12-01 08:00:00', '2025-12-01 20:00:00', 500, 500);