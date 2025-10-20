<?php
require_once('../model/comentario.php');

// Verificar si se está usando el método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos de la solicitud
    $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : null;
    $fecha_hora = isset($_POST['fecha_hora']) ? trim($_POST['fecha_hora']) : null;
    $estrellas = isset($_POST['estrellas']) ? (int)$_POST['estrellas'] : null;
    $id_producto = isset($_POST['id_producto']) ? "'" . $_POST['id_producto'] . "'" : 'NULL';

    // Validar que todos los parámetros estén presentes
    if ($comentario && $fecha_hora && $estrellas && $id_producto) {
        // Validar que las estrellas sean un número entre 1 y 5
        if ($estrellas < 1 || $estrellas > 5) {
            echo 'Error: Las estrellas deben estar entre 1 y 5.';
            exit;
        }

        // Validar formato de la fecha (ajustar formato según lo que se espera)
        $dateTimeFormat = 'Y-m-d H:i:s';
        $dt = DateTime::createFromFormat($dateTimeFormat, $fecha_hora);
        if (!$dt || $dt->format($dateTimeFormat) !== $fecha_hora) {
            echo 'Error: El formato de la fecha y hora es inválido. Usa el formato ' . $dateTimeFormat . '.';
            exit;
        }

        try {
            // Llamar al método para crear el comentario en la tabla comentarios
            Comentario::create_comentario($comentario, $fecha_hora, $estrellas, $id_producto);
            echo 'Comentario enviado exitosamente.';
        } catch (Exception $e) {
            echo 'Error al crear el comentario: ' . $e->getMessage();
        }
    } else {
        echo 'Faltan parámetros. Asegúrate de enviar todos los datos: comentario, fecha_hora, estrellas.';
    }
} else {
    echo 'Método no permitido. Por favor usa POST para agregar un comentario.';
}
