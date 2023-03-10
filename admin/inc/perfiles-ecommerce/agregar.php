<?php
$perfiles_ecommerce = new Clases\PerfilesEcommerce();
$estadoPedido = new Clases\EstadosPedidos();
$envios = new Clases\Envios();
$pagos = new Clases\Pagos();
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli(strtolower($_GET["idioma"])) : $_SESSION['lang'];
$enviosData = $envios->list([], "", "", $idiomaGet);
$pagosData = $pagos->list(['tipo IS NULL'], '', '', $idiomaGet);
$estadoData = $estadoPedido->list(["idioma = '$idiomaGet'"], "", "");

if (isset($_POST["agregar"])) {
    unset($_POST["agregar"]);
    $array = $funciones->antihackMulti($_POST);
    $id = $perfiles_ecommerce->add($array);
    $funciones->headerMove(URL_ADMIN . "/index.php?op=perfiles-ecommerce&accion=ver&tipo=". $array['minorista']);
}

?>
<div class="mt-20 ">
    <div class="card">
        <div class="card-header pb-0">
            <h4 class="card-title text-uppercase text-center">
                Agregar Perfil
            </h4>
            <hr style="border-style: dashed;">
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="post">
                    <div class="position-relative bg-secondary bg-light shadow my-2 pt-10">
                        <div class="badge badge badge-primary bg-light badge-title-section">Informacion General</div>
                        <div class="row mx-1 my-1">
                            <div class="col-md-8">
                                <fieldset class="form-group">
                                    <label for="titulo">Descripción</label>
                                    <input name="titulo" type="text" class="form-control" id="titulo" placeholder="Descripción del perfil">
                                </fieldset>
                            </div>
                            <div class="col-md-4">
                                <fieldset class="form-group">
                                    <label for="minorista">Tipo CLiente</label>
                                    <select name="minorista" class="form-control" id="minorista">
                                        <option value="2">Sin Registrar</option>
                                        <option value="1">Minorista</option>
                                        <option value="0">Mayorista</option>
                                    </select>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    <div class="position-relative bg-secondary bg-light shadow my-2 pt-10">
                        <div class="badge badge badge-primary bg-light badge-title-section">Productos</div>
                        <div class="row mx-1 my-1">
                            <div class="col-md-3">
                                <fieldset class="form-group">
                                    <div class="custom-control custom-switch custom-switch-glow">
                                        <label class="invoice-terms-title" for="mostrar_precios"> Mostrar Precios en la web </label><br>
                                        <input name="mostrar_precios" type="checkbox" id="mostrar_precios" class="custom-control-input" checked value="1">
                                        <label class="custom-control-label" for="mostrar_precios">
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-3">
                                <fieldset class="form-group">
                                    <div class="custom-control custom-switch custom-switch-glow">
                                        <label class="invoice-terms-title" for="usar_stock"> Utilizar Stock</label><br>
                                        <input name="usar_stock" type="checkbox" id="usar_stock" class="custom-control-input" checked value="1">
                                        <label class="custom-control-label" for="usar_stock">
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-3">
                                <fieldset class="form-group">
                                    <div class="custom-control custom-switch custom-switch-glow">
                                        <label class="invoice-terms-title" for="mostrar_sin_stock"> Mostrar productos sin stock</label><br>
                                        <input name="mostrar_sin_stock" type="checkbox" id="mostrar_sin_stock" class="custom-control-input" checked value="1">
                                        <label class="custom-control-label" for="mostrar_sin_stock">
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-3">
                            <fieldset class="form-group">
                                    <label for="remarcado_productos">% de Remarcado</label>
                                    <div class="input-group">
                                        <input name="remarcado_productos" type="number" class="form-control" placeholder="% de recargo" min="-100" value="0" aria-describedby="remarcado_productos">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="remarcado_productos">%</span>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    <div class="position-relative bg-secondary bg-light shadow my-2 pt-10">
                        <div class="badge badge badge-primary bg-light badge-title-section">Carro de compra</div>
                        <div class="row mx-1 my-1">
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <label for="recargo_factura">% de Recargo si solicita Facturación</label>
                                    <div class="input-group">
                                        <input name="recargo_factura" type="number" class="form-control" placeholder="% de recargo" value="0" aria-describedby="recargo_factura">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="recargo_factura">%</span>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                        </div>
                    </div>
                    <div class="position-relative bg-secondary bg-light shadow my-2 pt-10">
                        <div class="badge badge badge-primary bg-light badge-title-section">Proceso de Compra</div>
                        <div class="row mx-1 my-1">
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="saltar_checkout">Finalizar proceso en:</label>
                                    <select name="saltar_checkout" class="form-control" id="saltar_checkout" onchange="displayPredefinidos()">
                                        <option value="shipping">ANTES DE CARGAR LOS DATOS DE ENVÍO</option>
                                        <option value="select-shipping">ANTES DE SELECCIONAR METODO DE ENVÍO</option>
                                        <option value="payment">ANTES DE CARGAR LOS DATOS DE FACTURACIÓN</option>
                                        <option value="select-payment">ANTES DE SELECCIONAR METODO DE PAGO</option> 
                                        <option value="nothing">NO SALTAR EL PROCESO</option>
                                        <option value="all">BLOQUEAR LA COMPRA EN LA WEB</option>
                                        <option value="skip">AUTOMATIZAR EL PROCESO</option>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="estado_pedido">ESTADO DEL PEDIDO PREDEFINIDO</label>
                                    <select name="estado_pedido" class="form-control" id="estado_pedido">
                                        <?php foreach ($estadoData as $estado) { ?>
                                            <option value="<?= $estado['data']['id'] ?>"><?= mb_strtoupper($estado['data']['titulo']) ?></option>
                                        <?php } ?>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-md-6 display d-none">
                                <fieldset class="form-group">
                                    <label for="metodo_envio">METODO DE ENVIO PREDEFINIDO</label>
                                    <select name="metodo_envio" class="form-control" id="metodo_envio">
                                        <?php foreach ($enviosData as $envio) { ?>
                                            <option value="<?= $envio['data']['cod'] ?>"><?= $envio['data']['titulo']  ?></option>
                                        <?php  } ?>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-md-6 display d-none">
                                <fieldset class="form-group">
                                    <label for="metodo_pago">METODO DE PAGO PREDEFINIDO</label>
                                    <select name="metodo_pago" class="form-control" id="metodo_pago">
                                        <?php foreach ($pagosData as $pago) { ?>
                                            <option value="<?= $pago['data']['cod'] ?>"><?= $pago['data']['titulo']  ?></option>
                                        <?php  } ?>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <div class="custom-control custom-switch custom-switch-glow">
                                        <label class="invoice-terms-title" for="pedido_whatsapp"> Enviar al vendedor pedido por Whatsapp</label><br>
                                        <input name="pedido_whatsapp" type="checkbox" id="pedido_whatsapp" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="pedido_whatsapp">
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 d-flex justify-content-end">
                        <button name="agregar" type="submit" class="btn btn-primary mr-1 mb-1">Cargar Nuevo Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function displayPredefinidos(){
        var saltar_checkout = $('#saltar_checkout').val(); 
        (saltar_checkout == 'skip') ? $('.display').removeClass('d-none') : $('.display').addClass('d-none');    
    }
</script>