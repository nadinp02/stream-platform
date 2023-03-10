<?php
$f = new Clases\PublicFunction();
$detallePedidos = new Clases\DetallePedidos();
$pedidos_pagos = new Clases\PagosPedidos();
$pedidos_envios = new Clases\EnviosPedidos();
$envios = new Clases\Envios();
$pagos = new Clases\Pagos();
$pedidos = new Clases\Pedidos();
$carrito = new Clases\Carrito();
$checkout = new Clases\Checkout();

$data = [
    "envio" => $_SESSION["perfil-ecommerce"]['data']['metodo_envio'],
    "pago" =>  $_SESSION["perfil-ecommerce"]['data']['metodo_pago'],
    "usuario" => $_SESSION["usuarios"]["cod"],
    "nombre" => isset($_SESSION["usuarios"]["nombre"]) ? $_SESSION["usuarios"]["nombre"] : 'Sin datos',
    "apellido" => isset($_SESSION["usuarios"]["apellido"]) ? $_SESSION["usuarios"]["apellido"] : 'Sin datos',
    "documento" => !empty($_SESSION["usuarios"]["doc"]) ? $_SESSION["usuarios"]["doc"] : 'Sin datos',
    "email" => isset($_SESSION["usuarios"]["email"]) ? $_SESSION["usuarios"]["email"] : "Sin datos",
    "celular" => isset($_SESSION["usuarios"]["celular"]) ? $_SESSION["usuarios"]["celular"] : "Sin datos",
    "telefono" => isset($_SESSION["usuarios"]["telefono"]) ? $_SESSION["usuarios"]["telefono"] : "Sin datos",
    "provincia" => isset($_SESSION["usuarios"]["provincia"]) ? $_SESSION["usuarios"]["provincia"] : "Sin datos",
    "localidad" => isset($_SESSION["usuarios"]["localidad"]) ? $_SESSION["usuarios"]["localidad"] : "Sin datos",
    "postal" => isset($_SESSION["usuarios"]["postal"]) ? $_SESSION["usuarios"]["postal"] : 0,
    "calle" => isset($_SESSION["usuarios"]["calle"]) ? $_SESSION["usuarios"]["calle"] : "Sin datos",
    "numero" => isset($_SESSION["usuarios"]["numero"]) ? $_SESSION["usuarios"]["numero"] : 0,
    "piso" => isset($_SESSION["usuarios"]["piso"]) ? $_SESSION["usuarios"]["piso"] : 0,
    "hora" => isset($_SESSION["usuarios"]["hora"]) ? $_SESSION["usuarios"]["hora"] : "Sin datos",
    "similar" => 0,
    "facturar" => false
];

//CREAR PEDIDOS ENVIOS
$id_detalle_envio = $pedidos_envios->add($data);
$data["detalle_envio"] = $id_detalle_envio;
if ($id_detalle_envio) {
    $cod_pedido = $pedidos->generarCodPedido();
    $_SESSION["cod_pedido"] = $cod_pedido;
    $_SESSION['stages']['cod'] = $cod_pedido;

    //SELECCIONAR METODO DE ENVIO
    $envios->set("cod", $data['envio']);
    $envios->set("idioma", $_SESSION['lang']);
    $envioData = $envios->view();
    if ($envioData['data']) {
        $carrito->addShipping($envioData);
        $_SESSION['stages']['user_cod'] = $_SESSION["usuarios"]["cod"];
        $_SESSION["stages"]['stage-1'] = $data;


        //CREAR PEDIDOS PAGOS
        $id_detalle_pago = $pedidos_pagos->add($data);
        $data["detalle_pago"] = $id_detalle_pago;
        if ($id_detalle_pago) {
            $carrito->addBill($data['facturar']);
            $_SESSION["stages"]['stage-2'] = $data;

            //SELECCIONAR METODO DE PAGO
            $pagos->set("cod", $data['pago']);
            $pagos->set("idioma", $_SESSION['lang']);
            $pagoData = $pagos->view();
            if (!empty($pagoData['data'])) {
                $carrito->checkKeyOnCart("Metodo-Pago");
                $carrito->changePriceByPayment($pagoData);
                $_SESSION["stages"]['stage-3'] = $pagoData['data'];

                // CREAR PEDIDO
                $carro = $carrito->return();
                $precio = $carrito->totalPrice();
                $data = [
                    "cod" => $cod_pedido,
                    "entrega" => $precio,
                    "total" => $precio,
                    "estado" => $pagoData['data']['defecto'],
                    "usuario" => $data["usuario"],
                    "idioma" => $_SESSION['lang'],
                    "detalle_envio" => $id_detalle_envio,
                    "envio" => $envioData['data']['cod'],
                    "envio_titulo" => $envioData['data']["titulo"],
                    "detalle_pago" => $id_detalle_pago,
                    "pago" => $pagoData['data']['cod'],
                    "pago_titulo" => $pagoData['data']["titulo"]
                ];

                if ($pedidos->add($data)) {
                    // CREAR DETALLE DE PEDIDO
                    $detallePedidos->addCarrito($carro, $cod_pedido);
                    $checkout->close();
                    $f->headerMove(URL . '/checkout/detail');
                }
            }
        }
    }
}
