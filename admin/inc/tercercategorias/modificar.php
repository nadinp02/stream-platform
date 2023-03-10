<?php
$categorias = new Clases\Categorias();
$tercercategorias = new Clases\Tercercategorias();
$imagen = new Clases\Imagenes();
$funciones = new Clases\PublicFunction();

$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];
$cod = isset($_GET["cod"]) ? $funciones->antihack_mysqli($_GET["cod"]) : '';
$dataTercercategoria = $tercercategorias->list(["cod = '$cod'"], '', '', $idiomaGet, true);
$categorias = $categorias->list(["area= 'productos'"], '', '', $idiomaGet);


if (isset($_POST["modificar"])) {
    unset($_POST["modificar"]);
    $array = $funciones->antihackMulti($_POST);
    if(!isset($array["free_shipping"])) $array["free_shipping"] = 0;
    $tercercategorias->edit($array, ["cod = '$cod'", "idioma = '$idiomaGet'"]);

    if (!empty($_FILES['files']['name'][0])) {
        $imagen->resizeImages($cod, $_FILES['files'], "assets/archivos/recortadas", $funciones->normalizar_link($array["titulo"]), [$idiomaGet]);
    }
    $funciones->headerMove(URL_ADMIN . "/index.php?op=categorias&accion=ver&idioma=$idiomaGet");
} ?>
<div class="mt-20 ">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-uppercase text-center">
                Tercercategorías
            </h4>
            <hr style="border-style: dashed;">
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="post" class="row" enctype="multipart/form-data">
                    <input type="hidden" name="idioma" value="<?= $idiomaGet ?>">
                    <input type="hidden" name="orden" value="0">
                    <input type="hidden" name="descripcion" value=" ">
                    <label class="col-md-4">Código:<br />
                        <input type="text" name="cod"  maxlength="10" value="<?= $cod ?>" required>
                    </label>
                    <label class="col-md-4">
                        Título:<br />
                        <input type="text" name="titulo" value="<?= $dataTercercategoria["data"]["titulo"] ?>" required>
                    </label>
                    <label class="col-md-4">
                        Subcategoria:<br />
                        <select name="subcategoria" required>
                            <?php
                            foreach ($categorias as $cat) {
                                foreach ($cat['subcategories'] as $sub) {
                                    $selected = ($dataTercercategoria["data"]["subcategoria"] == $sub["data"]["cod"]) ? "selected" : "";
                            ?>
                                    <option value="<?= $sub["data"]["cod"] ?>" <?= $selected ?>><?= mb_strtoupper($cat["data"]["titulo"]) ?> -> <?= mb_strtoupper($sub["data"]["titulo"]) ?></option>
                            <?php }
                            }
                            ?>
                        </select>
                    </label>
                    <div class="col-12 mt-40">
                         <div class="custom-control custom-switch custom-switch-glow">
                             <span class="invoice-terms-title"> Envio Gratis</span>
                             <input name="free_shipping" type="checkbox" id="free_shipping" class="custom-control-input" value="1" <?= $dataTercercategoria["data"]["free_shipping"] ? "checked" : "" ?>>
                             <label class="custom-control-label" for="free_shipping">
                             </label>
                         </div>
                     </div>
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">
                        <?php $imagen->buildEditImagesAdmin($dataTercercategoria['image'], false) ?>
                    </div>
                    <div class="col-md-12 mt-20">
                        <input type="submit" class="btn btn-primary btn-block" name="modificar" value="Modificar Tercercategoría" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>