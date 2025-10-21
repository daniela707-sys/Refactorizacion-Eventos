<?php
// Configuración básica
@ini_set("display_errors", "1");
error_reporting(E_ALL);

// Variables de configuración
$pageTitle = "Encuesta de Satisfacción";

// Obtener parámetros desde la URL
$id_evento = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Escalas de calificación
$escalas = [
    1 => 'Muy Malo',
    2 => 'Malo', 
    3 => 'Regular',
    4 => 'Bueno',
    5 => 'Excelente'
];

// Variables para la vista
$evento_info = null;
$error_parametros = "";

if ($id_evento <= 0) {
    $error_parametros = "El ID del evento es requerido y debe ser válido.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Evento</title>

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
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .survey-header {
            background: linear-gradient(135deg, var(--ogenix-primary), var(--ogenix-primary-dark));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .survey-body {
            padding: 2rem;
        }

        .rating-group {
            margin-bottom: 1.5rem;
        }

        .rating-stars {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin: 1rem 0;
        }

        .rating-star {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .rating-star:hover,
        .rating-star.active {
            color: #ffc107;
            transform: scale(1.1);
        }

        .btn-primary {
            background: var(--ogenix-primary);
            border-color: var(--ogenix-primary);
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--ogenix-primary-dark);
            border-color: var(--ogenix-primary-dark);
            transform: translateY(-2px);
        }

        .form-control {
            border-radius: 15px;
            border: 2px solid var(--border-color);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--ogenix-primary);
            box-shadow: 0 0 0 0.2rem rgba(80, 167, 44, 0.25);
        }

        .alert {
            border-radius: 15px;
            border: none;
        }

        .loading {
            display: none !important;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 0 15px;
            }
            
            .survey-header,
            .survey-body {
                padding: 1.5rem;
            }
            
            .rating-star {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="main-survey-card">
            <div class="survey-header">
                <h1 class="mb-0">
                    <i class="bi bi-clipboard-check me-2"></i>
                    Encuesta de Satisfacción
                </h1>
                <p class="mb-0 mt-2 opacity-75" id="evento-nombre">Cargando información del evento...</p>
            </div>
            
            <div class="survey-body">
                <?php if (!empty($error_parametros)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error_parametros); ?>
                    </div>
                <?php else: ?>
                    <!-- Paso 1: Buscar registro -->
                    <div id="paso-busqueda">
                        <h3 class="text-center mb-4">
                            <i class="bi bi-search me-2"></i>
                            Verificar Registro de Asistencia
                        </h3>
                        
                        <form id="form-buscar-registro">
                            <input type="hidden" name="id_evento" value="<?php echo $id_evento; ?>">
                            
                            <div class="mb-3">
                                <label for="id_usuario_busqueda" class="form-label">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Número de Documento *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="id_usuario_busqueda" 
                                       name="id_usuario_busqueda" 
                                       placeholder="Ingrese su número de documento"
                                       required>
                                <div class="form-text">
                                    Ingrese el mismo documento con el que se registró al evento
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-2"></i>
                                    Buscar Registro
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Paso 2: Formulario de encuesta -->
                    <div id="paso-encuesta" style="display: none;">
                        <h3 class="text-center mb-4">
                            <i class="bi bi-star me-2"></i>
                            Califica tu Experiencia
                        </h3>
                        
                        <div id="info-registro" class="alert alert-info mb-4" style="display: none;">
                            <h5><i class="bi bi-person-check me-2"></i>Registro Encontrado</h5>
                            <p class="mb-0"><strong>Nombre:</strong> <span id="registro-nombre"></span></p>
                            <p class="mb-0"><strong>Email:</strong> <span id="registro-email"></span></p>
                        </div>
                        
                        <form id="form-encuesta">
                            <input type="hidden" id="id_registro" name="id_registro">
                            <input type="hidden" id="id_usuario_documento" name="id_usuario_documento">
                            
                            <!-- Experiencia General -->
                            <div class="rating-group">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-emoji-smile me-2"></i>
                                    ¿Cómo calificarías tu experiencia general en el evento? *
                                </label>
                                <div class="rating-stars" data-rating="experiencia_general">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <span class="rating-star" data-value="<?php echo $i; ?>">
                                            <i class="bi bi-star-fill"></i>
                                        </span>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="experiencia_general" required>
                                <div class="text-center">
                                    <small class="text-muted">1 = Muy Malo | 5 = Excelente</small>
                                </div>
                            </div>
                            
                            <!-- Calidad Ponentes -->
                            <div class="rating-group">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-person-workspace me-2"></i>
                                    ¿Cómo calificarías la calidad de los ponentes? *
                                </label>
                                <div class="rating-stars" data-rating="calidad_ponentes">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <span class="rating-star" data-value="<?php echo $i; ?>">
                                            <i class="bi bi-star-fill"></i>
                                        </span>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="calidad_ponentes" required>
                                <div class="text-center">
                                    <small class="text-muted">1 = Muy Malo | 5 = Excelente</small>
                                </div>
                            </div>
                            
                            <!-- Proceso Registro -->
                            <div class="rating-group">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-clipboard-check me-2"></i>
                                    ¿Cómo calificarías el proceso de registro? *
                                </label>
                                <div class="rating-stars" data-rating="proceso_registro">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <span class="rating-star" data-value="<?php echo $i; ?>">
                                            <i class="bi bi-star-fill"></i>
                                        </span>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="proceso_registro" required>
                                <div class="text-center">
                                    <small class="text-muted">1 = Muy Malo | 5 = Excelente</small>
                                </div>
                            </div>
                            
                            <!-- Recomendación -->
                            <div class="rating-group">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-hand-thumbs-up me-2"></i>
                                    ¿Recomendarías este evento a otros? *
                                </label>
                                <div class="rating-stars" data-rating="recomendaria">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <span class="rating-star" data-value="<?php echo $i; ?>">
                                            <i class="bi bi-star-fill"></i>
                                        </span>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="recomendaria" required>
                                <div class="text-center">
                                    <small class="text-muted">1 = Definitivamente No | 5 = Definitivamente Sí</small>
                                </div>
                            </div>
                            
                            <!-- Sugerencias -->
                            <div class="mb-4">
                                <label for="sugerencias" class="form-label fw-bold">
                                    <i class="bi bi-chat-text me-2"></i>
                                    Sugerencias o comentarios adicionales
                                </label>
                                <textarea class="form-control" 
                                          id="sugerencias" 
                                          name="sugerencias" 
                                          rows="4" 
                                          placeholder="Comparte tus comentarios, sugerencias o cualquier aspecto que consideres importante..."></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send me-2"></i>
                                    Enviar Encuesta
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Mensaje de éxito -->
                    <div id="mensaje-exito" style="display: none;">
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-success mb-3">¡Gracias por tu Feedback!</h3>
                            <p class="lead">Tu encuesta ha sido registrada exitosamente.</p>
                            <p>Tu opinión es muy valiosa para nosotros y nos ayuda a mejorar nuestros eventos.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Loading overlay -->
    <div class="loading position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
         style="background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status"></div>
            <p>Procesando...</p>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.js"></script>
    
    <script>
        // Pasar evento_id desde PHP a JavaScript
        window.EVENTO_ID = <?php echo $id_evento; ?>;
        // Ocultar loading inmediatamente
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.loading').style.display = 'none';
        });
    </script>
    <script src="../../assets/js/eventos/encuesta_satisfaccion.js"></script>
</body>
</html>