<?php
// oauth2callback.php - Archivo de callback para OAuth
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    header("Location: oauth_setup.php?code=" . urlencode($code));
    exit;
} elseif (isset($_GET['error'])) {
    $error = $_GET['error'];
    $error_description = $_GET['error_description'] ?? 'Error desconocido';
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Error de Autorización</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
            .error { color: #e74c3c; background: #fadbd8; padding: 20px; border-radius: 8px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <h1>Error de Autorización</h1>
        <div class="error">
            <h3>No se pudo autorizar la aplicación</h3>
            <p><strong>Error:</strong> ' . htmlspecialchars($error) . '</p>
            <p><strong>Descripción:</strong> ' . htmlspecialchars($error_description) . '</p>
        </div>
        <p><a href="oauth_setup.php">Volver a intentar</a></p>
    </body>
    </html>';
} else {
    header("Location: oauth_setup.php");
    exit;
}
?>