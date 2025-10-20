<style>
    /* Clase normal para eventos en grid */
    .events-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px 0;
    }

    /* Clase para centrar contenido (loading, mensajes de error, no hay eventos) */
    .events-card-grid-centered {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 200px;
        padding: 20px 0;
    }

    /* Estilos adicionales para mejorar la apariencia */
    .loading-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-weight: 500;
    }

    .error-message {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-weight: 500;
    }

    /* Asegurar que las cards de eventos tengan posición relativa para el estado */
    .event-card {
        position: relative;
        /* resto de estilos de la card */
    }

    .event-card-image {
        position: relative;
        /* resto de estilos de la imagen */
    }

    .event-card-status {
        position: absolute;
        background: rgba(0, 123, 255, 0.9);
        color: white;
        padding: 7px 17px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        z-index: 2;
    }

    /* Estilo específico para el estado "Finalizado" */
    .event-card-status.finalizado {
        background: rgba(108, 117, 125, 0.9);
    }
</style>

<div class="preloader">
    <div class="preloader__image"></div>
</div>

<div class="banner" id="banner">
    <div class="banner-content">
        <h1>¡Bienvenido!</h1>
        <p>Descubre lo mejor para ti</p>
    </div>
    <div class="dots" id="dots"></div>
</div>

<div class="events-container">
    <div class="row">
        <!-- Filtros (Barra lateral) -->
        <div class="col-xl-3 col-lg-3">
            <div class="product__sidebar">
                <div class="shop-search product__sidebar-single">
                    <form action="#" method="GET">
                        <div class="search-bar">
                            <input id="buscador" type="text" name="buscar" placeholder="Buscar eventos...">
                        </div>
                    </form>
                </div>
                <div style="margin: 20px 0px; width: 100%;" class="shop-search product__sidebar">
                    <button class="btn btn-primary" onclick="borrarFiltros()" style="width: 100%;">Borrar filtros</button>
                </div>

                <div style="margin-top: 20px; height: 125px;" class="shop-best-sellers product__sidebar-single">
                    <h3 class="product__sidebar-title">Departamento</h3>
                    <select id="departamento-select" name="departamento">
                        <!-- Las opciones se agregarán dinámicamente -->
                    </select>
                    <div class="nice-select" tabindex="0">
                        <span id="nombre_departamento" class="current"></span>
                        <ul class="list" id="departamento-list">
                            <!-- Las opciones se agregarán dinámicamente a excepcion de todos -->
                            <li data-value="todos" class="option selected focus">TODOS</li>
                        </ul>
                    </div>
                </div>

                <div style="margin: 30px 0px;" class="shop-category product__sidebar-single">
                    <h3 class="product__sidebar-title">Categorías de Eventos</h3>
                    <ul class="list-unstyled" id="category-event-list">
                        <!-- Los elementos de la lista se agregarán dinámicamente aquí -->
                    </ul>
                </div>
            </div>
        </div>

        <!-- Eventos (Contenido principal) -->
        <div class="col-xl-9 col-lg-9">
            <div class="product__items">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="product__showing-result"></div>
                    </div>
                </div>
                <div class="events-grid" id="events-close-to-you"></div>
                <div class="shop-page__pagination"></div>
            </div>
        </div>
    </div>
</div>

<div class="events-container events-carousel-container">
    <h2 class="events-heading">
        Eventos populares
        <div class="carousel-navigation">
            <div class="nav-button prev">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.41 7.41L14 6L8 12L14 18L15.41 16.59L10.83 12L15.41 7.41Z" fill="currentColor" />
                </svg>
            </div>
            <div class="nav-button next">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 6L8.59 7.41L13.17 12L8.59 16.59L10 18L16 12L10 6Z" fill="currentColor" />
                </svg>
            </div>
        </div>
    </h2>
    <div class="cards-container" id="events-popular-carousel"></div>
</div>

<div class="events-container events-online-container">
    <h2 class="events-heading">
        Eventos online
        <div class="carousel-navigation">
            <div class="nav-button prev">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.41 7.41L14 6L8 12L14 18L15.41 16.59L10.83 12L15.41 7.41Z" fill="currentColor" />
                </svg>
            </div>
            <div class="nav-button next">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 6L8.59 7.41L13.17 12L8.59 16.59L10 18L16 12L10 6Z" fill="currentColor" />
                </svg>
            </div>
        </div>
    </h2>
    <div class="cards-container" id="events-online-carousel"></div>
</div>

<div class="events-container events-calendar-container">
    <h2 class="events-heading">Calendario de Eventos</h2>
    <div id="calendar"></div>
</div>

<!-- Script del calendario -->
<script src="assets/js/eventos/Calendar.js"></script>
<script src="assets/js/eventos/eventos.js"></script>