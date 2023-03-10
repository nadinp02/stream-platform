<?php

namespace Clases;

use Exception;

class Opciones extends Conexion
{
    //Atributos
    public $id;
    public $cod;
    public $titulo;
    public $tipo;
    public $area;
    public $categoria;
    public $idioma;
    public $opciones;
    public $filtro;
    public $multiple;

    //Metodos 
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
        $return  = false;
        $sql = "INSERT INTO `opciones` (`cod`, `titulo`, `tipo`,`area`,`categoria`,`opciones`,`filtro`,`multiple`,`idioma`) VALUES ({$this->cod}, {$this->titulo}, {$this->tipo}, {$this->area},{$this->categoria},{$this->opciones},{$this->filtro},{$this->multiple},{$this->idioma})";
        $query = parent::sqlReturn($sql);
        if (!empty($query)) $return = true;
        return $return;
    }
    
    public function edit()
    {
        $return  = false;
        $sql = "UPDATE `opciones` SET `titulo` = {$this->titulo}, `tipo` = {$this->tipo},`area` = {$this->area},`categoria` = {$this->categoria},`opciones` = {$this->opciones},`filtro` = {$this->filtro}, `multiple` = {$this->multiple} WHERE `cod` = {$this->cod} AND `idioma` = {$this->idioma}";
        $query = parent::sqlReturn($sql);
        if (!empty($query)) $return = true;
        return $return;
    }


    public function delete()
    {
        $sql = "DELETE FROM `opciones` WHERE `cod` = {$this->cod} AND `idioma`= {$this->idioma} ";
        $query = parent::sqlReturn($sql);
        if (!empty($query)) $return = true;
        return $return;
    }
    public function list($idioma, $filter = [], $values = false, $cod_validation = "", $single = false, $order = "`opciones`.`categoria` ASC")
    {
        $array = array();
        $filter[] = "`opciones`.`idioma` = '$idioma'";
        $filterSql = (count($filter)) ? "WHERE " . implode(" AND ", $filter) : '';
        $leftJoin = "LEFT JOIN categorias ON opciones.categoria = categorias.cod ";
        $selectAttr = "`opciones`.`cod`,`opciones`.`titulo`,`opciones`.`tipo`,`opciones`.`area`,`opciones`.`categoria`,`opciones`.`idioma`,`opciones`.`opciones`,`opciones`.`multiple`,`opciones`.`filtro`, categorias.titulo as categoria_titulo";
        if ($values) {
            $leftJoin .= "LEFT JOIN `opciones_valor` ON `opciones_valor`.`opcion_cod` = `opciones`.`cod` AND `opciones_valor`.`idioma` = `opciones`.`idioma` AND `opciones_valor`.`relacion_cod` = '$cod_validation'";
            $selectAttr = "`opciones`.`cod`,`opciones`.`opciones`,`opciones`.`multiple`,`opciones`.`titulo`,`opciones`.`tipo`,`opciones`.`area`,`opciones`.`idioma`, `opciones_valor`.`valor`, `opciones`.`categoria`,categorias.titulo as categoria_titulo";
        }

        $sql = "SELECT $selectAttr FROM `opciones` $leftJoin $filterSql ORDER BY $order";

        $data = parent::sqlReturn($sql);
        if ($data) {
            while ($row = mysqli_fetch_assoc($data)) {
                if ($row["tipo"] == "int") $row["tipo_mostrar"] = "Numérico";
                if ($row["tipo"] == "text") $row["tipo_mostrar"] = "Texto Simple";
                if ($row["tipo"] == "textarea") $row["tipo_mostrar"] = "Texto Compuesto";
                if ($row["tipo"] == "boolean") $row["tipo_mostrar"] = "Si/No";
                if ($row["tipo"] == "select") $row["tipo_mostrar"] = "Selector";

                ($single) ? $array = ["data" => $row] : $array[$row['cod']] = array("data" => $row);
            }
            return $array;
        }
    }

    public function listIfHave($filters, $idioma = '', $single = false, $filtro = true)
    {
        $idioma = ($idioma) ? $idioma : $_SESSION['lang'];
        $filterSQL = ($filtro) ? " AND opciones.filtro = '1'" : '';
        $area = '';
        $array_ = [];
        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                if ($filter) $filterSQL .= " AND opciones." . $key . " = '" . $filter . "'";
                if ($key == 'area') $area = $filter;
            }
        }
        $array = array();
        $sql = " SELECT opciones.titulo,opciones.cod as cod ,opciones.categoria, opciones.tipo,count(opciones_valor.opcion_cod) AS cantidad,categorias.titulo AS categoria_titulo FROM opciones_valor , opciones LEFT JOIN categorias ON opciones.categoria = categorias.cod  WHERE opciones.idioma = '" . $idioma . "' " . $filterSQL . " AND  opciones_valor.opcion_cod = opciones.cod GROUP BY opciones.cod ORDER BY opciones.titulo ASC ";
        $listIfHave = parent::sqlReturn($sql);
        if ($listIfHave) {
            while ($row = mysqli_fetch_assoc($listIfHave)) {
                $valores = OpcionesValor::listIfHave($row["cod"], $idioma, $area);
                $array[$row["cod"]] = ["data" => $row, "valores" => $valores];
            }
            return ($single) ? $array_ : $array;
        }
    }

    public function getAttrWithTitle()
    {
        $listOpciones = $this->list($_SESSION["lang"], ["`opciones`.`area` = 'productos'"]);
        $data = [];
        foreach ($listOpciones as $opcionProducto) {
            $data[$opcionProducto['data']['cod']] = $opcionProducto['data']['titulo'];
        }
        return $data;
    }

    public function createForm($categoria, $array)
    {
    }
}
