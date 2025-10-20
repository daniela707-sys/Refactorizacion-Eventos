<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$departamento = isset($data['departamento']) ? "'" . $data['departamento'] . "'" : 'NULL';

// Construir la consulta para llamar al procedimiento almacenado
$query = "select * from banner_publicitario  where departamento = $departamento AND estado = 1 LIMIT 1";
$result = DB::Query(sql: $query);
$response = array();

if ($result) {
    $publicidad = array();
    while ($row = $result->fetchAssoc()) {
        $publicidad = $row;
    }
    $response = array("publicidad" => $publicidad);
} else {
    $response = array(
        "status" => "error",
        "message" => "Error al obtener los productos en oferta."
    );
}

// Devolver la respuesta al cliente
echo json_encode($response);
