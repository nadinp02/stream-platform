<?php
$config = new Clases\Config();
#Se carga la configuración de email
$contactData = $config->viewContact();
?>


<!-- <?php if ($contactData["data"]["whatsapp"]) { ?>
  <a target="_blank" href="https://wa.me/<?= $contactData["data"]["whatsapp"] ?>" class="whatsapp"><i class="fab fa-whatsapp"></i></a>
<?php } ?>
<a target="_blank" href="https://m.me/<?= $contactData["data"]["messenger"] ?>" class="messenger"><i class="fab fa-facebook"></i></a> -->


<footer id="gen-footer">
   <div class="gen-footer-style-1">
      <div class="gen-copyright-footer">
         <div class="container">
            <div class="row">
               <div class="col-md-12 align-self-center">

                  <span class="gen-copyright"><a target="_blank" href="<?= URL ?>">&copy; Copyright <?= date("Y") ?>. Todos los derechos reservados <?= TITULO ?></a></span>
               </div>
            </div>
         </div>
      </div>
   </div>
</footer>
<div id="back-to-top">
   <a class="top" id="top" href="#top"> <i class="ion-ios-arrow-up"></i> </a>
</div>

<!-- Scripts Template -->
<script src="<?= URL ?>/assets/theme/js/jquery-3.6.0.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/asyncloader.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/bootstrap.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/owl.carousel.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/jquery.waypoints.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/jquery.counterup.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/popper.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/swiper-bundle.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/isotope.pkgd.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/jquery.magnific-popup.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/slick.min.js"></script>
<script src="<?= URL ?>/assets/theme/js/streamlab-core.js"></script>
<script src="<?= URL ?>/assets/theme/js/script.js"></script>
<!-- Fin Scripts Template -->


<!-- Scripts CMS -->
<script src="<?= URL ?>/assets/js/services/lang.js"></script>
<script src="<?= URL ?>/assets/js/lightbox.js"></script>
<script src="<?= URL ?>/assets/js/jquery-ui.min.js"></script>
<script src="<?= URL ?>/assets/js/select2.min.js"></script>
<script src="<?= URL ?>/assets/js/bootstrap-notify.min.js"></script>
<script src="<?= URL ?>/assets/js/toastr.min.js"></script>
<script src="<?= URL ?>/assets/js/services/services.js"></script>
<script src="<?= URL ?>/assets/js/services/email.js"></script>
<script src="<?= URL ?>/assets/js/services/search.js"></script>
<script src="<?= URL ?>/assets/js/services/products.js"></script>
<script src="<?= URL ?>/assets/js/services/user.js"></script>
<script src="<?= URL ?>/assets/js/services/cart.js"></script>
<script src="<?= URL ?>/assets/js/sticky/sticky-sidebar.min.js"></script>
<!-- Fin Scripts CMS -->

<script>
   $(document).ready(function() {
      refreshCart($('body').attr('data-url'));
      viewCart($('body').attr('data-url'));
   });
</script>