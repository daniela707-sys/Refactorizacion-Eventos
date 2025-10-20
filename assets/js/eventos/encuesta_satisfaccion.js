        const API_URL = '../../../encuesta_satisfaccion.php';
        const EVENTO_ID = window.EVENTO_ID || 0;
        
        // Cargar información del evento
        async function cargarEvento() {
            try {
                const response = await fetch(`${API_URL}?action=obtener_evento&id=${EVENTO_ID}`);
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('evento-nombre').textContent = data.evento.nombre;
                } else {
                    document.getElementById('evento-nombre').textContent = 'Error al cargar evento';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('evento-nombre').textContent = 'Error al cargar evento';
            }
        }
        
        // Sistema de rating con estrellas
        document.querySelectorAll('.rating-stars').forEach(ratingGroup => {
            const stars = ratingGroup.querySelectorAll('.rating-star');
            const hiddenInput = ratingGroup.parentElement.querySelector('input[type="hidden"]');
            
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    const value = index + 1;
                    hiddenInput.value = value;
                    
                    // Actualizar estrellas visuales
                    stars.forEach((s, i) => {
                        s.classList.toggle('active', i < value);
                    });
                });
                
                star.addEventListener('mouseenter', () => {
                    stars.forEach((s, i) => {
                        s.style.color = i <= index ? '#ffc107' : '#ddd';
                    });
                });
            });
            
            ratingGroup.addEventListener('mouseleave', () => {
                const currentValue = parseInt(hiddenInput.value) || 0;
                stars.forEach((s, i) => {
                    s.style.color = i < currentValue ? '#ffc107' : '#ddd';
                });
            });
        });
        
        // Buscar registro
        document.getElementById('form-buscar-registro').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            formData.append('action', 'buscar_registro');
            
            showLoading(true);
            
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Mostrar información del registro
                    document.getElementById('registro-nombre').textContent = data.registro.nombre_completo;
                    document.getElementById('registro-email').textContent = data.registro.email;
                    document.getElementById('id_registro').value = data.registro.id_registro;
                    document.getElementById('id_usuario_documento').value = data.registro.id_usuario;
                    
                    // Cambiar a paso de encuesta
                    document.getElementById('paso-busqueda').style.display = 'none';
                    document.getElementById('paso-encuesta').style.display = 'block';
                    document.getElementById('info-registro').style.display = 'block';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión. Intente nuevamente.'
                });
            } finally {
                showLoading(false);
            }
        });
        
        // Enviar encuesta
        document.getElementById('form-encuesta').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Validar que todas las calificaciones estén completas
            const ratings = ['experiencia_general', 'calidad_ponentes', 'proceso_registro', 'recomendaria'];
            let valid = true;
            
            for (const rating of ratings) {
                const input = document.querySelector(`input[name="${rating}"]`);
                if (!input.value) {
                    valid = false;
                    break;
                }
            }
            
            if (!valid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos Requeridos',
                    text: 'Por favor completa todas las calificaciones antes de enviar.'
                });
                return;
            }
            
            const formData = new FormData(e.target);
            formData.append('action', 'registrar_encuesta');
            
            showLoading(true);
            
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Mostrar mensaje de éxito
                    document.getElementById('paso-encuesta').style.display = 'none';
                    document.getElementById('mensaje-exito').style.display = 'block';
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión. Intente nuevamente.'
                });
            } finally {
                showLoading(false);
            }
        });
        
        function showLoading(show) {
            document.querySelector('.loading').style.display = show ? 'flex' : 'none';
        }
        
        // Cargar evento al iniciar
        cargarEvento();