<?php
$f = new Clases\PublicFunction;
$usuarios = new Clases\Usuarios();
$pedidos_envios = new Clases\EnviosPedidos();
$pedidos = new Clases\Pedidos();
$detallePedidos = new Clases\DetallePedidos();
$estados_pedidos = new Clases\EstadosPedidos();
$carrito = new Clases\Carrito();

$lang = $_SESSION["lang-txt"]["checkout"]["shipping"];
$selected = false;
$direcciones = ($_SESSION["usuarios"]) ? $pedidos_envios->list(["usuario" => $_SESSION["usuarios"]['cod'], "soft_delete" => 0]) : '';

if (isset($_SESSION['stages'])) {
    $stage1 = !empty($_SESSION["stages"]['stage-1']) ? $_SESSION["stages"]['stage-1'] : (isset($_SESSION['usuarios']) ? $_SESSION['usuarios'] : '');
    if ($_SESSION['stages']['status'] == 'OPEN') {
?>
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <?php

                // SI CARGA UNA DIRECCION NUEVA
                if (isset($_POST["shipping_btn"]) && $_POST["shipping_btn"] === 'new') {
                    $data_user = $f->antihackMulti($_POST);
                    $usuarios->multiSetter($data_user);
                    //ARRAY CREATE USER GUEST
                    if ($_SESSION['stages']['type'] == 'GUEST') {
                        $emailData = $usuarios->validate();
                        $cod = $emailData['status'] ? $emailData['data']['cod'] : substr(md5(uniqid(rand())), 0, 10);
                        $usuarios->set("cod", $cod);
                        ($emailData['status']) ? $usuarios->guestSession() : $usuarios->firstGuestSession();
                        $checkout->user($cod, 'GUEST');
                    }
                    $_SESSION["usuarios"] = $usuarios->viewSession();
                    $data_user["usuario"] = $_SESSION["usuarios"]["cod"];

                    //CREAR PEDIDOS ENVIOS
                    $id_detalle_envio = $pedidos_envios->add($data_user);
                    if ($id_detalle_envio) {
                        $data_user["id"] = $id_detalle_envio;
                    } else {
                        echo "<div class='alert alert-danger' role='alert'>Faltan datos por completar</div>";
                    }
                }

                // SI ELIGE UNA DIRECCION GUARDADA
                if (isset($_POST["shipping_btn"]) && $_POST["shipping_btn"] !== 'new') {
                    $id = $f->antihack_mysqli($_POST["shipping_btn"]);
                    $data_user = $direcciones[$id]['data'];
                }

                // CREACION DEL PEDIDO Y LOS DATOS DEL STAGE 1 Y CONTINUAR A SELECCIONAR METODO DE ENVIO
                if (isset($_POST["shipping_btn"]) && isset($data_user["id"]) && !empty($data_user["id"])) {
                    $carro = $carrito->return();
                    $precio = $carrito->totalPrice();

                    if ($_SESSION["cod_pedido"] == '') {
                        $cod_pedido = $pedidos->generarCodPedido();
                        $data = [
                            "cod" => $cod_pedido,
                            "entrega" => $precio,
                            "total" => $precio,
                            "estado" => $_SESSION["perfil-ecommerce"]["data"]["estado_pedido"],
                            "usuario" => $data_user["usuario"],
                            "idioma" => $_SESSION['lang'],
                            "detalle_envio" => $data_user["id"]
                        ];

                        if ($pedidos->add($data)) {
                            $_SESSION["cod_pedido"] = $cod_pedido;
                            $_SESSION['stages']['cod'] = $cod_pedido;
                            $_SESSION['stages']['user_cod'] = $data_user["usuario"];
                            $_SESSION["stages"]['stage-1'] = $data_user;
                            $detallePedidos->addCarrito($carro, $cod_pedido);

                            $f->headerMove(URL . '/checkout/select-shipping');
                        }
                    } else {
                        $pedidos->set('cod', $_SESSION["cod_pedido"]);
                        $pedidos->editSingle("entrega", $precio);
                        $pedidos->editSingle("total", $precio);
                        $pedidos->editSingle("estado", $_SESSION["perfil-ecommerce"]["data"]["estado_pedido"]);
                        $pedidos->editSingle("usuario", $data_user["usuario"]);
                        $pedidos->editSingle("detalle_envio",  $data_user["id"]);
                        $_SESSION['stages']['user_cod'] = $data_user["usuario"];
                        $_SESSION["stages"]['stage-1'] = $data_user;
                        $detallePedidos->addCarrito($carro, $_SESSION["cod_pedido"]);

                        $f->headerMove(URL . '/checkout/select-shipping');
                    }
                }
                ?>


                <div class="accordion" id="datosEnvio">
                    <h2 class="fs-16 bold text-center">
                        <hr />
                        <?= $lang["informacion_envio"] ?>
                        <hr />
                    </h2>
                    <?php
                    if ($direcciones) {
                        foreach ($direcciones as $key => $direccion) {
                    ?>
                            <div class="card" id="d-<?= $direccion['data']['id'] ?>">
                                <div class="card-header" id="head-<?= $direccion['data']['id'] ?>">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#direccion-<?= $direccion['data']['id'] ?>" aria-expanded="true" aria-controls="direccion-<?= $direccion['data']['id'] ?>">
                                            <?= $key + 1 . ' - ' . $direccion['data']['nombre'] . ' ' . $direccion['data']['apellido'] . ' - ' . $direccion['data']['calle'] . ' ' . $direccion['data']['numero'] . ' ' . $direccion['data']['piso'] ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="direccion-<?= $direccion['data']['id'] ?>" class="collapse " aria-labelledby="head-<?= $direccion['data']['id'] ?>" data-parent="#datosEnvio">
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
                                        <?= ($direccion['data']['similar']) ? '<b>' . $lang['acepte-similar'] . '</b>' : '' ?>
                                        <hr>
                                        <form method="post" data-url="<?= URL ?>">
                                            <button class="btn btn-success fs-14 pull-right text-uppercase" type="submit" name="shipping_btn" value="<?= $key ?>" id="btn-shipping-1">
                                                <?= $lang["siguiente"] ?>
                                                <i class="fa fa-chevron-circle-right"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-danger   fs-14 mb-15 deleteConfirm" id="envios-<?= $direccion['data']['id'] ?>-<?= $direccion['data']['usuario'] ?>-delete">Eliminar</button>
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
                        <div id="direccion-nuevo" class="collapse <?= (!$direcciones) ? 'show' : '' ?>" aria-labelledby="head-nuevo" data-parent="#datosEnvio">
                            <div class="card-body">
                                <!-- <form id="shipping-f" method="post" data-url="<?= URL ?>"> -->
                                <form method="post" data-url="<?= URL ?>">
                                    <div class="row">
                                        <div class="col-md-6 mt-10">
                                            <label class="bold fs-12"><?= $lang["nombre"] ?>:</label>
                                            <input type="text" placeholder="<?= $lang["nombre"] ?>" name="nombre" value="<?= !isset($stage1["nombre"]) ? '' : $stage1["nombre"] ?>" class="form-control" />
                                        </div>
                                        <div class="col-md-6 mt-10">
                                            <label class="bold fs-12"><?= $lang["apellido"] ?>:</label>
                                            <input type="text" placeholder="<?= $lang["apellido"] ?>" name="apellido" value="<?= !isset($stage1["apellido"]) ? '' : $stage1["apellido"] ?>" class="form-control" />
                                        </div>
                                        <div class="col-md-12 mt-10">
                                            <label class="bold fs-12"><?= $lang["email"] ?>:</label>
                                            <input type="text" placeholder="<?= $lang["email"] ?>" name="email" value="<?= !isset($stage1["email"]) ? '' : $stage1["email"] ?>" class="form-control" />
                                        </div>
                                        <div class="col-md-6 mt-10">
                                            <label class="bold fs-12"><?= $lang["telefono"] ?>:</label>
                                            <input type="text" placeholder="<?= $lang["telefono"] ?>" name="telefono" value="<?= !isset($stage1["telefono"]) ? '' : $stage1["telefono"] ?>" class="form-control" />
                                        </div>
                                        <div class="col-md-6 mt-10">
                                            <label class="bold fs-12"><?= $lang["celular"] ?>:</label>
                                            <input type="text" placeholder="<?= $lang["celular"] ?>" name="celular" value="<?= !isset($stage1["celular"]) ? '' : $stage1["celular"] ?>" class="form-control" />
                                        </div>
                                        <div class="col-md-4 mt-10">
                                            <label class="bold fs-12"><?= $lang["provincia"] ?></label></label>
                                            <select id='provincia' data-url="<?= URL ?>" class="form-control" name="provincia" data-validation="required" required>
                                                <option value="<?= isset($stage1['data']['provincia']) ? $stage1['data']['provincia'] : '' ?>" selected><?= isset($stage1['data']['provincia']) ? $stage1['data']['provincia'] : $lang["seleccionar_provincia"] ?></option>
                                                <?php $f->provincias(); ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mt-10">
                                            <label class="bold fs-12"><?= $lang["localidad"] ?></label></label>
                                            <select id='localidad' class="form-control" name="localidad" data-validation="required" required>
                                                <option value="<?= isset($stage1['data']['localidad']) ? $stage1['data']['localidad'] : '' ?>" selected><?= isset($stage1['data']['localidad']) ? $stage1['data']['localidad'] : $lang["seleccionar_localidad"] ?></option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mt-10">
                                            <label class="bold fs-12"><?= $lang["postal"] ?>:</label>
                                            <input type="text" placeholder="<?= $lang["postal"] ?>" name="postal" value="<?= !isset($stage1["postal"]) ? '' : $stage1["postal"] ?>" class="form-control" />
                                        </div>
                                        <div class="col-md-4 mt-10">
                                            <label class="bold fs-12"><?= $lang["calle"] ?>:</label>
                                            <input type="text" placeholder="<?= $lang["calle"] ?>" name="calle" value="<?= !isset($stage1["calle"]) ? '' : $stage1["calle"] ?>" class="form-control" />
                                        </div>
                                        <div class="col-md-4 mt-10">
                                            <label class="bold fs-12"><?= $lang["numero"] ?>:</label>
                                            <input type="number" placeholder="<?= $lang["numero"] ?>" name="numero" value="<?= !isset($stage1["numero"]) ? '' : $stage1["numero"] ?>" class="form-control" />
                                        </div>
                                        <div class="col-md-4 mt-10">
                                            <label class="bold fs-12"><?= $lang["piso"] ?>:</label>
                                            <input type="text" placeholder="<?= $lang["piso"] ?>" name="piso" value="<?= !isset($stage1["piso"]) ? '' : $stage1["piso"] ?>" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-12 col-xs-12 mt-10 mb-10 fs-15 text-uppercase">
                                            <hr />
                                            <input type="checkbox" name="similar" value="1" <?= ($stage1['data']['similar'] ?? '') == '1' ? "checked" : ""; ?>>
                                            <?= $lang["cambiar_similar"] ?>
                                            <i class="d-block fs-12 normal">* <?= $lang["rta_cambiar_similar"] ?></i>
                                        </label>
                                        <div class="col-md-12">
                                            <hr />

                                            <button class="btn btn-next-checkout pull-right text-uppercase" type="submit" name="shipping_btn" value="new" id="btn-shipping-1"><?= $lang["siguiente"] ?> <i class="fa fa-chevron-circle-right"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="<?= URL ?>/carrito" class="btn btn-prev-checkout mt-4"><i class="fa fa-chevron-left"></i> <?= $lang["volver"] ?></a>
            </div>
        </div>
<?php
    } else {
        $f->headerMove(URL . '/checkout/detail');
    }
} else {
    $f->headerMove(URL . '/carrito');
}
?>