<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

try {
    $query = "SELECT DISTINCT c.id_categoria, c.nombre FROM categoria c INNER JOIN productos p ON p.categoria = c.id_categoria WHERE p.estado = 1 AND c.estado = 1 ORDER BY c.nombre ASC";
    $result = $conn->query($query);
    $response = array();
    
    if ($result && $result->num_rows > 0) {
        $categorias = array();
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }
        $response = array("categorias" => $categorias);
    } else {
        $response = array("categorias" => array());
    }
} catch (Exception $e) {
    // Datos de respaldo en caso de error de base de datos
    $response = array(
        "categorias" => array(
            array("id_categoria" => "1", "nombre" => "Tecnología"),
            array("id_categoria" => "2", "nombre" => "Educación"),
            array("id_categoria" => "3", "nombre" => "Negocios"),
            array("id_categoria" => "4", "nombre" => "Arte y Cultura")
        )
    );
}

echo json_encode($response);
