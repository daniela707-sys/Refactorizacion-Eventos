<?php
// gmail_api.php - Solo la clase GmailAPI
class GmailAPI {
    private $config;
    private $token_file;
    
    public function __construct() {
        $this->token_file = __DIR__ . '/gmail_token.json';
        
        if (!is_dir(dirname($this->token_file))) {
            mkdir(dirname($this->token_file), 0755, true);
        }
        
        // Cargar configuración desde archivo externo
        $config_file = __DIR__ . '/gmail_config.php';
        if (file_exists($config_file)) {
            $this->config = require $config_file;
        } else {
            throw new Exception('Archivo de configuración gmail_config.php no encontrado');
        }
    }
    
    private function refreshAccessToken($refresh_token) {
        $data = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token'
        ];
        
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Error renovando token: HTTP ' . $httpCode);
        }
        
        return json_decode($response, true);
    }
    
    private function getValidToken() {
        if (!file_exists($this->token_file)) {
            throw new Exception('Token no encontrado. Autoriza primero.');
        }
        
        $token_data = json_decode(file_get_contents($this->token_file), true);
        
        if (!$token_data || empty($token_data['access_token'])) {
            throw new Exception('Token inválido');
        }
        
        if (isset($token_data['expires_in']) && isset($token_data['created'])) {
            $token_age = time() - $token_data['created'];
            if ($token_age >= ($token_data['expires_in'] - 300)) {
                if (!empty($token_data['refresh_token'])) {
                    $new_token = $this->refreshAccessToken($token_data['refresh_token']);
                    $new_token['created'] = time();
                    $new_token['refresh_token'] = $token_data['refresh_token'];
                    file_put_contents($this->token_file, json_encode($new_token, JSON_PRETTY_PRINT));
                    return $new_token['access_token'];
                }
                throw new Exception('Token expirado');
            }
        }
        
        return $token_data['access_token'];
    }
    
    public function sendEmail($to, $subject, $html_body, $text_body = '') {
        $access_token = $this->getValidToken();
        $encoded_message = $this->createMimeMessage($to, $subject, $html_body, $text_body);
        
        $ch = curl_init('https://gmail.googleapis.com/gmail/v1/users/me/messages/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['raw' => $encoded_message]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Gmail API Error: ' . $response);
        }
        
        return json_decode($response, true);
    }
    
    private function createMimeMessage($to, $subject, $html_body, $text_body = '') {
        $boundary = uniqid('boundary_');
        $from = 'Red Emprendedores <avilajoseph2021@gmail.com>';
        
        $headers = [
            "MIME-Version: 1.0",
            "To: {$to}",
            "From: {$from}",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
            "Content-Type: multipart/alternative; boundary=\"{$boundary}\""
        ];
        
        $body = "--{$boundary}\r\n";
        if ($text_body) {
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n{$text_body}\r\n\r\n--{$boundary}\r\n";
        }
        $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n{$html_body}\r\n\r\n--{$boundary}--";
        
        $raw_message = implode("\r\n", $headers) . "\r\n\r\n" . $body;
        return rtrim(strtr(base64_encode($raw_message), '+/', '-_'), '=');
    }
    
    public function getAuthUrl() {
        $params = [
            'client_id' => $this->config['client_id'],
            'redirect_uri' => $this->config['redirect_uri'],
            'scope' => 'https://www.googleapis.com/auth/gmail.send',
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];
        
        return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
    }
    
    public function exchangeCodeForToken($code) {
        $data = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri' => $this->config['redirect_uri'],
            'grant_type' => 'authorization_code',
            'code' => $code
        ];
        
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Error obteniendo token: ' . $response);
        }
        
        $token_data = json_decode($response, true);
        $token_data['created'] = time();
        
        file_put_contents($this->token_file, json_encode($token_data, JSON_PRETTY_PRINT));
        return $token_data;
    }
}
?>