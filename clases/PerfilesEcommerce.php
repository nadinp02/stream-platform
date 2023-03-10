<?php

namespace Clases;

class PerfilesEcommerce extends Conexion
{
    public $id;
    public $titulo;
    public $activo;
    public $minorista;
    // CARRO
    public $recargo_factura;
    // PRODUCTOS
    public $mostrar_precios;
    public $usar_stock;
    public $mostrar_sin_stock;
    public $remarcado_productos;
    // CHECKOUT
    public $saltar_checkout;
    public $estado_pedido;
    public $metodo_envio;
    public $metodo_pago;
    public $pedido_whatsapp;



    //Metodos
    public function __construct()
    {
        if ($_ENV["DEVELOPMENT"]) {
            parent::sql("
                CREATE TABLE IF NOT EXISTS `_cfg_perfiles_ecommerce` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `titulo` VARCHAR(255) NOT NULL,
                `activo` BOOLEAN DEFAULT false,
                `minorista` BOOLEAN DEFAULT true,           
                `recargo_factura` FLOAT NULL,
                `remarcado_productos` FLOAT NULL,
                `mostrar_precios` BOOLEAN DEFAULT true,
                `usar_stock` BOOLEAN DEFAULT true,
                `mostrar_sin_stock` BOOLEAN DEFAULT false,
                `saltar_checkout` VARCHAR(255) NOT NULL,
                `estado_pedido` INT(11) NOT NULL,
                `metodo_envio` VARCHAR(255) NOT NULL,
                `metodo_pago` VARCHAR(255) NOT NULL,
                `pedido_whatsapp` BOOLEAN DEFAULT false,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            // parent::sql("ALTER TABLE `_cfg_perfiles_ecommerce` ADD CONSTRAINT `fk_perfil_estado-pedido` FOREIGN KEY (`estado_pedido`) REFERENCES `estados_pedidos`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION");
            // parent::sql("ALTER TABLE `_cfg_perfiles_ecommerce` ADD  CONSTRAINT `fk_perfil_metodo-envio` FOREIGN KEY (`metodo_envio`) REFERENCES `envios`(`cod`) ON DELETE NO ACTION ON UPDATE NO ACTION");
            // parent::sql("ALTER TABLE `_cfg_perfiles_ecommerce` ADD  CONSTRAINT `fk_perfil_metodo-pago` FOREIGN KEY (`metodo_pago`) REFERENCES `pagos`(`cod`) ON DELETE NO ACTION ON UPDATE NO ACTION");
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
     * @param    array $array ["titulo"=>"Saltar Pago","saltar"=>"pago","mensaje"=>"tu presupuesto es..."]. La key del array es la columna de la tabla y el value su valor
     * @return   array retorna un array con todos los array internos de productos
     *
     */

    public function add($array)
    {
        try {
            $array = $this->checkArrayWithAttr($array);
            $attr = implode(",", array_keys($array));
            $values = ":" . str_replace(",", ",:", $attr);
            $sql = "INSERT INTO `_cfg_perfiles_ecommerce` ($attr) VALUES ($values)";
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
            $sql = "UPDATE `_cfg_perfiles_ecommerce` SET $query WHERE $condition";
            $stmt = parent::conPDO()->prepare($sql);
            return $stmt->execute($array);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function changeStatus($id, $minorista)
    {
        try {
            $sql = "UPDATE `_cfg_perfiles_ecommerce` SET activo = '0' WHERE minorista = '$minorista'";
            $sql2 = "UPDATE `_cfg_perfiles_ecommerce` SET activo = '1' WHERE id = '$id'";
            $stmt = parent::conPDO()->prepare($sql);
            $stmt->execute();
            $stmt2 = parent::conPDO()->prepare($sql2);
            return  $stmt2->execute();
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
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

    public function delete($id)
    {
        try {
            $sql  = "DELETE FROM `_cfg_perfiles_ecommerce` WHERE id=$id";
            $stmt = parent::conPDO()->prepare($sql);
            return $stmt->execute();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function list($filter = [], $order = '', $limit = '', $single = false)
    {
        try {
            $array = array();
            $array_ = array();
            foreach ($filter as $key => $value) {
                $filters[] = $key . "=:" . $key;
            }
            $filterSql =  is_array($filters) ? "WHERE " . implode(" AND ", $filters) : '';
            $orderSql = ($order != '') ?  $order  : "`id` DESC";
            $limitSql = ($limit != '') ? "LIMIT " . $limit : '';
            $sql = "SELECT * FROM `_cfg_perfiles_ecommerce` $filterSql ORDER BY $orderSql $limitSql";
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
