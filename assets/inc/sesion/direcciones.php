<?php
$pedidos_envios = new Clases\EnviosPedidos();
$pedidos_pagos = new Clases\PagosPedidos();
$lang = $_SESSION["lang-txt"]["sesion"];
$type = isset($_GET["type"]) ? $f->antihack_mysqli($_GET["type"]) : 'envios';

?>
<ul class="nav nav-pills nav-fill" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?= ($type == 'envios') ? 'active' : '' ?>" id="envios-tab" href="<?= URL ?>/sesion/direcciones/envios" role="tab" aria-controls="envios" aria-selected="true"><?= $lang["mis_datos_envio"]  ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($type == 'pagos') ? 'active' : '' ?>" id="pedidos-tab" href="<?= URL ?>/sesion/direcciones/pagos" role="tab" aria-controls="pagos" aria-selected="false"><?= $lang["mis_datos_pago"]  ?></a>
    </li>
</ul>
<div class="tab-content" id="myTabContent">
    <?php if ($type == 'envios') {
        $direcciones_envio =  $pedidos_envios->list(["usuario" => $_SESSION["usuarios"]['cod'], "soft_delete" => 0]);
    ?>
        <div class="tab-pane fade show active" id="envios" role="tabpanel" aria-labelledby="envios-tab">
            <div class="accordion" id="datosEnvio">
                <?php
                if ($direcciones_envio) {
                    foreach ($direcciones_envio as $key => $direccion) {
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
                                        <?= ($direccion['data']['similar']) ? '<b>'. $lang['detalle_envios']['acepte-similar'].'</b>' : '' ?>
                                        <hr>
                                    <hr>
                                    <button class="btn btn-danger pull-right my-3 deleteConfirm" id="envios-<?= $direccion['data']['id'] ?>-<?= $direccion['data']['usuario'] ?>-delete"><?= $lang['detalle_envios']["eliminar"] ?></button>
                                </div>
                            </div>
                        </div>
                <?php }
                } ?>
                <div class="card">
                    <div class="card-header" id="head-nuevo">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#direccion-nuevo" aria-expanded="true" aria-controls="direccion-nuevo">
                                <?= $lang['detalle_envios']["nueva_direccion"] ?>
                            </button>
                        </h2>
                    </div>
                    <div id="direccion-nuevo" class="collapse <?= (!$direcciones) ? 'show' : '' ?>" aria-labelledby="head-nuevo" data-parent="#datosEnvio">
                        <div class="card-body">

                            <form id="form-envios_pedidos-new">
                                <div class="row">
                                    <div class="col-md-6 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["nombre"] ?>:</label>
                                        <input type="text" placeholder="<?= $lang['detalle_envios']["nombre"] ?>" name="nombre" value="" class="form-control" />
                                    </div>
                                    <div class="col-md-6 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["apellido"] ?>:</label>
                                        <input type="text" placeholder="<?= $lang['detalle_envios']["apellido"] ?>" name="apellido" value="" class="form-control" />
                                    </div>
                                    <div class="col-md-12 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["email"] ?>:</label>
                                        <input type="text" placeholder="<?= $lang['detalle_envios']["email"] ?>" name="email" value="" class="form-control" />
                                    </div>
                                    <div class="col-md-6 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["telefono"] ?>:</label>
                                        <input type="text" placeholder="<?= $lang['detalle_envios']["telefono"] ?>" name="telefono" value="" class="form-control" />
                                    </div>
                                    <div class="col-md-6 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["celular"] ?>:</label>
                                        <input type="text" placeholder="<?= $lang['detalle_envios']["celular"] ?>" name="celular" value="" class="form-control" />
                                    </div>
                                    <div class="col-md-4 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["provincia"] ?><br>
                                            <select id='provincia' data-url="<?= URL ?>" class="form-control" name="provincia" data-validation="required" required>
                                            <option value=""></option>
                                            <?php $f->provincias(); ?>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="col-md-4 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["localidad"] ?><br>
                                            <select id='localidad' class="form-control" name="localidad" data-validation="required" required>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="col-md-4 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["postal"] ?>:</label>
                                        <input type="text" placeholder="<?= $lang['detalle_envios']["postal"] ?>" name="postal" value="" class="form-control" />
                                    </div>
                                    <div class="col-md-4 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["calle"] ?>:</label>
                                        <input type="text" placeholder="<?= $lang['detalle_envios']["calle"] ?>" name="calle" value="" class="form-control" />
                                    </div>
                                    <div class="col-md-4 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["numero"] ?>:</label>
                                        <input type="number" placeholder="<?= $lang['detalle_envios']["numero"] ?>" name="numero" value="" class="form-control" />
                                    </div>
                                    <div class="col-md-4 mt-10">
                                        <label class="bold fs-12"><?= $lang['detalle_envios']["piso"] ?>:</label>
                                        <input type="text" placeholder="<?= $lang['detalle_envios']["piso"] ?>" name="piso" value="" class="form-control" />
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-md-12 col-xs-12 mt-10 mb-10 fs-15 text-uppercase">
                                        <hr />
                                        <input type="checkbox" name="similar" value="1">
                                        <?= $lang['detalle_envios']["cambiar_similar"] ?>
                                        <i class="d-block fs-12 normal">* <?= $lang['detalle_envios']["rta_cambiar_similar"] ?></i>
                                    </label>
                                    <div class="col-md-12">
                                        <hr />

                                        <button class="btn btn-primary pull-right text-uppercase" onclick="modificarDetallesPedidos('envios_pedidos','new')"><?= $lang['detalle_envios']["guardar"] ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($type == 'pagos') {
        $direcciones_pagos =  $pedidos_pagos->list(["usuario" => $_SESSION["usuarios"]['cod'], "soft_delete" => 0]);
    ?>
        <div class="tab-pane fade show active" id="pagos" role="tabpanel" aria-labelledby="pedidos-tab">
            <div class="accordion" id="datosPagos">
                <?php if ($direcciones_pagos) {
                    foreach ($direcciones_pagos as $key => $direccion) {
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
                                        <?= ($direccion['data']['factura']) ? '<b>'. $lang['detalle_pagos']['solicite-factura'].'</b>' : '' ?>
                                        <hr>

                                    <button class="btn btn-danger pull-right my-3  deleteConfirm" id="pagos-<?= $direccion['data']['id'] ?>-<?= $direccion['data']['usuario'] ?>-delete">Eliminar</button>
                                </div>
                            </div>
                        </div>
                <?php }
                } ?>
                <div class="card">
                    <div class="card-header" id="head-nuevo">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#direccion-nuevo" aria-expanded="true" aria-controls="direccion-nuevo">
                                <?= $lang['detalle_pagos']["nueva_direccion"] ?>
                            </button>
                        </h2>
                    </div>
                    <div id="direccion-nuevo" class="collapse <?= (!$direcciones) ? 'show' : '' ?>" aria-labelledby="head-nuevo" data-parent="#datosPagos">
                        <div class="card-body">
                            <form id="form-pagos_pedidos-new">

                                <div class="row">
                                    <div class="col-md-6 col-xs-12">
                                        <label><?= $lang['detalle_pagos']["nombre"] ?>:</label>
                                        <input class="form-control mb-10" type="text" value="" name="nombre" data-validation="required" required />
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <label><?= $lang['detalle_pagos']["apellido"] ?>:</label>
                                        <input class="form-control  mb-10" type="text" value="" name="apellido" data-validation="required" required />
                                    </div>
                                    <div class="col-md-8 col-xs-12">
                                        <label><?= $lang['detalle_pagos']["email"] ?>:</label>
                                        <input class="form-control  mb-10" type="email" value="" name="email" data-validation="required" required />
                                    </div>
                                    <div class="col-md-4 col-xs-12">
                                        <label><?= $lang['detalle_pagos']["dni"] ?>:</label>
                                        <input class="form-control mb-10" type="text" value="" name="documento" data-validation="required" required />
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <label><?= $lang['detalle_pagos']["telefono"] ?>:</label>
                                        <input class="form-control mb-10" type="text" value="" name="telefono" data-validation="required" required />
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <label><?= $lang['detalle_pagos']["celular"] ?>:</label>
                                        <input class="form-control mb-10" type="text" value="" name="celular" data-validation="required" required />
                                    </div>
                                    <div class="col-md-4 col-xs-12 form-row-wide">
                                        <label><?= $lang['detalle_pagos']["provincia"] ?></label>
                                        <!-- Dropdown -->
                                        <select id='provincia' data-url="<?= URL ?>" class="form-control" name="provincia" data-validation="required" required>
                                        <option value=""></option>
                                            <?php
                                            $f->provincias();
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-xs-12 form-row-wide">
                                        <label><?= $lang['detalle_pagos']["localidad"] ?></label>
                                        <!-- Dropdown -->
                                        <select id='localidad' class="form-control" name="localidad" data-validation="required" required>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label><?= $lang['detalle_pagos']["postal"] ?>:</label>
                                        <input class="form-control mb-10" type="text" placeholder="<?= $lang['detalle_pagos']["postal"] ?>" name="postal" value="" data-validation="required" required />
                                    </div>
                                    <div class="col-md-4 col-xs-12">
                                        <label><?= $lang['detalle_pagos']["calle"] ?>:</label>
                                        <input class="form-control mb-10" type="text" value="" name="calle" data-validation="required" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label><?= $lang['detalle_pagos']["numero"] ?>:</label>
                                        <input class="form-control mb-10" type="number" placeholder="<?= $lang['detalle_pagos']["numero"] ?>" name="numero" value="" data-validation="required" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label><?= $lang['detalle_pagos']["piso"] ?>:</label>
                                        <input class="form-control mb-10" type="text" placeholder="<?= $lang['detalle_pagos']["piso"] ?>" name="piso" value="" />
                                    </div>
                                    <label class="col-md-12 col-xs-12 mt-10 mb-10 fs-15 text-uppercase">
                                        <hr />
                                        <input type="checkbox" name="factura" value="1">
                                        <?= $lang['detalle_pagos']["factura_a"] ?>
                                    </label>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <hr />
                                        <button class="btn btn-primary pull-right text-uppercase" onclick="modificarDetallesPedidos('pagos_pedidos','new')"><?= $lang['detalle_pagos']["guardar"] ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    $(".deleteConfirm").on("click", function(e) {
        e.preventDefault();

        Swal.fire({
            title: "¿ESTÁS SEGURO DE ELIMINAR ESTE REGISTRO?",
            text: "No podrás recuperar este registro, una vez borrado.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, eliminar",
            cancelButtonText: "Cancelar",
        }).then((willDelete) => {
            if (willDelete.isConfimed) {
                $.ajax({
                    url: url + "/api/pedidos/deleteDetail.php",
                    type: "POST",
                    data: {
                        id: $(this).attr("id"),
                    },
                    success: function(data) {
                        data = JSON.parse(data);
                        if (data["status"]) {
                            $("#d-" + data["id"]).remove();
                            Swal.fire({
                                icon: "success",
                                title: "¡FUE ELIMINADO EXITOSAMENTE!",
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "¡OCURRIO UN ERROR!",
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }
                    },
                });
            }
        });
    });

    function modificarDetallesPedidos(type, id) {
        event.preventDefault();

        console.log(type, id);
        var form = $("#form-" + type + '-' + id).serialize();
        console.log(form);
        $.ajax({
            url: url + "/api/user/modificarDetallesPedidos.php?type=" + type + "&id=" + id,
            type: "POST",
            data: form,
            success: function(data) {
                data = JSON.parse(data);

            },
            error: function() {
                alertSide("Ocurrio un error, vuelva a recargar la página.");
            },
        });
    }
</script>