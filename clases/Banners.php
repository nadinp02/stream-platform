<?php

namespace Clases;

class Banners extends Conexion
{

    //Atributos
    public $id;
    public $cod;
    public $titulo;
    public $titulo_on;
    public $subtitulo;
    public $subtitulo_on;
    public $categoria;
    public $link;
    public $link_on;
    public $idioma;
    public $fecha;
    public $orden;

    public function add($array)
    {
        $attr = implode(",", array_keys($array));
        $values = ":" . str_replace(",", ",:", $attr);
        $sql = "INSERT INTO banners ($attr) VALUES ($values)";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($array);
    }
    public function edit($array, $cod)
    {
        $query = implode(", ", array_map(function ($v) {
            return "$v=:$v";
        }, array_keys($array)));
        $condition = implode(' AND ', $cod);
        $sql = "UPDATE banners SET $query WHERE $condition";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($array);
    }


    public function delete($array)
    {
        $sql   = "DELETE FROM `banners` WHERE cod=:cod AND idioma=:idioma";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($array);
        if (!empty(Imagenes::list($array, "", "", true))) {
            Imagenes::deleteAll($array);
        }
        return !empty($query) ? true : false;
    }

    public function list($filter, $order = '', $limit = '', $single = false)
    {
        $array = array();
        foreach ($filter as $key => $value) {
            $filters[] = $key . "=:" . $key;
        }
        $filterSql = implode(" AND ", $filters);
        $orderSql = ($order != '') ?  $order  : "`id` DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : '';
        $sql = "SELECT * FROM `banners` WHERE $filterSql ORDER BY $orderSql $limitSql";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($filter);
        $array_ = [];
        while ($row = $stmt->fetch()) {
            $img = Imagenes::list(["cod" => $row['cod'], "idioma" => $row["idioma"]], "", "", true);
            $cat = Categorias::list(["cod = '" . $row['categoria'] . "'"], '', '', $row["idioma"], true);
            $cat_ = (isset($cat['data'])) ? $cat['data'] : '';
            $array_ = array("data" => $row, "category" => $cat_, "image" => $img);
            $array[] = $array_;
        }

        return ($single) ? $array_ : $array;
    }
}
