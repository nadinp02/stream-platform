<?php

namespace Clases;

class EstadosPedidos
{

    //Atributos
    public $id;
    public $estado;
    public $titulo;
    public $asunto;
    public $mensaje;
    public $enviar;
    public $idioma;

    private $con;


    //Metodos
    public function __construct()
    {
        $this->con = new Conexion();
    }

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
        $sql = "INSERT INTO estados_pedidos ($attr) VALUES ($values)";
        $stmt = $this->con->conPDO()->prepare($sql);
        $stmt->execute($array);
    }

    public function edit($array, $id)
    {
        $query = implode(", ", array_map(function ($v) {
            return "$v=:$v";
        }, array_keys($array)));
        $condition = implode(' AND ', $id);
        $sql = "UPDATE estados_pedidos SET $query WHERE $condition";
        $stmt = $this->con->conPDO()->prepare($sql);
        $stmt->execute($array);
    }



    public function getStateBadge($id)
    {
        $state = $this->view($id);
        switch ($state['data']['estado']) {
            case 1:
                echo '<span class="badge badge-warning fs-13 text-uppercase  pull-right" style="margin-right:40px">Estado: ' . $state['data']['titulo'] . '</span>';
                break;
            case 2:
                echo '<span class="badge badge-success fs-13 text-uppercase  pull-right" style="margin-right:40px">Estado: ' . $state['data']['titulo'] . '</span>';
                break;
            case 3:
                echo '<span class="badge badge-danger fs-13 text-uppercase  pull-right" style="margin-right:40px">Estado: ' . $state['data']['titulo'] . '</span>';
                break;
        }
    }

    public function delete($array)
    {
        $sql   = "DELETE FROM `estados_pedidos` WHERE id=:id AND idioma=:idioma";
        $stmt = $this->con->conPDO()->prepare($sql);
        $stmt->execute($array);
        return !empty($query) ? true : false;
    }

    public function view($id)
    {
        $idioma = '';
        if ($this->idioma) {
            $idioma = "AND `idioma` = {$this->idioma}";
        }
        $sql = "SELECT * FROM `estados_pedidos` WHERE id = $id $idioma ORDER BY id DESC";
        $estados = $this->con->sqlReturn($sql);
        $row = !empty($estados) ? mysqli_fetch_assoc($estados) : '';
        $row_ = array("data" => $row);
        return $row_;
    }

    public function list($filter = [], $order, $limit, $single = false)
    {
        $array = array();
        $filterSql = (count($filter)) ? "WHERE " . implode(" AND ", $filter) : '';
        $orderSql =  ($order != '') ? $order : "estado ASC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : "";

        $sql = "SELECT * FROM `estados_pedidos` $filterSql  ORDER BY $orderSql $limitSql";
        $estados = $this->con->sqlReturn($sql);
        if ($estados) {
            while ($row = mysqli_fetch_assoc($estados)) {
                $array_ = array("data" => $row);
                $array[$row['id']] = $array_;
            }
            if ($single) {
                return $array_;
            } else {
                return $array;
            }
        }
    }
    public function listByEstado()
    {
        $array = [];
        $sql = "SELECT `estado` , group_concat(`id` separator ',') as `id`  , group_concat(`titulo` separator ',') as `titulo`, group_concat(`enviar` separator ',') as `enviar` FROM `estados_pedidos` GROUP BY `estados_pedidos`.`estado`";
        $estados = $this->con->sqlReturn($sql);
        if ($estados) {
            while ($row = mysqli_fetch_assoc($estados)) {
                $idEstado = explode(",", $row['id']);
                $tituloEstado = explode(",", $row['titulo']);
                $enviarEstado = explode(",", $row['enviar']);
                foreach ($idEstado as $key => $value) {
                    $data[$key] = ['id' => $value, 'titulo' => $tituloEstado[$key], 'enviar' => $enviarEstado[$key]];
                }
                $array[$row['estado']] = array("data" => $data);
                unset($data);
            }
            return $array;
        }
    }
}
