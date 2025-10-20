<?php
require_once('../model/comentario.php');

$data = json_decode(file_get_contents("php://input"), true);

// Validar si el comentario_id y subcomentario fueron enviados correctamente
if (!isset($data['comentario_id']) || !isset($data['subcomentario'])) {
    echo json_encode(['success' => false, 'message' => 'Datos faltantes']);
    exit;
}

$comentario_id = $data['comentario_id'];
$subcomentario = $data['subcomentario'];

// Llamar al método de Comentario para insertar el subcomentario
$response = Comentario::create_subcomentario($comentario_id, $subcomentario);

// Devolver la respuesta como JSON
echo json_encode($response);
?>