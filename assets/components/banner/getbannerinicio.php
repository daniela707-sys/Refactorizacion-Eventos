<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$query = "select foto from banner where estado = '1'";
$result = DB::Query($query);
$response = array();

if ($result) {
    while ($row = $result->fetchAssoc()) {
        $response[] = $row;
    }
}

// Devolver la respuesta al cliente
echo json_encode($response);
