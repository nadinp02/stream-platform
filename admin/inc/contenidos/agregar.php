<?php

$contenido = new Clases\Contenidos();
$idiomas = new Clases\Idiomas();
$area = new Clases\Area();
$imagenes = new Clases\Imagenes();
$categorias = new Clases\Categorias();
$funciones = new Clases\PublicFunction();
$opciones = new Clases\Opciones();
$opcionesValor = new Clases\OpcionesValor();

$getArea = isset($_GET["area"]) ? $funciones->antihack_mysqli($_GET["area"]) : '';
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];

$areaData = $area->list(["cod = '$getArea'"], '', '', $idiomaGet, true);
$idiomasData = $idiomas->list(["cod != '$idiomaGet'"], "", "");
$categoriasData = $categorias->list(["area = '$getArea'"], "titulo ASC", "", $idiomaGet);

$opcionesContenido = $opciones->list($idiomaGet, ["`opciones`.`area` = '$getArea'"], false, "");
$cod = substr(md5(uniqid(rand())), 0, 10);

if (isset($_POST["agregar"])) {
    unset($_POST["agregar"]);
    if (!isset($_POST["destacado"])) $_POST["destacado"] = "0";

    if (isset($_POST["idiomasInput"])) {
        $idiomasInputPost =  $_POST["idiomasInput"];
        $idiomasInputPost[] = $idiomaGet;
    } else {
        $idiomasInputPost = [$idiomaGet];
    }

    $opcionesData = isset($_POST['opcion']) ? $_POST['opcion'] : [];
    $opcionesSelect = isset($_POST["opcion-select"]) ? $_POST["opcion-select"] : [];

    unset($_POST["idiomasInput"]);
    unset($_POST["opcion"]);
    unset($_POST["opcion-select"]);

    $cod = $funciones->antihack_mysqli($_POST["cod"]);
    $array = $funciones->antihackMulti($_POST);

    if (isset($idiomasInputPost) && !empty($idiomasInputPost)) {
        foreach ($idiomasInputPost as $idiomasInputItem) {
            if (isset($opcionesSelect)) {
                foreach ($opcionesSelect as $key => $selectItem) {
                    $implodeArray = implode("|", $selectItem);
                    if ($implodeArray == "-- Sin seleccionar --" || $implodeArray == NULL) continue;
                    $opcionesValor->set("relacion_cod", $cod);
                    $opcionesValor->set("idioma", $idiomasInputItem);
                    $opcionesValor->set("opcion_cod", $key);
                    $opcionesValor->set("valor", $implodeArray);
                    $exist = $opcionesValor->checkIfExist();
                    $opcionesValor->set("cod", substr(md5(uniqid(rand())), 0, 10));
                    $opcionesValor->add();
                }
            }
            if (isset($opcionesData)) {
                foreach ($opcionesData as $key => $optionData) {
                    if ($optionData == "-- Sin seleccionar --" || $optionData == NULL) continue;
                    $opcionesValor->set("relacion_cod", $cod);
                    $opcionesValor->set("idioma", $idiomasInputItem);
                    $opcionesValor->set("opcion_cod", $key);
                    $opcionesValor->set("valor", $optionData);
                    $opcionesValor->set("cod", substr(md5(uniqid(rand())), 0, 10));
                    $opcionesValor->add();
                }
                unset($array['opcion']);
            }
            $array["idioma"] = $idiomasInputItem;
            $contenido->add($array);
        }
    }

    if (isset($_FILES['files'])) {
        $imagenes->resizeImages($cod, $_FILES['files'], "assets/archivos/recortadas", $funciones->normalizar_link(strip_tags($array["titulo"])), $idiomasInputPost);
    }

    $funciones->headerMove(URL_ADMIN . "/index.php?op=contenidos&accion=ver&area=" . $areaData['data']['cod'] . "&idioma=" . $idiomaGet);
}
?>

<div class="mt-20 ">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-uppercase text-center">
                <?= $areaData['data']['titulo'] ?>
            </h4>
            <hr style="border-style: dashed;">
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="post" class="row" enctype="multipart/form-data">
                    <hr />
                    <input type="hidden" name="area" value="<?= $getArea ?>">
                    <label class="col-md-5">Título
                        <input type="text" name="titulo" required>
                    </label>
                    <label class="col-md-5">Subtitulo
                        <input type="text" id="sub" name="subtitulo">
                    </label>
                    <label class="col-md-2">Código:<br />
                        <input type="text" name="cod" maxlength="10" value="<?= $cod ?>">
                    </label>
                    <label class="col-md-4">
                        Categoría:<br />
                        <select name="categoria">
                            <option value="" selected>-- categorías --</option>
                            <?php
                            foreach ($categoriasData as $categoria) {
                                echo "<option value='" . $categoria["data"]["cod"] . "'>" . mb_strtoupper($categoria["data"]["titulo"]) . "</option>";
                            }
                            ?>
                        </select>
                    </label>
                    <label class="col-md-4">
                        Subcategoría:<br />
                        <select name="subcategoria">
                            <option value="" selected>-- Sin subcategoría --</option>
                            <?php
                            foreach ($categoriasData as $categoria) {
                            ?>
                                <optgroup label="<?= mb_strtoupper($categoria["data"]['titulo']) ?>">
                                    <?php
                                    foreach ($categoria["subcategories"] as $subcategorias) {
                                        echo "<option value='" . $subcategorias["data"]["cod"] . "'>" . mb_strtoupper($subcategorias["data"]["titulo"]) . "</option>";
                                    }
                                    ?>
                                </optgroup>
                            <?php
                            }
                            ?>
                        </select>
                    </label>
                    <label class="col-md-3">Fecha:<br />
                        <input type="date" name="fecha" value="<?= date('Y-m-d') ?>">
                    </label>
                    <label class="col-md-1">Orden:<br />
                        <input type="text" name="orden" value="0">
                    </label>
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                    </div>
                    <label class="col-md-12">Contenido:<br />
                        <textarea name="contenido" class="ckeditorTextarea" required></textarea>
                    </label>
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                    </div>
                    <label class="col-md-12">Palabras claves dividas por ,<br />
                        <input type="text" name="keywords">
                    </label>
                    <label class="col-md-12">Descripción breve<br />
                        <textarea name="description"></textarea>
                    </label>
                    <br />
                    <label class="col-md-12">Link
                        <input type="text" id="link" name="link">
                    </label>
                    <br>

                    <label class="col-md-12">
                        <?php
                        $categoriaOption = '0';
                        $x = 0;
                        foreach ($opcionesContenido as $optionItem) {
                            if ($categoriaOption != $optionItem["data"]["categoria"]) {
                                if ($x != 0) echo "</div>";
                                echo '<div class="row"><div class="bold mb-10 col-md-12 mt-10">';
                                echo ($optionItem["data"]["categoria"] == NULL) ? "Sin Categoria" : $optionItem["data"]["categoria_titulo"];
                                echo "</div>";
                            }
                        ?>
                            <div class="<?= ($optionItem["data"]["tipo"] == "textarea") ? "my-2" : "col-sm-3" ?> col-12">
                                <h6 class="invoice-to"><?= $optionItem["data"]["titulo"] ?> <span style="font-size:10px!important">(<?= $optionItem["data"]["tipo_mostrar"] ?>)</span></h6>
                                <?php if ($optionItem["data"]["tipo"] == "text") { ?>
                                    <input type="text" class="form-control" name="opcion[<?= $optionItem["data"]["cod"] ?>]" value="">
                                <?php } ?>
                                <?php if ($optionItem["data"]["tipo"] == "textarea") { ?>
                                    <textarea class="ckeditorTextarea" name="opcion[<?= $optionItem["data"]["cod"] ?>]"></textarea>
                                <?php } ?>
                                <?php if ($optionItem["data"]["tipo"] == "int") { ?>
                                    <input type="number" class="form-control" name="opcion[<?= $optionItem["data"]["cod"] ?>]" value="">
                                <?php } ?>
                                <?php if ($optionItem["data"]["tipo"] == "boolean") { ?>
                                    <select class="form-control" name="opcion[<?= $optionItem["data"]["cod"] ?>]">
                                        <option>-- Sin seleccionar --</option>
                                        <option value="true">Si</option>
                                        <option value="false">No</option>
                                    </select>
                                <?php } ?>
                                <?php if ($optionItem["data"]["tipo"] == "select") { ?>
                                    <select class="form-control" id="select-<?= $optionItem['data']['cod'] ?>" onchange="getSelectedValues('<?= $optionItem['data']['cod'] ?>')" name="opcion-select[<?= $optionItem["data"]["cod"] ?>][]" <?= ($optionItem["data"]["multiple"] == "1") ? ' multiple ' : '' ?>>
                                        <?php foreach (explode("|", $optionItem["data"]["opciones"]) as $option) { ?>
                                            <option value="<?= $option ?>"><?= $option ?></option>
                                        <?php } ?>
                                    </select>
                                    <div id="div-<?= $optionItem["data"]["cod"] ?>"></div>
                                <?php } ?>
                            </div>
                        <?php
                            $categoriaOption = $optionItem["data"]["categoria"];
                            $x++;
                        }
                        ?>
                    </label>
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                    </div>
                    <label class="col-md-12">Imágenes:<br />
                        <input type="file" onchange="filePreview(this)" id="file" name="files[]" multiple="multiple" accept="image/*" />
                        <div id="preview-images" class="my-2"></div>
                    </label>
                    <?php
                    if (count($idiomasData) >= 1) { ?>
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
                        <input type="submit" class="btn btn-block btn-primary" name="agregar" value="Agregar" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#idiomasCheckBox').hide();
</script>