<!-- Añadir el HTML del spinner al inicio -->
<div id="loading-spinner" class="loading-container">
    <div class="spinner"></div>
</div>

<style>
    /* Estilos del spinner */
    .loading-container {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-radius: 50%;
        border-top: 5px solid var(--ogenix-base);
        animation: spin 1s linear infinite;
    }

    .close {
        display: none;
    }

    @media screen and (max-width: 765px) {
        .close {
            display: block;
            position: relative;
            top: 0px;
            left: 93%;
        }
    }


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
    function showSpinner() {
        const spinner = document.getElementById('loading-spinner');
        if (spinner) {
            spinner.style.display = 'flex';
        }
    }

    function hideSpinner() {
        const spinner = document.getElementById('loading-spinner');
        if (spinner) {
            spinner.style.display = 'none';
        }
    }

    // Mostrar spinner antes de comenzar la carga
    showSpinner();

    let departamento = localStorage.getItem('departamento');
    fetch('assets/components/publicidad/getpublicidad.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            departamento: departamento,
        }),
    })
        .then(response => response.json())
        .then(data => {
            function ensureRedemprendedoresPath(path) {
                let nombre_imagen = path.replace(/.*files\//, '');
                return `/redemprendedores/files/${nombre_imagen}`;
            }

            function ensurelocalredemprendedores(localPath) {
                let nombre_imagen = localPath.replace(/.*files\//, '');
                return `/redemprendedores/output/files/${nombre_imagen}`;
            }

            function convertLocalPathToUrl(localPath) {
                const baseUrl = window.location.origin;
                if (baseUrl.includes('localhost')) {
                    return ensurelocalredemprendedores(localPath);
                } else {
                    return ensureRedemprendedoresPath(localPath);
                }
            }

            data = data.publicidad;
            if (data && data.id_publicidad) {
                try {
                    console.log(data)
                    const imagenData = JSON.parse(data.imagen_evento);
                    const imagePath = imagenData[0].name;

                    document.querySelector('.section-title__tagline').innerText = data.departamento || 'Sin ubicación';
                    document.querySelector('.section-title__title').innerText = data.nombre_evento || 'Sin título';
                    document.querySelector('.custom-deal-modal__text').innerText = data.descripcion_evento || 'Sin descripción';
                    document.querySelector('.custom-deal-modal__text + p span').innerText = data.ubicacion || 'Sin ubicación';
                    document.querySelector('.countdown').setAttribute('name', data.fecha_inicio || '');

                    const imageUrl = imagePath || 'https://via.placeholder.com/400';
                    const imgElement = document.querySelector('.custom-deal-modal__img');
                    imgElement.src = convertLocalPathToUrl(imageUrl);

                    imgElement.onload = function () {
                        hideSpinner();
                    };

                    imgElement.onerror = function () {
                        console.error('Error al cargar la imagen');
                        hideSpinner();
                    };

                    // Iniciar el contador
                    updateCountdown();
                    setInterval(updateCountdown, 1000);

                } catch (error) {
                    console.error('Error al parsear la imagen:', error);
                    document.querySelector('.custom-deal-modal__img').src = 'https://via.placeholder.com/400';
                    hideSpinner();
                }
            } else {
                var modal = document.getElementById("Modal");
                var container_modal = document.getElementById("container_modal");
                var btn = document.getElementById("openModalBtn");
                var span = document.getElementsByClassName("close")[0];
                document.body.classList.add("no-scroll");
                modal.style.display = "none";
                container_modal.style.display = "none";
                document.body.classList.remove("no-scroll");
                console.error('No se encontraron datos válidos:', data);
                hideSpinner();
            }
        })
        .catch(error => {
            console.error('Error en la llamada fetch:', error);
            hideSpinner();
        });

    function updateCountdown() {
        const countdownElement = document.querySelector('.countdown');
        const endTime = new Date(countdownElement.getAttribute('name')).getTime();
        const now = new Date().getTime();
        const timeLeft = endTime - now;
        if (timeLeft <= 0) {
            document.getElementById('Modal').style.display = 'none';
            document.getElementById('container_modal').style.display = 'none';
            document.body.classList.remove('no-scroll');
            return;
        }

        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        countdownElement.querySelector('.days').innerText = formatTime(days);
        countdownElement.querySelector('.hours').innerText = formatTime(hours);
        countdownElement.querySelector('.minutes').innerText = formatTime(minutes);
        countdownElement.querySelector('.seconds').innerText = formatTime(seconds);
    }

    function formatTime(time) {
        return time < 10 ? `0${time}` : time;
    }



</script>


<!-- El Modal -->
<div id="container_modal" class="container_modal">
    <div id="Modal" class="modal">
        <div class="modal-content">
            <span onclick="close_modal_publicitario()" class="close">X</span>
            <script>
                var modal = document.getElementById("Modal");
                var container_modal = document.getElementById("container_modal");
                document.body.classList.add("no-scroll");

                {
                    /*if (!(window.location.pathname === "/red_emprendedores/output/front/files/index.php")) {
                                         container_modal.style.display = "none";
                                         modal.style.display = "none";
                                         document.body.classList.remove("no-scroll");
                                     }*/
                }


                var span = document.getElementsByClassName("close")[0];

                function close_modal_publicitario(){
                    modal.style.display = "none";
                        container_modal.style.display = "none";
                        document.body.classList.remove('no-scroll');
                }

                window.onclick = function (event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                        container_modal.style.display = "none";
                        document.body.classList.remove('no-scroll');
                    }
                }
            </script>
            <style>
                @keyframes slideInFromTop {
                    from {
                        opacity: 0;
                        transform: translateY(-30px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @keyframes slideOutToTop {
                    from {
                        opacity: 1;
                        transform: translateY(0);
                    }

                    to {
                        opacity: 0;
                        transform: translateY(-30px);
                    }
                }

                /* Contenedor principal */
                .custom-deal-modal {
                    animation: slideInFromTop 1s ease-in;
                    animation: slideOutToTop 0.3s ease-out;
                    background-color: #f9f9f9;
                    border-radius: 8px;
                    max-width: 1440px;
                    margin: 0 auto;
                    box-sizing: border-box;
                }

                /* Estructura interna del componente */
                .custom-deal-modal__container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    gap: 30px;
                    text-align: center;
                    flex-wrap: wrap;
                }

                /* Estilos para la parte izquierda */
                .custom-deal-modal__left {
                    flex: 1;
                    min-width: 280px;
                    padding-right: 20px;
                    box-sizing: border-box;
                    text-align: center;
                }

                /* Estilo del título y el subtítulo */
                .section-title__tagline {
                    font-size: 14px;
                    color: #ff6347;
                }

                .section-title__title {
                    font-size: 28px;
                    font-weight: bold;
                    margin: 10px 0;
                }

                /* Texto del componente */
                .custom-deal-modal__text {
                    font-size: 16px;
                    color: #333;
                    margin-bottom: 20px;
                    max-width: 400px;
                    margin-left: auto;
                    margin-right: auto;
                }

                /* Contenedor para la cuenta regresiva */
                .custom-deal-modal__countdown {
                    display: flex;
                    justify-content: center;
                    margin-bottom: 20px;
                }

                /* Estilos de los elementos individuales de la cuenta regresiva */
                .custom-deal-modal__countdown .countdown {
                    display: flex;
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }

                .custom-deal-modal__countdown .box {
                    text-align: center;
                    background: #fff;
                    border: none;
                    border-radius: 5px;
                    padding: 10px;
                    margin-right: 10px;
                    width: 70px;
                    font-size: 10px;
                }

                .custom-deal-modal__countdown .box span {
                    display: block;
                }

                .custom-deal-modal__countdown .box .days,
                .custom-deal-modal__countdown .box .hours,
                .custom-deal-modal__countdown .box .minutes,
                .custom-deal-modal__countdown .box .seconds {
                    font-size: 24px;
                    font-weight: bold;
                }

                /* Estilos para la parte derecha */
                .custom-deal-modal__right {
                    flex: 1;
                    min-width: 280px;
                    text-align: center;
                    position: relative;
                }

                /* Imagen del producto */
                .custom-deal-modal__img {
                    width: 100%;
                    max-width: 400px;
                    /* Ajusta según el tamaño de tu modal */
                    height: auto;
                    object-fit: cover;
                    border-radius: var(--ogenix-bdr-radius);
                    /* Usa el radio de borde definido en tu paleta */
                    box-shadow: 0 4px 8px rgba(var(--ogenix-black-rgb), 0.2);
                    /* Sombra con color negro definido en tu paleta */
                    transition: transform 0.3s ease, box-shadow 0.3s ease;

                }

                .custom-deal-modal__img:hover {
                    transform: scale(1.05);
                    /* Efecto de zoom en hover */
                    box-shadow: 0 6px 12px rgba(var(--ogenix-black-rgb), 0.3);
                    /* Sombra más intensa en hover */
                }


                /* Texto grande superpuesto en la imagen */
                .custom-deal-modal__big-text {
                    position: absolute;
                    bottom: 10px;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 36px;
                    font-weight: bold;
                    color: #ff6347;
                    opacity: 0.8;
                }

                /* Responsividad */
                @media only screen and (max-width: 767px) {


                    .custom-deal-modal__container {
                        flex-direction: column;
                        align-items: center;
                        gap: 15px;
                    }

                    .custom-deal-modal__left,
                    .custom-deal-modal__right {
                        padding: 0;
                        width: 100%;
                    }

                    .section-title__title {
                        font-size: 24px;
                        margin: 5px 0;
                    }

                    .custom-deal-modal__text {
                        font-size: 14px;
                        margin-bottom: 15px;
                    }

                    .custom-deal-modal__countdown {
                        margin-bottom: 15px;
                    }

                    .custom-deal-modal__countdown .box {
                        width: 60px;
                        padding: 8px;
                    }

                    .custom-deal-modal__countdown .box .days,
                    .custom-deal-modal__countdown .box .hours,
                    .custom-deal-modal__countdown .box .minutes,
                    .custom-deal-modal__countdown .box .seconds {
                        font-size: 20px;
                    }

                    .custom-deal-modal__img {
                        max-width: 80%;
                        max-height: 290px;
                        object-fit: contain;
                    }

                    .custom-deal-modal__big-text {
                        font-size: 24px;
                        bottom: 5px;
                    }
                }
            </style>

            <section class="custom-deal-modal">
                <div class="custom-deal-modal__container">
                    <div class="custom-deal-modal__left">
                        <div class="section-title">
                            <span class="section-title__tagline">Barranquilla</span>
                            <h2 class="section-title__title">Feria de vino</h2>
                        </div>
                        <p class="custom-deal-modal__text">Feria de vino, whisky, cervezas y otros licores. Expo
                            Bebidas es la
                            feria que permite a los visitantes conocer, degustar y conocer la cultura del buen beber y
                            sus
                            maridajes
                        </p>
                        <p>
                            Ubicacion: <span style="font-weight: bold; color: #ff6347;">centro de eventos puerta de
                                oro</span>
                        </p>
                        <span class="section-title__tagline" style="color: --ogenix-black">Fecha de inicio</span>
                        <div class="custom-deal-modal__countdown">
                            <ul class="countdown" id="contador" name="2024/10/03">
                                <li>
                                    <div class="box">
                                        <span class="days">00</span>
                                        <span class="timeRef">Days</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="box">
                                        <span class="hours">00</span>
                                        <span class="timeRef">Hours</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="box">
                                        <span class="minutes">00</span>
                                        <span class="timeRef">Minutes</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="box">
                                        <span class="seconds">00</span>
                                        <span class="timeRef">Seconds</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="custom-deal-modal__right">
                        <a href="./">
                            <img src="https://i.revistapym.com.co/cms/2024/07/29181104/expovinos2024.jpg?w=920&d=2"
                                alt="Product Image" class="custom-deal-modal__img">
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>