
    // ✅ VARIABLES GLOBALES (INICIO DEL SCRIPT)
    let departamentoToken = '';
    let departamentoFiltro = '';
    let categoriaFiltro = '';
    let busquedaFiltro = '';
    let paginaActual = 0;
    let mostrarEventosFinalizados = false;

    // ✅ VARIABLES PARA PREVENIR LLAMADAS MÚLTIPLES
    let cargarEventosTimeout = null;
    let cargarEventosEnProceso = false;

    // ✅ FUNCIÓN GLOBAL OPTIMIZADA - alternarTipoEventosIndex()
    function alternarTipoEventosIndex(mostrarFinalizados) {
        console.log('🔄 INICIO alternarTipoEventosIndex:', mostrarFinalizados ? 'finalizados' : 'próximos');

        // Evitar llamadas innecesarias
        if (mostrarEventosFinalizados === mostrarFinalizados) {
            console.log('⚠️ Ya estamos en ese estado, no hacer nada');
            return;
        }

        // Prevenir llamadas mientras se procesa
        if (cargarEventosEnProceso) {
            console.log('⚠️ Ya hay una carga en proceso, ignorando...');
            return;
        }

        mostrarEventosFinalizados = mostrarFinalizados;
        paginaActual = 0;

        console.log('🔍 Nuevo estado:', mostrarEventosFinalizados);

        // Llamada segura con debounce
        cargarEventosCercanosSafe();

        console.log('🔄 FIN alternarTipoEventosIndex');
    }

    // ✅ FUNCIÓN WRAPPER SEGURA CON DEBOUNCE
    function cargarEventosCercanosSafe() {
        // Cancelar llamada anterior si existe
        if (cargarEventosTimeout) {
            clearTimeout(cargarEventosTimeout);
        }

        // Programar nueva llamada con debounce
        cargarEventosTimeout = setTimeout(() => {
            if (typeof window.cargarEventosCercanos === 'function') {
                console.log('✅ Ejecutando cargarEventosCercanos de forma segura...');
                window.cargarEventosCercanos();
            } else {
                console.error('❌ cargarEventosCercanos no disponible');
            }
        }, 100); // Debounce de 100ms
    }

    // ✅ FUNCIÓN GLOBAL OPTIMIZADA - mostrarMensajeNoEventos()
    function mostrarMensajeNoEventos(totalContrario = 0) {
        const eventsGrid = document.getElementById('events-close-to-you');

        // ✅ CAMBIAR CLASE PARA CENTRAR CONTENIDO
        eventsGrid.className = 'events-card-grid-centered';

        const tipoTexto = mostrarEventosFinalizados ? 'eventos pasados' : 'eventos próximos';
        const icono = mostrarEventosFinalizados ? '📅' : '🎉';

        let mensajeHTML = `
    <div style="text-align: center; padding: 40px; background: transparent; border-radius: 8px; margin: 20px 0;">
        <div style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;">${icono}</div>
        <h3 style="color: #6c757d; margin-bottom: 10px; font-weight: 600; font-size: 18px;">No hay ${tipoTexto}</h3>
        <p style="color: #868e96; margin-bottom: 20px; font-size: 14px;">
            No se encontraron eventos con los filtros aplicados
        </p>
    `;

        if (totalContrario > 0) {
            const textoContrario = mostrarEventosFinalizados ? 'próximos' : 'pasados';

            mensajeHTML += `
        <button onclick="alternarTipoEventosIndex(${!mostrarEventosFinalizados})" 
                style="background: #50a72c; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 13px; margin-right: 10px;">
            Ver ${totalContrario} ${textoContrario}
        </button>
        `;
        }

        mensajeHTML += `
        <button onclick="limpiarFiltrosBtnAction()" 
                style="background: #50a72c; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 13px;">
            Limpiar filtros
        </button>
    </div>`;

        eventsGrid.innerHTML = mensajeHTML;

        const pagination = document.querySelector('.shop-page__pagination');
        if (pagination) pagination.innerHTML = '';
    }

    // ✅ FUNCIÓN GLOBAL - crearIndicadorSimple()
    function crearIndicadorSimple(totalActual, totalContrario) {
        // Buscar el contenedor product__items
        const productItems = document.querySelector('.product__items');
        if (!productItems) return;

        // Eliminar indicador anterior si existe
        let indicadorExistente = document.getElementById('eventos-indicator');
        if (indicadorExistente) {
            indicadorExistente.remove();
        }

        // Crear nuevo indicador
        const indicador = document.createElement('div');
        indicador.id = 'eventos-indicator';
        indicador.style.cssText = `
            text-align: center;
            padding: 15px;
            margin-top: 20px;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        `;

        const tipoTexto = mostrarEventosFinalizados ? 'eventos pasados' : 'eventos próximos';
        const textoContrario = mostrarEventosFinalizados ? 'próximos' : 'pasados';

        let contenidoHTML = `Mostrando ${tipoTexto} • ${totalActual} encontrado${parseInt(totalActual) !== 1 ? 's' : ''}`;

        if (parseInt(totalContrario) > 0) {
            contenidoHTML += ` • `;
            contenidoHTML += `<a href="#" onclick="alternarTipoEventosIndex(${!mostrarEventosFinalizados}); return false;" style="color: #50a72c; text-decoration: none; font-weight: 500;">`;
            contenidoHTML += `Ver ${totalContrario} ${textoContrario}`;
            contenidoHTML += `</a>`;
        }

        indicador.innerHTML = contenidoHTML;

        // Agregar al final de product__items
        productItems.appendChild(indicador);
    }

    // ✅ FUNCIONES AUXILIARES GLOBALES
    function convertLocalPathToUrl(localPath) {
        if (!localPath) return './assets/images/placeholder.jpg';
        const baseUrl = window.location.origin;
        if (baseUrl.includes('localhost')) {
            let nombre_imagen = localPath.replace(/.*files\//, '');
            return `/redemprendedores/output/files/${nombre_imagen}`;
        } else {
            let nombre_imagen = localPath.replace(/.*files\//, '');
            return `/redemprendedores/files/${nombre_imagen}`;
        }
    }

    function formatearFecha(fechaInicioString, fechaFinString = null) {
        const fechaInicio = new Date(fechaInicioString);
        const fechaFin = fechaFinString ? new Date(fechaFinString) : null;
        const hoy = new Date();
        const manana = new Date();
        manana.setDate(hoy.getDate() + 1);

        const hora = fechaInicio.getHours();
        const minutos = String(fechaInicio.getMinutes()).padStart(2, '0');
        const horaFormateada = `${hora}:${minutos}`;

        function formatearFechaConDia(fecha) {
            const opciones = {
                weekday: 'short',
                day: 'numeric',
                month: 'short'
            };
            return fecha.toLocaleDateString('es-ES', opciones);
        }

        if (!fechaFin || fechaFinString === null || fechaFinString === undefined || fechaInicio.toDateString() === fechaFin.toDateString()) {
            if (fechaInicio.toDateString() === hoy.toDateString()) {
                return `hoy a las ${horaFormateada}`;
            } else if (fechaInicio.toDateString() === manana.toDateString()) {
                return `mañana a las ${horaFormateada}`;
            } else {
                return `${formatearFechaConDia(fechaInicio)}, ${horaFormateada}`;
            }
        }

        const fechaInicioFormateada = formatearFechaConDia(fechaInicio);
        const fechaFinFormateada = formatearFechaConDia(fechaFin);
        return `${fechaInicioFormateada} - ${fechaFinFormateada}`;
    }

    function formatearPrecio(precio, esGratuito) {
        return esGratuito ? "Entrada Gratuita" : `Desde $ ${Number(precio).toLocaleString('es-CO')}`;
    }

    function determinarEstadoEvento(evento) {
        const fechaInicio = new Date(evento.fecha_inicio);
        const fechaFin = evento.fecha_fin ? new Date(evento.fecha_fin) : fechaInicio;
        const ahora = new Date();

        // Si el evento ya terminó
        if (fechaFin < ahora) {
            // Si estamos mostrando eventos finalizados, no mostrar estado adicional
            // porque ya hay un div "Finalizado" separado
            if (mostrarEventosFinalizados) {
                return null;
            }
            return "Finalizado";
        }

        const horasParaInicio = Math.floor((fechaInicio - ahora) / (1000 * 60 * 60));
        const diasParaInicio = Math.floor(horasParaInicio / 24);

        if (fechaInicio <= ahora && fechaFin >= ahora) {
            return "Evento en curso";
        } else if (diasParaInicio <= 0 && horasParaInicio > 0) {
            return "Evento próximo a iniciar";
        } else if (diasParaInicio <= 1) {
            return "Evento mañana";
        } else if (diasParaInicio <= 3) {
            return "Evento muy pronto";
        } else if (diasParaInicio <= 7) {
            return "Evento esta semana";
        }

        return null;
    }

    function incrementarVisitas(idEvento) {
        fetch('assets/components/eventos/IncrementView.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_evento: idEvento
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data.success ? "Visita registrada" : "Error al registrar visita");
            })
            .catch(error => {
                console.error('Error al incrementar visitas:', error);
            });
    }

    function generarPaginacion(totalEventos, eventosPorPagina) {
        const container = document.querySelector('.shop-page__pagination');
        if (!container) return;

        const totalPaginas = Math.ceil(totalEventos / eventosPorPagina);
        if (totalPaginas <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '<ul class="pagination">';

        if (paginaActual > 0) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); cambiarPagina(${paginaActual - 1})">‹</a></li>`;
        }

        for (let i = Math.max(0, paginaActual - 2); i <= Math.min(totalPaginas - 1, paginaActual + 2); i++) {
            const active = i === paginaActual ? 'active' : '';
            html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="event.preventDefault(); cambiarPagina(${i})">${i + 1}</a></li>`;
        }

        if (paginaActual < totalPaginas - 1) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); cambiarPagina(${paginaActual + 1})">›</a></li>`;
        }

        html += '</ul>';
        container.innerHTML = html;
    }

    function mostrarInformacionResultados(total) {
        const container = document.querySelector('.product__showing-result');
        if (!container) return;

        const inicio = (paginaActual * 4) + 1;
        const fin = Math.min((paginaActual + 1) * 4, total);
        container.innerHTML = `<p>Mostrando ${inicio}-${fin} de ${total} eventos</p>`;
    }

    function cambiarPagina(nuevaPagina) {
        paginaActual = nuevaPagina;

        if (typeof window.actualizarURL === 'function') {
            window.actualizarURL();
        }

        // ✅ USAR FUNCIÓN SEGURA
        cargarEventosCercanosSafe();

        document.getElementById('events-close-to-you').scrollIntoView({
            behavior: 'smooth'
        });
    }

    function limpiarFiltrosBtnAction() {
        departamentoFiltro = 'TODOS';
        categoriaFiltro = '';
        busquedaFiltro = '';
        paginaActual = 0;
        mostrarEventosFinalizados = false;

        const url = new URL(window.location.href);
        url.searchParams.delete('departamento');
        url.searchParams.delete('categoria');
        url.searchParams.delete('buscar');
        window.history.pushState({}, '', url);

        const current = document.getElementById('nombre_departamento');
        if (current) current.textContent = 'TODOS';

        const campoBusqueda = document.getElementById('buscador');
        if (campoBusqueda) campoBusqueda.value = '';

        document.querySelectorAll('.category-link').forEach(link =>
            link.classList.remove('active')
        );

        // ✅ USAR FUNCIÓN SEGURA
        cargarEventosCercanosSafe();
    }

    function borrarFiltros() {
        limpiarFiltrosBtnAction();
    }

    function verificarExposicionGlobal() {
        console.log('🔍 Verificando funciones globales...');
        console.log('window.cargarEventosCercanos existe:', typeof window.cargarEventosCercanos);
        console.log('window.actualizarURL existe:', typeof window.actualizarURL);

        if (typeof window.cargarEventosCercanos !== 'function') {
            console.error('❌ cargarEventosCercanos NO está expuesta globalmente');
        } else {
            console.log('✅ cargarEventosCercanos está disponible globalmente');
        }
    }

    // ✅ DOM CONTENT LOADED
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Iniciando carga del DOM...');

        // ✅ USAR VARIABLES GLOBALES (no redeclararlas)
        departamentoToken = localStorage.getItem('departamento') || '';
        departamentoToken = normalizarTexto(departamentoToken);

        inicializarFiltros();

        console.log('Departamento del TOKEN (inmutable):', departamentoToken);
        console.log('Departamento para FILTRO (mutable):', departamentoFiltro);

        // ✅ FUNCIÓN LOCAL OPTIMIZADA - cargarEventosCercanos()
        function cargarEventosCercanos() {
            console.log('📡 INICIO cargarEventosCercanos:', {
                mostrarFinalizados: mostrarEventosFinalizados,
                departamento: departamentoFiltro,
                categoria: categoriaFiltro,
                pagina: paginaActual
            });

            // ✅ MARCAR COMO EN PROCESO
            cargarEventosEnProceso = true;

            const eventsGrid = document.getElementById('events-close-to-you');
            if (!eventsGrid) {
                cargarEventosEnProceso = false;
                console.error('❌ No se encontró events-close-to-you');
                return;
            }

            eventsGrid.className = 'events-card-grid-centered';
            eventsGrid.innerHTML = '<div class="loading-indicator" style="text-align: center; padding: 20px; color: #666;">Cargando eventos...</div>';

            let departamentoParaEnviar = departamentoFiltro === 'TODOS' ? '' : departamentoFiltro;
            let categoriaParaEnviar = categoriaFiltro || '';
            let busquedaParaEnviar = busquedaFiltro || '';

            const requestBody = {
                estado: 'activo',
                limite: 4,
                pagina: paginaActual,
                orden: 'fecha_inicio',
                direccion: 'ASC',
                eventos_pasados: mostrarEventosFinalizados
            };

            if (departamentoParaEnviar) requestBody.departamento = departamentoParaEnviar;
            if (categoriaParaEnviar) requestBody.categoria = categoriaParaEnviar;
            if (busquedaParaEnviar) requestBody.buscar = busquedaParaEnviar;

            console.log('📤 Enviando request:', requestBody);

            fetch('assets/components/eventos/getEventos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestBody)
                })
                .then(response => response.json())
                .then(data => {
                    console.log('📡 Respuesta recibida:', data);

                    eventsGrid.innerHTML = '';
                    eventsGrid.className = 'events-card-grid';


                    if (data.success && data.eventos?.length > 0) {
                        const eventosParaMostrar = data.eventos;

                        // Renderizar eventos
                        // Dentro de la función cargarEventosCercanos, en la parte de renderizado:

                        eventosParaMostrar.forEach(evento => {
                            const eventItem = document.createElement('div');
                            eventItem.classList.add('event-card');

                            let imagenUrl = './assets/images/placeholder.jpg';
                            if (evento.imagen_principal) {
                                try {
                                    const arr = JSON.parse(evento.imagen_principal);
                                    if (arr[0]?.name) imagenUrl = convertLocalPathToUrl(arr[0].name);
                                } catch (e) {
                                    imagenUrl = convertLocalPathToUrl(evento.imagen_principal);
                                }
                            }

                            const estadoEvento = determinarEstadoEvento(evento);

                            // ✅ SEPARAR LÓGICA DE ESTADOS
                            let estadoHTML = '';

                            if (mostrarEventosFinalizados) {
                                // Para eventos finalizados, solo mostrar el badge "Finalizado"
                                estadoHTML = '<div class="event-card-status finalizado">Finalizado</div>';
                            } else if (estadoEvento) {
                                // Para eventos próximos, mostrar el estado correspondiente
                                estadoHTML = `<div class="event-card-status">${estadoEvento}</div>`;
                            }

                            eventItem.innerHTML = `
                                <div class="event-card-image">
                                    <img src="${imagenUrl}" alt="${evento.nombre}">
                                    ${estadoHTML}
                                </div>
                                <div class="event-card-details">
                                    <h3 class="event-card-title">${evento.nombre}</h3>
                                    <div class="event-card-info">
                                        <div class="event-card-date">${formatearFecha(evento.fecha_inicio, evento.fecha_fin)}</div>
                                        <div class="event-card-price">${formatearPrecio(evento.precio_entrada, evento.es_gratuito)}</div>
                                        <div class="event-card-location">${evento.nombre_municipio}, ${evento.departamento}</div>
                                        <div class="event-card-remaining">${evento.personas_registradas} personas registradas</div>
                                    </div>
                                </div>
                            `;

                            eventItem.addEventListener('click', () => {
                                incrementarVisitas(evento.id_evento);
                                window.location.href = `details_evento.php?id=${evento.id_evento}`;
                            });

                            eventsGrid.appendChild(eventItem);
                        });

                        generarPaginacion(parseInt(data.total), 4);
                        mostrarInformacionResultados(parseInt(data.total));

                        // Crear indicador simple al final
                        crearIndicadorSimple(data.total, data.total_contrario);

                        console.log('✅ Eventos renderizados exitosamente');

                    } else {
                        console.log('⚠️ No hay eventos, mostrando mensaje');
                        mostrarMensajeNoEventos(parseInt(data.total_contrario) || 0);

                        // Crear indicador simple para cuando no hay eventos
                        crearIndicadorSimple(0, data.total_contrario || 0);
                    }

                    // ✅ MARCAR COMO COMPLETADO
                    cargarEventosEnProceso = false;
                    console.log('📡 FIN cargarEventosCercanos');
                })
                .catch(error => {
                    console.error('❌ Error en fetch:', error);
                    eventsGrid.innerHTML = '<div class="error-message" style="text-align: center; padding: 20px; color: #dc3545;">❌ Error al cargar eventos</div>';

                    // ✅ MARCAR COMO COMPLETADO INCLUSO EN ERROR
                    cargarEventosEnProceso = false;
                });
        }

        // ✅ FUNCIONES AUXILIARES LOCALES
        function normalizarTexto(texto) {
            if (!texto) return '';
            return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase();
        }

        function inicializarFiltros() {
            const urlParams = new URLSearchParams(window.location.search);
            const filtroURL = urlParams.get('departamento');
            const categoriaURL = urlParams.get('categoria');
            const busquedaURL = urlParams.get('buscar');

            if (filtroURL) {
                departamentoFiltro = normalizarTexto(filtroURL);
            } else {
                departamentoFiltro = departamentoToken || 'TODOS';
            }

            categoriaFiltro = categoriaURL || '';
            busquedaFiltro = busquedaURL || '';

            const campoBusqueda = document.getElementById('buscador');
            if (campoBusqueda && busquedaFiltro) {
                campoBusqueda.value = busquedaFiltro;
            }
        }

        function actualizarURL() {
            const url = new URL(window.location.href);

            if (departamentoFiltro === 'TODOS' || departamentoFiltro === normalizarTexto(departamentoToken)) {
                url.searchParams.delete('departamento');
            } else {
                const current = document.getElementById('nombre_departamento');
                if (current && current.textContent !== 'TODOS') {
                    url.searchParams.set('departamento', current.textContent);
                }
            }

            if (categoriaFiltro) {
                url.searchParams.set('categoria', categoriaFiltro);
            } else {
                url.searchParams.delete('categoria');
            }

            if (busquedaFiltro) {
                url.searchParams.set('buscar', busquedaFiltro);
            } else {
                url.searchParams.delete('buscar');
            }

            if (paginaActual > 0) {
                url.searchParams.set('pagina', paginaActual);
            } else {
                url.searchParams.delete('pagina');
            }

            window.history.pushState({}, '', url);
        }

        function limpiarFiltros() {
            departamentoFiltro = 'TODOS';
            categoriaFiltro = '';
            busquedaFiltro = '';
            paginaActual = 0;
            mostrarEventosFinalizados = false;

            const url = new URL(window.location.href);
            url.searchParams.delete('departamento');
            url.searchParams.delete('categoria');
            url.searchParams.delete('buscar');
            window.history.pushState({}, '', url);

            const current = document.getElementById('nombre_departamento');
            if (current) current.textContent = 'TODOS';

            const campoBusqueda = document.getElementById('buscador');
            if (campoBusqueda) campoBusqueda.value = '';

            document.querySelectorAll('.category-link').forEach(link =>
                link.classList.remove('active')
            );

            actualizarSeleccionDepartamento('TODOS');
            cargarEventosCercanos();
        }

        function configurarBusqueda() {
            const campoBusqueda = document.getElementById('buscador');
            if (!campoBusqueda) return;

            let timeoutId;

            campoBusqueda.addEventListener('input', function(event) {
                clearTimeout(timeoutId);
                const valorBusqueda = event.target.value.trim();

                timeoutId = setTimeout(function() {
                    busquedaFiltro = valorBusqueda;
                    actualizarURL();
                    // ✅ USAR FUNCIÓN SEGURA
                    cargarEventosCercanosSafe();
                    console.log('Búsqueda aplicada:', busquedaFiltro);
                }, 300);
            });

            campoBusqueda.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    clearTimeout(timeoutId);
                    busquedaFiltro = event.target.value.trim();
                    actualizarURL();
                    // ✅ USAR FUNCIÓN SEGURA
                    cargarEventosCercanosSafe();
                    console.log('Búsqueda aplicada (Enter):', busquedaFiltro);
                }
            });
        }

        // ✅ RESTO DE FUNCIONES ORIGINALES (simplificadas, sin filtrado frontend)
        function cargarDepartamentos() {
            const departamentoContainer = document.querySelector('.shop-best-sellers.product__sidebar-single');
            if (!departamentoContainer) return;

            const current = document.getElementById('nombre_departamento');
            const list = document.getElementById('departamento-list');

            current.textContent = departamentoToken || 'TODOS';

            fetch('assets/components/caracterizacion/departamentos/departamentos.php')
                .then(response => response.json())
                .then(data => {
                    list.innerHTML = '<li data-value="TODOS" class="option">TODOS</li>';

                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(function(dep, index) {
                            var li = document.createElement('li');
                            li.className = 'option';
                            li.setAttribute('data-value', dep);
                            li.textContent = dep;

                            if (normalizarTexto(dep) === departamentoFiltro) {
                                li.classList.add('selected');
                                current.textContent = dep;
                            }

                            list.appendChild(li);
                        });

                        if (departamentoFiltro === 'TODOS' || departamentoFiltro === '') {
                            const todosOption = list.querySelector('[data-value="TODOS"]');
                            if (todosOption) {
                                todosOption.classList.add('selected');
                                current.textContent = 'TODOS';
                            }
                        }
                    }

                    actualizarSeleccionDepartamento(departamentoFiltro || 'TODOS');
                })
                .catch(error => {
                    console.error('Error:', error);
                    list.innerHTML = '<li data-value="TODOS" class="option selected">TODOS</li>';
                });

            document.querySelector('.nice-select').addEventListener('click', function() {
                this.classList.toggle('open');
            });

            list.addEventListener('click', function(event) {
                if (event.target && event.target.nodeName === "LI" && event.target.hasAttribute('data-value')) {
                    var selectedValue = event.target.getAttribute('data-value');

                    departamentoFiltro = normalizarTexto(selectedValue);
                    current.textContent = selectedValue;

                    actualizarURL();
                    actualizarSeleccionDepartamento(selectedValue);

                    // ✅ USAR FUNCIÓN SEGURA
                    cargarEventosCercanosSafe();

                    document.querySelector('.nice-select').classList.remove('open');
                }
            });

            document.addEventListener('click', function(event) {
                if (!departamentoContainer.contains(event.target)) {
                    document.querySelector('.nice-select').classList.remove('open');
                }
            });
        }

        function actualizarSeleccionDepartamento(departamentoSeleccionado) {
            const list = document.getElementById('departamento-list');
            const opciones = list.querySelectorAll('li[data-value]');

            opciones.forEach(opcion => {
                opcion.classList.remove('selected', 'focus');
            });

            const opcionSeleccionada = list.querySelector(`[data-value="${departamentoSeleccionado}"]`);
            if (opcionSeleccionada) {
                opcionSeleccionada.classList.add('selected', 'focus');
            }
        }

        function cargarCategorias() {
            const categoryContainer = document.getElementById('category-event-list');
            if (!categoryContainer) return;

            categoryContainer.innerHTML = '<div class="loading-indicator">Cargando categorías...</div>';

            fetch('assets/components/eventos/getCategoriasEvento.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    categoryContainer.innerHTML = '';

                    if (data.success && data.categoriasEvento && data.categoriasEvento.length > 0) {
                        data.categoriasEvento.forEach(categoria => {
                            const listItem = document.createElement('li');
                            const categoryLink = document.createElement('a');
                            categoryLink.href = "#";
                            categoryLink.classList.add('category-link');
                            categoryLink.setAttribute('data-value', categoria.id_cat_evento);

                            let categoryName = categoria.nombre_cat_evento;
                            categoryName = categoryName.split(' ').map(word =>
                                word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
                            ).join(' ');

                            categoryLink.textContent = categoryName;

                            if (categoriaFiltro === categoria.id_cat_evento) {
                                categoryLink.classList.add('active');
                            }

                            categoryLink.addEventListener('click', function(event) {
                                event.preventDefault();

                                document.querySelectorAll('.category-link').forEach(link =>
                                    link.classList.remove('active')
                                );

                                if (categoriaFiltro === categoria.id_cat_evento) {
                                    categoriaFiltro = '';
                                } else {
                                    this.classList.add('active');
                                    categoriaFiltro = categoria.id_cat_evento;
                                }

                                actualizarURL();
                                // ✅ USAR FUNCIÓN SEGURA
                                cargarEventosCercanosSafe();
                            });

                            listItem.appendChild(categoryLink);
                            categoryContainer.appendChild(listItem);
                        });
                    } else {
                        categoryContainer.innerHTML = '<div class="no-categories">No se encontraron categorías</div>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar categorías:', error);
                    categoryContainer.innerHTML = '<div class="error-message">No se pudieron cargar las categorías</div>';
                });
        }

        // ✅ RESTO DE FUNCIONES ORIGINALES (cargarBannerEventos, cargarEventosPopulares, etc.)
        function cargarBannerEventos() {
            const bannerContainer = document.getElementById('banner');
            if (!bannerContainer) return;

            fetch('assets/components/eventos/getBannerEvento.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ubicacion: 'EVENTOS'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.fotos && data.fotos.length > 0) {
                        const banner = document.getElementById('banner');
                        let imagenUrl = './assets/images/placeholder.jpg';

                        try {
                            const fotoData = data.fotos[0].foto;
                            const jsonArray = JSON.parse(fotoData);

                            if (Array.isArray(jsonArray) && jsonArray[0] && jsonArray[0].name) {
                                const nombreArchivo = jsonArray[0].name;
                                imagenUrl = convertLocalPathToUrl(nombreArchivo);
                            }
                        } catch (e) {
                            console.error("Error al parsear la imagen del banner:", e);
                        }

                        banner.style.backgroundImage = `url('${imagenUrl}')`;
                        banner.style.backgroundSize = 'cover';
                        banner.style.backgroundPosition = 'center';
                        banner.style.backgroundRepeat = 'no-repeat';
                        banner.style.minHeight = '400px';
                        banner.style.display = 'flex';
                        banner.style.alignItems = 'center';
                        banner.style.justifyContent = 'center';

                        const bannerContent = banner.querySelector('.banner-content');
                        if (bannerContent) {
                            bannerContent.style.display = 'block';
                            bannerContent.style.opacity = '1';
                            bannerContent.style.zIndex = '10';
                            bannerContent.style.position = 'relative';
                        }
                    } else {
                        cargarBannerFallback();
                    }
                })
                .catch(error => {
                    console.error('Error al cargar banner:', error);
                    cargarBannerFallback();
                });
        }

        function cargarBannerFallback() {
            const banner = document.getElementById('banner');
            if (!banner) return;

            const imagenFallback = 'https://images.unsplash.com/photo-1542281286-9e0a16bb7366?auto=format&fit=crop&w=1600&q=80';

            banner.style.backgroundImage = `url('${imagenFallback}')`;
            banner.style.backgroundSize = 'cover';
            banner.style.backgroundPosition = 'center';
            banner.style.backgroundRepeat = 'no-repeat';
            banner.style.minHeight = '400px';
            banner.style.display = 'flex';
            banner.style.alignItems = 'center';
            banner.style.justifyContent = 'center';

            const bannerContent = banner.querySelector('.banner-content');
            if (bannerContent) {
                bannerContent.style.display = 'block';
                bannerContent.style.opacity = '1';
                bannerContent.style.zIndex = '10';
                bannerContent.style.position = 'relative';
            }
        }

        // ✅ SIMPLIFICADA: cargarEventosPopulares - ya no filtra frontend
        function cargarEventosPopulares() {
            const cardsContainer = document.getElementById('events-popular-carousel');
            if (!cardsContainer) return;

            cardsContainer.innerHTML = '<div class="loading-indicator">Cargando eventos...</div>';

            fetch('assets/components/eventos/getMejoresEventos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        estado: 'activo',
                        limite: 10,
                        direccion: 'DESC',
                        eventos_pasados: false // Solo eventos próximos para populares
                    })
                })
                .then(response => response.json())
                .then(data => {
                    cardsContainer.innerHTML = '';

                    if (data.success && data.eventos && data.eventos.length > 0) {
                        data.eventos.forEach(evento => {
                            const eventItem = document.createElement('div');
                            eventItem.classList.add('event-item', 'carousel-event-item');

                            let imagenUrl = './assets/images/placeholder.jpg';
                            if (evento.imagen_principal) {
                                try {
                                    const arr = JSON.parse(evento.imagen_principal);
                                    if (Array.isArray(arr) && arr[0] && arr[0].name) {
                                        imagenUrl = convertLocalPathToUrl(arr[0].name);
                                    }
                                } catch (e) {
                                    imagenUrl = convertLocalPathToUrl(evento.imagen_principal);
                                }
                            }

                            const fechaTexto = formatearFecha(evento.fecha_inicio, evento.fecha_fin);
                            const estadoEvento = determinarEstadoEvento(evento);

                            eventItem.innerHTML = `
                            <div class="event-image">
                                <img src="${imagenUrl}" alt="${evento.nombre}">
                                ${estadoEvento ? `<div class="event-status">${estadoEvento}</div>` : ''}
                            </div>
                            <div class="event-details">
                                <h3 class="event-title">${evento.nombre}</h3>
                                <div class="event-info">
                                    <div class="event-date">${fechaTexto}</div>
                                    <div class="event-price">${formatearPrecio(evento.precio_entrada, evento.es_gratuito)}</div>
                                    <div class="event-location">${evento.nombre_municipio}, ${evento.departamento}</div>
                                    <div class="event-remaining">${evento.personas_registradas} personas registradas</div>
                                </div>
                            </div>
                        `;

                            eventItem.addEventListener('click', function() {
                                incrementarVisitas(evento.id_evento);
                                window.location.href = `details_evento.php?id=${evento.id_evento}`;
                            });

                            cardsContainer.appendChild(eventItem);
                        });

                        createEventsPopularCarousel();
                    } else {
                        cardsContainer.innerHTML = '<div class="no-events">No se encontraron eventos populares</div>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar eventos populares:', error);
                    cardsContainer.innerHTML = '<div class="error-message">No se pudieron cargar los eventos</div>';
                });
        }

        // ✅ SIMPLIFICADA: cargarEventosOnline - ya no filtra frontend
        function cargarEventosOnline() {
            const cardsContainer = document.getElementById('events-online-carousel');
            if (!cardsContainer) return;

            cardsContainer.innerHTML = '<div class="loading-indicator">Cargando eventos...</div>';

            fetch('assets/components/eventos/getEventoOnline.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        estado: 'activo',
                        tipo: 'online',
                        limite: 6,
                        pagina: 0,
                        orden: 'fecha_inicio',
                        direccion: 'ASC',
                        eventos_pasados: false // Solo eventos próximos para online
                    })
                })
                .then(response => response.json())
                .then(data => {
                    cardsContainer.innerHTML = '';

                    if (data.success && data.eventos && data.eventos.length > 0) {
                        data.eventos.forEach(evento => {
                            const eventItem = document.createElement('div');
                            eventItem.classList.add('event-item', 'carousel-event-item');

                            const fechaTexto = formatearFecha(evento.fecha_inicio, evento.fecha_fin);
                            const estadoEvento = determinarEstadoEvento(evento);

                            let imagenUrl = './assets/images/placeholder.jpg';
                            if (evento.imagen_principal) {
                                try {
                                    const arr = JSON.parse(evento.imagen_principal);
                                    if (Array.isArray(arr) && arr[0] && arr[0].name) {
                                        imagenUrl = convertLocalPathToUrl(arr[0].name);
                                    }
                                } catch (e) {
                                    imagenUrl = convertLocalPathToUrl(evento.imagen_principal);
                                }
                            }

                            eventItem.innerHTML = `
                            <div class="event-image">
                                <img src="${imagenUrl}" alt="${evento.nombre}">
                                ${estadoEvento ? `<div class="event-status">${estadoEvento}</div>` : ''}
                            </div>
                            <div class="event-details">
                                <h3 class="event-title">${evento.nombre}</h3>
                                <div class="event-date">${fechaTexto}</div>
                                <div class="event-price">${formatearPrecio(evento.precio_entrada, evento.es_gratuito)}</div>
                                <div class="event-location">${evento.nombre_municipio}, ${evento.departamento}</div>
                                <div class="event-remaining">${evento.personas_registradas} personas registradas</div>
                            </div>
                        `;

                            eventItem.addEventListener('click', function() {
                                incrementarVisitas(evento.id_evento);
                                window.location.href = `details_evento.php?id=${evento.id_evento}`;
                            });

                            cardsContainer.appendChild(eventItem);
                        });

                        createEventsOnlineCarousel();
                    } else {
                        cardsContainer.innerHTML = '<div class="no-events">No se encontraron eventos online</div>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar eventos online:', error);
                    cardsContainer.innerHTML = '<div class="error-message">No se pudieron cargar los eventos</div>';
                });
        }

        // ✅ EXPONER FUNCIONES GLOBALMENTE AL FINAL
        window.cargarEventosCercanos = cargarEventosCercanos;
        window.actualizarURL = actualizarURL;
        window.limpiarFiltros = limpiarFiltros;

        console.log('✅ Funciones expuestas globalmente');

        // Verificar después de un momento
        setTimeout(verificarExposicionGlobal, 1000);

        // ✅ CARGAR SECCIONES UNA SOLA VEZ
        cargarEventosCercanos();
        cargarEventosPopulares();
        cargarEventosOnline();
        cargarBannerEventos();
        cargarCategorias();
        cargarDepartamentos();
        configurarBusqueda();
    });

    // ✅ FUNCIONES DE CARRUSEL (mantener originales)
    function createEventsPopularCarousel() {
        const cardsContainer = document.getElementById('events-popular-carousel');
        const prevButton = document.querySelector('.events-carousel-container .nav-button.prev');
        const nextButton = document.querySelector('.events-carousel-container .nav-button.next');

        if (!cardsContainer || !prevButton || !nextButton) {
            console.error('No se encontraron elementos necesarios para el carrusel');
            return;
        }

        let currentIndex = 0;
        let autoScrollInterval;

        function getVisibleCardsCount() {
            const containerWidth = cardsContainer.clientWidth;
            const cardWidth = cardsContainer.querySelector('.carousel-event-item').offsetWidth;
            const gap = 16;
            return Math.floor(containerWidth / (cardWidth + gap));
        }

        const totalCards = cardsContainer.querySelectorAll('.carousel-event-item').length;

        function scrollToIndex(index) {
            const cardElement = cardsContainer.querySelector('.carousel-event-item');
            const cardWidth = cardElement.offsetWidth;
            const gap = 16;

            const maxIndex = totalCards - getVisibleCardsCount();
            if (index > maxIndex) index = 0;
            if (index < 0) index = maxIndex;

            currentIndex = index;

            const scrollLeftValue = index * (cardWidth + gap);
            cardsContainer.scrollTo({
                left: scrollLeftValue,
                behavior: 'smooth'
            });
        }

        function autoScroll() {
            scrollToIndex(currentIndex + 1);
        }

        function startAutoScroll() {
            clearInterval(autoScrollInterval);
            autoScrollInterval = setInterval(autoScroll, 5000);
        }

        function stopAutoScroll() {
            clearInterval(autoScrollInterval);
        }

        startAutoScroll();

        nextButton.addEventListener('click', function() {
            stopAutoScroll();
            scrollToIndex(currentIndex + 1);
            startAutoScroll();
        });

        prevButton.addEventListener('click', function() {
            stopAutoScroll();
            scrollToIndex(currentIndex - 1);
            startAutoScroll();
        });

        cardsContainer.addEventListener('mouseenter', stopAutoScroll);
        cardsContainer.addEventListener('mouseleave', startAutoScroll);

        window.addEventListener('resize', function() {
            scrollToIndex(currentIndex);
        });
    }

    function createEventsOnlineCarousel() {
        const cardsContainer = document.getElementById('events-online-carousel');
        const prevButton = document.querySelector('.events-online-container .nav-button.prev');
        const nextButton = document.querySelector('.events-online-container .nav-button.next');

        if (!cardsContainer || !prevButton || !nextButton) {
            console.error('No se encontraron elementos necesarios para el carrusel de eventos online');
            return;
        }

        let currentIndex = 0;
        let autoScrollInterval;

        function getVisibleCardsCount() {
            const containerWidth = cardsContainer.clientWidth;
            const cardWidth = cardsContainer.querySelector('.carousel-event-item').offsetWidth;
            const gap = 16;
            return Math.floor(containerWidth / (cardWidth + gap));
        }

        const totalCards = cardsContainer.querySelectorAll('.carousel-event-item').length;

        function scrollToIndex(index) {
            const cardElement = cardsContainer.querySelector('.carousel-event-item');
            const cardWidth = cardElement.offsetWidth;
            const gap = 16;

            const maxIndex = totalCards - getVisibleCardsCount();
            if (index > maxIndex) index = 0;
            if (index < 0) index = maxIndex;

            currentIndex = index;

            const scrollLeftValue = index * (cardWidth + gap);
            cardsContainer.scrollTo({
                left: scrollLeftValue,
                behavior: 'smooth'
            });
        }

        function autoScroll() {
            scrollToIndex(currentIndex + 1);
        }

        function startAutoScroll() {
            clearInterval(autoScrollInterval);
            autoScrollInterval = setInterval(autoScroll, 5000);
        }

        function stopAutoScroll() {
            clearInterval(autoScrollInterval);
        }

        startAutoScroll();

        nextButton.addEventListener('click', function() {
            stopAutoScroll();
            scrollToIndex(currentIndex + 1);
            startAutoScroll();
        });

        prevButton.addEventListener('click', function() {
            stopAutoScroll();
            scrollToIndex(currentIndex - 1);
            startAutoScroll();
        });

        cardsContainer.addEventListener('mouseenter', stopAutoScroll);
        cardsContainer.addEventListener('mouseleave', startAutoScroll);

        window.addEventListener('resize', function() {
            scrollToIndex(currentIndex);
        });
    }
