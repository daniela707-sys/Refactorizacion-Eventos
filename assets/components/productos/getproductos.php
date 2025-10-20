<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); // Permitir todos los orígenes
header("Content-Type: application/json"); // Establecer tipo de contenido

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$departamento = isset($data['departamento']) && !empty($data['departamento']) ? "'" . $data['departamento'] . "'" : 'NULL';
$categoria = isset($data['categoria']) ? "'" . $data['categoria'] . "'" : 'NULL';
$sector = isset($data['sector']) ? "'" . $data['sector'] . "'" : 'NULL';
$subcategoria = isset($data['subcategoria']) ? "'" . $data['subcategoria'] . "'" : 'NULL';
$buscar = isset($data['buscar']) && !empty($data['buscar']) ? "'" . $data['buscar'] . "'" : 'NULL';
$min = isset($data['min']) ? $data['min'] : '0';
$max = isset($data['max']) ? $data['max'] : '0';
if ($max > 10000000) {
    $max = '0';
}
$orden = isset($data['orden']) ? $data['orden'] : 'calificacion';
$page = isset($data['page']) ? $data['page'] : 0;
$promocion = isset($data['filtros']) ? $data['filtros'] : 'NULL';
$id_negocio = isset($data['id_negocio']) ? $data['id_negocio'] : 'NULL';
$stars = isset($data['estrellas']) ? $data['estrellas'] : 'NULL';

// Llamar al procedimiento almacenado get_total_productos
$totalQuery = "CALL sena_redemprende.get_total_productos(" .
    $departamento . ", " .
    $categoria . ", " .
    $sector . ", " .
    $subcategoria . ", " .
    $buscar . ", " .
    $min . ", " .
    $max . ", " .
    $promocion . ", " .
    $id_negocio . ")";

$totalResult = DB::Query($totalQuery);
$response = array();

if ($totalResult) {
    $totalRow = $totalResult->fetchAssoc();
    if ($totalRow) {
        $response['total_productos'] = $totalRow['total_productos'];
        $response['total_paginas'] = $totalRow['total_paginas'];

        // Llamar al procedimiento almacenado get_productos_filtrados
        $productosQuery = "CALL sena_redemprende.get_productos_filtrados(" .
            $departamento . ", " .
            $categoria . ", " .
            $sector . ", " .
            $subcategoria . ", " .
            $buscar . ", " .
            $min . ", " .
            $max . ", " .
            "'" . $orden . "', " .
            $page . ", " .
            $promocion . ", " .
            $id_negocio . ", " .
            $stars . ")"; // Agregar el parámetro de estrellas

        $productosResult = DB::Query($productosQuery);

        if ($productosResult) {
            $productos = array();
            while ($row = $productosResult->fetchAssoc()) {
                $productos[] = json_decode($row['producto'], true);
            }

            $response['productos'] = $productos;
        }
    }
}

echo json_encode($response);
