<?php
// Ejemplo de uso de PHPMailer con Composer
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);
    
    // Configuración SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tu_email@gmail.com';
    $mail->Password = 'tu_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Destinatarios
    $mail->setFrom('from@example.com', 'Nombre Remitente');
    $mail->addAddress('to@example.com', 'Nombre Destinatario');
    
    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Asunto del correo';
    $mail->Body = '<h1>Hola</h1><p>Este es un correo de prueba.</p>';
    
    $mail->send();
    echo 'Correo enviado exitosamente';
    
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}
?>