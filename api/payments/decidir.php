<?php
require_once dirname(__DIR__, 2) . "/Config/Autoload.php";
Config\Autoload::run();

$funciones = new Clases\PublicFunction();
$decidir = new Clases\Decidir();
$pedido = new Clases\Pedidos();
$pagos = new Clases\Pagos();
$checkout = new Clases\Checkout();

$order = $funciones->antihack_mysqli($_POST["order"]);
$pedido->set("cod", $order);
$pedidoData  = $pedido->view();
$pagos->set("cod", $pedidoData['data']['pago']);
$pagos->set("idioma", $_SESSION['lang']);
$pagosData = $pagos->view();

$paymentsData = $config->viewPayment();
$carroTotal = 0;
$mensaje_carro = 'Pedido: ' . $order . '\n';
foreach ($pedidoData["detail"] as $carriItem) {
    $carroTotal += $carriItem["cantidad"] * $carriItem["precio"];
    $mensaje_carro .= $carriItem["producto"] . ' (' . $carriItem["cantidad"] . ') :' . $carriItem["cantidad"] * $carriItem["precio"] . '\n';
}
$mensaje_carro .= '\n TOTAL: ' . $carroTotal;
$precioFinal = intval(str_replace(",", "", number_format($pedidoData["data"]["total"], 2, ",", "")));

if (!empty($pedidoData["data"])) {
    $paymentMethodId = $funciones->antihack_mysqli($_POST["card_type"]);
    unset($_POST["order"]);
    unset($_POST["card_type"]);
    unset($_POST["installments"]);

    $installments = $decidir->getInstallmentsForPayment($pedidoData["detail"]);
    if ($installments) {
        $data = $decidir->getPaymentToken($_POST);

        $data = json_decode($data, true);
        if (isset($data["status"]) && $data["status"] == "active") {
            $data = $decidir->processPayment(substr(md5(uniqid(rand())), 0, 10), $paymentMethodId, $data["id"], $data["bin"], $precioFinal, $installments, $mensaje_carro);
            $data = json_decode($data, true);
            if (isset($data["error_type"])) {
                echo json_encode(["status" => false, "message" => $data["error_type"]]);
            } else {
                if ($data["status"] == "approved") {
                    $estado = $pagosData['data']['estado_aprobado'];
                    echo json_encode(["status" => true, "message" => "¡Pago procesado!"]);
                }
                if ($data["status"] == "preapporved") {
                    $estado = $pagosData['data']['estado_pendiente'];

                    echo json_encode(["status" => true, "message" => "¡Pago pendiente!"]);
                }
                if ($data["status"] == "review") {
                    $estado = $pagosData['data']['estado_pendiente'];
                    echo json_encode(["status" => true, "message" => "¡Pago en proceso!"]);
                }
                if ($data["status"] == "rejected") {
                    $estado = $pagosData['data']['estado_rechazado'];
                    echo json_encode(["status" => false, "message" => "¡Pago rechazado!"]);
                }
                $pedido->editSingle("estado", $estado);
                $pedido->editSingle("observacion", json_encode($data));
                $checkout->close();
            }
        } else {
            echo json_encode(["status" => false, "message" => "Ocurrio un error", "error" => $data["message"]]);
        }
    } else {
        echo json_encode(["status" => false, "goBack" => true, "message" => "Ocurrio un error con el pedido"]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Ocurrio un error con el pedido"]);
}
