<?php
$funciones = new Clases\PublicFunction();
$idiomas = new Clases\Idiomas();
$opcionesProducto = new Clases\Opciones();
$area = new Clases\Area();
$categoria = new Clases\Categorias();

$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];
$idiomasData = $idiomas->list(["cod != '$idiomaGet'"], "", "");
$areas = $area->list([], "", "", $idiomaGet);

if (isset($_POST["agregar"])) {
    unset($_POST["agregar"]);
    if (isset($_POST["idiomasInput"])) {
        $idiomasInputPost =  $_POST["idiomasInput"];
        $idiomasInputPost[] = $idiomaGet;
    } else {
        $idiomasInputPost = [$idiomaGet];
    }
    unset($_POST["idiomasInput"]);
    $error = false;
    if (!$_POST["titulo"]) $error = true;
    if (!$_POST["tipo"]) $error = true;
    if (!$error) {
        $cod = isset($_POST["cod"]) ? $funciones->antihack_mysqli($_POST["cod"]) : "";
        $titulo = isset($_POST["titulo"]) ? $funciones->antihack_mysqli($_POST["titulo"]) : "";
        $tipo = isset($_POST["tipo"]) ? $funciones->antihack_mysqli($_POST["tipo"]) : "";
        $area = isset($_POST["area"]) ? $funciones->antihack_mysqli($_POST["area"]) : "";
        $multiple = isset($_POST["multiple"]) ? "1" : "0";
        $categoria = isset($_POST["categoria"]) ? $funciones->antihack_mysqli($_POST["categoria"]) : "";
        $filtro = isset($_POST["filtro"]) ? $funciones->antihack_mysqli($_POST["filtro"]) : "";
        $opciones = isset($_POST["opciones"]) ? $funciones->antihackMulti($_POST["opciones"]) : '';
        if (isset($idiomasInputPost) && !empty($idiomasInputPost) && !empty($area)) {
            foreach ($idiomasInputPost as $idiomasInputItem) {
                if (!empty($opciones)) $opciones = implode("|", $opciones);
                $opcionesProducto->set("cod", $cod);
                $opcionesProducto->set("titulo", $titulo);
                $opcionesProducto->set("tipo", $tipo);
                $opcionesProducto->set("idioma", $idiomasInputItem);
                $opcionesProducto->set("area", $area);
                $opcionesProducto->multiple = $multiple;
                $opcionesProducto->set("opciones", $opciones);
                $opcionesProducto->filtro = isset($filtro) ? '1' : '0';
                $opcionesProducto->set("categoria", $categoria);
                $opcionesProducto->add();
            }
        }
        $funciones->headerMove(URL_ADMIN . "/index.php?op=opciones-variables&accion=ver&idioma=$idiomaGet");
    } else {
        $funciones->headerMove(URL_ADMIN . "/index.php?op=opciones-variables&accion=ver&idioma=$idiomaGet&error=create");
    }
}
?>
<div>
    <div class="content-body mt-20">
        <div class="card">
            <div class="card-content">
                <div class=" agregar ">
                    <h4 class="card-title text-uppercase text-center">
                        AGREGAR OPCIONES O VARIABLES
                    </h4>
                    <hr style="border-style: dashed;">
                    <div class="clearfix"></div>
                    <div class="card-content">
                        <div class="card-body">
                            <form method="post" class="row" style="justify-content: center;" enctype="multipart/form-data">
                                <div class="col-md-3">Codigo
                                    <input type="text" name="cod" maxlength="10" value="<?= substr(md5(uniqid(rand())), 0, 10) ?>" required>
                                </div>
                                <div class="col-md-4">Título
                                    <input type="text" name="titulo" required>
                                </div>
                                <div class="col-md-3">Tipo
                                    <select name="tipo" onchange="checkTipeSelected()" required>
                                        <option value="">--- Seleccionar tipo ---</option>
                                        <option value="int">Numérico</option>
                                        <option value="text">Texto Simple</option>
                                        <option value="textarea">Texto Compuesto</option>
                                        <option value="select">Selector</option>
                                        <option value="boolean">Si/No</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    Multiple
                                    <div class="custom-control custom-switch custom-switch-glow ml-10 mt-10">
                                        <input name="multiple" type="checkbox" id="multiple" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="multiple"></label>
                                    </div>
                                </div>
                                <div class="col-md-12 hidden mt-10 mb-10" id="aditionals">
                                    Agregar opciones al selector
                                    <div class="row" id="options"></div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-primary" onclick="addOption()">Agregar opción</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">Área
                                    <select name="area" required>
                                        <option value="productos">Productos</option>
                                        <?php
                                        if (isset($areas)) {
                                            foreach ($areas as $areaItem) { ?>
                                                <option value="<?= $areaItem['data']['cod'] ?>"><?= $areaItem['data']['titulo'] ?></option>
                                        <?php }
                                        }
                                        ?>
                                        <option value="banners">Banners</option>
                                    </select>
                                </div>
                                <div class="col-md-6">Categoría
                                    <select name="categoria" id="categoriaSelect" class="text-uppercase select2">

                                    </select>
                                </div>
                                <div class="col-md-2">
                                    Usar como Filtro
                                    <div class="custom-control custom-switch custom-switch-glow ml-10 mt-10">
                                        <input name="filtro" type="checkbox" id="filtro" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="filtro"></label>
                                    </div>
                                </div>
                                <?php if (count($idiomasData) >= 1) { ?>
                                    <div class="col-md-12">
                                        <div class="btn btn-primary mt-10 mb-10" onclick="$('#idiomasCheckBox').show()">Republicar opción en otros idiomas</div>
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
                                <div class="col-12 mt-20">
                                    <input type="submit" class="btn btn-block btn-primary " name="agregar" value="Crear" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function checkTipeSelected() {
        var tipo = $('select[name="tipo"]').val();
        if (tipo == 'select') {
            $('#aditionals').removeClass('hidden');
            $('#options').html('<div class="col-md-2"><input type="text" class="form-control mb-10" name="opciones[]" placeholder="Opciones"></div>');
        } else {
            $('#aditionals').addClass('hidden');
            $('#options').html('');
        }
    }

    function addOption() {
        $('#options').append('<div class="col-md-2"><input type="text" class="form-control mb-10" name="opciones[]" placeholder="Opciones"></div>');
    }
    $('#idiomasCheckBox').hide();
    $(window).on("load", function() {
        addOptionCategory();
    });
    $('#areaSelect').on("change", function() {
        addOptionCategory();
    });

    function addOptionCategory() {
        var option = '<option class="text-uppercase" value="">--sin categoria--</option>';
        $("#categoriaSelect").html('');

        var area = $("#areaSelect").val();
        $.ajax({
            url: url_admin + '/api/opcionesVariables/getCategory.php',
            type: "GET",
            data: {
                area: area,
                idioma: "<?= $idiomaGet ?>",
            },
            success: (data) => {
                var data = JSON.parse(data);
                if (data["status"]) {

                    data.categories.forEach((cat) => {
                        option += ' <optgroup class="text-uppercase" label="' + cat['data']['area'].toUpperCase() + ' - ' + cat['data']['titulo'].toUpperCase() + '">';
                        option += ' <option class="text-uppercase" value="' + cat['data']['cod'] + '">' + cat['data']['titulo'].toUpperCase() + '</option>';
                        if (cat['subcategories'].length > 0) {
                            cat['subcategories'].forEach((subCat) => {
                                option += ' <option class="text-uppercase" value="' + subCat['data']['cod'] + '">' + cat['data']['titulo'].toUpperCase() + ' - ' + subCat['data']['titulo'].toUpperCase() + '</option>';
                                if (subCat['tercercategories'].length > 0) {
                                    subCat['tercercategories'].forEach((tercerCat) => {
                                        option += ' <option class="text-uppercase" value="' + tercerCat['data']['cod'] + '">' + cat['data']['titulo'].toUpperCase() + ' - ' + subCat['data']['titulo'].toUpperCase() + ' - ' + tercerCat['data']['titulo'].toUpperCase() + '</option>';
                                    })
                                }
                            })
                        }
                        option += ' </optgroup>';
                    })
                    $("#categoriaSelect").append(option);
                }
            }
        });
    };
</script>