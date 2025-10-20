<?php
// Configuración básica
@ini_set("display_errors", "1");
error_reporting(E_ALL);

// Incluir PHPMailer
require_once('./PHPMailer/src/PHPMailer.php');
require_once('./PHPMailer/src/SMTP.php');
require_once('./PHPMailer/src/Exception.php');

// Usar clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Incluir archivos necesarios
require_once("../include/dbcommon.php");

// Verificar conexión usando MySQLi estándar
if (!$conn) {
    die("Error de conexión: No se pudo establecer conexión a la base de datos");
}

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Deshabilitar headers de cache para desarrollo
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Variables de configuración
$pageTitle = "Registrarse al Evento";

// Obtener ID del evento desde la URL
$evento_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Debug: Mostrar información
echo "<!-- DEBUG: evento_id = $evento_id -->";

// Función para obtener información del evento
function obtenerEventoInfo($evento_id)
{
    global $conn;

    $query = "SELECT id_evento, nombre, descripcion, ubicacion, fecha_inicio, fecha_fin, 
                     aforo_maximo, estado, municipio, departamento
              FROM eventos 
              WHERE id_evento = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<!-- DEBUG: Error preparando consulta: " . $conn->error . " -->";
        return null;
    }

    $stmt->bind_param("i", $evento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $evento = $result->fetch_assoc();

    echo "<!-- DEBUG: Evento encontrado: " . ($evento ? 'SÍ' : 'NO') . " -->";

    return $evento;
}

// Función para verificar si el usuario ya está registrado
function yaEstaRegistrado($evento_id, $email, $numero_documento)
{
    global $conn;

    $query = "SELECT id_registro FROM registro_asistencia 
              WHERE id_evento = ? AND (email = ? OR numero_documento = ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<!-- DEBUG: Error en consulta duplicados: " . $conn->error . " -->";
        return false;
    }

    $stmt->bind_param("iss", $evento_id, $email, $numero_documento);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

// Función para generar código QR único
function generarCodigoQR($evento_id, $email)
{
    return md5($evento_id . $email . time() . rand(1000, 9999));
}

// Función para registrar asistencia (SIN VALIDAR CUPOS)
function registrarAsistencia($datos)
{
    global $conn;

    echo "<!-- DEBUG: Iniciando registro de asistencia -->";

    // Verificar si ya está registrado
    if (yaEstaRegistrado($datos['id_evento'], $datos['email'], $datos['numero_documento'])) {
        throw new Exception("Ya existe un registro con este email o documento para este evento");
    }

    // Generar código QR
    $qr_code = generarCodigoQR($datos['id_evento'], $datos['email']);
    echo "<!-- DEBUG: QR generado: $qr_code -->";

    // Insertar registro de asistencia usando consulta simple
    $query = "INSERT INTO registro_asistencia 
              (id_evento, nombre_completo, email, tipo_documento, numero_documento, 
               telefono, fecha_registro, asistio, qr) 
              VALUES (?, ?, ?, ?, ?, ?, NOW(), 0, ?)";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error preparando consulta de inserción: " . $conn->error);
    }

    $stmt->bind_param(
        "issssss",
        $datos['id_evento'],
        $datos['nombre_completo'],
        $datos['email'],
        $datos['tipo_documento'],
        $datos['numero_documento'],
        $datos['telefono'],
        $qr_code
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al registrar la asistencia: " . $stmt->error);
    }

    $id_registro = $conn->insert_id;
    echo "<!-- DEBUG: Registro insertado con ID: $id_registro -->";

    // Obtener información del evento para el correo
    $query_evento = "SELECT nombre, descripcion, ubicacion, fecha_inicio FROM eventos WHERE id_evento = ?";
    $stmt_evento = $conn->prepare($query_evento);
    if ($stmt_evento) {
        $stmt_evento->bind_param("i", $datos['id_evento']);
        $stmt_evento->execute();
        $result_evento = $stmt_evento->get_result();
        $evento_info = $result_evento->fetch_assoc();

        // Enviar correo de confirmación
        $mail_enviado = enviarCorreoConfirmacion($datos, $evento_info, $qr_code, $id_registro);
        echo "<!-- DEBUG: Correo enviado: " . ($mail_enviado ? 'SÍ' : 'NO') . " -->";
    }

    echo "<!-- DEBUG: Registro completado exitosamente -->";

    return [
        'success' => true,
        'qr_code' => $qr_code,
        'id_registro' => $id_registro
    ];
}

// Función para enviar correo de confirmación con PHPMailer
function enviarCorreoConfirmacion($datos, $evento_info, $qr_code, $id_registro)
{
    // Crear instancia de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'aac04e2fe4f46c';
        $mail->Password   = '****1f02';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525;
        $mail->CharSet    = 'UTF-8';

        // Remitentes y destinatarios
        $mail->setFrom('avilajoseph2021@gmail.com', 'Red Emprendedores');
        $mail->addAddress($datos['email'], $datos['nombre_completo']);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de Registro - ' . $evento_info['nombre'];

        // Formatear fecha del evento
        $fecha_evento = 'Fecha no definida';
        if (!empty($evento_info['fecha_inicio'])) {
            $fecha = new DateTime($evento_info['fecha_inicio']);
            $fecha_evento = $fecha->format('d/m/Y - H:i');
        }

        // Crear contenido HTML del correo
        $mensaje = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; background-color: #f9f9f9;">
            <div style="text-align: center; background-color: #50a72c; color: white; padding: 15px; border-radius: 8px 8px 0 0;">
                <h1 style="margin: 0; font-size: 24px;">¡Registro Confirmado!</h1>
            </div>
            
            <div style="padding: 20px; background-color: white; border-radius: 0 0 8px 8px;">
                <p style="margin-bottom: 20px;">Hola <strong>' . htmlspecialchars($datos['nombre_completo']) . '</strong>,</p>
                
                <p>Tu registro al evento <strong>' . htmlspecialchars($evento_info['nombre']) . '</strong> ha sido confirmado exitosamente.</p>
                
                <div style="background-color: #f5f5f5; border-left: 4px solid #50a72c; padding: 15px; margin: 20px 0;">
                    <h3 style="margin-top: 0; color: #333;">Detalles del Evento:</h3>
                    <p><strong>Nombre:</strong> ' . htmlspecialchars($evento_info['nombre']) . '</p>
                    <p><strong>Fecha:</strong> ' . $fecha_evento . '</p>
                    <p><strong>Ubicación:</strong> ' . htmlspecialchars($evento_info['ubicacion']) . '</p>
                </div>
                
                <div style="background-color: #f0f7ed; border: 1px solid #ccc; padding: 15px; margin: 20px 0; text-align: center; border-radius: 5px;">
                    <h3 style="margin-top: 0; color: #50a72c;">Información de tu Registro</h3>
                    <p><strong>ID de Registro:</strong> #' . $id_registro . '</p>
                    <p><strong>Código QR:</strong> ' . $qr_code . '</p>
                    <p style="font-size: 12px; color: #666;">Presenta este código QR el día del evento para confirmar tu asistencia.</p>
                </div>
                
                <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.</p>
                
                <p style="margin-top: 30px;">Saludos cordiales,</p>
                <p><strong>Equipo Red Emprendedores</strong></p>
            </div>
            
            <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #888;">
                <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            </div>
        </div>';

        $mail->Body = $mensaje;

        // Versión de texto plano del correo (alternativa)
        $mail->AltBody = 'Confirmación de Registro - ' . $evento_info['nombre'] . "\n\n" .
            'Hola ' . $datos['nombre_completo'] . ",\n\n" .
            'Tu registro al evento ' . $evento_info['nombre'] . ' ha sido confirmado exitosamente.' . "\n\n" .
            'Detalles del Evento:' . "\n" .
            'Nombre: ' . $evento_info['nombre'] . "\n" .
            'Fecha: ' . $fecha_evento . "\n" .
            'Ubicación: ' . $evento_info['ubicacion'] . "\n\n" .
            'Información de tu Registro:' . "\n" .
            'ID de Registro: #' . $id_registro . "\n" .
            'Código QR: ' . $qr_code . "\n\n" .
            'Presenta este código QR el día del evento para confirmar tu asistencia.' . "\n\n" .
            'Saludos cordiales,' . "\n" .
            'Equipo Red Emprendedores';

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "<!-- DEBUG: Error enviando correo: " . $e->getMessage() . " -->";
        return false;
    }
}

// Obtener información del evento
$evento_info = null;
if ($evento_id > 0) {
    $evento_info = obtenerEventoInfo($evento_id);

    // Si no existe el evento, crear uno de ejemplo para testing
    if (!$evento_info) {
        echo "<!-- DEBUG: Evento no encontrado, creando datos de ejemplo -->";

        $query_insert = "INSERT INTO eventos 
                        (id_evento, nombre, descripcion, ubicacion, fecha_inicio, fecha_fin, 
                         aforo_maximo, estado) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'activo')
                        ON DUPLICATE KEY UPDATE nombre = VALUES(nombre)";

        $stmt = $conn->prepare($query_insert);
        if ($stmt) {
            $nombre = "Evento de Prueba";
            $descripcion = "Este es un evento de prueba para verificar el sistema de registro";
            $ubicacion = "Centro de Convenciones";
            $fecha_inicio = date('Y-m-d H:i:s', strtotime('+1 week'));
            $fecha_fin = date('Y-m-d H:i:s', strtotime('+1 week +4 hours'));
            $aforo = 100;

            $stmt->bind_param(
                "isssssi",
                $evento_id,
                $nombre,
                $descripcion,
                $ubicacion,
                $fecha_inicio,
                $fecha_fin,
                $aforo
            );
            $stmt->execute();

            echo "<!-- DEBUG: Evento de prueba creado -->";
            $evento_info = obtenerEventoInfo($evento_id);
        }
    }
}

// Procesamiento del formulario de registro
$success_message = "";
$error_message = "";
$show_success_alert = false;
$show_error_alert = false;
$registro_exitoso = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_registration'])) {
    echo "<!-- DEBUG: Procesando formulario POST -->";

    try {
        // Validar que el evento exista
        if (!$evento_info) {
            throw new Exception("El evento no existe o no está disponible");
        }

        // Datos del participante
        $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
        $email = htmlspecialchars(trim($_POST['email'] ?? ''));
        $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
        $documento = htmlspecialchars(trim($_POST['documento'] ?? ''));
        $tipo_documento = htmlspecialchars(trim($_POST['tipo_documento'] ?? ''));

        echo "<!-- DEBUG: Datos recibidos - Nombre: $nombre, Email: $email, Documento: $documento -->";

        // Validación básica
        if (empty($nombre) || empty($email) || empty($documento) || empty($tipo_documento)) {
            throw new Exception("Los campos marcados con * son obligatorios");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email ingresado no es válido");
        }

        // Preparar datos para registro
        $datos_registro = [
            'id_evento' => $evento_id,
            'nombre_completo' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'numero_documento' => $documento,
            'tipo_documento' => $tipo_documento
        ];

        // Registrar asistencia (SIN VALIDAR CUPOS)
        $resultado = registrarAsistencia($datos_registro);

        if ($resultado['success']) {
            $success_message = "¡Registro exitoso! Te has inscrito correctamente al evento: " . $evento_info['nombre'];
            $show_success_alert = true;
            $registro_exitoso = $resultado;

            // Limpiar formulario
            $_POST = array();

            // Actualizar información del evento
            $evento_info = obtenerEventoInfo($evento_id);
        }
    } catch (Exception $e) {
        echo "<!-- DEBUG: Error capturado: " . $e->getMessage() . " -->";
        $error_message = $e->getMessage();
        $show_error_alert = true;
    }
}

// Tipos de documento
$tipos_documento = [
    'CC' => 'Cédula de Ciudadanía',
    'CE' => 'Cédula de Extranjería',
    'TI' => 'Tarjeta de Identidad',
    'PP' => 'Pasaporte',
    'NIT' => 'NIT'
];

$tipos_poblacion = [];
$query = "SELECT id_poblacion, nombre_poblacion FROM poblacion ORDER BY nombre_poblacion ASC";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tipos_poblacion[$row['id_poblacion']] = $row['nombre_poblacion'];
    }
} else {
    echo "<!-- DEBUG: Error al consultar poblaciones: " . $conn->error . " -->";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo $evento_info ? $evento_info['nombre'] : 'Evento'; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ogenix-primary: #50a72c;
            --ogenix-primary-dark: #408c23;
            --ogenix-success: #327516;
            --ogenix-danger: #d42929;
            --ogenix-warning: #f59e0b;
            --ogenix-light: #f3f3ed;
            --ogenix-dark: #37542b;
            --light-bg: #f8fafc;
            --dark-text: #1e293b;
            --gray-text: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            padding: 2rem 0;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .main-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Breadcrumb */
        .breadcrumb-nav {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin-bottom: 20px;
        }

        .breadcrumb-nav a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
        }

        .breadcrumb-nav a:hover {
            color: white;
        }

        /* Card única principal */
        .main-event-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Header del evento */
        .event-header {
            background: linear-gradient(135deg, var(--ogenix-primary) 0%, var(--ogenix-primary-dark) 100%);
            color: white;
            padding: 3rem 2.5rem;
            text-align: center;
            position: relative;
        }

        .event-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="0.8" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .event-header-content {
            position: relative;
            z-index: 1;
        }

        .event-title {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .event-subtitle {
            font-size: 1.1rem;
            opacity: 0.95;
            font-weight: 400;
        }

        /* Contenido principal */
        .event-content {
            padding: 2.5rem;
        }

        /* Información del evento */
        .event-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, auto);
            gap: 20px;
            margin-bottom: 30px;
            padding: 25px;
            background: rgba(248, 249, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(80, 167, 44, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--ogenix-primary);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            background: rgba(255, 255, 255, 0.9);
        }

        .info-icon {
            color: var(--ogenix-primary);
            font-size: 1.8rem;
            min-width: 32px;
        }

        .info-content h5 {
            color: var(--dark-text);
            font-weight: 600;
            margin: 0 0 5px 0;
            font-size: 0.9rem;
        }

        .info-content p {
            color: var(--gray-text);
            margin: 0;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Descripción del evento */
        .event-description {
            margin-bottom: 40px;
        }

        .event-description h4 {
            color: var(--dark-text);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .event-description p {
            color: var(--gray-text);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        /* Formulario de registro */
        .registration-section {
            background: rgba(248, 249, 255, 0.7);
            backdrop-filter: blur(15px);
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid rgba(80, 167, 44, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .registration-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .registration-header h3 {
            color: var(--ogenix-primary);
            font-weight: 700;
            font-size: 1.6rem;
            margin-bottom: 8px;
        }

        .registration-header p {
            color: var(--gray-text);
            font-size: 0.95rem;
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .required {
            color: var(--ogenix-danger);
            font-weight: 700;
        }

        .form-control,
        .form-select {
            border: 2px solid rgba(226, 232, 240, 0.6);
            border-radius: 15px;
            padding: 14px 18px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--ogenix-primary);
            box-shadow: 0 0 0 0.3rem rgba(80, 167, 44, 0.15), 0 4px 20px rgba(80, 167, 44, 0.1);
            background-color: rgba(255, 255, 255, 0.95);
            outline: none;
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: var(--gray-text);
            font-size: 0.9rem;
            font-weight: 400;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-text);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-icon .form-control,
        .input-icon .form-select {
            padding-left: 52px;
        }

        .input-icon .form-control:focus+i,
        .input-icon .form-select:focus+i {
            color: var(--ogenix-primary);
            transform: translateY(-50%) scale(1.1);
        }

        .btn-register {
            background: linear-gradient(135deg, var(--ogenix-primary) 0%, var(--ogenix-primary-dark) 100%);
            border: none;
            color: white;
            padding: 16px 2rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
            box-shadow: 0 6px 20px rgba(80, 167, 44, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, var(--ogenix-primary-dark) 0%, #2d5f18 100%);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(80, 167, 44, 0.5);
        }

        .btn-register:disabled {
            background: #6c757d !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn-clear {
            background: rgba(108, 117, 125, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 16px 2rem;
            border-radius: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-clear:hover {
            background: rgba(84, 91, 98, 0.95);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }

        .no-event {
            background: white;
            border-radius: 20px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .no-event i {
            font-size: 4rem;
            color: var(--ogenix-primary);
            margin-bottom: 1.5rem;
        }

        .no-event h2 {
            color: var(--dark-text);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .no-event p {
            color: var(--gray-text);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .btn-back {
            background: var(--ogenix-primary);
            color: white;
            text-decoration: none;
            padding: 12px 2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: var(--ogenix-primary-dark);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        /* Debug info */
        .debug-info {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem 0;
            }

            .main-container {
                padding: 0 15px;
            }

            .event-header {
                padding: 2rem 1.5rem;
            }

            .event-content {
                padding: 2rem 1.5rem;
            }

            .event-title {
                font-size: 2.2rem;
            }

            .event-info-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .registration-section {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .event-title {
                font-size: 1.8rem;
            }

            .event-info-grid {
                padding: 15px;
            }

            .info-item {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {

            0%,
            20%,
            40%,
            60%,
            80% {
                transform: translateX(-2px);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(2px);
            }
        }

        /* Personalización de SweetAlert2 */
        .swal2-popup {
            border-radius: 15px !important;
            font-family: 'Inter', sans-serif !important;
        }

        .swal2-title {
            font-weight: 600 !important;
        }

        .swal2-confirm {
            background-color: var(--ogenix-primary) !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 10px 25px !important;
        }

        .swal2-confirm:hover {
            background-color: var(--ogenix-primary-dark) !important;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <?php if ($evento_info): ?>

            <!-- Card principal única -->
            <div class="main-event-card">
                <!-- Header del evento -->
                <div class="event-header">
                    <div class="event-header-content">
                        <h1 class="event-title"><?php echo htmlspecialchars($evento_info['nombre']); ?></h1>
                        <p class="event-subtitle">Te invitamos a participar en este increíble evento</p>
                    </div>
                </div>

                <!-- Contenido principal -->
                <div class="event-content">
                    <!-- Información del evento -->
                    <div class="event-info-grid">
                        <div class="info-item">
                            <i class="bi bi-calendar-event info-icon"></i>
                            <div class="info-content">
                                <h5>Fecha de Inicio</h5>
                                <p>
                                    <?php
                                    if ($evento_info['fecha_inicio']) {
                                        $fecha = new DateTime($evento_info['fecha_inicio']);
                                        echo $fecha->format('d/m/Y - H:i');
                                    } else {
                                        echo 'Por definir';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="bi bi-geo-alt info-icon"></i>
                            <div class="info-content">
                                <h5>Ubicación</h5>
                                <p><?php echo htmlspecialchars($evento_info['ubicacion'] ?? 'Por definir'); ?></p>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="bi bi-gift info-icon"></i>
                            <div class="info-content">
                                <h5>Entrada</h5>
                                <p>Evento Gratuito</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="bi bi-people info-icon"></i>
                            <div class="info-content">
                                <h5>Aforo Máximo</h5>
                                <p><?php echo number_format($evento_info['aforo_maximo'] ?? 0); ?> personas</p>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción del evento -->
                    <div class="event-description">
                        <h4>Descripción del Evento</h4>
                        <p><?php echo nl2br(htmlspecialchars($evento_info['descripcion'] ?? 'Descripción del evento por definir.')); ?></p>
                    </div>

                    <!-- Formulario de registro -->
                    <div class="registration-section">
                        <div class="registration-header">
                            <h3><i class="bi bi-person-plus me-2"></i>Formulario de Registro</h3>
                            <p>Complete sus datos para registrarse en el evento</p>
                        </div>

                        <form id="formRegistro" onsubmit="return Register();" action="" class="needs-validation" novalidate>
                            <input type="hidden" name="evento_id" value="<?php echo $evento_id; ?>">

                            <div class="row">
                                <!-- Nombre completo -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre" class="form-label">
                                            <i class="bi bi-person"></i>
                                            Nombre Completo <span class="required">*</span>
                                        </label>
                                        <div class="input-icon">
                                            <input type="text"
                                                class="form-control"
                                                id="nombre"
                                                name="nombre"
                                                placeholder="Ingrese su nombre completo"
                                                value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"
                                                required>
                                            <i class="bi bi-person"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">
                                            <i class="bi bi-envelope"></i>
                                            Correo Electrónico <span class="required">*</span>
                                        </label>
                                        <div class="input-icon">
                                            <input type="email"
                                                class="form-control"
                                                id="email"
                                                name="email"
                                                placeholder="correo@ejemplo.com"
                                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                                required>
                                            <i class="bi bi-envelope"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Tipo de documento -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipo_documento" class="form-label">
                                            <i class="bi bi-card-text"></i>
                                            Tipo de Documento <span class="required">*</span>
                                        </label>
                                        <div class="input-icon">
                                            <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                                <option value="">Seleccione</option>
                                                <?php foreach ($tipos_documento as $value => $label): ?>
                                                    <option value="<?php echo $value; ?>"
                                                        <?php echo (($_POST['tipo_documento'] ?? '') === $value) ? 'selected' : ''; ?>>
                                                        <?php echo $label; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <i class="bi bi-card-text"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Número de documento -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="documento" class="form-label">
                                            <i class="bi bi-credit-card"></i>
                                            Número de Documento <span class="required">*</span>
                                        </label>
                                        <div class="input-icon">
                                            <input type="text"
                                                class="form-control"
                                                id="documento"
                                                name="documento"
                                                placeholder="Número de documento"
                                                value="<?php echo htmlspecialchars($_POST['documento'] ?? ''); ?>"
                                                required>
                                            <i class="bi bi-credit-card"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Teléfono -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="telefono" class="form-label">
                                            <i class="bi bi-telephone"></i>
                                            Teléfono
                                        </label>
                                        <div class="input-icon">
                                            <input type="tel"
                                                class="form-control"
                                                id="telefono"
                                                name="telefono"
                                                placeholder="Número de teléfono"
                                                value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
                                            <i class="bi bi-telephone"></i>
                                        </div>
                                    </div>
                                </div>


                                <!-- Tipo de Poblacion -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_poblacion" class="form-label">
                                            <i class="bi bi-people"></i>
                                            Tipo de Población <span class="required">*</span>
                                        </label>
                                        <div class="input-icon">
                                            <select class="form-select" id="tipo_poblacion" name="tipo_poblacion" required>
                                                <option value="">Seleccione</option>
                                                <?php foreach ($tipos_poblacion as $id => $nombre): ?>
                                                    <option value="<?php echo $id; ?>"
                                                        <?php echo (($_POST['tipo_poblacion'] ?? '') == $id) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($nombre); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <i class="bi bi-people"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fecha de Nacimiento -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_nacimiento" class="form-label">
                                            <i class="bi bi-calendar3"></i>
                                            Fecha de Nacimiento <span class="required">*</span>
                                        </label>
                                        <div class="input-icon">
                                            <input type="date"
                                                class="form-control"
                                                id="fecha_nacimiento"
                                                name="fecha_nacimiento"
                                                value="<?php echo htmlspecialchars($_POST['fecha_nacimiento'] ?? ''); ?>"
                                                max="<?php echo date('Y-m-d'); ?>"
                                                required>
                                            <i class="bi bi-calendar3"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="row mt-4">
                                <div class="col-md-8">
                                    <button type="submit" name="submit_registration" class="btn btn-register">
                                        <i class="bi bi-check-lg me-2"></i>Registrarme al Evento
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="reset" class="btn btn-clear">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- No se encontró el evento -->
            <div class="no-event">
                <i class="bi bi-calendar-x"></i>
                <h2>Evento No Encontrado</h2>
                <p>Lo sentimos, no pudimos encontrar el evento que está buscando. Verifique el enlace o seleccione un evento válido.</p>
                <p><strong>ID solicitado:</strong> <?php echo $evento_id; ?></p>
                <a href="eventos.php" class="btn-back">
                    <i class="bi bi-arrow-left"></i>Volver a Eventos
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.js"></script>

    <script>
        function Register() {
            const form = document.querySelector('#formRegistro');
            const formData = new FormData(form);
            formData.append('action', 'registrar');

            fetch('assets/components/eventos/registrar_evento.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#50a72c'
                        }).then(() => {
                            const eventoId = formData.get('evento_id');
                            window.location.href = window.location.pathname + '?id=' + encodeURIComponent(eventoId);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Cerrar',
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    // console.error('Error:', error);
                    Swal.fire({
                        title: 'Error de conexión',
                        text: 'Ocurrió un error al intentar registrar. Intente nuevamente.',
                        icon: 'error'
                    });
                });

            return false;
        }

        // Mostrar SweetAlert si hay mensajes
        <?php if ($show_success_alert): ?>
            Swal.fire({
                title: '¡Registro Exitoso!',
                html: `
                <div style="text-align: left; margin: 20px 0;">
                    <p><strong><?php echo addslashes($success_message); ?></strong></p>
                    <?php if ($registro_exitoso): ?>
                    <hr style="margin: 15px 0;">
                    <p><i class="bi bi-qr-code"></i> <strong>Código QR:</strong> <?php echo $registro_exitoso['qr_code']; ?></p>
                    <p><i class="bi bi-hash"></i> <strong>ID de Registro:</strong> #<?php echo $registro_exitoso['id_registro']; ?></p>
                    <p><i class="bi bi-info-circle"></i> <small>Presente este código QR el día del evento para confirmar su asistencia.</small></p>
                    <?php endif; ?>
                </div>
            `,
                icon: 'success',
                confirmButtonText: 'Perfecto',
                confirmButtonColor: '#50a72c',
                timer: 10000,
                timerProgressBar: true
            }).then((result) => {
                if (result.isConfirmed || result.isDismissed) {
                    window.location.href = window.location.href.split('?')[0] + '?id=<?php echo $evento_id; ?>';
                }
            });
        <?php endif; ?>

        <?php if ($show_error_alert): ?>
            Swal.fire({
                title: 'Error en el Registro',
                text: '<?php echo addslashes($error_message); ?>',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d42929'
            });
        <?php endif; ?>

            // Validación del formulario
            (function() {
                'use strict';
                window.addEventListener('load', function() {
                    var forms = document.getElementsByClassName('needs-validation');
                    var validation = Array.prototype.filter.call(forms, function(form) {
                        form.addEventListener('submit', function(event) {
                            if (form.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();

                                Swal.fire({
                                    title: 'Campos Incompletos',
                                    text: 'Por favor complete todos los campos obligatorios marcados con *',
                                    icon: 'warning',
                                    confirmButtonText: 'Revisar',
                                    confirmButtonColor: '#f59e0b'
                                });
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();

        // Validación en tiempo real para email
        document.getElementById('email')?.addEventListener('blur', function() {
            const email = this.value;
            if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                this.style.borderColor = 'var(--ogenix-danger)';
                this.style.boxShadow = '0 0 0 0.2rem rgba(212, 41, 41, 0.15)';
            } else {
                this.style.borderColor = 'var(--border-color)';
                this.style.boxShadow = 'none';
            }
        });

        // Formatear número de documento (solo números)
        document.getElementById('documento')?.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Formatear teléfono (solo números y algunos caracteres especiales)
        document.getElementById('telefono')?.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
        });

        // Confirmar antes de limpiar el formulario
        document.querySelector('button[type="reset"]')?.addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: '¿Limpiar Formulario?',
                text: 'Se perderán todos los datos ingresados',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#50a72c'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('form').reset();
                    document.querySelector('form').classList.remove('was-validated');

                    Swal.fire({
                        title: 'Formulario Limpiado',
                        text: 'Puede comenzar a llenar los datos nuevamente',
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        });

        // Efectos visuales adicionales
        document.addEventListener('DOMContentLoaded', function() {
            // Animación suave para los campos al hacer foco
            const formControls = document.querySelectorAll('.form-control, .form-select');
            formControls.forEach(function(control) {
                control.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                    this.parentElement.style.transition = 'transform 0.2s ease';
                });

                control.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Tooltip para campos requeridos
            const requiredFields = document.querySelectorAll('input[required], select[required]');
            requiredFields.forEach(function(field) {
                field.addEventListener('invalid', function() {
                    this.style.borderColor = 'var(--ogenix-danger)';
                    this.style.animation = 'shake 0.5s ease-in-out';
                });

                field.addEventListener('input', function() {
                    if (this.validity.valid) {
                        this.style.borderColor = 'var(--ogenix-success)';
                        this.style.animation = 'none';
                    }
                });
            });
        });

        // Prevenir envío múltiple del formulario
        document.querySelector('form')?.addEventListener('submit', function() {
            const submitBtn = document.querySelector('button[name="submit_registration"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Procesando...';

                setTimeout(() => {
                    if (!submitBtn.closest('form').querySelector('.was-validated')) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Registrarme al Evento';
                    }
                }, 3000);
            }
        });

        // console.log('🎯 Sistema de registro cargado correctamente');
        // console.log('📊 Evento ID:', <?php echo $evento_id; ?>);
        <?php if ($evento_info): ?>
            // console.log('✅ Evento encontrado:', '<?php echo addslashes($evento_info['nombre']); ?>');
        <?php endif; ?>

        document.getElementById('fecha_nacimiento')?.addEventListener('change', function() {
            const fechaNacimiento = new Date(this.value);
            const hoy = new Date();

            if (fechaNacimiento > hoy) {
                this.style.borderColor = 'var(--ogenix-danger)';
                this.style.boxShadow = '0 0 0 0.2rem rgba(212, 41, 41, 0.15)';
                Swal.fire({
                    title: 'Fecha Inválida',
                    text: 'La fecha de nacimiento no puede ser una fecha futura',
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b'
                });
                this.value = '';
            } else {
                this.style.borderColor = 'var(--ogenix-success)';
                this.style.boxShadow = '0 0 0 0.2rem rgba(50, 117, 22, 0.15)';
            }
        });
    </script>
</body>

</html>