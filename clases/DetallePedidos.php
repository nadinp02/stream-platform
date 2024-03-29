<?php

namespace Clases;

class DetallePedidos
{

    //Atributos
    public $id;
    public $cod;
    public $producto;
    public $cantidad;
    public $promo;
    public $precio;
    public $tipo;
    public $descuento;
    public $cod_producto;
    public $producto_cod;
    public $cuotas;
    private $con;
    private $productosClass;

    //Metodos
    public function __construct()
    {
        $this->con = new Conexion();
        $this->productosClass = new Productos();
    }

    public function set($atributo, $valor)
    {
        if (!empty($valor)) {
            $valor = "'" . $valor . "'";
        } else {
            $valor = "NULL";
        }
        $this->$atributo = $valor;
    }

    public function get($atributo)
    {
        return $this->$atributo;
    }

    public function add()
    {
        $sql = "INSERT INTO `detalle_pedidos`(`cod`, `producto`,`cantidad`,`promo`,`precio`, `tipo`, `descuento`, `cod_producto`,`producto_cod`, `cuotas`) 
                VALUES ({$this->cod}, 
                        {$this->producto},
                        {$this->cantidad},
                        {$this->promo},
                        {$this->precio}, 
                        {$this->tipo}, 
                        {$this->descuento}, 
                        {$this->cod_producto},
                        {$this->producto_cod},
                        {$this->cuotas})";
        $query = $this->con->sqlReturn($sql);

        return !empty($query) ? true : false;
    }

    public function addCarrito($carro, $cod_pedido, $reduceStock = false)
    {
        $this->reset($cod_pedido);

        foreach ($carro as $carroItem) {
            $producto_cod = isset($carroItem["producto_cod"]) ? $carroItem["producto_cod"] : "Descuento";
            $descuento = json_encode($carroItem["descuento"]);
            if ($carroItem["descuento"] == "") $descuento = "";
            if ($descuento == "null")  $descuento = "";
            #Agrega el detalle
            $this->set("cod", $cod_pedido);
            $this->set("producto", isset($carroItem["opciones"]["texto"]) ? $carroItem["titulo"] . " " . $carroItem["opciones"]["texto"] : $carroItem["titulo"]);
            $this->set("cantidad", $carroItem["cantidad"]);
            $this->set("promo", $carroItem["promo"]);
            $this->set("precio", $carroItem["precio"]);
            $this->set("tipo", strToUpper($carroItem['tipo']));
            $this->set("descuento", $descuento);
            $this->set("cod_producto",  $carroItem["id"]);
            $this->set("producto_cod",  $producto_cod);
            $this->set("cuotas", '');
            if ($this->add()) {
                if ($reduceStock && $_SESSION["perfil-ecommerce"]["data"]["usar_stock"] == 1) {
                    $this->productosClass->reduceStock($carroItem["id"], $carroItem["cantidad"], $carroItem["tipo"]);
                }
            }
        }
    }

    public function editSingle($atributo, $valor)
    {
        $sql = "UPDATE `detalle_pedidos` SET `$atributo` = '{$valor}' WHERE `cod`={$this->cod}";
        $this->con->sql($sql);
    }

    public function editSingleShipping($atributo, $valor)
    {
        $sql = "UPDATE `detalle_pedidos` SET `$atributo` = '{$valor}' WHERE `cod`={$this->cod} AND `tipo`= {$this->tipo}";
        $this->con->sql($sql);
    }

    public function delete($id)
    {
        $sql   = "DELETE FROM `detalle_pedidos` WHERE `id`  = {$id}";
        $query = $this->con->sqlReturn($sql);
        return $query;
    }
    public function reset($cod)
    {
        $sql   = "DELETE FROM `detalle_pedidos` WHERE `cod`  = '$cod'";
        $query = $this->con->sqlReturn($sql);
        return $query;
    }
    public function list($cod)
    {
        $array = array();
        $sql = "SELECT * FROM `detalle_pedidos` WHERE `cod` = {$cod} ORDER BY (`tipo` = 'PR') DESC, `id` ASC";
        $result = $this->con->sqlReturn($sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $array[] = $row;
            }
            return $array;
        }
    }


    function topBuy($limit)
    {
        $array = array();

        $sql = "SELECT `detalle_pedidos`.`cod_producto`, `detalle_pedidos`.`producto`,`detalle_pedidos`.`producto_cod`, COUNT(*) as `cantidad_pedidos`, SUM(detalle_pedidos.cantidad) AS `cantidad_vendida` FROM `detalle_pedidos` 
        LEFT JOIN `pedidos` ON `pedidos`.`cod` = `detalle_pedidos`.`cod` 
        LEFT JOIN `estados_pedidos` ON `pedidos`.`estado` = `estados_pedidos`.`id` 
        WHERE `estados_pedidos`.`estado` != '0' AND `estados_pedidos`.`estado` != '3'  AND detalle_pedidos.tipo = 'PR' 
        GROUP BY `detalle_pedidos`.`cod_producto` ORDER BY `cantidad_vendida` DESC LIMIT $limit";
        $result = $this->con->sqlReturn($sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $array[] = ["data" => $row];
            }
            return $array;
        }
    }
    function topBuyPerProvince($limit, $status = "", $provincia = "", $categoria = "", $subcategoria = "", $fecha = "")
    {
        $array = array();
        $where = "";
        $limite = "";
        $statusText = "";
        if (!empty($status) || $status === 0) $statusText = "`estados_pedidos`.`id` = $status AND";
        if (!empty($limit)) $limite = " LIMIT $limit";
        if (!empty($provincia)) $where .= " `usuarios`.`provincia` = '$provincia' AND ";
        if (!empty($categoria)) $where .= " `productos`.`categoria` = '$categoria' AND ";
        if (!empty($subcategoria)) $where .= " `productos`.`subcategoria` = '$subcategoria' AND ";
        if (!empty($fecha)) $where .= " `pedidos`.`fecha` BETWEEN  STR_TO_DATE('" . $fecha[0] . "','%d/%m/%Y %H:%i:%s') AND STR_TO_DATE('" . $fecha[1] . "','%d/%m/%Y %H:%i:%s') AND ";
        $sql = "SELECT `usuarios`.`provincia`,`detalle_pedidos`.`cod_producto`, `detalle_pedidos`.`producto`,`productos`.`categoria`,`productos`.`subcategoria`,`pedidos`.`fecha`,
            `detalle_pedidos`.`producto_cod`, COUNT(*) as `cantidad_pedidos`, 
            SUM(`detalle_pedidos`.`cantidad`) AS `cantidad_vendida` 
        FROM `detalle_pedidos` 
            LEFT JOIN `pedidos` ON `pedidos`.`cod` = `detalle_pedidos`.`cod` 
            LEFT JOIN `estados_pedidos` ON `pedidos`.`estado` = `estados_pedidos`.`id` 
            LEFT JOIN `usuarios` ON `pedidos`.`usuario` = `usuarios`.`cod` 
            LEFT JOIN `productos` ON `detalle_pedidos`.`cod_producto` = `productos`.`cod`
        WHERE $where $statusText `detalle_pedidos`.`tipo` = 'PR' GROUP BY `detalle_pedidos`.`producto_cod` ,`usuarios`.`provincia` 
	        ORDER BY `cantidad_vendida` DESC  $limite";
        $result = $this->con->sqlReturn($sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $array[] = ["data" => $row];
            }
            return $array;
        }
    }
}
