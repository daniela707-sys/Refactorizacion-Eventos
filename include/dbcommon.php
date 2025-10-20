<?php
// Conexión a base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "eventos_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar charset
$conn->set_charset("utf8mb4");
?>