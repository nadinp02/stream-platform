<?php
$config = new Clases\Config();

#Se carga la configuración de marketing
$marketing = $config->viewMarketing();

#Se carga la configuración del header y se la muestra
$configHeader = $config->viewConfigHeader();
$captchaData = $config->viewCaptcha();

#Script One Signal
if (!empty($marketing['data']['onesignal'])) { ?>
    <link rel="manifest" href="/manifest.json" />
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
        var OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "<?= $marketing["data"]["onesignal"] ?>",
            });
        });
    </script>
<?php }

#Script Google Analytics
if (!empty($marketing['data']['google_analytics'])) { ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-150839106-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', '<?= $marketing["data"]["google_analytics"] ?>');
    </script>
<?php }
?>

<link rel="shortcut icon" href="<?= FAVICON ?>">
<link rel="stylesheet" href="<?= URL ?>/assets/theme/css/bootstrap.min.css" />
<link rel="stylesheet" href="<?= URL ?>/assets/theme/css/style.css" />
<link rel="stylesheet" href="<?= URL ?>/assets/theme/css/responsive.css" />



<!-- Styles CMS -->
<link rel="stylesheet" href="<?= URL ?>/assets/css/main-rocha.css">
<link rel="stylesheet" href="<?= URL ?>/assets/css/lightbox.css">
<link rel="stylesheet" href="<?= URL ?>/assets/css/estilos-rocha.css">
<link rel="stylesheet" href="<?= URL ?>/assets/css/select2.min.css">
<link rel="stylesheet" href="<?= URL ?>/assets/css/loading.css">
<link rel="stylesheet" href="<?= URL ?>/assets/css/auto-complete.css">
<link rel="stylesheet" type="text/css" href="<?= URL ?>/assets/css/toastr.min.css">
<link href="<?= URL ?>/assets/css/progress-wizard.min.css" rel="stylesheet">
<!-- Fin Styles CMS -->

<link rel="stylesheet" href="<?= URL ?>/assets/css/custom-rocha.css">



<script src="https://www.google.com/recaptcha/api.js?render=<?= $captchaData['data']['captcha_key'] ?>"></script>
<script>
    window.captchaKey = '<?= $captchaData['data']['captcha_key'] ?>';
</script>