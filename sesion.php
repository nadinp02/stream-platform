<?php
require_once "Config/Autoload.php";
Config\Autoload::run();
$template = new Clases\TemplateSite();
$f = new Clases\PublicFunction();
$checkout = new Clases\Checkout();
$usuario = new Clases\Usuarios();

#Variables GET
$op = isset($_GET["op"]) ? $f->antihack_mysqli($_GET["op"]) : '';
$logout = isset($_GET["logout"]) ? true : false;
#Se carga la sesión del usuario
$usuarioSesion = $usuario->viewSession();

#Si no existe una sesión se redirige a usuarios
empty($usuarioSesion) ? $f->headerMove(URL . '/usuarios') : null;

#Si existe una sesión, pero es invitado, se sale de la cuenta y se redirige a usuarios
if ($usuarioSesion['invitado'] == 1) {
    $usuario->logout();
    $f->headerMove(URL . '/register');
}

#Si se encuentra la variable Get logout, se elimina el checkout y la sesión y se redirige a usuarios
if ($logout) {
    $checkout->destroy();
    $usuario->logout();
    $f->headerMove(URL . '/register');
}

#Se busca pedidos y cuenta en la URL para ponerle el atributo active al boton
$cuenta = $f->antihack_mysqli(strpos($_SERVER['REQUEST_URI'], "cuenta"));
$favoritos = $f->antihack_mysqli(strpos($_SERVER['REQUEST_URI'], "favoritos"));


#Información de cabecera
$template->set("title", $_SESSION["lang-txt"]["sesion"]["title"] . " | " . TITULO);
$template->themeInit();
?>
<div class="page-title-area pt-150 pb-55">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-titel-detalis  ">
                    <div class="section-title">
                        <h2><?= $_SESSION["lang-txt"]["usuarios"]["mi-cuenta"] ?> </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="content" class="site-content mt-50 mb-50" tabindex="-1">
    <div class="my-account-section section  pb-100 pb-lg-80 pb-md-70 pb-sm-60 pb-xs-50">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="gen-btn-container text-left">
                        <a href="<?= URL ?>" class="gen-button gen-button-flat">
                            <span class="text"><?= $_SESSION['lang-txt']['usuarios']['volver-inicio'] ?></span>
                        </a>
                    </div>
                    <div class="container" >
                        <a href="<?= URL ?>/sesion?logout" class="text-right text-uppercase fs-18 btn btn-sesion-closed d-block">
                            <span class="pb-10"><?= $_SESSION["lang-txt"]["sesion"]["salir"] ?></span>
                            <i class="fas blanco fa-sign-out-alt mt-10  pt-10 "></i>
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php $template->themeEnd(); ?>

<script>
    getData(
        type = "",
        div = "grid-favorites",
        products_page = false,
        filter = {
            favorite: true
        },
        start = 0,
        limit = 12,
        order = "id ASC"
    );
</script>