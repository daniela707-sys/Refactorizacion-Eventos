document.addEventListener('DOMContentLoaded', function() {
    // Ocultar loading al cargar
    document.querySelector('.loading').style.display = 'none';
    
    // Cargar evento
    if (window.EVENTO_ID && window.EVENTO_ID > 0) {
        fetch(`./eventos/peticiones/encuesta_satisfaccion.php?action=obtener_evento&id=${window.EVENTO_ID}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.evento) {
                    document.getElementById('evento-nombre').textContent = data.evento.nombre;
                }
            })
            .catch(error => {
                console.error('Error al cargar evento:', error);
            });
    }
    
    // Manejar las estrellas de calificación
    const ratingGroups = document.querySelectorAll('.rating-stars');
    
    ratingGroups.forEach(group => {
        const stars = group.querySelectorAll('.rating-star');
        const hiddenInput = group.parentElement.querySelector('input[type="hidden"]');
        
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.value);
                
                // Actualizar input oculto
                hiddenInput.value = rating;
                
                // Actualizar estrellas visuales
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            // Efecto hover
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.value);
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });
        
        // Restaurar colores al salir del grupo
        group.addEventListener('mouseleave', function() {
            const currentRating = parseInt(hiddenInput.value) || 0;
            stars.forEach((s, i) => {
                if (i < currentRating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });
    
    // Formulario de búsqueda
    const formBusqueda = document.getElementById('form-buscar-registro');
    if (formBusqueda) {
        formBusqueda.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const documento = document.getElementById('id_usuario_busqueda').value.trim();
            if (!documento) {
                Swal.fire('Error', 'Por favor ingrese su número de documento', 'error');
                return;
            }
            
            document.querySelector('.loading').style.display = 'flex';
            
            const formData = new FormData();
            formData.append('action', 'buscar_registro');
            formData.append('id_evento', window.EVENTO_ID);
            formData.append('id_usuario_busqueda', documento);
            
            fetch('./eventos/peticiones/encuesta_satisfaccion.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.querySelector('.loading').style.display = 'none';
                
                if (data.success) {
                    document.getElementById('paso-busqueda').style.display = 'none';
                    document.getElementById('paso-encuesta').style.display = 'block';
                    document.getElementById('info-registro').style.display = 'block';
                    document.getElementById('registro-nombre').textContent = data.registro.nombre_completo || 'N/A';
                    document.getElementById('registro-email').textContent = data.registro.email || 'N/A';
                    document.getElementById('id_registro').value = data.registro.id_registro;
                    document.getElementById('id_usuario_documento').value = documento;
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                document.querySelector('.loading').style.display = 'none';
                console.error('Error:', error);
                Swal.fire('Error', 'Error al buscar el registro. Intente nuevamente.', 'error');
            });
        });
    }
    
    // Formulario de encuesta
    const formEncuesta = document.getElementById('form-encuesta');
    if (formEncuesta) {
        formEncuesta.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const requiredRatings = ['experiencia_general', 'calidad_ponentes', 'proceso_registro', 'recomendaria'];
            let valid = true;
            
            for (let rating of requiredRatings) {
                const input = document.querySelector(`input[name="${rating}"]`);
                if (!input.value) {
                    Swal.fire('Error', 'Por favor complete todas las calificaciones requeridas', 'error');
                    valid = false;
                    break;
                }
            }
            
            if (valid) {
                document.querySelector('.loading').style.display = 'flex';
                
                const formData = new FormData(formEncuesta);
                formData.append('action', 'registrar_encuesta');
                
                fetch('./eventos/peticiones/encuesta_satisfaccion.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.loading').style.display = 'none';
                    
                    if (data.success) {
                        document.getElementById('paso-encuesta').style.display = 'none';
                        document.getElementById('mensaje-exito').style.display = 'block';
                        
                        setTimeout(() => {
                            window.location.href = '../../index.php';
                        }, 3000);
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    document.querySelector('.loading').style.display = 'none';
                    console.error('Error:', error);
                    Swal.fire('Error', 'Error al enviar la encuesta. Intente nuevamente.', 'error');
                });
            }
        });
    }
});