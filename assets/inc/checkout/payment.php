<?php
$pedidos_pagos = new Clases\PagosPedidos();
$detallePedidos = new Clases\DetallePedidos();
$carrito = new Clases\Carrito();

$direcciones =  $pedidos_pagos->list(["usuario" => $_SESSION["usuarios"]['cod'], "soft_delete" => 0]);
$lang = $_SESSION["lang-txt"]["checkout"]["billing"];
if (isset($_SESSION['stages'])) {
    if ($_SESSION['stages']['status'] == 'OPEN' && !empty($_SESSION['stages']['stage-1']) && !empty($_SESSION['stages']['user_cod'])) {
?>
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <?php
                if (isset($_POST["payment_btn"]) && $_POST["payment_btn"] === 'new') {
                    $data_payment = $f->antihackMulti($_POST);
                    $data_payment['usuario'] = $_SESSION["usuarios"]["cod"];

                    //CREAR PEDIDOS PAGOS
                    $id_detalle_pago = $pedidos_pagos->add($data_payment);
                    if ($id_detalle_pago) {
                        $data_payment["id"] = $id_detalle_pago;
                    } else {
                        echo "<div class='alert alert-danger' role='alert'>Faltan datos por completar</div>";
                    }
                }

                // SI ELIGE UNA DIRECCION GUARDADA
                if (isset($_POST["payment_btn"]) && $_POST["payment_btn"] !== 'new') {
                    $id = $f->antihack_mysqli($_POST["payment_btn"]);
                    $data_payment = $direcciones[$id]['data'];
                }

                // CREACION DE LOS DATOS DEL STAGE 2 Y CONTINUAR A SELECCIONAR METODO DE PAGO
                if (isset($_POST["payment_btn"]) && isset($data_payment["id"]) && !empty($data_payment["id"])) {
                    $factura = (isset($data_payment['factura']) && $data_payment['factura'] == '1') ? true : false;
                    $carrito->addBill($factura);
                    $carro = $carrito->return();
                    $precio = $carrito->totalPrice();
                    $detallePedidos->addCarrito($carro, $_SESSION["cod_pedido"]);
                    $pedidos->set('cod', $_SESSION["cod_pedido"]);
                    $pedidos->editSingle("detalle_pago", $data_payment["id"]);
                    $pedidos->editSingle("entrega", $precio);
                    $pedidos->editSingle("total", $precio);
                    $_SESSION["stages"]['stage-2'] = $data_payment;

                    $f->headerMove(URL . '/checkout/select-payment');
                }

                ?>
                <div class="accordion" id="datosPagos">
                    <h2 class="fs-16 bold text-center">
                        <hr />
                        <?= $lang["informacion_facturacion"] ?>
                        <hr />
                    </h2>

                    <?php if ($direcciones) {
                        foreach ($direcciones as $key => $direccion) {
                    ?>
                            <div class="card" id="d-<?= $direccion['data']['id'] ?>">
                                <div class="card-header" id="head-<?= $direccion['data']['id'] ?>">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#direccion-<?= $direccion['data']['id'] ?>" aria-expanded="true" aria-controls="direccion-<?= $direccion['data']['id'] ?>">
                                            <?= $key + 1  . ' - ' . $direccion['data']['documento'] . ' - ' . $direccion['data']['nombre'] . ' ' . $direccion['data']['apellido'] . ' - ' . $direccion['data']['calle'] . ' ' . $direccion['data']['numero'] . ' ' . $direccion['data']['piso'] ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="direccion-<?= $direccion['data']['id'] ?>" class="collapse " aria-labelledby="head-<?= $direccion['data']['id'] ?>" data-parent="#datosPagos">
                                    <div class="card-body">
                                        <?= $direccion['data']['calle'] ?>
                                        <?= $direccion['data']['numero'] ?>
                                        <?= $direccion['data']['piso'] ?> -
                                        <?= $direccion['data']['localidad'] ?>,
                                        <?= $direccion['data']['provincia'] ?> -
                                        <?= $direccion['data']['postal'] ?>
                                        <br>
                                        <?= $direccion['data']['email'] ?> -
                                        <?= $direccion['data']['telefono'] ?> -
                                        <?= $direccion['data']['celular'] ?>
                                        <br>
                                        <?= ($direccion['data']['factura']) ? '<b>' . $lang['solicite-factura'] . '</b>' : '' ?>
                                        <hr>
                                        <form method="post" data-url="<?= URL ?>">
                                            <button class="btn btn-success fs-14 pull-right text-uppercase" type="submit" name="payment_btn" value="<?= $key ?>" id="btn-payment-1">
                                                <?= $lang["siguiente"] ?>
                                                <i class="fa fa-chevron-circle-right"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-danger fs-14 mb-15 deleteConfirm text-uppercase" id="pagos-<?= $direccion['data']['id'] ?>-<?= $direccion['data']['usuario'] ?>-delete">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    } ?>
                    <div class="card">
                        <div class="card-header" id="head-nuevo">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#direccion-nuevo" aria-expanded="true" aria-controls="direccion-nuevo">
                                    <?= $lang["nueva_direccion"] ?>
                                </button>
                            </h2>
                        </div>
                        <div id="direccion-nuevo" class="collapse <?= (!$direcciones) ? 'show' : '' ?>" aria-labelledby="head-nuevo" data-parent="#datosPagos">
                            <div class="card-body">
                                <!-- <form id="billing-f" method="post" data-cod="<?= $_SESSION['cod_pedido'] ?>" data-url="<?= URL ?>" onsubmit="addBilling()"> -->
                                <form method="post" data-url="<?= URL ?>">
                                    <div class="row">
                                        <div class="col-md-6 col-xs-12">
                                            <label><?= $lang["nombre"] ?>:</label>
                                            <input class="form-control mb-10" type="text" value="<?= (!empty($_SESSION['stages']['stage-2'])) ? $_SESSION['stages']['stage-2']['nombre'] : $_SESSION['stages']['stage-1']['nombre']; ?>" name="nombre" data-validation="required" required />
                                        </div>
                                        <div class="col-md-6 col-xs-12">
                                            <label><?= $lang["apellido"] ?>:</label>
                                            <input class="form-control  mb-10" type="text" value="<?= (!empty($_SESSION['stages']['stage-2'])) ? $_SESSION['stages']['stage-2']['apellido'] : $_SESSION['stages']['stage-1']['apellido']; ?>" name="apellido" data-validation="required" required />
                                        </div>
                                        <div class="col-md-8 col-xs-12">
                                            <label><?= $lang["email"] ?>:</label>
                                            <input class="form-control  mb-10" type="email" value="<?= (!empty($_SESSION['stages']['stage-2'])) ? $_SESSION['stages']['stage-2']['email'] : $_SESSION['stages']['stage-1']['email']; ?>" name="email" data-validation="required" required />
                                        </div>
                                        <div class="col-md-4 col-xs-12">
                                            <label><?= $lang["dni"] ?>:</label>
                                            <input class="form-control mb-10" type="text" value="<?= (!empty($_SESSION['stages']['stage-2'])) ? $_SESSION['stages']['stage-2']['documento'] : '' ?>" name="documento" data-validation="required" required />
                                        </div>
                                        <div class="col-md-6 col-xs-12">
                                            <label><?= $lang["telefono"] ?>:</label>
                                            <input class="form-control mb-10" type="text" value="<?= (!empty($_SESSION['stages']['stage-2'])) ? $_SESSION['stages']['stage-2']['telefono'] : $_SESSION['stages']['stage-1']['telefono']; ?>" name="telefono" data-validation="required" required />
                                        </div>
                                        <div class="col-md-6 col-xs-12">
                                            <label><?= $lang["celular"] ?>:</label>
                                            <input class="form-control mb-10" type="text" value="<?= (!empty($_SESSION['stages']['stage-2'])) ? $_SESSION['stages']['stage-2']['celular'] : $_SESSION['stages']['stage-1']['celular']; ?>" name="celular" data-validation="required" required />
                                        </div>
                                        <div class="col-md-4 col-xs-12 form-row-wide">

                                            <label><?= $lang["provincia"] ?></label>
                                            <!-- Dropdown -->
                                            <select id='provincia' data-url="<?= URL ?>" class="form-control" name="provincia" data-validation="required" required>
                                                <?php
                                                if (!empty($_SESSION['stages']['stage-2']['provincia'])) {
                                                ?>
                                                    <option value="<?= $_SESSION['stages']['stage-2']['provincia'] ?>">
                                                        <?= $_SESSION['stages']['stage-2']['provincia'] ?>
                                                    </option>
                                                    <?php
                                                } else {
                                                    if (!empty($_SESSION['stages']['stage-1']['provincia'])) {
                                                    ?>
                                                        <option value="<?= $_SESSION['stages']['stage-1']['provincia'] ?>">
                                                            <?= $_SESSION['stages']['stage-1']['provincia'] ?>
                                                        </option>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <option value="" selected><?= $lang["seleccionar_provincia"] ?></option>
                                                <?php
                                                    }
                                                }
                                                $f->provincias();
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-xs-12 form-row-wide">
                                            <label><?= $lang["localidad"] ?></label>
                                            <!-- Dropdown -->
                                            <select id='localidad' class="form-control" name="localidad" data-validation="required" required>
                                                <?php
                                                if (!empty($_SESSION['stages']['stage-2']['localidad'])) {
                                                ?>
                                                    <option value="<?= $_SESSION['stages']['stage-2']['localidad'] ?>" selected>
                                                        <?= $_SESSION['stages']['stage-2']['localidad'] ?>
                                                    </option>
                                                    <?php
                                                } else {
                                                    if (!empty($_SESSION['stages']['stage-1']['localidad'])) {
                                                    ?>
                                                        <option value="<?= $_SESSION['stages']['stage-1']['localidad'] ?>" selected>
                                                            <?= $_SESSION['stages']['stage-1']['localidad'] ?>
                                                        </option>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <option value="" selected><?= $lang["seleccionar_localidad"] ?></option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label><?= $lang["postal"] ?>:</label>
                                            <input class="form-control mb-10" type="text" placeholder="<?= $lang["postal"] ?>" name="postal" value="<?= (!empty($_SESSION['stages']['stage-2'])) ? $_SESSION['stages']['stage-2']['postal'] : $_SESSION['stages']['stage-1']['postal']; ?>" data-validation="required" required />
                                        </div>
                                        <div class="col-md-4 col-xs-12">
                                            <label><?= $lang["calle"] ?>:</label>
                                            <input class="form-control mb-10" type="text" value="<?= (!empty($_SESSION['stages']['stage-2'])) ? $_SESSION['stages']['stage-2']['calle'] : $_SESSION['stages']['stage-1']['calle']; ?>" name="calle" data-validation="required" required />
                                        </div>
                                        <div class="col-md-4">
                                            <label><?= $lang["numero"] ?>:</label>
                                            <input class="form-control mb-10" type="number" placeholder="<?= $lang["numero"] ?>" name="numero" value="<?= (!empty($_SESSION['stages']['stage-2'])) ? $_SESSION['stages']['stage-2']['numero'] : $_SESSION['stages']['stage-1']['numero']; ?>" data-validation="required" required />
                                        </div>
                                        <div class="col-md-4">
                                            <label><?= $lang["piso"] ?>:</label>
                                            <input class="form-control mb-10" type="text" placeholder="<?= $lang["piso"] ?>" name="piso" value="<?= (!empty($_SESSION['stages']['stage-2'])) ? $_SESSION['stages']['stage-2']['piso'] : $_SESSION['stages']['stage-1']['piso']; ?>" />
                                        </div>
                                        <label class="col-md-12 col-xs-12 mt-10 mb-10 fs-15 text-uppercase">
                                            <hr />
                                            <input type="checkbox" name="factura" value="1" <?php
                                                                                            if (!empty($_SESSION['stages']['stage-2'])) {
                                                                                                if (!empty($_SESSION['stages']['stage-2']['factura'])) {
                                                                                                    echo "checked";
                                                                                                }
                                                                                            }
                                                                                            ?>>
                                            <?= $lang["factura_a"] ?>
                                        </label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr />
                                            <button class="btn btn-next-checkout pull-right text-uppercase" type="submit" name="payment_btn" value="new" id="btn-payment-1"><?= $lang["siguiente"] ?> <i class="fa fa-chevron-circle-right"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="<?= URL ?>/checkout/select-shipping" class="btn btn-prev-checkout mt-4"><i class="fa fa-chevron-left"></i> <?= $lang["volver"] ?></a>

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
                $f->headerMove(URL . '/checkout/shipping');
            }
        }
    }
} else {
    $f->headerMove(URL . '/carrito');
}
?>