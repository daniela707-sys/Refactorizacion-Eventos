<?php
//obtener token de phprunner que esta en la cookies de la pagina y se llama runnerSession
$auth = isset($_COOKIE['runnerSession']) ? true : false;
//conseguir el id del producto por la url
$id_producto = isset($_GET['id']) ? $_GET['id'] : null;

?>

<div class="preloader">
    <div class="preloader__image"></div>
</div>

<div class="banner" id="banner">
    <div class="banner-loading" id="banner-loading">
        <div class="spinner"></div>
    </div>
    <div class="banner-content" id="banner-content" style="display: none;">
        <h1 id="evento-titulo-banner">¡Bienvenido!</h1>
        <p id="evento-descripcion-banner">Descubre lo mejor para ti</p>
    </div>
    <div class="dots" id="dots"></div>
</div>

<div class="content-container">
    <div class="main-content" id="main-content">
        <div class="loading-indicator">Cargando detalles del evento...</div>
    </div>
</div>

<!-- Agregar después del banner, antes del contenido principal -->
<!-- Modal de Registro en Dos Columnas -->
<div class="modal-overlay" id="registrationModal">
    <div class="modal-container">
        <!-- Header Global -->
        <div class="modal-header">
            <button class="modal-close" id="modalClose">✕</button>
            <h2 class="modal-title">Registro al Evento</h2>
        </div>

        <!-- Lado Izquierdo - Ticket del Evento -->
        <div class="event-preview">
            <div class="event-ticket">
                <!-- Header del Ticket -->
                <div class="ticket-header">
                    <div class="event-type" id="ticketEventType">Evento Gratuito</div>
                    <div class="ticket-event-name" id="ticketEventName">Cargando...</div>
                    <div class="ticket-description" id="ticketEventDescription">
                        Cargando información del evento...
                    </div>
                </div>

                <!-- Cuerpo del Ticket -->
                <div class="ticket-body">
                    <div class="ticket-details">
                        <div class="detail-row">
                            <svg class="detail-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" />
                            </svg>
                            <div>
                                <div class="detail-label">Fecha</div>
                                <div class="detail-text" id="ticketEventDate">-</div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <svg class="detail-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" />
                            </svg>
                            <div>
                                <div class="detail-label">Hora</div>
                                <div class="detail-text" id="ticketEventTime">-</div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <svg class="detail-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" />
                            </svg>
                            <div>
                                <div class="detail-label">Ubicación</div>
                                <div class="detail-text" id="ticketEventLocation">-</div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <svg class="detail-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                            </svg>
                            <div>
                                <div class="detail-label">Cupos</div>
                                <div class="detail-text" id="ticketEventCapacity">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nota de Seguridad -->
            <div class="security-note">
                <span class="security-icon">🔒</span>
                Tus datos están protegidos y seguros
            </div>
        </div>

        <!-- Lado Derecho - Formulario -->
        <div class="registration-form">
            <form id="registrationForm">
                <div class="form-group">
                    <label class="form-label" for="fullName">Nombre completo *</label>
                    <input type="text" id="fullName" class="form-input" placeholder="Ej: María García López" required>
                    <div class="error-message" id="nameError">Por favor ingresa tu nombre completo</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Correo electrónico *</label>
                    <input type="email" id="email" class="form-input" placeholder="maria@ejemplo.com" required>
                    <div class="error-message" id="emailError">Por favor ingresa un email válido</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Teléfono *</label>
                    <input type="tel" id="phone" class="form-input" placeholder="+57 300 123 4567" required>
                    <div class="error-message" id="phoneError">Por favor ingresa tu teléfono</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="comments">Comentarios</label>
                    <textarea id="comments" class="form-input" rows="2" placeholder="¿Alguna pregunta especial?"></textarea>
                </div>

                <div class="form-checkbox">
                    <input type="checkbox" id="terms" class="checkbox-input" required>
                    <label for="terms" class="checkbox-label">
                        Acepto los <a href="#" target="_blank">términos y condiciones</a> y autorizo el tratamiento de mis datos personales.
                    </label>
                </div>

                <div class="form-buttons">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="loading-spinner" id="loadingSpinner"></span>
                        Confirmar Registro
                    </button>
                </div>
            </form>
        </div>

        <!-- Mensaje de Éxito -->
        <div class="success-message" id="successMessage">
            <div class="success-icon">🎉</div>
            <h3 class="success-title">¡Registro Completado!</h3>
            <p class="success-text">
                Hemos enviado la confirmación a tu correo electrónico. ¡Nos vemos en el evento!
            </p>
        </div>
    </div>
</div>


<!-- <div class="register-section">
    <button class="btn-register" id="registerButton">Registrarse al evento</button>
</div> -->