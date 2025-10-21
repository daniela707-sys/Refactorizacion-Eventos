<!DOCTYPE html>
<html style="min-width: 300px;" lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title> Red Emprender </title>
  <!-- favicons Icons -->
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/logo/logo1.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logo/logo1.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/logo/logo1.png" />
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
  <link rel="stylesheet" href="assets/vendors/ogenix-icons/style.css" />
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
  <link rel="stylesheet" href="assets/css/index.css">
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/ogenix.css">
  <link rel="stylesheet" href="assets/css/ogenix-responsive.css">


  <!-- Incluye los scripts de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</head>

<body class="custom-cursor">

  <div class="preloader">
    <div class="preloader__image"></div>
  </div>
  <!-- /.preloader -->
  <script>
    // Ocultar preloader después de 3 segundos máximo
    setTimeout(function() {
      const preloader = document.querySelector('.preloader');
      if (preloader) {
        preloader.style.display = 'none';
      }
    }, 3000);
  </script>
  <?php
  $username = 'front@gmail.com'; // Reemplaza con tu nombre de usuario
  $password = 'aprendiz23'; // Reemplaza con tu contraseña

  // Codificar el usuario y contraseña en Base64
  $auth = base64_encode("$username:$password");
  ?>
  <div>
    <!--Subscribe modal-->
    <?php require_once "assets/layout/offerofday.php"; ?>
    <!--Subscribe header-->
    <?php require_once "assets/layout/header.php"; ?>
    <!--Subscribe contenido-->
    <?php require_once "assets/components/evento_handler.php"; ?>
    <!--Subscribe One Start-->
    <?php require_once "assets/layout/subscribe.php"; ?>
    <!--Subscribe One End-->
    <?php require_once "assets/layout/footer.php"; ?>
  </div>




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
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://unpkg.com/tippy.js@6"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>




  <!-- template js -->
  <script src="assets/js/ogenix.js"></script>
</body>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('load', function() {
      document.querySelector('.preloader').classList.add('hide');
      document.querySelector('.page-wrapper').classList.add('loaded');
    });

    setTimeout(function() {
      document.querySelector('.preloader').classList.add('hide');
      document.querySelector('.page-wrapper').classList.add('loaded');
    }, 3000);
  });
</script>

</html>