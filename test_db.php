<?php
// Test database connection and tables
require_once 'include/dbcommon.php';
require_once 'config/config.php';

echo "<h2>Database Connection Test</h2>";

try {
    if ($conn) {
        echo "✅ Database connection successful<br>";
        
        // Test tables existence
        $tables = ['eventos', 'registro_asistencia_evento', 'encuesta_satisfaccion_evento'];
        
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "✅ Table '$table' exists<br>";
                
                // Show table structure
                $structure = $conn->query("DESCRIBE $table");
                if ($structure) {
                    echo "<details><summary>Structure of $table</summary>";
                    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
                    while ($row = $structure->fetch_assoc()) {
                        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
                    }
                    echo "</table></details>";
                }
            } else {
                echo "❌ Table '$table' does not exist<br>";
            }
        }
        
        // Test sample data
        echo "<h3>Sample Data</h3>";
        
        // Check events
        $events = $conn->query("SELECT id_evento, nombre FROM eventos LIMIT 3");
        if ($events && $events->num_rows > 0) {
            echo "<strong>Events:</strong><br>";
            while ($row = $events->fetch_assoc()) {
                echo "- ID: {$row['id_evento']}, Name: {$row['nombre']}<br>";
            }
        } else {
            echo "No events found<br>";
        }
        
        // Check registrations
        $registrations = $conn->query("SELECT id_registro, id_evento, id_usuario, nombre_completo FROM registro_asistencia_evento LIMIT 3");
        if ($registrations && $registrations->num_rows > 0) {
            echo "<strong>Registrations:</strong><br>";
            while ($row = $registrations->fetch_assoc()) {
                echo "- ID: {$row['id_registro']}, Event: {$row['id_evento']}, User: {$row['id_usuario']}, Name: {$row['nombre_completo']}<br>";
            }
        } else {
            echo "No registrations found<br>";
        }
        
    } else {
        echo "❌ Database connection failed<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h3>Configuration</h3>";
echo "Database: " . DB_NAME . "<br>";
echo "Host: " . DB_HOST . "<br>";
echo "Debug Mode: " . (DEBUG_MODE ? 'ON' : 'OFF') . "<br>";
?>