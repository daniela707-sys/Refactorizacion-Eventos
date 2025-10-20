     <x-layout>
     <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header-bg" style="background-image: url(assets/images/backgrounds/page-header-bg.jpg)">
            </div>
            <div class="page-header__ripped-paper"
                style="background-image: url(assets/images/shapes/page-header-ripped-paper.png);"></div>
            <div class="container">
                <div class="page-header__inner">
                    <ul class="thm-breadcrumb list-unstyled">
                        <li><a href="index.php">Inicio</a></li>
                        <li><span>/</span></li>
                        <li>Contacto</li>
                    </ul>
                    <h2>Contacto</h2>
                </div>
            </div>
        </section>
        <!--Page Header End-->


        <!--Google Map Start-->
        <section class="google-map">
            <div class="container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4562.753041141002!2d-118.80123790098536!3d34.152323469614075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80e82469c2162619%3A0xba03efb7998eef6d!2sCostco+Wholesale!5e0!3m2!1sbn!2sbd!4v1562518641290!5m2!1sbn!2sbd"
                    class="google-map__one" allowfullscreen></iframe>

            </div>
        </section>
        <!--Google Map End-->

        <!--Contact Details Start-->
        <section class="contact-details">
            <div class="container">
                <div class="contact-details__inner">
                    <ul class="contact-details__contact-list list-unstyled">
                        <li>
                            <div class="icon">
                                <span class="icon-help"></span>
                            </div>
                            <div class="content">
                                <p>¿Tiene alguna pregunta?</p>
                                <h4>
                                    <a href="tel:923076806860">+ 92 ( 307 ) 68 - 06860</a>
                                </h4>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <span class="icon-mailbox"></span>
                            </div>
                            <div class="content">
                                <p>Escribir correo electrónico</p>
                                <h4>
                                    <a href="mailto:needhelp@company.com">needhelp@company.com</a>
                                </h4>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <span class="icon-maps-and-flags"></span>
                            </div>
                            <div class="content">
                                <p>Visitar tienda</p>
                                <h4>Valentin, Street Road 24, New York</h4>
                            </div>
                        </li>
                    </ul>
                    <div class="contact-details__social-box">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-pinterest-p"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </section>
        <!--Contact Details End-->

        <!--Contact Page Start-->
        <section class="contact-page">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="section-title text-center">
                            <span class="section-title__tagline">Escribe un mensaje</span>
                            <h2 class="section-title__title">Siempre estamos aquí para
                                <br> ayudarte
                            </h2>
                        </div>
                        <div class="contact-page__content">
                            <form action="assets/inc/sendemail.php" class="contact-page__form contact-form-validated"
                                novalidate="novalidate">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="contact-page__form-input-box">
                                            <input type="text" placeholder="Nombre" name="name">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="contact-page__form-input-box">
                                            <input type="text" placeholder="Apellido" name="name">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="contact-page__form-input-box">
                                            <input type="email" placeholder="Dirección de correo electrónico" name="email">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="contact-page__form-input-box">
                                            <input type="text" placeholder="Telefono" name="phone">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="contact-page__form-input-box text-message-box">
                                        <textarea name="message" placeholder="Escribir un mensaje..."></textarea>
                                    </div>
                                    <div class="contact-page__btn-box">
                                        <button type="submit" class="thm-btn contact-page__btn">Enviar un mensaje</button>
                                    </div>
                                </div>
                            </form>
                            <div class="result"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Contact Page End-->
     </x-layout>
