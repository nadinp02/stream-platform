<?php
$funciones = new Clases\PublicFunction();
$area_class = new Clases\Area();

$cod = isset($_GET["cod"]) ? $funciones->antihack_mysqli($_GET["cod"]) : '';
$idioma = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];
$area = $area_class->list(["cod = '$cod'"], '', '', $idioma, true);

if (isset($_POST["guardar"])) {
    unset($_POST["guardar"]);
    $array = $funciones->antihackMulti($_POST);
    if ($area['data']["titulo"] != $array["titulo"] || $area['data']["cod"] != $array["cod"]) {
        $link_to_search = "/index.php?op=contenidos&accion=ver&area=" . $funciones->normalizar_link($area['data']["cod"]) . "&idioma=$idioma";
        $link = "/index.php?op=contenidos&accion=ver&area=" . $funciones->normalizar_link($array["cod"]) . "&idioma=$idioma";
        Clases\Menu::updateByLink($array["titulo"], $link, $idioma, $link_to_search);
    }
    $area_class->edit($array, ["cod = '$cod' AND idioma = '$idioma'"]);
    $funciones->headerMove(URL_ADMIN . "/index.php?op=area&accion=ver&idioma=$idioma");
}

?>

<div class="content-body mt-20">
    <div class="card">
        <div class="card-content">
            <div class="mt-20">
                <h4>
                    Modificar Áreas
                </h4>
                <hr style="border-style: dashed;">
                <div class="clearfix"></div>
                <form method="post" class="row" style="justify-content: center;" enctype="multipart/form-data">
                    <input type="hidden" value="<?= $area['data']["idioma"] ?>" name="idioma">
                    <div class="mb-1 col-md-6">
                        <label class="bold">Título *</label>
                        <input type="text" name="titulo" value="<?= $area['data']["titulo"] ?>" required>
                    </div>
                    <div class="mb-1 col-md-6">
                        <label class="bold">Código</label>
                        <input type="text" name="cod" value="<?= $area['data']["cod"] ?>" maxlength="10">
                    </div>
                    <div class="mb-1 col-md-4">
                        <label class="bold">Archivo General *</label>
                        <input type="text" name="archivo_area" value="<?= $area['data']["archivo_area"] ?>" required>
                    </div>
                    <div class="mb-1 col-md-4">
                        <label class="bold">Archivo Individual *</label>
                        <input type="text" name="archivo_individual" value="<?= $area['data']["archivo_individual"] ?>" required>
                    </div>
                    <div class="mb-1 col-md-4">
                        <label class="bold">Acortador *</label>
                        <input type="text" name="url" value="<?= $area['data']["url"] ?>" required>
                    </div>
                    <div class="col-12 mt-20">
                        <input type="submit" class="btn btn-block btn-primary" name="guardar" value="Modificar" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>