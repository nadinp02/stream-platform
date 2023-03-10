<?php
require_once dirname(__DIR__, 2) . "/Config/Autoload.php";
Config\Autoload::run();
$f = new Clases\PublicFunction();
$usuarios = new Clases\Usuarios();
$pedidos_envios = new Clases\EnviosPedidos();
$pedidos_pagos = new Clases\PagosPedidos();

$get = (isset($_GET) && !empty($_GET)) ? $f->antihackMulti($_GET) : '';
$post = (isset($_POST) && !empty($_POST)) ? $f->antihackMulti($_POST) : '';


if (isset($get['id']) && $get['id'] == 'new') {
    $_SESSION["usuarios"] = $usuarios->viewSession();
    $post["usuario"] = $_SESSION["usuarios"]["cod"];
    //CREAR PEDIDOS ENVIOS
    if (isset($get['type']) && $get['type'] == 'envios_pedidos') $pedidos_envios->add($post);
    if (isset($get['type']) && $get['type'] == 'pagos_pedidos') $pedidos_pagos->add($post);
}

// if (isset($get['id']) && $get['id'] != 'new') {
//     $_SESSION["usuarios"] = $usuarios->viewSession();
//     //CREAR PEDIDOS ENVIOS
//     if (isset($get['type']) && $get['type'] == 'envios_pedidos') $pedidos_envios->edit($post, ["id = '" . $get['id'] . "'", "usuario = '" . $_SESSION["usuarios"]["cod"] . "'"]);
//     if (isset($get['type']) && $get['type'] == 'pagos_pedidos') $pedidos_pagos->edit($post, ["id = '" . $get['id'] . "'", "usuario = '" . $_SESSION["usuarios"]["cod"] . "'"]);
// }
