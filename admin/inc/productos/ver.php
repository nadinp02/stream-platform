<?php
$producto = new Clases\Productos();
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

?>
<div id="permisos" data-editar="<?= $_SESSION["admin"]["crud"]["editar"] ?>" data-eliminar="<?= $_SESSION["admin"]["crud"]["eliminar"] ?>"></div>
<section id="table-transactions" class="mt-30">
    <h4 class="mt-20 pull-left text-uppercase">Productos</h4>
    <?php if ($_SESSION["admin"]["crud"]["crear"]) { ?>
        <a class="btn btn-primary pull-right text-uppercase mt-15" href="<?= URL_ADMIN ?>/index.php?op=productos&accion=agregar&idioma=<?= $idiomaGet ?>">
            AGREGAR PRODUCTOS
        </a>
        <a class="btn btn-outline-info pull-right text-uppercase mt-15 btn-product-ver" href="<?= URL_ADMIN ?>/index.php?op=productos&accion=importar">
            IMPORTAR
        </a>
        <a class="btn btn-outline-info pull-right text-uppercase mt-15 btn-product-ver" href="<?= URL_ADMIN ?>/index.php?op=productos&accion=exportar">
            EXPORTAR
        </a>
    <?php } ?>
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
                            <form id="filter-form" onsubmit="event.preventDefault();getData()">
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
                                        <select name="order" id="order" onchange="getData()">
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
                                            <input type="radio" id="mostrarWeb" <?= ($mostrarGet == 1) ? 'checked' : '' ?> name="mostrar_web" value="1" onchange="getData()">
                                            Si</label>
                                        <label for="no_mostrarWeb">
                                            <input class="ml-10" type="radio" id="no_mostrarWeb" <?= ($mostrarGet == 0) ? 'checked' : '' ?> name="mostrar_web" value="0" onchange="getData()">
                                            No</label>
                                        <label for="todo_mostrarWeb">
                                            <input class="ml-10" type="radio" id="todo_mostrarWeb" <?= ($mostrarGet == 2) ? 'checked' : '' ?> name="mostrar_web" value="2" onchange="getData()">
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
                                                                <input id="cat-<?= $cat['data']['cod'] ?>" value="<?= $cat['data']['cod'] ?>" <?= ($categoriaGet == $cat["data"]["cod"]) ? 'checked' : '' ?> name="categoria[]" class="check auto-save-categories" type="checkbox" onchange="changeSelect('<?= $cat['data']['cod'] ?>');getData();">
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
                                                                            <input id="sub-<?= $cat['data']['cod'] ?>-<?= $sub['data']['cod'] ?>" value="<?= $sub['data']['cod'] ?>" <?= strpos($subcategoriaGet, $sub["data"]["cod"]) !== false ? 'checked' : '' ?> class="check auto-save-subcategories" name="subcategoria[]" type="checkbox" onchange="changeSelect('<?= $cat['data']['cod'] ?>','<?= $sub['data']['cod'] ?>');getData()">
                                                                            <?= $sub['data']['titulo'] ?>
                                                                        </label>
                                                                        <ul id="<?= $sub['data']['cod'] ?>TerCat" class="ulProductsDropdown tercercategorias pl-20 dropdown" style="<?= strpos($subcategoriaGet, $sub["data"]["cod"]) !== false ? '' : 'display:none' ?>">
                                                                            <?php
                                                                            if (!empty($sub["tercercategories"])) {
                                                                                foreach ($sub["tercercategories"] as $key3 => $ter) { ?>
                                                                                    <li class="d-block  list-style-none my-0">
                                                                                        <label class="  text-uppercase my-0">
                                                                                            <input id="ter-<?= $ter["data"]["cod"] ?>" value="<?= $ter['data']['cod'] ?>" <?= strpos($tercercategoriaGet, $ter['data']['cod']) !== false ? 'checked' : '' ?> class="check auto-save-tercercategories" name="tercercategoria[]" type="checkbox" onchange="getData()">
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
                                    <th class=" titulo " width="20%">Titulo</th>
                                    <th class=" precio ">Precio</th>
                                    <th class=" precio_descuento ">Precio Descuento</th>
                                    <th class=" precio_mayorista ">Precio Mayorista</th>
                                    <th class=" categoria ">Categoria</th>
                                    <th class=" subcategoria ">Subcategoria</th>
                                    <th class=" keywords ">Keywords</th>
                                    <th class=" stock ">Stock</th>
                                    <th class=" peso ">Peso</th>
                                    <th class=" destacado ">DESTACADO</th>
                                    <th class=" envio_gratis "><i class="fa fa-truck"></i> Gratis</th>
                                    <th class=" mostrar_web ">Disponible</th>
                                    <th class="text-right"> Ajustes</th>
                                </tr>
                            </thead>
                            <div class="dropdown pull-right">
                                <button class="btn btn-secondary glow  dropdown-toggle " type="button" id="dropdownMenuButton" data-toggle="dropdown">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <div class="dropdown-menu  bar" aria-labelledby="dropdownMenuButton">
                                    <label class="dropdown-item" for="lb-titulo"><input id="lb-titulo" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('titulo')"> Título </label>
                                    <label class="dropdown-item" for="lb-precio"><input id="lb-precio" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('precio')"> Precio </label>
                                    <label class="dropdown-item" for="lb-precio_descuento"><input id="lb-precio_descuento" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('precio_descuento')"> Descuento</label>
                                    <label class="dropdown-item" for="lb-precio_mayorista"><input id="lb-precio_mayorista" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('precio_mayorista')"> Mayorista</label>
                                    <label class="dropdown-item" for="lb-categoria"><input id="lb-categoria" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('categoria')"> Categoria</label>
                                    <label class="dropdown-item" for="lb-subcategoria"><input id="lb-subcategoria" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('subcategoria')"> Subcategoria</label>
                                    <label class="dropdown-item" for="lb-keywords"><input id="lb-keywords" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('keywords')"> Keywords</label>
                                    <label class="dropdown-item" for="lb-stock"><input id="lb-stock" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('stock')"> Stock</label>
                                    <label class="dropdown-item" for="lb-peso"><input id="lb-peso" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('peso')"> Peso (kg)</label>
                                    <label class="dropdown-item" for="lb-destacado"><input id="lb-destacado" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('destacado')"> <i class="fa fa-star"></i></label>
                                    <label class="dropdown-item" for="lb-envio_gratis"><input id="lb-envio_gratis" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('envio_gratis')"> <i class="fa fa-truck"></i> Gratis</label>
                                    <label class="dropdown-item" for="lb-mostrar_web"><input id="lb-mostrar_web" class="checkbox-menu-products mr-10" style="width:20px;height:20px" type="checkbox" onchange="toggleColumn('mostrar_web')"> Disponible</label>
                                </div>
                            </div>
                            <ul class="nav nav-tabs">
                                <?php
                                foreach ($idiomas->list("", "id ASC", "") as $key => $idioma_) {
                                    $url =  URL_ADMIN . "/index.php?op=productos&accion=ver&idioma=" . $idioma_["data"]["cod"];
                                ?>
                                    <a class="nav-link <?= $idioma_["data"]["cod"] == $idiomaGet ? "active" : '' ?> " href="<?= $url ?>"><?= mb_strtoupper($idioma_["data"]["titulo"]) ?></a>
                                <?php } ?>
                            </ul>
                            <tbody data-url="<?= URL_ADMIN ?>" id="grid-products" data-idioma="<?= $idiomaGet ?>"></tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 ">
                        <div id="error-msg"></div>
                        <div class="text-center" id="grid-products-loader">
                            <button id="grid-products-btn" class="btn  mt-100" onclick="loadMore()">
                                CARGAR MÁS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- datatable ends -->
    </div>
</section>

<?php

if (isset($_GET["borrar"]) && $_SESSION["admin"]["crud"]["eliminar"]) {
    $cod = isset($_GET["borrar"]) ? $funciones->antihack_mysqli($_GET["borrar"]) : '';
    $producto->delete(['cod' => $cod, 'idioma' => $idiomaGet]);
    $funciones->headerMove(URL_ADMIN . "/index.php?op=productos&accion=ver&idioma=" . $idiomaGet);
}
?>

<script src="<?= URL_ADMIN ?>/js/loadMoreAdminProduct.js"></script>