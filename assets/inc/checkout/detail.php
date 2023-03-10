<?php
$f = new Clases\PublicFunction();
$pedidos = new Clases\Pedidos();

if (isset($_SESSION['stages'])) {
    $pedidos->checkMercadoPago();
    $pedidos->set("cod", $_SESSION['cod_pedido']);
    $pedido_info = $pedidos->view();
    if ($_SESSION['stages']['status'] == 'CLOSED') {
        if (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["pedido_whatsapp"] == 1) {
            $pedidos->enviarPedidoWsp($pedido_info);
        }
?>
        <div class="container">
            <div class="section pt-50  pb-70 pb-lg-50 pb-md-40 pb-sm-30 pb-xs-20">
                <div class="customer-login-register register-pt-0">
                    <form id="payment-f" method="post">
                        <div class="form-register-title" style="width:100%;display:block;margin-top:30px">
                            <h2><?= $_SESSION["lang-txt"]["checkout"]["detail"]["compra_finalizada"] ?></h2>
                            <h4><?= $_SESSION["lang-txt"]["checkout"]["detail"]["pedido"] ?> N°: <?= $pedido_info['data']['cod'] ?></h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                    <b><?= $_SESSION["lang-txt"]["checkout"]["detail"]["estado"] ?>:</b> <?= mb_strtoupper($pedido_info['estados']['data']['titulo']); ?><br />
                                    <?php if ($pedido_info['data']["pago_titulo"]) { ?><b><?= $_SESSION["lang-txt"]["checkout"]["detail"]["metodo_pago"] ?>:</b> <?= mb_strtoupper($pedido_info['data']["pago_titulo"]); ?><br /><?php } ?>
                                    <?php
                                    if (!empty($pedido_info['data']['leyenda_pago'])) {
                                        echo "<b>" . $_SESSION["lang-txt"]["checkout"]["detail"]["descripcion_pago"] . ": </b>" . $pedido_info['data']['leyenda_pago'] . "<br/>";
                                    }

                                    if (!empty($pedido_info['data']['link_pago'])) {
                                        echo "<b>" . $_SESSION["lang-txt"]["checkout"]["detail"]["url_pago"] . ": </b><a href='" . $pedido_info['data']['link_pago'] . "' target='_blank'>" . $_SESSION["lang-txt"]["checkout"]["detail"]["click_aqui"] . "</a>";
                                    }
                                    ?>
                                    <div class="row mb-15">
                                        <?php if (!empty($pedido_info['detalle_envio'])) { ?>
                                            <div class="col-md-6">
                                                <hr>
                                                <b><?= $_SESSION["lang-txt"]["checkout"]["detail"]["informacion_envio"] ?></b>
                                                <br>
                                                <?= $pedidos->getInfoPedido($pedido_info['detalle_envio']); ?>
                                                <p class='mb-0 fs-13'><b><?= $_SESSION["lang-txt"]["checkout"]["similar"] ?>: </b><?= $pedido_info['detalle_envio']['data']['similar'] ? "Si" : "No" ?></p>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                        <?php if (!empty($pedido_info['detalle_pago'])) { ?>
                                            <div class="col-md-6">
                                                <hr>
                                                <b><?= $_SESSION["lang-txt"]["checkout"]["detail"]["informacion_facturacion"] ?></b>
                                                <br>
                                                <?= $pedidos->getInfoPedido($pedido_info['detalle_pago']); ?>
                                                <?php
                                                if ($pedido_info['detalle_pago']['data']['factura']) {
                                                    echo "<p class='mb-0 fs-13'><b>" . $_SESSION["lang-txt"]["checkout"]["detail"]["factura_cuit"] . " </b></p>";
                                                }
                                                ?>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>

                                </div>
                                <div class="col-md-12" style="width:100%;display:block;margin-top:30px">
                                    <b><?= $_SESSION["lang-txt"]["checkout"]["detail"]["tu_compra"] ?></b>
                                    <hr>
                                    <?php include("assets/inc/checkout/pedidoDetail.php"); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div id="btn-payment-d" class="col-md-12 col-xs-12 mt-10 mb-50">
                                    <a class="btn btn-success btn-block text-center fs-20" style="line-height: 2.71!important;" href="<?= URL ?>/productos?page=1&con_stock=1&order=4" id="btn-payment-1">
                                        SEGUIR COMPRANDO
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php
    } else {
        $f->headerMove(URL . '/carrito');
    }
} else {
    $f->headerMove(URL . '/carrito');
}
?>
<script src="<?= URL ?>/assets/js/checkout/script.js"></script>
<script src="<?= URL ?>/assets/js/checkout/stages.js"></script>