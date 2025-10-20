<?php
// encuesta_satisfaccion_handler.php - Lógica de negocio para encuestas
require_once("../../vendor/autoload.php");
require_once("../../include/dbcommon.php");

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

class EncuestaSatisfaccionHandler {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function obtenerEventoInfo($id_evento) {
        $query = "SELECT id_evento, nombre, descripcion, ubicacion, fecha_inicio, fecha_fin, aforo_maximo
                  FROM eventos WHERE id_evento = ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return null;
        
        $stmt->bind_param("i", $id_evento);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function verificarRegistroAsistencia($id_evento, $id_usuario) {
        $query = "SELECT ra.id_registro, ra.id_evento, ra.nombre_completo, ra.email, 
                         ra.id_usuario, ra.asistio, ra.qr,
                         e.nombre as evento_nombre, e.descripcion as evento_descripcion,
                         e.ubicacion, e.fecha_inicio, e.fecha_fin
                  FROM registro_asistencia_evento ra
                  JOIN eventos e ON ra.id_evento = e.id_evento
                  WHERE ra.id_evento = ? AND ra.id_usuario = ? AND ra.asistio = 1";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return null;
        
        $stmt->bind_param("is", $id_evento, $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function yaRespondioEncuesta($id_registro) {
        $query = "SELECT id_encuesta FROM encuesta_satisfaccion_evento WHERE id_registro = ?";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) return false;
        
        $stmt->bind_param("i", $id_registro);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    public function registrarEncuesta($datos) {
        if ($this->yaRespondioEncuesta($datos['id_registro'])) {
            throw new Exception("Ya existe una respuesta de encuesta para este registro");
        }
        
        $query = "INSERT INTO encuesta_satisfaccion_evento 
                  (id_registro, id_usuario, experiencia_general, calidad_ponentes, 
                   proceso_registro, recomendaria, sugerencias, fecha_respuesta) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->conn->error);
        }
        
        $stmt->bind_param(
            "isiiiis",
            $datos['id_registro'],
            $datos['id_usuario_documento'],
            $datos['experiencia_general'],
            $datos['calidad_ponentes'],
            $datos['proceso_registro'],
            $datos['recomendaria'],
            $datos['sugerencias']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error al registrar encuesta: " . $stmt->error);
        }
        
        return ['success' => true, 'id_encuesta' => $this->conn->insert_id];
    }
}

// Procesar peticiones
try {
    if (!$conn || $conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos");
    }
    
    $handler = new EncuestaSatisfaccionHandler($conn);
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'buscar_registro':
            $id_evento = intval($_POST['id_evento'] ?? 0);
            $id_usuario = trim($_POST['id_usuario_busqueda'] ?? '');
            
            if ($id_evento <= 0 || empty($id_usuario)) {
                throw new Exception("Parámetros inválidos");
            }
            
            $registro = $handler->verificarRegistroAsistencia($id_evento, $id_usuario);
            
            if (!$registro) {
                throw new Exception("No se encontró registro de asistencia o no asistió al evento");
            }
            
            if ($handler->yaRespondioEncuesta($registro['id_registro'])) {
                throw new Exception("Ya completó la encuesta para este evento");
            }
            
            echo json_encode([
                'success' => true,
                'registro' => $registro
            ]);
            break;
            
        case 'registrar_encuesta':
            $datos = [
                'id_registro' => intval($_POST['id_registro'] ?? 0),
                'id_usuario_documento' => trim($_POST['id_usuario_documento'] ?? ''),
                'experiencia_general' => intval($_POST['experiencia_general'] ?? 0),
                'calidad_ponentes' => intval($_POST['calidad_ponentes'] ?? 0),
                'proceso_registro' => intval($_POST['proceso_registro'] ?? 0),
                'recomendaria' => intval($_POST['recomendaria'] ?? 0),
                'sugerencias' => trim($_POST['sugerencias'] ?? '')
            ];
            
            // Validaciones
            if ($datos['id_registro'] <= 0 || empty($datos['id_usuario_documento'])) {
                throw new Exception("Datos de registro inválidos");
            }
            
            if ($datos['experiencia_general'] < 1 || $datos['experiencia_general'] > 5 ||
                $datos['calidad_ponentes'] < 1 || $datos['calidad_ponentes'] > 5 ||
                $datos['proceso_registro'] < 1 || $datos['proceso_registro'] > 5 ||
                $datos['recomendaria'] < 1 || $datos['recomendaria'] > 5) {
                throw new Exception("Las calificaciones deben estar entre 1 y 5");
            }
            
            $resultado = $handler->registrarEncuesta($datos);
            
            echo json_encode([
                'success' => true,
                'message' => '¡Gracias por tu feedback! Tu encuesta ha sido registrada exitosamente.',
                'data' => $resultado
            ]);
            break;
            
        case 'obtener_evento':
            $id_evento = intval($_GET['id'] ?? 0);
            
            if ($id_evento <= 0) {
                throw new Exception("ID de evento inválido");
            }
            
            $evento = $handler->obtenerEventoInfo($id_evento);
            
            if (!$evento) {
                throw new Exception("Evento no encontrado");
            }
            
            echo json_encode([
                'success' => true,
                'evento' => $evento
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


