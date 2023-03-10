<?php
require_once dirname(__DIR__, 3) . "/Config/Autoload.php";
Config\Autoload::run();
$f = new Clases\PublicFunction();

$search = $f->antihack_mysqli(isset($_GET["localidad"]) ? $_GET["localidad"] : $_GET["provincia"]);
$searchReturn = isset($_GET["localidad"]) ? $f->localidadesApi($search) :  $f->provinciaApi($search);
echo json_encode($searchReturn);
