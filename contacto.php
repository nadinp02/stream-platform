<?php
require_once "config/Autoload.php";
Config\Autoload::run();

$template = new Clases\TemplateSite();
$f = new Clases\PublicFunction();
$config = new Clases\Config();
$contenidos = new Clases\Contenidos();
$enviar = new Clases\Email();
$emailData = $config->viewEmail();

$captchaData = $config->viewCaptcha();
$contactData = $config->viewContact();
#Se carga la configuración de email
$data = [
    "filter" => ['contenidos.area = "contacto"'],
];
$contenidoContacto = $contenidos->list($data, $_SESSION['lang']);
#Información de cabecera
$template->set("title", "Contacto | " . TITULO);
$template->set("description", "Envianos tus dudas y nosotros te asesoramos");
$template->set("keywords", "");
$template->themeInit();
?>
<div class="gen-breadcrumb" style="background-image: url('<?= URL ?>/assets/theme/images/background/asset-25.jpeg');">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb">
                    <div class="gen-breadcrumb-title">
                        <h1>
                            <?= $_SESSION["lang-txt"]["general"]["contacto"] ?>
                        </h1>
                    </div>
                    <div class="gen-breadcrumb-container">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL ?>"><i class="fas fa-home mr-2"></i><?= $_SESSION["lang-txt"]["general"]["inicio"] ?></a></li>
                            <li class="breadcrumb-item active"><?= $_SESSION["lang-txt"]["general"]["contacto"] ?></li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="gen-section-padding-3">
    <div class="container container-2">
        <div class="row">
            <div class="col-xl-4 col-md-6">
                <div class="gen-icon-box-style-1">
                    <div class="gen-icon-box-icon">
                        <span class="gen-icon-animation">
                            <i class="fas fa-map-marker-alt"></i></span>
                    </div>
                    <div class="gen-icon-box-content">
                        <h3 class="pt-icon-box-title mb-2">
                            <span><?= $_SESSION['lang-txt']['contacto']['direccion'] ?></span>
                        </h3>
                        <p class="gen-icon-box-description"><?= $contactData['data']['domicilio'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mt-4 mt-md-0">
                <div class="gen-icon-box-style-1">
                    <div class="gen-icon-box-icon">
                        <span class="gen-icon-animation">
                            <i class="fas fa-phone-alt"></i></span>
                    </div>
                    <div class="gen-icon-box-content">
                        <h3 class="pt-icon-box-title mb-2">
                            <span><?= $_SESSION['lang-txt']['contacto']['telefono'] ?></span>
                        </h3>
                        <p class="gen-icon-box-description"><?= $contactData['data']['whatsapp'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-12 mt-4 mt-xl-0">
                <div class="gen-icon-box-style-1">
                    <div class="gen-icon-box-icon">
                        <span class="gen-icon-animation">
                            <i class="far fa-envelope"></i></span>
                    </div>
                    <div class="gen-icon-box-content">
                        <h3 class="pt-icon-box-title mb-2">
                            <span><?= $_SESSION['lang-txt']['contacto']['email'] ?></span>
                        </h3>
                        <p class="gen-icon-box-description"><?= $contactData['data']['email'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<Section class="gen-section-padding-3 gen-top-border">
    <div class="container container-2">
        <?php
        if (isset($_POST['submit'])) {

            $nombre = $_POST["nombre"];
            $apellido = $_POST["apellido"];
            $email = $_POST["email"];
            $telefono = $_POST["telefono"];
            $mensaje = $_POST["mensaje"];
            $asunto = "Comentario";
            if (!empty($nombre) && !empty($apellido) && !empty($telefono)   && !empty($email) && !empty($mensaje)) {
                // isset($_POST) ? var_dump($_POST) : "";
                //MENSAJE A USUARIO
                $mensajeFinal = "<b>Gracias por realizar tu consulta, te contactaremos a la brevedad.</b><br/>";
                $mensajeFinal .= "<b>Consulta</b>: " . $mensaje . "<br/>";

                $enviar->set("asunto", "Realizaste tu consulta.");
                $enviar->set("receptor", $email);
                $enviar->set("emisor", $emailData['data']['remitente']);
                $enviar->set("mensaje", $mensajeFinal);
                $enviar->emailEnviar();

                //MENSAJE AL ADMIN
                $mensajeFinalAdmin = "<b>Nueva consulta desde la web.</b><br/>";
                $mensajeFinalAdmin .= "<b>Nombre</b>: " . $nombre . " <br/>";
                $mensajeFinalAdmin .= "<b>Apellido</b>: " . $apellido . " <br/>";
                $mensajeFinalAdmin .= "<b>Email</b>: " . $email . "<br/>";
                $mensajeFinalAdmin .= "<b>Telefono</b>: " . $telefono . "<br/>";
                $mensajeFinalAdmin .= "<b>Mensaje</b>: " . $mensaje . "<br/>";

                $enviar->set("asunto", "Nueva consulta desde la web :" . $asunto);
                $enviar->set("receptor", $emailData['data']['remitente']);
                $enviar->set("mensaje", $mensajeFinalAdmin);
                $enviar->emailEnviar();
                //mensaje de success
                echo "<div class='alert alert-success'><p>El email se ha enviado con exito!</p></div>";
            } else {
                //echo error
                echo "<div class='alert alert-danger'>Hubo un error al enviar el email, intente nuevamente.</div>";
            }
        }
        ?>

        <div class="row">
            <div class="col-xl-6">
                <h2 class="mb-5"><?= $_SESSION['lang-txt']['contacto']['contactanos'] ?></h2>
                <form class="contact-form-style" method="post" role="form">
                    <div class="row gt-form">
                        <div class="col-md-6 mb-4" >
                            <div class="contact-input">
                                <input name="nombre" id="name" class="form-control" placeholder="<?= $_SESSION['lang-txt']['usuarios']['nombre'] ?>*" type="text" required />
                            </div>
                        </div>
                        <div class="col-md-6 mb-4" >
                            <div class="contact-input">
                                <input name="apellido" class="form-control" id="apellido" placeholder="<?= $_SESSION['lang-txt']['usuarios']['apellido'] ?>*" type="text" required />
                            </div>
                        </div>
                        <div class="col-lg-6" >
                            <div class="contact-input">
                                <input name="email" class="form-control" id="email" placeholder="<?= $_SESSION['lang-txt']['usuarios']['email'] ?>*" type="email" required />
                            </div>
                        </div>
                        <div class="col-md-6 mb-4" >
                            <div class="contact-input">
                                <input id="telefono" class="form-control" name="telefono" placeholder="<?= $_SESSION['lang-txt']['usuarios']['telefono'] ?>" type="text" required />
                            </div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="contact-input">
                                <textarea name="mensaje" style="border-radius: 5px;" id="mensaje" placeholder="<?= $_SESSION['lang-txt']['usuarios']['mensaje'] ?>*" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="form-group mb-0">
                                <button type="submit" name="submit" class="theme-btn w-100 btn-contact"><?= $_SESSION['lang-txt']['contacto']['enviar'] ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-xl-6">
                <div style="width: 100%"><?= $contenidoContacto['mapa']['data']['contenido'] ?></div>
            </div>
        </div>
    </div>
</Section>


<?php $template->themeEnd() ?>