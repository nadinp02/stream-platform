<?php
$excel = new Clases\Excel();
$idiomas = new Clases\Idiomas();
$f = new Clases\PublicFunction();
$productos = new Clases\Productos();
$opciones = new Clases\Opciones();

$idiomasList = $idiomas->list();
$attrList = $productos->getAttrWithTitle();
?>

<?php
//IMPORTAR ARCHIVO DE EXCEL DE PRODUCTOS
if (empty($_POST)) {
?>
    <section class="invoice-edit-wrapper mt-40">
        <h4 class="mb-20">Importar Productos</h4>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="basicInputFile">Cargue su archivo excel</label>
                            <div class="custom-file" style="margin-top: 5px;">
                                <input type="file" class="custom-file-input" name="excel" id="file_import">
                                <label class="custom-file-label" for="file_import"></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary pull-right mt-20" name="import" value="check" type="submit">
                                Importar Listado
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php } ?>


<?php
//COMIENZA EL PROCESO DE IMPORTACIÓN
if (isset($_POST['import'])) {
    if ($_POST['import'] == 'check') {
        //HACEMOS SELECCIÓN DE LA HOJA QUE VAMOS A UTILIZAR
        $urlFile = $excel->saveFile();
        $sheets = $excel->getSheets($urlFile);
        if (count($sheets) > 1) {
?>
            <div class="container-fluid">
                <div class="card mt-20">
                    <h3>
                        Seleccionar Hoja a Importar
                    </h3>
                    <hr>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="urlFile" value="<?= $urlFile ?>">
                        <select name="sheet">
                            <?php foreach ($sheets as $key => $sheet) { ?>
                                <option value="<?= $key ?>"><?= $sheet ?></option>
                            <?php } ?>
                        </select>
                        <button class="btn btn-primary pull-right mt-20" name="import" value="import" type="submit">Importar Hoja</button>
                    </form>
                </div>
            <?php

        }
    }

    //UNA VEZ SELECCIONADA LA HOJA COMIENZA LA ´VINCULACIÓN DE COLUMNAS CONTRA EL SISTEMA
    if ($_POST['import'] == 'import' || ($_POST['import'] == 'check' && count($sheets) == 1)) {
        $sheet = isset($_POST['sheet']) ? $f->antihack_mysqli($_POST['sheet']) : '0';
        $urlFile = isset($_POST['urlFile']) ? $f->antihack_mysqli($_POST['urlFile']) : $urlFile;
        $arrayImport = $excel->importProduct($urlFile, $sheet);
            ?>
            <div class="container-fluid">
                <div class="card mt-20">
                    <h3>Vincular columnas del Excel a nuestro sistema</h3>
                    <hr />
                    <div class="table-responsive">
                        <form id="form_attr" method="POST">
                            <table class="table table-sm">
                                <thead>
                                    <th>Titulo</th>
                                    <th>Dato</th>
                                    <th>Vincular</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($arrayImport as $attr => $value) { ?>
                                        <tr>
                                            <td><?= $attr ?></td>
                                            <td><?= $value ?></td>
                                            <td>
                                                <select id="<?= $attr ?>" name="<?= $attr ?>" onchange="checkType('<?= $attr ?>')">
                                                    <option value="1">No Vincular</option>
                                                    <?php foreach ($productos->getAttrWithTitle() as $attr_ => $title) { ?>
                                                        <option value="<?= $attr_ ?>"><?= $title ?></option>
                                                    <?php } ?>
                                                    <?php foreach ($opciones->getAttrWithTitle() as $attr_ => $title) { ?>
                                                        <option value="<?= $attr_ ?>|option"><?= $title ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td>Idioma</td>
                                        <td></td>
                                        <td>
                                            <select name="modal-idioma-select" id="modal-idioma-select" required>
                                                <?php
                                                foreach ($idiomasList as $idioma) {
                                                ?>
                                                    <option <?= (count($idiomasList) == 1) ? "selected" : "" ?> value="<?= $idiomasList[0]['data']['cod'] ?>">
                                                        <?= $idioma['data']['titulo'] ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button name="selectImport" class="btn btn-primary pull-right">Importar</button>
                        </form>
                    </div>
                </div>
            </div>
    <?php
    }
}
    ?>

    <!--BorderLess Modal Modal -->
    <div class="modal fade text-left modal-borderless" id="modal-waiting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">
                        <div class="spinner-border text-success mr-10" role="status">
                            <span class="sr-only">Loading... </span>
                        </div>
                        <span id="title-modal">Procesando informacion</span>
                    </h2>
                </div>
                <div class="modal-body fs-16" id="description-modal">
                    Este proceso puede demorar varios minutos por favor <span class="bold fs-18 text-danger"><u>NO</u></span> cerrar la ventana hasta que el proceso finalice.
                </div>

            </div>
        </div>
    </div>

    <script>
        function checkType(attr) {
            var val = $('#' + attr).val();
            if (val == 'cod_producto' || val == 'cod' || val == 'titulo') {
                $('#search-' + attr).prop("disabled", false);
            }
        }


        //METODO QUE IMPACTA Y COMIENZA LA IMPORTACIÓN
        $('#form_attr').submit(function(e) {
            event.preventDefault();
            form = $('#form_attr').serializeArray();
            validCod = false;
            validIdioma = false;
            form.forEach(element => {
                if (element['value'] == 'idioma') validIdioma = true;
                if (element['value'] == 'cod') validCod = true;
            });
            if (!validCod) {
                errorMessage('Seleccionar vinculacion con codigo de producto para continuar');
            }

            if (!validIdioma) {
                if ($('#modal-idioma-select').val() != 1) {
                    validIdioma = true;
                } else {
                    $('#modal-idiomas').modal('toggle');
                }
            }
            if (validIdioma && validCod) {
                $.ajax({
                    url: "<?= URL_ADMIN ?>/api/excel/import_products.php",
                    type: 'POST',
                    data: form,
                    beforeSend: function() {
                        $('#modal-waiting').modal('toggle');
                    },
                    success: function(data) {
                        try {
                            data_json = JSON.parse(data);
                            if (data_json['status'] === true) {
                                $('#modal-waiting').modal('toggle');
                                document.location.href =
                                    '<?= URL_ADMIN ?>/index.php?op=productos&accion=ver&idioma=<?= $_SESSION['lang'] ?>';
                            } else {
                                $('#title-modal').html("Ups! Hay un error");
                                $('#description-modal').html(data);
                            }
                        } catch (error) {
                            $('#title-modal').html("Ups! Hay un error");
                            $('#description-modal').html(data);
                        }

                    }
                });
            }
        });
    </script>