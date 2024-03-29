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
$admin = isset($_GET['admin']) ? $f->antihack_mysqli($_GET['admin']) : '';
if (!empty($cod)) {
    $pedidos->set("cod", $cod);
    $pedidosData = $pedidos->view();
    if (!empty($admin)) {
        $nombre = $_SESSION['usuarios-ecommerce']["nombre"];
        $apellido = $_SESSION['usuarios-ecommerce']["apellido"];
        $email = $_SESSION['usuarios-ecommerce']["email"];
    } else {
        $nombre = $_SESSION['usuarios']["nombre"];
        $apellido = $_SESSION['usuarios']["apellido"];
        $email = $_SESSION['usuarios']["email"];
    }
    if (!empty($pedidosData['data'])) {
        $config->set("id", 1);
        $paymentsData = $config->viewPayment();
        $mp = new MP($paymentsData['data']['variable1'], $paymentsData['data']['variable2']);
        if (!empty($pedidosData['data']['entrega']) && $pedidosData['data']['entrega'] < $pedidosData['data']['total']) {
            $price = floatval($pedidosData['data']['entrega']);
            $title = "SEÑA DE COMPRA CÓDIGO N°:" . $_SESSION['cod_pedido'];
        } else {
            $price = floatval($pedidosData['data']['total']);
            $title = "COMPRA CÓDIGO N°:" . $_SESSION['cod_pedido'];
        }
        $preference_data = array(
            "items" => array(
                array(
                    "id" => $_SESSION['cod_pedido'],
                    "title" => $title,
                    "quantity" => 1,
                    "currency_id" => "ARS",
                    "unit_price" => $price
                )
            ),
            "payer" => array(
                "name" => $nombre,
                "surname" => $apellido,
                "email" => $email
            ),
            "back_urls" => array(
                "success" => URL . "/checkout/detail",
                "pending" => URL . "/checkout/detail",
                "failure" => URL . "/checkout/detail"
            ),
            "notification_url" => URL . "/api/payments/ipn.php",
            "external_reference" => $_SESSION['cod_pedido'],
            "auto_return" => "all"
        );
        $preference = $mp->create_preference($preference_data);

        $url = $preference['response']['init_point'];
        $pedidos->editSingle("link_pago", $url);


        $checkout->close();
        if (!empty($admin)) {
            $response = array("status" => true, "url" => URL_ADMIN . "/index.php?op=pedidos&accion=ver");
        } else {
            $response = array("status" => true, "url" => $url);
        }
        $f->headerMove($response['url']);
        echo json_encode($response);
    } else {
        $response = array("status" => false, "message" => "Ocurrió un error, recargar la página.");
        echo json_encode($response);
    }
} else {
    $response = array("status" => false, "message" => "Ocurrió un error, recargar la página.");
    echo json_encode($response);
}
