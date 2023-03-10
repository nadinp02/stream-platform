<?php

namespace Clases;

class Usuarios extends Conexion
{

    //Atributos
    public $id;
    public $cod;
    public $nombre;
    public $apellido;
    public $doc;
    public $email;
    public $password;
    public $calle;
    public $numero;
    public $piso;
    public $postal;
    public $localidad;
    public $provincia;
    public $pais;
    public $telefono;
    public $celular;
    public $minorista = 1;
    public $invitado = 1;
    public $descuento = 0;
    public $fecha;
    public $estado = 1;
    public $admin = 0;
    public $idioma;

    private $usuariosIp;

    //Metodos
    public function __construct()
    {
        $this->usuariosIp = new UsuariosIp();
    }

    public function set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function multiSetter(array $array)
    {
        foreach ($array as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function get($atributo)
    {
        return $this->$atributo;
    }

    public function getAttrWithTitle()
    {
        $data = [];
        $data["cod"] = "Codigo de Usuario";
        $data["doc"] = "DNI";
        $data["nombre"] = "Nombre";
        $data["apellido"] = "Apellido";
        $data["email"] = "Correo Electrónico";
        $data["password"] = "Contraseña";
        $data["telefono"] = "Telefono";
        $data["celular"] = "Celular";
        $data["calle"] = "Calle";
        $data["numero"] = "Numero";
        $data["piso"] = "Piso";
        $data["postal"] = "Código postal";
        $data["localidad"] = "Localidad";
        $data["provincia"] = "Provincia";
        $data["pais"] = "Pais";
        $data["minorista"] = "Tipo de Usuario";
        $data["estado"] = "Estado";
        $data["idioma"] = "Idioma";

        return $data;
    }

    public function transformQuery()
    {
        $atributes = array(
            "cod" => $this->cod,
            "nombre" => $this->nombre,
            "apellido" => $this->apellido,
            "doc" => $this->doc,
            "email" => $this->email,
            "password" => $this->password,
            "calle" => $this->calle,
            "numero" => $this->numero,
            "piso" => $this->piso,
            "postal" => $this->postal,
            "localidad" => $this->localidad,
            "provincia" => $this->provincia,
            "pais" => $this->pais,
            "telefono" => $this->telefono,
            "celular" => $this->celular,
            "minorista" => $this->minorista,
            "invitado" => $this->invitado,
            "descuento" => $this->descuento,
            "fecha" => date("Y-m-d H:i:s"),
            "estado" => $this->estado,
            "idioma" => $this->idioma
        );

        foreach ($atributes as $name => $value) {
            $this->$name = strlen($value) ? "'" . $value . "'" : "NULL";
        }
    }

    public function hash()
    {
        return hash('sha256', $this->password . SALT);
    }

    public function add()
    {
        $validar = $this->validate();
        if (!$validar['status']) {
            if (!empty($this->password)) $this->set("password", hash('sha256', $this->password . SALT));
            $this->transformQuery();
            $sql = "INSERT INTO `usuarios` (`cod`, `nombre`, `apellido`, `doc`, `email`, `password`, `calle`, `numero`,`piso`, `postal`, `localidad`, `provincia`, `pais`, `telefono`, `celular`, `minorista`, `invitado`, `descuento`, `fecha`, `estado`,`admin`, `idioma`) 
                    VALUES ({$this->cod},
                            {$this->nombre},
                            {$this->apellido},
                            {$this->doc},
                            {$this->email},
                            {$this->password},
                            {$this->calle},
                            {$this->numero},
                            {$this->piso},
                            {$this->postal},
                            {$this->localidad},
                            {$this->provincia},
                            {$this->pais},
                            {$this->telefono},
                            {$this->celular},
                            {$this->minorista},
                            {$this->invitado},
                            {$this->descuento},
                            {$this->fecha},
                            {$this->estado},
                            {$this->admin},
                            '" . $_SESSION['lang'] . "')";
            parent::sql($sql);
            $this->usuariosIp->usuario = $this->cod;
            $this->usuariosIp->set('ip', $_SERVER['REMOTE_ADDR']);
            $this->usuariosIp->set('dispositivo', $_SERVER['HTTP_USER_AGENT']);
            $this->usuariosIp->checkIfExists();
            return true;
        } else {
            return false;
        }
    }

    public function edit()
    {
        $usuario = $this->view();
        $validar = $this->validate();
        if (is_array($validar)) {
            if ($validar['data']["email"] == $usuario['data']["email"]) {
                if ($usuario['data']["password"] != $this->password) $this->set("password", hash('sha256', $this->password . SALT));
                $this->transformQuery();
                $sql = "UPDATE `usuarios` 
                        SET `nombre` = {$this->nombre},
                            `apellido` = {$this->apellido},
                            `doc` = {$this->doc},
                            `email` = {$this->email},
                            `password` = {$this->password},
                            `calle` = {$this->calle},
                            `numero` = {$this->numero},
                            `piso` = {$this->piso},
                            `postal` = {$this->postal},
                            `localidad` = {$this->localidad},
                            `provincia` = {$this->provincia},
                            `pais` = {$this->pais},
                            `telefono` = {$this->telefono},
                            `celular` = {$this->celular},
                            `invitado` = {$this->invitado},
                            `minorista` = {$this->minorista},
                            `descuento` = {$this->descuento},
                            `estado` = {$this->estado},
                            `fecha` = {$this->fecha},
                            `idioma` = {$this->idioma}
                        WHERE `cod`={$this->cod}";
                parent::sql($sql);

                $this->usuariosIp->set('usuario',  $validar['data']["cod"]);
                $this->usuariosIp->set('ip', $_SERVER['REMOTE_ADDR']);
                $this->usuariosIp->set('dispositivo', $_SERVER['HTTP_USER_AGENT']);
                $this->usuariosIp->checkIfExists();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function editSingle($atributo, $valor)
    {
        $validar = $this->validate();
        $usuario = $this->view();
        if ($atributo == 'password') $valor = hash('sha256', $valor . SALT);
        $sql = "UPDATE `usuarios` SET `$atributo` = '{$valor}' WHERE `cod`='{$this->cod}'";
        if ($validar['status'] == true) {
            if ($validar['data']["email"] == $usuario['data']["email"]) {
                parent::sql($sql);
                return true;
            } else {
                return false;
            }
        } else {
            parent::sql($sql);
            return true;
        }
    }

    public function delete()
    {
        $sql = "DELETE FROM `usuarios`WHERE `cod`= {$this->cod}";
        $query = parent::sql($sql);
        return $query;
    }

    public function login()
    {
        $response = NULL;
        $this->set("password", hash('sha256', $this->password . SALT));
        $sql = "SELECT * FROM `usuarios` WHERE `email` = '{$this->email}' AND `password`= '{$this->password}' AND invitado = 0";
        $usuarios = parent::sqlReturn($sql);
        if (!empty($usuarios)) {
            $row = mysqli_fetch_assoc($usuarios);
            if (!empty($row)) {
                if ($row["estado"] == 1) {
                    unset($row["password"]);
                    $_SESSION["usuarios"] = $row;
                    $response = array("status" => true);
                    $this->usuariosIp->set('usuario',  $row['cod']);
                    $this->usuariosIp->set('ip', $_SERVER['REMOTE_ADDR']);
                    $this->usuariosIp->set('dispositivo', $_SERVER['HTTP_USER_AGENT']);
                    $this->usuariosIp->checkIfExists();
                } else {
                    $response = array("status" => false, "error" => 1);
                }
            } else {
                $response = array("status" => false, "error" => 2); //contraseña o email incorrecto
            }
        } else {
            $response = array("status" => false, "error" => 3); //error inesperado no debe existir en la base
        }
        return $response;
    }


    public function logout()
    {
        unset($_SESSION["usuarios"]);
    }

    public function view()
    {
        $sql = "SELECT * FROM `usuarios` WHERE cod = '{$this->cod}' ORDER BY id DESC";
        $usuario = parent::sqlReturn($sql);
        if (!empty($usuario)) {
            $row = mysqli_fetch_assoc($usuario);
            $row_ = array("data" => $row);
            return $row_;
        } else {
            return [];
        }
    }

    public function validate()
    {
        if (!empty($this->email)) {
            $sql = "SELECT * FROM `usuarios` WHERE email = '{$this->email}'";
            $usuario = parent::sqlReturn($sql);
            $row = mysqli_fetch_assoc($usuario);
            $response = (!empty($row)) ? ["status" => true, "data" => $row] : ["status" => false];
        } else {
            $response = ["status" => false];
        }
        return $response;
    }

    public function list($filter, $order, $limit)
    {
        $array = [];
        $filterSql =  is_array($filter) ? "WHERE " . implode(" AND ", $filter) : '';
        $orderSql = ($order != '') ? $order : "id DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : "";
        $sql = "SELECT * FROM `usuarios` $filterSql  ORDER BY $orderSql $limitSql";
        $users = parent::sqlReturn($sql);
        if ($users) {
            while ($row = mysqli_fetch_assoc($users)) {
                $array[] = array("data" => $row);
            }
        }

        return $array;
    }

    //Sessions
    public function viewSession()
    {
        $_SESSION["usuarios"] = isset($_SESSION["usuarios"]) ? $_SESSION["usuarios"] : [];
        return $_SESSION["usuarios"];
    }

    public function firstGuestSession()
    {
        $this->guestSession();
        $this->add();
    }

    public function guestSession()
    {
        $_SESSION["usuarios"] = array(
            'cod' => $this->cod,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'calle' => $this->calle,
            'numero' => $this->numero,
            'piso' => $this->piso,
            'localidad' => $this->localidad,
            'provincia' => $this->provincia,
            'telefono' => $this->telefono,
            'invitado' => $this->invitado,
            'idioma' => $this->idioma,
            'minorista' => $this->minorista,
            'descuento' => $this->descuento,
            'fecha' => $this->fecha
        );
    }

    //Metodos admin


    public function refreshSession($cod)
    {
        if (isset($_SESSION["usuarios"]["invitado"]) && $_SESSION["usuarios"]["invitado"] == 0) {
            $this->set("cod", $cod);
            $_SESSION["usuarios"] = $this->view()["data"];
            unset($_SESSION["usuarios"]["password"]);
            return $_SESSION["usuarios"];
        }
    }

    public function userSession($user)
    {
        unset($user["data"]["password"]);
        $_SESSION["usuarios-ecommerce"] = $user["data"];
    }

    public function editEstado($atributo, $valor)
    {
        $validar = $this->validate();
        $usuario = $this->view();
        if ($atributo == 'password') {
            $valor = hash('sha256', $valor . SALT);
        }
        $sql = "UPDATE `usuarios` SET `$atributo` = '{$valor}' WHERE `cod`='{$this->cod}'";
        parent::sql($sql);
        return true;
    }


    public function userPurchases()
    {
        $array = [];
        $sql = "SELECT `usuarios`.`cod`, COUNT(*) as `cantidad_pedidos`, SUM(`pedidos`.`total`) AS `cantidad_gastada` FROM `pedidos` 
        LEFT JOIN `usuarios` ON `pedidos`.`usuario` = `usuarios`.`cod` 
        LEFT JOIN `estados_pedidos` ON `pedidos`.`estado` = `estados_pedidos`.`id` 
        WHERE `estados_pedidos`.`estado` != '3' AND `estados_pedidos`.`estado` != '0' GROUP BY `usuarios`.`cod` ORDER BY `cantidad_gastada` DESC LIMIT 100";
        $userPurchases = parent::sqlReturn($sql);
        if ($userPurchases) {
            while ($row = mysqli_fetch_assoc($userPurchases)) {
                $this->set("cod", $row["cod"]);
                $user = $this->view();
                $array[] = array("data" => $row, "user" => $user);
            }
            return $array;
        } else {
            return false;
        }
    }


    public function userNews()
    {
        $array = [];
        $array['minorista'] = [];
        $array['mayorista'] = [];
        $sql = "SELECT * FROM `usuarios` WHERE `fecha` BETWEEN (CURRENT_DATE() - INTERVAL 1 MONTH) AND CURRENT_DATE() ORDER BY `fecha` DESC";

        $userNews = parent::sqlReturn($sql);
        if ($userNews) {
            while ($row = mysqli_fetch_assoc($userNews)) {
                $array[($row['minorista'] == 1) ? 'minorista' : 'mayorista'][] = $row;
            }
        }
        return $array;
    }


    public function allUsersPuchases($filter = [], $limit = "")
    {
        if (is_int($limit)) $limit = "LIMIT $limit";
        $filter[] = "`estados_pedidos`.`estado` != '3' AND `estados_pedidos`.`estado` != '0'";
        $filterSql = (count($filter)) ? "WHERE " . implode(" AND ", $filter) : '';
        $query = "SELECT `usuarios`.`cod`, COUNT(*) as `cantidad_pedidos`, SUM(`pedidos`.`total`) AS `cantidad_gastada` 
        FROM `pedidos`  LEFT JOIN `usuarios` ON `pedidos`.`usuario` = `usuarios`.`cod` 
        LEFT JOIN `estados_pedidos` ON `pedidos`.`estado` = `estados_pedidos`.`id` $filterSql 
        GROUP BY `usuarios`.`cod` ORDER BY `cantidad_gastada` DESC $limit";
        $userPurchases = parent::sqlReturn($query);
        if ($userPurchases) {
            while ($row = mysqli_fetch_assoc($userPurchases)) {
                $this->set("cod", $row["cod"]);
                $user = $this->view();
                $array[] = array("data" => $row, "user" => $user);
            }
            return is_array($array) ? $array : [];
        } else {
            return false;
        }
    }


    public function countContents($filter = [])
    {
        $filterSql = (is_array($filter)) ? 'WHERE ' . implode(' OR ', $filter) : '';
        $sql = "SELECT COUNT(*) as cantidad FROM `usuarios` $filterSql ";
        $query = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($query);
        return $row["cantidad"];
    }


    function paginador($url, $filter, $limit, $page = 1, $range = 1, $friendly = true)
    {
        $filterCount = [];
        $filterCount = isset($filter['status']) ? $filter['status'] : $filter;
        $filterCount = isset($filter['date']) ? $filter['date'] : $filterCount;
        $separator = ($friendly) ? '/p/' : '&pagina=';

        $count = $this->countContents($filterCount);
        $total = ceil($count / $limit);
        $pre = $page - 1;
        $next = $page + 1;
        $html = "<nav class='pagination-section mt-30'>";
        $html .=  "<ul class='pagination justify-content-center'>";
        if ($pre > 0) {
            $html .= "<li class='page-item' style='height:5px'>";
            $html .= "<a class='page-link' href='" . $url . $separator . "1'><i class='fa fa-angle-double-left'></i></a>";
            $html .=  "</li>";
            $html .=  "<li class='page-item'>";
            $html .=  "<a class='page-link' href='" . $url . $separator . $pre . "'><i class='fa fa-angle-left'></i></a>";
            $html .=  "</li>";
        }
        foreach (range($page - $range, $page + $range) as $i) {
            if ($i > 0 && $i <= $total) {
                $active = ($i == $page) ? 'active' : '';
                $html .=  "<li class='page-item $active'>";
                $html .=  "<a class='page-link' href='" . $url . $separator . $i . "'>$i</a>";
                $html .=  "</li>";
            }
        }
        if ($next <= $total) {
            $html .= "<li class='page-item'>";
            $html .= "<a class='page-link' href='" . $url . $separator . $next . "'><i class='fa fa-angle-right'></i></a>";
            $html .=  "</li>";
            $html .= "<li class='page-item' style='height:5px'>";
            $html .= "<a class='page-link' href='" . $url . $separator . $total . "'><i class='fa fa-angle-double-right'></i></a>";
            $html .=  "</li>";
        }
        $html .=  "</ul>";
        $html .=  "</nav>";
        return $html;
    }
}
