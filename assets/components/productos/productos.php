<style>
    .product__all-price .original-price {
        text-decoration: line-through;
        color: #888;
        /* O cualquier color que desees para el precio tachado */
        margin-right: 10px;
    }

    .product__all-price .discounted-price {
        color: #50a72c;
        /* Puedes cambiar el color para destacar el precio con descuento */
        font-weight: bold;
    }

    .custom-price-filter {
        font-family: 'Arial', sans-serif;
        /* Font family */
        padding: 20px;
        background-color: var(--ogenix-extra);
        /* Light border */
        border-radius: 8px;
        /* Rounded corners */
        /* Subtle shadow */
        width: 100%;
        /* Fixed width for the filter */
        margin: 0 auto;
        /* Center the filter */
    }

    .custom-title {
        font-size: 20px;
        font-weight: 800;
        line-height: 20px;
        margin: 0;
        font-family: var(--ogenix-font);
        color: var(--ogenix-black);
    }



    .custom-ranger-min-max {
        display: flex;
        margin: 10px;
        align-items: center;

        justify-content: center;

    }

    .custom-ranger-inputs {
        display: flex;
        /* Flex layout for inputs */
        align-items: center;
        /* Center items vertically */
    }

    .custom-min,
    .custom-max {
        border: 2px solid #a0a0a0;

        border-radius: 4px;

        padding: 8px;

        width: 70px;

        margin: 0 5px;

        text-align: center;

        font-size: 1rem;

    }

    .custom-separator {
        margin: 0 10px;
        /* Space around the separator */
        font-size: 1.5rem;
        /* Larger font for separator */
        color: #4b4b4b;
        /* Dark color for separator */
    }

    .custom-price-filter-button {
        text-align: center;
        margin: 10px;
        /* Center the button */
    }

    .custom-filter-button {
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 15px;
        cursor: pointer;
        transition: background-color 0.3s;
        font-size: 1rem;
        font-weight: 700;
        padding: 5px 30px 5px;
        background-color: var(--ogenix-base);
    }

    .custom-filter-button:hover {
        background-color: #4cae4c;
        /* Darker green on hover */
    }

    /* Slider styles */
    #slider-range {
        height: 6px;
        /* Height of the slider */
        background: #e9ecef;
        /* Background color of the slider */
        border-radius: 5px;
        /* Rounded edges */
        margin: 10px 0;
        /* Space around the slider */
    }

    .ui-slider-handle {
        width: 20px;
        /* Width of the slider handles */
        height: 20px;
        /* Height of the slider handles */
        background: #f39c12;
        /* Color of the handles */
        border-radius: 50%;
        /* Circular handles */
        border: 2px solid white;
        /* White border around handles */
        cursor: pointer;
        /* Pointer on hover */
    }

    /* select */

    /* Estilos para el contenedor de botones */
    .sort-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    /* Estilos para los botones */
    .sort-btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        background-color: #007bff;
        /* Color de fondo azul */
        border: none;
        border-radius: 5px;
        text-decoration: none;
        /* Elimina el subrayado de los enlaces */
        transition: background-color 0.3s ease;
        /* Efecto suave al pasar el ratón */
    }

    /* Efecto hover para los botones */
    .sort-btn:hover {
        background-color: #0056b3;
        /* Color de fondo más oscuro al pasar el ratón */
    }

    /* Para mejorar el estilo en dispositivos móviles */
    @media (max-width: 600px) {
        .sort-btn {
            font-size: 14px;
            padding: 8px 15px;
        }
    }

    @media only screen and (max-width: 768px) {
        ul#filter-list {
            max-height: 271px !important;
            overflow: auto !important;
        }
    }
</style>




<?php
// Obtener los filtros de la URL
$sector = isset($_GET['sector']) ? htmlspecialchars($_GET['sector']) : '';
$categoria = isset($_GET['categorias']) ? htmlspecialchars($_GET['categorias']) : '';
$subcategoria = isset($_GET['subcategorias']) ? htmlspecialchars($_GET['subcategorias']) : '';
$filtros = isset($_GET['promocion']) ? htmlspecialchars($_GET['promocion']) : '';
$buscar = isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '';
$min = isset($_GET['min']) ? htmlspecialchars($_GET['min']) : '';
$max = isset($_GET['max']) ? htmlspecialchars($_GET['max']) : '';
$ordenar = isset($_GET['ordenar']) ? htmlspecialchars($_GET['ordenar']) : 'precio';
$departamentourl = isset($_GET['departamento']) ? htmlspecialchars($_GET['departamento']) : '';
$page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 1;



?>




<div class="page-wrapper">
    <div class="stricky-header stricked-menu main-menu">
        <div class="sticky-header__content"></div><!-- /.sticky-header__content -->
    </div><!-- /.stricky-header -->

    <!--Page Header Start-->
    <section class="page-header">
        <div class="page-header-bg" id="banner-bg" style="background-image: url(assets/images/productos-agricolas.jpg)">
        </div>

        <script>
            fetch('assets/components/tiendas/getbanner.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ubicacion: 'PRODUCTO'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.banner[0]) {
                        const bannerUrl = convertLocalPathToUrl(data.banner[0].name); // Eliminar las barras invertidas
                        document.getElementById('banner-bg').style.backgroundImage = `url(${bannerUrl})`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        </script>
        <!-- <div class="page-header__ripped-paper"
                style="background-image: url(assets/images/shapes/page-header-ripped-paper.png);"></div> -->
        <div class="container">
            <div class="page-header__inner">
                <ul class="thm-breadcrumb list-unstyled">
                    <li><a href="index.php">Inicio</a></li>
                    <li><span>/</span></li>
                    <li>Productos</li>
                </ul>
                <h2>Productos</h2>
            </div>
        </div>
    </section>
    <!--Page Header End-->

    <!--Product Start-->
    <section class="product">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-3">
                    <div class="product__sidebar">
                        <div class="shop-search product__sidebar-single">
                            <form action="productos.php" method="GET">
                                <div class="search-bar">
                                    <input id="buscador" type="text" name="buscar" placeholder="Buscar productos..."
                                        value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                                </div>
                            </form>
                        </div>
                        <div style="margin: 20px 0px; width: 100%;" class="shop-search product__sidebar">
                            <button class="btn btn-primary" onclick="borrarFiltros('<?php echo $sector; ?>')"
                                style="width: 100%;">Borrar filtros</button>
                        </div>

                        <?php
                        // Obtener los valores de min y max desde la URL y convertir a COP si están en USD
                        $min_cop = isset($_GET['min']) ? floatval($_GET['min']) : 0;
                        $max_cop = isset($_GET['max']) ? floatval($_GET['max']) : 9000000; // Ajustar el valor máximo según sea necesario
                        ?>

                        <!-- Asegúrate de que este script esté al final del documento, antes de </body> -->
                        <style>
                            /* Estilos del contenedor del spinner */
                            .spinner-container {
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                height: 100vh;
                                /* Ocupa toda la pantalla vertical */
                            }

                            /* Estilos del spinner */
                            .spinner {
                                width: 50px;
                                /* Tamaño del spinner */
                                height: 50px;
                                border: 6px solid #f3f3f3;
                                /* Color del borde de fondo */
                                border-top: 6px solid #3498db;
                                /* Color del borde animado */
                                border-radius: 50%;
                                /* Hace el borde circular */
                                animation: spin 1s linear infinite;
                                /* Animación de giro */
                            }

                            /* Animación de rotación */
                            @keyframes spin {
                                0% {
                                    transform: rotate(0deg);
                                }

                                100% {
                                    transform: rotate(360deg);
                                }
                            }
                        </style>
                        <script>
                            function ensureRedemprendedoresPath(path) {
                                // Eliminar el contenido "/home/fgjccq3tdzpq/public_html/"
                                let nombre_imagen = path.replace(/.*files\//, '');
                                // Usamos una expresión regular para asegurarnos de que la ruta comience desde "/redemprendedores"
                                return `/redemprendedores/files/${nombre_imagen}`;
                            }


                            function ensurelocalredemprendedores(localPath) {

                                // Eliminar todo lo que está antes de 'files/' y 'files/' también
                                let nombre_imagen = localPath.replace(/.*files\//, '');
                                return `/redemprendedores/output/files/${nombre_imagen}`;
                            }

                            // Lógica original modificada para usar la nueva función
                            function convertLocalPathToUrl(localPath) {
                                const baseUrl = window.location.origin;

                                // Detecta si está en el entorno local o en la nube
                                if (baseUrl.includes('localhost')) {
                                    return ensurelocalredemprendedores(localPath)
                                } else {
                                    // Aplica la transformación para producción
                                    return ensureRedemprendedoresPath(localPath)
                                }
                            }

                            function spinner() {
                                const tiendaContainer = document.querySelector('.row.productos_row');
                                // Limpiar el contenedor antes de agregar tiendas
                                tiendaContainer.innerHTML = '';
                                const tiendaHTML = `
                                                <div class="spinner-container">
                                                    <div class="spinner"></div>
                                                </div>`;
                                tiendaContainer.innerHTML += tiendaHTML;
                            }
                            // Espera a que el DOM esté completamente cargado
                            document.addEventListener("DOMContentLoaded", function() {
                                // Función para actualizar los valores de los labels
                                function updateRange() {
                                    const minRange = document.getElementById("minRange");
                                    const maxRange = document.getElementById("maxRange");
                                    document.getElementById("minInput").value = minRange.value;
                                    document.getElementById("maxInput").value = maxRange.value;
                                    document.getElementById("minValue").textContent = minRange.value;
                                    document.getElementById("maxValue").textContent = maxRange.value;
                                }

                                document.getElementById("priceFilterForm").addEventListener("submit", function(event) {
                                    event.preventDefault(); // Evitar el envío normal del formulario

                                    // Crear un objeto URLSearchParams a partir de los inputs del formulario
                                    const formData = new FormData(this);
                                    const params = new URLSearchParams(formData);

                                    // Actualizar la URL sin recargar la página
                                    history.pushState(null, '', `${this.action}?${params.toString()}`);

                                    reactividad_productos(<?php echo $page ?>);
                                });
                            });
                        </script>

                        <!--
                        <form id="priceFilterForm" action="productos.php" method="GET" class="custom-price-filter">
                            <div class="custom-title">Precio</div>
                            <div class="custom-ranger-container">
                                <input type="range" id="minRange" min="0" max="10000000" step="1000" value="<?php echo htmlspecialchars($min_cop); ?>" oninput="updateRange()">
                                <input type="range" id="maxRange" min="0" max="10000000" step="1000" value="<?php echo htmlspecialchars($max_cop); ?>" oninput="updateRange()">

                                <input type="hidden" name="min" id="minInput" value="<?php echo htmlspecialchars($min_cop); ?>">
                                <input type="hidden" name="max" id="maxInput" value="<?php echo htmlspecialchars($max_cop); ?>">

                                <div class="range-labels">
                                    <span id="minValue"><?php echo htmlspecialchars($min_cop); ?></span> -
                                    <span id="maxValue"><?php echo htmlspecialchars($max_cop); ?></span>
                                </div>
                            </div>
                            <div class="custom-price-filter-button">
                                <button type="submit" class="custom-filter-button">Filtrar</button>
                            </div>
                        </form>-->


                        <script>
                            const minRange = document.getElementById('minRange');
                            const maxRange = document.getElementById('maxRange');
                            const minInput = document.getElementById('minInput');
                            const maxInput = document.getElementById('maxInput');
                            const minValue = document.getElementById('minValue');
                            const maxValue = document.getElementById('maxValue');

                            function updateRange() {
                                // No permitir que el valor mínimo exceda al valor máximo y viceversa
                                if (parseInt(minRange.value) > parseInt(maxRange.value)) {
                                    minRange.value = maxRange.value;
                                }
                                if (parseInt(maxRange.value) < parseInt(minRange.value)) {
                                    maxRange.value = minRange.value;
                                }

                                // Actualizar los valores visibles y los inputs ocultos
                                minInput.value = minRange.value;
                                maxInput.value = maxRange.value;

                                minValue.textContent = minRange.value;
                                maxValue.textContent = maxRange.value;

                                // Actualizar el aspecto visual de la barra unida
                                setBarBackground();
                            }

                            function setBarBackground() {
                                const min = minRange.min;
                                const max = minRange.max;

                                const minVal = (minRange.value - min) / (max - min) * 100;
                                const maxVal = (maxRange.value - min) / (max - min) * 100;

                                minRange.style.background = `linear-gradient(to right, #ddd ${minVal}%, #007bff ${minVal}%, #007bff ${maxVal}%, #ddd ${maxVal}%)`;
                                maxRange.style.background = minRange.style.background;
                            }

                            // Inicialización
                            updateRange();
                        </script>



                        <style>
                            .custom-ranger-container {
                                position: relative;
                                height: 50px;
                                /* Ajustado para dejar espacio extra */
                                margin-bottom: 10px;
                            }

                            input[type=range] {
                                -webkit-appearance: none;
                                width: 100%;
                                position: absolute;
                                top: 50%;
                                transform: translateY(-50%);
                                pointer-events: none;
                                /* Evita que ambos inputs interfieran visualmente */
                            }

                            input[type=range]::-webkit-slider-runnable-track {
                                height: 5px;
                                background: #ddd;
                                border-radius: 5px;
                            }

                            input[type=range]::-moz-range-track {
                                height: 5px;
                                background: #ddd;
                                border-radius: 5px;
                            }

                            /* Personalización de las "bolitas" (thumb) para ambos sliders */
                            input[type=range]::-webkit-slider-thumb {
                                -webkit-appearance: none;
                                width: 20px;
                                height: 20px;
                                background: #28a745;
                                /* Color verde */
                                border-radius: 50%;
                                cursor: pointer;
                                position: relative;
                                pointer-events: all;
                                /* Reactivar eventos para que los sliders sean interactivos */
                            }

                            input[type=range]::-moz-range-thumb {
                                width: 20px;
                                height: 20px;
                                background: #28a745;
                                /* Color verde */
                                border-radius: 50%;
                                cursor: pointer;
                                position: relative;
                                pointer-events: all;
                                /* Reactivar eventos */
                            }

                            /* Aplicar el mismo estilo al rango de ambos sliders */
                            input[type=range]::-webkit-slider-runnable-track {
                                background: linear-gradient(to right, #ddd, #28a745, #28a745, #ddd);
                            }

                            input[type=range]:focus {
                                outline: none;
                            }

                            /* Alineación de los valores de mínimo y máximo */
                            .range-labels {
                                display: flex;
                                justify-content: center;
                                margin-top: 20px;
                                gap: 20%;
                            }

                            .range-labels span {
                                font-size: 14px;
                                color: #333;
                                margin-bottom: 10px;
                                /* Espaciado adicional entre los números y la barra */
                            }

                            .nice-select {
                                width: 100% !important;
                            }

                            .nice-select .list {
                                max-height: 240px !important;
                                overflow-y: auto !important;
                            }

                            .product__all-btn-box {
                                margin-top: 0px !important;
                            }

                            .product__all-content {
                                margin-top: 5px !important;
                                display: flex !important;
                                flex-direction: column !important;
                                justify-content: space-evenly !important;
                                flex-wrap: nowrap !important;
                                height: 40% !important;
                            }
                        </style>




                        <!-- Filtro de Sectores 
                        <div style="margin: 30px 0px;" class="shop-category product__sidebar-single">
                            <h3 class="product__sidebar-title">Sectores</h3>
                            <ul class="list-unstyled" id="sector-list">
                            Los elementos de la lista se agregarán dinámicamente aquí 
                            </ul>
                        </div>-->

                        <script>
                            function getNormalizedLocalStorageItem(value) {
                                // Convertir a mayúsculas
                                let normalizedValue = value.toUpperCase();
                                // Eliminar tildes
                                normalizedValue = normalizedValue.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                                return normalizedValue;
                            }
                        </script>

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

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                //departamento que esta en el localstorage si hay se agrega al select
                                var departamento = localStorage.getItem('departamento') || '';
                                var departamentos = []
                                var departamentourl = '<?php echo $departamentourl; ?>';
                                var list = document.getElementById('departamento-list');
                                var current = document.getElementById('nombre_departamento');
                                current.innerHTML = departamentourl ? getNormalizedLocalStorageItem(departamentourl) : getNormalizedLocalStorageItem(departamento);

                                fetch('assets/components/caracterizacion/departamentos/departamentos.php')
                                    .then(response => response.json())
                                    .then(data => {
                                        departamentos = data
                                        departamentos.forEach(function(departamento, index) {
                                            var li = document.createElement('li');
                                            li.className = 'option' + (index === 0 ? ' selected focus' : '');
                                            li.setAttribute('data-value', departamento);
                                            li.textContent = departamento;
                                            list.appendChild(li);
                                        });

                                    }).catch(() => {
                                        console.log('error al cargar departamentos');
                                    })


                                // Add event listener to update URL parameter on click and render stores
                                list.addEventListener('click', function(event) {
                                    if (event.target && event.target.nodeName === "LI") {
                                        var selectedValue = event.target.getAttribute('data-value');
                                        current.textContent = selectedValue;

                                        // Update URL parameter
                                        var url = new URL(window.location.href);
                                        url.searchParams.set('departamento', selectedValue);
                                        window.history.pushState({}, '', url);

                                        // Render stores based on selected department
                                        reactividad_productos(<?php echo $page ?>);
                                    }
                                });


                            });
                        </script>

                        <style>
                            .container_star-filter {
                                display: flex;
                                justify-content: space-between;
                                flex-direction: column;
                            }

                            .container_star-filter label {
                                display: flex;
                                flex-direction: row;
                                align-items: center;
                                justify-content: space-between;
                                width: 100%;
                                gap: 5px;
                            }

                            .container_star-filter label div {
                                display: flex;
                                flex-direction: row;
                                align-items: center;
                                justify-content: center;
                                gap: 5px;
                            }

                            .shop-category.product__sidebar-single {
                                zoom: 0.8;
                            }
                        </style>

                        <div class="shop-category product__sidebar-single" style="margin-top:20px;">
                            <h3 class="product__sidebar-title">Filtros por Estrellas</h3>
                            <div style="margin-left: 10px;" class="container_star-filter">
                                <?php
                                // Bucle para generar las opciones de filtro por estrellas, desde 5 hasta 1
                                for ($i = 5; $i >= 1; $i--): ?>
                                    <div style="justify-content: left;" class="product__all-review">
                                        <label>
                                            <div>
                                                <!-- Marcamos el radio si el valor coincide con el filtro seleccionado -->
                                                <input type="radio" name="star-filter" value="<?= $i; ?>"
                                                    <?= ($selectedStarFilter === $i) ? 'checked' : ''; ?>>
                                                <?php
                                                // Genera el número correcto de iconos de estrella
                                                for ($j = 1; $j <= $i; $j++): ?>
                                                    <i class="fa fa-star"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span>(<?= $i; ?> <?= $i > 1 ? 'Estrellas' : 'Estrella'; ?>)</span>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>


                        <div style="margin: 30px 0px;" class="shop-category product__sidebar-single">
                            <h3 class="product__sidebar-title">Filtros de Promociones</h3>

                            <ul class="list-unstyled" id="category-special-list">
                                <!-- Los elementos de la lista se agregarán dinámicamente aquí -->
                            </ul>

                        </div>

                        <!--  <div style="margin: 30px 0px;" class="shop-category product__sidebar-single">
                            <h3 class="product__sidebar-title" id="category-title">
                                <?php echo $categoria === '' ? 'Categoría' : 'Subcategoría'; ?>
                            </h3>
                            <ul class="list-unstyled" id="filter-list">
                              Los elementos de la lista se agregarán dinámicamente aquí 
                            </ul>
                        </div>-->

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const starFilterRadios = document.querySelectorAll('input[name="star-filter"]');

                                starFilterRadios.forEach(radio => {
                                    radio.addEventListener('change', function() {
                                        const selectedValue = this.value;
                                        updateURLAndReact(selectedValue, 'stars');
                                    });
                                });

                                function updateURLAndReact(value, key) {
                                    const urlParams = new URLSearchParams(window.location.search);
                                    urlParams.set(key, value);
                                    history.pushState(null, '', `?${urlParams.toString()}`);
                                    reactividad_productos(<?php echo $page ?>); // O reactividad_tiendas() dependiendo del archivo
                                }
                            });
                        </script>

                        <script>
                            // Crear un objeto URLSearchParams a partir de los inputs del formulario

                            // Función para actualizar la URL y manejar la reactividad
                            function updateURLAndReact(value, key) {
                                const url = new URL(window.location.href);
                                const params = new URLSearchParams(url.search);

                                if (value) {
                                    params.set(key, value); // Añadir o actualizar el filtro en el objeto params
                                } else {
                                    params.delete(key); // Eliminar el filtro si el valor está vacío
                                }

                                // Actualizar la URL sin recargar la página
                                history.pushState(null, '', `${url.pathname}?${params.toString()}`);

                                reactividad_productos(<?php echo $page ?>);
                            }

                            // Función genérica para adjuntar eventos a los filtros
                            function attachFilterLinkEvents(selector, key) {
                                document.querySelectorAll(selector).forEach(link => {
                                    link.addEventListener('click', function(event) {
                                        event.preventDefault(); // Evita la redirección
                                        const value = this.getAttribute('data-value');
                                        updateURLAndReact(value, key);
                                    });
                                });
                            }

                            // Función genérica para renderizar los filtros dinámicamente
                            function renderFilterItems(data, listElementId, className, key, attribute) {
                                const listElement = document.getElementById(listElementId);


                                data.forEach(item => {
                                    const listItem = document.createElement('li');
                                    const filterLink = document.createElement('a');
                                    filterLink.href = '#';
                                    filterLink.classList.add(className);
                                    filterLink.dataset.value = item[attribute];
                                    filterLink.textContent = item.nombre || item.nombre_sector;
                                    listItem.appendChild(filterLink);
                                    listElement.appendChild(listItem);
                                });
                                // Adjunta eventos después de agregar los elementos
                                attachFilterLinkEvents(`.${className}`, key);
                            }

                            // Llama a fetch para obtener los datos de filtros y renderizarlos
                            function fetchAndRenderFilters(url, options, listElementId, className, key, attribute) {
                                fetch(url, options)
                                    .then(response => response.json())
                                    .then(data => {
                                        const items = data[key];
                                        renderFilterItems(items, listElementId, className, key, attribute);
                                    })
                                    .catch(error => console.error('Error:', error));
                            }

                            // Configuración de encabezados
                            const headers = {
                                'Content-Type': 'application/json',

                            };

                            // Fetch para los filtros de promoción
                            fetchAndRenderFilters(
                                'assets/components/caracterizacion/filtros/getfiltros.php', {
                                    method: 'POST',
                                    headers: headers
                                },
                                'category-special-list',
                                'filter-link2',
                                'promocion',
                                'id_promocion'
                            );

                            // Fetch para los filtros de sectores
                            /*
                            fetchAndRenderFilters(
                                'assets/components/caracterizacion/sectores/getsectores.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    }
                                },
                                'sector-list',
                                'filter-link',
                                'sectores',
                                'id_sector'
                            );*/

                            // Determina la URL para categorías o subcategorías según la selección
                            const categoria = '<?php echo $categoria; ?>';
                            const categoriasUrl = 'assets/components/caracterizacion/categoriasysubcategorias/getcategorias.php';
                            const subcategoriasUrl = 'assets/components/caracterizacion/categoriasysubcategorias/getsubcategorias.php';
                            const url = categoria === '' ? categoriasUrl : subcategoriasUrl;

                            // Fetch para los filtros de categorías o subcategorías
                            /*
                            fetchAndRenderFilters(
                                url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    }
                                },
                                'filter-list',
                                'filter-link3',
                                categoria === '' ? 'categorias' : 'subcategorias',
                                categoria === '' ? 'id_categoria' : 'id_sub_categoria'
                            );
                            */
                            // Eventos para el campo de búsqueda
                            document.querySelectorAll('input[name="buscar"]').forEach(input => {
                                input.addEventListener('keydown', function(event) {
                                    if (event.key === 'Enter') {
                                        event.preventDefault();
                                        const buscar = this.value;
                                        updateURLAndReact(buscar, 'buscar');
                                    }
                                });
                            });



                            function borrarFiltros() {
                                var current = document.getElementById('nombre_departamento');
                                // Elimina todos los parámetros de la URL
                                const url = new URL(window.location.href);
                                current.innerHTML = localStorage.getItem('departamento');
                                url.search = ''; // Elimina todos los parámetros de la URL
                                history.pushState(null, '', url.toString()); // Actualiza la URL sin recargar la página
                                const titlecategoria = document.getElementById('category-title');
                                const subcategorias_contenido = document.getElementById('filter-list');
                                const buscador = document.getElementById('buscador');
                                buscador.value = '';
                                //titlecategoria.textContent = 'categorias';
                                //subcategorias_contenido.innerHTML = '';
                                /*
                                fetchAndRenderFilters('assets/components/caracterizacion/categoriasysubcategorias/getcategorias.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        }
                                    }, 'filter-list',
                                    'filter-link3',
                                    categoria === '' ? 'categorias' : 'subcategorias',
                                    categoria === '' ? 'id_categoria' : 'id_sub_categoria');
                                // Llama a la función para actualizar los productos
*/
                                reactividad_productos(<?php echo $page ?>);
                            }
                        </script>


                        <!--<div class="shop-best-sellers product__sidebar-single">
                            <h3 class="product__sidebar-title">Lo mas vistos</h3>
                            <ul class="list-unstyled shop-best-sellers__list" id="best-sellers-list">
                                 Los elementos se agregarán aquí dinámicamente 
                            </ul>
                        </div>-->


                        <script>
                            let departamento = localStorage.getItem('departamento') || '';

                            function removeAccents(text) {
                                return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                            }

                            departamento = removeAccents(departamento).toUpperCase();
                            // Configurar los parámetros para enviar
                            const param = {
                                departamento: departamento,
                                limite: 5
                            };

                            const option = {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify(param)
                            };

                            const bestSellersList = document.getElementById('best-sellers-list');
                            /*
                            fetch("assets/components/productos/mejores_productos.php", option)
                                .then(response => response.json())
                                .then(data => {
                                    if (data) {
                                        data = data.productos;
                                        console.log(data);
                                        data.forEach(store => {
                                            const foto = store.imagen ? JSON.parse(store.imagen) : null;
                                            const listItem = document.createElement('li');
                                            const fotourl = foto && convertLocalPathToUrl(foto[0].name);
                                            const imgDiv = document.createElement('div');
                                            imgDiv.className = 'shop-best-sellers__img';
                                            imgDiv.style.width = '90px';

                                            const img = document.createElement('img');
                                            img.style.height = '100px';
                                            img.style.objectPosition = 'center';
                                            img.style.objectFit = 'contain';
                                            img.src = fotourl;
                                            img.alt = '';

                                            imgDiv.appendChild(img);

                                            const contentDiv = document.createElement('div');
                                            contentDiv.className = 'shop-best-sellers__content';

                                            const reviewDiv = document.createElement('div');
                                            reviewDiv.className = 'shop-best-sellers__review';

                                            for (let i = 0; i < store.calificacion; i++) {
                                                const starIcon = document.createElement('i');
                                                starIcon.className = 'fa fa-star';
                                                reviewDiv.appendChild(starIcon);
                                            }

                                            const title = document.createElement('h4');
                                            title.className = 'shop-best-sellers__title';

                                            const link = document.createElement('a');
                                            link.href = `./producto-detalle.php?id=${store.id_producto}`;
                                            link.textContent = store.nombre;

                                            const precio = document.createElement('p');
                                            precio.className = 'shop-best-sellers__price';
                                            precio.textContent = `$${store.precio}`;

                                            title.appendChild(link);
                                            contentDiv.appendChild(reviewDiv);
                                            contentDiv.appendChild(title);
                                            contentDiv.appendChild(precio);

                                            listItem.appendChild(imgDiv);
                                            listItem.appendChild(contentDiv);

                                            bestSellersList.appendChild(listItem);
                                        });
                                    } else {
                                        bestSellersList.innerHTML = '<li>No data found.</li>';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    bestSellersList.innerHTML = '<li>Error fetching data.</li>';
                                });
                                */
                        </script>

                    </div>
                </div>
                <div class="col-xl-9 col-lg-9">
                    <div class="product__items">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="product__showing-result"></div>
                            </div>
                        </div>

                        <div class="row productos_row"></div>

                        <div class="shop-page__pagination"></div>

                        <script>
                            let abortController = new AbortController(); // Crear un AbortController
                            function reactividad_productos(page = 1) {
                                const urlParams = new URLSearchParams(window.location.search);
                                urlParams.set('page', page); // Agregar el parámetro page a la URL
                                window.history.pushState({}, '', `${window.location.pathname}?${urlParams.toString()}`); // Actualizar la URL sin recargar la página

                                spinner();
                                abortController.abort();
                                abortController = new AbortController();

                                let departamento = localStorage.getItem('departamento') || '';

                                function removeAccents(text) {
                                    return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                                }
                                departamento = removeAccents(departamento).toUpperCase();
                                let selectdepartamento = urlParams.get('departamento') ? urlParams.get('departamento') : departamento;

                                const sector = urlParams.get('sectores');
                                const estrellas = urlParams.get('stars');
                                const categoria = urlParams.get('categorias');
                                const subcategoria = urlParams.get('subcategorias');
                                const filtros = urlParams.get('promocion');
                                const buscar = urlParams.get('buscar');
                                const min = urlParams.get('min');
                                const max = urlParams.get('max');
                                const orden = urlParams.get('ordenar');

                                const params = {};

                                if (selectdepartamento) params.departamento = selectdepartamento != 'todos' ? selectdepartamento : null;
                                if (categoria) params.categoria = categoria;
                                if (sector) params.sector = sector;
                                if (subcategoria) params.subcategoria = subcategoria;
                                if (filtros) params.filtros = filtros;
                                if (buscar) params.buscar = buscar;
                                if (min) params.min = min;
                                if (max) params.max = max;
                                if (orden) params.orden = orden;
                                if (estrellas) params.estrellas = estrellas;
                                params.page = page;

                                const options = {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify(params),
                                    signal: abortController.signal
                                };

                                fetch("assets/components/productos/getproductos.php", options)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data) {
                                            const productosFiltrados = data.productos;
                                            const numero_productos = data.total_productos;
                                            const numero_paginas = data.total_paginas;
                                            const productContainer = document.querySelector('.productos_row');
                                            const orden_result_container = document.querySelector('.product__showing-result');
                                            const paginado_container = document.querySelector('.shop-page__pagination');
                                            productContainer.innerHTML = '';
                                            orden_result_container.innerHTML = '';
                                            paginado_container.innerHTML = '';

                                            const orden_result = `
                <div class="product__showing-text-box">
                    <p class="product__showing-text">Mostrando ${productosFiltrados.length} de ${numero_productos} - 20 resultados</p>
                </div>
                <div class="product__menu-showing-sort">
                    <div class="product__showing-sort">
                        <div class="select-box">
                            <!-- <select class="wide">
                                <option data-display="Ordenar por más popular">Ordenar por más popular</option>
                                <option value="3">Ordenar por calificación</option>
                            </select> -->
                        </div>
                    </div>
                </div>`;
                                            orden_result_container.innerHTML = orden_result;

                                            productosFiltrados.forEach(producto => {
                                                const fotojson = producto.foto ? JSON.parse(producto.foto) : [];
                                                const foto = fotojson ? convertLocalPathToUrl(fotojson[0].name) : ''
                                                const productHTML = `    
                                    <div class="col-xl-4 col-lg-4 col-md-6">
                                        <div class="product__all-single">
                                            <div class="product__all-single-inner">
                                                <div class="product__all-img">
                                                    <img class="imgproduct" src="${foto}" alt="">
                                                </div>
                                                <div class="product__all-content">
                                                    <div class="product__all-review">
                                                        ${'<i class="fa fa-star"></i>'.repeat(producto.calificacion)}
                                                    </div>
                                                    <h4 class="product__all-title">
                                                        <a href="./producto-detalle.php?id=${producto.id_producto}">${producto.nombre}</a>
                                                    </h4>
                                                    <p class="hot-product-three__text"><span id="ojito"><i class="bi bi-eye-fill"></i></span>${producto.click}</p>

                                                    <p class="product__all-price">
                                                        ${producto.en_oferta === 1 && new Date() <= new Date(producto.Tiempo) 
                                                            ? `<span class="original-price">$ ${formatCurrency(producto.precio)}</span> 
                                                                <span class="discounted-price">$ ${formatCurrency(producto.precio * (1 - producto.descuento))}</span>`
                                                            : `$ ${formatCurrency(producto.precio)}`}
                                                    </p>

                                                    <div class="product__all-btn-box">
                                                        <a href="./producto-detalle.php?id=${producto.id_producto}" class="thm-btn product__all-btn">Ver</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                                                productContainer.innerHTML += productHTML;
                                            });

                                            // Generar los enlaces de paginación
                                            let paginadoHTML = `<ul class="pg-pagination list-unstyled">
                                                        <li class="prev">
                                                            <a href="#" aria-label="Previous" class="pagination-prev"><i class="fa fa-angle-left"></i></a>
                                                        </li>`;
                                            const currentPage = parseInt(urlParams.get('page')) || 1;

                                            if (numero_paginas <= 9) {
                                                for (let i = 1; i <= numero_paginas; i++) {
                                                    paginadoHTML += `
                                                <li class="count">
                                                    <a href="#" data-page="${i}" class="pagination-link" style="${i === currentPage ? 'background-color: var(--ogenix-primary); color: #fff;' : ''}">${i}</a>
                                                </li>`;
                                                }
                                            } else {
                                                if (currentPage > 5) {
                                                    paginadoHTML += `
                                                <li class="count">
                                                    <a href="#" data-page="1" class="pagination-link">1</a>
                                                </li>
                                                <li class="dots">...</li>`;
                                                }

                                                let startPage = Math.max(currentPage - 2, 1);
                                                let endPage = Math.min(currentPage + 2, numero_paginas);

                                                if (currentPage <= 5) {
                                                    endPage = 9;
                                                } else if (currentPage + 4 >= numero_paginas) {
                                                    startPage = numero_paginas - 8;
                                                }

                                                for (let i = startPage; i <= endPage; i++) {
                                                    paginadoHTML += `
                                                <li class="count">
                                                    <a href="#" data-page="${i}" class="pagination-link" style="${i === currentPage ? 'background-color: var(--ogenix-primary); color: #fff;' : ''}">${i}</a>
                                                </li>`;
                                                }

                                                if (endPage < numero_paginas) {
                                                    paginadoHTML += `
                                                <li class="dots">...</li>
                                                <li class="count">
                                                    <a href="#" data-page="${numero_paginas}" class="pagination-link" style="${numero_paginas === currentPage ? 'background-color: var(--ogenix-primary); color: #fff;' : ''}">${numero_paginas}</a>
                                                </li>`;
                                                }
                                            }

                                            paginadoHTML += `
                                            <li class="next">
                                                <a href="#" aria-label="Next" class="pagination-next"><i class="fa fa-angle-right"></i></a>
                                            </li>
                                        </ul>`;
                                            paginado_container.innerHTML = paginadoHTML;

                                            // Añadir el evento de clic para la paginación
                                            document.querySelectorAll('.pagination-link').forEach(link => {
                                                link.addEventListener('click', (event) => {
                                                    event.preventDefault();
                                                    const selectedPage = event.target.getAttribute('data-page');
                                                    reactividad_productos(selectedPage); // Cargar los productos de la página seleccionada
                                                });
                                            });

                                            document.querySelector('.pagination-next').addEventListener('click', (event) => {
                                                event.preventDefault();
                                                const nextPage = Math.min(currentPage + 1, numero_paginas);
                                                reactividad_productos(nextPage);
                                            });

                                            document.querySelector('.pagination-prev').addEventListener('click', (event) => {
                                                event.preventDefault();
                                                const prevPage = Math.max(currentPage - 1, 1);
                                                reactividad_productos(prevPage);
                                            });

                                        } else {
                                            console.error("No se encontraron productos en la respuesta.");
                                        }
                                    })
                                    .catch(error => console.error("Error al cargar los productos:", error));
                            }

                            reactividad_productos(<?php echo $page ?>); // Cargar productos en la primera página
                        </script>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>