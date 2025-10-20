<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Consulta
$query = "
SELECT 
    id_cat_evento,
    nombre_cat_evento,
    image_cat_evento
FROM categoria_evento
";

// Ejecutar consulta
$result = DB::Query($query);
$categoriasEvento = []; // Inicializar para evitar errores si no hay resultados

if ($result) {
    while ($row = $result->fetchAssoc()) {
        $categoriasEvento[] = $row;
    }
}

// Devolver resultados
echo json_encode([
    'success' => true,
    'total' => count($categoriasEvento),
    'categoriasEvento' => $categoriasEvento,
    'debug' => [
        'query' => $query
    ]
]);
?>