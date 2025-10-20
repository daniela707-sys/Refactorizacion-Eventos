<?php
// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpiar todas las variables de sesión
$_SESSION = array();

// Definir rutas y dominios
$paths = array(
    '/redemprendedores/',
    '/redemprendedores/tienda/',
    '/'
);

$domains = array(
    'tecnoparqueatl.com',
    '.tecnoparqueatl.com'
);

// Lista de cookies específicas para eliminar basada en tu caso
$cookies_to_delete = array(
    'runnerSession',
    'mediaType',
    'pkE8rP4OtgyewYGcBeyVk',
    'Session',
    '_scc',
    '_tccl_visit',
    '_tccl_visitor'
);

// Tiempo de expiración en el pasado
$past = time() - 3600;

// Eliminar todas las cookies específicas
foreach ($cookies_to_delete as $cookie) {
    foreach ($paths as $path) {
        foreach ($domains as $domain) {
            setcookie($cookie, '', $past, $path, $domain, true, true);
            setcookie($cookie, '', $past, $path, $domain, true, false);
            setcookie($cookie, '', $past, $path, $domain, false, true);
            setcookie($cookie, '', $past, $path, $domain, false, false);
            setcookie($cookie, '', $past, $path, '', true, true);
            setcookie($cookie, '', $past, $path, '', false, true);
        }
    }
}

// Eliminar la cookie de sesión PHP
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    foreach ($paths as $path) {
        foreach ($domains as $domain) {
            setcookie(session_name(), '', $past, $path, $domain, true, true);
            setcookie(session_name(), '', $past, $path, $domain, false, true);
        }
    }
}

// Limpiar cualquier otra cookie que pueda existir
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach ($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        foreach ($paths as $path) {
            foreach ($domains as $domain) {
                setcookie($name, '', $past, $path, $domain, true, true);
                setcookie($name, '', $past, $path, $domain, false, true);
            }
        }
    }
}

// Destruir la sesión si está activa
if (session_status() == PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Limpiar el array de cookies
$_COOKIE = array();

// Headers para prevenir caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Clear-Site-Data: "cookies", "storage"');

// Respuesta
$response = array(
    "status" => "success",
    "message" => "Sesión cerrada correctamente y cookies eliminadas",
    "timestamp" => time()
);

header('Content-Type: application/json');
echo json_encode($response);
exit();