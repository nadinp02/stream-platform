<?php
$pagos = new Clases\Pagos();
$config = new Clases\Config();
$estadoPedido = new Clases\EstadosPedidos();
$imagenes = new Clases\Imagenes();
$idiomas = new Clases\Idiomas();

$cod = isset($_GET["cod"]) ? $funciones->antihack_mysqli($_GET["cod"]) : '';
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];

$estadoData = $estadoPedido->list(["idioma = '" . $idiomaGet . "'"], "", "");
$estadosPorEstado = [];
foreach ($estadoData as $estado) {
    $estadosPorEstado[$estado['data']['estado']][] = $estado;
}
$pagos->set("cod", $cod);
$pagos->set("idioma", $idiomaGet);
$pagos_ = $pagos->view();
$payments = $config->listPayment();
$imagen = $imagenes->view($pagos_["data"]["cod"]);

if (isset($_POST["modificar"])) {
    $pagos->set("cod", $pagos_['data']["cod"]);
    $pagos->set("titulo", isset($_POST["titulo"]) ?  $funciones->antihack_mysqli($_POST["titulo"]) : '');
    $pagos->set("leyenda", isset($_POST["leyenda"]) ?  $funciones->antihack_mysqli($_POST["leyenda"]) : '');
    $pagos->set("estado", isset($_POST["estado"]) ?  $funciones->antihack_mysqli($_POST["estado"]) : '');
    $pagos->set("tipo", isset($_POST["tipo"]) ?  $funciones->antihack_mysqli($_POST["tipo"]) : '');
    $pagos->set("estado_pendiente", isset($_POST["estado_pendiente"]) ?  $funciones->antihack_mysqli($_POST["estado_pendiente"]) : '');
    $pagos->set("estado_aprobado", isset($_POST["estado_aprobado"]) ?  $funciones->antihack_mysqli($_POST["estado_aprobado"]) : '');
    $pagos->set("estado_rechazado", isset($_POST["estado_rechazado"]) ?  $funciones->antihack_mysqli($_POST["estado_rechazado"]) : '');
    $pagos->set("idioma", $idiomaGet);
    $pagos->set("cuotas", isset($_POST["cuotas"]) ? $funciones->antihack_mysqli(intval($_POST["cuotas"])) : 0);

    $pagos->monto = isset($_POST["monto"]) ? $funciones->antihack_mysqli($_POST["monto"]) : 0;
    $pagos->set("defecto", isset($_POST["defecto"]) ?  $funciones->antihack_mysqli($_POST["defecto"]) : '');
    $pagos->minimo = !empty($_POST["minimo"]) ? $_POST["minimo"] : 0;
    $pagos->maximo = !empty($_POST["maximo"]) ? $_POST["maximo"] : 0;
    $pagos->entrega = !empty($_POST["entrega"]) ? $_POST["entrega"] : 0;
    $pagos->set("tipo_usuario", isset($_POST["tipo_usuario"]) ? $funciones->antihack_mysqli($_POST["tipo_usuario"]) : '');
    $pagos->acumular = isset($_POST["acumular"]) ? $funciones->antihack_mysqli($_POST["acumular"]) : 0;
    $pagos->desc_usuario = isset($_POST["desc_usuario"]) ? $funciones->antihack_mysqli($_POST["desc_usuario"]) : 0;
    $pagos->desc_cupon = isset($_POST["desc_cupon"]) ? $funciones->antihack_mysqli($_POST["desc_cupon"]) : 0;
    $pagos->edit();

    if (!empty($_FILES['files']['name'][0])) {
        $imagenes->resizeImages($cod, $_FILES['files'], "assets/archivos/recortadas", $funciones->normalizar_link(strip_tags($_POST["titulo"])), [$idiomaGet]);
    }
    $funciones->headerMove(URL_ADMIN . "/index.php?op=pagos&accion=ver&idioma=$idiomaGet");
}
?>
<div class="mt-20 card">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-uppercase text-center">
                Pagos
            </h4>
            <hr style="border-style: dashed;">
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="post" class="row" enctype="multipart/form-data">
                    <label class="mt-10 mb-10 col-md-6">Método de pago:<br />
                        <input type="text" name="titulo" value="<?= $pagos_['data']["titulo"] ? $pagos_['data']["titulo"] : '' ?>" required>
                    </label>
                    <label class="mt-10 mb-10 col-md-3">Monto de Compra Minimo:<br />
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="float" class="form-control" name="minimo" value="<?= $pagos_['data']["minimo"] ? $pagos_['data']["minimo"] : '' ?>">
                        </div>
                    </label>
                    <label class="mt-10 mb-10 col-md-3">Monto de Compra Maximo:<br />
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="float" class="form-control" name="maximo" value="<?= $pagos_['data']["maximo"] ? $pagos_['data']["maximo"] : '' ?>">
                        </div>
                    </label>
                    <label class="mt-10 mb-10 col-md-12">Descripción del método de pago:<br />
                        <textarea name="leyenda"><?= $pagos_['data']["leyenda"] ? $pagos_['data']["leyenda"] : '' ?></textarea>
                    </label>
                    <label class="mt-10 mb-10 col-md-4">
                        Estado
                        <select name="estado" class="form-control" required>
                            <option value="1" <?= ($pagos_['data']['estado'] == 1) ? "selected" : ''; ?>>Activo </option>
                            <option value="0" <?= ($pagos_['data']['estado'] == 0) ? "selected" : '' ?>>Desactivado </option>
                        </select>
                    </label>
                    <label class="mt-10 mb-10 col-md-4">
                        Tipo de pago online:
                        <select name="tipo" class="form-control">
                            <option value="" <?= ($pagos_['data']['tipo'] == '') ? "selected" : '' ?>> --- Sin elegir --- </option>
                            <?php
                            if (!empty($payments)) {
                                foreach ($payments as $payment) {
                            ?>
                                    <option value="<?= $payment['data']['id']; ?>" <?= ($pagos_['data']['tipo'] == $payment['data']['id']) ? "selected" : '' ?>>
                                        <?= $payment['data']['empresa']; ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </label>
                    <label class="mt-10 mb-10 col-md-4">Tipo Usuario:<br />
                        <select name="tipo_usuario" required>
                            <option value="0" <?= ($pagos_['data']['tipo_usuario'] == 0) ? "selected" : '' ?>>Ambos </option>
                            <option value="1" <?= ($pagos_['data']['tipo_usuario'] == 1) ? "selected" : '' ?>>Minorista </option>
                            <option value="2" <?= ($pagos_['data']['tipo_usuario'] == 2) ? "selected" : '' ?>>Mayorista </option>
                        </select>
                    </label>
                    <label class="mt-10 mb-10 col-md-3">
                        Defecto:
                        <select name="defecto" class="form-control" required>
                            <?php foreach ($estadoData as $estado) { ?>
                                <option value="<?= $estado['data']['id'] ?>" <?= $pagos_['data']['defecto'] == $estado['data']['id'] ? 'selected' : '' ?>><?= mb_strtoupper($estado['data']['titulo']) ?></option>
                            <?php } ?>
                        </select>
                    </label>
                    <label class="mb-20 col-md-3 mt-10">
                        Estado Pendiente:
                        <select name="estado_pendiente" class="form-control" required>
                            <?php foreach ($estadosPorEstado[1] as $estado) { ?>
                                <option value="<?= $estado['data']['id'] ?>" <?= ($pagos_['data']['estado_pendiente'] == $estado['data']['id']) ? "selected" : '' ?>><?= mb_strtoupper($estado['data']['titulo']) ?></option>
                            <?php } ?>
                        </select>
                    </label>
                    <label class="mb-20 col-md-3 mt-10">
                        Estado Aprobado:
                        <select name="estado_aprobado" class="form-control" required>
                            <?php foreach ($estadosPorEstado[2] as $estado) { ?>
                                <option value="<?= $estado['data']['id'] ?>" <?= ($pagos_['data']['estado_aprobado'] == $estado['data']['id']) ? "selected" : '' ?>><?= mb_strtoupper($estado['data']['titulo']) ?></option>
                            <?php } ?>
                        </select>
                    </label>
                    <label class="mb-20 col-md-3 mt-10">
                        Estado Rechazado:
                        <select name="estado_rechazado" class="form-control" required>
                            <?php foreach ($estadosPorEstado[3] as $estado) { ?>
                                <option value="<?= $estado['data']['id'] ?>" <?= ($pagos_['data']['estado_rechazado'] == $estado['data']['id']) ? "selected" : '' ?>><?= mb_strtoupper($estado['data']['titulo']) ?></option>
                            <?php } ?>
                        </select>
                    </label>
                    <label class="mt-10 mb-10 col-md-4">
                        Aumento o Descuento (%)<br />
                        <input data-suffix="%" value="<?= $pagos_['data']['monto'] ?>" min="-100" max="100" type="number" name="monto" onkeydown="return (event.keyCode!=13);" />
                    </label>
                    <label class="mt-10 mb-10 col-md-4">
                        Compra Parcial / Seña (%)<br />
                        <input data-suffix="%" value="<?= $pagos_['data']['entrega'] ?>" min="0" max="100" type="number" name="entrega" onkeydown="return (event.keyCode!=13);" />
                    </label>
                    <label class="col-md-4 mt-10">
                        Cuotas (solo aplica al método de pago decidir):
                        <select name="cuotas" class="form-control" required>
                            <option value="1" <?= ($pagos_['data']['cuotas'] == 1) ? "selected" : '' ?>>No</option>
                            <option value="3" <?= ($pagos_['data']['cuotas'] == 3) ? "selected" : '' ?>>3 Cuotas</option>
                            <option value="6" <?= ($pagos_['data']['cuotas'] == 6) ? "selected" : '' ?>>6 Cuotas</option>
                            <option value="12" <?= ($pagos_['data']['cuotas'] == 12) ? "selected" : '' ?>>12 Cuotas</option>
                            <option value="18" <?= ($pagos_['data']['cuotas'] == 18) ? "selected" : '' ?>>18 Cuotas</option>
                            <option value="24" <?= ($pagos_['data']['cuotas'] == 24) ? "selected" : '' ?>>24 Cuotas</option>
                            <option value="30" <?= ($pagos_['data']['cuotas'] == 30) ? "selected" : '' ?>>30 Cuotas</option>
                        </select>
                    </label>
                    <div class="col-md-12 mt-10 mb-10">
                        <label>
                            <span class="fs-15">¿Descuento acumulable?</span>
                        </label>
                        <div class="mt-6">
                            <div class="custom-control custom-switch custom-switch-glow ml-10 col-md-3">
                                <span class="invoice-terms-title"> Aplicar</span>
                                <input name="acumular" type="checkbox" id="acumular" class="custom-control-input" value="1" <?= ($pagos_['data']['acumular'] == 1) ? "checked" : "" ?>>
                                <label class="custom-control-label" for="acumular">
                                </label>
                            </div>
                        </div>
                        <i class="fs-14 d-block text-normal" style="color: red">* Al seleccionar esta opción el método de pago ejecutará beneficios si el producto ya posee descuentos, es decir que acumulará más descuentos.</i>
                    </div>
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                    <?php $imagenes->buildEditImagesAdmin($imagen, false) ?>
                    </div>
                    <div class="col-md-12 mt-20">
                        <input type="submit" class="btn btn-primary btn-block" name="modificar" value="Modificar Pago" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $("input[type='number']").inputSpinner()
</script>