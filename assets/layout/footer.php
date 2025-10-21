<?php
//obtener token de phprunner que esta en la cookies de la pagina y se llama runnerSession
$footer_auth = isset($_COOKIE['runnerSession']) ? true : false;

function footer_getInicioUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/tienda/';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/tienda/';
    }
}

function footer_getTiendaUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/tienda/tienda.php';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/tienda/tienda.php';
    }
}

function footer_getProductoUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/tienda/productos.php';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/tienda/productos.php';
    }
}

function footer_getEventosUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/eventos/';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/eventos/';
    }
}

function footer_getNoticiasUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/noticias/';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/noticias/';
    }
}

function footer_getTyCUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/tienda/tyc.php';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/tienda/tyc.php';
    }
}

function footer_getPrivacidadUrl(){
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return 'http://localhost/redemprendedores/output/tienda/privacidad.php';
    } else {
        return 'https://tecnoparqueatl.com/redemprendedores/tienda/privacidad.php';
    }
}

$footer_InicioUrl = footer_getInicioUrl();
$footer_TiendasUrl = footer_getTiendaUrl();
$footer_ProductosUrl = footer_getProductoUrl();
$footer_EventosUrl = footer_getEventosUrl();
$footer_NoticiasUrl = footer_getNoticiasUrl();
$footer_TYC = footer_getTyCUrl();
$footer_Privacidad = footer_getPrivacidadUrl();
?>

<!--Site Footer Start-->
<footer class="site-footer">
    <div class="site-footer__bg" style="background-image: url('../images/backgrounds/site-footer-bg-img.png')"></div>
    <!-- <div class="site-footer__ripped-paper"
        style="background-image: url(assets/images/shapes/site-footer-ripped-paper.png);"></div> -->
    <div class="container">
        <div class="site-footer__top">
            <div class="row" style="justify-content: space-between;">
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                    <div class="footer-widget__column footer-widget__about">
                        <div class="footer-widget__logo">
                            <a href="/"><img src="../images/logo/logo.png" width="21%" height="73px"
                                    alt=""></a>
                        </div>
                        <div class="footer-widget__about-text-box">
                            <p class="footer-widget__about-text">Red de emprendedores</p>
                            <p class="main-header__top-left-text">¡Bienvenido a la Red de Emprendedores! 
                                Un espacio que impulsa la conexión, colaboración e innovación entre emprendedores de todo el país.
                            </p>
                        </div>
                        <div class="footer-widget__social-box">
                            <a href="https://twitter.com/SENAComunica"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.facebook.com/SENA/"><i class="fab fa-facebook"></i></a>
                            <a href="https://www.tiktok.com/@senacomunica_"><i class="fab fa-tiktok"></i></a>
                            <a href="https://www.instagram.com/senacomunica/"><i class="fab fa-instagram"></i></a>
                            <a href="https://wa.me/573112545028" target="_blank">
                                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                    <div class="footer-widget__column footer-widget__explore">
                        <div class="footer-widget__title-box">
                            <h3 class="footer-widget__title">Explorar</h3>
                        </div>
                        <div class="footer-widget__explore-list-box">
                            <ul class="footer-widget__explore-list list-unstyled">
                                <li><a href="<?php echo $footer_InicioUrl; ?>">Inicio</a></li>
                                <li><a href="<?php echo $footer_TiendasUrl; ?>">Tiendas</a></li>
                                <li><a href="<?php echo $footer_ProductosUrl; ?>">Productos</a></li>
                                <li><a href="<?php echo $footer_EventosUrl; ?>">Eventos</a></li>
                                <li><a href="<?php echo $footer_NoticiasUrl; ?>">Noticias</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                    <div class="footer-widget__column footer-widget__contact">
                        <div class="footer-widget__title-box">
                            <h3 class="footer-widget__title">Contactanos</h3>
                        </div>
                        <p class="footer-widget__contact-text">Calle 57 No. 8 - 69 Bogotá D.C. (Cundinamarca), Colombia</p>
                        <ul class="list-unstyled footer-widget__contact-list">
                            <li>
                                <div class="text">
                                    <!-- <p><a href="tel:928800688960">+92 (8800) 68 - 8960</a></p> -->
                                </div>
                            </li>
                            <li>
                                <div class="text">
                                    <p class="footer-widget__contact-text">Bogotá +(57) 601 736 60 60 - Línea gratuita y resto del país 018000 910270</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-footer__bottom-inner">
                        <div class="site-footer__bottom-left">
                            <p class="site-footer__bottom-text">© Copyright 2024 by <a href="https://wa.me/3022092594"
                                    target="_blank">Tecnoparque Atlantico </a>
                            </p>
                        </div>
                        <div class="site-footer__bottom-right">
                            <ul class="list-unstyled site-footer__bottom-menu">
                                <li><a href="<?php echo $footer_TYC; ?>">Términos y condiciones</a></li>
                                <li><span>/</span></li>
                                <li><a href="<?php echo $footer_Privacidad; ?>">Política de privacidad</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!--Site Footer End-->

<!-- /.mobile-nav__wrapper -->

<!-- /.search-popup -->

<a href="#" data-target="html" class="scroll-to-target scroll-to-top"><i class="icon-up-arrow"></i></a>