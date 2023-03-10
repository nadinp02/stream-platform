<?php
require_once dirname(__DIR__, 2) . "/Config/Autoload.php";
Config\Autoload::run();
$f = new Clases\PublicFunction();
$producto = new Clases\Productos();
$categoria = new Clases\Categorias();
$subcategoria = new Clases\Subcategorias();
$productosRelacionados = new Clases\ProductosRelacionados();

$search = isset($_POST['titulo']) ?  $f->antihack_mysqli($_POST['titulo']) : '';
$order = isset($_GET['order']) ?  $f->antihack_mysqli($_GET['order']) : '';
$start = isset($_GET['start']) ?  $f->antihack_mysqli($_GET['start']) : '0';
$limit = isset($_GET['limit']) ?  $f->antihack_mysqli($_GET['limit']) : '24';
if (isset($_POST['en_promocion'])) $en_promocion = $f->antihack_mysqli($_POST['en_promocion']);
if (isset($_POST['en_descuento'])) $en_descuento = $f->antihack_mysqli($_POST['en_descuento']);
if (isset($_POST['con_stock'])) $con_stock = $f->antihack_mysqli($_POST['con_stock']);
if (isset($_POST['destacado'])) $destacado = $f->antihack_mysqli($_POST['destacado']);
$favorite = isset($_POST['favorite']) ?  $f->antihack_mysqli($_POST['favorite']) : false;

$options = isset($_POST['options']) ? $_POST['options'] : '';

$catsFilter = [];
if (isset($destacado)) $filter[] = 'productos.destacado = 1';
if (isset($con_stock)) $filter[] = 'productos.stock > 0';
if (isset($en_descuento)) $filter[] = 'productos.precio_descuento > 0';
if (isset($en_promocion)) $filter[] = 'promos.lleva > 0 AND promos.paga > 0';

$min = isset($_POST['min']) ? $f->antihack_mysqli($_POST['min']) : 0;
$min = str_replace("$", '', trim($min));
$max = isset($_POST['max']) ? $f->antihack_mysqli($_POST['max']) : '';
$max = str_replace("$", '', trim($max));


//Filtros
$filter[] = 'productos.mostrar_web = 1';

if (!empty($min) || !empty($max))  $filter[] = '(`productos`.`precio` BETWEEN ' . intval($min) . ' AND ' . intval($max) . ")";

if (!empty($search)) {
    $search = trim($search);
    $search_array = explode(' ', $search);
    $searchSql = '(';
    foreach ($search_array as $key => $searchData) {
        if ($key == 0) {
            $searchSql .= "productos.cod_producto LIKE '%$searchData%' OR productos.titulo LIKE '%$searchData%'";
        } else {
            $searchSql .= " AND productos.titulo LIKE '%$searchData%'";
        }
    }

    $searchSql .= ')';
    $filter[] = $searchSql;
    $order = 'search';
}

$optionsFilter = '';
if ($options) {
    foreach ($options as $key => $option) {
        $key = str_replace("'", "", $key);
        $optionsFilter .= " INNER JOIN `opciones_valor` AS `" . $key . "` ON `" . $key . "`.`opcion_cod` = '" . $key . "' AND `" . $key . "`.`idioma` = '" . $_SESSION['lang'] . "' AND ( `" . $key . "`.`relacion_cod` = `productos`.`cod` ";
        foreach ($option as $key2 => $valor) {
            if ($key2 == 0) {
                $optionsFilter .= " AND (`" . $key . "`.`valor` = '" . $valor . "'";
            } else {
                $optionsFilter .= " OR  `" . $key . "`.`valor`  = '" . $valor . "'";
            }
        }
        $optionsFilter .= ")) ";
    }
}

if (!empty($_POST['categoria'])) {
    foreach ($_POST['categoria'] as $key => $cat) {
        $cat_ = $f->antihack_mysqli($cat);
        if (!empty($cat_)) $cats[] = "'" . $cat_ . "'";
    }
    $catsImplode = implode(",", $cats);
    $catsFilter[] = "productos.categoria IN (" . $catsImplode . ")";
}

if (!empty($_POST['subcategoria'])) {
    foreach ($_POST['subcategoria'] as $key2 => $sub) {
        $subcat_ = $f->antihack_mysqli($sub);
        if (!empty($subcat_)) $subcats[] = "'" . $subcat_ . "'";
    }
    $subcatsImplode = implode(",", $subcats);
    $catsFilter[] = "productos.subcategoria IN (" . $subcatsImplode . ")";
}

if (!empty($_POST['tercercategoria'])) {
    foreach ($_POST['tercercategoria'] as $key3 => $ter) {
        $tercercat_ = $f->antihack_mysqli($ter);
        if (!empty($tercercat_)) $tercercats[] = "'" . $tercercat_ . "'";
    }
    $tercercatsImplode = implode(",", $tercercats);
    $catsFilter[] = "productos.tercercategoria IN (" . $tercercatsImplode . ")";
}

count($catsFilter) ? $filter[] = "(" . implode(" AND ", $catsFilter) . ")" : '';


switch ($order) {
    case "1":
        $order = "categorias.orden ASC, categorias.titulo ASC, subcategorias.orden ASC, subcategorias.titulo ASC, productos.titulo ASC";
        break;
    case "2":
        $order = "productos.precio ASC";
        break;
    case "3":
        $order = "productos.precio DESC";
        break;
    case "search":
        $order =  "CASE WHEN `productos`.`titulo` LIKE '%$search%'  THEN `productos`.`titulo` END DESC";
        break;
    default:
        $order = "categorias.orden ASC, categorias.titulo ASC, subcategorias.orden ASC, subcategorias.titulo ASC, productos.titulo ASC";
        break;
}


if (empty($filter)) $filter = '';
$data = [
    "filter" => $filter,
    "admin" => false,
    "category" => true,
    "subcategory" => true,
    "tercercategory" => true,
    "images" => 'single',
    "promos" => true,
    "limit" => $start . "," . $limit,
    "order" => $order,
    "options" => ($optionsFilter) ? $optionsFilter : false,
    "favorite" => $favorite
];
$productosData = $producto->list($data, $_SESSION['lang']);

if (!empty($productosData)) {
    echo json_encode(["limit" => $start . "," . $limit, "count" => count($productosData), "products" => $productosData, "user" => isset($_SESSION["usuarios"]) ? $_SESSION["usuarios"] : []]);
}
