<?php

namespace Clases;

class PagosPedidos extends Conexion
{
    public $id;
    public $usuario;
    public $nombre;
    public $apellido;
    public $email;
    public $telefono;
    public $celular;
    public $documento;
    public $postal;
    public $provincia;
    public $localidad;
    public $calle;
    public $numero;
    public $piso;
    public $factura;
    public $fecha;
    public $fecha_update;
    //Metodos
    public function __construct()
    {
        
        if ($_ENV["DEVELOPMENT"]) {
            parent::sql("
                CREATE TABLE IF NOT EXISTS `pagos_pedidos` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `nombre` varchar(50) NOT NULL,
                `apellido` varchar(50) NOT NULL,
                `email` varchar(100) NOT NULL,
                `telefono` varchar(16) NOT NULL,
                `celular` varchar(10) NOT NULL,
                `documento` varchar(150) NOT NULL,
                `postal` varchar(5) NOT NULL,
                `provincia` varchar(100) NOT NULL,
                `localidad` varchar(100) NOT NULL,
                `calle` varchar(150) NOT NULL,
                `numero` varchar(7) NOT NULL,
                `piso` varchar(10) DEFAULT NULL,
                `pago` varchar(10) NOT NULL,
                `factura` boolean DEFAULT false,
                `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `fecha_update` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }


    public function checkArrayWithAttr($array)
    {
        foreach ($array as $key => $item) {
            if (!property_exists($this, $key)) unset($array[$key]);
        }
        return $array;
    }

    /**
     *
     * Agregar datos a la tabla
     *
     * @param    array $array ["nombre"=>"Facundo","apellido"=>"Rocha"]. La key del array es la columna de la tabla y el value su valor
     * @return   array retorna un array con todos los array internos de productos
     *
     */

    public function add($array)
    {
        try {
            $array = $this->checkArrayWithAttr($array);
            $attr = implode(",", array_keys($array));
            $values = ":" . str_replace(",", ",:", $attr);
            $sql = "INSERT INTO pagos_pedidos ($attr) VALUES ($values)";
            $db = parent::conPDO();
            $stmt = $db->prepare($sql);
            $stmt->execute($array);
            $row = $db->lastInsertId();
            return $row;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     *
     * Editar valores bajo array y condición
     *
     * @param    array  $array array con todos los campos que quieran usar la key del array es el campo de la tabla y el value su valor
     * @param    array  $sql_condition ['id = 1','email = "rocha@gmail.com"'] puede usar cualquier condición que respete la tabla
     * @return   array retorna un array con todos los array internos de productos
     *
     */

    public function edit($array, $sql_condition)
    {
        try {
            $query = implode(", ", array_map(function ($v) {
                return "$v=:$v";
            }, array_keys($array)));
            $condition = implode(' AND ', $sql_condition);
            $sql = "UPDATE pagos_pedidos SET $query WHERE $condition";
            $stmt = parent::conPDO()->prepare($sql);
            return $stmt->execute($array);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     *
     * Editar valores bajo array y condición
     *
     * @param    int  $id Número del ID que debe eliminar
     * @return   array retorna un array con todos los array internos de productos
     *
     */

    public function delete($id, $user)
    {
        try {
            $sql  = "DELETE FROM pagos_pedidos WHERE id=$id AND usuario='$user'";
            $stmt = parent::conPDO()->prepare($sql);
            return $stmt->execute();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function list($filter = [], $order = '', $limit = '', $single = false)
    {
        try {
            $array = array();
            $array_ = array();
            foreach ($filter as $key => $value) {
                $filters[] = $key . "=:" . $key;
            }
            $filterSql = implode(" AND ", $filters);
            $orderSql = ($order != '') ?  $order  : "`id` DESC";
            $limitSql = ($limit != '') ? "LIMIT " . $limit : '';
            $sql = "SELECT * FROM `pagos_pedidos` WHERE $filterSql ORDER BY $orderSql $limitSql";
            $stmt = parent::conPDO()->prepare($sql);
            $stmt->execute($filter);
            while ($row = $stmt->fetch()) {
                $array_ = array("data" => $row);
                $array[] = $array_;
            }
            return ($single) ? $array_ : $array;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
