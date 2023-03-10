<?php
$contenido = new Clases\Contenidos();
$area = new Clases\Area();
$imagenes = new Clases\Imagenes();
$categorias = new Clases\Categorias();
$opciones = new Clases\Opciones();
$opcionesValor = new Clases\OpcionesValor();
$getArea = isset($_GET["area"]) ? $funciones->antihack_mysqli($_GET["area"]) : '';
$cod = isset($_GET["cod"]) ? $funciones->antihack_mysqli($_GET["cod"]) : '';
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];

$categoriasOpciones = $categorias->list(array("area = 'opciones'"), "", "", $idiomaGet);
$opcionesContenido = $opciones->list($idiomaGet, ["`opciones`.`area` = '$getArea'"], true, $cod);

$areaData = $area->list(["cod = '$getArea'"], '', '', $idiomaGet, true);

$categoriasData = $categorias->list(["area = '$getArea'"], "titulo ASC", '', $idiomaGet);
$contenidoSingle = $contenido->list(["filter" => ["contenidos.cod = '$cod'"], "images" => 'all'], $idiomaGet, true);

if (isset($_POST["modificar"])) {
    unset($_POST["modificar"]);
    unset($_POST["idioma"]);
    unset($_POST["cod"]);
    if (!isset($_POST["destacado"])) $_POST["destacado"] = "0";
    $array = $funciones->antihackMulti($_POST);

    $opcionesData = isset($array['opcion']) ? $array['opcion'] : [];
    unset($array["opcion"]);
    if (isset($opcionesData)) {
        foreach ($opcionesData as $key => $optionData) {
            if ($optionData == "-- Sin seleccionar --" || $optionData == NULL) continue;
            $opcionesValor->set("relacion_cod", $cod);
            $opcionesValor->set("idioma", $idiomaGet);
            $opcionesValor->set("opcion_cod", $key);
            $opcionesValor->set("valor", $optionData);
            $exist = $opcionesValor->checkIfExist();
            $codOpcionValor = (!empty($exist)) ? $exist["data"]["cod"] : substr(md5(uniqid(rand())), 0, 10);
            $opcionesValor->set("cod", $codOpcionValor);
            (!empty($exist)) ? $opcionesValor->edit() : $opcionesValor->add();
        }
    }

    $opcionesSelect = isset($array['opcion-select']) ? $array['opcion-select'] : [];
    unset($array["opcion-select"]);
    if (isset($opcionesSelect)) {
        foreach ($opcionesSelect as $key => $selectItem) {
            $implodeArray = implode("|", $selectItem);
            if ($implodeArray == "-- Sin seleccionar --" || $implodeArray == NULL) continue;
            $opcionesValor->set("relacion_cod", $cod);
            $opcionesValor->set("idioma", $idiomaGet);
            $opcionesValor->set("opcion_cod", $key);
            $opcionesValor->set("valor", $implodeArray);
            $exist = $opcionesValor->checkIfExist();
            $codOpcionValor = (!empty($exist)) ? $exist["data"]["cod"] : substr(md5(uniqid(rand())), 0, 10);
            $opcionesValor->set("cod", $codOpcionValor);
            (!empty($exist)) ? $opcionesValor->edit() : $opcionesValor->add();
        }
    }


    $contenido->edit($array, ["cod = '$cod'", "idioma = '$idiomaGet'"]);
    if (!empty($_FILES['files']['name'][0])) {
        $imagenes->resizeImages($cod, $_FILES['files'], "assets/archivos/recortadas", $funciones->normalizar_link(strip_tags($array["titulo"])), [$idiomaGet]);
    }
    if ($getArea != 'landing-area') {
        $funciones->headerMove(URL_ADMIN . "/index.php?op=contenidos&accion=ver&area=" . $areaData['data']['cod'] . "&idioma=" . $idiomaGet);
    } else {
        $funciones->headerMove(URL_ADMIN . "/index.php?op=landing");
    }
}
?>
<div class="mt-20 card">
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
                    <input name="destacado" type="hidden" value="<?= $contenidoSingle['data']['destacado'] ?> ">
                    <input type="hidden" name="idioma" value="<?= $idiomaGet ?>">
                    <label class="col-md-5">
                        Título
                        <input type="text" value="<?= $contenidoSingle['data']["titulo"] ?>" name="titulo">
                    </label>
                    <label class="col-md-4">
                        Subtitulo
                        <input type="text" id="sub" value="<?= $contenidoSingle['data']["subtitulo"] ?>" name="subtitulo">
                    </label>
                    <label class="col-md-3">
                        Código:<br />
                        <input type="text" name="cod" maxlength="10" disabled value="<?= $contenidoSingle["data"]["cod"] ?>">
                    </label>
                    <label class="col-md-5">
                        Categoría:<br />
                        <select name="categoria">
                            <option value="">-- categorías --</option>
                            <?php
                            foreach ($categoriasData as $categoria) {
                                $selected = ($contenidoSingle["data"]["categoria"] == $categoria["data"]["cod"]) ? "selected" : '';
                                echo "<option value='" . $categoria["data"]["cod"] . "' $selected >" . mb_strtoupper($categoria["data"]["titulo"]) . "</option>";
                            }
                            ?>
                        </select>
                    </label>
                    <label class="col-md-4">
                        Subcategoría:<br />
                        <select name="subcategoria">
                            <option value="">-- Sin subcategoría --</option>
                            <?php
                            foreach ($categoriasData as $categoria) {
                            ?>
                                <optgroup label="<?= mb_strtoupper($categoria["data"]['titulo']) ?>">
                                    <?php
                                    foreach ($categoria["subcategories"] as $subcategorias) {
                                        $selected = ($contenidoSingle["data"]["subcategoria"] == $subcategorias["data"]["cod"]) ? "selected" : '';
                                        echo "<option value='" . $subcategorias["data"]["cod"] . "' $selected >" . mb_strtoupper($subcategorias["data"]["titulo"]) . "</option>";
                                    }
                                    ?>
                                </optgroup>
                            <?php
                            }
                            ?>
                        </select>
                    </label>
                    <label class="col-md-2">
                        Fecha:<br />
                        <input type="date" name="fecha" value="<?= $contenidoSingle["data"]["fecha"] ?>">
                    </label>
                    <label class="col-md-1">
                        Orden:<br />
                        <input type="text" name="orden" value="<?= $contenidoSingle["data"]["orden"] ?>">
                    </label>

                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                    </div>
                    <label class="col-md-12">
                        Contenido:<br />
                        <textarea name="contenido" class="ckeditorTextarea" required>
                            <?= $contenidoSingle["data"]["contenido"]; ?>
                        </textarea>
                    </label>
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                    </div>
                    <label class="col-md-12">
                        Palabras claves dividas por ,<br />
                        <input type="text" name="keywords" value="<?= $contenidoSingle["data"]["keywords"] ?>">
                    </label>
                    <label class="col-md-12">
                        Descripción breve<br />
                        <textarea name="description"><?= $contenidoSingle["data"]["description"] ?></textarea>
                    </label>
                    <br />
                    <label class="col-md-12">Link
                        <input type="text" id="link" name="link" value="<?= $contenidoSingle["data"]["link"] ?>">
                    </label>

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
                                    <input type="text" class="form-control" name="opcion[<?= $optionItem["data"]["cod"] ?>]" value="<?= $optionItem["data"]["valor"] ?>">
                                <?php } ?>
                                <?php if ($optionItem["data"]["tipo"] == "textarea") { ?>
                                    <textarea class="ckeditorTextarea" name="opcion[<?= $optionItem["data"]["cod"] ?>]"><?= $optionItem["data"]["valor"] ?></textarea>
                                <?php } ?>
                                <?php if ($optionItem["data"]["tipo"] == "int") { ?>
                                    <input type="number" class="form-control" name="opcion[<?= $optionItem["data"]["cod"] ?>]" value="<?= $optionItem["data"]["valor"] ?>">
                                <?php } ?>
                                <?php if ($optionItem["data"]["tipo"] == "boolean") { ?>
                                    <select class="form-control" name="opcion[<?= $optionItem["data"]["cod"] ?>]">
                                        <option>-- Sin seleccionar --</option>
                                        <option <?= ($optionItem["data"]["valor"]) == "true" ? "selected" : '' ?> value="true">Si</option>
                                        <option <?= ($optionItem["data"]["valor"]) == "false" ? "selected" : '' ?> value="false">No</option>
                                    </select>
                                <?php } ?>
                                <?php if ($optionItem["data"]["tipo"] == "select") { ?>
                                    <select class="form-control" id="select-<?= $optionItem['data']['cod'] ?>" onchange="getSelectedValues('<?= $optionItem['data']['cod'] ?>')" name="opcion-select[<?= $optionItem["data"]["cod"] ?>][]" <?= ($optionItem["data"]["multiple"] == "1") ? " multiple " : "" ?>>
                                        <?php
                                        foreach (explode("|", $optionItem["data"]["opciones"]) as $option) { ?>
                                            <option <?= (strpos(trim($optionItem["data"]["valor"]), trim($option)) || strpos(trim($optionItem["data"]["valor"]), trim($option)) === 0 || trim($optionItem["data"]["valor"]) == trim($option))  ? "selected" : '' ?> value="<?= $option ?>"><?= $option ?></option>
                                        <?php } ?>
                                    </select>
                                    <div id="div-<?= $optionItem["data"]["cod"] ?>"></div>
                                    <script>
                                        $(document).ready(() => {
                                            getSelectedValues('<?= $optionItem['data']['cod'] ?>');
                                        })
                                    </script>
                                <?php } ?>
                            </div>
                        <?php
                            $categoriaOption = $optionItem["data"]["categoria"];
                            $x++;
                        }
                        ?>
                    </label>

                    <?php $imagenes->buildEditImagesAdmin($contenidoSingle['images']); ?>
                    <div class="col-md-12 mt-10">
                        <input type="submit" class="btn btn-block btn-primary" name="modificar" value="Modificar" />
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>
</div>