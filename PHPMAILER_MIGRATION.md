# Migración a PHPMailer via Composer

## ✅ Cambios Realizados

### 1. **Instalación via Composer**
- ✅ Inicializado `composer.json`
- ✅ Instalado `phpmailer/phpmailer` v7.0.0
- ✅ Configurado autoload automático

### 2. **Eliminación de Copias Locales**
- ✅ Removido `PHPMailer/` (raíz)
- ✅ Removido `assets/inc/PHPMailer/`
- ✅ Removido `assets/components/eventos/PHPMailer/`

### 3. **Actualización de Archivos**
- ✅ `assets/inc/sendemail.php` - Actualizado para usar Composer
- ✅ `.gitignore` - Agregado `vendor/` y `composer.lock`

### 4. **Archivos de Ejemplo**
- ✅ `assets/inc/phpmailer_example.php` - Ejemplo de uso

## 🚀 Cómo Usar Ahora

### Instalación en Nuevo Entorno
```bash
composer install
```

### Uso en PHP
```php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
// ... configuración
```

## 📈 Beneficios

- ✅ **Actualizaciones automáticas** de seguridad
- ✅ **Gestión profesional** de dependencias
- ✅ **Repositorio más liviano** (-30MB)
- ✅ **Autoload automático**
- ✅ **Versiones estables** y probadas

## 📝 Notas Importantes

- Los archivos `vendor/` no se suben a Git
- Ejecutar `composer install` en cada nuevo entorno
- PHPMailer se mantiene actualizado automáticamente