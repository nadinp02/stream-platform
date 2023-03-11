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
        $f->headerMove(URL . '/register');
    }
}
#Se carga la configuración de email
$captchaData = $config->viewCaptcha();
#Información de cabecera
$template->set("title", 'Acceso de usuarios | ' . TITULO);
$template->set("description", "Accedé con tu cuenta o registráte para empezar a comprar en nuestra tienda online.");
$template->set("keywords", "acceso de usuarios, login, registrarse, usuarios");
$template->themeInit();

?>


<section class="position-relative pb-0">
    <div class="gen-register-page-background" style="background-image: url('images/background/asset-3.jpeg');">
    </div>
    <div id="content" class="site-content mt-50 mb-50" tabindex="-1">
        <div class="container">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <div id="r-error"></div>
                            <form id="register" data-carrito="<?= $carrito ?>" data-url="<?= URL ?>" data-type="usuarios" data-link="<?= $link ?>" data-captcha="<?= $captchaData["data"]["captcha_key"]  ?>">
                            <h4 class="title "><?= $_SESSION["lang-txt"]["usuarios"]["registro"] ?></h4>
                                <input name="captcha-response" type="hidden" value="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["nombre"] ?></label>
                                            <input class="form-control" name="firstname" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["apellido"] ?></label>
                                            <input class="form-control" name="lastname" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-20">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["email"] ?></label>
                                            <input class="form-control" name="email" value="" type="email" required>
                                        </div>
                                    </div>
                                    <div class="mt-20 col-md-6">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["password"] ?></label>
                                            <input class="form-control" name="r-password1" value="" type="password" required>
                                        </div>
                                    </div>
                                    <div class="mt-20 col-md-6">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["re_password"] ?></label>
                                            <input class="form-control"  name="r-password2" value="" type="password" required>
                                        </div>
                                    </div>

                                    <div class="mt-20 col-md-6">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["telefono"] ?></label>
                                            <input class="form-control" placeholder="<?= $_SESSION["lang-txt"]["usuarios"]["telefono"] ?>" name="phone" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="mt-20 col-md-6">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["celular"] ?></label>
                                            <input class="form-control"  name="mobilephone" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="mt-20 col-md-4">
                                        <div class="form-fild" style="width: 100%;">
                                        <label></label>
                                            <select id='provincia' data-url="<?= URL ?>" class="form-control" name="provincia" required>
                                                <option value="" selected><?= $_SESSION["lang-txt"]["usuarios"]["provincia"] ?></option>
                                                <?php $f->provincias(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-20 col-md-4">
                                        <div class="form-fild">
                                        <label></label>
                                            <select id='localidad' class="form-control" name="localidad" required>
                                                <option value="" selected><?= $_SESSION["lang-txt"]["usuarios"]["localidad"] ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-20 col-md-4">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["postal"] ?></label>
                                            <input class="form-control" name="postal" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="mt-20 col-md-4">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["calle"] ?></label>
                                            <input class="form-control"  name="calle" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="mt-20 col-md-4">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["numero"] ?></label>
                                            <input class="form-control"  name="numero" value="" type="number" required>
                                        </div>
                                    </div>
                                    <div class="mt-20 col-md-4">
                                        <div class="form-fild">
                                        <label><?= $_SESSION["lang-txt"]["usuarios"]["piso"] ?></label>
                                            <input class="form-control" name="piso" value="" type="text">
                                        </div>
                                    </div>

                                </div>
                                <div id="btn-r" class="register-submit mt-10 mb-10">
                                    <button class="g-recaptcha btn btn-secondary btn-lg" type="submit" data-sitekey="<?= $captchaData["data"]["captcha_key"] ?>" data-callback='registerUser'><?= $_SESSION["lang-txt"]["usuarios"]["registro"] ?></button>
                                </div>
                            </form>
                            <br />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


    <!--Login Register section end-->
<?php $template->themeEnd(); ?>