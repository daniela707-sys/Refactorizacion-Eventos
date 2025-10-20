<?php
// Configuración básica
@ini_set("display_errors", "1");
error_reporting(E_ALL);

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
$pageTitle = "Encuesta de Satisfacción";

// Obtener parámetros desde la URL
$id_evento = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Debug: Mostrar información
echo "<!-- DEBUG: id_evento = $id_evento -->";

// Función para obtener información del evento
function obtenerEventoInfo($id_evento)
{
    global $conn;

    $query = "SELECT id_evento, nombre, descripcion, ubicacion, fecha_inicio, fecha_fin, 
                     aforo_maximo
              FROM eventos 
              WHERE id_evento = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<!-- DEBUG: Error preparando consulta: " . $conn->error . " -->";
        return null;
    }

    $stmt->bind_param("i", $id_evento);
    $stmt->execute();
    $result = $stmt->get_result();
    $evento = $result->fetch_assoc();

    echo "<!-- DEBUG: Evento encontrado: " . ($evento ? 'SÍ' : 'NO') . " -->";

    return $evento;
}

// Función para verificar registro de asistencia
function verificarRegistroAsistencia($id_evento, $id_usuario)
{
    global $conn;

    $query = "SELECT ra.id_registro, ra.id_evento, ra.nombre_completo, ra.email, 
                     ra.id_usuario, ra.asistio, ra.qr,
                     e.nombre as evento_nombre, e.descripcion as evento_descripcion,
                     e.ubicacion, e.fecha_inicio, e.fecha_fin
              FROM registro_asistencia_evento ra
              JOIN eventos e ON ra.id_evento = e.id_evento
              WHERE ra.id_evento = ? AND ra.id_usuario = ? AND ra.asistio = 1";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<!-- DEBUG: Error preparando consulta: " . $conn->error . " -->";
        return null;
    }

    $stmt->bind_param("is", $id_evento, $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $registro = $result->fetch_assoc();

    echo "<!-- DEBUG: Registro encontrado: " . ($registro ? 'SÍ' : 'NO') . " -->";
    if ($registro) {
        echo "<!-- DEBUG: ID Registro: " . $registro['id_registro'] . " -->";
    }

    return $registro;
}

// Función para verificar si ya respondió la encuesta
function yaRespondioEncuesta($id_registro)
{
    global $conn;

    $query = "SELECT id_encuesta FROM encuesta_satisfaccion_evento 
              WHERE id_registro = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_registro);

    if (!$stmt) {
        echo "<!-- DEBUG: Error en consulta duplicados: " . $conn->error . " -->";
        return false;
    }

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

// Función para registrar respuesta de encuesta
function registrarEncuestaSatisfaccion($datos)
{
    global $conn;

    echo "<!-- DEBUG: Iniciando registro de encuesta -->";
    echo "<!-- DEBUG: Datos recibidos: " . print_r($datos, true) . " -->";

    // Verificar si ya respondió la encuesta
    if (yaRespondioEncuesta($datos['id_registro'])) {
        throw new Exception("Ya existe una respuesta de encuesta para este registro");
    }

    // Insertar registro de encuesta
    $query = "INSERT INTO encuesta_satisfaccion_evento 
              (id_registro, id_usuario, experiencia_general, calidad_ponentes, 
               proceso_registro, recomendaria, sugerencias, fecha_respuesta) 
              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error preparando consulta de inserción: " . $conn->error);
    }

    // Convertir valores a los tipos correctos
    $id_registro = intval($datos['id_registro']);
    $id_usuario = $datos['id_usuario_documento']; // String del documento
    $experiencia_general = intval($datos['experiencia_general']);
    $calidad_ponentes = intval($datos['calidad_ponentes']);
    $proceso_registro = intval($datos['proceso_registro']);
    $recomendaria = intval($datos['recomendaria']);
    $sugerencias = $datos['sugerencias'];

    echo "<!-- DEBUG: Valores a insertar - ID_Registro: $id_registro, ID_Usuario: $id_usuario, Exp: $experiencia_general, Cal: $calidad_ponentes, Proc: $proceso_registro, Rec: $recomendaria -->";

    $stmt->bind_param(
        "isiiiis",
        $id_registro,
        $id_usuario,
        $experiencia_general,
        $calidad_ponentes,
        $proceso_registro,
        $recomendaria,
        $sugerencias
    );

    if (!$stmt->execute()) {
        echo "<!-- DEBUG: Error SQL: " . $stmt->error . " -->";
        throw new Exception("Error al registrar la encuesta: " . $stmt->error);
    }

    $id_encuesta = $conn->insert_id;
    echo "<!-- DEBUG: Encuesta insertada exitosamente con ID: $id_encuesta -->";

    return [
        'success' => true,
        'id_encuesta' => $id_encuesta
    ];
}

// Obtener información del evento
$evento_info = null;
$error_parametros = "";
$registro_info = null;

if ($id_evento <= 0) {
    $error_parametros = "El ID del evento es requerido y debe ser válido.";
} else {
    $evento_info = obtenerEventoInfo($id_evento);
    
    if (!$evento_info) {
        $error_parametros = "No se encontró el evento especificado.";
    }
}

// Procesamiento de verificación de registro
$buscar_registro = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_registro'])) {
    $id_usuario_busqueda = trim($_POST['id_usuario_busqueda'] ?? '');
    
    if (empty($id_usuario_busqueda)) {
        $error_parametros = "Debe ingresar el número de documento para buscar el registro.";
    } else {
        $registro_info = verificarRegistroAsistencia($id_evento, $id_usuario_busqueda);
        
        if (!$registro_info) {
            $error_parametros = "No se encontró un registro de asistencia para el evento especificado con el documento proporcionado, o el usuario no asistió al evento.";
        } else {
            // Verificar si ya respondió la encuesta
            if (yaRespondioEncuesta($registro_info['id_registro'])) {
                $error_parametros = "Ya has completado la encuesta de satisfacción para este evento.";
            }
        }
    }
    $buscar_registro = true;
}

// Procesamiento del formulario de encuesta - REMOVIDO (ahora se hace con JavaScript)

// Escalas de calificación
$escalas = [
    1 => 'Muy Malo',
    2 => 'Malo', 
    3 => 'Regular',
    4 => 'Bueno',
    5 => 'Excelente'
];
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
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .main-survey-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .survey-header {
            background: linear-gradient(135deg, var(--ogenix-primary) 0%, var(--ogenix-primary-dark) 100%);
            color: white;
            padding: 3rem 2.5rem;
            text-align: center;
            position: relative;
        }

        .survey-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="0.8" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .survey-header-content {
            position: relative;
            z-index: 1;
        }

        .survey-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .survey-subtitle {
            font-size: 1.1rem;
            opacity: 0.95;
            font-weight: 400;
        }

        .survey-content {
            padding: 2.5rem;
        }

        .event-info {
            background: rgba(248, 249, 255, 0.7);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid rgba(80, 167, 44, 0.2);
        }

        .search-section {
            background: rgba(248, 249, 255, 0.7);
            backdrop-filter: blur(15px);
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 25px;
            border: 1px solid rgba(80, 167, 44, 0.2);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .rating-group {
            background: rgba(248, 249, 255, 0.7);
            backdrop-filter: blur(15px);
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 25px;
            border: 1px solid rgba(80, 167, 44, 0.2);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .rating-label {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 15px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rating-stars {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .star-rating {
            display: flex;
            gap: 5px;
        }

        .star-input {
            display: none;
        }

        .star-label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .star-label:hover,
        .star-input:checked ~ .star-label,
        .star-label.active {
            color: #ffc107;
            transform: scale(1.1);
        }

        .rating-text {
            margin-left: 15px;
            font-weight: 500;
            color: var(--gray-text);
            min-width: 80px;
        }

        .recommendation-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .recommendation-option {
            flex: 1;
            position: relative;
        }

        .recommendation-input {
            display: none;
        }

        .recommendation-label {
            display: block;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .recommendation-input:checked + .recommendation-label {
            background: var(--ogenix-primary);
            color: white;
            border-color: var(--ogenix-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(80, 167, 44, 0.3);
        }

        .form-control {
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

        .form-control:focus {
            border-color: var(--ogenix-primary);
            box-shadow: 0 0 0 0.3rem rgba(80, 167, 44, 0.15), 0 4px 20px rgba(80, 167, 44, 0.1);
            background-color: rgba(255, 255, 255, 0.95);
            outline: none;
            transform: translateY(-2px);
        }

        .form-control.textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
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

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--ogenix-primary-dark) 0%, #2d5f18 100%);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(80, 167, 44, 0.5);
        }

        .btn-search {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            color: white;
            padding: 12px 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .btn-search:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .no-registro {
            background: white;
            border-radius: 20px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .no-registro i {
            font-size: 4rem;
            color: var(--ogenix-primary);
            margin-bottom: 1.5rem;
        }

        .error-parametros {
            background: rgba(212, 41, 41, 0.1);
            border: 1px solid rgba(212, 41, 41, 0.3);
            color: #d42929;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
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

        /* Estilo para estrellas activas */
        .star-label.active {
            color: #ffc107 !important;
            transform: scale(1.1) !important;
        }

        @media (max-width: 768px) {
            .survey-title {
                font-size: 2rem;
            }
            
            .star-label {
                font-size: 1.5rem;
            }
            
            .recommendation-group {
                flex-direction: column;
                gap: 10px;
            }
        }

        @media (max-width: 576px) {
            .survey-content {
                padding: 1.5rem;
            }
            
            .rating-group {
                padding: 20px 15px;
            }
            
            .star-rating {
                justify-content: center;
            }
            
            .rating-text {
                margin-left: 0;
                text-align: center;
                width: 100%;
                margin-top: 10px;
            }
            
            .rating-stars {
                flex-direction: column;
                align-items: center;
            }
        }

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
    </style>
</head>

<body>
    <div class="main-container">
        <?php if (!empty($error_parametros)): ?>
            <div class="main-survey-card">
                <div class="survey-header">
                    <div class="survey-header-content">
                        <h1 class="survey-title"><i class="bi bi-exclamation-triangle me-3"></i>Error de Acceso</h1>
                        <p class="survey-subtitle">No se puede acceder a la encuesta</p>
                    </div>
                </div>
                <div class="survey-content">
                    <div class="error-parametros">
                        <h4><i class="bi bi-x-circle me-2"></i>Error</h4>
                        <p><?php echo htmlspecialchars($error_parametros); ?></p>
                        <hr>
                        <p><strong>Parámetros recibidos:</strong></p>
                        <ul>
                            <li><strong>ID Evento:</strong> <?php echo $id_evento; ?></li>
                            <?php if (isset($_POST['id_usuario_busqueda'])): ?>
                            <li><strong>Documento:</strong> <?php echo htmlspecialchars($_POST['id_usuario_busqueda']); ?></li>
                            <?php endif; ?>
                        </ul>
                        <p><small><strong>URL esperada:</strong> Encuesta.php?id=ID_EVENTO</small></p>
                    </div>
                    <a href="eventos.php" class="btn-back">
                        <i class="bi bi-arrow-left"></i>Volver a Eventos
                    </a>
                </div>
            </div>
        <?php elseif ($evento_info && !$registro_info && !$buscar_registro): ?>
            <!-- Mostrar información del evento y formulario de búsqueda -->
            <div class="main-survey-card">
                <div class="survey-header">
                    <div class="survey-header-content">
                        <h1 class="survey-title"><i class="bi bi-clipboard-heart me-3"></i>Encuesta de Satisfacción</h1>
                        <p class="survey-subtitle"><?php echo htmlspecialchars($evento_info['nombre']); ?></p>
                    </div>
                </div>

                <div class="survey-content">
                    <div class="event-info">
                        <h4><i class="bi bi-calendar-event me-2"></i><?php echo htmlspecialchars($evento_info['nombre']); ?></h4>
                        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($evento_info['descripcion'] ?? 'Sin descripción'); ?></p>
                        <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($evento_info['ubicacion'] ?? 'Por definir'); ?></p>
                        <p><strong>Fecha del evento:</strong> 
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

                    <div class="search-section">
                        <h4><i class="bi bi-search me-2"></i>Buscar Registro de Asistencia</h4>
                        <p>Para completar la encuesta, primero debe verificar su registro de asistencia al evento.</p>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="id_usuario_busqueda" class="form-label">
                                        <i class="bi bi-credit-card"></i>
                                        Número de Documento <span style="color: #d42929;">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="id_usuario_busqueda" 
                                           name="id_usuario_busqueda" 
                                           placeholder="Ingrese su número de documento"
                                           value="<?php echo htmlspecialchars($_POST['id_usuario_busqueda'] ?? ''); ?>"
                                           required>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" name="buscar_registro" class="btn-search">
                                        <i class="bi bi-search me-2"></i>Buscar Registro
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php elseif ($registro_info): ?>
            <!-- Mostrar formulario de encuesta -->
            <div class="main-survey-card">
                <div class="survey-header">
                    <div class="survey-header-content">
                        <h1 class="survey-title"><i class="bi bi-clipboard-heart me-3"></i>Encuesta de Satisfacción</h1>
                        <p class="survey-subtitle">Tu opinión nos ayuda a mejorar nuestros eventos</p>
                    </div>
                </div>

                <div class="survey-content">
                    <div class="event-info">
                        <h4><i class="bi bi-calendar-event me-2"></i><?php echo htmlspecialchars($registro_info['evento_nombre']); ?></h4>
                        <p><strong>Participante:</strong> <?php echo htmlspecialchars($registro_info['nombre_completo']); ?></p>
                        <p><strong>Documento:</strong> <?php echo htmlspecialchars($registro_info['id_usuario']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($registro_info['email']); ?></p>
                        <p><strong>Fecha del evento:</strong> 
                            <?php 
                            if ($registro_info['fecha_inicio']) {
                                $fecha = new DateTime($registro_info['fecha_inicio']);
                                echo $fecha->format('d/m/Y - H:i');
                            }
                            ?>
                        </p>
                    </div>

                    <form id="formEncuesta" method="POST" action="">
                        <input type="hidden" name="id_evento" value="<?php echo $id_evento; ?>">
                        <input type="hidden" name="id_registro" value="<?php echo $registro_info['id_registro']; ?>">
                        <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($registro_info['id_usuario']); ?>">

                        <!-- Experiencia General -->
                        <div class="rating-group">
                            <div class="rating-label">
                                <i class="bi bi-star-fill text-warning"></i>
                                ¿Cómo calificarías tu experiencia general en el evento?
                            </div>
                            <div class="rating-stars">
                                <div class="star-rating">
                                    <input type="radio" name="experiencia_general" value="1" id="exp_1" class="star-input" required>
                                    <label for="exp_1" class="star-label">★</label>
                                    <input type="radio" name="experiencia_general" value="2" id="exp_2" class="star-input" required>
                                    <label for="exp_2" class="star-label">★</label>
                                    <input type="radio" name="experiencia_general" value="3" id="exp_3" class="star-input" required>
                                    <label for="exp_3" class="star-label">★</label>
                                    <input type="radio" name="experiencia_general" value="4" id="exp_4" class="star-input" required>
                                    <label for="exp_4" class="star-label">★</label>
                                    <input type="radio" name="experiencia_general" value="5" id="exp_5" class="star-input" required>
                                    <label for="exp_5" class="star-label">★</label>
                                </div>
                                <span class="rating-text" id="exp-text">Selecciona una calificación</span>
                            </div>
                        </div>

                        <!-- Calidad de Ponentes -->
                        <div class="rating-group">
                            <div class="rating-label">
                                <i class="bi bi-person-video3 text-primary"></i>
                                ¿Cómo calificarías la calidad de los ponentes?
                            </div>
                            <div class="rating-stars">
                                <div class="star-rating">
                                    <input type="radio" name="calidad_ponentes" value="1" id="cal_1" class="star-input" required>
                                    <label for="cal_1" class="star-label">★</label>
                                    <input type="radio" name="calidad_ponentes" value="2" id="cal_2" class="star-input" required>
                                    <label for="cal_2" class="star-label">★</label>
                                    <input type="radio" name="calidad_ponentes" value="3" id="cal_3" class="star-input" required>
                                    <label for="cal_3" class="star-label">★</label>
                                    <input type="radio" name="calidad_ponentes" value="4" id="cal_4" class="star-input" required>
                                    <label for="cal_4" class="star-label">★</label>
                                    <input type="radio" name="calidad_ponentes" value="5" id="cal_5" class="star-input" required>
                                    <label for="cal_5" class="star-label">★</label>
                                </div>
                                <span class="rating-text" id="cal-text">Selecciona una calificación</span>
                            </div>
                        </div>

                        <!-- Proceso de Registro -->
                        <div class="rating-group">
                            <div class="rating-label">
                                <i class="bi bi-clipboard-check text-success"></i>
                                ¿Cómo calificarías el proceso de registro al evento?
                            </div>
                            <div class="rating-stars">
                                <div class="star-rating">
                                    <input type="radio" name="proceso_registro" value="1" id="proc_1" class="star-input" required>
                                    <label for="proc_1" class="star-label">★</label>
                                    <input type="radio" name="proceso_registro" value="2" id="proc_2" class="star-input" required>
                                    <label for="proc_2" class="star-label">★</label>
                                    <input type="radio" name="proceso_registro" value="3" id="proc_3" class="star-input" required>
                                    <label for="proc_3" class="star-label">★</label>
                                    <input type="radio" name="proceso_registro" value="4" id="proc_4" class="star-input" required>
                                    <label for="proc_4" class="star-label">★</label>
                                    <input type="radio" name="proceso_registro" value="5" id="proc_5" class="star-input" required>
                                    <label for="proc_5" class="star-label">★</label>
                                </div>
                                <span class="rating-text" id="proc-text">Selecciona una calificación</span>
                            </div>
                        </div>

                        <!-- Recomendación -->
                        <div class="rating-group">
                            <div class="rating-label">
                                <i class="bi bi-hand-thumbs-up text-info"></i>
                                ¿Recomendarías este evento a otros?
                            </div>
                            <div class="recommendation-group">
                                <div class="recommendation-option">
                                    <input type="radio" name="recomendaria" value="1" id="rec_si" class="recommendation-input" required>
                                    <label for="rec_si" class="recommendation-label">
                                        <i class="bi bi-check-lg me-2"></i>Sí, lo recomendaría
                                    </label>
                                </div>
                                <div class="recommendation-option">
                                    <input type="radio" name="recomendaria" value="0" id="rec_no" class="recommendation-input" required>
                                    <label for="rec_no" class="recommendation-label">
                                        <i class="bi bi-x-lg me-2"></i>No lo recomendaría
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Sugerencias -->
                        <div class="rating-group">
                            <div class="rating-label">
                                <i class="bi bi-chat-square-text text-secondary"></i>
                                Sugerencias para mejorar futuros eventos (opcional)
                            </div>
                            <textarea name="sugerencias" class="form-control textarea" 
                                      placeholder="Comparte tus ideas para mejorar la experiencia en futuros eventos..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" onclick="return EnviarEncuesta();" class="btn-submit">
                                    <i class="bi bi-send me-2"></i>Enviar Encuesta
                                </button>
                            </div>
                            <div class="col-md-6">
                                <a href="?id=<?php echo $id_evento; ?>" class="btn btn-secondary w-100" style="padding: 16px; border-radius: 15px;">
                                    <i class="bi bi-arrow-left me-2"></i>Buscar Otro Registro
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.js"></script>
    
    <script>
        function EnviarEncuesta() {
            const form = document.querySelector('#formEncuesta');
            const formData = new FormData(form);
            formData.append('action', 'enviar_encuesta');

            fetch('./procesar_encuesta.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Encuesta Completada!',
                            html: `
                            <div style="text-align: left; margin: 20px 0;">
                                <p><strong>${data.message}</strong></p>
                                <hr style="margin: 15px 0;">
                                <p><i class="bi bi-clipboard-check"></i> <strong>ID de Encuesta:</strong> #${data.data.id_encuesta}</p>
                                <p><i class="bi bi-info-circle"></i> <small>Tu respuesta ha sido registrada exitosamente.</small></p>
                            </div>
                        `,
                            icon: 'success',
                            confirmButtonText: 'Perfecto',
                            confirmButtonColor: '#50a72c'
                        }).then(() => {
                            window.location.href = 'index.php'; // Redirigir a la página principal
                        });
                    } else {
                        Swal.fire({
                            title: 'Error en la Encuesta',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#d42929'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error de conexión',
                        text: 'Ocurrió un error al enviar la encuesta. Intente nuevamente.',
                        icon: 'error'
                    });
                });

            return false;
        }

        // Mostrar SweetAlert si hay mensajes - REMOVIDO (ahora se maneja con JavaScript)

        // Función para actualizar texto de calificación
        function updateRatingText(groupName, value) {
            const textElement = document.getElementById(groupName + '-text');
            const escalas = {
                1: 'Muy Malo',
                2: 'Malo',
                3: 'Regular', 
                4: 'Bueno',
                5: 'Excelente'
            };
            
            if (textElement) {
                textElement.textContent = escalas[value] || 'Selecciona una calificación';
                textElement.style.color = value >= 4 ? '#50a72c' : value >= 3 ? '#f59e0b' : '#d42929';
            }
        }

        // Manejar calificaciones por estrellas
        document.addEventListener('DOMContentLoaded', function() {
            const starRatingGroups = ['exp', 'cal', 'proc'];
            
            starRatingGroups.forEach(group => {
                const inputs = document.querySelectorAll(`input[name="${group === 'exp' ? 'experiencia_general' : group === 'cal' ? 'calidad_ponentes' : 'proceso_registro'}"]`);
                
                inputs.forEach((input, index) => {
                    input.addEventListener('change', function() {
                        updateRatingText(group, this.value);
                        
                        // Actualizar visualización de estrellas
                        const labels = this.closest('.star-rating').querySelectorAll('.star-label');
                        labels.forEach((label, labelIndex) => {
                            if (labelIndex < parseInt(this.value)) {
                                label.classList.add('active');
                            } else {
                                label.classList.remove('active');
                            }
                        });
                    });
                });
                
                // Efecto hover para estrellas
                const starLabels = document.querySelectorAll(`#${group}_1, #${group}_2, #${group}_3, #${group}_4, #${group}_5`);
                starLabels.forEach((label, index) => {
                    const labelElement = label.nextElementSibling;
                    
                    labelElement.addEventListener('mouseenter', function() {
                        const allLabels = this.parentElement.querySelectorAll('.star-label');
                        allLabels.forEach((l, i) => {
                            if (i <= index) {
                                l.style.color = '#ffc107';
                                l.style.transform = 'scale(1.1)';
                            } else {
                                l.style.color = '#ddd';
                                l.style.transform = 'scale(1)';
                            }
                        });
                    });
                    
                    labelElement.addEventListener('mouseleave', function() {
                        const checkedInput = this.parentElement.querySelector('input:checked');
                        const allLabels = this.parentElement.querySelectorAll('.star-label');
                        
                        allLabels.forEach((l, i) => {
                            if (checkedInput && i < parseInt(checkedInput.value)) {
                                l.style.color = '#ffc107';
                                l.style.transform = 'scale(1.1)';
                            } else {
                                l.style.color = '#ddd';
                                l.style.transform = 'scale(1)';
                            }
                        });
                    });
                });
            });
            
            // Validación del formulario
            const form = document.getElementById('formEncuesta');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Validar que todas las preguntas obligatorias estén respondidas
                    const groups = ['experiencia_general', 'calidad_ponentes', 'proceso_registro', 'recomendaria'];
                    let missingFields = [];
                    let isValid = true;
                    
                    groups.forEach(groupName => {
                        const isChecked = this.querySelector(`input[name="${groupName}"]:checked`);
                        if (!isChecked) {
                            isValid = false;
                            switch(groupName) {
                                case 'experiencia_general':
                                    missingFields.push('Experiencia general');
                                    break;
                                case 'calidad_ponentes':
                                    missingFields.push('Calidad de ponentes');
                                    break;
                                case 'proceso_registro':
                                    missingFields.push('Proceso de registro');
                                    break;
                                case 'recomendaria':
                                    missingFields.push('Recomendación del evento');
                                    break;
                            }
                        }
                    });
                    
                    if (!isValid) {
                        Swal.fire({
                            title: 'Campos Obligatorios Incompletos',
                            html: `<p>Por favor completa las siguientes calificaciones:</p>
                                   <ul style="text-align: left; margin: 10px 0; color: #d42929;">
                                       ${missingFields.map(field => `<li><strong>${field}</strong></li>`).join('')}
                                   </ul>
                                   <p style="font-size: 0.9em; color: #666; margin-top: 15px;">Todas las preguntas marcadas son obligatorias.</p>`,
                            icon: 'warning',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#f59e0b'
                        });
                        return;
                    }
                    
                    // Si todo está válido, mostrar confirmación
                    const formData = new FormData(this);
                    const responses = {
                        experiencia: formData.get('experiencia_general'),
                        calidad: formData.get('calidad_ponentes'),
                        proceso: formData.get('proceso_registro'),
                        recomendaria: formData.get('recomendaria'),
                        sugerencias: formData.get('sugerencias') || ''
                    };
                    
                    const escalas = {1: 'Muy Malo', 2: 'Malo', 3: 'Regular', 4: 'Bueno', 5: 'Excelente'};
                    
                    Swal.fire({
                        title: '¿Confirmar envío de encuesta?',
                        html: `
                            <div style="text-align: left; margin: 15px 0;">
                                <p><strong>Experiencia general:</strong> ${escalas[responses.experiencia]}</p>
                                <p><strong>Calidad de ponentes:</strong> ${escalas[responses.calidad]}</p>
                                <p><strong>Proceso de registro:</strong> ${escalas[responses.proceso]}</p>
                                <p><strong>Recomendarías:</strong> ${responses.recomendaria == '1' ? 'Sí' : 'No'}</p>
                                ${responses.sugerencias ? `<p><strong>Sugerencias:</strong> ${responses.sugerencias.substring(0, 100)}${responses.sugerencias.length > 100 ? '...' : ''}</p>` : '<p><strong>Sugerencias:</strong> <em>Ninguna</em></p>'}
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, enviar',
                        cancelButtonText: 'Revisar',
                        confirmButtonColor: '#50a72c',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Desactivar el botón y enviar
                            const submitBtn = this.querySelector('button[name="submit_encuesta"]');
                            if (submitBtn) {
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Enviando...';
                            }
                            this.submit();
                        }
                    });
                });
            }
            
            // Contador de caracteres para sugerencias
            const suggestionsTextarea = document.querySelector('textarea[name="sugerencias"]');
            if (suggestionsTextarea) {
                const maxLength = 1000;
                const counterDiv = document.createElement('div');
                counterDiv.style.cssText = 'text-align: right; font-size: 0.8rem; color: #64748b; margin-top: 5px;';
                suggestionsTextarea.parentElement.appendChild(counterDiv);
                
                function updateCounter() {
                    const remaining = maxLength - suggestionsTextarea.value.length;
                    counterDiv.textContent = `${suggestionsTextarea.value.length}/${maxLength} caracteres`;
                    counterDiv.style.color = remaining < 100 ? '#d42929' : '#64748b';
                }
                
                suggestionsTextarea.addEventListener('input', updateCounter);
                suggestionsTextarea.maxLength = maxLength;
                updateCounter();
            }
        });

        console.log('📊 Sistema de encuesta de satisfacción cargado correctamente');
        console.log('🎯 ID de evento:', <?php echo $id_evento; ?>);
        <?php if ($registro_info): ?>
            console.log('✅ Registro encontrado para:', '<?php echo addslashes($registro_info['nombre_completo']); ?>');
            console.log('🎪 Evento:', '<?php echo addslashes($registro_info['evento_nombre']); ?>');
            console.log('🆔 ID Registro:', <?php echo $registro_info['id_registro']; ?>);
        <?php endif; ?>
    </script>
</body>

</html>