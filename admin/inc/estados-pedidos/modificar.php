<?php
$estados = new Clases\EstadosPedidos();
$idiomas = new Clases\Idiomas();
$id = isset($_GET["id"]) ? $funciones->antihack_mysqli($_GET["id"]) : '';
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : '';
$idiomasData = $idiomas->list(["cod != '$idiomaGet'"], "", "");

$estados->set("idioma", $idiomaGet);
if (!empty($id)) {
    $estado = $estados->view($id);
    $array['id'] = $id;
}
if (isset($_POST["modificar"])) {
    unset($_POST["modificar"]);
    if (isset($_POST["estado"]) && $_POST["estado"] != 0) {
        $array['estado'] = $funciones->antihack_mysqli($_POST["estado"]);
    }
    $array["titulo"] = isset($_POST["titulo"]) ? $funciones->antihack_mysqli($_POST["titulo"]) : '';
    $array["asunto"] = isset($_POST["asunto"]) ? $funciones->antihack_mysqli($_POST["asunto"]) : '';
    $array["mensaje"] = isset($_POST["mensaje"]) ? $funciones->antihack_mysqli($_POST["mensaje"]) : '';
    $array["idioma"] = $idiomaGet;
    $array["enviar"] = isset($_POST["enviar"]) ? '1' : '0';
    if (isset($_GET["id"])) {
        $estados->edit($array,["id = '$id'", "idioma = '$idiomaGet'"]);
    } else {
        $estados->add($array);
        if (isset($_POST["idiomasInput"])) {
            foreach ($_POST["idiomasInput"] as $idioma_) {
                $array["idioma"] = $idioma_;
                $estados->add($array);
            }
        }
    }
    $funciones->headerMove(URL_ADMIN . "/index.php?op=estados-pedidos&accion=ver&idioma=$idiomaGet");
}
?>

<div class="mt-20 ">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-uppercase text-center">
                Modificar Estados de Pedidos
            </h4>
            <hr style="border-style: dashed;">
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="post" class="row" enctype="multipart/form-data">
                    <label class="col-md-6">
                        Título:<br />
                        <input type="text" value="<?= isset($estado['data']["titulo"]) ? $estado['data']["titulo"] : '' ?>" name="titulo" required>
                    </label>
                    <label class="col-md-4">Estado:<br />
                        <select name="estado" required>
                            <option value="1" <?= ((isset($estado['data']["estado"]) && $estado['data']['estado'] == 1) || empty($estado['data'])) ? "selected" : ''; ?>>Pendiente </option>
                            <option value="2" <?= (isset($estado['data']["estado"]) && $estado['data']['estado'] == 2) ? "selected" : ''; ?>>Aprobado</option>
                            <option value="3" <?= (isset($estado['data']["estado"]) && $estado['data']['estado'] == 3) ? "selected" : ''; ?>>Rechazado</option>
                        </select>
                    </label>
                    <label class="col-md-2">
                        <div class="custom-control custom-switch custom-switch-glow mt-20">
                            <span class="invoice-terms-title"> Enviar Email</span>
                            <input name="enviar" type="checkbox" id="enviar" class="custom-control-input" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" value="1" <?= (isset($estado['data']["enviar"]) && $estado['data']["enviar"] == 1) ? "checked" : "" ?>>
                            <label class="custom-control-label" for="enviar">
                            </label>
                        </div>
                    </label>
     
                    <label class="col-md-12" style="padding: 0px">
                        <div class="collapse" id="collapseExample">
                            <div class="card card-body" style="padding: 0px">
                                <label class="col-md-12 mt-20">
                                    <h6>Se enviará el siguiente email cuando tu pedido entre en este estado.</h6>
                                </label>
                                <label class="col-md-12 mt-10">
                                    Asunto:<br />
                                    <input type="text" name="asunto" value="<?= isset($estado['data']["asunto"]) ? $estado['data']["asunto"] : '' ?>">
                                </label>
                                <label class="col-md-12 mt-10">
                                    Mensaje:<br />
                                    <textarea name="mensaje" class="ckeditorTextarea" required><?= isset($estado['data']["mensaje"]) ? $estado['data']["mensaje"] : '' ?></textarea>
                                </label>
                            </div>
                        </div>
                    </label>
                    <?php if (!isset($estado['data']["titulo"])) { ?>
                        <?php if (count($idiomasData) >= 1) ?>
                        <div class="col-md-12">
                            <div class="btn btn-primary mt-10 mb-10" onclick="$('#idiomasCheckBox').show()">Republicar contenido en otros idiomas</div>
                            <div id="idiomasCheckBox">
                                <?php foreach ($idiomasData as $idiomaItem) { ?>
                                    <div class="ml-10">
                                        <label for="idioma<?= $idiomaItem['data']['cod'] ?>">
                                            <input type="checkbox" name="idiomasInput[]" value="<?= $idiomaItem['data']['cod'] ?>" id="idioma<?= $idiomaItem['data']['cod'] ?>"> <?= $idiomaItem['data']['titulo'] ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-md-12 mt-20">
                        <input type="submit" class="btn btn-primary btn-block" name="modificar" value="Modificar Estado" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#idiomasCheckBox').hide();

    if ($('#enviar').attr("checked") == "checked") {
        $('.collapse').addClass('show');
    }
</script>