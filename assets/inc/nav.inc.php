<?php
$f = new Clases\PublicFunction();
$config = new Clases\Config();
$usuarios = new Clases\Usuarios();
$menu = new Clases\Menu();
$idiomas = new Clases\Idiomas();
$categorias = new Clases\Categorias();
$listLang = $idiomas->list(["habilitado = 1"], "", "");
#Se carga la sesión del usuario
$usuario = $usuarios->viewSession();
#Se carga la configuración de contacto
$contactData = $config->viewContact();
$socialData = $config->viewSocial();
$sesionActiva = isset($_SESSION['usuarios']['cod']) ? true :  false;
#Captcha
//Categorias de productos globales
$GLOBALS["productosCategorias"] = $categorias->listIfHave('productos', 'productos', $_SESSION["lang"]);
$productosCategorias = $GLOBALS["productosCategorias"];

$captchaData = $config->viewCaptcha();
#Si existe la sesión y no es invitado, entonces se habilita el botón de mi cuenta en la nav
$habilitado = (isset($usuario["invitado"]) && $usuario["invitado"] == 0) ?  true : false;
?>
<div class="g-recaptcha" data-sitekey="<?= $captchaData['data']['captcha_key'] ?>" data-callback="onSubmit" data-size="invisible"></div>



<header id="gen-header" class="gen-header-style-1 gen-has-sticky">
   <div class="gen-bottom-header">
      <div class="container">
         <div class="row">
            <div class="col-lg-12">
               <nav class="navbar navbar-expand-lg navbar-light">
                  <a class="navbar-brand" href="index.php">
                     <img class="img-fluid logo" src="<?= LOGO ?>" alt="streamlab-image">
                  </a>
                  <div class="collapse navbar-collapse" id="navbarSupportedContent">
                     <div id="gen-menu-contain" class="gen-menu-contain">
                        <?= $menu->build_nav("", "", "web") ?>
                     </div>
                  </div>
                  <div class="gen-header-info-box">
                     <div class="gen-menu-search-block">
                        <a href="javascript:void(0)" id="gen-seacrh-btn"><i class="fa fa-search"></i></a>
                        <div class="gen-search-form">
                           <form data-url="<?= URL ?>" action="<?= URL ?>/contenidos" method="GET">
                              <div class="form-input-item">
                                 <label for="search" class="sr-only"></label>

                                 <input class="search_input fs-14 pl-15  " type="text" required id="search-bar-nav" name="titulo" placeholder="Buscar...">

                              </div>

                           </form>
                        </div>
                        <a href="javascript:;" class="search-close"><i class="pe-7s-close"></i></a>
                     </div>
                     <div class="gen-account-holder">
                        <a href="<?= URL ?>/usuarios" id="gen-user-btn"><i class="fa fa-user"></i>
                           <span class="hidden-md-down"></span>
                        </a>
                        <div class="gen-account-menu">
                           <ul class="gen-account-menu">
                              <!-- Library Menu -->
                              <li>
                                 <a href="library.html">
                                    <i class="fa fa-indent"></i>
                                    Mis Favoritos </a>
                              </li>
                              <li>
                                 <a href="library.html"><i class="fa fa-list"></i>
                                    Mi Playlist </a>
                              </li>
                              <li>
                                 <a href="upload-video.html"> <i class="fa fa-upload"></i>
                                    Subir Video </a>
                              </li>
                           </ul>
                        </div>
                     </div>
                     
                     <?php if (isset($_SESSION['usuarios']['cod']) && $_SESSION['usuarios']['invitado'] == 0) { ?>
                     <?php } else { ?>
                        <div class="gen-btn-container">
                           <a href="<?= URL ?>/register" class="gen-button">
                              <div class="gen-button-block">
                                 <span class="gen-button-line-left"></span>
                                 <span class="gen-button-text"><?= $_SESSION["lang-txt"]["usuarios"]["registro"] ?></span>
                              </div>
                           </a>
                        </div>
                     <?php } ?>


                  </div>
                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                     <i class="fas fa-bars"></i>
                  </button>
               </nav>
            </div>
         </div>
      </div>
   </div>
</header>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function searchProductsNav(url, type) {
        event.preventDefault();

        window.location.href = url + "/productos/b/titulo/" + $("#title-nav-" + type).val().replaceAll('%', '');
    }
</script>

<script>
   $('#search-bar-nav').keypress(function(event) {
      var keycode = event.keyCode || event.which;
      if (keycode == '13') {
         document.location.href = "<?= URL ?>/c/novedades/b/titulo/" + $('#search-bar-nav').val();
      }
   });
</script>
