<?php
// Conexión a base de datos
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP por defecto no tiene contraseña
$dbname = "eventos_db";

class DB {
    private static $conn = null;
    
    public static function getConnection() {
        if (self::$conn === null) {
            try {
                self::$conn = new mysqli($GLOBALS['servername'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['dbname']);
                
                if (self::$conn->connect_error) {
                    throw new Exception("Conexión fallida: " . self::$conn->connect_error);
                }
                
                self::$conn->set_charset("utf8mb4");
            } catch (Exception $e) {
                error_log("Error de base de datos: " . $e->getMessage());
                return null;
            }
        }
        return self::$conn;
    }
    
    public static function Query($query, $params = []) {
        $conn = self::getConnection();
        if (!$conn) return false;
        
        try {
            if (empty($params)) {
                $result = $conn->query($query);
            } else {
                $stmt = $conn->prepare($query);
                if ($stmt) {
                    $stmt->execute($params);
                    $result = $stmt->get_result();
                } else {
                    return false;
                }
            }
            
            return $result ? new DBResult($result) : false;
        } catch (Exception $e) {
            error_log("Error en consulta: " . $e->getMessage());
            return false;
        }
    }
    
    public static function LastError() {
        $conn = self::getConnection();
        return $conn ? $conn->error : "No connection";
    }
}

class DBResult {
    private $result;
    
    public function __construct($result) {
        $this->result = $result;
    }
    
    public function fetchAssoc() {
        return $this->result->fetch_assoc();
    }
}

try {
    // Crear conexión inicial
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