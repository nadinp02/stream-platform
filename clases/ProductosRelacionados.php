<?php

namespace Clases;

class ProductosRelacionados extends Conexion
{
    //Atributos
    public $id;
    public $titulo;
    public $productos_cod;
    public $cod;
    public $idioma;

    public function set($atributo, $valor)
    {
        if (($atributo == "tipo" && empty($valor)) || ($atributo == "sector" && empty($valor))) {
            $valor = 0;
        } else {
            if (!empty($valor)) {
                $valor = "'" . $valor . "'";
            } else {
                $valor = "NULL";
            }
        }

        $this->$atributo = $valor;
    }

    public function get($atributo)
    {
        return $this->$atributo;
    }

    public function add()
    {
        $sql = "INSERT INTO `productos_relacionados`(`cod`,`titulo`, `productos_cod`,`idioma`) 
                  VALUES ({$this->cod},
                          {$this->titulo},
                          {->_cod},
                          {$this->idioma})";
        $query = parent::sql($sql);
        return !empty($query) ? true : false;
    }

    public function edit()
    {
        $sql = "UPDATE `productos_relacionados` 
                  SET `cod`={$this->cod},
                      `titulo`={$this->titulo},
                      `productos_cod`={->_cod},
                      `idioma`={$this->idioma}
                  WHERE `id`={$this->id} AND `idioma`={$this->idioma}";
        $query = parent::sql($sql);
        return !empty($query) ? true : false;
    }

    public function delete()
    {
        $sql = "DELETE FROM `productos_relacionados` WHERE `cod`  = {$this->cod} AND `idioma`  = {$this->idioma}";
        $query = parent::sql($sql);

        return !empty($query) ? true : false;
    }

    public function view()
    {
        $sql = "SELECT * FROM productos_relacionados WHERE cod = {$this->cod} AND idioma = {$this->idioma}  ";
        $notas = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($notas);
        $row_ = array("data" => $row);
        return $row_;
    }

    public function list($filter, $order, $limit, $idioma = '')
    {
        if (empty($idioma)) $idioma = $_SESSION['lang'];
        is_array($filter) ? $filter[] = "productos_relacionados.idioma = '" . $idioma . "' " : $filter = "productos_relacionados.idioma = '" . $idioma . "'";
        $filterSql = (is_array($filter)) ? "WHERE " . implode(" AND ", $filter) : '';
        $orderSql = ($order != '') ? $order : "id DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : "";

        $sql = "SELECT * FROM `productos_relacionados` $filterSql  ORDER BY $orderSql $limitSql";
        $products = parent::sqlReturn($sql);
        $related = "";
        if ($products) {
            while ($row = mysqli_fetch_assoc($products)) {
                $related .= $row["productos_cod"] . ",";
            }
        }
        $explodeRow = explode(",", $related);
        $explodeRowFinal = array_unique($explodeRow);
        $explodeRowFinal = array_diff($explodeRowFinal, array("", 0, null));
        return $explodeRowFinal;
    }

    public function listAdmin($filter, $idioma = '')
    {
        $array = array();
        is_array($filter) ? $filter[] = "productos_relacionados.idioma = '" . $idioma . "' " : $filter = "productos_relacionados.idioma = '" . $idioma . "'";
        $filterSql = (is_array($filter)) ? "WHERE " . implode(" AND ", $filter) : '';

        $sql = "SELECT * FROM `productos_relacionados` $filterSql ORDER BY `productos_relacionados`.`id` DESC";
        $product = parent::sqlReturn($sql);
        if ($product) {
            while ($row = mysqli_fetch_assoc($product)) {
                $array[] = array("data" => $row);
            }
        }
        return $array;
    }

    public function CodRelatedProducts($cod_producto)
    {
        $sql = "SELECT `titulo` FROM `productos_relacionados` WHERE  productos_cod LIKE '%$cod_producto%'";
        $value = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($value);
        return $row;
    }
}
