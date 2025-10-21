<!DOCTYPE html>
<html style="min-width: 300px;" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> Red Emprender </title>
    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../images/logo/logo1.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../images/logo/logo1.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="../images/logo/logo1.png" />
    <link rel="manifest" href="../images/favicons/site.webmanifest" />
    <meta name="description" content="ogenix HTML 5 Template " />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@300;400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../vendors/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../vendors/animate/animate.min.css" />
    <link rel="stylesheet" href="../vendors/animate/custom-animate.css" />
    <link rel="stylesheet" href="../vendors/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="../vendors/jarallax/jarallax.css" />
    <link rel="stylesheet" href="../vendors/jquery-magnific-popup/jquery.magnific-popup.css" />
    <link rel="stylesheet" href="../vendors/nouislider/nouislider.min.css" />
    <link rel="stylesheet" href="../vendors/nouislider/nouislider.pips.css" />
    <link rel="stylesheet" href="../vendors/odometer/odometer.min.css" />
    <link rel="stylesheet" href="../vendors/swiper/swiper.min.css" />
    <link rel="stylesheet" href="../vendors/ogenix-icons/style.css" />
    <link rel="stylesheet" href="../vendors/tiny-slider/tiny-slider.min.css" />
    <link rel="stylesheet" href="../vendors/reey-font/stylesheet.css" />
    <link rel="stylesheet" href="../vendors/owl-carousel/owl.carousel.min.css" />
    <link rel="stylesheet" href="../vendors/owl-carousel/owl.theme.default.min.css" />
    <link rel="stylesheet" href="../vendors/bxslider/jquery.bxslider.css" />
    <link rel="stylesheet" href="../vendors/bootstrap-select/css/bootstrap-select.min.css" />
    <link rel="stylesheet" href="../vendors/vegas/vegas.min.css" />
    <link rel="stylesheet" href="../vendors/jquery-ui/jquery-ui.css" />
    <link rel="stylesheet" href="../vendors/timepicker/timePicker.css" />
    <link rel="stylesheet" href="../vendors/nice-select/nice-select.css" />
    <link rel="stylesheet" href="../css/details_evento.css">
    <link rel="stylesheet" href="../css/app.css">
    <link rel="stylesheet" href="../css/ogenix.css">
    <link rel="stylesheet" href="../css/ogenix-responsive.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body class="custom-cursor">
    <div class="preloader">
        <div class="preloader__image"></div>
    </div>
    
<?php
require_once("../../include/dbcommon.php");

$id_evento = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id_evento) {
    header('Location: ../../../index.php');
    exit;
}

$auth = isset($_COOKIE['runnerSession']) ? true : false;
?>

    <div>
        <?php require_once "../layout/offerofday.php"; ?>
        <?php require_once "../layout/header.php"; ?>
        
        <div class="banner" id="banner">
            <div class="banner-loading" id="banner-loading">
                <div class="spinner"></div>
            </div>
            <div class="banner-content" id="banner-content" style="display: none;">
                <h1 id="evento-titulo-banner">¡Bienvenido!</h1>
                <p id="evento-descripcion-banner">Descubre lo mejor para ti</p>
            </div>
            <div class="dots" id="dots"></div>
        </div>

        <div class="content-container">
            <div class="main-content" id="main-content">
                <div class="loading-indicator">Cargando detalles del evento...</div>
            </div>
        </div>
        
        <?php require_once "../layout/subscribe.php"; ?>
        <?php require_once "../layout/footer.php"; ?>
    </div>

    <script src='../js/eventos/detalles_evento.js'></script>
    <script src="../vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendors/jarallax/jarallax.min.js"></script>
    <script src="../vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js"></script>
    <script src="../vendors/jquery-appear/jquery.appear.min.js"></script>
    <script src="../vendors/jquery-circle-progress/jquery.circle-progress.min.js"></script>
    <script src="../vendors/jquery-magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="../vendors/jquery-validate/jquery.validate.min.js"></script>
    <script src="../vendors/nouislider/nouislider.min.js"></script>
    <script src="../vendors/odometer/odometer.min.js"></script>
    <script src="../vendors/swiper/swiper.min.js"></script>
    <script src="../vendors/tiny-slider/tiny-slider.min.js"></script>
    <script src="../vendors/wnumb/wNumb.min.js"></script>
    <script src="../vendors/wow/wow.js"></script>
    <script src="../vendors/isotope/isotope.js"></script>
    <script src="../vendors/countdown/jquery.countdown.min.js"></script>
    <script src="../vendors/owl-carousel/owl.carousel.min.js"></script>
    <script src="../vendors/bxslider/jquery.bxslider.min.js"></script>
    <script src="../vendors/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="../vendors/vegas/vegas.min.js"></script>
    <script src="../vendors/jquery-ui/jquery-ui.js"></script>
    <script src="../vendors/timepicker/timePicker.js"></script>
    <script src="../vendors/circleType/jquery.circleType.js"></script>
    <script src="../vendors/circleType/jquery.lettering.min.js"></script>
    <script src="../vendors/nice-select/jquery.nice-select.min.js"></script>
    <script src="../js/ogenix.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
      window.addEventListener('load', function() {
        const preloader = document.querySelector('.preloader');
        if (preloader) {
          preloader.style.display = 'none';
        }
      });
      
      setTimeout(function() {
        const preloader = document.querySelector('.preloader');
        if (preloader) {
          preloader.style.display = 'none';
        }
      }, 2000);
    });
</script>
</body>
</html>