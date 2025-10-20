<?php
// Incluir configuración
require_once __DIR__ . '/config/config.php';

// Usar constantes
echo "URL Base: " . BASE_URL . "\n";
echo "Assets: " . asset('js/app.js') . "\n";

// Usar variables globales
echo "Tipos de documento disponibles:\n";
foreach ($GLOBALS['TIPOS_DOCUMENTO'] as $codigo => $nombre) {
    echo "- $codigo: $nombre\n";
}

// Usar funciones
$email = "test@example.com";
if (isValidEmail($email)) {
    echo "Email válido\n";
}

// Log de ejemplo
logError('Sistema iniciado correctamente');
?>