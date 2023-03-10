<?php
$excel = new Clases\Excel();
$idiomas = new Clases\Idiomas();
$f = new Clases\PublicFunction();
$productos = new Clases\Productos();
$opciones = new Clases\Opciones();

$idiomasList = $idiomas->list();
$attrList = $productos->getAttrWithTitle();
if (isset($_POST["export"])) {
    $atributos = isset($_POST['attr_export']) ? $_POST['attr_export'] : '';
    $idioma = isset($_POST['idioma_export']) ? $_POST['idioma_export'] : $_SESSION['lang'];
    $path = $excel->exportProduct($atributos, $idioma);
    if ($path != false) {
        $link = URL . "/files/export/productos/$idioma/" . $path;
        $f->headerMove($link);
    }  
}
?>
<section class="invoice-edit-wrapper mt-40">
    <h4 class="mb-20">Exportar Productos</h4>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form method="POST" enctype="multipart/form-data">
                <div class="col-md-12">
                    <h6>Seleccionar el idioma que desea exportar</h6>
                    <?php if (isset($_GET['message'])) { ?>
                        <p class="text-danger"><?= ucfirst($_GET['message']) ?></p>
                    <?php } ?>
                    <div class="form-group">
                        <select data-url="<?= URL_ADMIN ?>" class="select2-icons form-control" name="idioma_export" required>
                            <?php
                            if (count($idiomasList) == 1) { ?>
                                <option selected value="<?= $idiomasList[0]['data']['cod'] ?>">
                                    <?= $idiomasList[0]['data']['titulo'] ?>
                                </option>
                            <?php } else { ?>
                                <option value="0" data-icon="bx bx-shopping-bag" selected>--- Selecciona un Idioma ---</option>
                                <?php foreach ($idiomasList as $idioma) { ?>
                                    <option value="<?= $idioma['data']['cod'] ?>" <?= ($idioma == $_SESSION['lang']) ? 'selected' : '' ?> data-icon="bx bx-shopping-bag">
                                        <?= $idioma['data']['titulo'] ?>
                                    </option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <p>Luego de seleccionar la tabla elija que atributos desea exportar al excel</p>
                </div>
                <div class="col-md-12">
                    <h6>Seleccione los atributos que desea exportar</h6>
                    <div class="form-group">
                        <select class="select2 form-control text-uppercase" multiple="multiple" name="attr_export[]" style="min-height: 200px;" required>
                            <?php
                            foreach ($attrList as $key => $attr) {
                                if ($key == 'cod') continue;
                            ?>
                                <option value="<?= $key ?>" data-icon="bx bx-user"><?= $attr ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 mt-10">
                    <button class="btn btn-primary pull-right" id="download" name="export" type="submit">
                        Exportar Listado
                    </button>
                    <iframe id="downloader" src="" style="display:none;"></iframe>
                </div>
            </form>
        </div>
    </div>
</section>