<?php
$funciones = new Clases\PublicFunction();
$opciones_class = new Clases\Opciones();
$area = new Clases\Area();
$categoria = new Clases\Categorias();


$cod = isset($_GET["cod"]) ? $funciones->antihack_mysqli($_GET["cod"]) : '';
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];
if (empty($cod) || empty($idiomaGet)) $funciones->headerMove(URL_ADMIN . "/index.php?op=opciones-variables&accion=ver&error=edit");
$opcion = $opciones_class->list($idiomaGet, ["opciones.cod = '$cod'"], false, "", true);
$areas = $area->list([], "", "", $idiomaGet);
$categorias = $categoria->list(["`categorias`.`area` = 'opciones'"], '', '', $_SESSION['lang'], false, false);
$opcionesExplode = [];
if (!empty($opcion["data"]["opciones"])) $opcionesExplode = explode("|", $opcion["data"]["opciones"]);
if (isset($_POST["guardar"])) {
    unset($_POST["guardar"]);
    $opciones = "";
    if (isset($_POST["opciones"])) {
        $opciones = $_POST["opciones"];
        unset($_POST["opciones"]);
    }
    $array = $funciones->antihackMulti($_POST);
    if (!empty($opciones)) $opciones =  $funciones->antihackMulti($opciones);
    $opciones_class->set("cod", $cod);
    $opciones_class->set("titulo", $array["titulo"]);
    $opciones_class->set("tipo", $array["tipo"]);
    $opciones_class->set("area", $array["area"]);
    $opciones_class->multiple = isset($array["multiple"]) ? "1" : "0";
    $opciones_class->filtro = isset($array["filtro"]) ? "1" : "0";

    $opciones_class->set("categoria", $array["categoria"]);
    $opciones_class->set("opciones", ($array["tipo"] == "select") ? implode("|", array_filter($opciones)) : '');
    $opciones_class->set("idioma", $idiomaGet);
    $opciones_class->edit();
    $funciones->headerMove(URL_ADMIN . "/index.php?op=opciones-variables&accion=ver&idioma=$idiomaGet");
}
?>

<div class="content-body mt-20">
    <div class="card">
        <div class="card-content">
            <div class="mt-20">
                <h4>
                    MODIFICAR OPCIONES O VARIABLES
                </h4>
                <hr style="border-style: dashed;">
                <div class="clearfix"></div>

                <form method="post" class="row" style="justify-content: center;" enctype="multipart/form-data">
                    <div class="col-md-3">Codigo
                        <input type="text" value="<?= $opcion['data']["cod"] ?>" name="titulo" disabled>
                    </div>
                    <div class="col-md-4">Título
                        <input type="text" value="<?= $opcion['data']["titulo"] ?>" name="titulo">
                    </div>
                    <div class="col-md-3">Tipo
                        <select name="tipo" required onchange="checkTipeSelected()">
                            <option <?= ($opcion["data"]["tipo"] == "int") ? "selected" : '' ?> value="int">Numérico</option>
                            <option <?= ($opcion["data"]["tipo"] == "text") ? "selected" : '' ?> value="text">Texto Simple</option>
                            <option <?= ($opcion["data"]["tipo"] == "textarea") ? "selected" : '' ?> value="textarea">Texto Compuesto</option>
                            <option <?= ($opcion["data"]["tipo"] == "boolean") ? "selected" : '' ?> value="boolean">Si/No</option>
                            <option <?= ($opcion["data"]["tipo"] == "select") ? "selected" : '' ?> value="select">Selector</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        Multiple
                        <div class="custom-control custom-switch custom-switch-glow ml-10 mt-10">
                            <input name="multiple" type="checkbox" id="multiple" <?= ($opcion["data"]["multiple"] == "1") ? " checked " : "" ?> class="custom-control-input" value="1">
                            <label class="custom-control-label" for="multiple"></label>
                        </div>
                    </div>
                    <div class="col-md-12 hidden mt-10 mb-10" id="aditionals">
                        Opciones del selector
                        <div class="row" id="options">
                            <?php if ($opcion["data"]["tipo"] == "select") { ?>
                                <?php foreach ($opcionesExplode as $optionItem) { ?>
                                    <div class="col-md-2"><input type="text" class="form-control mb-10" value="<?= $optionItem ?>" name="opciones[]" placeholder="Opcion"></div>
                                <?php } ?>
                            <?php  } ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" onclick="addOption()">Agregar opción</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">Área
                        <select name="area" required>
                            <?php
                            if (isset($areas)) {
                                foreach ($areas as $areaItem) { ?>
                                    <option <?= ($opcion['data']["area"] == $areaItem["data"]["cod"]) ? "selected" : '' ?> value="<?= $areaItem['data']['cod'] ?>"><?= $areaItem['data']['titulo'] ?></option>
                            <?php }
                            }
                            ?>
                            <option value="banners">Banners</option>
                            <option value="productos">Productos</option>
                        </select>
                    </div>
                    <div class="col-md-6">Categoría
                        <select name="categoria" id="categoriaSelect" class="text-uppercase select2">


                        </select>
                    </div>
                    <div class="col-md-2">
                        Usar como Filtro
                        <div class="custom-control custom-switch custom-switch-glow ml-10 mt-10">
                            <input name="filtro" type="checkbox" id="filtro" class="custom-control-input" <?= ($opcion['data']["filtro"]) ? 'checked' : '' ?> value="1">
                            <label class="custom-control-label" for="filtro"></label>
                        </div>
                    </div>
                    <div class="col-12 mt-20">
                        <input type="submit" class="btn btn-block btn-primary" name="guardar" value="Modificar" />
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<script>
    function checkTipeSelected() {
        var tipo = $('select[name="tipo"]').val();
        if (tipo == 'select') {
            $('#aditionals').removeClass('hidden');
            if ($('#options').children().length == 0) {
                addOption();
            }
        } else {
            $('#aditionals').addClass('hidden');
        }
    }

    function addOption() {
        $('#options').append('<div class="col-md-2"><input type="text" class="form-control mb-10" name="opciones[]" placeholder="Opcion"></div>');
    }
    $(document).ready(function() {
        checkTipeSelected();
    });

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
                        var checkCat = (cat['data']['cod'] == '<?= $opcion['data']['categoria'] ?>') ? 'selected' : '';
                        option += ' <optgroup class="text-uppercase" label="' + cat['data']['area'].toUpperCase() + ' - ' + cat['data']['titulo'].toUpperCase() + '">';
                        option += ' <option ' + checkCat + ' class="text-uppercase" value="' + cat['data']['cod'] + '">' + cat['data']['titulo'].toUpperCase() + '</option>';
                        if (cat['subcategories'].length > 0) {
                            cat['subcategories'].forEach((subCat) => {
                                var checkSubCat = (subCat['data']['cod'] == '<?= $opcion['data']['categoria'] ?>') ? 'selected' : '';
                                option += ' <option ' + checkSubCat + ' class="text-uppercase" value="' + subCat['data']['cod'] + '">' + cat['data']['titulo'].toUpperCase() + ' - ' + subCat['data']['titulo'].toUpperCase() + '</option>';
                                if (subCat['tercercategories'].length > 0) {
                                    subCat['tercercategories'].forEach((tercerCat) => {
                                        var checkTercerCat = (tercerCat['data']['cod'] == '<?= $opcion['data']['categoria'] ?>') ? 'selected' : '';
                                        option += ' <option ' + checkTercerCat + ' class="text-uppercase" value="' + tercerCat['data']['cod'] + '">' + cat['data']['titulo'].toUpperCase() + ' - ' + subCat['data']['titulo'].toUpperCase() + ' - ' + tercerCat['data']['titulo'].toUpperCase() + '</option>';
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