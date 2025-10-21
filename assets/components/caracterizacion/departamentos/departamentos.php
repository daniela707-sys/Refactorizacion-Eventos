<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

try {
    if (!$conn) {
        throw new Exception("No hay conexión a la base de datos");
    }

    $query = "SELECT DISTINCT departamento FROM municipio WHERE departamento <> 'BOGOTA' ORDER BY departamento ASC";
    $result = $conn->query($query);
    $departamentos = array();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $departamentos[] = $row['departamento'];
        }
    }

    // Devolver la respuesta al cliente
    echo json_encode($departamentos);
    
} catch (Exception $e) {
    // En caso de error, devolver array vacío
    error_log("Error en departamentos.php: " . $e->getMessage());
    echo json_encode([]);
}
?>
