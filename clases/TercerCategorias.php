<?php

namespace Clases;

use Exception;

class TercerCategorias extends Conexion
{

    //Atributos
    public $id;
    public $cod;
    public $titulo;
    public $subcategoria;
    public $descripcion;
    public $orden = 0;
    public $idioma = "es";
    public $free_shipping;

    public function set($atributo, $valor)
    {
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
        $sql = "INSERT INTO tercercategorias ($attr) VALUES ($values)";
        try {
            $stmt = parent::conPDO()->prepare($sql);
            $stmt->execute($array);
            $response = true;
        } catch (Exception $e) {
            $response["error"] = $e->getMessage();
        }
        return $response;
    }
    public function edit($array, $cod)
    {
        $query = implode(", ", array_map(function ($v) {
            return "$v=:$v";
        }, array_keys($array)));
        $condition = implode(' AND ', $cod);
        $sql = "UPDATE tercercategorias SET $query WHERE $condition";
        try {
            $stmt = parent::conPDO()->prepare($sql);
            $stmt->execute($array);
            $response = true;
        } catch (Exception $e) {
            $response["error"] = $e->getMessage();
        }
        return $response;
    }


    public function delete($array)
    {
        $sql = "DELETE FROM `tercercategorias` WHERE cod=:cod AND idioma=:idioma";
        try {
            $stmt = parent::conPDO()->prepare($sql);
            $stmt->execute($array);
            if (!empty(Imagenes::list($array, "", "", true))) {
                Imagenes::deleteAll($array);
            }
            $response = true;
        } catch (Exception $e) {
            $response["error"] = $e->getMessage();
        }
        return $response;
    }


    public static function list($filter = [], $order = '', $limit = '', $idioma, $single = false)
    {
        $array = array();
        $filter[] = "idioma = '" . $idioma . "'";
        $filterSql = (is_array($filter)) ? 'WHERE ' . implode(' AND ', $filter) : '';
        $orderSql = ($order != '') ?  $order  : "`id` DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : '';
        $sql = "SELECT * FROM `tercercategorias` $filterSql  ORDER BY $orderSql $limitSql";
        $subcategorias = parent::sqlReturn($sql);
        if ($subcategorias) {
            while ($row = mysqli_fetch_assoc($subcategorias)) {
                $img = Imagenes::list(["cod" => $row['cod'], "idioma" => $row['idioma']], '', '', true);
                $array_ = array("data" => $row, "image" => $img);
                $array[] = $array_;
            }
            return ($single) ? $array_ : $array;
        } else {
            return false;
        }
    }

    /**
     *
     * Traer un array con todas las tercercategorias que esten en uso en la base de datos buscada.
     *
     * @param    string $db nombre de la base de datos de la cual se desea traer las categorias.
     * @param    string  $area en caso de que la base de datos sea Contenido podes identificar un area en especifico.
     * @return   array retorna un array con toda la informacion de las categorias y la cantidad de veces que se usa.
     *
     */
    public static function listIfHave($db, $subcategoria, $idioma = '')
    {
        if ($db == 'productos') {
            $idioma = ($idioma) ? $idioma : $_SESSION['lang'];
            $productos = ($db == 'productos') ? "  AND  productos.mostrar_web = 1 AND productos.idioma = '$idioma'" : "";
            $subcategoria = ($subcategoria != '') ? " AND  tercercategorias.subcategoria = '$subcategoria' AND tercercategorias.idioma = '$idioma' " : "";
            $array = array();
            $sql = " SELECT `tercercategorias`.*  FROM `" . $db . "`,`tercercategorias` WHERE `productos`.`tercercategoria` = `tercercategorias`.`cod` $productos $subcategoria GROUP BY `tercercategorias`.`cod` ORDER BY tercercategorias.titulo ASC ";
            $listIfHave = parent::sqlReturn($sql);
            if ($listIfHave) {
                while ($row = mysqli_fetch_assoc($listIfHave)) {
                    $array[] = array("data" => $row);
                }
                return $array;
            }
        }
    }

    public static function listExcel($filter = [], $order = '', $limit = '', $idioma)
    {
        $array = array();
        $array_ = array();
        $filter[] = "idioma = '" . $idioma . "'";
        $filterSql = (is_array($filter)) ? 'WHERE ' . implode(' AND ', $filter) : '';
        $orderSql = ($order != '') ?  $order  : "`id` DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : '';

        $sql = "SELECT * FROM `tercercategorias` $filterSql  ORDER BY $orderSql $limitSql";
        $tercercategoria = parent::sqlReturn($sql);
        if ($tercercategoria) {
            while ($row = mysqli_fetch_assoc($tercercategoria)) {
                $array_ = array("titulo" => $row['titulo']);
                $array[$row['cod']] = $array_;
            }
            return $array;
        } else {
            return false;
        }
    }

    public function editSingle($atributo, $valor)
    {
        $sql = "UPDATE `tercercategorias` SET `$atributo` = {$valor} WHERE `cod`='{$this->cod}' AND `idioma` = '{$this->idioma}'";
        return (parent::sqlReturn($sql)) ? true : false;
    }
}
