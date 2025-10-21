<?php
@ini_set("display_errors", "1");
error_reporting(E_ALL);
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

try {
    if (!$conn) {
        throw new Exception("No hay conexión a la base de datos");
    }

    $query = "SELECT id_categoria, nombre, estado FROM categoria ORDER BY nombre ASC"; //se cambiaron nombres de las tablas
    $resultado = $conn->query($query);
    $categoriasEvento = [];

    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $categoriasEvento[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'total' => count($categoriasEvento),
        'categoriasEvento' => $categoriasEvento
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'categoriasEvento' => []
    ]);
}
?>