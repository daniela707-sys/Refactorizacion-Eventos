<?php
//obtener token de phprunner que esta en la cookies de la pagina y se llama runnerSession
$auth = isset($_COOKIE['runnerSession']) ? true : false;
//conseguir el id del producto por la url
$id_producto = isset($_GET['id']) ? $_GET['id'] : null;

?>


<style>
    .precio_base_sin_descuento {
        color: #7d8978 !important;
        /* O cualquier color que desees para el precio tachado */
        margin-right: 10px;
    }


    .precio_base {
        text-decoration: line-through;
        color: #7d8978 !important;
        /* O cualquier color que desees para el precio tachado */
        margin-right: 10px;
    }

    .precio_descuento {
        color: #50a72c;
        /* Puedes cambiar el color para destacar el precio con descuento */
        font-weight: bold;
    }

    .product-details__reveiw i {
        font-size: 16px;
        color: gold;
    }

    .fa-star.checked {
        color: #a1a1a1;
    }
</style>

<style>
    .ir_tienda {
        margin-top: 20px;
    }

    a.btn.contact-btn {
        color: #7d8da8;
    }

    span.nombre {
        color: #244019;
        font-weight: 800;
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

    function logContactClick() {
        const id_producto = urlParams.get('id');

        fetch('assets/components/productos/contactar_tracert.php', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id_producto: id_producto
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('Contacto registrado exitosamente');
                } else {
                    console.error('Error al registrar el contacto');
                }
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
            });
    }

    const urlParams = new URLSearchParams(window.location.search);
    document.addEventListener('DOMContentLoaded', function() {
        const id_producto = urlParams.get('id');
        const url = `assets/components/productos/getdetalle_producto.php`;

        fetch(url, {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id_producto: id_producto
                })
            })
            .then(response => response.json())
            .then(data => {
                const producto = data[0];
                const url = JSON.parse(producto.foto);

                // Actualizar imagen del producto
                document.querySelector('.product-details__img img').src = convertLocalPathToUrl(url[0].name);

                // Actualizar título y precio base
                document.querySelector('.product-details__title').innerHTML =
                    producto.estado_oferta === "1" ?
                    `${producto.nombre} <span class="precio_base">$ ${formatCurrency(producto.precio)}</span>` :
                    `${producto.nombre} <span class="precio_base_sin_descuento">$ ${formatCurrency(producto.precio)}</span>`;

                // Actualizar precio con descuento si hay oferta activa
                if (producto.estado_oferta === "1") {
                    document.querySelector('.product-details__title_Promocion').innerHTML =
                        `<span class="precio_descuento">$ ${formatCurrency(producto.precio * (1 - producto.porcentaje))}</span>`;
                }

                // Actualizar descripción del producto
                document.querySelector('.product-description__text1').textContent = producto.descripcion;

                // Actualizar información de disponibilidad
                document.querySelector('.product-details__content').innerHTML =
                    producto.estado === "1" ?
                    `<p class="product-details__content-text2">REF. ${producto.id_producto} <br> Disponible en tienda</p>` :
                    '';

                // Redes de contacto
                let socialLinks = '';
                if (producto.facebook != '') {
                    socialLinks += `<a href="${producto.facebook.startsWith('http') ? producto.facebook : 'http://' + producto.facebook}" class="contact-btn" target="_blank" rel="noopener noreferrer"><span class="fab fa-facebook"></span></a>`;
                }
                if (producto.Instagram != '') {
                    socialLinks += `<a href="${producto.Instagram.startsWith('http') ? producto.Instagram : 'http://' + producto.Instagram}" class="contact-btn" target="_blank" rel="noopener noreferrer"><span class="fab fa-instagram"></span></a>`;
                }
                if (producto.twitter != '') {
                    socialLinks += `<a href="${producto.twitter.startsWith('http') ? producto.twitter : 'http://' + producto.twitter}" class="contact-btn" target="_blank" rel="noopener noreferrer"><span class="fab fa-twitter"></span></a>`;
                }
                if (producto.pagina_web !== '') {
                    socialLinks += `<a href="${producto.pagina_web.startsWith('http') ? producto.pagina_web : 'http://' + producto.pagina_web}" class="contact-btn" target="_blank" rel="noopener noreferrer"><span class="fab fa-chrome"></span></a>`;
                }
                if (producto.whatsapp != '') {
                    socialLinks += `<a href="https://wa.me/57${producto.telefono.replace(/\D/g, '')}?text=Estoy%20interesado%20en%20su%20producto%20${encodeURIComponent(producto.nombre)}" target="_blank" rel="noopener noreferrer" class="contact-btn"><span class="fab fa-whatsapp"></span></a>`;
                }


                const ir_tienda = document.querySelector(".ir_tienda");

                if (ir_tienda) {
                    ir_tienda.innerHTML = `<a href="detalletienda.php?id=${producto.id_negocio}" class="btn contact-btn">Vendido por <span class="nombre">${producto.nombre_tienda}</span></a>`;
                }

                const socialLinkContainer = document.querySelector('.product-details__social-link');
                if (socialLinkContainer) {
                    socialLinkContainer.innerHTML = socialLinks;
                }

                // Calcular y mostrar las estrellas
                const calificacion = producto.calificacion;

                const estrellas = document.querySelectorAll('.product-details__reveiw .fa-star');
                estrellas.forEach((estrella, index) => {
                    if (index < calificacion) {
                        estrella.classList.remove('checked');
                    } else {
                        estrella.classList.add('checked');
                    }
                });

                // Mostrar el botón de contacto o ir a la tienda
                const whatsappLink = producto.telefono ?
                    `<a href="https://wa.me/57${producto.telefono.replace(/\D/g, '')}?text=Estoy%20interesado%20en%20su%20producto%20${encodeURIComponent(producto.nombre)}" class="thm-btn contact-btn" target="_blank" rel="noopener noreferrer">Contactar por WhatsApp</a>` :
                    `<a href="detalletienda.php?id=${producto.id_negocio}" class="thm-btn contact-btn">Ir a la tienda</a>`;
                document.querySelector('.product-details__buttons-1').innerHTML = whatsappLink;

                // Agregar evento click a los botones de contacto
                document.querySelectorAll('.contact-btn').forEach(button => {
                    button.addEventListener('click', logContactClick);
                });
            })
            .catch(error => console.error('Error:', error));
    });



    // Escucha el evento de clic en el botón "Comentar"
    function comentario() {
        const commentInput = document.querySelector('.comment-input');
        const commentText = commentInput.value.trim();
        if (commentText !== '') {
            // Crear un nuevo comentario
            const newComment = document.createElement('div');
            newComment.classList.add('comment');
            newComment.innerHTML = `<p><strong>Tú:</strong> ${commentText}</p>`;

            // Agregar el nuevo comentario a la lista de comentarios
            const commentsSection = document.querySelector('.comments-section');
            commentsSection.insertBefore(newComment, commentsSection.querySelector('.add-comment'));

            // Limpiar el campo de entrada
            commentInput.value = '';
        };
    }
</script>
<style>
    .comments-section {
        margin-top: 20px;
        max-height: 340px;
        overflow: auto;
        display: flex;
        flex-direction: column;
    }

    .comment {
        margin-bottom: 10px;
        font-size: 14px;
        color: #555;
    }

    .add-comment {
        display: flex;
        align-items: center;
        margin-top: 10px;
    }

    .comment-input {
        width: 100%;
        background: #f3f3ed;
        padding: 8px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        margin-right: 10px;
    }

    .comment-submit {
        background-color: var(--ogenix-base);
        border: none;
        color: white;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 4px;
        font-size: 14px;
        transition: background-color 0.2s ease;
    }

    .comment-submit:hover {
        background-color: #0056b3;
    }

    .like-btn {
        margin-top: 10px;
    }

    .like-btn button {
        background-color: #f0f0f0;
        border: none;
        color: #555;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
        display: flex;
        align-items: center;
    }



    .like-btn button:hover {
        background-color: #e0e0e0;
    }

    .like-btn i {
        margin-right: 5px;
        color: #50a72c;
    }

    .product__menu-showing-sort {
        max-width: none;
        justify-content: flex-end;
    }

    figure.thumb {
        height: 100%;
    }

    .comments-area .comment-box .author-thumb img {
        object-fit: cover;
    }
</style>


<!--Product Details Start-->
<section class="product-details">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-xl-6">
                <div class="product-details__img">
                    <img src="" alt="" />
                </div>
            </div>
            <div class="col-lg-6 col-xl-6">
                <div class="product-details__top">
                    <h3 class="product-details__title"></h3>
                    <h3 class="product-details__title_Promocion"></h3>
                </div>
                <div class="product-details__reveiw">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <!--<span>2 Comentarios</span>-->
                </div>
                <div class="product-details__content">
                    <!-- <p class="product-details__content-text2">REF. 4231/406 <br>
                                Available in store</p> -->
                </div>

                <!-- <div class="product-details__quantity">
                            <h3 class="product-details__quantity-title">Choose quantity</h3>
                            <div class="quantity-box">
                                <button type="button" class="sub"><i class="fa fa-minus"></i></button>
                                <input type="number" id="1" value="1" />
                                <button type="button" class="add"><i class="fa fa-plus"></i></button>
                            </div>
                        </div> -->


                <?php if ($auth): ?>
                    <div class="product-details__buttons">
                        <div class="product-details__buttons-1">

                        </div>

                        <div class="product-details__buttons-2">
                            <div onclick="agregarAFavoritos(<?= $id_producto ?>)" class="thm-btn">Agregar a Favoritos</div>
                        </div>

                    </div>
                    <div class="product-details__social">
                        <div class="title">
                            <h3>contactar por:</h3>
                        </div>
                        <div class="product-details__social-link"></div>
                    </div>
                    <div class="ir_tienda"></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<!--Product Details End-->

<!--Product Description Start-->
<section class="product-description">
    <div class="container">
        <h3 class="product-description__title">Descripción</h3>
        <p class="product-description__text1"></p>
    </div>
</section>
<!--Product Description End-->

<!--Review One Start-->
<section class="review-one">
    <div class="container">
        <div style="margin-bottom: 10px" class="product__menu-showing-sort">
            <!--<div class="product__showing-sort">
                        <div class="select-box">
                            <select class="wide">
                                <option data-display="Ordenar por mas relevante">Ordenar por mas relevante</option>
                                <option value="2">Ordenar por recientes</option>
                                <option value="2">Ordenar por calificacion asc</option>
                                <option value="3">Ordenar por calificacion desc</option>
                            </select>
                        </div>
                    </div>-->
        </div>
        <div class="comments-area">
            <div class="review-one__title">
                <h3 id="comentarios-count">Cargando comentarios...</h3>
            </div>

            <!-- Contenedor de los comentarios dinámicos -->
            <div id="comentarios-list">
                <!-- Los comentarios serán inyectados aquí por JavaScript -->
            </div>
        </div>
    </div>
</section>
<!--Review One End-->

<script>
    const url = "assets/components/productos/comentarios/api/";

    const comentarioService = {
        // Obtener todos los comentarios
        getAllComentarios: async () => {
            const id_producto = urlParams.get('id');
            try {
                const response = await fetch(`${url}obtenercomentario.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_producto: id_producto
                    })
                });
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                const data = await response.json();
                if (!Array.isArray(data)) {
                    throw new Error("La respuesta no es un array válido de comentarios.");
                }
                return data;
            } catch (error) {
                console.error("API ERROR: OBTENER COMENTARIOS: " + error);
                throw error;
            }
        },

        // Enviar un subcomentario al servidor
        addSubcomentario: async (comentarioId, subcomentarioTexto) => {
            try {
                const response = await fetch(`${url}crearsubcomentario.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        comentario_id: comentarioId,
                        subcomentario: subcomentarioTexto,
                    })
                });

                const data = await response.json();
                if (data.success) {
                    // Recargar los comentarios para incluir el nuevo subcomentario
                    const comentarios = await comentarioService.getAllComentarios();
                    displayComentarios(comentarios);
                } else {
                    console.error("Error al agregar subcomentario: " + data.message);
                }
            } catch (error) {
                console.error("Error al enviar subcomentario: " + error);
            }
        }
    };

    // Función para mostrar los comentarios
    const displayComentarios = (comentarios) => {
        const auth = <?php echo json_encode($auth); ?>;
        const comentariosList = document.getElementById("comentarios-list");
        const comentariosCount = document.getElementById("comentarios-count");

        comentariosCount.textContent = `${comentarios.length} Comentarios`;
        comentariosList.innerHTML = '';

        comentarios.forEach(comentario => {
            const commentBox = document.createElement('div');
            commentBox.classList.add('comment-box');

            let subcomentariosHtml = '';
            if (comentario.subcomentarios && comentario.subcomentarios.length > 0) {
                subcomentariosHtml = comentario.subcomentarios.map(subcomentario => `
                            <div class="comment">
                                <p><strong> ${subcomentario.subcomentario_usuario_nombres} ${subcomentario.subcomentario_usuario_apellidos}:</strong> ${subcomentario.subcomentario}</p>
                            </div>
                        `).join('');
            }
            const fotoperfil = comentario.usuario_foto != null ? JSON.parse(comentario.usuario_foto) : '';
            const foto = fotoperfil ? convertLocalPathToUrl(fotoperfil[0].name) : '';

            const comment = `
                        <div class="comment">
                            <div class="author-thumb">
                                <figure class="thumb">
                                    <img src="${foto ? foto : 'assets/components/mapa/img/OIP.jpg'}" alt="Imagen de autor">
                                </figure>
                            </div>
                            <div class="review-one__content">
                                <div class="review-one__content-top">
                                    <div class="info">
                                        <h2>${comentario.usuario_nombres} ${comentario.usuario_apellidos}<span style="margin-left:10px;">${comentario.fecha_hora}</span></h2>
                                    </div>
                                    <div class="reply-btn">
                                        ${renderStars(comentario.estrellas)}
                                    </div>
                                </div>
                                <div class="review-one__content-bottom">
                                    <p>${comentario.comentario}</p>
                                    <div class="comments-section">
                                        ${subcomentariosHtml}
                                    </div>
                                  ${auth ? `
                                        <div class="add-comment">
                                            <input type="text" placeholder="Escribe un comentario..." class="comment-input" />
                                            <button class="comment-submit" onclick="handleAddSubcomentario(${comentario.comentario_id}, this)">Comentar</button>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;

            commentBox.innerHTML = comment;
            comentariosList.appendChild(commentBox);
        });
    };

    // Función para renderizar las estrellas de calificación
    const renderStars = (estrellas) => {
        let starsHtml = '';
        for (let i = 0; i < 5; i++) {
            if (i < estrellas) {
                starsHtml += '<i class="fa fa-star"></i>';
            } else {
                starsHtml += '<i class="fa fa-star-o"></i>';
            }
        }
        return starsHtml;
    };

    // Función que se llama cuando se hace clic en el botón de comentar
    function handleAddSubcomentario(comentarioId, button) {
        const input = button.previousElementSibling;
        const subcomentarioTexto = input.value.trim();
        if (subcomentarioTexto) {
            comentarioService.addSubcomentario(comentarioId, subcomentarioTexto);
            input.value = ''; // Limpiar el campo de texto después de enviar
        } else {
            alert("Por favor, escribe un subcomentario.");
        }
    }

    // Cargar los comentarios cuando la página se cargue
    window.onload = async () => {
        try {
            const comentarios = await comentarioService.getAllComentarios();
            displayComentarios(comentarios);
        } catch (error) {
            console.error("No se pudieron cargar los comentarios:", error);
        }
    };
</script>

<!--Start Review Form-->
<section class="review-form-one">
    <div class="container">
        <?php if ($auth): ?>
            <div class="review-form-one__inner">
                <h3 class="review-form-one__title">Agregar Comentario</h3>
                <div class="review-form-one__rate-box">
                    <p class="review-form-one__rate-text">Puntaje del producto?</p>
                    <div class="review-form-one__rate">
                        <i class="fa fa-star" data-rating="1"></i>
                        <i class="fa fa-star" data-rating="2"></i>
                        <i class="fa fa-star" data-rating="3"></i>
                        <i class="fa fa-star" data-rating="4"></i>
                        <i class="fa fa-star" data-rating="5"></i>
                    </div>
                </div>
                <form id="comentarioForm" class="review-form-one__form contact-form-validated" novalidate="novalidate">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="review-form-one__input-box text-message-box">
                                <textarea name="comentario" id="comentario" placeholder="Escribir un comentario" required></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Campo oculto para la fecha y hora -->
                    <input type="hidden" id="fecha_hora" name="fecha_hora" required><br>
                    <!-- Campo oculto para almacenar el puntaje de las estrellas -->
                    <input type="hidden" id="estrellas" name="estrellas" required><br>

                    <div class="row">
                        <div class="col-xl-12">
                            <button type="submit" class="thm-btn review-form-one__btn" id="submitBtn">Enviar Comentario</button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>
<!--End Review Form-->

<script>
    // Manejar el envío del formulario sin recargar la página
    document.getElementById('comentarioForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Evitar el envío normal del formulario
        const id_producto = urlParams.get('id');
        // Obtener la fecha y hora actual en el formato correcto
        var now = new Date();
        var fecha_hora = now.getFullYear() + '-' +
            ('0' + (now.getMonth() + 1)).slice(-2) + '-' +
            ('0' + now.getDate()).slice(-2) + ' ' +
            ('0' + now.getHours()).slice(-2) + ':' +
            ('0' + now.getMinutes()).slice(-2) + ':' +
            ('0' + now.getSeconds()).slice(-2);

        // Establecer la fecha_hora en el campo oculto
        document.getElementById('fecha_hora').value = fecha_hora;

        // Verificar que se haya seleccionado un puntaje de estrellas
        var estrellas = document.getElementById('estrellas').value;
        if (!estrellas) {
            alert('Por favor, selecciona una calificación de estrellas.');
            return;
        }

        var formData = new FormData(this);
        formData.append('id_producto', id_producto);
        // Enviar el formulario usando fetch
        fetch('assets/components/productos/comentarios/api/crearcomentario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(async (data) => {
                // No mostrar la respuesta del servidor
                // document.getElementById('response').innerHTML = data;

                // Recargar los comentarios actualizados sin recargar la página
                try {
                    const comentarios = await comentarioService.getAllComentarios();
                    displayComentarios(comentarios);
                } catch (error) {
                    console.error("No se pudieron cargar los comentarios:", error);
                }

                // Limpiar el formulario después de enviar
                document.getElementById('comentarioForm').reset();
                document.querySelectorAll('.review-form-one__rate i').forEach(star => {
                    star.classList.remove('selected');
                });
            })
    });

    // Agregar interactividad para las estrellas (selección del puntaje)
    const stars = document.querySelectorAll('.review-form-one__rate i');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');

            // Marcar las estrellas seleccionadas
            stars.forEach(star => {
                if (star.getAttribute('data-rating') <= rating) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            });

            // Asignar el valor del puntaje
            document.getElementById('estrellas').value = rating;
        });
    });
</script>

<style>
    .review-form-one__rate i {
        color: #a1a1a1;
        /* Color gris cuando no está seleccionado */
    }

    .review-form-one__rate i.selected {
        color: gold;
        /* Color dorado cuando está seleccionado */
    }
</style>