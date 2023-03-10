<?php
$perfiles_ecommerce = new Clases\PerfilesEcommerce();
$minoristaGet = isset($_GET["tipo"]) ? $funciones->antihack_mysqli($_GET["tipo"]) : 2;

$filter = '';
$perfiles = $perfiles_ecommerce->list(['minorista' => $minoristaGet], '', '');
?>
<section id="basic-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <h4 class="mt-20 pull-left">PERFILES</h4>
                        <?php if ($_SESSION["admin"]["crud"]["crear"]) { ?>
                            <a class="btn btn-success pull-right text-uppercase mt-15" href="<?= URL_ADMIN ?>/index.php?op=perfiles-ecommerce&accion=agregar">
                                AGREGAR PERFIL
                            </a>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <hr />
                        <fieldset class="form-group position-relative has-icon-left mb-20">
                            <input type="search" class="form-control" id="myInput" type="text" placeholder="Buscar..">
                            <div class="form-control-position">
                                <i class="bx bx-search"></i>
                            </div>
                        </fieldset>
                        <div class="table-responsive">
                            <table class="table zero-configuration dataTable" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
                                <thead>
                                    <tr role="row">
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1">Activo</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1">Descripción</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1">Ajustes</th>
                                    </tr>
                                </thead>
                                <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                        <a class="nav-link <?= 2 == $minoristaGet ? "active" : '' ?> " href="<?= URL_ADMIN ?>/index.php?op=perfiles-ecommerce&accion=ver&tipo=2">
                                            <i class="bx bx-user-x align-middle"></i>
                                            <span class="align-middle">Sin Registrar</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a  class="nav-link <?= 1 == $minoristaGet ? "active" : '' ?> " href="<?= URL_ADMIN ?>/index.php?op=perfiles-ecommerce&accion=ver&tipo=1">
                                            <i class="bx bx-cart align-middle"></i>
                                            <span class="align-middle">Minorista</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?= 0 == $minoristaGet ? "active" : '' ?> " href="<?= URL_ADMIN ?>/index.php?op=perfiles-ecommerce&accion=ver&tipo=0">
                                            <i class="bx bx-package align-middle"></i>
                                            <span class="align-middle">Mayorista</span>
                                        </a>
                                    </li>

                                </ul>
                                <tbody>
                                    <?php
                                    if (is_array($perfiles)) {
                                        foreach ($perfiles as $key => $data) {
                                    ?>
                                            <tr role="row" class="odd">
                                                <td>
                                                    <div class="custom-control custom-switch custom-switch-glow">
                                                        <input name="estado" type="radio" id="activo-<?= $data['data']['id'] ?>" onchange="editEstado('<?= $data['data']['id'] ?>','<?= $data['data']['minorista'] ?>')" class="custom-control-input" <?= ($data['data']['activo']) ? 'checked' : '' ?> value="<?= $data['data']["id"] ?>">
                                                        <label class="custom-control-label" for="activo-<?= $data['data']['id'] ?>"></label>
                                                    </div>
                                                </td>
                                                <td style="padding: 0.5rem 0.5rem;"><?= $data['data']["titulo"] ?> </td>
                                                <td style="padding: 0.5rem 0.5rem;">
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        <?php if ($_SESSION["admin"]["crud"]["editar"]) { ?>
                                                            <a data-toggle="tooltip" class="btn btn-default" data-placement="top" title="Modificar" href="<?= URL_ADMIN ?>/index.php?op=perfiles-ecommerce&accion=modificar&id=<?= $data['data']["id"] ?>">
                                                                <div class="fonticon-wrap">
                                                                    <i class="bx bx-edit fs-20"></i>
                                                                </div>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($_SESSION["admin"]["crud"]["eliminar"]) { ?>
                                                            <a class="deleteConfirm btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar" href="<?= URL_ADMIN ?>/index.php?op=perfiles-ecommerce&accion=ver&borrar=<?= $data['data']["id"] ?>&tipo=<?= $minoristaGet ?>" >
                                                                <div class="fonticon-wrap">
                                                                    <i class="bx bx-trash fs-20"></i>
                                                                </div>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
if (isset($_GET["borrar"]) && $_SESSION["admin"]["crud"]["eliminar"]) {
    $id = isset($_GET["borrar"]) ? $funciones->antihack_mysqli($_GET["borrar"]) : '';
    $perfiles_ecommerce->delete($id);
    $funciones->headerMove(URL_ADMIN . "/index.php?op=perfiles-ecommerce&accion=ver&tipo=". $minoristaGet);
}
?>

<script>
    function editEstado(id,minorista) {
        console.log(id);
        $.ajax({
            url: "<?= URL_ADMIN ?>/api/perfiles-ecommerce/editStatus.php",
            type: "POST",
            data: {
                id: id,
                minorista: minorista
            },
            success: (data) => {
                var data = JSON.parse(data);
                if (data["status"]) {
                    successMessage(data["message"]);
                } else {
                    warningMessage(data["message"]);
                }
            },
        });
    }
</script>