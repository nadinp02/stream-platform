<?php

namespace Clases;

use Exception;

class Subcategorias extends Conexion
{

    //Atributos
    public $id;
    public $cod;
    public $titulo;
    public $categoria;
    public $orden;
    public $free_shipping;
    public $idioma;

    private $con;
    private $imagenes;
    private $tercercategoria;
    //Metodos
    public function __construct()
    {
        $this->con = new Conexion();
        $this->tercercategoria = new TercerCategorias();
        $this->imagenes = new Imagenes();
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

    public function add($array)
    {
        $attr = implode(",", array_keys($array));
        $values = ":" . str_replace(",", ",:", $attr);
        $sql = "INSERT INTO subcategorias ($attr) VALUES ($values)";
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
        $sql = "UPDATE subcategorias SET $query WHERE $condition";
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
        $sql = "DELETE FROM `subcategorias` WHERE cod=:cod AND idioma=:idioma";
        try {
            $stmt = parent::conPDO()->prepare($sql);
            $stmt->execute($array);
            if (!empty($this->imagenes->list($array, "", "", true))) {
                $this->imagenes->deleteAll($array);
            }
            $response = true;
        } catch (Exception $e) {
            $response["error"] = $e->getMessage();

        }
        return $response;
    }


    public static function  list($filter = [], $order = '', $limit = '', $idioma, $single = false, $images = true, $tercercategoria = true)
    {
        $array = array();
        $filter[] = "idioma = '" . $idioma . "'";
        $filterSql = (is_array($filter)) ? 'WHERE ' . implode(' AND ', $filter) : '';
        $orderSql = ($order != '') ?  $order  : "`id` DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : '';

        $sql = "SELECT * FROM `subcategorias` $filterSql  ORDER BY $orderSql $limitSql";
        $subcategorias = parent::sqlReturn($sql);
        if ($subcategorias) {
            while ($row = mysqli_fetch_assoc($subcategorias)) {
                $img = ($images) ? Imagenes::list(["cod" => $row['cod'], "idioma" => $row['idioma']], '', '', true) : [];
                $tercer = ($tercercategoria) ? TercerCategorias::list(["subcategoria = '" . $row['cod'] . "'"], '`tercercategorias`.`orden` ASC , `tercercategorias`.`titulo` ASC', '', $idioma) : [];
                $array_ = array("data" => $row, "tercercategories" => $tercer, "image" => $img);
                $array[] = $array_;
            }
            return ($single) ? $array_ : $array;
        } else {
            return false;
        }
    }

    /**
     *
     * Traer un array con todas las subcategorias que esten en uso en la base de datos buscada.
     *
     * @param    string $db nombre de la base de datos de la cual se desea traer las categorias.
     * @param    string  $area en caso de que la base de datos sea Contenido podes identificar un area en especifico.
     * @return   array retorna un array con toda la informacion de las categorias y la cantidad de veces que se usa.
     *
     */
    public static function listIfHave($db, $categoria, $idioma = '')
    {
        $idioma = ($idioma) ? $idioma : $_SESSION['lang'];
        $productos = ($db == 'productos') ? "  AND  productos.mostrar_web = 1 AND productos.idioma = '$idioma'" : "";
        $categoria = ($categoria != '') ? " AND  subcategorias.categoria = '$categoria' AND subcategorias.idioma = '$idioma' " : "";
        $array = array();
        $sql = " SELECT `subcategorias`.* FROM `" . $db . "`,`subcategorias` WHERE `subcategoria` = `subcategorias`.`cod` $productos $categoria GROUP BY `subcategorias`.`cod` ORDER BY subcategorias.orden ASC, subcategorias.titulo ASC ";
        $listIfHave = parent::sqlReturn($sql);
        if ($listIfHave) {
            while ($row = mysqli_fetch_assoc($listIfHave)) {
                $ter = TercerCategorias::listIfHave($db, $row["cod"], $idioma);
                $array[] = array("data" => $row, "tercercategories" => $ter);
            }
            return $array;
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

        $sql = "SELECT * FROM `subcategorias` $filterSql  ORDER BY $orderSql $limitSql";
        $subcategorias = parent::sqlReturn($sql);
        if ($subcategorias) {
            while ($row = mysqli_fetch_assoc($subcategorias)) {
                $ter = TercerCategorias::listExcel(["subcategoria='" . $row['cod'] . "'"], '', '', $idioma);
                $array_ = array("titulo" => $row['titulo'], "tercercategorias" => $ter);
                $array[$row['cod']] = $array_;
            }
            return $array;
        } else {
            return false;
        }
    }


    public function listForDiscount($cod)
    {
        $array = array();
        $sql = " SELECT `subcategorias`.`titulo`,`subcategorias`.`cod`  FROM `subcategorias` WHERE `categoria` = '$cod'";
        $listDiscount = parent::sqlReturn($sql);
        if ($listDiscount->num_rows) {
            while ($row = mysqli_fetch_assoc($listDiscount)) {
                $array[] = array("data" => $row);
            }
            return $array;
        }
    }
    public function editSingle($atributo, $valor)
    {
        $sql = "UPDATE `subcategorias` SET `$atributo` = {$valor} WHERE `cod`={$this->cod} AND `idioma` = {$this->idioma}";
        return (parent::sqlReturn($sql)) ? true : false;
    }
}
