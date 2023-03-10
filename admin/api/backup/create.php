<?php
require_once dirname(__DIR__, 3) . "/Config/Autoload.php";
Config\Autoload::run();
$f = new Clases\PublicFunction();
$backup  = new Clases\Backup();
$backup->create(false, dirname(__DIR__, 3));
echo json_encode(array("status" => true, "message" => "Backup restaurado con exito"));
