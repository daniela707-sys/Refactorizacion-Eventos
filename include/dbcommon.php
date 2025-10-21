<?php
// Conexión a base de datos
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP por defecto no tiene contraseña
$dbname = "eventos_db";

try {
    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }
    
    // Configurar charset
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // En caso de error, crear una conexión mock para evitar errores fatales
    $conn = null;
    error_log("Error de base de datos: " . $e->getMessage());
}
?>