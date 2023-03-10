<?php
require_once dirname(__DIR__, 2) . "/Config/Autoload.php";
Config\Autoload::run();
$pedidos = new Clases\Pedidos();
$funciones = new Clases\PublicFunction();
$envios_pedidos = new Clases\EnviosPedidos();
$pagos_pedidos = new Clases\PagosPedidos();

$data = isset($_POST["id"]) ? $funciones->antihack_mysqli($_POST["id"]) : '';
$data = explode('-', $data);
$soft_delete = ['soft_delete' => 1];
$result = false;
if ($data[0] == 'envios') $result = $envios_pedidos->edit($soft_delete,  ["id = '$data[1]'", "usuario = '$data[2]'"]);
if ($data[0] == 'pagos') $result = $pagos_pedidos->edit($soft_delete, ["id = '$data[1]'", "usuario = '$data[2]'"]);
echo json_encode(['status' => $result, 'id' => $data[1]]);
