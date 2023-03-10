<?php
require_once dirname(__DIR__, 3) . "/Config/Autoload.php";
Config\Autoload::run();
$categorias = new Clases\Categorias();
$f = new Clases\PublicFunction();

$area = isset($_GET['area']) ?  $f->antihack_mysqli($_GET['area']) : "''";
$idioma = isset($_GET['idioma']) ?  $f->antihack_mysqli($_GET['idioma']) : "es";
$categoriasData = ($area == 'productos' || $area == 'banners') ? $categorias->listIfHave($area, '', $idioma) : $categorias->listIfHave('contenidos', $area, $idioma);
$categoriasOpciones =  $categorias->list(["`categorias`.`area` = 'opciones'"], '', '', $_SESSION['lang'], false, false);
$categoryData = '';
if(empty($categoriasData) || empty($categoriasOpciones)){
    if(empty($categoriasData)){
        $categoryData = $categoriasOpciones;
    }
    if(empty($categoriasOpciones)){
        $categoryData = $categoriasData;
    }
}else{
    $categoryData = array_merge($categoriasOpciones,$categoriasData );
}

$result = ["status" => true, "categories" => $categoryData];

echo json_encode($result, JSON_PRETTY_PRINT);
