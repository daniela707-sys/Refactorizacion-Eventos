<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

try {
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || !$_SESSION['auth']) {
        throw new Exception('Usuario no autenticado');
    }

    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id_evento']) || empty($data['id_evento'])) {
        throw new Exception('ID del evento requerido');
    }

    $id_evento = intval($data['id_evento']);
    $id_usuario = intval($_SESSION['user_id']);
    $comentario = isset($data['comentario']) ? trim($data['comentario']) : '';

    // Validar que el evento existe y está activo
    $queryEvento = "SELECT nombre, cupos_disponibles FROM eventos WHERE id_evento = $id_evento AND activo = 1";
    $resultEvento = DB::Query($queryEvento);
    
    if (!$resultEvento || !($evento = $resultEvento->fetchAssoc())) {
        throw new Exception('Evento no encontrado o inactivo');
    }

    // Verificar si ya está registrado
    $queryVerificar = "SELECT id_registro FROM registro_asistencia_evento WHERE id_evento = $id_evento AND id_usuario = $id_usuario";
    $resultVerificar = DB::Query($queryVerificar);
    
    if ($resultVerificar && $resultVerificar->fetchAssoc()) {
        throw new Exception('Ya estás registrado en este evento');
    }

    // Verificar cupos disponibles
    $queryRegistrados = "SELECT COUNT(*) as registrados FROM registro_asistencia_evento WHERE id_evento = $id_evento";
    $resultRegistrados = DB::Query($queryRegistrados);
    $registrados = 0;
    
    if ($resultRegistrados) {
        $row = $resultRegistrados->fetchAssoc();
        $registrados = intval($row['registrados']);
    }

    if ($evento['cupos_disponibles'] > 0 && $registrados >= $evento['cupos_disponibles']) {
        throw new Exception('No hay cupos disponibles para este evento');
    }

    // Generar código QR único
    $qr_code = 'QR-' . $id_evento . '-' . $id_usuario . '-' . time();
    
    // Insertar registro de asistencia
    $queryInsertar = "
        INSERT INTO registro_asistencia_evento (id_evento, id_usuario, fecha_registro, asistio, qr) 
        VALUES ($id_evento, $id_usuario, NOW(), '" . DB::PrepareString($comentario) . "', '" . DB::PrepareString($qr_code) . "')
    ";
    
    $resultInsertar = DB::Query($queryInsertar);
    
    if (!$resultInsertar) {
        throw new Exception('Error al procesar el registro');
    }

    // Obtener el ID del registro insertado
    $id_registro = DB::getInsertedId();

    echo json_encode([
        'success' => true,
        'message' => '¡Registro exitoso! Te esperamos en el evento.',
        'data' => [
            'id_registro' => $id_registro,
            'qr_code' => $qr_code,
            'evento_nombre' => $evento['nombre']
        ],
        'debug' => [
            'query_insertar' => $queryInsertar,
            'id_evento' => $id_evento,
            'id_usuario' => $id_usuario
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage()
        ]
    ]);
}
?>