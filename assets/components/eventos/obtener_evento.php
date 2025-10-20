<?php
// obtener_evento.php
header('Content-Type: application/json');
@ini_set("display_errors","0");
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'obtener_evento') {
    try {
        // Incluir archivos necesarios
        require_once("../include/dbcommon.php");
        
        // Verificar conexión
        if (!isset($conn) || $conn->connect_error) {
            throw new Exception("Error de conexión a la base de datos");
        }
        
        $evento_id = intval($_POST['evento_id'] ?? 0);
        
        if ($evento_id <= 0) {
            throw new Exception("ID de evento inválido");
        }
        
        // Obtener información del evento
        $query = "SELECT id_evento, nombre, descripcion, ubicacion, fecha_inicio, fecha_fin, 
                         aforo_maximo, cupos_disponibles, estado 
                  FROM eventos 
                  WHERE id_evento = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $evento_id);
        $stmt->execute();
        $evento = $stmt->get_result()->fetch_assoc();
        
        if (!$evento) {
            // Si no existe, crear un evento de ejemplo
            $query_insert = "INSERT INTO eventos (id_evento, nombre, descripcion, ubicacion, fecha_inicio, fecha_fin, aforo_maximo, cupos_disponibles, estado) 
                            VALUES (?, 'Evento de Prueba', 'Este es un evento de prueba para verificar el sistema de registro', 'Centro de Convenciones', NOW() + INTERVAL 1 WEEK, NOW() + INTERVAL 1 WEEK + INTERVAL 4 HOUR, 100, 100, 'activo')
                            ON DUPLICATE KEY UPDATE nombre = nombre";
            
            $stmt_insert = $conn->prepare($query_insert);
            $stmt_insert->bind_param("i", $evento_id);
            $stmt_insert->execute();
            
            // Obtener el evento recién creado
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $evento_id);
            $stmt->execute();
            $evento = $stmt->get_result()->fetch_assoc();
        }
        
        if (!$evento) {
            throw new Exception("No se pudo obtener la información del evento");
        }
        
        echo json_encode([
            'success' => true,
            'data' => $evento
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Petición inválida'
    ]);
}
?>