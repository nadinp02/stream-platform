<?php
$productos = new Clases\Productos();
$idiomas = new Clases\Idiomas();
$categoria = new Clases\Categorias();
$f = new Clases\PublicFunction();

#Variables GET
$backFilter = isset($_GET["backFilter"]) ? $f->antihack_mysqli(str_replace("-", " ", $_GET["backFilter"])) : 0;
$tituloGet = isset($_GET["title"]) ? $f->antihack_mysqli(str_replace("-", " ", $_GET["title"])) : '';
$mostrarGet =  isset($_GET["mostrar_web"]) ? $f->antihack_mysqli($_GET["mostrar_web"]) : 2;
$categoriaGet = isset($_GET["categoria"]) ? $f->antihack_mysqli($_GET["categoria"]) : '';
$subcategoriaGet = isset($_GET["subcategoria"]) ? $f->antihack_mysqli($_GET["subcategoria"]) : '';
$tercercategoriaGet = isset($_GET["tercercategoria"]) ? $f->antihack_mysqli($_GET["tercercategoria"]) : '';
$ordenGet = isset($_GET["order"]) ? $f->antihack_mysqli($_GET["order"]) : 1;
$pageGet = isset($_GET["page"]) ? $f->antihack_mysqli($_GET["page"]) : 1;
#List de categorías del área productos
$idiomaGet = isset($_GET["idioma"]) ? $funciones->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];
$categoriasData = $categoria->listIfHave('productos', '', $idiomaGet);



if (isset($_POST["modificar"])) {
    $cods_productos = isset($_POST["cods_productos"]) ? $_POST["cods_productos"] : "";
    unset($_POST["cods_productos"]);
    unset($_POST["modificar"]);
    $_POST["mostrar_web"] = isset($_POST["mostrar_web"]) ? $_POST["mostrar_web"] : "0";
    $_POST["envio_gratis"] = isset($_POST["envio_gratis"]) ? $_POST["envio_gratis"] : "0";
    $_POST["destacado"] = isset($_POST["destacado"]) ? $_POST["destacado"] : "0";
    $array = [];

    foreach ($_POST as $key => $post) {
        if (!empty($post) || $post != '') $array[$key] = $post;
    }

    $productos->edit($array, ["cod IN ($cods_productos)", "idioma = '$idiomaGet'"]);
    $funciones->headerMove(CANONICAL);
    // die();
}

?>
<div id="permisos" data-editar="<?= $_SESSION["admin"]["crud"]["editar"] ?>" data-eliminar="<?= $_SESSION["admin"]["crud"]["eliminar"] ?>"></div>
<section id="table-transactions" class="mt-30">
    <h4 class="mt-20 pull-left text-uppercase">Modificar Masivo - Productos | TOTAL (<span id="totalProductos"></span>)</h4>
    <div class="clearfix"></div>
    <hr />
    <div class="pb-100">
        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            <i class='fa fa-filter'></i> Filtros
        </button>
        <div class="row mt-20">
            <aside class="sidebar_widget mt-10 mt-lg-0">
                <div class="collapse show" id="collapseExample">
                    <div class="">
                        <div class="" id="filters">
                            <form id="filter-form" onsubmit="event.preventDefault();getDataMassive()">
                                <input name="backFilter" id="backFilter" type="hidden" value="<?= $backFilter ?>" />
                                <input name="page" id="page" type="hidden" value="<?= $pageGet ?>" />
                                <div class="container">
                                    <div class="search-filter">
                                        <div class="sidbar-widget pt-0">
                                            <h4 class="title fs-18">POR PALABRA</h4>
                                            <hr />
                                        </div>
                                    </div>
                                    <div class="" data-url="<?= URL ?>">
                                        <div class="searchbar">
                                            <input class="search_input fs-14 pl-15 " type="text" name="title" value="<?= $tituloGet ?>" placeholder="Buscar en productos...">
                                        </div>
                                    </div>
                                    <div class="sidbar-widget mt-20">
                                        <h4 class="title fs-18">ORDENAR</h4>
                                        <hr />
                                        <select name="order" id="order" onchange="getDataMassive()">
                                            <option <?= $ordenGet == 1 ? 'selected' : '' ?> value="1">Precio Ascendente</option>
                                            <option <?= $ordenGet == 2 ? 'selected' : '' ?> value="2">Precio Descendente</option>
                                            <option <?= $ordenGet == 3 ? 'selected' : '' ?> value="3">Stock Ascendente</option>
                                            <option <?= $ordenGet == 4 ? 'selected' : '' ?> value="4">Stock Descendente</option>
                                            <option <?= $ordenGet == 5 ? 'selected' : '' ?> value="5">Categoria Ascendente</option>
                                            <option <?= $ordenGet == 6 ? 'selected' : '' ?> value="6">Categoria Descendente</option>
                                            <option <?= $ordenGet == 7 ? 'selected' : '' ?> value="7">Titulo Ascendente</option>
                                            <option <?= $ordenGet == 8 ? 'selected' : '' ?> value="8">Titulo Descendente</option>
                                            <option <?= $ordenGet == 9 ? 'selected' : '' ?> value="9">Destacado Ascendente</option>
                                            <option <?= $ordenGet == 10 ? 'selected' : '' ?> value="10">Destacado Descendente</option>
                                            <option <?= $ordenGet == 11 ? 'selected' : '' ?> value="11">Disponible Ascendente</option>
                                            <option <?= $ordenGet == 12 ? 'selected' : '' ?> value="12">Disponible Descendente</option>
                                            <option <?= $ordenGet == 13 ? 'selected' : '' ?> value="13">Envio Gratis Ascendente</option>
                                            <option <?= $ordenGet == 14 ? 'selected' : '' ?> value="14">Envio Gratis Descendente</option>
                                        </select>
                                    </div>
                                    <div class="sidbar-widget mt-20">
                                        <h4 class="title fs-18">DISPONIBLES</h4>
                                        <hr />
                                        <label for="mostrarWeb">
                                            <input type="radio" id="mostrarWeb" <?= ($mostrarGet == 1) ? 'checked' : '' ?> name="mostrar_web" value="1" onchange="getDataMassive()">
                                            Si</label>
                                        <label for="no_mostrarWeb">
                                            <input class="ml-10" type="radio" id="no_mostrarWeb" <?= ($mostrarGet == 0) ? 'checked' : '' ?> name="mostrar_web" value="0" onchange="getDataMassive()">
                                            No</label>
                                        <label for="todo_mostrarWeb">
                                            <input class="ml-10" type="radio" id="todo_mostrarWeb" <?= ($mostrarGet == 2) ? 'checked' : '' ?> name="mostrar_web" value="2" onchange="getDataMassive()">
                                            Ambos</label>

                                    </div>
                                    <div class="widget-list mb-10 mt-20">
                                        <div class="search-filter">
                                            <div class="sidbar-widget pt-0">
                                                <h4 class="title fs-18">CATEGORIAS</h4>
                                                <hr />
                                            </div>
                                        </div>
                                        <div class="ulProducts">
                                            <?php
                                            if (!empty($categoriasData)) {
                                                foreach ($categoriasData as $key => $cat) {
                                                    $link_cat =  URL . "/productos/b/categoria/" . $cat['data']['cod'];
                                            ?>
                                                    <li class="d-block list-style-none mb-0 text-uppercase drop menu-item-has-children categorias  ">
                                                        <div class="sidebar-widget-list-left ">
                                                            <label for="cat-<?= $cat['data']['cod'] ?>" class=" text-uppercase my-0">
                                                                <input id="cat-<?= $cat['data']['cod'] ?>" value="<?= $cat['data']['cod'] ?>" <?= ($categoriaGet == $cat["data"]["cod"]) ? 'checked' : '' ?> name="categoria[]" class="check auto-save-categories" type="checkbox" onchange="changeSelect('<?= $cat['data']['cod'] ?>');getDataMassive();">
                                                                <?= $cat['data']['titulo'] ?>
                                                            </label>
                                                        </div>
                                                        <ul id="<?= $cat['data']['cod'] ?>SubCat" class="ulProductsDropdown subcategorias pl-20 dropdown" style="<?= ($categoriaGet == $cat["data"]["cod"]) ? '' : 'display:none' ?>">
                                                            <?php
                                                            foreach ($cat["subcategories"] as $key_ => $sub) {
                                                            ?>
                                                                <li class="d-block  list-style-none">
                                                                    <div class="sidebar-widget-list-left   ">
                                                                        <label class="my-0">
                                                                            <input id="sub-<?= $cat['data']['cod'] ?>-<?= $sub['data']['cod'] ?>" value="<?= $sub['data']['cod'] ?>" <?= strpos($subcategoriaGet, $sub["data"]["cod"]) !== false ? 'checked' : '' ?> class="check auto-save-subcategories" name="subcategoria[]" type="checkbox" onchange="changeSelect('<?= $cat['data']['cod'] ?>','<?= $sub['data']['cod'] ?>');getDataMassive()">
                                                                            <?= $sub['data']['titulo'] ?>
                                                                        </label>
                                                                        <ul id="<?= $sub['data']['cod'] ?>TerCat" class="ulProductsDropdown tercercategorias pl-20 dropdown" style="<?= strpos($subcategoriaGet, $sub["data"]["cod"]) !== false ? '' : 'display:none' ?>">
                                                                            <?php
                                                                            if (!empty($sub["tercercategories"])) {
                                                                                foreach ($sub["tercercategories"] as $key3 => $ter) { ?>
                                                                                    <li class="d-block  list-style-none my-0">
                                                                                        <label class="  text-uppercase my-0">
                                                                                            <input id="ter-<?= $ter["data"]["cod"] ?>" value="<?= $ter['data']['cod'] ?>" <?= strpos($tercercategoriaGet, $ter['data']['cod']) !== false ? 'checked' : '' ?> class="check auto-save-tercercategories" name="tercercategoria[]" type="checkbox" onchange="getDataMassive()">
                                                                                            <?= $ter['data']['titulo'] ?>
                                                                                        </label>
                                                                                    </li>
                                                                            <?php }
                                                                            } ?>
                                                                        </ul>
                                                                    </div>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </li>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>


            <div class="col">
                <div class="table-responsive">
                    <div id="table-extended-transactions_wrapper" class="dataTables_wrapper no-footer">
                        <table id="table-extended-transactions" class="table mb-0 dataTable no-footer" role="grid" style="table-layout:fixed;">
                            <thead>
                                <tr role="row text-center">
                                    <!-- <th><input type="checkbox" onchange="massiveChangeCheck($(this).is(':checked'))" checked /></th> -->
                                    <th>titulo</th>
                                    <th>precio</th>
                                    <th>precio_descuento</th>
                                    <th>stock</th>
                                    <th>peso</th>
                                    <th>gratis</th>
                                    <th>destacado</th>
                                    <th>mostrar</th>
                                </tr>
                            </thead>
                            <form method="post" id="formMassive">
                                <div class="row mb-20 ">
                                    <div class="col-12">
                                        <input type="hidden" name="cods_productos" id="cods_productos" value="" />
                                    </div>
                                    <div class="col-sm-4 col-12  mb-30">
                                        <h6 class="invoice-to">1° Categoria</h6>
                                        <select name="categoria" class="form-control bg-transparent select2" id="categoria" onchange="getCategory('<?= URL_ADMIN ?>','subcategory','categoria','subcategoria','<?= $idiomaGet ?>')">
                                            <option value=""> </option>
                                            <?php
                                            foreach ($categoriasData as $categoria) {
                                                echo "<option value='" . $categoria["data"]["cod"] . "'>" . mb_strtoupper($categoria["data"]["titulo"]) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4 col-12 mb-30">
                                        <h6 class="invoice-to">2° Categoria</h6>
                                        <select name="subcategoria" class="form-control bg-transparent select2" id="subcategoria" onchange="getSubcategory('<?= URL_ADMIN ?>','tercercategory','subcategoria','tercercategoria','<?= $idiomaGet ?>')">
                                            <option value=""> </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4 col-12 mb-30">
                                        <h6 class="invoice-to">3° Categoria</h6>
                                        <select name="tercercategoria" class="form-control bg-transparent select2" id="tercercategoria">
                                            <option value=""> </option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 col-sm-12   mb-30">
                                        <h6 class="invoice-to">Precio</h6>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input name="precio" type="number" step="any" class="form-control" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12  mb-30">
                                        <h6 class="invoice-to">Precio Descuento</h6>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input name="precio_descuento" type="number" step="any" class="form-control" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-4  col-sm-12   mb-30">
                                        <h6 class="invoice-to">Precio Mayorista</h6>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input name="precio_mayorista" type="number" step="any" class="form-control" placeholder="0.00" />
                                        </div>
                                    </div>
                                    <div class="col-md-3  col-sm-12">
                                        <h6 class="invoice-to">Stock</h6>
                                        <input name="stock" type="text" class="form-control" placeholder="0000" value="">
                                    </div>
                                    <div class="col-md-3  col-sm-12 ">
                                        <h6 class="invoice-to">Peso</h6>
                                        <div class="input-group">
                                            <input name="peso" type="number" step="any" step="1" class="form-control" placeholder="0" value="">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Kg</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2  col-sm-12">
                                        <div class="custom-control custom-switch custom-switch-glow ml-10">
                                            <h6 class="invoice-to"> Mostrar en la Web</h6>
                                            <input name="mostrar_web" type="checkbox" id="mostrar_web" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="mostrar_web">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2  col-sm-12">
                                        <div class="custom-control custom-switch custom-switch-glow ml-10">
                                            <h6 class="invoice-to"> Envio Gratis</h6>
                                            <input name="envio_gratis" type="checkbox" id="envioGratis" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="envioGratis">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2  col-sm-12">
                                        <div class="custom-control custom-switch custom-switch-glow ml-10">
                                            <h6 class="invoice-to"> Destacado</h6>
                                            <input name="destacado" type="checkbox" id="destacado" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="destacado">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-10">
                                        <input type="submit" class="btn btn-primary btn-block" name="modificar" value="GUARDAR MASIVO">
                                    </div>
                                </div>
                            </form>
                            <tbody data-url="<?= URL_ADMIN ?>" id="grid-products" data-idioma="<?= $idiomaGet ?>"></tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="text-center" id="grid-products-loader">
                            <button id="grid-products-btn" class="btn  mt-100" onclick="loadMore()">
                                CARGAR MÁS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= URL_ADMIN ?>/js/loadMoreAdminProduct.js"></script>