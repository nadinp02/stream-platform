<?php
$envios = new Clases\Envios();
$imagenes = new Clases\Imagenes();
$idiomas = new Clases\Idiomas();

$cod = isset($_GET["cod"]) ? $funciones->antihack_mysqli($_GET["cod"]) : '';
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];


$envios->set("cod", $cod);
$envios->set("idioma", $idiomaGet);
$envios_ = $envios->view();
$imagen = $imagenes->view($envios_["data"]["cod"]);

if (isset($_POST["modificar"])) {
    $count = 0;
    $cod = $envios_['data']["cod"];
    $data["titulo"] = isset($_POST["titulo"]) ? $funciones->antihack_mysqli($_POST["titulo"]) : '';
    $data["descripcion"] = isset($_POST["descripcion"]) ? $funciones->antihack_mysqli($_POST["descripcion"]) : '';
    $data["opciones"] = isset($_POST["opciones"]) ? $funciones->antihack_mysqli($_POST["opciones"]) : '';
    $data["peso"] = isset($_POST["peso"]) ? $funciones->antihack_mysqli($_POST["peso"]) : '';
    $data["precio"] = isset($_POST["precio"]) ? $funciones->antihack_mysqli($_POST["precio"]) : '';
    $data["estado"] = isset($_POST["estado"]) ? $funciones->antihack_mysqli($_POST["estado"]) : '';
    $data["limite"] = isset($_POST["limite"]) ? $funciones->antihack_mysqli($_POST["limite"]) : '';
    $data["localidad"] = isset($_POST["localidad"]) ? $funciones->antihack_mysqli($_POST["localidad"]) : '';
    $data["tipo_usuario"] = isset($_POST["tipo_usuario"]) ? $funciones->antihack_mysqli($_POST["tipo_usuario"]) : "'0'";
    $data["idioma"] = $idiomaGet;
    $envios->edit($data, ["cod = '$cod'"]);
    if (!empty($_FILES['files']['name'][0])) {
        $imagenes->resizeImages($cod, $_FILES['files'], "assets/archivos/recortadas", $funciones->normalizar_link(strip_tags($_POST["titulo"])), [$idiomaGet]);
    }
    $funciones->headerMove(URL_ADMIN . "/index.php?op=envios&accion=ver&idioma=$idiomaGet");
}
?>

<div class="mt-20 ">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-uppercase text-center">
                Envios
            </h4>
            <hr style="border-style: dashed;">
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="post" class="row" enctype="multipart/form-data">
                    <label class="col-md-4">
                        Título:<br />
                        <input type="text" value="<?= $envios_['data']["titulo"] ?>" name="titulo" required>
                    </label>
                    <label class="col-md-4">Estado:<br />
                        <select name="estado" required>
                            <option value="1" <?= ($envios_['data']['estado'] == 1) ? "selected" : "" ?>>Activado
                            </option>
                            <option value="0" <?= ($envios_['data']['estado'] == 0) ? "selected" : "" ?>>Desactivado
                            </option>
                        </select>
                    </label>
                    <label class="col-md-4">Tipo de Usuario:<br />
                        <select name="tipo_usuario" required>
                            <option value="0" <?= ($envios_['data']['tipo_usuario'] == 0) ? "selected" : "" ?>>Ambos
                            </option>
                            <option value="1" <?= ($envios_['data']['tipo_usuario'] == 1) ? "selected" : "" ?>>Minorista
                            </option>
                            <option value="2" <?= ($envios_['data']['tipo_usuario'] == 2) ? "selected" : "" ?>>Mayorista
                            </option>
                        </select>
                    </label>
                    <label class="col-md-3">
                        Peso:<br />
                        <input value="<?= $envios_['data']["peso"] ?>" min="0" name="peso" type="text" required />
                    </label>
                    <label class="col-md-3">
                        Precio:<br />
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="float" class="form-control" min="0" value="<?= $envios_['data']["precio"] ?>" name="precio" required>
                        </div>
                    </label>
                    <label class="col-md-3">Limite:<br />
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="float" class="form-control" value="<?= $envios_['data']["limite"] ?>" name="limite">
                        </div>
                    </label>
                    <label class="col-md-3">Pedir datos adicionales:<br />
                        <select name="opciones" required>
                            <option value="0" <?= ($envios_['data']['opciones'] == 0) ? "selected" : '' ?>>Desactivado</option>
                            <option value="2" <?= ($envios_['data']['opciones'] == 2) ? "selected" : '' ?>>Hora y Fecha especifica</option>
                            <option value="3" <?= ($envios_['data']['opciones'] == 3) ? "selected" : '' ?>>Hora y Rango Fecha</option>
                        </select>
                    </label>
                    <label class="col-md-12">Descripción:<br />
                        <input type="text" name="descripcion" value="<?= $envios_['data']["descripcion"] ?>">
                    </label>


                    <label class="col-md-5">Incluir sólo para las localidades:<br />
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                            </div>
                            <input class="form-control" list="cityList" id="city">
                        </div>
                        <datalist id="cityList"></datalist>
                    </label>
                    <label class="col-md-12">
                        <input data-beautify="true" data-delimiter="|" value="<?= $envios_["data"]["localidad"] ?>" name="localidad" type="text" class="cityTags">
                    </label>
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                    <?php $imagenes->buildEditImagesAdmin($imagen, false) ?>
                    </div>
                    <div class="col-md-12 mt-20">
                        <input type="submit" class="btn btn-primary btn-block" name="modificar" value="Modificar Envio" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#idiomasCheckBox').hide();

    $("#pes").inputSpinner()

    $(function() {

        refreshCityData()

        $('.cityTags').tokenfield();
        var cityTags = [];

        $("#city").keyup(function() {
            $('#cityList option').each(function() {
                if ($(this).val() == $("#city").val()) {
                    $("#city").val('');
                    cityTags = $('.cityTags').tokenfield('getTokens');
                    cityTags.push({
                        value: $(this).val(),
                        label: $(this).attr('label')
                    });
                    $('.cityTags').tokenfield('setTokens', cityTags);
                }
            });
        });

        function refreshCityData() {
            let cities = $.getJSON('../.utils/localidades.json',
                function(data) {
                    $.each(data, function(index, value) {
                        $("#cityList").append('<option value="' + value.value + '" label="' + value.value + '">');
                    });
                });
        }
    });
</script>