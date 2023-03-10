<?php
$seo = new Clases\Seo();
$imagenes = new Clases\Imagenes();
$f = new Clases\PublicFunction();
$cod = isset($_GET["cod"]) ? $funciones->antihack_mysqli($_GET["cod"]) : '';
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];
$seo->set("cod", $cod);
$url = $seo->view();

$imagenes->set("cod", $url['data']["cod"]);
$imagenes->set("link", "seo&accion=modificar");

if (isset($_POST["modificar"])) {
    $count = 0;
    $cod = $url['data']["cod"];
    $title = isset($_POST["title"]) ? $funciones->antihack_mysqli($_POST["title"]) : '';
    $seo->set("cod", $cod);
    $seo->set("url", isset($_POST["url"]) ? $funciones->antihack_mysqli($_POST["url"]) : '');
    $seo->set("title", $title);
    $seo->set("description", isset($_POST["description"]) ? $funciones->antihack_mysqli($_POST["description"]) : '');
    $seo->set("keywords", isset($_POST["keywords"]) ? $funciones->antihack_mysqli($_POST["keywords"]) : '');
    $seo->set("idioma", $idiomaGet);

    $seo->edit();
    if (!empty($_FILES['files']['name'][0])) {
        $imagenes->resizeImages($cod, $_FILES['files'], "assets/archivos/recortadas", $funciones->normalizar_link(strip_tags($title)), [$idiomaGet]);
    }
    $funciones->headerMove(URL_ADMIN . "/index.php?op=seo&accion=ver&idioma=$idiomaGet");
}
?>
<div class="mt-20 card">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-uppercase text-center">
                SEO
            </h4>
            <hr style="border-style: dashed;">

        </div>
        <div class="card-content">
            <div class="card-body">

                <form method="post" class="row" enctype="multipart/form-data">
                    <label class="col-md-8">URL:<br />
                        <input type="text" value="<?= $url['data']["url"] ?>" name="url" required>
                    </label>
                    <label class="col-md-4">Título:<br />
                        <input type="text" value="<?= $url['data']["title"] ?>" name="title">
                    </label>

                    <div class="clearfix"></div>
                    <label class="col-md-12">Palabras claves dividas por ,<br />
                        <input type="text" name="keywords" value="<?= $url['data']["keywords"] ?>">
                    </label>
                    <label class="col-md-12">Descripción<br />
                        <textarea name="description"><?= $url['data']["description"] ?></textarea>
                    </label>
                    <br />
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                        <?php $imagenes->buildEditImagesAdmin($url['images'], false) ?>
                    </div>
                    <div class="col-md-12 mt-20">
                        <input type="submit" class="btn btn-primary btn-block" name="modificar" value="Modificar parametros SEO" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>