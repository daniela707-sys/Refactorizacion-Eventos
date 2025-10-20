<?php
@ini_set("display_errors", "1");
require_once("../../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);


$query = "select DISTINCT departamento FROM municipio WHERE departamento <> 'BOGOTA' ORDER BY departamento ASC;";
$result = DB::Query($query);
$response = array();

if ($result) {
    $departamentos = array();
    while ($row = $result->fetchAssoc()) {
        $departamentos[] = $row['departamento'];
    }
    $response = $departamentos;
}

// Devolver la respuesta al cliente
echo json_encode($response);
