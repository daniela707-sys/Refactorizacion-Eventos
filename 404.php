<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>404 Error</title>
    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicons/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicons/favicon-16x16.png" />
    <link rel="manifest" href="assets/images/favicons/site.webmanifest" />
    <meta name="description" content="ogenix HTML 5 Template " />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css2?family=Teko:wght@300;400;500&family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/vendors/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/vendors/animate/animate.min.css" />
    <link rel="stylesheet" href="assets/vendors/animate/custom-animate.css" />
    <link rel="stylesheet" href="assets/vendors/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/vendors/jarallax/jarallax.css" />
    <link rel="stylesheet" href="assets/vendors/jquery-magnific-popup/jquery.magnific-popup.css" />
    <link rel="stylesheet" href="assets/vendors/nouislider/nouislider.min.css" />
    <link rel="stylesheet" href="assets/vendors/nouislider/nouislider.pips.css" />
    <link rel="stylesheet" href="assets/vendors/odometer/odometer.min.css" />
    <link rel="stylesheet" href="assets/vendors/swiper/swiper.min.css" />
    <link rel="stylesheet" href="assets/vendors/ogenix-icons/style.css">
    <link rel="stylesheet" href="assets/vendors/tiny-slider/tiny-slider.min.css" />
    <link rel="stylesheet" href="assets/vendors/reey-font/stylesheet.css" />
    <link rel="stylesheet" href="assets/vendors/owl-carousel/owl.carousel.min.css" />
    <link rel="stylesheet" href="assets/vendors/owl-carousel/owl.theme.default.min.css" />
    <link rel="stylesheet" href="assets/vendors/bxslider/jquery.bxslider.css" />
    <link rel="stylesheet" href="assets/vendors/bootstrap-select/css/bootstrap-select.min.css" />
    <link rel="stylesheet" href="assets/vendors/vegas/vegas.min.css" />
    <link rel="stylesheet" href="assets/vendors/jquery-ui/jquery-ui.css" />
    <link rel="stylesheet" href="assets/vendors/timepicker/timePicker.css" />
    <link rel="stylesheet" href="assets/vendors/nice-select/nice-select.css" />

    <!-- template styles -->
    <link rel="stylesheet" href="assets/css/ogenix.css" />
    <link rel="stylesheet" href="assets/css/ogenix-responsive.css" />
</head>

<body class="custom-cursor">

    <div class="preloader">
        <div class="preloader__image"></div>
    </div>
    <!-- /.preloader -->


    <div class="page-wrapper">
    <?php require_once "assets/layout/header.php"; ?>
        <section class="error-page">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="error-page__inner">
                            <div class="error-page__title-box">
                                <h2 class="error-page__title">404
                                </h2>
                                <div class="error-page__title-img-1">
                                    <!-- <img src="assets/images/resources/error-page-img-1.png" alt=""> -->
                                </div>
                                <div class="error-page__title-img-2">
                                    <!-- <img src="assets/images/resources/error-page-img-2.png" alt=""> -->
                                </div>
                            </div>
                            <h3 class="error-page__tagline">¡PÁGINA NO ENCONTRADA!</h3>
                            <p class="error-page__text">¡Lo sentimos, no podemos encontrar esa página! La página que estás buscando <br>
                            porque nunca existió.</p>
                            <form class="error-page__form">
                                <div class="error-page__form-input">
                                    <input type="search" placeholder="Search here">
                                    <button type="submit"><i class="icon-magnifying-glass"></i></button>
                                </div>
                            </form>
                            <a href="index.php" class="thm-btn error-page__btn">De vuelta a casa</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Error Page End-->

        <!--Subscribe One Start-->
        <section class="subscribe-one">
            <div class="container">
                <div class="subscribe-one__inner">
                    <div class="subscribe-one__inner-content">
                        <div class="subscribe-one__shape-3">
                            <img src="assets/images/shapes/subscribe-one-shape-3.png" alt="">
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="subscribe-one__left">
                                    <div class="icon">
                                        <span class="icon-folder"></span>
                                    </div>
                                    <div class="subscribe-one__title-box">
                                        <span class="subscribe-one__tagline">Quisque vel ortor</span>
                                        <h2 class="subscribe-one__title">Subscribe to newsletter</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="subscribe-one__right">
                                    <div class="subscribe-one__shape-6">
                                        <img src="assets/images/shapes/subscribe-one-shape-6.png" alt="">
                                    </div>
                                    <div class="subscribe-one__form-box">
                                        <form class="subscribe-one__form mc-form" data-url="MC_FORM_URL">
                                            <div class="subscribe-one__input-box">
                                                <input type="email" placeholder="Email Address" name="email">
                                                <button type="submit" class="subscribe-one__btn"><i
                                                        class="fas fa-paper-plane"></i></button>
                                            </div>
                                        </form>
                                        <div class="mc-form__response"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Subscribe One End-->

        <!--Site Footer Start-->
        <?php require_once "assets/layout/footer.html"; ?>


    </div><!-- /.page-wrapper -->


    

    <div class="search-popup">
        <div class="search-popup__overlay search-toggler"></div>
        <!-- /.search-popup__overlay -->
        <div class="search-popup__content">
            <form action="#">
                <label for="search" class="sr-only">search here</label><!-- /.sr-only -->
                <input type="text" id="search" placeholder="Search Here..." />
                <button type="submit" aria-label="search submit" class="thm-btn">
                    <i class="icon-magnifying-glass"></i>
                </button>
            </form>
        </div>
        <!-- /.search-popup__content -->
    </div>
    <!-- /.search-popup -->

    <a href="#" data-target="html" class="scroll-to-target scroll-to-top"><i class="icon-up-arrow"></i></a>


    <script src="assets/vendors/jquery/jquery-3.6.0.min.js"></script>
    <script src="assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendors/jarallax/jarallax.min.js"></script>
    <script src="assets/vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js"></script>
    <script src="assets/vendors/jquery-appear/jquery.appear.min.js"></script>
    <script src="assets/vendors/jquery-circle-progress/jquery.circle-progress.min.js"></script>
    <script src="assets/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="assets/vendors/jquery-validate/jquery.validate.min.js"></script>
    <script src="assets/vendors/nouislider/nouislider.min.js"></script>
    <script src="assets/vendors/odometer/odometer.min.js"></script>
    <script src="assets/vendors/swiper/swiper.min.js"></script>
    <script src="assets/vendors/tiny-slider/tiny-slider.min.js"></script>
    <script src="assets/vendors/wnumb/wNumb.min.js"></script>
    <script src="assets/vendors/wow/wow.js"></script>
    <script src="assets/vendors/isotope/isotope.js"></script>
    <script src="assets/vendors/countdown/jquery.countdown.min.js"></script>
    <script src="assets/vendors/owl-carousel/owl.carousel.min.js"></script>
    <script src="assets/vendors/bxslider/jquery.bxslider.min.js"></script>
    <script src="assets/vendors/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="assets/vendors/vegas/vegas.min.js"></script>
    <script src="assets/vendors/jquery-ui/jquery-ui.js"></script>
    <script src="assets/vendors/timepicker/timePicker.js"></script>
    <script src="assets/vendors/circleType/jquery.circleType.js"></script>
    <script src="assets/vendors/circleType/jquery.lettering.min.js"></script>
    <script src="assets/vendors/nice-select/jquery.nice-select.min.js"></script>




    <!-- template js -->
    <script src="assets/js/ogenix.js"></script>
</body>

</html>