<?php
$seo = new Clases\Seo();
$imagenes = new Clases\Imagenes();

if (isset($_POST["agregar"])) {
    $count = 0;
    $cod = substr(md5(uniqid(rand())), 0, 10);
    $idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];
    $title = isset($_POST["title"]) ? $funciones->antihack_mysqli($_POST["title"]) : '';

    $seo->set("cod", $cod);
    $seo->set("url", isset($_POST["url"]) ? $funciones->antihack_mysqli($_POST["url"]) : '');
    $seo->set("title", $title);
    $seo->set("description", isset($_POST["description"]) ? $funciones->antihack_mysqli($_POST["description"]) : '');
    $seo->set("keywords", isset($_POST["keywords"]) ? $funciones->antihack_mysqli($_POST["keywords"]) : '');
    $seo->set("idioma", $idiomaGet);
    $seo->add();
    if (!empty($_FILES['files']['name'][0])){
        $imagenes->resizeImages($cod, $_FILES['files'], "assets/archivos/recortadas", $funciones->normalizar_link(strip_tags($title)), [$idiomaGet]);
    }
    $funciones->headerMove(URL_ADMIN . "/index.php?op=seo&accion=ver&idioma=$idiomaGet");
}

?>
<div class="mt-20 ">
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
                        <input type="url" name="url" required>
                    </label>
                    <label class="col-md-4">Título:<br />
                        <input type="text" name="title">
                    </label>
                    <div class="clearfix"></div>
                    <label class="col-md-12">Palabras claves dividas por ,<br />
                        <input type="text" name="keywords">
                    </label>
                    <label class="col-md-12">Descripción<br />
                        <textarea name="description"></textarea>
                    </label>
                    <br />
                    <label class="col-md-7 mt-10">Imágenes:<br />
                        <input type="file" onchange="filePreview(this)" id="file" name="files[]"  accept="image/*" />
                        <div id="preview-images" class="my-2"></div>
                    </label>
                    <div class="col-md-12 mt-20">
                        <input type="submit" class="btn btn-primary btn-block" name="agregar" value="Crear parametros SEO" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>