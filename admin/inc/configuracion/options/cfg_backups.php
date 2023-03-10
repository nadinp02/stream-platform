<?php
$backup = new Clases\Backup();
$backups = $backup->getAllFiles();
?>

<div>
    <div class="row">
        <div class="col-md-12">
            <h3 class="pull-left text-uppercase fs-16 bold text-center">Sección de Respaldos</h3>
            <a class="btn btn-success pull-right" href="#" onclick="createBackup()">
                <i class="fa fa-database" aria-hidden="true"></i> CREAR UN BACKUP
            </a>
        </div>
        <div class="col-md-12">
            <hr />
            <span>Utilizá o crea los respaldos de tu sitio desde esta sección. No te olvides que al cargar un nuevo respaldo eliminarás toda la información que tengas posterior a ese respaldo.</span>
            <hr />
        </div>
    </div>
    <?php if (!empty($backups)) {
        foreach ($backups as $item) {
    ?>
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-left text-left text-uppercase">
                        <i class="fa fa-database"></i>
                        <?= $item["titulo"] ?>
                    </span>
                    <div class="pull-right  btn-group">
                        <a data-toggle="tooltip" data-placement="top" class="btn btn-success pull-right" title="Cargar" onclick="executeBackup('<?= $item['url'] ?>')">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" class="btn btn-danger pull-right deleteConfirm" title="Eliminar" onclick="deleteBackup('<?= $item['url'] ?>')">
                            <i class="bx bx-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
            <hr classs="my-2" />
    <?php  }
    } ?>
</div>