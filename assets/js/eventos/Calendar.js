document.addEventListener('DOMContentLoaded', function () {
    // Inicializar el calendario después de cargar los eventos
    initCalendar();

    function initCalendar() {
        var calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        // Crear el calendario
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            locale: 'es',
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                list: 'Lista'
            },
            events: function (info, successCallback, failureCallback) {
                // Cargar eventos desde el API
                fetch('assets/components/eventos/peticiones/getEventos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        estado: 'activo',
                        limite: 100, // Obtener más eventos para el calendario
                        pagina: 0
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.eventos && data.eventos.length > 0) {
                            // Convertir los eventos al formato esperado por FullCalendar
                            const events = data.eventos.map(evento => {
                                // Determinar color según tipo de evento
                                let backgroundColor;
                                if (evento.es_gratuito) {
                                    backgroundColor = '#50a72c'; // Verde para eventos gratuitos
                                } else if (evento.tipo_evento === 'online') {
                                    backgroundColor = '#007bff'; // Azul para eventos online
                                } else {
                                    backgroundColor = '#ff6347'; // Naranja para otros eventos
                                }

                                return {
                                    id: evento.id_evento,
                                    title: evento.nombre,
                                    start: evento.fecha_inicio,
                                    end: evento.fecha_fin || evento.fecha_inicio,
                                    backgroundColor: backgroundColor,
                                    borderColor: backgroundColor,
                                    url: `detalles_evento.php?id=${evento.id_evento}`,
                                    extendedProps: {
                                        location: `${evento.nombre_municipio}, ${evento.departamento}`,
                                        price: evento.es_gratuito ? 'Gratuito' : `$${Number(evento.precio_entrada).toLocaleString('es-CO')}`,
                                        available: evento.cupos_disponibles
                                    }
                                };
                            });

                            // Pasar los eventos al calendario
                            successCallback(events);
                        } else {
                            successCallback([]); // Sin eventos
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar eventos para el calendario:', error);
                        failureCallback(error);
                    });
            },
            eventClick: function (info) {
                // Incrementar visitas al hacer clic en un evento
                incrementarVisitas(info.event.id);

                // No es necesario redirigir manualmente, ya que usamos la propiedad 'url'
                // para manejar la redirección automáticamente
            },
            eventDidMount: function (info) {
                // Agregar tooltip con información adicional
                const tooltip = document.createElement('div');
                tooltip.className = 'event-tooltip';
                tooltip.innerHTML = `
                        <strong>${info.event.title}</strong><br>
                        ${info.event.extendedProps.location}<br>
                        ${info.event.extendedProps.price}<br>
                        ${info.event.extendedProps.available} cupos disponibles
                    `;

                // Usar popperjs o similar para posicionar el tooltip
                tippy(info.el, {
                    content: tooltip,
                    allowHTML: true,
                    placement: 'top',
                    theme: 'light-border'
                });
            }
        });

        // Renderizar el calendario
        calendar.render();
    }
});