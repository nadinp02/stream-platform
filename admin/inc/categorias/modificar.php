<?php
$categorias = new Clases\Categorias();
$imagenes = new Clases\Imagenes();
$area = new Clases\Area();

$cod = isset($_GET["cod"]) ? $funciones->antihack_mysqli($_GET["cod"]) : '';
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];
$data = $categorias->list(["cod = '$cod'"], "", "", $idiomaGet, true);

$imagenes->set("idioma", $idiomaGet);
$areas = $area->list([], "", "", $idiomaGet);


if (isset($_POST["modificar"])) {
    unset($_POST["modificar"]);
    $array = $funciones->antihackMulti($_POST);
    if(!isset($array["free_shipping"])) $array["free_shipping"] = 0;
    $categorias->edit($array, ["cod = '$cod'", "idioma = '$idiomaGet'"]);

    if (!empty($_FILES['files']['name'][0])){
        $imagenes->resizeImages($cod, $_FILES['files'], "assets/archivos/recortadas", $funciones->normalizar_link($array["titulo"]), [$idiomaGet]);
    }
    $funciones->headerMove(URL_ADMIN . "/index.php?op=categorias&accion=ver&idioma=$idiomaGet");
}


?>
<div class="mt-20 card">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-uppercase text-center">
                Categorías
            </h4>
            <hr style="border-style: dashed;">
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="post" class="row" enctype="multipart/form-data">
                    <input type="hidden" name="idioma" value="<?= $idiomaGet ?>">
                    <label class="col-md-4">Código:<br />
                        <input type="text" value="<?= $data['data']["cod"] ?>" name="cod" disabled required>
                    </label>
                    <label class="col-md-4">Título:<br />
                        <input type="text" value="<?= $data['data']["titulo"] ?>" name="titulo" required>
                    </label>
                    <label class="col-md-4">Área:<br />
                        <select name="area" required>
                            <option value="<?= $data['data']["area"] ?>" selected><?= $data['data']["area"]  ?></option>
                            <option>---------------</option>
                            <?php
                            if (isset($areas)) {
                                foreach ($areas as $areaItem) { ?>
                                    <option value="<?= $areaItem['data']['cod'] ?>"><?= $areaItem['data']['titulo'] ?></option>
                            <?php }
                            }
                            ?>
                            <option value="banners">Banners</option>
                            <option value="productos">Productos</option>
                            <option value="landing">Landing</option>
                            <option value="menu">Menu</option>
                            <option value="opciones">Opciones</option>
                        </select>
                    </label>
                    <label class="col-md-12 mt-10">Descripción:<br />
                        <textarea class="form-control" name="descripcion"><?= $data['data']["descripcion"] ?></textarea>
                    </label>
                    <div class="col-6 mt-40">
                         <div class="custom-control custom-switch custom-switch-glow">
                             <span class="invoice-terms-title"> Envio Gratis</span>
                             <input name="free_shipping" type="checkbox" id="free_shipping" class="custom-control-input" value="1" <?= $data["data"]["free_shipping"] ? "checked" : "" ?>>
                             <label class="custom-control-label" for="free_shipping">
                             </label>
                         </div>
                     </div>
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                    </div>
                    <?php $imagenes->buildEditImagesAdmin($data['images'], true) ?>
           
                    <div class="col-md-12 mt-20">
                        <input type="submit" class="btn btn-primary btn-block" name="modificar" value="Modificar Categoría" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>