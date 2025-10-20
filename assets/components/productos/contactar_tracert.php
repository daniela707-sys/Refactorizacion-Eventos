<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$id_producto = isset($data['id_producto']) ? $data['id_producto'] : 'NULL';
$correo = $_SESSION['UserID'];
$fecha = date('Y-m-d H:i:s'); // Fecha y hora actual del servidor

$query = "call sp_insertar_contactanos($id_producto, '$correo', '$fecha')";
$result = DB::Query($query);

$response = array('status' => 'success');

// Devolver la respuesta al cliente
echo json_encode($response);
?>
