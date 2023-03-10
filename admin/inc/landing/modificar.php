<?php
$landing = new Clases\Landing();
$imagenes = new Clases\Imagenes();
$categorias = new Clases\Categorias();

$cod = isset($_GET["cod"]) ? $funciones->antihack_mysqli($_GET["cod"]) : '';
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];

$landing->set("cod", $cod);
$landing->set("idioma", $idiomaGet);
$landingInd = $landing->list(["idioma" => $idiomaGet, "cod" => $cod], "", "", true);

$imagenes->set("cod", $landingInd['data']["cod"]);
$imagenes->set("link", "landing&accion=modificar");

$data = $categorias->list(["`categorias`.`area` = 'landing'"], '', '', $_SESSION['lang'], false, true);

//CAMBIAR ORDEN DE LAS IMAGENES
if (isset($_GET["ordenImg"]) && isset($_GET["idImg"])) {
    $imagenes->set("id", $funciones->antihack_mysqli($_GET["idImg"]));
    $imagenes->orden = $funciones->antihack_mysqli($_GET["ordenImg"]);
    $imagenes->setOrder();
    $funciones->headerMove(URL_ADMIN . "/index.php?op=landing&accion=modificar&cod=$cod&idioma=$idiomaGet");
}


if (isset($_POST["agregar"])) {
    unset($_POST["modificar"]);
    $array = $funciones->antihackMulti($_POST);
    $landing->edit($array, ["cod = '$cod'", "idioma = '$idiomaGet'"]);

    if (!empty($_FILES['files']['name'][0])) {
        $imagenes->resizeImages($cod, $_FILES['files'], "assets/archivos/recortadas", $funciones->normalizar_link(strip_tags($array["titulo"])), [$idiomaGet]);
    }
    $funciones->headerMove(URL_ADMIN . "/index.php?op=landing");
}
?>

<div class="mt-20 card">
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
                    <label class="col-md-4">
                        Título:<br />
                        <input type="text" value="<?= $landingInd['data']["titulo"] ?>" name="titulo" required>
                    </label>
                    <label class="col-md-4">
                        Categoría:<br />
                        <select name="categoria">
                            <?php
                            foreach ($data as $categoria) {
                                if ($landingInd['data']["categoria"] == $categoria['data']["cod"]) {
                                    echo "<option value='" . $categoria['data']["cod"] . "' selected>" . $categoria['data']["titulo"] . "</option>";
                                } else {
                                    echo "<option value='" . $categoria['data']["cod"] . "'>" . $categoria['data']["titulo"] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </label>
                    <label class="col-md-4">
                        Fecha:<br />
                        <input type="date" name="fecha" value="<?= $landingInd['data']["fecha"] ?>">
                    </label>
                    <div class="clearfix">
                    </div>
                    <label class="col-md-12 mt-10">
                        Desarrollo:<br />
                        <textarea name="desarrollo" class="ckeditorTextarea"><?= $landingInd['data']["desarrollo"]; ?> </textarea>
                    </label>
                    <div class="clearfix">
                    </div>
                    <label class="col-md-12 mt-10">
                        Palabras claves dividas por ,<br />
                        <input type="text" name="keywords" value="<?= $landingInd['data']["keywords"] ?>">
                    </label>
                    <label class="col-md-12">
                        Descripción breve<br />
                        <textarea name="description"><?= $landingInd['data']["description"] ?></textarea>
                    </label>
                    <br />
                    <?php $imagenes->buildEditImagesAdmin($landingInd['images'], true) ?>
                    <div class="clearfix">
                    </div>
                    <br />
                    <div class="col-md-12 mt-20">
                        <input type="submit" class="btn btn-primary btn-block" name="agregar" value="Modificar" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>