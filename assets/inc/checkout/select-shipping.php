<?php
$f = new Clases\PublicFunction;
$envios = new Clases\Envios();
$carrito = new Clases\Carrito();
$pedidos = new Clases\Pedidos();
$detallePedidos = new Clases\DetallePedidos();

if (isset($_SESSION['stages'])) {
    if ($_SESSION['stages']['status'] == 'OPEN') {
?>
        <div class="mt-10">
            <h2 class="fs-20 bold text-center"><?= $_SESSION["lang-txt"]["checkout"]["shipping"]["como_enviar"] ?>
                <hr />
            </h2>
            <?php
            if (isset($_POST["select-shipping_btn"])) {
                $metodoEnvio = $f->antihackMulti($_POST);
                if (isset($metodoEnvio['envio'])) {
                    $envios->set("cod", $metodoEnvio['envio']);
                    $envios->set("idioma", $_SESSION['lang']);
                    $envioData = $envios->view();
                    if ($envioData['data']) {
                        if ($carrito->addShipping($envioData)) {
                            $carro = $carrito->return();
                            $precio = $carrito->totalPrice();
                            $detallePedidos->addCarrito($carro, $_SESSION["cod_pedido"]);
                            $pedidos->set('cod', $_SESSION["cod_pedido"]);
                            $pedidos->editSingle("envio", $envioData['data']['cod']);
                            $pedidos->editSingle("envio_titulo", $envioData['data']["titulo"]);
                            $pedidos->editSingle("entrega", $precio);
                            $pedidos->editSingle("total", $precio);
                            $_SESSION["stages"]['stage-1']['envio'] = $envioData['data']['cod'];
                            $_SESSION["stages"]['stage-1']['titulo_envio'] = $envioData['data']["titulo"];
                            $f->headerMove(URL . '/checkout/payment');
                        }
                    }
                } else {
                    echo "<div class='alert alert-danger' role='alert'>Seleccionar un tipo de envío.</div>";
                }
            }
            ?>
            <form method="post" data-url="<?= URL ?>">
                <div class="product_bar px-2">
                    <div class="form-register-title">
                        <?php
                        $tipoUsuario = ($_SESSION['stages']['type'] == 'GUEST' || isset($_SESSION['usuarios']['minorista']) && $_SESSION['usuarios']['minorista'] == 1) ? 1 : 2; // 1 Minorista - 2 Mayorista
                        $pesoFinal = $carrito->finalWeight();
                        $tope = $f->roundUpToAny($pesoFinal, 5);
                        $metodos_de_envios = $envios->list(["(envios.localidad = '' OR envios.localidad LIKE '%" . $_SESSION['stages']['stage-1']['localidad'] . ', ' . $_SESSION['stages']['stage-1']['provincia'] . "%')", "((envios.peso BETWEEN " . $pesoFinal . " AND " . $tope . ") OR envios.peso=0)", "envios.estado = 1", "envios.tipo_usuario = 0 || envios.tipo_usuario = $tipoUsuario"], '', '', $_SESSION['lang']);
                        $precioFinal = $carrito->totalPrice();

                        foreach ($metodos_de_envios as $key => $metodos_de_envio_) {
                            if ($metodos_de_envio_['data']['limite'] != 0) {
                                if ($precioFinal >= $metodos_de_envio_['data']['limite']) {
                                    $metodos_de_envio_precio =  $_SESSION["lang-txt"]["checkout"]["shipping"]["envio_gratis"];
                                    $metodos_de_envio_['data']["precio"] = 0;
                                } else {
                                    $metodos_de_envio_precio = ($metodos_de_envio_['data']["precio"] != 0) ? "$" . $metodos_de_envio_['data']["precio"] : "";
                                }
                            } else {
                                $metodos_de_envio_precio = ($metodos_de_envio_['data']["precio"] != 0) ? "$" . $metodos_de_envio_['data']["precio"] : "";
                            } ?>

                            <label class="bold text-center text-uppercase btn btn-default btn-block pt-20 pb-20 fs-14">
                                <input type="radio" name="envio" value="<?= $metodos_de_envio_['data']["cod"] ?>" id="envio-propio-<?= $key ?>" required>
                                <img style="max-width: 50px;" class="mx-2" src='<?= $metodos_de_envio_['data']["img"]  ?>' alt='<?= $metodos_de_envio_['data']["titulo"] ?>'>
                                <?= $metodos_de_envio_['data']["titulo"] ?>
                                <price> &nbsp|
                                    <span><?= $metodos_de_envio_precio ?></span>
                                </price>
                            </label>
                        <?php  } ?>
                    </div>
                </div>

                <div id="btn-shipping-d" class="mx-2 mt-10 mb-50">
                    <a href="<?= URL ?>/checkout/shipping" class="btn fs-14 btn-default pull-left text-uppercase"><i class="fa fa-chevron-circle-left"></i> <?= $_SESSION["lang-txt"]["checkout"]["shipping"]["volver"] ?></a>
                    <button class="btn btn-success fs-14 pull-right text-uppercase" type="submit" name="select-shipping_btn" id="btn-select-shipping"><?= $_SESSION["lang-txt"]["checkout"]["shipping"]["siguiente"] ?> <i class="fa fa-chevron-circle-right"></i></button>
                </div>
            </form>

        </div>

<?php
    } else {
        $f->headerMove(URL . '/checkout/detail');
    }
} else {
    $f->headerMove(URL . '/carrito');
}
?>
<script>
    function closeCollapse(type, description) {
        $(".closeCollapse").collapse('hide');
        $("input[name=puntoEntrega]").prop("checked", false);
        $("#envio-type").val(type);
        $("#envio-description").val(description);
    }
</script>