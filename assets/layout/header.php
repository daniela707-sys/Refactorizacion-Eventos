<div class="custom-cursor__cursor"></div>
<div class="custom-cursor__cursor-two"></div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


<?php
//obtener token de phprunner que esta en la cookies de la pagina y se llama runnerSession
$auth = isset($_COOKIE['runnerSession']) ? true : false;

function getInicioUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/tienda/';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/tienda/';
    }
}

function getTiendaUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/tienda/tienda.php';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/tienda/tienda.php';
    }
}

function getProductoUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/tienda/productos.php';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/tienda/productos.php';
    }
}

function getEventosUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/eventos/';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/eventos/';
    }
}

function getNoticiasUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/noticias/';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/noticias/';
    }
}

function getWishlistUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/tienda/wishlist.php';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/tienda/wishlist.php';
    }
}

$InicioUrl = getInicioUrl();
$TiendasUrl = getTiendaUrl();
$ProductosUrl = getProductoUrl();
$EventosUrl = getEventosUrl();
$WishlistUrl = getWishlistUrl();
$NoticiasUrl = getNoticiasUrl();
?>

<script>
    function formatCurrency(amount) {
        // Verifica si el número es válido
        if (isNaN(amount)) {
            return "Invalid amount";
        }

        // Convierte el número a formato de moneda
        return Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2 });
    }
    function agregarAFavoritos(id) {
        try {
            // Obtener favoritos actuales del localStorage o crear array vacío si no existe
            let favoritos = JSON.parse(localStorage.getItem('favoritos')) || [];

            // Crear objeto con los datos proporcionados
            const productoParaFavorito = {
                id
            };

            // Verificar si el producto ya existe en favoritos
            const existeEnFavoritos = favoritos.some(fav => fav.id === id);

            // Si no existe, lo agregamos
            if (!existeEnFavoritos) {
                favoritos.push(productoParaFavorito);
                localStorage.setItem('favoritos', JSON.stringify(favoritos));
                alert("Agregado a favoritos");
                return true; // Éxito al agregar
            }

            return false; // Ya existía en favoritos

        } catch (error) {
            console.error('Error al agregar a favoritos:', error);
            return false; // Error al agregar
        }
    }
</script>
<header class="main-header">
    <div class="main-header__wrapper">
        <div class="main-header__wrapper-inner">
            <div class="main-header__right">
                <div class="main-header__top">
                    <div class="main-header__top-inner">
                        <div class="main-header__top-left">
                            <p class="main-header__top-left-text">¡Bienvenido a nuestra Red de Emprendedores!</p>
                            <div class="main-header__social">
                                <a href="https://twitter.com/SENAComunica"><i class="fab fa-twitter"></i></a>
                                <a href="https://www.facebook.com/SENA/"><i class="fab fa-facebook"></i></a>
                                <a href="https://www.tiktok.com/@senacomunica_"><i class="fab fa-tiktok"></i></a>
                                <a href="https://www.instagram.com/senacomunica/"><i class="fab fa-instagram"></i></a>
                                <a href="https://wa.me/573112545028" target="_blank">
                                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                        <div class="main-header__top-right">
                            <ul class="list-unstyled main-header__contact-list">
                                <li>
                                    <!-- <div class="icon">
                                        <i class="fas fa-mobile"></i>
                                    </div> -->
                                    <!-- <div class="text">
                                        <p><a href="tel:923076806860">+ 92 ( 307 ) 68 - 06860</a></p>
                                    </div> -->
                                </li>
                                <li>
                                    <div class="icon" style="width: 10px;">
                                        <i class="fas fa-envelope-open"></i>
                                    </div>
                                    <div class="text">
                                        <p><a href="mailto:redemprendedores@sena.com">redemprendedores@sena.com</a></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="main-header__right-bottom">
                    <nav class="main-menu">
                        <div class="main-menu__wrapper">
                            <div class="main-menu__wrapper-inner">
                                <div class="main-header__logo">
                                    <a href="<?php echo $InicioUrl; ?>">
                                        <img src="assets/images/logo/logo1.png" height="60px" width="60PX" alt="">
                                    </a>
                                </div>
                                <div class="main-menu__main-menu-box">
                                    <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                                    <ul class="main-menu__list">
                                        <li>
                                            <a href="<?php echo $InicioUrl; ?>">Inicio </a>
                                        </li>
                                        <!-- <li class="dropdown">
                                            <a>Categorias</a>
                                            <ul class="sub-menu" id="categories-list">
                                            </ul>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function () {
                                                    fetch('assets/components/caracterizacion/categoriasysubcategorias/getcategorias.php', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json'
                                                        },
                                                    })
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            const categories = data.categorias;
                                                            const categoriesList = document.getElementById('categories-list');

                                                            categories.forEach(categoria => {
                                                                const listItem = document.createElement('li');
                                                                listItem.classList.add('dropdown');
                                                                const categoryLink = document.createElement('a');
                                                                categoryLink.href = './productos.php?departamento=todos&categorias=' + encodeURIComponent(categoria.id_categoria);
                                                                categoryLink.textContent = categoria.nombre;
                                                                listItem.appendChild(categoryLink);
                                                                categoriesList.appendChild(listItem);
                                                            });

                                                            if ($(".main-menu__list").length && $(".mobile-nav__container").length) {
                                                                let navContent = document.querySelector(".main-menu__list").outerHTML;
                                                                let mobileNavContainer = document.querySelector(".mobile-nav__container");
                                                                mobileNavContainer.innerHTML = navContent;

                                                                <?php if (!$auth): ?>
                                                                    mobileNavContainer.innerHTML += `
                                                                            <div class="main-menu__btn-box">
                                                                                <button onclick="openlogin_mobil()" id="openLoginModal" class="thm-btn main-menu__btn">Iniciar Sesion</button>
                                                                            </div>`;
                                                                <?php else: ?>
                                                                    mobileNavContainer.innerHTML += `
                                                                            <div class="main-menu__btn-box">
                                                                                <button onclick="cerrar_session()" class="thm-btn main-menu__btn">Cerrar Sesion</button>
                                                                            </div>`;
                                                                <?php endif; ?>
                                                            }

                                                            if ($(".mobile-nav__container .main-menu__list").length) {
                                                                let dropdownAnchor = $(
                                                                    ".mobile-nav__container .main-menu__list .dropdown > a"
                                                                );
                                                                dropdownAnchor.each(function () {
                                                                    let self = $(this);
                                                                    let toggleBtn = document.createElement("BUTTON");
                                                                    toggleBtn.setAttribute("aria-label", "dropdown toggler");
                                                                    toggleBtn.innerHTML = "<i class='fa fa-angle-down'></i>";
                                                                    self.append(function () {
                                                                        return toggleBtn;
                                                                    });
                                                                    self.find("button").on("click", function (e) {
                                                                        e.preventDefault();
                                                                        let self = $(this);
                                                                        self.toggleClass("expanded");
                                                                        self.parent().toggleClass("expanded");
                                                                        self.parent().parent().children("ul").slideToggle();
                                                                    });
                                                                });
                                                            }
                                                        })
                                                        .catch(error => console.error('Error:', error));
                                                });
                                            </script>
                                        </li> -->
                                        <li>
                                            <a href="<?php echo $TiendasUrl; ?>">Tiendas</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $ProductosUrl; ?>">Productos</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $EventosUrl; ?>">Eventos</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $NoticiasUrl; ?>">Noticias</a>
                                        </li>
                                        <li>
                                            <a id="openmapmodal" onclick="openmap_mobil()">Mi ubicacion</a>
                                        </li>
                                        <li class="main-menu__wishlist-box">
                                            <a href="<?php echo $WishlistUrl; ?>" class="main-menu__wishlist" title="Wishlist">
                                                <i class="fas fa-heart"></i>
                                                <span class="main-menu__wishlist-text">Favoritos</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <script>
                                    function cerrar_session() {
                                        fetch('assets/components/acceso/logout.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            }
                                        }).then(response => response.json())
                                            .then(data => {
                                                if (data.status === 'success') {
                                                    window.location.reload();
                                                }
                                            })
                                            .catch(error => console.error('Error:', error));
                                    }
                                </script>
                                <?php if (!$auth): ?>
                                    <div class="main-menu__btn-box desktop-only">
                                        <button onclick="openlogin_mobil()" id="openLoginModal"
                                            class="thm-btn main-menu__btn">Iniciar Sesion</button>
                                    </div>
                                <?php else: ?>
                                    <!--<div class="main-menu__btn-box desktop-only">
                                        <a href="./perfil.php" class="thm-btn main-menu__btn">Mi Perfil</a>
                                    </div>
                                -->
                                    <div class="main-menu__btn-box desktop-only">
                                        <button onclick="cerrar_session()" class="thm-btn main-menu__btn">Cerrar
                                            Sesion</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>

<?php require_once "assets/components/login.php"; ?>
<?php require_once "assets/components/map.php"; ?>

<div class="mobile-nav__wrapper">
    <div class="mobile-nav__overlay mobile-nav__toggler"></div>
    <!-- /.mobile-nav__overlay -->
    <div class="mobile-nav__content">
        <span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>

        <div class="logo-box">
            <a href="index.html" aria-label="logo image"><img src="assets/images/logo/logo_blanco.png" width="104"
                    alt="" /></a>
        </div>
        <!-- /.logo-box -->
        <div class="mobile-nav__container">
            <ul class="mobile-nav__list">
                <li>
                    <a href="./index.php">Inicio </a>
                </li>
                <li class="dropdown">
                    <a>Categorias</a>
                    <ul class="sub-menu" id="mobile-categories-list">
                        <!-- Los elementos de la lista se agregarán dinámicamente aquí -->
                    </ul>
                </li>
                <li>
                    <a href="./tienda.php">Tiendas</a>
                </li>
                <li>
                    <a href="./productos.php">Productos</a>
                </li>
                <li>
                    <a id="openmapmodal" onclick="openmap_mobil()">Mi ubicacion</a>
                </li>
                <li class="main-menu__wishlist-box">
                    <a href="./wishlist.php" class="main-menu__wishlist" title="Wishlist">
                        <i class="fas fa-heart"></i>
                        <span class="main-menu__wishlist-text">Favoritos</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- /.mobile-nav__container -->

        <ul class="mobile-nav__contact list-unstyled">
            <li>
                <i class="fa fa-envelope"></i>
                <a href="mailto:redemprendedores@sena.com">redemprendedores@sena.com</a>
            </li>
            <!-- <li>
                <i class="fa fa-phone-alt"></i>
                <a href="tel:666-888-0000">666 888 0000</a>
            </li> -->
        </ul><!-- /.mobile-nav__contact -->
        <div class="mobile-nav__top">
            <div class="mobile-nav__social">
                <a href="#" class="fab fa-twitter"></a>
                <a href="#" class="fab fa-facebook-square"></a>
                <a href="#" class="fab fa-pinterest-p"></a>
                <a href="#" class="fab fa-instagram"></a>
            </div><!-- /.mobile-nav__social -->
        </div><!-- /.mobile-nav__top -->
    </div>
    <!-- /.mobile-nav__content -->
</div>

<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('assets/components/caracterizacion/categoriasysubcategorias/getcategorias.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
        })
            .then(response => response.json())
            .then(data => {
                const categories = data.categorias;
                const categoriesList = document.getElementById('mobile-categories-list');

                categories.forEach(categoria => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('dropdown');
                    const categoryLink = document.createElement('a');
                    categoryLink.href = './productos.php?categorias=departamento=todos&' + encodeURIComponent(categoria.id_categoria);
                    categoryLink.textContent = categoria.nombre;
                    listItem.appendChild(categoryLink);
                    categoriesList.appendChild(listItem);
                });

                // Crear el menú móvil después de cargar las categorías
                if ($(".main-menu__list").length && $(".mobile-nav__container").length) {
                    let navContent = document.querySelector(".main-menu__list").outerHTML;
                    let mobileNavContainer = document.querySelector(".mobile-nav__container");
                    mobileNavContainer.innerHTML = navContent;

                    // Agregar botones de sesión después de cargar las categorías
                    <?php if (!$auth): ?>
                        mobileNavContainer.innerHTML += `
                                <div class="main-menu__btn-box">
                                    <button onclick="openlogin_mobil()" id="openLoginModal" class="thm-btn main-menu__btn">Iniciar Sesion</button>
                                </div>`;
                    <?php else: ?>
                        mobileNavContainer.innerHTML += `
                                <div class="main-menu__btn-box">
                                    <button onclick="cerrar_session()" class="thm-btn main-menu__btn">Cerrar Sesion</button>
                                </div>`;
                    <?php endif; ?>
                }

                if ($(".mobile-nav__container .main-menu__list").length) {
                    let dropdownAnchor = $(
                        ".mobile-nav__container .main-menu__list .dropdown > a"
                    );
                    dropdownAnchor.each(function () {
                        let self = $(this);
                        let toggleBtn = document.createElement("BUTTON");
                        toggleBtn.setAttribute("aria-label", "dropdown toggler");
                        toggleBtn.innerHTML = "<i class='fa fa-angle-down'></i>";
                        self.append(function () {
                            return toggleBtn;
                        });
                        self.find("button").on("click", function (e) {
                            e.preventDefault();
                            let self = $(this);
                            self.toggleClass("expanded");
                            self.parent().toggleClass("expanded");
                            self.parent().parent().children("ul").slideToggle();
                        });
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    });

    // Toggle mobile navigation
    if ($(".mobile-nav__toggler").length) {
        $(".mobile-nav__toggler").on("click", function (e) {
            e.preventDefault();
            $(".mobile-nav__wrapper").toggleClass("expanded");
            $("body").toggleClass("locked");
        });
    }

    if ($(".search-toggler").length) {
        $(".search-toggler").on("click", function (e) {
            e.preventDefault();
            $(".search-popup").toggleClass("active");
            $(".mobile-nav__wrapper").removeClass("expanded");
            $("body").toggleClass("locked");
        });
    }
</script>

<style>
    /* Ocultar el menú de la parte superior en la versión móvil */
    @media (max-width: 768px) {
        .main-header__top {
            display: none;
        }

        .desktop-only {
            display: none;
        }
    }
</style>