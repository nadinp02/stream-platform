<?php
require_once dirname(__DIR__, 2) . "/Config/Autoload.php";
include(dirname(__DIR__, 2) . "/vendor/mercadopago/sdk/lib/mercadopago.php");
Config\Autoload::run();
$config = new Clases\Config();
$pedidos = new Clases\Pedidos();
$pagos = new Clases\Pagos();
$f = new Clases\PublicFunction();
$config->set("id", 1);
$paymentsData = $config->viewPayment();

$mp = new MP($paymentsData['data']['variable1'], $paymentsData['data']['variable2']);
$payment_info = $mp->get_payment_info($f->antihack_mysqli($_GET["id"]));
if ($payment_info["status"] == 200) {
    $cod = $payment_info["response"]["external_reference"];
    $status = $payment_info["response"]["status"];

    $pedidos->set("cod", $cod);
    $pedidoData = $pedidos->view();
    $pagos->set("cod", $pedidoData['data']['cod_pago']);
    $pagos->set("idioma", $_SESSION['lang']);
    $pagosData = $pagos->view();

    switch ($status) {
        case 'approved':
            $estado = $pagosData["data"]["estado_aprobado"];
            break;
        case 'in_process':
            $estado = $pagosData["data"]["estado_pendiente"];
            break;
        case 'pending':
            $estado = $pagosData["data"]["estado_pendiente"];
            break;
        case 'rejected':
            $estado = $pagosData["data"]["estado_rechazado"];
            break;
        default:
            $estado = $pagosData["data"]["defecto"];
            break;
    }
    $pedidos->editSingle("estado", $estado);

    echo $estado;
}
