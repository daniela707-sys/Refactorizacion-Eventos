<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que se recibió un ID de evento
if (!isset($data['id_evento']) || empty($data['id_evento'])) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'ID de evento no proporcionado'
    ]);
    exit;
}

// Obtener el ID del evento
$id_evento = intval($data['id_evento']);

// Verificar que el ID sea un número válido
if ($id_evento <= 0) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'ID de evento inválido'
    ]);
    exit;
}

// Actualizar el contador de visitas
$query = "UPDATE eventos SET contador_visitas = contador_visitas + 1 WHERE id_evento = " . $id_evento;

try {
    // Ejecutar la consulta
    $result = DB::Query($query);
    
    if ($result) {
        // Consulta para obtener el nuevo contador
        $consulta_contador = "SELECT contador_visitas FROM eventos WHERE id_evento = " . $id_evento;
        $result_contador = DB::Query($consulta_contador);
        
        if ($result_contador && $row = $result_contador->fetchAssoc()) {
            echo json_encode([
                'success' => true,
                'mensaje' => 'Contador de visitas incrementado',
                'contador' => $row['contador_visitas']
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'mensaje' => 'Contador de visitas incrementado',
                'contador' => 'desconocido'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Error al actualizar el contador de visitas'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error al actualizar el contador de visitas: ' . $e->getMessage()
    ]);
}
?>