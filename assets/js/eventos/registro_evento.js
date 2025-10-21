// registro_evento.js - Conecta el template con la lógica de negocio
class RegistroEventoManager {
    constructor() {
        this.API_URL = 'eventos/peticiones/registrar_evento.php';
        this.eventoId = this.getEventoIdFromURL();
        this.init();
    }
    
    getEventoIdFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id') || 1;
    }
    
    init() {
        this.cargarEventoInfo();
        this.setupFormulario();
        this.setupModal();
    }
    
    async cargarEventoInfo() {
        try {
            const response = await fetch(`${this.API_URL}?action=obtener_evento&id=${this.eventoId}`);
            const data = await response.json();
            
            if (data.success) {
                this.actualizarTicketInfo(data.evento);
            } else {
                console.error('Error al cargar evento:', data.message);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    actualizarTicketInfo(evento) {
        // Actualizar información en el ticket del modal
        document.getElementById('ticketEventName').textContent = evento.nombre || 'Evento';
        document.getElementById('ticketEventDescription').textContent = evento.descripcion || '';
        
        // Formatear fecha
        if (evento.fecha_inicio) {
            const fecha = new Date(evento.fecha_inicio);
            document.getElementById('ticketEventDate').textContent = fecha.toLocaleDateString('es-ES');
            document.getElementById('ticketEventTime').textContent = fecha.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        document.getElementById('ticketEventLocation').textContent = evento.ubicacion || 'Por definir';
        document.getElementById('ticketEventCapacity').textContent = 
            `${evento.cupos_disponibles || 0} disponibles`;
        
        // Actualizar tipo de evento
        const tipoEvento = evento.es_gratuito ? 'Evento Gratuito' : `$${evento.precio_entrada}`;
        document.getElementById('ticketEventType').textContent = tipoEvento;
    }
    
    setupModal() {
        const modal = document.getElementById('registrationModal');
        const closeBtn = document.getElementById('modalClose');
        const cancelBtn = document.getElementById('cancelBtn');
        
        // Buscar botones de registro en la página
        const registerButtons = document.querySelectorAll('[id*="register"], .btn-register, .register-btn');
        
        registerButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });
        
        const cerrarModal = () => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            this.resetFormulario();
        };
        
        closeBtn.addEventListener('click', cerrarModal);
        cancelBtn.addEventListener('click', cerrarModal);
        
        // Cerrar al hacer clic fuera del modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                cerrarModal();
            }
        });
    }
    
    setupFormulario() {
        const form = document.getElementById('formRegistro');
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!this.validarFormulario()) {
                return;
            }
            
            await this.enviarRegistro();
        });
    }
    
    validarFormulario() {
        const campos = [
            { id: 'fullName', error: 'nameError', mensaje: 'Por favor ingresa tu nombre completo' },
            { id: 'email', error: 'emailError', mensaje: 'Por favor ingresa un email válido' },
            { id: 'phone', error: 'phoneError', mensaje: 'Por favor ingresa tu teléfono' }
        ];
        
        let valido = true;
        
        // Limpiar errores previos
        campos.forEach(campo => {
            document.getElementById(campo.error).style.display = 'none';
            document.getElementById(campo.id).classList.remove('error');
        });
        
        // Validar cada campo
        campos.forEach(campo => {
            const input = document.getElementById(campo.id);
            const valor = input.value.trim();
            
            if (!valor) {
                this.mostrarError(campo.id, campo.error, campo.mensaje);
                valido = false;
            } else if (campo.id === 'email' && !this.validarEmail(valor)) {
                this.mostrarError(campo.id, campo.error, 'Por favor ingresa un email válido');
                valido = false;
            }
        });
        
        // Validar términos y condiciones
        const terms = document.getElementById('terms');
        if (!terms.checked) {
            alert('Debes aceptar los términos y condiciones');
            valido = false;
        }
        
        return valido;
    }
    
    validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    mostrarError(inputId, errorId, mensaje) {
        document.getElementById(inputId).classList.add('error');
        const errorElement = document.getElementById(errorId);
        errorElement.textContent = mensaje;
        errorElement.style.display = 'block';
    }
    
    async enviarRegistro() {
        const submitBtn = document.getElementById('submitBtn');
        const spinner = document.getElementById('loadingSpinner');
        
        // Mostrar loading
        submitBtn.disabled = true;
        spinner.style.display = 'inline-block';
        
        try {
            const formData = new FormData();
            formData.append('action', 'registrar');
            formData.append('evento_id', this.eventoId);
            formData.append('nombre', document.getElementById('fullName').value.trim());
            formData.append('email', document.getElementById('email').value.trim());
            formData.append('telefono', document.getElementById('phone').value.trim());
            formData.append('documento', document.getElementById('documento').value.trim());
            formData.append('tipo_documento', document.getElementById('tipo_documento').value.trim());
            
            const response = await fetch(this.API_URL, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.mostrarExito();
            } else {
                alert('Error: ' + data.message);
            }
            
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión. Intenta nuevamente.');
        } finally {
            submitBtn.disabled = false;
            spinner.style.display = 'none';
        }
    }
    
    mostrarExito() {
        // Ocultar formulario y mostrar mensaje de éxito
        document.querySelector('.registration-form').style.display = 'none';
        document.querySelector('.event-preview').style.display = 'none';
        document.getElementById('successMessage').style.display = 'block';
        
        // Cerrar modal después de 3 segundos
        setTimeout(() => {
            document.getElementById('registrationModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            this.resetFormulario();
        }, 3000);
    }
    
    resetFormulario() {
        document.getElementById('registrationForm').reset();
        document.querySelector('.registration-form').style.display = 'block';
        document.querySelector('.event-preview').style.display = 'block';
        document.getElementById('successMessage').style.display = 'none';
        
        // Limpiar errores
        document.querySelectorAll('.error-message').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.form-input').forEach(el => {
            el.classList.remove('error');
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new RegistroEventoManager();
});

// Exportar para uso global si es necesario
window.RegistroEventoManager = RegistroEventoManager;



/**
 *     <script>
        function Register() {
            const form = document.querySelector('#formRegistro');
            const formData = new FormData(form);
            formData.append('action', 'registrar');

            fetch('assets/components/eventos/registrar_evento.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#50a72c'
                        }).then(() => {
                            const eventoId = formData.get('evento_id');
                            window.location.href = window.location.pathname + '?id=' + encodeURIComponent(eventoId);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Cerrar',
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    // console.error('Error:', error);
                    Swal.fire({
                        title: 'Error de conexión',
                        text: 'Ocurrió un error al intentar registrar. Intente nuevamente.',
                        icon: 'error'
                    });
                });

            return false;
        }

        // Mostrar SweetAlert si hay mensajes
        <?php if ($show_success_alert): ?>
            Swal.fire({
                title: '¡Registro Exitoso!',
                html: `
                <div style="text-align: left; margin: 20px 0;">
                    <p><strong><?php echo addslashes($success_message); ?></strong></p>
                    <?php if ($registro_exitoso): ?>
                    <hr style="margin: 15px 0;">
                    <p><i class="bi bi-qr-code"></i> <strong>Código QR:</strong> <?php echo $registro_exitoso['qr_code']; ?></p>
                    <p><i class="bi bi-hash"></i> <strong>ID de Registro:</strong> #<?php echo $registro_exitoso['id_registro']; ?></p>
                    <p><i class="bi bi-info-circle"></i> <small>Presente este código QR el día del evento para confirmar su asistencia.</small></p>
                    <?php endif; ?>
                </div>
            `,
                icon: 'success',
                confirmButtonText: 'Perfecto',
                confirmButtonColor: '#50a72c',
                timer: 10000,
                timerProgressBar: true
            }).then((result) => {
                if (result.isConfirmed || result.isDismissed) {
                    window.location.href = window.location.href.split('?')[0] + '?id=<?php echo $evento_id; ?>';
                }
            });
        <?php endif; ?>

        <?php if ($show_error_alert): ?>
            Swal.fire({
                title: 'Error en el Registro',
                text: '<?php echo addslashes($error_message); ?>',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d42929'
            });
        <?php endif; ?>

            // Validación del formulario
            (function() {
                'use strict';
                window.addEventListener('load', function() {
                    var forms = document.getElementsByClassName('needs-validation');
                    var validation = Array.prototype.filter.call(forms, function(form) {
                        form.addEventListener('submit', function(event) {
                            if (form.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();

                                Swal.fire({
                                    title: 'Campos Incompletos',
                                    text: 'Por favor complete todos los campos obligatorios marcados con *',
                                    icon: 'warning',
                                    confirmButtonText: 'Revisar',
                                    confirmButtonColor: '#f59e0b'
                                });
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();

        // Validación en tiempo real para email
        document.getElementById('email')?.addEventListener('blur', function() {
            const email = this.value;
            if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                this.style.borderColor = 'var(--ogenix-danger)';
                this.style.boxShadow = '0 0 0 0.2rem rgba(212, 41, 41, 0.15)';
            } else {
                this.style.borderColor = 'var(--border-color)';
                this.style.boxShadow = 'none';
            }
        });

        // Formatear número de documento (solo números)
        document.getElementById('documento')?.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Formatear teléfono (solo números y algunos caracteres especiales)
        document.getElementById('telefono')?.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
        });

        // Confirmar antes de limpiar el formulario
        document.querySelector('button[type="reset"]')?.addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: '¿Limpiar Formulario?',
                text: 'Se perderán todos los datos ingresados',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#50a72c'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('form').reset();
                    document.querySelector('form').classList.remove('was-validated');

                    Swal.fire({
                        title: 'Formulario Limpiado',
                        text: 'Puede comenzar a llenar los datos nuevamente',
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        });

        // Efectos visuales adicionales
        document.addEventListener('DOMContentLoaded', function() {
            // Animación suave para los campos al hacer foco
            const formControls = document.querySelectorAll('.form-control, .form-select');
            formControls.forEach(function(control) {
                control.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                    this.parentElement.style.transition = 'transform 0.2s ease';
                });

                control.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Tooltip para campos requeridos
            const requiredFields = document.querySelectorAll('input[required], select[required]');
            requiredFields.forEach(function(field) {
                field.addEventListener('invalid', function() {
                    this.style.borderColor = 'var(--ogenix-danger)';
                    this.style.animation = 'shake 0.5s ease-in-out';
                });

                field.addEventListener('input', function() {
                    if (this.validity.valid) {
                        this.style.borderColor = 'var(--ogenix-success)';
                        this.style.animation = 'none';
                    }
                });
            });
        });

        // Prevenir envío múltiple del formulario
        document.querySelector('form')?.addEventListener('submit', function() {
            const submitBtn = document.querySelector('button[name="submit_registration"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Procesando...';

                setTimeout(() => {
                    if (!submitBtn.closest('form').querySelector('.was-validated')) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Registrarme al Evento';
                    }
                }, 3000);
            }
        });

        // console.log('🎯 Sistema de registro cargado correctamente');
        // console.log('📊 Evento ID:', <?php echo $evento_id; ?>);
        <?php if ($evento_info): ?>
            // console.log('✅ Evento encontrado:', '<?php echo addslashes($evento_info['nombre']); ?>');
        <?php endif; ?>

        document.getElementById('fecha_nacimiento')?.addEventListener('change', function() {
            const fechaNacimiento = new Date(this.value);
            const hoy = new Date();

            if (fechaNacimiento > hoy) {
                this.style.borderColor = 'var(--ogenix-danger)';
                this.style.boxShadow = '0 0 0 0.2rem rgba(212, 41, 41, 0.15)';
                Swal.fire({
                    title: 'Fecha Inválida',
                    text: 'La fecha de nacimiento no puede ser una fecha futura',
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b'
                });
                this.value = '';
            } else {
                this.style.borderColor = 'var(--ogenix-success)';
                this.style.boxShadow = '0 0 0 0.2rem rgba(50, 117, 22, 0.15)';
            }
        });
    </script>
 */