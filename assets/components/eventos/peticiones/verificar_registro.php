<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

try {
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || !$_SESSION['auth']) {
        echo json_encode([
            'success' => true,
            'ya_registrado' => false,
            'message' => 'Usuario no autenticado'
        ]);
        exit;
    }

    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id_evento']) || empty($data['id_evento'])) {
        echo json_encode([
            'success' => true,
            'ya_registrado' => false,
            'message' => 'ID evento requerido'
        ]);
        exit;
    }

    $id_evento = intval($data['id_evento']);
    $id_usuario = intval($_SESSION['user_id']);

    // Consultar si ya está registrado
    $query = "
        SELECT 
            r.id_registro,
            r.fecha_registro,
            r.qr,
            e.nombre as evento_nombre
        FROM registro_asistencia_evento r
        INNER JOIN eventos e ON r.id_evento = e.id_evento
        WHERE r.id_evento = $id_evento AND r.id_usuario = $id_usuario
    ";
    
    $result = DB::Query($query);
    $ya_registrado = false;
    $datos_registro = null;

    if ($result && ($registro = $result->fetchAssoc())) {
        $ya_registrado = true;
        $datos_registro = $registro;
    }

    echo json_encode([
        'success' => true,
        'ya_registrado' => $ya_registrado,
        'datos' => $datos_registro,
        'debug' => [
            'query' => $query,
            'id_evento' => $id_evento,
            'id_usuario' => $id_usuario
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'ya_registrado' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage()
        ]
    ]);
}
?>