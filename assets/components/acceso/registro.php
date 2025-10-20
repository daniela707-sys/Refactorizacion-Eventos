<?php
@ini_set("display_errors", "1");
require_once("../../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$correo = isset($data['correo']) ? "'" . $data['correo'] . "'" : 'NULL';
$password = isset($data['password']) ? "'" . $data['password'] . "'" : 'NULL';
$nombres = isset($data['nombres']) ? "'" . $data['nombres'] . "'" : 'NULL';
$apellidos = isset($data['apellidos']) ? "'" . $data['apellidos'] . "'" : 'NULL';
$tipo_identificacion = isset($data['tipo_identificacion']) ? "'" . $data['tipo_identificacion'] . "'" : 'NULL';
$numero_identificacion = isset($data['numero_identificacion']) ? "'" . $data['numero_identificacion'] . "'" : 'NULL';
$numero_celular = isset($data['numero_celular']) ? "'" . $data['numero_celular'] . "'" : 'NULL';

// Construir la consulta para llamar al procedimiento almacenado
$query = "CALL sena_redemprende.registrar_usuario($correo, $password, $tipo_identificacion, $numero_identificacion, $nombres, $apellidos, $numero_celular)";

$result = DB::Query($query);
$response = array();

if ($result) {
    $response = array( 
        "status" => "success",
        "message" => "Usuario registrado exitosamente."
    );
} else {
    $response = array(
        "status" => "error",
        "message" => "Error al registrar el usuario por correo o documento ya en uso."
    );
}

// Devolver la respuesta al cliente
echo json_encode($response);
