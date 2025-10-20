<?php
// Incluir el modelo de Comentario
require_once('../model/comentario.php');
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$id_producto = isset($data['id_producto']) ? "'" . $data['id_producto'] . "'" : 'NULL';


$comentarios = Comentario::get_all_comentarios_con_subcomentarios($id_producto);

header('Content-Type: application/json');
echo json_encode($comentarios);
?>