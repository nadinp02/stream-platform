<?php

namespace Clases;

class Roles extends Conexion
{

    //Atributos
    public $id;
    public $nombre;
    public $permisos;
    public $cod;
    public $editar;
    public $crear;
    public $eliminar;

    public function set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function get($atributo)
    {
        return $this->$atributo;
    }

    public function add()
    {
        $sql = "INSERT INTO `roles` ( `nombre`,  `cod`,`permisos`,`crear`,`eliminar`,`editar`) VALUES ('{$this->nombre}','{$this->cod}', '{$this->permisos}', '{$this->crear}', '{$this->eliminar}', '{$this->editar}')";
        $query = parent::sql($sql);
        return $query;
    }

    public function delete()
    {
        $sql = "DELETE FROM `roles` WHERE `cod`  = '$this->cod'";
        $query = parent::sql($sql);
        return $query;
    }
    public function edit()
    {
        $sql = "UPDATE `roles` 
                  SET nombre =  '{$this->nombre}' ,
                  cod =  '{$this->cod}' ,
                  permisos =  '{$this->permisos}'  ,
                  crear =  '{$this->crear}'  ,
                  editar =  '{$this->editar}'  ,
                  eliminar =  '{$this->eliminar}'  
                  WHERE `id`= {$this->id} ";
        parent::sql($sql);
        return true;
    }

    public static function list($filter, $order, $limit, $groupBy = '')
    {

        $array = array();
        $filterSql = (is_array($filter)) ? "WHERE " . implode(" AND ", $filter) : '';
        $orderSql = ($order != '') ? $order : "ORDER BY id ASC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : "";

        $sql = "SELECT *  FROM roles $filterSql $groupBy $orderSql $limitSql ";
        $notas = parent::sqlReturn($sql);
        if ($notas) {
            while ($row = mysqli_fetch_assoc($notas)) {
                $array[]["data"] = [
                    "id" => $row["id"],
                    "nombre" => $row["nombre"],
                    "cod" => $row["cod"],
                    "permisos" => [
                        "id" => $row["permisos"],
                        "crear" =>  $row["crear"],
                        "editar" =>  $row["editar"],
                        "eliminar" =>  $row["eliminar"]
                    ]
                ];
            }
            return $array;
        }
    }


    public static function listForMenu($filter, $order, $limit)
    {
        $array = array();
        $filterSql = (is_array($filter)) ? "WHERE " . implode(" AND ", $filter) : '';
        $orderSql = ($order != '') ? $order : "ORDER BY orden ASC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : "";

        $sql = "SELECT menu.* , `roles`.`crear`,`roles`.`editar`,`roles`.`eliminar`,`roles`.`permisos` FROM roles LEFT JOIN menu ON menu.id = roles.permisos $filterSql $orderSql $limitSql";
        $roles = parent::sqlReturn($sql);
        if ($roles) {
            while ($row = mysqli_fetch_assoc($roles)) {
                $array["data"][] = $row;
            }
            return $array;
        }
    }
    public function addDevPermissions($area, $titulo, $link, $idioma, $codInit = '')
    {
        $menuAdd = Menu::list(["area = $area", "titulo = $titulo", "link = $link"], str_replace("'", "", $idioma), true);
        if (isset($menuAdd[0]["id"])) {
            $cod = empty($codInit) ? $this->list(["nombre= 'desarrollador'"], "", "1")[0]["data"]["cod"] : $codInit;
            $this->set("nombre", "desarrollador");
            $this->set("cod", "$cod");
            $this->set("permisos", $menuAdd[0]["id"]);
            $this->set("crear", "1");
            $this->set("eliminar", "1");
            $this->set("editar", "1");
            $this->add();
        }
    }

    public function editArray($array, $params)
    {
        $query = implode(", ", array_map(function ($v) {
            return "$v=:$v";
        }, array_keys($array)));
        $condition = implode(' AND ', $params);
        $sql = "UPDATE menu SET $query WHERE $condition";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($array);
    }

}
