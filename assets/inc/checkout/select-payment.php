<?php
$carrito = new Clases\Carrito();
$pagos = new Clases\Pagos();
$detallePedidos = new Clases\DetallePedidos();
$checkout = new Clases\Checkout();
$pedidos = new Clases\Pedidos();

$tipoUsuario = ($_SESSION['stages']['type'] == 'GUEST' || $_SESSION['usuarios']['minorista'] == 1) ? 1 : 2; // 1 Minorista - 2 Mayorista
$carrito->deleteOnCheck("Metodo-Pago");
if (isset($_SESSION['stages'])) {
    if ($_SESSION['stages']['status'] == 'OPEN' && !empty($_SESSION['stages']['stage-2']) && !empty($_SESSION['stages']['user_cod'])) {
?>
        <div class="row">
            <div class="col-md-12 pt-10">
                <hr>
                <p class="text-uppercase bold fs-20 text-center"><?= $_SESSION["lang-txt"]["checkout"]["payment"]["seleccionar_pago"] ?></p>
                <hr>
                <?php
                if (isset($_POST["select-payment_btn"])) {
                    $metodoPago = $f->antihackMulti($_POST);
                    if (isset($metodoPago['pago'])) {

                        $pagos->set("cod", $metodoPago['pago']);
                        $pagos->set("idioma", $_SESSION['lang']);
                        $pagoData = $pagos->view();
                        if (!empty($pagoData['data'])) {
                            $carrito->checkKeyOnCart("Metodo-Pago");
                            $carrito->changePriceByPayment($pagoData);
                            $precio = $carrito->totalPrice();
                            $entrega = (!empty($pagoData['data']['entrega'])) ? (($pagoData['data']['entrega'] * $precio) / 100) : $precio;

                            $carro = $carrito->return();
                            $precio = $carrito->totalPrice();
                            $detallePedidos->addCarrito($carro, $_SESSION["cod_pedido"], true);

                            $pedidos->set("cod", $_SESSION['cod_pedido']);
                            $pedidos->editSingle("estado", $pagoData['data']['defecto']);
                            $pedidos->editSingle("pago", $pagoData['data']["cod"]);
                            $pedidos->editSingle("pago_titulo", $pagoData['data']["titulo"]);
                            $pedidos->editSingle("leyenda_pago", $pagoData['data']['leyenda']);
                            $pedidos->editSingle("entrega", $entrega);
                            $pedidos->editSingle("total", $precio);

                            $_SESSION["stages"]['stage-3'] = $pagoData['data'];
                            $checkPedido = $pedidos->view();
                            if (!empty($checkPedido['data'])) {
                                if (!empty($pagoData['data']['tipo'])) {
                                    switch ($pagoData['data']['tipo']) {
                                        case 1:
                                            $response = array("status" => true, "type" => "API", "url" => URL . '/api/payments/mp.php?cod=' . $_SESSION['cod_pedido']);
                                            break;
                                        case 2:
                                            $response = array("status" => true, "type" => "APIV2", "url" => URL . "/mp/index.php?codPago=" . $cod . "&codPedido=" . $_SESSION['cod_pedido']);
                                            break;
                                        case 5:
                                            $response = array("status" => true, "type" => "", "url" => URL . "/decidir?order=" . $_SESSION["cod_pedido"]);
                                            break;
                                        default:
                                            $response = array("status" => false, "message" => "[301] Ocurrió un error, recargar la página.");
                                            break;
                                    }
                                } else {
                                    $checkout->close();
                                    $response = array("status" => true, "url" => URL . '/checkout/detail');
                                }
                                if ($response['status']) {
                                    $f->headerMove($response['url']);
                                } else {
                                    echo "<div class='alert alert-danger' role='alert'>" . $response['message'] . "</div>";
                                }
                            }
                        }
                    } else {
                        echo "<div class='alert alert-danger' role='alert'>Seleccionar un método de pago.</div>";
                    }
                }
                ?>
                <!-- <form id="payment-f" method="post" data-url="<?= URL ?>" data-cod="<?= $_SESSION['last_cod_pedido'] ?>" onsubmit="addPayment()"> -->
                <form method="post" data-url="<?= URL ?>">
                    <div id="formPago" class="p-2">
                        <ul class="p-0 m-0">
                            <?php
                            $listPagos = $pagos->list(["pagos.estado = 1", "(pagos.tipo_usuario = $tipoUsuario OR pagos.tipo_usuario = 0)", "(" . $carrito->precioSinMetodoDePago() . " >= pagos.minimo OR pagos.minimo IS NULL) AND (" . $carrito->precioSinMetodoDePago() . " <= pagos.maximo OR pagos.maximo = 0 OR pagos.maximo IS NULL)"], "", "", $_SESSION['lang']);
                            foreach ($listPagos as $key => $pago) {
                                $totalSinEnvio = $carrito->precioSinMetodoDeEnvio();
                                $precio_total = $carrito->checkPriceOnPayments($pago);
                            ?>
                                <label class="bold text-center text-uppercase btn btn-default btn-block fs-14 pt-20 pb-20">
                                    <input type="radio" <?= ((count($listPagos) < 2)  ? "checked" : '') ?> name="pago" value="<?= $pago['data']['cod'] ?>" id="r-<?= $key ?>" required>
                                    <img style="max-width: 50px;" class="mx-2" src='<?= $pago['data']['img']  ?>' alt='<?= $pago['data']['titulo'] ?>'>
                                    <?= $pago['data']['titulo'] ?>
                                    <?php if ($precio_total > 0) { ?>
                                        <price> &nbsp|
                                            <span class="fs-14" style="font-weight: 700px;"><?= "$" . $precio_total ?></span>
                                        </price>
                                    <?php } ?>
                                    <?= !empty($pago['data']['leyenda']) ? '<p class="fs-12"> &nbsp ' . $pago['data']['leyenda'] . '</p>' : ''; ?>
                                </label>
                            <?php
                            }
                            ?>
                        </ul>
                    </div>

                    <div id="btn-payment-d" class="mx-2 mt-10 mb-50">
                        <a href="<?= URL ?>/checkout/payment" class="btn btn-default fs-14">
                            <i class="fa fa-chevron-circle-left"></i>
                            <?= $_SESSION["lang-txt"]["checkout"]["payment"]["volver"] ?>
                        </a>
                        <button class="btn btn-success fs-14 pull-right text-uppercase" type="submit" name="select-payment_btn" id="btn-select-payment">
                            <?= $_SESSION["lang-txt"]["checkout"]["payment"]["finalizar"] ?> <i class="fa fa-check-circle"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>
<?php
    } else {
        if ($_SESSION['stages']['status'] == 'CLOSED') {
            $f->headerMove(URL . '/checkout/detail');
        } else {
            if (empty($_SESSION['stages']['user_cod'])) {
                $f->headerMove(URL . '/login');
            } else {
                $f->headerMove(URL . '/checkout/billing');
            }
        }
    }
} else {
    $f->headerMove(URL . '/carrito');
}

?>
<div id="modalS" class="modal fade mt-120" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div id="textS" class="text-center">
                </div>
            </div>
        </div>
    </div>
</div>