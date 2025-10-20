<?php
// =============================================================================
// CONFIGURACIÓN GLOBAL DEL SISTEMA - EVENTOS
// =============================================================================

// Prevenir acceso directo
if (!defined('SISTEMA_INICIADO')) {
    define('SISTEMA_INICIADO', true);
}

// =============================================================================
// CONFIGURACIÓN DE BASE DE DATOS
// =============================================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventos_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// =============================================================================
// RUTAS DEL SISTEMA
// =============================================================================
define('BASE_URL', 'http://localhost/eventos-copia/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('UPLOADS_PATH', __DIR__ . '/../uploads/');
define('UPLOADS_URL', BASE_URL . 'uploads/');

// =============================================================================
// CONFIGURACIÓN DE CORREO
// =============================================================================
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'senareddeemprendedores@gmail.com');
define('MAIL_PASSWORD', 'piaetogvaufgdsvp');
define('MAIL_FROM_NAME', 'Red Emprendedores');

// =============================================================================
// CONFIGURACIÓN DE EVENTOS
// =============================================================================
define('MAX_CUPOS_EVENTO', 500);
define('DIAS_LIMITE_REGISTRO', 1); // Días antes del evento para cerrar registro
define('QR_SIZE', '250x250');
define('QR_API_URL', 'https://api.qrserver.com/v1/create-qr-code/');

// =============================================================================
// TIPOS DE DOCUMENTO
// =============================================================================
$TIPOS_DOCUMENTO = [
    'CC' => 'Cédula de Ciudadanía',
    'TI' => 'Tarjeta de Identidad',
    'CE' => 'Cédula de Extranjería',
    'PP' => 'Pasaporte'
];

// =============================================================================
// TIPOS DE POBLACIÓN
// =============================================================================
$TIPOS_POBLACION = [
    1 => 'Estudiante',
    2 => 'Emprendedor',
    3 => 'Empresario',
    4 => 'Profesional',
    5 => 'Otro'
];

// =============================================================================
// CONFIGURACIÓN DE ARCHIVOS
// =============================================================================
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx']);

// =============================================================================
// CONFIGURACIÓN DE SESIÓN
// =============================================================================
define('SESSION_TIMEOUT', 3600); // 1 hora
define('COOKIE_LIFETIME', 86400); // 24 horas

// =============================================================================
// MENSAJES DEL SISTEMA
// =============================================================================
$MENSAJES = [
    'REGISTRO_EXITOSO' => '¡Registro exitoso! Se ha enviado confirmación a tu correo.',
    'ERROR_CUPOS' => 'No hay cupos disponibles para este evento.',
    'ERROR_REGISTRO_DUPLICADO' => 'Ya existe un registro con este email o documento.',
    'ERROR_EVENTO_NO_EXISTE' => 'El evento no existe o no está disponible.',
    'ERROR_CAMPOS_REQUERIDOS' => 'Los campos marcados con * son obligatorios.',
    'ERROR_EMAIL_INVALIDO' => 'El email ingresado no es válido.'
];

// =============================================================================
// CONFIGURACIÓN DE DESARROLLO/PRODUCCIÓN
// =============================================================================
define('ENVIRONMENT', 'development'); // 'development' o 'production'
define('DEBUG_MODE', ENVIRONMENT === 'development');

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// =============================================================================
// FUNCIONES GLOBALES ÚTILES
// =============================================================================

/**
 * Obtener URL completa de un asset
 */
function asset($path) {
    return ASSETS_URL . ltrim($path, '/');
}

/**
 * Obtener URL base con ruta
 */
function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

/**
 * Generar código QR
 */
function generateQRUrl($data, $size = null) {
    $size = $size ?: QR_SIZE;
    return QR_API_URL . "?size={$size}&data=" . urlencode($data);
}

/**
 * Validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitizar string
 */
function sanitize($string) {
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}

/**
 * Log de errores
 */
function logError($message, $context = []) {
    $log = date('[Y-m-d H:i:s] ') . $message;
    if (!empty($context)) {
        $log .= ' | Context: ' . json_encode($context);
    }
    error_log($log);
}

// =============================================================================
// INICIALIZACIÓN
// =============================================================================

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

// Configurar codificación
mb_internal_encoding('UTF-8');

// Variables globales disponibles
$GLOBALS['TIPOS_DOCUMENTO'] = $TIPOS_DOCUMENTO;
$GLOBALS['TIPOS_POBLACION'] = $TIPOS_POBLACION;
$GLOBALS['MENSAJES'] = $MENSAJES;

?>