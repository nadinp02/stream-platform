<?php
require_once dirname(__DIR__, 3) . "/Config/Autoload.php";
Config\Autoload::run();

$perfiles_ecommerce = new Clases\PerfilesEcommerce();

$id = isset($_POST["id"]) ? $_POST["id"] : '';
$minorista = isset($_POST["minorista"]) ? $_POST["minorista"] : '';
if (!empty($id) && $minorista != '') {
    $result = $perfiles_ecommerce->changeStatus($id,$minorista);
    if (!isset($result['error'])) {
        $response = ['status' => true, 'message' => 'Perfil Activado'];
    } else {
        $response = ['status' => false, 'message' => $result['error']];
    }
} else {
    $response = ['status' => false, 'message' => 'Faltan Datos'];
}

echo json_encode($response);
