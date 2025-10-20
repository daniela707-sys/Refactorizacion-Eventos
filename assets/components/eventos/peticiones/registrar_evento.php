<?php
// registrar_evento.php - Versión QR SÚPER SIMPLE: solo documento + evento
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers para JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Si es una petición OPTIONS, responder inmediatamente
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Función para log de errores detallado
function logError($message, $context = []) {
    $log_message = date('[Y-m-d H:i:s] ') . $message;
    if (!empty($context)) {
        $log_message .= ' | Context: ' . json_encode($context);
    }
    error_log($log_message);
}

// 1. Función Gmail API (principal)
function enviarCorreoGmailAPI($datos, $evento_info, $qr_code, $id_registro) {
    try {
        error_log("🚀 Intentando Gmail API");
        
        // Incluir clase Gmail API
        $gmail_api_path = __DIR__ . '/config/gmail_api.php';
        
        if (!file_exists($gmail_api_path)) {
            throw new Exception("gmail_api.php no encontrado");
        }
        
        require_once($gmail_api_path);
        $gmail = new GmailAPI();
        
        // Para el QR visual en el correo, usar el código completo
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qr_code);
        
        $fecha_evento = 'Fecha no definida';
        if (!empty($evento_info['fecha_inicio'])) {
            try {
                $fecha = new DateTime($evento_info['fecha_inicio']);
                $fecha_evento = $fecha->format('d/m/Y - H:i');
            } catch (Exception $e) {
                $fecha_evento = 'Fecha no válida';
            }
        }
        
        $subject = 'Confirmación de Registro - ' . $evento_info['nombre'];
        
        $html_body = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Confirmación de Registro</title></head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: #50a72c; color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px;">¡Registro Confirmado!</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 30px;">
            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                Hola <strong>' . htmlspecialchars($datos['nombre_completo']) . '</strong>,
            </p>
            
            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                Tu registro al evento <strong>' . htmlspecialchars($evento_info['nombre']) . '</strong> 
                ha sido confirmado exitosamente.
            </p>
            
            <!-- Event Details -->
            <div style="background: #f8f9fa; border-left: 4px solid #50a72c; padding: 20px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #333;">📅 Detalles del Evento</h3>
                <p style="margin: 5px 0;"><strong>Evento:</strong> ' . htmlspecialchars($evento_info['nombre']) . '</p>
                <p style="margin: 5px 0;"><strong>Fecha:</strong> ' . $fecha_evento . '</p>
                <p style="margin: 5px 0;"><strong>Ubicación:</strong> ' . htmlspecialchars($evento_info['ubicacion'] ?? 'Por definir') . '</p>
            </div>
            
            <!-- QR Code -->
            <div style="background: #f0f7ed; border: 2px solid #50a72c; padding: 20px; margin: 20px 0; text-align: center; border-radius: 8px;">
                <h3 style="margin-top: 0; color: #50a72c;">🎫 Tu Código QR</h3>
                <p><strong>ID:</strong> #' . $id_registro . '</p>
                <p><strong>Código:</strong> ' . $qr_code . '</p>
                
                <div style="margin: 20px 0;">
                    <img src="' . $qr_url . '" alt="Código QR" style="max-width: 200px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                
                <p style="font-size: 14px; color: #666;">
                    📱 Presenta este QR el día del evento
                </p>
            </div>
            
            <p style="font-size: 16px; line-height: 1.6; margin-top: 30px;">
                ¡Nos vemos en el evento!<br>
                <strong>Equipo Red Emprendedores</strong>
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background: #f8f9fa; padding: 15px; text-align: center; border-top: 1px solid #eee;">
            <p style="margin: 0; font-size: 12px; color: #888;">
                Este es un correo automático. No respondas a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>';

        $text_body = "Confirmación de Registro\n\n" .
            "Hola " . $datos['nombre_completo'] . ",\n\n" .
            "Tu registro al evento '" . $evento_info['nombre'] . "' ha sido confirmado.\n\n" .
            "ID de Registro: #" . $id_registro . "\n" .
            "Código QR: " . $qr_code . "\n\n" .
            "Equipo Red Emprendedores";
        
        // Enviar con Gmail API
        $result = $gmail->sendEmail($datos['email'], $subject, $html_body, $text_body);
        
        if ($result) {
            error_log("✅ Correo enviado exitosamente via Gmail API");
            return true;
        } else {
            error_log("❌ Gmail API falló");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("❌ Gmail API Exception: " . $e->getMessage());
        return false;
    }
}

// 2. Función PHPMailer mejorada (respaldo)
function enviarCorreoPHPMailerMejorado($datos, $evento_info, $qr_code, $id_registro) {
    try {
        error_log("📄 Intentando PHPMailer como respaldo");
        
        // Buscar PHPMailer en múltiples ubicaciones
        $base_paths = [
            __DIR__ . '/PHPMailer/src/',
            __DIR__ . '/../PHPMailer/src/',
            __DIR__ . '/../../PHPMailer/src/',
            __DIR__ . '/../../../PHPMailer/src/',
            __DIR__ . '/../../../../PHPMailer/src/'
        ];
        
        $phpmailer_loaded = false;
        foreach ($base_paths as $phpmailer_dir) {
            if (file_exists($phpmailer_dir . 'PHPMailer.php') && 
                file_exists($phpmailer_dir . 'SMTP.php') && 
                file_exists($phpmailer_dir . 'Exception.php')) {
                
                if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                    include $phpmailer_dir . 'Exception.php';
                    include $phpmailer_dir . 'PHPMailer.php';
                    include $phpmailer_dir . 'SMTP.php';
                }
                
                $phpmailer_loaded = true;
                error_log("✅ PHPMailer encontrado en: " . $phpmailer_dir);
                break;
            }
        }
        
        if (!$phpmailer_loaded) {
            throw new Exception("PHPMailer no encontrado en ninguna ubicación");
        }
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuración Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'senareddeemprendedores@gmail.com';
        $mail->Password = 'piaetogvaufgdsvp'; // App password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Configurar correo
        $mail->setFrom('senareddeemprendedores@gmail.com', 'Red Emprendedores');
        $mail->addAddress($datos['email'], $datos['nombre_completo']);
        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de Registro - ' . $evento_info['nombre'];
        
        // QR URL para el correo
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qr_code);
        
        $fecha_evento = 'Fecha no definida';
        if (!empty($evento_info['fecha_inicio'])) {
            try {
                $fecha = new DateTime($evento_info['fecha_inicio']);
                $fecha_evento = $fecha->format('d/m/Y - H:i');
            } catch (Exception $e) {
                $fecha_evento = 'Fecha no válida';
            }
        }
        
        $html = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Confirmación de Registro</title></head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
        <div style="background: #50a72c; color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px;">¡Registro Confirmado!</h1>
        </div>
        <div style="padding: 30px;">
            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                Hola <strong>' . htmlspecialchars($datos['nombre_completo']) . '</strong>,
            </p>
            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                Tu registro al evento <strong>' . htmlspecialchars($evento_info['nombre']) . '</strong> ha sido confirmado exitosamente.
            </p>
            <div style="background: #f8f9fa; border-left: 4px solid #50a72c; padding: 20px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #333;">📅 Detalles del Evento</h3>
                <p style="margin: 5px 0;"><strong>Evento:</strong> ' . htmlspecialchars($evento_info['nombre']) . '</p>
                <p style="margin: 5px 0;"><strong>Fecha:</strong> ' . $fecha_evento . '</p>
                <p style="margin: 5px 0;"><strong>Ubicación:</strong> ' . htmlspecialchars($evento_info['ubicacion'] ?? 'Por definir') . '</p>
            </div>
            <div style="background: #f0f7ed; border: 2px solid #50a72c; padding: 20px; margin: 20px 0; text-align: center; border-radius: 8px;">
                <h3 style="margin-top: 0; color: #50a72c;">🎫 Tu Código QR</h3>
                <p><strong>ID:</strong> #' . $id_registro . '</p>
                <p><strong>Código:</strong> ' . $qr_code . '</p>
                <div style="margin: 20px 0;">
                    <img src="' . $qr_url . '" alt="Código QR" style="max-width: 200px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <p style="font-size: 14px; color: #666;">📱 Presenta este QR el día del evento</p>
            </div>
            <p style="font-size: 16px; line-height: 1.6; margin-top: 30px;">
                ¡Nos vemos en el evento!<br><strong>Equipo Red Emprendedores</strong>
            </p>
        </div>
    </div>
</body>
</html>';
        
        $mail->Body = $html;
        $mail->AltBody = "Confirmación de Registro\n\nHola " . $datos['nombre_completo'] . ",\n\nTu registro al evento '" . $evento_info['nombre'] . "' ha sido confirmado.\n\nID: #" . $id_registro . "\nCódigo QR: " . $qr_code;
        
        if ($mail->send()) {
            error_log("✅ Correo enviado exitosamente via PHPMailer");
            return true;
        } else {
            error_log("❌ PHPMailer error: " . $mail->ErrorInfo);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("❌ PHPMailer Exception: " . $e->getMessage());
        return false;
    }
}

// 3. Función de respaldo ultra simple SIN PHPMailer
function enviarCorreoUltraSimple($datos, $evento_info, $qr_code, $id_registro) {
    try {
        $to = $datos['email'];
        $subject = 'Confirmación de Registro - ' . $evento_info['nombre'];
        
        $headers = array();
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        $headers[] = 'From: Red Emprendedores <senareddeemprendedores@gmail.com>';
        $headers[] = 'Reply-To: senareddeemprendedores@gmail.com';
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_code);
        
        $message = '<html><body style="font-family: Arial, sans-serif;">';
        $message .= '<div style="max-width: 500px; margin: 0 auto; padding: 20px;">';
        $message .= '<h2 style="color: #50a72c;">¡Registro Confirmado!</h2>';
        $message .= '<p>Hola <strong>' . htmlspecialchars($datos['nombre_completo']) . '</strong>,</p>';
        $message .= '<p>Tu registro al evento <strong>' . htmlspecialchars($evento_info['nombre']) . '</strong> ha sido confirmado.</p>';
        $message .= '<div style="border: 1px solid #ccc; padding: 15px; margin: 20px 0; text-align: center;">';
        $message .= '<h3>Tu Código QR</h3>';
        $message .= '<p><strong>ID:</strong> #' . $id_registro . '</p>';
        $message .= '<p><strong>Código:</strong> ' . $qr_code . '</p>';
        $message .= '<img src="' . $qr_url . '" alt="QR Code" style="max-width: 150px;">';
        $message .= '</div>';
        $message .= '<p>Saludos,<br><strong>Red Emprendedores</strong></p>';
        $message .= '</div></body></html>';
        
        if (mail($to, $subject, $message, implode("\r\n", $headers))) {
            error_log("✅ Correo simple enviado a: " . $datos['email']);
            return true;
        } else {
            error_log("❌ Error con mail() simple");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("❌ Error correo simple: " . $e->getMessage());
        return false;
    }
}

// 4. Función principal que prueba todos los métodos
function enviarCorreoDefinitivoCompleto($datos, $evento_info, $qr_code, $id_registro) {
    // Método 1: Gmail API (principal)
    if (enviarCorreoGmailAPI($datos, $evento_info, $qr_code, $id_registro)) {
        return ['success' => true, 'method' => 'Gmail API'];
    }
    
    // Método 2: PHPMailer con Gmail (respaldo)
    if (enviarCorreoPHPMailerMejorado($datos, $evento_info, $qr_code, $id_registro)) {
        return ['success' => true, 'method' => 'PHPMailer Gmail'];
    }
    
    // Método 3: mail() ultra simple (último recurso)
    if (enviarCorreoUltraSimple($datos, $evento_info, $qr_code, $id_registro)) {
        return ['success' => true, 'method' => 'mail() PHP'];
    }
    
    return ['success' => false, 'method' => 'ninguno'];
}

// LÓGICA PRINCIPAL
try {
    // Verificar método de petición
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método de petición no válido');
    }
    
    // Verificar acción
    if (!isset($_POST['action']) || $_POST['action'] !== 'registrar') {
        throw new Exception('Acción no válida');
    }
    
    // Incluir archivo de base de datos
    $db_paths = [
        __DIR__ . "/../../../../include/dbcommon.php",
        __DIR__ . "/../../../include/dbcommon.php",
        __DIR__ . "/../../include/dbcommon.php",
        __DIR__ . "/../include/dbcommon.php"
    ];
    
    $db_found = false;
    foreach ($db_paths as $path) {
        if (file_exists($path)) {
            require_once($path);
            $db_found = true;
            logError('Base de datos encontrada en: ' . $path);
            break;
        }
    }
    
    if (!$db_found) {
        throw new Exception("Archivo de conexión a base de datos no encontrado");
    }
    
    // Verificar conexión
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . ($conn->connect_error ?? 'Conexión no definida'));
    }
    
    // Obtener y validar datos del POST
    $evento_id = intval($_POST['evento_id'] ?? 0);
    $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $documento = htmlspecialchars(trim($_POST['documento'] ?? ''));
    $tipo_documento = htmlspecialchars(trim($_POST['tipo_documento'] ?? ''));
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($documento) || empty($tipo_documento)) {
        throw new Exception("Los campos marcados con * son obligatorios");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El email ingresado no es válido");
    }
    
    if ($evento_id <= 0) {
        throw new Exception("ID de evento inválido");
    }
    
    logError('Iniciando proceso de registro', [
        'evento_id' => $evento_id,
        'email' => $email,
        'nombre' => $nombre
    ]);
    
    // Verificar que el evento existe y está activo
    $query_evento = "SELECT id_evento, nombre, descripcion, ubicacion, fecha_inicio, cupos_disponibles 
                    FROM eventos WHERE id_evento = ?";
    $stmt_evento = $conn->prepare($query_evento);
    
    if (!$stmt_evento) {
        throw new Exception("Error en la consulta del evento: " . $conn->error);
    }
    
    $stmt_evento->bind_param("i", $evento_id);
    $stmt_evento->execute();
    $resultado_evento = $stmt_evento->get_result();
    $evento = $resultado_evento->fetch_assoc();
    $stmt_evento->close();
    
    if (!$evento) {
        throw new Exception("El evento no existe o no está disponible");
    }
    
    // Verificar cupos disponibles
    if ($evento['cupos_disponibles'] <= 0) {
        throw new Exception("No hay cupos disponibles para este evento");
    }
    
    // Verificar si ya está registrado
    $query_existente = "SELECT id_registro FROM registro_asistencia_evento 
                       WHERE id_evento = ? AND (email = ? OR id_usuario = ?)";
    $stmt_existente = $conn->prepare($query_existente);
    
    if (!$stmt_existente) {
        throw new Exception("Error en la consulta de registro existente: " . $conn->error);
    }
    
    $stmt_existente->bind_param("iss", $evento_id, $email, $documento);
    $stmt_existente->execute();
    $resultado_existente = $stmt_existente->get_result();
    
    if ($resultado_existente->num_rows > 0) {
        $stmt_existente->close();
        throw new Exception("Ya existe un registro con este email o documento para este evento");
    }
    $stmt_existente->close();
    
    // Iniciar transacción
    $conn->autocommit(false);
    
    try {
        // 🎯 QR SÚPER SIMPLE - Solo documento + evento
        $qr_code = "DOC_{$documento}_EVT_{$evento_id}";
        
        logError('QR simple generado', [
            'qr_code' => $qr_code,
            'documento' => $documento,
            'evento_id' => $evento_id
        ]);
        
        // Insertar registro con QR simple
        $query_insert = "INSERT INTO registro_asistencia_evento 
                        (id_evento, id_usuario, nombre_completo, email, tipo_documento, 
                         telefono, fecha_registro, asistio, qr) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW(), 0, ?)";
        
        $stmt_insert = $conn->prepare($query_insert);
        
        if (!$stmt_insert) {
            throw new Exception("Error preparando la inserción: " . $conn->error);
        }
        
        $stmt_insert->bind_param("issssss", 
            $evento_id, $documento, $nombre, $email, $tipo_documento, 
            $telefono, $qr_code
        );
        
        if (!$stmt_insert->execute()) {
            throw new Exception("Error al registrar la asistencia: " . $stmt_insert->error);
        }
        
        $id_registro = $conn->insert_id;
        $stmt_insert->close();
        
        logError('Registro insertado correctamente', ['id_registro' => $id_registro]);
        
        // Actualizar cupos disponibles
        $query_update = "UPDATE eventos SET cupos_disponibles = cupos_disponibles - 1 
                        WHERE id_evento = ? AND cupos_disponibles > 0";
        $stmt_update = $conn->prepare($query_update);
        
        if (!$stmt_update) {
            throw new Exception("Error preparando la actualización: " . $conn->error);
        }
        
        $stmt_update->bind_param("i", $evento_id);
        
        if (!$stmt_update->execute()) {
            throw new Exception("Error al actualizar cupos disponibles: " . $stmt_update->error);
        }
        
        if ($stmt_update->affected_rows == 0) {
            $stmt_update->close();
            throw new Exception("No se pudieron actualizar los cupos disponibles");
        }
        $stmt_update->close();
        
        // Confirmar transacción
        $conn->commit();
        logError('Transacción confirmada exitosamente');
        
        // ENVIAR CORREO CON NUEVO SISTEMA
        $datos_correo = [
            'nombre_completo' => $nombre,
            'email' => $email,
            'id_usuario' => $documento
        ];
        
        $correo_enviado = false;
        $metodo_envio = '';
        $error_correo = '';
        
        try {
            // Intentar enviar correo con el nuevo sistema
            $resultado_correo = enviarCorreoDefinitivoCompleto($datos_correo, $evento, $qr_code, $id_registro);
            
            if ($resultado_correo['success']) {
                $correo_enviado = true;
                $metodo_envio = $resultado_correo['method'];
                logError('✅ Correo enviado exitosamente con: ' . $metodo_envio);
            } else {
                $error_correo = 'No se pudo enviar con ningún método disponible';
                logError('❌ Error: ' . $error_correo);
            }
            
        } catch (Exception $e) {
            $error_correo = $e->getMessage();
            logError('❌ Error enviando correo: ' . $error_correo);
        }
        
        // Obtener cupos actualizados
        $query_cupos = "SELECT cupos_disponibles FROM eventos WHERE id_evento = ?";
        $stmt_cupos = $conn->prepare($query_cupos);
        $stmt_cupos->bind_param("i", $evento_id);
        $stmt_cupos->execute();
        $resultado_cupos = $stmt_cupos->get_result();
        $cupos_actualizados = $resultado_cupos->fetch_assoc()['cupos_disponibles'];
        $stmt_cupos->close();
        
        // Preparar mensaje de respuesta
        $mensaje_exito = '¡Registro exitoso! Te has inscrito correctamente al evento: ' . $evento['nombre'];
        
        if ($correo_enviado) {
            $mensaje_exito .= ' Se ha enviado un correo de confirmación con tu código QR.';
        } else {
            $mensaje_exito .= ' NOTA: No se pudo enviar el correo de confirmación, pero tu registro es válido.';
        }
        
        // Respuesta exitosa
        echo json_encode([
            'success' => true,
            'message' => $mensaje_exito,
            'data' => [
                'id_registro' => $id_registro,
                'id_usuario' => $documento,
                'qr_code' => $qr_code,
                'cupos_disponibles' => $cupos_actualizados,
                'nombre_evento' => $evento['nombre'],
                'correo_enviado' => $correo_enviado,
                'metodo_envio' => $metodo_envio,
                'error_correo' => $correo_enviado ? null : $error_correo
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit();
        
    } catch (Exception $e) {
        // Rollback en caso de error
        $conn->rollback();
        logError('Error en transacción, rollback ejecutado', ['error' => $e->getMessage()]);
        throw $e;
    } finally {
        $conn->autocommit(true);
    }
    
} catch (Exception $e) {
    logError('Error general en el proceso', ['error' => $e->getMessage()]);
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_details' => [
            'file' => basename(__FILE__),
            'line' => __LINE__,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit();
} catch (Error $e) {
    // Capturar errores fatales de PHP
    logError('Error fatal de PHP', ['error' => $e->getMessage()]);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'error_details' => [
            'type' => 'PHP Error',
            'message' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit();
}
?>