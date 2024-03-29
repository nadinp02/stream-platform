<?php

namespace Clases;

class Pagos
{

    //Atributos
    public $id;
    public $titulo;
    public $leyenda;
    public $cod;
    public $estado;
    public $monto;
    public $defecto;
    public $estado_pendiente;
    public $estado_aprobado;
    public $estado_rechazado;
    public $tipo;
    public $minimo;
    public $maximo;
    public $tipo_usuario;
    public $acumular;
    public $desc_usuario;
    public $desc_cupon;
    public $idioma;
    public $entrega;
    public $cuotas;

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

    public function add()
    {
        $sql = "INSERT INTO `pagos`(`titulo`, `leyenda`, `cod`, `estado`, `monto`, `defecto`,`estado_pendiente`,`estado_aprobado`,`estado_rechazado`,`tipo`, `minimo`, `maximo`,`entrega`,`tipo_usuario`,`acumular`,`desc_usuario`,`desc_cupon`,`idioma`,`cuotas`) 
                VALUES ({$this->titulo},
                        {$this->leyenda},
                        {$this->cod},
                        {$this->estado},
                        {$this->monto},
                        {$this->defecto},
                        {$this->estado_pendiente},
                        {$this->estado_aprobado},
                        {$this->estado_rechazado},
                        {$this->tipo},
                        {$this->minimo},
                        {$this->maximo},
                        {$this->entrega},
                        {$this->tipo_usuario},
                        {$this->acumular},
                        {$this->desc_usuario},
                        {$this->desc_cupon},
                        {$this->idioma},
                        {$this->cuotas})";
        $query = $this->con->sql($sql);
        return !empty($query) ? true : false;
    }

    public function edit()
    {
        $sql = "UPDATE `pagos` 
                SET  `titulo` = {$this->titulo},
                    `leyenda` = {$this->leyenda},
                    `estado` = {$this->estado},
                    `monto` = {$this->monto},
                    `defecto` = {$this->defecto},
                    `estado_pendiente` = {$this->estado_pendiente},  
                    `estado_aprobado` = {$this->estado_aprobado}, 
                    `estado_rechazado` = {$this->estado_rechazado},
                    `tipo` = {$this->tipo}, 
                    `minimo` = {$this->minimo}, 
                    `maximo` = {$this->maximo}, 
                    `entrega` = {$this->entrega}, 
                    `tipo_usuario` = {$this->tipo_usuario},
                    `acumular` = {$this->acumular},
                    `desc_usuario` = {$this->desc_usuario},
                    `desc_cupon` = {$this->desc_cupon},
                    `idioma` = {$this->idioma},
                    `cuotas` = {$this->cuotas}
                WHERE `cod`={$this->cod} AND `idioma`={$this->idioma}";
        $query = $this->con->sql($sql);

        return !empty($query) ? true : false;
    }

    public function changeState()
    {
        $sql = "UPDATE `pagos` SET `estado`={$this->estado} WHERE `cod`={$this->cod} AND `idioma`={$this->idioma}";
        $query = $this->con->sql($sql);

        return !empty($query) ? true : false;
    }

    public function delete()
    {
        $sql = "DELETE FROM `pagos` WHERE `cod`  = {$this->cod} AND `idioma`  = {$this->idioma}";
        $query = $this->con->sql($sql);
        return !empty($query) ? true : false;
    }

    public function view()
    {
        $row_ = [];
        $sql = "SELECT * FROM `pagos` WHERE cod = {$this->cod} AND idioma = {$this->idioma} ORDER BY id DESC";
        $pagos = $this->con->sqlReturn($sql);
        if (mysqli_num_rows($pagos)) {

            $row = mysqli_fetch_assoc($pagos);
            if (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["mostrar_precios"] == 1 || $row['tipo'] == null) $row_ = array("data" => $row);
        }
        return $row_;
    }

    public function list($filter = '', $order = '', $limit = '', $idioma)
    {
        $array = array();
        $filter[] = "pagos.idioma = '$idioma'";
        $filterSql = (is_array($filter)) ? 'WHERE ' . implode(' AND ', $filter) : '';
        $orderSql = ($order != '') ? $order : "pagos.id DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : '';

        $sql = "SELECT pagos.*, imagenes.ruta AS img FROM `pagos` LEFT JOIN imagenes ON imagenes.cod = pagos.cod $filterSql  ORDER BY $orderSql $limitSql";
        $pagos = $this->con->sqlReturn($sql);
        if (mysqli_num_rows($pagos)) {
            while ($row = mysqli_fetch_assoc($pagos)) {
                $row['img'] = (!$row['img']) ? LOGO : URL . "/" . $row['img'];
                if (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["mostrar_precios"] == 1 || $row['tipo'] == null) $array[] = array("data" => $row);
            }
            return $array;
        }
    }

    public function getApiPayment($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error !== '') {
            throw new \Exception($error);
        }

        return $response;
    }
}
