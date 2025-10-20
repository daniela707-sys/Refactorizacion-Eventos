
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

    <script src="assets/js/eventos/registro_evento.js"></script>
</body>

</html>