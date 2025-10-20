# Configuración de Gmail API

## Configuración Inicial

1. **Copia el archivo de configuración:**
   ```bash
   cp gmail_config.example.php gmail_config.php
   ```

2. **Edita `gmail_config.php` con tus credenciales:**
   - `client_id`: Tu Client ID de Google OAuth
   - `client_secret`: Tu Client Secret de Google OAuth  
   - `redirect_uri`: URL de callback para OAuth
   - `project_id`: ID de tu proyecto en Google Cloud

3. **Copia el archivo de token:**
   ```bash
   cp gmail_token.example.json gmail_token.json
   ```

4. **Configura el token inicial** ejecutando el proceso de OAuth o copiando un token válido.

## Archivos Importantes

- `gmail_config.php` - Configuración con credenciales (NO subir a Git)
- `gmail_token.json` - Token de acceso (NO subir a Git)
- `gmail_config.example.php` - Plantilla de configuración
- `gmail_token.example.json` - Plantilla de token

## Seguridad

Los archivos `gmail_config.php` y `gmail_token.json` están incluidos en `.gitignore` para evitar subir credenciales al repositorio.