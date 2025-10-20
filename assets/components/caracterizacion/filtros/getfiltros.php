<?php
@ini_set("display_errors", "1");
require_once("../../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);


$query = "select * from promocion where estado = 1 AND nombre = 'Descuento' OR nombre = 'Tiempo limitado' OR nombre = 'No Aplica'";
$result = DB::Query($query);
$response = array();

if ($result) {
    $promocion = array();
    while ($row = $result->fetchAssoc()) {
        $promocion[] = $row;
    }
    $response = array("promocion" => $promocion);
}

// Devolver la respuesta al cliente
echo json_encode($response);
