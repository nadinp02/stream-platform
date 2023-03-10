<?php
require_once dirname(__DIR__, 3) . "/Config/Autoload.php";
Config\Autoload::run();
$f = new Clases\PublicFunction();
$carrito = new Clases\Carrito();
$producto = new Clases\Productos();
$checkout = new Clases\Checkout();

$product = isset($_POST['product']) ? $f->antihack_mysqli($_POST['product']) : ''; //TODO POST
$amount = intval(isset($_POST['amount']) ? $f->antihack_mysqli($_POST['amount']) : $f->antihack_mysqli($_POST['stock'])); //TODO POST
$product = trim(str_replace(" ", "", $product));
$titulo = '';

$detalleData = false;
$data = [
    "filter" => ["productos.cod = " . "'$product'"],
    "category" => true,
    "promos" => true
];
$productoData = $producto->list($data, $_SESSION['lang'], true);

$precio = $productoData["data"]["precio_final"];
$stock = $productoData["data"]["stock"];

//BUSCA POR TITULO PARA PODER DIFERENCIAR LOS ATRIBUTOS
$keyProduct = array_search($productoData['data']['titulo'] , array_column($_SESSION['carrito'], 'titulo'));
if ($keyProduct !== false) { // SI EXISTE EN EL CARRITO LE SUMA LA CANTIDAD QUE TENIA Y LO ELIMINA ASI VUELVE A REALIZAR EL ADD CON TODAS LAS VALIDACIONES SOBRE LA CANTIDAD TOTAL
    $amount = $amount + $_SESSION['carrito'][$keyProduct]['cantidad'];
    if ($amount <= $stock) $carrito->delete($keyProduct); // CORROBORA EL STOCK PARA NO BORRARLO DEL CARRITO SI PASA EL LIMITE
}


if (empty($productoData)) {
    echo json_encode(["status" => false, "type" => "error", "message" => "Ocurrió un error, recargar la página."]);
    die();
} else {

    $carrito->deleteOnCheck("Envio-Seleccion");
    $carrito->deleteOnCheck("factura");
    $carrito->deleteOnCheck("Metodo-Pago");
    $carrito->set("id", $productoData['data']['cod']);
    $carrito->set("cantidad", $amount);
    $carrito->set("promo", '');
    $carrito->set("titulo", $productoData['data']['titulo']);
    $stock = $productoData["data"]["stock"];
    $carrito->set("stock", $stock);
    $carrito->set("peso", number_format($productoData['data']['peso'], 2, ".", ""));
    $carrito->set("opciones", $opciones);
    $carrito->set("producto_cod", $productoData['data']['cod_producto']);
    $carrito->set("precio_inicial", $productoData["data"]['precio']);
    $carrito->set("precio", $precio);
    $carrito->set("link", $productoData['link']);
    $carrito->set("tipo", "pr"); //producto
    $carrito->set("descuento", "");

    if ($amount <= $stock) {
        if (!empty($productoData["data"]['promoLleva']) && !empty($productoData["data"]['promoPaga']) && $amount >= $productoData["data"]['promoLleva']) {
            $multiplo = floor($amount / $productoData["data"]['promoLleva']);
            $carrito->set("promo", ($amount - ($productoData["data"]['promoLleva'] * $multiplo)) + ($multiplo * $productoData["data"]['promoPaga']));
            $carrito->set("titulo", "Promo " . $productoData['data']['promoLleva'] . "x" . $productoData['data']['promoPaga'] . ": " . $productoData['data']['titulo']);
        }
        if ($carrito->add()) {
            $checkout->destroy();
            $result = array("status" => true, "cod_producto" => $productoData['data']['cod']);
            echo json_encode($result);
        } else {
            $result = array("status" => false, "message" =>  $_SESSION["lang-txt"]["productos"]["stock_combinacion"]);
            echo json_encode($result);
        }
    } else {
        $result = array("status" => false, "message" =>  $_SESSION["lang-txt"]["productos"]["stock_combinacion"]);
        echo json_encode($result);
    }
}
