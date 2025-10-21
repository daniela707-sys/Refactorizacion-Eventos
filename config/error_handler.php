<?php
// Configuración de manejo de errores
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Función para manejar errores de base de datos
function handleDatabaseError($conn, $query = '') {
    if ($conn->connect_error) {
        return array(
            'success' => false,
            'error' => 'Database connection failed',
            'data' => array()
        );
    }
    
    if ($conn->error) {
        return array(
            'success' => false,
            'error' => 'Database query failed',
            'data' => array()
        );
    }
    
    return null;
}

// Función para respuesta JSON estándar
function jsonResponse($success = true, $data = array(), $message = '', $error = '') {
    $response = array(
        'success' => $success,
        'data' => $data
    );
    
    if (!empty($message)) {
        $response['message'] = $message;
    }
    
    if (!empty($error)) {
        $response['error'] = $error;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>