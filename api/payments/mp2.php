<?php


require_once dirname(__DIR__, 2) . "/Config/Autoload.php";
Config\Autoload::run();

$f = new Clases\PublicFunction();
$carrito = new Clases\Carrito();
$pagos = new Clases\Pagos();
$checkout = new Clases\Checkout();
$pedidos = new Clases\Pedidos();
$config = new Clases\Config();


$cod = isset($_GET['cod']) ? $f->antihack_mysqli($_GET['cod']) : '';
$pagos->set("cod", $cod);
$pagos->set("idioma", $_SESSION['lang']);
$pedidos->set("cod", $_SESSION["cod_pedido"]);
$config->set("id", 2);

$pagosData = $pagos->view();

$paymentsData = $config->viewPayment();
//REPLACE WITH YOUR ACCESS TOKEN AVAILABLE IN: https://www.mercadopago.com/developers/panel
MercadoPago\SDK::setAccessToken($paymentsData['data']['variable2']);

$payment = new MercadoPago\Payment();
$payment->transaction_amount = (float)$carrito->totalPrice();
$payment->token = $_POST['token'];
$payment->description = "COMPRA CÓDIGO N°:" . $_SESSION['cod_pedido'];
$payment->external_reference =  $_SESSION['cod_pedido'];
$payment->installments = (int)$_POST['installments'];
$payment->payment_method_id = $_POST['paymentMethodId'];
$payment->issuer_id = (int)$_POST['issuerId'];

$payer = new MercadoPago\Payer();
$payer->email = $_SESSION["usuarios"]["email"];
$payer->identification = array(
    "type" => $_POST['payer']['identification']['type'],
    "number" => $_POST['payer']['identification']['number']
);
$payment->payer = $payer;

$payment->save();

$response = array(
    'status' => $payment->status,
    'message' => $payment->status_detail,
    'id' => $payment->id
);

switch ($payment->status) {
    case 'approved':
        $estado = $pagosData["data"]["estado_aprobado"];
        break;
    case 'in_process':
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
$pedidos->editSingle("observacion", json_encode($payment));
$checkout->close();


echo json_encode($response);
