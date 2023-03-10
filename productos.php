<?php
require_once "Config/Autoload.php";
Config\Autoload::run();

$template = new Clases\TemplateSite();
$f = new Clases\PublicFunction();
$producto = new Clases\Productos();
$promo = new Clases\Promos();
$categoria = new Clases\Categorias();
$opciones = new Clases\Opciones();

#Variables GET
$filtroOpciones = $opciones->listIfHave(["filtro" => 1, "area" => "productos"], $_SESSION['lang']);

#Variables GET
$ordenGet = isset($_GET["orden"]) ? $f->antihack_mysqli($_GET["orden"]) : '1';

#Variables GET
$tituloGet = isset($_GET["titulo"]) ? $f->antihack_mysqli(str_replace("-", " ", $_GET["titulo"])) : '';
$categoriaGet = isset($_GET["categoria"]) ? $f->antihack_mysqli($_GET["categoria"]) : '';
$subcategoriaGet = isset($_GET["subcategoria"]) ? $f->antihack_mysqli($_GET["subcategoria"]) : '';
$tercercategoriaGet = isset($_GET["tercercategoria"]) ? $f->antihack_mysqli($_GET["tercercategoria"]) : '';
$pageGet = isset($_GET["page"]) ? $f->antihack_mysqli($_GET["page"]) : 1;

$dataPrice = $producto->maxPrice();
#List de categorías del área productos
$filtroPromo = $promo->exist();
$filtroConDescuento = $producto->list(['filter' => ["precio_descuento > 0", "mostrar_web = '1'"], "limit" => 1], $_SESSION['lang'], true);
$filtroStock = $producto->list(['filter' => ["stock = 0", "mostrar_web = '1'"], "limit" => 1], $_SESSION['lang'], true);

#Información de cabecera
$template->set("title", "Productos | " . TITULO);
$template->set("description", "");
$template->set("keywords", "");
$template->themeInit();

$categoriasData = $GLOBALS["productosCategorias"];
?>
<div class="page-title-area pt-150 pb-55">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-titel-detalis">
                    <div class="section-title">
                        <h2><?= $_SESSION['lang-txt']['general']['productos'] ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="product-tab bg-white pt-30 px-2">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-12 col-custom" id="filters">
                <form id="filter-form" onsubmit="event.preventDefault();getData()">
                    <input name="orden" id="orden" type="hidden" value="<?= $ordenGet ?>" />
                    <input name="page" id="page" type="hidden" value="<?= $pageGet ?>" />
                    <aside class="sidebar_widget mt-10 mt-lg-0">
                        <div class="container">
                            <div class="cs-shop_sidebar cs-style1 ">
                                <div class="cs-shop_widget mb-10  ">
                                    <div class="row">
                                        <div class="col-12">
                                            <h3 class="fs-14 text-uppercase bold"><?= $_SESSION["lang-txt"]["productos"]["buscar_productos"] ?></h3>
                                        </div>
                                        <div class="col-7">
                                            <input class="form-control fs-14" type="text" value="<?= (!empty($tituloGet)) ? $tituloGet : '' ?>" id="titulo" name="titulo">
                                        </div>
                                        <div class="col-4">
                                            <button class=" btn btn-primary" type="submit">
                                                <i class="fa fa-search fs-14"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="cs-shop_widget mb-10">
                                    <h3 class="fs-14 text-uppercase bold pt-10">Filtrar por precio ($)</h3>
                                    <div class="row ">
                                        <div class="col-5">
                                            <span class="fs-14"><?= $_SESSION["lang-txt"]["productos"]["filtro_desde"] ?></span>
                                            <input type="text" id="min" name="min" onchange="resetPage();" placeholder="0" value="0" class="fs-13 form-control mb-10" />
                                        </div>
                                        <div class="col-5">
                                            <span class="fs-14"><?= $_SESSION["lang-txt"]["productos"]["filtro_hasta"] ?></span>
                                            <input type="text" id="max" name="max" onchange="resetPage();" placeholder="<?= $dataPrice["precio"] ?>" value="<?= $dataPrice["precio"] ?>" class="fs-13 form-control mb-10" />
                                        </div>
                                    </div>
                                </div>
                                <div class="sidebar-search mb-100 mt-20 hidden-md-up">
                                    <div class="sidebar-search-form">
                                        <div class="row">
                                            <div class="col-9">
                                                <input type="text" class="form-control" value="<?= (!empty($tituloGet)) ? $tituloGet : '' ?>" name="title" placeholder="<?= $_SESSION["lang-txt"]["productos"]["buscar_productos"] ?>">
                                            </div>
                                            <div class="col-3">
                                                <button type="submit" class="btnSearch" onclick="$('#filters').hide();">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="widget-list mb-10 mt-20">
                                    <div class="search-filter">
                                        <div class="sidbar-widget pt-0">
                                            <h4 class="title fs-14 text-uppercase bold"><?= $_SESSION["lang-txt"]["productos"]["categorias"] ?></h4>
                                        </div>
                                    </div>
                                    <div class="cs-shop_widget mb-10">
                                        <ul class="menu mt-0">
                                            <?php
                                            if (!empty($categoriasData)) {
                                                foreach ($categoriasData as $key => $cat) {
                                                    $link_cat =  URL . "/productos/b/categoria/" . $cat['data']['cod'];
                                            ?>
                                                    <li class=" list-style-none mb-0 text-uppercase drop menu-item-has-children categorias  fs-12">
                                                        <div class="sidebar-widget-list-left ">
                                                            <label for="cat-<?= $cat['data']['cod'] ?>" class="fs-12 text-uppercase">
                                                                <input id="cat-<?= $cat['data']['cod'] ?>" value="<?= $cat['data']['cod'] ?>" <?= ($categoriaGet == $cat["data"]["cod"]) ? 'checked' : '' ?> name="categoria[]" class="check auto-save-categories" type="checkbox" onchange="changeSelect('<?= $cat['data']['cod'] ?>');getData();">
                                                                <?= $cat['data']['titulo'] ?>
                                                            </label>
                                                        </div>
                                                        <ul id="<?= $cat['data']['cod'] ?>SubCat" class=" mt-1 ulProductsDropdown subcategorias pl-20 dropdown" style="<?= ($categoriaGet == $cat["data"]["cod"]) ? '' : 'display:none' ?>">
                                                            <?php
                                                            foreach ($cat["subcategories"] as $key_ => $sub) {
                                                            ?>
                                                                <li class="list-style-none">
                                                                    <div class="sidebar-widget-list-left  fs-12">
                                                                        <label>
                                                                            <input id="sub-<?= $cat['data']['cod'] ?>-<?= $sub['data']['cod'] ?>" value="<?= $sub['data']['cod'] ?>" <?= strpos($subcategoriaGet, $sub["data"]["cod"]) !== false ? 'checked' : '' ?> class="check auto-save-subcategories" name="subcategoria[]" type="checkbox" onchange="changeSelect('<?= $cat['data']['cod'] ?>','<?= $sub['data']['cod'] ?>');getData()">
                                                                            <?= $sub['data']['titulo'] ?>
                                                                        </label>
                                                                        <ul id="<?= $sub['data']['cod'] ?>TerCat" class=" mt-1 ulProductsDropdown tercercategorias pl-20 dropdown" style="<?= strpos($subcategoriaGet, $sub["data"]["cod"]) !== false ? '' : 'display:none' ?>">
                                                                            <?php
                                                                            if (!empty($sub["tercercategories"])) {
                                                                                foreach ($sub["tercercategories"] as $key3 => $ter) { ?>
                                                                                    <li class="list-style-none">
                                                                                        <label class="fs-12 text-uppercase">
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
                                        </ul>
                                        <?php
                                        if (!empty($filtroOpciones)) { ?>
                                            <div class="cs-shop_widget mb-10">
                                                <ul class="menu">
                                                    <?php foreach ($filtroOpciones as $opcion) {
                                                        $nameOption = "options['" . $opcion['data']['cod'] . "'][]";
                                                        $getOption = isset($_GET['options'][$opcion["data"]["cod"]]) ? $_GET['options'][$opcion["data"]["cod"]] : '';
                                                    ?>
                                                        <h3 class="fs-14 mt-10 bold text-uppercase"><?= $opcion['data']['titulo'] ?></h3>
                                                        <?php foreach ($opcion['valores'] as $key => $valor) {
                                                            $checkedOption = strpos($getOption, $valor['data']['valor']) !== false;
                                                            if ($opcion['data']['tipo'] == 'boolean') {
                                                                if ($valor['data']['valor'] == '1') { ?>
                                                                    <li class=" list-style-none mb-0 text-uppercase drop menu-item-has-children categorias  fs-12">
                                                                        <label for="op-<?= $opcion['data']['cod'] ?>-<?= $key ?>" class="fs-12 text-uppercase">
                                                                            <input id="op-<?= $opcion['data']['cod'] ?>-<?= $key ?>" value="<?= $valor['data']['valor'] ?>" <?= ($checkedOption) ? 'checked' : '' ?> name="<?= $nameOption ?>" class="check auto-save-categories" type="checkbox" onchange="getData();">
                                                                            SI
                                                                        </label>
                                                                    </li>
                                                                <?php }
                                                            } else { ?>
                                                                <li class=" list-style-none mb-0 text-uppercase drop menu-item-has-children categorias  fs-12">
                                                                    <label for="op-<?= $opcion['data']['cod'] ?>-<?= $key ?>" class="fs-12 text-uppercase">
                                                                        <input id="op-<?= $opcion['data']['cod'] ?>-<?= $key ?>" value="<?= $valor['data']['valor'] ?>" <?= ($checkedOption) ? 'checked' : '' ?> name="<?= $nameOption ?>" class="check auto-save-categories" type="checkbox" onchange="getData();">
                                                                        <?= $valor['data']['valor'] ?>
                                                                    </label>
                                                                </li>
                                                        <?php }
                                                        } ?>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr />
                                <div class="sidebar-widget-list-left mt-20 ">
                                    <?php if (!empty($filtroPromo)) { ?>
                                        <label for="en_promocion" class="fs-14 text-uppercase bold">
                                            <input class="auto-save" type="checkbox" name="en_promocion" id="en_promocion" value="1" onclick="resetPage();">
                                            <?= $_SESSION["lang-txt"]["productos"]["en_promocion"] ?>
                                        </label>
                                    <?php } ?>
                                    <?php if (!empty($filtroConDescuento)) { ?>
                                        <br>
                                        <label for="en_descuento" class="fs-14 text-uppercase bold">
                                            <input class="auto-save" type="checkbox" name="en_descuento" id="en_descuento" value="1" onclick="resetPage();">
                                            <?= $_SESSION["lang-txt"]["productos"]["en_descuento"] ?>
                                        </label>
                                    <?php } ?>
                                    <?php if (!empty($filtroStock)) { ?>
                                        <br>
                                        <label for="con_stock" class="fs-14 text-uppercase bold mt-10">
                                            <input class="auto-save" type="checkbox" name="con_stock" id="con_stock" value="1" onclick="resetPage();">
                                            <?= $_SESSION["lang-txt"]["productos"]["con_stock"] ?>
                                        </label>
                                    <?php } ?>
                                </div>
                                <div class="row hidden-md-up">
                                    <div class="col-6">
                                        <div onclick="$('#filters').hide();" class="btn-filter-options"><i class="fa fa-times-circle"></i> CERRAR</div>
                                    </div>
                                    <div class="col-6">
                                        <div onclick="$('#filters').hide();" class="btn-filter-options"><i class="fa fa-check-circle"></i> APLICAR</div>
                                    </div>
                                </div>
                            </div>
                    </aside>
                </form>
            </div>
            <div class="col-lg-7 mb-30">
                <div class="grid-nav-wraper bg-lighten2 mb-30">
                    <div class="row align-items-center">
                        <div class="position-relative">
                            <div class="shop-grid-button d-flex align-items-center">
                                <span class="pull-left bold fs-14 text-uppercase mr-10" style="width:70%"><?= $_SESSION["lang-txt"]["productos"]["ordenar"] ?></span>
                                <select id="order" class="fs-13 form-select custom-select auto-save" onchange="getData()">
                                    <option value="1"><?= $_SESSION["lang-txt"]["productos"]["ultimos"] ?></option>
                                    <option value="2"><?= $_SESSION["lang-txt"]["productos"]["menor_mayor"] ?></option>
                                    <option value="3"><?= $_SESSION["lang-txt"]["productos"]["mayor_menor"] ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- product-tab-nav end -->
                <div class="tab-content" id="pills-tabContent">
                    <!-- first tab-pane -->
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <div class="row grid-view theme1">
                            <div class="pull-right hidden-md-up" style="width: 100%">
                                <button id="filter-button" class="btn btn-primary btn-filter" onclick="$('#filters').show();"> <b><?= $_SESSION["lang-txt"]["productos"]["ver_filtros"] ?></b></button>
                            </div>
                            <div class="products-section shop mt-0" style="width: 100%">
                                <div class=" shop_wrapper grid_3">
                                    <div class="row grid-products" data-url="<?= URL ?>"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12 text-center mt-40">
                                <button id="grid-products-btn" class="btn btn-cart loadMoreportfolio" onclick="loadMore()">
                                    <span><?= $_SESSION["lang-txt"]["productos"]["cargar_mas"] ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3  pull-right d-none d-lg-block navCart">
                <div id="sideCart">
                    <div class="offcanvas-cart-content">
                        <h2 class="offcanvas-cart-title mb-10 fs-16  text-uppercase ">
                            <i class="fa fa-shopping-cart"></i> <?= $_SESSION["lang-txt"]["carrito"]["mi_carrito"] ?>
                        </h2>
                        <cart></cart>
                        <btn-finish-cart></btn-finish-cart>
                        <?php if (isset($_SESSION["usuarios"]["cod"])) { ?>
                            <div class="row mt-10 mb-100  text-center">
                                <div class="col-md-12 col-12 ">
                                    <a onclick="saveCartPerFile('<?= URL ?>')" class="btn btn-light fs-12"><i class="fa fa-save" aria-hidden="true"></i> GUARDAR CARRITO</a>
                                    <a href="<?= URL ?>/sesion/carritos" class="btn btn-light fs-12">VER GUARDADOS</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row mt-10 text-center">
                                <div class="col-md-12 col-12">
                                    <a href="<?= URL ?>/usuarios?carrito=1" class="btn btn-light fs-12  ">
                                        <i class="fa fa-save" aria-hidden="true"> </i> GUARDAR CARRITO
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$template->themeEnd();
?>

<script>
    jQuery(document).ready(function() {
        initPage();
    });
</script>