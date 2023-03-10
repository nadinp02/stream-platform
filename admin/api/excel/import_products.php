<?php
require_once dirname(__DIR__, 3) . "/Config/Autoload.php";
Config\Autoload::run();
$con = new Clases\Conexion();
$f = new Clases\PublicFunction();
$productos = new Clases\Productos();
$excel = new Clases\Excel();
$categorias = new Clases\Categorias();
$opcionesValor = new Clases\OpcionesValor();


$_SESSION['categorias'] = $categorias->listExcel(["area = 'productos'"], "", "", "es");

foreach (array_keys($_POST, '1', true) as $key) {
    unset($_POST[$key]);
}



$idiomaSelected = '';
if (isset($_POST['modal-idioma-select'])) {
    $idiomaSelected = $_POST['modal-idioma-select'];
    unset($_POST['modal-idioma-select']);
}

$_SESSION['import'] = $f->replaceKey($_SESSION['import'], ["variable2" => "tercercategoria"]);
$_SESSION['import'] = $f->replaceKey($_SESSION['import'], $_POST);

//Valido que esté seteado idioma
$_POST['idioma'] = $_POST['idioma'] ?? false;

//Inicializo las consultas
$pdoProducts = $con->conPDO();
$pdoProducts->beginTransaction();

$pdoOptions = $con->conPDO();
$pdoOptions->beginTransaction();

//Valido que se importen opciones para hacer el truncate de la tabla
foreach ($_POST as $attr) {
    $validateOptions = strpos($attr, '|');
    if ($validateOptions !== false) break;
}
if ($validateOptions !== false) {
    $sql = "TRUNCATE TABLE `opciones_valor`";
    $query = $con->sqlReturn($sql);
}

//recorro cada fila del excel como producto
foreach ($_SESSION['import'] as $key =>  $producto) {
    if (!empty($producto['cod'])) {
        $insert = '';
        $producto['idioma'] = (isset($producto['idioma'])) ? $producto['idioma'] : $idiomaSelected;

        //valido cada columna del producto para saber si es opcion, y si es opcion, cargarla en la DB
        foreach ($producto as $attr => $value) {
            if (array_search($attr, $_POST, true) !== false) {
                $validateType = explode('|', $attr);
                if (isset($validateType[1])) {
                    $value = trim($value, $character_mask = " \t\n\r\0\x0B$");
                    if ($value != '' && strtolower($value) != 'null') {
                        $array["idioma"] = $producto['idioma'];
                        $array["valor"] = $value;
                        $array["opcion_cod"] = $validateType[0];
                        $array["relacion_cod"] = $producto['cod'];
                        $array['cod'] = substr(md5(uniqid(rand())), 0, 10);

                        $query_opciones = [];
                        foreach ($array as $key => $value) {
                            $query_opciones[] = "`" . $key . "`= '" . $value . "'";
                        }
                        $query_opciones = implode(',', $query_opciones);
                        $pdoOptions->prepare("INSERT INTO `opciones_valor` SET $query_opciones ON DUPLICATE KEY UPDATE $query_opciones")->execute();
                    }
                    unset($producto[$attr]);
                }
            } else {
                if ($attr != 'idioma') unset($producto[$attr]);
            }
        }

        //Una vez la fila esté limpia de opciones comienzo el tratamiento del producto para el update o create
        if (isset($producto['peso'])) $producto['peso'] = intval($producto['peso']);
        if (isset($producto['stock'])) $producto['stock'] = intval($producto['stock']);
        if (isset($producto['precio'])) $producto['precio'] = $producto['precio'] != '' ? str_replace(',', '', $producto['precio']) : 0;
        if (isset($producto['precio_descuento']) && strtolower($producto['precio_descuento']) == 'null') $producto['precio_descuento'] = 0;
        if (isset($producto['precio_descuento'])) $producto['precio_descuento'] =  str_replace(',', '', $producto['precio_descuento']);
        if (isset($producto["categoria"]) || isset($producto["subcategoria"]) || isset($producto["tercercategoria"])) {
            $categoriaFinalCheck = $excel->checkCategories(isset($producto["categoria"]) ? trim($producto["categoria"]) : '', isset($producto["subcategoria"]) ?  trim($producto["subcategoria"]) : '', isset($producto["tercercategoria"]) ?  trim($producto["tercercategoria"]) : '', $producto['idioma']);
            unset($producto["categoria"]);
            unset($producto["subcategoria"]);
            unset($producto["tercercategoria"]);
            if (isset($categoriaFinalCheck["categoria"]) && !empty($categoriaFinalCheck["categoria"])) $producto['categoria'] = $categoriaFinalCheck["categoria"];
            if (isset($categoriaFinalCheck["subcategoria"]) && !empty($categoriaFinalCheck["subcategoria"]))  $producto['subcategoria'] = $categoriaFinalCheck["subcategoria"];
            if (isset($categoriaFinalCheck["tercercategoria"]) && !empty($categoriaFinalCheck["tercercategoria"]))  $producto['tercercategoria'] = $categoriaFinalCheck["tercercategoria"];
        }

        $query = [];
        foreach ($producto as $key => $productoItem) {
            $query[] = "`" . $key . "`= '" . $productoItem . "'";
        }
        $query = implode(',', $query);

        $sql = "INSERT INTO `productos` SET $query ON DUPLICATE KEY UPDATE $query";
        $pdoProducts->prepare($sql)->execute();
    }
}

//Ejecuto ambas sentencias (productos y opciones)
try {
    $pdoProducts->commit();
} catch (PDOException $ex) {
    $pdoProducts->rollback();
}
try {
    $pdoOptions->commit();
} catch (PDOException $ex) {
    $pdoOptions->rollback();
}
$response = ['status' => true, "msg" => 'Los productos se han actualizado correctamente.'];

unset($_SESSION['import']);
echo json_encode($response);
