<?php
require_once dirname(__DIR__, 3) . "/Config/Autoload.php";
Config\Autoload::run();
$f = new Clases\PublicFunction();
$imagenes = new Clases\Imagenes();

$id =  isset($_POST["id"]) ? $f->antihack_mysqli($_POST["id"]) : '';
$idioma =  isset($_POST["idioma"]) ? $f->antihack_mysqli($_POST["idioma"]) : '';

if ($id != '' && $idioma != '') {
    $result = $imagenes->delete(['id' => $id, 'idioma' => $idioma]);
    if (!isset($result['error'])) {
        $response = ['status' => true, 'message' => 'Imagen Eliminada'];
    } else {
        $response = ['status' => false, 'message' => $result['error']];
    }
} else {
    $response = ['status' => false, 'message' => 'Faltan Datos'];
}

echo json_encode($response);
