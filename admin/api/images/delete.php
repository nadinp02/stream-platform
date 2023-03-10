<?php
require_once dirname(__DIR__, 3) . "/Config/Autoload.php";
Config\Autoload::run();
$f = new Clases\PublicFunction();

if (isset($_POST["url"])) {
    $url = dirname(__DIR__, 3) . str_replace(URL, "", $_POST["url"]);
    $urlThumb = str_replace('.' . $_ENV['TYPE_IMG'], '_thumb.' . $_ENV['TYPE_IMG'], $url);
    if (unlink($url)) {
        @unlink($urlThumb);
        $response = ['status' => true, 'message' => 'Imagen Eliminada'];
    } else {
        $response = ['status' => false, 'message' => 'Ocurrio un error'];
    }
    echo json_encode($response);
    die();
}

if (isset($_POST["remove-all"])) {
    $r = false;
    foreach ($_POST["img"] as $img) {
        $url = dirname(__DIR__, 3) . str_replace([URL, "'"], "", $img);
        $urlThumb = str_replace('.' . $_ENV['TYPE_IMG'], '_thumb.' . $_ENV['TYPE_IMG'], $url);
        if (unlink($url)) {
            @unlink($urlThumb);
            $r = true;
        }
    }
    if ($r) echo "<script>window.close();</script>";
}

if (isset($_GET["url"])) {
    $url = dirname(__DIR__, 3) . str_replace(URL, "", $_GET["url"]);
    $urlThumb = str_replace('.' . $_ENV['TYPE_IMG'], '_thumb.' . $_ENV['TYPE_IMG'], $url);
    if (unlink($url)) {
        @unlink($urlThumb);
        echo "<script>window.close();</script>";
    }
}
