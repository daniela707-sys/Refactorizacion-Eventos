<?php
// Database setup script
require_once 'config/config.php';

echo "<h1>Database Setup</h1>";

try {
    // Connect to MySQL server (without database)
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "✅ Connected to MySQL server<br>";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        echo "✅ Database '" . DB_NAME . "' created or already exists<br>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db(DB_NAME);
    
    // Read and execute the main structure SQL
    $structureFile = 'database/estructura_completa.sql';
    if (file_exists($structureFile)) {
        $sql = file_get_contents($structureFile);
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(--|\/\*)/', $statement)) {
                if ($conn->query($statement)) {
                    // Success - don't output for each statement to avoid clutter
                } else {
                    echo "⚠️ Warning executing statement: " . $conn->error . "<br>";
                }
            }
        }
        echo "✅ Main database structure loaded<br>";
    }
    
    // Create the survey table
    $surveyFile = 'database/encuesta_satisfaccion.sql';
    if (file_exists($surveyFile)) {
        $sql = file_get_contents($surveyFile);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(--|\/\*)/', $statement)) {
                if ($conn->query($statement)) {
                    // Success
                } else {
                    echo "⚠️ Warning creating survey table: " . $conn->error . "<br>";
                }
            }
        }
        echo "✅ Survey table created<br>";
    }
    
    // Add sample data
    $sampleFile = 'database/sample_data.sql';
    if (file_exists($sampleFile)) {
        $sql = file_get_contents($sampleFile);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(--|\/\*)/', $statement)) {
                if ($conn->query($statement)) {
                    // Success
                } else {
                    echo "⚠️ Warning adding sample data: " . $conn->error . "<br>";
                }
            }
        }
        echo "✅ Sample data added<br>";
    }
    
    echo "<h2>Setup Complete!</h2>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li><a href='test_db.php'>Test the database connection</a></li>";
    echo "<li><a href='index.php'>Go to the main page</a></li>";
    echo "<li><a href='assets/components/encuesta_satisfaccion_handler.php?id=1'>Test survey for event 1</a></li>";
    echo "</ul>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>