<?php

namespace Clases;

use Exception;

class Landing
{

    //Atributos
    public $id;
    public $cod;
    public $titulo;
    public $desarrollo;
    public $categoria;
    public $keywords;
    public $description;
    public $fecha;
    public $idioma;

    private $con;
    private $imagenes;
    private $categorias;

    //Metodos
    public function __construct()
    {
        $this->con = new Conexion();
        $this->imagenes = new Imagenes();
        $this->categorias = new Categorias();
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
        $sql = "INSERT INTO landing ($attr) VALUES ($values)";
        $stmt = $this->con->conPDO()->prepare($sql);
        $stmt->execute($array);
    }


    public function edit($array, $cod)
    {
        $query = implode(", ", array_map(function ($v) {
            return "$v=:$v";
        }, array_keys($array)));
        $condition = implode(' AND ', $cod);
        $sql = "UPDATE landing SET $query WHERE $condition";
        $stmt = $this->con->conPDO()->prepare($sql);
        $stmt->execute($array);
    }


    public function delete($array)
    {
        $sql   = "DELETE FROM `landing` WHERE cod=:cod AND idioma=:idioma";
        $stmt = $this->con->conPDO()->prepare($sql);
        $stmt->execute($array);
        if (!empty($this->imagenes->list($array, "", "", true))) {
            $this->imagenes->deleteAll($array);
        }
        return !empty($query) ? true : false;
    }

    public function list($filter = [], $order, $limit, $single = false)
    {
        $array = array();
        $filters = array();
        foreach ($filter as $key => $value) {
            $filters[] = $key . "=:" . $key;
        }

        $filterSql = (is_array($filters)&& !empty($filters)) ? "WHERE " . implode(" AND ", $filters) : '';
        $orderSql = ($order != '') ?  $order  : "`id` DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : '';
        $sql = "SELECT * FROM `landing` $filterSql ORDER BY $orderSql $limitSql";
        $stmt = $this->con->conPDO()->prepare($sql);
        $stmt->execute($filter);
        while ($row = $stmt->fetch()) {
            $imagesData =  $this->imagenes->list(["cod" => $row["cod"], "idioma" => $row['idioma']], "", "");
            $img = $this->createArrayImages($imagesData);
            $cat = $this->categorias->list(["cod = '" . $row['categoria'] . "'"], '', '', $_SESSION['lang']);
            $array_ = array("data" => $row, "category" => $cat, "images" => $img);
            $array[$row['cod']] = $array_;
        }
        return ($single) ? $array_ : $array;
    }

    public function createArrayImages($images)
    {
        $images_array = [];
        foreach ($images as $img) {
            $images_array[] = ["id" => $img["id"], "orden" => $img["orden"], "url" => URL . "/" . $img["ruta"],"idioma" => $img["idioma"]];
        }
        $images = (count($images_array)) ? $images_array : [["id" => 000000, "url" => URL . "/assets/archivos/sin_imagen.jpg"]];
        return $images;
    }
}
