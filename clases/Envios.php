<?php

namespace Clases;

class Envios extends Conexion
{

    //Atributos
    public $id;
    public $cod;
    public $titulo;
    public $peso;
    public $precio;
    public $estado;
    public $opciones;
    public $descripcion;
    public $limite;
    public $tipo_usuario;
    public $idioma;
    private $table = "envios";

    public function set($atributo, $valor)
    {
        if ($valor != '') {
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

    public function add($array)
    {
        $attr = implode(",", array_keys($array));
        $values = ":" . str_replace(",", ",:", $attr);
        $sql = "INSERT INTO $this->table ($attr) VALUES ($values)";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($array);
    }

    public function edit($array, $cod)
    {
        $query = implode(", ", array_map(function ($v) {
            return "$v=:$v";
        }, array_keys($array)));
        $condition = implode(' AND ', $cod);
        $sql = "UPDATE $this->table  SET $query WHERE $condition";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($array);
    }

    public function changeState()
    {
        $sql = "UPDATE $this->table  SET `estado`={$this->estado} AND `idioma`={$this->idioma} WHERE `cod`={$this->cod} AND `idioma`={$this->idioma}";
        $query = parent::sql($sql);
        return !empty($query) ? true : false;
    }
    public function delete()
    {
        $sql = "DELETE FROM $this->table WHERE `cod`  = {$this->cod} AND  `idioma`  = {$this->idioma}";
        $query = parent::sql($sql);

        return !empty($query) ? true : false;
    }
    public function view()
    {
        $row_ = [];
        $sql = "SELECT * FROM $this->table WHERE cod = {$this->cod} AND idioma = {$this->idioma} ORDER BY id DESC";
        $envios = parent::sqlReturn($sql);
        if (mysqli_num_rows($envios)) {
            $row = mysqli_fetch_assoc($envios);
            $row['precio'] = (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["mostrar_precios"] == 1) ?  $row['precio'] : 0;
            $row_ = array("data" => $row);
        }
        return $row_;
    }

    public function list($filter = [], $order, $limit, $idioma)
    {
        $array = [];
        $filter[] = $this->table . ".idioma = '$idioma'";
        $filterSql = (is_array($filter)) ? 'WHERE ' . implode(' AND ', $filter) : '';
        $orderSql = ($order != '') ? $order : $this->table . ".id DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : '';

        $sql = "SELECT $this->table.*, imagenes.ruta AS img FROM $this->table LEFT JOIN imagenes ON imagenes.cod = envios.cod  $filterSql  ORDER BY $orderSql $limitSql";
        $envios = parent::sqlReturn($sql);
        if ($envios) {
            while ($row = mysqli_fetch_assoc($envios)) {
                $row['img'] = (!$row['img']) ? LOGO : URL . "/" . $row['img'];
                $row['precio'] = (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["mostrar_precios"] == 1) ? $row['precio'] : 0;
                $array[] = array("data" => $row);
            }
            return $array;
        }
    }
}
