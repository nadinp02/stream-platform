<?php
require_once dirname(__DIR__, 2) . "/Config/Autoload.php";
Config\Autoload::run();
$f = new Clases\PublicFunction();
$carrito = new Clases\Carrito();
$checkout = new Clases\Checkout();

if (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["saltar_checkout"] == 'all') {
    echo json_encode(["status" => 'disabled']);
    die();
}

if (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["mostrar_precios"] == 0 || (!empty(floatval($carrito->totalPrice())) && ($carrito->checkPaymentsLimits()))) {
    $type = !empty($_SESSION['usuarios']) ? "USER" : "GUEST";
    $cod_user = ($type == "USER") ?  $_SESSION['usuarios']['cod'] : "";
    $link = ($type == "USER") ? URL . "/" . $checkout->checkSkip() : URL . "/login";
    $minimo = false;
    $exceededMinimum = true;
} else {
    $link = URL . "/carrito";
    $minimo = $carrito->checkMiniumLimits()["data"]["minimo"];
    $exceededMinimum = false;
}
echo json_encode(["status" => $exceededMinimum, "link" => $link, "minimo" => $minimo]);
