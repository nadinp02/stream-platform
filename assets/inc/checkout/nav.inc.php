<?php
$config = new Clases\Config();
$captchaData = $config->viewCaptcha();
?>
<div class="g-recaptcha" data-sitekey="<?= $captchaData['data']['captcha_key'] ?>" data-callback="onSubmit" data-size="invisible"></div>
<div class="skip-loader">
    <div style="display: flex;align-items: center;justify-content: center;top:0;left:0;width:100%;height:100vh;background:rgba(255,255,255,.5)">
        <div style="text-align: center;">
            <img style="margin-bottom: 20px;" src=" <?= LOGO ?>" width="300px" alt="">
            <div style="margin-bottom: 10px;"><?= $_SESSION["lang-txt"]["checkout"]["generando_pedido"] ?></div>
            <img style="filter: invert(48%) sepia(64%) saturate(5959%) hue-rotate(11deg) brightness(108%) contrast(100%)" src="<?= URL ?>/assets/images/loader-skip-checkout.svg" width="50px" alt="">
        </div>
    </div>
</div>
<div class="row hidden-md-up text-center">
    <div class="col-12">
        <img class=" mt-20" width="50%" src="<?= LOGO ?>">
    </div>
    <?php
    if ($_SESSION['cod_pedido'] && CANONICAL != URL . "/login") {
    ?>
        <div class="col-12  ">
            <h3 class="fs-20 mt-20"><span><?= $_SESSION["lang-txt"]["checkout"]["navbar"]["pedido"] ?></span> Nº<?= $_SESSION["cod_pedido"] ?></h3>
        </div>
    <?php  } ?>
</div>


<div class="container-fluid hidden-md-down">
    <img class="hidden-sm-down mt-20" width="20%" style="max-width:170px" src="<?= LOGO ?>">

    <?php
    if ($_SESSION['cod_pedido'] && CANONICAL != URL . "/login") {
    ?>
        <h3 class="pull-right fs-20 mt-24"><span><?= $_SESSION["lang-txt"]["checkout"]["navbar"]["pedido"] ?></span> Nº<?= $_SESSION["cod_pedido"] ?></h3>
    <?php  } ?>
</div>
<hr>