<?php
session_start();
require_once('gmail_api.php');
$gmail = new GmailAPI();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Configuración Gmail API - Red Emprendedores</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #27ae60; background: #d5f4e6; padding: 15px; border: 1px solid #27ae60; border-radius: 5px; margin: 15px 0; }
        .error { color: #e74c3c; background: #fadbd8; padding: 15px; border: 1px solid #e74c3c; border-radius: 5px; margin: 15px 0; }
        .warning { color: #f39c12; background: #fdf2e9; padding: 15px; border: 1px solid #f39c12; border-radius: 5px; margin: 15px 0; }
        .step { background: #ebf3fd; padding: 20px; margin: 15px 0; border-left: 4px solid #3498db; border-radius: 5px; }
        .auth-button { background: #4285f4; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; display: inline-block; font-size: 16px; font-weight: bold; transition: background 0.3s; }
        .auth-button:hover { background: #3367d6; }
        code { background: #f8f9fa; padding: 3px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
        .status-icon { font-size: 20px; margin-right: 10px; }
        h1 { color: #2c3e50; text-align: center; margin-bottom: 30px; }
        h3 { color: #34495e; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Configuración Gmail API</h1>
        <p style="text-align: center; color: #7f8c8d;">Red de Emprendedores - Sistema de Eventos</p>
        
        <?php
        if (isset($_GET['code'])) {
            try {
                $token_data = $gmail->exchangeCodeForToken($_GET['code']);
                
                echo '<div class="success">';
                echo '<span class="status-icon">✅</span>';
                echo '<h3>¡Configuración Completada Exitosamente!</h3>';
                echo '<p>Token de Gmail API guardado correctamente.</p>';
                echo '<p><strong>Estado:</strong> Listo para enviar correos</p>';
                echo '<p><strong>Proyecto:</strong> red-de-emprendedores-462019</p>';
                echo '</div>';
                
                echo '<div class="step">';
                echo '<h3>✨ ¿Qué sigue?</h3>';
                echo '<ol>';
                echo '<li>Tu sistema ya puede enviar correos usando Gmail API</li>';
                echo '<li>Los correos se enviarán desde: <code>avilajoseph2021@gmail.com</code></li>';
                echo '<li>El token se renovará automáticamente cuando expire</li>';
                echo '<li>Puedes probar el envío usando el formulario de abajo</li>';
                echo '</ol>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="error">';
                echo '<span class="status-icon">❌</span>';
                echo '<h3>Error en la configuración</h3>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<p>Por favor, intenta nuevamente o verifica las credenciales.</p>';
                echo '</div>';
            }
        }
        
        $token_file = __DIR__ . '/gmail_token.json';
        $token_exists = false;
        
        if (file_exists($token_file)) {
            $token_data = json_decode(file_get_contents($token_file), true);
            if (!empty($token_data['access_token'])) {
                $token_exists = true;
            }
        }

        if (!$token_exists) {
            echo '<div class="warning">';
            echo '<span class="status-icon">⚠️</span>';
            echo '<h3>Gmail API No Configurado</h3>';
            echo '<p>Necesitas completar la configuración OAuth para enviar correos.</p>';
            echo '</div>';
            
            echo '<div class="step">';
            echo '<h3>🔐 Paso 2: Autorizar Aplicación</h3>';
            echo '<p>Haz clic en el siguiente botón para autorizar el acceso a Gmail:</p>';
            echo '<div style="text-align: center; margin: 20px 0;">';
            echo '<a href="' . $gmail->getAuthUrl() . '" class="auth-button">';
            echo '🚀 Autorizar Gmail API';
            echo '</a>';
            echo '</div>';
            echo '<p><small>Serás redirigido a Google para autorizar el acceso y luego regresarás aquí.</small></p>';
            echo '</div>';
        }
        ?>
        
        <?php if ($token_exists): ?>
        <div class="step">
            <h3>🧪 Probar Envío de Correo</h3>
            <p>Envía un correo de prueba para verificar que todo funciona:</p>
            
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
                try {
                    $test_email = $_POST['test_email'];
                    
                    if (!filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
                        throw new Exception('Email inválido');
                    }
                    
                    $subject = 'Prueba Gmail API - Red Emprendedores';
                    $html_body = '
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                        <div style="background: #50a72c; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
                            <h1>✅ Prueba Exitosa</h1>
                        </div>
                        <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 8px 8px;">
                            <p>¡Excelente! Gmail API está funcionando correctamente.</p>
                            <p><strong>Configuración:</strong></p>
                            <ul>
                                <li>Proyecto: red-de-emprendedores-462019</li>
                                <li>Enviado desde: avilajoseph2021@gmail.com</li>
                                <li>Método: Gmail API (OAuth2)</li>
                            </ul>
                            <p>Tu sistema de eventos ya puede enviar correos de confirmación.</p>
                            <hr>
                            <p style="color: #666; font-size: 12px;">Este es un correo de prueba del sistema.</p>
                        </div>
                    </div>';
                    
                    $text_body = "Prueba Gmail API - Red Emprendedores\n\n¡Gmail API está funcionando correctamente!\n\nTu sistema ya puede enviar correos.";
                    
                    $result = $gmail->sendEmail($test_email, $subject, $html_body, $text_body);
                    
                    if ($result) {
                        echo '<div class="success">';
                        echo '<span class="status-icon">✅</span>';
                        echo '<strong>¡Correo enviado exitosamente!</strong><br>';
                        echo 'Destinatario: ' . htmlspecialchars($test_email) . '<br>';
                        echo 'Revisa la bandeja de entrada (y spam si es necesario).';
                        echo '</div>';
                    } else {
                        throw new Exception('Error enviando correo');
                    }
                    
                } catch (Exception $e) {
                    echo '<div class="error">';
                    echo '<span class="status-icon">❌</span>';
                    echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
            }
            ?>
            
            <form method="post" style="margin-top: 15px;">
                <div style="margin-bottom: 10px;">
                    <label><strong>Correo de destino:</strong></label><br>
                    <input type="email" name="test_email" required 
                           style="width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                           placeholder="tu@email.com">
                </div>
                <button type="submit" 
                        style="background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                    📧 Enviar Prueba
                </button>
            </form>
        </div>
        <?php endif; ?>
        
        <div class="step">
            <h3>📊 Estado del Sistema</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Gmail API</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        <?php if ($token_exists): ?>
                            <span style="color: #27ae60;">✅ Configurado</span>
                        <?php else: ?>
                            <span style="color: #e74c3c;">❌ Pendiente</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>PHPMailer (Respaldo)</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        <?php if (file_exists("../PHPMailer/src/PHPMailer.php")): ?>
                            <span style="color: #27ae60;">✅ Disponible</span>
                        <?php else: ?>
                            <span style="color: #f39c12;">⚠️ No encontrado</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>