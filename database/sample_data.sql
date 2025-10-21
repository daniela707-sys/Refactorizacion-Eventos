-- Sample data for testing the survey functionality
USE eventos_db;

-- Insert sample registrations for testing
INSERT INTO registro_asistencia_evento 
(id_evento, id_usuario, nombre_completo, email, telefono, tipo_documento, tipo_poblacion, fecha_nacimiento, asistio, qr) 
VALUES 
(1, '12345678', 'Juan Pérez', 'juan.perez@email.com', '3001234567', 'CC', 1, '1990-05-15', 1, 'QR-1-12345678-1'),
(1, '87654321', 'María García', 'maria.garcia@email.com', '3007654321', 'CC', 2, '1985-08-22', 1, 'QR-1-87654321-2'),
(2, '11223344', 'Carlos López', 'carlos.lopez@email.com', '3009876543', 'CC', 3, '1992-12-10', 1, 'QR-2-11223344-3'),
(3, '44332211', 'Ana Rodríguez', 'ana.rodriguez@email.com', '3005555555', 'TI', 1, '2000-03-18', 1, 'QR-3-44332211-4');

-- Note: These are sample registrations with asistio=1 (attended) so they can fill out surveys