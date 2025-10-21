<?php
@ini_set("display_errors", "0");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

try {
    // Datos de respaldo para publicidad
    $response = array(
        "success" => true,
        "publicidad" => array(
            array(
                "id" => "1",
                "titulo" => "Publicidad de Ejemplo",
                "imagen" => "assets/images/banners/banner1.jpg",
                "enlace" => "#",
                "activo" => "1"
            )
        )
    );
} catch (Exception $e) {
    $response = array(
        "success" => false,
        "publicidad" => array(),
        "error" => "Error loading ads"
    );
}

echo json_encode($response);
?>