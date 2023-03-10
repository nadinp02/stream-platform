<?php
require_once dirname(__DIR__, 3) . "/Config/Autoload.php";
Config\Autoload::run();
$producto = new Clases\Productos();
$categoria = new Clases\Categorias();
$f = new Clases\PublicFunction();
$idiomaGet = isset($_GET["idioma"]) ? $f->antihack_mysqli($_GET["idioma"]) : $_SESSION['lang'];
$filter = [];
$start = isset($_GET['start']) ?  $f->antihack_mysqli($_GET['start']) : '0';
$full = isset($_GET['full']) ?  $_GET['full'] : false;
$images = isset($_GET['images']) ?  $_GET['images'] : false;
$limit = isset($_GET['limit']) ?  $f->antihack_mysqli($_GET['limit']) : '24';
$search = isset($_POST['title']) ?  $f->antihack_mysqli($_POST['title']) : '';
$order = isset($_POST['order']) ?  $f->antihack_mysqli($_POST['order']) : '';


$mostrarWeb = isset($_POST['mostrar_web']) ?  $f->antihack_mysqli($_POST['mostrar_web']) : 2;

if ($mostrarWeb != 2) {
    $filter[] = "productos.mostrar_web = " . $mostrarWeb;
}

$catsFilter = [];
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
}


if (!empty($_POST['categories'])) {
    foreach ($_POST['categories'] as $key => $cat) {
        $cat_ = $f->antihack_mysqli($cat);
        if (!empty($cat_)) $cats[] = "'" . $cat_ . "'";
    }
    $catsImplode = implode(",", $cats);
    $catsFilter[] = "productos.categoria IN (" . $catsImplode . ")";
}

if (!empty($_POST['subcategories'])) {
    foreach ($_POST['subcategories'] as $key2 => $sub) {
        $subcat_ = $f->antihack_mysqli($sub);
        if (!empty($subcat_)) $subcats[] = "'" . $subcat_ . "'";
    }
    $subcatsImplode = implode(",", $subcats);
    $catsFilter[] = "productos.subcategoria IN (" . $subcatsImplode . ")";
}

if (!empty($_POST['tercercategories'])) {
    foreach ($_POST['tercercategories'] as $key3 => $ter) {
        $tercercat_ = $f->antihack_mysqli($ter);
        if (!empty($tercercat_)) $tercercats[] = "'" . $tercercat_ . "'";
    }
    $tercercatsImplode = implode(",", $tercercats);
    $catsFilter[] = "productos.tercercategoria IN (" . $tercercatsImplode . ")";
}

count($catsFilter) ? $filter[] = "(" . implode(" AND ", $catsFilter) . ")" : '';


$categoriasData = $categoria->list(["area = 'productos'"], "titulo ASC", "", $idiomaGet);


switch ($order) {
    case "1":
        $order = "productos.precio ASC";
        break;
    case "2":
        $order = "productos.precio DESC";
        break;
    case "3":
        $order = "productos.stock ASC";
        break;
    case "4":
        $order = "productos.stock DESC";
        break;
    case "5":
        $order = "productos.categoria ASC";
        break;
    case "6":
        $order = "productos.categoria DESC";
        break;
    case "7":
        $order = "productos.titulo ASC";
        break;
    case "8":
        $order = "productos.titulo DESC";
        break;
    case "9":
        $order = "productos.destacado ASC";
        break;
    case "10":
        $order = "productos.destacado DESC";
        break;
    case "11":
        $order = "productos.mostrar_web ASC";
        break;
    case "12":
        $order = "productos.mostrar_web DESC";
        break;
    case "13":
        $order = "productos.envio_gratis ASC";
        break;
    case "14":
        $order = "productos.envio_gratis DESC";
        break;
    default:
        $order = "productos.id DESC";
        break;
}



$data = [
    "filter" => $filter,
    "admin" => true,
    "promos" => true,
    "images" => ($full) ? true : false,
    "category" => !($full) ? true : false,
    "subcategory" => !($full) ? true : false,
    "tercercategory" => !($full) ? true : false,
    "order" => $order,
    "limit" =>  $start . "," . $limit,
];

$productos = $producto->list($data, $idiomaGet);
?>

<?php
if (!empty($productos)) {
    $result = ["product" => $productos, "category" => $categoriasData];
    echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    echo json_encode(false);
}
