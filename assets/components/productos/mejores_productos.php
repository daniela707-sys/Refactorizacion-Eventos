<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$departamento = isset($data['departamento']) ? "'" . $data['departamento'] . "'" : 'NULL';
$limite = isset($data['limite']) ? (int)$data['limite'] : 10;
$id_negocio = isset($data['id_negocio']) ? (int)$data['id_negocio'] : 'NULL';

// Construir la consulta para llamar al procedimiento almacenado
$query = "CALL obtener_productos_mas_vendidos($departamento, $limite, $id_negocio)";

$result = DB::Query($query);
$response = array();

if ($result) {
    $productos = array();
    while ($row = $result->fetchAssoc()) {
        $productos[] = $row;
    }
    $response = array("productos" => $productos);
} else {
    $response = array(
        "status" => "error",
        "message" => "Error al obtener los productos más vendidos."
    );
}

// Devolver la respuesta al cliente
echo json_encode($response);
