<?php
$landing = new Clases\Landing();
$imagenes = new Clases\Imagenes();
$idiomas = new Clases\Idiomas();
$categorias = new Clases\Categorias();

$data = $categorias->list(["`categorias`.`area` = 'landing'"], '', '', $_SESSION['lang'], false, true);
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];
$idiomasData = $idiomas->list(["cod != '$idiomaGet'"], "", "");
if (isset($_POST["agregar"])) {
    unset($_POST["agregar"]);
    if (isset($_POST["idiomasInput"])) {
        $idiomasInputPost =  $_POST["idiomasInput"];
        $idiomasInputPost[] = $idiomaGet;
    } else {
        $idiomasInputPost = [$idiomaGet];
    }
    unset($_POST["idiomasInput"]);
    $cod = $funciones->antihack_mysqli($_POST["cod"]);
    $array = $funciones->antihackMulti($_POST);
    if (isset($idiomasInputPost) && !empty($idiomasInputPost)) {
        foreach ($idiomasInputPost as $idiomasInputItem) {
            $array["idioma"] = $idiomasInputItem;
            $landing->add($array);
        }
    }

    if (!empty($_FILES['files']['name'][0])) {
        $imagenes->resizeImages($cod, $_FILES['files'], "assets/archivos/recortadas", $funciones->normalizar_link(strip_tags($array["titulo"])), $idiomasInputPost);
    }

    $funciones->headerMove(URL_ADMIN . "/index.php?op=landing");
}
?>
<div class="mt-20 ">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-uppercase text-center">
                Landing
            </h4>
            <hr style="border-style: dashed;">
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="post" class="row" enctype="multipart/form-data">
                    <label class="col-md-3">Título:<br />
                        <input type="text" name="titulo" required>
                    </label>
                    <label class="col-md-3">Categoría:<br />
                        <select name="categoria">
                            <?php
                            foreach ($data as $categoria) {
                                echo "<option value='" . $categoria['data']["cod"] . "'>" . $categoria['data']["titulo"] . "</option>";
                            }
                            ?>
                        </select>
                    </label>
                    <label class="col-md-3">Código<br />
                        <input type="text" name="cod"  maxlength="10" value="<?= substr(md5(uniqid(rand())), 0, 10) ?>" required>
                    </label>
                    <label class="col-md-3">Fecha:<br />
                        <input type="date" name="fecha">
                    </label>
                    <label class="col-md-12 mt-10">Desarrollo:<br />
                        <textarea name="desarrollo" class="ckeditorTextarea"></textarea>
                    </label>
                    <label class="col-md-12 mt-10">Palabras claves dividas por ,<br />
                        <input type="text" name="keywords">
                    </label>
                    <label class="col-md-12">Descripción breve<br />
                        <textarea name="description"></textarea>
                    </label>
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                    </div>
                    <label class="col-md-7">Imágenes:<br />
                        <input type="file" onchange="filePreview(this)" id="file" name="files[]" multiple="multiple" accept="image/*" />
                        <div id="preview-images" class="my-2"></div>
                    </label>
                    <?php if (count($idiomasData) >= 1) { ?>
                        <div class="col-md-12">
                            <div class="btn btn-primary mt-10 mb-10" onclick="$('#idiomasCheckBox').show()">Republicar landing page en otros idiomas</div>
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
                        <input type="submit" class="btn btn-primary btn-block" name="agregar" value="Agregar" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#idiomasCheckBox').hide();
</script>