<?php

namespace Clases;

class Pedidos extends Conexion
{
    //Atributos
    public $id;
    public $cod;
    public $estado = 0;
    public $envio;
    public $envio_titulo;
    public $detalle_envio;
    public $tracking;
    public $pago;
    public $pago_titulo;
    public $detalle_pago;
    public $leyenda_pago;
    public $link_pago;
    public $observacion = "";
    public $entrega;
    public $total;
    public $usuario;
    public $fecha;
    public $fecha_update;
    public $visto = 0;
    public $idioma;

    private $detallePedido;
    private $estado_pedido;
    private $envios_pedido;
    private $pagos_pedido;
    private $user;
    private $f;
    private $descuentos;

    //Metodos
    public function __construct()
    {

        $this->detallePedido = new DetallePedidos();
        $this->estado_pedido = new EstadosPedidos();
        $this->envios_pedido = new EnviosPedidos();
        $this->pagos_pedido = new PagosPedidos();
        $this->user = new Usuarios();
        $this->f = new PublicFunction();
    }

    public function set($atributo, $valor)
    {
        $valor = strlen($valor) ? "'" . $valor . "'" : "NULL";
        $this->$atributo = $valor;
    }

    public function get($atributo)
    {
        return $this->$atributo;
    }

    public function generarCodPedido()
    {
        $sql = "SELECT IF(MAX(id),(MAX(id)+1),1) AS id FROM pedidos";
        $pedido = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($pedido);
        $cod_pedido = str_pad($row["id"], 10, "0", STR_PAD_LEFT);
        return $cod_pedido;
    }

    public function add($array)
    {
        try {
            $attr = implode(",", array_keys($array));
            $values = ":" . str_replace(",", ",:", $attr);
            $sql = "INSERT INTO `pedidos` ($attr) VALUES ($values)";
            $stmt = parent::conPDO()->prepare($sql);
            return $stmt->execute($array);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function edit()
    {
        $sql = "UPDATE `pedidos` 
                     SET  entrega =  {$this->entrega},
                     total =  {$this->total},
                     estado = {$this->estado},           
                     pago = {$this->pago},           
                     usuario = {$this->usuario},                     
                     observacion = {$this->observacion},           
                     fecha = {$this->fecha},           
                     visto = {$this->visto},
                     idioma = {$this->idioma}         
                  WHERE `cod`= {$this->cod} AND `idioma` ={$this->idioma}";
        $query = parent::sql($sql);
        return !empty($query) ?  true : false;
    }

    public function editSingle($atributo, $valor)
    {
        $sql = "UPDATE `pedidos` SET `$atributo` = '{$valor}' WHERE `cod`={$this->cod}";
        $query = parent::sqlReturn($sql);
        return $query;
    }

    public function delete()
    {
        $sql = "DELETE FROM `pedidos` WHERE `cod`  = {$this->cod}";
        $query = parent::sql($sql);
        $this->detallePedido->delete($this->cod);
        return $query;
    }

    public function view()
    {
        $sql = "SELECT * FROM `pedidos` WHERE cod = {$this->cod} ORDER BY id DESC";
        $pedidos = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($pedidos);
        if ($row) {
            $details = $this->detallePedido->list($this->cod);
            $envio = $this->envios_pedido->list(["id" => $row['detalle_envio']], '', '', true);
            $pago = $this->pagos_pedido->list(["id" => $row['detalle_pago']], '', '', true);
            $this->estado_pedido->set("id", $_SESSION['lang']);
            $estado = $this->estado_pedido->view($row["estado"]);
            $this->user->set("cod", $row['usuario']);
            $user = $this->user->view();
            $data = ["data" => $row, "user" => $user, "detalle_envio" => $envio, "detalle_pago" => $pago, "detail" => $details, "estados" => $estado];
        } else {
            $data = false;
        }
        return $data;
    }

    public function list($filter, $order, $limit)
    {
        $array = [];
        $filterSql = '';
        if (is_array($filter) && !isset($filter['status']) && !isset($filter['date'])) $filterSql = "WHERE " . implode(" AND ", $filter);
        if (isset($filter['status'])) $filterSql = "WHERE " . implode(" OR ", $filter['status']);
        if (isset($filter['date'])) $filterSql = "WHERE " . implode(" AND ", $filter['date']);
        $orderSql = ($order != '') ? $order :  "id DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : "";
        $sql = "SELECT * FROM `pedidos` $filterSql  ORDER BY $orderSql $limitSql";
        $result = parent::sqlReturn($sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $details = $this->detallePedido->list("'" . $row['cod'] . "'");
                $envio = $this->envios_pedido->list(["id" => $row['detalle_envio']], '', '', true);
                $pago = $this->pagos_pedido->list(["id" => $row['detalle_pago']], '', '', true);
                $this->user->set("cod", $row['usuario']);
                $estado = $this->estado_pedido->view($row["estado"]);
                $user = $this->user->view();
                $array[] = ["data" => $row, "user" => $user, "detail" => $details, "detalle_envio" => $envio, "detalle_pago" => $pago, "estados" => $estado];
            }
        }
        return $array;
    }

    public function getTotalByStatus($filter = '')
    {
        // Genero todo el array en vacio
        $status = [];
        $statusTotal = [
            "1" => ["data" => ["cantidad" => 0, "total" => 0]],
            "2" => ["data" => ["cantidad" => 0, "total" => 0]],
            "3" => ["data" => ["cantidad" => 0, "total" => 0]]
        ];
        $filterSql = (is_array($filter)) ? "WHERE " . implode(" AND ", $filter['date']) : '';

        $sql = "SELECT COUNT(*) as cantidad, SUM(pedidos.total) AS total , estados_pedidos.id as idEstado, estados_pedidos.titulo as titulo, pedidos.estado, estados_pedidos.estado as tipoEstado  
        FROM pedidos 
        LEFT JOIN estados_pedidos ON pedidos.estado = estados_pedidos.id $filterSql GROUP BY estados_pedidos.id ORDER BY estados_pedidos.estado ASC";
        $pedidos = parent::sqlReturn($sql);
        if ($pedidos) {
            while ($row = mysqli_fetch_assoc($pedidos)) {
                $status[] = ["data" => $row]; // Relleno el array vacio con los datos que traiga de la consulta y si no encuentra mantiene el vacio
                $statusTotal[$row['tipoEstado']] = ["data" => [
                    "cantidad" => ($statusTotal[$row['tipoEstado']]['data']['cantidad'] + $row['cantidad']),
                    "total" => ($statusTotal[$row['tipoEstado']]['data']['total'] + $row['total'])
                ]];
            }
            $array = ["status" => $status, "statusTotal" => $statusTotal];
        }
        return $array;
    }

    public function tooltipData($totalByStatus)
    {
        $tooltipName = [];
        foreach ($totalByStatus as $tootltipStatus) {
            $tooltipName[$tootltipStatus['data']['tipoEstado']][] = [
                "cantidad" => $tootltipStatus['data']['cantidad'],
                "total" => $tootltipStatus['data']['total'],
                "titulo" => $tootltipStatus['data']['titulo']
            ];
        }
        return $tooltipName;
    }

    public function getTooltip($tooltipData)
    {
        if ($tooltipData) {
            $total = count($tooltipData);
            $i = 0;
            foreach ($tooltipData as $tooltip) {
                $i++;
                echo "<div class='mt-20'><b>" . $tooltip["titulo"] . "</b> ( " . $tooltip["cantidad"] . " )<br/> <b>$" . number_format($tooltip["total"], "2", ",", ".") . "</div>";
                echo ($i != $total) ? "<hr/>" : "<br/>";
            }
        } else {
            echo "<div class='mt-20 mb-15'><b>No existen pedidos en este estado</b></div>";
        }
    }

    /**
     *
     * Traer un array con el detalle del pedido y el  de informacion '' o 'pago'
     *
     * @param    array  $detalle array con la informacion del pedido
     * @param    string  $typeInfo  de informacion si es del apartado de pagos o del de s
     * @return   array retorna un array con cada dato ya incluido en una etiqueta <p></p>
     *
     */
    public function getInfoPedido($detalle)
    {
        $textReturn = '';
        foreach ($detalle['data'] as $key => $value) {
            if ($key != 'similar' && $key != 'factura' && $key != 'usuario' && $key != 'id'  && $key != 'fecha_update' && $key != 'fecha') {
                $textReturn .= !empty($value) ? "<p class='mb-0 fs-13'><b class='text-dark'>" . $_SESSION["lang-txt"]["checkout"][$key] . ": </b>" . str_replace('/u([\da-fA-F]{4})/', '&#x\1;', $value) . "</p> " : "";
            }
        }
        return $textReturn;
    }

    public function countContents($filter = [])
    {
        $filterSql = (is_array($filter)) ? 'WHERE ' . implode(' OR ', $filter) : '';
        $sql = "SELECT COUNT(*) as cantidad FROM `pedidos` $filterSql ";
        $query = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($query);
        return $row["cantidad"];
    }

    public function paginador($url, $filter, $limit, $page = 1, $range = 1, $friendly = true)
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

    public function checkMercadoPago()
    {
        if (strpos(CANONICAL, 'collection_id')) {
            $urlCanonical = explode("&", CANONICAL);
            $collection_id = str_replace(URL . "/checkout/detail?collection_id=", "", $urlCanonical[0]);
            if ($collection_id != "null") {
                if (isset($collection_id) && !empty($collection_id)) {
                    $this->f->curl("GET", URL . "/api/payments/ipn.php?id=" . $collection_id, '');
                }
            }
        }
    }

    public function gestionLTV($filter = [])
    {
        $array = [];
        $filterSql = (is_array($filter)) ? "WHERE " . implode(" AND ", $filter) : '';

        $sql = "SELECT COUNT(`usuarios`.`cod`) AS cantidad_pedidos,`usuarios`.`nombre`, `usuarios`.`apellido`,`usuarios`.`email`,
        `usuarios`.`telefono`,`usuarios`.`localidad`,`usuarios`.`provincia`,
        SUBSTRING_INDEX( GROUP_CONCAT( `pedidos`.`fecha` ORDER BY `pedidos`.`id` DESC SEPARATOR '||' ), '||', 1 ) ultima_compra , 
        DATEDIFF(NOW(),SUBSTRING_INDEX( GROUP_CONCAT( `pedidos`.`fecha` ORDER BY `pedidos`.`id` DESC SEPARATOR '||' ), '||', 1 )) AS ultimo_dia 
        FROM pedidos 
        LEFT JOIN `usuarios` ON `usuarios`.`cod` = `pedidos`.`usuario`  
        LEFT JOIN `estados_pedidos` ON `estados_pedidos`.`id` = `pedidos`.`estado` 
        $filterSql
        GROUP BY `pedidos`.`usuario` ORDER BY ultima_compra DESC";
        $result = parent::sqlReturn($sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $array[] = ["data" => $row];
            }
        }
        return $array;
    }

    public function listEstadisticas($filter, $order, $limit)
    {
        $array = [];
        $filterSql = '';
        if (is_array($filter) && !isset($filter['status']) && !isset($filter['date'])) {
            $filterSql = "WHERE " . implode(" AND ", $filter);
        }
        if (isset($filter['status'])) {
            $filterSql = "WHERE " . implode(" OR ", $filter['status']);
        }
        if (isset($filter['date'])) {
            $filterSql = "WHERE " . implode(" AND ", $filter['date']);
        }

        $orderSql = ($order != '') ? $order : "pedidos.id DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : "";
        $sql = "SELECT pedidos.* FROM `pedidos` LEFT JOIN usuarios ON usuarios.cod = pedidos.usuario  $filterSql  ORDER BY $orderSql $limitSql";
        $result = parent::sqlReturn($sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $details = $this->detallePedido->list("'" . $row['cod'] . "'");
                $this->user->set("cod", $row['usuario']);
                $estado = $this->estado_pedido->view($row["estado"]);
                $user = $this->user->view();
                $array[] = ["data" => $row, "user" => $user, "detail" => $details, "estados" => $estado];
            }
        }
        return $array;
    }

    public function getProductsFromOrder($date = '')
    {
        $this->descuentos = new Descuentos();
        $array = [];
        $pedidos = $this->descuentos->getOrdersCod($date);
        if (!empty($pedidos)) {
            foreach ($pedidos as $key => $pedidoItem) {
                $array_ = [];
                foreach ($pedidoItem["pedidos_cod"] as $cod) {
                    foreach ($this->detallePedido->list("'" . $cod . "'") as $detail) {
                        if ($detail["tipo"] != "PR") continue;
                        $array[$key] = ["pedidos_cod" => $pedidoItem["pedidos_cod"], "cant_pedidos" => $pedidoItem["pedidos"]];
                        $array[$key]["productos"][$detail["cod_producto"]] = [];
                        if (isset($array_[$detail["cod_producto"]]["cantidad"])) {
                            $array_[$detail["cod_producto"]]["cantidad"] = $array_[$detail["cod_producto"]]["cantidad"]  + $detail["cantidad"];
                        } else {
                            $array_[$detail["cod_producto"]] = ["cantidad" => $detail["cantidad"], "titulo" => $detail["producto"]];
                        }
                    }
                    foreach ($this->list(["cod = '" . $cod . "'"], "", "") as $order) {
                        $arrayPedido["fecha"][] =  $order["data"]["fecha"];
                        $arrayPedido["precio"][] =  $order["data"]["total"];
                        $arrayUsuario[$order["user"]["data"]["cod"]] = ["email" => $order["user"]["data"]["email"]];
                    }
                }
                $array[$key]["pedido"] = $arrayPedido;
                $array[$key]["usuario"] = $arrayUsuario;
                $array[$key]["productos"] = $array_;
                unset($arrayPedido, $arrayUsuario, $array_);
                $array[$key]["cant_productos"] = count($array[$key]["productos"]);
            }
            return $array;
        }
    }

    public function getUsersCod($filter)
    {
        $array = [];
        $filterSql = (is_array($filter)) ? "WHERE " . implode(" AND ", $filter) : '';

        $sql = "SELECT `pedidos`.`usuario` FROM `pedidos` 
        LEFT JOIN `usuarios` on `pedidos`.`usuario` = `usuarios`.`cod`
        LEFT JOIN `estados_pedidos` on `estados_pedidos`.`id` = `pedidos`.`estado` $filterSql";
        $result = parent::sqlReturn($sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $array[] =  $row["usuario"];
            }
        }
        return $array;
    }

    public function getOrderPerState($filter)
    {
        $array = [];
        $filterSql = (is_array($filter)) ? "WHERE " . implode(" AND ", $filter) : '';
        $sql = "SELECT `pedidos`.`cod`,`pedidos`.`fecha`,`pedidos`.`total`,`usuarios`.`nombre`,`usuarios`.`apellido`,`usuarios`.`provincia` 
                    FROM `pedidos` 
                LEFT JOIN `usuarios` ON `usuarios`.`cod` = `pedidos`.`usuario` 
                $filterSql
                ORDER BY `pedidos`.`fecha` DESC;";
        $result = parent::sqlReturn($sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $array[] = ["data" => $row];
            }
        }
        return $array;
    }

    public function enviarPedidoWsp($pedido)
    {
        $cfg = new Config();
        $contactData = $cfg->viewContact();

        $mensaje =  "Hola soy *" . $pedido['detalle_envio']['data']['nombre'] . " " . $pedido['detalle_envio']['data']['apellido'] . "* realice el siguiente pedido " . $pedido['data']['cod'] . " <br>";
        $mensaje .=  "<br>*Detalle del Pedido:* <br>";
        $productos = '';
        $cupon = '';
        $envio = '';
        $pago = '';
        foreach ($pedido['detail'] as $detail) {
            switch ($detail['tipo']) {
                case 'PR':
                    $cantidad = ($detail['promo']) ? $detail['promo'] : $detail['cantidad'];
                    $subtotal = ($detail['precio'] * $cantidad);
                    $costo = ($detail['precio'] > 0) ? " - $" . $subtotal : '';
                    $productos .=  "(" . $detail['cantidad'] . ") - " . $detail['producto'] . $costo . "<br>";
                    break;
                case 'CP':
                    $cupon .= "*Descuento:* " . $detail['producto'] . " - $" . $detail['precio'] . "<br>";
                    break;
                case 'ME':
                    $costo = ($detail['precio'] > 0) ? " - $" . $detail['precio'] : '';
                    $envio .=  "*Método de Envio:* " . $detail['producto'] . $costo . "<br>";
                    break;
                case 'MP':
                    $costo = ($detail['precio'] > 0) ? " - $" . $detail['precio'] : '';
                    $pago .=  "*Método de Pago:* " . $detail['producto'] . $costo . "<br>";
                    break;
            }
        }
        $mensaje .=  $productos . '<br>' . $cupon . $envio . $pago;
        if ($pedido['data']['total'] > 0) $mensaje .=  "<br>*Total:* $" . $pedido['data']['total'] . "<br>";
        $mensaje .=  "<br>*Mis datos de contacto:* <br>";
        $mensaje .=  "Nombre: " . $pedido['detalle_envio']['data']['nombre'] . " " . $pedido['detalle_envio']['data']['apellido'] . "<br>";
        $mensaje .=  "Teléfono: " . $pedido['detalle_envio']['data']['telefono'] . " / " . $pedido['detalle_envio']['data']['celular']  . "<br>";
        $mensaje .=  "Email: " . $pedido['detalle_envio']['data']['email'] . "<br>";
        $mensaje .=  "Dirección: " . $pedido['detalle_envio']['data']['calle'] . " " . $pedido['detalle_envio']['data']['numero'] . " " . $pedido['detalle_envio']['data']['piso'] . ", " . $pedido['detalle_envio']['data']['localidad'] . ", " . $pedido['detalle_envio']['data']['provincia'] . "<br>";
        $mensaje .=  "<br>¡Muchas gracias!";
        $mensaje = str_replace(" ", "%20", $mensaje);
        $mensaje = str_replace("<br>", "%0A", $mensaje);

        $this->f->openUrl("https://wa.me/" . $contactData['data']['whatsapp'] . "?text=" . $mensaje);
    }
}
