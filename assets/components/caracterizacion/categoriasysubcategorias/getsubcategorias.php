<?php
@ini_set("display_errors", "1");
require_once("../../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);


$query = "select * from sub_categoria where estado = 1";
$result = DB::Query($query);
$response = array();

if ($result) {
    $subcategorias = array();
    while ($row = $result->fetchAssoc()) {
        $subcategorias[] = $row;
    }
    $response = array("subcategorias" => $subcategorias);
}

// Devolver la respuesta al cliente
echo json_encode($response);
