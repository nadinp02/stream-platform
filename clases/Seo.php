<?php

namespace Clases;

use Exception;

class Seo extends Conexion
{

    //Atributos
    public $id;
    public $cod;
    public $url;
    public $title;
    public $description;
    public $keywords;
    public $idioma;

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
        $sql = "INSERT INTO `seo`(`cod`, `url`, `title`,`description`,`keywords`,`idioma`) 
                VALUES ({$this->cod},
                        {$this->url},
                        {$this->title},
                        {$this->description},
                        {$this->keywords},
                        {$this->idioma})";
        $query = parent::sql($sql);
        return $query;
    }

    public function edit()
    {
        $sql = "UPDATE `seo` 
                SET cod = {$this->cod},
                    url = {$this->url},
                    title = {$this->title},
                    description = {$this->description},
                    keywords = {$this->keywords}
                WHERE `cod`={$this->cod} AND `idioma`={$this->idioma}";
        $query = parent::sql($sql);
        return $query;
    }

    public function delete($array)
    {
        $sql = "DELETE FROM `seo` WHERE cod=:cod AND idioma=:idioma";
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

    public function view()
    {
        $sql = "SELECT * FROM `seo` WHERE cod = {$this->cod} ORDER BY id DESC LIMIT 1";
        $seo = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($seo);
        $img = Imagenes::list(["cod" => $row["cod"], "idioma" => $row['idioma']], "", "", true);
        $array = array("data" => $row, "images" => $img);
        return $array;
    }

    public function viewURL($idioma)
    {
        $sql = "SELECT * FROM `seo` WHERE `url` = {$this->url} AND `idioma` = '$idioma' ORDER BY id DESC LIMIT 1";
        $seo = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($seo);
        if ($row) {
            $img = Imagenes::list(["cod" => $row["cod"], "idioma" => $row['idioma']], "", "", true);
            $array = array("data" => $row, "images" => $img);
        } else {
            $array = false;
        }

        return $array;
    }

    public function list($filter, $order, $limit, $idioma)
    {
        if (empty($idioma)) $idioma = $_SESSION["lang"];
        $array = array();
        is_array($filter) ? $filter[] = "`idioma` = '" . $idioma . "' " : $filter = "`idioma` = '" . $idioma . "'";
        $filterSql = (is_array($filter)) ? "WHERE " . implode(" AND ", $filter) : '';
        $orderSql = ($order != '') ? $order : "id DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : "";
        $sql = "SELECT * FROM `seo` $filterSql ORDER BY $orderSql $limitSql";
        $seo = parent::sqlReturn($sql);
        if ($seo) {
            while ($row = mysqli_fetch_assoc($seo)) {
                $img = Imagenes::list(["cod" => $row["cod"], "idioma" => $row['idioma']], "", "", true);
                $array[] = array("data" => $row, "images" => $img);
            }
            return $array;
        }
    }
}
