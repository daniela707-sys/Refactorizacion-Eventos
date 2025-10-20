<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Manejar solicitudes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Obtener el ID del evento
$id_evento = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_evento <= 0) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'ID de evento no válido'
    ]);
    exit;
}

// Consultar el evento
$query = "SELECT * FROM eventos WHERE id_evento = 2";
$params = [$id_evento];

$result = DB::Query($query, $params);
$evento = null;

if ($result && ($row = $result->fetchAssoc())) {
    // Formatear datos numéricos para JSON
    $row['id_evento'] = intval($row['id_evento']);
    $row['categoria'] = intval($row['categoria']);
    $row['subcategoria'] = $row['subcategoria'] ? intval($row['subcategoria']) : null;
    $row['aforo_maximo'] = intval($row['aforo_maximo']);
    $row['contador_visitas'] = intval($row['contador_visitas']);
    $row['precio_entrada'] = floatval($row['precio_entrada']);
    $row['es_gratuito'] = $row['es_gratuito'] == '1';
    $row['cupos_disponibles'] = intval($row['cupos_disponibles']);
    
    if ($row['calificacion'] !== null) {
        $row['calificacion'] = floatval($row['calificacion']);
    }
    
    $evento = $row;
}

// Devolver respuesta
echo json_encode([
    'success' => ($evento !== null),
    'evento' => $evento
]);