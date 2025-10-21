// Validación y mejoras del formulario de registro
document.addEventListener('DOMContentLoaded', () => {
    // Validación en tiempo real para email
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value;
            if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                this.style.borderColor = 'var(--ogenix-danger)';
                this.style.boxShadow = '0 0 0 0.2rem rgba(212, 41, 41, 0.15)';
            } else {
                this.style.borderColor = 'var(--border-color)';
                this.style.boxShadow = 'none';
            }
        });
    }

    // Formatear número de documento (solo números)
    const documentoInput = document.getElementById('documento');
    if (documentoInput) {
        documentoInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // Formatear teléfono (solo números y algunos caracteres especiales)
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
        });
    }

    // Confirmar antes de limpiar el formulario
    const resetBtn = document.querySelector('button[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('¿Está seguro de que desea limpiar el formulario? Se perderán todos los datos ingresados.')) {
                document.getElementById('formRegistro').reset();
            }
        });
    }

    // Validación básica del formulario
    const form = document.getElementById('formRegistro');
    if (form) {
        form.addEventListener('submit', function(e) {
            const campos = [
                { id: 'nombre', mensaje: 'Por favor ingresa tu nombre completo' },
                { id: 'email', mensaje: 'Por favor ingresa un email válido' },
                { id: 'documento', mensaje: 'Por favor ingresa tu documento' },
                { id: 'tipo_documento', mensaje: 'Por favor selecciona el tipo de documento' },
                { id: 'tipo_poblacion', mensaje: 'Por favor selecciona el tipo de población' },
                { id: 'fecha_nacimiento', mensaje: 'Por favor ingresa tu fecha de nacimiento' }
            ];
            
            for (let campo of campos) {
                const input = document.getElementById(campo.id);
                if (!input || !input.value.trim()) {
                    alert(campo.mensaje);
                    if (input) input.focus();
                    e.preventDefault();
                    return false;
                }
                
                if (campo.id === 'email' && !input.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                    alert('Por favor ingresa un email válido');
                    input.focus();
                    e.preventDefault();
                    return false;
                }
            }
        });
    }
});