<?php
@ini_set("display_errors", "1");
require_once("../../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);


$query = "select DISTINCT c.id_categoria, c.nombre FROM categoria c INNER JOIN productos p ON p.categoria = c.id_categoria WHERE p.estado = 1 and  c.estado = 1 ORDER BY c.nombre ASC;";
$result = DB::Query($query);
$response = array();

if ($result) {
    $categorias = array();
    while ($row = $result->fetchAssoc()) {
        $categorias[] = $row;
    }
    $response = array("categorias" => $categorias);
}

// Devolver la respuesta al cliente
echo json_encode($response);
