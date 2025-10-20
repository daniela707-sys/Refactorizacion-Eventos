<?php
// gmail_api_manual.php - Implementación manual sin Google Client Library
header('Content-Type: application/json');

// Configuración de Gmail API (manual)
class GmailAPIManual {
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $token_file = '../../config/gmail_token.json';
    
    public function __construct() {
        // Cargar configuración desde archivo externo
        $config_file = __DIR__ . '/config/gmail_config.php';
        if (file_exists($config_file)) {
            $config = require $config_file;
            $this->client_id = $config['client_id'];
            $this->client_secret = $config['client_secret'];
            $this->redirect_uri = $config['redirect_uri'];
        } else {
            throw new Exception('Archivo de configuración gmail_config.php no encontrado');
        }
    }
    
    // Función para obtener token usando cURL
    public function getAccessToken($refresh_token = null) {
        if ($refresh_token) {
            // Renovar token existente
            $data = [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token'
            ];
        } else {
            // Primer token (necesitas el código de autorización)
            throw new Exception('Necesitas configurar OAuth primero');
        }
        
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Error renovando token: ' . $response);
        }
        
        return json_decode($response, true);
    }
    
    // Función para enviar correo usando Gmail API con cURL
    public function sendEmail($to, $subject, $message_html, $message_text = '') {
        // Cargar token existente
        if (!file_exists($this->token_file)) {
            throw new Exception('Token no encontrado. Configura OAuth primero.');
        }
        
        $token_data = json_decode(file_get_contents($this->token_file), true);
        $access_token = $token_data['access_token'];
        
        // Verificar si el token ha expirado y renovarlo
        if (isset($token_data['expires_in']) && isset($token_data['created'])) {
            $token_age = time() - $token_data['created'];
            if ($token_age >= $token_data['expires_in']) {
                // Token expirado, renovar
                if (isset($token_data['refresh_token'])) {
                    $new_token = $this->getAccessToken($token_data['refresh_token']);
                    $new_token['created'] = time();
                    $new_token['refresh_token'] = $token_data['refresh_token']; // Mantener refresh token
                    file_put_contents($this->token_file, json_encode($new_token));
                    $access_token = $new_token['access_token'];
                } else {
                    throw new Exception('Token expirado sin refresh token');
                }
            }
        }
        
        // Crear mensaje MIME
        $boundary = uniqid('boundary_');
        $from = 'Red Emprendedores <avilajoseph2021@gmail.com>';
        
        $headers = [
            "MIME-Version: 1.0",
            "To: {$to}",
            "From: {$from}",
            "Subject: {$subject}",
            "Content-Type: multipart/alternative; boundary=\"{$boundary}\""
        ];
        
        $body = "--{$boundary}\r\n";
        
        // Texto plano
        if ($message_text) {
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
            $body .= quoted_printable_encode($message_text) . "\r\n\r\n";
            $body .= "--{$boundary}\r\n";
        }
        
        // HTML
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $body .= quoted_printable_encode($message_html) . "\r\n\r\n";
        $body .= "--{$boundary}--";
        
        $raw_message = implode("\r\n", $headers) . "\r\n\r\n" . $body;
        $encoded_message = rtrim(strtr(base64_encode($raw_message), '+/', '-_'), '=');
        
        // Enviar usando Gmail API
        $data = json_encode(['raw' => $encoded_message]);
        
        $ch = curl_init('https://gmail.googleapis.com/gmail/v1/users/me/messages/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Error enviando correo: ' . $response);
        }
        
        return json_decode($response, true);
    }
    
    // Generar URL de autorización OAuth
    public function getAuthUrl() {
        $params = [
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'scope' => 'https://www.googleapis.com/auth/gmail.send',
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];
        
        return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
    }
    
    // Intercambiar código por token
    public function exchangeCodeForToken($code) {
        $data = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'grant_type' => 'authorization_code',
            'code' => $code
        ];
        
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Error obteniendo token: ' . $response);
        }
        
        $token_data = json_decode($response, true);
        $token_data['created'] = time();
        
        // Crear directorio si no existe
        $dir = dirname($this->token_file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Guardar token
        file_put_contents($this->token_file, json_encode($token_data));
        
        return $token_data;
    }
}

// Resto del código igual que antes...
?>