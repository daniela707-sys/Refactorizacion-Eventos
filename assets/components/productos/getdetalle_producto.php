<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$id_producto = isset($data['id_producto']) ? "'" . $data['id_producto'] . "'" : 'NULL';

$query = "call sp_obtener_producto($id_producto)";
$result = DB::Query($query);
$response = array();

if ($result) {
    while ($row = $result->fetchAssoc()) {
        // Validar la fecha de promoción
        $fechaActual = new DateTime();
        $fechaPromocion = new DateTime($row['tiempo_inicio_promocion']);
        
        // Si la fecha de promoción es menor a la actual, cambiar estado_oferta a 0
        if ($fechaPromocion < $fechaActual || $row['tiempo_inicio_promocion'] == null) {
            $row['estado_oferta'] = "0";
            // También podríamos querer resetear otros campos relacionados con la oferta
            $row['en_oferta'] = "0";
            $row['porcentaje'] = "0";
        }
        
        $response[] = $row;
    }
}

// Devolver la respuesta al cliente
echo json_encode($response);