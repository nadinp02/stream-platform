<?php

namespace Clases;



use Exception;

class OpcionesValor extends Conexion
{
    //Atributos
    public $id;
    public $cod;
    public $relacion_cod;
    public $opcion_cod;
    public $valor;
    public $idioma;

    public function set($atributo, $value)
    {
        if (!empty($value)) {
            $value = "'" . $value . "'";
        } else {
            $value = "NULL";
        }
        $this->$atributo = $value;
    }

    public function get($atributo)
    {
        return $this->$atributo;
    }

    public function add()
    {
        $return  = false;
        $sql = "INSERT INTO `opciones_valor` (`cod`,`relacion_cod`,`opcion_cod`,`valor`,`idioma`) VALUES ({$this->cod},{$this->relacion_cod}, {$this->opcion_cod},  {$this->valor},{$this->idioma})";
        $query = parent::sqlReturn($sql);
        if (!empty($query)) $return = true;
        return $return;
    }
    public function edit()
    {
        $return  = false;
        if ($this->valor != "NULL") {
            $sql = "UPDATE `opciones_valor` SET `valor` = {$this->valor} WHERE `cod` = {$this->cod} AND `idioma` = {$this->idioma} AND `opcion_cod` = {$this->opcion_cod};";
            $query = parent::sqlReturn($sql);
            if (!empty($query)) $return = true;
        } else {
            $this->delete();
            $return = true;
        }
        return $return;
    }
    public function checkIfExist()
    {
        $array = [];
        $sql = "SELECT `opciones_valor`.`cod` FROM `opciones_valor` WHERE `idioma` ={$this->idioma} AND `relacion_cod` = {$this->relacion_cod} AND `opcion_cod` = {$this->opcion_cod};";
        $query = parent::sqlReturn($sql);
        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $array = array("data" => $row);
            }
            return $array;
        }
    }
    public function delete()
    {
        $sql = "DELETE FROM `opciones_valor` WHERE `cod` = {$this->cod} AND `idioma`= {$this->idioma} ";
        $query = parent::sqlReturn($sql);
        if (!empty($query)) $return = true;
        return $return;
    }

    public static function list($idioma, $filter = [], $labels = false)
    {
        $array = array();
        $filter[] = "`opciones_valor`.`idioma` = '$idioma'";
        $filterSql = (count($filter)) ? "WHERE " . implode(" AND ", $filter) : '';
        $selectAttr = "`opciones_valor`.*";
        $leftJoin = "";
        if ($labels) {
            $selectAttr = "`opciones_valor`.*,`opciones`.`titulo`";
            $leftJoin = "LEFT JOIN `opciones` ON `opciones`.`cod` = `opciones_valor`.`opcion_cod`";
        }
        $sql = "SELECT $selectAttr FROM `opciones_valor` $leftJoin $filterSql";
        $data = parent::sqlReturn($sql);
        if ($data) {
            while ($row = mysqli_fetch_assoc($data)) {
                $array[$row['opcion_cod']] = array("data" => $row);
            }
            return $array;
        }
    }


    public static function listIfHave($cod, $idioma = '', $area = '')
    {
        $idioma = ($idioma) ? $idioma : $_SESSION['lang'];
        $db = '';
        $filter = '';
        if ($area == 'productos') {
            $db = $area . " , ";
            $filter = "AND productos.cod = opciones_valor.relacion_cod AND productos.mostrar_web = 1  AND productos.idioma = '$idioma'";
        }
        $array = array();
        $sql = " SELECT valor, count(opciones_valor.opcion_cod) as cantidad FROM " . $db . " opciones_valor WHERE opciones_valor.valor IS NOT NULL AND opciones_valor.idioma = '" . $idioma . "' AND  opciones_valor.opcion_cod = '" . $cod . "' " . $filter . " GROUP BY opciones_valor.valor ORDER BY opciones_valor.valor ASC ";
        $listIfHave = parent::sqlReturn($sql);
        if ($listIfHave) {
            while ($row = mysqli_fetch_assoc($listIfHave)) {
                $array[] = array("data" => $row);
            }
            return $array;
        }
    }
}
