<?php
// procesar_encuesta.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers para JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Si es una petición OPTIONS, responder inmediatamente
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Función para log de errores
function logError($message, $context = []) {
    $log_message = date('[Y-m-d H:i:s] ') . $message;
    if (!empty($context)) {
        $log_message .= ' | Context: ' . json_encode($context);
    }
    error_log($log_message);
}

try {
    // Verificar método de petición
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método de petición no válido');
    }

    // Verificar acción
    if (!isset($_POST['action']) || $_POST['action'] !== 'enviar_encuesta') {
        throw new Exception('Acción no válida');
    }

    // Incluir archivos necesarios
    require_once("../include/dbcommon.php");

    // Verificar conexión
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . ($conn->connect_error ?? 'Conexión no definida'));
    }

    // Obtener y validar datos del POST
    $id_evento = intval($_POST['id_evento'] ?? 0);
    $id_registro = intval($_POST['id_registro'] ?? 0);
    $id_usuario = trim($_POST['id_usuario'] ?? '');
    $experiencia_general = intval($_POST['experiencia_general'] ?? 0);
    $calidad_ponentes = intval($_POST['calidad_ponentes'] ?? 0);
    $proceso_registro = intval($_POST['proceso_registro'] ?? 0);
    $recomendaria = intval($_POST['recomendaria'] ?? -1);
    $sugerencias = htmlspecialchars(trim($_POST['sugerencias'] ?? ''));

    // Validaciones
    $errores = [];

    if ($id_evento <= 0) $errores[] = "ID de evento inválido";
    if ($id_registro <= 0) $errores[] = "ID de registro inválido";
    if (empty($id_usuario)) $errores[] = "ID de usuario requerido";
    if ($experiencia_general < 1 || $experiencia_general > 5) $errores[] = "Experiencia general debe ser 1-5";
    if ($calidad_ponentes < 1 || $calidad_ponentes > 5) $errores[] = "Calidad de ponentes debe ser 1-5";
    if ($proceso_registro < 1 || $proceso_registro > 5) $errores[] = "Proceso de registro debe ser 1-5";
    if ($recomendaria < 0 || $recomendaria > 1) $errores[] = "Debe indicar si recomendaría el evento";

    if (!empty($errores)) {
        throw new Exception("Errores de validación: " . implode(', ', $errores));
    }

    logError('Iniciando proceso de encuesta', [
        'id_evento' => $id_evento,
        'id_registro' => $id_registro,
        'id_usuario' => $id_usuario
    ]);

    // Verificar que el registro existe y corresponde al usuario
    $query_verificar = "SELECT ra.id_registro, ra.nombre_completo, ra.email, e.nombre as evento_nombre
                       FROM registro_asistencia_evento ra
                       JOIN eventos e ON ra.id_evento = e.id_evento
                       WHERE ra.id_registro = ? AND ra.id_evento = ? AND ra.id_usuario = ? AND ra.asistio = 1";
    
    $stmt_verificar = $conn->prepare($query_verificar);
    if (!$stmt_verificar) {
        throw new Exception("Error en la consulta de verificación: " . $conn->error);
    }

    $stmt_verificar->bind_param("iis", $id_registro, $id_evento, $id_usuario);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();
    $registro = $resultado_verificar->fetch_assoc();
    $stmt_verificar->close();

    if (!$registro) {
        throw new Exception("No se encontró un registro válido para este usuario en este evento");
    }

    // Verificar si ya respondió la encuesta
    $query_existente = "SELECT id_encuesta FROM encuesta_satisfaccion_evento 
                       WHERE id_registro = ?";
    $stmt_existente = $conn->prepare($query_existente);
    
    if (!$stmt_existente) {
        throw new Exception("Error en la consulta de encuesta existente: " . $conn->error);
    }

    $stmt_existente->bind_param("i", $id_registro);
    $stmt_existente->execute();
    $resultado_existente = $stmt_existente->get_result();

    if ($resultado_existente->num_rows > 0) {
        $stmt_existente->close();
        throw new Exception("Ya has completado la encuesta de satisfacción para este evento");
    }
    $stmt_existente->close();

    // Insertar encuesta de satisfacción
    $query_insert = "INSERT INTO encuesta_satisfaccion_evento 
                    (id_registro, id_usuario, experiencia_general, calidad_ponentes, 
                     proceso_registro, recomendaria, sugerencias, fecha_respuesta) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt_insert = $conn->prepare($query_insert);
    if (!$stmt_insert) {
        throw new Exception("Error preparando la inserción: " . $conn->error);
    }

    $stmt_insert->bind_param("isiiiis", 
        $id_registro, $id_usuario, $experiencia_general, $calidad_ponentes, 
        $proceso_registro, $recomendaria, $sugerencias
    );

    if (!$stmt_insert->execute()) {
        throw new Exception("Error al registrar la encuesta: " . $stmt_insert->error);
    }

    $id_encuesta = $conn->insert_id;
    $stmt_insert->close();

    logError('Encuesta registrada correctamente', ['id_encuesta' => $id_encuesta]);

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => '¡Gracias por completar la encuesta! Tu opinión es muy valiosa para nosotros.',
        'data' => [
            'id_encuesta' => $id_encuesta,
            'id_registro' => $id_registro,
            'evento_nombre' => $registro['evento_nombre'],
            'participante' => $registro['nombre_completo']
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit();

} catch (Exception $e) {
    logError('Error general en el proceso', ['error' => $e->getMessage()]);
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_details' => [
            'file' => basename(__FILE__),
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit();
} catch (Error $e) {
    logError('Error fatal de PHP', ['error' => $e->getMessage()]);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'error_details' => [
            'type' => 'PHP Error',
            'message' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit();
}
?>