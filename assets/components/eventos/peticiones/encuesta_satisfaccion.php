<?php
// Configuración de headers para JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "eventos_db";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'obtener_evento':
            $id_evento = intval($_GET['id'] ?? 0);
            
            if ($id_evento <= 0) {
                throw new Exception("ID de evento inválido");
            }
            
            $stmt = $conn->prepare("SELECT id_evento, nombre, descripcion, ubicacion FROM eventos WHERE id_evento = ?");
            $stmt->bind_param("i", $id_evento);
            $stmt->execute();
            $result = $stmt->get_result();
            $evento = $result->fetch_assoc();
            
            if (!$evento) {
                throw new Exception("Evento no encontrado");
            }
            
            echo json_encode([
                'success' => true,
                'evento' => $evento
            ]);
            break;
            
        case 'buscar_registro':
            $id_evento = intval($_POST['id_evento'] ?? 0);
            $id_usuario = trim($_POST['id_usuario_busqueda'] ?? '');
            
            if ($id_evento <= 0 || empty($id_usuario)) {
                throw new Exception("Parámetros inválidos");
            }
            
            $stmt = $conn->prepare("SELECT ra.id_registro, ra.nombre_completo, ra.email FROM registro_asistencia_evento ra WHERE ra.id_evento = ? AND ra.id_usuario = ?");
            $stmt->bind_param("is", $id_evento, $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $registro = $result->fetch_assoc();
            
            if (!$registro) {
                throw new Exception("No se encontró registro de asistencia");
            }
            
            // Verificar si ya respondió
            $stmt2 = $conn->prepare("SELECT id_encuesta FROM encuesta_satisfaccion_evento WHERE id_registro = ?");
            $stmt2->bind_param("i", $registro['id_registro']);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            
            if ($result2->num_rows > 0) {
                throw new Exception("Ya completó la encuesta para este evento");
            }
            
            echo json_encode([
                'success' => true,
                'registro' => $registro
            ]);
            break;
            
        case 'registrar_encuesta':
            $id_registro = intval($_POST['id_registro'] ?? 0);
            $id_usuario_documento = trim($_POST['id_usuario_documento'] ?? '');
            $experiencia_general = intval($_POST['experiencia_general'] ?? 0);
            $calidad_ponentes = intval($_POST['calidad_ponentes'] ?? 0);
            $proceso_registro = intval($_POST['proceso_registro'] ?? 0);
            $recomendaria = intval($_POST['recomendaria'] ?? 0);
            $sugerencias = trim($_POST['sugerencias'] ?? '');
            
            if ($id_registro <= 0 || empty($id_usuario_documento)) {
                throw new Exception("Datos de registro inválidos");
            }
            
            if ($experiencia_general < 1 || $experiencia_general > 5 ||
                $calidad_ponentes < 1 || $calidad_ponentes > 5 ||
                $proceso_registro < 1 || $proceso_registro > 5 ||
                $recomendaria < 1 || $recomendaria > 5) {
                throw new Exception("Las calificaciones deben estar entre 1 y 5");
            }
            
            $stmt = $conn->prepare("INSERT INTO encuesta_satisfaccion_evento (id_registro, id_usuario, experiencia_general, calidad_ponentes, proceso_registro, recomendaria, sugerencias, fecha_respuesta) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isiiiis", $id_registro, $id_usuario_documento, $experiencia_general, $calidad_ponentes, $proceso_registro, $recomendaria, $sugerencias);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al registrar encuesta");
            }
            
            echo json_encode([
                'success' => true,
                'message' => '¡Gracias por tu feedback! Tu encuesta ha sido registrada exitosamente.'
            ]);
            break;
            
        default:
            throw new Exception("Acción no válida");
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>