<?php
require_once "config/Autoload.php";
Config\Autoload::run();

$template = new Clases\TemplateSite();
$f = new Clases\PublicFunction();
$config = new Clases\Config();
$usuario = new Clases\Usuarios();

#Se carga la sesión del usuario
$userData = $usuario->viewSession();

$link = (isset($_GET["link"])) ? $f->antihack_mysqli($_GET["link"]) : '';
$carrito = (isset($_GET["carrito"])) ? $f->antihack_mysqli($_GET["carrito"]) : 0;
#Comprueba si existe la sesión
if (!empty($userData)) {
    #Si existe y es un usuario registrado, lo redirige a su panel
    if ($userData["invitado"] == 0) {
        $f->headerMove(URL . '/sesion');
    }
    #Si existe y es un usuario invitado, lo redirige a usuarios para loguearse o registrarse
    if ($userData["invitado"] == 1) {
        $usuario->logout();
        $f->headerMove(URL . '/usuarios');
    }
}
#Se carga la configuración de email
$captchaData = $config->viewCaptcha();
#Información de cabecera
$template->set("title", 'Acceso de usuarios | ' . TITULO);
$template->set("description", "¿Quieres ver StreamLab ya? Ingresa tu email para crear una cuenta o ...");
$template->set("keywords", "acceso de usuarios, login, registrarse, usuarios");
$template->themeInit();

?>


<section class="position-relative pb-0">
    <div class="gen-register-page-background">
    </div>
    <div id="content" class="site-content mt-50 mb-50" tabindex="-1">
        <div class="container">
            <div class="container">
                <div class="row">
                   <div class="col-lg-12">
                        <div class="text-center">
                            <div id="l-error"></div>
                            <form id="login" data-carrito="<?= $carrito ?>" data-url="<?= URL ?>" data-link="<?= $link ?>" data-type="usuarios" data-captcha="<?= $captchaData["data"]["captcha_key"]  ?>">
                                <h4><?= $_SESSION["lang-txt"]["usuarios"]["ingresar"] ?></h4>
                                <input name="link" type="hidden" value="">
                                <input name="captcha-response" type="hidden" value="">
                                <input class="form-control" type="hidden" name="stg-l" value="1">
                                <div class="form-fild">
                                    <label><?= $_SESSION["lang-txt"]["usuarios"]["email"] ?></label>
                                    <input class="form-control" name="l-user" value="" type="email" required>
                                </div>
                                <div class="form-fild mt-30">
                                    <label><?= $_SESSION["lang-txt"]["usuarios"]["password"] ?></label>
                                    <input class="form-control" name="l-pass" id="l-pass" value="" type="password" required>
                                </div>
                                <p><label><input name="rememberme" type="checkbox" id="rememberme" value="forever">Recordar</label></p>
                                <div id="btn-l" class="login-submit mb-10">
                                    <button class="btn btn-secondary btn-lg g-recaptcha" data-sitekey="<?= $captchaData["data"]["captcha_key"] ?>" data-callback='loginUser' type="submit"><?= $_SESSION["lang-txt"]["login"]["ingresar"] ?></button>
                                </div>
                                <div class="lost-password">
                                    <a href="<?= URL ?>/recuperar"><?= $_SESSION["lang-txt"]["usuarios"]["olvidaste_password"] ?></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


    <!--Login Register section end-->
<?php $template->themeEnd(); ?>